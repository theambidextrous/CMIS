<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/
define('CCADMIN',true);
include_once (dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."config.php");
cometchatDBConnect();
include_once (dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."shared.php");


$response = array('status'=>0,'message'=> 'Unable to get token and version.');
$data = json_decode(file_get_contents('php://input'), true);
if(!empty($data)){
	if(!empty($data['token'])){
		$response['status'] = 1;
		configeditor(array('latest_update_token'=>$data['token']));
		configeditor(array('LATEST_VERSION'=>$data['version']));
		$response['message'] = 'Success';
	}
}
header('Content-Type: application/json');
echo json_encode($response);
