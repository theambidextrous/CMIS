<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."modules.php");

if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php")) {
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php");
}
if(!checkMembershipAccess('realtimetranslate','modules')){exit();}
include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'translate.php');

if (!checkcURL(1)) {
	echo "<div style='background:white;height: 100%;'>Please ask your webhost to install libcurl for PHP and configure it for HTTPs as well</div>"; exit;
}

if (empty($bingClientID) && empty($googleKey) && (!checkLicenseVersion())) {
	echo "<div style='background:white;'>".$realtimetranslate_language['real_time_translate']."</div>"; exit;
}

$translatingtext = '';

if (!empty($_COOKIE[$cookiePrefix.'rttlang'])) {
	$translatingtext = '<div class="current">'.$realtimetranslate_language[1].strtoupper($_COOKIE[$cookiePrefix.'rttlang']).' | <a href="javascript:void(0);" onclick="javascript:stoptranslating()">'.$realtimetranslate_language[2].'</a></div>';
}

$languagescode = '';
$languages = translate_languages();

$crossdomain = 0;
if(defined('CROSS_DOMAIN')) {
	$crossdomain = CROSS_DOMAIN;
}

foreach ($languages as $code => $name) {
	if ($useGoogle == 0) {
        if($code == 'zh-CHS') {$name = 'Chinese (Simpl)';}elseif($code == 'zh-CHT') {$name = 'Chinese (Trad)';}
    }
	$languagescode .= '<li id="'.$code.'">'.$name.'</li>';
}

$extrajs = "";
if ($sleekScroller == 1) {
	$extrajs =  getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'scroll', 'ext' => 'js'));
}

$cc_layout = 'docked';
if(!empty($_REQUEST['cc_layout'])){
  $cc_layout = $_REQUEST['cc_layout'];
}
$jQuerytag = getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'jquery', 'ext' => 'js'));
$css = getDynamicScriptAndLinkTags(array('type' => "module",'name' => 'realtimetranslate','ext' => 'css'));
echo <<<EOD
<!DOCTYPE html>
<html>
<head>
<title>{$realtimetranslate_language[100]}</title>
<meta name="viewport" content="user-scalable=1,width=device-width, initial-scale=1.0" />
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="-1">
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
$css
$jQuerytag
<script>$ = jQuery = jqcc;</script>
{$extrajs}
<script>
$(function() {

	if (jQuery().slimScroll) {
		var updated_height = '290';
		if("{$cc_layout}"=="embedded"){
			if( $('.cometchat_wrapper').length>0){
				updated_height = $('.cometchat_wrapper').height()-25;
			}
		}
		$('.cometchat_wrapper').slimScroll({height: $('.cometchat_wrapper').height()+'px',allowPageScroll: false});
		$(".languages").css("height", $('.cometchat_wrapper').height()+'px');
		$(".cometchat_wrapper").css("height",updated_height+'px');
	}

	$("li").click(function() {
		$('.current').hide();
		var info = $(this).attr('id');

		document.cookie = '{$cookiePrefix}rttlang='+info+';path=/';
		var crossdomain = {$crossdomain};
		if(crossdomain == 1) {
			var controlparameters = {"type":"modules", "name":"realtimetranslate", "method":"setCookie", "params":{"name":"{$cookiePrefix}rttlang","lang":info}};
			controlparameters = JSON.stringify(controlparameters);
			parent.postMessage('CC^CONTROL_'+controlparameters,'*');
		}

		$('.languages').hide();
		$('.translating').show();
		setTimeout(function() {
		try {
			if (parent.jqcc.cometchat.ping() == 1) {
				parent.jqcc.cometchat.closeModule('realtimetranslate');
			}
		} catch (e) { }

		$('.languages').show();
		$('.translating').hide();

		window.location.reload();

		},3000);
	});
});

function stoptranslating() {
	document.cookie = '{$cookiePrefix}rttlang=;path=/';
	var crossdomain = {$crossdomain};
	if(crossdomain == 1) {
		var controlparameters = {"type":"modules", "name":"realtimetranslate", "method":"setCookie", "params":{"name":"{$cookiePrefix}rttlang","lang":''}};
		controlparameters = JSON.stringify(controlparameters);
		parent.postMessage('CC^CONTROL_'+controlparameters,'*');
	}
	$('.current').hide();
}

</script>

</head>
<body style="margin: 0px;">
<div style="width:100%;margin:0 auto;margin-top: 0px;height: 100%;overflow-y: auto;">

<div class="cometchat_wrapper">
{$translatingtext}
<div style="clear:both"></div>
<ul class="languages">
{$languagescode}
</ul>

<div class="translating">{$realtimetranslate_language[0]}</div>

<div style="clear:both"></div>
</div>
</div>
</body>
</html>
EOD;
?>
