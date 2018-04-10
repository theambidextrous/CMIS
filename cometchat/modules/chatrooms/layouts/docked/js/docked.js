<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/
?>

if(typeof(jqcc) === 'undefined') {
    jqcc = jQuery;
}

if(typeof($) === 'undefined') {
    $ = jqcc;
}

(function($) {
    var settings = {};
    settings = jqcc.cometchat.getcrAllVariables();
    var calleeAPI = jqcc.cometchat.getChatroomVars('calleeAPI');
    var baseUrl = jqcc.cometchat.getBaseUrl();
    var staticCDNUrl = '<?php echo STATIC_CDN_URL; ?>';
    var tabWidth = 'width: 50%;right: 0;';
    var chromeReorderFix = '_';
    var newmess;
    var newmesscr;
    var chatboxDistance = 10;
    var chatboxHeight = parseInt('<?php echo $chatboxHeight; ?>');
    var chatboxWidth = parseInt('<?php echo $chatboxWidth; ?>');
    var mobileDevice = navigator.userAgent.match(/ipad|ipod|iphone|android|windows ce|Windows Phone|blackberry|palm|symbian/i);
    var iOSmobileDevice = navigator.userAgent.match(/ipad|ipod|iphone/i);
    var showUsername = "<?php echo $showUsername; ?>";
    $.crdocked = (function() {
        return {
                chatroomInit: function(){

                    $('.create_value').find('#name').on('focus', function() {
                        document.body.scrollTop = $(this).offset().top;
                    });

                    jqcc.cometchat.chatroomHeartbeat();
                    $('.cometchat_noactivity').css('display','none');
                },
                createChatroomPopup:function(){
                    var createChatroom = '<div><div class="content_div" id="create"><div id="cometchat_createchatroom" class="content_div"><form class="create" onsubmit="javascript:jqcc.cometchat.createChatroomSubmit(); return false;"><div style="clear:both;padding-top:10px"></div><div class="create_value"><input type="text" id="name" class="create_input" placeholder="<?php echo $chatrooms_language[27];?>" /></div><div class="password_hide" style="clear:both;padding-top:10px"></div><div class="create_value password_hide"><input id="cometchat_chatroom_password" type="password" autocomplete="off" class="create_input" placeholder="<?php echo $chatrooms_language[32];?>" /></div><div style="clear:both;padding-top:10px"></div><div class="create_value" ><select id="type" onchange="jqcc[\''+calleeAPI+'\'].crcheckDropDown(this)" class="create_options"><option value="0"><?php echo $chatrooms_language[29];?></option><option value="1"><?php echo $chatrooms_language[30];?></option><option value="2"><?php echo $chatrooms_language[31];?></option></select></div><div class="create_value"><input type="submit" class="cometchat_createroombutton" value="<?php echo $chatrooms_language[33];?>" /></div></form></div></div></div>';
                    var createchatroompopup = '<div id="cometchat_createchatroom_popup" class="cometchat_tabpopup cometchat_tabhidden"><div class="cometchat_userstabtitle"><div class="cometchat_userstabtitletext" style="width: 88%;text-align: center;"><?php echo $chatrooms_language[105];?></div><div class="cometchat_closebox cometchat_tooltip" id="cometchat_minimize_createchatroompopup" title="Close"></div></div><div id="cometchat_createchatroom_content" class="cometchat_tabcontent cometchat_optionstyle" style="overflow:hidden;">'+createChatroom+'</div></div>';

                    jqcc('#cometchat_sidebar').append(createchatroompopup);
                    jqcc('#cometchat').find('#cometchat_userstab_popup').removeClass('cometchat_tabopen').addClass('cometchat_tabhidden');
                    jqcc('#cometchat_newcompose_option').remove();
                    jqcc('#cometchat').find('#cometchat_createchatroom_popup').removeClass('cometchat_tabhidden').addClass('cometchat_tabopen');

                    jqcc('#cometchat_minimize_createchatroompopup').click(function(e){
                        jqcc('#cometchat').find('#cometchat_createchatroom_popup').removeClass('cometchat_tabopen').addClass('cometchat_tabhidden');
                        jqcc('#cometchat').find('#cometchat_userstab_popup').removeClass('cometchat_tabhidden').addClass('cometchat_tabopen');
                        jqcc('#cometchat').find('#cometchat_createchatroom_popup').remove();
                    });
                },
                chatroomTab: function(){
                },
                chatroomOffline: function(){
                    jqcc.cometchat.leaveChatroom();
                },
                playsound: function(type) {
                    try {
                        if(type == 1){
                            document.getElementById('messageOpenBeep').play();
                        }else{
                            document.getElementById('messageBeep').play();
                        }
                    } catch (error) {
                        jqcc.cometchat.setChatroomVars('messageBeep',0);
                    }
                },
                sendChatroomMessage: function(chatboxtextarea) {
                    $(chatboxtextarea).val('');
                    if($('#cometchat_container_smilies').length != 1 && mobileDevice && !iOSmobileDevice) {
                        $[calleeAPI].chatroomWindowResize();
                    }
                    $(chatboxtextarea).focus();
                },
                createChatroom: function() {
                    $('#createtab').addClass('tab_selected');
                    $('#create').css('display','block');
                    $('div.welcomemessage').html("<?php echo $chatrooms_language['create_a_chatroom'];?>");
                },
                getTimeDisplay: function(ts,id) {
                    var time = getTimeDisplay(ts);
                    if(ts < jqcc.cometchat.getChatroomVars('todays12am')) {
                            return time.hour+":"+time.minute+time.ap+" "+time.date+time.type+" "+time.month;
                    } else {
                            return time.hour+":"+time.minute+time.ap;
                    }
                },
                deletemessage: function(delid) {
                    $("#cometchat_groupmessage_"+delid).remove();
                },
                addChatroomMessage: function(incoming){
                    var fromid = incoming.fromid,
                        incomingmessage = jqcc.cometchat.htmlEntities(incoming.message),
                        incomingid = incoming.id,
                        sent = incoming.sent,
                        fromname = incoming.from,
                        calledfromsend = incoming.calledfromsend,
                        chatroomid = incoming.roomid,
                        incomingself = 1,
                        todaysdate = new Date(),
                        tdmonth  = todaysdate.getMonth(),
                        tddate  = todaysdate.getDate(),
                        tdyear = todaysdate.getFullYear(),
                        today_date_class = tdmonth+"_"+tddate+"_"+tdyear,
                        ydaysdate = new Date((new Date()).getTime() - 3600000 * 24),
                        ydmonth  = ydaysdate.getMonth(),
                        yddate  = ydaysdate.getDate(),
                        ydyear = ydaysdate.getFullYear(),
                        yday_date_class = ydmonth+"_"+yddate+"_"+ydyear,
                        d = '',
                        month = '',
                        date  = '',
                        year = '',
                        msg_date_class = '',
                        msg_date = '',
                        date_class = '',
                        msg_date_format = '',
                        cometchat_del_style = '',
                        localmessageid = '',
                        cc_dir = '<?php if ($rtl == 1) { echo 1; } else { echo 0; }?>',
                        prepend = '',
                        avatarstofetch = {},
                        messagewrapperid = '',
                        trayIcons = jqcc.cometchat.getTrayicon(),
                        isRealtimetranslateEnabled = trayIcons.hasOwnProperty('realtimetranslate');

                        incoming.message = jqcc.cometchat.htmlEntities(incoming.message);
                        message = jqcc.cometchat.processcontrolmessage(incoming);

                    if( (!incoming.hasOwnProperty('id') || incoming.id == '') && (!incoming.hasOwnProperty('localmessageid') || incoming.localmessageid == '') && (incoming.message+'').indexOf('CC^CONTROL_')==-1){
                        return;
                    }

                    if(isRealtimetranslateEnabled && jqcc.cookie(settings.cookiePrefix+'rttlang') && fromid != settings.myid && (message).indexOf('CC^CONTROL_') == -1){
                        incoming.message = message;
                        text_translate(incoming);
                    }

                    if(incoming.hasOwnProperty('self')){
                        incomingself = incoming.self;
                    }
                    if(incoming.hasOwnProperty('selfadded')){
                        incomingself = incoming.selfadded;
                    }

                    if( incoming.hasOwnProperty('id') ) {
                        messagewrapperid = incoming.id;
                    }else if( incoming.hasOwnProperty('localmessageid') ) {
                        messagewrapperid = incoming.localmessageid;
                    }
                    incomingid = messagewrapperid;
                    if(fromid != settings.myid || (incoming.hasOwnProperty('botid') && incoming.botid != 0)) {
                        incomingself = 0;
                    }
                    if(typeof(fromname) === 'undefined' || fromname == 0 || incomingself){
                        fromname = "<?php echo $chatrooms_language['me']; ?>";
                    }
                    var temp = '',
                        chatroomreadmessages = jqcc.cometchat.getFromStorage("crreadmessages"),
                        controlparameters = {"id":incomingid, "from":fromname, "fromid":fromid, "message":incomingmessage, "sent":sent};
                    if (calledfromsend != '1') {
                        settings.timestamp=incomingid;
                    }
                    separator = "<?php echo $chatrooms_language['semicolon']; ?>";
                    var message = jqcc.cometchat.processcontrolmessage(incoming),
                        msg_time = jqcc.cometchat.processTimestamp(incoming.sent),
                        months_set = [];

                    <?php
                    $months_array = array($chatrooms_language[90],$chatrooms_language[91],$chatrooms_language[92],$chatrooms_language[93],$chatrooms_language[94],$chatrooms_language[95],$chatrooms_language[96],$chatrooms_language[97],$chatrooms_language[98],$chatrooms_language[99],$chatrooms_language[101],$chatrooms_language[102]);

                    foreach($months_array as $key => $val){
                        ?>
                        months_set.push('<?php echo $val; ?>');
                        <?php
                    }
                    ?>

                    d = new Date(parseInt(msg_time));
                    month  = d.getMonth();
                    date  = d.getDate();
                    year = d.getFullYear();
                    msg_date_class = month+"_"+date+"_"+year;
                    msg_date = months_set[month]+" "+date+", "+year;

                    var type = 'th',
                        add_bg = '',
                        add_arrow_class = '',
                        add_style = "";//for images and smileys

                    if(date==1||date==21||date==31){
                        type = 'st';
                    }else if(date==2||date==22){
                        type = 'nd';
                    }else if(date==3||date==23){
                        type = 'rd';
                    }
                    msg_date_format = date+type+' '+months_set[month]+', '+year;

                    if(msg_date_class == today_date_class){
                        date_class = "today";
                        msg_date = "<?php echo $chatrooms_language['today']; ?>";
                    }else  if(msg_date_class == yday_date_class){
                        date_class = "yesterday";
                        msg_date = "<?php echo $chatrooms_language['yesterday']; ?>";
                    }
                    if(typeof(message) != 'undefined' && message != '') {
                        var prepend = '',
                            smileycount = (message.match(/cometchat_smiley/g) || []).length,
                            smileymsg = message.replace(/<img[^>]*>/g,"");
                        smileymsg = smileymsg.trim();

                        if(smileycount == 1 && smileymsg == '') {
                            message = message.replace('height="20"', 'height="64px"');
                            message = message.replace('width="20"', 'width="64px"');
                        }
                        if($("#cometchat_groupmessage_"+incomingid).length > 0) {
                                $("#cometchat_groupmessage_"+incomingid).find("div.cometchat_chatboxmessagecontent").html(message);
                        } else {
                            sentdata = '';
                            if(sent != null) {
                                var ts = parseInt(sent);
                                sentdata = $[calleeAPI].getTimeDisplay(ts,incomingid);
                            }
                            if(!incomingself) {
                                var avatar = jqcc.cometchat.getThemeArray('buddylistAvatar', fromid);
                                if(typeof avatar=="undefined"){
                                    avatarstofetch[fromid]=1;
                                    avatar = staticCDNUrl+'images/noavatar.png';
                                }
                                var fromavatar = '<a class="cometchat_floatL" href="'+jqcc.cometchat.getThemeArray('buddylistLink', fromid)+'"><img class="cometchat_userscontentavatarsmall cometchat_avatar_'+fromid+'" src="'+avatar+'" title="'+fromname+'"/></a>';

                                if(message.indexOf('cometchat_hw_lang')!=-1){
                                    var hw_ts = 'margin-left: 4px';
                                }

                                var sentdata_box = "<span class=\"cometchat_ts_other\" style='"+hw_ts+"'>"+sentdata+"</span>";

                                if((message.indexOf('<img')==-1 && message.indexOf('src')==-1) || (smileycount > 0 && smileymsg != '')){
                                    add_bg = 'cometchat_chatboxmessagecontent';
                                    add_arrow_class = '<div class="msgArrow"><div class="after"></div></div>';
                                }else{
                                    if(message.indexOf('cometchat_smiley')!=-1) {
                                        add_style = "margin:5px 5px 0px 8px";
                                    }else if(message.indexOf('cometchat_hw_lang')!=-1){
                                        add_style = "margin:0px 0px 0px 8px";
                                    }else{
                                        add_style = "margin:-6px 0px 0px 8px";
                                    }
                                }
                                var usernamecontent = '';
                                if (showUsername == '1') {
                                    usernamecontent = '<span class="cometchat_groupusername">'+fromname+':</span><br>';
                                }

                                if(incoming.hasOwnProperty('botid') && incoming.botid != 0) {
                                    fromavatar = '<a class="cometchat_floatL"><img class="cometchat_userscontentavatarsmall" src="'+jqcc.cometchat.getThemeArray('botlistAvatar', incoming.botid)+'" title="'+jqcc.cometchat.getThemeArray('botlistName', incoming.botid)+'"/></a>';
                                    if (showUsername == '1') {
                                        usernamecontent = '<span class="cometchat_groupusername">'+jqcc.cometchat.getThemeArray('botlistName', incoming.botid)+':</span><br>';
                                    }
                                }
                                temp += '<div class="cometchat_time cometchat_time_'+msg_date_class+' '+date_class+'" msg_format="'+msg_date_format+'">'+msg_date+'</div><div class="cometchat_chatboxmessage" id="cometchat_groupmessage_'+incomingid+'">'+fromavatar+'<div class="'+add_bg+' '+'cometchat_ts_margin cometchat_floatL" style="'+add_style+'">'+usernamecontent+'<span id="cc_groupmessage_'+incomingid+'" class="cometchat_msg">'+message+'</span></div>'+sentdata_box+''+add_arrow_class+'</div>';
                            } else {
                                var sentdata_box = "<span class=\"cometchat_ts\">"+sentdata+"</span>";
                                if((message.indexOf('<img')==-1 && message.indexOf('src')==-1) || (smileycount > 0 && smileymsg != '')){
                                    add_bg = 'cometchat_chatboxmessagecontent cometchat_self';
                                    add_arrow_class = '<div class="selfMsgArrow"><div class="after"></div></div>';
                                }else{
                                    if(message.indexOf('cometchat_smiley')!=-1) {
                                        add_style = "margin-right:13px;max-width:135px;";
                                    }else{
                                        add_style = "margin-right:4px;margin-left:4px";
                                    }
                                }
                                temp += '<div class="cometchat_time cometchat_time_'+msg_date_class+' '+date_class+'" msg_format="'+msg_date_format+'">'+msg_date+'</div><div class="cometchat_chatboxmessage" id="cometchat_groupmessage_'+incomingid+'"><div class="'+add_bg+' '+'cometchat_ts_margin cometchat_self_msg cometchat_floatR" style="'+add_style+'"><span id="cc_groupmessage_'+incomingid+'" class="cometchat_msg">'+message+'</span></div></span>'+sentdata_box+add_arrow_class+'</div><span id="cometchat_chatboxseen_'+incomingid+'">';
                            }

                            if(!$.isEmptyObject(avatarstofetch)){
                                jqcc.cometchat.getUserDetails(Object.keys(avatarstofetch),'updateView');
                            }
                            var grouppopup = $('#cometchat_group_'+chatroomid+'_popup');
                            if(incoming.hasOwnProperty('id') && incoming.hasOwnProperty('localmessageid') && $("#cometchat_groupmessage_"+incoming.localmessageid).length>0){
                                $("#cometchat_groupmessage_"+incoming.localmessageid).after(temp);
                                $("#cometchat_groupmessage_"+incoming.localmessageid).remove();
                                $("#cometchat_chatboxseen_"+incoming.localmessageid).remove();
                                var offlinemessages = jqcc.cometchat.getFromStorage('offlinemessagesqueue');
                                if(offlinemessages.hasOwnProperty(incoming.localmessageid)) {
                                    delete offlinemessages[incoming.localmessageid];
                                    jqcc.cometchat.updateToStorage('offlinemessagesqueue',offlinemessages);
                                }
                            }else{
                                grouppopup.find("#cometchat_grouptabcontenttext_"+chatroomid).append(temp);
                            }

                            if(typeof(incomingid) != 'undefined' && !jqcc.isNumeric(incomingid) &&  incomingid.indexOf('_')>-1) {
                                $("#cometchat_chatboxseen_"+incomingid).addClass('cometchat_offlinemessage');
                            }
                            if($(".cometchat_ts_margin").next().hasClass("cometchat_ts")){
                                var msg_containerHeight = $("#cometchat_groupmessage_"+incomingid+" .cometchat_ts_margin").outerHeight();
                                var cometchat_ts_margin_right = $("#cometchat_groupmessage_"+incomingid+" .cometchat_ts_margin").outerWidth(true)+3;
                                jqcc('#cometchat_groupmessage_'+incomingid).find('.cometchat_ts').css('margin-top',(msg_containerHeight-8));
                                jqcc('#cometchat_groupmessage_'+incomingid).find('.cometchat_ts').css('margin-right',cometchat_ts_margin_right);
                           }
                           if(($(".cometchat_ts_margin").next().hasClass("cometchat_ts_other")) && (cc_dir == 1)){
                                var cometchat_ts_margin_left = $("#cometchat_groupmessage_"+incomingid+" .cometchat_ts_margin").outerWidth(true)+30;
                                jqcc('#cometchat_groupmessage_'+incomingid).find('.cometchat_ts_other').css({'margin-left':cometchat_ts_margin_left});
                            }
                        }

                        if(jqcc.cometchat.getSettings().disableRecentTab == 0) {
                            var temp_msg = jqcc.cometchat.processRecentmessages(message);
                            var params = {'chatid':chatroomid,'isgroup':1,'timestamp':sent,'m':temp_msg,'msgid':incomingid,'force':0,'del':0};
                            jqcc.cometchat.updateRecentChats(params);
                        }
                    }

                    if(typeof(message) != 'undefined' && (jqcc.cometchat.getChatroomVars('owner')|| jqcc.cometchat.checkModerator(incoming.groupid) || (jqcc.cometchat.getChatroomVars('allowDelete') == 1 && incomingself))) {
                        var grouppopup = $('#cometchat_group_'+chatroomid+'_popup');
                        if(grouppopup.find("#cometchat_groupmessage_"+incomingid).find(".delete_msg").length < 1) {
                            if(incomingself){
                                cometchat_ts_class = 'cometchat_ts';
                                cometchat_ts_style = 'float:right';
                                if(message.indexOf('imagemessage mediamessage')!=-1) {
                                    cometchat_ts_style = cometchat_ts_style+';margin-top:12px';
                                }
                            }else{
                                cometchat_ts_class = 'cometchat_ts_other';
                                cometchat_ts_style = 'float:left;margin-left:-3px';
                                var cometchat_ts_other_width = $("#cometchat_groupmessage_"+incomingid+" .cometchat_ts_margin").outerWidth();
                                if(cometchat_ts_other_width >= 135){
                                    cometchat_ts_style = 'float:left;margin-left:-23px';
                                }else{
                                    if(message.indexOf('imagemessage mediamessage')!=-1) {
                                        cometchat_ts_style = 'float:left;margin-left:-6px;margin-top:10px';
                                    }else{
                                        cometchat_ts_style = 'float:left;margin-left:-3px';
                                    }
                                }
                            }
                            if(grouppopup.find("#cometchat_groupmessage_"+incomingid).find(".cometchat_ts_other").length < 1) {
                                   if((message.indexOf('<img')==-1 && message.indexOf('src')==-1) || (smileycount > 0 && smileymsg != '')){
                                    cometchat_del_style = '';
                                }else {
                                    if(message.indexOf('cometchat_smiley')!=-1) {
                                        cometchat_del_style = 'margin: 0px 0px 14px 0px';
                                    }
                                }
                            }else{
                             cometchat_del_style = '';
                        }

                            grouppopup.find('#cometchat_groupmessage_'+incomingid).find("."+cometchat_ts_class).after('<span class="delete_msg" style="'+cometchat_ts_style+';'+cometchat_del_style+';" onclick="javascript:jqcc.cometchat.confirmDelete(\''+incomingid+'\',\''+chatroomid+'\');"><img class="hoverbraces" src="'+staticCDNUrl+'layouts/docked/images/bin.svg"></span>');
                        }

                       $(".cometchat_chatboxmessage").live("mouseover",function() {
                            $(this).find(".delete_msg").css('opacity','0.7');
                            var msg_containerHeight = $(this).find(".cometchat_ts_margin").outerHeight();
                            var cometchat_ts_margin_right = $(this).find(".cometchat_ts_margin").outerWidth(true)+3;
                            $(this).find('.cometchat_ts').css('margin-top',(msg_containerHeight-8));
                            $(this).find('.cometchat_ts').css('margin-right',cometchat_ts_margin_right);
                        });
                        $(".cometchat_chatboxmessage").live("mouseout",function() {
                            $(this).find(".delete_msg").css('opacity','0');
                            var msg_containerHeight = $(this).find(".cometchat_ts_margin").outerHeight();
                            var cometchat_ts_margin_right = $(this).find(".cometchat_ts_margin").outerWidth(true)+3;
                            $(this).find('.cometchat_ts').css('margin-top',(msg_containerHeight-8));
                            $(this).find('.cometchat_ts').css('margin-right',cometchat_ts_margin_right);
                        });
                        $(".delete_msg").mouseover(function() {
                            $(this).find(".delete_msg").css('opacity','1');
                        });
                        $(".delete_msg").mouseout(function() {
                            $(this).find(".delete_msg").css('opacity','0');
                        });
                    }
                    var forced = (incomingself) ? 1 : 0;

                    $.each($("#cometchat_grouptabcontenttext_"+chatroomid+" .cometchat_prependCrMessages"),function (i,divele){
                        $("#cometchat_grouptabcontenttext_"+chatroomid+" .cometchat_prependCrMessages:first").show();
                    });

                    if (message != '' && jqcc.cometchat.getExternalVariable('lastgroupmessageid') < incoming.id){
                        if (typeof(jqcc[jqcc.cometchat.getChatroomVars('calleeAPI')].addMessageCounter) == "function" && !incomingself){
                            if ((typeof $.cookie(settings.cookiePrefix+"sound") == 'undefined' || $.cookie(settings.cookiePrefix+"sound") == null) || $.cookie(settings.cookiePrefix + "sound") == 'true') {
                                $[calleeAPI].playSound(0);
                            }
                            jqcc[jqcc.cometchat.getChatroomVars('calleeAPI')].addMessageCounter(incoming.groupid,1);
                        }
                    }

                    if(jqcc('#currentroom:visible').length<1){
                        var newMessagesCount = jqcc.cometchat.getChatroomVars('newMessages');
                        $('#cometchat_chatroomlist_'+jqcc.cometchat.getChatroomVars('currentroom')).find('.cometchat_chatroommsgcounttext').text(newMessagesCount);
                        if(newMessagesCount > 0){
                            $('#cometchat_chatroomlist_'+jqcc.cometchat.getChatroomVars('currentroom')).find('.cometchat_chatroommsgcount').show();
                        }
                    }

                    $[calleeAPI].updateCRReadMessages(jqcc.cometchat.getChatroomVars('currentroom'));
                    var crreadmessages = jqcc.cometchat.getFromStorage("crreadmessages");
                    jqcc.cometchat.setChatroomVars('crreadmessages',crreadmessages);
                    jqcc.crdocked.groupbyDate(chatroomid);

                    $('#cometchat_grouptabcontenttext_'+chatroomid).find('.cometchat_prependCrMessages').remove();

                    prepend = '<div class=\"cometchat_prependCrMessages\" onclick\="jqcc.crdocked.prependCrMessagesInit('+chatroomid+')\" id = \"cometchat_prependCrMessages_'+chatroomid+'\"><?php echo $chatrooms_language[74];?></div>';

                    if($('#cometchat_grouptabcontenttext_'+chatroomid+' .cometchat_prependMessages').length != 1){
                        $('#cometchat_grouptabcontenttext_'+chatroomid).prepend(prepend);
                    }

                    if(typeof(message) != 'undefined' &&  (message).indexOf('<img')!=-1 && (message).indexOf('src')!=-1){
                        $[calleeAPI].chatroomScrollDown(forced,chatroomid);
                        $( "#cometchat_groupmessage_"+incomingid+" img" ).load(function() {
                            var cometchat_ts_margin_right = $("#cometchat_groupmessage_"+incomingid+" .cometchat_ts_margin").outerWidth(true)+2;
                            jqcc('#cometchat_groupmessage_'+incomingid).find('.cometchat_ts').css({'margin-right':cometchat_ts_margin_right});
                            $[calleeAPI].chatroomScrollDown(forced,chatroomid);
                        });
                    }else{
                        $[calleeAPI].chatroomScrollDown(forced,chatroomid);
                    }
                },
                updateView: function(ids){
                    jqcc.each(ids,function(index,id){
                        var avatar = jqcc.cometchat.getThemeArray('buddylistAvatar', id);
                        if(typeof avatar != 'undefined'){
                            jqcc('.cometchat_avatar_'+id).attr('src',avatar);
                        }
                    });
                },
                updateCRReadMessages: function(id){
                    if(typeof(id) == 'object'){
                        jqcc.each(id, function(chatroomId,lastmessage) {
                            chatroomId = chatroomId.replace('_','');
                            if(typeof (jqcc.cometchat.updateToStorage)!=='undefined'){
                                var alreadycrreadmessages = jqcc.cometchat.getFromStorage('crreadmessages');
                                if((typeof(alreadycrreadmessages[chatroomId])!='undefined' && parseInt(alreadycrreadmessages[chatroomId])<parseInt(lastmessage)) || typeof(alreadycrreadmessages[chatroomId])=='undefined'){
                                    var crreadmessages = {};
                                    crreadmessages[chatroomId] = parseInt(lastmessage);
                                    jqcc.cometchat.updateToStorage('crreadmessages',crreadmessages);
                                    jqcc.cometchat.setChatroomVars('crreadmessages',jqcc.cometchat.getFromStorage("crreadmessages"));
                                }
                            }
                        });
                    } else {
                        if($('#currentroom_convotext').find('.cometchat_chatboxmessage:last').length){
                            if(id == 0){ return; }
                            if(typeof (jqcc.cometchat.updateToStorage)!=='undefined'){
                                var alreadycrreadmessages = jqcc.cometchat.getFromStorage('crreadmessages');
                                var lastid = parseInt($('#currentroom_convotext').find('.cometchat_chatboxmessage:last').attr('id').replace('cometchat_groupmessage_',''));
                                if((typeof(alreadycrreadmessages[id])!='undefined' && parseInt(alreadycrreadmessages[id])<parseInt(lastid)) || typeof(alreadycrreadmessages[id])=='undefined'){
                                    var crreadmessages = {};
                                    crreadmessages[id] = parseInt(lastid);
                                    jqcc.cometchat.updateToStorage('crreadmessages',crreadmessages);
                                    jqcc.cometchat.setChatroomVars('crreadmessages',jqcc.cometchat.getFromStorage("crreadmessages"));
                                }
                            }
                        }
                    }
                },
                updateCRReceivedUnreadMessages: function(id,lastid){

                },
                chatroomBoxKeyup: function(event,chatboxtextarea,id) {
                },
                chatroomBoxKeydown: function(event,chatboxtextarea,id) {
                    jqcc.cometchat.chatroomBoxKeydown(event,chatboxtextarea,id);
                },
                hidetabs: function() {
                },
                loadLobby: function() {
                },
                crcheckDropDown: function(dropdown) {
                    var id = dropdown.selectedIndex;
                    if(id == 1) {
                        $('div.password_hide').css('display','block');
                    } else {
                        $('div.password_hide').css('display','none');
                    }
                },
                loadRoom: function(clicked,id,minimized,unreadmsgcount) {
                    var roomname = jqcc.cometchat.getChatroomVars('currentroomname');
                    var roomno = jqcc.cometchat.getChatroomVars('currentroom');
                    var messageCounter = '0';
                    var room_no = '_'+roomno;
                    var userid = parseInt(settings.myid);
                    if(typeof(id) != "undefined"){
                        roomno = id;
                    }
                    var openChatrooms = jqcc.cometchat.getChatroomVars('chatroomsOpened');
                    if(openChatrooms[roomno]!=null){
                        if($('#cometchat_unseenUsers').find('#cometchat_group_'+id).length != 0) {
                            uid = 'cometchat_group_'+id;
                            jqcc.docked.swapTab(uid,1);
                        }else if($("#cometchat_group_"+id+"_popup").hasClass('cometchat_tabhidden')){
                            $("#cometchat_group_"+id).click();
                        }

                        return;
                    }

                    var widthavailable = (jqcc(window).width() - jqcc('#cometchat_chatboxes').outerWidth() - chatboxWidth - chatboxDistance);

                    if(widthavailable < (chatboxWidth+chatboxWidth)){
                        jqcc.docked.rearrange(1);
                    }else{
                        $('#cometchat_chatboxes_wide').width($('#cometchat_chatboxes_wide').width()+chatboxWidth+chatboxDistance);
                        $('#cometchat_chatboxes').css(jqcc.cometchat.getThemeVariable('dockedAlignment'),$('#cometchat_userstab').outerWidth(true)+'px');
                    }

                    $('.cometchat_prependMessages_container > .cometchat_prependCrMessages').text("<?php echo $chatrooms_language['load_earlier_msgs'];?>");
                    $('.cometchat_prependMessages_container > .cometchat_prependCrMessages').attr('onclick','jqcc.docked.prependCrMessagesInit('+roomno+')');
                    /*$("#cometchat_group_"+roomno+"_popup").find('#cometchat_grouptabcontenttext_'+roomno).attr('onscroll','jqcc.crdocked.chatScroll('+roomno+')');*/

                    var cometchat_chatboxes = $("#cometchat_chatboxes");
                    var chatBoxInlineCss = {'margin-right':chatboxDistance+'px','width':chatboxWidth+'px'};
                    if(jqcc.cometchat.getSettings().dockedAlignToLeft == 1){
                        chatBoxInlineCss = {'margin-left':chatboxDistance+'px','width':chatboxWidth+'px'};
                    }
                    $("<span/>").attr("id", "cometchat_group_"+roomno).attr("amount", 0).attr("groupid", roomno).addClass("cometchat_tab").addClass('cometchat_tabopen_bottom').css(chatBoxInlineCss).html('<div class="cometchat_groupname" style="margin-left:4px;">'+roomname+'</div><div class="cometchat_closebox cometchat_tooltip" title="<?php echo $chatrooms_language["close_tab"];?>" id="cometchat_groupclosebox_bottom_'+roomno+'" style="margin-right:5px;"></div><div class="cometchat_unreadCount cometchat_floatR" style="display:none;"></div>').prependTo($("#cometchat_chatboxes_wide"));

                    var plugins = jqcc.cometchat.getChatroomVars('plugins');
                    var pluginslength = plugins.length;
                    var pluginstophtml = '';
                    var pluginsbottomhtml='';
                    var avchathtml = '';
                    var audiochathtml = '';
                    var smiliehtml = '';

                    pluginstophtml += '<div class="cometchat_pluginstop">';

                    pluginstophtml += '<div class="cometchat_plugins_dropdownlist" name="viewusers" to="'+roomno+'" chatroommode="1" title=\'<?php echo $chatrooms_language["view_users"];?>\' ><div class="cometchat_plugins_name <?php echo $cometchat_float;?>"><?php echo $chatrooms_language["view_users"];?></div></div>';

                    pluginstophtml += '<div class="cometchat_plugins_dropdownlist" name="inviteusers" to="'+roomno+'" chatroommode="1" title="<?php echo $chatrooms_language[67];?>" ><div class="cometchat_plugins_name <?php echo $cometchat_float;?>"><?php echo $chatrooms_language[67];?></div></div>';

                    if(jqcc.cometchat.checkModerator(roomno) || jqcc.cometchat.getChatroomVars('owner')){
                        pluginstophtml += '<div class="cometchat_plugins_dropdownlist" name="unbanusers" to="'+roomno+'" chatroommode="1" title="<?php echo $chatrooms_language[39];?>" ><div class="cometchat_plugins_name <?php echo $cometchat_float;?>"><?php echo $chatrooms_language[39];?></div></div>';
                    }

                    pluginstophtml += '<div class="cometchat_plugins_dropdownlist" name="leavegroup" to="'+roomno+'" chatroommode="1" title="<?php echo $chatrooms_language[72];?>" ><div class="cometchat_plugins_name <?php echo $cometchat_float;?>"><?php echo $chatrooms_language[72];?></div></div>';

                    if(pluginslength>0){
                        pluginsbottomhtml += '<div class="cometchat_pluginsbottom">';
                        for(var i = 0; i<pluginslength; i++){
                            if(mobileDevice && (plugins[i]=='transliterate' || plugins[i]=='screenshare')) {
                                continue;
                            }

                            var name = 'cc'+plugins[i];
                            if(typeof ($[name])=='object'){
                                 if(plugins[i]=='avchat') {
                                    if(mobileDevice){
                                        pluginsbottomhtml += '<div class="cometchat_plugins_openuplist" name="'+name+'" to="'+roomno+'" chatroommode="1" title="'+$[name].getTitle()+'" ><div class="cometchat_plugins_name <?php echo $cometchat_float;?>">'+$[name].getTitle()+'</div></div>';
                                    } else {
                                        avchathtml = '<div id="cometchat_'+plugins[i]+'_'+roomno+'" class="cometchat_tooltip cometchat_tabicons cometchat_'+plugins[i]+'" name="'+name+'" to="'+roomno+'" chatroommode="1" title="'+$[name].getTitle()+'"></div>';
                                    }
                                } else if(plugins[i]=='audiochat') {
                                    if(mobileDevice){
                                        pluginsbottomhtml += '<div class="cometchat_plugins_openuplist" name="'+name+'" to="'+roomno+'" chatroommode="1" title="'+$[name].getTitle()+'" ><div class="cometchat_plugins_name <?php echo $cometchat_float;?>">'+$[name].getTitle()+'</div></div>';
                                    } else {
                                        audiochathtml = '<div id="cometchat_'+plugins[i]+'_'+roomno+'" class="cometchat_tooltip cometchat_tabicons cometchat_'+plugins[i]+'" name="'+name+'" to="'+roomno+'" chatroommode="1" title="'+$[name].getTitle()+'"></div>';
                                    }
                                } else if(plugins[i]=='smilies') {
                                    smiliehtml = '<div class="cometchat_'+plugins[i]+'" name="'+name+'" to="'+roomno+'" chatroommode="1" title="'+$[name].getTitle()+'"></div>';
                                } else if(plugins[i]=='clearconversation' || plugins[i]=='report' || plugins[i]=='chathistory' || plugins[i]=='block' || plugins[i]=='save' || plugins[i]=='style'){
                                    pluginstophtml += '<div class="cometchat_plugins_dropdownlist" name="'+name+'" to="'+roomno+'" chatroommode="1" title="'+$[name].getTitle()+'" ><div class="cometchat_plugins_name <?php echo $cometchat_float;?>">'+$[name].getTitle()+'</div></div>';
                                }else{
                                    pluginsbottomhtml += '<div class="cometchat_plugins_openuplist" name="'+name+'" to="'+roomno+'" chatroommode="1" title="'+$[name].getTitle()+'" ><div class="cometchat_plugins_name <?php echo $cometchat_float;?>">'+$[name].getTitle()+'</div></div>';
                                }
                            }
                        }
                        pluginsbottomhtml += '</div>';
                    }

                    pluginstophtml += '</div>';

                    if(typeof(minimized)=="undefined" || minimized == ''){
                        minimized = 1;
                    }
                    var tabstateclass = (minimized == 2)?'tabhidden':'tabopen';
                    var plugins_openup_css = '';
                    var inner_container_margin = '';
                    var send_message_box = '';
                    var cctextarea_width = '';
                    if(pluginsbottomhtml=='<div class="cometchat_pluginsbottom"></div>') {
                        plugins_openup_css = 'display:none';
                        inner_container_margin = 'margin-left:0px !important';
                    }

                    var prepend = '';
                    prepend = '<div class=\"cometchat_prependCrMessages\" onclick\="jqcc.crdocked.prependCrMessagesInit('+roomno+')\" id = \"cometchat_prependCrMessages_'+roomno+'\"><?php echo $chatrooms_language[74];?></div>';

                    if(mobileDevice){
                        cctextarea_width = "width:140px !important";
                        send_message_box = '<div id="cometchat_sendmessagebtn"></div>';
                    }else{
                        cctextarea_width = "";
                    }

                    var plugin_divider = '<div class="cometchat_vline"></div>';
                    if (audiochathtml == '' && avchathtml == '') {
                        plugin_divider = '';
                    }
                    $("<div/>").attr("id", "cometchat_group_"+roomno+"_popup").addClass("cometchat_tabpopup").addClass('cometchat_'+tabstateclass).html('<div class="cometchat_tabtitle"><div class="cometchat_primarytabtitle"><div class="cometchat_groupname" title="'+roomname+'">'+roomname+'</div><div id="cometchat_groupclosebox_'+roomno+'" title="<?php echo $chatrooms_language["close_tab"];?>" class="cometchat_closebox cometchat_floatR cometchat_tooltip"></div>'+plugin_divider+audiochathtml+avchathtml+'<div class="cometchat_plugins_dropdown <?php echo $cometchat_float;?>"><div class="cometchat_plugins_dropdown_icon cometchat_tooltip" id="cometchat_groupplugins_dropdown_icon_'+roomno+'" title="<?php echo $chatrooms_language["more_options"];?>"></div><div class="cometchat_popup_plugins">'+pluginstophtml+'</div></div></div><div class="cometchat_secondarytabtitle cometchat_tabhidden"></div></div><div class="cometchat_tabcontent"><div class="cometchat_messagElement" id="cc_cr_'+roomno+'"></div><div class="cometchat_primarytabcontent"><div class="cometchat_tabcontenttext" id="cometchat_grouptabcontenttext_'+roomno+'" onscroll="jqcc.crdocked.chatScroll(\''+roomno+'\');"></div><div class="cometchat_tabcontentinput"><div class="cometchat_plugins_openup cometchat_floatL"><div class="cometchat_plugins_openup_icon cometchat_tooltip" id="cometchat_groupplugins_openup_icon_'+roomno+'" title="<?php echo $chatrooms_language["more_options"];?>"></div><div class="cometchat_popup_convo_plugins">'+pluginsbottomhtml+'</div></div><div class="cometchat_inner_container"><textarea class="cometchat_textarea" style="'+cctextarea_width+'"; placeholder="<?php echo $chatrooms_language[64];?>"></textarea>'+send_message_box+smiliehtml+'</div></div></div><div class="cometchat_secondaryviewuserscontent cometchat_tabhidden"></div><div class="cometchat_secondaryinviteuserscontent cometchat_tabhidden"></div></div>').appendTo($('#cometchat_group_'+roomno));

                    openChatrooms[roomno] = 1;
                    jqcc.cometchat.setChatroomVars('chatroomsOpened',openChatrooms);

                    var chatboxcontentheight = $('#cometchat_chatboxes').find('.cometchat_tabcontent .cometchat_tabcontenttext').height();

                    var cometchat_group_popup = $("#cometchat_group_"+roomno+"_popup");
                    var cometchat_group_popup1 = document.getElementById("cometchat_group_"+roomno+"_popup");

                    cometchat_group_popup.find('.cometchat_tabcontenttext').click(function(){
                        if(cometchat_group_popup.find(".cometchat_tabcontent .cometchat_chatboxpopup_"+roomno).length){
                            closeChatboxCCPopup(roomno,1);
                        }
                    });

                    var cometchat_group_id = $("#cometchat_group_"+roomno);

                    if(!cometchat_group_popup.find('cometchat_uploadfile_'+roomno).length) {
                        var uploadf = document.createElement("INPUT");
                        uploadf.setAttribute("type", "file");
                        uploadf.setAttribute("class", "cometchat_fileupload");
                        uploadf.setAttribute("id", 'cometchat_uploadfile_'+roomno);
                        uploadf.setAttribute("name", "Filedata");
                        uploadf.setAttribute("multiple", "true");
                        cometchat_group_popup.find(".cometchat_tabcontent").append(uploadf);
                        uploadf.addEventListener("change", jqcc.ccfiletransfer.FileSelectHandler(cometchat_group_popup.find('.cometchat_tabcontent'),roomno,1), false);
                    }

                    /*var xhr = new XMLHttpRequest();
                    if(xhr.upload) {
                        cometchat_group_popup1.addEventListener("dragover", jqcc.ccfiletransfer.FileDragHover(), false);
                        cometchat_group_popup1.addEventListener("dragleave", jqcc.ccfiletransfer.FileDragHover(), false);
                        cometchat_group_popup1.addEventListener("drop", jqcc.ccfiletransfer.FileSelectHandler(cometchat_group_popup.find('.cometchat_tabcontent'),roomno,1), false);
                    }*/

                    cometchat_group_popup.find('.cometchat_plugins_dropdown').click(function(e){
                        e.stopImmediatePropagation();
                        if(cometchat_group_popup.find(".cometchat_tabcontent .cometchat_chatboxpopup_"+roomno).length){
                            closeChatboxCCPopup(roomno,1);
                        }
                        if(cometchat_group_popup.find('.cometchat_plugins_openup').hasClass('cometchat_plugins_openup_active')) {
                            cometchat_group_popup.find('.cometchat_plugins_openup').toggleClass('cometchat_plugins_openup_active').find('div.cometchat_popup_convo_plugins').slideToggle('fast');
                            if($(this).hasClass('cometchat_plugins_openup_active')){
                               cometchat_group_popup.find('#cometchat_groupplugins_openup_icon_'+roomno).addClass('cometchat_pluginsopenup_arrowrotate');
                            } else {
                               cometchat_group_popup.find('#cometchat_groupplugins_openup_icon_'+roomno).removeClass('cometchat_pluginsopenup_arrowrotate');
                            }
                        }
                        $(this).toggleClass('cometchat_plugins_dropdown_active').find('div.cometchat_popup_plugins').slideToggle('fast');

                        if($(this).hasClass('cometchat_plugins_dropdown_active')){
                            cometchat_group_popup.find('#cometchat_groupplugins_dropdown_icon_'+roomno).addClass('cometchat_pluginsdropdown_arrowrotate');
                        } else {
                            cometchat_group_popup.find('#cometchat_groupplugins_dropdown_icon_'+roomno).removeClass('cometchat_pluginsdropdown_arrowrotate');
                        }
                        if(jqcc().slimScroll){
                            var cometchat_slimscroll_height = cometchat_group_popup.find('.cometchat_pluginstop').height();
                            var cometchat_slimscroll_width = cometchat_group_popup.find('.cometchat_pluginstop').width();
                            cometchat_group_popup.find('.cometchat_pluginstop').slimScroll({height: (cometchat_slimscroll_height)+'px', width: (cometchat_slimscroll_width)+'px'});
                            cometchat_group_popup.find('.cometchat_popup_plugins').find('.slimScrollDiv').css({'box-shadow': '0px 5px 8px -3px #D1D1D1'});
                        }
                    });

                    cometchat_group_popup.find('.cometchat_plugins_openup').click(function(){
                        if(cometchat_group_popup.find(".cometchat_tabcontent .cometchat_chatboxpopup_"+roomno).length){
                            closeChatboxCCPopup(roomno,1);
                        } else {
                            if(cometchat_group_popup.find('.cometchat_plugins_dropdown').hasClass('cometchat_plugins_dropdown_active')) {
                            cometchat_group_popup.find('.cometchat_plugins_dropdown').toggleClass('cometchat_plugins_dropdown_active').find('div.cometchat_popup_plugins').slideToggle('fast');
                                if($(this).hasClass('cometchat_plugins_dropdown_active')){
                                    cometchat_group_popup.find('#cometchat_groupplugins_dropdown_icon_'+roomno).addClass('cometchat_pluginsdropdown_arrowrotate');
                                } else {
                                    cometchat_group_popup.find('#cometchat_groupplugins_dropdown_icon_'+roomno).removeClass('cometchat_pluginsdropdown_arrowrotate');
                                }
                            }
                            $(this).toggleClass('cometchat_plugins_openup_active').find('div.cometchat_popup_convo_plugins').slideToggle('fast');
                            if($(this).hasClass('cometchat_plugins_openup_active')){
                               cometchat_group_popup.find('#cometchat_groupplugins_openup_icon_'+roomno).addClass('cometchat_pluginsopenup_arrowrotate');
                            } else {
                               cometchat_group_popup.find('#cometchat_groupplugins_openup_icon_'+roomno).removeClass('cometchat_pluginsopenup_arrowrotate');
                            }
                        }
                        if(mobileDevice){
                            cometchat_group_popup.find('.cometchat_pluginsbottom').css('overflow-y','auto');
                        }else if(jqcc().slimScroll){
                            var cometchat_slimscroll_height = cometchat_group_popup.find('.cometchat_pluginsbottom').height();
                            var cometchat_slimscroll_width = cometchat_group_popup.find('.cometchat_pluginsbottom').width();
                            if(cometchat_group_popup.find('.cometchat_pluginsbottom').parent().hasClass('slimScrollDiv')){
                                cometchat_group_popup.find('.cometchat_popup_convo_plugins').find("div.slimScrollDiv").css('height', (cometchat_slimscroll_height+1)+'px');
                                cometchat_group_popup.find('.cometchat_popup_convo_plugins').find("div.slimScrollDiv").css('width', (cometchat_slimscroll_width+1)+'px');
                            }else{
                                cometchat_group_popup.find('.cometchat_pluginsbottom').slimScroll({height: (cometchat_slimscroll_height+1)+'px', width: (cometchat_slimscroll_width+1)+'px'});
                            }
                            var scrolltop_height = parseInt(285 - cometchat_slimscroll_height);
                            cometchat_group_popup.find('.cometchat_popup_convo_plugins').find('.slimScrollDiv').css({'top':scrolltop_height,'box-shadow': '0px -4px 10px -3px #d1d1d1'});
                        }
                    });

                    cometchat_group_popup.find('.cometchat_plugins_openuplist, .cometchat_plugins_dropdownlist, .cometchat_smilies, .cometchat_avchat, .cometchat_audiochat').click(function(e){
                        e.stopImmediatePropagation();
                        var name = $(this).attr('name');
                        var to = $(this).attr('to');
                        var chatroommode = $(this).attr('chatroommode');
                        var controlparameters = {"to":to, "chatroommode":chatroommode, "roomname":roomname};
                        if(cometchat_group_popup.find('.cometchat_plugins_openup').hasClass('cometchat_plugins_openup_active')){
                            cometchat_group_popup.find('.cometchat_plugins_openup').toggleClass('cometchat_plugins_openup_active').find('div.cometchat_popup_convo_plugins').slideToggle('fast');
                            if($(this).hasClass('cometchat_plugins_openup_active')){
                               cometchat_group_popup.find('#cometchat_groupplugins_openup_icon_'+roomno).addClass('cometchat_pluginsopenup_arrowrotate');
                            } else {
                               cometchat_group_popup.find('#cometchat_groupplugins_openup_icon_'+roomno).removeClass('cometchat_pluginsopenup_arrowrotate');
                            }
                        }
                        if(cometchat_group_popup.find('.cometchat_plugins_dropdown').hasClass('cometchat_plugins_dropdown_active')){
                            cometchat_group_popup.find('.cometchat_plugins_dropdown').toggleClass('cometchat_plugins_dropdown_active').find('div.cometchat_popup_plugins').slideToggle('fast');
                            if($(this).hasClass('cometchat_plugins_dropdown_active')){
                                cometchat_group_popup.find('#cometchat_groupplugins_dropdown_icon_'+roomno).addClass('cometchat_pluginsdropdown_arrowrotate');
                            } else {
                                cometchat_group_popup.find('#cometchat_groupplugins_dropdown_icon_'+roomno).removeClass('cometchat_pluginsdropdown_arrowrotate');
                            }
                        }
                        if(name == "viewusers"){
                            jqcc.cometchat.getGroupUsers(to);
                            jqcc.docked.viewGroupUsers(to);
                        } else if(name == "inviteusers"){
                            jqcc.docked.inviteGroupUsers(to);
                        } else if(name == "unbanusers"){
                            jqcc.docked.unbanGroupUsers(to);
                        } else if(name == "leavegroup"){
                            jqcc.cometchat.leaveChatroom(to);
                        } else {
                            jqcc[name].init(controlparameters);
                        }
                    });

                    cometchat_group_id.find('.cometchat_closebox').click(function(e){
                        e.stopImmediatePropagation();
                        jqcc.crdocked.closeChatroom(roomno);
                    });

                    cometchat_group_popup.find('.cometchat_tabtitle').click(function(e){
                        e.stopImmediatePropagation();
                        cometchat_group_id.find(cometchat_group_popup).removeClass('cometchat_tabopen').addClass('cometchat_tabhidden');
                        jqcc.cometchat.updateChatBoxState({id:roomno,g:1,s:2});
                    });

                    cometchat_group_id.click(function(e){
                        cometchat_group_id.find(cometchat_group_popup).removeClass('cometchat_tabhidden').addClass('cometchat_tabopen');
                        $.each($('#cometchat_group_'+id+'_popup .cometchat_chatboxmessage'),function (i,divele){
                            if($(this).find(".cometchat_ts_margin").next().hasClass("cometchat_ts")){
                                var msg_containerHeight = $(this).find(".cometchat_ts_margin").outerHeight();
                                var cometchat_ts_margin_right = $(this).find(".cometchat_ts_margin").outerWidth(true)+3;
                                jqcc(this).find('.cometchat_ts').css('margin-top',(msg_containerHeight-8));
                                jqcc(this).find('.cometchat_ts').css('margin-right',cometchat_ts_margin_right);
                            }
                        });
                        jqcc.cometchat.updateChatBoxState({id:roomno,g:1,s:1,c:0});
                        jqcc[jqcc.cometchat.getChatroomVars('calleeAPI')].addMessageCounter(roomno);
                    });

                    var cometchat_grouptextarea = cometchat_group_popup.find("textarea.cometchat_textarea");
                    cometchat_grouptextarea.keydown(function(event){
                        jqcc.crdocked.resizeCrinputTextarea(cometchat_group_popup,this,roomno,event);
                        return jqcc.docked.chatroomBoxKeydown(event, this, roomno);
                    });
                    cometchat_grouptextarea.keyup(function(event){
                        jqcc.crdocked.resizeCrinputTextarea(cometchat_group_popup,this,roomno,event);
                        return jqcc.docked.chatroomBoxKeyup(event, this, roomno);
                    });

                    jqcc('#cometchat_sendmessagebtn').click(function(e){
                        var message = cometchat_grouptextarea.val();
                        var basedata = jqcc.cometchat.getBaseData();
                        message = message.replace(/^\s+|\s+$/g, "");
                        jqcc.cometchat.sendmessageProcess(message, roomno, basedata);
                        cometchat_grouptextarea.val('');
                        cometchat_grouptextarea.addClass('cometchat_placeholder');
                        $(cometchat_grouptextarea).attr('style', 'height: 15px !important;width:140px !important;');
                        cometchat_group_popup.find('.cometchat_inner_container').height(20);
                        if(cometchat_group_popup.find('.cometchat_tabcontent .cometchat_chatboxpopup_'+roomno).length == 0){
                            cometchat_group_popup.find('.cometchat_tabcontenttext').height(285);
                        }else{
                            var iframe_name = jqcc('.cometchat_iframe').attr('id');
                            var default_height = 0;
                            if (iframe_name == 'cometchat_trayicon_smilies_iframe'){
                                default_height = 108;
                            }else if(iframe_name == 'cometchat_trayicon_stickers_iframe'){
                                default_height = 102;
                            }else if(iframe_name == 'cometchat_trayicon_handwrite_iframe'){
                                default_height = 143;
                            }
                            if(default_height!=0){
                                if(iframe_name == 'cometchat_trayicon_smilies_iframe') {
                                    var paramstoresizeIframe = {
                                        type:"plugin",
                                        name:"smilies",
                                        method: 'resizeContainerbody',
                                        params:{
                                            height:default_height
                                        }
                                    };
                                    cometchat_group_popup.find('.cometchat_iframe')[0].contentWindow.postMessage('CC^CONTROL_'+JSON.stringify(paramstoresizeIframe),'*');
                                }else{
                                    cometchat_group_popup.find('.cometchat_iframe').height(default_height);////143 is the default height of sketch popup
                                }
                            }
                        }
                    });

                    if(cometchat_group_popup.find(".cometchat_prependCrMessages").length == 0){
                        cometchat_group_popup.find('#cometchat_grouptabcontenttext_'+roomno).append(prepend);
                        $('#cometchat_grouptabcontenttext_'+roomno).find(".cometchat_prependCrMessages").css("display","block");
                    }

                    var extension_set = jqcc.cometchat.getSettings().extensions;
                    var extensions_array = [];
                    extensions_array.push(extension_set);

                    if(extensions_array[0].indexOf('ads') > -1){
                        jqcc.ccads.init(roomno,1);
                    }
                    if(typeof(jqcc.cometchat.checkInternetConnection) && !jqcc.cometchat.checkInternetConnection()) {
                        jqcc.docked.noInternetConnection(true);
                    }
                    jqcc[jqcc.cometchat.getChatroomVars('calleeAPI')].addMessageCounter(id);
                },
                resizeCrinputTextarea: function(cometchat_group_popup,chatboxtextarea,id,event){
                    var forced = 1;
                    var difference = $(chatboxtextarea).innerHeight() - $(chatboxtextarea).height();
                    var cctabcontenttext_resize = '';
                    var container_height = cometchat_group_popup.find('.cometchat_inner_container').outerHeight();
                    if ($(chatboxtextarea).innerHeight < chatboxtextarea.scrollHeight ) {
                    } else if(event.keyCode != 13) {
                        if($(chatboxtextarea).height() < 50 || event.keyCode == 8) {
                            if(mobileDevice){
                                $(chatboxtextarea).attr('style', 'height: 15px !important;width:140px !important;');
                            }else{
                                $(chatboxtextarea).attr('style', 'height: 15px !important;width:165px !important;');
                            }
                            cometchat_group_popup.find('.cometchat_inner_container').height(20);
                            if(chatboxtextarea.scrollHeight - difference >= 47){
                                if(mobileDevice){
                                    $(chatboxtextarea).attr('style', 'height: 50px !important;width:140px !important');
                                cometchat_group_popup.find('.cometchat_inner_container').height((chatboxtextarea.scrollHeight - difference) + 12);
                                    $(chatboxtextarea).css('overflow-y','auto');
                                }else{
                                    if($(chatboxtextarea).parent().attr('class') != 'slimScrollDiv'){
                                        $(chatboxtextarea).slimScroll({scroll: '1'});
                                    }
                                    $(chatboxtextarea).attr('style', 'height: 50px !important;width:161px !important');
                                    cometchat_group_popup.find('.cometchat_inner_container').height((chatboxtextarea.scrollHeight - difference) + 12);
                                    cometchat_group_popup.find('.cometchat_inner_container .slimScrollDiv').css({'float':'left','width':'172px'});
                                }
                                $(chatboxtextarea).focus();
                                cometchat_group_popup.find('.cometchat_inner_container').height(56);
                            }else if(chatboxtextarea.scrollHeight - difference>20){
                                if(mobileDevice){
                                    $(chatboxtextarea).attr('style', 'height: '+(chatboxtextarea.scrollHeight - difference)+'px !important;width:140px !important;');
                                }else{
                                    $(chatboxtextarea).attr('style', 'height: '+(chatboxtextarea.scrollHeight - difference)+'px !important;width:165px !important;');
                                }

                                cometchat_group_popup.find('.cometchat_inner_container').height((chatboxtextarea.scrollHeight - difference) + 7);
                            }
                            var newcontainerheight = cometchat_group_popup.find('.cometchat_inner_container').outerHeight();
                            if(container_height!=(newcontainerheight)){
                               cctabcontenttext_resize = (cometchat_group_popup.find('.cometchat_tabcontent').height() - cometchat_group_popup.find('.cometchat_inner_container').height() - 10);
                                if(cometchat_group_popup.find('.cometchat_tabcontent .cometchat_chatboxpopup_'+id).length == 0){
                                    cometchat_group_popup.find('.cometchat_tabcontenttext').height(cctabcontenttext_resize - 1);
                                    if($('#cometchat_grouptabcontenttext_'+id).parent().hasClass('slimScrollDiv') == true){
                                        $('#cometchat_grouptabcontenttext_'+id).parent().height(cctabcontenttext_resize - 1);
                                    }
                                    $[calleeAPI].chatroomScrollDown(forced,id);
                                }else{
                                    var iframe_name = jqcc('.cometchat_iframe').attr('id');
                                    var default_height = 0;//default height of sketch popup
                                    if (iframe_name == 'cometchat_trayicon_smilies_iframe'){
                                        default_height = 108;
                                    }else if(iframe_name == 'cometchat_trayicon_stickers_iframe'){
                                        default_height = 102;
                                    }else if(iframe_name == 'cometchat_trayicon_handwrite_iframe'){
                                        default_height = 143;
                                    }
                                    if(default_height!=0){
                                        var new_height = (cometchat_group_popup.find('.cometchat_tabcontentinput').height()-22);
                                        if(iframe_name == 'cometchat_trayicon_smilies_iframe') {
                                            var paramstoresizeIframe = {
                                                type:"plugin",
                                                name:"smilies",
                                                method: 'resizeContainerbody',
                                                params:{
                                                    height:default_height-new_height
                                                }
                                            };
                                            cometchat_group_popup.find('.cometchat_iframe')[0].contentWindow.postMessage('CC^CONTROL_'+JSON.stringify(paramstoresizeIframe),'*');
                                        }else{
                                            cometchat_group_popup.find('.cometchat_iframe').height(default_height-new_height);
                                        }
                                    }
                                }
                                var inputheight = cometchat_group_popup.find('.cometchat_tabcontentinput').outerHeight();
                                cometchat_group_popup.find('.cometchat_popup_convo_plugins').css('bottom',inputheight);
                                var scrolltop_height = parseInt(cometchat_group_popup.find('.cometchat_popup_convo_plugins').outerHeight() - cometchat_group_popup.find('.cometchat_pluginsbottom').outerHeight());
                                cometchat_group_popup.find('.cometchat_popup_convo_plugins').find('.slimScrollDiv').css({'top':scrolltop_height});
                            }
                        }
                    }else{
                        if(mobileDevice){
                            $(chatboxtextarea).attr('style', 'height: 15px !important;width:140px !important;');
                        }else{
                            $(chatboxtextarea).attr('style', 'height: 15px !important;width:165px !important;');
                        }
                        cometchat_group_popup.find('.cometchat_inner_container').height(20);
                        if(cometchat_group_popup.find('.cometchat_tabcontent .cometchat_chatboxpopup_'+id).length == 0){
                            cometchat_group_popup.find('.cometchat_tabcontenttext').height(chatboxHeight - 75);
                           if($('#cometchat_grouptabcontenttext_'+id).parent().hasClass('slimScrollDiv') == true){
                                $('#cometchat_grouptabcontenttext_'+id).parent().height(chatboxHeight - 75);
                            }
                            $[calleeAPI].chatroomScrollDown(forced,id);
                        }else{
                            var iframe_name = jqcc('.cometchat_iframe').attr('id');
                            var default_height = 0;
                            if (iframe_name == 'cometchat_trayicon_smilies_iframe'){
                                default_height = 108;
                            }else if(iframe_name == 'cometchat_trayicon_stickers_iframe'){
                                default_height = 102;
                            }else if(iframe_name == 'cometchat_trayicon_handwrite_iframe'){
                                default_height = 143;
                            }
                            if(default_height!=0){
                                if(iframe_name == 'cometchat_trayicon_smilies_iframe') {
                                    var paramstoresizeIframe = {
                                        type:"plugin",
                                        name:"smilies",
                                        method: 'resizeContainerbody',
                                        params:{
                                            height:default_height
                                        }
                                    };
                                    cometchat_group_popup.find('.cometchat_iframe')[0].contentWindow.postMessage('CC^CONTROL_'+JSON.stringify(paramstoresizeIframe),'*');
                                }else{
                                    cometchat_group_popup.find('.cometchat_iframe').height(default_height);////143 is the default height of sketch popup
                                }
                            }
                        }
                        cometchat_group_popup.find('.cometchat_popup_convo_plugins').css('bottom',29);
                        var scrolltop_height = parseInt(cometchat_group_popup.find('.cometchat_popup_convo_plugins').outerHeight() - cometchat_group_popup.find('.cometchat_pluginsbottom').outerHeight());
                        cometchat_group_popup.find('.cometchat_popup_convo_plugins').find('.slimScrollDiv').css({'top':scrolltop_height});
                    }
                },
                viewGroupUsers: function(groupid) {
                    var cometchat_group_popup = $("#cometchat_group_"+groupid+"_popup");
                    var tabtitle_content = '<div class="cometchat_backbutton_viewgroupuserspopup"></div><div class="cometchat_userstabtitletext" style="width: 80%;text-align: center;"><?php echo $chatrooms_language["group_users"];?></div>';

                    cometchat_group_popup.find('div.cometchat_tabtitle .cometchat_secondarytabtitle').html(tabtitle_content);
                    cometchat_group_popup.find('div.cometchat_tabtitle .cometchat_primarytabtitle').removeClass('cometchat_tabopen').addClass('cometchat_tabhidden');
                    cometchat_group_popup.find('div.cometchat_tabtitle .cometchat_secondarytabtitle').removeClass('cometchat_tabhidden').addClass('cometchat_tabopen');
                    cometchat_group_popup.find('div.cometchat_tabcontent .cometchat_primarytabcontent').removeClass('cometchat_tabopen').addClass('cometchat_tabhidden');
                    cometchat_group_popup.find('div.cometchat_tabcontent .cometchat_secondaryviewuserscontent').removeClass('cometchat_tabhidden').addClass('cometchat_tabopen');

                    cometchat_group_popup.find('div.cometchat_tabtitle .cometchat_secondarytabtitle .cometchat_backbutton_viewgroupuserspopup').click(function(e){
                        e.stopImmediatePropagation();
                        cometchat_group_popup.find('div.cometchat_tabtitle .cometchat_secondarytabtitle').removeClass('cometchat_tabopen').addClass('cometchat_tabhidden');
                        cometchat_group_popup.find('div.cometchat_tabtitle .cometchat_primarytabtitle').removeClass('cometchat_tabhidden').addClass('cometchat_tabopen');
                        cometchat_group_popup.find('div.cometchat_tabcontent .cometchat_secondaryviewuserscontent').removeClass('cometchat_tabopen').addClass('cometchat_tabhidden');
                        cometchat_group_popup.find('div.cometchat_tabcontent .cometchat_primarytabcontent').removeClass('cometchat_tabhidden').addClass('cometchat_tabopen');
                    });

                },
                inviteGroupUsers: function(groupid,groupdetails) {
                    var baseurl = jqcc.cometchat.getBaseUrl();
                    var basedata = jqcc.cometchat.getBaseData();
                    if(typeof(groupdetails)=='undefined') {
                        jqcc.cometchat.getChatroomDetails({id:groupid,force:1,callback:'inviteGroupUsers'});
                    }else {

                        var roompass = '';
                        if(groupdetails.hasOwnProperty('i')){
                            roompass = groupdetails.i;
                        }else if(groupdetails.hasOwnProperty('password')){
                             roompass = groupdetails.password;
                        }
                        var roomname = b2a(groupdetails.name);
                        var url = baseurl+'modules/chatrooms/chatrooms.php?action=invite&roomid='+groupid+'&inviteid='+roompass+'&basedata='+basedata+'&roomname='+roomname;

                        var cometchat_group_popup = $("#cometchat_group_"+groupid+"_popup");
                        var tabtitle_content = '<div class="cometchat_backbutton_viewgroupuserspopup"></div><div class="cometchat_userstabtitletext" style="width: 80%;text-align: center;"><?php echo $chatrooms_language[20];?></div>';

                        var inviteuser_content = '<div><iframe id="cometchat_inviteusers_iframe" src="'+url+'" height="316" width="100%" style="border:0px;"></div>';

                        cometchat_group_popup.find('div.cometchat_tabtitle .cometchat_secondarytabtitle').html(tabtitle_content);
                        cometchat_group_popup.find('div.cometchat_tabcontent .cometchat_secondaryinviteuserscontent').html(inviteuser_content);
                        cometchat_group_popup.find('div.cometchat_tabtitle .cometchat_primarytabtitle').removeClass('cometchat_tabopen').addClass('cometchat_tabhidden');
                        cometchat_group_popup.find('div.cometchat_tabtitle .cometchat_secondarytabtitle').removeClass('cometchat_tabhidden').addClass('cometchat_tabopen');
                        cometchat_group_popup.find('div.cometchat_tabcontent .cometchat_primarytabcontent').removeClass('cometchat_tabopen').addClass('cometchat_tabhidden');
                        cometchat_group_popup.find('div.cometchat_tabcontent .cometchat_secondaryinviteuserscontent').removeClass('cometchat_tabhidden').addClass('cometchat_tabopen');

                        cometchat_group_popup.find('div.cometchat_tabtitle .cometchat_secondarytabtitle .cometchat_backbutton_viewgroupuserspopup').click(function(e){
                            e.stopImmediatePropagation();
                            cometchat_group_popup.find('div.cometchat_tabtitle .cometchat_secondarytabtitle').removeClass('cometchat_tabopen').addClass('cometchat_tabhidden');
                            cometchat_group_popup.find('div.cometchat_tabtitle .cometchat_primarytabtitle').removeClass('cometchat_tabhidden').addClass('cometchat_tabopen');
                            cometchat_group_popup.find('div.cometchat_tabcontent .cometchat_secondaryinviteuserscontent').removeClass('cometchat_tabopen').addClass('cometchat_tabhidden');
                            cometchat_group_popup.find('div.cometchat_tabcontent .cometchat_primarytabcontent').removeClass('cometchat_tabhidden').addClass('cometchat_tabopen');
                        });
                    }
                },
                unbanGroupUsers: function(groupid,groupdetails) {
                    var baseurl = jqcc.cometchat.getBaseUrl();
                    var staticCDNUrl = jqcc.cometchat.getStaticCDNUrl();
                    var basedata = jqcc.cometchat.getBaseData();
                    if(jqcc.isEmptyObject(groupdetails)) {
                        jqcc.cometchat.getChatroomDetails({id:groupid,force:1,callback:'unbanGroupUsers'});
                        return;
                    }
                    var roompass = groupdetails.password;
                    var roomname = b2a(groupdetails.name);
                    var url = baseurl+'modules/chatrooms/chatrooms.php?action=unban&roomid='+groupid+'&inviteid='+roompass+'&basedata='+basedata+'&roomname='+roomname;

                    var cometchat_group_popup = $("#cometchat_group_"+groupid+"_popup");
                    var tabtitle_content = '<div class="cometchat_backbutton_viewgroupuserspopup"></div><div class="cometchat_userstabtitletext" style="width: 80%;text-align: center;"><?php echo $chatrooms_language[39];?></div>';

                    var inviteuser_content = '<div><iframe id="cometchat_inviteusers_iframe" src="'+url+'" height="316" width="100%" style="border:0px;"></div>';

                    cometchat_group_popup.find('div.cometchat_tabtitle .cometchat_secondarytabtitle').html(tabtitle_content);
                    cometchat_group_popup.find('div.cometchat_tabcontent .cometchat_secondaryinviteuserscontent').html(inviteuser_content);
                    cometchat_group_popup.find('div.cometchat_tabtitle .cometchat_primarytabtitle').removeClass('cometchat_tabopen').addClass('cometchat_tabhidden');
                    cometchat_group_popup.find('div.cometchat_tabtitle .cometchat_secondarytabtitle').removeClass('cometchat_tabhidden').addClass('cometchat_tabopen');
                    cometchat_group_popup.find('div.cometchat_tabcontent .cometchat_primarytabcontent').removeClass('cometchat_tabopen').addClass('cometchat_tabhidden');
                    cometchat_group_popup.find('div.cometchat_tabcontent .cometchat_secondaryinviteuserscontent').removeClass('cometchat_tabhidden').addClass('cometchat_tabopen');

                    cometchat_group_popup.find('div.cometchat_tabtitle .cometchat_secondarytabtitle .cometchat_backbutton_viewgroupuserspopup').click(function(e){
                        e.stopImmediatePropagation();
                        cometchat_group_popup.find('div.cometchat_tabtitle .cometchat_secondarytabtitle').removeClass('cometchat_tabopen').addClass('cometchat_tabhidden');
                        cometchat_group_popup.find('div.cometchat_tabtitle .cometchat_primarytabtitle').removeClass('cometchat_tabhidden').addClass('cometchat_tabopen');
                        cometchat_group_popup.find('div.cometchat_tabcontent .cometchat_secondaryinviteuserscontent').removeClass('cometchat_tabopen').addClass('cometchat_tabhidden');
                        cometchat_group_popup.find('div.cometchat_tabcontent .cometchat_primarytabcontent').removeClass('cometchat_tabhidden').addClass('cometchat_tabopen');
                    });
                },
                updateChatroomUsers: function(item,fetchedUsers) {
                    var temp = '';
                    var temp1 = '';
                    var roomid = 0;
                    var moderatorhtml = '';
                    var userhtml = '';
                    var newUsers = {};
                    var newUsersName = {};
                    var usercount = 0;
                    fetchedUsers = 1;
                    $.each(item, function(i,user) {
                        roomid = user.chatroomid;
                        longname = user.n;
                        if(settings.users[user.id] != 1 && settings.initializeRoom == 0) {
                            var nowTime = new Date();
                            var ts = Math.floor(nowTime.getTime()/1000);
                        }
                        if(parseInt(user.b)!=1) {
                            var userstatus = user.s;

                            if(typeof(userstatus) == "undefined" || userstatus == "undefined"){
                                userstatus = 'offline'
                            }

                            usercount++;
                            newUsers[user.id] = 1;
                            newUsersName[user.id] = user.n;
                            userhtml='<div class="cometchat_chats_labels"><?php echo $chatrooms_language[61]?></div>';
                            moderatorhtml='<div class="cometchat_chats_labels"><?php echo $chatrooms_language[62]?></div>';
                            var showavatar = '<div class="cometchat_userscontentavatar"><img class="cometchat_userscontentavatarimage" src="'+user.a+'"></div>';
                            if(jqcc.cometchat.getSettings().allowAvatar == 0){
                                showavatar = '';
                                $(".cometchat_userscontentname").css('margin-left', '0px', 'important');
                            }

                            if($.inArray(user.id ,jqcc.cometchat.getChatroomVars('moderators')) != -1) {
                                if(user.id == settings.myid) {
                                    temp1 += '<div id="chatroom_userlist_'+user.id+'" class="cometchat_groupuserlist cometchat_chatroomuserlist" style="cursor:default !important;" userid="'+user.id+'">'+showavatar+'<div><div class="cometchat_userscontentname">'+longname+'</div></div><div><div class="cometchat_userscontentdot cometchat_user_'+userstatus+'"></div><div class="cometchat_buddylist_status">'+userstatus+'</div></div></div>';
                                } else {
                                    temp1 += '<div id="chatroom_userlist_'+user.id+'" class="cometchat_groupuserlist cometchat_chatroomuserlist" onmouseover="jqcc(this).addClass(\'cometchat_userlist_hover\');" onmouseout="jqcc(this).removeClass(\'cometchat_userlist_hover\');"  userid="'+user.id+'" owner="'+settings.owner+'" username="'+user.n+'">'+showavatar+'<div><div class="cometchat_userscontentname">'+longname+'</div></div><div><div class="cometchat_userscontentdot cometchat_user_'+userstatus+'"></div><div class="cometchat_buddylist_status">'+userstatus+'</div></div></div>';
                                }
                            } else {
                                if(user.id == settings.myid) {
                                    temp += '<div id="chatroom_userlist_'+user.id+'" class="cometchat_groupuserlist cometchat_chatroomuserlist" style="cursor:default !important;" userid="'+user.id+'">'+showavatar+'<div><div class="cometchat_userscontentname">'+longname+'</div></div><div><div class="cometchat_userscontentdot cometchat_user_'+userstatus+'"></div><div class="cometchat_buddylist_status">'+userstatus+'</div></div></div>';
                                } else {
                                    var moderatoroptions = '';
                                    if($.inArray(settings.myid ,jqcc.cometchat.getChatroomVars('moderators')) != -1 || jqcc.cometchat.getChatroomVars('owner')) {
                                        moderatoroptions = '<div id="cometchat_moderatoroptions_'+user.id+'" class="cometchat_moderatoroptions"><input type=button id="cc_kick" value="<?php echo $chatrooms_language[40]?>" uid="'+user.id+'" chatroomid="'+roomid+'" class="moderatorbutton kickBan" /><input type=button id="cc_ban" value="<?php echo $chatrooms_language[41]?>" uid = "'+user.id+'" class="moderatorbutton kickBan" chatroomid="'+roomid+'" /><input type=button id="cc_chat" value="Chat" uid = "'+user.id+'" class="moderatorbutton chatwith" /></div>';
                                    }

                                    temp += '<div id="chatroom_userlist_'+user.id+'" class="cometchat_groupuserlist cometchat_chatroomuserlist" onmouseover="jqcc(this).addClass(\'cometchat_userlist_hover\');" onmouseout="jqcc(this).removeClass(\'cometchat_userlist_hover\');" userid="'+user.id+'" owner="'+settings.owner+'" username="'+user.n+'"><div class="cometchat_cruserlistcontent">'+showavatar+'<div><div class="cometchat_userscontentname">'+longname+'</div></div><div><div class="cometchat_userscontentdot cometchat_user_'+userstatus+'"></div><div class="cometchat_buddylist_status">'+userstatus+'</div></div></div>'+moderatoroptions+'</div>';
                                }
                            }
                        }
                    });



                    for (user in settings.users) {
                        if(settings.users.hasOwnProperty(user)) {
                            if(newUsers[user] != 1 && settings.initializeRoom == 0) {
                                var nowTime = new Date();
                                var ts = Math.floor(nowTime.getTime()/1000);
                            }
                        }
                    }

                    $('#cometchat_grouplist_'+roomid).find('.cometchat_groupusercount > span.cometchat_count').text(usercount);
                    var cometchat_group_popup = $("#cometchat_group_"+roomid+"_popup");
                    if(temp1 != "" && temp !="") {
                        cometchat_group_popup.find('div.cometchat_tabcontent .cometchat_secondaryviewuserscontent').html('<div>'+moderatorhtml+temp1+userhtml+temp+'</div>');
                    } else if(temp == "") {
                        cometchat_group_popup.find('div.cometchat_tabcontent .cometchat_secondaryviewuserscontent').html('<div>'+moderatorhtml+temp1+'</div>');
                    } else {
                        cometchat_group_popup.find('div.cometchat_tabcontent .cometchat_secondaryviewuserscontent').html('<div>'+userhtml+temp+'</div>');
                    }

                    var groupuserlistheight = cometchat_group_popup.find('.cometchat_tabcontent').innerHeight()+'px';
                    if(mobileDevice){
                        cometchat_group_popup.find('div.cometchat_tabcontent .cometchat_secondaryviewuserscontent > div').css({'height':groupuserlistheight,'overflow-y':'auto'});
                    }else if(jqcc().slimScroll){
                       cometchat_group_popup.find('div.cometchat_tabcontent .cometchat_secondaryviewuserscontent > div').css({'height':groupuserlistheight});
                       cometchat_group_popup.find('div.cometchat_tabcontent .cometchat_secondaryviewuserscontent > div').slimScroll({height: groupuserlistheight});
                    }

                    cometchat_group_popup.find('.moderatorbutton').on('click',function(e){
                        e.stopImmediatePropagation();
                        var uid = $(this).attr('uid');
                        var chatroomid = $(this).attr('chatroomid');
                        var method = $(this).attr('id');

                        if(method == 'cc_kick' && confirm("Are you sure!")){
                            jqcc[jqcc.cometchat.getChatroomVars('calleeAPI')].kickid(uid);
                            jqcc.cometchat.kickChatroomUser(uid,1,chatroomid);
                            cometchat_group_popup.find('#chatroom_userlist_'+uid).click();
                        } else if(method == 'cc_ban' && confirm("Are you sure!")) {
                            jqcc[jqcc.cometchat.getChatroomVars('calleeAPI')].banid(uid);
                            jqcc.cometchat.banChatroomUser(uid,1,chatroomid);
                            cometchat_group_popup.find('#chatroom_userlist_'+uid).click();
                        } else {
                            jqcc.cometchat.chatWith(uid);
                            cometchat_group_popup.find('#chatroom_userlist_'+uid).click();
                        }
                    });

                    cometchat_group_popup.find('.cometchat_chatroomuserlist').on('click',function(e){
                        e.stopImmediatePropagation();
                        var uid = $(this).attr('userid');
                        if(cometchat_group_popup.find('#chatroom_userlist_'+uid+' #cometchat_moderatoroptions_'+uid).length){
                            if(cometchat_group_popup.find('#cometchat_moderatoroptions_'+uid).css('display') == 'none') {
                                cometchat_group_popup.find('#chatroom_userlist_'+uid).animate({height: "64px"});
                                cometchat_group_popup.find('#cometchat_moderatoroptions_'+uid).css('display','block');
                            } else {
                                cometchat_group_popup.find('#chatroom_userlist_'+uid).animate({height: "32px"},{complete: function(){cometchat_group_popup.find('#cometchat_moderatoroptions_'+uid).css('display','none');}});
                            }
                        } else if(uid != settings.myid) {
                            jqcc.cometchat.chatWith(uid);
                        }
                    });

                    $(document).ready(function(){
                        if(jqcc.cometchat.getSettings().allowAvatar == 0){
                            $(".cometchat_userscontentname").css('margin-left', '0px', 'important');
                        }
                    });

                    jqcc.cometchat.setChatroomVars('users',newUsers);
                    jqcc.cometchat.setChatroomVars('usersName',newUsersName);
                    jqcc.cometchat.setChatroomVars('initializeRoom',0);
                },
                chatroomWindowResize: function() {
                    var w=window,d=document,e=d.documentElement,g=d.getElementsByTagName('body')[0],winWidth=w.innerWidth||e.clientWidth||g.clientWidth,winHt=w.innerHeight||e.clientHeight||g.clientHeight;
                    var searchbar_Height = $('#cometchat_chatroom_searchbar').is(':visible') ? $('#cometchat_chatroom_searchbar').outerHeight(true) : 0;
                    var createChatroomHeight = $('#create').is(':visible') ? $('#create').outerHeight(true) : 0;
                    var lobbyroomsHeight = $('#cometchat_tabcontainer').is(':visible') ? (winHt-$('#cometchat_self_container').outerHeight(true)-$('#cometchat_tabcontainer').outerHeight(true)-$('#cometchat_trayicons').outerHeight(true)-$('#createChatroomOption').outerHeight(true)-searchbar_Height-createChatroomHeight+'px') : (winHt-$('#cometchat_self_container').outerHeight(true)-$('#cometchat_trayicons').outerHeight(true)-$('#createChatroomOption').outerHeight(true)-searchbar_Height-createChatroomHeight+'px');
                    if($('#create').is(':visible') && mobileDevice ){
                        if(winWidth<winHt){
                            $('#cometchat_lefttab').find('#lobby').css('display','block');
                        } else{
                            $('#cometchat_lefttab').find('#lobby').css('display','none');
                        }
                    }
                    if($('#chatroomusers_popup').hasClass('cometchat_tabopen')){
                        var winHt = $(window).innerHeight();
                        var winWidth = $(window).innerWidth();
                        var tabsubtitleHt = $(".cometchat_userchatarea").find('.cometchat_tabsubtitle').outerHeight(true);
                        if((winWidth > winHt) && mobileDevice){
                            $('#chatroomusers_popup').css('max-height',(winHt-tabsubtitleHt-5));
                            $('#chatroomuser_container').css('max-height',(winHt-tabsubtitleHt-5));
                        } else{
                            $('#chatroomusers_popup').css('max-height','');
                            $('#chatroomuser_container').css('max-height','');
                        }
                    }
                    /*if(jqcc().slimScroll && !mobileDevice){
                        $('#lobby_rooms').parent('.slimScrollDiv').css('height',lobbyroomsHeight);
                    }
                    $('#lobby_rooms').css('height',lobbyroomsHeight);*/
                    var prependHeight = parseInt($('.cometchat_prependMessages_container').outerHeight(true));
                    var roomConvoHeight = winHt-$('#currentroom').find('.cometchat_ad').outerHeight(true)-$('.cometchat_tabinputcontainer').outerHeight(true)-($('#currentroom_left').find('.cometchat_tabsubtitle').outerHeight(true))-prependHeight;
                    if($('#cometchat_container_stickers').length != 1 && $('#cometchat_container_smilies').length != 1 && mobileDevice){
                        $("#currentroom_convo").css('height',roomConvoHeight+'px');
                    }
                    if(iOSmobileDevice && $('#cometchat_container_stickers').length != 1 && $('#cometchat_container_smilies').length != 1){
                        $('#currentroom').find('.cometchat_userchatarea').css('display','block');
                        $('#currentroom_convo').css('height',$(window).height()-(jqcc('#currentroom').find('.cometchat_tabsubtitle').outerHeight()+jqcc('#currentroom').find('.cometchat_tabinputcontainer').outerHeight(true)+jqcc('#currentroom').find('.cometchat_prependCrMessages').outerHeight(true)));
                    }
                },
                kickid: function(kickid) {
                    $("#chatroom_userlist_"+kickid).remove();
                },
                banid: function(banid) {
                    $("#chatroom_userlist_"+banid).remove();
                },
                chatroomScrollDown: function(forced, id) {
                    if(typeof id != "undefined"){
                        var grouppopup = $("#cometchat_group_"+id+"_popup");
                        if(mobileDevice){
                            grouppopup.find('#cometchat_grouptabcontenttext_'+id).css('overflow-y','auto');
                            grouppopup.find('#cometchat_grouptabcontenttext_'+id).scrollTop(10000000);
                        }else if(jqcc().slimScroll){
                            grouppopup.find('#cometchat_grouptabcontenttext_'+id).slimScroll({scroll: '1'});
                        }
                    }

                    /*if(settings.newMessageIndicator == 1 && ($('.cometchat_tabcontenttext').length > 0) && ($('.cometchat_tabcontenttext').outerHeight()+$('.cometchat_tabcontenttext').offset().top-$('.cometchat_tabcontent').height()-$('.cometchat_tabcontent').offset().top-(2*$('.cometchat_chatboxmessage').outerHeight(true))>0)){
                        if(($('.cometchat_tabcontent').height()-$('.cometchat_tabcontenttext').outerHeight()) < 0){
                            if(forced) {
                                if(jqcc().slimScroll && !mobileDevice){
                                    $('.cometchat_tabcontent').slimScroll({scroll: '1'});
                                } else {
                                    setTimeout(function() {
                                    $(".cometchat_tabcontent").scrollTop(50000);
                                    },100);
                                }
                                if($('.talkindicator').length != 0){
                                $('.talkindicator').fadeOut();
                                }
                            }else{
                                if(!$('.talkindicator').length != 0){
                                    var indicator = "<a class='talkindicator' href='#'><?php echo $chatrooms_language[52];?></a>";
                                    $('.cometchat_tabcontent').append(indicator);
                                    $('.talkindicator').click(function(e) {
                                        e.preventDefault();
                                        if(jqcc().slimScroll && !mobileDevice){
                                            $('.cometchat_tabcontent').slimScroll({scroll: '1'});
                                        } else {
                                            setTimeout(function() {
                                                $(".cometchat_tabcontent").scrollTop(50000);
                                            },100);
                                        }
                                        $('.talkindicator').fadeOut();
                                    });
                                    $('.cometchat_tabcontent').scroll(function(){
                                        if($('.cometchat_tabcontenttext').outerHeight() + $('.cometchat_tabcontenttext').offset().top - $('.cometchat_tabcontent').offset().top <= $('.cometchat_tabcontent').height()){
                                            $('.talkindicator').fadeOut();
                                        }
                                    });
                                }
                            }
                        }
                    }else{
                        if(jqcc().slimScroll && !mobileDevice){
                            $('.cometchat_tabcontent').slimScroll({scroll: '1'});
                        } else {
                            setTimeout(function() {
                                $(".cometchat_tabcontent").scrollTop(50000);
                            },100);
                        }
                    }*/
                },
                createChatroomSubmitStruct: function() {
                    var string = $('input.create_input').val();
                    var room={};
                    if(($.trim( string )).length == 0) {
                        return false;
                    }
                    var name = $("#cometchat").find("#name").val();
                    name = (name).replace(/'/g, "%27");
                    var type = $("#cometchat").find("#type").val();
                    var password = document.getElementById('cometchat_chatroom_password').value;
                    if(name != '' && name != null && name != "<?php echo $chatrooms_language['name'];?>") {
                        name = name.replace(/^\s+|\s+$/g,"");
                        if(type == 1 && password == '') {
                            alert ("<?php echo $chatrooms_language['enter_password'];?>");
                            return 'invalid password';
                        }
                        if(type == 0 || type == 2) {
                            password = '';
                        }
                        room['name'] = name;
                        room['password'] = password;
                        room['type'] = type;
                    }else{
                        alert("<?php echo $chatrooms_language['enter_roomname'];?>");
                        return false;
                    }
                    document.getElementById('name').value = '';
                    document.getElementById('cometchat_chatroom_password').value = '';
                    return room;
                },
                crgetWindowHeight: function() {
                },
                crgetWindowWidth: function() {
                },
                selectChatroom: function(currentroom,id) {
                },
                checkOwnership: function(owner,isModerator,name) {
                },
                leaveRoomClass : function(currentroom) {
                    /*jqcc("#cometchat_chatroomlist_"+currentroom).removeClass("cometchat_chatroomselected");*/
                },
                removeCurrentRoomTab : function(id) {
                },
                updateGroupCategory : function(id) {
                    if($('#cometchat_grouplist_'+id).length){
                        var element = $('#cometchat_grouplist_'+id).detach();
                        $('#cometchat_othergroupslist').append(element);
                        var count = parseInt(element.find('.cometchat_groupusercount > span.cometchat_count').text()) - 1;
                        element.find('.cometchat_groupusercount > span.cometchat_count').text(count);
                    }

                    if($('#cometchat_othergroupslist').children().length > 0 && $('#cometchat_othergroups').length == 0){
                        $('#cometchat_othergroupslist').prepend('<div id="cometchat_othergroups" class="cometchat_groupsclassifier"><div class="cometchat_chats_labels"><?php echo $chatrooms_language[77];?></div></div>');
                    }

                    if($('#cometchat_joinedgroupslist').children().length > 0 && $('#cometchat_joinedgroups').length == 0){
                        $('#cometchat_joinedgroupslist').prepend('<div id="cometchat_joinedgroups" class="cometchat_groupsclassifier"><div class="cometchat_chats_labels"><?php echo $chatrooms_language[78];?></div></div>');
                    }

                    if($('#cometchat_joinedgroupslist').children().length == 0) {
                        $('#cometchat_joinedgroups').remove();
                        $('#cometchat_othergroups').remove();
                    }
                },
                chatroomLogout : function() {
                },
                loadChatroomList : function(item) {
                    var temp = '';
                    var joinedgroupslist = '<div id="cometchat_joinedgroupslist">';
                    var othergroupslist = '<div id="cometchat_othergroupslist">';
                    var joinedgroups = 0;
                    var othergroups = 0;
                    var userCountCss = "style='display:none'";
                    var joinedgroupshtml = '';
                    var othergroupshtml = '';
                    var chatrooms = {};
                    var unreadmessagecount;
                    var msgcountercss;
                    if(settings.showChatroomUsers == 1){
                        userCountCss = '';
                    }
                    $.each(item, function(i,room) {
                        chatrooms[i] = room;
                        jqcc.cometchat.setChatroomVars('chatroomdetails',chatrooms);

                        longname = room.name;
                        shortname = room.name;

                        unreadmessagecount = jqcc.cometchat.getUnreadMessageCount({groups: [parseInt(room.id)]});
                        msgcountercss = "display:none;";
                        if(unreadmessagecount!=0){
                            msgcountercss = "";
                        }

                        var roomtype = '';
                        var roomowner = '';
                        var deleteroom = '';
                        var renamegroup = '';
                        var usercount = '<div class="cometchat_groupusercount" '+userCountCss+'><?php echo $chatrooms_language["participants"];?>: <span class="cometchat_count">'+room.online+'</span></div>';

                        if(room.type == 1) {
                            roomtype = '<div class="cometchat_grouptype"><img src="'+staticCDNUrl+'layouts/'+calleeAPI+'/images/lock.png" class="cometchat_grouptypeimage" /></div>';
                        }

                        if(room.owner == 1) {
                            roomowner = '<img src="'+staticCDNUrl+'layouts/'+calleeAPI+'/images/user.png" />';
                        }

                        var deletegroup = '';
                        if(room.owner == true){
                            renamegroup = '<div class="cometchat_grouprename" title="<?php echo $chatrooms_language[80]; ?>" onclick = "javascript:jqcc.'+[calleeAPI]+'.renameChatroom(event,\''+room.id+'\');"><img class="cometchat_grouprenameimage" src="'+staticCDNUrl+'layouts/'+calleeAPI+'/images/pencil_buddylist.png"></div>';
                            deletegroup = '<div class="cometchat_groupdelete" title="<?php echo $chatrooms_language[58]; ?>" onclick = "javascript:jqcc.cometchat.deleteChatroom(event,'+room.id+');"><img class="hoverbraces" src="'+staticCDNUrl+'layouts/docked/images/bin.svg"></div>';
                        }

                        temp = '<div id="cometchat_grouplist_'+room.id+'" class="cometchat_grouplist" onmouseover="jqcc(this).addClass(\'cometchat_grouplist_hover\');" onmouseout="jqcc(this).removeClass(\'cometchat_grouplist_hover\');" onclick="javascript:jqcc.cometchat.chatroom(\''+room.id+'\',\''+cc_urlencode(shortname)+'\',\''+room.type+'\',\''+room.i+'\',\''+room.s+'\',\'1\',\'1\');" amount="'+unreadmessagecount+'"><div class="cometchat_groupscontentavatar"><img class="cometchat_groupscontentavatarimage" src="'+staticCDNUrl+'layouts/'+calleeAPI+'/images/group.svg"></div><div><div class="cometchat_groupscontentname">'+longname+'</div></div>'+deletegroup+renamegroup+roomtype+usercount+'<div class="cometchat_unreadCount cometchat_floatR" style="'+msgcountercss+'">'+unreadmessagecount+'</div></div>';

                        if(room.j === 1) {
                            jqcc.cometchat.joinGroup(room.id);
                            joinedgroups++;
                            joinedgroupslist += temp;
                        } else {
                            othergroups++;
                            othergroupslist += temp;
                        }
                    });

                    joinedgroupslist += '</div>';
                    othergroupslist += '</div>';
                    jqcc.cometchat.refreshRecentChats();

                    if(document.getElementById('cometchat_groupslist_content')){
                        if(Object.keys(item).length != 0) {
                            if(joinedgroups>0){
                                joinedgroupshtml = '<div id="cometchat_joinedgroups" class="cometchat_groupsclassifier"><div class="cometchat_chats_labels"><?php echo $chatrooms_language[78];?></div></div>';
                            }
                            if(othergroups>0 && joinedgroups>0){
                                othergroupshtml = '<div id="cometchat_othergroups" class="cometchat_groupsclassifier"><div class="cometchat_chats_labels"><?php echo $chatrooms_language[77];?></div></div>';
                            }
                            jqcc.cometchat.replaceHtml('cometchat_groupslist_content', '<div>'+joinedgroupshtml+joinedgroupslist+othergroupshtml+othergroupslist+'</div>');
                        }else{
                            jqcc('#cometchat_groupslist_content').html('<div class="cometchat_nogroupcreated"><div class="cometchat_nogroups"><?php echo $chatrooms_language[53]; ?></div></div>');
                        }

                        var userstabpopup = jqcc('#cometchat_userstab_popup');
                        if(jqcc.cometchat.getThemeVariable('hasSearchbox')){
                            var grouplistheight = ($(".right_footer").length == 1) ? "240px" : "259px";
                        } else {
                            var grouplistheight = ($(".right_footer").length == 1) ? "270px" : "286px";
                        }
                        /*if(settings.allowUsers){
                            grouplistheight = grouplistheight-28;
                        }*/
                        if(mobileDevice){
                            userstabpopup.find('#cometchat_userscontent #cometchat_groupslist_content > div').css({'height': grouplistheight});
                            userstabpopup.find('#cometchat_userscontent #cometchat_groupslist_content > div').css('overflow-y','auto');
                        }else if(jqcc().slimScroll){
                            userstabpopup.find('#cometchat_userscontent #cometchat_groupslist_content > div').css({'height': grouplistheight});
                            userstabpopup.find('#cometchat_userscontent').slimScroll({height: grouplistheight});
                        }
                    }
                },
                displayChatroomMessage: function(item,fetchedUsers) {
                    var beepNewMessages = 0,
                        chatroomreadmessages = jqcc.cometchat.getFromStorage("crreadmessages"),
                        todaysdate = new Date(),
                        tdmonth  = todaysdate.getMonth(),
                        tddate  = todaysdate.getDate(),
                        tdyear = todaysdate.getFullYear(),
                        today_date_class = tdmonth+"_"+tddate+"_"+tdyear,
                        ydaysdate = new Date((new Date()).getTime() - 3600000 * 24),
                        ydmonth  = ydaysdate.getMonth(),
                        yddate  = ydaysdate.getDate(),
                        ydyear = ydaysdate.getFullYear(),
                        yday_date_class = ydmonth+"_"+yddate+"_"+ydyear,
                        d = '',
                        month = '',
                        date  = '',
                        year = '',
                        msg_date_class = '',
                        msg_date = '',
                        date_class = '',
                        msg_date_format = '',
                        cometchat_del_style = '',
                        cc_dir = '<?php if ($rtl == 1) { echo 1; } else { echo 0; }?>',
                        prepend = '',
                        localmessageid = '',
                        avatarstofetch = {},
                        trayIcons = jqcc.cometchat.getTrayicon(),
                        isRealtimetranslateEnabled = trayIcons.hasOwnProperty('realtimetranslate');
                    $.each(item, function(i, incoming) {
                    	var message = jqcc.cometchat.processcontrolmessage(incoming);
                        if($('#cometchat_group_'+incoming.chatroomid+'_popup').length != 0 && $('#cometchat_group_'+incoming.chatroomid+'_popup').find("#cometchat_grouptabcontenttext_"+incoming.chatroomid).length != 0){
                            var grouppopup = $('#cometchat_group_'+incoming.chatroomid+'_popup');
                            var messagewrapperid = '';
                            incoming.message = jqcc.cometchat.htmlEntities(incoming.message);
                            if(incoming.fromid == settings.myid){
                                incoming.from = "<?php echo $chatrooms_language['me'];?>";
                            }

                            if( (!incoming.hasOwnProperty('id') || incoming.id == '') && (!incoming.hasOwnProperty('localmessageid') || incoming.localmessageid == '') && (incoming.message).indexOf('CC^CONTROL_')==-1){
                                return;
                            }

                            if(isRealtimetranslateEnabled && jqcc.cookie(settings.cookiePrefix+'rttlang') && incoming.fromid != settings.myid && (incoming.message).indexOf('CC^CONTROL_') == -1){
                                text_translate(incoming);
                            }

                            if(incoming.hasOwnProperty('id') && !incoming.hasOwnProperty('localmessageid')) {
                                messagewrapperid = incoming.id;
                            }else if( !incoming.hasOwnProperty('id') && incoming.hasOwnProperty('localmessageid') ) {
                                messagewrapperid = incoming.localmessageid;
                            }else{
                                messagewrapperid = incoming.id;
                                if($("#cometchat_groupmessage_"+incoming.localmessageid).length>0){
                                    $("#cometchat_groupmessage_"+incoming.localmessageid).attr('id',"cometchat_groupmessage_"+incoming.id);
                                    $("#cometchat_chatboxseen_"+incoming.localmessageid).attr('id',"cometchat_chatboxseen_"+incoming.id).removeClass("cometchat_offlinemessage");
                                    $("#message_"+incoming.localmessageid).attr('id','message_'+incoming.id);
                                    var offlinemessages = jqcc.cometchat.getFromStorage('offlinemessagesqueue');
                                    if(offlinemessages.hasOwnProperty(incoming.localmessageid)) {
                                        delete offlinemessages[incoming.localmessageid];
                                        jqcc.cometchat.updateToStorage('offlinemessagesqueue',offlinemessages);
                                    }
                                    return;
                                }
                            }

                            jqcc.cometchat.setChatroomVars('timestamp',incoming.id);
                            var msg_time = jqcc.cometchat.processTimestamp(incoming.sent),
                                incomingself = 1,
                                months_set = [];

                            <?php
                            $months_array = array($chatrooms_language[90],$chatrooms_language[91],$chatrooms_language[92],$chatrooms_language[93],$chatrooms_language[94],$chatrooms_language[95],$chatrooms_language[96],$chatrooms_language[97],$chatrooms_language[98],$chatrooms_language[99],$chatrooms_language[101],$chatrooms_language[102]);

                            foreach($months_array as $key => $val){
                                ?>
                                months_set.push('<?php echo $val; ?>');
                                <?php
                            }
                            ?>

                            d = new Date(parseInt(msg_time));
                            month  = d.getMonth();
                            date  = d.getDate();
                            year = d.getFullYear();

                            msg_date_class = month+"_"+date+"_"+year;
                            msg_date = months_set[month]+" "+date+", "+year;

                            var type = 'th';
                            if(date==1||date==21||date==31){
                                type = 'st';
                            }else if(date==2||date==22){
                                type = 'nd';
                            }else if(date==3||date==23){
                                type = 'rd';
                            }
                            msg_date_format = date+type+' '+months_set[month]+', '+year;

                            if(msg_date_class == today_date_class){
                                date_class = "today";
                                msg_date = "<?php echo $chatrooms_language['today']; ?>";
                            }else  if(msg_date_class == yday_date_class){
                                date_class = "yesterday";
                                msg_date = "<?php echo $chatrooms_language['yesterday']; ?>";
                            }
                            if (message != '' && typeof(message) != 'undefined') {
                                var temp = '';
                                fromname = incoming.from,
                                sent = incoming.sent,
                                ts = parseInt(sent),
                                add_bg = '',
                                add_arrow_class = '',
                                add_style = "",
                                sentdata = $[calleeAPI].getTimeDisplay(ts,incoming.id),
                                fromid = incoming.fromid,
                                marginclass = '',
                                smileycount = (message.match(/cometchat_smiley/g) || []).length,
                                smileymsg = message.replace(/<img[^>]*>/g,"");
                                smileymsg = smileymsg.trim();

                                if(incoming.hasOwnProperty('self')){
                                    incomingself = incoming.self;
                                }
                                if(fromid != settings.myid) {
                                    incomingself = 0;
                                }
                                if(smileycount == 1 && smileymsg == '') {
                                    message = message.replace('height="20"', 'height="64px"');
                                    message = message.replace('width="20"', 'width="64px"');
                                }

                                if((incoming.message).indexOf('has shared a file')!=-1){
                                    if(jqcc.cometchat.getCcvariable().callbackfn=="desktop"){
                                        if(incoming.message.indexOf('target')>=-1){
                                            incoming.message=incoming.message.replace(/target="_blank"/g,'');
                                        }
                                    }
                                }
                                if((incoming.message).indexOf('has shared a handwritten message')!=-1){
                                    if(jqcc.cometchat.getCcvariable().callbackfn=="desktop"){
                                        if(incoming.message.indexOf('href')>=-1){
                                            var start = (incoming.message).indexOf('href');
                                            var end = (incoming.message).indexOf('target');
                                            var HtmlString=(incoming.message).slice(start,end);
                                            incoming.message=(incoming.message).replace(HtmlString,'');
                                        }
                                    }
                                }
                                if($("#cometchat_groupmessage_"+incoming.id).length > 0) {
                                    $("#cometchat_groupmessage_"+incoming.id).find("span.cometchat_chatboxmessagecontent").html(message);
                                } else {
                                    var ts = parseInt(incoming.sent)*1000;
                                    if(!incomingself) {
                                        var avatar = jqcc.cometchat.getThemeArray('buddylistAvatar', incoming.fromid);
                                        if(typeof avatar=="undefined"){
                                            avatarstofetch[incoming.fromid]=1;
                                            avatar = staticCDNUrl+'images/noavatar.png';
                                        }
                                        var fromavatar = '<a class="cometchat_floatL"><img class="cometchat_userscontentavatarsmall cometchat_avatar_'+incoming.fromid+'" src="'+avatar+'" title="'+fromname+'"/></a>';
                                        var sentdata = $[calleeAPI].getTimeDisplay(ts,incoming.id);
                                        var hw_ts = '';
                                        if(message.indexOf('cometchat_hw_lang')!=-1){
                                            hw_ts = 'margin-left: 4px';
                                        }
                                        var sentdata_box = "<span class=\"cometchat_ts_other\" style='"+hw_ts+"'>"+sentdata+"</span>";
                                        if((message.indexOf('<img')==-1 && message.indexOf('src')==-1) || (smileycount > 0 && smileymsg != '')){
                                            add_bg = 'cometchat_chatboxmessagecontent';
                                            add_arrow_class = '<div class="msgArrow"><div class="after"></div></div>';
                                        }else{
                                            if(message.indexOf('cometchat_smiley')!=-1) {
                                                add_style = "margin:5px 5px 0px 8px;max-width:135px;";
                                            }else if(message.indexOf('cometchat_hw_lang')!=-1){
                                                add_style = "margin:0px 0px 0px 8px";
                                            }else{
                                                add_style = "margin:-6px 0px 0px 8px";
                                            }
                                        }
                                        var usernamecontent = '';
                                        if (showUsername == '1') {
                                            usernamecontent = '<span class="cometchat_groupusername">'+fromname+':</span><br>';
                                        }
                                        if(incoming.hasOwnProperty('botid') && incoming.botid != 0) {
                                            fromavatar = '<a class="cometchat_floatL"><img class="cometchat_userscontentavatarsmall" src="'+jqcc.cometchat.getThemeArray('botlistAvatar', incoming.botid)+'" title="'+jqcc.cometchat.getThemeArray('botlistName', incoming.botid)+'"/></a>';
                                            if (showUsername == '1') {
                                                usernamecontent = '<span class="cometchat_groupusername">'+jqcc.cometchat.getThemeArray('botlistName', incoming.botid)+':</span><br>';
                                            }
                                        }
                                        temp += '<div class="cometchat_time cometchat_time_'+msg_date_class+' '+date_class+'" msg_format="'+msg_date_format+'">'+msg_date+'</div><div class="cometchat_chatboxmessage" id="cometchat_groupmessage_'+incoming.id+'">'+fromavatar+'<div class="'+add_bg+' '+'cometchat_ts_margin cometchat_floatL" style="'+add_style+'">'+usernamecontent+'<span id="cc_groupmessage_'+incoming.id+'" class="cometchat_msg">'+message+'</span></div>'+sentdata_box+' '+add_arrow_class+'</div>';
                                        beepNewMessages++;
                                    } else {
                                        var sentdata = $[calleeAPI].getTimeDisplay(ts,incoming.id);
                                        var sentdata_box = "<span class=\"cometchat_ts\">"+sentdata+"</span>";
                                        if((message.indexOf('<img')==-1 && message.indexOf('src')==-1) || (smileycount > 0 && smileymsg != '')){
                                            add_bg = 'cometchat_chatboxmessagecontent cometchat_self';
                                            add_arrow_class = '<div class="selfMsgArrow"><div class="after"></div></div>';
                                        }else{
                                            if(message.indexOf('cometchat_smiley')!=-1) {
                                                add_style = "margin-right:13px;max-width:135px;";
                                            }else{
                                                add_style = "margin-right:4px;margin-left:4px";
                                            }
                                        }
                                        temp += '<div class="cometchat_time cometchat_time_'+msg_date_class+' '+date_class+'" msg_format="'+msg_date_format+'">'+msg_date+'</div><div class="cometchat_chatboxmessage" id="cometchat_groupmessage_'+incoming.id+'"><div class="'+add_bg+' '+'cometchat_ts_margin cometchat_self_msg cometchat_floatR" style="'+add_style+'"><span id="cc_groupmessage_'+incoming.id+'" class="cometchat_msg">'+message+'</span><span id="cometchat_chatboxseen_'+incoming.id+'"></span></div>'+sentdata_box+''+add_arrow_class+'</div>';
                                    }
                                    grouppopup.find("#cometchat_grouptabcontenttext_"+incoming.chatroomid).append(temp);
                                }

                                if($(".cometchat_ts_margin").next().hasClass("cometchat_ts")){
                                    var msg_containerHeight = $("#cometchat_groupmessage_"+incoming.id+" .cometchat_ts_margin").outerHeight();
                                    var cometchat_ts_margin_right = $("#cometchat_groupmessage_"+incoming.id+" .cometchat_ts_margin").outerWidth(true)+3;
                                    jqcc('#cometchat_groupmessage_'+incoming.id).find('.cometchat_ts').css('margin-top',(msg_containerHeight-8));
                                    jqcc('#cometchat_groupmessage_'+incoming.id).find('.cometchat_ts').css({'margin-right':cometchat_ts_margin_right});
                                }

                                if($(".cometchat_ts_margin").next().hasClass("cometchat_ts_other")){
                                    var cometchat_ts_margin_left = $("#cometchat_groupmessage_"+incoming.id+" .cometchat_ts_margin").outerWidth();
                                    if(cometchat_ts_margin_left >= 135){
                                        jqcc('#cometchat_groupmessage_'+incoming.id).find('.cometchat_ts_other');
                                    }else if(cc_dir == 1){
                                        jqcc('#cometchat_groupmessage_'+incoming.id).find('.cometchat_ts_other').css({'margin-left':cometchat_ts_margin_left+30});
                                    }
                                }
                                if(typeof(message) != 'undefined' && (jqcc.cometchat.getChatroomVars('owner') || jqcc.cometchat.checkModerator(incoming.groupid) || (jqcc.cometchat.getChatroomVars('allowDelete') == 1 && incomingself))) {
                                    if(grouppopup.find("#cometchat_groupmessage_"+incoming.id).find(".delete_msg").length < 1) {
                                        if(incomingself){
                                            cometchat_ts_class = 'cometchat_ts';
                                            cometchat_ts_style = 'float:right';

                                            if(message.indexOf('imagemessage mediamessage')!=-1) {
                                                cometchat_ts_style = cometchat_ts_style+';margin-top:12px';
                                            }
                                        }else{
                                            cometchat_ts_class = 'cometchat_ts_other';
                                            var cometchat_ts_other_width = $("#cometchat_groupmessage_"+incoming.id+" .cometchat_ts_margin").outerWidth();
                                            if(cometchat_ts_other_width >= 135){
                                                cometchat_ts_style = 'float:left;margin-left:-23px';
                                            }else{
                                                if(message.indexOf('imagemessage mediamessage')!=-1) {
                                                    cometchat_ts_style = 'float:left;margin-left:-6px;margin-top:10px';
                                                }else{
                                                    cometchat_ts_style = 'float:left;margin-left:-3px';
                                                }
                                            }
                                        }
                                        if(grouppopup.find("#cometchat_groupmessage_"+incoming.id).find(".cometchat_ts_other").length < 1) {
                                            if((message.indexOf('<img')==-1 && message.indexOf('src')==-1) || (smileycount > 0 && smileymsg != '')){
                                                cometchat_del_style = '';
                                            }else {
                                                if(message.indexOf('cometchat_smiley')!=-1) {
                                                    cometchat_del_style = 'margin: 0px 0px 14px 0px';
                                                }
                                            }
                                        }else{
                                            cometchat_del_style = '';
                                        }
                                        grouppopup.find('#cometchat_groupmessage_'+incoming.id).find("."+cometchat_ts_class).after('<span class="delete_msg" style="'+cometchat_ts_style+';'+cometchat_del_style+';" onclick="javascript:jqcc.cometchat.confirmDelete(\''+incoming.id+'\',\''+incoming.chatroomid+'\');"><img class="hoverbraces" src="'+staticCDNUrl+'layouts/docked/images/bin.svg"></span>');
                                    }

                                    $(".cometchat_chatboxmessage").live("mouseover",function() {
                                        $(this).find(".delete_msg").css('opacity','1');
                                        var msg_containerHeight = $(this).find(".cometchat_ts_margin").outerHeight();
                                        var cometchat_ts_margin_right = $(this).find(".cometchat_ts_margin").outerWidth(true)+3;
                                        $(this).find('.cometchat_ts').css('margin-top',(msg_containerHeight-8));
                                        $(this).find('.cometchat_ts').css('margin-right',cometchat_ts_margin_right);

                                    });
                                    $(".cometchat_chatboxmessage").live("mouseout",function() {
                                        $(this).find(".delete_msg").css('opacity','0');
                                        var msg_containerHeight = $(this).find(".cometchat_ts_margin").outerHeight();
                                        var cometchat_ts_margin_right = $(this).find(".cometchat_ts_margin").outerWidth(true)+3;
                                        $(this).find('.cometchat_ts').css('margin-top',(msg_containerHeight-8));
                                        $(this).find('.cometchat_ts').css('margin-right',cometchat_ts_margin_right);
                                    });
                                    $(".delete_msg").mouseover(function() {
                                        $(this).find(".delete_msg").css('opacity','1');
                                    });
                                    $(".delete_msg").mouseout(function() {
                                        $(this).find(".delete_msg").css('opacity','0');
                                    });
                                }
                                var forced = (incomingself) ? 1 : 0;

                                if((message).indexOf('<img')!=-1 && (message).indexOf('src')!=-1){
                                    $( "#cometchat_groupmessage_"+incoming.id+" img" ).load(function() {
                                        var cometchat_ts_margin_right = $("#cometchat_groupmessage_"+incoming.id+" .cometchat_ts_margin").outerWidth(true)+2;
                                        jqcc('#cometchat_groupmessage_'+incoming.id).find('.cometchat_ts').css({'margin-right':cometchat_ts_margin_right});
                                        $[calleeAPI].chatroomScrollDown(forced,incoming.chatroomid);
                                    });
                                }
                                $[calleeAPI].chatroomScrollDown(forced,incoming.chatroomid);

                                if(jqcc.cometchat.getSettings().disableRecentTab == 0) {
                                    var temp_msg = jqcc.cometchat.processRecentmessages(message);
                                    var params = {'chatid':incoming.chatroomid,'isgroup':1,'timestamp':incoming.sent,'m':temp_msg,'msgid':incoming.id,'force':0,'del':0};
                                    jqcc.cometchat.updateRecentChats(params);
                                }
                            }
                        }
                        if (message != '' && jqcc.cometchat.getExternalVariable('lastgroupmessageid') < incoming.id){
                             if (typeof(jqcc[jqcc.cometchat.getChatroomVars('calleeAPI')].addMessageCounter) == "function" && !incomingself){
                                if ((typeof $.cookie(settings.cookiePrefix+"sound") == 'undefined' || $.cookie(settings.cookiePrefix+"sound") == null) || $.cookie(settings.cookiePrefix + "sound") == 'true') {
                                    $[calleeAPI].playSound(0);
                                }
                                jqcc[jqcc.cometchat.getChatroomVars('calleeAPI')].addMessageCounter(incoming.groupid,1);
                            }
                        }
                    });
                        if(!$.isEmptyObject(avatarstofetch)){
                            jqcc.cometchat.getUserDetails(Object.keys(avatarstofetch),'updateView');
                        }

                        jqcc.cometchat.setChatroomVars('heartbeatCount',1);
                        jqcc.cometchat.setChatroomVars('heartbeatTime',settings.minHeartbeat);

                        var current_roomid = '';
                        if(item != '' || typeof(item) != "undefined"){
                            current_roomid = item[0].chatroomid;
                            jqcc.crdocked.groupbyDate(current_roomid);

                            $('#cometchat_grouptabcontenttext_'+current_roomid).find('.cometchat_prependCrMessages').remove();
                            prepend = '<div class=\"cometchat_prependCrMessages\" onclick\="jqcc.crdocked.prependCrMessagesInit('+current_roomid+')\" id = \"cometchat_prependCrMessages_'+current_roomid+'\"><?php echo $chatrooms_language[74];?></div>';

                            if($('#cometchat_grouptabcontenttext_'+current_roomid+' .cometchat_prependMessages').length != 1){
                                    $('#cometchat_grouptabcontenttext_'+current_roomid).prepend(prepend);

                            }
                            $[calleeAPI].chatroomScrollDown(1,current_roomid);
                        }
                    },
                    silentRoom: function(id, name, silent) {
                        basedata = jqcc.cometchat.getBaseData();
                        if(settings.lightboxWindows == 1) {
                            var controlparameters = {
                                type: 'modules',
                                name: 'core',
                                method: 'loadCCPopup',
                                params: {
                                    url: settings.baseUrl+'modules/chatrooms/chatrooms.php?id='+id+'&basedata='+basedata+'&name='+name+'&silent='+silent+'&action=passwordBox',
                                    name: 'passwordBox',
                                    properties: 'status=0, toolbar=0, menubar=0, directories=0, resizable=0, location=0, status=0, scrollbars=1,  width=320, height=130',
                                    width: '320',
                                    height: '110',
                                    title: urldecode(name),
                                    force: null,
                                    allowmaximize: null,
                                    allowresize: null,
                                    allowpopout: null,
                                    windowMode: null
                                }
                            };
                            controlparameters = JSON.stringify(controlparameters);
                            parent.postMessage('CC^CONTROL_'+controlparameters,'*');
                        } else {
                            var temp = prompt("<?php echo $chatrooms_language['chatroom_password'];?>",'');
                            if(temp) {
                                jqcc.cometchat.checkChatroomPass(id,name,silent,temp);
                            } else {
                                return;
                            }
                        }
                    },
                    switchChatroom: function(id, force) {
                        return;
                    },
                    renameChatroom: function(event,id){
                        event.stopPropagation();
                        jqcc('.cancel_edit').click();

                        jqcc('#cometchat_grouplist_'+id).append('<div class="cometchat_chatroom_overlay"><input class="chatroomName" type="textbox" value="0"/><a title="<?php echo $chatrooms_language[51];?>" class="cancel_edit" href="javascript:void(0);" onclick="javascript:jqcc.'+jqcc.cometchat.getChatroomVars('calleeAPI')+'.canceledit(event,\''+id+'\');" style="display:none;"><?php echo $chatrooms_language[51];?></a></div>');

                        var currentroomname = jqcc('#cometchat_grouplist_'+id).find('.cometchat_groupscontentname').html();
                        jqcc('#cometchat_grouplist_'+id).find('.cometchat_groupscontentname').show();
                        jqcc('#cometchat_grouplist_'+id).find('.cancel_edit').show();
                        jqcc('#cometchat_grouplist_'+id).find('.chatroomName').val(currentroomname);

                        jqcc('.chatroomName').on('click', function(e) {
                            e.stopPropagation();
                        });
                        jqcc('.cometchat_chatroom_overlay').on('click', function(e) {
                            e.stopPropagation();
                            var cname = jqcc('#cometchat_grouplist_'+id).find('.chatroomName').val();
                            jqcc[jqcc.cometchat.getChatroomVars('calleeAPI')].renameChatroomsubmit(id,currentroomname,cname);
                        });
                        jqcc(".chatroomName").keydown(function(e) {
                            if (e.keyCode == 13) {
                                var cname = jqcc(this).val();
                                jqcc[jqcc.cometchat.getChatroomVars('calleeAPI')].renameChatroomsubmit(id,currentroomname,cname);
                            }
                        });
                    },
                    renameChatroomsubmit: function(id, currentroomname, name) {
                        var baseUrl = settings.baseUrl;
                        var basedata = jqcc.cometchat.getBaseData();
                        var guestMode = jqcc.cometchat.getSettings().guestMode;
                        var url = baseUrl+"modules/chatrooms/chatrooms.php?action=renamechatroom";
                        if(guestMode){
                            var queryString = settings.cookiePrefix+'guest'+'='+jqcc.cookie(settings.cookiePrefix+'guest');
                            url += "&cookie_"+queryString;
                        }
                        name = name.trim();
                        name = decodeURI(name);
                        if(currentroomname != name) {
                            name = encodeURI(name);
                            jqcc.ajax({
                                url: url,
                                data: {id: id, basedata: basedata, cname: name},
                                type: 'post',
                                cache: false,
                                timeout: 10000,
                                async: false,
                                success: function(data) {
                                    if (data == 0) {
                                        alert("<?php echo $chatrooms_language['roomname_not_available'];?>");
                                    }else{
                                        jqcc('#cometchat_grouplist_'+id).find('.cancel_edit').hide();
                                        jqcc('#cometchat_grouplist_'+id).find('.currentroomname').css('visibility','visible');
                                        jqcc('#cometchat_grouplist_'+id).find('.chatroomName').hide();
                                        name = decodeURI(name);
                                        if(currentroomname == jqcc('.cometchat_chatroomdisplayname').text()){
                                            jqcc('.cometchat_chatroomdisplayname').text(name);
                                        }

                                        $('#cometchat_group_'+id+'_popup').find('.cometchat_groupname').text(name);
                                        $('#cometchat_grouplist_'+id).find('.cometchat_groupscontentname').text(name);
                                        jqcc.cometchat.chatroomHeartbeat(1);
                                    }
                                }
                            });
                        } else {
                            jqcc('#cometchat_grouplist_'+id).find('.cancel_edit').click();
                        }
                    },
                    canceledit: function(event,id) {
                        event.stopPropagation();
                        jqcc('#cometchat_grouplist_'+id).find('.cometchat_chatroom_overlay').remove();
                        jqcc('#cometchat_grouplist_'+id).find('.chatroomName').hide();
                        jqcc('#cometchat_grouplist_'+id).find('.cancel_edit').hide();
                        jqcc('#cometchat_grouplist_'+id).find('.cometchat_groupscontentname').css('visibility','visible');
                        jqcc('#cometchat_grouplist_'+id).find('.cometchat_groupscontentname').show();
                    },
                    updateChatroomsTabtext: function(){
                        /*$('#cometchat_chatroomstab_text').text('<?php echo $chatrooms_language[100];?>');*/
                    },
                    minimizeChatrooms: function(){
                    },
                    loadCCPopup: function(url,name,properties,width,height,title,force,allowmaximize,allowresize,allowpopout){
                        /*if(jqcc.cometchat.getChatroomVars('lightboxWindows') == 1) {
                            var controlparameters = {"type":"modules", "name":"chatrooms", "method":"loadCCPopup", "params":{"url":url, "name":name, "properties":properties, "width":width, "height":height, "title":title, "force":force, "allowmaximize":allowmaximize, "allowresize":allowresize, "allowpopout":allowpopout}};
                            controlparameters = JSON.stringify(controlparameters);
                            parent.postMessage('CC^CONTROL_'+controlparameters,'*');
                        } else {
                            var w = window.open(url,name,properties);
                            w.focus();
                        }*/
                    },
                    inviteUsertab: function(){
                   },
                    prependCrMessagesInit: function(id){
                        var messages = jqcc('#cometchat_grouptabcontenttext_'+id).find('.cometchat_chatboxmessage');
                        if(messages.length > 0){
                            jqcc('#scrolltop_'+id).remove();
                            prepend = messages[0].id.split('_')[2];
                        }else{
                            prepend = -1;
                        }
                        jqcc.cometchat.updateChatroomMessages(id,prepend);
                    },
                    prependCrMessages:function(id,data){
                        var oldMessages = '',
                        temp = '',
                        count = 0,
                        todaysdate = new Date(),
                        tdmonth  = todaysdate.getMonth(),
                        tddate  = todaysdate.getDate(),
                        tdyear = todaysdate.getFullYear(),
                        today_date_class = tdmonth+"_"+tddate+"_"+tdyear,
                        ydaysdate = new Date((new Date()).getTime() - 3600000 * 24),
                        ydmonth  = ydaysdate.getMonth(),
                        yddate  = ydaysdate.getDate(),
                        ydyear = ydaysdate.getFullYear(),
                        yday_date_class = ydmonth+"_"+yddate+"_"+ydyear,
                        d = '',
                        month = '',
                        date  = '',
                        year = '',
                        msg_date_class = '',
                        msg_date = '',
                        date_class = '',
                        msg_date_format = '',
                        cometchat_del_style = '',
                        cc_dir = '<?php if ($rtl == 1) { echo 1; } else { echo 0; }?>',
                        prepend = '',
                        avatarstofetch = {},
                        messagewrapperid = '';

                        $.each(data, function(i, incoming){
                            if(incoming.fromid == settings.myid){
                                incoming.from = "<?php echo $chatrooms_language['me'];?>";
                            }
                            lastMessageId = incoming.id;
                            if( (!incoming.hasOwnProperty('id') || incoming.id == '') && (!incoming.hasOwnProperty('localmessageid') || incoming.localmessageid == '') && (incoming.message).indexOf('CC^CONTROL_')==-1){
                                return;
                            }
                            var deleteMessage = '',
                                message = jqcc.cometchat.processcontrolmessage(incoming),
                                msg_time = jqcc.cometchat.processTimestamp(incoming.sent),
                                add_bg = '',
                                add_arrow_class = '',
                                months_set = [];
                                if( incoming.hasOwnProperty('id') && !incoming.hasOwnProperty('localmessageid') ) {
                                    messagewrapperid = incoming.id;
                                }else if( !incoming.hasOwnProperty('id') && incoming.hasOwnProperty('localmessageid') ) {
                                    messagewrapperid = incoming.localmessageid;
                                }else{
                                    messagewrapperid = incoming.id;
                                    if($("#cometchat_groupmessage_"+incoming.localmessageid).length>0){
                                        $("#cometchat_groupmessage_"+incoming.localmessageid).attr('id',"cometchat_groupmessage_"+incoming.id);
                                        $("#cometchat_chatboxseen_"+incoming.localmessageid).attr('id',"cometchat_chatboxseen_"+incoming.id).removeClass("cometchat_offlinemessage");
                                        $("#message_"+incoming.localmessageid).attr('id','message_'+incoming.id);
                                        var offlinemessages = jqcc.cometchat.getFromStorage('offlinemessagesqueue');
                                        if(offlinemessages.hasOwnProperty(incoming.localmessageid)) {
                                            delete offlinemessages[incoming.localmessageid];
                                            jqcc.cometchat.updateToStorage('offlinemessagesqueue',offlinemessages);
                                        }
                                        return;
                                    }
                                }
                            <?php

                            $months_array = array($chatrooms_language[90],$chatrooms_language[91],$chatrooms_language[92],$chatrooms_language[93],$chatrooms_language[94],$chatrooms_language[95],$chatrooms_language[96],$chatrooms_language[97],$chatrooms_language[98],$chatrooms_language[99],$chatrooms_language[101],$chatrooms_language[102]);

                            foreach($months_array as $key => $val){
                                ?>
                                months_set.push('<?php echo $val; ?>');
                                <?php
                            }
                            ?>

                            d = new Date(parseInt(msg_time));
                            month  = d.getMonth();
                            date  = d.getDate();
                            year = d.getFullYear();

                            msg_date_class = month+"_"+date+"_"+year;
                            msg_date = months_set[month]+" "+date+", "+year;

                            var type = 'th';
                            if(date==1||date==21||date==31){
                                type = 'st';
                            }else if(date==2||date==22){
                                type = 'nd';
                            }else if(date==3||date==23){
                                type = 'rd';
                            }
                            msg_date_format = date+type+' '+months_set[month]+', '+year;

                            if(msg_date_class == today_date_class){
                                date_class = "today";
                                msg_date = "<?php echo $chatrooms_language['today']; ?>";
                            }else  if(msg_date_class == yday_date_class){
                                date_class = "yesterday";
                                msg_date = "<?php echo $chatrooms_language['yesterday']; ?>";
                            }
                            if (message != '') {
                                count = count + 1;
                                var fromname = incoming.from,
                                    prepend = '',
                                    smileycount = (message.match(/cometchat_smiley/g) || []).length,
                                    smileymsg = message.replace(/<img[^>]*>/g,"");
                                smileymsg = smileymsg.trim();

                                if(smileycount == 1 && smileymsg == '') {
                                    message = message.replace('height="20"', 'height="64px"');
                                    message = message.replace('width="20"', 'width="64px"');
                                }

                                if((incoming.message).indexOf('<img class="file_image"')!=-1){
                                    if(jqcc.cometchat.getCcvariable().callbackfn=="desktop"){
                                        if(incoming.message.indexOf('target')>=-1){
                                            incoming.message=incoming.message.replace(/target="_blank"/g,'');
                                        }
                                    }
                                }
                                if((incoming.message).indexOf('has shared a handwritten message')!=-1){
                                    if(jqcc.cometchat.getCcvariable().callbackfn=="desktop"){
                                        if(incoming.message.indexOf('href')>=-1){
                                            var start = (incoming.message).indexOf('href');
                                            var end = (incoming.message).indexOf('target');
                                            var HtmlString=(incoming.message).slice(start,end);
                                            incoming.message=(incoming.message).replace(HtmlString,'');
                                        }
                                    }
                                }
                                var ts = parseInt(incoming.sent)*1000;

                                if(jqcc.cometchat.getChatroomVars('owner') || jqcc.cometchat.checkModerator(incoming.groupid) || (jqcc.cometchat.getChatroomVars('allowDelete') == 1 && incoming.fromid == settings.myid)) {
                                    var grouppopup = $('#cometchat_group_'+id+'_popup');
                                    if(grouppopup.find("#cometchat_groupmessage_"+messagewrapperid).find(".delete_msg").length < 1) {
                                        if(incoming.fromid == settings.myid){
                                            cometchat_ts_class = 'cometchat_ts';
                                            cometchat_ts_style = 'float:right';
                                            if(message.indexOf('imagemessage mediamessage')!=-1) {
                                                cometchat_ts_style = cometchat_ts_style+';margin-top:12px';
                                            }
                                        }else{
                                            cometchat_ts_class = 'cometchat_ts_other';
                                            var cometchat_ts_other_width = $("#cometchat_groupmessage_"+messagewrapperid+" .cometchat_ts_margin").outerWidth();
                                            if(cometchat_ts_other_width >= 135){
                                                cometchat_ts_style = 'float:left;margin-left:-23px';
                                            }else{
                                                cometchat_ts_style = 'float:left;margin-left:-3px';
                                            }
                                        }

                                        if(grouppopup.find("#cometchat_groupmessage_"+messagewrapperid).find(".cometchat_ts_other").length < 1) {
                                                 if((message.indexOf('<img')==-1 && message.indexOf('src')==-1) || (smileycount > 0 && smileymsg != '')){
                                                    cometchat_del_style = '';
                                                }else {
                                                    if(message.indexOf('cometchat_smiley')!=-1) {
                                                        cometchat_del_style = 'margin: 0px 0px 14px 0px';
                                                    }
                                                }
                                        }else{
                                             cometchat_del_style = '';
                                        }
                                       deleteMessage = '<span class="delete_msg" style="'+cometchat_ts_style+';'+cometchat_del_style+';" onclick="javascript:jqcc.cometchat.confirmDelete(\''+messagewrapperid+'\',\''+id+'\');"><img class="hoverbraces" src="'+staticCDNUrl+'layouts/docked/images/bin.svg"></span>';
                                    }
                                }
                                if (incoming.fromid != settings.myid || (incoming.hasOwnProperty('botid') && incoming.botid != 0)) {
                                    var avatar = jqcc.cometchat.getThemeArray('buddylistAvatar', incoming.fromid);
                                    if(typeof avatar=="undefined"){
                                        avatarstofetch[incoming.fromid]=1;
                                        avatar = staticCDNUrl+'images/noavatar.png';
                                    }
                                    var fromavatar = '<a class="cometchat_floatL" href="'+jqcc.cometchat.getThemeArray('buddylistLink', incoming.fromid)+'"><img class="cometchat_userscontentavatarsmall cometchat_avatar_'+incoming.fromid+'" src="'+avatar+'" title="'+fromname+'"/></a>';
                                   var sentdata = $[calleeAPI].getTimeDisplay(ts,messagewrapperid);
                                   var sentdata_box = "<span class=\"cometchat_ts_other\">"+sentdata+"</span>";
                                   if((message.indexOf('<img')==-1 && message.indexOf('src')==-1) || (smileycount > 0 && smileymsg != '')){
                                        add_bg = 'cometchat_chatboxmessagecontent';
                                        add_arrow_class = '<div class="msgArrow"><div class="after"></div></div>';
                                   }

                                    var usernamecontent = '';
                                    if (showUsername == '1') {
                                        usernamecontent = '<span class="cometchat_groupusername">'+fromname+':</span><br>';
                                    }
                                    if(incoming.hasOwnProperty('botid') && incoming.botid != 0) {
                                        fromavatar = '<a class="cometchat_floatL"><img class="cometchat_userscontentavatarsmall" src="'+jqcc.cometchat.getThemeArray('botlistAvatar', incoming.botid)+'" title="'+jqcc.cometchat.getThemeArray('botlistName', incoming.botid)+'"/></a>';
                                        if (showUsername == '1') {
                                            usernamecontent = '<span class="cometchat_groupusername">'+jqcc.cometchat.getThemeArray('botlistName', incoming.botid)+':</span><br>';
                                        }
                                    }
                                    temp += '<div class="cometchat_time cometchat_time_'+msg_date_class+' '+date_class+'" msg_format="'+msg_date_format+'">'+msg_date+'</div><div class="cometchat_chatboxmessage" id="cometchat_groupmessage_'+messagewrapperid+'">'+fromavatar+'<div class="'+add_bg+' '+'cometchat_ts_margin cometchat_floatL">'+usernamecontent+'<span id="cc_groupmessage_'+messagewrapperid+'" class="cometchat_msg">'+message+'</span></div>'+sentdata_box+' '+add_arrow_class+deleteMessage+'</div>';
                                } else {
                                    var sentdata = $[calleeAPI].getTimeDisplay(ts,messagewrapperid);
                                    var sentdata_box = "<span class=\"cometchat_ts\">"+sentdata+"</span>";
                                    if((message.indexOf('<img')==-1 && message.indexOf('src')==-1) || (smileycount > 0 && smileymsg != '')){
                                        add_bg = 'cometchat_chatboxmessagecontent cometchat_self';
                                        add_arrow_class = '<div class="selfMsgArrow"><div class="after"></div></div>';
                                    }
                                    temp += '<div class="cometchat_time cometchat_time_'+msg_date_class+' '+date_class+'" msg_format="'+msg_date_format+'">'+msg_date+'</div><div class="cometchat_chatboxmessage" id="cometchat_groupmessage_'+messagewrapperid+'"><div class="'+add_bg+' '+'cometchat_ts_margin cometchat_self_msg cometchat_floatR"><span id="cc_groupmessage_'+messagewrapperid+'" class="cometchat_msg">'+message+'</span><span id="cometchat_chatboxseen_'+messagewrapperid+'"></span></div>'+sentdata_box+' '+add_arrow_class+deleteMessage+'</div>';
                                }
                            }
                        });

                        if(!$.isEmptyObject(avatarstofetch)){
                            jqcc.cometchat.getUserDetails(Object.keys(avatarstofetch),'updateView');
                        }
                        var grouppopup = $('#cometchat_group_'+id+'_popup');

                        var current_top_element  = $('#cometchat_grouptabcontenttext_'+id+' .cometchat_chatboxmessage:first');
                        grouppopup.find("#cometchat_grouptabcontenttext_"+id).prepend(temp);

                        if(mobileDevice){
                            $('#cometchat_grouptabcontenttext_'+id).css('overflow-y','auto');
                        }else{
                            var offsetheight = 0;
                            if(current_top_element.length>0){
                                var offsetheight = current_top_element.offset().top - $('#cometchat_grouptabcontenttext_'+id+' .cometchat_chatboxmessage:first').offset().top+$('.cometchat_time').height()+$('#cometchat_prependCrMessages'+id).height()+100;
                                var height = offsetheight-$('#cometchat_grouptabcontenttext_'+id).height();
                                $('#cometchat_grouptabcontenttext_'+id).slimScroll({scrollTo: height+'px'});
                            }else{
                                $('#cometchat_grouptabcontenttext_'+id).slimScroll({scroll: 0});
                            }
                        }
                         $.each(data, function(i, incoming){
                            if($(".cometchat_chatboxmessagecontent").next().hasClass("cometchat_ts")){
                               var msg_containerHeight = $("#cometchat_groupmessage_"+messagewrapperid+" .cometchat_ts_margin").outerHeight();
                               var cometchat_ts_margin_right = $("#cometchat_groupmessage_"+messagewrapperid+" .cometchat_ts_margin").outerWidth(true)+3;
                               jqcc('#cometchat_groupmessage_'+messagewrapperid).find('.cometchat_ts').css('margin-top',(msg_containerHeight-8));
                               jqcc('#cometchat_groupmessage_'+messagewrapperid).find('.cometchat_ts').css('margin-right',cometchat_ts_margin_right);
                           }
                           if(($(".cometchat_chatboxmessagecontent").next().hasClass("cometchat_ts_other")) && (cc_dir == 1)){
                               var cometchat_ts_margin_left = $("#cometchat_groupmessage_"+messagewrapperid+" .cometchat_ts_margin").outerWidth(true)+30;
                               jqcc('#cometchat_groupmessage_'+messagewrapperid).find('.cometchat_ts_other').css('margin-left',cometchat_ts_margin_left);
                           }
                         });

                        $(".cometchat_chatboxmessage").live("mouseover",function() {
                            $(this).find(".delete_msg").css('opacity','0.7');
                            var msg_containerHeight = $(this).find(".cometchat_ts_margin").outerHeight();
                            var cometchat_ts_margin_right = $(this).find(".cometchat_ts_margin").outerWidth(true)+3;
                            $(this).find('.cometchat_ts').css('margin-top',(msg_containerHeight-8));
                            $(this).find('.cometchat_ts').css('margin-right',cometchat_ts_margin_right);
                        });
                        $(".cometchat_chatboxmessage").live("mouseout",function() {
                            $(this).find(".delete_msg").css('opacity','0');
                            var msg_containerHeight = $(this).find(".cometchat_ts_margin").outerHeight();
                            var cometchat_ts_margin_right = $(this).find(".cometchat_ts_margin").outerWidth(true)+3;
                            $(this).find('.cometchat_ts').css('margin-top',(msg_containerHeight-8));
                            $(this).find('.cometchat_ts').css('margin-right',cometchat_ts_margin_right);
                        });
                        $(".delete_msg").mouseover(function() {
                             $(this).find(".delete_msg").css('opacity','0.7');
                        });
                        $(".delete_msg").mouseout(function() {
                             $(this).find(".delete_msg").css('opacity','0');
                        });


                        $.each($('#cometchat_group_'+id+'_popup .cometchat_chatboxmessage'),function (i,divele){
                            if($(this).find(".cometchat_ts_margin").next().hasClass("cometchat_ts")){
                                var msg_containerHeight = $(this).find(".cometchat_ts_margin").outerHeight();
                                var cometchat_ts_margin_right = $(this).find(".cometchat_ts_margin").outerWidth(true)+3;
                                jqcc(this).find('.cometchat_ts').css('margin-top',(msg_containerHeight-8));
                                jqcc(this).find('.cometchat_ts').css('margin-right',cometchat_ts_margin_right);
                            }
                        });

                        jqcc.crdocked.groupbyDate(id);

                        $('#cometchat_grouptabcontenttext_'+id).find('.cometchat_prependCrMessages').remove();

                        prepend = '<div class=\"cometchat_prependCrMessages\" onclick\="jqcc.crdocked.prependCrMessagesInit('+id+')\" id = \"cometchat_prependCrMessages_'+id+'\"><?php echo $chatrooms_language[74];?></div>';

                        if($('#cometchat_grouptabcontenttext_'+id+' .cometchat_prependMessages').length != 1){
                                $('#cometchat_grouptabcontenttext_'+id).prepend(prepend);
                        }

                        if((count - parseInt(settings.prependLimit) < 0)){
                            $('#cometchat_group_'+id+'_popup .cometchat_prependCrMessages').text('<?php echo $chatrooms_language[75];?>');
                            jqcc('#cometchat_group_'+id+'_popup .cometchat_prependCrMessages').attr('onclick','');
                            jqcc('#cometchat_group_'+id+'_popup .cometchat_prependCrMessages').css('cursor','default');
                        }else{
                            jqcc('#cometchat_group_'+id+'_popup .cometchat_prependCrMessages').attr('onclick','jqcc.docked.prependCrMessagesInit('+id+')');
                        }
                    },
                    getActiveChatrooms: function(item){
                        return;
                    },
                    activeChatrooms: function(item){
                    },
                    chatroomUnreadMessages: function(crUnreadMessages,chatroomid){
                        return;
                    },
                    addMessageCounter: function(id, amount){
                        if (typeof(amount) == 'undefined') {
                            amount = 0;
                        }
                    	if($('#cometchat_group_'+id+'_popup:visible').length!=0){
                    		amount = 0;
                    	}
                        amount = jqcc.cometchat.updateChatBoxState({id: parseInt(id),g:1, c: parseInt(amount)});

                        var cometchat_group_id = jqcc('#cometchat_group_'+id+',#cometchat_recentgrouplist_'+id+', #cometchat_grouplist_'+id);

                        if(amount>0){
                            cometchat_group_id.removeClass('cometchat_new_message').attr('amount', amount).find('div.cometchat_unreadCount').html(amount);
                        	cometchat_group_id.find('div.cometchat_unreadCount').show();
                        }else{
                        	cometchat_group_id.find('div.cometchat_unreadCount').hide();
                        }
                    },
                    groupbyDate: function(roomno){
                        $('#cometchat_grouptabcontenttext_'+roomno).find('.cometchat_time').hide();
                        $.each($('#cometchat_grouptabcontenttext_'+roomno).find('.cometchat_time'),function (i,divele){
                            var classes = $(divele).attr('class').split(/\s+/);
                            for(var i in classes){
                                if(typeof classes[i] == 'string'){
                                    if(classes[i].indexOf('cometchat_time_') === 0){
                                         $('#cometchat_grouptabcontenttext_'+roomno).find('.'+classes[i]+':first').css('display','table');
                                    }
                                }
                            }
                        });
                    },
                    closeChatroom: function(roomno,from){
                        var cometchat_group_popup = $("#cometchat_group_"+roomno+"_popup");
                        if(cometchat_group_popup.length != 0){
                            var chatroomsOpened = jqcc.cometchat.getChatroomVars('chatroomsOpened');
                            delete(chatroomsOpened[roomno]);
                            jqcc.cometchat.setChatroomVars('chatroomsOpened',chatroomsOpened);
                            jqcc.cometchat.updateChatBoxState({id:roomno,g:1,s:0,r:from});
                            var groupulh = jqcc.cometchat.getChatroomVars('groupulh');
                            groupulh[roomno] = '';
                            jqcc.cometchat.setChatroomVars('groupulh',groupulh);
                        }

                        if($('#cometchat_unseenchatboxes').find("#cometchat_group_"+roomno).length == 1) {
                            cometchat_group_popup.remove();
                            $("#cometchat_group_"+roomno).remove();
                            $('#cometchat_chatbox_left').click();

                            if($('#cometchat_unseenUsers').find('#cometchat_group_'+roomno).length == 1){
                                $('#cometchat_unseenUsers').find('#cometchat_group_'+roomno).remove();
                                $("#cometchat_group_"+roomno).remove();

                                var count = $('#cometchat_unseenchatboxes').children().length;
                                if(typeof(count) != "undefined"){
                                    $('#cometchat_chatbox_left').find('.cometchat_tabtext').text(parseInt(count));
                                    $('#cometchat_chatbox_left').click();
                                    if(count == 0){
                                        $('#cometchat_chatbox_left').hide();
                                    }
                                }
                            }
                            return;
                        }else {
                            cometchat_group_popup.remove();
                            $("#cometchat_group_"+roomno).remove();
                        }

                        if($('#cometchat_unseenUsers').children().length > 0){
                            $.docked.popoutUnseenuser();
                        }else{
                            $('#cometchat_chatboxes_wide').width($('#cometchat_chatboxes_wide').width()-chatboxWidth-chatboxDistance);
                        }
                    },
                    chatScroll: function(id){
                        var baseUrl = settings.baseUrl;
                        if($('#scrolltop_'+id).length == 0){
                            $("#cometchat_grouptabcontenttext_"+id).prepend('<div id="scrolltop_'+id+'" class="cometchat_scrollup"><img src="'+staticCDNUrl+'images/arrowtop.svg" class="cometchat_scrollimg" /></div>');
                        }
                        if($('#scrolldown_'+id).length == 0){
                            $("#cometchat_grouptabcontenttext_"+id).append('<div id="scrolldown_'+id+'" class="cometchat_scrolldown"><img src="'+staticCDNUrl+'images/arrowbottom.svg" class="cometchat_scrollimg" /></div>');
                        }
                        $('#cometchat_grouptabcontenttext_'+id).unbind('wheel');
                        $('#cometchat_grouptabcontenttext_'+id).on('wheel',function(event){
                            var scrollTop = $(this).scrollTop();
                            if(event.originalEvent.deltaY != 0){
                                clearTimeout($.data(this, 'scrollTimer'));
                                if(event.originalEvent.deltaY > 0){
                                    $('#scrolltop_'+id).hide();
                                    var down = jqcc("#cometchat_grouptabcontenttext_"+id)[0].scrollHeight-300-50;
                                    if(scrollTop < down){
                                        $('#scrolldown_'+id).fadeIn('slow');
                                    }else{
                                        $('#scrolldown_'+id).fadeOut();
                                    }
                                    $.data(this, 'scrollTimer', setTimeout(function() {
                                        $('#scrolldown_'+id).fadeOut('slow');
                                    },2000));

                                }else{
                                    $('#scrolldown_'+id).hide();
                                    var top = 45+50;
                                    if(scrollTop > top){
                                        $('#scrolltop_'+id).fadeIn('slow');
                                    }else{
                                        $('#scrolltop_'+id).fadeOut();
                                    }
                                    $.data(this, 'scrollTimer', setTimeout(function() {
                                        $('#scrolltop_'+id).fadeOut('slow');
                                    },2000));
                                }
                            }
                        });

                        $('#scrolltop_'+id).click(function(){
                            $('#scrolltop_'+id).hide();
                            $('#cometchat_grouptabcontenttext_'+id).slimScroll({scroll: 0});
                        });

                        $('#scrolldown_'+id).click(function(){
                            $('#scrolldown_'+id).hide();
                            $('#cometchat_grouptabcontenttext_'+id).slimScroll({scroll: 1});
                        });
                    }
                };

        })();
})(jqcc);

if(typeof(jqcc.lite) === "undefined"){
    jqcc.docked=function(){};
}

jqcc.extend(jqcc.docked, jqcc.crdocked);

jqcc(document).ready(function(){
    jqcc('.leaveRoom').live('click',function(){
        jqcc.cometchat.leaveChatroom();
    });
    jqcc( "#cometchat_chatroom_password" ).live('keyup',function() {
        if(jqcc("#cometchat_chatroom_password").val() == ' '){
            alert("<?php echo $chatrooms_language['Password_start_with_space']; ?>");
            jqcc("#cometchat_chatroom_password").val('');
        }
    });
});
