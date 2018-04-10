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

	$.cchandwrite = (function () {

		var request = {};
		var count = 0;
		var calleeAPI = "<?php echo 'cc'.$layout; ?>";
		var mobileDevice = navigator.userAgent.match(/ipad|ipod|iphone|android|windows ce|Windows Phone|blackberry|palm|symbian/i);
		var theme = "<?php echo $layout;?>";

        return {

			getTitle: function() {
				return jqcc.cchandwrite.getLanguage('title');
			},

			init: function (params) {
					if (jqcc.cometchat.membershipAccess('handwrite','plugins')){
						var id = params.to;
						baseUrl = $.cometchat.getBaseUrl();
						baseData = $.cometchat.getBaseData();
						params.openinchatbox = 0;

						if(typeof(params.windowMode) == "undefined") {
							params.windowMode = 0;
						} else {
							params.windowMode = 1;
						}

						if(mobileDevice) {
							params.windowMode = 1;
						} else if(params.chatroommode == 1 && mobileDevice == null) {
							if(theme != 'embedded'){
								params.openinchatbox = 1;
								$('#cometchat_group_'+id+'_popup').find('#cometchat_groupplugins_openup_icon_'+id).removeClass('cometchat_pluginsopenup_arrowrotate');
							}
						} else {
							if(theme != 'embedded'){
								params.openinchatbox = 1;
								$('#cometchat_user_'+id+'_popup').find('#cometchat_plugins_openup_icon_'+id).removeClass('cometchat_pluginsopenup_arrowrotate');
							}
						}
						jqcc.cchandwrite.loadhandwrite(params);
				}
			},
			getLanguage: function(id) {
				handwrite_language =  <?php echo json_encode($handwrite_language); ?>;
				if(typeof id==undefined){
					return handwrite_language;
				}else{
					return handwrite_language[id];
				}
			},
			loadhandwrite: function(params){
				var extraQueryString = '';
				var sendername = '';
				baseUrl = $.cometchat.getBaseUrl();
				baseData = $.cometchat.getBaseData();
				
				if(params.chatroommode==1){
					extraQueryString = '&chatroommode=1';
					sendername = '&sendername='+params.roomname;
				} else {
					sendername = '&sendername='+jqcc.cometchat.getName(jqcc.cometchat.getThemeVariable('userid'));
				}

				if(params.openinchatbox == 1){
					loadPopupInChatbox(baseUrl+'plugins/handwrite/index.php?id='+params.to+extraQueryString+'&basedata='+baseData+sendername, 'handwrite', 0, params.to, params.chatroommode);

				} else {
					loadCCPopup(baseUrl+'plugins/handwrite/index.php?id='+params.to+extraQueryString+'&basedata='+baseData+sendername, 'handwrite',"status=0,toolbar=0,menubar=0,directories=0,resizable=1,location=0,status=0,scrollbars=0, width=330,height=250",330,250,jqcc.cchandwrite.getLanguage('title'),0,1,1,1,params.windowMode);
				}
			}
        };
    })();
})(jqcc);
