<?php
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."config.php");

	if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php")) {
		include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php");
	}
?>

/*
 * CometChat
 * Copyright (c) 2016 Inscripts - support@cometchat.com | http://www.cometchat.com | http://www.inscripts.com
*/

(function($){

	$.ccsmilies = (function () {

		var height = <?php echo $smlHeight;?>;
		var width = <?php echo $smlWidth;?>;
		var theme = "<?php echo $layout;?>";
		var mobileDevice = navigator.userAgent.match(/ipad|ipod|iphone|android|windows ce|Windows Phone|IEMobile|blackberry|palm|symbian/i);

        return {

			getTitle: function() {
				return jqcc.ccsmilies.getLanguage('title');
			},

			init: function (params) {
				if (jqcc.cometchat.membershipAccess('smilies','plugins')){
					baseUrl = $.cometchat.getBaseUrl();
					baseData = $.cometchat.getBaseData();
					if(typeof(params.windowMode) == "undefined") {
						params.windowMode = 0;
					} else {
						params.windowMode = 1;
					}				
					if(typeof(params.caller) == "undefined") {
						params.caller = '';
					}				
					jqcc.ccsmilies.loadsmilies(params);
				}
			},

			addtext: function (params) {
				var string = '';
				var id = params.to;
				var text = params.pattern+' ';
				var chatroommode = params.chatroommode;
				if(chatroommode == 1 && mobileDevice == null) {
					if(theme == 'embedded'){
						var currentroom_textarea = $('#currentroom').find('textarea.cometchat_textarea');
					}else{
						var currentroom_textarea = $('#cometchat_group_'+id+'_popup').find('textarea.cometchat_textarea');
					}

					if(mobileDevice == null){
	                    currentroom_textarea.focus();
	                }
					string = currentroom_textarea.val();
					if (string.charAt(string.length-1) == ' ') {
						currentroom_textarea.val(currentroom_textarea.val()+text);
					} else {
						if (string.length == 0) {
							currentroom_textarea.val(text);
						} else {
							currentroom_textarea.val(currentroom_textarea.val()+' '+text);
						}
					}
				} else if(chatroommode == 1 && mobileDevice) {
					if(theme == 'embedded'){
						var currentroom_textarea = $('#currentroom').find('textarea.cometchat_textarea');
					}else{
						var currentroom_textarea = $('#cometchat_group_'+id+'_popup').find('textarea.cometchat_textarea');
					}
					currentroom_textarea.focus();
					string = currentroom_textarea.val();
					currentroom_textarea.focus();
					if (string.charAt(string.length-1) == ' ') {
						currentroom_textarea.val(currentroom_textarea.val()+text);
					} else {
						if (string.length == 0) {
							currentroom_textarea.val(text);
						} else {
							currentroom_textarea.val(currentroom_textarea.val()+' '+text);
						}
					}
				} else if(chatroommode == 0 && mobileDevice) {
					if($('#cometchat_user_'+id+'_popup').length > 0) {
						var cometchat_user_textarea = $('#cometchat_user_'+id+'_popup').find('textarea.cometchat_textarea');
						jqcc.cometchat.chatWith(id);
						cometchat_user_textarea.focus();
						string = cometchat_user_textarea.val();
						cometchat_user_textarea.focus();
						if (string.charAt(string.length-1) == ' ') {
							cometchat_user_textarea.val(string+text);
						} else {
							if (string.length == 0) {
								cometchat_user_textarea.val(text);
							} else {
								cometchat_user_textarea.val(string+' '+text);
							}
						}

					} else {
						jqcc.cometchat.chatWith(id);
						var cometchat_user_textarea = $('#cometchat_user_'+id+'_popup').find('textarea.cometchat_textarea');
						cometchat_user_textarea.focus();
						string = cometchat_user_textarea.val();

						if (string.charAt(string.length-1) == ' ') {
							cometchat_user_textarea.val(string+text);
						} else {
							if (string.length == 0) {
								cometchat_user_textarea.val(text);
							} else {
								cometchat_user_textarea.val(string+' '+text);
							}
						}
						var tabcontenttext_height = ($(window).height()*40)/100;
                    	jqcc('#cometchat_tabcontenttext_'+id).css('height',tabcontenttext_height);
					}


				} else {
					if($('#cometchat_user_'+id+'_popup').length > 0) {
						var cometchat_user_textarea = $('#cometchat_user_'+id+'_popup').find('textarea.cometchat_textarea');
						cometchat_user_textarea.focus();
						jqcc.cometchat.chatWith(id);
						string = cometchat_user_textarea.val();

						if (string.charAt(string.length-1) == ' ') {
							cometchat_user_textarea.val(string+text);
						} else {
							if (string.length == 0) {
								cometchat_user_textarea.val(text);
							} else {
								cometchat_user_textarea.val(string+' '+text);
							}
						}
					} else {
						jqcc.cometchat.chatWith(id);
						var cometchat_user_textarea = $('#cometchat_user_'+id+'_popup').find('textarea.cometchat_textarea');
						cometchat_user_textarea.focus();
						string = cometchat_user_textarea.val();

						if (string.charAt(string.length-1) == ' ') {
							cometchat_user_textarea.val(string+text);
						} else {
							if (string.length == 0) {
								cometchat_user_textarea.val(text);
							} else {
								cometchat_user_textarea.val(string+' '+text);
							}
						}
					}
				}
			},
			getLanguage: function(id) {
				smilies_language =  <?php echo json_encode($smilies_language); ?>;
				if(typeof id==undefined){
					return smilies_language;
				}else{
					return smilies_language[id];
				}
			},
			loadsmilies: function(params){
				var extraQueryString = '';
				baseUrl = $.cometchat.getBaseUrl();
				baseData = $.cometchat.getBaseData();
				
				if(params.chatroommode==1){
					extraQueryString='&chatroommode=1';
				}

				loadPopupInChatbox(baseUrl+'plugins/smilies/index.php?id='+params.to+extraQueryString+'&basedata='+baseData+'&caller='+params.caller, 'smilies', 0, params.to, params.chatroommode);
			}
        };
    })();

})(jqcc);
