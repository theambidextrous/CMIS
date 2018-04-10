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

	$.ccreport = (function () {

		var mobileDevice = navigator.userAgent.match(/ipad|ipod|iphone|android|windows ce|Windows Phone|IEMobile|blackberry|palm|symbian/i);

        return {

			getTitle: function() {
				return jqcc.ccreport.getLanguage('title');
			},

			init: function (params) {
				var id = params.to;
				if (jqcc.cometchat.membershipAccess('report','plugins')){				
					if(typeof(params.windowMode) == "undefined") {
						params.windowMode = 0;
					} else {
						params.windowMode = 1;
					}
					if(typeof(params.caller) == "undefined") {
						params.caller = '';
					}
					if ($("#cometchat_user_"+id+"_popup").find("div.cometchat_tabcontenttext").html() != '') {
						baseData = $.cometchat.getBaseData();
						baseUrl = $.cometchat.getBaseUrl();
						if (mobileDevice) {
							window.open(baseUrl+'plugins/report/index.php?id='+id+'&basedata='+baseData+'&callback=mobilewebapp');
						} else {
							jqcc.ccreport.loadreport(params);
						}
					} else {
						alert(jqcc.ccreport.getLanguage('empty_conversation'));
					}
				}
			},
			getLanguage: function(id) {
				report_language =  <?php echo json_encode($report_language); ?>;
				if(typeof id==undefined){
					return report_language;
				}else{
					return report_language[id];
				}
			},
			loadreport: function(params){
				var extraQueryString = '';
				if(jqcc.cometchat.getCcvariable().callbackfn=='desktop'){
					params.windowMode = 1;
					extraQueryString='&caller='+params.caller;
				}
				baseUrl = $.cometchat.getBaseUrl();
				baseData = $.cometchat.getBaseData();
				if(mobileDevice){
					windowMode = 1;
				}
				loadCCPopup(baseUrl+'plugins/report/index.php?id='+params.to+extraQueryString+'&basedata='+baseData, 'report',"status=0,toolbar=0,menubar=0,directories=0,resizable=0,location=0,status=0,scrollbars=0, width=430,height=220",430,175,jqcc.ccreport.getLanguage('reason'),0,0,0,0,params.windowMode);
			}
        };
    })();

})(jqcc);
