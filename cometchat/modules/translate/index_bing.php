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

$scrolljstag = '';

if ($sleekScroller == 1) {
    $scrolljstag = getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'scroll', 'ext' => 'js'));
}
$jqueryjstag = getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'jquery', 'ext' => 'js'));
$translatecsstag = getDynamicScriptAndLinkTags(array('type' => "plugin",'name' => 'translate', 'ext' => 'css'));

echo <<<EOD
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="cache-control" content="no-cache">
		<meta http-equiv="pragma" content="no-cache">
		<meta http-equiv="expires" content="-1">
		<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
		{$translatecsstag}
		{$jqueryjstag}
		<script>
			$ = jQuery = jqcc;
		</script>
		{$scrolljstag}
		<script type="text/javascript">
		jQuery.cookie = function (key, value, options) {

			// key and at least value given, set cookie...
			if (arguments.length > 1 && String(value) !== "[object Object]") {
				options = jQuery.extend({}, options);

				if (value === null || value === undefined) {
					options.expires = -1;
				}

				if (typeof options.expires === 'number') {
					var days = options.expires, t = options.expires = new Date();
					t.setDate(t.getDate() + days);
				}

				value = String(value);

				return (document.cookie = [
					encodeURIComponent(key), '=',
					options.raw ? value : encodeURIComponent(value),
					options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
					options.path ? '; path=' + options.path : '',
					options.domain ? '; domain=' + options.domain : '',
					options.secure ? '; secure' : ''
				].join(''));
			}

			// key and possibly options given, get cookie...
			options = value || {};
			var result, decode = options.raw ? function (s) { return s; } : decodeURIComponent;
			return (result = new RegExp('(?:^|; )' + encodeURIComponent(key) + '=([^;]*)').exec(document.cookie)) ? decode(result[1]) : null;
		};
		</script>
		<script>

			$(function() {
				if (jQuery().slimScroll) {
					$('.cometchat_wrapper').slimScroll({height: '310px',allowPageScroll: false});
					$(".cometchat_wrapper").css("height","290px");
				}
				var controlparameters = {"type":"modules", "name":"translatepage", "method":"addLanguageCode", "params":{}};
				controlparameters = JSON.stringify(controlparameters);
				parent.postMessage('CC^CONTROL_'+controlparameters,'*');

				$("li").click(function() {
					var info = $(this).attr('id');
					if(info=='en'){
						$.cookie("mstto",null,{ path: '/' });
						var controlparameters = {"type":"modules", "name":"translate", "method":"closeModule", "params":{}};
						controlparameters = JSON.stringify(controlparameters);
						parent.postMessage('CC^CONTROL_'+controlparameters,'*');
					}
					else{
					var controlparameters = {"type":"modules", "name":"translatepage", "method":"changeLanguage", "params":{"lang":info}};
					controlparameters = JSON.stringify(controlparameters);
					parent.postMessage('CC^CONTROL_'+controlparameters,'*');
					$('.languages').hide();
					$('.translating').show();
					setTimeout(function() {
					try {
						if (parent.jqcc.cometchat.ping() == 1) {
							var controlparameters = {"type":"modules", "name":"translate", "method":"closeModule", "params":{}};
							controlparameters = JSON.stringify(controlparameters);
							parent.postMessage('CC^CONTROL_'+controlparameters,'*');
						}
					} catch (e) { }

					$('.languages').show();
					$('.translating').hide();

					},3000);
					}
				});
			});
		</script>
	</head>
	<body>
		<div style="width:100%; margin:0 auto; margin-top: 0px;">

			<div class="cometchat_wrapper">

				<ul class="languages">

					<li id="ar">Arabic</li><li id="bg">Bulgarian</li><li id="ca">Catalan</li><li id="zh-chs">Chinese (Simpl)</li><li id="zh-cht">Chinese (Trad)</li><li id="cs">Czech</li><li id="da">Danish</li><li id="nl">Dutch</li><li id="en">English</li><li id="et">Estonian</li><li id="fi">Finnish</li><li id="fr">French</li><li id="de">German</li><li id="el">Greek</li><li id="ht">Haitian Creole</li><li id="he">Hebrew</li><li id="hi">Hindi</li><li id="hu">Hungarian</li><li id="id">Indonesian</li><li id="it">Italian</li><li id="ja">Japanese</li><li id="ko">Korean</li><li id="lv">Latvian</li><li id="lt">Lithuanian</li><li id="no">Norwegian</li><li id="pl">Polish</li><li id="pt">Portuguese</li><li id="ro">Romanian</li><li id="ru">Russian</li><li id="sk">Slovak</li><li id="sl">Slovenian</li><li selected="selected" id="es">Spanish</li><li id="sv">Swedish</li><li id="th">Thai</li><li id="tr">Turkish</li><li id="uk">Ukrainian</li><li id="vi">Vietnamese</li>

				</ul>

				<div class="translating">{$translate_language[0]}</div>
				<div style="clear:both"></div>
			</div>
		</div>
	</body>
</html>
EOD;
?>
