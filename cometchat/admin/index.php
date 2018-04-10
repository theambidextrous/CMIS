<?php

/*
CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/
define('CCADMIN',true);
global $licensekey;
include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."config.php");
cometchatDBConnect();

include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."cometchat_session.php");
include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."php4functions.php");
session_name('CCADMIN');
session_start();


$livesoftware = 'software';
$accessKey = 'flGBNxeq8Mgu5bynUhS5w3S2CJ7dfo3latMTxDNa';
if(!empty($_COOKIE['software-dev'])){
  $livesoftware = 'software-dev';
}
$marketplace = 0;
if(substr($licensekey, -2) == '-M'){
  $marketplace = 1;
}

$ts = time();
define('ADMIN_URL',BASE_URL.'admin/');
if(!session_id()){
  session_name('CCADMIN');
  @session_start();
}

if(get_magic_quotes_runtime()){
  set_magic_quotes_runtime(false);
}

include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."shared.php");

if(get_magic_quotes_gpc()||(defined('FORCE_MAGIC_QUOTES')&&FORCE_MAGIC_QUOTES==1)){
  $_GET = stripSlashesDeep($_GET);
  $_POST = stripSlashesDeep($_POST);
  $_COOKIE = stripSlashesDeep($_COOKIE);
}

cometchatMemcacheConnect();

$usertable = TABLE_PREFIX.DB_USERTABLE;
$usertable_username = DB_USERTABLE_NAME;
$usertable_userid = DB_USERTABLE_USERID;

$body = '';
if (empty($client) && !empty($_POST['username']) && !empty($_POST['password'])) {
  if ($_POST['username'] == ADMIN_USER && (sha1($_POST['password']) == ADMIN_PASS || $_POST['password'] == ADMIN_PASS)){
    $_SESSION['cometchat']['cometchat_admin_user'] = $_POST['username'];
    if(sha1($_POST['password']) == ADMIN_PASS){
      $_SESSION['cometchat']['cometchat_admin_pass'] = sha1($_POST['password']);
    }else{
      $_SESSION['cometchat']['cometchat_admin_pass'] = $_POST['password'];
    }
  } else {
    $_SESSION['cometchat']['error'] = "Incorrect username/password. Please try again.";
    $_SESSION['cometchat']['type'] = 'alert';
  }
}

if(!function_exists("authenticate")) {
  function authenticate(){
    if(empty($_SESSION['cometchat']['cometchat_admin_user'])||empty($_SESSION['cometchat']['cometchat_admin_pass'])||!($_SESSION['cometchat']['cometchat_admin_user']==ADMIN_USER&&$_SESSION['cometchat']['cometchat_admin_pass']==ADMIN_PASS)){
      if (filter_var(ADMIN_USER, FILTER_VALIDATE_EMAIL) !== false){
            $usernameplaceholder='Email';
            $texttype='email';
      } else {
            $usernameplaceholder='Username';
            $texttype='text';
      }
      $staticCDNUrl = STATIC_CDN_URL;
      global $body;
      $body = <<<EOD
        <script>
          $(function(){
            var todaysDate = new Date();
            var currentTime = Math.floor(todaysDate.getTime()/1000);
            $(".currentTime").val(currentTime);
          });
        </script>
        <div class="outerframe">
          <div class="middleform">
            <div class="cometchat_logo_div"><img class="cometchat_logo_image" src="{$staticCDNUrl}/admin/images/logo.png"></div>
            <div class="module form-module">
              <div class="form" >
                <h2 >CometChat Administration Panel</h2>
                <form method="post" action="?module=dashboard"+currentTime>
                  <input type="{texttype}" name="username" placeholder="{$usernameplaceholder}" required="true"/>
                  <input type="password" name="password" placeholder="Password" required="true"/>
                  <button type="submit" value="Login">Login</button>
                  <input type="hidden" name="currentTime" class="login_inputbox currentTime">
                  <div class="cometchat_forgotpwd"><a href="index.php?module=forgotpassword&action=forgotpassword" target="_blank">Forgot Password?</a></div>
                </form>
              </div>
            </div>
          </div>
        </div>
EOD;
      template(1);
    }
  }
}

$_GET['module']= (!isset($_GET['module'])) ? '' : $_GET['module'];

if($_GET['module']!='forgotpassword'){
  authenticate();
}

$module = "dashboard";
$action = "index";
error_reporting(E_ALL);
ini_set('display_errors','On');

if(!empty($_GET['module'])){
  if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.$_GET['module'].'.m.php')){
    $module = $_GET['module'];
  }
}

if(!file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.$module.'.m.php')){
  $_SESSION['cometchat']['error'] = 'Oops. This module does not exist.';
  $module = 'dashboard';
}

if (empty($_SESSION['cometchat']['VERSION_CHECK']) && $marketplace == 0) {
  if(checkLicenseVersion()){
    $CometChatResponse = checkCometChatResponse($licensekey);
    if($CometChatResponse['success'] == 1 && $CometChatResponse['has_plan_changed']==1){
      $_SESSION['cometchat']['error'] = 'Your plan has been changed.';
    }elseif($CometChatResponse['success'] == 0){
      $_SESSION['cometchat']['error'] = 'Invalid License.';
    }
  }else{
    $url = "https://my.cometchat.com/".$livesoftware."/getversion?licenseKey=".$licensekey;
    $response = cc_curl_call($url,array());
    $response = json_decode($response,true);
    if(!empty($response)){
      $l_version = '';
      if(cc_version_compare($response['version'],$currentversion)==1){
        $l_version = $response['version'];
      }
      updateNewVersion($l_version);
    }
  }
  $_SESSION['cometchat']['VERSION_CHECK'] = 1;
}

include_once (dirname(__FILE__).DIRECTORY_SEPARATOR.$module.'.m.php');

$allowedActions = array('deleteannouncement','updateorder','ccauth','addauthmode','updateauthmode','index','updatesettings','moderator','newchatroomprocess','newannouncement','newannouncementprocess','newchatroom','updatechatroomorder','loadexternal','makedefault','removecolorprocess','viewuser','viewuserchatroomconversation','viewuserconversation','updatevariablesprocess','editlanguage','editlanguageprocess','restorelanguageprocess','importlanguage','previewlanguage','removelanguageprocess','sharelanguage','data','moderatorprocess','createmodule','createmoduleprocess','chatroomplugins','additionallanguages','createlanguage','createlanguageprocess','uploadlanguage','uploadlanguageprocess','comet','guests','banuser','changeuserpass','updatecomet','updateguests','banuserprocess','changeuserpassprocess','chatroomlog','searchlogs','deletechatroom','finduser','updatelanguage','newlogprocess','addchatroomplugin','whosonline','updatewhosonline','cron','processcron','getlanguage','exportlanguage','caching','updatecaching','removecustommodules','clearcachefilesprocess','makemoderatorprocess','removemoderatorprocess','banusersprocess','unbanusersprocess','ccautocomplete','themeembedcodesettings','googleanalytics','updategoogleanalytics','storage','updatestoragemode','saveplatform','updatecolorval','addnewcolor','devsettings','updatedevsetting','updatebaseurl','loadthemetype','processUpdate','compareHashes','backupFiles','applyChanges','extractZip','generateHash','updateNewVersion','updateNow','desktop','mobile','updatemoduleorder','updateextensionorder','forceUpdate','generalsettings','addmodule','addplugin','updatelicensekey','removeBot','addBot','rebuildBots','updatBotsetting','addReadytoUseBot','callUpdateMethod','updatecustomsetting','forgotpassword','resetpassword','sendemail','resetpasswordprocess','downloadlogs','updatecmssettings','cmssettings','updateAPIResponse','dockedsettings','dockedsettingsprocess','apikey','updatemembership','updateccstatus','addextension','ccinboxsync','updateccinboxsync','exportchat','savelogs','loaduickstats','loadfirstgraphdata','loadsecondgraphdata');


if(!empty($_GET['action'])&&in_array($_GET['action'],$allowedActions)&&function_exists($_GET['action'])){
  $action = sql_real_escape_string($_GET['action']);
}

call_user_func($action);

function onlineusers(){
  global $db;

  $query = sql_query('admin_onlineusers',array('sent'=>getTimeStamp()));
  $chat = sql_fetch_assoc($query);

  $count = !empty($chat['users'])?$chat['users']:0;

  return $count;
}

function template($auth = 0){

  global $ts, $body, $menuoptions, $module, $navigation, $action, $currentversion,$api_response;
  $plan_change_alert = '';
  include_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'sidebar.php');

  $errorjs = '';

  if(!empty($_SESSION['cometchat']['error'])){
    $type = !empty($_SESSION['cometchat']['type']) ? $_SESSION['cometchat']['type'] : 'success';
    $errorjs = <<<EOD
<script>
\$(function() {
  \$.fancyalert('{$_SESSION['cometchat']['error']}','{$type}');
});
</script>
EOD;
    unset($_SESSION['cometchat']['error']);
    unset($_SESSION['cometchat']['type']);
  }
$inactiveoverlay='';
  if(!empty($api_response) && (isset($api_response['active']) && $api_response['active'] != 1) && $module != 'settings' && $action != 'generalsettings'){
    $licenseErrorMessage = "Your License Key is not valid please renew your License at <a href='https://secure.cometchat.com' target='_blank'>CometChat Member's Area</a> and update your License Key <a href='?module=settings&action=generalsettings'>here</a>";
    $inactiveoverlay = <<<EOD
   <div id="admin-panel-disabled" class="admin-disabled-overlay"><div class="overlay-content">Admin Panel Disabled</div></div>
    <script>
      $(document).ready(function(){
        $("#adminModellink").trigger('click');
        $("#admin-modal-title").text('Error Message');
        $("#admin-modal-body").html("{$licenseErrorMessage}");
      })
    </script>
EOD;
  }
  if(!empty($_GET['plan_changed']) && $_GET['plan_changed']==1){
    $plan_change_alert = <<<EOD
    <script>
      $(document).ready(function(){
        $("#adminModellink").trigger('click');
        $("#admin-modal-title").text('Error Message');
        $("#admin-modal-body").html("Your plan has been changed.");
      })
    </script>
EOD;
  }
$testnavigation = <<<EOD
  <div id="leftnav">
  </div>
EOD;

  if ($navigation == $testnavigation || empty($navigation)) {
    $nosubnav = 'nosubnav';
  } else {
    $nosubnav = '';
  }

echo <<<EOD
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="shortcut icon" href="images/favicon.ico">
  <title>CometChat Admin Panel</title>
  {$GLOBALS['adminjstag']}
  {$GLOBALS['admincsstag']}
</head>
EOD;

if ($auth == 1) {
echo <<<EOD
 <body><br><br><br>
      {$body}
EOD;
} else {
echo <<<EOD
 <body class="navbar-fixed sidebar-nav fixed-nav">
 {$inactiveoverlay}
 {$plan_change_alert}
   <header class="navbar">
    <div class="container-fluid">
      <button class="navbar-toggler mobile-toggler hidden-lg-up" type="button">☰</button>
      <a class="navbar-brand" href="#"></a>
    </div>
  </header>
  <div class="sidebar">
   {$navigationbar}
  </div>
  <main class="main">
    <div class="container-fluid">
      {$body}
    </div>
  </main>
  <a style="display:none;" id="adminModellink" href="javascript:void();" data-toggle="modal" data-target="#adminModal">click</a>
  <!-- Modal -->
  <div class="modal fade" id="adminModal" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 id="admin-modal-title" class="modal-title"></h4>
        </div>
        <div id="admin-modal-body" class="modal-body">
        </div>
        <div class="admin-modal-footer" class="modal-footer">
        </div>
      </div>

    </div>
  </div>
<!-- Modal -->
EOD;
}
$adminappjstag =  getDynamicScriptAndLinkTags(array('admin'=>1,'app'=>1,'ext'=>'js'));
echo <<<EOD
  {$adminappjstag}
  {$errorjs}
  <script>
  $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();
  });
  </script>
</body>
</html>
EOD;
    exit();
}
