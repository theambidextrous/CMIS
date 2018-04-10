<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins.php");
include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."config.php");

if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php")) {
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php");
}

if( checkplan('plugins','whiteboard') == 0){ exit;}

$action = $subaction = $room = $chatroommode = $to = $grp = $random = $callback = $callbackfn = null;

if(!empty($_REQUEST['action'])){
	$action = $_REQUEST['action'];
}
if(!empty($_REQUEST['subaction'])){
	$subaction = $_REQUEST['subaction'];
}
if(!empty($_REQUEST['room'])){
	$room = $_REQUEST['room'];
}
if(!empty($_REQUEST['chatroommode'])){
	$chatroommode = $_REQUEST['chatroommode'];
}
if(!empty($_REQUEST['id'])){
	$to = intval($_REQUEST['id']);
}
if(!empty($_REQUEST['to'])){
	$to = intval($_REQUEST['to']);
}
if(!empty($_REQUEST['grp'])){
	$grp = intval($_REQUEST['grp']);
}
if(!empty($_REQUEST['random'])){
	$random = $_REQUEST['random'];
}
if(!empty($_REQUEST['callback'])){
	$callback = $_REQUEST['callback'];
}
if(!empty($_REQUEST['callbackfn'])){
	$callbackfn = $_REQUEST['callbackfn'];
}

if(!empty($_REQUEST['whiteboard_session_id'])){
	$whiteboard_session_id = $_REQUEST['whiteboard_session_id'];
}

if(empty($room)){
	if(!empty($chatroommode)){
		$room = md5($channelprefix."whiteboard_groups".$to);
	}else{
		$room = $userid<$to? md5($userid).md5($to) : md5($to).md5($userid);
		$room = md5($channelprefix.'whiteboard_users'.$room);
	}
}


if ($action == 'request') {
    $optionalmessage = 0;

	if(function_exists('hooks_sendOptionalMessage')) {
		$optionalmessage = hooks_sendOptionalMessage(array('to' => $to, 'plugin' => 'whiteboard'));
	}

	if($optionalmessage == 0){
		$message = $whiteboard_language[2]." <a href='javascript:void(0);' class='accept_White' to='".$userid."' random='".$to."' chatroommode='0' mobileAction=\"javascript:jqcc.ccwhiteboard.accept('".$userid."','".$to."');\">".$whiteboard_language[3]."</a> ".$whiteboard_language[4];
	    	$response = sendMessage($to,$message,1);
		$processedMessage = $_SESSION['cometchat']['user']['n'].": ".$whiteboard_language[2];
		$response['push']=pushMobileNotification($to,$response['id'],$processedMessage);
		sendMessage($to,$whiteboard_language[5],2);
		if (!empty($callback)) {
			header('content-type: application/json; charset=utf-8');
			echo $callback.'('.json_encode($response).')';
		}else{
			echo json_encode($response);
		}
    }
}

if ($action == 'accept') {
	sendMessage($to,$whiteboard_language[6],1);
	if (!empty($callback)) {
		header('content-type: application/json; charset=utf-8');
		echo $callback.'()';
	}
}

if ($action == 'whiteboard') {
	if(empty($whiteboard_session_id)){
		if (!empty($chatroommode)) {
			if (!empty($subaction)) {
				$message = $whiteboard_language[7]." <a href='javascript:void(0);' class='accept_White' to='".$to."' random='0' room='".$room."' chatroommode='1' mobileAction=\"javascript:jqcc.ccwhiteboard.accept('".$to."','0','".$chatroommode."','".$room."');\">".$whiteboard_language[8]."</a>";
				sendChatroomMessage($to,$message,0);
			}
		} else {
			if(!empty($random)){
				sendMessage($to,$whiteboard_language[5],2);
				incrementCallback();
				$message = $whiteboard_language[2]." <a href='javascript:void(0);' class='accept_White' to='".$userid."' random='".$random."' room='".$room."' chatroommode='0' mobileAction=\"javascript:jqcc.ccwhiteboard.accept('".$userid."','".$random."','".$room."');\">".$whiteboard_language[3]."</a> ".$whiteboard_language[4];
				$response = sendMessage($to,$message,1);
				decrementCallback();
				$processedMessage = $_SESSION['cometchat']['user']['n'].": ".$whiteboard_language[2];
				$response['push']=pushMobileNotification($to,$response['id'],$processedMessage);
			}
		}
	}else{
		$room = $whiteboard_session_id;
	}
	if(!empty($callbackfn) && $callbackfn == 'mobileapp'){
		header('content-type: application/json; charset=utf-8');
		echo json_encode(array('room'=>$room));
		exit();
	}
echo <<<EOD
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="user-scalable=1,width=device-width, initial-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<title>{$whiteboard_language[0]}</title>
<style>
html, body, div, span, applet, object, iframe,
h1, h2, h3, h4, h5, h6, p, blockquote, pre,
a, abbr, acronym, address, big, cite, code,
del, dfn, em, font, img, ins, kbd, q, s, samp,
small, strike, strong, sub, sup, tt, var,
dl, dt, dd, ol, ul, li,
fieldset, form, label, legend,
table, caption, tbody, tfoot, thead, tr, th, td{
	margin: 0;
	padding: 0;
	border: 0;
	outline: 0;
	font-weight: inherit;
	font-style: inherit;
	font-size: 100%;
	font-family: inherit;
	vertical-align: baseline;
    text-align: center;
}
html{
  height: 100%;
  overflow: hidden; /* Hides scrollbar in IE */
}
body{
  height: 100%;
  margin: 0;
  padding: 0;
}
</style>
<script>
	function resizePopup(){
		window.resizeTo(754, 612);
	}
</script>
</head>
<body onload="resizePopup()">
	<iframe src="{$drawURL}/d/draw-{$room}" width="100%" height="100%" frameborder="0">
</body>
</html>
EOD;
}
