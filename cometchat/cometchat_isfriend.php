<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."cometchat_init.php");

$response = array();
$messages = array();

$fetchid = $_REQUEST['userid'];

$time = getTimeStamp();
$sql = getFriendsList($userid,$time);
$query = sql_query($sql,array(),1);

if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }

$isfriend = 0;

while ($chat = sql_fetch_assoc($query)) {
	if ($chat['userid'] == $fetchid) {
		$isfriend = 1;
		break;
	}
}

$response =  array('friend' => $isfriend);

header('Content-type: application/json; charset=utf-8');
if (!empty($_GET['callback'])) {
	echo $_GET['callback'].'('.json_encode($response).')';
} else {
	echo json_encode($response);
}
exit;
