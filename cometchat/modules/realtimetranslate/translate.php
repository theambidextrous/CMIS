<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'config.php');
include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'config.php');

if(!empty($_REQUEST['action']) && $_REQUEST['action'] == 'translate_text') {
	$text = $_REQUEST['text'];
	$response = array();
	if(!empty($_REQUEST['to'])){
		$to = $_REQUEST['to'];
	} else {
		$to = $_COOKIE[$cookiePrefix.'rttlang'];
	}

	$from = 'en';
	if(!empty($_REQUEST['from'])){
		$from = $_REQUEST['from'];
	}

	if($useGoogle == 1){
		include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'google.php');
		$translatedText = translate_text($text,$from,$to);
		$response = array('translatedText'=>$translatedText);
	} else {
		include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'bing.php');
		$translatedText = translate_text($text,$from,$to);
		$response = array('translatedText'=>$translatedText);
	}

	header('Content-type: application/json; charset=utf-8');
	$response = json_encode($response);
	if (!empty($_GET['callback'])) {
		echo $_GET['callback'].'('.$response.')';
	} else {
		echo $response;
	}
} else {
	if ($useGoogle == 1) {
		include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'google.php');
	} else {
		include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'bing.php');
	}
}
