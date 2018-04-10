<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."modules.php");

if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php")){
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php");
}

/**
 * The variable globalvarsforgroup maps the names of the global variables to its default value and request parameter used
 */
$globalvarsforgroup = array(
	'basedata'				=> array(null, array('basedata')),
	'force'					=> array(0, array('force', 'f')),
	'initialize'			=> array(0, array('initialize')),
	'callbackfn'			=> array('', array('callbackfn')),
	'action'				=> array('', array('action')),
	'lastgroupmessageid'	=> array(0, array('lastgroupmessageid', 'crtimestamp')),
	'crreadmessages'		=> array(array(), array('crreadmessages')),
	'groupid'				=> array(0, array('groupid', 'roomid', 'chatroomid', 'currentroom', 'id')),
	'password'				=> array('', array('password', 'currentp', 'inviteid')),
	'groupname'				=> array('', array('groupname', 'cname', 'roomname', 'name')),
	'type'					=> array(0, array('type')),
	'silent'				=> array(0, array('silent')),
	'noBar'					=> array(0, array('noBar')),
	'request_rttlang'		=> array(0, array($cookiePrefix.'rttlang'), $_COOKIE),
	'cc_layout'				=> array('', array('cc_layout')),
	'embed'					=> array(0, array('embed')),
	'request_ulh'			=> array('', array('ulh')),
	'request_clh'			=> array('', array('clh')),
	'prepend'				=> array(0, array('prepend')),
	'userstoinvite'			=> array('', array('invite')),
	'appinfo'				=> array(array(), array('appinfo')),
	'useridtokick'			=> array(0, array('kickid')),
	'useridtoban'			=> array(0, array('banid')),
	'kick'					=> array(0, array('kick')),
	'ban'					=> array(0, array('ban')),
	'banflag'				=> array(0, array('banflag')),
	'kickflag'				=> array(0, array('kickflag')),
	'userstounban'			=> array('', array('unban')),
	'groupmessageidtodelete'=> array(0, array('delid')),
);

defineFromRequest($globalvarsforgroup);

if($userid == 0 || in_array($userid,$bannedUserIDs)){
	$response['logout'] = 1;
	$response['loggedout'] = 1;
	sendCCResponse(json_encode($response));
	exit;
}elseif(!empty($initialize)){
	$response['userid'] = $userid;
	$query = sql_query('getMaxID',array('field'=>'id', 'tablename'=>'cometchat_chatroommessages'));
	if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }
	$result = sql_fetch_assoc($query);
	$response['lastgroupmessageid'] = '0';
	if(!empty($result['id'])){
		$response['lastgroupmessageid'] = $result['id'];
	}
	$force = 1;
}

if(!empty($groupname)){
	if(!in_array($action, array('createchatroom','renamechatroom'))){
		$encodedgroupname = urldecode($groupname);
		$groupname = base64_decode($groupname);
	}else{
		$encodedgroupname = base64_encode($groupname);
	}
	$groupname = urldecode($groupname);
}

if($action == 'sendmessage'){
	$action = 'sendChatroomMessage';
}

$embed = '';
$embedcss = '';
$close = "setTimeout('closePopup();',2000);";
if(!empty($embed) && $embed == 'web'){
	$embed = 'web';
	$embedcss = 'embed';
	$close = "setTimeout('closePopup();',2000);";
}

if(empty($response)){
	$response = array();
}
if($action == 'getChatroomName'){
	if(!empty($getId)){
		$sql = sql_getQuery('getRoomName', array('chatroomid'=>$groupid));
		$query = sql_query($sql, array(), 1);
		$result = sql_fetch_assoc($query);
		$response['name'] = $result['name'];
		sendCCResponse(json_encode($response));
		exit;
	}
}

if($action == 'updateChatroomMessages'){
	global $lastMessages;
	getChatroomData($groupid, $prepend, $lastMessages);
}

function heartbeat(){
	global $response, $userid, $chatrooms_language, $chatroomTimeout, $lastMessages, $cookiePrefix, $allowAvatar, $guestsMode, $crguestsMode, $guestnamePrefix, $chromeReorderFix, $showChatroomUsers, $showGroupsOnlineUsers, $force, $groupid, $password, $groupname, $encodedgroupname, $initialize, $callbackfn, $channelprefix, $pushplatformsuffix;

	$usertable = TABLE_PREFIX.DB_USERTABLE;
	$usertable_username = DB_USERTABLE_NAME;
	$usertable_userid = DB_USERTABLE_USERID;
	$time = getTimeStamp();
	$chatroomList = array();
	$key = '';

	$joinedgroups = getJoinedGroups($userid,$force);

	if(empty($_SESSION['cometchat']['cometchat_lastlactivity']) || ($time-$_SESSION['cometchat']['cometchat_lastlactivity'] >= REFRESH_BUDDYLIST/4)){
		$sql = updateLastActivity($userid);
		if(function_exists('hooks_updateLastActivity')){
			hooks_updateLastActivity($userid);
		}
		$query = sql_query($sql, array(), 1);

		if(defined('DEV_MODE') && DEV_MODE == '1'){ echo sql_error($GLOBALS['dbh']); }
		$_SESSION['cometchat']['cometchat_lastlactivity'] = $time;
	}

	if(empty($_SESSION['cometchat']['cometchat_chatroomslist']) || $force==1 || (!empty($_SESSION['cometchat']['cometchat_chatroomslist']) && ($time-$_SESSION['cometchat']['cometchat_chatroomslist'] > REFRESH_BUDDYLIST))){

		if(!is_array($cachedGroups = getCache('chatroom_list'))|| ($force==1)){
			$cachedGroups = array();

			if($showGroupsOnlineUsers == 1){
				$sqlPart = " and ((cometchat_status.lastactivity > (".sql_real_escape_string($time)."-".((ONLINE_TIMEOUT)*2).")) OR cometchat_status.isdevice = 1) and (cometchat_status.status IS NULL OR cometchat_status.status <> 'invisible' OR cometchat_status.status <> 'offline')";
			}else{
				$sqlPart = "";
			}
			if($showChatroomUsers == 1){
				$sqlPart = sql_getQuery('groups_sqlpart',array('timestampCondition'=>$sqlPart));
			}else{
				$sqlPart = '0';
			}

			$sql = sql_getQuery('getGroupsData',array('sqlpart'=>$sqlPart));
			/* hooks for group list*/
			if(function_exists('hooks_getGroupList')){
				$sql = hooks_getGroupList(array('sqlPart'=>$sqlPart));
			}

			$query = sql_query($sql,array(),1);
			while ($group = sql_fetch_assoc($query)){
				$cachedGroups[$chromeReorderFix.$group['id']] = array(
					'id' => $group['id'],
					'name' => urldecode($group['name']),
					'online' => $group['members'],
					'members' => $group['members'],
					'type' => $group['type'],
					'password' => $group['password'],
					'lastactivity' => $group['lastactivity'],
					'createdby' => $group['createdby'],
					'invitedusers' => $group['invitedusers']
				);
			}
			setCache('chatroom_list',$cachedGroups,30);
		}
		if(defined('KEY_A') && defined('KEY_B') && defined('KEY_C')){
			$key = KEY_A.KEY_B.KEY_C;
		}
		foreach($cachedGroups as $key=>$group){
			if((($group['createdby'] == 0 || ($group['createdby'] <> 0 && $time - $group['lastactivity'] < $chatroomTimeout)) || $group['createdby'] == $userid) && ($group['type'] <> 3)){
				$userList = explode(',', $group['invitedusers']);

				if($group['type'] == 2 && !in_array($userid, $userList) && $group['createdby'] != $userid){
					continue;
				}else{
					$joined = 0;
					if(in_array($group['id'], $joinedgroups)){
						$joined = 1;
					}
					if($joined == 0){
						$group['password'] = '';
					}
					$chatroomList[$chromeReorderFix.$group['id']] = array(
						'id' => $group['id'],
						'name' => $group['name'],
						'online' => $group['online'],
						'type' => $group['type'],
						'i' => $group['password'],

						/* START: Backward Compatibility 6-Nov-2017 CometChat v6.9.7 */
						's' => $joined,
						/* END: Backward Compatibility 6-Nov-2017 CometChat v6.9.7 */

						'owner' => isOwner($userid, $group['id']),
						'isModerator' => isModerator($userid),
						'createdby' => $group['createdby'],
						'j' => $joined
					);
					if(USE_COMET == 1 && COMET_CHATROOMS == 1 && in_array($group['id'], $joinedgroups)){
						$chatroomList[$chromeReorderFix.$group['id']]['cometid'] = md5('chatroom_'.$group['id'].$key);
					}
					if(in_array($group['id'], $joinedgroups)){
						$chatroomList[$chromeReorderFix.$group['id']]['push_channel'] = 'C_'.md5($channelprefix."CHATROOM_".$group['id'].BASE_URL).getPlatformSuffix($pushplatformsuffix);
					}
					if (!empty($group['invitedusers'])) {
						$chatroomList[$chromeReorderFix.$group['id']]['members'] = $group['invitedusers'];
					}
				}
			}
		}
		$response['joinedgroups'] = $joinedgroups;
		$_SESSION['cometchat']['cometchat_chatroomslist'] = $time;

		$clh = md5(serialize($chatroomList));
		if(empty($request_clh) || $clh <> $request_clh || $force == 1){
			$response['chatrooms'] = $chatroomList;
			$response['clh'] = $clh;
		}
	}

	/* START: Backward Compatibility 28-Dec-2017 CometChat v6.9.20 */
	if($initialize==1 && !empty($joinedgroups) || (!empty($groupid) && $force == 1 && USE_COMET == 1 && COMET_CHATROOMS == 1)){
		if(USE_COMET == 1 && COMET_CHATROOMS == 1){
			$cometresponse = array();
			foreach($joinedgroups as $key => $group){
				$key = '';
				if(defined('KEY_A') && defined('KEY_B') && defined('KEY_C')){
					$key = KEY_A.KEY_B.KEY_C;
				}
				$cometresponsedata = array(
					'chatroomid' => $group,
					'cometid' => md5('chatroom_'.$group.$key),
					'userid' => $userid
				);
				array_push($cometresponse, $cometresponsedata);
			}
			$response['subscribeChatrooms'] = $cometresponse;
		}
	}
	/* END: Backward Compatibility 28-Dec-2017 CometChat v6.9.20 */

	if($initialize==0){
		fetchChatroomMessages(array('joinedgroups'=>$joinedgroups,'force'=>$force));
	}

	$sql = '';
	if(!empty($groupid)){
		$sql = sql_getQuery('getGroupPassword',array('currentroom'=>$groupid));
	}
	if(!empty($sql)){
		$query = sql_query($sql, array(), 1);
		if($room = sql_fetch_assoc($query)){
			if(!empty($room['password']) && (empty($password) || $room['password'] <> $password)){
				unset($response['users']);
				unset($response['crmessages']);
			}
		}
	}

	if(function_exists('hooks_groupHeartbeat')){
		hooks_groupHeartbeat(array());
	}
}

function groupSyncMessages(){
	global $response, $userid, $force;

	$force = 1;
	$joinedgroups = getJoinedGroups($userid,$force);

	fetchChatroomMessages(array('joinedgroups'=>$joinedgroups,'force'=>$force));
	sendCCResponse(json_encode($response));
	exit();
}

function fetchChatroomMessages($params){
	global $response, $userid, $lastMessages, $guestsMode, $crguestsMode, $lastgroupmessageid, $chatrooms_language, $cookiePrefix, $guestnamePrefix, $trayicon, $groupid, $callbackfn, $request_rttlang;

	$joinedgroups = $params['joinedgroups'];
	$force = $params['force'];

	if(count($joinedgroups) > 0){
		$messages = array();
		$moremessages = array();

		$limit = $lastMessages;

		if(!empty($groupid) && $force == 1 && !empty($_SESSION['cometchat']['cometchat_chatroom_'.$groupid])){
			$messages = getChatroomData($groupid,0,10);
			$messages = array_reverse($messages);
		}else{
			if(USE_COMET == 1 && empty($initialize) && $force != 1){ return; }
			$guestpart = "";
			$limitClause = " limit ".sql_real_escape_string($limit)." ";
			$timestampCondition = "";

			$timestampCondition = "";
			if(!empty($lastgroupmessageid)){
				$timestampCondition = sql_getQuery('group_timestampcondition3',array('joinedrooms'=>implode(",", $joinedgroups), 'id'=>$lastgroupmessageid));
				$limitClause = "";
			}else{
				$timestampCondition = sql_getQuery('group_timestampcondition4',array('joinedrooms'=>implode(",", $joinedgroups)));
				$limitClause = sql_getQuery('groups_guestpart_limitClause',array('limit'=>$limit));
			}

			if($guestsMode && $crguestsMode){
				$guestpart = sql_getQuery('groups_guestpart',array('guestnamePrefix'=>$guestnamePrefix, 'timestampCondition'=>$timestampCondition, 'limitClause'=>$limitClause));
			}

			$query = sql_query('groupMessages',array('timestampCondition'=>$timestampCondition, 'guestpart'=>$guestpart, 'limitClause'=>$limitClause));

			if(sql_num_rows($query) > 0){
				while ($chat = sql_fetch_assoc($query)){
					if(function_exists('processName')){
						$chat['from'] = processName($chat['from']);
					}

					if($lastMessages == 0 && $lastgroupmessageid == 0){
						$chat['message'] = '';
					}

					if($userid == $chat['userid']){
						$chat['from'] = $chatrooms_language[6];
					}else{
						if(!empty($trayicon['realtimetranslate']) && !empty($request_rttlang) && strpos($chat['message'],'CC^CONTROL_') === false){
							if(!empty($request_rttlang)){
								$translated = text_translate($chat['message'],'',$request_rttlang);
							}
							if($translated != ''){
								$chat['message'] = strip_tags($translated).' ('.$chat['message'].')';
							}
						}
					}

					$localmessageid = 0;
					if(!empty($_SESSION['cometchat']['duplicates']['group_localmessageid'])){
						$localmessageid = array_search($chat['id'], $_SESSION['cometchat']['duplicates']['group_localmessageid']);
					}
					$messagetoadd = array(
						'id' => $chat['id'],
						'from' => $chat['from'],
						'fromid' => $chat['fromid'],
						'message' => $chat['message'],
						'sent' => $chat['sent'],
						'groupid' => $chat['chatroomid'],
						/* START: Backward Compatibility 18-Oct-2017 CometChat v6.9.0 */
						'roomid' => $chat['chatroomid'],
						'chatroomid' => $chat['chatroomid'],
						/* END: Backward Compatibility 18-Oct-2017 CometChat v6.9.0 */
						'localmessageid' => $localmessageid
					);
					array_unshift($messages, $messagetoadd);
					$_SESSION['cometchat']['cometchat_chatroom_'.$chat['chatroomid']][$chat['id']] = $messagetoadd;
				}
			}
		}

		if(!empty($messages)){
			$response['crmessages'] = $messages;
		}
	}
}

function createchatroom(){
	global $userid, $password, $groupname, $type, $groupid,$pushplatformsuffix;

	$response = array();
	$response['success'] = false;
	if($userid > 0){
		$query = sql_query('getGroupName',array('name' => $groupname));
		if(sql_num_rows($query) == 0){
			$time = getTimeStamp();
			if(!empty($password)){
				$password = sha1($password);
			}
			$query = sql_query(
				'insertChatroom',
				array(
					'name' => $groupname,
					'createdby' => $userid,
					'lastactivity' => getTimeStamp(),
					'createdon' => getTimeStamp(),
					'password' => $password,
					'type' => $type
				)
			);
			$groupid = sql_insert_id('cometchat_chatrooms');

			$query = sql_query(
				'insertChatroomUser',
				array(
					'userid' => $userid,
					'chatroomid' => $groupid
				)
			);
			$response = array(
				'id' => $groupid,
				'name' => $groupname
			);
			//Store chatroom name in session for push notifications
			$_SESSION['cometchat']['chatrooms'] = array(
				'_'.$groupid => $response
			);

			/*hooks create group*/
			if(function_exists('hooks_createGroup')){
				hooks_createGroup(array('chatroomid'=>$groupid));
			}
			$response['success'] = true;
		}else{
			$response['error'] = 'Group with name '.$groupname.' already exists';
		    /**
		    * START: Keep the backword compitability for iOS App
		    */
			if ($pushplatformsuffix == 'i') {
				if (!empty($_GET['callback'])) {
					echo $_GET['callback'].'('.json_encode(0).')';
					exit();
				}
			}
		    /**
		    * END: Keep the backword compitability for iOS App
		    */
		}
	}else{
		$response['error'] = 'Userid cannot be '.var_export($userid);
	}
	sendCCResponse(json_encode($response));
	exit;
}

function getchatroomusers(){
	global $userid, $guestsMode, $crguestsMode, $allowAvatar, $chromeReorderFix, $force, $status, $showGroupsOnlineUsers, $groupid, $request_ulh, $action;

	$response = array();
	$users = array();
	$time = getTimeStamp();

	if(!is_array($users = getCache('chatrooms_users'.$groupid)) || ($force == 1)){
		if($showGroupsOnlineUsers == 1){
			$sqlPart = "and ((cometchat_status.lastactivity > (".sql_real_escape_string($time)."-".((ONLINE_TIMEOUT)*2).")) OR cometchat_status.isdevice = 1) and (cometchat_status.status IS NULL OR cometchat_status.status <> 'invisible' OR cometchat_status.status <> 'offline')";
		}else{
			$sqlPart = "";
		}
		$sql = sql_getQuery('getchatroomusers',array('chatroomid'=>$groupid,'timestampCondition'=>$sqlPart));
		if($guestsMode && $crguestsMode){
			$sql = getChatroomGuests($groupid,$time,$sql);
		}
		$query = sql_query($sql, array(), 1);

		while ($user = sql_fetch_assoc($query)){
			if(((($time-processTime($user['lastactivity'])) < ONLINE_TIMEOUT) || $user['isdevice'] == 1) && $user['status'] != 'invisible' && $user['status'] != 'offline'){
				if(($user['status'] != 'busy' && $user['status'] != 'away')){
					$user['status'] = 'available';
				}
			}else{
				$user['status'] = 'offline';
			}
			if(function_exists('processName')){
				$user['username'] = processName($user['username']);
			}
			$avatar = '';
			if($allowAvatar){
				$avatar = getAvatar($user['avatar']);
			}
			$user['userid'] = (int)$user['userid'];

			$users[$chromeReorderFix.$user['userid']] = array(
				'id' => $user['userid'],
				'n' => $user['username'],
				'a' => $avatar,
				'b' => $user['isbanned'],
				/* START: Backward compatibility CometChat Version 6.9.4  25th Oct 2017*/
				'chatroomid' => $groupid,
				/* END: Backward compatibility CometChat Version 6.9.4  25th Oct 2017*/
				'groupid' => $groupid,
				's' => $user['status'],
				'ismoderator'=> isModerator($user['userid'])
			);
		}
		setCache('chatrooms_users'.$groupid,$users,30);
	}

	if(empty($_SESSION['cometchat']['cometchat_chatroom_'.$groupid])){
		$_SESSION['cometchat']['cometchat_chatroom_'.$groupid] = array();
	}

	if(!empty($users)){
		$ulh = md5(serialize($users));
		if(empty($request_ulh) || $ulh <> $request_ulh){
			$response['ulh'] = $ulh;
			$response['users'] = $users;
		}
	}

	if($action == 'getchatroomusers'){
		sendCCResponse(json_encode($response));
		exit;
	}

	return $response;
}

function deletechatroom(){
	global $userid, $groupid;

	$response['success'] = false;
	if(!empty($groupid) && $userid>0){
		$sql = sql_getQuery('getChatroomById',array('id'=>$groupid));
		/* hook to get group details*/
		if(function_exists('hooks_getGroupByID')){
			$sql = hooks_getGroupByID(array('id'=>$groupid));
		}
		$query = sql_query($sql,array(),1);
		if($group = sql_fetch_assoc($query)){
			$owner = $group['createdby'];
			if($owner==0){
				/** Admin created group **/
				$response['error'] = 'You can not delete groups created by admin';
			}elseif(isOwner($userid, $groupid)){
				updateToJoinedGroups($groupid,true);
				$controlparameters = json_encode(
					array(
						'type' => 'modules',
						'name' => 'chatroom',
						'method' => 'deletedchatroom',
						'params' => array(
							'id' => $groupid,
							'deletedby' => $userid
						)
					)
				);
				$response['messageid'] = sendChatroomMessage($groupid,'CC^CONTROL_'.$controlparameters,0);
				/**
					$options: Only for SQL server
					SQLSRV_CURSOR_FORWARD: Lets you move one row at a time starting at the first row of the result set until you reach the end of the result set.
				*/
				$options =  array( "Scrollable" => SQLSRV_CURSOR_FORWARD );
				$query = sql_query('deleteGroup',array('id'=>$groupid), 0, $options);
				$affectedrow =  sql_affected_rows($query);
				if($affectedrow>0){
					$response['success'] = true;
					$response['acknowledgment'] = 'A group with groupid '.$groupid.' has been deleted successfully.';
				}else{
					$response['error'] = 'An error occurred while deleting group with groupid '.$groupid.' from DB.';
				}
			}else{
				$response['error'] = 'You are not an Owner of the group with groupid '.$groupid.' to delete it';
			}
		}else{
			/** Group not found in DB **/
			$response['error'] = 'Group with id '.$groupid.' does not exists.';
		}
	}else{
		$response['error'] = 'Userid cannot be '.var_export($userid);
		if(empty($groupid)){
			$response['error'] = 'Groupid cannot be '.var_export($groupid);
		}
	}
	sendCCResponse(json_encode($response));
	exit;
}

function renamechatroom(){
	global $userid, $groupname, $groupid;

	$response = array();
	$response['success'] = false;
	if(!empty($groupid) && $userid>0){
		$sql = sql_getQuery('getChatroomById',array('id'=>$groupid));
		/* hook to get group details*/
		if(function_exists('hooks_getGroupByID')){
			$sql = hooks_getGroupByID(array('id'=>$groupid));
		}

		$query = sql_query($sql,array(),1);

		if($group = sql_fetch_assoc($query)){
			$owner = $group['createdby'];
			if($owner==0){
				/** Admin created group **/
				$response['error'] = 'You can not rename groups created by admin';
			}elseif(isOwner($userid,$groupid)){
				$query = sql_query('getGroupName', array('name' => $groupname));
				if(sql_num_rows($query) == 0){
					/**
						$options: Only for SQL server
						SQLSRV_CURSOR_FORWARD: Lets you move one row at a time starting at the first row of the result set until you reach the end of the result set.
					*/
					$options =  array( "Scrollable" => SQLSRV_CURSOR_FORWARD );
					$query = sql_query('renameGroup', array('name'=>$groupname,'id'=>$groupid), 0, $options);
					$affectedrow =  sql_affected_rows($query);
					if($affectedrow>0){
						$response['success'] = true;
						$response['acknowledgment'] = 'A group with groupid '.$groupid.' has been has been renamed to '.$groupname.'.';
					}else{
						$response['error'] = 'An error occurred while renaming group with groupid '.$groupid.' to '.$groupname.' in DB.';
					}
				}else{
					$response['error'] = 'Group with name '.$groupname.' already exists';
				}
			}else{
				$response['error'] = 'Only Owner of the group can rename the group';
			}
		}else{
			/** Group not found in DB **/
			$response['error'] = 'Group with id '.$groupid.' does not exists.';
		}
	}else{
		$response['error'] = 'Userid cannot be '.var_export($userid);
		if(empty($groupid)){
			$response['error'] = 'Groupid cannot be '.var_export($groupid);
		}
	}
	sendCCResponse(json_encode($response));
	exit;
}

function isOwner($userid,$groupid,$setOwner=false){
	global $response;
	if(!isset($_SESSION['cometchat'])){
		$_SESSION['cometchat'] = array();
	}
	if(!isset($_SESSION['cometchat']['group'])){
		$_SESSION['cometchat']['group'] = array();
	}
	if(!isset($_SESSION['cometchat']['group']['group_'.$groupid])){
		$_SESSION['cometchat']['group']['group_'.$groupid] = array();
	}
	if(!isset($_SESSION['cometchat']['group']['group_'.$groupid]['user_'.$userid])){
		$_SESSION['cometchat']['group']['group_'.$groupid]['user_'.$userid] = array();
	}
	if(!isset($_SESSION['cometchat']['group']['group_'.$groupid]['user_'.$userid]['isOwner'])){
		if($setOwner==false){
			$sql = sql_getQuery('getChatroomById',array('id'=>$groupid));
			/* hook to get group details*/
			if(function_exists('hooks_getGroupByID')){
				$sql = hooks_getGroupByID(array('id'=>$groupid));
			}
			$query = sql_query($sql,array(),1);
			if($room = sql_fetch_assoc($query)){
				$setOwner = ($room['createdby'] == $userid);
			}
		}
		$_SESSION['cometchat']['group']['group_'.$groupid]['user_'.$userid]['isOwner'] = $setOwner;
	}
	return $_SESSION['cometchat']['group']['group_'.$groupid]['user_'.$userid]['isOwner'];
}

function isModerator($userid){
	global $moderatorUserIDs;
	return in_array($userid, $moderatorUserIDs);
}

function hasModeratorAccess($userid,$groupid){
	return isOwner($userid,$groupid) || isModerator($userid);
}

function checkpassword(){
	global $userid, $groupid, $type, $password, $silent, $cookiePrefix, $channelprefix, $pushplatformsuffix, $callbackfn;

	$response = array();

	if($type==1 && empty($silent) && !in_array($groupid, getJoinedGroups($userid))){
		$response['s'] = 'REQUIRED_PASSWORD';
		sendCCResponse(json_encode($response));
		exit;
	}
	$query = sql_query('checkchatroombanneduser',array('userid'=>$userid, 'chatroomid'=>$groupid));
	if(sql_num_rows($query) == 1){
		$response['s'] = 'BANNED';
		sendCCResponse(json_encode($response));
		exit;
	}
	if($userid > 0){
		$sql = sql_getQuery('getChatroomById',array('id'=>$groupid));
		/* hook to get group details*/
		if(function_exists('hooks_getGroupByID')){
			$sql = hooks_getGroupByID(array('id'=>$groupid));
		}
		$query = sql_query($sql,array(),1);
		if($group = sql_fetch_assoc($query)){
			if(!in_array($groupid, getJoinedGroups($userid))&&!empty($group['password']) && (empty($password) || ($group['password'] != $password))){
				$response['s'] = 'INVALID_PASSWORD';
				sendCCResponse(json_encode($response));
				exit;
			}else{
				removeCache('chatrooms_users'.$groupid);
				removeCache('chatroom_list');

				$query = sql_query('deleteKickedMessage',array('chatroomid'=>$groupid, 'userid'=>$userid));
				$query = sql_query('unbanChatroomUser',array('chatroomid'=>$groupid, 'userid'=>$userid));
				$owner = false;
				if($group['createdby'] == $userid){
					$owner = true;
				}
				isOwner($userid, $groupid, $owner);
				$key = '';
				if(defined('KEY_A') && defined('KEY_B') && defined('KEY_C')){
					$key = KEY_A.KEY_B.KEY_C;
				}

				$response = array(
					's' => 'JOINED',
					'chatroomname' => $group['name'],
					'groupname' => $group['name'],
					'timestamp' => 0,
					'cometid' => md5('chatroom_'.$groupid.$key),
					'owner' => $owner,
					'userid' => $userid,
					'ismoderator' => isModerator($userid),
					'push_channel' => 'C_'.md5($channelprefix."CHATROOM_".$groupid.BASE_URL).getPlatformSuffix($pushplatformsuffix)
				);
				//Store chatroom name in session for push notifications
				$_SESSION['cometchat']['chatrooms'] = array(
					'_'.$groupid=>array(
						'id'=>$groupid,
						'name'=>$group['name']
					)
				);
				$_SESSION['cometchat']['chatroom']['n'] = $group['name'];
				$_SESSION['cometchat']['chatroom']['id'] = $groupid;
				updateToJoinedGroups($groupid);
				$messages = getChatroomData($groupid,0,10);
				if(!empty($messages)){
					$response['messages'] = array_reverse($messages);
				}

			}
		}
		sendCCResponse(json_encode($response));
	}
	exit;
}

function invite(){
	global $userid, $chatrooms_language, $language, $embed, $embedcss, $guestsMode, $basedata, $cookiePrefix, $chromeReorderFix, $hideOffline, $plugins, $firstguestID, $groupid, $password, $groupname, $force;
	$base_url = BASE_URL;

	$time = getTimeStamp();
	$invitedusers = array();
	$query = sql_query('getChatroomById',array('id'=>$groupid));

	if(defined('DEV_MODE') && DEV_MODE == '1'){ echo sql_error($GLOBALS['dbh']); }

	$result = sql_fetch_assoc($query);
	$chatroomType = $result['type'];
	if($chatroomType == 2){
		$invitedusers = array_filter(explode(',', $result['invitedusers']));
	}

	$query = sql_query('getchatroombannedusers',array('chatroomid'=>$groupid));

	if(defined('DEV_MODE') && DEV_MODE == '1'){ echo sql_error($GLOBALS['dbh']); }

	$result = sql_fetch_assoc($query);
	$bannedUsers = explode(',',$result['bannedusers']);

	$onlineCacheKey = 'all_online';
	if($userid > $firstguestID){
		$onlineCacheKey .= 'guest';
	}
	$role = getRole($userid);
	if(!empty($role)){
		$onlineCacheKey .= $role;
	}
	if(!is_array($buddyList = getCache($onlineCacheKey))|| ($force == 1)){
		$buddyList = array();
		$sql = getFriendsList($userid,$time);
		if($guestsMode){
			$sql = getGuestsList($userid,$time,$sql);
		}
		$query = sql_query($sql, array(), 1);

		if(defined('DEV_MODE') && DEV_MODE == '1'){ echo sql_error($GLOBALS['dbh']); }

		while ($chat = sql_fetch_assoc($query)){

			if(((($time-processTime($chat['lastactivity'])) < ONLINE_TIMEOUT) && $chat['status'] != 'invisible' && $chat['status'] != 'offline') || $chat['isdevice'] == 1){
				if($chat['status'] != 'busy' && $chat['status'] != 'away'){
					$chat['status'] = 'available';
				}
			}else{
				$chat['status'] = 'offline';
			}

			$avatar = getAvatar($chat['avatar']);

			if(!empty($chat['username'])){
				if(function_exists('processName')){
					$chat['username'] = processName($chat['username']);
				}

				if(!(in_array($chat['userid'],$bannedUsers)) && $chat['userid'] != $userid && ($hideOffline == 0||($hideOffline == 1 && $chat['status']!='offline'))){
					$buddyList[$chromeReorderFix.$chat['userid']] = array('id' => $chat['userid'], 'n' => $chat['username'], 'a' => $avatar, 's' => $chat['status']);
				}
			}
		}
	}

	if(DISPLAY_ALL_USERS == 0 && MEMCACHE <> 0 && !checkAuthMode('social')){
		$tempBuddyList = array();
		if(!is_array($friendIds = getCache('friend_ids_of_'.$userid))|| ($force == 1)){
			$friendIds=array();
			$sql = getFriendsIds($userid);
			$query = sql_query($sql, array(), 1);
			if(sql_num_rows($query) == 1){
				$buddy = sql_fetch_assoc($query);
				$friendIds = explode(',',$buddy['friendid']);
			}else {
				while($buddy = sql_fetch_assoc($query)){
					$friendIds[]=$buddy['friendid'];
				}
			}
			setCache('friend_ids_of_'.$userid,$friendIds, 30);
		}
		foreach($friendIds as $friendId){
			$friendId = $chromeReorderFix.$friendId;
			if(!empty($buddyList[$friendId])){
				$tempBuddyList[$friendId] = $buddyList[$friendId];
			}
		}
		$buddyList = $tempBuddyList;
	}

	if(function_exists('hooks_forcefriends') && is_array(hooks_forcefriends())){
		$buddyList = array_merge(hooks_forcefriends(),$buddyList);
	}

	$blockList = array();
	if(in_array('block',$plugins)){
		$blockedIds = getBlockedUserIDs();
		foreach ($blockedIds as $bid){
			array_push($blockList,$bid);
			if(isset($buddyList[$chromeReorderFix.$bid])){
				unset($buddyList[$chromeReorderFix.$bid]);
			}
		}
	}

	$chatroomUserList = getChatroomUserIDs($groupid);
	foreach ($chatroomUserList as $cid){
		if(isset($buddyList[$chromeReorderFix.$cid])){
			unset($buddyList[$chromeReorderFix.$cid]);
		}
	}

	$s['available'] = '';
	$s['away'] = '';
	$s['busy'] = '';
	$s['offline'] = '';
	foreach ($buddyList as $buddy){
		$invitedusers_class = '';
		$tooltip = '';
		if($buddy['id'] != $userid){
			if($chatroomType == 2 && count($invitedusers) > 0 && in_array($buddy['id'], $invitedusers)){
				$invitedusers_class = 'invitedusers';
				$tooltip = 'title="'.$chatrooms_language[73].'"';
			}
			$s[$buddy['s']] .= '
			<div class="invite_1">
			<div class="invite_2" '.$tooltip.' onclick="javascript:document.getElementById(\'check_'.$buddy['id'].'\').checked = document.getElementById(\'check_'.$buddy['id'].'\').checked?false:true;">
			<img class="useravatar" height=30 width=30 src="'.$buddy['a'].'" />
			</div>
			<div class="invite_3" onclick="javascript:document.getElementById(\'check_'.$buddy['id'].'\').checked = document.getElementById(\'check_'.$buddy['id'].'\').checked?false:true;">
			<span class="invite_name">
			'.$buddy['n'].'
			</span>
			<div class="cometchat_userscontentdot cometchat_margin_top cometchat_user_'.$buddy['s'].'">
			</div>
			<span class="invite_5">
			'.$language['status_'.$buddy['s']].'
			</span>
			</div>
			<label class="cometchat_checkboxcontrol cometchat_checkboxouter">
			<input class="invite_4 cometchat_checkbox" type="checkbox" name="invite[]" value="'.$buddy['id'].'" id="check_'.$buddy['id'].'" />
			<div class="cometchat_controlindicator">
			</div>
			</label>
			</div>';
		}
	}

	$inviteContent = '';
	$invitehide = '';
	$inviteContent = $s['available']."".$s['away']."".$s['offline'];
	if(empty($inviteContent)){
		$inviteContent = '<div class="lobby_noroom">'.$chatrooms_language['no_users_available'].'</div>';
		$invitehide = 'style="display:none;"';
	}
	generateUserlistForm('inviteusers',$inviteContent,$chatrooms_language['invite_users_title'],$chatrooms_language['invite_users_button'],$groupid,$password,$groupname);
	exit;
}

function inviteusers(){
	global $chatrooms_language, $close, $embedcss, $groupid, $groupname, $password, $encodedgroupname, $userstoinvite, $callbackfn;
	$base_url = BASE_URL;
	$response = array();
	$invitedusers = array();
	$response['success'] = false;
	$response['roomid'] = $groupid;

	if(!empty($userstoinvite)){
		$blockedIds = getBlockedUserIDs();
		$userstoinvite = array_diff($userstoinvite, $blockedIds);
		foreach ($userstoinvite as $user){
			$invitemessage = $chatrooms_language[18].' '.$groupname.' <a href="javascript:jqcc.cometchat.joinChatroom(\''.$groupid.'\',\''.$password.'\',\''.$encodedgroupname.'\')">'.$chatrooms_language[19].'</a>';
			$invitedusers[] = $user;
			$sentinfo = sendMessage($user,$invitemessage,1);
			addUsersToChatroom($groupid, $user);
			$processedMessage = $_SESSION['cometchat']['user']['n'].": ".$chatrooms_language['chatroom_invite']." ".$groupname;
			if($sentinfo != ''){
				pushMobileNotification($user,$sentinfo['id'],$processedMessage);
			}
		}
		if(!empty($invitedusers)){
			$response['invitedusers'] = $invitedusers;
			$response['success'] = true;
		}
	}
	if($callbackfn == 'mobileapp'){
		sendCCResponse(json_encode($response));
	}else{
		showSuccessfulInvitation($groupid,$chatrooms_language[18],$chatrooms_language[16]);
	}
}

function showSuccessfulInvitation($groupid,$title,$successtext){
	global $close;
	global $embedcss;
	$base_url = BASE_URL;
	$chatroomscsstag =  getDynamicScriptAndLinkTags(array('type' => "module",'name' => 'chatrooms', 'ext' => 'css'));
	echo <<<EOD
	<!DOCTYPE html>
	<html>
	<head>
	<title>{$title}</title>
	<meta name="viewport" content="user-scalable=0,width=device-width, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
	{$chatroomscsstag}
	<script type="text/javascript">
	function closePopup(name){
		var controlparameters = {
			type: 'modules',
			name: 'chatrooms',
			method: 'closeCCPopup',
			params: {
				name: 'invite',
				roomid: '{$groupid}'
			}
		};
		controlparameters = JSON.stringify(controlparameters);
		if(typeof(parent) != 'undefined' && parent != null && parent != self){
			parent.postMessage('CC^CONTROL_'+controlparameters,'*');
		}else{
			window.close();
		}
	}
	</script>
	<style>
	body{
		margin: 0px;
	}
	</style>
	</head>
	<body onload="{$close}">
	<div class="cometchat_wrapper">
	<div class="container_body container_body_layout {$embedcss}">
	{$successtext}
	<div style="clear:both"></div>
	</div>
	</div>
	</body>
	</html>
EOD;
}

function passwordBox(){

	global $chatrooms_language, $groupid, $groupname, $silent, $cc_layout, $noBar, $embedcss, $embed, $close;
	$base_url = BASE_URL;

	$options=" <input type=button id='passwordBox' class='invitebutton' value='$chatrooms_language[19]' /><input type=button id='close' class='invitebutton' onclick=$close value='$chatrooms_language[51]' />";
	$jqueryjstag = getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'jquery', 'ext' => 'js'));
	$scrolljstag = getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'scroll', 'ext' => 'js'));
	$chatroomscsstag = getDynamicScriptAndLinkTags(array('layout' => $cc_layout,'ext' => 'css'));

	echo <<<EOD
	<!DOCTYPE html>
	<html>
	<head>
	<title>{$name}</title>
	<meta name="viewport" content="user-scalable=0,width=device-width, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
	{$jqueryjstag}
	{$chatroomscsstag}
	<script> $ = jQuery = jqcc;	</script>
	{$scrolljstag}
	<script type="text/javascript">

	function closePopup(name){
		var controlparameters = {
			type: 'modules',
			name: 'chatrooms',
			method: 'closeCCPopup',
			params: {
				name: 'passwordBox'
			}
		};
		controlparameters = JSON.stringify(controlparameters);
		if(typeof(parent) != 'undefined' && parent != null && parent != self){
			parent.postMessage('CC^CONTROL_'+controlparameters,'*');
		}else{
			window.close();
		}
	}

	function cccheckPass(){
		password = jqcc('#chatroomPass').val();
		var controlparameters = {
			type: 'modules',
			name: 'cometchat',
			method: 'checkChatroomPass',
			params: {
				id: '{$groupid}',
				name: '{$groupname}',
				silent: '{$silent}',
				password: password,
				clicked: 1,
				encryptPass: 1,
				noBar: '{$noBar}'
			}
		};
		controlparameters = JSON.stringify(controlparameters);
		if(window.opener==null || window.opener==''){
			parent.postMessage('CC^CONTROL_'+controlparameters,'*');
		}else{
			window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');
		}
	}

	jqcc(function(){
		var controlparameters = {
			type: 'module',
			name: 'chatrooms',
			method: 'resizeCCPopup',
			params: {
				id: 'passwordBox',
				width: 110,
				height: 320
			}
		};
		controlparameters = JSON.stringify(controlparameters);
		if(typeof(window.opener) == null){
			window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');
		}else{
			parent.postMessage('CC^CONTROL_'+controlparameters,'*');
		}

		jqcc('#passwordBox').click(function(e){
			cccheckPass();
			{$close}
		});

		jqcc('#chatroomPass').keyup(function(e){
			if(e.keyCode == 13){
				cccheckPass();
				{$close}
			}
		});
	});
	</script>
	</head>
	<body>
	<div class="container passwordBox_container">
	<div class="container_title {$embedcss}">{$name}</div>
	<div style="overflow:hidden;" class="container_body {$embedcss}">
	<div class="passwordbox_body">{$chatrooms_language[8]}</div>
	<input style="width: 95%;margin-top: 8px;" id="chatroomPass" type="password" name="pwd" autofocus/>
	<div style="clear:both"></div>
	</div>
	<div align="right" class="cometchat_container_sub {$embedcss}">{$options}</div>
	</div>
	</body>
	</html>
EOD;
	exit;
}

function getJoinedGroups($userid, $force=false){
	$joinedgroupssessionkey = 'cometchat_joinedchatroomids';
	if(empty($_SESSION['cometchat'])){
		$_SESSION['cometchat'] = array();
	}
	if(!isset($_SESSION['cometchat'][$joinedgroupssessionkey])){
		$_SESSION['cometchat'][$joinedgroupssessionkey] = array();
	}
	if($force){
		$joinedgroups = array();
		$sql = sql_getQuery('getJoinedGroups',array('userid'=>$userid));
		/* hooks for joined group list*/
		if(function_exists('hooks_getGroupList')){
			$sql = hooks_getJoinedGroupList(array('userid'=>$userid));
		}
		$query = sql_query($sql,array(),1);
		if(defined('DEV_MODE') && DEV_MODE == '1'){ echo sql_error($GLOBALS['dbh']); }
		while ($group = sql_fetch_assoc($query)){
			array_push($joinedgroups, $group['id']);
		}
		$_SESSION['cometchat'][$joinedgroupssessionkey] = $joinedgroups;
	}
	return $_SESSION['cometchat'][$joinedgroupssessionkey];
};

function updateToJoinedGroups($groupid,$remove=false){
	$joinedgroupssessionkey = 'cometchat_joinedchatroomids';
	if(empty($_SESSION['cometchat'])){
		$_SESSION['cometchat'] = array();
	}
	if(!isset($_SESSION['cometchat'][$joinedgroupssessionkey])){
		$_SESSION['cometchat'][$joinedgroupssessionkey] = array();
	}
	$joinedgroups = $_SESSION['cometchat'][$joinedgroupssessionkey];
	$key = array_search($groupid,$joinedgroups);
	if($remove==true){
		if($key!==false){
			unset($joinedgroups[$key]);
		}
	}else{
		if($key===false){
			array_push($joinedgroups,$groupid);
		}
	}
	$_SESSION['cometchat'][$joinedgroupssessionkey] = $joinedgroups;

	removeCache('chatrooms_users'.$groupid);
	removeCache('chatroom_list');

	unset($_SESSION['cometchat']['cometchat_chatroom_'.$groupid]);
	unset($_SESSION['cometchat']['cometchat_chatroomslist']);
}

function leavechatroom(){
	global $userid, $cookiePrefix, $groupid, $kickflag, $banflag;

	if(empty($banflag)){
		$query = sql_query('leavechatroom',array('userid'=>$userid, 'chatroomid'=>$groupid));
		if(!empty($kickflag)){
			$query = sql_query('deleteKickedMessage',array('userid'=>$userid, 'chatroomid'=>$groupid));
		}
	}else{
		$query = sql_query('deleteBanUserMessage',array('userid'=>$userid, 'chatroomid'=>$groupid));
	}
	updateToJoinedGroups($groupid,true);
	sendCCResponse(json_encode($groupid));
	exit;
}

function kickUser(){
	global $cookiePrefix, $userid, $groupid, $kick, $useridtokick;
	$response =  array('success' => false);

	if(empty($kick)){
		$response['error'] =  'Kick parameter is empty';
	}elseif(!hasModeratorAccess($userid,$groupid)){
		$response['error'] =  'Only the owner or moderators can kick a user';
	}elseif(hasModeratorAccess($useridtokick,$groupid)){
		$response['error'] =  'The owner or moderators cannot be kicked';
	}else{
		$query = sql_query('kickUser',array('userid'=>$useridtokick, 'chatroomid'=>$groupid));
		$controlparameters = array(
			'type' => 'modules',
			'name' => 'chatroom',
			'method' => 'kicked',
			'params' => array(
				'id' => $useridtokick
			)
		);
		$controlparameters = json_encode($controlparameters);
		addUsersToChatroom($groupid, $useridtokick, 1);
		sendChatroomMessage($groupid,'CC^CONTROL_'.$controlparameters,0);
		removeCache('chatrooms_users'.$groupid);
		removeCache('chatroom_list');
		$response['acknowledgment'] =  'User with userid '.$useridtokick.' has been kicked successfully.';
		$response['success'] = true;
	}

	sendCCResponse(json_encode($response));
	exit;
}

function banUser(){
	global $cookiePrefix, $groupid, $userid, $useridtoban, $ban;
	$response =  array('success' => false);

	if(empty($ban)){
		$response['error'] =  'ban parameter is empty';
	}elseif(!hasModeratorAccess($userid,$groupid)){
		$response['error'] =  'Only the owner or moderators can ban a user';
	}elseif(hasModeratorAccess($useridtoban,$groupid)){
		$response['error'] =  'The owner or moderators cannot be banned';
	}else{
		$query = sql_query('banUser',array('userid'=>$useridtoban, 'chatroomid'=>$groupid));
		$controlparameters = array(
			'type' => 'modules',
			'name' => 'chatroom',
			'method' => 'banned',
			'params' => array(
				'id' => $useridtoban
			)
		);
		$controlparameters = json_encode($controlparameters);
		sendChatroomMessage($groupid,'CC^CONTROL_'.$controlparameters,0);
		removeCache('chatrooms_users'.$groupid);
		removeCache('chatroom_list');
		$response['acknowledgment'] =  'User with userid '.$useridtoban.' has been banned successfully.';
		$response['success'] = true;
	}
	sendCCResponse(json_encode($response));
	exit;
}

function unban(){
	global $userid, $groupid, $groupname, $password, $cc_layout, $chatrooms_language, $language, $embed, $embedcss, $guestsMode, $basedata, $chromeReorderFix, $callbackfn;
	$base_url = BASE_URL;

	$time = getTimeStamp();
	$bannedusers = array();
	$sql = sql_getQuery('unban',array('userid'=>$userid, 'chatroomid'=>$groupid));

	if($guestsMode){
		$sql = getChatroomBannedGuests($groupid,$time,$sql);
	}

	$query = sql_query($sql, array(), 1);

	if(defined('DEV_MODE') && DEV_MODE == '1'){ echo sql_error($GLOBALS['dbh']); }

	while ($chat = sql_fetch_assoc($query)){

		$avatar = getAvatar($chat['avatar']);

		if(!empty($chat['username'])){
			if(function_exists('processName')){
				$chat['username'] = processName($chat['username']);
			}

			$bannedusers[$chromeReorderFix.$chat['userid']] = array(
				'id' => $chat['userid'],
				'n' => $chat['username'],
				'a' => $avatar,
				's' => $chat['status']
			);
		}
	}
	if($callbackfn == 'mobileapp'){
		$response['unban'] = $bannedusers;
		echo json_encode($response);
		exit;
	}

	$s['count'] = '';

	foreach ($bannedusers as $user){
		$s['count'] .= '
		<div class="invite_1">
		<div class="invite_2" onclick="javascript:document.getElementById(\'check_'.$user['id'].'\').checked = document.getElementById(\'check_'.$user['id'].'\').checked?false:true;">
		<img class="useravatar" height=30 width=30 src="'.$user['a'].'" />
		</div>
		<div class="invite_3" onclick="javascript:document.getElementById(\'check_'.$user['id'].'\').checked = document.getElementById(\'check_'.$user['id'].'\').checked?false:true;">
		<span class="invite_name">
		'.$user['n'].'
		</span>
		<div style="margin: 4px 6px 0px 0px;" class="cometchat_userscontentdot cometchat_user_'.$user['s'].'">
		</div>
		<div class="cometchat_buddylist_status">
		'.$language['status_'.$user['s']].'
		</div>
		</div>
		<label class="cometchat_checkboxcontrol cometchat_checkboxouter">
		<input class="invite_4 cometchat_checkbox" type="checkbox" name="unban[]" value="'.$user['id'].'" id="check_'.$user['id'].'" />
		<div class="cometchat_controlindicator">
		</div>
		</label>
		</div>';

	}

	if($s['count'] == ''){
		$s['count'] = '<div class="lobby_noroom">'.$chatrooms_language['no_users_to_unban'].'</div>';
	}
	generateUserlistForm('unbanusers',$s['count'],$chatrooms_language['select_users'],$chatrooms_language['unban_users'],$groupid,$password,$groupname);
	exit;
}

function generateUserlistForm($action,$userlist,$title,$submittext,$groupid,$password,$groupname){
	global $embedcss, $embed, $basedata, $cc_layout, $encodedgroupname;

	$base_url = BASE_URL;
	$jqueryjstag = getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'jquery', 'ext' => 'js'));
	$scrolljstag 	= getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'scroll', 'ext' => 'js'));
	$chatroomscsstag = getDynamicScriptAndLinkTags(array('type' => "module",'name' => 'chatrooms','layout' => $cc_layout,'ext' => 'css'));

	echo <<<EOD
	<!DOCTYPE html>
	<html>
	<head>
	<title>{$title}</title>
	<meta name="viewport" content="user-scalable=0,width=device-width, height=device-height minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
	{$chatroomscsstag}
	{$jqueryjstag}
	{$scrolljstag}
	<style>
	body{
		margin:	0px;
	}
	</style>
	</head>
	<body>
	<form method="post" action="{$base_url}modules/chatrooms/chatrooms.php?action={$action}&embed={$embed}&basedata={$basedata}">
	<div class="cometchat_wrapper">
	<div class="container_body container_body_layout1 {$embedcss}">
	{$userlist}
	<div style="clear:both"></div>
	</div>
	<div class="cometchat_container_sub container_subbox {$embedcss}">
	<input type=submit value="{$submittext}" class="{$action}" disabled />
	</div>
	<script>
	jqcc(document).ready(function(){
		jqcc('body').css('overflow','hidden');
		var mobileDevice = navigator.userAgent.match(/ipad|ipod|iphone|android|windows ce|Windows Phone|blackberry|palm|symbian/i);
		if(mobileDevice){
			jqcc('.container_body').css('height',jqcc(window).height()-(jqcc('.container_title').outerHeight(true)+jqcc('.cometchat_container_sub').outerHeight(true)+30));
			jqcc('body').css({'overflow':'hidden','overflow-y':'auto'});
		}else{
			var contentheight = (window.innerHeight - jqcc('.cometchat_container_sub').outerHeight())+'px';
			if(jqcc().slimScroll){
				jqcc('.container_body ').css({'height': contentheight});
				jqcc('.container_body ').slimScroll({scroll: 0,height: contentheight});
			}
		}
		jqcc('.invite_1').click(function(){
			var checked = jqcc("input:checked").length;
			if(checked > 0){
				jqcc('.{$action}').attr("disabled", false);
			}else{
				jqcc('.{$action}').attr("disabled", true);
			}
		});
		if(jqcc(".invite_1").length == 0){

			jqcc('.container_body').css({'height':window.innerHeight-16+'px'});
			if(jqcc('.container_body').parent().hasClass('slimScrollDiv')){
				jqcc('.container_body').parent().height(window.innerHeight-16+'px');
			}
			jqcc('.container_subbox').hide();
		}
	});
	</script>
	</div>
	<input type="hidden" name="groupid" value="{$groupid}" />
	<input type="hidden" name="password" value="{$password}" />
	<input type="hidden" name="groupname" value="{$encodedgroupname}" />
	</form>
	</body>
	</html>
EOD;
}

function unbanusers(){
	global $chatrooms_language, $close, $embedcss, $userid, $callbackfn, $groupid, $userstounban,$encodedgroupname,$groupname,$password;

	$response = array();
	$response['success'] = false;
	$unbannedusers = array();
	if(hasModeratorAccess($userid,$groupid) && !empty($userstounban)){
		if(!is_array($userstounban)){
			$userstounban = explode(',',ltrim(rtrim($userstounban,']'),'['));
		}
		foreach ($userstounban as $user){
			$user = trim($user);
			if(!empty($user)){
				$query = sql_query('unbanusers',array('chatroomid'=>$groupid, 'userid'=>$user));
				$query = sql_query('deleteBanUserMessage',array('chatroomid'=>$groupid, 'userid'=>$user));
				addUsersToChatroom($groupid, $user);
				sendMessage($user,$chatrooms_language[18].$groupname. " <a href=\"javascript:jqcc.cometchat.joinChatroom('".$groupid."','".$password."','".$encodedgroupname."')\">".$chatrooms_language[19]."</a>",1);
				$unbannedusers[] = $user;
			}
		}
	}else{
		$response['error'] = 'Only group owner and moderators can unban other users';
		if(empty($userstounban)){
			$response['error'] = 'Users to unban can not be empty';
		}
	}

	if($callbackfn=='mobileapp'){
		if(!empty($unbannedusers)){
			$response['success'] = true;
			$response['unbannedusers'] = $unbannedusers;
		}
		sendCCResponse(json_encode($response));
	}elseif(!empty($unbannedusers)){
		showSuccessfulInvitation($groupid,$chatrooms_language[18],$chatrooms_language[16]);
	}
	exit;
}

function deleteChatroomMessage(){
	global $allowDelete, $groupid, $userid, $groupmessageidtodelete;

	$deleteflag = 0;
	$response = array('success'=>false);

	if(hasModeratorAccess($userid,$groupid)){
		$deleteflag = 1;
	}elseif(!empty($allowDelete)){
		$query = sql_query('getUserIdByChatroom',array('id'=>$groupmessageidtodelete));
		$row = sql_fetch_assoc($query);
		if($row['userid'] == $userid){
			$deleteflag = 1;
		}else{
			$response['error'] = 'You are not a sender of the message to delete it.';
		}
	}else{
		$response['error'] = 'Non-moderator users are not allowed to delete the messages';
	}

	if(!empty($deleteflag)){
		$query = sql_query('deleteGroupMessage',array('id'=>$groupmessageidtodelete));
		$affectedrow = sql_affected_rows($query);
		if($affectedrow > 0){
			$response['success'] = true;
			$response['acknowledgment'] = 'The group message with id '.$groupmessageidtodelete.' has been deleted successfully.';
			$controlparameters = array(
				'type' => 'modules',
				'name' => 'chatroom',
				'method' => 'deletemessage',
				'params' => array(
					'id' => $groupmessageidtodelete
				)
			);
			$controlparameters = json_encode($controlparameters);
			sendChatroomMessage($groupid,'CC^CONTROL_'.$controlparameters,0);
		}else{
			$response['error'] = 'An error occurred while deleting a group message with id '.$groupmessageidtodelete;
		}
	}
	sendCCResponse(json_encode($response));
	exit;
}

function addUsersToChatroom($groupid, $userid, $remove = 0){

	if(empty($groupid) || empty($userid)){ return; }

	$query = sql_query('getChatroomById',array('id'=>$groupid));
	if(defined('DEV_MODE') && DEV_MODE == '1'){ echo sql_error($GLOBALS['dbh']); }
	$row = sql_fetch_assoc($query);

	if($row['type'] != 2){ return; }

	$userList = array();
	$implodedUserList = '';
	$updateInvitedUsers = 0;
	$invitedusers = '';
	if(!empty($row['invitedusers'])){
		$invitedusers = $row['invitedusers'];
	}
	$userList = array_filter(explode(',', $invitedusers));
	if(!in_array($userid, $userList) && $remove == 0){
		$userList[] = $userid;
		$updateInvitedUsers = 1;
	}
	if($remove == 1){
		$key = array_search($userid,$userList);
		if($key!==false){
			unset($userList[$key]);
			$updateInvitedUsers = 1;
		}
	}
	if($updateInvitedUsers == 1){
		$implodedUserList = implode(',',$userList);
		$query = sql_query('addUsersToChatroom',array('id'=>$groupid, 'invitedusers'=>$implodedUserList));
		if(defined('DEV_MODE') && DEV_MODE == '1'){ echo sql_error($GLOBALS['dbh']); }
	}
}

function getChatroomUserIDs($groupid){
	$chatroomusers = array();
	$sql = sql_getQuery('getChatroomUserIDs',array('chatroomid'=>$groupid));
	/* hook to get group userids*/
	if(function_exists('hooks_getGroupUserIds')){
		$sql = hooks_getGroupUserIds(array('chatroomid'=>$groupid));
	}
	$query = sql_query($sql,array(),1);
	if(defined('DEV_MODE') && DEV_MODE == '1'){ echo sql_error($GLOBALS['dbh']); }

	while ($result = sql_fetch_assoc($query)){
		array_push($chatroomusers, $result['chatroomusers']);
	}
	return $chatroomusers;
}

$allowedActions = array(
	'banUser',
	'checkpassword',
	'createchatroom',
	'deletechatroom',
	'deleteChatroomMessage',
	'getChatroomDetails',
	'getChatroomName',
	'getchatroomusers',
	'heartbeat',
	'invite',
	'inviteusers',
	'kickUser',
	'leavechatroom',
	'passwordBox',
	'renamechatroom',
	'sendChatroomMessage',
	'unban',
	'unbanusers',
	'groupSyncMessages',
);

if(in_array($action,$allowedActions)){
	if($action=='getChatroomDetails' && !empty($groupid)){
		call_user_func($action, $groupid);
	}else{
		call_user_func($action);
	}
}
