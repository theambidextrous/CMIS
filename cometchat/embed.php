<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

$mtime = microtime();
$mtime = explode(" ",$mtime);
$mtime = $mtime[1] + $mtime[0];
$starttime =  $mtime;

if (!empty($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
	header("HTTP/1.1 304 Not Modified");
	exit;
}
ob_start('ob_gzhandler');
include_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'config.php');

include dirname(__FILE__).DIRECTORY_SEPARATOR."modules/chatrooms/lang".DIRECTORY_SEPARATOR."en.php";

if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."modules/chatrooms/lang".DIRECTORY_SEPARATOR.$lang.".php")) {
	include dirname(__FILE__).DIRECTORY_SEPARATOR."modules/chatrooms/lang".DIRECTORY_SEPARATOR.$lang.".php";
}

include_once (dirname(__FILE__).DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."jquery.js");

if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."extra_".$integration.".js")  && empty($_GET['callbackfn'])) {
	include_once (dirname(__FILE__).DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR."extra_".$integration.".js");
}

$baseurl = BASE_URL;
$id = '';

if (!empty($_GET['id'])) {
	$id = 'id='.intval($_GET['id']).'&';
}

if (!empty($_GET['width'])) {
	$width = $_GET['width'];
}

if (!empty($_GET['height'])) {
	$height = $_GET['height'];
}

$embedcode = <<<EOD

jqcc(document).ready(function() {
	if (typeof cc_base != 'undefined'){
		var cc_data = (typeof cc_base === 'object')?base64_encode(JSON.stringify(cc_base)):base64_encode(cc_base);
		jqcc('#chatroom').html('<ifr'+'ame src="{$baseurl}modules/chatrooms/?{$id}basedata='+cc_data+'" width="{$width}" height="{$height}" frameborder="0" style="border:1px solid #ccc" id = "cometchat_chatrooms_iframe"></ifr'+'ame>');
	} else {
		jqcc('#chatroom').html('{$chatrooms_language[0]}');
	}
});

EOD;

$mtime = microtime();
$mtime = explode(" ",$mtime);
$mtime = $mtime[1] + $mtime[0];
$endtime = $mtime;
$totaltime = ($endtime - $starttime);

$js = ob_get_clean();
$js .= $embedcode."\n\n/* Execution time: ".$totaltime." seconds */";


header('Content-type: text/javascript;charset=utf-8');
header('Content-Length: '.strlen($js));
echo $js;



function cleanInput($input) {
	$input = trim($input);
	$input = preg_replace("/[^+A-Za-z0-9\_]/", "", $input);
	return strtolower($input);
}
