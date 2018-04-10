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

if( checkplan('plugins','filetransfer') == 0){ exit;}
if(!checkMembershipAccess('filetransfer','plugins')){exit();}

$toId = $_GET['id'];
$baseData = $_REQUEST['basedata'];

$chatroommode = 0;

if (!empty($_GET['chatroommode'])) {
	$chatroommode = 1;
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

$cc_layout = '';
if(!empty($_REQUEST['cc_layout'])){
	$cc_layout = $_REQUEST['cc_layout'];
}

$csstag = getDynamicScriptAndLinkTags(array('type' => "plugin",'name' => 'filetransfer','layout' => $cc_layout,'ext' => 'css'));
$jQuerytag = getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'jquery','callbakcfn' => $callbakcfn, 'ext' => 'js'));
$jstag = getDynamicScriptAndLinkTags(array('type' => "plugin",'name' => 'filetransfer', 'ext' => 'js'));

echo <<<EOD
	<!DOCTYPE html>
	<html>
	<head>
		<meta name="viewport" content="user-scalable=0,width=device-width, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<title>{$filetransfer_language[0]}</title>
		$csstag
		$jQuerytag
		<script>
			$ = jQuery = jqcc;
		</script>
		$jstag
		<script type="text/javascript">
		function disableButton() {
			document.getElementById('uploadfile').setAttribute('disabled','true');
		}
		</script>
	</head>
	<body>
		<form name="upload" action="upload.php?embed={$embed}" method="post" enctype="multipart/form-data">
			<div class="cometchat_wrapper">
				<div class="container_title {$embedcss}">{$filetransfer_language[1]}</div>
				<div class="container_body {$embedcss}">
					<div class="container_body_1">{$filetransfer_language[2]}</div>
					<div id="select-0" class="container_body_2">
						<label class="cabinet">
							<input id='uploadfile' type="file" class="file" name="Filedata" onchange="javascript:document.upload.submit();disableButton();" />
						</label>
					</div>
					<div class="container_body_3 {$embedcss}">{$filetransfer_language[4]}</div>
					<div style="clear:both"></div>
					<div class="container_body_4">{$filetransfer_language[3]}</div>
					<input type="hidden" name="to" value="{$toId}">
					<input type="hidden" name="basedata" value="{$baseData}">
					<input type="hidden" name="chatroommode" value="{$chatroommode}">
					<input type="hidden" name="sendername" value="{$sendername}">
				</div>
			</div>
			<script>SI.Files.stylizeAll();</script>
			<script type='text/javascript'>
				var width = 0;
				var height = 0;
				if(typeof $ != 'undefined')
				$(document).ready(function(){
					setTimeout(function(){
						width = ($("form").outerWidth(false)+window.outerWidth-$("form").outerWidth(false));
						height = ($('form').outerHeight(false)+window.outerHeight-window.innerHeight)+10;//margin-top+margin-bottom
						window.resizeTo(width,height);
						$('#uploadfile').trigger('click');
					},150);

					if(typeof(parent) != 'undefined'){
						var controlparameters = {'type':'plugin', 'name':'filetransfer', 'method':'resizeCCPopup', 'params':{"id":"loadChatroomPro", "height":height, "width":width}};
	                	controlparameters = JSON.stringify(controlparameters);
	                	if(typeof(window.opener) == null){
	                		window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');
	                	}else{
	                		parent.postMessage('CC^CONTROL_'+controlparameters,'*');
	                	}
					}
					//Height 80 = container_body.height(50) + embed.padding(10*2) + container.margin(5*2)

					$('#uploadfile').click();


				});
			</script>
		</form>
	</body>
	</html>
EOD;
