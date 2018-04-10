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

var enableType = 0;

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
    var chatroomuserscount = new Array();
    var mobileDevice = navigator.userAgent.match(/ipad|ipod|iphone|android|windows ce|Windows Phone|blackberry|palm|symbian/i);
    var iOSmobileDevice = navigator.userAgent.match(/ipad|ipod|iphone/i);
    var rtl = "<?php echo  $rtl; ?>";
    var showUsername = "<?php echo $showUsername; ?>";
    var allowGuests = <?php echo $allowGuests; ?>;
    var crguestsMode = <?php echo $crguestsMode; ?>;
    $.crembedded = (function() {
        return {
                chatroomInit: function(){
                    var createChatroom='';
                    var chatroomsTab = '';
                    var chatroomstabpopup = '';
                    if (typeof jqcc.cometchat.getSettings != "undefined") {
                        enableType = jqcc.cometchat.getSettings().enableType;
                    }
                    if(enableType==1){
                        $('#cometchat_righttab').find('.cometchat_noactivity').find('h3').text("<?php echo $chatrooms_language['get_started'];?>");
                    }

                    if (enableType!=1) {
                        chatroomsTab = '<span id="cometchat_chatroomstab" class="cometchat_tab" style="'+tabWidth+'"><span id="cometchat_chatroomstab_text" class="cometchat_tabstext"><?php echo $chatrooms_language[100];?></span></span>';
                    }
                    if (enableType!=2) {
                        chatroomstabpopup = '<div id="cometchat_chatroomstab_popup">'+createChatroom+'<div id="lobby"><div class="lobby_rooms cometchat_tabpopup" id="lobby_rooms"></div></div></div>';
                    }

                    selectlang = '<select id="selectlanguage" class="selectlanguage"></select>';
                    var currentroom = '<div class="content_div" id="currentroom" ><div id="currentroom_left" class="content_div cometchat_tabpopup"><div class="cometchat_userchatarea"><div class="cometchat_tabsubtitle"><div id="cometchat_pluginsonheader" class="cometchat_chatboxMenuOptions"></div><div class="cometchat_chatboxLeftDetails"><div class="cometchat_chatroomimage"><img src="'+staticCDNUrl+'layouts/embedded/images/group.svg" class="cometchat_chatroomavatarimage" /></div><div class="cometchat_chatboxDisplayDetails"><div class="cometchat_chatroomdisplayname" style="margin-left: 20px;position:absolute;"></div></div></div></div><div class="cometchat_messageElement" id="cc_cr"></div><div class="cometchat_prependMessages_container"></div><div id="currentroom_convo"><div id="currentroom_convotext" class="cometchat_message_container"></div><div style="clear:both"></div>'+selectlang+'</div><div id="cometchat_tabinputcontainer"></div></div></div>';
                    var chatroomusers = '<div id="chatroomuser_container_hidden" style="display:none;"></div>';
                    jqcc("#cometchat_righttab").append(chatroomusers);
                    if($('#cometchat_userstab').length > 0) {
                        $('#cometchat_userstab').after(chatroomsTab);
                    }

                    if($('#cometchat_userstab_popup').length > 0) {
                        $('#groups').after(chatroomstabpopup);
                    } else {
                        $('#groups').html(chatroomstabpopup);
                    }

                    if(enableType==1) {
                        $('#cometchat_tabcontainer').remove();
                        $('#cometchat_chatroomstab_popup').addClass("cometchat_tabopen");
                    }

                    if(enableType!=2){
                        $('#cometchat_righttab').append(currentroom);
                        $('#currentroom').hide();
                    }
                    if(jqcc().slimScroll && !mobileDevice){
                        $('#lobby_rooms').slimScroll({height: 'auto'});
                        $("#plugin_container").slimScroll({width: 'auto'});
                        $("#chatroomuser_container").slimScroll({width: 'auto'});
                        $('#lobby_rooms').attr('style','overflow: hidden !important');
                    }
                    $('#currentroom').on('click','.cometchat_user_closebox',function(){
                        jqcc.embedded.closeChatroom();
                    });
                    setTimeout(function(){
                        var openedChatbox = jqcc.cometchat.getThemeVariable('openedChatbox');
                        for (var key in openedChatbox)
                        {
                            if(openedChatbox.hasOwnProperty(key))
                            {
                                if(typeof (jqcc.embedded.addPopup)!=='undefined'){
                                    jqcc.embedded.addPopup(key, parseInt(openedChatbox[key]), 0);
                                }
                            }
                        }
                    },500);
                    $('.cometchat_noactivity').css('display','block');
                    jqcc.cometchat.chatroomHeartbeat();
                },
                chatroomTab: function(){
                    var cometchat_chatroom_search = $("#cometchat_user_search");
                    var lobby_rooms = $('#lobby_rooms');
                    cometchat_chatroom_search.click(function(){
                        var searchString = $(this).val();
                        if(searchString=="<?php echo $chatrooms_language['find_a_chatroom'];?>"){
                            cometchat_chatroom_search.val('');
                            cometchat_chatroom_search.addClass('cometchat_search_light');
                        }
                    });
                    cometchat_chatroom_search.blur(function(){
                        var searchString = $(this).val();
                        if(searchString==''){
                            cometchat_chatroom_search.addClass('cometchat_search_light');
                        }
                    });
                    var cometchat_userstab = $('#cometchat_chatstab');
                    var cometchat_chatroomstab = $('#cometchat_groupstab');
                    cometchat_chatroomstab.click(function(e){
                        if (!(jqcc.cometchat.membershipAccess('chatrooms','modules'))){
                            return;
                        }
                        if(jqcc.cometchat.getCcvariable().hasOwnProperty('loggedinusertype') && jqcc.cometchat.getCcvariable().loggedinusertype == 'guestuser' && crguestsMode == 0){
                            alert("<?php echo $chatrooms_language['access_group_guest']; ?>");
                            e.stopImmediatePropagation();
                            return;
                        }
                        jqcc[calleeAPI].hideMenuPopup();
                        $('#cometchat_chatroomstab_text').text("<?php echo $chatrooms_language['title'];?>");
                        if(typeof(newmess)!="undefined"){
                            clearInterval(newmess);
                        }
                        newmess = setInterval(function(){
                            if($("#cometchat_groupstab.cometchat_tabclick").length>0){
                                var newOneonOneMessages = 0;
                                jqcc('#cometchat_activechatboxes_popup .cometchat_msgcount').each(function(){
                                    newOneonOneMessages += parseInt(jqcc(this).children('.cometchat_msgcounttext').text());
                                });
                                if(newOneonOneMessages>0){
                                    $('#cometchat_userstab_text').text("<?php echo $language['unread']?> ("+newOneonOneMessages+")");
                                }
                                setTimeout(function(){
                                    $('#cometchat_userstab_text').text("<?php echo $language['chat_now_tab'];?> ("+jqcc.cometchat.getThemeVariable('lastOnlineNumber')+")");
                                },2000);
                            }else{
                                if(typeof(newmess)!='undefined'){
                                    clearInterval(newmess);
                                }
                            }
                        },4000);
                        if(jqcc.cometchat.getThemeVariable('offline')==1){
                            jqcc.cometchat.setThemeVariable('offline', 0);
                            jqcc.cometchat.setThemeArray('buddylistStatus', jqcc.cometchat.getThemeVariable('userid'), 'available');
                            jqcc[calleeAPI].removeUnderline();
                            $("#cometchat_self .cometchat_userscontentdot").addClass('cometchat_available');
                            $('.cometchat_optionsstatus.available').css('text-decoration', 'underline');
                            $('#cometchat_userstab_text').html("<?php echo $language['chat_now_tab'];?> ("+jqcc.cometchat.getThemeVariable('lastOnlineNumber')+")");
                            $("#cometchat_optionsbutton_popup").find("span.available").click();
                        }

                        jqcc.cometchat.setSessionVariable('buddylist', '0');
                        $(this).addClass("cometchat_tabclick");
                        cometchat_userstab.removeClass("cometchat_tabclick");
                        $('#cometchat_userstab_popup').removeClass("cometchat_tabopen");
                        $('#cometchat_chatroomstab_popup').addClass("cometchat_tabopen");
                        $[calleeAPI].chatroomWindowResize();
                    });
                },
                chatroomOffline: function(){
                    $('#cometchat_chatroomstab_popup').removeClass('cometchat_tabopen');
                    $('#cometchat_chatroomstab').removeClass('cometchat_tabclick');
                    jqcc.cometchat.leaveChatroom();
                },
                playsound: function(type) {
                    try{
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
                    $(chatboxtextarea).css('height','20px');
                    $(chatboxtextarea).css('overflow-y','hidden');
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
                getTimeDisplay: function(ts,id,dir) {
                    var time = getTimeDisplay(ts);
                    var cometchat_self_ts = '';
                    if(dir == 1){
                        cometchat_self_ts = 'cometchat_self_ts';
                    }
                    if(ts < jqcc.cometchat.getChatroomVars('todays12am')) {
            return "<span class=\"cometchat_ts\" "+cometchat_self_ts+" "+style+">("+time.hour+":"+time.minute+time.ap+" "+time.date+time.type+" "+time.month+")</span>";
                    } else {
                            return "<span class=\"cometchat_ts\" "+cometchat_self_ts+" "+style+">("+time.hour+":"+time.minute+time.ap+")</span>";
                    }
                },
                deletemessage: function(delid) {
                    $("#cometchat_groupmessage_"+delid).parent().remove();
                    $("#cometchat_groupmessage_"+delid).remove();
                    $("#cometchat_usersavatar_"+delid).remove();
                },
                addChatroomMessage: function(incoming){
                    var fromid = incoming.fromid,
                        localmessageid = '';
                        incomingid = incoming.id,
                        incomingself = 1,
                        sent = incoming.sent,
                        fromname = incoming.from,
                        calledfromsend = incoming.calledfromsend,
                        chatroomid = incoming.roomid,
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
                        prepend = '',
                        avatarstofetch = {},
                        messagewrapperid = '',
                        trayIcons = jqcc.cometchat.getTrayicon(),
                        isRealtimetranslateEnabled = trayIcons.hasOwnProperty('realtimetranslate');
                        incoming.message = jqcc.cometchat.htmlEntities(incoming.message);
                        message = jqcc.cometchat.processcontrolmessage(incoming);

                        if( (!incoming.hasOwnProperty('id') || incoming.id == '') && (!incoming.hasOwnProperty('localmessageid') || incoming.localmessageid == '') && (incoming.message).indexOf('CC^CONTROL_')==-1){
                            return;
                        }

                        if(isRealtimetranslateEnabled && jqcc.cookie(settings.cookiePrefix+'rttlang') && fromid != settings.myid && (message).indexOf('CC^CONTROL_') == -1){
                            incoming.message = message;
                            text_translate(incoming);
                        }

                    if( incoming.hasOwnProperty('id')) {
                        messagewrapperid = incoming.id;
                    }else if(incoming.hasOwnProperty('localmessageid') ) {
                        messagewrapperid = incoming.localmessageid;
                    }

                    incomingid = messagewrapperid;
                    if(incoming.hasOwnProperty('self')){
                        incomingself = incoming.self;
                    }

                    if(fromid != settings.myid || (incoming.hasOwnProperty('botid') && incoming.botid != 0)) {
                        incomingself = 0;
                    }
                    if(typeof(fromname) === 'undefined' || fromname == 0 || incomingself){
                        fromname = "<?php echo $chatrooms_language['me']; ?>";
                    }
                    var temp = '';
                    var crUnreadMessages = jqcc.cometchat.getChatroomVars('crUnreadMessages');
                    var chatroomreadmessages = jqcc.cometchat.getFromStorage("crreadmessages");
                    var receivedcrunreadmessages = jqcc.cometchat.getFromStorage('crreceivedunreadmessages');
                    if (calledfromsend != '1') {
                        settings.timestamp=incomingid;
                    }
                    separator = "<?php echo $chatrooms_language['semicolon']; ?>";
                    /*if(incomingmessage.indexOf('CC^CONTROL_') == -1 && fromid != settings.myid) {
                        jqcc.cometchat.updateChatBoxState({id:chatroomid,g:1,c:1});
                        if(chatroomid != jqcc.cometchat.getChatroomVars('currentroom')) {
                            if (typeof(jqcc[jqcc.cometchat.getChatroomVars('calleeAPI')].addMessageCounter) == "function"){
                                jqcc[jqcc.cometchat.getChatroomVars('calleeAPI')].addMessageCounter(chatroomid);
                            }
                        }
                    }*/

                    var msg_time = incoming.sent;
                    msg_time = msg_time+'';
                    if (msg_time.length == 10){
                        msg_time = parseInt(msg_time * 1000);
                    }

                    var months_set = new Array();

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

                    if(typeof(message) != 'undefined' && message != '' && chatroomid == jqcc.cometchat.getChatroomVars('currentroom')) {
                        var smileycount = (message.match(/cometchat_smiley/g) || []).length;
                        var smileymsg = message.replace(/<img[^>]*>/g,"");
                        var imagemsg_rightcss = '';
                        var deleteiconcss = '';
                        var deleteiconcssself = '';
                        var marginclass = '';
                        smileymsg = smileymsg.trim();

                        if(message.indexOf('<img')!=-1 ){
                            if(!incomingself){
                                imagemsg_rightcss = 'margin-left:-20px !important;';
                            }
                            if(smileycount == 1 && smileymsg == '') {
                                imagemsg_rightcss = 'margin-left:-20px !important;';
                                message = message.replace('height="20"', 'height="64px"');
                                message = message.replace('width="20"', 'width="64px"');
                                if(incomingself){
                                    imagemsg_rightcss = '';
                                }
                            }else if(smileycount > 1 && smileymsg == ''){
                                imagemsg_rightcss = 'margin-left:-20px !important;margin-top:20px !important;';
                                deleteiconcssself = 'margin-top:-5px;';
                                if(rtl == 1){
                                    deleteiconcss = 'margin-right:46px;';
                                }else{
                                    deleteiconcss = 'margin-left:46px;';
                                }
                                if(incomingself){
                                    imagemsg_rightcss = '';
                                    deleteiconcss = '';
                                }
                            }else if(message.indexOf('<img')!=-1 && message.indexOf('src')!=-1){
                                imagemsg_rightcss = 'margin-left:-15px !important;';
                                if(incomingself){
                                    imagemsg_rightcss = 'margin-top:20px !important;';
                                }
                            }
                            if(smileycount > 0 && smileymsg != ''){
                                imagemsg_rightcss = '';
                            }
                        }
                        if($("#cometchat_groupmessage_"+incomingid).length > 0) {
                            $("#cometchat_groupmessage_"+incomingid).find("span.cometchat_chatboxmessagecontent").html(message);
                        } else {
                            var add_bg_self = ' cometchat_self';
                            var add_bg = 'cometchat_chatboxmessage';
                            if((message.indexOf('<img')!=-1 && message.indexOf('src')!=-1 && message.indexOf('cometchat_smiley') == -1) || (smileycount > 0 && smileymsg == '')){
                               if( incomingself ) {
                                    add_bg_self = 'cometchat_chatboxselfmedia';
                                    add_bg = '';
                                }else {
                                    add_bg = 'cometchat_chatboxmedia';
                                    add_bg_self = '';
                                }
                            }
                            sentdata = '';
                            if(sent != null) {
                                var ts = parseInt(sent);
                                if(incomingself) {
                                    sentdata = $[calleeAPI].getTimeDisplay(ts,incomingid,1);
                                }else{
                                    sentdata = $[calleeAPI].getTimeDisplay(ts,incomingid,0);
                                }
                            }
                            if(!incomingself) {
                                var avatar = jqcc.cometchat.getThemeArray('buddylistAvatar', fromid);
                                if(typeof avatar=="undefined"){
                                    avatarstofetch[fromid]=1;
                                    avatar = staticCDNUrl+'images/noavatar.png';
                                }
                                var fromavatar = '<a id="cometchat_usersavatar_'+incomingid+'" href="javascript:void(0)" onclick="javascript:parent.jqcc.cometchat.chatWith(\''+fromid+'\');" class="cometchat_float_l"><span class="cometchat_cr_other_avatar"><img class="cometchat_userscontentavatarsmall cometchat_avatar_'+fromid+'" title="'+fromname+'" src="'+avatar+'"></span></a>';
                                var usernamecontent = '';
                                if (mobileDevice || showUsername == '1') {
                                    usernamecontent = '<span class="cometchat_groupusername">'+fromname+':</span>';
                                }
                                if(incoming.hasOwnProperty('botid') && incoming.botid != 0) {
                                    fromavatar = '<a id="cometchat_usersavatar_'+incoming.id+'" href="javascript:void(0)" onclick="javascript:parent.jqcc.cometchat.chatWith(\''+incoming.fromid+'\');" class="cometchat_float_l"><span class="cometchat_cr_other_avatar"><img class="cometchat_userscontentavatarsmall" title="'+jqcc.cometchat.getThemeArray('botlistName', incoming.botid)+'" src="'+jqcc.cometchat.getThemeArray('botlistAvatar', incoming.botid)+'"></span></a>';
                                    if (mobileDevice || showUsername == '1') {
                                        usernamecontent = '<span class="cometchat_groupusername">'+jqcc.cometchat.getThemeArray('botlistName', incoming.botid)+':</span>';
                                    }
                                }
                                temp += ('<div class="cometchat_time cometchat_time_'+msg_date_class+' '+date_class+'" msg_format="'+msg_date_format+'">'+msg_date+'</div><div style="clear:both;"></div><div class="cometchat_messagebox" id="cometchat_messagebox_'+incomingid+'">'+fromavatar+'<div class="'+add_bg+'" id="cometchat_groupmessage_'+incomingid+'">');
                                temp += (usernamecontent+'<span class="cometchat_chatboxmessagecontent" style="'+imagemsg_rightcss+'">'+message+'</span></div>'+sentdata+'</div><div style="clear:both;"></div>');
                            } else {
                                deletemessagecss = 'style = "'+deleteiconcssself+'"';
                                marginclass = ' cometchat_margin_left ';
                                    marginclass = marginclass+'cometchat_margin_top';
                                var offlineindicator = '<span id="cometchat_chatboxseen_'+incomingid+'"></span>';

                                temp += ('<div class="cometchat_time cometchat_time_'+msg_date_class+' '+date_class+'" msg_format="'+msg_date_format+'">'+msg_date+'</div><div style="clear:both;"></div><div class="cometchat_messagebox cometchat_messagebox_self" id="cometchat_messagebox_'+incomingid+'"><div class="'+add_bg+add_bg_self+'" id="cometchat_groupmessage_'+incomingid+'"><span class="cometchat_chatboxmessagecontent" style="'+imagemsg_rightcss+'">'+message+'</span></div>'+offlineindicator+sentdata+'</div><div style="clear:both;"></div>');
                            }

                            if(incoming.hasOwnProperty('id') && incoming.hasOwnProperty('localmessageid') && $("#cometchat_messagebox_"+incoming.localmessageid).length>0){
                                $("#cometchat_messagebox_"+incoming.localmessageid).after(temp);
                                $("#cometchat_messagebox_"+incoming.localmessageid).remove();
                                var offlinemessages = jqcc.cometchat.getFromStorage('offlinemessagesqueue');
                                if(offlinemessages.hasOwnProperty(incoming.localmessageid)) {
                                    delete offlinemessages[incoming.localmessageid];
                                    jqcc.cometchat.updateToStorage('offlinemessagesqueue',offlinemessages);
                                }
                            }else{
                                $("#currentroom_convotext").append(temp);
                            }

                            if(jqcc.cometchat.getSettings().disableRecentTab == 0) {
                                var temp_msg = jqcc.cometchat.processRecentmessages(message);
                                var params = {'chatid':chatroomid,'isgroup':1,'timestamp':sent,'m':temp_msg,'msgid':incomingid,'force':0,'del':0};
                                jqcc.cometchat.updateRecentChats(params);
                            }
                            if(typeof(incomingid) != 'undefined' && !jqcc.isNumeric(incomingid) &&  incomingid.indexOf('_')>-1) {
                                $("#cometchat_chatboxseen_"+incomingid).addClass('cometchat_offlinemessage');
                            }
                        }
                        if(jqcc.cometchat.getChatroomVars('owner')|| jqcc.cometchat.checkModerator(chatroomid) || (jqcc.cometchat.getChatroomVars('allowDelete') == 1 && incomingself)) {
                            if($("#cometchat_messagebox_"+incomingid).find(".delete_msg").length < 1) {
                                jqcc('#cometchat_messagebox_'+incomingid).append('<span class="delete_msg '+marginclass+'" style="'+deleteiconcss+deleteiconcssself+'" onclick="javascript:jqcc.cometchat.confirmDelete(\''+incomingid+'\');"><img class="hoverbraces" src="'+staticCDNUrl+'modules/chatrooms/bin.svg"></span>');
                            }
                            $(".cometchat_messagebox").live("mouseover",function() {
                                $(this).find(".delete_msg").css('display','inline-block');
                            });
                            $(".cometchat_messagebox").live("mouseout",function() {
                                $(this).find(".delete_msg").css('display','none');
                            });
                        }
                        var forced = (incomingself) ? 1 : 0;
                        if((message).indexOf('<img')!=-1 && (message).indexOf('src')!=-1){
                            $( "#cometchat_groupmessage_"+incomingid+" img" ).load(function() {
                                $[calleeAPI].chatroomScrollDown(forced);
                            });
                        }else{
                            $[calleeAPI].chatroomScrollDown(forced);
                        }

                        if (message != '' && chatroomid != jqcc.cometchat.getChatroomVars('currentroom') && (typeof(receivedcrunreadmessages[chatroomid])=='undefined' || receivedcrunreadmessages[chatroomid] < incomingid)){
                            if(!crUnreadMessages.hasOwnProperty(chatroomid)){
                                crUnreadMessages[chatroomid] = 1;
                            } else {
                                var newUnreadMessages = parseInt(crUnreadMessages[chatroomid]) + 1;
                                crUnreadMessages[chatroomid] = newUnreadMessages;
                            }
                            $[calleeAPI].updateCRReceivedUnreadMessages(chatroomid,incomingid);
                        }
                        if(!incomingself){
                            if($.cookie(settings.cookiePrefix+"sound") && $.cookie(settings.cookiePrefix+"sound") == 'true'){
                                jqcc[calleeAPI].playsound(1);
                            }
                        }
                        if(!$.isEmptyObject(avatarstofetch)){
                            jqcc.cometchat.getUserDetails(Object.keys(avatarstofetch),'updateView');
                        }
                        jqcc.cometchat.setChatroomVars('crUnreadMessages',crUnreadMessages);
                        receivedcrunreadmessages = jqcc.cometchat.getFromStorage('crreceivedunreadmessages');
                        $.each(crUnreadMessages, function(chatroomid,unreadMessageCount) {
                            var chatroomreadmessagesId = chatroomreadmessages[chatroomid];
                            var receivedcrunreadmessagesId = receivedcrunreadmessages[chatroomid];
                            if(receivedcrunreadmessagesId != 'undefined'){
                                if(receivedcrunreadmessagesId > chatroomreadmessagesId){
                                    $[calleeAPI].chatroomUnreadMessages(jqcc.cometchat.getChatroomVars('crUnreadMessages'),chatroomid);
                                }
                            }
                        });

                        var currentroomid = jqcc.cometchat.getChatroomVars('currentroom');
                        if(currentroomid != chatroomid && !incomingself){
                            var count = jqcc.cometchat.getChatroomVars('newMessages');
                            if(count > 0){
                                jqcc[jqcc.cometchat.getChatroomVars('calleeAPI')].addMessageCounter(chatroomid,1,1);
                                jqcc.cometchat.updateChatBoxState({id:chatroomid,g:1,c:1});
                            }
                        }

                        $[calleeAPI].updateCRReadMessages(jqcc.cometchat.getChatroomVars('currentroom'));
                        var crreadmessages = jqcc.cometchat.getFromStorage("crreadmessages");
                        jqcc.cometchat.setChatroomVars('crreadmessages',crreadmessages);

                        $('#currentroom').find('.cometchat_prependCrMessages').remove();
                        prepend = '<div class=\"cometchat_prependCrMessages\" onclick\="jqcc.crembedded.prependCrMessagesInit('+chatroomid+')\" id = \"cometchat_prependCrMessages_'+chatroomid+'\"><?php echo $chatrooms_language[74];?></div>';
                        jqcc.crembedded.groupbyDate();

                        if($("#currentroom").find('.cometchat_prependMessages').length != 1){
                            $('#currentroom_convo').prepend(prepend);
                        }
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
                    if(typeof (jqcc.cometchat.updateToStorage)!=='undefined'){
                        var alreadycrreceivedmessages = jqcc.cometchat.getFromStorage('crreceivedunreadmessages');
                        if((typeof(alreadycrreceivedmessages[id])!='undefined' && parseInt(alreadycrreceivedmessages[id])<parseInt(lastid)) || typeof(alreadycrreceivedmessages[id])=='undefined'){
                            var crreceivedmessages = {};
                            crreceivedmessages[id] = parseInt(lastid);
                            jqcc.cometchat.updateToStorage('crreceivedunreadmessages',crreceivedmessages);
                        }
                    }
                },
                chatroomBoxKeyup: function(event,chatboxtextarea) {
                    if(event.keyCode==8&&$(chatboxtextarea).val()==''){
                        $(chatboxtextarea).css('height', '20px');
                        if(!iOSmobileDevice){
                            $[calleeAPI].chatroomWindowResize();
                        }
                    }
                    var chatboxtextareaheight  = $(chatboxtextarea).height();
                    var maxHeight = 94;
                    chatboxtextareaheight = Math.max(chatboxtextarea.scrollHeight, chatboxtextareaheight);
                    chatboxtextareaheight = Math.min(maxHeight, chatboxtextareaheight);
                    if(chatboxtextareaheight>chatboxtextarea.clientHeight && chatboxtextareaheight<maxHeight){
                        $(chatboxtextarea).css('height', chatboxtextareaheight+'px');
                    }else if(chatboxtextareaheight>chatboxtextarea.clientHeight){
                        $(chatboxtextarea).css('height', maxHeight+'px');
                        $(chatboxtextarea).css('overflow-y', 'auto');
                    }
                    if(!iOSmobileDevice){
                        $[calleeAPI].chatroomWindowResize();
                    }

                },
                chatroomBoxKeydown: function(event,chatboxtextarea,roomno,force){
                    jqcc.cometchat.chatroomBoxKeydown(event,chatboxtextarea,roomno,force);
                },
                crtextboxresize: function(event,chatboxtextarea,roomno,force){
                    var difference = $(chatboxtextarea).innerHeight() - $(chatboxtextarea).height();
                    var container_height = $('#currentroom').find('#cometchat_tabinputcontainer').outerHeight();
                    if((event.keyCode==8 && $(chatboxtextarea).val()=='') || $(chatboxtextarea).val()=='' ) {
                        $(chatboxtextarea).height(20);
                    }
                    if ($(chatboxtextarea).innerHeight < chatboxtextarea.scrollHeight ) {
                    } else if($(chatboxtextarea).height() < 75 || event.keyCode == 8 ) {
                        $(chatboxtextarea).height(20);
                        if(chatboxtextarea.scrollHeight - difference >= 75){
                            $(chatboxtextarea).height(75);
                        }else if(chatboxtextarea.scrollHeight - difference>20){
                            $(chatboxtextarea).height(chatboxtextarea.scrollHeight - difference);
                        }
                        if($('#cometchat_container_smilies').length != 1 && $('#cometchat_container_stickers').length != 1 && $('#cometchat_container_transliterate').length != 1 && $('#cometchat_container_voicenote').length != 1){
                            $[calleeAPI].chatroomWindowResize();
                        }
                        if($('#cometchat_container_smilies').length == 1 || $('#cometchat_container_stickers').length == 1 || $('#cometchat_container_transliterate').length == 1 || $('#cometchat_container_voicenote').length == 1){
                            $('.cometchat_container').css('bottom',$('#currentroom').find('#cometchat_tabinputcontainer').outerHeight(true)+1);
                        }
                        var newcontainerheight = $('#currentroom').find('#cometchat_tabinputcontainer').outerHeight();
                        if(container_height != newcontainerheight){
                            $[calleeAPI].chatroomScrollDown(1);
                        }
                    }else{
                        $(chatboxtextarea).slimScroll({scroll: '1'});
                        $(chatboxtextarea).focus();
                    }
                },
                hidetabs: function() {

                },
                loadLobby: function() {
                    $[calleeAPI].hidetabs();
                    $('#lobbytab').addClass('tab_selected');
                    $('#lobby').css('display','block');
                    clearTimeout(jqcc.cometchat.getChatroomVars('heartbeatTimer'));
                    if(typeof(jqcc.cometchat.getThemeVariable) == 'undefined' || jqcc.cometchat.getThemeVariable('currentStatus') != 'offline'){
                        jqcc.cometchat.chatroomHeartbeat(1);
                    }
                },
                crcheckDropDown: function(dropdown) {
                    var id = dropdown.selectedIndex;
                    if(id == 1) {
                        $('div.password_hide').css('display','block');
                    } else {
                        $('div.password_hide').css('display','none');
                    }
                    $[calleeAPI].chatroomWindowResize();
                },
                loadRoom: function(clicked,id,minimized,unreadmsgcount) {
                    jqcc('.cometchat_userchatbox').removeClass('cometchat_tabopen').addClass('cometchat_tabhidden');
                    $('.cometchat_float_list').remove();
                    jqcc[calleeAPI].hideMenuPopup();
                    var roomname = jqcc.cometchat.getChatroomVars('currentroomname');
                    var roomno = jqcc.cometchat.getChatroomVars('currentroom');
                    var messageCounter = '0';
                    if(typeof(chatroomuserscount['_'+roomno]) == 'undefined'){
                        chatroomuserscount['_'+roomno] = 1;
                    }
                    if($('.cometchat_container').length > 0){
                        jqcc('.cometchat_container').remove();
                        jqcc[calleeAPI].windowResize();
                    }
                    if(embeddedchatroomid==0 || (embeddedchatroomid>0 && embeddedchatroomid==roomno)){
                        if(clicked==1){
                            jqcc.cometchat.setThemeVariable('trayOpen','chatrooms');
                            jqcc.cometchat.setSessionVariable('trayOpen', 'chatrooms');
                            $('.cometchat_userchatbox').removeClass('cometchat_tabopen');
                        }

                        var roomnametext = '<span class="cometchat_user_shortname">'+roomname.toString() + '</span><div id="cometchat_pluginrightarrow_'+roomno+'" class="cometchat_pluginrightarrow"></div><div class="cometchat_userdisplaystatus cometchat_chatroomusercount" style="padding:0;margin:0;"> <?php echo  $chatrooms_language["participants"]; ?>: '+chatroomuserscount['_'+roomno]+'</div>';
                        $('#currentroom').find('.cometchat_chatroomdisplayname').html(roomnametext);
                        $('div.welcomemessage').html('<?php echo $chatrooms_language[4];?>'+'<span> | </span>'+'<?php echo $chatrooms_language[48];?>'+'<?php echo $chatrooms_language[39];?>');
                        var crUnreadMessages = jqcc.cometchat.getChatroomVars('crUnreadMessages');
                        crUnreadMessages[roomno] = 0;
                        jqcc.cometchat.setChatroomVars('crUnreadMessages',crUnreadMessages);
                        var moderatorcontainer = '';
                        if(jqcc.cometchat.checkModerator(roomno) || jqcc.cometchat.getChatroomVars('owner')){
                           moderatorcontainer += '<div class="cometchat_float_outer"><div id="float_unbanchatroomusers" class="unbanChatroomUserembedded float_list list_up" title="<?php echo $chatrooms_language[39];?>" chatroommode="1"><?php echo $chatrooms_language[39];?></div></div>';
                        }
                        if(mobileDevice){
                            var index = settings.plugins.indexOf('screenshare');
                            if(index != -1){
                                settings.plugins.splice(index, 1);
                            }
                        }
                        $('.cometchat_prependMessages_container > .cometchat_prependCrMessages').text("<?php echo $chatrooms_language['load_earlier_msgs'];?>");
                        $('.cometchat_prependMessages_container > .cometchat_prependCrMessages').attr('onclick','jqcc.embedded.prependCrMessagesInit('+roomno+')');
                        $('#currentroom_convo').attr('onscroll','jqcc.crembedded.chatScroll('+roomno+')');

                        var pluginshtml = '';
                        var leavegroup = '<div class="cometchat_float_outer"><div id="cometchat_float_leavegroup" class="cometchat_leavegroup float_list list_up leaveRoom" title="<?php echo $chatrooms_language[72];?>" chatroommode="1"><?php echo $chatrooms_language[72];?></div></div>';
                        if(embeddedchatroomid > 0){
                            leavegroup = '';
                        }

                        var pluginsuphtml = '<div id="cometchat_pluginrightarrow_'+roomno+'_float_list"  class="cometchat_float_list cometchat_arrowup" style="top:-400px;left:-400px;display:block;"><div class="cometchat_arrow-up"></div><div class="cometchat_float_outer"><div id="cometchat_float_chatroomusers" class="cometchat_chatroomusers float_list list_up" title="<?php echo $chatrooms_language["view_users"];?>" chatroommode="1"><?php echo $chatrooms_language["view_users"];?></div></div>'+leavegroup+moderatorcontainer;
                        var pluginsdownhtml = '<div id="cometchat_group_pluginuparrow_'+roomno+'_float_list" class="cometchat_float_list cometchat_arrowdown" style="top:-400px;left:-400px;display:block;"><div class="cometchat_arrow-down"></div>';
                        var audiochat = '';
                        var plugins = jqcc.cometchat.getChatroomVars('plugins');
                        var avchathtml = '';
                        var smilieshtml = '';
                        var voicenotehtml = '';
                        var filetransferhtml = '';

                        if(jqcc.cometchat.getCcvariable().callbackfn!=""&&jqcc.cometchat.getCcvariable().callbackfn=="desktop"){
                            if((plugins).indexOf('screenshare') != -1){
                               var ccpluginindex=(plugins).indexOf('screenshare');
                               plugins.splice(ccpluginindex,1);
                           }
                        }
                        if(plugins.length > 0) {
                            for (var i=0;i<plugins.length;i++) {
                                var name = 'cc'+settings.plugins[i];
                                if(settings.plugins[i] == 'audiochat'){
                                    audiochat = '<div class="ccplugins" id="cometchat_audiochaticon" title="'+$[name].getTitle()+'" name="'+name+'" to="'+roomno+'" chatroommode="1"></div>';
                                    if($(window).width() < 300){
                                        audiochat = '';
                                        pluginsdownhtml += '<div class="cometchat_float_outer"><div id="cometchat_float_'+settings.plugins[i]+'" class="ccplugins cometchat_'+settings.plugins[i]+' float_list list_down" title="'+$[name].getTitle()+'" name="'+name+'" to="'+roomno+'" chatroommode="1">'+$[name].getTitle()+'</div></div>';
                                    }
                                }else if(settings.plugins[i]=='avchat'){
                                    avchathtml = '<div class="ccplugins " id="cometchat_videochaticon" title="'+$[name].getTitle()+'" name="'+name+'" to="'+roomno+'" chatroommode="1"></div>';
                                    if($(window).width() < 250){
                                        avchathtml = '';
                                        pluginsdownhtml += '<div class="cometchat_float_outer"><div id="cometchat_float_'+settings.plugins[i]+'" class="ccplugins cometchat_'+settings.plugins[i]+' float_list list_down" title="'+$[name].getTitle()+'" name="'+name+'" to="'+roomno+'" chatroommode="1">'+$[name].getTitle()+'</div></div>';
                                    }
                                }else if(settings.plugins[i]=='smilies'){

                                    smilieshtml = '<div id="smileyicon" class="ccplugins cometchat_smilies" title="'+$[name].getTitle()+'" name="'+name+'" to="'+roomno+'" chatroommode="1" ><img src="'+staticCDNUrl+'layouts/<?php echo $layout;?>/images/smileyicon.svg"></div>';
                                }else if(settings.plugins[i]=='filetransfer'){
                                    filetransferhtml='<div id="cometchat_attachements"><img src="'+staticCDNUrl+'layouts/'+calleeAPI+'/images/attach.svg" class="ccplugins" title="'+$[name].getTitle()+'" name="'+name+'" to="'+roomno+'" chatroommode="1"/></div>';
                                }else if(typeof($[name]) == 'object') {
                                    if(name == 'ccstickers' || name == 'cchandwrite' || name == 'ccscreenshare' || name == 'ccwhiteboard' || name == 'ccwriteboard' || name == 'ccbroadcast' || name == 'cctransliterate' || name == 'ccaudiochat' || name == 'ccavchat' || name == 'ccvoicenote'){
                                        if($(window).width() < 300 && name == 'ccaudiochat'){
                                            pluginsdownhtml += '<div class="cometchat_float_outer"><div id="cometchat_float_'+settings.plugins[i]+'" class="ccplugins cometchat_'+settings.plugins[i]+' float_list list_down" title="'+$[name].getTitle()+'" name="'+name+'" to="'+roomno+'" chatroommode="1">'+$[name].getTitle()+'</div></div>';
                                        }else if($(window).width() < 250 && name == 'ccavchat'){
                                            pluginsdownhtml += '<div class="cometchat_float_outer"><div id="cometchat_float_'+settings.plugins[i]+'" class="ccplugins cometchat_'+settings.plugins[i]+' float_list list_down" title="'+$[name].getTitle()+'" name="'+name+'" to="'+roomno+'" chatroommode="1">'+$[name].getTitle()+'</div></div>';
                                        }else{
                                            if(mobileDevice && name == 'cctransliterate'){
                                            }else{
                                                pluginsdownhtml += '<div class="cometchat_float_outer"><div id="cometchat_float_'+settings.plugins[i]+'" class="ccplugins cometchat_'+settings.plugins[i]+' float_list list_down" title="'+$[name].getTitle()+'" name="'+name+'" to="'+roomno+'" chatroommode="1">'+$[name].getTitle()+'</div></div>';
                                            }
                                        }

                                    }else if(name == 'ccclearconversation' || name == 'ccblock' || name == 'ccchathistory' || name == 'ccreport' || name == 'ccsave' || name == 'ccstyle'){
                                        pluginsuphtml += '<div class="cometchat_float_outer"><div id="cometchat_float_'+settings.plugins[i]+'" class="ccplugins cometchat_'+settings.plugins[i]+' float_list list_up" title="'+$[name].getTitle()+'" name="'+name+'" to="'+roomno+'" chatroommode="1">'+$[name].getTitle()+'</div></div>';
                                    }
                                }
                            }
                        }
                        pluginsuphtml += '</div>';
                        pluginsdownhtml += '</div>';
                        var headerplugin = avchathtml+audiochat+'<div class="inviteChatroomUsers" id="inviteusersicon" title="<?php echo $chatrooms_language[22]; ?>"></div><div id="vline"><img src="'+staticCDNUrl+'layouts/<?php echo $layout;?>/images/vline.svg"/ style="margin-top:12px;"></div> <div  class="cometchat_user_closebox"><img src="'+staticCDNUrl+'layouts/<?php echo $layout;?>/images/remove.svg"/></div>';
                        pluginshtml = pluginsuphtml+pluginsdownhtml;
                        var inputcontainer = '<div id="downplugins"><div id="cometchat_group_pluginuparrow_'+roomno+'" class="cometchat_pluginuparrow"><img class="cometchat_pluginuparrowimage" src="'+staticCDNUrl+'layouts/'+calleeAPI+'/images/circledown.svg"/></div><textarea id="cometchat_textarea_'+roomno+'" class="cometchat_textarea" style="overflow-y: hidden;"  placeholder="<?php echo $chatrooms_language[64]; ?>"></textarea>'+smilieshtml+filetransferhtml+pluginshtml+'</div>';
                        if($('#currentroom_left .cometchat_avchatOption').length > 0){
                            $('#currentroom_left .cometchat_avchatOption').remove();
                        }
                        $('#currentroom_left .cometchat_chatboxMenuOptions').html(headerplugin);
                        if($('#currentroom_left .cometchat_smilies').length > 0){
                            $('#currentroom_left .cometchat_smilies').remove();
                        }
                        $('#currentroom_left .cometchat_tabcontentinput').prepend(smilieshtml);
                        if($('#currentroom_left .cometchat_filetransfer').length > 0){
                            $('#currentroom_left .cometchat_filetransfer').remove();
                        }
                        $('#currentroom_left #cometchat_tabinputcontainer').html(inputcontainer);
                        if(typeof(jqcc.cometchat.checkInternetConnection) && !jqcc.cometchat.checkInternetConnection()) {
                            jqcc.embedded.noInternetConnection(true);
                        }
                    }

                    setTimeout(function(){
                        $[calleeAPI].chatroomScrollDown(1);
                    },500);
                    if($('#currentroom_convo').find('.cometchat_prependCrMessages').length  == 0){
                        $("#currentroom").find('#currentroom_convo').prepend('<div class=\"cometchat_prependCrMessages\" onclick\="jqcc.crembedded.prependCrMessagesInit('+roomno+')\" id = \"cometchat_prependCrMessages_'+roomno+'\"><?php echo $chatrooms_language[74];?></div>');
                    }
                    if(!$('#currentroom').find('#cometchat_uploadfile_'+roomno).length) {
                        var uploadf = document.createElement("INPUT");
                        uploadf.setAttribute("type", "file");
                        uploadf.setAttribute("class", "cometchat_fileupload");
                        uploadf.setAttribute("id", 'cometchat_uploadfile_'+roomno);
                        uploadf.setAttribute("name", "Filedata");
                        uploadf.setAttribute("multiple", "true");
                        $('#currentroom').find(".cometchat_userchatarea").append(uploadf);
                        uploadf.addEventListener("change", jqcc.ccfiletransfer.FileSelectHandler($("#currentroom"),roomno,1), false);
                    }
                    /*Uncomment for drag and drop*/
                    /*
                        var cometchat_user_popup1 = document.getElementById('currentroom');
                        var xhr = new XMLHttpRequest();
                        if(xhr.upload) {
                            cometchat_user_popup1.addEventListener("dragover", jqcc.ccfiletransfer.loadRFileDragHover($("currentroom"),roomno), false);
                            cometchat_user_popup1.addEventListener("dragleave", jqcc.ccfiletransfer.FileDragHover($("currentroom"),roomno), false);
                            cometchat_user_popup1.addEventListener("drop", jqcc.ccfiletransfer.FileSelectHandler($("#currentroom"),roomno,1), false);
                        }
                    }*/
                    var tab = $('#cometchat_righttab').width();
                    $('#cometchat_righttab').find('.cometchat_textarea').css('width',tab-140);
                    $('#currentroom').find('.cometchat_textarea').on('paste input',function(){
                        if($(this).val().length > 380){
                            $(this).height(75);
                            $('#currentroom').find('.cometchat_textarea').slimScroll({scroll: '1'});
                            $[calleeAPI].chatroomWindowResize();
                        }
                    });
                    $('#currentroom').find('.cometchat_textarea').keyup(function(event){
                        var textvalue = $('#currentroom').find("textarea.cometchat_textarea").val();
                        if(mobileDevice && textvalue != '' && $('#currentroom').find('#cometchat_send').length == 0 ){
                            var sendicon = '<div id="cometchat_send"><img src="'+staticCDNUrl+'layouts/'+calleeAPI+'/images/send.svg" class="cometchat_tabcontentsubmit" /></div>';
                            $('#currentroom').find('#cometchat_attachements').replaceWith(sendicon);
                        }else if(mobileDevice && textvalue == '' && $('#currentroom').find('#cometchat_send').length == 1){
                            var attachicon = '<div id="cometchat_attachements"><img src="'+staticCDNUrl+'layouts/'+calleeAPI+'/images/attach.svg" class="ccplugins" title="'+$['ccfiletransfer'].getTitle()+'" name="ccfiletransfer" to="'+roomno+'" chatroommode="0"/></div>';
                            $('#currentroom').find('#cometchat_send').replaceWith(attachicon);
                        }
                        $[calleeAPI].crtextboxresize(event,this,roomno);
                        if(event.keyCode==8&&$(this).val()==''){
                            $(this).css('height', '20px');
                            if(!iOSmobileDevice){
                                $[calleeAPI].chatroomWindowResize();
                            }
                        }
                    });
                    $('#currentroom').on('click','.cometchat_tabcontentsubmit',function(){
                        var textarea = $('#currentroom').find('.cometchat_textarea');
                        return jqcc.embedded.chatroomBoxKeydown(event, textarea, jqcc.cometchat.getChatroomVars('currentroom'),1);
                    });
                    $('#currentroom').find('.cometchat_textarea').keydown(function(event){
                        return jqcc.embedded.chatroomBoxKeydown(event, this, roomno);
                    });

                    $('#currentroom').find('.cometchat_textarea').blur(function(event){
                        var textvalue = $('#currentroom').find("textarea.cometchat_textarea").val();
                        if(textvalue == '' && mobileDevice){
                            var attachicon = '<div id="cometchat_attachements"><img src="'+staticCDNUrl+'layouts/'+calleeAPI+'/images/attach.svg" class="ccplugins" title="'+$['ccfiletransfer'].getTitle()+'" name="ccfiletransfer" to="'+roomno+'" chatroommode="1"/></div>';
                            $('#currentroom').find('#cometchat_send').replaceWith(attachicon);
                        }
                    });
                    $('#currentroom').find('.cometchat_textarea').focus(function(event){
                        setTimeout(function(){
                            var textvalue = $('#currentroom').find("textarea.cometchat_textarea").val();
                            if(mobileDevice && textvalue != ''){
                                var sendicon = '<div id="cometchat_send"><img src="'+staticCDNUrl+'layouts/'+calleeAPI+'/images/send.svg" class="cometchat_tabcontentsubmit" /></div>';
                                $('#currentroom').find('#cometchat_attachements').replaceWith(sendicon);
                            }else if(mobileDevice && textvalue == ''){
                                var attachicon = '<div id="cometchat_attachements"><img src="'+staticCDNUrl+'layouts/'+calleeAPI+'/images/attach.svg" class="ccplugins" title="'+$['ccfiletransfer'].getTitle()+'" name="ccfiletransfer" to="'+id+'" chatroommode="0"/></div>';
                                $('#currentroom').find('#cometchat_send').replaceWith(attachicon);
                                $('#currentroom').find("textarea.cometchat_textarea").height(20);
                            }
                        },100);
                    });
                    $('#currentroom').find('.ccplugins').click(function(event){
                        event.stopImmediatePropagation();
                        $('.cometchat_float_list').hide();
                        jqcc[calleeAPI].hideMenuPopup();
                        var name = $(this).attr('name');
                        var to = $(this).attr('to');
                        var chatroommode = $(this).attr('chatroommode');
                        var roomname = jqcc.cometchat.getChatroomVars('currentroomname');
                        var roomid = jqcc.cometchat.getChatroomVars('currentroom');
                        var tabcontenttext_height = ($(window).innerHeight()*30)/100;
                        var tabcontenttext_width = $(window).innerWidth();
                        var winHt = $(window).innerHeight();
                        var winWidth = $(window).innerWidth();
                        var caller = 'cometchat_embedded_iframe';
                        $('#currentroom').find('.cometchat_pluginrightarrow').rotate(0);
                        if(jqcc.cometchat.getCcvariable().callbackfn != "desktop" && window.top != window.self){
                            if(window.hasOwnProperty('frameElement') && window['frameElement']!=null && window['frameElement'].hasOwnProperty('id')){
                                caller = window.frameElement.id;
                            }
                        }
                        if((!mobileDevice)|| name == 'ccclearconversation' || name == 'ccsave') {
                            if(name == 'ccsmilies' && $("#cometchat_container_smilies").length > 0){
                                $('#cometchat_container_smilies').animate({"top":"100%"}, "slow").removeClass('visible');
                                jqcc("#cometchat_tooltip").css('display', 'none');
                                setTimeout(function() {
                                    $('#cometchat_container_smilies').remove();
                                },500);
                            }else{
                                if(name == 'ccsmilies'){
                                    $('#main_container').find('.cometchat_pluginuparrow').addClass('rotated').css({'transform' :'rotate(180deg)'});
                                }
                                var controlparameters = {"to":to, "chatroommode":chatroommode, "roomname":roomname, "roomid":roomid};
                                jqcc[name].init(controlparameters);
                            }
                        } else if(name=='ccstickers' && mobileDevice){
                            if($('#cometchat_container_stickers').length == 0){
                                var controlparameters = {"to":to, "chatroommode":chatroommode, "roomname":roomname, "roomid":roomid};
                                jqcc[name].init(controlparameters);
                            }
                        } else if(name=='ccsmilies' && mobileDevice){
                            var controlparameters = {"to":to, "chatroommode":chatroommode, "roomname":roomname, "roomid":roomid};
                            jqcc[name].init(controlparameters);
                        }else {
                            var controlparameters = {"to":to, "chatroommode":chatroommode, "roomname":roomname, "roomid":roomid, "caller":caller};
                            jqcc[name].init(controlparameters);
                        }
                        if(name != 'ccstickers' && name != 'cctransliterate' && name != 'ccsmilies' && name != 'ccvoicenote') {
                            $('.cometchat_pluginuparrow').removeClass('rotated').css({'transform' :'rotate(0deg)'});
                        }
                    });
                    $("#currentroom").show();
                    $[calleeAPI].chatroomWindowResize();
                    var prevchatroom = jqcc.cometchat.getThemeVariable('openedChatbox');
                    if(typeof(prevchatroom) != 'undefined' && prevchatroom != '' && isNaN(prevchatroom)) {
                        prevchatroom = prevchatroom.replace('_','');
                        jqcc.cometchat.updateChatBoxState({id:prevchatroom,g:1,s:2});
                    }

                    jqcc.cometchat.setThemeVariable('openedChatbox','_'+id);
                    var extension_set = jqcc.cometchat.getSettings().extensions;
                    var extensions_array = [];
                    extensions_array.push(extension_set);

                    if(extensions_array[0].indexOf('ads') > -1){
                        jqcc.ccads.init();
                    }
                    if(typeof(jqcc.cometchat.checkInternetConnection) && !jqcc.cometchat.checkInternetConnection()) {
                        jqcc.embedded.noInternetConnection(true);
                    }
                    if ($('#cometchat_group_pluginuparrow_'+roomno+'_float_list').children().length <=1) {
                        $(".cometchat_pluginuparrow").remove();
                    }
                },
                chatroomWindowResize: function() {
                    var roomno = jqcc.cometchat.getChatroomVars('currentroom');
                    var w=window,d=document,e=d.documentElement,g=d.getElementsByTagName('body')[0],winWidth=w.innerWidth||e.clientWidth||g.clientWidth,winHt=w.innerHeight||e.clientHeight||g.clientHeight;
                    var searchbar_Height = $("#searchbar").outerHeight(true);
                    var jabber_Height = $('#jabber_login').is(':visible') ? $('#jabber_login').outerHeight(true) : 0;
                    var lobbyroomsHeight = winHt-$('#cometchat_header').outerHeight(true)-$('#cometchat_chatstab').outerHeight(true)-$('#cometchat_trayicons').outerHeight(true)-searchbar_Height-jabber_Height-21+'px';
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
                    if(jqcc().slimScroll && !mobileDevice){
                        if($( ".right_footer" ).length == 1){
                            lobbyroomsHeight = parseInt(lobbyroomsHeight)-20+'px';
                        }
                        $('#lobby_rooms').parent('.slimScrollDiv').css('height','auto');
                    }
                    $('#lobby_rooms').css('height','auto');
                    var prependHeight = parseInt($('.cometchat_prependMessages_container').outerHeight(true));
                    var headerheight = parseInt($('#cometchat_header').outerHeight(true));
                    if($('#cometchat_righttab').css('top') == "0px" || $('#cometchat_header').length != 1){
                        var roomConvoHeight = winHt-$('#cometchat_tabinputcontainer').outerHeight(true)-($('#currentroom_left').find('.cometchat_tabsubtitle').outerHeight(true))-prependHeight ;
                    }else{
                        var roomConvoHeight = winHt-$('#cometchat_tabinputcontainer').outerHeight(true)-($('#currentroom_left').find('.cometchat_tabsubtitle').outerHeight(true))-prependHeight - headerheight;
                    }
                    var tab = $('#cometchat_righttab').width();
                    var diff = 140;
                    if ($('#cometchat_group_pluginuparrow_'+roomno+'_float_list').children().length <=1) {
                        var diff = 0;
                    }

                    $('.cometchat_textarea').css('width',tab - diff);
                    if(jqcc().slimScroll && !mobileDevice){
                        $("#currentroom_convo").css('height',roomConvoHeight+'px');
                        $("#currentroom_convo").parent("div.slimScrollDiv").css('height',roomConvoHeight+'px');
                    } else {
                        $("#currentroom_convo").css('height',roomConvoHeight +'px');
                        $("#currentroom_convo").css('overflow','auto');
                    }
                    $[calleeAPI].cradjustIcons($(window).width(),jqcc('#currentroom'),roomno,1);
                },
                kickid: function(kickid) {
                    $("#cometchat_container_chatroomusers").find("#chatroom_userlist_"+kickid).remove();
                },
                banid: function(banid) {
                    $("#cometchat_container_chatroomusers").find("#chatroom_userlist_"+banid).remove();
                },
                chatroomScrollDown: function(forced) {
                    if(enableType != 2 && settings.newMessageIndicator == 1 && ($('#currentroom_convotext').length > 0) && ($('#currentroom_convotext').outerHeight()+$('#currentroom_convotext').offset().top-$('#currentroom_convo').height()-$('#currentroom_convo').offset().top-(2*$('.cometchat_chatboxmessage').outerHeight(true))>0)){
                        if(($('#currentroom_convo').height()-$('#currentroom_convotext').outerHeight()) < 0){
                            if(forced) {
                                if(jqcc().slimScroll && !mobileDevice){
                                    $('#currentroom_convo').slimScroll({scroll: '1'});
                                } else {
                                    setTimeout(function() {
                                    $("#currentroom_convo").scrollTop(50000);
                                    },100);
                                }
                                if($('.talkindicator').length != 0){
                                $('.talkindicator').fadeOut();
                                }
                            }else{
                                if(!$('.talkindicator').length != 0){
                                    var indicator = "<a class='talkindicator' href='#'><?php echo $chatrooms_language[52];?></a>";
                                    $('#currentroom_convo').append(indicator);
                                    $('.talkindicator').click(function(e) {
                                        e.preventDefault();
                                        if(jqcc().slimScroll && !mobileDevice){
                                            $('#currentroom_convo').slimScroll({scroll: '1'});
                                        } else {
                                            setTimeout(function() {
                                                $("#currentroom_convo").scrollTop(50000);
                                            },100);
                                        }
                                        $('.talkindicator').fadeOut();
                                    });
                                    $('#currentroom_convo').scroll(function(){
                                        if($('#currentroom_convotext').outerHeight() + $('#currentroom_convotext').offset().top - $('#currentroom_convo').offset().top <= $('#currentroom_convo').height()){
                                            $('.talkindicator').fadeOut();
                                        }
                                    });
                                }
                            }
                        }
                    }else{
                        if(jqcc().slimScroll && !mobileDevice){
                            setTimeout(function() {
                                $('#currentroom_convo').slimScroll({scroll: '1'});
                            },100);
                        } else {
                            setTimeout(function() {
                                $("#currentroom_convo").scrollTop(50000);
                            },100);
                        }
                    }
                    jqcc[calleeAPI].windowResize();
                },
                cradjustIcons: function(width,cometchat_div,id,chatroommode){
                    var chathtml = '';

                    if(width < 300 && cometchat_div.find('#cometchat_audiochaticon').length > 0){
                        cometchat_div.find('#cometchat_audiochaticon').remove();
                        var content = '<div class="cometchat_float_outer"><div id="cometchat_float_audiochat" class="ccplugins cometchat_ccaudiochat float_list list_down" title="'+$['ccaudiochat'].getTitle()+'" name="ccaudiochat" to="'+id+'" chatroommode="1">'+$['ccaudiochat'].getTitle()+'</div></div>';
                        cometchat_div.find('#cometchat_group_pluginuparrow_'+id+'_float_list').append(content);
                    }
                    if(width < 276 && cometchat_div.find('#cometchat_videochaticon').length > 0){
                        cometchat_div.find('#cometchat_videochaticon').remove();
                        var content = '<div class="cometchat_float_outer"><div id="cometchat_float_avchat" class="ccplugins cometchat_ccaudiochat float_list list_down" title="'+$['ccavchat'].getTitle()+'" name="ccavchat" to="'+id+'" chatroommode="1">'+$['ccavchat'].getTitle()+'</div></div>';
                        cometchat_div.find('#cometchat_group_pluginuparrow_'+id+'_float_list').append(content);
                    }
                    if(width > 276 && cometchat_div.find('#cometchat_videochaticon').length == 0 && $.inArray('avchat',settings.plugins) != -1){
                        var name = 'ccavchat';
                        chathtml = '<div class="ccplugins " id="cometchat_videochaticon" title="'+$[name].getTitle()+'" name="'+name+'" to="'+id+'" chatroommode="'+chatroommode+'"></div>';
                        cometchat_div.find('#cometchat_group_pluginuparrow_'+id+'_float_list').find('#cometchat_float_avchat').parent().remove();
                    }
                    if(width > 300 && cometchat_div.find('#cometchat_audiochaticon').length == 0 && $.inArray('audiochat',settings.plugins) != -1){
                        var name = 'ccaudiochat';
                        chathtml = '<div class="ccplugins" id="cometchat_audiochaticon" title="'+$[name].getTitle()+'" name="'+name+'" to="'+id+'" chatroommode="'+chatroommode+'"></div>';
                        cometchat_div.find('#cometchat_group_pluginuparrow_'+id+'_float_list').find('#cometchat_float_audiochat').parent().remove();
                    }
                    cometchat_div.find('#cometchat_pluginsonheader').prepend(chathtml);
                },
                createChatroomSubmitStruct: function() {
                    var string = $('input.create_input').val();
                    var room={};
                    if(($.trim( string )).length == 0) {
                        return false;
                    }
                    var name = document.getElementById('name').value;
                    name = (name).replace(/'/g, "%27");
                    var type = document.getElementById('type').value;
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
                    var windowHeight = 0;
                    if(typeof(window.innerHeight) == 'number') {
                        windowHeight = window.innerHeight;
                    } else {
                        if(document.documentElement && document.documentElement.clientHeight) {
                            windowHeight = document.documentElement.clientHeight;
                        } else {
                            if(document.body && document.body.clientHeight) {
                                windowHeight = document.body.clientHeight;
                            }
                        }
                    }
                    return windowHeight;
                },
                crgetWindowWidth: function() {
                    var windowWidth = 0;
                    if(typeof(window.innerWidth) == 'number') {
                        windowWidth = window.innerWidth;
                    } else {
                        if(document.documentElement && document.documentElement.clientWidth) {
                            windowWidth = document.documentElement.clientWidth;
                        } else {
                            if(document.body && document.body.clientWidth) {
                                windowWidth = document.body.clientWidth;
                            }
                        }
                    }
                    return windowWidth;
                },
                selectChatroom: function(currentroom,id) {
                    jqcc("#cometchat_chatroomlist_"+currentroom).removeClass("cometchat_chatroomselected");
                    jqcc("#cometchat_chatroomlist_"+id).addClass("cometchat_chatroomselected");
                },
                checkOwnership: function(owner,isModerator,name) {
                    name  = decodeURI(name);
                    var id = jqcc.cometchat.getChatroomVars('currentroom');
                    var switchroom = 'javascript:jqcc["'+calleeAPI+'"].switchChatroom('+id+',"1")';
                    if(owner || isModerator) {
                        jqcc.cometchat.setChatroomVars('isModerator',1);
                    } else {
                        jqcc('#currentroomtab').html('<a href="javascript:void(0);" show=0 onclick='+switchroom+'>'+name+'</a>');
                        jqcc.cometchat.setChatroomVars('isModerator',0);
                    }
                    jqcc.cometchat.chatroomHeartbeat(1);
                    jqcc('#currentroom_convotext').html('');
                    jqcc("#chatroomuser_container").html('');
                },
                leaveRoomClass : function(currentroom) {
                    jqcc("#cometchat_chatroomlist_"+currentroom).removeClass("cometchat_chatroomselected");
                },
                removeCurrentRoomTab : function(id) {
                    $('.cometchat_user_closebox').click();
                    /*jqcc('.lobby_rooms').find('#cometchat_chatroomlist_'+id).first().css('display','none');*/
                    /*var cc_chatroom = JSON.parse($.cookie('<?php echo $cookiePrefix;?>crstate'));
                    var chatroomdata = cc_chatroom.active;
                    if(Object.keys(chatroomdata).length == 0){
                        $('#currentroom').hide();
                    }*/
                },
                updateGroupCategory : function(id) {
                    if($('#cometchat_chatroomlist_'+id).length){
                        var element = $('#cometchat_chatroomlist_'+id).detach();
                        $('#cometchat_othergroupslist').append(element);
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
                    var temp1 = '';
                    var temp2 = '';
                    var joinedgroupshtml = '';
                    var othergroupshtml = '';
                    var joinedgroups = 0;
                    var othergroups = 0;
                    var onlineNumber = 0;
                    var userCountCss = "style='display:none'";
                    var chatrooms = {};
                    var joinedgroupslist = '<div id="cometchat_joinedgroupslist">';
                    var othergroupslist = '<div id="cometchat_othergroupslist">';
                    if(settings.showChatroomUsers == 1){
                        userCountCss = '';
                    }
                    $.each(item, function(i,room) {
                        chatrooms[i] = room;
                        jqcc.cometchat.setChatroomVars('chatroomdetails',chatrooms);

                        longname = room.name;
                        shortname = room.name;
                        if(room.status == 'available') {
                            onlineNumber++;
                        }
                        var selected = '';
                        if(jqcc.cometchat.getChatroomVars('currentroom') == room.id) {
                            selected = ' cometchat_chatroomselected';
                        }
                        var roomtype = '';
                        var deleteroom = '';
                        var renameChatroom = '';
                        var crUnreadMessages = jqcc.cometchat.getChatroomVars('crUnreadMessages');

                        if(room.type == 1) {
                            roomtype = '<span class="lobby_room_3"><img src="'+staticCDNUrl+'layouts/'+calleeAPI+'/images/lock.png" /></span>';
                        }

                        if(room.owner == true){
                            deleteroom = '<span class="lobby_room_4" title="<?php echo $chatrooms_language[58];?>" onclick="javascript:jqcc.cometchat.deleteChatroom(event,\''+room.id+'\');"><img class="hoverbraces" src="'+staticCDNUrl+'layouts/docked/images/bin.svg"></span>';
                            renameChatroom = '<span class="lobby_room_6" title="<?php echo $chatrooms_language[80];?>" onclick="javascript:jqcc.'+[calleeAPI]+'.renameChatroom(event,\''+room.id+'\');"><img src="'+staticCDNUrl+'layouts/'+calleeAPI+'/images/pencil_buddylist.png" /></span>';
                        }
                        var temp = '<div id="cometchat_chatroomlist_'+room.id+'" class="lobby_room'+selected+'" onmouseover="jqcc(this).addClass(\'cometchat_userlist_hover\');" onmouseout="jqcc(this).removeClass(\'cometchat_userlist_hover\');" onclick="javascript:jqcc.cometchat.chatroom(\''+room.id+'\',\''+cc_urlencode(shortname)+'\',\''+room.type+'\',\''+room.i+'\',\''+room.s+'\',\'1\',\'1\');" style="display:block !important;">'+roomtype+''+deleteroom+'<span class="lobby_room_5" style="display:none;"></span>'+renameChatroom+'<div><span class="cometchat_chatroomimage"><img src="'+staticCDNUrl+'layouts/<?php echo $layout;?>/images/group.svg"></span><span class="lobby_room_1" title="'+longname+'"><span class="currentroomname">'+longname+'</span><br/> <span class="lobby_room_2" '+userCountCss+' title="'+room.online+' <?php echo $chatrooms_language[34];?>"><?php echo  $chatrooms_language["participants"]; ?>:&nbsp;'+room.online+'</span></span></div></span></div>';
                        if(room.id == jqcc.cometchat.getChatroomVars('currentroom')){
                            $('#currentroom').find('.cometchat_chatroomusercount').html('<?php echo  $chatrooms_language["participants"]; ?>:&nbsp;'+room.online);
                        }
                        chatroomuserscount['_'+room.id] = room.online;
                        if(room.j === 1) {
                            joinedgroups++;
                            jqcc.cometchat.joinGroup(room.id);
                            joinedgroupslist += temp;
                        } else {
                            othergroups++;
                            othergroupslist += temp;
                        }
                    });

                    joinedgroupslist += '</div>';
                    othergroupslist += '</div>';
                    jqcc.cometchat.refreshRecentChats();

                    if(Object.keys(item).length != 0) {
                        if(document.getElementById('lobby_rooms')) {
                            if(joinedgroups>0){
                                joinedgroupshtml = '<div id="cometchat_joinedgroups" class="cometchat_groupsclassifier"><div class="cometchat_labels"><?php echo $chatrooms_language[78];?></div></div>';
                            }

                            if(othergroups>0 && joinedgroups>0){
                                othergroupshtml = '<div id="cometchat_othergroups" class="cometchat_groupsclassifier"><div class="cometchat_labels"><?php echo $chatrooms_language[77];?></div></div>';
                            }
                            jqcc.cometchat.replaceHtml('lobby_rooms', '<div>'+joinedgroupshtml+joinedgroupslist+othergroupshtml+othergroupslist+'</div>');
                        }
                    }else{
                        jqcc('#lobby_rooms').html('<div class="lobby_noroom"><?php echo $chatrooms_language[53]; ?></div>');
                    }
                },
                displayChatroomMessage: function(item,fetchedUsers) {
                    var beepNewMessages = 0,
                        crUnreadMessages = jqcc.cometchat.getChatroomVars('crUnreadMessages'),
                        chatroomreadmessages = jqcc.cometchat.getFromStorage("crreadmessages"),
                        receivedcrunreadmessages = jqcc.cometchat.getFromStorage('crreceivedunreadmessages'),
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
                        prepend = '',
                        localmessageid = '',
                        avatarstofetch = {},
                        messagewrapperid = '',
                        trayIcons = jqcc.cometchat.getTrayicon(),
                        isRealtimetranslateEnabled = trayIcons.hasOwnProperty('realtimetranslate');

                    $.each(item, function(i,incoming) {
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



                        jqcc.cometchat.setChatroomVars('timestamp',incoming.id);
                        var message = jqcc.cometchat.processcontrolmessage(incoming);
                        var msg_time = incoming.sent;
                        var incomingself = 1;
                        var fromid = incoming.fromid;

                        msg_time = String(msg_time);

                        if (msg_time.length == 10){
                            msg_time = parseInt(msg_time * 1000);
                        }

                        var months_set = new Array();

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
                        var deletemessagecss = '';

                        if(incoming.hasOwnProperty('self')){
                            incomingself = incoming.self;
                        }
                        if(fromid != settings.myid || (incoming.hasOwnProperty('botid') && incoming.botid != 0)) {
                            incomingself = 0;
                        }

                        if (message != '' && incoming.chatroomid == jqcc.cometchat.getChatroomVars('currentroom') && typeof(message) != 'undefined') {
                                var temp = '';
                                var fromname = incoming.from;

                                var prepend = '';
                                prepend = '<div class=\"cometchat_prependCrMessages\" onclick\="jqcc.crembedded.prependCrMessagesInit('+incoming.chatroomid+')\" id = \"cometchat_prependCrMessages_'+incoming.chatroomid+'\"><?php echo $chatrooms_language[74];?></div>';
                                    if((incoming.message).indexOf('has shared a file')!=-1){
                                        if(jqcc.cometchat.getCcvariable().callbackfn=="desktop"){
                                            if(incoming.message.indexOf('target')>=-1){
                                                incoming.message=incoming.message.replace(/target="_blank"/g,'');
                                            }
                                        }
                                    }
                                    if((incoming.message).indexOf('has shared a handwritten message')!=-1){
                                        if(jqcc.cometchat.getCcvariable().callbackfn=="desktop"){
                                            /*if(incoming.message.indexOf('href')>=-1){
                                                var start = (incoming.message).indexOf('href');
                                                var end = (incoming.message).indexOf('target');
                                                var HtmlString=(incoming.message).slice(start,end);
                                                incoming.message=(incoming.message).replace(HtmlString,'');
                                            }*/
                                        }
                                    }
                                var smileycount = (message.match(/cometchat_smiley/g) || []).length;
                                var smileymsg = message.replace(/<img[^>]*>/g,"");
                                var imagemsg_rightcss = '';
                                var deleteiconcss = '';
                                var deleteiconcssself = '';
                                var marginclass = '';

                                smileymsg = smileymsg.trim();
                                if(message.indexOf('<img')!=-1){
                                    if(!incomingself){
                                        imagemsg_rightcss = 'margin-left:-20px !important;';
                                    }
                                    if(smileycount == 1 && smileymsg == '') {
                                        imagemsg_rightcss = 'margin-left:-20px !important;';
                                        message = message.replace('height="20"', 'height="64px"');
                                        message = message.replace('width="20"', 'width="64px"');
                                        if(incomingself){
                                            imagemsg_rightcss = '';
                                        }
                                    }else if(smileycount > 1 && smileymsg == ''){
                                        imagemsg_rightcss = 'margin-left:-20px !important;margin-top:20px !important;';
                                        deleteiconcssself = 'margin-top:-5px;';
                                        if(rtl == 1){
                                            deleteiconcss = 'margin-right:46px;';
                                        }else{
                                            deleteiconcss = 'margin-left:46px;';
                                        }
                                        if(incomingself){
                                            imagemsg_rightcss = '';
                                            deleteiconcss = '';
                                        }
                                        if(smileycount > 0 && smileymsg != ''){
                                            imagemsg_rightcss = '';
                                        }
                                    }else if(message.indexOf('<img')!=-1 && message.indexOf('src')!=-1){
                                        imagemsg_rightcss = 'margin-left:-15px !important;';
                                        if(incomingself)
                                            imagemsg_rightcss = '';
                                    }
                                    if(smileymsg != '' && smileycount > 0){
                                        imagemsg_rightcss = '';
                                    }
                                }
                                if($("#cometchat_groupmessage_"+incoming.id).length > 0) {
                                    $("#cometchat_groupmessage_"+incoming.id).find("span.cometchat_chatboxmessagecontent").html(message);
                                } else {
                                    var ts = parseInt(incoming.sent)*1000;
                                    var add_bg_self = ' cometchat_self';
                                    var add_bg = ' cometchat_chatboxmessage';
                                    if((message.indexOf('<img')!=-1 && message.indexOf('src')!=-1 && message.indexOf('cometchat_smiley') == -1) || (smileycount > 0 && smileymsg == '')){
                                        if( incomingself ) {
                                            add_bg_self = 'cometchat_chatboxselfmedia';
                                            add_bg = '';
                                        }else {
                                            add_bg = 'cometchat_chatboxmedia';
                                            add_bg_self = '';
                                        }
                                    }
                                    if(!incomingself) {
                                        var avatar = jqcc.cometchat.getThemeArray('buddylistAvatar', incoming.fromid);
                                        if(typeof avatar=="undefined"){
                                            avatarstofetch[incoming.fromid]=1;
                                            avatar = staticCDNUrl+'images/noavatar.png';
                                        }
                                        var fromavatar = '<a id="cometchat_usersavatar_'+incoming.id+'" href="javascript:void(0)" onclick="javascript:parent.jqcc.cometchat.chatWith(\''+incoming.fromid+'\');" class="cometchat_float_l"><span class="cometchat_cr_other_avatar"><img class="cometchat_userscontentavatarsmall cometchat_avatar_'+incoming.fromid+'" title="'+fromname+'" src="'+avatar+'"></span></a>';
                                        var usernamecontent = '';
                                        if (mobileDevice || showUsername == '1') {
                                            usernamecontent = '<span class="cometchat_groupusername">'+fromname+':</span>';
                                        }

                                        if(incoming.hasOwnProperty('botid') && incoming.botid != 0) {
                                            fromavatar = '<a id="cometchat_usersavatar_'+incoming.id+'" href="javascript:void(0)" onclick="javascript:parent.jqcc.cometchat.chatWith(\''+incoming.fromid+'\');" class="cometchat_float_l"><span class="cometchat_cr_other_avatar"><img class="cometchat_userscontentavatarsmall" title="'+jqcc.cometchat.getThemeArray('botlistName', incoming.botid)+'" src="'+jqcc.cometchat.getThemeArray('botlistAvatar', incoming.botid)+'"></span></a>';
                                            if (mobileDevice || showUsername == '1') {
                                                usernamecontent = '<span class="cometchat_groupusername">'+jqcc.cometchat.getThemeArray('botlistName', incoming.botid)+':</span>';
                                            }
                                        }

                                        temp += ('<div class="cometchat_time cometchat_time_'+msg_date_class+' '+date_class+'" msg_format="'+msg_date_format+'">'+msg_date+'</div><div style="clear:both;"></div><div class="cometchat_messagebox" id="cometchat_messagebox_'+incoming.id+'">'+fromavatar+'<div class="'+add_bg+'" id="cometchat_groupmessage_'+incoming.id+'">');

                                        temp += (usernamecontent+'<span class="cometchat_chatboxmessagecontent" style="'+imagemsg_rightcss+'">'+message+'</span></div>'+$[calleeAPI].getTimeDisplay(ts,incoming.from,0)+'</div><div style="clear:both;"></div>');
                                        beepNewMessages++;
                                        deletemessagecss = 'style="'+deleteiconcss+'"';
                                    } else {
                                        deletemessagecss = 'style = "'+deleteiconcssself+'"';
                                        marginclass = ' cometchat_margin_left ';
                                        if(message.indexOf('imagemessage')!=-1) {
                                            marginclass = marginclass+'cometchat_margin_top';
                                        }
                                        temp += ('<div class="cometchat_time cometchat_time_'+msg_date_class+' '+date_class+'"  msg_format="'+msg_date_format+'">'+msg_date+'</div><div style="clear:both;"></div><div class="cometchat_messagebox cometchat_messagebox_self" id="cometchat_messagebox_'+incoming.id+'"><div class="'+add_bg+add_bg_self+'" id="cometchat_groupmessage_'+incoming.id+'"><span class="cometchat_chatboxmessagecontent" style="'+imagemsg_rightcss+'">'+message+'</span></div>'+$[calleeAPI].getTimeDisplay(ts,incoming.from,1)+'</div><div style="clear:both;"></div>');
                                    }
                                }

                                $('#currentroom_convotext').append(temp);
                                if(jqcc.cometchat.getChatroomVars('owner') || jqcc.cometchat.checkModerator(incoming.chatroomid) || (incomingself && jqcc.cometchat.getChatroomVars('allowDelete') == 1)) {
                                    if($("#cometchat_messagebox_"+incoming.id).find(".delete_msg").length < 1) {
                                        jqcc('#cometchat_messagebox_'+incoming.id).append('<span class="delete_msg '+marginclass+'" '+deletemessagecss  +' onclick="javascript:jqcc.cometchat.confirmDelete(\''+incoming.id+'\');"><img class="hoverbraces" src="'+staticCDNUrl+'modules/chatrooms/bin.svg"></span>');
                                    }
                                    $(".cometchat_messagebox").live("mouseover",function() {
                                        $(this).find(".delete_msg").css('display','block');
                                    });
                                    $(".cometchat_messagebox").live("mouseout",function() {
                                        $(this).find(".delete_msg").css('display','none');
                                    });
                                }
                                var forced = (incomingself) ? 1 : 0;
                                if((message).indexOf('<img')!=-1 && (message).indexOf('src')!=-1){
                                    $( "#cometchat_groupmessage_"+incoming.id+" img" ).load(function() {
                                         $[calleeAPI].chatroomScrollDown(forced);
                                    });
                                }else{
                                    $[calleeAPI].chatroomScrollDown(forced);
                                }

                                if(jqcc.cometchat.getSettings().disableRecentTab == 0) {
                                    var temp_msg = jqcc.cometchat.processRecentmessages(message);
                                    var params = {'chatid':incoming.chatroomid,'isgroup':1,'timestamp':incoming.sent,'m':temp_msg,'msgid':incoming.id,'force':0,'del':0};
                                    jqcc.cometchat.updateRecentChats(params);
                                }
                            }

                            if (message != '' && incoming.chatroomid != jqcc.cometchat.getChatroomVars('currentroom') && (typeof(receivedcrunreadmessages[incoming.chatroomid])=='undefined' || receivedcrunreadmessages[incoming.chatroomid] < incoming.id)){
                                if(!incomingself){
                                    if(!crUnreadMessages.hasOwnProperty(incoming.chatroomid)){
                                        crUnreadMessages[incoming.chatroomid] = 1;
                                    } else {
                                        var newUnreadMessages = parseInt(crUnreadMessages[incoming.chatroomid]) + 1;
                                        crUnreadMessages[incoming.chatroomid] = newUnreadMessages;
                                    }
                                }
                                $[calleeAPI].updateCRReceivedUnreadMessages(incoming.chatroomid,incoming.id);
                                if(typeof(jqcc[jqcc.cometchat.getChatroomVars('calleeAPI')].addMessageCounter) == "function"  && incoming['message'].indexOf('CC^CONTROL_') == -1 && !incomingself) {
                                    if($.cookie(settings.cookiePrefix+"sound") && $.cookie(settings.cookiePrefix+"sound") == 'true'){
                                        jqcc[calleeAPI].playsound(0);
                                    }
                                    jqcc[jqcc.cometchat.getChatroomVars('calleeAPI')].addMessageCounter(incoming.chatroomid,1,1);
                                    jqcc.cometchat.updateChatBoxState({id:incoming.chatroomid,g:1,c:1});
                                }
                            }
                        });
                        if(!$.isEmptyObject(avatarstofetch)){
                            jqcc.cometchat.getUserDetails(Object.keys(avatarstofetch),'updateView');
                        }
                        jqcc.cometchat.setChatroomVars('crUnreadMessages',crUnreadMessages);
                        receivedcrunreadmessages = jqcc.cometchat.getFromStorage('crreceivedunreadmessages');
                        $.each(crUnreadMessages, function(chatroomid,unreadMessageCount) {
                            var chatroomreadmessagesId = chatroomreadmessages[chatroomid];
                            var receivedcrunreadmessagesId = receivedcrunreadmessages[chatroomid];
                            if(receivedcrunreadmessagesId != 'undefined'){
                                if(receivedcrunreadmessagesId > chatroomreadmessagesId || typeof(chatroomreadmessagesId) == 'undefined'){
                                    $[calleeAPI].chatroomUnreadMessages(jqcc.cometchat.getChatroomVars('crUnreadMessages'),chatroomid);
                                }
                            }
                        });

                        jqcc.cometchat.setChatroomVars('heartbeatCount',1);
                        jqcc.cometchat.setChatroomVars('heartbeatTime',settings.minHeartbeat);

                        if(($("#currentroom_convo")[0].scrollHeight) - ($("#currentroom_convo").scrollTop() + $("#currentroom_convo").innerHeight()) > 80) {
                            $('.talkindicator').fadeIn();
                        }
                        $[calleeAPI].updateCRReadMessages(jqcc.cometchat.getChatroomVars('currentroom'));
                        var crreadmessages = jqcc.cometchat.getFromStorage("crreadmessages");
                        jqcc.cometchat.setChatroomVars('crreadmessages',crreadmessages);

                        jqcc.crembedded.groupbyDate();
                        $("#currentroom_convo").find('.cometchat_prependCrMessages').remove();
                        prepend = '<div class=\"cometchat_prependCrMessages\" onclick\="jqcc.crembedded.prependCrMessagesInit('+jqcc.cometchat.getChatroomVars('currentroom')+')\" id = \"cometchat_prependCrMessages_'+jqcc.cometchat.getChatroomVars('currentroom')+'\"><?php echo $chatrooms_language[74];?></div>';

                        if($("#currentroom").find(".cometchat_prependCrMessages").length != 1){
                            $("#currentroom_convo").prepend(prepend);
                        }
                    },
                    silentRoom: function(id, name, silent) {
                        if(jqcc.cometchat.getCcvariable().callbackfn=='desktop'  || mobileDevice){
                            loadCCPopup(settings.baseUrl+'modules/chatrooms/chatrooms.php?id='+id+'&basedata='+settings.basedata+'&name='+name+'&silent='+silent+'&action=passwordBox','passwordBox','status=0,toolbar=0,menubar=0,directories=0,resizable=0,location=0,status=0,scrollbars=1, width=320,height=130',320, 110, name, null, null,null,null,1);
                        }else if(settings.lightboxWindows == 1) {
                            loadCCPopup(settings.baseUrl+'modules/chatrooms/chatrooms.php?id='+id+'&basedata='+settings.basedata+'&name='+name+'&silent='+silent+'&action=passwordBox','passwordBox','status=0,toolbar=0,menubar=0,directories=0,resizable=0,location=0,status=0,scrollbars=1, width=320,height=130',320, 110, name, null, null,null,null,0);
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
                        /*jqcc.cometchat.updateChatBoxState({id:id,g:1,s:1});*/
                    },
                    renameChatroom: function(event,id){
                        event.stopPropagation();
                        jqcc('.cancel_edit').click();
                        var chatroomDivHeight = jqcc('#cometchat_chatroomlist_'+id).outerHeight();
                        jqcc('#cometchat_chatroomlist_'+id).append('<div class="cometchat_chatroom_overlay"><input class="chatroomName" type="textbox" value="0" style="display:none;" /><a title="<?php echo $chatrooms_language[51];?>" class="cancel_edit" href="javascript:void(0);" onclick="javascript:jqcc.'+jqcc.cometchat.getChatroomVars('calleeAPI')+'.canceledit(event,\''+id+'\');" style="display:none;"><?php echo $chatrooms_language[51];?></a></div>');

                        jqcc('.cometchat_chatroom_overlay').css('height',chatroomDivHeight);
                        var currentroomname = jqcc('#cometchat_chatroomlist_'+id).find('.currentroomname').html();
                        jqcc('#cometchat_chatroomlist_'+id).find('.currentroomname').css('visibility','hidden');
                        jqcc('#cometchat_chatroomlist_'+id).find('.chatroomName').show();
                        jqcc('#cometchat_chatroomlist_'+id).find('.cancel_edit').show();
                        jqcc('#cometchat_chatroomlist_'+id).find('.chatroomName').val(currentroomname);
                        jqcc('#cometchat_chatroomlist_'+id).find('.lobby_room_3').hide();
                        jqcc('#cometchat_chatroomlist_'+id).find('.lobby_room_4').hide();
                        jqcc('#cometchat_chatroomlist_'+id).find('.lobby_room_6').hide();

                        jqcc('.chatroomName').on('click', function(e) {
                            e.stopPropagation();
                        });
                        jqcc('.cometchat_chatroom_overlay').on('click', function(e) {
                            e.stopPropagation();
                            var cname = jqcc('#cometchat_chatroomlist_'+id).find('.chatroomName').val();
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
                        var basedata = settings.basedata;
                        name = name.trim();
                        name = decodeURI(name);
                        if(currentroomname != name) {
                            name = encodeURI(name);
                            jqcc.ajax({
                                url: baseUrl+"modules/chatrooms/chatrooms.php?action=renamechatroom",
                                data: {id: id, basedata: basedata, cname: name},
                                type: 'post',
                                cache: false,
                                timeout: 10000,
                                async: false,
                                success: function(data) {
                                    if (data == 0) {
                                        alert("<?php echo $chatrooms_language['roomname_not_available'];?>");
                                    }else{
                                        name = decodeURI(name);
                                        jqcc.cometchat.chatroomHeartbeat(1);
                                        jqcc('#cometchat_chatroomlist_'+id).find('.cancel_edit').hide();
                                        jqcc('#cometchat_chatroomlist_'+id).find('.currentroomname').css('visibility','visible');
                                        if(currentroomname == jqcc('#currentroom').find('.cometchat_user_shortname').text()){
                                            jqcc('#currentroom').find('.cometchat_user_shortname').text(name);
                                        }
                                        jqcc('#cometchat_chatroomlist_'+id).find('.cancel_edit').click();
                                    }
                                }
                            });
                        } else {
                            jqcc('#cometchat_chatroomlist_'+id).find('.cancel_edit').click();
                        }
                    },
                    canceledit: function(event,id) {
                        event.stopPropagation();
                        jqcc('#cometchat_chatroomlist_'+id).find('.cometchat_chatroom_overlay').remove();
                        jqcc('#cometchat_chatroomlist_'+id).find('.currentroomname').css('visibility','visible');
                        jqcc('#cometchat_chatroomlist_'+id).find('.chatroomName').hide();
                        jqcc('#cometchat_chatroomlist_'+id).find('.cancel_edit').hide();
                        jqcc('#cometchat_chatroomlist_'+id).find('.lobby_room_3').show();
                        jqcc('#cometchat_chatroomlist_'+id).find('.lobby_room_4').show();
                        jqcc('#cometchat_chatroomlist_'+id).find('.lobby_room_6').show();
                    },
                    updateChatroomsTabtext: function(){
                        $('#cometchat_chatroomstab_text').text("<?php echo $chatrooms_language['title'];?>");
                    },
                    minimizeChatrooms: function(){
                        jqcc.cometchat.setChatroomVars('newMessages',0);
                    },
                    updateChatroomUsers: function(item,fetchedUsers) {
                        var temp = '';
                        var temp1 = '';
                        var moderatorhtml = '';
                        var userhtml = '';
                        var newUsers = {};
                        var newUsersName = {};
                        fetchedUsers = 1;
                        $.each(item, function(i,user) {
                            longname = user.n;
                            roomid = user.chatroomid;
                            if(settings.users[user.id] != 1 && settings.initializeRoom == 0) {
                                var nowTime = new Date();
                                var ts = Math.floor(nowTime.getTime()/1000);
                                $("#currentroom_convotext").append('<div style="clear:both;"></div><div class="cometchat_chatboxalert" id="cometchat_message_0">'+user.n+'<?php echo $chatrooms_language[14]?>'+$[calleeAPI].getTimeDisplay(ts,user.id)+'</div>');
                                $[calleeAPI].chatroomScrollDown();
                            }
                            if(parseInt(user.b)!=1) {
                                var userstatus = 'offline';
                                userstatus = user.s;
                                if(typeof(userstatus) === 'undefined'){
                                    userstatus = 'offline';
                                }
                                var avatar = '';
                                if(user.a != '') {
                                    avatar = '<span class="cometchat_userscontentavatar"><img class="cometchat_userscontentavatarimage" src="'+user.a+'"></span>';
                                }
                                newUsers[user.id] = 1;
                                newUsersName[user.id] = user.n;
                                userhtml='<div class="cometchat_labels" style="padding:9px;"><?php echo $chatrooms_language[61]?></div>';
                                moderatorhtml='<div class="cometchat_labels" style="padding:9px;"><?php echo $chatrooms_language[62]?></div>';
                               if($.inArray(user.id ,jqcc.cometchat.getChatroomVars('moderators')) != -1 ) {
                                if(user.id == settings.myid) {
                                    temp1 += '<div id="chatroom_userlist_'+user.id+'" class="cometchat_cruserlist cometchat_chatroomuserlist" style="cursor:default !important;overflow:hidden !important;" userid="'+user.id+'"><div class="cometchat_userscontentavatar"><img class="cometchat_userscontentavatarimage" src="'+user.a+'"></div><div><div class="cometchat_userscontentname" >'+longname+'</div></div></br><div class="cometchat_usersstatus"><div class="cometchat_userscontentdot cometchat_user_'+userstatus+'" style="margin-top:5px;"></div><div class="cometchat_buddylist_status">'+userstatus+'</div></div></div>';
                                } else {
                                    temp1 += '<div id="chatroom_userlist_'+user.id+'" class="cometchat_cruserlist cometchat_chatroomuserlist" onmouseover="jqcc(this).addClass(\'cometchat_userlist_hover\');" onmouseout="jqcc(this).removeClass(\'cometchat_userlist_hover\');"  userid="'+user.id+'" owner="'+settings.owner+'" username="'+user.n+'" style="overflow:hidden !important;"><div class="cometchat_userscontentavatar"><img class="cometchat_userscontentavatarimage" src="'+user.a+'"></div><div><div class="cometchat_userscontentname" >'+longname+'</div></div><br/><div class="cometchat_usersstatus"><div class="cometchat_userscontentdot cometchat_user_'+userstatus+'" style="margin-top:5px;"></div><div class="cometchat_buddylist_status">'+userstatus+'</div></div></div>';
                                }
                            } else {
                                if(user.id == settings.myid) {
                                    temp += '<div id="chatroom_userlist_'+user.id+'" class="cometchat_cruserlist cometchat_chatroomuserlist" style="cursor:default !important;overflow:hidden !important;" userid="'+user.id+'"><div class="cometchat_userscontentavatar"><img class="cometchat_userscontentavatarimage" src="'+user.a+'"></div><div><div class="cometchat_userscontentname">'+longname+'</div></div></br><div class="cometchat_usersstatus"><div class="cometchat_userscontentdot cometchat_user_'+userstatus+'" style="margin-top:5px;"></div><div class="cometchat_buddylist_status">'+userstatus+'</div></div></div>';
                                } else {
                                    var moderatoroptions = '';
                                    if($.inArray(settings.myid ,jqcc.cometchat.getChatroomVars('moderators')) != -1 || jqcc.cometchat.getChatroomVars('owner')) {
                                        moderatoroptions = '<div id="cometchat_moderatoroptions_'+user.id+'" class="cometchat_moderatoroptions"><input type=button id="cc_kick" value="<?php echo $chatrooms_language[40]?>" uid="'+user.id+'" chatroomid="'+roomid+'" class="moderatorbutton kickBan" /><input type=button id="cc_ban" value="<?php echo $chatrooms_language[41]?>" uid = "'+user.id+'" class="moderatorbutton kickBan" chatroomid="'+roomid+'" /><input type=button id="cc_chat" value="Chat" uid = "'+user.id+'" class="moderatorbutton chatwith" /></div>';
                                    }

                                    temp += '<div id="chatroom_userlist_'+user.id+'" class="cometchat_cruserlist cometchat_chatroomuserlist" onmouseover="jqcc(this).addClass(\'cometchat_userlist_hover\');" onmouseout="jqcc(this).removeClass(\'cometchat_userlist_hover\');" userid="'+user.id+'" owner="'+settings.owner+'" username="'+user.n+'" style="overflow:hidden !important;"><div class="cometchat_cruserlistcontent"><div class="cometchat_userscontentavatar"><img class="cometchat_userscontentavatarimage" src="'+user.a+'"></div><div><div class="cometchat_userscontentname">'+longname+'</div></div></br><div class="cometchat_usersstatus"><div class="cometchat_userscontentdot cometchat_user_'+userstatus+'" style="margin-top:5px;"></div><div class="cometchat_buddylist_status">'+userstatus+'</div></div></div>'+moderatoroptions+'</div>';
                                }
                            }
                            }
                        });
                        for (user in settings.users) {
                            if(settings.users.hasOwnProperty(user)) {
                                if(newUsers[user] != 1 && settings.initializeRoom == 0) {
                                    var nowTime = new Date();
                                    var ts = Math.floor(nowTime.getTime()/1000);
                                    $("#currentroom_convotext").append('<div class="cometchat_chatboxalert" id="cometchat_message_0">'+settings.usersName[user]+'<?php echo $chatrooms_language[13]?>'+$[calleeAPI].getTimeDisplay(ts,user.id)+'</div>');
                                    $[calleeAPI].chatroomScrollDown();
                                }
                            }
                        }
                        if(temp1 != "" && temp !=""){
                            jqcc('#chatroomuser_container_hidden').html(moderatorhtml+temp1+userhtml+temp);
                        } else if(temp == ""){
                            jqcc('#chatroomuser_container_hidden').html(moderatorhtml+temp1);
                        } else{
                            jqcc('#chatroomuser_container_hidden').html(userhtml+temp);
                        }

                        var cometchat_group_popup = $("#currentroom");

                        cometchat_group_popup.find('.moderatorbutton').on('click',function(e){
                            e.stopImmediatePropagation();
                            var uid = $(this).attr('uid');
                            var chatroomid = $(this).attr('chatroomid');
                            var method = $(this).attr('id');

                            if(method == 'cc_kick'){
                                jqcc.cometchat.kickChatroomUser(uid,1,chatroomid);
                            } else if(method == 'cc_ban') {
                                jqcc.cometchat.banChatroomUser(uid,1,chatroomid);
                            } else {
                                jqcc.cometchat.chatWith(uid);
                                jqcc[calleeAPI].windowResize();
                            }
                        });
                        jqcc.cometchat.setChatroomVars('users',newUsers);
                        jqcc.cometchat.setChatroomVars('usersName',newUsersName);
                        jqcc.cometchat.setChatroomVars('initializeRoom',0);
                    },
                    loadCCPopup: function(url,name,properties,width,height,title,force,allowmaximize,allowresize,allowpopout){
                        if(jqcc.cometchat.getChatroomVars('lightboxWindows') == 1) {
                            var controlparameters = {"type":"modules", "name":"chatrooms", "method":"loadCCPopup", "params":{"url":url, "name":name, "properties":properties, "width":width, "height":height, "title":title, "force":force, "allowmaximize":allowmaximize, "allowresize":allowresize, "allowpopout":allowpopout}};
                            controlparameters = JSON.stringify(controlparameters);
                            parent.postMessage('CC^CONTROL_'+controlparameters,'*');
                        } else {
                            var w = window.open(url,name,properties);
                            w.focus();
                        }
                    },
                    prependCrMessagesInit: function(id){
                        var messages = jqcc('#currentroom_convotext').find('.cometchat_chatboxmessage');
                        jqcc('.cometchat_prependCrMessages').attr('onclick','');
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
                            prepend = '',
                            avatarstofetch = {},
                            messagewrapperid = '';

                        $.each(data, function(i, incoming){
                            if(incoming.fromid == settings.myid){
                                incoming.from = "<?php echo $chatrooms_language['me'];?>";
                            }
                            if( (!incoming.hasOwnProperty('id') || incoming.id == '') && (!incoming.hasOwnProperty('localmessageid') || incoming.localmessageid == '') && (incoming.message).indexOf('CC^CONTROL_')==-1){
                                return;
                            }

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

                            lastMessageId = messagewrapperid;
                            var deleteMessage = '';
                            var message = jqcc.cometchat.processcontrolmessage(incoming);
                            var msg_time = incoming.sent;
                            msg_time = msg_time+'';

                            if (msg_time.length == 10){
                                msg_time = parseInt(msg_time * 1000);
                            }

                            var months_set = new Array();

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
                                var fromname = incoming.from;
                                if((incoming.message).indexOf('has shared a file')!=-1){
                                    if(jqcc.cometchat.getCcvariable().callbackfn=="desktop"){
                                        if(incoming.message.indexOf('target')>=-1){
                                            incoming.message=incoming.message.replace(/target="_blank"/g,'');
                                        }
                                    }
                                }
                                if((incoming.message).indexOf('has shared a handwritten message')!=-1){
                                    if(jqcc.cometchat.getCcvariable().callbackfn=="desktop"){
                                        /*if(incoming.message.indexOf('href')>=-1){
                                            var start = (incoming.message).indexOf('href');
                                            var end = (incoming.message).indexOf('target');
                                            var HtmlString=(incoming.message).slice(start,end);
                                            incoming.message=(incoming.message).replace(HtmlString,'');
                                        }*/
                                    }
                                }
                                var smileycount = (message.match(/cometchat_smiley/g) || []).length;
                                var smileymsg = message.replace(/<img[^>]*>/g,"");
                                var imagemsg_rightcss = '';
                                var deleteiconcss = '';
                                var deleteiconcssself = '';
                                var incomingself = 1;
                                smileymsg = smileymsg.trim();
                                 if(incoming.hasOwnProperty('self')){
                                    incomingself = incoming.self;
                                }
                                if(incoming.fromid != settings.myid || (incoming.hasOwnProperty('botid') && incoming.botid != 0)) {
                                    incomingself = 0;
                                }
                                if(message.indexOf('<img')!=-1){
                                    if(!incomingself){
                                        imagemsg_rightcss = 'margin-left:-20px !important;';
                                    }
                                    if(smileycount == 1 && smileymsg == '') {
                                        imagemsg_rightcss = 'margin-left:-20px !important;';
                                        message = message.replace('height="20"', 'height="64px"');
                                        message = message.replace('width="20"', 'width="64px"');
                                        if(incomingself){
                                            imagemsg_rightcss = '';
                                        }
                                    }else if(smileycount > 1 && smileymsg == ''){
                                        imagemsg_rightcss = 'margin-left:-20px !important;margin-top:20px !important;';
                                        deleteiconcssself = 'margin-top:-5px;';
                                        if(rtl == 1){
                                            deleteiconcss = 'margin-right:46px;';
                                        }else{
                                            deleteiconcss = 'margin-left:46px;';
                                        }
                                        if(incomingself){
                                            imagemsg_rightcss = '';
                                            deleteiconcss = '';
                                        }
                                    }else if(message.indexOf('<img')!=-1 && message.indexOf('src')!=-1){
                                        imagemsg_rightcss = 'margin-left:-15px !important;';
                                        if(incomingself){
                                            imagemsg_rightcss = '';
                                        }
                                    }
                                    if(smileymsg != '' && smileycount > 0){
                                        imagemsg_rightcss = '';
                                    }
                                }
                                var ts = parseInt(incoming.sent)*1000;
                                var add_bg_self = ' cometchat_self';
                                var add_bg = ' cometchat_chatboxmessage';
                                var deletemessagecss = '';
                                if((message.indexOf('<img')!=-1 && message.indexOf('src')!=-1 && message.indexOf('cometchat_smiley') == -1) || (smileycount > 0 && smileymsg == '')){
                                    if( incomingself ) {
                                        add_bg_self = 'cometchat_chatboxselfmedia';
                                        add_bg = '';
                                    }else {
                                        add_bg = 'cometchat_chatboxmedia';
                                        add_bg_self = '';
                                    }
                                }

                                if (!incomingself) {
                                    if (jqcc.cometchat.getChatroomVars('owner') || jqcc.cometchat.checkModerator(incoming.roomid) || (incomingself && jqcc.cometchat.getChatroomVars('allowDelete') == 1)) {
                                        deleteMessage = '<span class="delete_msg" onclick="javascript:jqcc.cometchat.confirmDelete(\''+messagewrapperid+'\');"><img class="hoverbraces" src="'+staticCDNUrl+'modules/chatrooms/bin.svg"></span>';
                                    }
                                    var avatar = jqcc.cometchat.getThemeArray('buddylistAvatar', incoming.fromid);
                                    if(typeof avatar=="undefined"){
                                        avatarstofetch[incoming.fromid]=1;
                                        avatar = staticCDNUrl+'images/noavatar.png';
                                    }
                                    var fromavatar = '<a id="cometchat_usersavatar_'+messagewrapperid+'" href="javascript:void(0)" class="cometchat_float_l"><span class="cometchat_cr_other_avatar"><img class="cometchat_userscontentavatarsmall cometchat_avatar_'+incoming.fromid+'" title="'+fromname+'" src="'+avatar+'"></span></a>';
                                    var usernamecontent = '';
                                    if (mobileDevice || showUsername == '1') {
                                        usernamecontent = '<span class="cometchat_groupusername">'+fromname+':</span>';
                                    }
                                    if(incoming.hasOwnProperty('botid') && incoming.botid != 0) {
                                    fromavatar = '<a id="cometchat_usersavatar_'+messagewrapperid+'" href="javascript:void(0)" onclick="javascript:parent.jqcc.cometchat.chatWith(\''+incoming.fromid+'\');" class="cometchat_float_l"><span class="cometchat_cr_other_avatar"><img class="cometchat_userscontentavatarsmall" title="'+jqcc.cometchat.getThemeArray('botlistName', incoming.botid)+'" src="'+jqcc.cometchat.getThemeArray('botlistAvatar', incoming.botid)+'"></span></a>';
                                    if (mobileDevice || showUsername == '1') {
                                        usernamecontent = '<span class="cometchat_groupusername">'+jqcc.cometchat.getThemeArray('botlistName', incoming.botid)+':</span>';
                                    }
                                }
                                    temp += ('<div class="cometchat_time cometchat_time_'+msg_date_class+' '+date_class+'" msg_format="'+msg_date_format+'">'+msg_date+'</div><div style="clear:both;"></div><div class="cometchat_messagebox" id="cometchat_messagebox_'+messagewrapperid+'">'+fromavatar+'<div class="'+add_bg+'" id="cometchat_groupmessage_'+messagewrapperid+'"><div class="cometchat_messagearrow"></div>');
                                    temp += (usernamecontent+'<span class="cometchat_chatboxmessagecontent" style="'+imagemsg_rightcss+'">'+message+'</span></div>'+$[calleeAPI].getTimeDisplay(ts,incoming.from,0)+''+deleteMessage+'</div><div style="clear:both;"></div>');
                                } else {
                                    if (jqcc.cometchat.getChatroomVars('owner') || jqcc.cometchat.checkModerator(incoming.roomid) || (incomingself && jqcc.cometchat.getChatroomVars('allowDelete') == 1)) {
                                        deleteMessage = '<span class="delete_msg cometchat_margin_left" style="'+deleteiconcssself+deleteiconcss+'" onclick="javascript:jqcc.cometchat.confirmDelete(\''+messagewrapperid+'\');"><img class="hoverbraces" src="'+staticCDNUrl+'modules/chatrooms/bin.svg"></span>';
                                    }
                                    temp += ('<div class="cometchat_time cometchat_time_'+msg_date_class+' '+date_class+'" msg_format="'+msg_date_format+'">'+msg_date+'</div><div style="clear:both;"></div><div class="cometchat_messagebox cometchat_messagebox_self" id="cometchat_messagebox_'+messagewrapperid+'"><div class="'+add_bg+add_bg_self+'" id="cometchat_groupmessage_'+messagewrapperid+'"><span class="cometchat_chatboxmessagecontent" style="'+imagemsg_rightcss+'">'+message+'</span></div>'+$[calleeAPI].getTimeDisplay(ts,incoming.from,1)+''+deleteMessage+'</div><div style="clear:both;"></div>');
                                }
                            }
                        });
                        if(!$.isEmptyObject(avatarstofetch)){
                            jqcc.cometchat.getUserDetails(Object.keys(avatarstofetch),'updateView');
                        }
                        var current_top_element  = jqcc('#currentroom_convotext .cometchat_chatboxmessage:first');
                        jqcc('#currentroom_convotext').prepend(temp);

                        $(".cometchat_messagebox").live("mouseover",function() {
                            $(this).find(".delete_msg").css('display','block');
                        });
                        $(".cometchat_messagebox").live("mouseout",function() {
                            $(this).find(".delete_msg").css('display','none');
                        });


                        jqcc.crembedded.groupbyDate();

                        $('#currentroom').find('.cometchat_prependCrMessages').remove();

                        prepend = '<div class=\"cometchat_prependCrMessages\" onclick\="jqcc.crembedded.prependCrMessagesInit('+id+')\" id = \"cometchat_prependCrMessages_'+id+'\"><?php echo $chatrooms_language[74];?></div>';

                        if($('#currentroom .cometchat_prependMessages').length != 1){
                            $('#currentroom_convo').prepend(prepend);
                        }

                        if((count - parseInt(settings.prependLimit) < 0)){
                            $('#currentroom .cometchat_prependCrMessages').text("<?php echo $chatrooms_language['no_more_msgs'];?>");
                            $('#currentroom .cometchat_prependCrMessages').attr('onclick','');
                            $('#currentroom .cometchat_prependCrMessages').css('cursor','default');
                        }else{
                            $('#currentroom .cometchat_prependCrMessages').attr('onclick','jqcc.embedded.prependCrMessagesInit('+id+')');
                        }

                        if(jqcc().slimScroll && mobileDevice == null){
                            if(current_top_element.length>0){
                                var offsetheight = 0;
                                var offsetheight = current_top_element.offset().top - jqcc('#currentroom_convotext .cometchat_chatboxmessage:first').offset().top+jqcc('.cometchat_time').height()+jqcc('#cometchat_prependMessages_'+id).height();
                                var height = offsetheight-jqcc('#cometchat_tabcontenttext_'+id).height();
                                $('#currentroom_convo').slimScroll({scrollTo: height+'px'});
                            }else{
                                $('#currentroom_convo').slimScroll({scroll: 1});
                            }
                        }
                    },
                    getActiveChatrooms: function(item){
                        /*var chatroomitem = {};
                        var cc_chatroom = jqcc.cometchat.getSessionVariable('chatboxstates');
                        var chatroomData = cc_chatroom.active;
                        var Ids = new Array();
                        var temp = '';
                        var onlineNumber = 0;

                        for(chatroomId in chatroomData){
                            Ids.push(chatroomId);
                        }
                        for(var key in item) {
                            if(Ids.indexOf(key) >= 0){
                                chatroomitem[key] = item[key];
                            }
                        }
                        return chatroomitem;*/
                        return;
                    },
                    activeChatrooms: function(item){
                        var chatroomitem = $[calleeAPI].getActiveChatrooms(item);
                        var temp = '';
                        var userCountCss = "style='display:none'";
                        if(settings.showChatroomUsers == 1){
                            userCountCss = '';
                        }

                        $.each(chatroomitem, function(i,room) {
                            longname = room.name;
                            shortname = room.name;

                            if (room.status == 'available') {
                                onlineNumber++;
                            }
                            var selected = '';
                            if (jqcc.cometchat.getChatroomVars('currentroom') == room.id) {
                                selected = ' cometchat_chatroomselected';
                                $('#currentroom').find('.cometchat_chatroomusercount').html("<?php echo  $chatrooms_language['participants']; ?>:&nbsp;"+room.online);
                            }
                            var roomtype = '';
                            var deleteroom = '';
                            var renameChatroom = '';

                            if (room.type == 1) {
                                roomtype = '<span class="lobby_room_3"><img src="'+staticCDNUrl+'layouts/'+calleeAPI+'/images/lock.png" /></span>';
                            }

                            if(room.owner == true){
                                deleteroom = '<span class="lobby_room_4" title="<?php echo $chatrooms_language[58];?>" onclick="javascript:jqcc.cometchat.deleteChatroom(event,\''+room.id+'\');"><img src="'+staticCDNUrl+'layouts/'+calleeAPI+'/images/remove.png" /></span>';
                                renameChatroom = '<span class="lobby_room_6" title="<?php echo $chatrooms_language[80];?>" onclick="javascript:jqcc.'+[calleeAPI]+'.renameChatroom(event,\''+room.id+'\');"><img src="'+staticCDNUrl+'layouts/'+calleeAPI+'/images/pencil_buddylist.png" /></span>';
                            }
                            temp += '<div id="cometchat_chatroomlist_'+room.id+'" class="lobby_room'+selected+'" onmouseover="jqcc(this).addClass(\'cometchat_userlist_hover\');" onmouseout="jqcc(this).removeClass(\'cometchat_userlist_hover\');" onclick="javascript:jqcc.cometchat.chatroom(\''+room.id+'\',\''+cc_urlencode(shortname)+'\',\''+room.type+'\',\''+room.i+'\',\''+room.owner+'\',\'1\',\'1\');" style="display:block !important;">'+roomtype+''+deleteroom+'<span class="lobby_room_5" style="display:none;"></span>'+renameChatroom+'<div><span class="cometchat_chatroomimage"><img src="'+staticCDNUrl+'layouts/<?php echo $layout;?>/images/group.svg"></span><span class="lobby_room_1" title="'+longname+'"><span class="currentroomname">'+longname+'</span><br/><span class="lobby_room_2" '+userCountCss+' title="'+room.online+' <?php echo $chatrooms_language[34];?>"><?php echo  $chatrooms_language["participants"]; ?>:&nbsp;'+room.online+'</span></span></div></span></div>';

                        });
                        return temp;
                    },
                    chatroomUnreadMessages: function(crUnreadMessages,chatroomid){
                       return;
                    },
                    addMessageCounter: function(id, count, add){
                        if(id == 'undefined') {
                            return;
                        }
                        var chatboxstates = jqcc.cometchat.getInternalVariable('chatboxstates');
                        var key = '_'+id;
                        var cometchat_chatroommsgcount = $('#cometchat_recentchatroomlist_'+id).find('.cometchat_chatroommsgcounttext');
                        if(chatboxstates.hasOwnProperty(key)){
                            var state = chatboxstates[key].split(/\|/);
                            if(state[2] && add == 0) {
                                count = state[2];
                            }else if(add == 1) {
                                count = parseInt(cometchat_chatroommsgcount.attr('amount'))+parseInt(count);
                            }
                            if(id != jqcc.cometchat.getChatroomVars('currentroom') && count != 0){
                                cometchat_chatroommsgcount.attr('amount', count).html(count).parent().show();
                            }else {
                                cometchat_chatroommsgcount.attr('amount', 0).html(0).parent().hide();
                            }
                        }
                        var crUnreadMessages = jqcc.cometchat.getChatroomVars('crUnreadMessages');
                        crUnreadMessages[id] = count;
                        jqcc.cometchat.setChatroomVars('crUnreadMessages',crUnreadMessages);
                    },
                    groupbyDate: function(){
                        $('.cometchat_time').hide();
                        $.each($('.cometchat_time'),function (i,divele){
                            var classes = $(divele).attr('class').split(/\s+/);
                            for(var i in classes){
                                if(typeof classes[i] == 'string'){
                                    if(classes[i].indexOf('cometchat_time_') === 0){
                                    $('.'+classes[i]+':first').css('display','table');
                                    }
                                }
                            }
                        });
                    },
                    closeChatroom: function(id){
                        var currentroom = $('#currentroom');
                        var closeflag = 1;
                        var confirmmsg = false;
                        if($('.cometchat_container').hasClass('visible')){
                            confirmmsg = confirm("<?php echo $chatrooms_language['close_chatbox']; ?>");
                            if(confirmmsg)
                                jqcc('.cometchat_closebox').click();
                            else
                                closeflag = 0;
                        }
                        if(closeflag){
                            currentroom.hide();
                            var currentroomid = jqcc.cometchat.getChatroomVars('currentroom');
                            jqcc.cometchat.updateChatBoxState({id:currentroomid,g:1,s:0});
                            var chatBoxOrder = jqcc.cometchat.getThemeVariable('chatBoxOrder');
                            var nextChatBox = chatBoxOrder[chatBoxOrder.length-1];
                            jqcc.cometchat.setThemeVariable('openedChatbox',nextChatBox);
                            if(chatBoxOrder == '' && typeof(nextChatBox) == 'undefined' ){
                                jqcc.cometchat.setChatroomVars('currentroom',0);
                            }
                            if(nextChatBox) {
                                if(!isNaN(nextChatBox) && nextChatBox.charAt(0) != '_') {
                                    $("#cometchat_user_"+nextChatBox+"_popup").addClass('cometchat_tabopen');
                                    jqcc.cometchat.updateChatBoxState({id:nextChatBox,s:1});
                                    jqcc[calleeAPI].addPopup(nextChatBox,0,0);
                                    if(jqcc.cometchat.getThemeVariable('currentStatus')!='invisible'){
                                       if(typeof $("#cometchat_user_"+nextChatBox+"_popup .cometchat_message_container").find('.cometchat_chatboxmessage ').last().attr('id') != 'undefined'){
                                            var messageid = $("#cometchat_user_"+nextChatBox+"_popup .cometchat_message_container").find('.cometchat_chatboxmessage ').last().attr('id').split('_')[2];
                                        }
                                        var message = {"id": messageid, "from": nextChatBox, "self": 0};
                                        if(typeof jqcc.cometchat.getCcvariable().lastmessagereadstatus[nextChatBox] != "undefined" && jqcc.cometchat.getCcvariable().lastmessagereadstatus[nextChatBox]==0){
                                            jqcc.cometchat.sendReceipt(message, 'readMessageNotify');
                                        }
                                    }
                                }else {
                                    $('.cometchat_noactivity').css('display','none');
                                    nextChatBox = nextChatBox.replace('_','');
                                    jqcc.cometchat.getChatroomDetails({id:nextChatBox,loadroom:1});
                                }
                            }else{
                                $('.cometchat_noactivity').css('display','block');
                            }
                            jqcc[calleeAPI].windowResize();
                        }
                    },
                    chatScroll: function(id){
                        var baseUrl = settings.baseUrl;
                        if($('#scrolltop_'+id).length == 0){
                            $("#currentroom_convo").prepend('<div id="scrolltop_'+id+'" class="cometchat_scrollup"><img src="'+staticCDNUrl+'images/arrowtop.svg" class="cometchat_scrollimg" /></div>');
                        }
                        if($('#scrolldown_'+id).length == 0){
                            $("#currentroom_convo").append('<div id="scrolldown_'+id+'" class="cometchat_scrolldown"><img src="'+staticCDNUrl+'images/arrowbottom.svg" class="cometchat_scrollimg" /></div>');
                        }
                        $('#currentroom_convo').unbind('wheel');
                        $('#currentroom_convo').on('wheel',function(event){
                            var scrollTop = $(this).scrollTop();
                            if(event.originalEvent.deltaY != 0){
                                clearTimeout($.data(this, 'scrollTimer'));
                                if(event.originalEvent.deltaY > 0){
                                    $('#scrolltop_'+id).hide();
                                    var down = jqcc("#currentroom_convo")[0].scrollHeight-250-50;
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
                            $('#currentroom_convo').slimScroll({scroll: 0});
                        });

                        $('#scrolldown_'+id).click(function(){
                            $('#scrolldown_'+id).hide();
                            $('#currentroom_convo').slimScroll({scroll: 1});
                        });
                    }
                };

        })();
})(jqcc);

if(typeof(jqcc.lite) === "undefined"){
    jqcc.embedded=function(){};
}

jqcc.extend(jqcc.embedded, jqcc.crembedded);

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
    jqcc('.inviteChatroomUsers').live('click',function(){
        var baseurl = jqcc.cometchat.getBaseUrl();
        var basedata = jqcc.cometchat.getBaseData();
        var roomid = jqcc.cometchat.getChatroomVars('currentroom');
        var roompass = jqcc.cometchat.getChatroomVars('currentp');
        var roomname = cc_urlencode(jqcc.cometchat.getChatroomVars('currentroomname'));
        var popoutmode = jqcc.cometchat.getChatroomVars('popoutmode');
        var lang = "<?php echo $chatrooms_language['invite_users_title'];?>";
        var caller = 'cometchat_embedded_iframe';
        var url = baseurl+'modules/chatrooms/chatrooms.php?action=invite&caller='+caller+'&roomid='+roomid+'&inviteid='+roompass+'&basedata='+basedata+'&roomname='+roomname+'&popoutmode='+popoutmode;
        var mobileDevice = navigator.userAgent.match(/ipad|ipod|iphone|android|windows ce|Windows Phone|blackberry|palm|symbian/i);
        if(jqcc.cometchat.getCcvariable().callbackfn=='desktop' || mobileDevice){
            jqcc.cometchat.inviteChatroomUser(1);
        }else{
            if(typeof(parent) != 'undefined' && parent != null && parent != self){
                var controlparameters = {"type":"modules", "name":"cometchat", "method":"inviteChatroomUser", "params":{"url":url, "action":"invite", "lang":lang}};
                controlparameters = JSON.stringify(controlparameters);
                loadCCPopup(url, 'invite',"status=0,toolbar=0,menubar=0,directories=0,resizable=0,location=0,status=0,scrollbars=0,width=300,height=300",300,300,'Invite users',null,null,null,null,0);
            } else {
                var controlparameters = {};
                jqcc.cometchat.inviteChatroomUser();
            }
        }
    });

jqcc('.cometchat_chatroomusers').live('click',function(){
    if (jqcc(".cometchat_container").length>0) {
        if (!confirm(jqcc.cometchat.getLanguage('close_existing_popup'))) {
             return;
         }
    }
    var rtl = "<?php echo  $rtl; ?>";
    var groupid = jqcc.cometchat.getChatroomVars('currentroom');
    jqcc.cometchat.getGroupUsers(groupid);
    var width = 500;
    var borderstyle = '';
    if(rtl == 1){
        borderstyle = 'border-right:1px solid #d1d1d1;';
    }else{
        borderstyle = 'border-left:1px solid #d1d1d1;';
    }
    $('.cometchat_float_list').hide();
    $('.cometchat_pluginrightarrow').removeClass('rotated').css({'transform' :'rotate(0deg)'});
    if(!jqcc('#cometchat_container_chatroomusers').hasClass('visible')){
        var htmlcontent = $('#chatroomuser_container_hidden').html();
        var chatroomusers = '<div class="cometchat_container" id="cometchat_container_chatroomusers" ><div class="cometchat_container_title"><?php echo $chatrooms_language["group_users"];?><div id="cometchat_closebox_chatroomusers" class="cometchat_closebox cometchat_tooltip" ></div></div><div class="cometchat_container_body" style="height:100%;'+borderstyle+'"><div id="chatroomuser_container" style="height:inherit;"></div></div></div>';
        if(jqcc("#cometchat_righttab").find('#cometchat_container_chatroomusers').length < 1){
            jqcc("#cometchat_righttab").find('#cometchat_container_chatroomusers').remove();
        }
        if(jqcc('#cometchat_righttab').find('#cometchat_container_chatroomusers').length < 1){
            jqcc('#cometchat_righttab').append(chatroomusers);
        }
        jqcc("#cometchat_righttab").find('#chatroomuser_container').html(htmlcontent);

        if(jqcc.cometchat.getCcvariable().mobileDevice){
            jqcc("#cometchat_righttab").find('#chatroomuser_container').css({'overflow-y':'auto','height':jqcc(window).height() - jqcc('#cometchat_header').outerHeight(true) - jqcc('.cometchat_container_title').outerHeight(true)});
        }else if(jqcc().slimScroll){
            jqcc("#cometchat_righttab").find('#chatroomuser_container').slimScroll({scroll:'1',height:jqcc(window).height() - jqcc('#cometchat_header').outerHeight(true) - jqcc('.cometchat_container_title').outerHeight(true)});
        }

        var cometchat_container = $('#cometchat_container_chatroomusers');
        $('#cometchat_container_'+name).css('width',width);
        if($('.cometchat_windows').hasClass('visible')){
            $('.cometchat_container').each(function(){
                if($('#'+this.id).hasClass('cometchat_windows') && $('#'+this.id).hasClass('visible')){
                    $('#'+this.id).remove();
                }
            });
            if(rtl == 1){
                $('.cometchat_windows').animate({'right':'100%'},"fast").removeClass('visible');
            }else{
                $('.cometchat_windows').animate({'left':'100%'},"fast").removeClass('visible');
            }
        }
        if (cometchat_container.hasClass('visible')){
            cometchat_container.animate({"left":"100%"}, "fast").removeClass('visible');
        }else{
            var left = cometchat_container.offset().left;
            animatewidth = $(window).width()-cometchat_container.width();
            var reducesize = cometchat_container.width();
            if(!$('.cometchat_windows').hasClass('visible') && !$('.cometchat_container').hasClass('visible')){

                if($("#cometchat_righttab").width()-cometchat_container.width() <= 400 && $('#cometchat_righttab').width()!=$(window).width()){
                    var textareasize = 200;
                    if($(window).width() < 850){
                        cometchat_container.width(400);
                        reducesize = 400;
                        textareasize = 100;
                        animatewidth = $(window).width() - cometchat_container.width();
                    }
                    if(rtl == 1){
                        $('#cometchat_righttab').css({'right':'301px','width':$('#cometchat_righttab').width()});
                        $('#cometchat_leftbar').css({'position':'absolute','right':'0'});
                        $("#cometchat_righttab").animate({'right':'-=300px','width':($(window).width()-cometchat_container.width())},500);
                        $("#cometchat_righttab").find(".cometchat_textarea").animate({'width':'-='+ textareasize},500);
                        $('#cometchat_leftbar').animate({'right':'-=300px'},500);
                        cometchat_container.animate({"right":'-='+reducesize}, 500).addClass('visible');
                    }else{
                        $('#cometchat_righttab').css({'left':'301px','width':$('#cometchat_righttab').width()});
                        $('#cometchat_leftbar').css({'position':'absolute','left':'0'});
                        $("#cometchat_righttab").animate({'left':'-=300px','width':($(window).width()-cometchat_container.width())},500);
                        $("#cometchat_righttab").find(".cometchat_textarea").animate({'width':'-='+ textareasize},500);
                        $('#cometchat_leftbar').animate({'left':'-=300px'},500);
                        cometchat_container.css({left:left}).animate({"left":'-='+reducesize}, 500).addClass('visible');
                    }
                }else if($('#cometchat_righttab').width()==$(window).width()){
                    animatewidth = '0';
                    if(embeddedchatroomid > 0 && $(window).width() > 800){
                        cometchat_container.width($(window).width()/2);
                        reducesize = $(window).width()/2;
                        animatewidth = $(window).width() - cometchat_container.width();
                        $("#cometchat_righttab").animate({'width':$("#cometchat_righttab").width()-reducesize+'px'},"fast");
                        $("#cometchat_righttab").find(".cometchat_textarea").animate({'width':$("#cometchat_righttab").width() - (reducesize + 140) + 'px'},"fast");
                    }else{
                        cometchat_container.width($(window).width());
                        reducesize = $(window).width();
                    }
                    if(rtl == 1){
                        cometchat_container.animate({"right":'-='+reducesize}, 500).addClass('visible');
                    }else{
                        cometchat_container.css({left:left}).animate({"left":'-='+reducesize}, 500).addClass('visible');
                    }
                }else{
                    $("#cometchat_righttab").animate({'width':'-='+reducesize},500);
                    $("#cometchat_righttab").find(".cometchat_textarea").animate({'width':'-='+reducesize},500);
                    if(rtl == 1){
                        cometchat_container.animate({"right":'-='+reducesize}, 500).addClass('visible');
                    } else{
                        cometchat_container.css({left:left}).animate({"left":'-='+reducesize}, 500).addClass('visible');
                    }
                }
            }else if($('.cometchat_container').hasClass('visible')){
                width = $('.visible').width();
                cometchat_container.width(width);
                animatewidth = $(window).width() - width;
                $('.cometchat_container').filter('.visible').remove().removeClass('visible');
                if(rtl == 1){
                    cometchat_container.animate({"right":'-='+reducesize}, 500).addClass('visible');
                } else{
                    cometchat_container.css({left:left}).animate({"left":'-='+reducesize}, 500).addClass('visible');
                }
            }
            if($('#cometchat_righttab').css('top') == "0px" || $('#cometchat_header').length != 1){
                cometchat_container.css({'top':'0px','height':'100%','position':'fixed'});
            }else{
                cometchat_container.css({'top':'73px','height':'100%','position':'absolute'});
            }
            $('#cometchat_righttab').find('.moderatorbutton').on('click',function(e){
                e.stopImmediatePropagation();
                var uid = $(this).attr('uid');
                var chatroomid = $(this).attr('chatroomid');
                var method = $(this).attr('id');

                if(method == 'cc_kick'){
                    jqcc.cometchat.kickChatroomUser(uid,1,chatroomid);
                } else if(method == 'cc_ban') {
                    jqcc.cometchat.banChatroomUser(uid,1,chatroomid);
                } else {
                    jqcc('.cometchat_closebox').click();
                    cometchat_container.remove();
                    jqcc.cometchat.chatWith(uid);
                }
            });
            cometchat_container.find('.cometchat_closebox').click(function(){
                if(rtl == 1){
                    cometchat_container.animate({"right":"+="+cometchat_container.width()}, 500).removeClass('visible');
                }else{
                    cometchat_container.animate({"left":"+="+cometchat_container.width()}, 500).removeClass('visible');
                }
                jqcc("#cometchat_tooltip").css('display', 'none');
                var windowwidth = cometchat_container.width();
                if($("#cometchat_righttab").width()+cometchat_container.width() >= ($(window).width()-2) ){
                    var increasesize = ($(window).width() - $("#cometchat_leftbar").width()) - $('#cometchat_righttab').width();
                    if(embeddedchatroomid > 0){
                        $("#cometchat_righttab").find(".cometchat_textarea").animate({'width':$(document).width() - 140 + 'px'},"fast");
                    }else{
                        $("#cometchat_righttab").find(".cometchat_textarea").animate({'width':$("#cometchat_righttab").width() + 60 + 'px'},"fast");
                    }
                    if(rtl == 1){
                        $("#cometchat_righttab").animate({'right':'+='+$("#cometchat_leftbar").width(),'width':'+='+increasesize},500);
                        $("#cometchat_leftbar").animate({'right':'+='+$("#cometchat_leftbar").width()},500);
                    }else{
                        $("#cometchat_righttab").animate({'left':'+='+$("#cometchat_leftbar").width(),'width':'+='+increasesize},500);
                        $("#cometchat_leftbar").animate({'left':'+='+$("#cometchat_leftbar").width()},500);
                    }
                    setTimeout(function(){
                        $('#cometchat_righttab').removeAttr('style');
                        $('#cometchat_leftbar').removeAttr('style');
                    },2000);
                }else if($('#cometchat_righttab').width()==$(window).width()){
                    if(rtl == 1){
                        cometchat_container.css({right:left}).animate({"right":'+='+$(document).width()}, 500).addClass('visible');
                    }else{
                        cometchat_container.css({left:left}).animate({"left":'+='+$(document).width()}, 500).addClass('visible');
                    }
                }else{
                    $("#cometchat_righttab").animate({'width':'+='+windowwidth},500);
                    $("#cometchat_righttab").find(".cometchat_textarea").animate({'width':$("#cometchat_righttab").width() +(windowwidth - 140) + 'px'},"fast");
                }
                setTimeout(function() {
                    cometchat_container.remove();
                },1000);
            });
        }
    }
});

    jqcc('.unbanChatroomUserembedded').live('click',function(){
        var baseurl = jqcc.cometchat.getBaseUrl();
        var basedata = jqcc.cometchat.getBaseData();
        var roomid = jqcc.cometchat.getChatroomVars('currentroom');
        var roompass = jqcc.cometchat.getChatroomVars('currentp');
        var roomname = cc_urlencode(jqcc.cometchat.getChatroomVars('currentroomname'));
        var popoutmode = jqcc.cometchat.getChatroomVars('popoutmode');
        var theme = "<?php echo $layout; ?>";
        var caller = 'cometchat_embedded_iframe';
        var lang = "<?php echo $chatrooms_language['select_users'];?>";
        var url = baseurl+'modules/chatrooms/chatrooms.php?action=unban&caller='+caller+'&roomid='+roomid+'&inviteid='+roompass+'&basedata='+basedata+'&roomname='+roomname+'&cc_layout='+theme+'&popoutmode='+popoutmode+'&time='+Math.random();
        var mobileDevice = navigator.userAgent.match(/ipad|ipod|iphone|android|windows ce|Windows Phone|blackberry|palm|symbian/i);

        if(jqcc.cometchat.getCcvariable().callbackfn=='desktop' || mobileDevice){
            jqcc.cometchat.unbanChatroomUser(1);
        }else{
            if(typeof(parent) != 'undefined' && parent != null && parent != self){
                var controlparameters = {"type":"modules", "name":"cometchat", "method":"unbanChatroomUser", "params":{"url":url, "action":"invite", "lang":lang}};
                controlparameters = JSON.stringify(controlparameters);
                loadCCPopup(url, 'unban',"status=0,toolbar=0,menubar=0,directories=0,resizable=0,location=0,status=0,scrollbars=0,width=300,height=300",300,300,'Unban users',null,null,null,null,0);
            } else {
                var controlparameters = {};
                jqcc.cometchat.unbanChatroomUser();
            }
        }
        $('.cometchat_float_list').hide();
        $('.cometchat_pluginuparrow').removeClass('rotated').css({'transform' :'rotate(0deg)'});
        $('#cometchat_newcompose').removeClass('rotated');
        $('.cometchat_pluginrightarrow').removeClass('rotated').css({'transform' :'rotate(0deg)'});
    });
    jqcc('.cometchat_cruserlist').live('click',function(){
        var userid = jqcc.cometchat.getChatroomVars('myid');
        id = this.id.split('_')[2];
        if(userid != id) {
            jqcc('#cometchat_righttab').find('#cometchat_container_chatroomusers').find('.cometchat_closebox').click();
            jqcc.cometchat.chatWith(id);
        }
    });
    jqcc('.loadChatroomActions').live('click',function(){
        var cometchat_group_popup = $('#currentroom');
        if(cometchat_group_popup.find('#chatroom_userlist_'+uid+' #cometchat_moderatoroptions_'+uid).length){
            if(cometchat_group_popup.find('#cometchat_moderatoroptions_'+uid).css('display') == 'none') {
                jqcc('#chatroom_userlist_'+uid).animate({height: "64px"});
                jqcc('#cometchat_moderatoroptions_'+uid).css('display','block');
            } else {
                jqcc('#chatroom_userlist_'+uid).animate({height: "32px"},{complete: function(){jqcc('#cometchat_moderatoroptions_'+uid).css('display','none');}});
            }
        }
    });
});
