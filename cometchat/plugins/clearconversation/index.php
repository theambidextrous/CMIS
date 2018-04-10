<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins.php");

if( checkplan('plugins','clearconversation') == 0){ exit;}
if(!checkMembershipAccess('clearconversation','plugins')){exit();}

if ($_REQUEST['action'] == 'clear' && !empty($_REQUEST['clearid'])) {
	$id = $_REQUEST['clearid'];

	if(!empty($_REQUEST['chatroommode'])) {
		$_SESSION['cometchat']['chatrooms_'.$id.'_clearId'] = $_REQUEST['lastid'];
		unset($_SESSION['cometchat']['cometchat_chatroom_'.$id]);
	} else {
		$lastentry = 0;

		if (!empty($_SESSION['cometchat']['cometchat_user_'.$id]) && is_array($_SESSION['cometchat']['cometchat_user_'.$id])) {
			$lastentry = end($_SESSION['cometchat']['cometchat_user_'.$id]);
			$lastentry = $lastentry['id'];
			unset($_SESSION['cometchat']['cometchat_user_'.$id]);
		}

		$_SESSION['cometchat']['cometchat_user_'.$id.'_clear'] = array('timestamp' => getTimeStamp().'999', 'lastentry' => array('id' => $lastentry));
	}
	if (!empty($_GET['callback'])) {
		header('content-type: application/json; charset=utf-8');
		echo $_GET['callback'].'()';
	}
}else{
	if(!empty($_REQUEST['deleteid'])){
		$to = $_REQUEST['deleteid'];
	}
	$_SESSION['cometchat']['cometchat_user_'.$to]=array();

	$query = sql_query('update_clearconversation',array('userid'=>$userid, 'to'=>$to));
	$response = array();
	$response['id'] = $to;
	if (!empty($error) ) {
		$response['result'] = "0";
		header('content-type: application/json; charset=utf-8');
		$response['error'] = sql_error($GLOBALS['dbh']);
		echo $_REQUEST['callback'].'('.json_encode($response).')';
		exit;
	}
	header('content-type: application/json; charset=utf-8');
	$response['result'] = "1";
	echo $_REQUEST['callback'].'('.json_encode($response).')';
}
?>
