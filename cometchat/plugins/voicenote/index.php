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

if( checkplan('plugins','voicenote') == 0){ exit;}
if(!checkMembershipAccess('voicenote','plugins')){exit();}

if (empty($_GET['id'])) { exit; }

$basedata = '';
$toId = intval($_GET['id']);
$chatroommode = 0;
if (!empty($_GET['chatroommode'])) {
	$chatroommode = 1;
}

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

$basedata = $_REQUEST['basedata'];

$cc_layout = 'docked';
if(!empty($_REQUEST['cc_layout'])){
  $cc_layout = $_REQUEST['cc_layout'];
}

$method = 'closeCCPopup';
$params = "'name':'voicenote','roomid':'{$_REQUEST['id']}'";
if(!empty($cc_layout) && $cc_layout == 'docked'){
    $method = 'closeChatboxCCPopup';
    $params = "'name':'voicenote','id':'{$_REQUEST['id']}',chatroommode:'{$chatroommode}'";
}
$jQuerytag = getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'jquery','callbakcfn' => $embed, 'ext' => 'js'));
$jstag = getDynamicScriptAndLinkTags(array('type' => "plugin",'name' => 'voicenote', 'ext' => 'js'));
$csstag = getDynamicScriptAndLinkTags(array('type' => "plugin",'name' => 'voicenote','layout' => $cc_layout,'ext' => 'css'));
$staticCDNUrl = STATIC_CDN_URL;
echo <<<EOD
<!DOCTYPE html>
	<html>
	<head>
	<meta name="viewport" content="user-scalable=1,width=device-width, initial-scale=1.0" />
	<title>{$voicenote_language[0]}</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	$jQuerytag
	<script>  $ = jQuery = jqcc; </script>
	$jstag
	$csstag
	<script type="text/javascript">
		var tid = '{$toId}';
		jqcc(document).ready(function(){
			//Record Button
			jqcc(document).on("click","#recordCircle",function(e)
			{
				if(jqcc(this).attr('class').indexOf('startRecord') !== -1){
					jqcc(this).removeClass('startRecord').addClass('stopRecord');
					jqcc("#recordContainer").removeClass('startContainer').addClass('stopContainer');
					jqcc("#recordText").html("Stop");
					jqcc("#recordCircleOverlay").toggle();
					jqcc.stopwatch.startTimer('sw'); // Stopwatch Start
					startRecording(this); // Voice Recording Start
					animateRecord();
				}else{
					jqcc.stopwatch.resetTimer(); // Stopwatch Reset
					jqcc(this).removeClass('stopRecord').addClass('startRecord');
					jqcc("#recordContainer").removeClass('stopContainer').addClass('startContainer');
					jqcc("#recordText").html("Record");
					jqcc("#recordCircleOverlay").toggle();
					jqcc("#recordCircle").hide();
					jqcc("#recordContainer").append('<svg version="1.1" id="Layer_1" class="voicenote_loader" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="64px" height="64px" viewBox="0 0 64 64" enable-background="new 0 0 64 64" xml:space="preserve"><path d="M53.9,10.1C48.1,4.2,40.3,1,32,1s-16.1,3.2-21.9,9.1C-2,22.2-2,41.8,10.1,53.9C15.9,59.8,23.7,63,32,63s16.1-3.2,21.9-9.1C66,41.8,66,22.2,53.9,10.1z M16,38c-3.3,0-6-2.7-6-6s2.7-6,6-6s6,2.7,6,6S19.3,38,16,38z M32,54c-3.3,0-6-2.7-6-6s2.7-6,6-6s6,2.7,6,6S35.3,54,32,54z M32,22c-3.3,0-6-2.7-6-6s2.7-6,6-6s6,2.7,6,6S35.3,22,32,22z M48,38c-3.3,0-6-2.7-6-6s2.7-6,6-6s6,2.7,6,6S51.3,38,48,38z"/></svg>');
					stopRecording(this); // Voice Recording Stop
				}
			});

		})
		
		function animateRecord(){
			var height = '60px';
			var width = '60px';
			if(jqcc('#recordCircleOverlay').width() == jqcc('#recordCircle').width()){
			height = '80px';
			width = '80px';
			}
			$( "#recordCircleOverlay" ).animate({
			   width:width,
			   height: height
			 }, 1000 ,function() {
			   // Animation complete.
				if(jqcc('#recordCircle').attr('class').indexOf('startRecord') == -1){
			   		animateRecord();
				}
			});
		}
		function uploadVoiceNote(voicenote)
		{
			var date = new Date();
			var foramttedDate = date.toLocaleDateString() + " " + date.toLocaleTimeString();
			var audioFile = new File ([voicenote],'audiofile_'+foramttedDate+'.mp3');
			var formData = new FormData();
			formData.set('Filedata',audioFile);
			formData.set('to',tid);
			formData.set('chatroommode','{$chatroommode}');
			formData.set('basedata','{$basedata}');
			jqcc.ajax({
				type: "post",
				url: "{$staticCDNUrl}plugins/filetransfer/upload.php",
				data: formData,
				cache: false,
		        contentType: false,
		        processData: false,
				success: function(html)
				{
					closeVoiceNote()
				}
			});

		}
		function closeVoiceNote(){
			var controlparameters = {'type':'plugins', 'name':'voicenote', 'method':'{$method}', 'params':{{$params}}};
	        controlparameters = JSON.stringify(controlparameters);
	        if(typeof(parent) != 'undefined' && parent != null && parent != self){
	            parent.postMessage('CC^CONTROL_'+controlparameters,'*');
	        } else {
	            window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');
	        }
		}
	</script>
	</head>
	<body>
		<pre id="log" style="display: none;"></pre>
		<div id="recordTime"> <span id="sw_m">00</span><span>:</span><span id="sw_s">00</span></div>
		<div id="recordContainer" class="startContainer">
			<div id="recordCircle" class="startRecord"><div id="recordText">Record</div></div>
			<div id="recordCircleOverlay" style="display: none;"></div>
		</div>
	</body>
	</html>
EOD;
