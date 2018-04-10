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

if( checkplan('plugins','stickers') == 0){ exit;}
if(!checkMembershipAccess('stickers','plugins')){exit();}

$basedata = $action = null;

if(!empty($_REQUEST['basedata'])) {
	$basedata = sql_real_escape_string($_REQUEST['basedata']);
}

if(!empty($_REQUEST['action'])){
	$action = $_REQUEST['action'];
}

if($action == 'sendSticker') {
	$to = $_REQUEST['to'];
	$key = $_REQUEST['key'];
	$chatroommode = $_REQUEST['chatroommode'];
	$category = $_REQUEST['category'];
	$caller = $_REQUEST['caller'];
	if (!empty($chatroommode)) {
		$controlparameters = array('type' => 'plugins', 'name' => 'stickers', 'method' => 'sendSticker', 'params' => array('to' => $to, 'key' => $key, 'chatroommode' => $chatroommode, 'category' => $category, 'caller' => $caller));
		$controlparameters = json_encode($controlparameters);
		$response = sendChatroomMessage($to, 'CC^CONTROL_'.$controlparameters);
	} else {
		$controlparameters = array('type' => 'plugins', 'name' => 'stickers', 'method' => 'sendSticker', 'params' => array('to' => $to, 'key' => $key, 'chatroommode' => $chatroommode, 'category' => $category, 'caller' => $caller));
		$controlparameters = json_encode($controlparameters);
		$response = sendMessage($to,'CC^CONTROL_'.$controlparameters);
		pushMobileNotification($to,$response['id'],$_SESSION['cometchat']['user']['n'].":".$stickers_language[2]);
	}
	if (!empty($_REQUEST['callback'])) {
		echo $_REQUEST['callback'].'('.json_encode($response).')';
	} else if($response !=null && !empty($response)) {
		echo json_encode($response);
	}
} else {
	$id = $_GET['id'];
	$text = '';
	$categories = array();

	/* Fetching Response of Stickers Data From RestAPI */
	$stickersAPIResponse = getCachedSettings('stickersAPIResponse');
	if($stickersAPIResponse == 'NULL' || $stickersAPIResponse == ''){
		$stickersAPIResponse = cc_curl_call('https://m.chatforyoursite.com/api/get/?accessKey=IeMWnb1v8BFFqjGDVpLYsRRMf74AGI92Gj3RGPKHuaz8UtJ3swOe&licenseKey='.$licensekey);

		setCachedSettings('stickersAPIResponse',$stickersAPIResponse,3600);
	}

	$stickersData = json_decode($stickersAPIResponse,true);
	$stickercategories = $stickersData['categories'];

	foreach($stickercategories as $c){
	    if($c['slug'] != 'christmas'){
	        array_push($categories, $c['slug']);
	    }
	}
	array_unshift($categories, "christmas");

	$tab = '';
	$body_content = '';
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
			$before = "jqcc('#cometchat_trayicon_chatrooms_iframe,#cometchat_container_chatrooms .cometchat_iframe,.cometchat_embed_chatrooms',parent.document)[0].contentWindow";
		}
		if ($broadcastmode == 1) {
			$before = "jqcc('#cometchat_trayicon_chatrooms_iframe,#cometchat_container_chatrooms .cometchat_iframe,.cometchat_embed_chatrooms',parent.document)[0].contentWindow";
		}
	}

	if (!empty($_GET['embed']) && $_GET['embed'] == 'desktop') {
		$embed = 'desktop';
		$embedcss = 'embed';
		$close = "";
		$before = 'parentSandboxBridge';
	}

	$hideadditional = '';

	$stickerstyle = "";
	foreach ($categories as $key=>$value) {
		$selected = '';
		$sticker_selected = '';
		$content = '';
		if($key==0){
			$selected = 'sticker_tab_selected';
			$sticker_selected = 'sticker_selected';
		}
		$tab .= '<div id="'.$value.'" class="tab '.$value.' '.$selected.' '.$sticker_selected.'" ></div>';
		$stickerstyle .= '.'.$value.'{ background: url("'.$stickersData['stickers'][$value]['featured'].'") no-repeat center}';
		$images = $stickersData['stickers'][$value]['images'];
		foreach ($images as $key=>$val) {
			$imageURL = $val['url'];
			$imageSlugVal = str_ireplace($stickersImageUrl.$value.'/', '', $imageURL);
			$imageSlugVal = str_ireplace('.png', '', $imageSlugVal);
			$content .= '<span class="cometchat_sticker_image '.$imageSlugVal.'" category="'.$value.'" chatroommode="'.$chatroommode.'" caller = "'.$caller.'"></span>';
			$stickerstyle .= '.'.$imageSlugVal.'{ background-image: url("'.$imageURL.'")}';
		}
		$body_content .= '<div class="'.$value.' stickers '.$sticker_selected.'">'.$content.'</div>';
	}

	if(!empty($stickerstyle)){
		$stickerstyle = '<style>'.$stickerstyle.'</style>';
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
		$scrollcss = "height:160px !important;";
	}

$jQuerytag = getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'jquery', 'ext' => 'js'));
$jstag = getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'slick', 'ext' => 'js'));
$css = getDynamicScriptAndLinkTags(array('type' => "plugin",'name' => 'stickers','ext' => 'css'));

echo <<<EOD
<!DOCTYPE html>
<html>
	<head>
		<title>{$stickers_language[0]}</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
		<meta name="viewport" content="width=device-width,initial-scale=1" />
		$jQuerytag
		$jstag
		$css
		{$stickerstyle}
		<script>
			$ = jQuery = jqcc;
			var theme = '{$cc_layout}';
		</script>
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
	    	jqcc(function(){
	    		jqcc('.tab').click(function(){
	    			jqcc('.tab').removeClass('selected');
	    			jqcc(this).addClass('selected');
	    			jqcc('.tab').removeClass('sticker_tab_selected');
	    			jqcc('.stickers').removeClass('sticker_selected');
	    			if(theme == 'embedded'){
	    				jqcc('.tab').removeClass('sticker_tab_selected');
	    				jqcc(this).addClass('sticker_tab_selected');
	    			} else {
	    				jqcc('.tab').removeClass('sticker_selected');
	    				jqcc(this).addClass('sticker_selected');
	    			}
	    			jqcc('.'+jqcc(this).attr('id')).addClass('sticker_selected');
	    		});
			jqcc('.cometchat_sticker_image').click(function(){
				var key = jqcc(this).attr('class').split(' ')[1];
				var category = jqcc(this).attr('category');
				var chatroommode = jqcc(this).attr('chatroommode');
				var caller = jqcc(this).attr('caller');
				var controlparameters = {"type":"plugins", "name":"ccstickers", "method":"sendStickerMessage", "params":{"to":{$id}, "key":key, "chatroommode":chatroommode, "category":category, "caller":caller}};
				controlparameters = JSON.stringify(controlparameters);
				if(typeof(parent) != 'undefined' && parent != null && parent != self){
					parent.postMessage('CC^CONTROL_'+controlparameters,'*');
				} else {
					window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');
				}
			});
			var mobileDevice = navigator.userAgent.match(/ipad|ipod|iphone|android|windows ce|Windows Phone|blackberry|palm|symbian/i);
				if(mobileDevice){
					jqcc(".container_body").css({'overflow-y': 'auto'});
					jqcc("#tabs_container").css({'float': 'none'});
				}else if (jQuery().slimScroll) {
					var container_body_height = jqcc(window).height()-jqcc('#tabs_container').height();
					jqcc(".container_body").slimScroll({ width: '100%'});
					jqcc(".container_body").height(container_body_height);
					jqcc(".container_body").slimScroll({ height: container_body_height});
				}
			if (jQuery().slick) {
				jqcc('#tabs_container').slick({ infinite: false, slidesToShow: 4, slidesToScroll: 1 });
			}
		});
	    </script>
	</head>
	<body>
		<div class="cometchat_wrapper">
			<div id="tabs_container">
				{$tab}
		    </div>
			<div class="container_body {$embedcss}">
				{$body_content}
			</div>
		</div>
	</body>
</html>
EOD;
}
