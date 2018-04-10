<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/
define('CCADMIN',true);
if(!empty($_GET['backup'])){
	$oldfile = file_get_contents(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."integration.php");
	file_put_contents(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'writable'.DIRECTORY_SEPARATOR.'backup'.DIRECTORY_SEPARATOR.'integration.bak',$oldfile);
}else{
	$oldfile = file_get_contents(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'writable'.DIRECTORY_SEPARATOR.'backup'.DIRECTORY_SEPARATOR.'integration.bak');
	file_put_contents(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."integration.php",$oldfile);
}
