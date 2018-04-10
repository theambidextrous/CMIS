<?php

if(!empty($_POST['userid'])){
	$_POST['basedata'] = $_POST['userid'];
}
$route = '';
if(!empty($_GET['route'])){
	$route = trim($_GET['route']);
	$route =  stripslashes($route);
	$route = str_replace('/','',$route);
}

if (!empty($route)) {
	$_POST['action'] = $route;
}

include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."config.php");
include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."cometchat_init.php");

if(!empty($client) && file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."api.php")) {
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."api.php");
}

$userid = 0;
$toid = 0;
$isGroup = 0;
$apikeyvalue = null;
$username = null;
$avatarfile = null;
$avatarlink = null;
$username = null;
$password = null;
$newpassword = null;
$displayname = null;
$profilelink = null;
$group = null;
$friends = null;
$groupname='';
$grouptype='0';
$grouppassword='';
$groupid = null;
$user = '';
$params = '';
$id = null;

if($userid == 0){
	if(!empty($_REQUEST['userid'])){
		$userid = $_REQUEST['userid'];
	} elseif(!empty($_REQUEST['basedata']) && !empty($_REQUEST['action']) && $_REQUEST['action']!= 'removeuser') {
		$userid = getUserID();
	}
}

if(!empty($_FILES["Filedata"]) && $_FILES["Filedata"]['name'] != ''){
	$avatarfile = $_FILES["Filedata"];
}
if(!empty($_FILES["avatar"])&& $_FILES["avatar"]['name'] != ''){
	$avatarfile = $_FILES["avatar"];
}
if(!empty($_REQUEST['avatar']) && $_REQUEST['avatar']!=''){
	$avatarlink = $_REQUEST['avatar'];
}
if(!empty($_REQUEST['username'])){
	$username = $_REQUEST['username'];
}
if(!empty($_REQUEST['password'])){
	$password = $_REQUEST['password'];
}
if(!empty($_REQUEST['newpassword'])){
	$newpassword = $_REQUEST['newpassword'];
}
if(!empty($_REQUEST['displayname'])){
	$displayname = $_REQUEST['displayname'];
}
if(!empty($_REQUEST['link'])){
	$profilelink = $_REQUEST['link'];
}
if(!empty($_REQUEST['group'])){
	$group = $_REQUEST['group'];
}
if(!empty($_REQUEST['friends'])){
	$friends = $_REQUEST['friends'];
}
/*For backward compatibility*/
if(!empty($_REQUEST['chatroomname'])){
	$groupname = $_REQUEST['chatroomname'];
}
if(!empty($_REQUEST['chatroomtype'])){
	$grouptype = $_REQUEST['chatroomtype'];
}
if(!empty($_REQUEST['chatroompassword'])){
	$grouppassword = $_REQUEST['chatroompassword'];
}

/*New API for group*/
if(!empty($_REQUEST['groupname'])){
	$groupname = $_REQUEST['groupname'];
}
if(!empty($_REQUEST['grouptype'])){
	$grouptype = $_REQUEST['grouptype'];
}
if(!empty($_REQUEST['grouppassword'])){
	$grouppassword = $_REQUEST['grouppassword'];
}

if(!empty($_REQUEST['users'])){
	$users = $_REQUEST['users'];
}
if(!empty($_REQUEST['groupid'])){
	$groupid = $_REQUEST['groupid'];
}
if(!empty($_REQUEST['id'])){
	$id = $_REQUEST['id'];
}
if(!empty($_REQUEST['uid'])){
	$uid = $_REQUEST['uid'];
}
/*** Block User API ***/
if(!empty($_REQUEST['fromuserid'])){
	$fromuserid = $_REQUEST['fromuserid'];
}
if(!empty($_REQUEST['touserid'])){
	$touserid = $_REQUEST['touserid'];
}
if(!empty($_REQUEST['toid'])){
	$toid = $_REQUEST['toid'];
}
if(!empty($_REQUEST['isGroup'])){
	$isGroup = $_REQUEST['isGroup'];
}
/*** End Block User API ***/

$apikeyvalue = cometchat_getApi();
if(empty($apikeyvalue) && !empty($_REQUEST['api-key'])){
	$apikeyvalue = $_REQUEST['api-key'];
}

switch ($_REQUEST['action']) {

	case 'createuser':
		createuser($apikeyvalue, $username, $password, $displayname, $avatarfile, $avatarlink, $profilelink,$group);
		break;

	case 'getuserinfo':
		getuserInfo($apikeyvalue, $userid);
		break;

	case 'updateuser':
		updateuser($apikeyvalue, $userid, $username, $password, $newpassword, $displayname, $avatarfile, $avatarlink, $profilelink);
		break;

	case 'blockuser':
		blockuser($apikeyvalue, $fromuserid, $touserid);
		break;

	case 'unblockuser':
		unblockuser($apikeyvalue, $fromuserid, $touserid);
		break;

	case 'addfriend':
		addFriend($apikeyvalue, $userid, $friends);
		break;

	case 'removefriend':
		removeFriend($apikeyvalue, $userid, $friends);
		break;

	case 'getfriend':
		getfriend($apikeyvalue, $userid);
		break;

	case 'checkAPIKEY':
		checkAPIKEY($apikeyvalue);
		break;

	case 'checkpassword':
		checkpassword($apikeyvalue, $password);
		break;

	case 'authenticateUser':
		authenticateUser($apikeyvalue, $username, $password);
		break;

	case 'removeuser':
		removeuser($apikeyvalue, $userid);
		break;

	case 'createchatroom':
		createchatroom($apikeyvalue, $userid,$chatroomname,$chatroomtype,$chatroompassword);
		break;

	case 'creategroup':
		$params = array(
			'apikeyvalue' => $apikeyvalue,
		 	'groupname' => $groupname,
		  	'grouptype' => $grouptype,
		   	'grouppassword' => $grouppassword,
		    'groupid' => $groupid
		);
		creategroup($params);
		break;

	case 'checkgroup':
		checkgroup($groupname);
		break;

	case 'addgroupusers':
		if($id != null){
			$params = array(
				'apikeyvalue' => $apikeyvalue,
				'id' => $id,
				'users' => $users
			);
		}else{
			$params = array(
				'apikeyvalue' => $apikeyvalue,
				'groupid' => $groupid,
				'users' => $users
			);
		}
		addgroupusers($params);
		break;

	case 'removegroupusers':
		$params = array(
			'apikeyvalue' => $apikeyvalue,
			'groupid' => $groupid,
			'users' => $users
		);
		removegroupusers($params);
		break;

	case 'deletegroup':
		$params = array(
			'apikeyvalue' => $apikeyvalue,
			'groupid' => $groupid
		);
		deletegroup($params);
		break;

	case 'getpushnotificationchannels':
		$params = array(
			'apikeyvalue' => $apikeyvalue,
			'userid' => $userid,
			'uid' => $uid
		);
		getpushnotificationchannels($params);
		break;
	case 'createwhiteboard':
	$params = array('apikeyvalue' => $apikeyvalue, 'userid' => $userid, 'uid' => $uid,'toid' => $toid,'isGroup' => $isGroup);
	createwhiteboard($params);
	break;

	case 'createwriteboard':
		$params = array('apikeyvalue' => $apikeyvalue, 'uid' => $uid, 'toid' => $toid, 'isGroup' => $isGroup);
		createwriteboard($params);
		break;

	case 'getCredits':
		$credits = 0;
		if(method_exists($GLOBALS['integration'], 'getCredits')){
			$credits = $GLOBALS['integration']->getCredits();
		}
		sendCCResponse(json_encode(array('credits'=>$credits)));
		break;

	case 'getCreditsToDeduct':
		$creditsToDeduct = 0;
		if(method_exists($GLOBALS['integration'], 'getCreditsToDeduct')){
			$params =  array();
			if(!empty($_REQUEST['type'])){
				$params['type'] = $_REQUEST['type'];
			}
			if(!empty($_REQUEST['name'])){
				$params['name'] = $_REQUEST['name'];
			}
			if(!empty($_REQUEST['to'])){
				$params['to'] = $_REQUEST['to'];
			}
			if(!empty($_REQUEST['isGroup'])){
				$params['isGroup'] = $_REQUEST['isGroup'];
			}
			if(!empty($_REQUEST['creditsToDeduct'])){
				$params['creditsToDeduct'] = $_REQUEST['creditsToDeduct'];
			}
			$creditsToDeduct = $GLOBALS['integration']->getCreditsToDeduct($params);
		}
		sendCCResponse(json_encode(array('creditsinfo'=>$creditsToDeduct)));
		break;

	case 'deductCredits':
		$response = false;
		if(method_exists($GLOBALS['integration'], 'deductCredits')){
			$params =  array();
			if(!empty($_REQUEST['type'])){
				$params['type'] = $_REQUEST['type'];
			}
			if(!empty($_REQUEST['name'])){
				$params['name'] = $_REQUEST['name'];
			}
			if(!empty($_REQUEST['to'])){
				$params['to'] = $_REQUEST['to'];
			}
			if(!empty($_REQUEST['isGroup'])){
				$params['isGroup'] = $_REQUEST['isGroup'];
			}
			if(!empty($_REQUEST['creditsToDeduct'])){
				$params['creditsToDeduct'] = $_REQUEST['creditsToDeduct'];
			}
			$response = $GLOBALS['integration']->deductCredits($params);
		}
		sendCCResponse(json_encode($response));
		break;
	case 'getbotinfo':
		$response = false;
		global $chromeReorderFix;
		if (!empty($_REQUEST['botid'])) {
			$botList = getBotList();
			if (!empty($botList[$chromeReorderFix.$_REQUEST['botid']])) {
				$response['botdetails'] = $botList[$chromeReorderFix.$_REQUEST['botid']];
			}
		}
		sendCCResponse(json_encode($response));
		break;
	default:
		$response['error'] = "Invalid API";
		sendCCResponse(json_encode($response));
		exit;
}

/* FUNCTIONS */

function checkAPIKEY($keyvalue) {
	global $apikey;
	if(!empty($keyvalue) && !empty($apikey)) {
		if($apikey == $keyvalue) {
			return 1; // key verified
		}
		$msg = 'Incorrect API KEY.';
		$response = array('failed' => array('status' => '1011', 'message' => $msg));
		echo json_encode($response); exit; // Incorrect API KEY
	}
	$msg = 'Invalid API KEY.';
	$response = array('failed' => array('status' => '1010', 'message' => $msg));
	echo json_encode($response); exit; // Invalid API KEY
}

function checkpassword($apikeyvalue, $password) {
	global $userid;
	$status = 0; // invalid password or userid

	checkAPIKEY($apikeyvalue);

	if(empty($password)||empty($userid)) {
		return $status;
	} else {
		$password = md5($password);
	}

	if($query = sql_query('api_checkpassword',array('id'=>$userid))) {
		$result = sql_fetch_assoc($query);
		if($result['id'] == $userid && $result['password'] == $password) {
			$status = 1; // password authenticated
		}
	}
	return $status;
}

function createuser($apikeyvalue, $username, $password, $displayname, $avatarfile, $avatarlink, $profilelink, $group) {
	$msg = '';
	checkAPIKEY($apikeyvalue);

	if(!isset($profilelink)) {
		$profilelink = '';
	}

	if(!isset($username) || $username == "") {
		$msg = 'Invalid Username.';
		$response = array('failed' => array('status' => '1007', 'message' => $msg));
		echo json_encode($response); exit;
	}

	if(!isset($displayname) || $displayname == "") {
		$displayname = $username;
	}

	if(!isset($password) || $password == "") {
		$msg = 'Invalid Password.';
		$response = array('failed' => array('status' => '1009', 'message' => $msg));
		echo json_encode($response); exit;
	}

	$password_md5 = md5($password);

	$query = sql_query('api_getData',array('fetchfield'=>'*', 'value'=>$username, 'fieldname'=>'username'));

	if(sql_num_rows($query) > 0) {
		$user = sql_fetch_assoc($query);
		unset($user['password']);
		$msg = 'username already exists';
		$response = array('failed' => array('status' => '1001', 'message' => $msg, 'data' => $user));
		echo json_encode($response); exit;
	} else {
		if($query = sql_query('api_createuser',array('username'=>$username, 'password'=>$password_md5, 'displayname'=>$displayname, 'link'=>$profilelink, 'grp'=>$group))) {
			$userid = sql_insert_id('cometchat_users', 'userid');

			if(isset($avatarfile)) {

				$filename = '';
				$avatarlink = '';
				$isImage = false;

				$filename = preg_replace("/[^a-zA-Z0-9\. ]/", "", sql_real_escape_string($avatarfile['name']));
				$filename = str_replace(" ", "_",$filename);
				$path = pathinfo($filename);

				if(strtolower($path['extension']) == 'jpg' || strtolower($path['extension']) == 'jpeg' || strtolower($path['extension']) == 'png' || strtolower($path['extension']) == 'gif') {
					$isImage = true;
				}

				$md5filename = md5(str_replace(" ", "_",str_replace(".","",$filename))."cometchat".time());
				if ($isImage){
					$md5filename .= ".".strtolower($path['extension']);
					if (!empty($avatarfile) && is_uploaded_file($avatarfile['tmp_name'])) {
						if (move_uploaded_file($avatarfile['tmp_name'], dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'writable'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'avatars'.DIRECTORY_SEPARATOR. $md5filename)) {
							$avatarlink = $_SERVER['SERVER_NAME'].BASE_URL.'writable/images/avatars/'.$md5filename;
						}
					}
				}
			}

			if(isset($avatarlink) && $avatarlink != '') {
				if(!sql_query('api_updateuser',array('fieldname'=>'avatar', 'value'=>$avatarlink, 'userid'=>$userid))) {
					$msg = 'Failed to update avatar.';
					$response = array('failed' => array('status' => '1014', 'message' => $msg , 'userid' => $userid));
					echo json_encode($response); exit;
				} else {
					$msg = 'Avatar updated successfully!';
					$response = array('success' => array('status' => '1000', 'message' => $msg, 'userid' => $userid));
				}
			} elseif(isset($avatarlink) && $avatarlink == '') {
				$msg = 'Failed to update avatar.';
				$response = array('failed' => array('status' => '1014', 'message' => $msg, 'userid' => $userid));
				echo json_encode($response); exit;
			}

			$msg = 'User created successfully!';
			$response = array('success' => array('status' => '1000', 'message' => $msg, 'userid' => $userid));
			echo json_encode($response); exit;
		} else {
			$msg = 'Failed to create user.';
			$response = array('failed' => array('status' => '1016', 'message' => $msg));
			echo json_encode($response); exit;
		}
	}

}

function getuserInfo($apikeyvalue, $userid){
	$msg = '';
	$response = array();
	checkAPIKEY($apikeyvalue);

	$query = sql_query('api_getData',array('fetchfield'=>'*', 'value'=>$userid, 'fieldname'=>'userid'));
	$result = sql_fetch_assoc($query);
	if(sql_num_rows($query)>0){
		$response= array('userdetails'=>array('userid'=>$result['userid'],'username'=>$result['username'],'displayname'=>$result['displayname'],'password'=>$result['password'],'avatar'=>$result['avatar'],'link'=>$result['link'],'grp'=>$result['grp'],'friends'=>$result['friends']));
		echo json_encode($response); exit;
	}
	else{
		$msg = 'Invalid user ID';
		$response = array('failed' => array('status' => '1007', 'message' => $msg));
		echo json_encode($response); exit;
	}

}

function updateuser($apikeyvalue, $userid, $username, $password, $newpassword, $displayname, $avatarfile, $avatarlink, $profilelink) {
	$msg = '';
	$response = array();
	checkAPIKEY($apikeyvalue);
	$changed = 0;

	$query = sql_query('api_getData',array('fetchfield'=>'count(userid) as count', 'value'=>$userid, 'fieldname'=>'userid'));
	$result = sql_fetch_assoc($query);
	if(empty($result['count'])){
		$msg = 'Invalid user ID';
		$response = array('failed' => array('status' => '1007', 'message' => $msg));
		echo json_encode($response); exit;
	}
	if(!empty($userid)) {
		if(isset($newpassword) && $newpassword != '') {
			$password_md5 = md5($newpassword);
			$query = sql_query('api_updateuser',array('fieldname'=>'password', 'value'=>$password_md5, 'userid'=>$userid));
			if(!$query) {
				$msg = 'Failed to update password.';
				$response = array('failed' => array('status' => '1014', 'message' => $msg));
				echo json_encode($response); exit;
			} else {
				$msg = 'Password updated successfully!';
				$changed = 1;
			}
		}

		if(isset($username) && $username != '') {
			if(!sql_query('api_updateuser',array('fieldname'=>'username', 'value'=>$username, 'userid'=>$userid))) {
				$msg = 'Failed to update username. Invalid username or username already exists.';
				$response =  array('failed' => array('status' => '1014', 'message' => $msg));
				echo json_encode($response); exit;
			} else {
				$msg = 'username updated successfully!';
				$changed = 1;
			}
		}

		if(isset($displayname)) {
			if(!sql_query('api_updateuser',array('fieldname'=>'displayname', 'value'=>$displayname, 'userid'=>$userid))) {
				$msg = 'Failed to update displayname.';
				$response = array('failed' => array('status' => '1014', 'message' => $msg));
				echo json_encode($response); exit;
			} else {
				$msg = 'displayname updated successfully!';
				$changed = 1;
			}
		}

		if(isset($profilelink)) {
			if(!sql_query('api_updateuser',array('fieldname'=>'link', 'value'=>$profilelink, 'userid'=>$userid))) {
				$msg = 'Failed to update link.';
				$response = array('failed' => array('status' => '1014', 'message' => $msg));
				echo json_encode($response); exit;
			} else {
				$msg = 'Profile link updated successfully!';
				$changed = 1;
			}
		}

		if(!empty($avatarfile)) {
			$filename = '';
			$avatarlink = '';
			$isImage = false;

			$filename = preg_replace("/[^a-zA-Z0-9\. ]/", "", sql_real_escape_string($avatarfile['name']));
			$filename = str_replace(" ", "_",$filename);
			$path = pathinfo($filename);

			if(strtolower($path['extension']) == 'jpg' || strtolower($path['extension']) == 'jpeg' || strtolower($path['extension']) == 'png' || strtolower($path['extension']) == 'gif') {
				$isImage = true;
			}

			$md5filename = md5(str_replace(" ", "_",str_replace(".","",$filename))."cometchat".time());
			if ($isImage){

				$md5filename .= ".".strtolower($path['extension']);
				if (!empty($avatarfile) && is_uploaded_file($avatarfile['tmp_name'])) {
					if (move_uploaded_file($avatarfile['tmp_name'],dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'writable'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'avatars'.DIRECTORY_SEPARATOR. $md5filename)) {
						$avatarlink = $_SERVER['SERVER_NAME'].BASE_URL.'writable/images/avatars/'.$md5filename;
					}
				}
			}
		}

		if(isset($avatarlink) && $avatarlink != '') {
			if(!sql_query('api_updateuser',array('fieldname'=>'avatar', 'value'=>$avatarlink, 'userid'=>$userid))) {
				$msg = 'Failed to update avatar.';
				$response = array('failed' => array('status' => '1014', 'message' => $msg));
				echo json_encode($response); exit;
			} else {
				$msg = 'Avatar updated successfully!';
				$changed = 1;
			}
		} elseif(isset($avatarlink) && $avatarlink == '') {
			$msg = 'Failed to update avatar.';
			$response = array('failed' => array('status' => '1014', 'message' => $msg));
			echo json_encode($response); exit;
		}
	} else {
		$msg = 'Failed to update details.';
		$response = array('failed' => array('status' => '1016', 'message' => $msg));
		echo json_encode($response); exit;
	}

	if($changed == 1){
		$msg = 'Details updated successfully!';
		$response = array('success' => array('status' => '1000', 'message' => $msg));
		echo json_encode($response); exit;
	}else{
		$msg = 'Failed to update details.';
		$response = array('failed' => array('status' => '1016', 'message' => $msg));
		echo json_encode($response); exit;
	}
}

/**
 * blockuser success response if user blocked
 * @params  apikeyvalue, fromuserid and touserid
 * @return json object
*/
function blockuser($apikeyvalue, $fromuserid, $touserid) {
	$msg = '';
	$response = array();
	checkAPIKEY($apikeyvalue);
	$changed = 0;
	if(!empty($fromuserid) && !empty($touserid)) {
		$query = sql_query('blockUser',array('fromid'=>$fromuserid, 'toid'=>$touserid));
		$error = sql_error($GLOBALS['dbh']);
		if (!empty($error)) {
			header('content-type: application/json; charset=utf-8');
			$response['error'] = sql_error($GLOBALS['dbh']);
			$response = array('failed' => array('status' => '1014', 'message' => "Failed to blocked user."));
		}else{
			$response = array('success' => array('status' => '1000', 'message' => "User blocked successfully."));
		}

	}else {
		$response['failed'] = array('status' => '1001','message' => 'Invalid fromuserid or touserid.');
	}
	echo json_encode($response); exit;
}

/**
 * unblockuser success response if user blocked
 * @params  apikeyvalue, fromuserid and touserid
 * @return json object
*/
function unblockuser($apikeyvalue, $fromuserid, $touserid) {
	$msg = '';
	$response = array();
	checkAPIKEY($apikeyvalue);
	$changed = 0;
	if(!empty($fromuserid) && !empty($touserid)) {
		$query = sql_query('unblockUser',array('toid'=>$touserid, 'fromid'=>$fromuserid));
		$error = sql_error($GLOBALS['dbh']);
		if (!empty($error)) {
			header('content-type: application/json; charset=utf-8');
			$response['error'] = sql_error($GLOBALS['dbh']);
			$response = array('failed' => array('status' => '1014', 'message' => "Failed to unblocked user."));
		}else{
			$response = array('success' => array('status' => '1000', 'message' => "User unblocked successfully."));
		}

	}else {
		$response['failed'] = array('status' => '1001','message' => 'Invalid fromuserid or touserid.');
	}
	echo json_encode($response); exit;
}

function addfriend($apikeyvalue, $userid, $friends) {
	$msg = '';
	$response = array();
	checkAPIKEY($apikeyvalue);

	if(!empty($userid) && !empty($friends)) {
		if(!is_array($friends)) {
			$friends = trim($friends);
			if(strpos($friends,'[') !== false){
				$friends = substr($friends, 1, -1);
			}elseif (strpos($friends,'(') !== false) {
				$friends = substr($friends, 1, -1);
			}
			$friends = explode(',',$friends);
		}
		$final_friends_list = array();
		$db_friend_list = array();
		$added_friends = array();

		$result = sql_query('api_getData',array('fetchfield'=>'count(userid) as count', 'value'=>$userid, 'fieldname'=>'userid'));
		$fromuser = sql_fetch_assoc($result);
		if(!empty($fromuser['count'])){
			foreach ($friends as $to) {
				if(!empty($to)) {
					if($userid != $to) {
						$column_name = is_numeric($to)?'userid':'username';
						$result = sql_query('api_getData',array('fetchfield'=>'count(userid) as count', 'value'=>$to, 'fieldname'=>$column_name));
						$touser = sql_fetch_assoc($result);
						if(!empty($touser['count'])) {
							$result = sql_query('api_getData',array('fetchfield'=>'friends', 'value'=>$userid, 'fieldname'=>'userid'));
							$db_friend_list = sql_fetch_assoc($result);
							if(!empty($db_friend_list['friends'])){
								$db_friend_array = explode(",",$db_friend_list['friends']);
								/*check if already friends*/
								if(!in_array($to,$db_friend_array)) {
									$added_friends[] = $to;
								}else{
									$failed_id[] = $to;
								}
							}else {
								$added_friends[] = $to;
							}
						}else {
							$failed_id[] = $to;
						}
					}else{
						$failed_id[] = $userid;
					}
				}
			}

			if(!empty($added_friends)) {
				$final_friends_list = !empty($db_friend_array)?array_merge($db_friend_array,$added_friends):$added_friends;
				$friends_list = implode(",",$final_friends_list);

				$list = implode(',',$added_friends);
				sql_query('api_updateuser',array('fieldname'=>'friends', 'value'=>$friends_list, 'userid'=>$userid));
				$msg = 'Friends added successfully!';
				$response = array('success' => array('status' => '1000', 'message' => $msg,'data' => array($column_name => $list)));
				echo json_encode($response); exit;
			}

			if(!empty($failed_id)){
				$list = implode(",",$failed_id);
				$msg = 'Failed to add friend.';
				$response = array('failed' => array('status' => '1006', 'message' => $msg));
				echo json_encode($response); exit;
			}
		}else{
			$msg = 'Invalid user ID';
			$response = array('failed' => array('status' => '1007', 'message' => $msg));
			echo json_encode($response); exit;
		}
	} else {
		$msg = 'Invalid input';
		$response = array('failed' => array('status' => '1005', 'message' => $msg));
		echo json_encode($response); exit;
	}
	echo json_encode($response); exit;
}

function removefriend($apikeyvalue, $userid, $friends) {
	$msg = '';
	$response = array();
	checkAPIKEY($apikeyvalue);

	if(!empty($userid) && !empty($friends)) {
		if(!is_array($friends)) {
			$friends = trim($friends);
			if(strpos($friends,'[') !== false){
				$friends = substr($friends, 1, -1);
			}elseif (strpos($friends,'(') !== false) {
				$friends = substr($friends, 1, -1);
			}
			$friends = explode(',',$friends);
		}
		$friends_list = '';
		/*logged in user does not exist*/
		$result = sql_query('api_getData',array('fetchfield'=>'count(userid) as count', 'value'=>$userid, 'fieldname'=>'userid'));
		$user = sql_fetch_assoc($result);
		if(!empty($user['count'])){
			$result = sql_query('api_getData',array('fetchfield'=>'friends', 'value'=>$userid, 'fieldname'=>'userid'));
			$friends_id = sql_fetch_assoc($result);
			if(!empty($friends_id['friends'])){
				$db_friend_list = explode(",",$friends_id['friends']);
				foreach ($friends as $to) {
					if(!empty($to)) {
						$column_name = is_numeric($to)?'id':'username';
						/*check if user is a friends*/
						if (($key = array_search($to, $db_friend_list)) !== false) {
							$removed_friends[] = $to;
							unset($db_friend_list[$key]);
						}else{
							$not_friends[] = $to;
						}
					}
				}
				if(!empty($db_friend_list)) {
					$friends_list = implode(",",$db_friend_list);
				}
			}

			if(!empty($removed_friends)) {
				$list = implode(',',$db_friend_list);
				sql_query('api_updateuser',array('fieldname'=>'friends', 'value'=>$friends_list, 'userid'=>$userid));
				$msg = 'Friends removed successfully!';
				$removed_friends = implode(',',$removed_friends);
				$response = array('success' => array('status' => '1000', 'message' => $msg,'data' => array($column_name => $removed_friends)));
			}else {
				if(!empty($not_friends)) {
					$list = implode(',',$not_friends);
					$msg = 'Failed to remove friends!';
					$response = array('failed' => array('status' => '1002', 'message' => $msg));
					echo json_encode($response); exit;

				}else{
					$msg = 'Failed to remove friends!';
					$response = array('failed' => array('status' => '1002', 'message' => $msg));
					echo json_encode($response); exit;
				}
			}
			echo json_encode($response); exit;
		}else{
			$response['failed'] = array('status' => '1007','message' => 'Invalid user ID');
			$msg = 'Invalid user ID';
			$response = array('failed' => array('status' => '1002', 'message' => $msg));
			echo json_encode($response); exit;
		}
	} else {
		$msg = 'Invalid input';
		$response = array('failed' => array('status' => '1005', 'message' => $msg));
	}
	echo json_encode($response); exit;
}


function getfriend($apikeyvalue, $userid) {
	$msg = '';

	checkAPIKEY($apikeyvalue);

	if(!empty($userid)) {
		/*logged in user does not exist*/
		$result = sql_query('api_getData',array('fetchfield'=>'username', 'value'=>$userid, 'fieldname'=>'userid'));
		$user = sql_fetch_assoc($result);
		if(!empty($user)) {
			if($result = sql_query('api_getData',array('fetchfield'=>'friends', 'value'=>$userid, 'fieldname'=>'userid'))) {
				$db_friend_list = sql_fetch_assoc($result);
				$friend_list = $db_friend_list['friends'];
				$msg = 'Friend list fetched successfully!';
				$response = array('success' => array('status' => '1000', 'message' => $msg, 'data' => $friend_list));
				echo json_encode($response); exit;
			} else {
				$msg = 'Error fetching friends';
				$response = array('failed' => array('status' => '1007', 'message' => $msg));
				echo json_encode($response); exit;
			}
		} else {
			$msg = 'Invalid user ID';
			$response = array('failed' => array('status' => '1007', 'message' => $msg));
			echo json_encode($response); exit;
		}
	} else {
		$msg = 'Invalid input';
		$response = array('failed' => array('status' => '1005', 'message' => $msg));
		echo json_encode($response); exit;
	}
}

function authenticateUser($apikeyvalue, $username, $password) {
	$msg = '';

	checkAPIKEY($apikeyvalue);

	if(isset($password) && $password != '') {
		$password = md5($password);
	} else {
		$msg = 'Invalid Password.';
		$response = array('failed' => array('status' => '1009', 'message' => $msg));
		echo json_encode($response); exit;
	}

	if(!isset($username) || $username == '') {
		$msg = 'Invalid username.';
		$response = array('failed' => array('status' => '1007', 'message' => $msg));
		echo json_encode($response); exit;
	}

	if(!empty($username) && !empty($password)) {
		if($query = sql_query('api_authenticateUser',array('username'=>$username, 'password'=>$password))) {
			$result = sql_fetch_assoc($query);
			if(sql_num_rows($query)> 0 && $result['userid'] > 0) {
				$msg = 'Login successfull';
				$userid = $result['userid'];
				$response = array('success' => array('status' => '1000', 'message' => $msg, 'userid' => $userid));
				echo json_encode($response); exit;
			} else {
				$msg = 'Incorrect username/password combination.';
				$response = array('failed' => array('status' => '1017', 'message' => $msg));
				echo json_encode($response, JSON_UNESCAPED_SLASHES); exit;
			}
		} else {
			$msg = 'Error occurred. Please try again.';
			$response = array('failed' => array('status' => '1012', 'message' => $msg));
			echo json_encode($response); exit;
		}
	} else {
		$msg = 'Invalid input';
		$response = array('failed' => array('status' => '1005', 'message' => $msg));
		echo json_encode($response); exit;
	}
}

function removeuser($apikeyvalue, $userid) {
	$msg = '';

	checkAPIKEY($apikeyvalue);

	if(!empty($userid)) {
		$result = sql_query('api_getData',array('fetchfield'=>'username', 'value'=>$userid, 'fieldname'=>'userid'));
		$user = sql_fetch_assoc($result);
		if(!empty($user)) {
			if(sql_query('api_removeuser',array('userid'=>$userid))) {
				$msg = 'User removed successfully!';
				$response = array('success' => array('status' => '1000', 'message' => $msg));
				echo json_encode($response); exit;
			} else {
				$msg = 'Remove user failed';
				$response = array('failed' => array('status' => '1007', 'message' => $msg));
				echo json_encode($response); exit;
			}
		} else {
			$msg = 'Invalid user ID';
			$response = array('failed' => array('status' => '1007', 'message' => $msg));
			echo json_encode($response); exit;
		}
	} else {
		$msg = 'Invalid input';
		$response = array('failed' => array('status' => '1005', 'message' => $msg));
		echo json_encode($response); exit;
	}
}

function checkgroup($groupname){
	if (!empty($groupname)) {
		$result = sql_query('check_group',array('groupname'=>$groupname));
		if($row = sql_fetch_assoc($result)){
			$response = array('success' => array('status' => '1000', 'message' => "Group ".$groupname.' already exists'), 'guid' => $row['guid'],'groupname' => $groupname);
		}else{
			$response = array('failed' => array('status' => '1007', 'message' => "Group ".$groupname.' does not exists'),'groupname' => $groupname);
		}
	} else{
		$response = array('failed' => array('status' => '1005', 'message' => "Invalid input"));
	}
	echo json_encode($response);
	exit;
}

function createchatroom($apikeyvalue,$userid,$chatroomname,$chatroomtype,$chatroompassword){
	$msg = '';

	checkAPIKEY($apikeyvalue);

	if(!empty($chatroomtype)){
		if($chatroomtype==1 && empty($chatroompassword)){
			$msg = 'Password required for password protected room';
			$response = array('failed' => array('status' => '1007', 'message' => $msg));
			echo json_encode($response); exit;
		}
	}else if($chatroomtype=0 && empty($chatroomname)){
		$msg = 'Chatroom name or type required: Type list 0: Public, 1:Password Protected, 2: Invitation only';
		$response = array('failed' => array('status' => '1007', 'message' => $msg));
		echo json_encode($response); exit;
	}

	$name = str_replace("%27","'", $chatroomname);

	$query = sql_query('api_getData',array('fetchfield'=>'name', 'value'=>$name, 'fieldname'=>'name'));

	if(sql_num_rows($query) == 0) {
		if ($userid > 0) {
			$time = getTimeStamp();
			if (!empty($chatroompassword)) {
				$chatroompassword = sha1($password);
			} else {
				$chatroompassword = '';
			}

			$query = sql_query('api_creategroup',array('name'=>sanitize_core($name), 'createdby'=>$userid, 'lastactivity'=>getTimeStamp(), 'password'=>sanitize_core($chatroompassword), 'type'=>sanitize_core($chatroomtype)));
			$currentroom = sql_insert_id('cometchat_chatrooms');

			$query = sql_query('api_creategroupuser',array('userid'=>$userid, 'chatroomid'=>$currentroom));
			$response = array('success' => array('status'=>'1000','chatroomid' => $currentroom, 'message' => 'chatroom created'));
			echo json_encode($response);
		}else{
			$msg = 'Please login to create chatroom';
			$response = array('failed' => array('status' => '1007', 'message' => $msg));
			echo json_encode($response); exit;
		}
	} else {
		$msg = 'Room already exist';
		$response = array('failed' => array('status' => '1007', 'message' => $msg));
		echo json_encode($response); exit;
	}
}

function creategroup($params){
	/*
	* It will allow users who has the API key to create the group.
	* Params:
	* apikey: Mendatory parameter, Unique API key that client can obtain from CometChat admin panel
	* grouid: Mendatory parameter, unique group ID
	* groupname: Mendatory parameter, Unique group name
	* grouptype: 0-> Public group, 1-> Password protected group, 2-> Invitation only group
	* grouppassword: Optional parameter, If grouptype is 1 then it's mendatory
	* Upon execution will show success/failed response and will create a new group
	*/
	$msg = '';

	checkAPIKEY($params['apikeyvalue']);	//Verify the API key
	if(!empty($params['groupid'])){
		if(empty($params['groupname'])){
			$msg = 'Group name is not defined';
			$response = array('failed' => array('status' => '1007', 'message' => $msg));
			echo json_encode($response); exit;
		}
		if(!empty($params['grouptype'])){
			if($params['grouptype']==1 && empty($params['grouppassword'])){
				$msg = 'Password required for password protected room';
				$response = array('failed' => array('status' => '1007', 'message' => $msg));
				echo json_encode($response); exit;
			}
		}else if($params['grouptype']==0 && empty($params['groupname'])){
			$msg = 'Group name or type required: Type list 0: Public, 1:Password Protected, 2: Invitation only';
			$response = array('failed' => array('status' => '1007', 'message' => $msg));
			echo json_encode($response); exit;
		}

		$name = str_replace("%27","'", $params['groupname']);
		$query = sql_query('getGroup', array('guid'=>$params['groupid']));
		/* check if group exists*/
		if(sql_num_rows($query) == 0) {
			$time = getTimeStamp();
			if (!empty($params['grouppassword'])) {
				$params['grouppassword'] = sha1($params['grouppassword']);
			} else {
				$params['grouppassword'] = '';
			}

			$query = sql_query('api_creategroup', array('name'=>sanitize_core($name), 'createdby'=>'0', 'lastactivity'=>getTimeStamp(), 'password'=>sanitize_core($params['grouppassword']), 'type'=>sanitize_core($params['grouptype']), 'guid'=>sanitize_core($params['groupid'])));
			$currentroom = sql_insert_id('cometchat_chatrooms');

			$response = array('success' => array('status'=>'1000','roomid' => $currentroom, 'message' => 'Group created', 'guid'=>$params['groupid']));
			echo json_encode($response);
		} else {
			$msg = 'guid is not unique';
			$response = array('failed' => array('status' => '1007', 'message' => $msg));
			echo json_encode($response); exit;
		}
	}else{
		$msg = 'guid is not defined';
		$response = array('failed' => array('status' => '1007', 'message' => $msg));
		echo json_encode($response); exit;

	}
}

function deletegroup($params){
	/*
	* Users can delete the group who has the API key.
	* Params:
	* apikey: Mendatory parameter, Unique API key that client can obtain from CometChat admin panel
	* grouid: Mendatory parameter, unique group ID
	* Upon execution will show success/failed response and will delete a group
	*/
	$msg = '';

	checkAPIKEY($params['apikeyvalue']); 	//Verify the API key

	if(empty($params['groupid'])){
		$msg = 'Group GUID or type required: Type list 0: Public, 1:Password Protected, 2: Invitation only';
		$response = array('failed' => array('status' => '1007', 'message' => $msg));
		echo json_encode($response); exit;
	}

	$query = sql_query('getGroupId', array('guid'=>$params['groupid']));
	/* check if group exists*/
	if(sql_num_rows($query) > 0) {
		$result = sql_fetch_assoc($query);
		$currentroom = $result['id'];
		$time = getTimeStamp();
		$query = sql_query('deleteGroupApi', array('guid'=>$params['groupid']));
		$query = sql_query('deleteGroupUserApi', array('chatroomid'=>$currentroom));
		$response = array('success' => array('status'=>'1000', 'message' => 'group deleted'));
		echo json_encode($response);
	} else {
		$msg = 'Group does not exist';
		$response = array('failed' => array('status' => '1007', 'message' => $msg));
		echo json_encode($response); exit;
	}
}

function addgroupusers($params){

	/*
	* Client can add users to specific group.
	* Params:
	* apikey: Mendatory parameter, Unique API key that client can obtain from CometChat admin panel
	* grouid: Mendatory parameter, unique group ID
	* users: Mendatory parameter, array or JSON of users(can be username or userid)
	* Upon execution will show success/failed response and will add the users into specific group
	*/
	$msg = '';
	$response = array();
	checkAPIKEY($params['apikeyvalue']);	// Verify the API key

	if((!empty($params['id']) || !empty($params['groupid'])) && !empty($params['users'])) {
		if(!is_array($params['users'])) {
			$params['users'] = trim($params['users']);
			if(strpos($params['users'],'[') !== false){
				$params['users'] = substr($params['users'], 1, -1);
			}elseif (strpos($params['users'],'(') !== false) {
				$params['users'] = substr($params['users'], 1, -1);
			}
			$params['users'] = explode(',',$params['users']);
		}
		$addedUsers = array();
		$failedUsers = array();
		if(empty($params['id'])){
			$query = sql_query('getGroupId', array('guid'=>$params['groupid'])); /* check if group exists*/
			$result = sql_fetch_assoc($query);
			$roomId = $result['id'];
		}else{
			$roomId = $params['id'];
		}
		if(!empty($roomId)){
			foreach ($params['users'] as $to) {
				if(!empty($to)) {
					$columnName = is_numeric($to)?DB_USERTABLE_USERID:DB_USERTABLE_NAME;
					$result = sql_query('checkUserExists', array('field'=>$columnName, 'value'=>$to)); /*check whether the requested users exists or not */
					$touser = sql_fetch_assoc($result);
					if(!empty($touser['userid'])) {
						$result = sql_query('checkGroupUserExists', array('chatroomid'=>$roomId, 'userid'=>$touser['userid']));
						$dbUserArray = sql_fetch_assoc($result);
						if(!empty($dbUserArray['userid'])){
							/*check if user already in group*/
							$failedUsers[] = $to;

						}else {
							/*Users to be added in group*/
							$addedUsers[] = $to;
						}
					}else {
						$failedUsers[] = $to;
					}
				}
			}
			if(!empty($addedUsers)) {
				$list = implode(',',$addedUsers);
				foreach($addedUsers as $user){
					sql_query('addGroupUser', array('chatroomid'=>$roomId, 'userid'=>$user));
				}
				$msg = 'Users added successfully in group';
				$response = array('success' => array('status' => '1000', 'message' => $msg,'data' => array($columnName => $list)));
				echo json_encode($response); exit;
			}
			if(!empty($failedUsers)){

				$list = implode(",",$failedUsers);
				$msg = 'Failed to add users.';
				$response = array('failed' => array('status' => '1006', 'message' => $msg,'data' => array($columnName => $list)));
				echo json_encode($response); exit;
			}
		}else{
			$msg = 'Invalid guid';
			$response = array('failed' => array('status' => '1007', 'message' => $msg));
			echo json_encode($response); exit;
		}
	} else {
		$msg = 'Invalid input';
		$response = array('failed' => array('status' => '1005', 'message' => $msg));
		echo json_encode($response); exit;
	}
	echo json_encode($response); exit;
}

function removegroupusers($params){
	/*
	* Client can remove users from specific group.
	* Params:
	* apikey: Mendatory parameter, Unique API key that client can obtain from CometChat admin panel
	* grouid: Mendatory parameter, unique group ID
	* users: Mendatory parameter, array or JSON of users(can be username or userid)
	* Upon execution will show success/failed response and will remove the users into specific group
	*/
	$msg = '';
	$response = array();
	checkAPIKEY($params['apikeyvalue']);

	if(!empty($params['groupid']) && !empty($params['users'])) {
		if(!is_array($params['users'])) {
			$params['users'] = trim($params['users']);
			if(strpos($params['users'],'[') !== false){
				$params['users'] = substr($params['users'], 1, -1);
			}elseif (strpos($params['users'],'(') !== false) {
				$params['users'] = substr($params['users'], 1, -1);
			}
			$params['users'] = explode(',',$params['users']);
		}
		$removedUsers = array();
		$failedUsers = array();
		$query = sql_query('getGroupId', array('guid'=>$params['groupid'])); /* check if group exists*/
		$result = sql_fetch_assoc($query);
		$roomId = $result['id'];
		if(!empty($roomId)){
			foreach ($params['users'] as $to) {
				if(!empty($to)) {
					$columnName = is_numeric($to)?'userid':'username';
					$result = sql_query('checkUserExists', array('field'=>$columnName, 'value'=>$to)); /*check whether the requested users exists or not */
					$touser = sql_fetch_assoc($result);
					if(!empty($touser['userid'])) {
						$result = sql_query('checkGroupUserExists', array('chatroomid'=>$roomId, 'userid'=>$touser['userid']));
						$dbUserArray = sql_fetch_assoc($result);
						if(empty($dbUserArray['userid'])){
							/*check if user added in group*/
							$failedUsers[] = $to;
						}else {
							/*Users to be removed from group*/
							$removedUsers[] = $to;
						}
					}else {
						$failedUsers[] = $to;
					}
				}
			}
			if(!empty($removedUsers)) {
				$list = implode(',',$removedUsers);
				foreach($removedUsers as $user){
					sql_query('deleteGroupUser', array('chatroomid'=>$roomId, 'userid'=>$user));
				}
				$msg = 'Users removed successfully from group:';
				$response = array('success' => array('status' => '1000', 'message' => $msg,'data' => array($columnName => $list)));
				echo json_encode($response); exit;
			}
			if(!empty($failedUsers)){
				$list = implode(",",$failedUsers);
				$msg = 'Failed to remove users.';
				$response = array('failed' => array('status' => '1006', 'message' => $msg,'data' => array($columnName => $list)));
				echo json_encode($response); exit;
			}
		}else{
			$msg = 'Invalid groupid';
			$response = array('failed' => array('status' => '1007', 'message' => $msg));
			echo json_encode($response); exit;
		}
	} else {
		$msg = 'Invalid input';
		$response = array('failed' => array('status' => '1005', 'message' => $msg));
		echo json_encode($response); exit;
	}
	echo json_encode($response); exit;
}

function getpushnotificationchannels($params){

	/*
	* It will allow users who has the API key to get Push Notification channels of users.
	* Params:
	* apikey: Mendatory parameter, Unique API key that client can obtain from CometChat admin panel
	* userid or uid: Mendatory parameter, unique userid or uid
	* Upon execution will show success/failed response and will provide push notification channels
	*/

	checkAPIKEY($params['apikeyvalue']);	//Verify the API key
	if(!empty($params['userid']) || !empty($params['uid'])){
		$value = 0;
		if(!empty($params['userid'])){
			$value = $params['userid'];
			$columnName = DB_USERTABLE_USERID;
		}
		else{
			$value = $params['uid'];
			$columnName = 'uid';
		}
		$result = sql_query('checkUserExists', array('field'=>$columnName, 'value'=>$value)); /*check whether the requested users exists or not */
		$user = sql_fetch_assoc($result);

		if(!empty($user)){
			$userid = $user['userid'];
			include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR."announcements".DIRECTORY_SEPARATOR."config.php");

			$channels = array();
			$channels['user'] = array('userid'=>$userid,'channel'=>'C_'.md5($channelprefix."USER_".$userid.BASE_URL).getPlatformSuffix($pushplatformsuffix));
			$channels['announcements'] = array('channels'=>$announcementpushchannel.getPlatformSuffix($pushplatformsuffix));

			$groups = array();

			$query = sql_query('getJoinedGroups',array('userid'=>$userid));
			while ($group = sql_fetch_assoc($query)) {
				$groups['_'.$group['id']] = array('groupid'=>$group['id'],'C_'.md5($channelprefix."CHATROOM_".$group['id'].BASE_URL).getPlatformSuffix($pushplatformsuffix));
			}
			if(!empty($groups)){
				$channels['groups'] = $groups;
			}
			$response = $channels;
			echo json_encode($response); exit;
		}else{
			$msg = 'userid not found';
			$response = array('failed' => array('status' => '1007', 'message' => $msg));
			echo json_encode($response); exit;
		}
	}else{
		$msg = 'invalid userid or userid not passed';
		$response = array('failed' => array('status' => '1007', 'message' => $msg));
		echo json_encode($response); exit;
	}

}


function createwhiteboard($params){

    /*
    * It will allow users who has the API key to create a WhiteBoard URL.
    * Params:
    * isGroup - 1 if WhiteBoard is for Group 0 otherwise
    * userid - userid of user who has initiated a whiteboard
    * toid - id of the Group / user with whom whiteboard must be shared
    * apikey: Mendatory parameter, Unique API key that client can obtain from CometChat admin panel
    * Return a url using which users can share a whiteboard
    */
    global $channelprefix;
	$protocol = isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] != 'off') ? 'https' : 'http';

    checkAPIKEY($params['apikeyvalue']);    //Verify the API key
    $params['toid'] = !empty($params['toid']) ? $params['toid'] : rand();
    $params['userid'] = !empty($params['userid']) ? $params['userid'] : rand();
    if(!empty($params['isGroup'])){
        $whiteboard_session_id = md5($channelprefix."whiteboard_groups".$params['toid']);
    }else{
        $whiteboard_session_id = $params['userid']<$params['toid']? md5($params['userid']).md5($params['toid']) : md5($params['toid']).md5($params['userid']);
        $whiteboard_session_id = md5($channelprefix.'whiteboard_users'.$whiteboard_session_id);
    }

    if (filter_var(BASE_URL, FILTER_VALIDATE_URL)) {
    	$whiteboardURL = BASE_URL."plugins/whiteboard/index.php?action=whiteboard&whiteboard_session_id=".$whiteboard_session_id;
	}else if(!empty($_SERVER['SERVER_NAME'])){
		$whiteboardURL = $protocol.'://'.$_SERVER['SERVER_NAME'].BASE_URL."plugins/whiteboard/index.php?action=whiteboard&whiteboard_session_id=".$whiteboard_session_id;
	}else {
		$whiteboardURL = $protocol.'://'.$_SERVER['HTTP_HOST'].BASE_URL."plugins/whiteboard/index.php?action=whiteboard&whiteboard_session_id=".$whiteboard_session_id;
	}
    $response = array('success'=> array('status'=>'1040','whiteboardURL'=>$whiteboardURL));
    echo json_encode($response);
    exit;

}

function createwriteboard($params){

    /*
    * It will allow users who has the API key to create a WriteBoard URL.
    * Params:
    * isGroup - 1 if WriteBoard is for Group 0 otherwise
    * userid - userid of user who has initiated a writeboard
    * toid - id of the Group / user with whom writeboard must be shared
    * apikey: Mendatory parameter, Unique API key that client can obtain from CometChat admin panel
    * Return a url using which users can share a writeboard
    */
    global $channelprefix;
	$protocol = isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] != 'off') ? 'https' : 'http';

    checkAPIKEY($params['apikeyvalue']);    //Verify the API key
    $params['toid'] = !empty($params['toid']) ? $params['toid'] : rand();
    $params['userid'] = !empty($params['userid']) ? $params['userid'] : rand();
    if(!empty($params['isGroup'])){
        $writeboard_session_id = md5($channelprefix."writeboard_groups".$params['toid']);
    }else{
        $writeboard_session_id = $params['userid']<$params['toid']? md5($params['userid']).md5($params['toid']) : md5($params['toid']).md5($params['userid']);
        $writeboard_session_id = md5($channelprefix.'writeboard_users'.$writeboard_session_id);
    }

    if (filter_var(BASE_URL, FILTER_VALIDATE_URL)) {
    	$writeboardURL = BASE_URL."plugins/writeboard/index.php?action=writeboard&writeboard_session_id=".$writeboard_session_id;
	}else if(!empty($_SERVER['SERVER_NAME'])){
		$writeboardURL = $protocol.'://'.$_SERVER['SERVER_NAME'].BASE_URL."plugins/writeboard/index.php?action=writeboard&writeboard_session_id=".$writeboard_session_id;
	}else {
		$writeboardURL = $protocol.'://'.$_SERVER['HTTP_HOST'].BASE_URL."plugins/writeboard/index.php?action=writeboard&writeboard_session_id=".$writeboard_session_id;
	}
    $response = array('success'=> array('status'=>'1040','writeboardURL'=>$writeboardURL));
    echo json_encode($response);
    exit;

}
