<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins.php");
if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php")) {
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php");
}

if( checkplan('plugins','transliterate') == 0){ exit;}

$toId = $_GET['id'];

$caller="";
if(!empty($_REQUEST['caller'])){
	$caller = $_REQUEST['caller'];
}

if (!empty($_COOKIE[$cookiePrefix."language"])) {
	$_GET['action'] = 'cached';
	$_GET['lang'] = $_COOKIE[$cookiePrefix."language"];
}

$chatroommode = 0;

if (!empty($_GET['chatroommode'])) {
	$chatroommode = 1;
}

$embed = '';
$embedcss = '';
$before = 'window.opener';

if (!empty($_GET['embed']) && $_GET['embed'] == 'web') {
	$embed = 'web';
	$embedcss = 'embed';
	$before = 'parent';

	if ($chatroommode == 1) {
		$before = "$('#cometchat_trayicon_chatrooms_iframe',parent.document)[0].contentWindow";
	}
}

if (!empty($_GET['embed']) && $_GET['embed'] == 'desktop') {
	$embed = 'desktop';
	$embedcss = 'embed';
	$before = 'parentSandboxBridge';
}

$extrajs = "";
$scrollcss = "overflow-y:scroll;overflow-x:hidden;position:absolute;top:26px;";
if ($sleekScroller == 1) {
	$extrajs = getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'scroll', 'ext' => 'js'));
	$scrollcss = "";
}

$cc_layout = '';
if(!empty($_REQUEST['cc_layout'])){
	$cc_layout = $_REQUEST['cc_layout'];
}
$transliteratejstag = getDynamicScriptAndLinkTags(array('type' => "plugin",'name' => 'transliterate','ext' => 'js'));
$transliteratecsstag = getDynamicScriptAndLinkTags(array('type' => "plugin",'name' => 'transliterate','layout' => $cc_layout ,'ext' => 'css'));
if (empty($_GET['action'])) {
	$toId = $_GET['id'];
	$baseData = rawurlencode($_REQUEST['basedata']);
	echo <<<EOD
	<!DOCTYPE html>
	<html>
	<head>
		<title>{$transliterate_language[0]}</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		{$transliteratecsstag}
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
		{$transliteratejstag}
		<script> $ = jQuery = jqcc;	</script>
		{$extrajs}
		<style>
			#languages {

				{$scrollcss};
			}
		</style>
		<script type="text/javascript">

		google.load("elements", "1", {
			packages: "transliteration"
		});

		function formatlang(str) {
			return str[0].toUpperCase()+str.substr(1).toLowerCase();
		}

		function onLoad() {
			var languages = google.elements.transliteration.getDestinationLanguages('en');
			html = '';
			for (x in languages) {
				if (languages[x] != 'ne') {
					html += '<li id="'+languages[x]+'">'+formatlang(x)+'</li>';
				}
			}
			$("#languages").html(html);

			$("li").click(function() {
				var info = $(this).attr('id');
				setCookie('{$cookiePrefix}language',info);
				location.href = 'index.php?cc_layout={$cc_layout}&caller={$caller}&action=transliterate&basedata={$baseData}&embed={$embed}&chatroommode={$chatroommode}&id={$toId}&lang='+info;
			});

			if (jQuery().slimScroll) {
				var langheight = ($(window).height() - $('.container_title').outerHeight())+'px';
				$('#languages').height(langheight);
				$("#languages").slimScroll({ height: langheight});
			}
		}

		function setCookie(cookie_name, cookie_value, cookie_life) {
			var today = new Date();
			var expiry = new Date(today.getTime() + cookie_life * 24*60*60*1000);
			if (cookie_value != null && cookie_value != ""){
				var cookie_string =cookie_name + "=" + escape(cookie_value)
				if(cookie_life){ cookie_string += "; expires=" + expiry.toGMTString();}
				cookie_string += "; path=/";
				document.cookie = cookie_string;
			}
		}

		google.setOnLoadCallback(onLoad);
		</script>
	</head>
	<body>
		<div class="cometchat_wrapper">
			<div class="container_title {$embedcss}">{$transliterate_language[1]}</div>

			<div class="container_body {$embedcss}">

				<ul id="languages">Loading...</ul>
				<div style="clear:both"></div>
			</div>
		</div>
	</div>

	</body>
	<script>
	var controlparameters = {"type":"plugins", "name":"cctransliterate", "method":"setTitle", "params":{"lang":"{$transliterate_language[0]}"}};
	controlparameters = JSON.stringify(controlparameters);
	parent.postMessage('CC^CONTROL_'+controlparameters,'*');
	</script>
	</html>
EOD;
} else {
	$toId = $_GET['id'];
	$lang = $_GET['lang'];
	$baseData = rawurlencode($_REQUEST['basedata']);
	$extra = '';
	if (!empty($_GET['chatroommode'])) {
		$decide = '#currentroom';
		$chatroommode = $_GET['chatroommode'];
	} else {
		$decide = '#cometchat_user_'.$toId.'_popup';
		$chatroommode = 0;
	}
$transliteratejstag = getDynamicScriptAndLinkTags(array('type' => "plugin",'name' => 'transliterate','ext' => 'js'));
$transliteratecsstag = getDynamicScriptAndLinkTags(array('type' => "plugin",'name' => 'transliterate','layout' => $cc_layout,'ext' => 'css'));
	echo <<<EOD
	<!DOCTYPE html>
	<html>
	<head>
		<title>{$transliterate_language[0]}</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		{$transliteratecsstag}
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
		{$transliteratejstag}
		<script type="text/javascript">

		google.load("elements", "1", {
			packages: "transliteration"
		});

		function formatlang(str) {
			return str[0].toUpperCase()+str.substr(1).toLowerCase();
		}

		function onLoad() {
			var options = {
				sourceLanguage: 'en',
				destinationLanguage: ['{$lang}'],
				shortcutKey: 'ctrl+g',
				transliterationEnabled: true
			};
			var control =
			new google.elements.transliteration.TransliterationControl(options);
			var ids = ["transliteratebox" ];
			control.makeTransliteratable(ids);

			$("#transliteratebox").keyup(function(event) {
				return chatboxKeydown(event);
			});

		}

		function pushcontents() {
			var data = document.getElementById('transliteratebox').value;
			document.getElementById('transliteratebox').value = '';
			var controlparameters = {"type":"plugins", "name":"cctransliterate", "method":"appendMessage", "params":{"to":"{$toId}", "data":data, "chatroommode": "{$chatroommode}", "caller": "{$caller}"}};
			controlparameters = JSON.stringify(controlparameters);
			if(typeof(window.opener) == 'undefined' || window.opener == null){
				parent.postMessage('CC^CONTROL_'+controlparameters,'*');
			}else{
				window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');
			}
			setTimeout('document.getElementById(\'transliteratebox\').focus()',100);
			setTimeout('document.getElementById(\'transliteratebox\').focus()',1000);
		}

		function changeLanguage() {
			setCookie('{$cookiePrefix}language','',0);
			location.href = 'index.php?cc_layout={$cc_layout}&caller={$caller}&id={$toId}&embed={$embed}&basedata={$baseData}&chatroommode={$chatroommode}';
		}

		function setCookie(cookie_name, cookie_value, cookie_life) {
			var today = new Date()
			var expiry = new Date(today.getTime() + cookie_life * 24*60*60*1000)
			var cookie_string =cookie_name + "=" + escape(cookie_value)
			if(cookie_life){ cookie_string += "; expires=" + expiry.toGMTString()}
			cookie_string += "; path=/"
			document.cookie = cookie_string
		}

		function chatboxKeydown(event) {
			if(event.keyCode == 13 && event.shiftKey == 0)  {
				pushcontents();

			}
		}

		google.setOnLoadCallback(onLoad);
		</script>
	</head>
	<body>
		<div class="cometchat_wrapper">

			<div class="container_body {$embedcss}">
				<textarea id="transliteratebox" placeholder="{$transliterate_language[2]}"></textarea><div style="clear:both"></div>
				<div>
					<div id="send">
						<input type="button" value="{$transliterate_language[3]}" onclick="javascript:pushcontents()" class="button">
					</div>
					<div id="change">
						<a href="javascript:void(0);" onclick="changeLanguage()">{$transliterate_language[4]}</a>
					</div>
					<div style="clear:both"></div>
				</div>
			</div>
		</div>
	</div>

	</body>
	<script>
	var languages = google.elements.transliteration.getDestinationLanguages('en');
	$.each(languages, function(key,value) {
		if(value == '{$lang}'){
			var formatLang = formatlang(key);
			var controlparameters = {"type":"plugins", "name":"cctransliterate", "method":"setTitle", "params":{"lang":"{$transliterate_language[0]}", "formatLang":formatLang}};
			controlparameters = JSON.stringify(controlparameters);
			parent.postMessage('CC^CONTROL_'+controlparameters,'*');
		}
	});
	</script>
	</html>
EOD;
}
