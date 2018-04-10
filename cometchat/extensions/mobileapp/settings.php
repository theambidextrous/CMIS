<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

if (!defined('CCADMIN')) { echo "NO DICE"; exit; }

//global $getstylesheet;
include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."config.php");
include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'config.php');
global $lang, $login_url, $logout_url, $client;
$site_url = "";
if(defined('CC_SITE_URL')) {
	$site_url = CC_SITE_URL;
}
$base_url = BASE_URL;
$hidden = '';
$hiddenwhitelabapp = '';
$mobileappOptionYes = '';
$mobileappOptionNo = '';
$useWhitelabelledappCheck = '';
$cloudSettings = '';
if (empty($client)) {
	$cloudSettings = 'style="display:none;"';
}
if($mobileappOption) {
	$mobileappOptionYes = 'checked="checked"';
	$mobileappOptionNo = '';
} else {
	$hidden = 'style="display:none;"';
	$mobileappOptionYes = '';
	$mobileappOptionNo = 'checked="checked"';
}

if($useWhitelabelledapp) {
	$useWhitelabelledappCheckY = 'checked';
	$useWhitelabelledappCheckN = '';
} else {
	$useWhitelabelledappCheckY = '';
	$useWhitelabelledappCheckN = 'checked';
	$hiddenwhitelabapp = 'display:none;';
}

if ($invite_via_sms == 1) {
	$invite_via_smsYes = 'checked="checked"';
	$invite_via_smsNo = '';
} else {
	$invite_via_smsNo = 'checked="checked"';
	$invite_via_smsYes = '';
}

if ($share_this_app == 1) {
	$share_this_appYes = 'checked="checked"';
	$share_this_appNo = '';
} else {
	$share_this_appNo = 'checked="checked"';
	$share_this_appYes = '';
}

if($invite_via_sms == 0 && $share_this_app == 0){
	$share_text_style = 'display:none;';
} else {
	$share_text_style = 'display:block;';
}
$OneSignalOption = $FireBaseOption = $OneSignalStyle = $FireBaseStyle = "";
if($provider_pushnotification == "OneSignal"){
	$OneSignalOption = "checked";
	$FireBaseStyle = "style='display:none;'";
}
if($provider_pushnotification == "FireBase"){
	$FireBaseOption = "checked";
	$OneSignalStyle = "style='display:none;'";
}
$push_notification_providers_option = "";
if (!empty($_REQUEST['dev'])) {
	$push_notification_providers_option = <<<EOD
		<div class="form-group row col-md-12" style="">
			<div class="col-md-12" style="padding-top:7px;">Push Notification Providers:</div>
			<div class="col-md-12">
				<input class="pushnotification"  name="provider_pushnotification" value="FireBase" {$FireBaseOption} type="radio" style="margin-top:8px;" /> &nbsp;&nbsp;FireBase
				&nbsp;&nbsp;&nbsp;
				<input class="pushnotification" name="provider_pushnotification" {$OneSignalOption} value="OneSignal" type="radio" />&nbsp;&nbsp;OneSignal
			</div>
		</div>
EOD;
}
if (empty($_GET['process'])) {
	$firebaseauthserverkey = ($firebaseauthserverkey == "AIzaSyCCqPdNExgQdIQgaxJ0P1fV5fUcaH99CO4") ? '' : $firebaseauthserverkey;
	$jqueryjstag =  getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'jquery', 'ext' => 'js'));
	echo <<<EOD
<!DOCTYPE html>
<html>
	<head>
	{$jqueryjstag}
	<script type="text/javascript">
		$ = jQuery = jqcc;
	</script>

	{$GLOBALS['adminjstag']}
    {$GLOBALS['admincsstag']}
	<script type="text/javascript">
		$(function() {
			$('input:radio').change(function(){
				var radio_array = [];
				var i = 0;
				$('input[type="radio"]:checked').each(function() {
					radio_array[i++] = $(this).val();
				});
				if(radio_array.indexOf('1')>=0){
					$('.invite_text').show(600);
				} else {
					$('.invite_text').hide(600);
				}
			});
			$(".pushnotification").on('click',function(){
				if($(this).val() == 'OneSignal'){
					$(".firebase").hide();
					$(".onesignal").show();
				} else {
					$(".firebase").show();
					$(".onesignal").hide();
				}
			});

			$(".mobileappradio").click(function(){
		        var optionval = $(this).attr("value");
		        if(optionval == "1"){
		        	$("#mobileappdetails").show('slow');
		        } else {
		        	$("#mobileappdetails").hide('slow');
		        }
		    });

			$(".whilteappcheckbox").click(function(){
		        var optionval = $(this).attr("value");
		        if(optionval == "1"){
				    $(".whiteappdetails").show('slow');
				} else {
				    $(".whiteappdetails").hide('slow');
				}
		    });

		});
	</script>
</head>
<body class="navbar-fixed sidebar-nav fixed-nav" style="background-color:white;overflow-y:hidden;">
	<form action="?module=dashboard&action=loadexternal&type=extension&name=mobileapp&process=true" method="post" enctype="multipart/form-data">
		<div class="col-md-6">
			<div class="form-group row col-md-12" style="">
				<div class="col-md-12" style="padding-top:7px;">Title:</div>
				<div class="col-md-12">
					<input type="text" class="form-control" id="app_title" name="app_title" value="$app_title" placeholder="CometChat" />
				</div>
			</div>

			{$push_notification_providers_option}

			<div class="form-group row col-md-12 firebase" {$FireBaseStyle}>
				<div class="col-md-12" style="padding-top:7px;">Firebase server key:</div>
				<div class="col-md-12">
					<input type="text" class="form-control" id="firebase_field" name="firebaseauthserverkey" value="$firebaseauthserverkey" />
				</div>
				<div class="col-md-12" style="padding-top:7px;">
				Firebase server key allows you to start receiving push notifications on your mobile app.
				</div>
			</div>

			<div class="form-group row col-md-12 onesignal" {$OneSignalStyle}>
				<div class="col-md-12" style="padding-top:7px;">OneSignal App ID</div>
				<div class="col-md-12">
					<input type="text" class="form-control" id="onesignalAppId" name="onesignalAppId" value="$onesignalAppId" />
				</div>

			</div>

			<div class="form-group row col-md-12 onesignal" {$OneSignalStyle}>
				<div class="col-md-12" style="padding-top:7px;">OneSignal API Key</div>
				<div class="col-md-12">
					<input type="text" class="form-control" id="onesignalAPIKey" name="onesignalAPIKey" value="$onesignalAPIKey" />
				</div>
				<div class="col-md-12" style="padding-top:7px;">
				OneSignal App ID allows you to start receiving push notifications from OneSignal PushNotification Provider on your mobile app.
				</div>
			</div>

			<div class="form-group row col-md-12">
				<div class="col-md-12" style="padding-top:7px;">AdMob Ad Unit Id:</div>
				<div class="col-md-12">
					<input type="text" class="form-control" id="adunit_field" name="adunit_id" value="$adunit_id" />
				</div>
				<div class="col-md-12" style="padding-top:7px;">
					<a href="https://support.google.com/admob/answer/3052638?hl=en" target="_blank"> AdMob Ad unit id </a> is used to display Google Admob advertisements in Mobile App.
				</div>
			</div>

			<div class="form-group row col-md-12" style="padding-top:5px;">
				<div class="col-md-12" style="padding-top:7px;">Enable invite via SMS:</div>
				<div class="col-md-12">
					<input  name="invite_via_sms" value="1" {$invite_via_smsYes} type="radio" style="margin-top:8px;" />&nbsp;&nbsp;Yes
					&nbsp;&nbsp;&nbsp;
					<input name="invite_via_sms" {$invite_via_smsNo} value="0" type="radio" />&nbsp;&nbsp;&nbsp;No
				</div>
				<div class="col-md-12">
					You can invite user to use Mobile App by sending download link of Mobile App via SMS.
				</div>
			</div>

			<div class="form-group row col-md-12" style="padding-top:5px;">
				<div class="col-md-12" style="padding-top:7px;">Enable share this app:</div>
				<div class="col-md-12">
					<input  name="share_this_app" value="1" {$share_this_appYes} type="radio" style="margin-top:8px;" />&nbsp;&nbsp;Yes
					&nbsp;&nbsp;&nbsp;
					<input name="share_this_app" {$share_this_appNo} value="0" type="radio" />&nbsp;&nbsp;&nbsp;No
				</div>
				<div class="col-md-12">
					Share this app feature allows you to share the app link to all your friends accross different social media networks.
				</div>
			</div>

			<div class="form-group row col-md-12" style="padding-top:5px;">
				<div class="col-md-12" style="padding-top:7px;">Enable Deep Linking:</div>
				<div class="col-md-12">
					<input class="mobileappradio"  name="mobileappOption" value="1" {$mobileappOptionYes} type="radio" style="margin-top:8px;" />&nbsp;&nbsp;Yes
					&nbsp;&nbsp;&nbsp;
					<input class="mobileappradio" name="mobileappOption" {$mobileappOptionNo} value="0" type="radio" />&nbsp;&nbsp;&nbsp;No
				</div>
				<div class="col-md-12">
					Deep linking allows your users to open the mobile app directly from the mobile browser.
				</div>
			</div>

			<div id="mobileappdetails" $hidden>
				<div class="form-group row col-md-12" style="padding-top:5px;">
					<div class="col-md-12" style="padding-top:7px;">Have the CometChat White-labelled Mobile App:</div>
					<div class="col-md-12">

					<input class="whilteappcheckbox" name="useWhitelabelledapp" value="1" {$useWhitelabelledappCheckY} type="radio" style="margin-top:8px;" />&nbsp;&nbsp;Yes
					&nbsp;&nbsp;&nbsp;
					<input class="whilteappcheckbox" name="useWhitelabelledapp" {$useWhitelabelledappCheckN} value="0" type="radio" />&nbsp;&nbsp;&nbsp;No
					</div>
					<div class="col-md-12">
						If you do not have the CometChat White-labelled Mobile App, then your users will be directed to the free CometChat Mobile App if you have this feature enabled.
					</div>
				</div>

				<div class="form-group row col-md-12 whiteappdetails" style="$hiddenwhitelabapp">
					<div class="col-md-12" style="padding-top:7px;">App Bundle id:</div>
					<div class="col-md-12">
						<div class="input-group">
							<span class="input-group-addon">https://</span>
							<input type="text" class="form-control" name="mobileappBundleid" value="$mobileappBundleid" />
						</div>
					</div>
				</div>

				<div class="form-group row col-md-12 whiteappdetails" style="$hiddenwhitelabapp">
					<div class="col-md-12" style="padding-top:7px;">Playstore URL:</div>
					<div class="col-md-12">
						<div class="input-group">
							<span class="input-group-addon">https://</span>
							<input type="text" class="form-control" name="mobileappPlaystore" value="$mobileappPlaystore">
						</div>
					</div>
				</div>

				<div class="form-group row col-md-12 whiteappdetails" style="$hiddenwhitelabapp">
					<div class="col-md-12" style="padding-top:7px;">Appstore URL:</div>
					<div class="col-md-12">
						<input type="text" class="form-control" name="mobileappAppstore" value="$mobileappAppstore">
					</div>
				</div>

			</div>

			<div class="form-group row col-md-12" $cloudSettings>
				<div class="col-md-12" style="padding-top:7px;">Site URL:</div>
				<div class="col-md-12">
					<div class="input-group">
					<span class="input-group-addon">http://</span>
						<input type="text" class="form-control" id = "site_url" value="{$site_url}" name="CC_SITE_URL" placeholder="yoursite.com">
					</div>
				</div>
			</div>

			<div class="form-group row col-md-12"  $cloudSettings>
				<div class="col-md-12" style="padding-top:7px;">Login URL(Optional):</div>
				<div class="col-md-12">
					<input type="text" id= "logi_url" class="form-control" value="{$login_url}" name="MOBILE_URL" placeholder="yoursite.com/sign-In">
				</div>
			</div>

			<div class="form-group row col-md-12"  $cloudSettings>
				<div class="col-md-12" style="padding-top:7px;">Logout URL(Optional):</div>
				<div class="col-md-12">
					<input type="text" id= "logout_url" class="form-control" value="{$logout_url}" name="MOBILE_LOGOUTURL" placeholder="yoursite.com/sign-Out">
				</div>
			</div>

			<div class="form-group row col-md-12">
				<div class="col-md-12" style="padding-top:7px;">Register URL:</div>
				<div class="col-md-12">
					<input type="text" class="form-control" id="register_url" name="register_url" value="$register_url" />
				</div>
			</div>



			<div class="form-group row col-md-12" style="padding-left:28px;">
				<input type="submit" value="Update Settings" class="btn btn-primary" />
			</div>
		</div>
	</form>
</body>
</html>
EOD;
} else {
	if (empty($_POST['firebaseauthserverkey'])) {
		$_POST['firebaseauthserverkey'] = "AIzaSyCCqPdNExgQdIQgaxJ0P1fV5fUcaH99CO4";
	}

	if (!empty($_POST['CC_SITE_URL']) && !empty($GLOBALS['client'])) {
		$domain = preg_replace('#^https?://#', '', rtrim($_POST['CC_SITE_URL'],'/'));
		$valid_domain = is_valid_domain_name($domain);
		if($valid_domain){
			$url = "http://my.cometchat.com/updatedomain2.php";
			$data = array('client' => $client,'domain' => $domain);
			fetchURL($url,$data);
			$_SESSION['cometchat']['error'] = 'Domain & platform updated successfully';
		}else{
			$_SESSION['cometchat']['error'] = 'Invalid domain name. Note: IP address is not allowed';
		}
	}

	configeditor($_POST);
	header("Location:?module=dashboard&action=loadexternal&type=extension&name=mobileapp");
}

function is_valid_domain_name($domain_name){
	$domain_name = preg_replace('#((?:https?://)?[^/]*)(?:/.*)?$#', '$1', $domain_name);
	return preg_match("/^([a-z](-*[a-z0-9])*)(\.([a-z0-9](-*[a-z0-9])*))*$/i", $domain_name);
}
