<?php

/*
CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

if (!defined('CCADMIN')) { echo "NO DICE"; exit; }

$options = array(
    'hideBar'				=> array('choice', 'Hide bar for non-logged in users?'),
    'autoPopupChatbox'		=> array('choice', 'Auto-open chatbox when a new message arrives:'),
    'messageBeep'			=> array('choice', 'Beep on arrival of message from new user?'),
    'beepOnAllMessages'		=> array('choice', 'Beep on arrival of all messages?'),
    'notificationTime'		=> array('textbox', 'The number of milliseconds for which a notification will be displayed:'),
    'announcementTime'		=> array('textbox', 'The number of milliseconds for which an announcement will be displayed:'),
	/*'scrollTime'			=> array('textbox', 'Can be set to 800 for smooth scrolling when moving from one chatbox to another'),*/
    'armyTime'				=> array('choice', 'If set to yes, time will be shown in 24-hour clock format:'),
    'startOffline'			=> array('choice', 'Load CometChat in offline mode for all first time users?'),
    /*'fixFlash'			=> array('choice', 'Set to yes, if Adobe Flash animations/ads are appearing on top of the bar (experimental):'),*/
    /*'lightboxWindows'		=> array('choice', 'Set to yes, if you want to use the lightbox style popups:'),*/
    /*'sleekScroller'		=> array('choice', 'Set to yes, if you want to use the new sleek scroller'),*/
    'desktopNotifications'	=> array('choice', 'Enable Desktop Notifications:'),
    'windowTitleNotify'		=> array('choice', 'If yes, notify new incoming messages by changing the browser title:'),
    'floodControl'			=> array('textbox', 'Chat spam control in milliseconds (Disabled if set to 0):'),
    'windowFavicon'			=> array('choice', 'If yes, Update favicon with number of messages (Supported on Chrome, Firefox, Opera):'),
    'blockpluginmode'		=> array('choice', 'If set to yes, show blocked users in Who\'s Online list:'),
    'lastseen'              => array('choice', 'If set to yes, users last seen will be shown:'),
);

if(empty($apikey) && empty($client)){
	$apikey = md5(time().$_SERVER['SERVER_NAME']);
	$apisave = array('apikey' => $apikey);
	configeditor($apisave);
}
if(!empty($client)) {
	$excludesettings = array('minHeartbeat', 'maxHeartbeat');
	foreach ($excludesettings as $value) {
		unset($options[$value]);
	}
}

function index() {
	global $body, $navigation, $options, $ts, $apikey, $gatrackerid, $client;
	$form = showSettingsUI($options);
	$dy = $dn = "";
	if (defined('DISPLAY_ALL_USERS') && DISPLAY_ALL_USERS == 1) {
		$dy = "checked";
	} else {
		$dn = "checked";
	}

$body .= <<<EOD
<div class="row">
  <div class="col-sm-12 col-lg-12">
  	<div class="row">
	  	<div class="col-sm-12 col-lg-12">
		    <div class="card">
		      	<div class="card-header">
		        	Web Settings
		      	</div>
		      	<div class="card-block">
		      	<form action="?module=settings&action=updatesettings&web=1&ts={$ts}" method="post">
					{$form}
		      		<div class="form-group row"><div class="col-md-12"><label class="form-control-label">Google Analytics ID:</label>
		      			<input class="form-control" type="text" name="gatrackerid" value="{$gatrackerid}" >
		      		</div>
		      		</div>
					<div class="row col-md-12" style="padding-bottom:5px;">
				      <input type="submit" value="Update Settings"  class="btn btn-primary">
				    </div>
				</form>

	            </div>
	    	</div>
	  	</div>
	</div>
  </div>
</div>
<script>
$(function(){
	$("#web").addClass("active");
});
</script>
EOD;
	template();

}

function generalsettings() {
	global $ts, $body, $client, $botsStatus, $clearcache_interface;
	$advance_interface = $licensekey_interface = $changeuserpass_interface = $storage_interface = $caching_interface = $customsettings_interface = '';
	$options = array(
	    'minHeartbeat'	 	=> array('textbox', 'Minimum poll-time in milliseconds (1 second = 1000 milliseconds):'),
	    'maxHeartbeat'	 	=> array('textbox', 'Maximum poll-time in milliseconds'),
	    'typingTimeout'	 	=> array('textbox', 'The number of milliseconds after which typing to will timeout:'),
	    'REFRESH_BUDDYLIST'	=> array('textbox', 'The number of seconds after which the Contacts tab is refreshed:',REFRESH_BUDDYLIST),
	    'ONLINE_TIMEOUT' 	=> array('textbox', 'The number of seconds after which a user is considered offline:',ONLINE_TIMEOUT),
	    'idleTimeout'	 	=> array('textbox', 'The number of seconds after which user will be considered as idle:')
	);
	if(!empty($client)) {
		$excludesettings = array('minHeartbeat', 'maxHeartbeat');
		foreach ($excludesettings as $value) {
			unset($options[$value]);
		}
	}
	$form = showSettingsUI($options);
	if(empty($client)) {
		$changeuserpass_interface 	= changeuserpass();
		$licensekey_interface		= licensekey();
		$storage_interface 			= storage();
	}
	$banuser_interface 			= banuser();
	$ccauth_interface 			= ccauth();
	$guest_interface 			= guestlogin();
	$dockedsettings_interface 	= dockedsettings();
	$caching_interface 			= caching();
	$recentsettings_interface 	= recentsettings();
	$generalsettings_interface 	= baseUrlsettings();
	$oneonone_interface 		= oneoneonsettings();
	$group_interface 			= groupsettings();
$advance_interface = <<<EOD
		<div class="col-sm-12 col-lg-12">
		    <div class="card">
		      	<div class="card-header">
		        	Advanced Settings
		      	</div>
		      	<div class="card-block">
			      	<form id="devsetting" action="?module=settings&action=updatedevsetting&ts={$ts}" method="post">
			      		{$form}
						<div class="row col-md-10" style="padding-bottom:5px;">
					      	<input type="submit" value="Update"  class="btn btn-primary">
					    </div>
		            </form>
	            </div>
	    	</div>
	  	</div>
EOD;
$cometchatStatus = "";
$ccupdate = "";
$title = "";
if (defined('BAR_DISABLED') && BAR_DISABLED == 0) {
	$cometchatStatus = "value=0";
	$ccupdate = "value=0";
	$title = "Disable CometChat";
}else{
	$cometchatStatus = "checked";
	$ccupdate = "value";
	$title = "Enable CometChat";
}
	$body = <<<EOD
	<div class="row">
    <div class="col-sm-12 col-lg-12">
    <div class="card">
        <div class="card-block">
            <form id="disabled_cc" action="?module=settings&action=updateccstatus&ts={$ts}" method="post" onSubmit="">
            <div class="col-xs-6" style="padding-left:0px;">Disable CometChat:</div>
            <div class="col-xs-6">
                <div class="material-switch pull-right" title="{$title}" data-toggle="tooltip"  >
                    <input id="someSwitchOptionSuccess" $botsStatus name="BAR_DISABLED" $cometchatStatus $ccupdate type="checkbox"/>
                    <label for="someSwitchOptionSuccess" class="label-danger" style="margin-bottom: .3rem;"></label>
                </div>
            </div>
            </form>
        </div>
    </div>
    </div>
	  	<div class="col-sm-4 col-lg-4">
		  	<div class="row">
		  		$recentsettings_interface
				$generalsettings_interface
				$dockedsettings_interface
			  	$guest_interface
			  	$ccauth_interface
		  		$advance_interface
			</div>
	  	</div>
	  	<div class="col-sm-4 col-lg-4">
		  	<div class="row">
		  		$oneonone_interface
				$licensekey_interface
				$changeuserpass_interface
				$banuser_interface
			</div>
	  	</div>
	  	<div class="col-sm-4 col-lg-4">
			<div class="row">
				$group_interface
				$clearcache_interface
				$caching_interface
				$storage_interface
			</div>
		</div>
	</div>
<script type="text/javascript">
	$(function(){
		$("#general").addClass("active");
	    $("input[name='BAR_DISABLED']").click(function(){
	      $("#disabled_cc").submit();
	    });
	});
</script>
EOD;
	template();
}

function apikey() {
	global $ts, $body, $client, $apikey;
	$options = array(
	    "apikey"						=> array('display', 'API key:'),
	);
	$form = showSettingsUI($options);
	$body = <<<EOD
	<div class="row">
		<div class="col-sm-12 col-lg-12">
		    <div class="card">
		      	<div class="card-header">
		        	API Key
		      	</div>
		      	<div class="card-block" style="height:400px;">
			      	<form id="devsetting" action="?module=settings&action=updatedevsetting&ts={$ts}" method="post">
			      		<div class="form-group row">
							<div class="col-md-12">
							<input class="form-control" readonly type="text" id="MC_PASSWORD" name="MC_PASSWORD" value="{$apikey}" >
							</div>
						</div>
		            </form>
	            </div>
	    	</div>
	  	</div>
	  	</div>
EOD;
	template();
}
function caching() {
/*START: caching*/
global $ts;
$caching_interface = "";
if (empty($GLOBALS['client'])) {
	$nc = "";
	$mc = "";
	$fc = "";
	$mcr = "";
	$apc = "";
	$win = "";
	$sqlite = "";
	$memcached = "";
	$MC_SERVER = MC_SERVER;
	$MC_PORT = MC_PORT;
	$MC_USERNAME = MC_USERNAME;
	$MC_PASSWORD = MC_PASSWORD;
	$MC_NAME = MC_NAME;


	if($MC_NAME == 'files') {
		$fc = "selected = ''";
	} elseif ($MC_NAME == 'memcache') {
		$mc = "selected = ''";
	} elseif ($MC_NAME == 'memcachier') {
		$mcr = "selected = ''";
	}  elseif ($MC_NAME == 'wincache') {
		$win = "selected = ''";
	} elseif ($MC_NAME == 'sqlite') {
		$sqlite = "selected = ''";
	}  elseif ($MC_NAME == 'memcached') {
		$memcached = "selected = ''";
	} elseif ($MC_NAME == 'apc') {
		$apc = "selected = ''";
	} else {
		$nc = "selected = ''";
	}

$caching_interface .= <<<EOD
  <div class="col-sm-12 col-lg-12">
  	<div class="row">
	  	<div class="col-sm-12 col-lg-12">
		    <div class="card">
		      	<div class="card-header">
		        	Caching
		      	</div>
		      	<div class="card-block">
		      	<form id="memcache" action="?module=settings&action=updatecaching&ts={$ts}" method="post">
				<div class="form-group row">
					<div class="col-md-12">
						<label class="form-control-label">Select caching type:</label>
		      			<select class="form-control" id="MC_NAME" name="MC_NAME" >
							<option value="" {$nc}>No caching</option>
							<option value="memcache" {$mc}>Memcache</option>
							<option value="files" {$fc}>File caching</option>
							<option value="memcachier" {$mcr}>Memcachier</option>
							<option value="apc" {$apc}>APC</option>
							<option value="wincache" {$win}>Wincache</option>
							<option value="sqlite" {$sqlite}>SQLite</option>
							<option value="memcached" {$memcached}>Memcached</option>
						</select>
		      		</div>
	      		</div>
	      		<div class="form-group row memcache" style="display:none">
					<div class="col-md-12">
					<label class="form-control-label">Memcache server name:</label>
					<input class="form-control" type="text" id="MC_SERVER" name="MC_SERVER" value={$MC_SERVER}  required="true" >
					</div>
				</div>

	      		<div class="form-group row memcache" style="display:none">
					<div class="col-md-12">
					<label class="form-control-label">Memcache server port:</label>
					<input class="form-control" type="text" id="MC_PORT" name="MC_PORT" value={$MC_PORT} required="true" >
					</div>
				</div>

	      		<div class="form-group row memcachier" style="display:none">
					<div class="col-md-12">
					<label class="form-control-label">Memcachier Username:</label>
					<input class="form-control" type="text" id="MC_USERNAME"  name="MC_USERNAME" value="{$MC_USERNAME}" >
					</div>
				</div>

	      		<div class="form-group row memcachier" style="display:none">
					<div class="col-md-12">
					<label class="form-control-label">Memcachier Password:</label>
					<input class="form-control" type="text" id="MC_PASSWORD" name="MC_PASSWORD" value="{$MC_PASSWORD}" >
					</div>
				</div>
				<div class="row col-md-12" style="padding-bottom:5px;"><br>
			      <input type="submit" value="Update"  class="btn btn-primary">
			      <a class="btn btn-primary" id="clearcache" href="?module=settings&action=clearcachefilesprocess&ts={$ts}" style="color:White;">Clear Cache</a>
			    </div>
			   </form>
	            </div>


	    	</div>
	  	</div>
	</div>
  </div>
<script>
	$(function(){

		if($("#MC_NAME option:selected").val() == 'memcache' || $("#MC_NAME option:selected").val() == 'memcached') {
			$('.memcache').css('display', 'block');
			$('.memcachier').hide();
		} else if($("#MC_NAME option:selected").val() == 'memcachier') {
			$('.memcache').css('display', 'block');
			$('.memcachier').show();
			$('#MC_USERNAME,#MC_PASSWORD').attr('required', 'true');
		}
	});


	$('select[id^=MC_NAME]').change(function() {
		$('#MC_USERNAME,#MC_PASSWORD').removeAttr('required');
		if($("#MC_NAME option:selected").index() == 1 || $("#MC_NAME option:selected").index() == 7) {
		   $('.memcache').css('display', 'block');
		   $('.memcachier').hide();
		} else if ($("#MC_NAME option:selected").index() == 3){
		   $('#MC_USERNAME,#MC_PASSWORD').attr('required', 'true');
		   $('.memcache').css('display', 'block');
		   $('.memcachier').show();
		} else {
		   $('.memcache').css('display', 'none');
		   $('.memcachier').hide();
		}
	});
	setTimeout(function () {
			var myform = document.getElementById('memcache');
			myform.addEventListener('submit', function(e) {
				e.preventDefault();
				if ($("#MC_NAME option:selected").index() == 1 && ($('#MC_SERVER').val() == null || $('#MC_SERVER').val() == '' || $('#MC_PORT').val() == null || $('#MC_PORT').val() == '')) {
					alert('Please enter memcache server name and port.');
					return false;
				} else if ($("#MC_NAME option:selected").index() == 3 && ($('#MC_SERVER').val() == null || $('#MC_SERVER').val() == '' || $('#MC_PORT').val() == null || $('#MC_PORT').val() == '' || $('#MC_USERNAME').val() == null || $('#MC_USERNAME').val() == '' || $('#MC_PASSWORD').val() == null || $('#MC_PASSWORD').val() == '' )) {
					alert('Please enter all the details for memcachier server.');
				} else if ($("#MC_NAME option:selected").index() == 7 && ($('#MC_SERVER').val() == null || $('#MC_SERVER').val() == '' || $('#MC_PORT').val() == null || $('#MC_PORT').val() == '')){
					alert('Please enter all the details for memcached server.');
				} else {
					myform.submit();
				}
			});
	}, 500);
</script>
EOD;
}
return $caching_interface;

}

function updatecaching(){
	if (!empty($GLOBALS['client'])) { header("Location:?module=dashboard&ts=".$GLOBALS['ts']); exit; }
    global $ts;
	$conn = 1;
	$errorCode = 0;
	$memcacheAuth = 0;
	include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."cometchat_cache.php");
	if ($_POST['MC_NAME'] == 'memcachier') {
		$memcacheAuth = 1;
		$conn = 0;
		$memcache = new MemcacheSASL;
		$memcache->addServer($_POST['MC_SERVER'], $_POST['MC_PORT']);
		if($memcachierAuth = $memcache->setSaslAuthData($_POST['MC_USERNAME'], $_POST['MC_PASSWORD'])) {
			$memcache->set('auth', 'ok');
			if(!$conn = $memcache->get('auth')) {
				$errorCode = 3;
			}
			$memcache->delete('auth');
		} else {
			$errorCode = 3;
		}
	} elseif ($_POST['MC_NAME'] != '') {
			$conn = 0;
			$memcacheAuth = 1;
			phpFastCache::setup("storage",$_POST['MC_NAME']);
			$memcache = new phpFastCache();
			$driverPresent = (isset($memcache->driver->option['availability'])) ? 0 : 1;
			if ($driverPresent) {
				if(($_POST['MC_NAME'] == 'memcache' && class_exists("Memcache")) || ($_POST['MC_NAME'] == 'memcached' && class_exists("Memcached"))) {
					if ($_POST['MC_NAME'] == 'memcache'){
						$server = array(array($_POST['MC_SERVER'],$_POST['MC_PORT'],1));
						$memcache->option('server', $server);
					}
					if ($_POST['MC_NAME'] == 'memcached'){
						$server = array(array($_POST['MC_SERVER'],$_POST['MC_PORT'],1));
						$memcache->option('server', $server);
					}
					$memcache->set('auth', 'ok',30);
					if (!$conn = $memcache->get('auth')){
						$errorCode = 1;
					}
					$memcache->delete('auth');
				}
			}
	}
	if (!$errorCode) {
		configeditor($_POST);
		$_SESSION['cometchat']['error'] = 'Caching details updated successfully.';
	} else {
		if($_POST['MC_NAME']== 'memcachier') {
			$_SESSION['cometchat']['error'] = 'Failed to update caching details. Please check your Memchachier server details';
		} elseif ($_POST['MC_NAME'] == 'files') {
			$_SESSION['cometchat']['error'] = 'Please check file permission of your cache directory. Please try 755/777/644';
		} elseif ($_POST['MC_NAME'] == 'apc') {
			$_SESSION['cometchat']['error'] = 'Failed to update caching details. Please check your APC configuration.';
		} elseif ($_POST['MC_NAME'] == 'wincache') {
			$_SESSION['cometchat']['error'] = 'Failed to update caching details. Please check your Wincache configuration.';
		} elseif ($_POST['MC_NAME'] == 'sqlite') {
			$_SESSION['cometchat']['error'] = 'Failed to update caching details. Please check your SQLite configuration.';
		} elseif ($_POST['MC_NAME'] == 'memcached') {
			$_SESSION['cometchat']['error'] = 'Failed to update caching details. Please check your Memcached configuration.';
		} else {
			$_SESSION['cometchat']['error'] = 'Failed to update caching details. Please check your Memcache server configuration.';
		}
	}
	header("Location:?module=settings&action=generalsettings&ts={$ts}");
}

function baseUrlsettings() {
	global $body, $client, $form, $ts;
	if (!empty($client)) {return '';}
	$options = array(
	    "BASE_URL"				=> array('textbox', 'Base URL:',BASE_URL)
	);
	$form = showSettingsUI($options);

$body = <<<EOD
<div class="col-sm-12 col-lg-12">
	<div class="card">
		<div class="card-header">
	    	General Settings
		<h4><small>If CometChat is not working on your site, your Base URL might be incorrect.<br>Our detection algorithm suggests: <b><script>document.write(window.location.pathname.replace(/admin\/.*/,"").replace("admin",""));</script></b></small></h4>
	  	</div>
		<div class="card-block">
		      	<form action="?module=settings&action=updatebaseurl&ts={$ts}" method="post">
			<form action="?module=settings&action=updatesettings&ts={$ts}" method="post">
				$form
				<div class="row col-md-12"><br>
					<input type="submit" value="Update"  class="btn btn-primary">
				</div>
			</form>
		</div>
	</div>
</div>
EOD;
	return $body;
}

function recentsettings() {
	global $body, $client, $form, $ts;
	$options = array(
	    'disableRecentTab'				=> array('choice', 'Disable Recent Chats?'),
	    'recentListLimit'				=> array('textbox', 'The number of user will displayed in recent tab:'),
	);
	$form = showSettingsUI($options);

$body = <<<EOD
<div class="col-sm-12 col-lg-12">
	<div class="card">
		<div class="card-header">
	    	Recent Chat
	  	</div>
		<div class="card-block">
			<form action="?module=settings&action=updatesettings&ts={$ts}" method="post">
				$form
				<div class="row col-md-12"><br>
					<input type="submit" value="Update"  class="btn btn-primary">
				</div>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(function(){
		$("input[name='recentListLimit']").attr("required",true);
		$("input[name='recentListLimit']").on("keyup",function(){
			var limit = $(this).val();
		    if(isNaN(limit))
		    	 $(this).val(1);
		    else if(parseInt(limit) <= 0)
		        $(this).val(1);
		    else if(parseInt(limit) > 30)
		        $(this).val(30);
		    else return limit;
		});
	});
</script>
EOD;
	return $body;
}

function oneoneonsettings() {
	global $body, $client, $ts;
	$options = array(
	    'disableContactsTab'			=> array('choice', 'Disable Contacts Chats?'),
	    'hideOffline'					=> array('choice', 'Hide offline users in Contacts Chats?'),
	    'DISPLAY_ALL_USERS'				=> array('choice', 'Show all online users (Including Non-friends)', DISPLAY_ALL_USERS),
	    'displayOfflineNotification'	=> array('choice', 'If yes, user offline notification will be displayed:'),
    	'displayBusyNotification'		=> array('choice', 'If yes, user busy notification will be displayed:'),
    	'displayOnlineNotification'		=> array('choice', 'If yes, user online notification will be displayed:'),
    	'thumbnailDisplayNumber'		=> array('textbox', 'The number of users in Contacts list after which thumbnails will be hidden:'),
    	'searchDisplayNumber'			=> array('textbox', 'The number of users in Contacts list after which search bar will be displayed:')
	);

	if(!empty($client)) {
		$excludesettings = array();
		foreach ($excludesettings as $value) {
			unset($options[$value]);
		}
	}

	if(defined('SWITCH_ENABLED') && SWITCH_ENABLED == 0){
		unset($options['DISPLAY_ALL_USERS']);
	}

	$form = showSettingsUI($options);

$body = <<<EOD
<div class="col-sm-12 col-lg-12">
	<div class="card">
		<div class="card-header">
	    	One-on-one Chat
	  	</div>
		<div class="card-block">
			<form action="?module=settings&action=updatesettings&ts={$ts}" method="post">
				$form
				<div class="row col-md-12"><br>
					<input type="submit" value="Update"  class="btn btn-primary">
				</div>
			</form>
		</div>
	</div>
</div>
EOD;
	return $body;
}

function groupsettings() {
	global $body, $client, $ts;
	$options = array(
		'disableGroupTab'		=> array('choice', 'Disable Groups Chats?'),
	    'showchatbutton'		=> array('choice', 'Show private chat for friends only'),
	    'allowUsers'			=> array('choice', 'If yes, users can create groups:'),
	    'allowGuests'			=> array('choice', 'If yes, guests can create groups:'),
	    'allowDelete'			=> array('choice', 'If yes, users can delete his own message in groups:'),
	    'allowAvatar'			=> array('choice', 'If yes, user avatars will be displayed in groups:'),
	    'crguestsMode'			=> array('choice', 'If yes, guests can access groups (Guest chat needs to be enabled)'),
	    'showChatroomUsers'		=> array('choice', 'If yes, show total number of participants in groups'),
	    'messageBeep'			=> array('choice', 'Beep on new messages:'),
	    'newMessageIndicator'	=> array('choice', 'Show indicator on new messages:'),
	    'showUsername'			=> array('choice', 'Show username in groups:'),
	    'showGroupsOnlineUsers'	=> array('choice', 'Show only online users in groups:'),
	    'chatroomTimeout'		=> array('textbox', 'The number of seconds after which a user created group will be removed if no activity:'),
	    'lastMessages'			=> array('textbox', 'Number of messages that are fetched when load earlier messages is clicked:'),
	);

	if(!defined('DISPLAY_ALL_USERS') || DISPLAY_ALL_USERS != '0') {
		unset($options['showchatbutton']);
	}

	$form = showSettingsUI($options);
	$body = <<<EOD
	<div class="col-sm-12 col-lg-12">
		<div class="card">
			<div class="card-header">
		    	Groups Chat
		  	</div>
			<div class="card-block">
				<form action="?module=settings&action=updatesettings&ts={$ts}" method="post">
					$form

					<div class="row col-md-12"><br>
						<input type="submit" value="Update"  class="btn btn-primary">
					</div>
				</form>
			</div>
		</div>
	</div>
EOD;
	return $body;
}

function updatesettings() {
    global $ts;
	configeditor($_POST);
		if(!empty($_REQUEST['avoidredirection'])){
		exit();
	}
	$_SESSION['cometchat']['error'] = 'Settings updated successfully';
	if (!empty($_REQUEST['web'])) {
		header("Location:?module=settings&ts={$ts}");
		exit();
	}
	header("Location:?module=settings&action=generalsettings&ts={$ts}");

}

function ccinboxsync(){
	global $body;
	if(getcmsSettings()){
		$body = getcmsSettings();
	}
	template();
}

function updateccinboxsync(){
	global $ts;
	$GLOBALS['integration']->updateUserRoles();
	configeditor($_POST);
	$_SESSION['cometchat']['error'] = 'Setting updated successfully';
	header("Location:?module=settings&action=ccinboxsync&ts={$ts}");

}

function devsettings() {
	global $body, $firstguestID, $ts, $uniqueguestname;
	$dmo = $dmof = $elo = $elof = $cdo = $cdof = $recentTy = $recentTn = $contactTy = $contactTn = $groupTy = $groupTn =
	$usecslegacyn = $usecslegacyy = $showSiteUrl = $baseurl = $avoidcby = $avoidcbn = $gzipyes = $gzipno = $uniqueyes = $uniqueno = "";

	if (DEV_MODE == 1) {
		$dmo = "checked";
	} else {
		$dmof = "checked";
	}
	if (ERROR_LOGGING == 1) {
		$elo = "checked";
	} else {
		$elof = "checked";
	}
	if (CROSS_DOMAIN == 1) {
		$cdo = "checked";
		$showSiteUrl = "style='display:block;'";
	} else {
		$cdof = "checked";
		$showSiteUrl = "style='display:none;'";
	}
	if(!defined('CC_SITE_URL')) {
		$CC_SITE_URL =  '';
	} else {
		$CC_SITE_URL =  CC_SITE_URL;
	}

	if(!defined('CS_RELAY_PORT')) {
		$CS_RELAY_PORT =  '';
	} else {
		$CS_RELAY_PORT =  CS_RELAY_PORT;
	}

	if(!defined('CS_URL_PATH')) {
		$CS_URL_PATH =  '';
	} else {
		$CS_URL_PATH =  CS_URL_PATH;
	}

	if(defined('BASE_URL')){
		$baseurl = BASE_URL;
	}

	if(defined('USE_CS_LEGACY') && USE_CS_LEGACY == 1){
		$usecslegacyy = "checked";
	}else{
		$usecslegacyn ="checked";
	}

	if(defined('AVOIDCALLBACK') && AVOIDCALLBACK == 1){
		$avoidcby = "checked";
	}else{
		$avoidcbn = "checked";
	}

	if(defined('GZIP_ENABLED') && GZIP_ENABLED == 1){
		$gzipyes = "checked";
	}else{
		$gzipno = "checked";
	}

	if ($uniqueguestname == 1) {
		$uniqueyes = "checked";
	} else {
		$uniqueno = "checked";
	}

	if (empty($GLOBALS['client']) && !empty($_REQUEST['dev'])) {
		$body = <<<EOD
		<div class="row">
		  <div class="col-sm-12 col-lg-12">
		  	<div class="row">
				<div class="col-sm-12 col-lg-12">
				    <div class="card">
				      	<div class="card-header">
				        	Developer Settings
				      	</div>
					    <div class="card-block">
							<form id="devsetting" action="?module=settings&action=updatedevsetting&dev=1&ts={$ts}" method="post">
								<div class="form-group row">
									<div class="col-md-12">
							      	<label class="form-control-label">Dev Mode:</label>
							      		<div class=""><label class=""><div style="position:relative;top:4px;"><input style="position: absolute;" type="radio" name="DEV_MODE" value="1" $dmo ></div><span style="padding-left:25px;">Yes</span></label><label class=""><div style="position:relative;top:4px;"><input style="position: absolute;left:8px;" type="radio" name="DEV_MODE" value="0" $dmof></div><span style="padding-left:36px;">No</span></label></div>
									</div>
								</div>

								<div class="form-group row">
									<div class="col-md-12">
							      	<label class="form-control-label">Error Logging:</label>
							      		<div class=""><label class=""><div style="position:relative;top:4px;"><input style="position: absolute;" type="radio" name="ERROR_LOGGING" value="1" $elo ></div><span style="padding-left:25px;">Yes</span></label><label class=""><div style="position:relative;top:4px;"><input style="position: absolute;left:8px;" type="radio" name="ERROR_LOGGING" value="0" $elof></div><span style="padding-left:36px;">No</span></label></div>
									</div>
								</div>

								<div class="form-group row">
									<div class="col-md-12">
							      	<label class="form-control-label">Cross Domain:</label>
							      		<div class=""><label class=""><div style="position:relative;top:4px;"><input style="position: absolute;" type="radio" id="cdon" name="CROSS_DOMAIN" value="1" $cdo ></div><span style="padding-left:25px;">Yes</span></label><label class=""><div style="position:relative;top:4px;"><input style="position: absolute;left:8px;" type="radio" id="cdoff" name="CROSS_DOMAIN" value="0" $cdof></div><span style="padding-left:36px;">No</span></label></div>
									</div>
								</div>

								<div class="form-group row">
									<div class="col-md-12">
							      	<label class="form-control-label">Avoid callbacks?:</label>
							      		<div class=""><label class=""><div style="position:relative;top:4px;"><input style="position: absolute;" type="radio" name="AVOIDCALLBACK" value="1" $avoidcby ></div><span style="padding-left:25px;">Yes</span></label><label class=""><div style="position:relative;top:4px;"><input style="position: absolute;left:8px;" type="radio" name="AVOIDCALLBACK" value="0" $avoidcbn></div><span style="padding-left:36px;">No</span></label></div>
									</div>
								</div>

								<div class="form-group row">
									<div class="col-md-12">
							      	<label class="form-control-label">Enable GZIP?:</label>
							      		<div class=""><label class=""><div style="position:relative;top:4px;"><input style="position: absolute;" type="radio" name="GZIP_ENABLED" value="1" $gzipyes ></div><span style="padding-left:25px;">Yes</span></label><label class=""><div style="position:relative;top:4px;"><input style="position: absolute;left:8px;" type="radio" name="GZIP_ENABLED" value="0" $gzipno></div><span style="padding-left:36px;">No</span></label></div>
									</div>
								</div>

								<div class="form-group row">
									<div class="col-md-12">
							      	<label class="form-control-label">Unique guest name?:</label>
							      		<div class=""><label class=""><div style="position:relative;top:4px;"><input style="position: absolute;" type="radio" name="uniqueguestname" value="1" $uniqueyes ></div><span style="padding-left:25px;">Yes</span></label><label class=""><div style="position:relative;top:4px;"><input style="position: absolute;left:8px;" type="radio" name="uniqueguestname" value="0" $uniqueno></div><span style="padding-left:36px;">No</span></label></div>
									</div>
								</div>

								<div id="ccurl" class="form-group row serverurl_text" $showSiteUrl>
									<div class="col-md-12">
							      		<label class="form-control-label">Enter Site URL:</label>
							      		<input class="form-control" id="CC_SITE_URL" name="CC_SITE_URL" value="{$CC_SITE_URL}" autocomplete="off" type="text">
									</div>
								</div>

								<div class="form-group row">
									<div class="col-md-12">
							      	<label class="form-control-label">Guest-ID start: (Guest id must be greater than 10000000)</label>
							      		<input class="form-control" name="firstguestID" value="$firstguestID" type="number" min="10000000" max="1000000000">
									</div>
								</div>

								<div class="form-group row">
									<div class="col-md-12">
							      	<label class="form-control-label">Relay Port: (Used for audio/video chat CS)</label>
							      		<input class="form-control" name="CS_RELAY_PORT" value="{$CS_RELAY_PORT}" type="number">
									</div>
								</div>

								<div class="form-group row">
									<div class="col-md-12">
							      	<label class="form-control-label"> CS SELFHOSTED URL Path: (Used for TextChat of CS)</label>
							      		<input class="form-control" name="CS_URL_PATH" value="{$CS_URL_PATH}">
									</div>
								</div>

								<div class="form-group row">
									<div class="col-md-12">
							      	<label class="form-control-label">Use CometService Legacy? (Not recommended; Contact CometChat Support before enabling)</label>
							      		<div class=""><label class=""><div style="position:relative;top:4px;"><input style="position: absolute;" type="radio" name="USE_CS_LEGACY" value="1" $usecslegacyy ></div><span style="padding-left:25px;">Yes</span></label><label class=""><div style="position:relative;top:4px;"><input style="position: absolute;left:8px;" type="radio" $usecslegacyn name="USE_CS_LEGACY" value="0"></div><span style="padding-left:36px;">No</span></label></div>
									</div>
								</div>

								<div class="row col-md-10" style="padding-bottom:5px;">
							      	<input type="submit" value="Update"  class="btn btn-primary">
							    </div>
							</form>

							<div class="row col-md-10" style="padding-bottom:5px;">
						      	<label class="form-control-label">

						      	</label>
						    </div>

							<div class="row col-md-10" style="padding-bottom:5px;">
						      	<label class="form-control-label">
						      		<a href="?module=settings&action=downloadlogs&log=error&ts={$ts}">Download Error Log</a>
						      	</label>
						    </div>

						    <div class="row col-md-10" style="padding-bottom:5px;">
						      	<label class="form-control-label">
						      		<a href="?module=settings&action=downloadlogs&log=event&ts={$ts}">Download Event Log</a>
						      	</label>
						    </div>

						    <div class="row col-md-10" style="padding-bottom:5px;">
						      	<label class="form-control-label">
						      		<a href="?module=settings&action=downloadlogs&log=system&ts={$ts}">Download System Log</a>
						      	</label>
						    </div>

						</div>
					</div>
				</div>
			</div>
		  </div>
		  	<div class="col-sm-6 col-lg-6">
				<div class="row">

				</div>
			</div>
		</div>
		<script type="text/javascript">
				$(function() {
					if($('input[name=CROSS_DOMAIN]:checked').val() == 0){
						$("#ccurl").hide();
					}
					$('#cdoff').click(function(){
						$('#ccurl').hide('slow');
					});
					$('#cdon').click(function(){
						$('#ccurl').show('slow');
					});
					$("#leftnav_settings").find('a').removeClass('active_setting');
					$("#dev_settings").addClass('active_setting');

					$("#devsetting").submit(function(){
						var CROSS_DOMAIN = $('input[name=CROSS_DOMAIN]:checked').val();
						var CC_SITE_URL = $('input[name=CC_SITE_URL]').val();
						if(CROSS_DOMAIN == 1 && CC_SITE_URL == ''){
							alert("Please enter your site URL");
							return false;
						}
						return true;
					});
				});
			</script>
EOD;


	}
		template();
}
function updateccstatus(){
    global $ts;

    $_POST['BAR_DISABLED'] = (!isset($_POST['BAR_DISABLED'])) ? 0: 1;

	configeditor($_POST);
	$_SESSION['cometchat']['error'] = 'Settings updated successfully';
	header("Location:?module=settings&action=generalsettings&ts={$ts}");
	exit();
}

function updatedevsetting(){
    global $ts;
	configeditor($_POST);

	$query = sql_query('getTblDetails',array('table'=>'cometchat_guests', 'key' => 'id', 'value' => $_REQUEST['firstguestID']));
	$row = sql_fetch_assoc($query);

	if (count($row) && !empty($_REQUEST['firstguestID']) && $_REQUEST['firstguestID'] != '') {
		$result = sql_query('insertFirstGuestID',array('id'=>$_REQUEST['firstguestID']));
	}

	$_SESSION['cometchat']['error'] = 'Settings updated successfully';
	if (!empty($_REQUEST['dev']) && $_REQUEST['dev'] == 1) {
		header("Location:?module=settings&action=devsettings&dev=1&ts={$ts}");
		exit();
	} else {
		header("Location:?module=settings&action=generalsettings&ts={$ts}");
		exit();
	}

}

function downloadlogs(){
    global $ts, $currentversion;

    $log = $_REQUEST['log'];
    $file = $log.'_'.$currentversion.'.log';

    if($log == 'system'){
    	$file = 'systeminfo.html';
    	ob_start();
		phpinfo();
		$filedata = ob_get_contents();
		$filesize = ob_get_length();
    } else {
	    $filepath = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'writable'.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.($file);
		$filedata = file_exists($filepath);
		$filesize = filesize($filepath);
		$filedata = readfile($filepath);
    }
    if ($filedata){
			header('Content-Description: File Transfer');
			header('Content-Type: application/force-download');
			header('Content-Disposition: attachment; filename='.$file);
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . $filesize);
			ob_start();
			ob_clean();
			flush();
		}
    header("Location:?module=settings&action=devsettings&dev=1&ts={$ts}");
    exit();
}

function clearcachefilesprocess() {
    global $ts,$client;
	$_SESSION['cometchat']['error'] = 'Cache cleared successfully';

	clearcachejscss(dirname(dirname(__FILE__)));
	if(function_exists('purgecache')) {
		purgecache($client);
	}
	if(isset($_SERVER['HTTP_REFERER'])) {
		header("Location:".$_SERVER['HTTP_REFERER']);
		exit();
	}
	header("Location:?module=settings&action=generalsettings&ts={$ts}");
}

function cometchatrmdir($dir) {
	if (is_dir($dir)) {
		$objects = scandir($dir);
		foreach ($objects as $object) {
			if ($object != "." && $object != "..") {
				if (filetype($dir."/".$object) == "dir")
					cometchatrmdir($dir."/".$object);
				else
					unlink   ($dir."/".$object);
			}
		}
		reset($objects);
		rmdir($dir);
	}
}

function banuser() {
	global $body,$navigation,$bannedUserIDs,$bannedUserIPs,$bannedMessage,$bannedWords,$ts;
	$bannedids = $bannedips = '';
	foreach ($bannedUserIDs as $b) {
		$bannedids .= $b.', ';
	}
	foreach ($bannedUserIPs as $b) {
		$bannedips .= $b.', ';
	}
	$bannedw = '';
	foreach ($bannedWords as $b) {
		$bannedw .= "'".$b.'\', ';
	}
$body = <<<EOD
<div class="col-sm-12 col-lg-12">
<div class="card">
  	<div class="card-header">
    	Banned Words and Users
  		<h4><small>You can ban users and add words to the abusive list.</small></h4>
  	</div>
  	<div class="card-block">
      	<form action="?module=settings&action=banuserprocess&ts={$ts}" method="post">
	  		<div class="form-group row">
				<div class="col-md-12">
				<label class="form-control-label">Banned Words:</label>
				<input class="form-control" type="text" name="bannedWords" value="$bannedw">
				</div>
			</div>

	  		<div class="form-group row">
				<div class="col-md-12">
				<label class="form-control-label">Banned User IDs:</label>
				<input class="form-control" type="text" name="bannedUserIDs" value="$bannedids">
				</div>
			</div>

	  		<div class="form-group row">
				<div class="col-md-12">
				<label class="form-control-label">Banned User IPs:</label>
				<input class="form-control" type="text" name="bannedUserIPs" value="$bannedips">
				</div>
			</div>

	  		<div class="form-group row">
				<div class="col-md-12">
				<label class="form-control-label">Banned Message:</label>
				<input class="form-control" type="text" name="bannedMessage" value="$bannedMessage" required="true">
				</div>
			</div>

			<div class="row col-md-12">
		      <input type="submit" value="Update"  class="btn btn-primary">
		    </div>
	   	</form>
    </div>
	<div class="card-footer">
		<a class="searchuser" href="javascript:void(0);">If you do not know the user's ID</a>
	</div>
</div>
</div>
<script>
	$(function(){
		$(this).click(function(){
			$('#suggestions').html('');
		});
		$(".searchuser").click(function(){
			$("#adminModellink").trigger('click');
			$("#admin-modal-title").text('Find User ID');
			$("#admin-modal-body").css('height', '200px');
			var form ='<div class="form-group row">';
				form +='<div class="col-md-12">';
				form +='<label class="form-control-label">Username:</label>';
				form +='<input type="text" class="form-control" id="susername" name="susername" required="true" autocomplete="off" placeholder="Enter Username">';
				form +='</div></div>';
			 	form +='<div class="form-group row">';
				form +='<div class="col-md-12">';
				form +='<button  type="button" id="search" class="btn btn-primary">Search</button>';
				form +='</div></div>';

			$("#admin-modal-body").html(form);
			$("#search").off('click').click(function(){
				var user = $("#susername").val();
				if(user == ''){
					alert("please enter the username");
					return false;
				}else{
			       	$.ajax({
						type: "POST",
						url: "?module=settings&action=searchlogs&ts={$ts}",
						data: {susername: user},
						success: function(data) {
							$("#admin-modal-title").text('Search Result');
							$("#admin-modal-body").css('height', '520px');
							$("#admin-modal-body").html(data);
							$('[data-toggle="tooltip"]').tooltip();
			            }
					});
				}
			});
		});
	});
</script>
EOD;
return $body;
}


function banuserprocess() {
    global $ts;
	if (!empty($_POST['bannedMessage'])) {
		if(!empty($_POST['bannedUserIDs'])){
			$_POST['bannedUserIDs'] = rtrim($_POST['bannedUserIDs'], ', ');
    		$_POST['bannedUserIDs'] = explode(', ', $_POST['bannedUserIDs']);
		}else{
			$_POST['bannedUserIDs'] = array();
		}

		if(!empty($_POST['bannedWords'])){
			$_POST['bannedWords'] = rtrim($_POST['bannedWords'], ', ');
			$_POST['bannedWords'] = str_replace("'", "", $_POST['bannedWords']);
    		$_POST['bannedWords'] = explode(', ', $_POST['bannedWords']);
		}else{
			$_POST['bannedWords'] = array();
		}

		if(!empty($_POST['bannedUserIPs'])){
			$_POST['bannedUserIPs'] = rtrim($_POST['bannedUserIPs'], ', ');
    		$_POST['bannedUserIPs'] = explode(', ', $_POST['bannedUserIPs']);
		}else{
			$_POST['bannedUserIPs'] = array();
		}
		$_SESSION['cometchat']['error'] = 'Banned words and users successfully modified.';

		configeditor($_POST);
	}
	header("Location:?module=settings&action=generalsettings&ts={$ts}");
}

function dockedsettings() {
	include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."layouts".DIRECTORY_SEPARATOR."docked".DIRECTORY_SEPARATOR."config.php");

	global $body,$navigation,$ts;

	$options = array(
	    "disableDockedLayout" => array('choice', 'Disable Docked Layout'),
	    "dockedChatBoxAvatar" => array('choice', 'Show Avatar in ChatBox'),
	    "dockedAlignToLeft" => array('choice', 'Align Docked Layout to the left?'),
	    "dockedChatListAudioCall" => array('choice', 'Show Audio Call button in Contacts list?'),
	);
	$UI = showSettingsUI($options);
$body = <<<EOD
<div class="col-sm-12 col-lg-12">
<div class="card">
  	<div class="card-header">
    	Docked Layout Settings
  		<h4><small>You can set height and width of chatboxes for docked layout.</small></h4>
  	</div>
  	<div class="card-block">
      	<form action="?module=settings&action=dockedsettingsprocess&ts={$ts}" method="post">
			{$UI}
	  		<div class="form-group row">
				<div class="col-md-12">
				<label class="form-control-label">Chatbox Height (Minimum Height can be 350px)</label>
				<input class="form-control" type="text" name="chatboxHeight" value="$chatboxHeight">
				</div>
			</div>

	  		<div class="form-group row">
				<div class="col-md-12">
				<label class="form-control-label">Chatbox Width (Minimum Width can be 230px)</label>
				<input class="form-control" type="text" name="chatboxWidth" value="$chatboxWidth">
				</div>
			</div>
		    <div class="row col-md-10" style="padding-bottom:5px;"><br>
		      <input type="submit" value="Update"  class="btn btn-primary">
		    </div>
	   	</form>
    </div>
</div>
</div>

EOD;
return $body;
}


function dockedsettingsprocess() {
    global $ts;
	if (!empty($_POST['chatboxHeight']) && !empty($_POST['chatboxWidth'])) {
		$_SESSION['cometchat']['error'] = 'Docked Layout Settings successfully updated.';
		configeditor($_POST);
	}
	header("Location:?module=settings&action=generalsettings&ts={$ts}");
}

function changeuserpass() {
	if (!empty($GLOBALS['client'])) { header("Location:?module=dashboard&ts=".$GLOBALS['ts']); exit; }
	global $body;
	global $navigation;
    global $ts;

	$nuser = ADMIN_USER;
	$npass = ADMIN_PASS;

$body = <<<EOD
  	<div class="col-sm-12 col-lg-12">
	    <div class="card">
	      	<div class="card-header">
	        	Admin Panel Login Details
	      	</div>
	      	<div class="card-block">

	      	<form action="?module=settings&action=changeuserpassprocess&ts={$ts}" method="post">

      		<div class="form-group row">
				<div class="col-md-12">
				<label class="form-control-label">Email:</label>
				<input class="form-control" name="ADMIN_USER" placeholder="Enter your email" value="$nuser" required="true" autocomplete="off" type="email">
				</div>
			</div>

			<div class="form-group row">
				<div class="col-md-12">
				<label class="form-control-label">Old Password:</label>
				<input class="form-control" name="old_PASS" value="" required="true" autocomplete="off" type="password">
				</div>
			</div>

      		<div class="form-group row">
				<div class="col-md-12">
				<label class="form-control-label">New Password:</label>
				<input class="form-control" name="ADMIN_PASS" value="" required="true" autocomplete="off" type="password">
				</div>
			</div>

			<div class="form-group row">
				<div class="col-md-12">
				<label class="form-control-label">Confirm New Password:</label>
				<input class="form-control" name="Confirm_PASS" value="" required="true" autocomplete="off" type="password">
				</div>
			</div>

			<div class="row col-md-12" style="padding-bottom:5px;"><br>
		      <input type="submit" value="Update"  class="btn btn-primary">
		    </div>

		   </form>
            </div>
    	</div>
  	</div>
EOD;
return $body;
}

function changeuserpassprocess() {
	global $ts;
	if (!empty($_POST['ADMIN_USER']) && !empty($_POST['ADMIN_PASS']) && !empty($_POST['Confirm_PASS']) && !empty($_POST['old_PASS'])) {
		if((sha1($_POST['old_PASS'])==ADMIN_PASS) || ($_POST['old_PASS']==ADMIN_PASS)){
			if($_POST['ADMIN_PASS']==$_POST['Confirm_PASS']){
				$_POST['ADMIN_PASS']=sha1($_POST['ADMIN_PASS']);
				$_SESSION['cometchat']['error'] = 'User/pass successfully modified';
				configeditor($_POST);
			}else{
				$_SESSION['cometchat']['error'] = 'Comfirm password did not match';
				$_SESSION['cometchat']['type'] = 'alert';
				header("Location:?module=settings&action=generalsettings&ts={$ts}");
				exit();
			}
		}else{
			$_SESSION['cometchat']['error'] = 'Old password did not match';
			$_SESSION['cometchat']['type'] = 'alert';
			header("Location:?module=settings&action=generalsettings&ts={$ts}");
			exit();
		}
	}
	header("Location:?module=dashboard&ts={$ts}");
}

function updatebaseurl() {
	if (!empty($GLOBALS['client'])) { header("Location:?module=dashboard&ts=".$GLOBALS['ts']); exit; }
    global $ts;
	if (!empty($_POST['BASE_URL'])) {

		$baseurl = str_replace('\\', '/',$_POST['BASE_URL']);

		if ($baseurl[0] != '/' && strpos($baseurl,'http://')===false && strpos($baseurl,'https://')===false) {
			$baseurl = '/'.$baseurl;
		}

		if ($baseurl[strlen($baseurl)-1] != '/') {
			$baseurl = $baseurl.'/';
		}

		$_SESSION['cometchat']['error'] = 'Base URL successfully modified';
		configeditor($_POST);
	}
	header("Location:?module=settings&action=generalsettings&ts={$ts}");
}



function comet() {
	if ( !empty($GLOBALS['client']) && (empty($_SERVER['environment'])||(!empty($_SERVER['environment']) && $_SERVER['environment']!='dev'))) {
		header("Location:?module=dashboard&ts=".$GLOBALS['ts']);
		exit;
	}
	global $body, $navigation, $ts, $cometservice;
	$cometchecky = "";
	$cometcheckn = "";
	$isTypingy = "";
	$isTypingn = "";
	$cometselfhostedy = "";
	$cometselfhostedn = "";
	$messagereceipty = "";
	$messagereceiptn = "";
	$messagesyncy = "";
	$messagesyncn = "";
	$usecslegacyHTML = "";

	if (defined('USE_COMET') && USE_COMET == 1) {
		$cometchecky = "checked";
	} else {
		$cometcheckn = "checked";
	}

	if (defined('IS_TYPING') && IS_TYPING == 1) {
		$isTypingy = "checked";
	} else {
		$isTypingn = "checked";
	}

	if (defined('MESSAGE_RECEIPT') && MESSAGE_RECEIPT == 1) {
		$messagereceipty = "checked";
	} else {
		$messagereceiptn = "checked";
	}

	if (defined('CS_MESSAGE_SYNC') && CS_MESSAGE_SYNC == 1) {
		$messagesyncy = "checked";
	} else {
		$messagesyncn = "checked";
	}

	if(USE_COMET == 1){
		$cometservice = 'style="display:block"';
	}else{
		$cometservice = 'style="display:none"';
	}

	$keya = KEY_A;
	$keyb = KEY_B;
	$keyc = KEY_C;
	$transport = TRANSPORT;
	$server_url = CS_TEXTCHAT_SERVER;
	$cs_http_port = CS_HTTP_PORT;
	$cs_https_port = CS_HTTPS_PORT;
	$cs_domain_name = CS_DOMAIN_NAME;
	$required = 'required="true"';
	$cometserviceselfhosted = 0;
	if($transport == 'cometserviceselfhosted') {
		$cometselfhostedy = "checked";
		$required = '';
	} else {
		$cometselfhostedn = "checked";
	}

	$overlay = '';
	if (file_exists(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'transports'.DIRECTORY_SEPARATOR.'cometserviceselfhosted'.DIRECTORY_SEPARATOR.'comet.php')) {
		$cometserviceselfhosted = 1;
	}
	if (!checkCurl()) {
		$overlay = <<<EOD
			<script>
			jQuery('#error_div').before('<div class="col-sm-12 col-lg-12" id="overlaymain" style="position:relative"></div>');
				var overlay = $('<div></div>')
					.attr('id', 'overlay')
					.css({
						'position':'absolute',
						'height':$('#error_div').innerHeight(),
						'width':$('#error_div').innerWidth(),
						'background-color':'#FFFFFF',
						'opacity':'0.9',
						'z-index':'99',
						'right': '0',
						'margin-left':'1px'
					})
					.appendTo('#overlaymain');
					$('<div class="col-sm-12 col-lg-12">cURL extension is disabled on your server. Please contact your webhost to enable it.<br> cURL is required for CometService.</div>')
						.css({'z-index':' 9999',
						'color':'#000000',
						'font-size':'15px',
						'font-weight':'bold',
						'display':'block',
						'text-align':'center',
						'margin':'auto',
						'position':'absolute',
						'width':'100%',
						'top':'100px'
					}).appendTo('#overlaymain');

			</script>
EOD;
	}


$body .= <<<EOD
<div class="row">
	<div class="col-sm-12 col-lg-12" id="error_div">
    <div class="card">
      	<div class="card-header">
        	CometService
        	<h4><small>If you are using CometService, please enter the details here</small></h4>
      	</div>
      	<div class="card-block">
      	<form action="?module=settings&action=updatecomet&ts={$ts}" method="post">

      	<div class="form-group row"><div class="col-md-12">
      	<label class="form-control-label">Use CometService?</label>
			<div class=""><label class=""><div style="position:relative;top:4px;"><input style="position: absolute;" id="cs1" class="comet" type="radio" name="dou" value="1" $cometchecky type="radio" ></div><span style="padding-left:25px;">Yes</span></label><label class=""><div style="position:relative;top:4px;"><input style="position: absolute;left:8px;" id="cs2" class="comet" type="radio" $cometcheckn name="dou" value="0"></div><span style="padding-left:36px;">No</span></label></div>
		</div></div>

		<div class="enabled_cs" $cometservice>

			<div class="form-group row">
				<div class="col-md-12">
		      	<label class="form-control-label">Use Self-hosted CometService?</label>
		      		<div class=""><label class=""><div style="position:relative;top:4px;"><input style="position: absolute;" type="radio" name="dos" value="1" $cometselfhostedy ></div><span style="padding-left:25px;">Yes</span></label><label class=""><div style="position:relative;top:4px;"><input style="position: absolute;left:8px;" type="radio" name="dos" value="0" $cometselfhostedn></div><span style="padding-left:36px;">No</span></label></div>
				</div>
			</div>

			<div class="form-group row serverurl_text" style="display:none;">
				<div class="col-md-12">
		      	<label class="form-control-label">Domain Name:</label>
		      		<input class="form-control" name="cs_domain_name" value="$cs_domain_name" placeholder="yoursite.com" type="text">
				</div>
			</div>

			<div class="form-group row serverurl_text" style="display:none;">
				<div class="col-md-12">
		      	<label class="form-control-label">HTTPS Port:</label>
		      		<input class="form-control" name="cs_https_port" value="$cs_https_port" placeholder="443 or 8443" type="text">
				</div>
			</div>

			<div class="form-group row serverurl_text" style="display:none;">
				<div class="col-md-12">
		      	<label class="form-control-label">HTTP Port:</label>
		      		<input class="form-control" name="cs_http_port" value="$cs_http_port" placeholder="80 or 8080" type="text">
				</div>
			</div>

		    <div class="cckeys">
				<div class="form-group row">
					<div class="col-md-12">
			      	<label class="form-control-label">Key A:</label>
			      		<input class="form-control cometkeys" name="keya" value="$keya" {$required} type="text">
					</div>
				</div>
				<div class="form-group row">
					<div class="col-md-12">
			      	<label class="form-control-label">Key B:</label>
			      		<input class="form-control cometkeys" name="keyb" value="$keyb" {$required} type="text">
					</div>
				</div>
				<div class="form-group row">
					<div class="col-md-12">
			      	<label class="form-control-label">Key C:</label>
			      		<input class="form-control cometkeys" name="keyc" value="$keyc" {$required} type="text">
					</div>
				</div>
			</div>

			<div class="form-group row">
				<div class="col-md-12">
		      	<label class="form-control-label">Enable Typing Notification?</label>
		      		<div class=""><label class=""><div style="position:relative;top:4px;"><input style="position: absolute;" type="radio" name="typ" value="1" $isTypingy ></div><span style="padding-left:25px;">Yes</span></label><label class=""><div style="position:relative;top:4px;"><input style="position: absolute;left:8px;" type="radio" name="typ" $isTypingn value="0"></div><span style="padding-left:36px;">No</span></label></div>
				</div>
			</div>

			<div class="form-group row">
				<div class="col-md-12">
		      	<label class="form-control-label">Enable Receipt Notification?</label>
		      		<div class=""><label class=""><div style="position:relative;top:4px;"><input style="position: absolute;" type="radio" name="rec" value="1" $messagereceipty ></div><span style="padding-left:25px;">Yes</span></label><label class=""><div style="position:relative;top:4px;"><input style="position: absolute;left:8px;" type="radio" $messagereceiptn name="rec" value="0"></div><span style="padding-left:36px;">No</span></label></div>
				</div>
			</div>

			<div class="form-group row">
				<div class="col-md-12">
		      	<label class="form-control-label">Synchronize messages across all user devices? (Not recommended; Will increase usage of CometService on your server)</label>
		      		<div class=""><label class=""><div style="position:relative;top:4px;"><input style="position: absolute;" type="radio" name="cs_sync" value="1" $messagesyncy ></div><span style="padding-left:25px;">Yes</span></label><label class=""><div style="position:relative;top:4px;"><input style="position: absolute;left:8px;" type="radio" $messagesyncn name="cs_sync" value="0"></div><span style="padding-left:36px;">No</span></label></div>
				</div>
			</div>
	    </div>
		<div class="row col-md-10" style="padding-bottom:5px;"><br>
	      <input type="submit" value="Update"  class="btn btn-primary">
	    </div>
	   </form>
        </div>
	</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		var cometenabled = $("input:radio[name=dou]:checked").val();
		if(cometenabled == 1){
			$('.enabled_cs').show();
		}
		var cometserviceselfhosted = $cometserviceselfhosted;
		if(cometserviceselfhosted != 1){
			$('.cometserviceselfhosted').hide();
		}
		$('input:radio[name=dou]').change(function(){
			cometenabled = $(this).val();
			if(cometenabled == 1){
				$('.enabled_cs').show();
			} else {
				$('.enabled_cs').hide();
			}
		});
		var transport = "$transport";
		if(transport == 'cometserviceselfhosted'){
			$('.serverurl_text').show();
			$('.cometkeys').removeAttr('required');
			$('.cckeys').hide();
		}
		$('input:radio[name=dos]').change(function(){
			selfhostedenabled = $(this).val();
			if(selfhostedenabled == 1){
				$('.serverurl_text').show();
				$('.cometkeys').removeAttr('required');
				$('input:text[name=cs_domain_name]').attr('required', 'true');
				$('input:text[name=cs_http_port]').attr('required', 'true');
				$('input:text[name=cs_https_port]').attr('required', 'true');
				$('.cckeys').hide();
			} else {
				$('.serverurl_text').hide();
				$('.cckeys').show();
				$('.cometkeys').attr('required', 'true');
				$('input:text[name=cs_domain_name]').removeAttr('required');
				$('input:text[name=cs_http_port]').removeAttr('required');
				$('input:text[name=cs_https_port]').removeAttr('required');
			}
		});
		$("#leftnav_settings").find('a').removeClass('active_setting');
		$("#cometservice_settings").addClass('active_setting');
	});
</script>
{$overlay}
EOD;
	template();
}

function updatecomet() {
	if ( !empty($GLOBALS['client']) && (empty($_SERVER['environment'])||(!empty($_SERVER['environment']) && $_SERVER['environment']!='dev'))) {
		header("Location:?module=dashboard&ts=".$GLOBALS['ts']);
		exit;
	}

    global $ts;
	$_SESSION['cometchat']['error'] = 'Comet service settings successfully updated';
	if($_POST['dos'] == 1){
		$transport = 'cometserviceselfhosted';
	} else {
		$transport = 'cometservice';
	}
	$data = array('USE_COMET' => $_POST['dou'],
				'KEY_A' => trim($_POST['keya']),
				'KEY_B' => trim($_POST['keyb']),
				'KEY_C' => trim($_POST['keyc']),
				'IS_TYPING' => $_POST['typ'],
				'MESSAGE_RECEIPT' => $_POST['rec'],
				'CS_MESSAGE_SYNC' => $_POST['cs_sync'],
				'TRANSPORT' => $transport,
				'CS_DOMAIN_NAME' => trim($_POST['cs_domain_name']),
				'CS_HTTP_PORT' => trim($_POST['cs_http_port']),
				'CS_HTTPS_PORT' => trim($_POST['cs_https_port']),
				);
	configeditor($data);
	header("Location:?module=settings&action=comet&ts={$ts}");
}



function searchlogs() {
    global $ts,$usertable_userid,$usertable_username,$usertable,$body,$bannedUserIDs;
	$username = $_REQUEST['susername'];
	if (empty($username)) {
		$username = 'Q293YXJkaWNlIGFza3MgdGhlIHF1ZXN0aW9uIC0gaXMgaXQgc2FmZT8NCkV4cGVkaWVuY3kgYXNrcyB0aGUgcXVlc3Rpb24gLSBpcyBpdCBwb2xpdGljPw0KVmFuaXR5IGFza3MgdGhlIHF1ZXN0aW9uIC0gaXMgaXQgcG9wdWxhcj8NCkJ1dCBjb25zY2llbmNlIGFza3MgdGhlIHF1ZXN0aW9uIC0gaXMgaXQgcmlnaHQ/DQpBbmQgdGhlcmUgY29tZXMgYSB0aW1lIHdoZW4gb25lIG11c3QgdGFrZSBhIHBvc2l0aW9uDQp0aGF0IGlzIG5laXRoZXIgc2FmZSwgbm9yIHBvbGl0aWMsIG5vciBwb3B1bGFyOw0KYnV0IG9uZSBtdXN0IHRha2UgaXQgYmVjYXVzZSBpdCBpcyByaWdodC4=';
	}

	$query = sql_query('admin_getbanneduserID',array('usertable_userid'=>$usertable_userid, 'usertable_username'=>$usertable_username, 'usertable'=>$usertable, 'username'=>sanitize_core($username)));
	$userslist = '';
	while ($user = sql_fetch_assoc($query)) {
		if (function_exists('processName')) {
			$user['username'] = processName($user['username']);
		}
        $banuser = '<a style="color:red;opacity:0.2;" title="Ban the user" data-toggle="tooltip" href="?module=settings&amp;action=banusersprocess&amp;susername='.$username.'&amp;bannedids='.$user['id'].'&amp;ts='.$ts.'"><i class="fa fa-lg fa-ban"></i></a>';

        if(in_array($user['id'],$bannedUserIDs)) {
            $banuser = '<a style="color:red;" title="Unban the user" data-toggle="tooltip" href="?module=settings&amp;action=unbanusersprocess&amp;susername='.$username.'&amp;bannedids='.$user['id'].'&amp;ts='.$ts.'"><i class="fa fa-lg fa-ban"></i></a>';
        }
		$userslist .= '<tr><td>'.$user['username'].'</td><td>'.$user['id'].'</td><td>'.$banuser.'</td></tr>';
	}
	if ($userslist == '') {
		$userslist = '<tr><td colspan="3">No record found.</td></tr>';
	}
	echo <<<EOD
		<div style="height:500px;overflow:auto;overflow-x:hidden;">
		<table class="table">
			<thead>
				<tr><th width="50%">Name</th><th>ID</th>
				<th width="5%">&nbsp;</th></tr>
			</thead>
			<tbody>
			$userslist
		</table>
EOD;
}

function banusersprocess() {
    global $ts;
    global $bannedUserIDs;

    array_push($bannedUserIDs, $_REQUEST['bannedids']);
    $_SESSION['cometchat']['error'] = 'Ban ID list successfully modified.';

    configeditor(array('bannedUserIDs' => $bannedUserIDs));
    header("Location:?module=settings&action=generalsettings&ts={$ts}");
}

function unbanusersprocess() {
    global $ts;
    global $bannedUserIDs;

    if(($key = array_search($_GET['bannedids'], $bannedUserIDs)) !== false) {
        unset($bannedUserIDs[$key]);
    }
    $unbanarray = array_values($bannedUserIDs);

    $_SESSION['cometchat']['error'] = 'Ban ID list successfully modified.';
    configeditor(array('bannedUserIDs' => $unbanarray));
	header("Location:?module=settings&action=generalsettings&ts={$ts}");
}

function cron() {
	if (!empty($GLOBALS['client'])) { header("Location:?module=dashboard&ts=".$GLOBALS['ts']); exit; }
	global $body, $navigation, $trayicon, $plugins, $disableGroupTab, $ts;

	$auth = md5(md5(ADMIN_USER).md5(ADMIN_PASS));
	$baseurl = BASE_URL;
$body .= <<<EOD
<div class="row">
      <div class="col-sm-12 col-lg-12">
        <div class="card">
              <div class="card-header">
                Clean Up
                </h4>
              </div>
              <div class="card-block">
              <form action="?module=settings&action=processcron&ts={$ts}" method="post" onsubmit="return cron_submit()">

                <div class="form-group row">
                    <div class="col-md-12">
                      <input id='individual' type="radio" name="cron[type]" value="individual" onclick="javascript:$('#individualcat').show('slow')" checked>&nbsp;&nbsp;<b>Run Individual Clean Up</b>
                    </div>
                </div>
                <div id ="individualcat">
                <table class="table">
                    <tr>
                        <td width="5%"><input id="plugins" type="checkbox" name="cron[plugins]" value="1"  class="title_class" onclick="check_all('plugins', 'sub_plugins', '{$auth}')"></td>
                        <td colspan="2" onclick="javascript:$('.sub_plugins').toggle('slow')" style="cursor: pointer;"> Clean Up All Media & files</td>
                        <td><a data-toggle="tooltip" title="Clean Up URL Code" href="javascript:void(0)" style="float: right;" onclick="javascript:cron_auth_link('{$baseurl}', 'plugins', '{$auth}')"><i class="fa fa-lg fa-code"></i></a></td>
                    </tr>

                    <tr>
                        <td><input id="core" type="checkbox" name="cron[core]" value="1" class="title_class" onclick="check_all('core', 'sub_core', '{$auth}')"></td>
                        <td colspan="2" onclick="javascript:$('.sub_core').toggle('slow')" style="cursor: pointer;"> Clean Up Old Messages And Inactive Guest Entries</td>
                        <td><a data-toggle="tooltip" title="Clean Up URL Code" onclick="javascript:cron_auth_link('{$baseurl}', 'core', '{$auth}')" href="javascript:void(0)" style="float: right;"><i class="fa fa-lg fa-code"></i></a></td>
                    </tr>

                    <tr>
                        <td><input id="modules" type="checkbox" name="cron[modules]" value="1" class="title_class" onclick="check_all('modules', 'sub_modules', '{$auth}')"></td>
                        <td colspan="2"  onclick="javascript:$('.sub_modules').toggle('slow')" style="cursor: pointer;"> Clean Up Inactive Groups and Old Messages</td>
                        <td><a data-toggle="tooltip" title="Clean Up URL Code" href="javascript:void(0)" style="float: right;"  onclick="javascript:cron_auth_link('{$baseurl}', 'modules', '{$auth}')"><i class="fa fa-lg fa-code"></i></a></td>
                    </tr>
                </table>
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                          <input id='all' type="radio" name="cron[type]" value="all" onclick="javascript:$('#individualcat').hide('slow')" >&nbsp;&nbsp;<b>Run Entire Clean Up</b>
                    </div>
                </div>

            <div class="row col-md-10" style="padding-bottom:5px;"><br>
            <input type="hidden" value="{$auth}" name="auth">
              <input type="submit" value="Run" class="btn btn-primary">
            </div>

           </form>
            </div>
        </div>
      </div>
</div>
EOD;
    template();
}

function processcron() {
	if (!empty($GLOBALS['client'])) { header("Location:?module=dashboard&ts=".$GLOBALS['ts']); exit; }
	global $ts;
	$auth = md5(md5(ADMIN_USER).md5(ADMIN_PASS));
	include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'cron.php');
	$_SESSION['cometchat']['error'] = 'Cron executed successfully';
	header("Location:?module=settings&action=cron&ts={$ts}");
}

function guestlogin(){

	global $body, $ts, $settings, $guestsMode, $guestsList, $guestsUsersList, $guestnamePrefix;

	$dy = "";
	$dn = "";
	$gL1 = $gL2 = $gL3 = $gUL1 = $gUL2 = $gUL3 = '';

	if ($guestsMode == 1) {
		$dy = "checked";
	} else {
		$dn = "checked";
	}

	if ($guestsList == 1) {	$gL1 = "selected"; }
	if ($guestsList == 2) {	$gL2 = "selected"; }
	if ($guestsList == 3) {	$gL3 = "selected"; }

	if ($guestsUsersList == 1) { $gUL1 = "selected"; }
	if ($guestsUsersList == 2) { $gUL2 = "selected"; }
	if ($guestsUsersList == 3) { $gUL3 = "selected"; }

$body = <<<EOD
  	<div class="col-sm-12 col-lg-12">
	    <div class="card">
	      	<div class="card-header">
	        	Guest Settings
	      	</div>
	      	<div class="card-block">
	      	<form action="?module=settings&action=updatesettings&ts={$ts}" method="post">

		    <div id="guest">
				<div class="form-group row">
					<div class="col-md-12">
			      	<label class="form-control-label">Enable Guest Chat:</label>
			      		<div class=""><label class=""><div style="position:relative;top:4px;"><input style="position: absolute;" type="radio" name="guestsMode" value="1" $dy ></div><span style="padding-left:25px;">Yes</span></label><label class=""><div style="position:relative;top:4px;"><input style="position: absolute;left:8px;" type="radio" $dn name="guestsMode" value="0"></div><span style="padding-left:36px;">No</span></label></div>
					</div>
				</div>
				<div id="guest_options">
					<div class="form-group row serverurl_text">
						<div class="col-md-12">
				      	<label class="form-control-label">Prefix for guest names:</label>
				      		<input class="form-control" name="guestnamePrefix" value="$guestnamePrefix" type="text">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-md-12">
				      	<label class="form-control-label">In Who`s Online list, for guests:</label>
				      		<select class="form-control" name="guestsList"><option value="1" $gL1>Show only guests</option><option value="2" $gL2>Show only logged in users</option><option value="3" $gL3>Show both</option></select>
						</div>
					</div>
					<div class="form-group row">
						<div class="col-md-12">
				      	<label class="form-control-label">For logged in users:</label>
				      		<select class="form-control" name="guestsUsersList"><option value="1" $gUL1>Show only guests</option><option value="2" $gUL2>Show only logged in users</option><option value="3" $gUL3>Show both</option></select>
						</div>
					</div>
				</div>
			</div>

			<div class="form-group row col-md-12">
		      <input type="submit" value="Update"  class="btn btn-primary">
		    </div>

		   </form>
            </div>
    	</div>
  	</div>
EOD;
	return $body;
}

function ccauth() {
	global $body, $ccactiveauth, $ts, $client,$firebaseAPIKey, $firebaseAuthDomain, $firebaseProjectID, $firebaseccAPIKey, $firebaseccAuthDomain, $firebaseccProjectID;
	$ccauthoptions = array('Facebook', 'Google', 'Twitter');
	$ccauthicons   = array('fa-facebook', 'fa-google', 'fa-twitter');
	/*if(checkAuthMode('social')){
		$ccauthshow = '';
		$siteauthshow = 'style="display:none"';
		$siteauthradio_checked = '';
		$ccauthradio_checked = 'checked';
	}else{
		$siteauthshow = '';
		$ccauthshow = 'style="display:none"';
		$ccauthradio_checked = '';
		$siteauthradio_checked = 'checked';
	}*/
	if (USE_CCAUTH == 1) {
		$ccauthshow = '';
		$siteauthshow = 'style="display:none"';
		$siteauthradio_checked = '';
		$ccauthradio_checked = 'checked';
	}else{
		$siteauthshow = '';
		$ccauthshow = 'style="display:none"';
		$ccauthradio_checked = '';
		$siteauthradio_checked = 'checked';
	}
	$authmode = USE_CCAUTH;
	$ccactiveauthlist = '';
	$ccauthlistoptions = '';
	$no = 0;
	$no_auth = '';
	$config = '';
	$firebaseAPIKey 	= 	($firebaseAPIKey ==	$firebaseccAPIKey) ? '' : $firebaseAPIKey;
	$firebaseAuthDomain =	($firebaseAuthDomain == $firebaseccAuthDomain)	? '' : $firebaseAuthDomain;
	$firebaseProjectID 	=	($firebaseProjectID == $firebaseccProjectID)	? '' : $firebaseProjectID;

	foreach ($ccauthoptions as $ccauthoption) {
		++$no;
		if(empty($client)) {
			$config = ' <a style="color:black;" data-toggle="tooltip" title="Configure" href="javascript:void(0)" onclick="javascript:auth_configauth(\''.$ccauthoption.'\')" style="margin-right:5px"><i class="fa fa-lg fa-cogs"></i></a>';
		}
		if (in_array($ccauthoption, $ccactiveauth)) {
			$ccauth = "active";
			$action = '<a title="Remove" style="color:red;" data-toggle="tooltip" href="javascript:void(0)" onclick="javascript:ccauth_removeauthmode(\''.$no.'\',this)"><i class="fa fa-lg fa-minus-circle"></i></a>';
		} else {
			$ccauth = "inactive";
			$action = '<a title="Add" data-toggle="tooltip" style="color:green;" href="javascript:void(0)" onclick="javascript:ccauth_addauthmode('.$no.',\''.$ccauthoption.'\',this);"><i class="fa fa-lg fa-plus-circle"></i></a>';
		}
			$ccactiveauthlist .= '<tr ccauth="'.$ccauth.'" id="'.$no.'" d1="'.$ccauthoption.'" rel="'.$ccauthoption.'"><td style="color:#4285f4;font-size:20px;"><i class="fa fa-lg '.$ccauthicons[$no-1].'"></i><td>'.stripslashes($ccauthoption).'</td><td width="5px"></td><td width="5px">'.$action.'</td></tr>';
	}

	if(!$ccactiveauthlist){
		$no_auth .= '<tr id="no_auth"><td colspan="3">You have no Authentication Mode activated at the moment. To activate an Authentication Mode, please add them from the list of available Authentication Modes.</td></tr>';
	}


	//if(!checkLicenseVersion()){
$ccSiteConfig = <<<EOD
	<div class="form-group row">
		<div class="col-md-12">
      	<input type="radio" name="USE_CCAUTH" class="auth_select" id="site_auth_radio" value=0 $siteauthradio_checked >&nbsp;&nbsp;Site's Authentication
		</div>
	</div>
EOD;

$ccSocialConfig = <<<EOD
<input type="radio" name="USE_CCAUTH" class="auth_select" id="cc_auth_radio" value="1" {$ccauthradio_checked}>&nbsp;&nbsp;
EOD;

$ccOldScript = <<<EOD
	$(function(){
		$('#site_auth_radio').click(function(){
			$('#site_auth_options').show();
			$('.cc_auth_options').hide();
			$('#site_auth_radio').val(0);
		});
		$('#cc_auth_radio').click(function(){
			$('.cc_auth_options').show();
			$('#site_auth_options').hide();
			$('#site_auth_radio').val(1);
		});
	});
EOD;
	//}
$body = <<<EOD
	<div class="col-sm-12 col-lg-12">
    <div class="card">
      	<div class="card-header">
        	Authentication Mode
        	<h4><small>You can choose to either integrate with your site's login system (if you have one) or to use our social login feature to enable your users to login using their social accounts.</small></h4>
      	</div>
      	<div class="card-block">
      	<form onsubmit="return ccauth_updateorder({$authmode});" action="?module=settings&action=updateauthmode&ts={$ts}" method="post">
		<input type="hidden" name="ccactiveauth" id="cc_auth_order">
		{$ccSiteConfig}
	   <div class="form-group row">
			<div class="col-md-12">
	      	{$ccSocialConfig}Social Login
	      	</div>
		</div>


		<div class="row cc_auth_options" {$ccauthshow}>
			<div class="col-md-12">
				<table class="table" id="auth_livemodes" class="ui-sortable" unselectable="on">
		          {$ccactiveauthlist}
		        </table>
	        </div>
        </div>

      	<div class="panel-collapse collapse in cc_auth_options" {$ccauthshow}>
		    <div class="panel panel-default">
		      	<div class="panel-heading">
			        <h4 class="panel-title">
			          <a data-toggle="collapse" data-parent="#accordion" href="#collapse">Advanced</a>
			        </h4>
		        </div>
		      	<div id="collapse" class="panel-collapse collapse" style="padding:20px;">
		      	<div class="form-group row" style="padding-left:15px;padding-right:15px;">
			      	<div class="col-md-12 note note-success">
			        	Configure with your Firebase app
			      	</div>
		      	</div>
	        		<div class="form-group row">
			        	<div class="col-md-12">
			        		<label class="form-control-label">API Key:</label>
			        		<input class="form-control" name="firebaseAPIKey" value="{$firebaseAPIKey}" autocomplete="off" type="text">
			        	</div>
	        		</div>
					<div class="form-group row">
						<div class="col-md-12">
							<label class="form-control-label">Auth Domain:</label>
							<input class="form-control" name="firebaseAuthDomain" value="{$firebaseAuthDomain}" autocomplete="off" type="text">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-md-12">
							<label class="form-control-label">Project ID:</label>
							<input class="form-control" name="firebaseProjectID" value="{$firebaseProjectID}" autocomplete="off" type="text">
						</div>
					</div>
		      	</div>
		    </div>
        </div>

		<div class="row col-md-10" style="padding-bottom:5px;"><br>
	      <input type="submit" value="Update"  class="btn btn-primary">
	    </div>

	   </form>
        </div>
	</div>
	</div>
<script type="text/javascript">
	/*$(function() {
		$("#auth_livemodes").sortable({
			items: "tr:not(.ui-state-unsort)",
			connectWith: 'table'
		});
		$("#auth_livemodes").disableSelection();
	});*/
	{$ccOldScript}
</script>
EOD;

	return $body;

}

function updateauthmode() {
	global $ts;
	global $ccactiveauth;
	global $firebaseAPIKey, $firebaseAuthDomain, $firebaseProjectID, $firebaseccAPIKey, $firebaseccAuthDomain, $firebaseccProjectID;

	$_POST['firebaseAPIKey'] = empty($_POST['firebaseAPIKey']) ? $firebaseccAPIKey : $_POST['firebaseAPIKey'];
	$_POST['firebaseAuthDomain'] = empty($_POST['firebaseAuthDomain']) ? $firebaseccAuthDomain : $_POST['firebaseAuthDomain'];
	$_POST['firebaseProjectID'] = empty($_POST['firebaseProjectID']) ? $firebaseccProjectID : $_POST['firebaseProjectID'];

	if(USE_CCAUTH!=$_POST['USE_CCAUTH']){
		$sql = sql_getQuery('admin_updateauthmode');
		$result = sql_multi_query($GLOBALS['dbh'],$sql);
	}
	$_POST['ccactiveauth'] = json_decode($_POST['ccactiveauth'],true);
	configeditor($_POST);
	$_SESSION['cometchat']['error'] = 'Authentication Mode details updated successfully';
	header("Location:?module=settings&action=generalsettings&ts={$ts}");
}

function updategoogleanalytics(){
	global $ts;
	configeditor($_POST);
	$_SESSION['cometchat']['error'] = "Settings updated successfully";
	header('location:?module=settings&action=googleanalytics&ts='.$ts);
}

function storage() {
	if (!empty($GLOBALS['client'])) { header("Location:?module=dashboard&ts=".$GLOBALS['ts']); exit; }
	global $body, $ts, $aws_bucket_url;
	$defaultradio_checked = $awsradio_checked = $required = '';
	if(AWS_STORAGE == '0'){
		$defaultradio_checked = 'checked';
	}else{
		$awsradio_checked = 'checked';
		$required = 'required';
	}
	$storagemode = AWS_STORAGE;
	$aws_access_key = AWS_ACCESS_KEY;
	$aws_secret_key = AWS_SECRET_KEY;
	$aws_bucket = AWS_BUCKET;

$body = <<<EOD
  	<div class="col-sm-12 col-lg-12">
	    <div class="card">
	      	<div class="card-header">
	        	Storage
	        	<h4><small>You can choose to use either the default folder storage or Amazon AWS S3 to store files being transferred.</small></h4>
	      	</div>
	      	<div class="card-block">
	      	<form action="?module=settings&action=updatestoragemode&ts={$ts}" method="POST">

			<div class="row col-md-12" style="padding-bottom:5px;">

				<div class="form-group row">
					<div class="col-md-12">
			      	<input name="AWS_STORAGE" id="default_radio" value="0" $defaultradio_checked type="radio" style="margin-top:8px;">&nbsp;&nbsp;Default Folder Storage<br>
			      	<input name="AWS_STORAGE" id="aws_radio" value="1" $awsradio_checked type="radio">&nbsp;&nbsp;Amazon Simple Storage Service (AWS)
					</div>
				</div>


		    <div id="aws_keys" style="display:none;">
				<div class="form-group row">
					<div class="col-md-12">
			      	<label class="form-control-label">AWS Access Key:</label>
			      		<input placeholder="3820d3b04d0781280d7c" class="form-control" name="AWS_ACCESS_KEY" value="$aws_access_key" $required type="text">
					</div>
				</div>

				<div class="form-group row">
					<div class="col-md-12">
			      	<label class="form-control-label">AWS Secret Key:</label>
			      		<input placeholder="f3913820d132ssd3b04d0781280d7c" class="form-control" name="AWS_SECRET_KEY" value="$aws_secret_key" $required type="text">
					</div>
				</div>

				<div class="form-group row">
					<div class="col-md-12">
			      	<label class="form-control-label">AWS Bucket:</label>
			      		<input placeholder="f3913820d3b04d0781280d7c" class="form-control" name="AWS_BUCKET" value="$aws_bucket" $required type="text">
					</div>
				</div>

				<div class="form-group row">
					<div class="col-md-12">
			      	<label class="form-control-label">AWS Bucket URL:</label>
			      		<input class="form-control" placeholder="http://bucket.xyz.com/" name="aws_bucket_url" value="$aws_bucket_url" $required type="text">
					</div>
				</div>
		    </div>
			<div class="row col-md-10" style="padding-bottom:5px;"><br>
		      <input type="submit" value="Update"  class="btn btn-primary">
		    </div>

		   </form>
            </div>
    	</div>
  	</div>
	<script type="text/javascript">
		$(document).ready(function(){
			var storagemode = '{$storagemode}';
			if(storagemode == 1) {
				$('#aws_keys').show();
			}
			$('#default_radio').click(function(){
				$('#aws_keys').hide();
				$('#aws_keys input').attr('required', '');
			});
			$('#aws_radio').click(function(){
				$('#aws_keys').show();
				$('#aws_keys input').attr('required', 'required');
			});
		});
	</script>
EOD;

	return $body;
}
function updatestoragemode() {
	if (!empty($GLOBALS['client'])) { header("Location:?module=dashboard&ts=".$GLOBALS['ts']); exit; }
    global $ts;
    $_POST['aws_bucket_url'] = preg_replace('#^http(s)?://#', '', rtrim($_POST['aws_bucket_url'],'/'));
	configeditor($_POST);
	$_SESSION['cometchat']['error'] = 'Storage mode details updated successfully';
	header("Location:?module=settings&action=generalsettings&ts={$ts}");

}

function mobile() {
	global $body, $options, $ts;
    $form = '';
    $queryString = "";
    if (!empty($_REQUEST['dev'])) {
    	$queryString = "&dev=1";
    }
	$body = <<<EOD
	    <div class="row">
	  	<div class="col-sm-6 col-lg-6">
		    <div class="card">
		     	<div class="card-header">
		       		Mobile Web
		      	</div>
		      	<div class="card-block">
		      		<iframe height="450px" width="100%"  style="margin-left:-28px;border:none;" src="?module=dashboard&action=loadthemetype&type=layout&name=mobile&ts={$ts}"></iframe>
		    	</div>
	    	</div>
	  	</div>
	  	<div class="col-sm-6 col-lg-6">
		    <div class="card">
		     	<div class="card-header">
		       		White-labelled Mobile App
		      	</div>
		      	<div class="card-block">
		      		<iframe height="1050px" width="100%"  style="margin-left:-28px;border:none;" src="?module=dashboard&action=loadexternal&type=extension&name=mobileapp{$queryString}"></iframe>
		    	</div>
	    	</div>
	    </div>
	  	</div>
	  <script type="text/javascript">
	  $(function() {
	    $("#mobile").addClass('active');
	  });
	</script>
EOD;
    template();
}

function desktop() {
    global $body, $navigation, $options, $ts, $apikey;
    $form = '';
    $extraquerystring = '';
	if (isset($_REQUEST['branded']) && $_REQUEST['branded']==1) {
		$extraquerystring = '&branded=1';
	}
$body = <<<EOD
    <div class="row">
  	<div class="col-sm-12 col-lg-12">
    <div class="card">
      <div class="card-header">
        White-labelled Desktop App
        <h4><small>If you would like to use your own images and colors for the Desktop Messenger, you can make necessary changes here.</small></h4>
      </div>
      <div class="card-block" style="padding-top:0px;">
      <iframe height="630px" width="100%"  style="border:none;" src="?module=dashboard&action=loadexternal&type=extension&name=desktop{$extraquerystring}"></iframe>
      </div>
    </div>
  </div>
EOD;

    template();

}

function licensekey(){
	if (!empty($GLOBALS['client'])) { header("Location:?module=dashboard&ts=".$GLOBALS['ts']); exit; }
	global $body, $navigation, $ts, $settings, $licensekey;
	if(!empty($settings['licensekey'])){
		$licensekey = $settings['licensekey']['value'];
	}

$body = <<<EOD
  	<div class="col-sm-12 col-lg-12">
	    <div class="card">
	      	<div class="card-header">
	        	License Key
	      	</div>
	      	<div class="card-block">

	      	<form action="?module=settings&action=updatelicensekey&ts={$ts}" method="post">

      		<div class="form-group row">
				<div class="col-md-12">
				<label class="form-control-label">License Key:</label>
				<input type="text" class="form-control" value="{$licensekey}" name="licensekey">
				</div>
			</div>

			<div class="form-group row col-md-12">
		      <input type="submit" value="Update"  class="btn btn-primary">
		    </div>

		   </form>
            </div>
    	</div>
  	</div>
EOD;
	return $body;
}

function updatelicensekey(){
	global $accessKey,$licensekey;
	if (!empty($GLOBALS['client'])) { header("Location:?module=dashboard&ts=".$GLOBALS['ts']); exit; }
	global $ts;
	/*
	* Process Updated License
	*/
	if(substr($_POST['licensekey'],0,10) == "COMETCHAT-"){
	  	$CometChatResponse=checkCometChatResponse($_POST['licensekey']);
	  	if($CometChatResponse['success'] == 1){
	  		configeditor(array('licensekey' => $_POST['licensekey'],'api_response' => '') );
	  		$_SESSION['cometchat']['error'] = 'license key updated successfully.';
	  	} else{
			$_SESSION['cometchat']['error'] = 'Invalid License.';
	  	}
	  	if($CometChatResponse['has_plan_changed']==1){
			$_SESSION['cometchat']['error'] = 'Your plan has been changed.';
	  	}
  	} else {
  		configeditor(array('licensekey' => $_POST['licensekey'],'api_response' => '') );
  		$_SESSION['cometchat']['error'] = 'license key updated successfully.';
  	}
	header("Location:?module=settings&action=generalsettings&ts={$ts}");
}
