<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/
$callbackfn = null;
if(!empty($_REQUEST['callbackfn'])){
    $callbackfn = $_REQUEST['callbackfn'];
}
if($callbackfn <> 'mobileapp'){
    echo "Nothing to look here";
    exit;
}
include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."config.php");
/*
* App validity
*/
if(checkLicenseVersion()){
    $platformErrorMessage = "White Labelled Mobile Apps";
    $platform = 'whitelabelledmobileapp';
    if(!empty($_REQUEST['stockapp']) && $_REQUEST['stockapp'] ==1 ){
        $platformErrorMessage = "Mobile Apps";
        $platform = 'mobileapp';
    }
    if(!empty($_REQUEST['cc_sdk']) && $_REQUEST['cc_sdk'] ==1 ){
        $platformErrorMessage = "Mobile SDK";
        $platform = 'mobilesdk';
    }
    if(!in_array($platform, $api_response['plan']['platforms'])){
        echo $platformErrorMessage." are not included in your plans, Please upgrade your plan.";
        exit;
    }
}

include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."config.php");
if(file_exists(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR."chatrooms".DIRECTORY_SEPARATOR."lang.php")){
    include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR."chatrooms".DIRECTORY_SEPARATOR."lang.php");
}
if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php")){
    include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php");
}

if(file_exists(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."colors".DIRECTORY_SEPARATOR.$color.'.php')){
    include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."colors".DIRECTORY_SEPARATOR.$color.'.php');
}else{
    include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'colors'.DIRECTORY_SEPARATOR.'color.php');
}
$response_lang = Array();
$supported_plugins = array('clearconversation', 'style', 'report', 'avchat', 'filetransfer','block','audiochat','whiteboard', 'broadcast', 'handwrite', 'writeboard', 'smilies');

foreach($supported_plugins as $key => $plugin){
    if(in_array($plugin,$plugins) || in_array($plugin,$crplugins)){
        if(file_exists(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR.$plugin.DIRECTORY_SEPARATOR."lang.php")){
            include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR.$plugin.DIRECTORY_SEPARATOR."lang.php");
        }
        $key = $plugin. '_language';
        ${$key}['h'] = "cc";
        $response_lang[$plugin] = ${$key};
    }
}
$mobileapp_language['h'] = "cc";
$language['h'] = "cc";
$response_lang['core'] = $language;
$response_lang['chatrooms'] = $chatrooms_language;
$response_lang['mobile'] = $mobileapp_language;
$response['cookieprefix'] = $cookiePrefix;
$response['currentversion'] = $currentversion;
$response['licensekey'] = $licensekey;
$response['showindependencedayussticker'] = '1';
$response['guestnamePrefix'] = $guestnamePrefix;

foreach($response_lang as $key => $val){
    if(is_array($val)){
        foreach($val as $langkey => $langval){
            $response_lang[$key][$langkey] = strip_tags($langval);
        }
    }
}

$response['lang'] = $response_lang;
$response['history_message_limit'] = 10;


$response['mobile_theme']['login_button_pressed']= $response['mobile_theme']['login_button'] = $layoutSettings['primary_color'];
$response_config['oneonone_enabled'] = ($disableContactsTab) ? '0' : '1';
$response_config['announcement_enabled'] = $announcement_enabled;

$response_config['DISPLAY_ALL_USERS'] = DISPLAY_ALL_USERS;
$response_config['REFRESH_BUDDYLIST'] = REFRESH_BUDDYLIST;
$response_config['USE_COMET'] = USE_COMET;
$response_config['minHeartbeat'] = $minHeartbeat;
$response_config['maxHeartbeat'] = $maxHeartbeat;
$response_config['bots_enabled'] = $usebots;
$response_config['disableRecentTab'] = (string)$disableRecentTab;
$response_config['disableContactsTab'] = (string)$disableContactsTab;
$response['chatroomsmodule_enabled'] = ($disableGroupTab) ? '0':'1';

if(defined('USE_COMET') && USE_COMET == '1'){
    $response_config['KEY_A'] = KEY_A;
    $response_config['KEY_B'] = KEY_B;
    $response_config['KEY_C'] = KEY_C;
    $response_config['TRANSPORT'] = TRANSPORT;
    $response_config['COMET_CHATROOMS'] = COMET_CHATROOMS;
    $response_config['CS2_TEXTCHAT_SERVER'] = CS2_TEXTCHAT_SERVER;
    if(defined('CS_TEXTCHAT_SERVER')){
        /* START: Backward Compatibility 16-Oct-2017 CometChat v6.8.12 */
        $response['websync_server'] = preg_replace('/\s+/', '', CS_TEXTCHAT_SERVER);
        /* END: Backward Compatibility 16-Oct-2017 CometChat v6.8.12 */
        $response['CS_TEXTCHAT_SERVER'] = preg_replace('/\s+/', '', CS_TEXTCHAT_SERVER);
        $response_config['CS_HTTP_PORT']    = CS_HTTP_PORT;
        $response_config['CS_HTTPS_PORT']   = CS_HTTPS_PORT;
        $response_config['CS_RELAY_PORT']   = CS_RELAY_PORT;
        $response_config['CS_DOMAIN_NAME']  = CS_DOMAIN_NAME;
        $response_config['CS_URL_PATH']     = CS_URL_PATH;
    }
}

/* ROLE BASE ACCESS CONTROL RESPONSE START */
if (defined('ROLE_BASE_ACCESS') && ROLE_BASE_ACCESS==1) {
    $members_roles  = getRolesDetails();
    $role_base_access = array();
    foreach ($members_roles as $rKey => $rValue) {
        $role_base_access[$rKey.'_plugins']             =   ${$rKey.'_plugins'};
        $role_base_access[$rKey.'_modules']             =   ${$rKey.'_modules'};
        $role_base_access[$rKey.'_extensions']          =   ${$rKey.'_extensions'};
        $role_base_access[$rKey.'_disabledweb']         =   ${$rKey.'_disabledweb'};
        $role_base_access[$rKey.'_disabledmobileapp']   =   ${$rKey.'_disabledmobileapp'};
        $role_base_access[$rKey.'_disableddesktop']     =   ${$rKey.'_disableddesktop'};
        $role_base_access[$rKey.'_disabledcc']          =   ${$rKey.'_disabledcc'};
    }
    $response['role_base_access']  = $role_base_access;
}
/* ROLE BASE ACCESS CONTROL RESPONSE END */


$response['cometchat_version']  = $currentversion;
$response['config'] = $response_config;
$response['avchat_enabled'] = '0';

if(in_array('avchat',$plugins) && file_exists(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."avchat".DIRECTORY_SEPARATOR."config.php")){
    include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."avchat".DIRECTORY_SEPARATOR."config.php");
    $response['avchat_enabled'] = '1';
    $response['webRTCServer'] = $webRTCServer;
}

 $response['voicenote_enabled'] = '0';
if(in_array('voicenote',$plugins) && file_exists(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."voicenote".DIRECTORY_SEPARATOR."index.php")){
    $response['voicenote_enabled'] = '1';
}

$response['audiochat_enabled'] = '0';
if(in_array('audiochat',$plugins) && file_exists(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."audiochat".DIRECTORY_SEPARATOR."config.php")){
    include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."audiochat".DIRECTORY_SEPARATOR."config.php");
    $response['audiochat_enabled'] = '1';
    $response['webRTCServer'] = $webRTCServer;

}

$response['config']['avbroadcast_enabled'] = '0';
if(in_array('broadcast', $plugins) && file_exists(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."broadcast".DIRECTORY_SEPARATOR."config.php")){
	include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."broadcast".DIRECTORY_SEPARATOR."config.php");
    $response['config']['avbroadcast_enabled'] = '1';
    $response['webRTCServer'] = $webRTCServer;
}

$response['config']['audioconference_enabled'] = '0';
if(in_array('audiochat',$crplugins) && file_exists(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."audiochat".DIRECTORY_SEPARATOR."config.php")){
   include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."audiochat".DIRECTORY_SEPARATOR."config.php");
    $response['audioconference_enabled'] = '1';
    $response['config']['audioconference_enabled'] = '1';
    $response['webRTCServer'] = $webRTCServer;
}

 $response['crvoicenote_enabled'] = '0';
if(in_array('voicenote',$crplugins) && file_exists(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."voicenote".DIRECTORY_SEPARATOR."index.php")){
    $response['crvoicenote_enabled'] = '1';
}

$response['config']['writeboard_enabled'] = '0';
if(in_array('writeboard', $plugins) && file_exists(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."writeboard".DIRECTORY_SEPARATOR."config.php")){
    include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."writeboard".DIRECTORY_SEPARATOR."config.php");
    $response['config']['writeboard_enabled'] = '1';
}

$response['config']['smilies_enabled'] = '0';
if(in_array('smilies', $plugins)){
    $response['config']['smilies_enabled'] = '1';
}

$response['config']['handwrite_enabled'] = '0';
if(in_array('handwrite', $plugins)){
    $response['config']['handwrite_enabled'] = '1';
}

$response['filetransfer_enabled'] = '0';
if(in_array('filetransfer',$plugins)){
    $response['filetransfer_enabled'] = '1';
}
$response['report_enabled'] = '0';
if(in_array('report',$plugins) && file_exists(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."report".DIRECTORY_SEPARATOR."config.php")){
    $response['report_enabled'] = '1';
}

$response['block_user_enabled']= '0';
if(in_array('block', $plugins)){
    $response['block_user_enabled']= '1';
}

$response['clearconversation_enabled'] = '0';
if(in_array('clearconversation',$plugins)){
    $response['clearconversation_enabled'] = '1';
}

$response['config']['stickers_enabled'] = '0';
if(in_array('stickers', $plugins)){
    $response['config']['stickers_enabled'] = '1';
}

$response['config']['whiteboard_enabled'] = '0';
if(in_array('whiteboard', $plugins) && file_exists(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."whiteboard".DIRECTORY_SEPARATOR."config.php")){
    include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."whiteboard".DIRECTORY_SEPARATOR."config.php");
    $response['config']['whiteboard_enabled'] = '1';
    $response['config']['whiteboard_url'] = $drawURL;
}

$response['config']['last_seen_enabled'] = $lastseen;
$response['config']['typing_enabled'] = IS_TYPING;
$response['config']['receipts_enabled'] = MESSAGE_RECEIPT;

$response['realtime_translation'] = '0';
$response['config']['rtt_key'] = '';
$response['config']['broadcastmessage_enabled'] = '0';
$response['config']['single_games_enabled'] = '0';


foreach ($trayicon as $key => $value) {
    if($trayicon[$key][0] == 'announcements'){
        if(file_exists(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR."announcements".DIRECTORY_SEPARATOR."lang.php")){
            include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR."announcements".DIRECTORY_SEPARATOR."lang.php");
        }
        $response['config']['announcement_enabled'] = "1";
        $response['lang']['announcements'] = $announcements_language;
        $response['lang']['announcements']['h']="cc";
    }

    if($trayicon[$key][0] == 'realtimetranslate'){
        include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR."realtimetranslate".DIRECTORY_SEPARATOR."config.php");

        if($useGoogle==1){
            include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR."realtimetranslate".DIRECTORY_SEPARATOR."google.php");
            $response['languageskeycode'] = translate_languages();
            if(file_exists(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR."realtimetranslate".DIRECTORY_SEPARATOR."lang.php")){
                include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR."realtimetranslate".DIRECTORY_SEPARATOR."lang.php");
            }
            $response['realtime_translation'] = '1';
            $response['config']['rtt_key'] = $googleKey;
            $response['lang']['realtimetranslate'] = $realtimetranslate_language;
        }
    }

    if($trayicon[$key][0] == 'broadcastmessage'){
        $response['config']['broadcastmessage_enabled'] = '1';
    }
    if($trayicon[$key][0] == 'games'){
       $response['config']['single_games_enabled'] = '1';
   }
}

if($response['chatroomsmodule_enabled'] == '1'){
    $response['allowusers_createchatroom'] = '0';
    $response['allowguests_createchatroom'] = '0';
    if($allowGuests == '1'){
        $response['allowguests_createchatroom'] = '1';
    }
    if($allowUsers == '1'){
        $response['allowusers_createchatroom'] = '1';
    }

    $response['config']['crpersonal_chat'] = '0';
    if($showchatbutton == '1'){
        $response['config']['crpersonal_chat'] = '1';
    }
    $response['config']['crhideusercount'] = '0';
    if($showChatroomUsers == '0'){
        $response['config']['crhideusercount'] = '1';
    }
    $response['config']['crtextcolor_enabled'] = '0';
    if(in_array('style', $crplugins) && file_exists(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."style".DIRECTORY_SEPARATOR."config.php")){
        include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."style".DIRECTORY_SEPARATOR."config.php");
        $response['config']['crtextcolor_enabled'] = '1';
        $response['config']['crstyles'] = array("textcolor" => explode(',', $styleOptions));
    }

    $response['config']['crwhiteboard_enabled'] = '0';
    if(in_array('whiteboard', $crplugins) && file_exists(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."whiteboard".DIRECTORY_SEPARATOR."config.php")){
        include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."whiteboard".DIRECTORY_SEPARATOR."config.php");
        $response['config']['crwhiteboard_enabled'] = '1';
    }

    $response['config']['crsmilies_enabled'] = '0';
    if(in_array('smilies', $crplugins)){
        $response['config']['crsmilies_enabled'] = '1';
    }

    $response['config']['cravbroadcast_enabled'] = '0';
    if(in_array('broadcast', $crplugins) && file_exists(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."broadcast".DIRECTORY_SEPARATOR."config.php")){
        include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."broadcast".DIRECTORY_SEPARATOR."config.php");
        $response['config']['cravbroadcast_enabled'] = '1';
        $response['webRTCServer'] = $webRTCServer;
    }

    $response['config']['avconference_enabled'] = '0';
    if(in_array('avchat', $crplugins) && file_exists(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."avchat".DIRECTORY_SEPARATOR."config.php")){
        include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."avchat".DIRECTORY_SEPARATOR."config.php");
        $response['config']['avconference_enabled'] = '1';
        $response['webRTCServer'] = $webRTCServer;
    }

    $response['config']['crwriteboard_enabled'] = '0';
    if(in_array('writeboard', $crplugins) && file_exists(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."writeboard".DIRECTORY_SEPARATOR."config.php")){
        include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."writeboard".DIRECTORY_SEPARATOR."config.php");
        $response['config']['crwriteboard_enabled'] = '1';
    }

    $response['config']['crhandwrite_enabled'] = '0';
    if(in_array('handwrite', $crplugins)){
        $response['config']['crhandwrite_enabled'] = '1';
    }

    $response['config']['crstickers_enabled'] = '0';
    if(in_array('stickers', $crplugins) && file_exists(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."stickers".DIRECTORY_SEPARATOR."config.php")){
        include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."stickers".DIRECTORY_SEPARATOR."config.php");
        $response['config']['crstickers_enabled'] = '1';
    }
    $response['crclearconversation_enabled'] = '0';
    if(in_array('clearconversation',$crplugins)){
        $response['crclearconversation_enabled'] = '1';
    }
    $response['crfiletransfer_enabled'] = '0';
    if(in_array('filetransfer',$crplugins)){
        $response['crfiletransfer_enabled'] = '1';
    }
}
$response['ad_unit_id'] = $adunit_id;
$response['register_url']  = $register_url;
$response['config']['invite_via_sms'] = $invite_via_sms;
$response['config']['share_this_app'] = $share_this_app;
$response['mobile_theme']['actionbar_color']= $layoutSettings['tab_title_background'] = $layoutSettings['primary_color'];
$response['mobile_theme']['primary_color']= $layoutSettings['primary_color'];
$response['mobile_theme']['secondary_color']= $layoutSettings['secondary_color'];
$response['mobile_theme']['right_bubble_color']= $layoutSettings['primary_color'];

/* ------------ config starts ------------*/

$response['mobile_config']['social_auth_enabled']= USE_CCAUTH; //If you are using client's social login. Set this to 1.
$response['mobile_config']['notification_provider']= $provider_pushnotification;
$response['mobile_config']['f_enabled']= '0';
$response['mobile_config']['t_enabled']= '0';
$response['mobile_config']['g_enabled']= '0';
if(in_array('Facebook', $ccactiveauth)){
    $response['mobile_config']['f_enabled']= '1';
}
if(in_array('Google', $ccactiveauth)){
    $response['mobile_config']['g_enabled']= '1';
}
if(in_array('Twitter', $ccactiveauth)){
    $response['mobile_config']['t_enabled']= '1';
}
$response['mobile_config']['guest_enabled']= $guestsMode;
$response['mobile_config']['uniqueguestname']= $uniqueguestname;

if(checkAuthMode('social')){
    $response['mobile_config']['username_password_enabled']= '0';
}
$response['mobile_config']['logout_enabled'] = $response['mobile_config']['phone_number_enabled'] == '1' ? '0' : '1';

/* -----------  config ends --------------*/

$new_mobile_lang['common']['set'] = $mobileapp_language[103];
$new_mobile_lang['common']['complete_action'] = $mobileapp_language[104];
$new_mobile_lang['common']['inapp_notification_message'] = $mobileapp_language[66];

$new_mobile_lang['settings']['change_profile_pic'] = $mobileapp_language[105];
$new_mobile_lang['settings']['edit_status_message'] = $mobileapp_language[106];
$new_mobile_lang['settings']['status_message_hint'] = $mobileapp_language[107];
$new_mobile_lang['settings']['set_status_message'] = $mobileapp_language[108];
$new_mobile_lang['settings']['invite_viasms'] = $mobileapp_language[109];
$new_mobile_lang['settings']['edit_username'] = $mobileapp_language[110];
$new_mobile_lang['settings']['set_user_name'] = $mobileapp_language[111];
$new_mobile_lang['settings']['username_hint'] = $mobileapp_language[10];
$new_mobile_lang['settings']['set_status'] = $mobileapp_language[112];
$new_mobile_lang['settings']['set_language'] = $mobileapp_language[113];

$new_mobile_lang['ann']['tab_text'] = $mobileapp_language[62];
$new_mobile_lang['ann']['read_more'] = $mobileapp_language[114];
$new_mobile_lang['ann']['read_less'] = $mobileapp_language[115];

$new_mobile_lang['home']['tab_text'] = $mobileapp_language[61];

/* Login screen */
$new_mobile_lang['login']['loader'] = $mobileapp_language[116];
$new_mobile_lang['login']['url_hint'] = $mobileapp_language[117];
$new_mobile_lang['login']["username_hint"] = $mobileapp_language[118];
$new_mobile_lang['login']["password_hint"] = $mobileapp_language[11];
$new_mobile_lang['login']["phone_hint"] = $mobileapp_language[119];
$new_mobile_lang['login']["country_code"] = $mobileapp_language[120];

$new_mobile_lang['login']["remember_me"] = $mobileapp_language[54];
$new_mobile_lang['login']["login_button_text"] = $mobileapp_language[12];
$new_mobile_lang['login']["register_number_button_text"] = $mobileapp_language[121];
$new_mobile_lang['login']["register_link_text"] = $mobileapp_language[53];

$new_mobile_lang['login']["url_blank"] = $mobileapp_language[122];
$new_mobile_lang['login']["username_blank"] = $mobileapp_language[47];
$new_mobile_lang['login']["password_blank"] = $mobileapp_language[123];
$new_mobile_lang['login']["phone_blank"] = $mobileapp_language[124];

$new_mobile_lang['login']["invalid_url"] = $mobileapp_language[125];
$new_mobile_lang['login']["invalid_username"] = $mobileapp_language[126];
$new_mobile_lang['login']["invalid_password"] = $mobileapp_language[127];
$new_mobile_lang['login']["invalid_phone"] = $mobileapp_language[128];

/* Verification screen */
$new_mobile_lang['verify']['actionbar'] = $mobileapp_language[129];
$new_mobile_lang['verify']['loader'] = $mobileapp_language[130];
$new_mobile_lang['verify']['field_hint'] = $mobileapp_language[131];
$new_mobile_lang['verify']['verify_button'] = $mobileapp_language[132];
$new_mobile_lang['verify']['resend_button'] = $mobileapp_language[133];
$new_mobile_lang['verify']['wrong_code'] = $mobileapp_language[134];

/* Create profile */
$new_mobile_lang['create_profile']['actionbar'] = $mobileapp_language[135];
$new_mobile_lang['create_profile']['loader'] = $mobileapp_language[136];
$new_mobile_lang['create_profile']['create_button'] = $mobileapp_language[137];
$new_mobile_lang['create_profile']['field_hint'] = $mobileapp_language[138];
$new_mobile_lang['create_profile']['err_username'] = $mobileapp_language[139];
$new_mobile_lang['create_profile']['photo_hint'] = $mobileapp_language[140];

/* Invite via SMS screen */
$new_mobile_lang['invite_sms']['actionbar'] = $mobileapp_language[141];
$new_mobile_lang['invite_sms']['contacts_hint'] = $mobileapp_language[142];
$new_mobile_lang['invite_sms']['contacts_label'] = $mobileapp_language[142];

$new_mobile_lang['invite_sms']['sms_hint'] = $mobileapp_language[143];
$new_mobile_lang['invite_sms']['sms_android'] = $mobileapp_language[77];
$new_mobile_lang['invite_sms']['sms_ios'] = $mobileapp_language[76];

/*FIXME: variables/keys should not be named like this*/
$response['new_mobile'] = $new_mobile_lang;

$response['upload_max_filesize'] = getMaxFileUploadSize();

$response['mobile_config']['logout_enabled']  = $response['mobile_config']['phone_number_enabled'] == '1' ? '0' : '1';
$useragent = (!empty($_SERVER["HTTP_USER_AGENT"])) ? $_SERVER["HTTP_USER_AGENT"] : '';
if(phpversion()>='4.0.4pl1'&&(strstr($useragent,'compatible')||strstr($useragent,'Gecko'))){
    if(extension_loaded('zlib')&&GZIP_ENABLED==1){
        $response['ob_gzhandler']=1;
    }else{
        $response['ob_gzhandler']=2;
    }
}else{
    $response['ob_gzhandler']=3;
}

if(!empty($gatrackerid)){
    $response['mobile_config']['gatrackerid'] = $gatrackerid;
}
$useragent = (!empty($_SERVER["HTTP_USER_AGENT"])) ? $_SERVER["HTTP_USER_AGENT"] : '';
if(phpversion()>='4.0.4pl1'&&(strstr($useragent,'compatible')||strstr($useragent,'Gecko'))){
    if(extension_loaded('zlib')&&GZIP_ENABLED==1 && !in_array('ob_gzhandler', ob_list_handlers())){
        ob_start('ob_gzhandler');
    }else{
        ob_start();
    }
}else{
    ob_start();
}
if (!empty($_GET['callback'])) {
    echo $_GET['callback'].'('.json_encode($response).')';
} else {
    $response['response_hash'] = md5(json_encode($response));
    if(!empty($_REQUEST['response_hash'])){
        if($_REQUEST['response_hash'] == $response['response_hash']){
            echo json_encode(array('no_change'=>'1')); exit;
        }
    }
    echo json_encode($response);
}
