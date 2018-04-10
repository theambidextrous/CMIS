<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/
include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'config.php');

if(phpversion()>='5'){
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'jsmin.php');
}

if(BAR_DISABLED==1 && empty($_REQUEST['admin'])){
	exit();
}

if(get_magic_quotes_runtime()){
	set_magic_quotes_runtime(false);
}
$cometchat_float = "cometchat_floatL";
if($rtl==1){
	$cometchat_float = "cometchat_floatR";
}

$mtime = explode(" ",microtime());
$starttime = $mtime[1]+$mtime[0];

ob_start();
if(!empty($_REQUEST['admin'])){
	$adminappjs = empty($_GET['app'])?'':'app';
	$adminjs = dirname(__FILE__).DIRECTORY_SEPARATOR.'writable'.DIRECTORY_SEPARATOR.$writable.DIRECTORY_SEPARATOR.'admin'.$adminappjs.'.js';
	if(file_exists($adminjs)&&DEV_MODE!=1){
		if ((!empty($_SERVER['HTTP_IF_MODIFIED_SINCE']) && @strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == filemtime($adminjs)) || (!empty($_SERVER['HTTP_IF_NONE_MATCH']) && @trim($_SERVER['HTTP_IF_NONE_MATCH']) == md5_file($adminjs))) {
			header("HTTP/1.1 304 Not Modified");
			exit;
		}
		readfile($adminjs);
		$js = ob_get_clean();
	}else{
		if(empty($adminappjs)){
			include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."admin".DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."bower_components".DIRECTORY_SEPARATOR."jquery".DIRECTORY_SEPARATOR."dist".DIRECTORY_SEPARATOR."jquery.min.js");
			include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."admin".DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."admin.js");
			include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."admin".DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."bower_components".DIRECTORY_SEPARATOR."tether".DIRECTORY_SEPARATOR."dist".DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."tether.js");
			include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."admin".DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."bower_components".DIRECTORY_SEPARATOR."bootstrap".DIRECTORY_SEPARATOR."dist".DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."bootstrap.js");
			include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."admin".DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."colorpicker.js");
			include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."admin".DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."jquery-linedtextarea.js");
			include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."admin".DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."Chart.bundle.js");
			include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."admin".DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."utils.js");
		}else{
			include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."admin".DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."app.js");
		}


		if(phpversion()>='5'){
			$js = JSMin::minify(ob_get_clean());
		}else{
			$js = ob_get_clean();
		}
		$fp = @fopen($adminjs,'w');
		@fwrite($fp,$js);
		@fclose($fp);
	}
	$lastModified = filemtime($adminjs);
	$etag = md5_file($adminjs);
}else{
	$type = 'core';
	$name = 'default';
	$js = '';
	if(!empty($_REQUEST['type'])&&!empty($_REQUEST['name'])){
		$type = cleanInput($_REQUEST['type']);
		$name = cleanInput($_REQUEST['name']);
	}

	$subtype = '';
	if(!empty($_REQUEST['subtype'])){
		$subtype = cleanInput($_REQUEST['subtype']);
	}

	$cbfn = '';
	if(!empty($_REQUEST['callbackfn'])){
		$cbfn = cleanInput($_REQUEST['callbackfn']);
	}

	if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."layouts".DIRECTORY_SEPARATOR.$layout.DIRECTORY_SEPARATOR.'config.php')){
		include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."layouts".DIRECTORY_SEPARATOR.$layout.DIRECTORY_SEPARATOR.'config.php');
	}else{
		include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'layouts'.DIRECTORY_SEPARATOR.'docked'.DIRECTORY_SEPARATOR.'config.php');
	}
	$jsfile = dirname(__FILE__).DIRECTORY_SEPARATOR.'writable'.DIRECTORY_SEPARATOR.$writable.DIRECTORY_SEPARATOR.$cbfn.$type.$name.$layout.$color.$lang.$enablecustomjs.$forcedockedenable.'.js';
	$cometchat = array();
	if(file_exists($jsfile)&&DEV_MODE!=1){
		if ((!empty($_SERVER['HTTP_IF_MODIFIED_SINCE']) && @strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == filemtime($jsfile)) || (!empty($_SERVER['HTTP_IF_NONE_MATCH']) && @trim($_SERVER['HTTP_IF_NONE_MATCH']) == md5_file($jsfile))) {
			header("HTTP/1.1 304 Not Modified");
			exit;
		}
		readfile($jsfile);
		$js = ob_get_clean();
	}else{
		if(($type!='core'||$name!='default')&&($type!='extension'||($type=='extension'&&$name=='jabber'))&&($type!='external')){
			if($type =='core' && $name=='embedcode'){
				include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."jquery.js");
				include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."libraries.js");
				if(!empty($client) && file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'cloud'.DIRECTORY_SEPARATOR."integrations".DIRECTORY_SEPARATOR.$cms.'.js')&&empty($cbfn)){
					include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'cloud'.DIRECTORY_SEPARATOR.'integrations'.DIRECTORY_SEPARATOR.$cms.'.js');
				}
			}
			if($type=='core'){
				include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR.$name.".js");
			}else{
				if($type!='transport'){
					include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."libraries.js");
				}
				if(empty($subtype)){
					$subtype = $name;
				}
				if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.$type."s".DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR.$subtype.".js")){
					include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.$type."s".DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR.$subtype.".js");
				}
				if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.$type."s".DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.'layouts'.DIRECTORY_SEPARATOR.$layout.DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR.$layout.".js")){
					include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.$type."s".DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.'layouts'.DIRECTORY_SEPARATOR.$layout.DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR.$layout.".js");
				}
				if($name=='whiteboard') {
					include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.'swfobject.js');
				}
			}
		}else{

			if(USE_COMET==1){
				$minHeartbeat = REFRESH_BUDDYLIST.'000';
				$maxHeartbeat = REFRESH_BUDDYLIST.'000';
			}
			if(((defined('INCLUDE_JQUERY')&&INCLUDE_JQUERY==1)&&empty($cbfn))||($type=='extension'&&$name=='desktop')||($cbfn=="desktop")){
				include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."jquery.js");
			}
			if($cbfn=="desktop"){
				if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."extensions".DIRECTORY_SEPARATOR.$cbfn.DIRECTORY_SEPARATOR.$cbfn.".js")){
					include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."extensions".DIRECTORY_SEPARATOR.$cbfn.DIRECTORY_SEPARATOR.$cbfn.".js");
				}
			}
			$jssettings = '';

			if(defined('DISPLAY_ALL_USERS')&&DISPLAY_ALL_USERS==1){
				$language[14] = $language[28];
			}elseif($hideOffline==1||MEMCACHE!=0){
				$language[14] = $language[29];
			}

			foreach ($language as $key => $value) {
				$cometchat['language'][$key] = $value;
			}

			$jssettings .= "var language = ".json_encode($cometchat['language']).";";
			$cometchat['trayicon'] = array();

			foreach ($trayicon as $module => $moduleinfo){
				$id = $module;
				if(!empty($moduleinfo[7])&&$moduleinfo[7]==1){
					$moduleinfo[2] = BASE_URL.$moduleinfo[2];
				}

				if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR."lang.php")){
					include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR."lang.php");
					$traylanguage = $module.'_language';
					if(!empty(${$traylanguage}[100])){
						$moduleinfo[1] = ${$traylanguage}[100];
					}
				}

				$cometchat['trayicon'][$module] = $moduleinfo;
			}

			if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR."chatrooms".DIRECTORY_SEPARATOR."lang.php")){
				include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR."chatrooms".DIRECTORY_SEPARATOR."lang.php");
			}

			$jssettings .= "var trayicon = ".json_encode($cometchat['trayicon']).";";
			if(!empty($cbfn)){
				$hideBar = 0;
			}

			$ccauth = array('enabled' => checkAuthMode('social'), 'active' => $ccactiveauth);

			$cometchat['jssettings']['showSettingsTab'] 	= $showSettingsTab; // Show Settings tab
			$cometchat['settings']['showOnlineTab'] 		= $showOnlineTab; //Show Who's Online tab
			$cometchat['settings']['showModules'] 			= $showModules; //Show Modules in Who\'s Online tab
			$cometchat['settings']['disableDockedLayout'] 	= $disableDockedLayout; //Disabled Docked Layout
			$cometchat['settings']['forceDockedEnable'] 	= $forcedockedenable;

			if($layout=='synergy') {
				$cometchat['settings']['enableType'] = $enableType; //Show only chatrooms or one-on-one chat in synergy
			}
			if($layout=='embedded') {
				$cometchat['settings']['enableType'] = $enableType; //Show only chatrooms or one-on-one chat in synergy
			}

			if(in_array("report", $plugins)) {
				if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR.'report'.DIRECTORY_SEPARATOR.'config.php')){
					include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR.'report'.DIRECTORY_SEPARATOR.'config.php');
				}
				if(empty($reportEmail) && ($key = array_search("report", $plugins)) !== false) {
					array_splice($plugins,$key,1); // Unset report conversation plugin if E-mail / SMTP is not configured from CometChat Administartion Panel
				}
			}
			/* ROLE BASE ACCESS CONTROL START */
			if (defined('ROLE_BASE_ACCESS') && ROLE_BASE_ACCESS==1) {
			    $membersRes = getRolesDetails();
			    foreach ($membersRes as $mRkey => $mRvalue) {
			        $cometchat['settings'][$mRkey.'_core']    = array_keys(${$mRkey.'_core'});
			        $cometchat['settings'][$mRkey.'_plugins']    = array_keys(${$mRkey.'_plugins'});
			        $cometchat['settings'][$mRkey.'_modules']    = array_keys(${$mRkey.'_modules'});
			        $cometchat['settings'][$mRkey.'_extensions'] = array_keys(${$mRkey.'_extensions'});
			    }
			    $cometchat['settings']['memberShipLevel'] = 1;
			} else{
				$cometchat['settings']['memberShipLevel'] = 0;
			}
			/* ROLE BASE ACCESS CONTROL END */
			$cometchat['settings']['plugins'] = array_values($plugins);
			$cometchat['settings']['extensions'] = array_values($extensions);
			$cometchat['settings']['disableRecentTab'] = intval($disableRecentTab); // Disable recent chats tab
			$cometchat['settings']['recentListLimit'] = intval($recentListLimit); // Recent list limit
			$cometchat['settings']['disableContactsTab'] = intval($disableContactsTab); // Disable contacts tab
			$cometchat['settings']['disableGroupTab'] = intval($disableGroupTab); // Disable contacts tab
			$cometchat['settings']['hideOffline'] = intval($hideOffline); // Hide offline users in Whos Online list?
			$cometchat['settings']['autoPopupChatbox'] = intval($autoPopupChatbox); // Auto-open chatbox when a new message arrives
			$cometchat['settings']['messageBeep'] = intval($messageBeep); // Beep on arrival of message from new user?
			$cometchat['settings']['beepOnAllMessages'] = intval($beepOnAllMessages); // Beep on arrival of all messages?
			$cometchat['settings']['barPadding'] = intval($barPadding); // Padding of bar from the end of the window
			$cometchat['settings']['minHeartbeat'] = intval($minHeartbeat); // Minimum poll-time in milliseconds (1 second = 1000 milliseconds)
			$cometchat['settings']['maxHeartbeat'] = intval($maxHeartbeat); // Maximum poll-time in milliseconds
			$cometchat['settings']['searchDisplayNumber'] = intval($searchDisplayNumber); // The number of users in Whos Online list after which search bar will be displayed
			$cometchat['settings']['thumbnailDisplayNumber'] = intval($thumbnailDisplayNumber); // The number of users in Whos Online list after which thumbnails will be hidden
			$cometchat['settings']['typingTimeout'] = intval($typingTimeout); // The number of milliseconds after which typing to will timeout
			$cometchat['settings']['idleTimeout'] = intval($idleTimeout); // The number of seconds after which user will be considered as idle
			$cometchat['settings']['displayOfflineNotification'] = intval($displayOfflineNotification); // If yes, user offline notification will be displayed
			$cometchat['settings']['displayOnlineNotification'] = intval($displayOnlineNotification); // If yes, user online notification will be displayed
			$cometchat['settings']['displayBusyNotification'] = intval($displayBusyNotification); // If yes, user busy notification will be displayed
			$cometchat['settings']['notificationTime'] = intval($notificationTime); // The number of milliseconds for which a notification will be displayed
			$cometchat['settings']['announcementTime'] = intval($announcementTime); // The number of milliseconds for which an announcement will be displayed
			$cometchat['settings']['scrollTime'] = intval($scrollTime); // Can be set to 800 for smooth scrolling when moving from one chatbox to another
			$cometchat['settings']['armyTime'] = intval($armyTime); // If set to yes, show time plugin will use 24-hour clock format
			$cometchat['settings']['disableForIE6'] = intval($disableForIE6); // If set to yes, CometChat will be hidden in IE6
			$cometchat['settings']['iPhoneView'] = intval($iPhoneView); // iPhone style messages in chatboxes? (not compatible with dark theme)
			$cometchat['settings']['hideBarCheck'] = intval($hideBar); // Hide bar for non-logged in users?
			$cometchat['settings']['startOffline'] = intval($startOffline); // Load bar in offline mode for all first time users?
			$cometchat['settings']['fixFlash'] = intval($fixFlash); // Set to yes, if Adobe Flash animations/ads are appearing on top of the bar (experimental)
			$cometchat['settings']['lightboxWindows'] = intval($lightboxWindows); // Set to yes, if you want to use the lightbox style popups
			$cometchat['settings']['sleekScroller'] = intval($sleekScroller);
			$cometchat['settings']['color'] = $color;
			$cometchat['settings']['cookiePrefix'] = $cookiePrefix;
			$cometchat['settings']['disableForMobileDevices'] = intval($disableForMobileDevices);
            $cometchat['settings']['desktopNotifications'] = intval($desktopNotifications);
			$cometchat['settings']['windowTitleNotify'] = intval($windowTitleNotify);
			$cometchat['settings']['floodControl'] = intval($floodControl);
			$cometchat['settings']['windowFavicon'] = intval($windowFavicon);
			$cometchat['settings']['theme'] = $layout;
			$cometchat['settings']['ccauth'] = $ccauth;
			$cometchat['settings']['prependLimit'] = !empty($prependLimit)?$prependLimit:'0';
			$cometchat['settings']['cometserviceEnabled'] = USE_COMET;
			$cometchat['settings']['stickersImageUrl'] = $stickersImageUrl;
			$cometchat['settings']['istypingEnabled'] = IS_TYPING;
			$cometchat['settings']['messagereceiptEnabled'] = MESSAGE_RECEIPT;
			$cometchat['settings']['onlinetimeout'] = ONLINE_TIMEOUT;
			$cometchat['settings']['lastseen'] = $lastseen;
			$cometchat['settings']['transport'] = TRANSPORT;
			$cometchat['settings']['usebots'] = $usebots;
			$cometchat['settings']['channelprefix'] = $channelprefix;
			$cometchat['settings']['allowAvatar'] = $allowAvatar;
			$cometchat['settings']['dockedChatBoxAvatar'] = !empty($dockedChatBoxAvatar)?$dockedChatBoxAvatar:'0';
			$cometchat['settings']['dockedAlignToLeft'] = !empty($dockedAlignToLeft)?$dockedAlignToLeft:'0'; //Set to yes, if docked layout has to be aligned to left.
			$cometchat['settings']['dockedChatListAudioCall'] = !empty($dockedChatListAudioCall)?$dockedChatListAudioCall:'0'; //Set to yes, if docked layout has to be aligned to left.
			$cometchat['settings']['uniqueguestname'] = $uniqueguestname; //Set to yes, if docked layout has to be aligned to left.
			$cometchat['settings']['guestMode'] = $guestsMode; //Set to yes, if docked layout has to be aligned to left.


			if(defined('CC_SITE_URL')) {
				$cometchat['settings']['ccsiteurl'] = CC_SITE_URL;
			} else {
				$cometchat['settings']['ccsiteurl'] = 'http'.(isset($_SERVER['HTTPS'])?'s':'').'://'."{$_SERVER['HTTP_HOST']}";
			}

			if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."layouts".DIRECTORY_SEPARATOR.'mobile'.DIRECTORY_SEPARATOR.'config.php')){
				include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."layouts".DIRECTORY_SEPARATOR.'mobile'.DIRECTORY_SEPARATOR.'config.php');
			}

			$cometchat['settings']['enableMobileTab'] = !empty($enableMobileTab)?$enableMobileTab:'0';

			$jssettings .= "var settings = ".json_encode($cometchat['settings']).";";

			$mobileappdetails = "";
			include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."extensions".DIRECTORY_SEPARATOR."mobileapp".DIRECTORY_SEPARATOR."config.php");
			$cometchat['mobileappdetails']['mobileappOption'] = $mobileappOption;
			$cometchat['mobileappdetails']['useWhitelabelledapp'] = $useWhitelabelledapp;
			$cometchat['mobileappdetails']['mobileappBundleid'] = $mobileappBundleid;
			$cometchat['mobileappdetails']['mobileappPlaystore'] = $mobileappPlaystore;
			$cometchat['mobileappdetails']['mobileappAppstore'] = $mobileappAppstore;
			$mobileappdetails = "var mobileappdetails = ".json_encode($cometchat['mobileappdetails']).";";

			include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."libraries.js");

			if($sleekScroller==1){
				include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."scroll.js");
			}
			if($type=='core'&&$name=='slick'){
				include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."slick.js");
			}

			if(USE_COMET==1){
				include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."transports".DIRECTORY_SEPARATOR.TRANSPORT.DIRECTORY_SEPARATOR.'includes.php');
			}

			// Modifying this will void license
			if($p_<2){
				$jsfn = 'c5';
			}else{
				$jsfn = 'c6';
			}
			include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."cometchat.js");
			include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."jstorage.js");

			if($layout=='embedded' && (($disableGroupTab == 0) || !empty($_REQUEST['chatroomid']) || !empty($_REQUEST['chatroomsonly']))){
				include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR."chatrooms".DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."chatrooms.js");
				if(!empty($_REQUEST['chatroomid']) || !empty($_REQUEST['chatroomsonly'])) {
					include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR."chatrooms".DIRECTORY_SEPARATOR."layouts".DIRECTORY_SEPARATOR."embedded".DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."embedded.js");
				}
			}
			if($layout=='embedded' && ($disableGroupTab == 0)){
				if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR."chatrooms".DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."chatrooms.js")){
					include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR."chatrooms".DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."chatrooms.js");
				}
				if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR."chatrooms".DIRECTORY_SEPARATOR."layouts".DIRECTORY_SEPARATOR."embedded".DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."embedded.js")){
					include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR."chatrooms".DIRECTORY_SEPARATOR."layouts".DIRECTORY_SEPARATOR."embedded".DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."embedded.js");
				}
			} else {
				if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR."chatrooms".DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."chatrooms.js")){
					include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR."chatrooms".DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."chatrooms.js");
				}
				if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR."chatrooms".DIRECTORY_SEPARATOR."layouts".DIRECTORY_SEPARATOR."docked".DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."docked.js")){
					include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR."chatrooms".DIRECTORY_SEPARATOR."layouts".DIRECTORY_SEPARATOR."docked".DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."docked.js");
				}
			}
			if(empty($cbfn) || $cbfn=='desktop'){
				if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."layouts".DIRECTORY_SEPARATOR.$layout.DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR.$layout.".js")){
					include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."layouts".DIRECTORY_SEPARATOR.$layout.DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR.$layout.".js");
				}else{
					include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."layouts".DIRECTORY_SEPARATOR.$layout.DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."docked.js");
				}
				if($p_>2 && file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."layouts".DIRECTORY_SEPARATOR.'mobile'.DIRECTORY_SEPARATOR.'config.php')){
					if($enableMobileTab&&file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."layouts".DIRECTORY_SEPARATOR.'mobile'.DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."mobile.js")){
						include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."layouts".DIRECTORY_SEPARATOR.'mobile'.DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."mobile.js");
					}
				}
			}elseif($type=='external'){
				include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$name.".js");
			}else{
				include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."extensions".DIRECTORY_SEPARATOR.$cbfn.DIRECTORY_SEPARATOR.$cbfn.".js");
				if($type=='extension'){
					if($name!=$cbfn){
						if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."extensions".DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$name.".js")){
							include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."extensions".DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$name.".js");
						}
					}
					if($name=='mobilewebapp'){
						include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR.'chatrooms'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR."chatrooms.js");
						include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."extensions".DIRECTORY_SEPARATOR.'mobilewebapp'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR."vendor".DIRECTORY_SEPARATOR."custom.modernizr.js");
						include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."extensions".DIRECTORY_SEPARATOR.'mobilewebapp'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR."jquery.nicescroll.js");
						$plugins = array_intersect($plugins,array('clearconversation','report','smilies'));
					}
				}
			}

			$include = 'init';
			$allplugins = array();

			if ($handle = opendir(dirname(__FILE__).DIRECTORY_SEPARATOR.'plugins')) {
				while (false !== ($file = readdir($handle))) {
					if ($file != "." && $file != ".." && is_dir(dirname(__FILE__).DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$file) && file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$file.DIRECTORY_SEPARATOR.'init.js') && $file != 'style') {
						$allplugins[] = $file;
					}
				}
				closedir($handle);
			}

			foreach($allplugins as $plugin){
				if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR.$plugin.DIRECTORY_SEPARATOR."init.js")){
					include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR.$plugin.DIRECTORY_SEPARATOR."init.js");
				}
				if($plugin == 'transliterate' && (in_array('transliterate', $plugins) || in_array('transliterate', $crplugins))){
					if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR.$plugin.DIRECTORY_SEPARATOR."jsapi.js")){
						include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR.$plugin.DIRECTORY_SEPARATOR."jsapi.js");
					}
				}
			}

			if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."style".DIRECTORY_SEPARATOR."init.js")){
				include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."style".DIRECTORY_SEPARATOR."init.js");
			}

			foreach($extensions as $extension){
				if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."extensions".DIRECTORY_SEPARATOR.$extension.DIRECTORY_SEPARATOR."init.js")){
					include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."extensions".DIRECTORY_SEPARATOR.$extension.DIRECTORY_SEPARATOR."init.js");
				}
			}

			foreach ($trayicon as $module => $moduleinfo){
				if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR."extra.js")){
					include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR."extra.js");
				}
			}
			if(!empty($client) && file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'cloud'.DIRECTORY_SEPARATOR."integrations".DIRECTORY_SEPARATOR.$cms.'.js')&&empty($cbfn)&&empty($cc_layout)){
				include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'cloud'.DIRECTORY_SEPARATOR.'integrations'.DIRECTORY_SEPARATOR.$cms.'.js');
			}
			if(!empty($client) && file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."cloud".DIRECTORY_SEPARATOR."ccapi.js")){
				include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."cloud".DIRECTORY_SEPARATOR."ccapi.js");
			}

			if(!empty($enablecustomjs) && $enablecustomjs == 1 && !empty($customjs)){
				echo $customjs;
			}
		}

		if(phpversion()>='5'){
			$js = JSMin::minify(ob_get_clean());
		}else{
			$js = ob_get_clean();
		}
		$fp = @fopen($jsfile,'w');
		@fwrite($fp,$js);
		@fclose($fp);
	}
	$lastModified = filemtime($jsfile);
	$etag = md5_file($jsfile);
}
if(phpversion()>='4.0.4pl1' && extension_loaded('zlib') && GZIP_ENABLED==1 && !empty($_SERVER["HTTP_ACCEPT_ENCODING"]) && (strpos($_SERVER["HTTP_ACCEPT_ENCODING"], 'gzip') !== false) && (strstr($GLOBALS['useragent'],'compatible') || strstr($GLOBALS['useragent'],'Gecko'))){
	ob_start('ob_gzhandler');
}else{
	ob_start();
}
header('Content-type: text/javascript;charset=utf-8');

if(!empty($client)) {
	header('Content-Length: '. strlen($js));
	$tags = array(
		'cod-'.$_SERVER['environment'].'-'.$client,
		'cod-'.$_SERVER['environment'].'-'.$client.'-'.$lang,
		'cod-'.$_SERVER['environment'].'-'.$client.'-'.$layout,
		'cod-'.$_SERVER['environment'].'-'.$client.'-'.$color,
		'cod-'.$_SERVER['environment'].'-'.$client.'-'.$enablecustomjs /* or css */
	);
	header('Cache-Tag: '.implode(' ', $tags));
}

header("Last-Modified: ".gmdate("D, d M Y H:i:s",$lastModified)." GMT");
header('Expires: '.gmdate("D, d M Y H:i:s",time()+3600*24*365).' GMT');
header("Etag: ".$etag);
header("Vary: Accept-Encoding");

echo $js;

$mtime = explode(" ",microtime());
$endtime = $mtime[1]+$mtime[0];

if(empty($client)) {
	echo "\n\n/* Execution time: ".($endtime-$starttime)." seconds */";
}

function cleanInput($input){
	return strtolower(preg_replace("/[^+A-Za-z0-9\_]/","",trim($input)));
}
