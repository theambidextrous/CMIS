<?php
include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."config.php");
if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php")) {
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php");
}
?>

/*
 * CometChat
 * Copyright (c) 2016 Inscripts - support@cometchat.com | http://www.cometchat.com | http://www.inscripts.com
*/

(function($){
	$.ccvoicenote = (function() {
		var aws_storage = '<?php echo AWS_STORAGE;?>';
		var aws_bucket_url = '<?php echo $aws_bucket_url;?>';
		var bucket_path = '<?php echo $bucket_path;?>';
		var duration;
		var cometchat_message_media={}



		return {
			getTitle: function() {
				return jqcc.ccvoicenote.getLanguage('title');
			},
			init: function (params) {
				if(location.protocol == 'https:' || location.host == 'localhost'){
					if (jqcc.cometchat.membershipAccess('voicenote','plugins')){
						var id = params.to;
						var extraQueryString = '';
						var baseUrl = $.cometchat.getBaseUrl();
						var baseData = $.cometchat.getBaseData();
						if(typeof(params.chatroommode) != "undefined" && params.chatroommode == 1) {
							var extraQueryString = '&chatroommode=1';
						}
						var caller = '';
						if(typeof(params.caller) != "undefined") {
							caller = params.caller;
						}
						var windowMode = 0;
						if(typeof(params.windowMode) == "undefined") {
							windowMode = 0;
						} else {
							windowMode = 1;
						}
						loadPopupInChatbox(baseUrl+'plugins/voicenote/index.php?id='+id+extraQueryString+'&caller='+caller+'&basedata='+baseData, 'voicenote',0,params.to,params.chatroommode);
					}	
				}else{
					alert('Please contact your website administrator if you are unable to use this plugin.')
				}
			},
			voicenotetimeUpdate: function (msgid,music) {
				var playhead =  jqcc('#cometchat_message_'+msgid).find('.cometchat_mediaplayhead');
				var timeline = jqcc('#cometchat_message_'+msgid).find('.cometchat_mediatimeline');
				var playpausebutton = jqcc('#cometchat_message_'+msgid).find('#cometchat_playpausebutton');
				var timelineWidth = timeline.get(0).offsetWidth - playhead.get(0).offsetWidth;
				var playPercent = timelineWidth * (music[0].currentTime / duration);
				var mediaplayed;
				playhead[0].style.marginLeft = playPercent + "px";
				if (music[0].currentTime == duration) {
					playhead[0].style.marginLeft = "0px";
					playpausebutton.className = "";
					playpausebutton.removeClass('cometchat_mediapause').addClass('cometchat_mediaplay');
				}
				mediaplayed = (music[0].currentTime/100).toFixed(2);
				jqcc('#cometchat_message_'+msgid).find('.cometchat_mediaplayedtime').css('display','table').text(mediaplayed);
			},
			addEventListenersForMediaMessages: function (msgid,music){
				if(music[0].duration!==null){
					duration = music[0].duration;
				}else{
					music[0].addEventListener("canplay", function () {
						duration = music[0].duration;
					}, false);
				}
				music[0].addEventListener("timeupdate", function(){
				jqcc.ccvoicenote.voicenotetimeUpdate(msgid,music)}, false);
				/*jqcc('#cometchat_message_'+msgid).find('#cometchat_playpausebutton').on('click',function(){
					jqcc.ccvoicenote.playvoicenote(msgid);
				});*/
			},
			playvoicenote: function (msgid) {
				var music = jqcc('#cometchat_message_'+msgid).find('audio');
				var voiceFilelocation = jqcc('#cometchat_message_'+msgid).find('source').attr('src');
				var voicefile_status = jqcc.ccvoicenote.voicefileExists(voiceFilelocation);
				if(voicefile_status != '' && voicefile_status == '404'){
					alert('This voice file doesn\'t exist.');
					return;
				}

				var playpausebutton = jqcc('#cometchat_message_'+msgid).find('#cometchat_playpausebutton');
				if(!cometchat_message_media.hasOwnProperty('mediamessage_initialised_'+msgid)){
					jqcc.ccvoicenote.addEventListenersForMediaMessages(msgid,music);
					cometchat_message_media['mediamessage_initialised_'+msgid]=1;
				}
				if (music.get(0).paused) {
					music.get(0).play();
					playpausebutton.removeClass('cometchat_mediaplay').addClass('cometchat_mediapause');
				} else {
					music.get(0).pause();
					playpausebutton.removeClass('cometchat_mediapause').addClass('cometchat_mediaplay');
				}
			},
			voicefileExists: function(fileLocation) {
				var voiceresponse = $.ajax({
					url: fileLocation,
					type: 'HEAD',
					async: false
				}).status;
				return voiceresponse;
			},
			getLanguage: function(id) {
				voicenote_language =  <?php echo json_encode($voicenote_language); ?>;
				if(typeof id==undefined){
					return voicenote_language;
				}else{
					return voicenote_language[id];
				}
			}
		};
	})();
})(jqcc);

jqcc(function(){
	jqcc('#cometchat_playpausebutton').live('click',function(e){
		var message_id='';
		var mode = jqcc(this).parent().closest('span').attr('class');
		if(mode != '' && mode!='chatroom_msg'){
			msg_span = jqcc(this).parents('.cometchat_chatboxmessage').attr('id').split('_');
			message_id = msg_span[2];
		}else{
			message_id = jqcc(this).closest('span').next().text();
		}
		jqcc.ccvoicenote.playvoicenote(message_id);
	});
});
