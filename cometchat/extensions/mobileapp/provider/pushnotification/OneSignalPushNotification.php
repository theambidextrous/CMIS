<?php

class OneSignalPushNotification {
	private $onesignalnotificationUrl;
	private $appId;
	private $APIKey;
	private $title;

	public function __construct($settings = array()) {
		$this->onesignalnotificationUrl  = "https://onesignal.com/api/v1/notifications";
		$this->appId  = setConfigValue('onesignalAppId','ed7e3e06-3598-4beb-80c6-01f3a89227f3');
		$this->APIKey = setConfigValue('onesignalAPIKey','M2QwMzY3MGItN2ZlZi00MGIxLWFlZTktM2IzYjhkMmZhMDg2');
		$this->title = !empty($settings['app_title']) ? $settings['app_title'] : 'CometChat';
	}

	public function sendNotification($channel, $messageData, $isChatroom = '0', $isAnnouncement = '0',$isWebRTC = '0') {
		if(empty($this->appId) || empty($this->APIKey)){
			return 'Empty OneSignal appId or APIKey';
		}
		if(function_exists('curl_version') && !empty($channel) && $messageData ) {
			$messageType="O";
			$notificationData = array();
			$soundfile = 'default';
			if(!empty($messageData['id'])){
				$messageData['id'].='';
			}
			if(!empty($messageData['fid'])){
				$messageData['fid'].='';
			}
			if(!empty($messageData['cid'])){
				$messageData['cid'].='';
			}
			if ($isAnnouncement == '1') {
				$messageType = "A";
				$notificationData['isANN'] = $isAnnouncement;
				$messageData['m'] = strip_tags($messageData['m']);
			}else{
				if($isChatroom == '1') {
					$messageType = "C";
					$notificationData['isCR'] = $isChatroom;
				} elseif($isWebRTC != '0'){
					$soundfile = 'avpushsound.wav';
					$messageDataSplit = explode('_#wrtcgrp_',$messageData['m']);
					$notificationData['grp'] = $messageDataSplit[0];
					$messageData['m'] = $messageDataSplit[1];
					if($isWebRTC == 'AC'){
						$messageType = "O_AC";
					}elseif($isWebRTC == 'AVC'){
						$messageType = "O_AVC";
					}
				}
				$channel = "C_".$channel;
				$breaks = array("<br />","<br>","<br/>");
				$messageData['m'] = htmlspecialchars_decode(strip_tags(str_ireplace($breaks, "\n", $messageData['m'])));
			}

			$notificationData = array_merge(array('alert' => $messageData['m'],'t' => $messageType,'m' => $messageData,'action' => "PARSE_MSG",'sound' => $soundfile,"title" => $this->title, "isOneSignalNotification" => '1'),$notificationData);

			$postdata = array(
				'app_id' => $this->appId,
				'filters' => array(array("field" => "tag", "key" => $channel, "relation" => "=", "value" => "1")),
				'contents' => array(
					"en" => json_encode(array(
						'title' => $this->title,
					 	'body'  => $messageData['m']
					))
				),
				'data' => $notificationData,
				'ios_badgeType' => 'Increase',
	    		'ios_badgeCount' => 1
			);
			$postdata = json_encode($postdata);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->onesignalnotificationUrl);
			curl_setopt($ch, CURLOPT_HTTPHEADER,
							array(
								'Content-Type: application/json; charset=utf-8',
								'Authorization: Basic '.$this->APIKey
							)
						);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			$response['success'] = curl_exec($ch);
			if(!$response) {
				$response['error'] = 'Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch);
			}
			curl_close($ch);
			$response['test'] = $channel;
			return $response;

		} else {
			return "Missing or invalid parameters.";
		}
	}

	public function getPlatformSuffix($suffix){
		return '';
	}
}
