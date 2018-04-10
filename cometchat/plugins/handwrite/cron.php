<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

if (!defined('CC_CRON')) { echo "NO DICE"; exit; }

$days = 1;
$seconds = ($days*24*60*60);
$dir = dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'writable'.DIRECTORY_SEPARATOR.'handwrite'.DIRECTORY_SEPARATOR.'uploads';
$files = scandir($dir);

foreach ($files as $num => $fname){
	if (file_exists("$dir/$fname") && ((time() - filemtime("$dir/$fname")) > $seconds)) {
		if ($fname != 'index.html' && $fname != '.htaccess' && !is_dir($fname)) {
			@unlink("$dir/$fname");
		}
	}
}
