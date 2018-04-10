<?php

class FireBasePushNotification {

	private $pushPayload;
	private $firebasenotificationUrl;
	private $firebaseauthserverkey;
	private $messageType;
	private $title;

	public function __construct($settings = array()) {
		$this->firebasenotificationUrl = 'https://fcm.googleapis.com/fcm/send';
		$this->firebaseauthserverkey = setConfigValue('firebaseauthserverkey','AIzaSyCCqPdNExgQdIQgaxJ0P1fV5fUcaH99CO4');
		$this->title = !empty($settings['app_title']) ? $settings['app_title'] : 'CometChat';
	}

	public function sendNotification($channel, $messageData, $isChatroom = '0', $isAnnouncement = '0',$isWRTC = '0') {
		if(empty($this->firebaseauthserverkey)){
			return 'Empty firebase auth server key';
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
				} elseif($isWRTC != '0'){
					$soundfile = 'avpushsound.wav';
					$messageDataSplit = explode('_#wrtcgrp_',$messageData['m']);
					$notificationData['grp'] = $messageDataSplit[0];
					$messageData['m'] = $messageDataSplit[1];
					if($isWRTC == 'AC'){
						$messageType = "O_AC";
					}elseif($isWRTC == 'AVC'){
						$messageType = "O_AVC";
					}
				}
				$channel = "C_".$channel;
				$breaks = array("<br />","<br>","<br/>");
				$messageData['m'] = htmlspecialchars_decode(strip_tags(str_ireplace($breaks, "\n", $messageData['m'])));
			}

			$notificationData = array_merge(array('alert' => $messageData['m'],'t' => $messageType,'m' => $messageData,'action' => "PARSE_MSG",'sound' => $soundfile,"title" => $this->title, "badge" => "Increment" , "isFirebaseNotification" => '1'),$notificationData);

			/****** START: For android devices :START *********/
			$pushPayload = array( "to" => '/topics/'.$channel.'a', "data" => $notificationData, "priority" => "high");
			$response = array('android' => array(),'ios' => array());
			$response['android']['pushPayload'] = $pushPayload;
			$curl = curl_init();
			curl_setopt($curl,CURLOPT_URL,$this->firebasenotificationUrl);
			curl_setopt($curl,CURLOPT_PORT,443);
			curl_setopt($curl,CURLOPT_POST,1);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
			curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($pushPayload));
			curl_setopt($curl,CURLOPT_HTTPHEADER,
				array(
					"Authorization: key=" .$this->firebaseauthserverkey ,
					"Content-Type: application/json"
					));
			$response['android']['success'] = curl_exec($curl);
			if(!$response) {
				$response['android']['error'] = 'Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl);
			}
			curl_close($curl);
			/****** END: For android devices :END *********/

			/****** START: For ios devices :START *********/
			$pushPayload['to'] = '/topics/'.$channel.'i';
			$pushPayload['notification'] = array('text' => $messageData['m'],'title'=>$this->title,'icon'=>'ic_launcher_small','sound' => $soundfile);
			$response['ios']['pushPayload'] = $pushPayload;
			$curl = curl_init();
			curl_setopt($curl,CURLOPT_URL,$this->firebasenotificationUrl);
			curl_setopt($curl,CURLOPT_PORT,443);
			curl_setopt($curl,CURLOPT_POST,1);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
			curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($pushPayload));
			curl_setopt($curl,CURLOPT_HTTPHEADER,
				array(
					"Authorization: key=" .$this->firebaseauthserverkey ,
					"Content-Type: application/json"
					));
			$response['ios']['success'] = curl_exec($curl);
			if(!$response) {
				$response['ios']['error'] = 'Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl);
			}
			curl_close($curl);
			/****** END: For ios devices :END *********/
			return $response;
		} else {
			return "Missing or invalid parameters.";
		}
	}

	public function getPlatformSuffix($suffix) {
		return $suffix;
	}
}

?>
