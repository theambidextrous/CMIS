/*
 * CometChat
 * Copyright (c) 2016 Inscripts - support@cometchat.com | http://www.cometchat.com | http://www.inscripts.com
*/

$(function() {
	var mobileDevice = navigator.userAgent.match(/cc_ios|cc_android|ipad|ipod|iphone|android|windows ce|Windows Phone|blackberry|palm|symbian/i);
	if (jQuery().slimScroll && !mobileDevice) {
		var height = $('.cometchat_wrapper').height();
		$('.announcements').slimScroll({height: '100%',allowPageScroll: false});
		$(".announcements").css("height","100%");
	} else{
		$(".announcements").css("height","100%");
	}

	jqcc('.chattime').each(function(key,value){
		var ts = jqcc(this).attr('timestamp');
		jqcc(this).html(ts);
	});
	jqcc( ".chattime" ).mouseover(function() {
  		var ts = jqcc(this).attr('timehover');
		jqcc(this).html(ts);
	});
	jqcc( ".chattime" ).mouseleave(function() {
  		var ts = jqcc(this).attr('timestamp');
		jqcc(this).html(ts);
	});

});
