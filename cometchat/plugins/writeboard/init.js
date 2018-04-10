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

	$.ccwriteboard = (function () {

		var lastcall = 0;
		var height = <?php echo $writebHeight;?>;
		var width = <?php echo $writebWidth;?>;
		var mobileDevice = navigator.userAgent.match(/ipad|ipod|iphone|android|windows ce|Windows Phone|blackberry|palm|symbian/i);

        return {
			getTitle: function() {
				return $.ccwriteboard.getLanguage('title');
			},
			init: function (params) {
				if (jqcc.cometchat.membershipAccess('writeboard','plugins')){
					var id = params.to;
					var theme = '<?php echo $layout; ?>';
					if(typeof(params.windowMode) == "undefined") {
						params.windowMode = 0;
					} else {
						params.windowMode = 1;
					}
					var currenttime = new Date();
					currenttime = parseInt(currenttime.getTime()/1000);
					params.type = 1;
					params.force = 0;
					baseUrl = $.cometchat.getBaseUrl();
					baseData = $.cometchat.getBaseData();

					if (currenttime-lastcall > 10) {
						lastcall = currenttime;
						if(params.chatroommode == 1) {
							params.force = 1;
						} else {
							$.getJSON(baseUrl+'plugins/writeboard/index.php?action=request&callback=?', {to: id, basedata: baseData});
						}
						if(mobileDevice){
							params.windowMode = 1;
						}
						$.ccwriteboard.loadwriteboard(params);
					} else {
						alert($.ccwriteboard.getLanguage('wait_message'));
					}
				}
			},
			accept: function (params) {
				if (jqcc.cometchat.membershipAccess('writeboard','plugins')){
					params.type = 0;
					params.force = 0;
					baseUrl = $.cometchat.getBaseUrl();
					baseData = $.cometchat.getBaseData();

					if(typeof(params.windowMode) == "undefined") {
						params.windowMode = 0;
					} else {
						params.windowMode = 1;
					}
					$.getJSON(baseUrl+'plugins/writeboard/index.php?action=accept&callback=?', {to: params.to, basedata: baseData});
					if(mobileDevice){
						params.windowMode = 1;
					}
					$.ccwriteboard.loadwriteboard(params);
				}
			},
			getLanguage: function(id) {
				writeboard_language =  <?php echo json_encode($writeboard_language); ?>;
				if(typeof id==undefined){
					return writeboard_language;
				}else{
					return writeboard_language[id];
				}
			},
			loadwriteboard: function(params){
				if (jqcc.cometchat.membershipAccess('writeboard','plugins')){
					var extraQueryString = '';

					if(typeof(params.chatroommode) != "undefined" && params.chatroommode==1){
						extraQueryString="&chatroommode=1";
						if(typeof(params.random) != "undefined" && params.random!=''){
							extraQueryString+="&room="+params.random;
						}
					}

					baseUrl = $.cometchat.getBaseUrl();
					baseData = $.cometchat.getBaseData();
					if(mobileDevice){
						params.windowMode = 1;
					}
					loadCCPopup(baseUrl+'plugins/writeboard/index.php?action=writeboard&'+extraQueryString+'&to='+params.to+'&basedata='+baseData, 'writeboard',"status=0,toolbar=0,menubar=0,directories=0,resizable=1,location=0,status=0,scrollbars=0, width=<?php echo $writebWidth;?>,height=<?php echo $writebHeight;?>",width,height-50,$.ccwriteboard.getLanguage('collaborative_document'),params.force,0,1,1,params.windowMode);
				}
			}
        };
    })();
})(jqcc);

jqcc(document).ready(function(){
	jqcc('.accept_Write').live('click',function(){
		var to = jqcc(this).attr('to');
		var random = jqcc(this).attr('random');
		var chatroommode = jqcc(this).attr('chatroommode');
		var controlparameters = {"to":to, "random":random, "chatroommode":chatroommode};
		jqcc.ccwriteboard.accept(controlparameters);
	});
});
