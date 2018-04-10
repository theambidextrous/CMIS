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
if( checkplan('plugins','handwrite') == 0){ exit;}
if(!checkMembershipAccess('handwrite','plugins')){exit();}

if (empty($_GET['id'])) { exit; }

$toId = intval($_GET['id']);

if (!empty($_GET['chatroommode'])) {
	$toId = "c".$toId;
}

$sendername = $_REQUEST['sendername'];
$embed = '';
$embedcss = '';

if (!empty($_GET['embed']) && $_GET['embed'] == 'web') {
	$embed = 'web';
	$embedcss = 'embed';
}

if (!empty($_GET['embed']) && $_GET['embed'] == 'desktop') {
	$embed = 'desktop';
	$embedcss = 'embed';
}

if (!empty($_REQUEST['callbackfn']) && $_REQUEST['callbackfn'] == 'mobileapp') {
  $embed = 'mobileapp';
}

$toId .= ';'.$_REQUEST['basedata'].';'.$embed.';';

$cc_layout = 'docked';
if(!empty($_REQUEST['cc_layout'])){
  $cc_layout = $_REQUEST['cc_layout'];
}

$jQuerytag = getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'jquery','callbakcfn' => $embed, 'ext' => 'js'));
$jstag = getDynamicScriptAndLinkTags(array('type' => "plugin",'name' => 'handwrite', 'ext' => 'js'));
$csstag = getDynamicScriptAndLinkTags(array('type' => "plugin",'name' => 'handwrite','layout' => $cc_layout,'ext' => 'css'));
$staticCDNUrl = STATIC_CDN_URL;
echo <<<EOD
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="user-scalable=1,width=device-width, initial-scale=1.0" />
<title>{$handwrite_language[0]}</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
$jQuerytag
<script>  $ = jQuery = jqcc; </script>
$jstag
$csstag
<script type="text/javascript">
        var tid = '{$toId}';
	if( {$lightboxWindows} == 0 ){
		window.onresize = function() { window.resizeTo('350','320' ); }
		window.load = function() { window.resizeTo('350','320' ); }
	}
        function isIE () {
            var myNav = navigator.userAgent.toLowerCase();
            return (myNav.indexOf('msie') != -1) ? true : false;
        }
</script>

</head>
<body>
     <div id="content">
         <object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="//fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="100%" height="250" align="middle" id="main">
    <param name="allowScriptAccess" value="sameDomain" />
    <param name="movie" value="handwriting.swf" />
    <param name="quality" value="high" />
    <param name="bgcolor" value="#ffffff" />
    <param name="FlashVars" value="tid={$toId}" />
    <param name="scale" value="exactFit" />
    <embed src="{$staticCDNUrl}plugins/handwrite/handwriting.swf"
           width="100%"
           height="250"
           autostart="false"
           quality="high"
           bgcolor="#ffffff"
           FlashVars="tid={$toId}"
           name="main"
           align="middle"
           allowScriptAccess="sameDomain"
           type="application/x-shockwave-flash"
           pluginspage="http://www.macromedia.com/go/getflashplayer" />
</object>
</div>
<div id="sketch">
        <canvas id="paint"></canvas>
        <div class="color-select">
            <div val="white" class="color-opt white"></div>
            <div val="maroon" class="color-opt maroon"></div>
            <div val="steelblue" class="color-opt steelblue"></div>
            <div val="green" class="color-opt green"></div>
            <div val="gold" class="color-opt gold"></div>
            <div val="black" class="color-opt black"></div>
            <div val="blueviolet" class="color-opt blueviolet"></div>
            <div val="deepskyblue" class="color-opt deepskyblue"></div>
            <div val="chartreuse" class="color-opt chartreuse"></div>
            <div val="red" class="color-opt red"></div>
        </div>
        <div id="footer">
            <div class="handwrite-btn pencil-btn"><img src="{$staticCDNUrl}plugins/handwrite/images/pencil.png"></div>
            <div class="handwrite-btn eraser-btn"><img src="{$staticCDNUrl}plugins/handwrite/images/eraser.png"></div>
            <div class="width-btn" >
                <span>WIDTH</span>
                <span class="width-select onepx selected" val="1"></span>
                <span class="width-select twopx" val="2" ></span>
                <span class="width-select threepx" val="3" ></span>
                <span class="width-select fourpx" val="5" ></span>
            </div>
            <div class="handwrite-btn color-btn" ><img src="{$staticCDNUrl}plugins/handwrite/images/pencil.png"></div>
            <div class="handwrite-btn clear-btn"><img src="{$staticCDNUrl}plugins/handwrite/images/clear.png"></div>
            <div class="send-btn" onclick="javascript:send()"><img title="Send" src="{$staticCDNUrl}plugins/handwrite/images/send.svg"></div>
        </div>
</div>
<input id="sendername" type="hidden" name="sendername" value="{$sendername}">
</body>
<script type="text/javascript">
if(isIE()){
    $('#sketch').remove();
} else {
   $('#content').remove();
}
</script>
</html>
EOD;
