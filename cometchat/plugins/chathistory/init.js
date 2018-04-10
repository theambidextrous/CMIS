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

	$.ccchathistory = (function () {

		var mobileDevice = navigator.userAgent.match(/ipad|ipod|iphone|android|windows ce|Windows Phone|IEMobile|blackberry|palm|symbian/i);
		var theme = '<?php echo $layout; ?>';
		var height = 480;
		var width = 650;

        return {
			getTitle: function() {
				return jqcc.ccchathistory.getLanguage('title');
			},
			init: function (params) {
				if(jqcc.cometchat.membershipAccess('chathistory','plugins')){
					params.callbackfn='';				
					if(typeof(jqcc.cometchat.getCcvariable) != "undefined"){
						if(jqcc.cometchat.getCcvariable().callbackfn=='desktop'){
							params.callbackfn='&callbackfn=desktop';
						}
					}
					if(typeof(params.windowMode) == "undefined") {
						params.windowMode = 0;
					} else {
						params.windowMode = 1;
					}
					if(theme == 'embedded'){
						height = $(document).height() - 100;
						width = 500;
					}
					if(mobileDevice){
						params.windowMode = 1;
					}
					jqcc.ccchathistory.loadchathistory(params);
				}
			},
			getLanguage: function(id) {
				chathistory_language =  <?php echo json_encode($chathistory_language); ?>;
				if(typeof id==undefined){
					return chathistory_language;
				}else{
					return chathistory_language[id];
				}
			},
			loadchathistory: function(params){
				var extraQueryString = '';
				if(typeof(params.chatroommode) != "undefined" && params.chatroommode == 1) {
					var extraQueryString = '&chatroommode=1';
				}
				
				baseUrl = $.cometchat.getBaseUrl();
				baseData = $.cometchat.getBaseData();
				if(mobileDevice){
					params.windowMode = 1;
				}
				loadCCPopup(baseUrl+'plugins/chathistory/index.php?embed=web'+extraQueryString+'&logs=1&history='+params.to+'&basedata='+baseData+params.callbackfn, 'chathistory',"status=0,toolbar=0,menubar=0,directories=0,resizable=0,location=0,status=0,scrollbars=1,width="+width+",height="+height,width,height,jqcc.ccchathistory.getLanguage('chat_history'),null,null,null,null,params.windowMode);
				
			}
        };
    })();

})(jqcc);
