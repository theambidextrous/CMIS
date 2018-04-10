<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/
if (file_exists(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."lang.php")) {
	include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."lang.php");
}

foreach ($broadcastmessage_language as $i => $l) {
	$broadcastmessage_language[$i] = str_replace("'", "\'", $l);
}
?>
if (typeof($) === 'undefined') {
	$ = jqcc;
}
if (typeof(jQuery) === 'undefined') {
	jQuery = jqcc;
}
$(document).ready(function() {
	var mobileDevice = navigator.userAgent.match(/ipad|ipod|iphone|android|windows ce|Windows Phone|blackberry|palm|symbian/i);
	if(mobileDevice){
		jqcc("#inviteuserboxes").css({'overflow':'hidden','overflow-y': 'auto', height: jqcc("#inviteuserboxes").css('height')});
	}else{
		jqcc("#inviteuserboxes").slimScroll({scroll: '1', height: jqcc("#inviteuserboxes").css('height')});
	}
	$(document).find("textarea.cometchat_textarea").on('paste input',function(event){
		if($(this).val().length > 380){
			$(this).height(70);
			$(this).slimScroll({scroll: '1'});
		}
	});
	$(document).find("textarea#cometchat_broadcastMessage_textarea").keyup(function(event){
		var container_height = $(this).parent().outerHeight();
		if (event.keyCode == 8 || event.keyCode == 46) {
			if($(document).find("textarea.cometchat_textarea").val()==""){
				if("<?php echo $layout; ?>" == 'embedded'){
					$(document).find("textarea#cometchat_broadcastMessage_textarea").css('height','30px');
					$(document).find("div.cometchat_tabcontentinput").css('height','30px');
				}else{
					$(document).find("textarea#cometchat_broadcastMessage_textarea").css('height','16px');
					$(document).find("div.cometchat_tabcontentinput").css('height','27px');
				}
				broadcastWindowResize();
			}
		}
		var newcontainerheight = $(this).parent().outerHeight();
		if(container_height!=(newcontainerheight)){
			$(document).find("div.inviteuserboxes").slimScroll({scroll:'1'});
		}
	});
	$(document).find("textarea.cometchat_textarea").keydown(function(event){
		broadcastBoxKeydown(event, this);
	});

	$('#cometchat_broadcastMessage_submit').live("click",function(e){
		e.stopPropagation();
		addbroadcastmsg(e);
	});

	$('.cometchat_tabcontentsubmit').live("click",function(e){
		if(!$('.cometchat_textarea').hasClass('placeholder')){
			e.stopPropagation();
			addbroadcastmsg(e);
		}
	});

	var userSelectionrunning = false;
	$('#cc_refreshbroadcastusers').live("click",function(e1){
		if(!userSelectionrunning){
			userSelectionrunning = true;
			e1.stopPropagation();
			cc_deselectallusers();
			$.ajax({
				url: "index.php?action=userSelection",
				dataType: 'jsonp',
				success: function (data) {
					userSelectionrunning = false;
					var buddyList = data.buddyList;
					var status = data.status;

					var s = [];
					s['available'] = '';
					s['away'] = '';
					s['busy'] = '';
					s['offline'] = '';

					$.each( buddyList, function( key, value ) {
						s[value.s] += '<div class="invite_1"><div class="invite_2" onclick="javascript:document.getElementById(\'check_'+value.id+'\').checked = document.getElementById(\'check_'+value.id+'\').checked?false:true;"><img class="useravatar" height=30 width=30 src="'+value.a+'" /></div><div class="invite_3" onclick="javascript:document.getElementById(\'check_'+value.id+'\').checked = document.getElementById(\'check_'+value.id+'\').checked?false:true;"><span class="invite_name">'+value.n+'</span><div class="cometchat_userscontentdot cometchat_user_'+value.s+'"></div><span class="invite_5">'+value.m+'</span></div><label class="cometchat_checkboxcontrol cometchat_checkboxouter"><input class="cometchat_checkbox" type="checkbox" name="to[]" value="'+value.id+'" id="check_'+value.id+'" class="invite_4" /><div class="cometchat_controlindicator"></div></label></div>';
					});

					var inviteContent = s['available']+""+s['away']+""+s['offline'];
					inviteContent = inviteContent.trim();
					if(inviteContent == '' || inviteContent ==null){
						inviteContent = '<div style= "padding-top:6px">'+'<?php echo $broadcastmessage_language[2]?>'+'</div>';
					}

					$(document).find('#inviteuserboxes').html('');
					$(document).find('#inviteuserboxes').html(inviteContent);

					if(!($(document).find("#ccbroadcastuserrel").is(':animated'))) {
						$(document).find("#ccbroadcastuserrel").fadeIn().delay( 500 ).fadeOut('slow');
					}else{
						$(document).find("#ccbroadcastuserrel").clearQueue();
					}
				},
				error: function(data){
					userSelectionrunning = false;
				}
			});
		}
	});

	$('#cometchat [placeholder]').focus(function() {
		var input = $(this);
		if (input.val() == input.attr('placeholder')) {
			input.val('');
			input.removeClass('placeholder');
		}
	}).blur(function() {
		var input = $(this);
		if (input.val() == '') {
			input.addClass('placeholder');
			input.val(input.attr('placeholder'));
		}
	}).blur();
	$('#cometchat [placeholder]').parents('form').submit(function() {
		$(this).find('[placeholder]').each(function() {
			var input = $(this);
			if (input.val() == input.attr('placeholder')) {
				input.val('');
			}
		});
	});

	$('#cometchat_broadcastsearchbar').find('#cometchat_broadcastsearch').keyup(function(){
		searchbroadcastusers();
	});

	broadcastWindowResize();

});

function broadcastBoxKeydown(event,chatboxtextarea) {
	var newheight = '';
	if (event.keyCode == 13 && event.shiftKey == 0 && !$(chatboxtextarea).hasClass('placeholder'))  {
		event.preventDefault();
		event.stopPropagation();
		addbroadcastmsg(event);
	}
	var adjustedHeight = chatboxtextarea.clientHeight;
	var maxHeight = 70;
	var minHeight;
	var theme = "<?php echo $layout; ?>";
	if(theme == 'embedded'){
		minHeight = 17;
		textcontainer_minheight = 52;
	}else{
		minHeight = 15;
	}
	var height = $(document).find("div.inviteuserboxes").css('height');
	var heightbody = $(document).find("body").css('height');
	var searchbarheight = 25;
	var cctopbar = 26;
	var difference = $(chatboxtextarea).innerHeight() - $(chatboxtextarea).height();
	var container_height = $(chatboxtextarea).parent().outerHeight();
	if ($(chatboxtextarea).innerHeight < chatboxtextarea.scrollHeight ) {

	} else if($(chatboxtextarea).height() < maxHeight || event.keyCode == 8) {
		if($('#cometchat_broadcastsearch').length>0){
			searchbarheight = $('#cometchat_broadcastsearch').outerHeight(true);
		}
		if($('.cc_broadcasttopbar').length>0) {
			cctopbar = $('.cc_broadcasttopbar').outerHeight(true);
		}
		minHeight = Math.round(minHeight);
		$(chatboxtextarea).height(minHeight);
		if("<?php echo $layout; ?>" == 'embedded'){
			$(chatboxtextarea).parent().height(Math.round(textcontainer_minheight));
		}else{
			$(document).find("div.slimScrollDiv").height(231);
			$(document).find("div.cometchat_tabcontentinput").height(231);
		}
		if(chatboxtextarea.scrollHeight - difference >= maxHeight){
			$(chatboxtextarea).height(maxHeight);
			newheight = parseInt(heightbody) - (parseInt(adjustedHeight) +searchbarheight +11 +cctopbar);
			$(document).find("div.inviteuserboxes").css('height', (newheight-16)+'px');
		$(document).find("div.slimScrollDiv").css('height', (newheight-17)+'px');
		}else if(chatboxtextarea.scrollHeight - difference>minHeight){
			$(chatboxtextarea).height(chatboxtextarea.scrollHeight - difference);
			newheight = parseInt(heightbody) - (parseInt(adjustedHeight) +searchbarheight +11 +cctopbar);
			$(document).find("div.inviteuserboxes").css('height', (newheight-16)+'px');
		$(document).find("div.slimScrollDiv").css('height', (newheight-17)+'px');
		}
		/*26 topbar, 11 textareapadding & 30 searchbar*/

		if("<?php echo $layout; ?>" == 'docked'){
			$(document).find("div.cometchat_tabcontentinput").css('height',chatboxtextarea.clientHeight+5);
		}else{
			$(document).find("div.cometchat_tabcontentinput").css('height',chatboxtextarea.clientHeight+8);
		}
		$(document).find("div.inviteuserboxes").css('height', (newheight-16)+'px');
		$(document).find("div.slimScrollDiv").css('height', (newheight-16)+'px');
	}else{
		$(chatboxtextarea).slimScroll({scroll: '1'});
		$(chatboxtextarea).focus();
		if("<?php echo $layout; ?>" == 'docked'){
			$(document).find("div.slimScrollDiv").css({'width':'230px'});
			$(chatboxtextarea).css('width','222px');
		}
	}
	broadcastWindowResize();
}

function confirmBroadcast() {
	if (confirm("<?php echo $broadcastmessage_language[9]?>") == true) {
		return true;
	} else {
		return false;
	}
}

function addbroadcastmsg(event) {
	var message = $('#cometchat_broadcastMessage_textarea').val().trim();
	var inviteids = Array();
	$(document).find('input[type="checkbox"]:checked').each(function(index){
		inviteids[index] = $(this).val();
	});
	if(inviteids.length < 1){
		alert("<?php echo $broadcastmessage_language[3]?>");
		return;
	}

	var toids = inviteids.join();
	if(message!=""&&message!=null && inviteids.length > 0){
		if(!confirmBroadcast()){
			return;
		}
		$(document).find("textarea.cometchat_textarea").val('');
		var controlparameters = {"type":"modules", "name":"cometchat", "method":"updateOfflinemessages", "params":{"ids":inviteids, "message":message}};
		controlparameters = JSON.stringify(controlparameters);
		if(typeof(parent) != 'undefined' && parent != null && parent != self){
			parent.postMessage('CC^CONTROL_'+controlparameters,'*');
		}else{
			window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');
		}
		var broadcastData = {};
		var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
		var eventer = window[eventMethod];
		var messageEvent = eventMethod == "attachEvent" ? "onmessage" : "message";
		eventer(messageEvent,function(e) {
    		if(typeof(e.data) != 'undefined' && typeof(e.data) == 'string') {
    			if(e.data.indexOf('CC^CONTROL_')!== -1){
    				var controlparameters = e.data.slice(11);
	                broadcastData = JSON.parse(controlparameters);
	                sendBroadcast(broadcastData);
    			}
    		}
    	},false);
	}

}

function sendBroadcast(broadcastData) {
	var addmsg = $('#cometchat_broadcastMessage_textarea').attr('addmsg');
	var caller = $('#cometchat_broadcastMessage_textarea').attr('caller');
	var basedata = "<?php echo $_REQUEST['basedata']?>";

	$.ajax({
		url: "index.php?action=sendbroadcast",
		data: {broadcastmessage: 1,broadcastData:broadcastData,basedata:basedata},
		dataType:'jsonp',
		success: function (data) {
			if(data != null && data != 'undefined'){
				$.each( data, function( key, value ) {
					var controlparameters = {"type":"modules", "name":"cometchat", "method":"addMessage", "params":{"from":parseInt(value.from), "message":value.m, "messageId":value.id, "nopopup":"1", "caller":caller, "localmessageid":value.localmessageid}};
					controlparameters = JSON.stringify(controlparameters);
					if(addmsg==1){
						if(typeof(parent) != 'undefined' && parent != null && parent != self){
							parent.postMessage('CC^CONTROL_'+controlparameters,'*');
						}else{
							window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');
						}
					}

					var controlparameters = {"type":"modules", "name":"cometchat", "method":"deleteOfflinemessages", "params":{"localmessageid":value.localmessageid}};
					controlparameters = JSON.stringify(controlparameters);
					if(typeof(parent) != 'undefined' && parent != null && parent != self){
						parent.postMessage('CC^CONTROL_'+controlparameters,'*');
					}else{
						window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');
					}
				});
			}
			$(document).find("textarea.cometchat_textarea").css('height','16px');
			broadcastWindowResize();
			$(document).find("#ccbroadcastsucc").fadeIn().delay( 500 ).fadeOut('slow');
			return;
		}
	});
}

function cc_selectallusers() {
	$('input::not(:checked)').each(function(index){
		if($(this).parent().css('display') == 'block') {
	      this.click();
	    }
		$('#cc_selectallusers').hide();
		$('#cc_deselectallusers').css('display','inline-block');
	});
}
function cc_deselectallusers() {
	$('input:checked').each(function(index){
		if($(this).parent().css('display') == 'block') {
	      this.click();
	    }
		$('#cc_selectallusers').css('display','inline-block');
		$('#cc_deselectallusers').hide();
	});
}

function searchbroadcastusers(){
	var searchString = $('#cometchat_broadcastsearchbar').find('#cometchat_broadcastsearch').val();
	var inviteuserboxes = $('.cometchat_broadcastMessage').find('#inviteuserboxes');
	var uncheckedusers = 0;
	var availableusers = 0;
	searchString = searchString.trim();
	if(searchString.length>=1&&searchString!="<?php echo $broadcastmessage_language[4]?>"){
		availableusers = inviteuserboxes.find('div.invite_1').hide().parent().find('.invite_name:icontains("'+searchString+'")').parent().parent().show().length;

		$('input::not(:checked)').each(function(index){
		    if($(this).parent().css('display') == 'block') {
		      uncheckedusers++;
		    }
		});
	}else{
		inviteuserboxes.find('div.invite_1').show();
		$('input::not(:checked)').each(function(index){
		    uncheckedusers++;
		});
	}
	if(uncheckedusers > 1) {
		$('#cc_selectallusers').css('display','inline-block');
		$('#cc_deselectallusers').hide();
	} else {
		$('#cc_deselectallusers').css('display','inline-block');
		$('#cc_selectallusers').hide();
	}

	if(availableusers < 1 && searchString.length>=1 && searchString!="<?php echo $broadcastmessage_language[4]?>") {
		$('#cc_selectallusers').hide();
		$('#cc_deselectallusers').hide();
		$('.cc_separator').hide();
		if($(document).find('#cometchat_nousers_found').length == 0){
			$(document).find('#inviteuserboxes').append('<div id="cometchat_nousers_found"><div class="chatmessage"><div class="search_nouser">'+'<?php echo $broadcastmessage_language[12]?>'+'</div></div></div></div>');
		}
	} else {
		$('.cc_separator').show();
		$(document).find('#inviteuserboxes').find('#cometchat_nousers_found').remove();
	}
}

function broadcastWindowResize() {
	var searchbarheight = 25;
	var cctopbar = 26;
	var chatboxtextarea = $(document).find("#cometchat_broadcastMessage_textarea");
	var heightbody = bmgetWindowHeight();
	var adjustedHeight = chatboxtextarea.outerHeight(false);
	var maxHeight = 70;
	if (maxHeight){
		adjustedHeight = Math.min(maxHeight, adjustedHeight);
	}
	if($('#cometchat_broadcastsearch').length>0){
		searchbarheight = $('#cometchat_broadcastsearch').outerHeight(true);
	}
	if($('.cc_broadcasttopbar').length>0) {
		cctopbar = $('.cc_broadcasttopbar').outerHeight(true);
	}
	var newheight = parseInt(heightbody) - (parseInt(adjustedHeight) +searchbarheight +8 +cctopbar);/*26 topbar, 8 textareapadding & 30 searchbar*/
	$(document).find("div.inviteuserboxes").css('height', newheight-5+'px');
	$(document).find("div.slimScrollDiv").css('height', (newheight-5)+'px');
}

function bmgetWindowHeight() {
	var windowHeight = 0;
	if (typeof(window.innerHeight) == 'number') {
		windowHeight = window.innerHeight;
	} else {
		if (document.documentElement && document.documentElement.clientHeight) {
			windowHeight = document.documentElement.clientHeight;
		} else {
			if (document.body && document.body.clientHeight) {
				windowHeight = document.body.clientHeight;
			}
		}
	}
	return windowHeight;
}
window.onresize = function(event) {
	broadcastWindowResize();
};
