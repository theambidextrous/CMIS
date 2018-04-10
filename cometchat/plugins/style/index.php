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

include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."config.php");

if( checkplan('plugins','style') == 0){ exit;}

$id = $_GET['id'];

$text = '';

$styles = explode(',',$styleOptions);

$embed = '';
$close = "setTimeout('window.close()',100);";
$before = 'window.opener';

if (!empty($_GET['embed'])) {
	$before = "$('#cometchat_trayicon_chatrooms_iframe,#cometchat_container_chatrooms .cometchat_iframe,.cometchat_embed_chatrooms',parent.document)[0].contentWindow";
	$embed = 'embed'; $close = "parent.closeCCPopup('style');";
}

foreach ($styles as $style) {
	$text .= '<span class="setStyle cometchat_colorbox" pattern="'.$style.'" style="margin:5px;background-color:#'.$style.';display:block;float:left;"></span>';
}

$cc_layout = '';
if(!empty($_REQUEST['cc_layout'])){
	$cc_layout = $_REQUEST['cc_layout'];
}
$jQuerytag = getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'jquery', 'ext' => 'js'));
$csstag = getDynamicScriptAndLinkTags(array('type' => "plugin",'name' => 'style','layout' => $cc_layout,'ext' => 'css'));
echo <<<EOD
<!DOCTYPE html>
<html>
<head>
<title>{$style_language[0]}</title>
<meta name="viewport" content="user-scalable=0,width=device-width, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
$jQuerytag
<script>
	$ = jQuery = jqcc;
	$(document).ready(function(){
		$('.setStyle').on('click',function(){
			var pattern = $(this).attr('pattern');
			var controlparameters = {"type":"plugins", "name":"ccstyle", "method":"updatecolor", "params":{"pattern":pattern,"chatroommode":"1"}};
			controlparameters = JSON.stringify(controlparameters);
			if(typeof(parent) != 'undefined' && parent != null && parent != self){
				if(typeof(parent) != 'undefined' && parent != null && parent != self){
					parent.postMessage('CC^CONTROL_'+controlparameters,'*');
					var controlparameters = {'type':'plugins', 'name':'style', 'method':'closeCCPopup', 'params':{'name':'style'}};
                    controlparameters = JSON.stringify(controlparameters);
                    parent.postMessage('CC^CONTROL_'+controlparameters,'*');
				} else {
					window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');
					window.close();
				}
			} else {
				if(window.top == window.self){
					window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');
					window.close();
				}else{
					var controlparameters = {"pattern":pattern};
		            $.ccstyle.updatecolor(controlparameters);
		            closeCCPopup('style');
				}
			}
			document.cookie="{$cookiePrefix}chatroomcolor="+pattern+";path=/";
		});
	});
</script>
$csstag
</head>
<body>
<div class="cometchat_wrapper">
<div class="container_title {$embed}">{$style_language[1]}</div>

<div class="container_body {$embed}">
$text
<div style="clear:both"></div>
</div>
</div>
</div>

</body>
</html>
EOD;

?>
