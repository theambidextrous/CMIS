<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/
if(file_exists(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'cometchat_init.php')) {
	include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'cometchat_init.php');
}
if($_REQUEST['apiKey'] != '' && $_REQUEST['channel'] != '' && $_REQUEST['message'] != '') {
	global $userid;
	$botlist = getBotList();
	foreach ($botlist as $bot){
		if (isset($bot['api']) && $_REQUEST['apiKey'] == $bot['api'])
		{
			$channel = explode('_', $_REQUEST['channel']);
			$to = $channel[2];
			$userid = $channel[1];
			$chatroommode = 0;
			$botid = 0;
			$messagetype = 'text';

			$query = sql_query('admin_getBotId',array('apikey'=>$_REQUEST['apiKey']));
			$result = sql_fetch_assoc($query);

			if(!empty($result['id'])){
				$botid = $result['id'];
			}

			if($channel[0] == 'group'){
				$chatroommode = 1;
			}

			$message = rawurldecode($_REQUEST['message']);

			if(strpos($message,'<img ') !== false){
				$messagetype = 'image';
				$xpath = new DOMXPath(@DOMDocument::loadHTML($message));
				$src = $xpath->evaluate("string(//img/@src)");
				$message = '<img class="file_image cometchat_botimagefile" type="image" src="'.$src.'"/>';
			}else if(strpos($message,'<a ') !== false){
				$messagetype = 'anchor';
			}

			$controlparameters = array('type' => 'core', 'name' => 'bots', 'method' => 'botresponse', 'params' => array('message' => $message, 'chatroommode' => $chatroommode, 'botid' => $botid, 'messagetype' => $messagetype));
			$controlparameters = 'CC^CONTROL_'.json_encode($controlparameters);

			if($chatroommode) {
				sendChatroomMessage($to,$controlparameters,0,'botresponse');
			}else {
				$response = sendMessage($to,$controlparameters,0,'botresponse');

				if(USE_COMET == 1){
					$cometmessage = array();
					$cometresponse = array('to' => $to,'message' => $controlparameters, 'dir' => 0,'type' => "botresponse");
					array_push($cometmessage, $cometresponse);
					publishCometMessages($cometmessage,$response['id']);
				}
			}
		}
	}
}

?>
