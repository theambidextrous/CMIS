<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."modules.php");
include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."config.php");

if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php")){
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php");
}
if(!checkMembershipAccess('broadcastmessage','modules')){exit();}
function index() {
	$baseUrl = BASE_URL;
	global $userid;
	global $broadcastmessage_language;
	global $language;
	global $embed;
	global $embedcss;
	global $guestsMode;
	global $basedata;
	global $sleekScroller;
	global $inviteContent;
	if (!empty($_REQUEST['basedata'])) {
		$basedata = $_REQUEST['basedata'];
	}
	$layout = '';
	if(!empty($_REQUEST['cc_layout'])){
		$layout = $_REQUEST['cc_layout'];
	}
	$caller="";
	if(!empty($_REQUEST['caller'])){
		$caller = $_REQUEST['caller'];
	}
	$popoutmode = 0;
	if(empty($userid)){
		echo <<<EOD
		    <!DOCTYPE html>
			<html>
				<head></head>
				<body style="margin:0px">
					<div style="background: #FFF;font-family: Tahoma,Verdana,Arial,'Bitstream Vera Sans',sans-serif;font-size: 12px;padding: 10px;" class="cometchat_wrapper">
					$broadcastmessage_language[7]
					</div>
				<body>
			</html>
EOD;
		exit;
	}
	if(!empty($_GET['popoutmode'])){
		$popoutmode = 1;
	}

	userSelection(1);

	$embedcss = 'web';

	$extrajs = "";
	if ($sleekScroller == 1) {
		$extrajs = getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'scroll', 'ext' => 'js'));
	}

	if($popoutmode==1){
		$addmsg = 1;
	}else{
		$addmsg = 0;
	}
	$jQuerytag = getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'jquery', 'ext' => 'js'));
	$jstag = getDynamicScriptAndLinkTags(array('type' => "module",'name' => 'broadcastmessage','layout' => $layout, 'ext' => 'js'));
	$css = getDynamicScriptAndLinkTags(array('type' => "module",'name' => 'broadcastmessage','layout' => $layout,'ext' => 'css'));
echo <<<EOD
<!DOCTYPE html>
<html>
	<head>
		<title>{$broadcastmessage_language[100]}</title>
		<meta name="viewport" content="user-scalable=1,width=device-width, initial-scale=1.0" />
		<meta http-equiv="cache-control" content="no-cache">
		<meta http-equiv="pragma" content="no-cache">
		<meta http-equiv="expires" content="-1">
		<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
		$jQuerytag
		$jstag
		$css
		{$extrajs}
	</head>
	<body>
		<div style="background: #FFF;">
			<div class="cometchat_wrapper">
				<div>
					<div class="cometchat_broadcastMessage" >
						<div class="cc_broadcasttopbar" >
							<div id="cc_selectallusers" onclick="cc_selectallusers()">{$broadcastmessage_language[5]}</div>
							<div id="cc_deselectallusers" onclick="cc_deselectallusers()">{$broadcastmessage_language[6]}</div>
							<div class="cc_separator" style=" display: inline-block; margin-left: 3px;margin-right: 3px;"> | </div>
							<div id="cc_refreshbroadcastusers">{$broadcastmessage_language[1]}</div>
						</div>
						<div id="cometchat_broadcastsearchbar" style="display: block;">
							<div id="cometchat_searchbar_icon"></div>
							<input type="text" name="cometchat_broadcastsearch" class="cometchat_broadcastsearch" id="cometchat_broadcastsearch" placeholder="{$language[18]}">
						</div>
						<div class="inviteuserboxes" id="inviteuserboxes">
							{$inviteContent}
						</div>

						<div class="broadcastMessage_textarea_container" >
							<div class="cometchat_tabcontentinput">
									<textarea class="cometchat_textarea" addmsg="{$addmsg}" caller="{$caller}" id="cometchat_broadcastMessage_textarea" placeholder="{$broadcastmessage_language[11]}"></textarea>
							</div>
							</div>
							<div id="ccbroadcastsucc" class="ccbroadcastnotif">{$broadcastmessage_language[8]}</div>
							<div id="ccbroadcastuserrel" class="ccbroadcastnotif">{$broadcastmessage_language[10]}</div>
					</div>

				</div>
			</div>
		</div>
		<script type="text/javascript">
		var mobileDevice = navigator.userAgent.match(/ipad|ipod|iphone|android|windows ce|Windows Phone|blackberry|palm|symbian/i);
		var theme = "{$layout}";
		if(mobileDevice){
					if( theme == "docked"){
						$('#cometchat_broadcastMessage_textarea').css('width', '88%');
					}else if(theme == "embedded"){
						$('#cometchat_broadcastMessage_textarea').css('width', '84%');
					}
					$('#cometchat_broadcastMessage_textarea').after('<div id="cometchat_broadcastMessage_submit"></div>');
				}
	    </script>
	</body>
</html>
EOD;
}

function userSelection($silent = 0) {
	$baseUrl = BASE_URL;
	global $userid;
	global $broadcastmessage_language;
	global $language;
	global $embed;
	global $embedcss;
	global $guestsMode;
	global $basedata;
	global $sleekScroller;
	global $inviteContent;
	global $chromeReorderFix;
	global $hideOffline;
	global $plugins;
	global $firstguestID;
	$status['available'] = $language[30];
	$status['busy'] = $language[31];
	$status['offline'] = $language[32];
	$status['invisible'] = $language[33];
	$status['away'] = $language[34];
	$time = getTimeStamp();

	$onlineCacheKey = 'all_online';
	if($userid > $firstguestID){
		$onlineCacheKey .= 'guest';
	}
	$role = getRole($userid);
	if(!empty($role)){
		$onlineCacheKey .= $role;
	}
	if (!is_array($buddyList = getCache($onlineCacheKey))) {
		$buddyList = array();
		$sql = getFriendsList($userid,$time);
		if($guestsMode){
	    	$sql = getGuestsList($userid,$time,$sql);
		}
		$query = sql_query($sql, array(), 1);
		if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }
		while ($chat = sql_fetch_assoc($query)) {
			if (((($time-processTime($chat['lastactivity'])) < ONLINE_TIMEOUT) && $chat['status'] != 'invisible' && $chat['status'] != 'offline') || $chat['isdevice'] == 1) {
				if ($chat['status'] != 'busy' && $chat['status'] != 'away') {
					$chat['status'] = 'available';
				}
			} else {
				$chat['status'] = 'offline';
			}

			if ($chat['message'] == null) {
				$chat['message'] = $status[$chat['status']];
			}

			$avatar = getAvatar($chat['avatar']);

			if (!empty($chat['username'])) {
				if (function_exists('processName')) {
					$chat['username'] = processName($chat['username']);
				}
				if ($chat['userid'] != $userid && ($hideOffline == 0||($hideOffline == 1 && $chat['status']!='offline'))) {
					$buddyList[$chromeReorderFix.$chat['userid']] = array('id' => $chat['userid'], 'n' => $chat['username'], 'a' => $avatar, 's' => $chat['status'], 'm'=> $chat['message']);
				}
			}
		}
	}

	if (DISPLAY_ALL_USERS == 0 && MEMCACHE <> 0 && !checkAuthMode('social')) {
		$tempBuddyList = array();
		if (!is_array($friendIds = getCache('friend_ids_of_'.$userid))) {
			$friendIds=array();
			$sql = getFriendsIds($userid);
			$query = sql_query($sql, array(), 1);
			if(sql_num_rows($query) == 1 ){
				$buddy = sql_fetch_assoc($query);
				$friendIds = explode(',',$buddy['friendid']);
			}else{
				while($buddy = sql_fetch_assoc($query)){
					$friendIds[]=$buddy['friendid'];
				}
			}
			setCache('friend_ids_of_'.$userid,$friendIds, 30);
		}
		foreach($friendIds as $friendId) {
			$friendId = $chromeReorderFix.$friendId;
			if (isset($buddyList[$friendId])) {
				$tempBuddyList[$friendId] = $buddyList[$friendId];
			}
		}
		$buddyList = $tempBuddyList;
	}

	if (function_exists('hooks_forcefriends') && is_array(hooks_forcefriends())) {
		$buddyList = array_merge(hooks_forcefriends(),$buddyList);
	}

	$blockList = array();
	if (in_array('block',$plugins)) {

		$blockedIds = getBlockedUserIDs();

		foreach ($blockedIds as $bid) {
			array_push($blockList,$bid);
			if (isset($buddyList[$chromeReorderFix.$bid])) {
				unset($buddyList[$chromeReorderFix.$bid]);
			}
		}
	}

	if (isset($buddyList[$chromeReorderFix.$userid])) {
		unset($buddyList[$chromeReorderFix.$userid]);
	}

	if(empty($silent)){
		$buddyOrder = array();
		$buddyGroup = array();
		$buddyStatus = array();
		$buddyName = array();
		$buddyGuest = array();
		foreach ($buddyList as $key => $row) {
			if (empty($row['g'])) { $row['g'] = ''; }
			$buddyGroup[$key]  = strtolower($row['g']);
			$buddyStatus[$key] = strtolower($row['s']);
			$buddyName[$key] = strtolower($row['n']);
			if ($row['g'] == '') {
				$buddyOrder[$key] = 1;
			} else {
				$buddyOrder[$key] = 0;
			}
			$buddyGuest[$key] = 0;
			if ($row['id']>$firstguestID) {
				$buddyGuest[$key] = 1;
			}
		}
		array_multisort($buddyOrder, SORT_ASC, $buddyGroup, SORT_STRING, $buddyStatus, SORT_STRING, $buddyGuest, SORT_ASC, $buddyName, SORT_STRING, $buddyList);
		$response['buddyList'] = $buddyList;
		$response['status'] = $status;
	}else{
		$s['available'] = '';
		$s['away'] = '';
		$s['busy'] = '';
		$s['offline'] = '';
		foreach ($buddyList as $buddy) {
			$s[$buddy['s']] .= '<div class="invite_1"><div class="invite_2" onclick="javascript:document.getElementById(\'check_'.$buddy['id'].'\').checked = document.getElementById(\'check_'.$buddy['id'].'\').checked?false:true;"><img class="useravatar" height=30 width=30 src="'.$buddy['a'].'" /></div><div class="invite_3" onclick="javascript:document.getElementById(\'check_'.$buddy['id'].'\').checked = document.getElementById(\'check_'.$buddy['id'].'\').checked?false:true;"><span class="invite_name">'.$buddy['n'].'</span><div class="cometchat_userscontentdot cometchat_user_'.$buddy['s'].'"></div><span class="invite_5">'.$status[$buddy['s']].'</span></div><label class="cometchat_checkboxcontrol cometchat_checkboxouter"><input class="cometchat_checkbox" type="checkbox" name="to[]" value="'.$buddy['id'].'" id="check_'.$buddy['id'].'" class="invite_4" /><div class="cometchat_controlindicator"></div></label></div>';
		}

		$inviteContent = '';
		$invitehide = '';
		$inviteContent = $s['available']."".$s['away']."".$s['offline'];
		if(empty($inviteContent)) {
			$inviteContent = '<div style= "padding-top:6px">'.$broadcastmessage_language[2].'</div>';
			$invitehide = 'style="display:none;"';
		}
	}
	if(empty($silent)){
		header('content-type: application/json; charset=utf-8');
		echo $_GET['callback'].'('.json_encode($response).')';
	}else{
		return $inviteContent;
	}

}

function sendbroadcast() {
	global $userid;
	global $bannedUserIDs;
	global $bannedUserIPs;
	$broadcast = array();
	$broadcast_toids = array();
	if (!in_array($userid,$bannedUserIDs) && !in_array($_SERVER['REMOTE_ADDR'],$bannedUserIPs)) {
		if(empty($_REQUEST['broadcastData'])) {
			$message = $_REQUEST['message'];
			$broadcast_toids = explode(",",$_REQUEST['to']);
			$message = sanitize($_REQUEST['message']);
			for ($i=0; $i <sizeof($broadcast_toids) ; $i++) {
				array_push($broadcast, array(
						'to' => $broadcast_toids[$i],
						'message' => $message,
						'dir' => 0
					)
				);
			}
		}else {
			$broadcastData = $_REQUEST['broadcastData'];
			$broadcastData['message'] = sanitize($broadcastData['message']);
			foreach ($broadcastData as $key => $value) {
				if(is_array($value)) {
					array_push($broadcast, array(
							'to' => $value['id'],
							'message' => $broadcastData['message'],
							'dir' => 0,
							'localmessageid'=>$key
						)
					);
				}
			}
		}
		$_REQUEST['broadcast'] = 1;
		broadcastMessage($broadcast);
	}
}

$allowedActions = array('index','sendbroadcast','userSelection');
$action = 'index';

if (!empty($_GET['action'])) {
    $action = sql_real_escape_string($_GET['action']);
}

if (!empty($action) && in_array($action,$allowedActions)) {
	call_user_func($action);
}
