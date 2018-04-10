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

	$.ccwhiteboard = (function () {

		var lastcall = 0;
		var height = <?php echo $whitebHeight;?>;
		var width = <?php echo $whitebWidth;?>;
		var mobileDevice = navigator.userAgent.match(/ipad|ipod|iphone|android|windows ce|Windows Phone|blackberry|palm|symbian/i);
        return {

			getTitle: function() {
				return $.ccwhiteboard.getLanguage('title');
			},

			init: function (params) {
				if (jqcc.cometchat.membershipAccess('whiteboard','plugins')){
					var to = params.to;
					params.force = 0;
					if(typeof(params.windowMode) == "undefined") {
						params.windowMode = 0;
					} else {
						params.windowMode = 1;
					}

					var currenttime = new Date();
					currenttime = parseInt(currenttime.getTime()/1000);
					lastcall = currenttime;
					if (10 > (currenttime-lastcall)) {
						params.random = currenttime;

						if(params.chatroommode == 1){
							params.force = 1;
						}
						if(mobileDevice){
							params.windowMode = 1;
						}
						jqcc.ccwhiteboard.loadwhiteboard(params);

					} else {
						alert(jqcc.ccwhiteboard.getLanguage('wait_message'));
					}
				}
			},
			loadwhiteboard: function(params){
				if (jqcc.cometchat.membershipAccess('whiteboard','plugins')){
					var to = params.to;
					var extraQueryString = "&random="+params.random;
					if(params.hasOwnProperty('room')) {
						extraQueryString += "&room="+params.room;
					}
					if(params.chatroommode==1){
						extraQueryString +="&chatroommode=1&subaction=request";
					}
					baseUrl = $.cometchat.getBaseUrl();
					baseData = $.cometchat.getBaseData();
					if(mobileDevice){
						params.windowMode = 1;
					}
					loadCCPopup(baseUrl+'plugins/whiteboard/index.php?action=whiteboard&to='+to+extraQueryString+'&basedata='+baseData, 'whiteboard',"status=0,toolbar=0,menubar=0,directories=0,resizable=1,location=0,status=0,scrollbars=0, width="+width+",height="+height,width,height-50,jqcc.ccwhiteboard.getLanguage('whiteboard'),params.force,0,1,1,params.windowMode);
				}
			},
			accept: function (params) {
				if (jqcc.cometchat.membershipAccess('whiteboard','plugins')){
					var to = params.to;
					params.random = 0;
					params.chatroommode = 0;
					params.force = 0;

					if(typeof(params.windowMode) == "undefined") {
						params.windowMode = 0;
					} else {
						params.windowMode = 1;
					}
					baseUrl = $.cometchat.getBaseUrl();
					baseData = $.cometchat.getBaseData();
					$.getJSON(baseUrl+'plugins/whiteboard/index.php?action=accept&callback=?', {to: to, basedata: baseData});
					if(!mobileDevice){
						jqcc.ccwhiteboard.loadwhiteboard(params);
					} else{
						params.windowMode = 1;
						jqcc.ccwhiteboard.loadwhiteboard(params);
					}
				}
			},
			getLanguage: function(id) {
				whiteboard_language =  <?php echo json_encode($whiteboard_language); ?>;
				if(typeof id==undefined){
					return whiteboard_language;
				}else{
					return whiteboard_language[id];
				}
			}
		};
    })();

})(jqcc);

jqcc(document).ready(function(){
	jqcc('.accept_White').live('click',function(){
		var to = jqcc(this).attr('to');
		var random = jqcc(this).attr('random');
		var room = jqcc(this).attr('room');
		var chatroommode = jqcc(this).attr('chatroommode');
		var controlparameters = {"to":to, "random":random, "room":room, "chatroommode":chatroommode};
		jqcc.ccwhiteboard.accept(controlparameters);
	});
});
