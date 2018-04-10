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

if( checkplan('plugins','smilies') == 0){ exit;}
if(!checkMembershipAccess('smilies','plugins')){exit();}

$id = $_GET['id'];

$text = '';
$people_text = '';
$nature_text = '';
$objects_text = '';
$places_text = '';
$symbols_text = '';

$used = array();

$chatroommode = 0;
$broadcastmode = 0;
$caller = '';
if (!empty($_GET['chatroommode'])) {
	$chatroommode = 1;
}
if (!empty($_GET['broadcastmode'])) {
	$broadcastmode = 1;
}
if (!empty($_GET['caller'])) {
	$caller = $_GET['caller'];
}
$embed = '';
$embedcss = '';
$close = "setTimeout('window.close()',2000);";
$before = 'window.opener';

if (!empty($_GET['embed']) && $_GET['embed'] == 'web') {
	$embed = 'web';
	$embedcss = 'embed';
	$close = "";
	$before = 'parent';

	if ($chatroommode == 1) {
		$before = "$('#cometchat_trayicon_chatrooms_iframe,#cometchat_container_chatrooms .cometchat_iframe,.cometchat_embed_chatrooms',parent.document)[0].contentWindow";
	}
	if ($broadcastmode == 1) {
		$before = "$('#cometchat_trayicon_chatrooms_iframe,#cometchat_container_chatrooms .cometchat_iframe,.cometchat_embed_chatrooms',parent.document)[0].contentWindow";
	}
}

if (!empty($_GET['embed']) && $_GET['embed'] == 'desktop') {
	$embed = 'desktop';
	$embedcss = 'embed';
	$close = "";
	$before = 'parentSandboxBridge';
}

foreach ($smileys as $pattern => $result) {

	if (!empty($used[$result])) {
	} else {
		$title = str_replace("-"," ",ucwords(preg_replace("/\.(.*)/","",$result)));
		$class = str_replace("-"," ",preg_replace("/\.(.*)/","",$result));
		if (in_array($result, $people)) {
			$people_text .= '<span class="cometchat_smiley '.$class.' people" title="'.$pattern.' ('.$title.')" to="'.$id.'" pattern="'.$pattern.'" chatroommode="'.$chatroommode.'" caller = "'.$caller.'" style="padding:2px;"></span>';
		} elseif (in_array($result, $nature)) {
			$nature_text .= '<span class="cometchat_smiley '.$class.' nature" title="'.$pattern.' ('.$title.')" to="'.$id.'" pattern="'.$pattern.'" chatroommode="'.$chatroommode.'" caller = "'.$caller.'" style="padding:2px;"></span>';
		} elseif (in_array($result, $objects)) {
			$objects_text .= '<span class="cometchat_smiley '.$class.' objects" title="'.$pattern.' ('.$title.')" to="'.$id.'" pattern="'.$pattern.'" chatroommode="'.$chatroommode.'" caller = "'.$caller.'" style="padding:2px;"></span>';
		} elseif (in_array($result, $places)) {
			$places_text .= '<span class="cometchat_smiley '.$class.' places" title="'.$pattern.' ('.$title.')" to="'.$id.'" pattern="'.$pattern.'" chatroommode="'.$chatroommode.'" caller = "'.$caller.'" style="padding:2px;"></span>';
		} elseif (in_array($result, $symbols)) {
			$symbols_text .= '<span class="cometchat_smiley '.$class.' symbols" title="'.$pattern.' ('.$title.')" to="'.$id.'" pattern="'.$pattern.'" chatroommode="'.$chatroommode.'" caller = "'.$caller.'" style="padding:2px;"></span>';
		} else {
			$text .= '<img class="cometchat_smiley" width="20" height="20" src="'.BASE_URL.'writable/images/smileys/'.$result.'" title="'.$pattern.' ('.$title.')" to="'.$id.'" pattern="'.$pattern.'" chatroommode="'.$chatroommode.'" caller = "'.$caller.'" style="padding:2px">';
		}

		$used[$result] = 1;
	}
}
$hideadditional = '';
$tablength = "tab_length6";
$showadditional = '<div id="additional" class="tab tab_length6 "><h2>'.$smilies_language[7].'</h2></div>';
if(empty($text)){
	$tablength = "tab_length5";
	$showadditional = '';
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
if($cc_layout == 'embedded'){
	$scrollcss = "";
}
$jQuerytag = getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'jquery', 'ext' => 'js'));
$csstag = getDynamicScriptAndLinkTags(array('type' => "plugin",'name' => 'smilies','subtype' => 'smilies','layout' => $cc_layout,'ext' => 'css'));
$baseurl = BASE_URL;
$staticCDNUrl = STATIC_CDN_URL;
echo <<<EOD
<!DOCTYPE html>
<html>
	<head>
		<title>{$smilies_language[0]}</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
		$csstag
		$jQuerytag
		<script> $ = jQuery = jqcc;	</script>
		{$extrajs}
		<style>
			.container_body {
				{$scrollcss};
			}
			.container_body.embed {
				{$scrollcss};
			}
		</style>
		<script type="text/javascript">
			var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
			var eventer = window[eventMethod];
			var messageEvent = eventMethod == "attachEvent" ? "onmessage" : "message";
			eventer(messageEvent,function(e) {
				if(typeof(e.data) != 'undefined' && typeof(e.data) == 'string') {
					if(e.data.indexOf('CC^CONTROL_')!== -1){
						var controlparameters = e.data.slice(11);
						controlparameters = JSON.parse(controlparameters);
						if(controlparameters.type == 'plugin' && controlparameters.name == 'smilies'){
							window[controlparameters.method](controlparameters.params)
						}
					}
				}
			},false);
			function resizeContainerbody(params){
				if(params.hasOwnProperty('height')){
					jqcc('.container_body.embed').height(params.height+'px');
				}
				if(params.hasOwnProperty('width')){
					jqcc('.container_body.embed').width(params.width+'px');
				}
			}
	    	$(function(){
	    		$('.tab').click(function(){
	    			$('.tab').removeClass('selected');
	    			$('.emojis').removeClass('emoji_selected');
	    			$(this).addClass('selected');
	    			$('.'+$(this).attr('id')).addClass('emoji_selected');
	    		});
				$('.cometchat_smiley').click(function(){
					var to = $(this).attr('to');
					var pattern = $(this).attr('pattern');
					var chatroommode = $(this).attr('chatroommode');
					var caller = $(this).attr('caller');
					var controlparameters = {"type":"plugins", "name":"ccsmilies", "method":"addtext", "params":{"to":to, "pattern":pattern, "chatroommode":chatroommode, "caller":caller}};
					controlparameters = JSON.stringify(controlparameters);
					if(typeof(parent) != 'undefined' && parent != null && parent != self){
						parent.postMessage('CC^CONTROL_'+controlparameters,'*');
					} else {
						window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');
					}
				});
				var mobileDevice = navigator.userAgent.match(/ipad|ipod|iphone|android|windows ce|Windows Phone|blackberry|palm|symbian/i);
				if(mobileDevice){
					$(".container_body").css({ 'overflow-y': 'auto','width':'100%'});
				}else if (jQuery().slimScroll) {
					var container_body_height = $(window).height()-$('#tabs').height();
					$(".container_body").height(container_body_height);
					$(".container_body").slimScroll({ width: '100%'});
					$(".container_body").slimScroll({ height: 'auto'});
				}
			});
	    </script>
	</head>
	<body>
		<div class="cometchat_wrapper">
			<div id="tabs">
			    <div id="people" class="tab {$tablength} selected"><img src="{$staticCDNUrl}images/smile.svg"/></div>
			    <div id="nature" class="tab {$tablength}"><img src="{$staticCDNUrl}images/panda.svg"/></div>
			    <div id="objects" class="tab {$tablength}"><img src="{$staticCDNUrl}images/coffee.svg"/></div>
			    <div id="places" class="tab {$tablength}"><img src="{$staticCDNUrl}images/transportation.svg"/></div>
			    <div id="symbols" class="tab {$tablength}"><img src="{$staticCDNUrl}images/ball.svg"/></div>
			    {$showadditional}
		    </div>
			<div class="container_body {$embedcss}">
				<div class="people emojis emoji_selected" id="emoji-people">{$people_text}</div>
				<div class="nature emojis" id="emoji-nature">{$nature_text}</div>
				<div class="objects emojis" id="emoji-objects">{$objects_text}</div>
				<div class="places emojis" id="emoji-places">{$places_text}</div>
				<div class="symbols emojis" id="emoji-symbols">{$symbols_text}</div>
				<div class="additional emojis" id="emoji-additional">{$text}</div>
			</div>
		</div>
	</body>
</html>
EOD;
