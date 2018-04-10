<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."cometchat_init.php");

$response = array();
$messages = array();

$status['available'] = $language[30];
$status['busy'] = $language[31];
$status['offline'] = $language[32];
$status['invisible'] = $language[33];
$status['away'] = $language[34];

if (!empty($_REQUEST['userid'])) {
	$fetchid = $_REQUEST['userid'];
} else {
	$fetchid = $userid;
}
if(strpos($fetchid,',')>0){
	$userIds = "'".str_replace(",", "','", sql_real_escape_string($fetchid))."'";
	$sql =  getActivechatboxdetails($userIds);
}else{
	$fetchid = intval($fetchid);
	$sql = getUserDetails($fetchid);
	if ($guestsMode && $fetchid >= $firstguestID) {
		$sql = getGuestDetails($fetchid);
	}
}
$time = getTimeStamp();

$query = sql_query($sql, array(), 1);

if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }

while($chat = sql_fetch_assoc($query)){

	if ((($time-processTime($chat['lastactivity'])) < ONLINE_TIMEOUT || $chat['isdevice'] == 1) && $chat['status'] != 'invisible' && $chat['status'] != 'offline') {
		if ($chat['status'] != 'busy' && $chat['status'] != 'away') {
			$chat['status'] = 'available';
		}
	} else {
		$chat['status'] = 'offline';
	}

	if ($chat['message'] == null) {
		$chat['message'] = $status[$chat['status']];
	}

	$link = fetchLink($chat['link']);
	$avatar = getAvatar($chat['avatar']);

	if(empty($chat['ch'])) {
		if( defined('KEY_A') && defined('KEY_B') && defined('KEY_C') ){
			$key = KEY_A.KEY_B.KEY_C;
		}
		$chat['ch'] = md5($chat['userid'].$key);
	}

	if (function_exists('processName')) {
		$chat['username'] = processName($chat['username']);
	}

	if(empty($chat['readreceiptsetting']) || $chat['readreceiptsetting'] == null || $chat['readreceiptsetting'] == "null"){
		$chat['readreceiptsetting'] = 0;
		if(MESSAGE_RECEIPT==1){
			$chat['readreceiptsetting'] = 1;
		}
	}

	if($chat['lastseen'] == null || $chat['lastseen'] == "null"){
		$chat['lastseen'] = '';
	}

	if($chat['lastseensetting'] == null || $chat['lastseensetting'] == "null"){
		$chat['lastseensetting'] = '';
	}

	if(strpos($fetchid,',')>0){
		$response[] =  array('id' => $chat['userid'], 'n' => $chat['username'], 'l' => $link, 'd' => $chat['isdevice'],'a' => $avatar, 's' => $chat['status'], 'm' => $chat['message'], 'ch' => $chat['ch'], 'ls' => $chat['lastseen'], 'lstn' => $chat['lastseensetting'],'rdrs'=>$chat['readreceiptsetting']);
	}else{
		$response =  array('id' => $chat['userid'], 'n' => $chat['username'], 'l' => $link, 'd' => $chat['isdevice'],'a' => $avatar, 's' => $chat['status'], 'm' => $chat['message'], 'ch' => $chat['ch'], 'ls' => $chat['lastseen'], 'lstn' => $chat['lastseensetting'],'rdrs'=>$chat['readreceiptsetting']);
	}

}
header('Content-type: application/json; charset=utf-8');
if (!empty($_GET['callback'])) {
	echo $_GET['callback'].'('.json_encode($response).')';
} else {
	echo json_encode($response);
}
exit;
