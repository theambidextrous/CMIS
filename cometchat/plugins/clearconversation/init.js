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

 	$.ccclearconversation = (function () {

 		var type = '<?php echo $isDelete;?>';

 		return {

 			getTitle: function() {
 				return jqcc.ccclearconversation.getLanguage('title');
 			},

			init: function (params) {
                if(jqcc.cometchat.membershipAccess('clearconversation','plugins')){
    				var id = params.to;
    				var chatroommode = params.chatroommode;
                    var parameter = {clearid: id};
                    if(type == 1) {
                        parameter = {deleteid: id};
                    }
     				if(chatroommode == 1) {
     					if($("#currentroom_convotext").length){
     						if ($("#currentroom_convotext").html() != '') {
     							baseUrl = $.cometchat.getBaseUrl();
     							basedata = $.cometchat.getBaseData();
                                var lastid = parseInt($('#currentroom_convotext').find('.cometchat_chatboxmessage:last').attr('id').replace('cometchat_groupmessage_',''));
                                parameter.lastid = lastid;
     							$.getJSON(baseUrl+'plugins/clearconversation/index.php?action=clear&basedata='+basedata+'&chatroommode=1&callback=?', parameter);
     							$("#currentroom_convotext").html('');
     						}
     					}else{
     						if ($("#cometchat_grouptabcontenttext_"+id).html() != '') {
     							baseUrl = $.cometchat.getBaseUrl();
     							basedata = $.cometchat.getBaseData();
     							var lastid = parseInt($('#cometchat_grouptabcontenttext_'+id).find('.cometchat_chatboxmessage:last').attr('id').replace('cometchat_groupmessage_',''));
     							parameter.lastid = lastid;
                                $.getJSON(baseUrl+'plugins/clearconversation/index.php?action=clear&basedata='+basedata+'&chatroommode=1&callback=?', parameter);
                                $("#cometchat_grouptabcontenttext_"+id).find('.cometchat_ts').remove();
                                $("#cometchat_grouptabcontenttext_"+id).find('.cometchat_chatboxmessage').remove();
                                $("#cometchat_grouptabcontenttext_"+id).find('.cometchat_time').remove();
     						}
     					}
     				} else {
     					var settings = jqcc.cometchat.getSettings();
     					if ($("#cometchat_user_"+id+"_popup").find("div.cometchat_tabcontenttext").html() != '') {
     						baseUrl = $.cometchat.getBaseUrl();
     						baseData = $.cometchat.getBaseData();
                            parameter.basedata = (typeof(baseData) == undefined) ? '' : baseData;
     						$.getJSON(baseUrl+'plugins/clearconversation/index.php?action=clear&callback=?', parameter);
     						
                            if($("#cometchat_user_"+id+"_popup").find("div.cometchat_tabcontenttext > div.cometchat_chatboxmessage").length == 0){
     							$("#cometchat_user_"+id+"_popup").find("div.cometchat_tabcontenttext > div.cometchat_message_container > div.cometchat_messagebox").remove();
     							$("#cometchat_user_"+id+"_popup").find("div.cometchat_tabcontenttext > div.cometchat_time").remove();
     							$("#cometchat_user_"+id+"_popup").find("div.cometchat_tabcontenttext > div.cometchat_message_container > div.cometchat_time").remove();
                                $("#cometchat_user_"+id+"_popup").find("div.cometchat_tabcontenttext > span.cometchat_sentnotification").remove();
     						}else{
     							$("#cometchat_user_"+id+"_popup").find("div.cometchat_tabcontenttext > div.cometchat_chatboxmessage").remove();
     							$("#cometchat_user_"+id+"_popup").find("div.cometchat_tabcontenttext > table.cometchat_iphone").remove();
     							$("#cometchat_user_"+id+"_popup").find("div.cometchat_tabcontenttext > div.cometchat_time").remove();
     							$("#cometchat_user_"+id+"_popup").find("div.cometchat_tabcontenttext > div.cometchat_message_container > div.cometchat_time").remove();
                                $("#cometchat_user_"+id+"_popup").find("div.cometchat_tabcontenttext > span.cometchat_sentnotification").remove();
     						}
     					}
                    }
 				}
 			},

            getLanguage: function(id) {
                clearconversation_language =  <?php echo json_encode($clearconversation_language); ?>;
                if(typeof id==undefined){
                    return clearconversation_language;
                }else{
                    return clearconversation_language[id];
                }
            }
 		};
 	})();

 })(jqcc);
