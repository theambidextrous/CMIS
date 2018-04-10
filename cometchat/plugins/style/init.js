<?php
	if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php")) {
		include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php");
	}
?>

/*
 * CometChat
 * Copyright (c) 2016 Inscripts - support@cometchat.com | http://www.cometchat.com | http://www.inscripts.com
*/

(function($){

	$.ccstyle = (function () {

		var mobileDevice = navigator.userAgent.match(/ipad|ipod|iphone|android|windows ce|Windows Phone|IEMobile|blackberry|palm|symbian/i);

        return {

			getTitle: function() {
				return jqcc.ccstyle.getLanguage('title');
			},

			init: function (params) {
				if (jqcc.cometchat.membershipAccess('style','plugins')){
					if(typeof(params.windowMode) == "undefined") {
						params.windowMode = 0;
					} else {
						params.windowMode = 1;
					}
					if(mobileDevice){
						params.windowMode = 1;
					}
					jqcc.ccstyle.loadstyle(params);
				}
			},

			updatecolor: function (params) {
				var text = params.pattern;
				if (text != '' && text != null) {
					document.cookie = '<?php echo $cookiePrefix;?>chatroomcolor='+text+'; path=/';
				}
				$('#currentroom').find("textarea.cometchat_textarea").focus();
			},
			getLanguage: function(id) {
				style_language =  <?php echo json_encode($style_language); ?>;
				if(typeof id==undefined){
					return style_language;
				}else{
					return style_language[id];
				}
			},
			loadstyle: function(params){
				baseUrl = $.cometchat.getBaseUrl();
				baseData = $.cometchat.getBaseData();

				loadCCPopup(baseUrl+'plugins/style/index.php?id='+params.to+'&basedata='+baseData, 'style',"status=0,toolbar=0,menubar=0,directories=0,resizable=0,location=0,status=0,scrollbars=0, width=286,height=130",286,185,jqcc.ccstyle.getLanguage('select_color'),null,null,null,null,params.windowMode);
			}
        };
    })();

})(jqcc);
