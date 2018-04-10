<?php

class PushNotification {
	private $title;
	private $provider;
	private $providerObject;
	public function __construct($settings=array()) {
		$this->title  = setConfigValue('app_title','CometChat');
		$this->provider  = setConfigValue('provider_pushnotification','FireBase');
		include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'provider'.DIRECTORY_SEPARATOR.'pushnotification'.DIRECTORY_SEPARATOR.$this->provider.'PushNotification.php');
		$className = $this->provider."PushNotification";
		$this->providerObject = new $className(array('app_title'=>$this->title));
	}

	public function sendNotification($channel, $messageData, $isChatroom = '0', $isAnnouncement = '0',$isWRTC = '0') {
		$response = $this->providerObject->sendNotification($channel, $messageData, $isChatroom , $isAnnouncement ,$isWRTC);
		return $response;
	}

	public function getPlatformSuffix($suffix){
		return $this->providerObject->getPlatformSuffix($suffix);
	}
}
