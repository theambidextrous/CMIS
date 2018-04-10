<?php
/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

if (!defined('CCADMIN')) { echo "NO DICE"; exit; }

$online = onlineusers();
if(!empty($guestnamePrefix)){ $guestnamePrefix .= '-'; }

function index() {
	global $body , $online;
	$overlay = '';

	$body = <<<EOD
	<div class="row">
	<div class="col-sm-2 col-lg-2">
	<div class="card card-inverse card-primary">
		<div class="card-block pb-0">
			<h1 class="mb-0" id="online">{$online}</h1>
			<p>Users Online</p>
		</div>
	</div>
	</div>

	  <div class="col-sm-10 col-lg-10">
	    <div class="card">
	      <div class="card-header">
	        Real Time Monitoring
	    </div>
	    <div class="card-block">
        <table class="table" id="monitor">
          <thead>
            <tr>
              <th width="25%">Flow</th>
              <th width="55%">Message</th>
              <th width="30%">Time</th>
            </tr>
          </thead>
          <tbody id="data">
          </tbody>
        </table>
	    </div>
	    </div>
	  </div>
	</div>
	<script>
			jQuery.cometchatmonitor();
	</script>
EOD;
	template();
}

function data() {

	global $guestsMode, $guestnamePrefix, $cms, $stickersImageUrl;

	$usertable = TABLE_PREFIX.DB_USERTABLE;
	$usertable_username = DB_USERTABLE_NAME;
	$usertable_userid = DB_USERTABLE_USERID;
	$guestpart = "";

	$criteria = " cometchat.id > ".sql_real_escape_string($_POST['timestamp'])." and ";
	$criteria2 = sql_getQuery('admin_monitor_desc');

	if($guestsMode) {
		$guestpart = sql_getQuery('admin_monitor_guestpart',array('usertable_username'=>$usertable_username, 'usertable_userid'=>$usertable_userid, 'usertable'=>$usertable, 'guestnamePrefix'=>$guestnamePrefix, 'criteria'=>$criteria));
	}

	$response = array();
	$messages = array();

	if (empty($_POST['timestamp'])) {
		$criteria = ' ';
		$criteria2 = sql_getQuery('admin_monitor_criteria2');

	}

	$query = sql_query('admin_monitor',array('guestpart'=>$guestpart, 'criteria2'=>$criteria2, 'criteria'=>$criteria, 'usertable_username'=>$usertable_username, 'usertable'=>$usertable, 'usertable_userid'=>$usertable_userid));

	$timestamp = $_POST['timestamp'];

	while ($chat = sql_fetch_assoc($query)) {

		if (function_exists('processName')) {
			$chat['fromu'] = processName($chat['fromu']);
			$chat['tou'] = processName($chat['tou']);
		}

		$time=$chat['sent']*1000;

		if(strpos($chat['message'], 'CC^CONTROL_') === false)
			array_unshift($messages,  array('id' => $chat['id'], 'from' => $chat['from'], 'to' => $chat['to'], 'fromu' => $chat['fromu'], 'tou' => $chat['tou'], 'message' => $chat['message'], 'time' => $time));
		elseif (strpos($chat['message'], 'sendSticker')) {
			$message = str_replace('CC^CONTROL_', '', $chat['message']);
			$message = json_decode($message);
			$category = $message->params->category;
			$key = $message->params->key;
			$image = '<img class="cometchat_stickerImage" type="image" src="'.$stickersImageUrl.$category.'/'.$key.'.png">';
			array_unshift($messages,  array('id' => $chat['id'], 'from' => $chat['from'], 'to' => $chat['to'], 'fromu' => $chat['fromu'], 'tou' => $chat['tou'], 'message' => $image, 'time' => $time));
		}

		if ($chat['id'] > $timestamp) {
			$timestamp = $chat['id'];
		}
	}

	$response['timestamp'] = $timestamp;
	$response['online'] = onlineusers();

	if (!empty($messages)) {
		$response['messages'] = $messages;
	}
	//header('Content-type: application/json; charset=utf-8');
	echo json_encode($response);
exit;
}
