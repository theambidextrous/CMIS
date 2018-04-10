<?php

/**
 * Product   CometChat
 * Copyright (c) 2016 Inscripts
 * License: https://www.cometchat.com/legal/license

 * This is a Config file of CometChat.
 * We always define Configuration related Variables inside this file.
 *
 *
 * @category   Core
 * @package    CometChat
 * @class      Integration
 * @author     CometChat Support <support@cometchat.com>
 * @since      NA
 * @deprecated NA
 */

 /* TIMEZONE SPECIFIC INFORMATION (DO NOT TOUCH) */

/**
* Set Default Timezone
*/
date_default_timezone_set('UTC');

/**
* CometChat Current Version.
* @var string
*/
$currentversion = '7.0.6';

/**
* Check if variable is set for CDN
* Description: With the help of CDN web should load CometChat as faster as it can.
* CDN Part Satrt
*/
if (!empty($_GET['cdnparams'])) {

  /**
  * CometChat CDN Parameters Array.
  * @var array
  */
	$getparamsfromCDN = array(
		0 => 'hash',
		1 => 'type',
		2 => 'name',
		3 => 'layout',
		4 => 'color',
		5 => 'callbackfn',
		6 => 'lang',
		7 => 'subtype',
		8 => 'admin',
		9 => 'app',
		10 => 'v',
	);

  /**
  * Explode CDN Parameter
  * @var array
  */
	$cdnparams = explode('x_x', $_GET['cdnparams']);
	foreach ($getparamsfromCDN as $paramindex => $paramname) {
		if(!empty($cdnparams[$paramindex])){
			$_GET[$paramname] = $cdnparams[$paramindex];
		}
	}
}
if (!empty($_GET['hash']) && strlen($_GET['hash'])>5) {
	$_GET['forcedockedenable'] = '1';
}
/**
* CDN Part End
*/

/* END: Check if variable is set for CDN*/
include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'cometchat_shared.php');

/**
* $firebaseAPIKey : Firebase API Key Variable Decleration
* @var string
*/
$firebaseAPIKey = $firebaseccAPIKey = '';

/**
* Firebase Authenticated Domain
* @var string
*/
$firebaseAuthDomain = $firebaseccAuthDomain = '';

/**
* Firebase Project Id
* @var string
*/
$firebaseProjectID 	= $firebaseccProjectID 	= '';

/**
* environment.php use for Cloud purpose.
* @var string
*/
if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'environment.php')) {
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'environment.php');
}

/**
* CometChat Cloud
*/
global $client, $bucket_url;

/**
* $client - Client Id of Cloud.
* @var string
*/
$client = !empty($client)?$client:'';

/**
* $apikey - Client Cloud API Key
* @var string
*/
$apikey = !empty($apikey)?$apikey:'';

/**
* $writable - Cache Folder Path on Cloud for Client
* @var string
*/
$writable = (!empty($client)?$client.DIRECTORY_SEPARATOR:'').'cache';

/**
* check if $writable directory is present on CLoud and Give Folder permission as 777 to Cache Folder.
* @var string
*/
if (!empty($client) && !is_dir(dirname(__FILE__).DIRECTORY_SEPARATOR.'writable'.DIRECTORY_SEPARATOR.$writable)) {
	mkdir(dirname(__FILE__).DIRECTORY_SEPARATOR.'writable'.DIRECTORY_SEPARATOR.$writable, 0777, true);
}

/**
* include 'integration.php' file
* SOFTWARE SPECIFIC INFORMATION (DO NOT TOUCH)
*/
if (empty($client) && file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'integration.php')) {
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'integration.php');
}

/**
* Default Database Type. Currently we have integration for following database "mysql", "mssql" & "pgsql"
* @var string
*/
$dbms = empty($dbms) ? "mysql" : $dbms;

if($dbms == "mssql" && file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'mssql.php')){
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'mssql.php');
}else if($dbms == "pgsql" && file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'pgsql.php')){
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'pgsql.php');
}else{
 include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'sql.php');
}

if(defined('CC_INSTALL')){
	return;
}

/**
* $sql_queries
* Define Global variable for SQL Queries
* @var string
*/
global $sql_queries;

/**
* $settings
* Define Global variable for Settings
* @var array
*/
global $settings;

/**
* Function : settingsCacheConnect()
* Store all settings related data into caching
*/
settingsCacheConnect();

/**
* Function : cometchatMemcacheConnect()
* Store all settings related data into caching
*/
cometchatMemcacheConnect();
/**
* $api_response
* Define api response
* @var array
*/
$api_response = setConfigValue('api_response',array());

/**
* $authentication
* authentication is basically social auth, site auth etc
* @var array
*/
$authentication = setConfigValue('authentication',array());

/**
* USE_CCAUTH
* Its CometChat Authentication method. If user dont have any authentication in his site he can use CometChat Authentication for his site login. If its 0 its disabled & if its 1 its enabled.
* @var array
*/
define('USE_CCAUTH', setConfigValue('USE_CCAUTH','0'));

/**
* $firebaseAPIKey
* Set Default Config Value for Firebase API Key
* @var string
*/
$firebaseAPIKey = setConfigValue('firebaseAPIKey',$firebaseAPIKey);

/**
* $firebaseAuthDomain
* Set Default Config Value for Firebase Auth Domain
* @var string
*/
$firebaseAuthDomain = setConfigValue('firebaseAuthDomain',$firebaseAuthDomain);

/**
* $firebaseProjectID
* Set Default Config Value for Firebase Project Id
* @var string
*/
$firebaseProjectID = setConfigValue('firebaseProjectID',$firebaseProjectID);

/**
* $ccactiveauth
* Set Default Config Value for ccactiveauth. Such as Facebook, Google, Twitter
* @var string
*/
$ccactiveauth = setConfigValue('ccactiveauth',array('Facebook','Google','Twitter'));

/**
* $guestsMode
* Set Default Config Value for Guest Mode. If value is 1 : Guest Mode Enabled, 0 : Guest Mode Disabled.You can Edit this value from "Settings->General (Enable Guest Chat)" from Admin Panel
* @var string
*/
$disableGuestModeCms = array("buddypress","crea8social","datingscript","wordpress","drupal","skadate ","joomla","moosocial","ossate","phpbb","shopify");
$guestEnabled = 1;

if (in_array($cms, $disableGuestModeCms)) {
	$guestEnabled = 0;
}
$guestsMode = setConfigValue('guestsMode',$guestEnabled);

/**
* $uniqueguestname
* Set Default Config Value for Unique Guest Names. If value is 1 : Unique Guest Names Enabled, 0 : Unique Guest Names Disabled.
* @var string
*/
$uniqueguestname = setConfigValue('uniqueguestname','1');

/**
* $guestnamePrefix
* Set Default Config Value for Guest Name Prefix. By Default its value is 'Guest'. You can Edit this value from "Settings->General (Prefix for guest names)"  from Admin Panel
* @var string
*/
$guestnamePrefix = setConfigValue('guestnamePrefix','Guest');

/**
* $firstguestID
* First Guest Id Always Start With '10000000'
* @var string
*/
$firstguestID = setConfigValue('firstguestID','10000000');

/**
* $guestsList
* 1: show only Guest, 2: Show Only Logged in Users, 3: Show Both. You can Edit this value from "Settings->General (In Who`s Online list, for guests:)"  from Admin Panel
* @var integer
*/
$guestsList = setConfigValue('guestsList','3');

/**
* $guestsUsersList
* 1: show only Guest, 2: Show Only Logged in Users, 3: Show Both. You can Edit this value from "Settings->General (For logged in users:)"  from Admin Panel
* @var integer
*/
$guestsUsersList = setConfigValue('guestsUsersList','3');

/**
* $cms
* Get CMS/Framework Name and get Dynamic Integration file for it.
* @var string
*/
if (!empty($GLOBALS['client'])) {
		$cms = setConfigValue('cms','custom');
		if ($cms == "buddypress")
			$cms = "wordpress";
		if(!file_exists(COD_DIR.DIRECTORY_SEPARATOR.'integrations'.DIRECTORY_SEPARATOR.$cms.'.php')){
				$cms = "custom";
		}
		include_once(COD_DIR.DIRECTORY_SEPARATOR.'integrations'.DIRECTORY_SEPARATOR.$cms.'.php');
}

/**
* $integration
* Global Variable For Integration
* @Global string
*/
global $integration;

/**
* $licensekey
* License Key Decleration
* @Global string
*/
$licensekey = setConfigValue('licensekey',$licensekey);

/**
* If Auth Mode is 'Social'. Its include  cometchat_auth file and create object of "CCAuth"  else its create object of "Integration"
*/
if(checkAuthMode('social')){
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'cometchat_auth.php');
	$integration = new CCAuth();
}else{
	$integration = new Integration();
}

/**
* $dbversion
* It is a version of CometChat Database Query Files. You will get Query files in "db\mssql"
* @var string
*/
$dbversion = $checkdbversion = setConfigValue('dbversion',0);

/**
* It will scan all files inside "db\database_type(mssql,mysql etc)"
*/
$dir = dirname(__FILE__).DIRECTORY_SEPARATOR.'db'.DIRECTORY_SEPARATOR.$dbms.DIRECTORY_SEPARATOR;
$files = scandir($dir,2);

/**
* Create array of version and store it in "$files"
* @var array
*/
foreach ($files as $key => $value) {
		list($name , $ext) = explode('.', $value);
		$files[$key] = $name;
}

asort($files);

/**
* get Current Version of Database File
*/
$currentdbversion =  intval(end($files));

while ($dbversion < $currentdbversion) {
$dbversion++;
	if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'db'.DIRECTORY_SEPARATOR.$dbms.DIRECTORY_SEPARATOR.$dbversion.'.php')) {
		include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'db'.DIRECTORY_SEPARATOR.$dbms.DIRECTORY_SEPARATOR.$dbversion.'.php');
	}
}

if ($checkdbversion == 0 && empty($client) && empty($iscmsplugin)) {
		setBaseUrl();
}
/*Pull values from database if cache is not present*/

/**
* $languages
* @global array
*/
global $languages;

/**
* function: getLanguageVar
* get language data from database and store it into cache
* @var array
*/
getLanguageVar();

/**
* $colors
* @global array
*/
global $colors;

/**
* function: getColorVars
* get color data from database and store it into cache
* @var array
*/
getColorVars();

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/**
* $cookiePrefix
* CometChat Prefix
* @var string
*/
$cookiePrefix = setConfigValue('cookiePrefix',$client.'cc_');


/**
* $gatrackerid
* Google Analytics tracking ID
*/
$gatrackerid=setConfigValue('gatrackerid','');

/**
* BASE_URL
* @var string
* You can edit this base_url from "settings->general (Base URL:)" from admin panel.
*/
if(!defined('BASE_URL')) {
	define('BASE_URL',setConfigValue('BASE_URL','/cometchat/'));
}


/**
* DYNAMIC_CDN_URL
* @define string
* Dynamic CDN URL use for CometChat Css & Js File
*/
if(!defined('DYNAMIC_CDN_URL')){
	define('DYNAMIC_CDN_URL', setConfigValue('DYNAMIC_CDN_URL',BASE_URL));
}

/**
* STATIC_CDN_URL
* @define string
* Static CDN URL use for Images & Related Media Files
*/
if(!defined('STATIC_CDN_URL')){
	define('STATIC_CDN_URL', setConfigValue('STATIC_CDN_URL',BASE_URL));
}

/**
* $lang
* @define string
* Default language set is "en (English)". You can edit this setting from "Localize" from admin panel.
*/
$lang = setConfigValue('lang','en');

/**
* $lang
* @define string
* get Language type from Cookie. (language type such as 'en','fr' etc.). We are checking this language in Cookie
*/
if (!empty($_COOKIE[$cookiePrefix."lang"])) {
	$lang = preg_replace("/[^A-Za-z0-9\-]/", '', $_COOKIE[$cookiePrefix . "lang"]);
}

/**
* $lang
* @define string
* get Language type from GET Parameter. (language type such as 'en','fr' etc.). We are checking this language in GET Parameter.
*/
if (!empty($_GET[$cookiePrefix."lang"])) {
	$lang = preg_replace("/[^A-Za-z0-9\-]/", '', $_GET[$cookiePrefix . "lang"]);
}

/**
* CUSTOM SETTINGS
* for setting Custom CSS & JS.
*/
$customjs = <<<EOD
			(function($){
					$.customcore = (function(){
							return {

							};
					})();
			})(jqcc);

			if(typeof(jqcc.cometchat) === "undefined"){
					jqcc.cometchat=function(){};
			}

			jqcc.extend(jqcc.cometchat, jqcc.customcore);


			(function($){
					$.customdocked = (function(){
							return {

							};
					})();
			})(jqcc);

			if(typeof(jqcc.docked) === "undefined"){
					jqcc.docked=function(){};
			}

			jqcc.extend(jqcc.docked, jqcc.customdocked);



			(function($){
					$.customembedded = (function(){
							return {

							};
					})();
			})(jqcc);

			if(typeof(jqcc.embedded) === "undefined"){
					jqcc.embedded=function(){};
			}

			jqcc.extend(jqcc.embedded, jqcc.customembedded);
EOD;

$customjs = setConfigValue('customjs', $customjs);
$customcss = setConfigValue('customcss', '');
$enablecustomjs = setConfigValue('enablecustomjs', 0);
$enablecustomcss = setConfigValue('enablecustomcss', 0);
$enablecustomphp = setConfigValue('enablecustomphp', 0);

/**
* $trayicon
* @var array
* Array containing property of different cometchat modules
*/
$trayicon = array();
$trayicon['home'] = array('home','Home','/','','','','','','');
$trayicon['chatrooms'] = array('chatrooms','Chatrooms','modules/chatrooms/index.php','_popup','600','300','','1','1');
$trayicon['announcements'] = array('announcements','Announcements','modules/announcements/index.php','_popup','280','300','','1','');
$trayicon['games'] = array('games','Single Player Games','modules/games/index.php','_popup','465','300','','1','');
$trayicon['share'] = array('share','Share This Page','modules/share/index.php','_popup','350','50','','1','');
$trayicon['scrolltotop'] = array('scrolltotop','Scroll To Top','javascript:jqcc.cometchat.scrollToTop();','','','','','','');

$trayicon = setConfigValue('trayicon',$trayicon);

/*Deprecated Theme Changer*/
unset($trayicon['themechanger']);

/**
* $plugins
* @var array
* Array containing list of different plugin. such as smilies, clear conversation. Plugin Start.
*/
$plugins = array('smilies','clearconversation');
$plugins = setConfigValue('plugins',$plugins);


/**
* $extensions
* @var array
* Array containing list of different extensions. such as mobileapp, desktop. Extensions Start.
*/
$extensions = array('mobileapp','desktop');
$extensions = setConfigValue('extensions',$extensions);

/**
* Fix Me
* Deprecated Jabber
*/
if(($key = array_search("jabber", $extensions)) !== false) {
		unset($extensions[$key]);
}

/**
* $usebots
* @var boolean
* if $usebots = 0 bots are disabled. if $usebots = 1 bots are enabled.
*/
$usebots = 0;
if (is_dir(dirname(__FILE__).DIRECTORY_SEPARATOR.'extensions'.DIRECTORY_SEPARATOR.'bots')) {
	 $usebots = setConfigValue('usebots','1');
}

/**
* $crplugins
* @var array
*/
$crplugins = array('style','filetransfer','smilies');

/**
* $crplugins
* set default value for crplugins
* @var array
*/
$crplugins = setConfigValue('crplugins',$crplugins);

/**
* Include Config for 'smilies'
*/
include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'smilies'.DIRECTORY_SEPARATOR.'config.php');

/**
* $uploaded_smileys Decleration
* @var array
*/
$uploaded_smileys = array ();
$uploaded_smileys = setConfigValue('uploaded_smileys',$uploaded_smileys);

/**
* $smileys & $emojis Decleration
* @var array
*/
$smileys = array_merge($uploaded_smileys,$emojis);

/**
* $smileys_sorted
* Smileys Sorted
* @var array
*/
$smileys_sorted = $smileys;
krsort($smileys_sorted);
uksort($smileys_sorted, "cmpsmileyskey");

/**
* $bannedWords : @var array : Set Config Values for banned Words
* $bannedUserIDs : @var array :  Set Config Values for banned User Id
* $bannedUserIPs : @var array :  Set Config Values for banned User IPs
* $bannedMessage : @var array :  Set Config Values for banned messages
*/
$bannedWords 	= setConfigValue('bannedWords',array());
$bannedUserIDs 	= setConfigValue('bannedUserIDs',array());
$bannedUserIPs 	= setConfigValue('bannedUserIPs',array());
$bannedMessage 	= setConfigValue('bannedMessage','Sorry, you have been banned from using this service. Your messages will not be delivered.');


/**
* ADMIN_USER : @define string : Define CometChat Admin Panel User Name
* ADMIN_PASS : @define string : Define CometChat Admin Panel Password
*/
define('ADMIN_USER',setConfigValue('ADMIN_USER','cometchat'));
define('ADMIN_PASS',setConfigValue('ADMIN_PASS','cometchat'));


/**
* SETTINGS START
*/

/**
* $hideOffline
* Hide offline users in Who's Online list?
* @var boolean
*/
$hideOffline = setConfigValue('hideOffline','1');

/**
* $cloudOfflineUsersLimit
* Offline user limit in case of cloud
* @var integer
*/
$cloudOfflineUsersLimit = setConfigValue('cloudOfflineUsersLimit','100');

/**
* $disableRecentTab
* Disable recent chats tab
* @var boolean
*/
$disableRecentTab = setConfigValue('disableRecentTab','0');


/**
* $stickersImageUrl
* Sticker Images URL
* @var string
*/
$stickersImageUrl = 'https://m.chatforyoursite.com/stickers/';

/**
* $recentListLimit
* Recent list display limit.
* @var integer
*/
$recentListLimit = setConfigValue('recentListLimit','30');

/**
* $disableContactsTab
* Disable contacts tab. 1 : enable, 0 : disable
* @var boolean
*/
$disableContactsTab = setConfigValue('disableContactsTab','0');

/**
* $disableGroupTab
* Disable group tab. 1 : enable, 0 : disable
* @var boolean
*/
$disableGroupTab = setConfigValue('disableGroupTab','0');

/**
* $autoPopupChatbox
* Auto-open chatbox when a new message arrives. 1 : enabled, 0 : disabled
* @var boolean
*/
$autoPopupChatbox = setConfigValue('autoPopupChatbox','1');

/**
* $messageBeep
* Beep on arrival of message from new user?. 1 : enabled, 0 : disabled
* @var boolean
*/
$messageBeep = setConfigValue('messageBeep','1');

/**
* $beepOnAllMessages
* Beep on arrival of all messages?. 1 : enabled, 0 : disabled
* @var boolean
*/
$beepOnAllMessages = setConfigValue('beepOnAllMessages','1');

/**
* $minHeartbeat
* Minimum poll-time in milliseconds (1 second = 1000 milliseconds)
* @var integer
*/
$minHeartbeat = setConfigValue('minHeartbeat','3000');

/**
* $maxHeartbeat
* Maximum poll-time in milliseconds (1 second = 1000 milliseconds)
* @var integer
*/
$maxHeartbeat = setConfigValue('maxHeartbeat','12000');

/**
* $searchDisplayNumber
* The number of users in Whos Online list after which search bar will be displayed
* @var integer
*/
$searchDisplayNumber = setConfigValue('searchDisplayNumber','10');

/**
* $thumbnailDisplayNumber
* The number of users in Whos Online list after which thumbnails will be hidden
* @var integer
*/
$thumbnailDisplayNumber = setConfigValue('thumbnailDisplayNumber','40');

/**
* $typingTimeout
* The number of milliseconds after which typing to will timeout
* @var integer
*/
$typingTimeout = setConfigValue('typingTimeout','10000');

/**
* $idleTimeout
* The number of seconds after which user will be considered as idle
* @var integer
*/
$idleTimeout = setConfigValue('idleTimeout','300');

/**
* $displayOfflineNotification
* If yes, user offline notification will be displayed
* 1 : yes, 0 : no
* @var boolean
*/
$displayOfflineNotification = setConfigValue('displayOfflineNotification','1');

/**
* $displayOnlineNotification
* If yes, user online notification will be displayed
* @var boolean
*/
$displayOnlineNotification = setConfigValue('displayOnlineNotification','1');

/**
* $displayBusyNotification
* If yes, user busy notification will be displayed
* @var boolean
*/
$displayBusyNotification = setConfigValue('displayBusyNotification','1');

/**
* $notificationTime
* The number of milliseconds for which a notification will be displayed
* @var integer
*/
$notificationTime = setConfigValue('notificationTime','5000');

/**
* $announcementTime
* The number of milliseconds for which an announcement will be displayed
* @var integer
*/
$announcementTime = setConfigValue('announcementTime','15000');

/**
* $scrollTime
* Can be set to 800 for smooth scrolling when moving from one chatbox to another
* @var boolean
*/
$scrollTime = setConfigValue('scrollTime','1');

/**
* $armyTime
* If set to yes, time will be shown in 24-hour clock format
* @var boolean
*/
$armyTime = setConfigValue('armyTime','0');

/**
* $disableForIE6
* If set to yes, CometChat will be hidden in IE6
* @var boolean
*/
$disableForIE6 = setConfigValue('disableForIE6','0');

/**
* $hideBar
* Hide bar for non-logged in users?
* @var boolean
*/
$hideBar = setConfigValue('hideBar','0');

/**
* $disableForMobileDevices
* If set to yes, CometChat bar will be hidden in mobile devices
* @var boolean
*/
$disableForMobileDevices = setConfigValue('disableForMobileDevices','1');

/**
* $startOffline
* Load bar in offline mode for all first time users?
* @var boolean
*/
$startOffline = setConfigValue('startOffline','0');

/**
* $fixFlash
* Set to yes, if Adobe Flash animations/ads are appearing on top of the bar (experimental)
* setting depricated, Fix Me.
* @var boolean
*/
$fixFlash = setConfigValue('fixFlash','0');

/**
* $lightboxWindows
* Set to yes, if you want to use the lightbox style popups
* @var boolean
*/
$lightboxWindows = setConfigValue('lightboxWindows','1');

/**
* $sleekScroller
* Set to yes, if you want to use the new sleek scroller
* @var boolean
*/
$sleekScroller = setConfigValue('sleekScroller','1');

/**
* $desktopNotifications
* If yes, Google desktop notifications will be enabled for Google Chrome
* @var boolean
*/
$desktopNotifications = setConfigValue('desktopNotifications','1');

/**
* $windowTitleNotify
*  If yes, notify new incoming messages by changing the browser title
* @var boolean
*/
$windowTitleNotify = setConfigValue('windowTitleNotify','1');

/**
* $floodControl
* Chat spam control in milliseconds (Disabled if set to 0)
* @var boolean
*/
$floodControl = setConfigValue('floodControl','0');

/**
* $windowFavicon
* If yes, Update favicon with number of messages (Supported on Chrome, Firefox, Opera)
* @var boolean
*/
$windowFavicon = setConfigValue('windowFavicon','0');

/**
* $prependLimit
* Number of messages that are fetched when load earlier messages is clicked
* @var integer
*/
$prependLimit = setConfigValue('prependLimit','10');

/**
* $blockpluginmode
* If set to yes, show blocked users in Who's Online list
* @var boolean
*/
$blockpluginmode = setConfigValue('blockpluginmode','0');

/**
* $lastseen
* If set to yes, users last seen will be shown
* @var boolean
*/
$lastseen = setConfigValue('lastseen','0');

/**
* $latest_vesion
* Use for latest CometChat Version
* @var string
*/
$latest_vesion = setConfigValue('LATEST_VERSION','');

/**
* $planId
* current user plan id
* @var string
*/
$planId = setConfigValue('planId','');

/**
* $planName
* current user plan name
* @var string
*/
$planName = setConfigValue('planName','');

/**
* $token_key
* $token_key is use for Auto Update.
* @var string
*/
$token_key = setConfigValue('latest_update_token','');

/**
* $chromeReorderFix
* Currently we are using this variable every where in CometChat for concatenating '_' to specific numeric value. you can see this "_" in budylist array.
* Once browser got numeric value its convert Object into Array and its change Conde behaviour for avoiding this we have use "_"
* @var string
*/
$chromeReorderFix = '_';

/**
* $notificationsFeature
* Set to yes, only if you are using notifications
* 0 : no, 1 : yes
* @var boolean
*/
$notificationsFeature = setConfigValue('notificationsFeature',1);

/**
* $hideDockedLayout
* If set to no, Docked theme will be hidden for all users
* 0 : no, 1 : yes
* @var boolean
*/
$hideDockedLayout = setConfigValue('hideDockedLayout',1);

/**
* $disableDockedLayout
* If set to no, Docked theme will be hidden for all users
* 0 : no, 1 : yes
* @var boolean
*/
$disableDockedLayout = setConfigValue('disableDockedLayout','0');

/**
* $forcedockedenable
* If set to yes, Docked theme forcefully Enabled for all users
* 0 : no, 1 : yes
* @var boolean
*/
$forcedockedenable = '0';
if (!empty($_REQUEST["forcedockedenable"])) {
	$forcedockedenable = '1';
}
/**
* $dockedAlignToLeft
* If set to yes, Docked theme will be display on left side corner. by default docked layout display on Right Corner.
* 0: no, 1 : yes
* @var boolean
*/
$dockedAlignToLeft = setConfigValue('dockedAlignToLeft','0');

/**
* $dockedChatListAudioCall
* Display Call icon/button beside User name in contact.
* 0 : no, 1 : yes
* @var boolean
*/
$dockedChatListAudioCall = setConfigValue('dockedChatListAudioCall','0');

/**
* $dockedChatBoxAvatar
* Display Avatar of user in chatbox.
* 0 : no, 1 : yes
* @var boolean
*/
$dockedChatBoxAvatar = setConfigValue('dockedChatBoxAvatar', '0');

/**
* $apikey
* API key for RESTful APIs for User Management on custom coded sites
* @var string
*/
$apikey = setConfigValue('apikey',$apikey);

/**
* MEMCACHE Setting Start
* MEMCACHE  :  @var boolean : 1 = yes, 0 = no : Set to 0 if you want to disable caching and 1 to enable it.
* MC_SERVER  :  @var string : Set name of your memcache  server
* MC_PORT  :  @var string : Set port of your memcache  server
* MC_USERNAME  :  @var string : Set username of memcachier  server
* MC_PASSWORD  :  @var string : Set password your memcachier  server
* MC_NAME  :  @var string : Set name of caching method if 0 : '', 1 : memcache, 2 : files, 3 : memcachier, 4 : apc, 5 : wincache, 6 : sqlite & 7 : memcached
*/
if(!defined('MEMCACHE')) {
	define('MEMCACHE',setConfigValue('MEMCACHE','1'));
	define('MC_SERVER',setConfigValue('MC_SERVER','localhost'));
	define('MC_PORT',setConfigValue('MC_PORT','11211'));
	define('MC_USERNAME',setConfigValue('MC_USERNAME',''));
	define('MC_PASSWORD',setConfigValue('MC_PASSWORD',''));
	define('MC_NAME',setConfigValue('MC_NAME','files'));
}
/**
* MEMCACHE Setting End
*/

/**
* $color
* Color Setting
* @var string
*/
$color = setConfigValue('color','color1');

/**
* $color_original
* Color
* @var string
*/
$color_original = $color;

/**
* $_COOKIE[$cookiePrefix."color"]
* Take a current Color from cookie
* @var string
*/
if (!empty($_COOKIE[$cookiePrefix."color"])) {
	$color = preg_replace("/[^A-Za-z0-9\-]/", '', $_COOKIE[$cookiePrefix."color"]);
}

/**
* $layout
* Theme name
* @var string
*/
$layout = setConfigValue('theme','docked');

/**
* $layout_original
* Theme name
* @var string
*/
$layout_original = $layout;

/**
* $_COOKIE[$cookiePrefix."layout"]
* Take a current layout from cookie
* @var string
*/
if (!empty($_COOKIE[$cookiePrefix."layout"])) {
	$layout = preg_replace("/[^A-Za-z0-9\-]/", '', $_COOKIE[$cookiePrefix."layout"]);
}

/**
* $_REQUEST["layout"]
* Take a current layout from request
* @var string
*/
if (!empty($_REQUEST["layout"])) {
	$layout = preg_replace("/[^A-Za-z0-9\-]/", '', $_REQUEST["layout"]);
}

/**
* DISPLAY_ALL_USERS
* set Config value for DISPLAY_ALL_USERS
* 1 : display all user, 0: show only friends
* @var boolean
*/
define('DISPLAY_ALL_USERS',setConfigValue('DISPLAY_ALL_USERS','1'));

/**
* BAR_DISABLED
* disabled & enabled chat bar
* 1 : disabled chat bar, 0: enabled chat bar
* @var boolean
*/
define('BAR_DISABLED',setConfigValue('BAR_DISABLED','0'));


/* COMET START */

/**
* $uidkey
* unique Identifier for Comet Service
* @var string
*/
$uidkey = md5('cod_'.$client);

/**
* $hideconfig
* Hide CometChat Configuration
* @var string
*/
$hideconfig = (!empty($hideconfig))?$hideconfig:array();

/**
* $cmswithfriends
* For Few CMS We have already Map Buddy List / Friend List
* @var array
*/
$cmswithfriends = (!empty($cmswithfriends))?$cmswithfriends:array();

/**
* $login_url
* set default Config Value for Mobile login URL
* @var string
*/
$login_url = setConfigValue('MOBILE_URL','');

/**
* $logout_url
* set default Config Value for Mobile logout URL
* @var string
*/
$logout_url = setConfigValue('MOBILE_LOGOUTURL','');

/**
* $plan
* set default Config Value for CometChat plan
* @var string
*/
$plan           =   setConfigValue('plan','');

/**
* $mobileapp
* set default Config Value for Mobile App
* @var boolean
*/
$mobileapp      =   setConfigValue('mob','0');

/**
* $sdk
* set default Config Value for Mobile sdk
* @var boolean
*/
$sdk            =   setConfigValue('sdk','0');

/**
* $dm
* set default Config Value for Desktop Messanger
* @var boolean
*/
$dm             =   setConfigValue('dm','0');

/**
* Fix Me
*/
$facebook       =   setConfigValue('facebook','');

/**
* $protocol
* set default Config Value for protocol
* @var string
*/
$protocol       =   setConfigValue('protocol','http:');

/**
* $pubkey
* set default Config Value for pubnub key
* @var string
*/
$pubkey = setConfigValue('pubkey','');

/**
* $subkey
* set default Config Value for pubnub key
* @var string
*/
$subkey = setConfigValue('subkey','');

/**
* $uidkey
* unique Identifier for Comet Service
* @var string
*/
$uidkey = !empty($uidkey)? $uidkey :setConfigValue('uidkey','');

/**
* $use_comet
* set default Config Value for comet service.
* @var boolean
*/
$use_comet = (!empty($pubkey) && !empty($subkey))?'1':'0';

/**
* $use_comet
* Set to 0 if you want to disable transport service and 1 to enable it.
* @var boolean
*/
define('USE_COMET',setConfigValue('USE_COMET',$use_comet));
define('KEY_A',setConfigValue('KEY_A',$pubkey));
define('KEY_B',setConfigValue('KEY_B',$subkey));
define('KEY_C',setConfigValue('KEY_C',$uidkey));

/**
* IS_TYPING
* Set to 0 if you want to disable is Typing... feature and 1 to enable it.
* @define boolean
*/
if(!defined('IS_TYPING')) {
	define('IS_TYPING',setConfigValue('IS_TYPING','0'));
}

/**
* MESSAGE_RECEIPT
* Set to 0 if you want to disable message receipts feature and 1 to enable it.
* @define boolean
*/
if(!defined('MESSAGE_RECEIPT')) {
	define('MESSAGE_RECEIPT',setConfigValue('MESSAGE_RECEIPT','0'));
}

/**
* CS_MESSAGE_SYNC
* Set to 0 if you want to disable message sync with cometservice and 1 to enable it.
* @define boolean
*/
if(!defined('CS_MESSAGE_SYNC')) {
	define('CS_MESSAGE_SYNC',setConfigValue('CS_MESSAGE_SYNC','0'));
}

/**
* TRANSPORT
* set default Config Value for TRANSPORT
* @define string
*/
define('TRANSPORT',setConfigValue('TRANSPORT','cometservice'));

/**
* CS_HTTP_PORT
* set default Config Value for CS_HTTP_PORT (Comet Service Port for http)
* @define numeric
*/
define('CS_HTTP_PORT',setConfigValue('CS_HTTP_PORT',''));

/**
* CS_HTTPS_PORT
* set default Config Value for CS_HTTP_PORT (Comet Service Port for https)
* @define numeric
*/
define('CS_HTTPS_PORT',setConfigValue('CS_HTTPS_PORT',''));

/**
* CS_RELAY_PORT
* set default Config Value for CS_RELAY_PORT (Comet Service Port for https)
* @define numeric
*/
define('CS_RELAY_PORT',setConfigValue('CS_RELAY_PORT','3478'));

/**
* CS_DOMAIN_NAME
* set default Config Value for CS_DOMAIN_NAME (Comet Service domain name)
* @define string
*/
define('CS_DOMAIN_NAME',setConfigValue('CS_DOMAIN_NAME',''));

/**
* CS_URL_PATH
* set default Config Value for CS_URL_PATH (Comet Service URL Path)
* @define string
*/
define('CS_URL_PATH',setConfigValue('CS_URL_PATH','cometservice.ashx'));

/**
* $cs_textchat_server
* set default Config Value for $cs_textchat_server (Comet Service text chat server)
* @define string
*/
$cs_textchat_server = (CS_DOMAIN_NAME != '' && CS_HTTPS_PORT != '') ? "https://".CS_DOMAIN_NAME.":".CS_HTTPS_PORT : '';

/**
* CS_TEXTCHAT_SERVER
* set default Config Value for CS_TEXTCHAT_SERVER (Comet Service text chat server)
* @define string
*/
define('CS_TEXTCHAT_SERVER', $cs_textchat_server);

/**
* CS2_TEXTCHAT_SERVER
* set default Config Value for CS2_TEXTCHAT_SERVER (Comet Service version 2 text chat server)
* @define string
*/
define('CS2_TEXTCHAT_SERVER',setConfigValue('CS2_TEXTCHAT_SERVER','instant.cometondemand.net'));

/**
* USE_CS_LEGACY
* set default Config Value for USE_CS_LEGACY (Comet Service Dependency alternate way)
* @define boolean
*/
define('USE_CS_LEGACY',setConfigValue('USE_CS_LEGACY','0'));

/**
* COMET_CHATROOMS
* set default Config Value for COMET_CHATROOMS (Comet Service Chatrooms)
* @define boolean
*/
define('COMET_CHATROOMS',setConfigValue('COMET_CHATROOMS','1'));

if(!defined('AWS_STORAGE')) {

  /**
  * AWS_STORAGE
  * set default Config Value for AWS Storage
  * @define boolean
  */
	define('AWS_STORAGE',setConfigValue('AWS_STORAGE','0'));

  /*AWS Keys and bucket URL*/
  /**
  * AWS_ACCESS_KEY
  * set default Config Value for AWS Access Key
  * @define string
  */
	if(!defined('AWS_ACCESS_KEY')) {
		define('AWS_ACCESS_KEY',setConfigValue('AWS_ACCESS_KEY',''));
	}

  /**
  * AWS_SECRET_KEY
  * set default Config Value for AWS Secret Key
  * @define string
  */
	if(!defined('AWS_SECRET_KEY')) {
		define('AWS_SECRET_KEY',setConfigValue('AWS_SECRET_KEY',''));
	}

  /**
  * AWS_BUCKET
  * set default Config Value for AWS Bucket
  * @define string
  */
	if(!defined('AWS_BUCKET')) {
		define('AWS_BUCKET',setConfigValue('AWS_BUCKET',''));
	}
}

/**
* $aws_bucket_url
* set default Config Value for $aws_bucket_url
* @define string
*/
$aws_bucket_url = setConfigValue('aws_bucket_url',AWS_BUCKET);
$buckets = explode("/", $aws_bucket_url);
$awsendpoint = !empty($buckets)?$buckets[0]:'';


/**
* $bucket_path
* Add client's id in bucket URL if it cloud
* @define string
*/
$bucket_path = !empty($client)?$client.'/':'';

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/* ADVANCED Settings Start */
/**
* REFRESH_BUDDYLIST
* Refresh Buddy list timing
* @define integer
*/
define('REFRESH_BUDDYLIST',setConfigValue('REFRESH_BUDDYLIST','60'));   // Time in seconds after which the user's "Who's Online" list is refreshed

/**
* DISABLE_SMILEYS
* Set to 1 if you want to disable smileys
* @define boolean
*/
define('DISABLE_SMILEYS',setConfigValue('DISABLE_SMILEYS','0'));

/**
* DISABLE_LINKING
* Set to 1 if you want to disable auto linking
* @define boolean
*/
define('DISABLE_LINKING',setConfigValue('DISABLE_LINKING','0'));

/**
* DISABLE_YOUTUBE
* Set to 1 if you want to disable  CometChat
* @define boolean
*/
define('DISABLE_YOUTUBE',setConfigValue('DISABLE_YOUTUBE','1'));

/**
* GZIP_ENABLED : Dev Mode Setting
* Set to 1 if you would like to compress output of JS and CSS
* @define boolean
*/
define('GZIP_ENABLED',setConfigValue('GZIP_ENABLED','1'));
if(!defined('DEV_MODE')) {
  /**
  * DEV_MODE
  * Set to 1 only during development
  * @define boolean
  */
	define('DEV_MODE',setConfigValue('DEV_MODE','0'));
}

/**
* Set to 1 to log all errors (error.log file)
* @define boolean
*/
define('ERROR_LOGGING',setConfigValue('ERROR_LOGGING','0'));

/**
* AVOIDCALLBACK
* Set to 1 to avoid callback while autoupdate
* @define boolean
*/
define('AVOIDCALLBACK',setConfigValue('AVOIDCALLBACK','0'));

/**
* ONLINE_TIMEOUT
* Time in seconds after which a user is considered offline
* @define string
*/
define('ONLINE_TIMEOUT',setConfigValue('ONLINE_TIMEOUT',USE_COMET?REFRESH_BUDDYLIST*2:($maxHeartbeat/1000*2.5)));

/**
* DISABLE_ANNOUNCEMENTS
* Reduce server stress by disabling announcements
* @define boolean
*/
define('DISABLE_ANNOUNCEMENTS',setConfigValue('DISABLE_ANNOUNCEMENTS','0'));

/**
* DISABLE_ISTYPING
* Reduce server stress by disabling X is typing feature
* @define boolean
*/
define('DISABLE_ISTYPING',setConfigValue('DISABLE_ISTYPING','1'));
if(!defined('CROSS_DOMAIN')) {
  /**
  * CROSS_DOMAIN
  * Do not activate without consulting the CometChat Team
  * @define boolean
  */
	define('CROSS_DOMAIN',setConfigValue('CROSS_DOMAIN','0'));
}
if (CROSS_DOMAIN == 0){
  /**
  * ENCRYPT_USERID
  * Set to 1 to encrypt userid
  * @define boolean
  */
	define('ENCRYPT_USERID', '0');
}else{
	define('ENCRYPT_USERID', '0');
  /**
  * CC_SITE_URL
  * Enter Site URL only if Cross Domain is enabled.
  * @define string
  */
	define('CC_SITE_URL', setConfigValue('CC_SITE_URL',''));
}

$queries = new SqlQueries();
$sql_queries = array_merge($sql_queries, $queries->getQueries());

/* ROLE BASE ACCESS CONTROL START */

/**
* ROLE_BASE_ACCESS
* 1:enabled / 0:disabled.
* @define boolean
*/
if (defined('ROLE_BASE_ACCESS') && ROLE_BASE_ACCESS==1) {
    $featureArray['core'] = array(
        'name' => 'Text Chat',
        'credit' => array('creditsToDeduct' => 0,'deductionInterval' => 0)
    );
    $coreFeature  = setConfigValue('coreFeature',$featureArray);
    $pluginsCore    = setCreditKey(unserialize($settings['plugins_core']['value']),'0');
    $modulesCore    = setCreditKey(unserialize($settings['modules_core']['value']),'1');
    $extensionCore  = setCreditKey(unserialize($settings['extensions_core']['value']),'');
		$membersRestrictions = getRolesDetails();
		foreach ($membersRestrictions as $mRkey => $mRvalue) {
        		${$mRkey.'_core'}               = setConfigValue($mRkey.'_core',$coreFeature);
				${$mRkey.'_plugins'}            = setConfigValue($mRkey.'_plugins',$pluginsCore);
				${$mRkey.'_modules'}            = setConfigValue($mRkey.'_modules',$modulesCore);
				${$mRkey.'_extensions'}         = setConfigValue($mRkey.'_extensions',$extensionCore);
				${$mRkey.'_disabledweb'}        = setConfigValue($mRkey.'_disabledweb','0');
				${$mRkey.'_disabledmobileapp'}  = setConfigValue($mRkey.'_disabledmobileapp','0');
				${$mRkey.'_disableddesktop'}    = setConfigValue($mRkey.'_disableddesktop','0');
				${$mRkey.'_disabledcc'}         = setConfigValue($mRkey.'_disabledcc','0');
		}
}
/* ROLE BASE ACCESS CONTROL END */

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/* GROUPS SETTINGS START */
/**
* $chatroomTimeout
* Set config value for chat room Timeout
* @var integer
*/
$chatroomTimeout = setConfigValue('chatroomTimeout','604800');

/**
* $lastMessages
* Set config value for last Messages
* @var integer
*/
$lastMessages = setConfigValue('lastMessages','10');

/**
* $allowUsers
* Set config value for allow Users
* @var boolean
*/
$allowUsers = setConfigValue('allowUsers','1');

/**
* $allowGuests
* Set config value for allow Guests
* @var boolean
*/
$allowGuests = setConfigValue('allowGuests','1');

/**
* $allowDelete
* Set config value for allow Delete
* @var boolean
*/
$allowDelete = setConfigValue('allowDelete','1');

/**
* $allowAvatar
* Set config value for allow Avator. Show users avatars while viewing Groups users
* @var boolean
*/
$allowAvatar = setConfigValue('allowAvatar','1');

/**
* $crguestsMode
* Set config value for crguests Mode
* @var boolean
*/
$crguestsMode = setConfigValue('crguestsMode','1');

/**
* $showChatroomUsers
* Set config value for Show Chatroom Users
* @var boolean
*/
$showChatroomUsers = setConfigValue('showChatroomUsers','1');

/**
* $minHeartbeat
* Set config value for Min Heart Beat
* @var integer
*/
$minHeartbeat = setConfigValue('minHeartbeat','3000');

/**
* $maxHeartbeat
* Set config value for Max Heart Beat
* @var integer
*/
$maxHeartbeat = setConfigValue('maxHeartbeat','12000');

/**
* $autoLogin
* Set config value for Auto Login
* @var boolean
*/
$autoLogin = setConfigValue('autoLogin','0');

/**
* $messageBeep
* Set config value for Message Beep
* @var boolean
*/
$messageBeep = setConfigValue('messageBeep','1');

/**
* $newMessageIndicator
* Set config value for New Message Indicator
* @var boolean
*/
$newMessageIndicator = setConfigValue('newMessageIndicator','1');

/**
* $showchatbutton
* Show private chat for friends only
* @var boolean
*/
$showchatbutton = setConfigValue('showchatbutton','1');

/**
* $showUsername
* If set to 1 then only online users will be shown under view users setting of group
* @var boolean
*/
$showUsername = setConfigValue('showUsername','0');

/**
* $showGroupsOnlineUsers
* Set config value for Show Groups Online Users
* @var boolean
*/
$showGroupsOnlineUsers = setConfigValue('showGroupsOnlineUsers','0');

/**
* $moderatorUserIDs
* Set config value for Moderator User IDs
* @var array()
*/
$moderatorUserIDs = setConfigValue('moderatorUserIDs',array());


if (USE_COMET == 1 && COMET_CHATROOMS == 1) {
	$minHeartbeat = $maxHeartbeat = REFRESH_BUDDYLIST.'000';
}

/**
* $chatroomLongNameLength
* The chatroom length after which characters will be truncated
* @var integer()
*/
$chatroomLongNameLength = 60;

/**
* $moderatorUserIDs
* The chatroom length after which characters will be truncated
* @var integer()
*/
$chatroomShortNameLength = 30;

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Pulls the language file if found

/**
* include language file if found
*/
if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'lang.php')){
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'lang.php');
}

if(!empty($client)) {
	$sdk = ($plan == '43' || $plan == '44') ? 1 : $sdk;
}

global $channelprefix;
$channelprefix = (preg_match('/www\./', $_SERVER['HTTP_HOST']))?$_SERVER['HTTP_HOST']:'www.'.$_SERVER['HTTP_HOST'];
$channelprefix = !empty($client)?md5($client):md5($channelprefix.BASE_URL);

/**
* $GLOBALS['useragent']
* set value for user agent
* @var string()
*/
$GLOBALS['useragent'] = (!empty($_SERVER["HTTP_USER_AGENT"])) ? $_SERVER["HTTP_USER_AGENT"] : '';

/**
* $GLOBALS['adminjstag']
*/
$GLOBALS['adminjstag'] = getDynamicScriptAndLinkTags(array('admin'=>1,'ext'=>'js'));

/**
* $GLOBALS['admincsstag']
*/
$GLOBALS['admincsstag'] = getDynamicScriptAndLinkTags(array('admin'=>1,'ext'=>'css'));
