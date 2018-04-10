<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."cometchat_init.php");

if(!empty($_REQUEST['callbackfn']) && $_REQUEST['callbackfn'] == 'mobileapp') {
	sql_query('mobileapp_logout',array('userid'=>$userid));
} else if(empty($_REQUEST['callbackfn']) || (!empty($_REQUEST['callbackfn']) && in_array($_REQUEST['callbackfn'], array('desktop', 'web')))){
	unset($_SESSION['cometchat']);
    unset($_SESSION['CCAUTH_SESSION']);
	setcookie($cookiePrefix.'guest',"",time() - 3600,'/');
	setcookie($cookiePrefix.'guest_login',"",time() - 3600,'/');
	setcookie($cookiePrefix."state", "", time() - 3600,'/');
	if($_REQUEST['callbackfn'] == 'desktop'){
		$cookies = explode(';', $_SERVER['HTTP_COOKIE']);
	    foreach($cookies as $cookie) {
	        $parts = explode('=', $cookie);
	        $name = trim($parts[0]);
	        setcookie($name, '', time()-3600);
	        setcookie($name, '', time()-3600, '/');
	    }
	}
	session_destroy($client.md5($_REQUEST['basedata']));
	echo json_encode(1);
}else{
    echo "Nothing to look here";
}

exit;
