<?php

if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'cometchat_init.php')) {
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'cometchat_init.php');
}

if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."extensions".DIRECTORY_SEPARATOR.'mobileapp'.DIRECTORY_SEPARATOR.'config.php')) {
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."extensions".DIRECTORY_SEPARATOR.'mobileapp'.DIRECTORY_SEPARATOR.'config.php');
}

$url = '';
if(isset($_REQUEST['platform']) && !empty($_REQUEST['platform'])) {
	if($_REQUEST['platform'] == "Android"){
		$url = 'Location: '.$mobileappPlaystore;
	} else if($_REQUEST['platform'] == "iPhone") {
		$url = 'Location: '.$mobileappAppstore;
	}
	header($url);
}

?>
