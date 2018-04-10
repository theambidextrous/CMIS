<?php

include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'config.php');

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/* SETTINGS START */


$app_title = setConfigValue('app_title',"CometChat");
$register_url = setConfigValue('register_url',"");
$adunit_id = setConfigValue('adunit_id','');
$invite_via_sms = setConfigValue('invite_via_sms','0');
$share_this_app = setConfigValue('share_this_app','0');
$firebaseauthserverkey = setConfigValue('firebaseauthserverkey','AIzaSyCCqPdNExgQdIQgaxJ0P1fV5fUcaH99CO4');
$mobileappOption = setConfigValue('mobileappOption','0');
$mobileappBundleid = setConfigValue('mobileappBundleid','com.inscripts.cometchat');
$useWhitelabelledapp = setConfigValue('useWhitelabelledapp','0');
$mobileappPlaystore = setConfigValue('mobileappPlaystore','https://play.google.com/store/apps/details?id=com.inscripts.cometchat&hl=en');
$mobileappAppstore = setConfigValue('mobileappAppstore','https://itunes.apple.com/in/app/cometchat/id594110077?mt=8');

$onesignalAppId  = setConfigValue('onesignalAppId','ed7e3e06-3598-4beb-80c6-01f3a89227f3');
$onesignalAPIKey = setConfigValue('onesignalAPIKey','M2QwMzY3MGItN2ZlZi00MGIxLWFlZTktM2IzYjhkMmZhMDg2');
$pushNotificationsProviders = array('FireBase','OneSignal');
$provider_pushnotification = setConfigValue('provider_pushnotification','FireBase');
/* SETTINGS END */

$oneonone_enabled = '1';
$announcement_enabled = '0';

$pushNotifications = '1';

/* 1 => Phone number, 2 => Phone number with email */

$response['mobile_config']['phone_number_enabled']= '0';
$response['mobile_config']['username_password_enabled']= '1';

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
