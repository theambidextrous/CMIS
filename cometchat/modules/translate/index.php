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
if(!checkMembershipAccess('translate','modules')){exit();}
$extrajs = "";
if ($sleekScroller == 1) {
	$extrajs =  getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'scroll', 'ext' => 'js'));
}

$cc_layout = 'docked';
if(!empty($_REQUEST['cc_layout'])){
  $cc_layout = $_REQUEST['cc_layout'];
}
$jQuerytag = getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'jquery', 'ext' => 'js'));
$css = getDynamicScriptAndLinkTags(array('type' => "module",'name' => 'translate','ext' => 'css'));
echo <<<EOD
<!DOCTYPE html>
<html>
<head>
<title>{$translate_language[100]}</title>
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
	var controlparameters = {"type":"modules", "name":"translatepage", "method":"addLanguageCode", "params":{}};
	controlparameters = JSON.stringify(controlparameters);
	parent.postMessage('CC^CONTROL_'+controlparameters,'*');

	$("li").click(function() {
		var lang = $(this).attr('id');
		var controlparameters = {"type":"modules", "name":"translatepage", "method":"changeLanguage", "params":{"lang":lang}};
		controlparameters = JSON.stringify(controlparameters);
		parent.postMessage('CC^CONTROL_'+controlparameters,'*');

		$('.languages').hide();
		$('.translating').show();
		setTimeout(function() {
		try {
			var controlparameters = {"type":"modules", "name":"translate", "method":"closeModule", "params":{}};
			controlparameters = JSON.stringify(controlparameters);
			parent.postMessage('CC^CONTROL_'+controlparameters,'*');
		} catch (e) { }

		$('.languages').show();
		$('.translating').hide();

		},5000);
	});
});



</script>

</head>
<body>
<div style="width:100%;margin:0 auto;margin-top: 0px;height: 100%;overflow-y: auto;">

<div class="cometchat_wrapper">

<ul class="languages">
<li id="af">Afrikaans</li>
<li id="sq">Albanian</li>
<li id="ar">Arabic</li>
<li id="be">Belarusian</li>
<li id="bg">Bulgarian</li>
<li id="ca">Catalan</li>
<li id="zh-CN">Chinese (Simpl)</li>
<li id="zh-TW">Chinese (Trad)</li>
<li id="hr">Croatian</li>
<li id="cs">Czech</li>
<li id="da">Danish</li>
<li id="nl">Dutch</li>
<li id="en">English</li>
<li id="et">Estonian</li>
<li id="tl">Filipino</li>
<li id="fi">Finnish</li>
<li id="fr">French</li>
<li id="gl">Galician</li>
<li id="de">German</li>
<li id="el">Greek</li>
<li id="ht">Haitian Creole</li>
<li id="iw">Hebrew</li>
<li id="hi">Hindi</li>
<li id="hu">Hungarian</li>
<li id="is">Icelandic</li>
<li id="id">Indonesian</li>
<li id="ga">Irish</li>
<li id="it">Italian</li>
<li id="ja">Japanese</li>
<li id="ko">Korean</li>
<li id="lv">Latvian</li>
<li id="lt">Lithuanian</li>
<li id="mk">Macedonian</li>
<li id="ms">Malay</li>
<li id="mt">Maltese</li>
<li id="no">Norwegian</li>
<li id="fa">Persian</li>
<li id="pl">Polish</li>
<li id="pt">Portuguese</li>
<li id="ro">Romanian</li>
<li id="ru">Russian</li>
<li id="sr">Serbian</li>
<li id="sk">Slovak</li>
<li id="sl">Slovenian</li>
<li id="es">Spanish</li>
<li id="sw">Swahili</li>
<li id="sv">Swedish</li>
<li id="th">Thai</li>
<li id="tr">Turkish</li>
<li id="uk">Ukrainian</li>
<li id="vi">Vietnamese</li>
<li id="cy">Welsh</li>
<li id="yi">Yiddish</li>
</ul>

<div class="translating">{$translate_language[0]}</div>

<div style="clear:both"></div>
</div>
</div>
</body>
</html>
EOD;
?>
