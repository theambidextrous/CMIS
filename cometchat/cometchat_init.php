<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/
$iscmsplugin = 0;
if(stripos(dirname(__FILE__),'/plugins/cometchat')){
	$trace = debug_backtrace();
	foreach($trace as $traceindex => $traceval ){
		if(!empty($traceval['file'])&&stripos($traceval['file'], 'cometchat.php')){
			$iscmsplugin = 1;
			break;
		}
	}
}
function cleanRequestParams($Input){
    if (!is_array($Input)){
    	return str_replace('<','',str_replace('"','',str_replace("'",'',str_replace('>','',trim($Input)))));
    }
    return array_map('cleanRequestParams', $Input);
}
if(!$iscmsplugin && !isset($_REQUEST['deny_sanitize'])){
	$requestKeyExceptions = array('message', 'statusmessage', 'social_details', 'receivedunreadmessages', 'readmessages', 'crreadmessages','appinfo','recentchats','ci_session');
	foreach($_REQUEST as $key => $val){
		if(!in_array($key, $requestKeyExceptions)){
			$_REQUEST[$key] = cleanRequestParams($_REQUEST[$key]);
			if(!empty($_POST[$key])){
				$_POST[$key] = cleanRequestParams($_POST[$key]);
			}
			if(!empty($_GET[$key])){
				$_GET[$key] = cleanRequestParams($_GET[$key]);
			}
			if(!empty($_COOKIE[$key])){
				$_COOKIE[$key] = cleanRequestParams($_COOKIE[$key]);
			}
		}
	}
}

include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."config.php");
include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."cometchat_guests.php");
include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."php4functions.php");

if(!empty($enablecustomphp) && $enablecustomphp == 1 && file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."writable".DIRECTORY_SEPARATOR."custom".DIRECTORY_SEPARATOR.'custom.php')){
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."writable".DIRECTORY_SEPARATOR."custom".DIRECTORY_SEPARATOR."custom.php");
}

if(USE_COMET == 1){
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'transports'.DIRECTORY_SEPARATOR.TRANSPORT.DIRECTORY_SEPARATOR.'comet.php');
}

if(CROSS_DOMAIN == 1){
	header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');
}

if(defined('SET_SESSION_NAME') && SET_SESSION_NAME != ''){
	session_name(SET_SESSION_NAME);
}

if(empty($_REQUEST['basedata'])){
	$_REQUEST['basedata'] = 'null';
} elseif($_REQUEST['basedata']=='logout'){
	$response = array('loggedout'=>1, 'message'=>"No Auth Found");
	sendCCResponse(json_encode($response));
}else{
	if(CROSS_DOMAIN == 1 || (!empty($_REQUEST['callbackfn']) && in_array($_REQUEST['callbackfn'],array('desktop','mobileapp')))){
		if($_REQUEST['basedata'] != 'null'){
			$basedata = json_decode(base64_decode(rawurldecode($_REQUEST['basedata'])));
			if(is_object($basedata) && $basedata->id){
				$sessionid = $basedata->id;
			}else {
				$sessionid = $_REQUEST['basedata'];
			}
			session_id($client.md5($sessionid));
			session_start();
		}
	}
}

if(session_id() == ''){
    session_start();
}

if(get_magic_quotes_gpc() || (defined('FORCE_MAGIC_QUOTES') && FORCE_MAGIC_QUOTES == 1)){
	$_GET = stripSlashesDeep($_GET);
	$_POST = stripSlashesDeep($_POST);
	$_REQUEST = stripSlashesDeep($_REQUEST);
	$_COOKIE = stripSlashesDeep($_COOKIE);
}

if(CROSS_DOMAIN == 1  || (!empty($_REQUEST['callbackfn']) && $_REQUEST['callbackfn'] == 'mobileapp')){
    if(!empty($_REQUEST)){
        foreach ($_REQUEST as $param => $value){
            if(substr($param,0,7) == 'cookie_'){
                if($value != 'null'){
                    $_COOKIE[substr($param,7)] = $value;
                }
            }
        }
    }
}

if(!empty($_REQUEST['basedata'])){
	if(!empty($client)){
		$_SESSION['old_basedata'] = !empty($_SESSION['basedata']) ? rawurldecode($_SESSION['basedata']):'';
	}
	$_SESSION['basedata'] = $_REQUEST['basedata'];
}

if(get_magic_quotes_runtime()){
    set_magic_quotes_runtime(false);
}


ini_set('log_errors', 'Off');
ini_set('display_errors','Off');

if(defined('ERROR_LOGGING') && ERROR_LOGGING == '1'){
	error_reporting(E_ALL);
	ini_set('error_log', 'error.log');
	ini_set('log_errors', 'On');
}

if(defined('DEV_MODE') && DEV_MODE == '1'){
	error_reporting(E_ALL);
	ini_set('display_errors','On');
}

cometchatDBConnect();

cometchatMemcacheConnect();

if(empty($bannedUserIPs)){
	$bannedUserIPs = array();
}
$userid = getUserID();

if(function_exists('hooks_processUserID')){
	$userid = hooks_processUserID(array('userid' => $userid));
}

if($guestsMode && $userid == 0 && (empty($_REQUEST['callbackfn']) ||  ($_REQUEST['callbackfn'] <> 'mobileapp' && $_REQUEST['callbackfn'] <> 'desktop'))){
	if(empty($noguestlogin) && empty($_SESSION['noguestmode']) && (USE_CCAUTH == 0 || !empty($_COOKIE['cc_guest_login']))){
		$username = '';
		if(!empty($_REQUEST['username'])){
			$username = $_REQUEST['username'];
		}
		$userid = getGuestID($username);
	}
}

if(defined('ROLE_BASE_ACCESS') && ROLE_BASE_ACCESS == 1){
	$platformType = empty($_REQUEST['callbackfn']) ? 'web' : $_REQUEST['callbackfn'];
	$getRole = getRole($userid);
	if ($GLOBALS[$getRole."_disabled".$platformType] == 1) {
		$userid = 0;
	}
	if ($GLOBALS[$getRole."_disabledcc"] == 1) {
		$userid = 0;
	}
}

if((!empty($_SERVER['REMOTE_ADDR']) && in_array($_SERVER['REMOTE_ADDR'], $bannedUserIPs)) || in_array($userid, $bannedUserIDs)){
	$userid = 0;
}

if(empty($_SESSION['cometchat'])||empty($_SESSION['cometchat']['user'])||empty($_SESSION['cometchat']['user']['n'])){
	getStatus();
}

if(empty($_SESSION['cometchat']['userid']) || $_SESSION['cometchat']['userid'] <> $userid){
	unset($_SESSION['cometchat']);
	unset($_SESSION['CCAUTH_SESSION']);
	$_SESSION['cometchat']['userid'] = $userid;
	setcookie ($cookiePrefix."state", "", time() - 3600,'/');
}
