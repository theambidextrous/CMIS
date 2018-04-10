<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

function themeslist() {
	$layouts = array();

	if ($handle = opendir(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'layouts')) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != ".." && is_dir(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'layouts'.DIRECTORY_SEPARATOR.$file) && file_exists(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'layouts'.DIRECTORY_SEPARATOR.$file.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'cometchat.css')) {
				$layouts[] = $file;
			}
		}
		closedir($handle);
	}


	return $layouts;
}

function configeditor ($config) {
    global $dbh;
    global $client;
    global $writable;
    global $dbms;

    $insertvalues = '';
    $key_type;

    if (!empty($client)) {
        $bad_keys = array('DEV_MODE','ERROR_LOGGING','CROSS_DOMAIN','enablecustomphp');
        $config = array_diff_key($config,array_flip($bad_keys));
    }
    foreach ($config as $name => $value) {
        if($name == strtoupper($name)){
            $key_type = 0;
        }else if(!is_array($value)){
            $key_type = 1;
        }else{
            $key_type = 2;
            $value = serialize($value);
        }

        if($dbms == 'mssql' || $dbms == 'pgsql'){
            $query = sql_query('admin_configeditor',array('name'=>$name, 'value'=>$value, 'key_type'=>$key_type));
        }else{
            $insertvalues .= sql_getQuery('admin_configeditor_insertvalues',array('name'=>$name, 'value'=>$value, 'key_type'=>$key_type));
        }
    }

    if(!empty($config)){
        $eventmessage = json_encode($config);
        log_error($eventmessage, 'event');
    }

    if(!empty($insertvalues)){
        $insertvalues = rtrim($insertvalues,',');
        $query = sql_query('admin_configeditor',array('insertvalues'=>$insertvalues));
    }
    removeCachedSettings($client.'settings');
    if (is_dir(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'writable'.DIRECTORY_SEPARATOR.$writable)){
        clearcache(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'writable'.DIRECTORY_SEPARATOR.$writable);
    }
    if(function_exists('purgecache')) {
        purgecache($client);
    }
}

function cc_mail( $to, $subject, $message, $headers, $attachments = array() ) {
    if ( ! is_array( $attachments ) ) {
        $attachments = explode( "\n", str_replace( "\r\n", "\n", $attachments ) );
    }
    global $phpmailer;

    if ( ! ( $phpmailer instanceof PHPMailer ) ) {
        if(file_exists(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."functions".DIRECTORY_SEPARATOR."mail".DIRECTORY_SEPARATOR."class-phpmailer.php")){
            include_once (dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."functions".DIRECTORY_SEPARATOR."mail".DIRECTORY_SEPARATOR."class-phpmailer.php");
            include_once (dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."functions".DIRECTORY_SEPARATOR."mail".DIRECTORY_SEPARATOR."class-smtp.php");
        }
        $phpmailer = new PHPMailer( true );
    }
    $cc = $bcc = $reply_to = array();
    if ( empty( $headers ) ) {
        $headers = array();
    } else {
        if ( !is_array( $headers ) ) {
            $tempheaders = explode( "\n", str_replace( "\r\n", "\n", $headers ) );
        } else {
            $tempheaders = $headers;
        }
        $headers = array();
        if ( !empty( $tempheaders ) ) {
            foreach ( (array) $tempheaders as $header ) {
                if ( strpos($header, ':') === false ) {
                    if ( false !== stripos( $header, 'boundary=' ) ) {
                        $parts = preg_split('/boundary=/i', trim( $header ) );
                        $boundary = trim( str_replace( array( "'", '"' ), '', $parts[1] ) );
                    }
                    continue;
                }
                list( $name, $content ) = explode( ':', trim( $header ), 2 );
                $name    = trim( $name    );
                $content = trim( $content );

                switch ( strtolower( $name ) ) {
                    case 'from':
                    $bracket_pos = strpos( $content, '<' );
                    if ( $bracket_pos !== false ) {
                        if ( $bracket_pos > 0 ) {
                            $from_name = substr( $content, 0, $bracket_pos - 1 );
                            $from_name = str_replace( '"', '', $from_name );
                            $from_name = trim( $from_name );
                        }

                        $from_email = substr( $content, $bracket_pos + 1 );
                        $from_email = str_replace( '>', '', $from_email );
                        $from_email = trim( $from_email );
                    } elseif ( '' !== trim( $content ) ) {
                        $from_email = trim( $content );
                    }
                    break;
                    case 'content-type':
                    if ( strpos( $content, ';' ) !== false ) {
                        list( $type, $charset_content ) = explode( ';', $content );
                        $content_type = trim( $type );
                        if ( false !== stripos( $charset_content, 'charset=' ) ) {
                            $charset = trim( str_replace( array( 'charset=', '"' ), '', $charset_content ) );
                        } elseif ( false !== stripos( $charset_content, 'boundary=' ) ) {
                            $boundary = trim( str_replace( array( 'BOUNDARY=', 'boundary=', '"' ), '', $charset_content ) );
                            $charset = '';
                        }

                    } elseif ( '' !== trim( $content ) ) {
                        $content_type = trim( $content );
                    }
                    break;
                    case 'cc':
                    $cc = array_merge( (array) $cc, explode( ',', $content ) );
                    break;
                    case 'bcc':
                    $bcc = array_merge( (array) $bcc, explode( ',', $content ) );
                    break;
                    case 'reply-to':
                    $reply_to = array_merge( (array) $reply_to, explode( ',', $content ) );
                    break;
                    default:
                    $headers[trim( $name )] = trim( $content );
                    break;
                }
            }
        }
    }

    $phpmailer->ClearAllRecipients();
    $phpmailer->ClearAttachments();
    $phpmailer->ClearCustomHeaders();
    $phpmailer->ClearReplyTos();

    if ( !isset( $from_name ) )
        $from_name = 'bounce ';

    $phpmailer->setFrom( $from_email, $from_name, false );
    if ( !is_array( $to ) )
        $to = explode( ',', $to );
    $phpmailer->Subject = $subject;
    $phpmailer->Body    = $message;
    $address_headers = compact( 'to', 'cc', 'bcc', 'reply_to' );
    foreach ( $address_headers as $address_header => $addresses ) {
        if ( empty( $addresses ) ) {
            continue;
        }

        foreach ( (array) $addresses as $address ) {
            try {
                $recipient_name = '';
                if ( preg_match( '/(.*)<(.+)>/', $address, $matches ) ) {
                    if ( count( $matches ) == 3 ) {
                        $recipient_name = $matches[1];
                        $address        = $matches[2];
                    }
                }

                switch ( $address_header ) {
                    case 'to':
                    $phpmailer->addAddress( $address, $recipient_name );
                    break;
                    case 'cc':
                    $phpmailer->addCc( $address, $recipient_name );
                    break;
                    case 'bcc':
                    $phpmailer->addBcc( $address, $recipient_name );
                    break;
                    case 'reply_to':
                    $phpmailer->addReplyTo( $address, $recipient_name );
                    break;
                }
            } catch ( phpmailerException $e ) {
                continue;
            }
        }
    }

    $phpmailer->IsMail();

    if ( !isset( $content_type ) )
        $content_type = 'text/plain';

    $phpmailer->ContentType = $content_type;

    if ( 'text/html' == $content_type )
        $phpmailer->IsHTML( true );

    if ( !empty( $headers ) ) {
        foreach ( (array) $headers as $name => $content ) {
            $phpmailer->AddCustomHeader( sprintf( '%1$s: %2$s', $name, $content ) );
        }

        if ( false !== stripos( $content_type, 'multipart' ) && ! empty($boundary) )
            $phpmailer->AddCustomHeader( sprintf( "Content-Type: %s;\n\t boundary=\"%s\"", $content_type, $boundary ) );
    }

    if ( !empty( $attachments ) ) {
        foreach ( $attachments as $attachment ) {
            try {
                $phpmailer->AddAttachment($attachment);
            } catch ( phpmailerException $e ) {
                continue;
            }
        }
    }

    try {
        return $phpmailer->Send();
    } catch ( phpmailerException $e ) {
        $mail_error_data = compact( 'to', 'subject', 'message', 'headers', 'attachments' );
        $mail_error_data['phpmailer_exception_code'] = $e->getCode();
        return false;
    }
}
function languageeditor($lang){
	global $dbh;
	global $client;
	global $writable;
	if(empty($lang['lang_key']) || empty($lang['name']) || empty($lang['code']) || empty($lang['type'])){
		return 0;
	}
	$query = sql_query('admin_languageeditor',array('lang_key'=>$lang['lang_key'], 'lang_text'=>$lang['lang_text'], 'code'=>$lang['code'], 'type'=>$lang['type'], 'name'=>$lang['name']));
	removeCachedSettings($client.'cometchat_language');
	if (is_dir(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'writable'.DIRECTORY_SEPARATOR.$writable)){
		clearcache(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'writable'.DIRECTORY_SEPARATOR.$writable);
	}
}

function coloreditor($data,$color_name){
	global $dbh;
	global $client;
	global $writable;
        global $dbms;

	$insertvalues = '';
	foreach ($data as $name => $value) {
        if($dbms == 'mssql'){
           $query = sql_query('admin_coloreditor',array('name'=>$name, 'value'=>$value, 'color_name'=>$color_name));
        }else{
        $insertvalues .= sql_getQuery('admin_coloreditor_insertvalues',array('name'=>$name, 'value'=>$value, 'color_name'=>$color_name));
        }
    }

	if(!empty($insertvalues)){
                $insertvalues = rtrim($insertvalues,',');
		$query = sql_query('admin_coloreditor',array('insertvalues'=>$insertvalues));
	}
	removeCachedSettings($client.'cometchat_color');
	if (is_dir(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'writable'.DIRECTORY_SEPARATOR.$writable)){
		clearcache(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'writable'.DIRECTORY_SEPARATOR.$writable);
	}
}

function createslug($title,$rand = false) {
	$slug = preg_replace("/[^a-zA-Z0-9]/", "", $title);
	if ($rand) { $slug .= rand(0,9999); }
	return strtolower($slug);
}

function extension($filename) {
	return pathinfo($filename, PATHINFO_EXTENSION);
}

function deletedirectory($dir) {
    if (!file_exists($dir)) return true;
    if (!is_dir($dir) || is_link($dir)) return unlink($dir);
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') continue;
            if (!deleteDirectory($dir . "/" . $item)) {
                chmod($dir . "/" . $item, 0777);
                if (!deleteDirectory($dir . "/" . $item)) return false;
            };
        }
    return rmdir($dir);
}

function pushMobileAnnouncement($zero,$sent,$message,$isAnnouncement = '0',$insertedid){
	global $userid;
	global $lang;

		$announcementpushchannel = '';

		if(file_exists(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR."announcements".DIRECTORY_SEPARATOR."config.php")){
			include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR."announcements".DIRECTORY_SEPARATOR."config.php");
			include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR."announcements".DIRECTORY_SEPARATOR."lang.php");
		}

		if(!empty($isAnnouncement)){
			$rawMessage = array("m" => $announcements_language['announces'].": ".$message, "sent" => $sent, "id" => $insertedid);
		}
        pushToMobileDevice($announcementpushchannel, $rawMessage, 0, 1, 0);
}

function datify($ts) {
	if(!ctype_digit($ts)) {
		$ts = strtotime($ts);
	}
	$diff = time() - $ts;
	$date = date('l, F j, Y',$ts).' at '.date('g:ia',$ts);
	if($diff == 0) {
		return array ('now',$date);
	} elseif($diff > 0) {
		$day_diff = floor($diff / 86400);
		if($day_diff == 0) {
			if($diff < 60) return array('just now',$date);
			if($diff < 120) return array ('1 minute ago',$date);
			if($diff < 3600) return array (floor($diff / 60) . ' minutes ago',$date);
			if($diff < 7200) return array ('1 hour ago',$date);
			if($diff < 86400) return array (floor($diff / 3600) . ' hours ago',$date);
		}
		if($day_diff == 1) { return array ('Yesterday at '.date('g:ia',$ts),$date); }

		if (date('Y') == date('Y',$ts)) {
			return array (date('F jS',$ts).' at '.date('g:ia',$ts),$date);
		} else {
			return array (date('F jS, Y',$ts).' at '.date('g:ia',$ts),$date);
		}
	} else {
	return array (date('F jS, Y',$ts).' at '.date('g:ia',$ts),$date);
	}
}
function deleteFolderContent($path,$exclude = array(),$emptyfolder = 0) {
    $exclude = array_merge($exclude,array('.', '..'));
    if (is_dir($path)){
        $files = @array_diff(@scandir($path), $exclude);
        foreach ($files as $file){
            deleteFolderContent(realpath($path) . DIRECTORY_SEPARATOR . $file,array(),1);
        }
        if($emptyfolder) {
            return @rmdir($path);
        }
    } elseif (is_file($path)){
        return @unlink($path);
    } else {
        return false;
    }
}

function clearcachejscss($directory, $recursive = true, $listDirs = false, $listFiles = true, $exclude = '') {
    global $writable;
    $path = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'writable'.DIRECTORY_SEPARATOR.$writable;
    deleteFolderContent($path,array("index.html"),0);
}

function checkCometChatResponse($licenseKey){
    /*
    * Calls CometChat Server to retrieve user plan information and latest version
    * Params: None
    * Returns/Results success 1 if licence is valid ,plan_has_changed is true if plan has changed
    */
    global $accessKey;
    $CometChatResponse =  array();
    if (!empty($licenseKey))
    {
        $queryString = (empty($_COOKIE['software-dev'])) ? '' : '&dev=1';
        $url = "https://secure.cometchat.com/api-software/license?accessKey=".$accessKey."&licenseKey=".$licenseKey.$queryString;
        $response = cc_curl_call($url,array());
        $response = json_decode($response,true);
        if(!empty($response) && $response['success']==1){
            $response['licensekey'] = $licenseKey;
            $result = updateAPIResponse($response);
            if(isset($result['has_plan_changed'])){
                $CometChatResponse['has_plan_changed'] = 1;
            }
            $CometChatResponse['success'] = 1;
        }else{
            $CometChatResponse['success'] = 0;
        }
    }
    return $CometChatResponse;
}

function updateAPIResponse($latest_response){
    /*
    * What? Updates latest response received from CometChat server.
    * Why?
    * Params: None
    * Returns/Results if plan has changed or new version is available to upgrade.
    */
    global $ts,$plugins,$crplugins,$extensions,$trayicon,$extensions,$usebots,$currentversion,$planId,$client,$api_response;
    $response =  array();
    $active_plugins = !empty($latest_response['plan']['plugins']) ? $latest_response['plan']['plugins'] : array();
    $active_extensions = !empty($latest_response['plan']['extensions']) ? $latest_response['plan']['extensions'] : array();


    if(empty($latest_response['plan']['platforms'])){
        $latest_response['plan']['platforms'] = array();
    }

    if(empty($plugins)){
        $plugins = array();
    }
    $newSettings = array();
    $newSettings['authentication'] = !empty($latest_response['plan']['authentication']) ? $latest_response['plan']['authentication'] : array();
    /*
    * update latest version
    */
    $newSettings['api_response'] = $latest_response;
    if(cc_version_compare($latest_response['version'],$currentversion)==1){
        $newSettings['LATEST_VERSION'] = $latest_response['version'];
        $response['is_version_available'] = 1;
    }
    /*
    * check plan and change settings if plan has changes
    */
    if($latest_response['plan']['id'] != $planId){
        $response['has_plan_changed'] = 1;
        $newSettings['planId'] = $latest_response['plan']['id'];
        $newSettings['planName'] = $latest_response['plan']['name'];
    }

    /*
    * filtering active plugins
    */
    $defaultActivePlugins = array('smilies','clearconversation','avchat' ,'audiochat','broadcast','voicenote','filetransfer','stickers','block','whiteboard ','writeboard');
    $defaultActivePlugins = setConfigValue('plugins',$defaultActivePlugins);
    $plugins = array_intersect($active_plugins, $defaultActivePlugins);


    /*
    * Update SDK Setting
    */

    $newSettings['sdk'] = in_array('mobilesdk', $latest_response['plan']['platforms']) ? 1 : 0;

    /*
    * Update SDK Setting
    */

    $newSettings['sdk'] = in_array('mobilesdk', $latest_response['plan']['platforms']) ? 1 : 0;

    /*
    * configure cometservice
    */
    if(!empty($latest_response['cometservice']) && !empty($latest_response['active'])){
        $newSettings['USE_COMET'] = $latest_response['active'];
        $newSettings['KEY_A'] = $latest_response['cometservice']['pub_key'];
        $newSettings['KEY_B'] = $latest_response['cometservice']['sub_key'];
    }

    /*
    * filtering active modules
    */
    $trayicon['realtimetranslate'] = array('realtimetranslate','Translate Conversations','modules/realtimetranslate/index.php','_popup','280','310','','1','1');
    $trayicon = setConfigValue('trayicon',$trayicon);
    foreach ($trayicon as $key => $value) {
        if(!in_array($key, $latest_response['plan']['modules'])){
            unset($trayicon[$key]);
        }
    }
    $newSettings['trayicon'] = $trayicon;

    /*
    * filter extensions
    */
    $extensions = array_intersect($active_extensions, $extensions);
    $isActiveBots = array_search('bots', $active_extensions);
    if($isActiveBots <= 0 && $usebots == 1 ){
        $newSettings['usebots'] = 1;
    }else{
        $newSettings['usebots'] = 0;
    }
    $newSettings['extensions'] = $extensions;

    if(!empty($latest_response['integration']['file'])){
        $newSettings['cms'] = ($latest_response['integration']['file'] == 'standalone') ? 'custom' : $latest_response['integration']['file'];
    }

    if(!empty($client)){
        $newSettings['licensekey'] = $latest_response['licensekey'];
    }
    if(!empty($newSettings)){
        $api_response = $newSettings['api_response'];

        configeditor($newSettings);
    }
    return $response;
}

function updateNewVersion($version){
    global $ts;
    if($version == ''){
        $newVersion = array('LATEST_VERSION' => '','api_response' => '');
    }else{
        $newVersion = array('LATEST_VERSION' => $version,'api_response' => '');
    }
    configeditor($newVersion);
}

function generateReport(){
    global $cronTimestamp, $archiveGuest;

    $query  = sql_query('get_users',array());
    $result = sql_fetch_assoc($query);
    $totalUsers = !empty( $result['totalusers'])? $result['totalusers']:0;

    $query  = sql_query('get_guest_users',array());
    $result = sql_fetch_assoc($query);
    $totalGuestUsers = !empty( $result['totalguestusers'])? $result['totalguestusers']:0;
    $totalGuestUsers = $totalGuestUsers + $archiveGuest;

    $query = sql_query('admin_getActiveUsersCount',array('sent'=>time()-60*60*24, 'firstguestid'=>$GLOBALS['firstguestID']));
    $getActiveUsersCount = sql_fetch_assoc($query);
    $activeUsersin24hrs = !empty($getActiveUsersCount['activeusers'])?$getActiveUsersCount['activeusers']:0;

    $query = sql_query('admin_getlast24hoursActiveGuestsCount',array('sent'=>time()-60*60*24, 'firstguestid'=>$GLOBALS['firstguestID']));
    $getActiveUsersCount = sql_fetch_assoc($query);
    $activeGuestin24hrs = !empty($getActiveUsersCount['activeguests'])?$getActiveUsersCount['activeguests']:0;

    $query = sql_query('admin_getlast24hrsPrivateMessageCount',array('sent' => $cronTimestamp));
    $messageCount = sql_fetch_assoc($query);
    $privateMessageCount = !empty($messageCount['messagecount'])?$messageCount['messagecount']:0;

    $query = sql_query('admin_getlast24hoursGroupMessageCount',array('sent' => $cronTimestamp));
    $messageCount = sql_fetch_assoc($query);
    $groupMessageCount = !empty($messageCount['messagecount'])?$messageCount['messagecount']:0;

    $query = sql_query('groupCreatedin24hrs',array('createdon'=>time()-60*60*24));
    $result = sql_fetch_assoc($query);
    $groupCount = !empty($result['groupCount'])?$result['groupCount']:0;

    $report = array(
        'timestamp_start' => time(),
        'total_no_of_users' => $totalUsers,
        'total_no_of_guest' => $totalGuestUsers,
        'no_of_active_users_last_24_hrs' => $activeUsersin24hrs,
        'no_of_active_guest_last_24_hrs' => $activeGuestin24hrs,
        'no_of_messages_exchange_one_on_one_last_24_hrs' => $privateMessageCount,
        'no_of_messages_exchange_groupchat_last_24_hrs'  => $groupMessageCount,
        'no_of_group_created_last_24_hrs' => $groupCount,
    );
    $query = sql_query('insert_report',$report);
    configeditor(array('cronTimestamp' => time()));
    if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }
}

?>
