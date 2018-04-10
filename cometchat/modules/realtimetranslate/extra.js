/*
 * CometChat
 * Copyright (c) 2016 Inscripts - support@cometchat.com | http://www.cometchat.com | http://www.inscripts.com
*/

function text_translate(params){
	var trayIcons = jqcc.cometchat.getTrayicon();
	var isRealtimetranslateEnabled = trayIcons.hasOwnProperty('realtimetranslate');
	var settings = jqcc.cometchat.getSettings();
	var baseUrl = jqcc.cometchat.getBaseUrl();
	var key 	= '<?php echo $licensekey;?>';
	var to 		= jqcc.cookie(settings.cookiePrefix+'rttlang');
	var smileycount    = params.message.indexOf('cometchat_smiley');
	var handwritecount    = params.message.indexOf('cc_handwrite_image');
	var stickercount    = params.message.indexOf('cometchat_stickerImage');
	if(!params.hasOwnProperty('isTranslated') && smileycount == -1 && handwritecount == -1 && stickercount == -1 && params.message.indexOf('CC^CONTROL_') == -1){
		if (typeof(key) != 'undefined' && key.indexOf('COMETCHAT-') != -1) {
			url = "//instant.cometondemand.net/translate/";
		} else {
			url = baseUrl+'modules/realtimetranslate/translate.php?action=translate_text';
		}
        jqcc.ajax({
			url : url,
			type : 'GET',
			data : {text: params.message, key: key, to: to},
			dataType : 'jsonp',
			success : function(data) {
				if (data.translatedText) {
					if (typeof (data.translatedText) == "object") {
						data.translatedText = (data.translatedText).data.translations[0].translatedText;
					}
					params.message = decodeURI(data.translatedText)+' <span class="untranslatedtext">('+params.message+')</span>';
					params.isTranslated = 1;
					var item = {'msg' : params};
					if(params.hasOwnProperty('chatroomid') || params.hasOwnProperty('roomid')){
						params.roomid = params.roomid || params.chatroomid;
						jqcc[jqcc.cometchat.getChatroomVars('calleeAPI')].addChatroomMessage(params);
					} else {
						jqcc[settings.theme].addMessages(item);
					}
				}
			},
			error : function(data) {
				console.log("failed:",data);
			}
		});

    }
}
