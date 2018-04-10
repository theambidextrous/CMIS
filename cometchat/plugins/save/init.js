<?php

if (file_exists(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR."chatrooms".DIRECTORY_SEPARATOR."lang.php")) {
    include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR."chatrooms".DIRECTORY_SEPARATOR."lang.php");
}

foreach ($chatrooms_language as $i => $l) {
    $chatrooms_language[$i] = str_replace("'", "\'", $l);
}

if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php")) {
    include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php");
}
?>

/*
* CometChat
* Copyright (c) 2016 Inscripts - support@cometchat.com | http://www.cometchat.com | http://www.inscripts.com
*/

(function($){
    $.ccsave = (function () {
        return {

            getTitle: function() {
                return jqcc.ccsave.getLanguage('title');
            },

            init: function (params) {
                if (!(jqcc.cometchat.membershipAccess('save','plugins'))){
                    return;
                }
                var id = params.to;
                var chatroommode = params.chatroommode;
                var currentTime = new Date();
                var currentTimestamp = currentTime.getTime();
                var monthNames = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "July", "Aug", "Sep", "Oct", "Nov", "Dec" ];
                var month = currentTime.getMonth();
                var day = currentTime.getDate();
                var year = currentTime.getFullYear();
                var type = 'th';
                if(day==1||day==21||day==31){
                    type = 'st';
                }else if(day==2||day==22){
                    type = 'nd';
                }else if(day==3||day==23){
                    type = 'rd';
                }

                var today = monthNames[month] + " " + day + type + " " + year;
                var hour    = currentTime.getHours();
                var min     = currentTime.getMinutes();
                var ap = hour>11 ? "pm" : "am";
                hour = hour==0 ? 12 : hour>12 ? hour-12 : hour;
                hour = hour<10 ? "0"+hour : hour;
                min = min<10 ? "0"+min : min;
                var savedTime = hour+":"+min+ap;

                baseUrl = $.cometchat.getBaseUrl();
                baseData = $.cometchat.getBaseData();
                var settings = {};

                if(typeof(jqcc.cometchat.getSettings) != "undefined") {
                    settings = jqcc.cometchat.getSettings();
                } else if(typeof(parent.jqcc.cometchat.getSettings) != "undefined") {
                    settings = parent.jqcc.cometchat.getSettings();
                }

                var stickerMessage = '<div style="display:none">'+jqcc.ccsave.getLanguage('sticker_received')+'</div>';
                var stickerSelfMessage = '<div style="display:none">'+jqcc.ccsave.getLanguage('sticker_sent')+'</div>';
                if(chatroommode == 1) {
                    var roomname = params.roomname;
                    if (($("#currentroom_convotext").find('.cometchat_messagebox').length >0) || ($('#cometchat_group_'+id+'_popup').find('.cometchat_chatboxmessage').length > 0)) {
                        var filename = 'Conversation in '+roomname+' chatroom saved on '+today+' at '+savedTime;
                        $("#currentroom").find("span.cometchat_chatboxmessagefrom").after('<div class="cc_newline" style="display:none;">\n<\div>');
                        $('div.cometchat_chatboxmessage').find('img.cometchat_smiley').each(function(key,value){
                            $(this).before('<div class="cc_newline_smile"  style="display:none">('+$(this).attr('title')+')<\div>');
                        });
                        $('div.cometchat_chatboxmessage').find("img.cometchat_stickerImage").each(function(key,value){
                            $(this).before(stickerSelfMessage);
                        });
                        var deletemsg = '<?php echo $chatrooms_language[46];?>';
                        deletemsg ="\\(" + deletemsg + "\\)";

                        var content = '';
                        if (settings.theme == 'docked') {
                            $("#cometchat_grouptabcontenttext_"+id).find(".cometchat_ts_margin").each(function(i,d){
                                time = $(d).next('.cometchat_ts').text();
                                if(time == ""){
                                    time = $(d).next('.cometchat_ts_other').text();
                                }

                                var me = $(d).prev().find('img').attr('title');
                                 if(typeof(me) == "undefined"){
                                    me = '\n<?php echo $chatrooms_language[6];?>:';
                                }else{
                                    me =  "\n"+me+":";
                                }

                                data_msg = $(d).find('.cometchat_msg').text();

                                if(me != '\n<?php echo $chatrooms_language[6];?>'+jqcc.ccsave.getLanguage('title')+':' && data_msg == jqcc.ccsave.getLanguage('sticker_sent')){
                                        data_msg = jqcc.ccsave.getLanguage('sticker_received');
                                    }

                                var msg_day = $(d).parent().prev('.cometchat_time:visible').attr('msg_format');
                                if(typeof(msg_day) == "undefined"){
                                    msg_day = '';
                                }else{
                                    msg_day =  "\n\n"+msg_day+":\n";
                                }

                                $(d).each(function(j,data){
                                    content += msg_day+me+' '+data_msg +' ('+time+')';
                                });
                            });
                        }else if(settings.theme == 'embedded'){
                              $("#currentroom_convotext").find('.cometchat_messagebox').each(function(i,d){
                                time = $(d).find('.cometchat_ts').text();
                                data_msg = $(d).find('.cometchat_chatboxmessagecontent').text();

                                var me = $(d).find('.cometchat_cr_other_avatar img').attr('title');
                                 if(typeof(me) == "undefined"){
                                    me = '\n<?php echo $chatrooms_language[6];?>:';
                                }else{
                                    me =  "\n"+me+":";
                                }

                                if(me != '\n<?php echo $chatrooms_language[6];?>:' && data_msg == jqcc.ccsave.getLanguage('sticker_sent')){
                                    data_msg = jqcc.ccsave.getLanguage('sticker_received');
                                }

                                if($(d).prev().prev().css('display') != 'none'){
                                    var msg_day = $(d).prev().prev().attr('msg_format');
                                    msg_day =  "\n\n"+msg_day+':\n';
                                }else{
                                   var msg_day = '';
                                }

                                if(data_msg == '' || data_msg == null || (!data_msg.trim())){
                                    if($(d).find('.cometchat_chatboxmessagecontent img').hasClass('cometchat_smiley')){
                                        $(d).find('.cometchat_chatboxselfmedia .cometchat_smiley').each(function(j,sm){
                                            data_msg += $(sm).attr('title');
                                        });

                                        $(d).find('.cometchat_chatboxmedia .cometchat_smiley').each(function(j,sm){
                                            data_msg += $(sm).attr('title');
                                        });

                                    }else if($(d).find('.cometchat_chatboxmessagecontent img').hasClass('cometchat_stickerImage')){
                                        $(d).find('.cometchat_chatboxselfmedia .cometchat_stickerImage').each(function(j,sm){
                                            data_msg = jqcc.ccsave.getLanguage('sticker_sent');
                                        });

                                        $(d).find('.cometchat_chatboxmedia .cometchat_stickerImage').each(function(j,sm){
                                            data_msg = jqcc.ccsave.getLanguage('sticker_received');
                                        });
                                    }
                                    content += msg_day+me+data_msg +' ('+time+')';
                                }else{
                                   $(d).each(function(j,data){
                                    content += msg_day+me+' '+data_msg +' ('+time+')';
                                });
                                }

                            });

                        }
                        $('div.cc_newline').remove();
                        $('div.cc_newline_smile').remove();
                        $('#cc_saveconvochatroom').remove();

                        setTimeout(function(){
                            $('<form id = "ccsaveform" action="" method="post">'+
                                '<input type="hidden" name="roomname" />'+
                                '<input type="hidden" name="content" />'+
                                '<input type="hidden" name="filename" />'+
                                '</form>').appendTo('body');
                            var form = $('#ccsaveform');
                            form.attr('action',baseUrl+'plugins/save/index.php?id='+roomname+'&basedata='+baseData);
                            form.find('input[name=roomname]').val(roomname);
                            form.find('input[name=content]').val(content);
                            form.find('input[name=filename]').val(filename);
                            form.submit();
                        },50);
                    } else {
                        alert(jqcc.ccsave.getLanguage('log_empty'));
                    }
                } else {
                    var cometchat_user_popup = $("#cometchat_user_"+id+"_popup");
                    if (cometchat_user_popup.find('.cometchat_chatboxmessage').length > 0) {
                        var username = $.cometchat.getName(id);
                        var filename = 'Conversation with '+username+' saved on '+today+' at '+savedTime;
                        var settings = jqcc.cometchat.getSettings();
                        if(settings.theme == 'docked'){
                            $('div.cometchat_chatboxmessage').find("img.cometchat_smiley").each(function(key,value){
                                $(this).before('<div class="cc_newline_smile"  style="display:none">('+$(this).attr('title')+')</div>');
                                });

                            $('div.cometchat_chatboxmessage').find("img.cometchat_stickerImage").each(function(key,value){
                                if($(this).parent().hasClass('cometchat_self_msg')){
                                    $(this).before(stickerSelfMessage);
                                } else {
                                    $(this).before(stickerMessage);
                                }
                            });
                            var content = '';
                            cometchat_user_popup.find('.cometchat_chatboxmessage').each(function(i,d){
                                time = $(d).find('.cometchat_ts').text();
                                if(time == ""){
                                    time = $(d).find('.cometchat_ts_other').text();
                                }

                                data_msg = $(d).find('.cometchat_ts_margin').text();
                                var me = $(d).find('.cometchat_chatboxmessagefrom').text().trim();
                                var me = $(d).find("a.cometchat_floatL img").attr('title');
                                if(typeof(me) == "undefined"){
                                    me = '\nMe: ';
                                }else{
                                     me =  "\n"+me+': ';
                                }

                                var msg_day = $(d).prev('.cometchat_time:visible').attr('msg_format');

                                if(typeof(msg_day) == "undefined"){
                                    msg_day = '';
                                }else{
                                     msg_day =  "\n"+msg_day+':\n';
                                }

                                $(d).find('.cometchat_ts_margin').not('.cometchat_self_msg').each(function(j,data){
                                    content += msg_day+me+' '+ data_msg +' ('+time+')';
                                });
                                $(d).find('.cometchat_ts_margin.cometchat_self_msg').each(function(j,data){
                                    if(data_msg == jqcc.ccsave.getLanguage('sticker_received')){
                                        data_msg = jqcc.ccsave.getLanguage('sticker_sent');
                                    }
                                    content += msg_day+me+' '+ data_msg +' ('+time+')';
                                });
                            });
                            $('div.cc_newline_smile').remove();
                            $('iframe.cc_saveconvoframe').remove();
                            $('#cc_saveconvochatroom').remove();
                        }else if(settings.theme == 'embedded'){
                            $('div.cometchat_chatboxmessage').find("img.cometchat_smiley").each(function(key,value){
                                $(this).before('<div class="cc_newline_smile"  style="display:none">('+$(this).attr('title')+')</div>');
                            });

                            $('div.cometchat_chatboxmessage').find("img.cometchat_stickerImage").each(function(key,value){
                                if($(this).parent().hasClass('cometchat_self_msg')){
                                    $(this).before(stickerSelfMessage);
                                } else {
                                    $(this).before(stickerMessage);
                                }
                            });
                            var username = cometchat_user_popup.find(".cometchat_username").text();
                            var content = '';
                            cometchat_user_popup.find('.cometchat_messagebox').each(function(i,d){
                                time = $(d).find('.cometchat_ts').text();

                                data_msg = $(d).find('.cometchat_chatboxmessage .cometchat_chatboxmessagecontent').text();

                                var me = $(d).find(".cometchat_other_avatar img").attr('src');

                                if(typeof(me) == "undefined" || me == ''){
                                    me = '\nMe: ';
                                }else{
                                   me =  "\n"+username+': ';
                               }

                               if($(d).prev('.cometchat_time').css('display') != 'none'){
                                 var msg_day = $(d).prev('.cometchat_time').attr('msg_format');
                                 msg_day =  "\n\n"+msg_day+':\n';
                               }else{
                                var msg_day = '';
                               }

                           if(data_msg == ''){
                            if($(d).find('.cometchat_chatboxmessagecontent img').hasClass('cometchat_smiley')){
                                $(d).find('.cometchat_chatboxselfmedia .cometchat_smiley').each(function(j,sm){
                                    data_msg += ' '+$(sm).attr('title');
                                });

                                $(d).find('.cometchat_chatboxmedia .cometchat_smiley').each(function(j,sm){
                                    data_msg += ' '+$(sm).attr('title');
                                });

                            }else if($(d).find('.cometchat_chatboxmessagecontent img').hasClass('cometchat_stickerImage')){
                                $(d).find('.cometchat_chatboxselfmedia .cometchat_stickerImage').each(function(j,sm){
                                    data_msg = jqcc.ccsave.getLanguage('sticker_sent');
                                });

                                $(d).find('.cometchat_chatboxmedia .cometchat_stickerImage').each(function(j,sm){
                                    data_msg = jqcc.ccsave.getLanguage('sticker_received');
                                });
                            }
                        }
                            content += msg_day+me+' '+ data_msg +' ('+time+')';
                       });
                            $('div.cc_newline_smile').remove();
                            $('iframe.cc_saveconvoframe').remove();
                            $('#cc_saveconvochatroom').remove();
                        }
                        var iframe = $('<iframe id="cc_saveconvoframe'+id+'" class="cc_saveconvoframe" frameborder="0" style="width: 1px; height: 1px; display: none;"></iframe>').appendTo('body');
                        setTimeout(function(){
                            var formHTML = '<form action="" method="post">'+
                            '<input type="hidden" name="username" />'+
                            '<input type="hidden" name="content" />'+
                            '<input type="hidden" name="filename" />'+
                            '</form>';
                            var body = (iframe.prop('contentDocument') !== undefined) ?
                            iframe.prop('contentDocument').body :
                            iframe.prop('document').body;
                            body = $(body);
                            body.html(formHTML);
                            var form = body.find('form');
                            form.attr('action',baseUrl+'plugins/save/index.php?id='+id+'&basedata='+baseData);
                            form.find('input[name=username]').val(username);
                            form.find('input[name=content]').val(content);
                            form.find('input[name=filename]').val(filename);
                            form.submit();
                        },50);
                    } else {
                        alert(jqcc.ccsave.getLanguage('log_empty'));
                    }
                }
            },
            getLanguage: function(id) {
                save_language =  <?php echo json_encode($save_language); ?>;
                if(typeof id==undefined){
                    return save_language;
                }else{
                    return save_language[id];
                }
            }
        };
    })();
})(jqcc);
