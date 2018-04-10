<?php
    foreach ($trayicon as $value){
        if($value[0]=='chatrooms'){
            if(file_exists(dirname(dirname(dirname(dirname(__FILE__)))).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR."chatrooms".DIRECTORY_SEPARATOR."layouts".DIRECTORY_SEPARATOR.$layout.DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR.$layout.".js")){
            include_once(dirname(dirname(dirname(dirname(__FILE__)))).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR."chatrooms".DIRECTORY_SEPARATOR."layouts".DIRECTORY_SEPARATOR.$layout.DIRECTORY_SEPARATOR."js".DIRECTORY_SEPARATOR.$layout.".js");
            }
        }
    }
?>

(function($){
    $.ccembedded = (function(){
        var settings = {};
        var baseUrl;
        var staticCDNUrl;
        var language;
        var trayicon;
        var typingSenderTimer;
        var typingRecieverTimer;
        var typingSenderFlag = 1;
        var typingReceiverFlag = {};
        var resynchTimer;
        var notificationTimer;
        var chatboxOpened = {};
        var undeliveredmessages = [];
        var unreadmessages = [];
        var trayWidth = 0;
        var siteOnlineNumber = 0;
        var tooltipPriority = 0;
        var desktopNotifications = {};
        var webkitRequest = 0;
        var lastmessagetime = Math.floor(new Date().getTime());
        var favicon;
        var checkfirstmessage;
        var cometchat_lefttab;
        var cometchat_header;
        var cometchat_righttab;
        var chromeReorderFix = '_';
        var hasChatroom = 0;
        var newmesscr;
        var statusvalue = '';
        var cookiePrefix = '<?php echo $cookiePrefix; ?>';
        var lastseen = 0;
        var lastseenflag = false;
        var iOSmobileDevice = navigator.userAgent.match(/ipad|ipod|iphone/i);
        var mobileDevice = navigator.userAgent.match(/ipad|ipod|iphone|android|windows ce|Windows Phone|blackberry|palm|symbian/i);
        var messagereceiptflag = 0;
        var statusvalue = '';
        var allowGuests = <?php echo $allowGuests; ?>;
        var crguestsMode = <?php echo $crguestsMode; ?>;
        var guestsMode = <?php echo $guestsMode; ?>;
        var rtl = "<?php echo  $rtl; ?>";
        return {
            playSound: function(type){
                var flag = 0;
                try{
                    if(settings.messageBeep==1&&(settings.beepOnAllMessages==1||(settings.beepOnAllMessages == 0 && checkfirstmessage == 1))){
                        if(type == 1){
                            document.getElementById('messageOpenBeep').play();
                        }else if(type == 2){
                            document.getElementById('announcementBeep').play();
                        }else if(type == 3){
                            document.getElementById('incommingcall').play();
                        }else if(type == 4){
                            document.getElementById('outgoingcall').play();
                        }else{
                            document.getElementById('messageBeep').play();
                        }
                    }
                }catch(error){
                }
            },
            initialize: function(){
                settings = jqcc.cometchat.getSettings();
                baseUrl = jqcc.cometchat.getBaseUrl();
                staticCDNUrl = jqcc.cometchat.getStaticCDNUrl();
                language = jqcc.cometchat.getLanguage();
                trayicon = jqcc.cometchat.getTrayicon();
                var trayData = '';
                var tabWidth = 'width: 100%;';

                if(settings.windowFavicon==1){
                    favicon = new Favico({
                        animation: 'pop'
                    });
                }

                $("body").append('<div id="cometchat"></div><div id="cometchat_tooltip"><div class="cometchat_tooltip_content"></div></div>');
                var optionsbutton = '';
                var optionsbuttonpop = '';
                var ccauthlogout = '';
                var usertab = '';
                var usertabpop = '';
                var optionsbuttonpadding = '';
                var count = 140;
                var lastseenoption = '';
                var messagereceiptoption = '';
                if(settings.lastseen == 1){
                    lastseenoption = '<div class="cometchat_lightdisplay"><div style="height:25px;" class="cometchat_disablelastseen cometchat_control cometchat_control--checkbox">'+language[108]+'<input type="checkbox" class="cometchat_checkbox" name="cometchat_disablelastseen" id="cometchat_disablelastseen"><div class="cometchat_control__indicator"></div></div></div>';
                } else{
                    lastseenflag = true;
                }
                if(settings.messagereceiptEnabled == 1 && settings.cometserviceEnabled == 1){
                    messagereceiptoption = '<div style="clear:both"></div><div><input type="checkbox" id="cometchat_messagereceipt" style="vertical-align: -2px;">'+language['disable_message_receipt']+'</div>';
                }
                var modules = '';
                if(settings.usebots == 1) {
                    modules = '<div id="cometchat_module_bots" class="cometchat_lightdisplay cometchat_module leftpadding"><div style="margin-top:2px;">'+language['bots']+'</div></div>  <div id="bots_window" class="cometchat_windows"><div id="cometchat_windowtitlebar"><div id="cometchat_bot_title_text">'+language['bots']+'</div><div id="bots_closewindow" class="cometchat_closewindow" ><img src="<?php echo STATIC_CDN_URL; ?>layouts/<?php echo $layout;?>/images/removewhite.svg"/></div></div><div id="bots_container" class="cometchat_side_container"></div></div></div>';
                }
                var pluginwindows = '';
                var blockuserwindow = '';
                for(i=0;i<settings.plugins.length;i++){
                    var name = 'cc'+settings.plugins[i];
                    if( name == 'ccblock' ){
                        blockuserwindow = '<div class="cometchat_chats_labels"></div><div id="cometchat_module_block" class="cometchat_module"><div class="cometchat_lightdisplay leftpadding"><div style="margin-top:2px;">'+language['blocked_users']+'</div><span id="cometchat_blockeduserscount"></span></div></div>';
                    }
                }
                if(settings.showModules==1){
                    var listedmodules = ['home', 'scrolltotop', 'announcements', 'broadcastmessage','themechanger','chatrooms'];
                    for(x in trayicon){
                        if(trayicon.hasOwnProperty(x)){
                            if(listedmodules.indexOf(x) < 0) {
                                var modulewindow = jqcc[settings.theme].create_side_window(x,trayicon[x][1],'');
                                modules += '<div id="cometchat_module_'+x+'" class="cometchat_lightdisplay cometchat_module leftpadding"><div style="margin-top:2px;">'+trayicon[x][1]+'</div></div>'+modulewindow;
                            }
                        }
                    }
                }
                if(settings.disableGroupTab == 0) {
                     hasChatroom = 1;
                }
                var readreceipthtml = '';
                if(settings.cometserviceEnabled == 1){
                    readreceipthtml = '<div class="cometchat_lightdisplay"><div style="height:25px;" class="cometchat_readreceipt_div cometchat_control cometchat_control--checkbox">'+language['show_read_receipt']+'<input type="checkbox" class="cometchat_checkbox" name="cometchat_readreceipt" id="cometchat_readreceipt"><div class="cometchat_control__indicator"></div></div></div>';
                }
                if(embeddedchatroomid != 0 || chatroomsonly == 1){
                    settings.enableType = 1;
                }

                if(settings.enableType == 0 ) {
                    tabWidth = 'width: 50%;';
                }

                optionsbuttonpop = '<div id="cometchat_optionsbutton_popup" class="cometchat_tabcontent cometchat_optionstyle" ><div class="cometchat_chats_labels">'+language[43].toUpperCase()+'</div><textarea readonly class="cometchat_lightdisplay" id="cometchat_selfdisplayname"></textarea><div id="guestsname" class="cometchat_lightdisplay"><input type="text" class="cometchat_guestnametextbox" style="border: 1px solid;"/><div class="cometchat_guestnamebutton">'+language[44]+'</div></div><div class="cometchat_chats_labels" style="border-bottom:1px solid #D1D1D1">'+language[2].toUpperCase()+'</div><div id="cometchat_statusmessageinput"><textarea class="cometchat_statustextarea" maxlength="140"></textarea><div class="cometchat_statusmessagecount"></div></div>    <div class="cometchat_chats_labels" style="margin-top:10px;">'+language[23].toUpperCase()+'</div><div class="cometchat_optionsstatus available cometchat_lightdisplay"><div class="cometchat_optionsstatus2 cometchat_user_available"></div><div style="margin-top:3px;">'+language['available']+'</div><label class="cometchat_radio"><input id="cometchat_statusavailable_radio" type="radio" name="cometchat_statusoptions" value="available" checked><span class="cometchat_radio_outer"><span class="cometchat_radio_inner"></span></span></label></div><div class="cometchat_optionsstatus away cometchat_lightdisplay"><div class="cometchat_optionsstatus2 cometchat_user_away" ></div><div style="margin-top:3px;">'+language['away']+'</div><label class="cometchat_radio"><input id="cometchat_statusaway_radio" type="radio" name="cometchat_statusoptions" value="away"><span class="cometchat_radio_outer"><span class="cometchat_radio_inner"></span></span></label></div><div class="cometchat_optionsstatus busy cometchat_lightdisplay"><div class="cometchat_optionsstatus2 cometchat_user_busy"></div><div style="margin-top:3px;">'+language['busy']+'</div><label class="cometchat_radio"><input id="cometchat_statusbusy_radio" type="radio" name="cometchat_statusoptions" value="busy"><span class="cometchat_radio_outer"><span class="cometchat_radio_inner"></span></span></label></div><div class="cometchat_optionsstatus cometchat_gooffline invisible cometchat_lightdisplay"><div class="cometchat_optionsstatus2 cometchat_user_invisible"></div><div style="margin-top:3px;">'+language['invisible']+'</div><label class="cometchat_radio"><input id="cometchat_statusinvisible_radio" type="radio" name="cometchat_statusoptions" value="invisible"><span class="cometchat_radio_outer"><span class="cometchat_radio_inner"></span></span></label></div><div class="cometchat_chats_labels">'+language['notifications'].toUpperCase()+'</div><div class="cometchat_lightdisplay"><div style="height:25px;" class="cometchat_soundnotifications_div cometchat_control cometchat_control--checkbox">'+language[13]+'<input type="checkbox" class="cometchat_checkbox" name="cometchat_soundnotifications" id="cometchat_soundnotifications"><div class="cometchat_control__indicator"></div></div></div><div class="cometchat_lightdisplay"><div style="height:25px;" class="cometchat_popupnotifications_div cometchat_control cometchat_control--checkbox">'+language[24]+'<input type="checkbox" class="cometchat_checkbox" name="cometchat_popupnotifications" id="cometchat_popupnotifications"><div class="cometchat_control__indicator"></div></div></div>'+readreceipthtml+lastseenoption+blockuserwindow+'<div class="cometchat_chats_labels"></div><div id="cometchat_moduleslist">'+modules+'</div>';
                var newcompose = '';
                var notificationicon = '';
                if(trayicon.announcements){
                    notificationicon = '<div id="cometchat_notification" style="padding-top:6px;"><div id="cometchat_notification_icon" class="cometchat_notificationimages"></div></div>';
                }
                if((jqcc.cometchat.getChatroomVars('allowUsers') == 1 && settings.disableContactsTab == 0) || (trayicon.broadcastmessage && hasChatroom == 1)) {
                   newcompose = '<div id = "cometchat_newcompose"><div id = "newcomposeimages"></div></div>';
                }
                var moreoption = '<div id = "moreoption" style="padding-top: 20px;"><div id="cometchat_more_icon" class="cometchat_moreimages"></div></div>';
                var auth_logout = '';
                var headericon = '';
                if($(document).width() <= 414 ){
                    headericon = '<div id = "cometchat_smallmenu"><img src="<?php echo STATIC_CDN_URL; ?>layouts/<?php echo $layout;?>/images/menu.svg" height="24" width="24" class="smallmenuimages"/></div>';
                }else{
                    headericon = newcompose+moreoption+notificationicon;
                }
                if(settings.ccauth.enabled == "1" || jqcc.cometchat.getCcvariable().callbackfn == 'desktop'){
                    auth_logout = '<div id = "cometchat_authlogout" style="padding-top: 2px;"><div id="cometchat_logout_icon" class="cometchat_logoutimages" title="'+language[80]+'" onclick="javascript: jqcc.embedded.logout_click()"></div></div>';
                }
                var selfDetails = '<div id = "cometchat_header"><div id="cometchat_self_container"><div id="cometchat_self_right">'+headericon+auth_logout+'</div><div id="cometchat_self_left"></div></div></div>';
                var recentgroup = '';
                var tabscontainer = '';
                var searchbar = '';
                var usertab = '';
                var grouptab = '';
                var recenttab = '';
                var recentpopup = '';
                var chats = '';
                var group = '';

                if(settings.disableContactsTab == 0){
                    chats = '<div id="chats" class="tabcontent" style="display:block;"><div id="cometchat_userslist" class="tabcontent"></div></div>';
                }
                if(hasChatroom){
                    group = '<div id="groups" ><div id="cometchat_chatroomstab_popup"></div></div>';
                }

                if(settings.showOnlineTab==1 && settings.enableType!=1){
                    if(settings.disableContactsTab == 0){
                        usertab = '<div id = "cometchat_chats" class="cometchat_tablecell" ><span id="cometchat_chatstab" class="cometchat_tab tab_click"><span class="cometchat_tabstext">'+language["contacts"]+'</span></span></div>';
                    }
                    if(hasChatroom){
                        grouptab = '<div id = "cometchat_groups" class="cometchat_tablecell" ><span id="cometchat_groupstab" class="cometchat_tab "><span class="cometchat_tabstext">'+language["groups"]+'</span></span></div>';
                    }
                }else if(chatroomsonly == 1 && hasChatroom){
                    grouptab = '<div id = "cometchat_groups" class="cometchat_tablecell" ><span id="cometchat_groupstab" class="cometchat_tab "><span class="cometchat_tabstext">'+language["groups"]+'</span></span></div>';
                }

                if(settings.disableRecentTab == 0 && !chatroomsonly) {
                    recenttab = '<div id = "cometchat_recent" class="cometchat_tablecell" ><span id="cometchat_recenttab" class="cometchat_tab "><span class="cometchat_tabstext">'+language["recent_chats"]+'</span></span></div>';
                    recentpopup = '<div id="cometchat_recentpopup" class="tabcontent" style="display:none;"><div id="cometchat_recentlist"></div></div>';
                }

                if(chatroomsonly == 1 && hasChatroom){
                    group = '<div id="groups" class="tabcontent tab_click" style="display:block;"><div id="cometchat_chatroomstab_popup"></div></div>';
                    chats = '';
                    tabscontainer = grouptab;
                }else{
                    tabscontainer = recenttab+usertab+grouptab;
                }
                searchbar = '<span id="searchbar"><input type="text" name="cometchat_user_search" id = "cometchat_user_search" class="cometchat_textsearch" placeholder="'+language[18]+'"/><div class="cometchat_closeboxsearch cometchat_tooltip" id="close_user_search" title="'+language[115]+'"></div></span><hr id="newhr">';
                if(settings.enableType != 0 && chatroomsonly != 1){
                    tabscontainer = '';
                    searchbar = '';
                    if(jqcc.cometchat.getSessionVariable('buddylist')!=1){
                        jqcc.cometchat.setThemeArray('sessionVars','buddylist', '1');
                    }
                }
                var chatbox = '<div id="cometchat_righttab"><div class="cometchat_noactivity"><h1>'+language[89]+' <span id="cometchat_welcome_username"></span>'+language[91]+'</h1><h3>'+language[90]+'</h3></div></div>';
                usertabpop = '<div id="cometchat_leftbar"><div class=\'cometchat_table\'><div class=\'cometchat_tablerow\'>'+tabscontainer+'</div></div>'+searchbar+'<div id="cometchat_userscontent"><div id="cc_gotoPrevNoti"></div><div id="cc_gotoNextNoti"></div>'+recentpopup+chats+group+'</div></div>'+chatbox;
                var createChatroom = '';
                if(jqcc.cometchat.getChatroomVars("allowUsers") == 1 && hasChatroom){
                    createChatroom='<div class="content_div" id="create" style="display:block;"><div id="create_chatroom" class="content_div"><form class="create" onsubmit="javascript:jqcc.cometchat.createChatroomSubmit(); return false;"><div style="clear:both;padding-top:10px"></div><div class="create_value"><input type="text" id="name" class="create_input" placeholder="<?php echo $chatrooms_language[27];?>" /></div><div style="clear:both;"><div class="create_value password_hide"><input id="cometchat_chatroom_password" type="password" autocomplete="off" class="create_input" placeholder="<?php echo $chatrooms_language[32];?>" /></div></div><div class="create_value" ><select id="type" onchange="jqcc.crembedded.crcheckDropDown(this)" class="create_input"><option value="0"><?php echo $chatrooms_language[29];?></option><option value="1"><?php echo $chatrooms_language[30];?></option><option value="2"><?php echo $chatrooms_language[31];?></option></select></div><div class="password_hide" style="clear:both;padding-top:10px"></div><div class="create_value"><input type="submit" class="createroombutton" value="<?php echo $chatrooms_language[33];?>" /></div></form></div></div>';
                }
                var calculatedheight = '90%';
                var morewindow = jqcc[settings.theme].create_side_window('more',language['more'],optionsbuttonpop);
                if(trayicon.broadcastmessage){
                    var composebroadcastwindow = jqcc[settings.theme].create_side_window('compose',language[117],'');
                }
                var composechatwindow = jqcc[settings.theme].create_side_window('composechat',language['new_group'],createChatroom);
                var notificationwindow = jqcc[settings.theme].create_side_window('notification',language['announcements'],'');
                var baseCode = '<div class="cometchat_offline_overlay"><h3>'+language[92]+'</h3></div>'+selfDetails+'<div id="main_container">'+usertabpop+'</div></div>'+morewindow+composebroadcastwindow+notificationwindow+composechatwindow+pluginwindows+'</div></div>';
                document.getElementById('cometchat').innerHTML = baseCode;
                setTimeout(function(){
                    if($('body').find("#cometchat_loader").length == 1){
                        $('body').find("#cometchat_loader").remove();
                    }
                },200);
                if(hasChatroom == 1){
                    jqcc.crembedded.chatroomInit();
                }
                if(settings.enableType == 1){
                    $('#cometchat_righttab').find('#cometchat_tabinputcontainer').width('100%');
                    $('#cometchat_righttab').find('.cometchat_textarea').width($(window).width() - 140);
                }
                if(settings.enableType == 2) {
                    $('#cometchat_userstab_popup').addClass("cometchat_tabopen");
                }
                if(settings.showSettingsTab==0){
                    $('#cometchat_userstab').addClass('cometchat_extra_width');
                    $('#cometchat_userstab_popup').find('div.cometchat_tabstyle').addClass('cometchat_border_bottom');
                }
                if(jqcc().slimScroll && mobileDevice == null){
                    var calculatedheight = $(window).height() -150;
                    $('#cometchat_userscontent').slimScroll({height: calculatedheight});
                    $('#cometchat_userscontent').attr('style','overflow: hidden !important');
                }
                jqcc[settings.theme].optionsButton();
                jqcc[settings.theme].chatTab();
                $('.cometchat_statustextarea').keyup(function(){
                    $('.cometchat_statusmessagecount').show();
                    count = $(this).attr('maxlength')-$(this).val().length;
                    $('.cometchat_statusmessagecount').html(count);
                });
                $('.cometchat_statustextarea').mouseup(function(){
                    $('.cometchat_statusmessagecount').show();
                    count = $(this).attr('maxlength')-$(this).val().length;
                    $('.cometchat_statusmessagecount').html(count);
                });
                $('.cometchat_statustextarea').mousedown(function(){
                    $('.cometchat_statusmessagecount').show();
                    count = $(this).attr('maxlength')-$(this).val().length;
                    $('.cometchat_statusmessagecount').html(count);
                });
                $('.cometchat_statustextarea').blur(function() {
                    $('.cometchat_statusmessagecount').hide();
                });
                $('#cometchat_userscontent').attr('unselectable', 'on').css('MozUserSelect', 'none').bind('selectstart.ui', function(){
                    return false;
                });
                $('.cometchat_trayicon').attr('unselectable', 'on').css('MozUserSelect', 'none').bind('selectstart.ui', function(){
                    return false;
                });
                $('.cometchat_tab').attr('unselectable', 'on').css('MozUserSelect', 'none').bind('selectstart.ui', function(){
                    return false;
                });
                $(window).bind('resize', function(){
                    if(mobileDevice){
                        $('#cometchat').css('overflow','scroll');
                    }
                    jqcc[settings.theme].windowResize();
                });
                if(typeof document.body.style.maxHeight==="undefined"){
                    jqcc[settings.theme].scrollFix();
                    $(window).bind('scroll', function(){
                        jqcc[settings.theme].scrollFix();
                    });
                }/*else if(mobileDevice){
                    var mobileOverlay = '';
                    if('<?php echo $p_; ?>'<3){
                        mobileOverlay = '<div class="cometchat_mobile_overlay"><p>'+language[94]+'</p></div>';
                        $('#cometchat').html(mobileOverlay);
                        jqcc.cometchat.setThemeVariable('runHeartbeat', 0);
                    }
                }*/

                if($.cookie(settings.cookiePrefix+"sound")){
                    if($.cookie(settings.cookiePrefix+"sound")=='true'){
                        $("#cometchat_soundnotifications").attr("checked", true);
                    }else{
                        $("#cometchat_soundnotifications").attr("checked", false);
                    }
                } else {
                    $.cookie(settings.cookiePrefix+"sound", 'true',{path: '/'});
                    $("#cometchat_soundnotifications").attr("checked", true);
                }

                if($.cookie(settings.cookiePrefix+"popup")){
                     if($.cookie(settings.cookiePrefix+"popup")=='true'){
                        $("#cometchat_popupnotifications").attr("checked", true);
                    }else{
                        $("#cometchat_popupnotifications").attr("checked", false);
                    }
                } else {
                    $.cookie(settings.cookiePrefix+"popup", 'true',{path: '/'});
                    $("#cometchat_popupnotifications").attr("checked", true);
                }

                if($.cookie(settings.cookiePrefix+"disablelastseen")){
                    if($.cookie(settings.cookiePrefix+"disablelastseen")=='false'){
                        $("#cometchat_disablelastseen").attr("checked", true);
                    }else{
                        $("#cometchat_disablelastseen").attr("checked", false);
                    }
                } else {
                    $.cookie(settings.cookiePrefix+"disablelastseen", 'false',{path: '/'});
                    $("#cometchat_disablelastseen").attr("checked", true);
                }
                var cc_state = $.cookie(settings.cookiePrefix+'state');
                if(cc_state==null){
                    setTimeout(function(){
                        if ($(".cometchat_recentchatlist").length > 0){
                            jqcc.embedded.openChatTab(0,0);
                        }else{
                            jqcc.embedded.openChatTab(1,0);
                        }
                    },500);
                }
                /*//////////////////////clicks/////////////*/
                var calculatedheight = '100%';
                if(mobileDevice){
                    $('#cometchat_optionsbutton_popup').css({'overflow-y':'auto','height':'100%'});
                }else{
                    $('#cometchat_optionsbutton_popup').slimScroll({height: calculatedheight,srcoll:1});
                }
                $('#cometchat_header').on('click','#cometchat_more_icon',function(){
                    if($.cookie(settings.cookiePrefix+"read")){
                        if($.cookie(settings.cookiePrefix+"read")=='true'){
                            readreceiptsettingDB = 1;
                        }else{
                            readreceiptsettingDB = 0;
                        }
                    } else {
                        readreceiptsettingDB = jqcc.cometchat.getThemeArray('buddylistReadReceiptSetting',jqcc.cometchat.getUserID());
                    }
                    if(readreceiptsettingDB == 1){
                        $("#cometchat_readreceipt").attr("checked", true);
                    }else{
                        $("#cometchat_readreceipt").attr("checked", false);
                    }
                    var hidden = $('#more_window');
                    jqcc[settings.theme].moveWindow(hidden);
                });
                $('#cometchat_header').on('click','#cometchat_smallmenu', function(e){
                    if(!$('#cometchat_smallmenu').hasClass('rotated')){
                        $('#newcompose_float_list').remove();
                        $('#cometchat_smallmenu').addClass('rotated');
                        var offset = $('#cometchat_smallmenu').offset();
                        var list = [language['more']];

                        if(jqcc.cometchat.getChatroomVars('allowUsers') == '1' && hasChatroom == 1){
                            list.push(language['new_group']);
                        }

                        if(trayicon.broadcastmessage && settings.disableContactsTab == 0){
                            list.push(language[113]);
                        }
                        if(trayicon.announcements){
                            list.push(language['announcements']);
                        }
                        var newdiv = jqcc[settings.theme].create_dropdownpopup(this.id,offset,list,'up',0,8);
                        $('#cometchat_header').append(newdiv);
                        $('.cometchat_float_list').addClass('floatactive');
                        $('.cometchat_float_list').css('top',offset.top+65);
                        if(rtl == 1){
                            $('.cometchat_float_list').css('left',offset.left+15);
                        }else{
                            $('.cometchat_float_list').css('left',offset.left-190);
                        }
                        $('.cometchat_arrow-up').css('top',offset.top+56);
                        $('.cometchat_arrow-up').css('left',offset.left+32);
                    }else{
                        $('#cometchat_smallmenu').removeClass('rotated');
                        $( '#cometchat_header' ).find('#cometchat_smallmenu_float_list').remove();
                        $('.cometchat_float_list').hide();
                    }
                });
                $('#cometchat_header').on('click','#cometchat_newcompose', function(e){
                    if(!$('#cometchat_newcompose').hasClass('rotated')){
                        $('#newcompose_float_list').remove();
                        $('.cometchat_float_list').hide();
                        $('#cometchat_newcompose').addClass('rotated');
                        var offset = $('#cometchat_newcompose').offset();
                        var list = [];
                        if(trayicon.broadcastmessage && settings.disableContactsTab == 0){
                            list.push(language[113]);
                        }
                        if(jqcc.cometchat.getChatroomVars('allowUsers') == 1 && hasChatroom == 1){
                            list.push(language['new_group']);
                        }
                        var newdiv = jqcc[settings.theme].create_dropdownpopup(this.id,offset,list,'up',0,8);
                        $('#cometchat_header').append(newdiv);
                        $('.cometchat_float_list').addClass('floatactive');
                        $('.cometchat_float_list').css('top',offset.top+65);
                        if(rtl == 1){
                            $('.cometchat_float_list').css('left',offset.left+15);
                        }else{
                            $('.cometchat_float_list').css('left',offset.left-190);
                        }
                        $('.cometchat_arrow-up').css('top',offset.top+56);
                        $('.cometchat_arrow-up').css('left',offset.left+32);
                    }else{
                        $('#cometchat_newcompose').removeClass('rotated');
                        $( '#cometchat_header' ).find('#cometchat_newcompose_float_list').remove();
                        $('.cometchat_float_list').hide();
                    }
                });
                $('#main_container').on('click','.cometchat_pluginrightarrow', function(e){
                    var rotation;
                    id = this.id;
                    id = id.split("_");
                    $('#cometchat_pluginuparrow_'+id[2]+'_float_list').hide();
                    $('.cometchat_float_list , .cometchat_arrowdown').hide();
                    var plugin_userid = id[2];
                    var rightPluginList = ['clearconversation','block','chathistory','report','save'];
                    if(!$('#'+this.id).hasClass('rotated')){
                        $('#'+this.id).addClass('rotated');
                        jqcc[settings.theme].downWindow(e);
                        if(rtl == 1){
                            rotation = 270;
                        }else{
                            rotation = 90;
                        }
                        var offset = $(this).offset();
                        var list = settings.plugins.filter(function(element) {
                            return rightPluginList.indexOf(element) != -1
                        });
                        var chatboxplugin = jqcc[settings.theme].create_dropdownpopup(this.id,offset,list,'up',1,plugin_userid);
                        if($('#cometchat_righttab').find('#currentroom_left').hasClass("cometchat_tabpopup") && !$('#cometchat_righttab').find('.cometchat_userchatbox').hasClass("cometchat_tabopen")){
                            var height = $('.cometchat_arrowup').height();
                            var top = offset.top - height;
                        }else{
                            $('#cometchat_user_'+plugin_userid+'_popup').append(chatboxplugin);
                        }
                        $('.cometchat_float_list').addClass('floatactive');
                        $('.cometchat_float_list').css('top',offset.top+27);
                        if(rtl == 1){
                            $('.cometchat_float_list').css('left',offset.left-167);
                        }else{
                            $('.cometchat_float_list').css('left',offset.left-67);
                        }
                        $('.cometchat_arrowup').css('display','block');
                        $('.cometchat_arrow-up').css('top',offset.top+18);
                        $('.cometchat_arrow-up').css('left',offset.left);
                        var floatheight = $('#cometchat_pluginrightarrow_'+id[2]+'_float_list').height();
                        if($(window).height() < floatheight){
                            $(".cometchat_float_list").slimScroll({height: $(window).height()-125});
                        }
                    }else{
                        $('#'+this.id).removeClass('rotated');
                        rotation = 0;
                        $('.cometchat_float_list').hide();
                    }
                    $(this).rotate(rotation);
                });

                $('#main_container').on('click','.cometchat_pluginuparrow', function(e){
                    if($('#cometchat_header').find('#cometchat_newcompose_float_list').length > 0){
                        $('#cometchat_header').find('#cometchat_newcompose_float_list').remove();
                    }
                    id = this.id;
                    id = id.split("_");
                    var rotation;
                    var plugin_userid = id[2];
                    var upPluginList = ['stickers','handwrite','screenshare','whiteboard','writeboard','broadcast','transliterate','voicenote'];
                    if($(window).width() < 300 ){
                        upPluginList.push('audiochat');
                    }
                    if($(window).width() < 270 ){
                        upPluginList.push('avchat');
                    }
                    if(mobileDevice){
                        upPluginList.splice( $.inArray('transliterate', upPluginList), 1 );
                    }
                    if( $('#cometchat_container_smilies').length <= 0 && $('#cometchat_container_stickers').length <= 0 && $('#cometchat_container_transliterate').length <= 0 &&  $('#cometchat_container_voicenote').length <= 0){
                        if(!$('#'+this.id).hasClass('rotated')){
                            $('#'+this.id).addClass('rotated');
                            rotation = 180;
                            var offset = $(this).offset();
                            var list = settings.plugins.filter(function(element) {
                                return upPluginList.indexOf(element) != -1
                            });
                            var chatboxplugin = jqcc[settings.theme].create_dropdownpopup(this.id,offset,list,'down',1,plugin_userid);
                            if($('#cometchat_righttab').find('#currentroom_left').hasClass("cometchat_tabpopup") && !$('#cometchat_righttab').find('.cometchat_userchatbox').hasClass("cometchat_tabopen")){
                                var height = $('.cometchat_arrowdown').height();
                                var top = offset.top - height;
                            }else{
                                if($('#'+this.id+'_float_list').hasClass('floatactive')){
                                    $('#'+this.id+'_float_list').remove();
                                }
                                $('#cometchat_user_'+plugin_userid+'_popup').append(chatboxplugin);
                                var height = $('#'+this.id+'_float_list').height();
                                var top = offset.top - height;
                            }
                            $('.cometchat_float_list').addClass('floatactive');
                            $('.cometchat_float_list').css('top',top);
                            if(rtl == 1){
                                $('.cometchat_float_list').css('left',offset.left-220);
                            }else{
                                $('.cometchat_float_list').css('left',offset.left-10);
                            }
                            $('.cometchat_arrowdown').css('display','block');
                            $('.cometchat_arrow-down').css('top',offset.top-3);
                            $('.cometchat_arrow-down').css('left',offset.left+4);
                            var floatheight = $('#cometchat_pluginuparrow_'+id[2]+'_float_list').height();
                            if($(window).height() < floatheight){
                                $('.cometchat_float_list').css('top','13px');
                                $('.cometchat_float_list').css('height',$(window).height()-50);
                                $(".cometchat_float_list").slimScroll({height: $(window).height()-50});
                            }
                        }else{
                            $('#'+this.id).removeClass('rotated');
                            $('.cometchat_float_list').hide();
                            rotation = 0;
                        }
                    }else{
                        jqcc[settings.theme].downWindow(e);
                        $('.cometchat_pluginuparrow').removeClass('rotated').css({'transform' :'rotate(0deg)'});
                    }
                    $(this).rotate(rotation);
                });
                $('#cometchat_recenttab').click(function(){
                    /*$('#cometchat_user_search').val('');
                    $('#cometchat').find('#cometchat_user_search').keyup();*/
                    $('#cometchat_recentpopup').show();
                    $('#cometchat_chatroomstab_popup').hide();
                    $('#chats').hide();
                    $('#cometchat_chatstab').removeClass('tab_click');
                    $('#cometchat_groupstab').removeClass('tab_click');
                    $('#cometchat_recenttab').addClass('tab_click');
                    $('#cometchat_chatroomstab_popup').removeClass("cometchat_tabopen");
                    $('#cometchat_userstab_popup').removeClass("cometchat_tabopen");
                    jqcc.cometchat.setSessionVariable('openedtab',0);
                });
                $('#cometchat_chatstab').click(function(){
                    $('#cometchat_user_search').val('');
                    $('#cometchat').find('#cometchat_user_search').keyup();
                    $('#chats').show();
                    $('#cometchat_chatroomstab_popup').hide();
                    $('#cometchat_recentpopup').hide();
                    $('#cometchat_recenttab').removeClass('tab_click');
                    $('#cometchat_groupstab').removeClass('tab_click');
                    $('#cometchat_chatstab').addClass('tab_click');
                    jqcc.cometchat.setSessionVariable('openedtab',1);
                });
                $('#cometchat_groupstab').click(function(){
                    $('#cometchat_user_search').val('');
                    $('#cometchat').find('#cometchat_user_search').keyup();
                    $('#cometchat_chatroomstab_popup').show();
                    $('#cometchat_recentpopup').hide();
                    $('#chats').hide();
                    $('#cometchat_chatstab').removeClass('tab_click');
                    $('#cometchat_recenttab').removeClass('tab_click');
                    $('#cometchat_groupstab').addClass('tab_click');
                    jqcc.cometchat.setSessionVariable('openedtab',2);
                });

                $('.cometchat_module').click(function(){
                    id = this.id;
                    id = id.split("_");
                    var hidden = $('#'+id[2]+'_window');
                    if(id[2] == 'block'){
                        if (jqcc.cometchat.membershipAccess('block','plugins')){
                            jqcc['ccblock'].blockList(1);
                        }
                    }else if(id[2] == 'bots'){
                        if (jqcc.cometchat.membershipAccess('bots','extensions')){
                            var container = $('#'+id[2]+'_window').find('#'+id[2]+'_container');
                            var content = '<iframe id="cometchat_bots_iframe" src="'+baseUrl+'extensions/bots/index.php?cc_layout=embedded" style="height:100%;border:none;"></iframe>';
                            container.html(content);
                            jqcc[settings.theme].moveWindow(hidden,1);
                        }
                    }else{
                        if (jqcc.cometchat.membershipAccess(id[2],'modules')){
                            var container = $('#'+id[2]+'_window').find('#'+id[2]+'_container');
                            var height = container.height();
                            var content = '<iframe src="'+baseUrl+'modules/'+id[2]+'/index.php?cc_layout=embedded&basedata=null" frameborder="0" height="'+height+'" id="cometchat_'+id[2]+'"></iframe>';
                            container.html(content);
                            jqcc[settings.theme].moveWindow(hidden,1);
                        }
                    }
                });
                $('#cometchat_header').on('click','#cometchat_notification',function(){
                    if (jqcc.cometchat.membershipAccess('announcements','modules')){
                        var hidden = $('#notification_window');
                        var announcementcontent = '<iframe src="'+baseUrl+'modules/announcements/index.php?cc_layout=embedded&basedata=null" frameborder="0" style="height:calc(100% - 48px);"></iframe>';
                        $('#notification_window').find('#notification_container').html(announcementcontent);
                        jqcc[settings.theme].moveWindow(hidden);
                    }
                });
                $( '#cometchat' ).on( 'click', '.cometchat_closewindow', function () {
                    id = this.id;
                    id = id.split("_");
                    jqcc[settings.theme].moveWindow($('#'+id[0]+'_window'));
                });
                $( '#cometchat_header' ).on( 'click', '.float_list', function () {
                    id = this.id;
                    id = id.split("_");
                    if (!(jqcc.cometchat.membershipAccess('chatrooms','modules'))){
                        return;
                    }
                    if(jqcc.cometchat.getCcvariable().hasOwnProperty('loggedinusertype') && jqcc.cometchat.getCcvariable().loggedinusertype == 'guestuser' && allowGuests == 0 && (id[2] != 'undefined' && id[2] == 'NewGroup')){
                        alert(language['create_group_guest']);
                        return;
                    }
                    $('#cometchat_header').find('#cometchat_smallmenu').removeClass('rotated');
                    $('#cometchat_header').find('#cometchat_newcompose').removeClass('rotated');
                    $( '#cometchat_header' ).find('#cometchat_smallmenu_float_list').remove();
                    $( '#cometchat_header' ).find('#cometchat_newcompose_float_list').remove();
                    switch(id[2]){
                        case language['new_group'].replace(/\s+/, ""):
                            var hidden = $('#composechat_window');
                            $('.cometchat_float_list').hide();
                            jqcc[settings.theme].moveWindow(hidden);
                            break;
                        case language[113].replace(/\s+/, ""):
                            if (jqcc.cometchat.membershipAccess('broadcastmessage','modules')){
                                var hidden = $('#compose_window');
                                $('.cometchat_float_list').hide();
                                var content = '<iframe style="border-top:1px solid #D1D1D1;height:100%;" frameborder="0" width="300" src="'+baseUrl+'modules/broadcastmessage/index.php?cc_layout=embedded&id=1&basedata='+jqcc.cometchat.getBaseData()+'"></iframe>';
                                $('#compose_window').find('.cometchat_side_container').css('height','calc(100% - 46px)');
                                $('#compose_window').find('.cometchat_side_container').html(content);
                                jqcc[settings.theme].moveWindow(hidden);
                            }
                            break;
                        case language['more'].replace(/\s+/, ""):
                            var hidden = $('#more_window');
                            jqcc[settings.theme].moveWindow(hidden);
                            break;
                        case language['announcements'].replace(/\s+/, ""):
                            var hidden = $('#notification_window');
                            var announcementcontent = '<iframe src="'+baseUrl+'modules/announcements/index.php?cc_layout=embedded&basedata=null" frameborder="0" style="height:calc(100% - 48px);"></iframe>';
                            $('#notification_window').find('#notification_container').html(announcementcontent);
                            jqcc[settings.theme].moveWindow(hidden);
                            break;
                    }
                });

                $.fn.rotate = function(degrees) {
                    $(this).css({'transform' : 'rotate('+ degrees +'deg)','-ms-transform': 'rotate('+ degrees +'deg)','-webkit-transform': 'rotate('+ degrees +'deg)'});
                };
                $(document).mouseup(function (e){
                    var container = $(".cometchat_float_list");
                    if (!container.is(e.target) && container.has(e.target).length === 0 && (!$('.cometchat_pluginrightarrow').is(e.target) && $('.cometchat_pluginrightarrow').has(e.target).length === 0) && (!$('.cometchat_pluginuparrow').is(e.target) && $('.cometchat_pluginuparrow').has(e.target).length === 0) && (!$('#cometchat_smallmenu').is(e.target) && $('#cometchat_smallmenu').has(e.target).length === 0) && (!$('#cometchat_newcompose').is(e.target) && $('#cometchat_newcompose').has(e.target).length === 0) ){
                        container.hide();
                        $('.cometchat_pluginuparrow').removeClass('rotated').css({'transform' : 'rotate(0deg)','-ms-transform': 'rotate(0deg)','-webkit-transform': 'rotate(0deg)'});
                        $('#cometchat_newcompose').removeClass('rotated');
                        $('.cometchat_pluginrightarrow').removeClass('rotated').css({'transform' : 'rotate(0deg)','-ms-transform': 'rotate(0deg)','-webkit-transform': 'rotate(0deg)'});
                        $('#main_container').find('.cometchat_pluginrightarrow').removeClass('rotated');
                        $('#cometchat_header').find('#cometchat_smallmenu').removeClass('rotated');
                        $('#cometchat_header').find('#cometchat_newcompose').removeClass('rotated');
                        $( '#cometchat_header' ).find('#cometchat_smallmenu_float_list').remove();
                        $( '#cometchat_header' ).find('#cometchat_newcompose_float_list').remove();
                        rotation = 0;
                        $('#main_container').find('.cometchat_pluginrightarrow').rotate(rotation);
                        jqcc[settings.theme].downWindow(e);
                    }
                });
                /*/////////////////////////clicksend/////////////*/
                $('.cometchat_openmobiletab').click(function(event){
                    var url = jqcc.cometchat.getBaseUrl()+'cometchat_popout.php?cookiePrefix='+cookiePrefix+'&basedata='+jqcc.cometchat.getBaseData()+'&ccmobileauth='+jqcc.cometchat.getThemeVariable('ccmobileauth');
                    jqcc.ccmobiletab.openWebapp(url);
                });
                $('.cometchat_trayiconimage').click(function(event){
                    event.stopImmediatePropagation();
                    var moduleName = $(this).attr('name');
                    var windowMode = 0;
                    if(jqcc.cometchat.getCcvariable().callbackfn=='desktop' || mobileDevice){
                        windowMode = 1;
                    }
                    if(moduleName == 'home') {
                        if(typeof settings.ccsiteurl != "undefined" && settings.ccsiteurl != "") {
                            window.location = settings.ccsiteurl;
                        } else {
                            window.location = "/";
                        }
                    } else if(window.top == window.self || jqcc.cometchat.getCcvariable().callbackfn=='desktop') {
                        jqcc.cometchat.lightbox(moduleName,'',windowMode);
                    } else {
                        var controlparameters = {"type":"modules", "name":"cometchat", "method":"lightbox", "params":{"moduleName":moduleName, "caller":"cometchat_embedded_iframe"}};
                        controlparameters = JSON.stringify(controlparameters);
                        parent.postMessage('CC^CONTROL_'+controlparameters,'*');
                    }

                });
                document.onmousemove = function(){
                    var nowTime = new Date();
                    jqcc.cometchat.setThemeVariable('idleTime', Math.floor(nowTime.getTime()/1000));
                };
                var extlength = settings.extensions.length;
                if(extlength>0){
                    for(var i = 0; i<extlength; i++){
                        var name = 'cc'+settings.extensions[i];
                        if(typeof ($[name])=='object' && name != 'ccbots'){
                            $[name].init();
                        }
                    }
                }
                if($.inArray('block', settings.plugins)>-1){
                    $.ccblock.addCode();
                }

                $('#cometchat_userscontent').on('DOMMouseScroll mousewheel', function(event){
                    clearTimeout($.data(this, 'timer'));
                    $.data(this, 'timer', setTimeout(function() {
                            jqcc[settings.theme].calcPrevNoti();
                            jqcc[settings.theme].calcNextNoti();
                    }, 250));
                });
                $('#cometchat_userstab_popup').find('.cometchat_tabcontent').on('mouseup', function(event){
                            jqcc[settings.theme].calcPrevNoti();
                            jqcc[settings.theme].calcNextNoti();
                });
                $('#cometchat_userstab_popup').find('.cometchat_tabcontent').on('mousedown', function(event){
                            jqcc[settings.theme].calcPrevNoti();
                            jqcc[settings.theme].calcNextNoti();
                });
                $('#cc_gotoPrevNoti').click(function(event){
                    var mindiff = 0;
                    var bar =$('#cometchat_userstab_popup').find('.cometchat_tabcontent');
                    var cometchat_userslist = $('#cometchat_userslist');
                    var cometchat_activechatboxes_popup = $('#cometchat_activechatboxes_popup');
                    var fullheight = cometchat_userslist.outerHeight() + cometchat_activechatboxes_popup.outerHeight() - 75;
                    var cometchat_userscontent = $('#cometchat_userscontent');
                    var cometchat_userscontent_ht = cometchat_userscontent.outerHeight();
                    var railMinusBarHt =  (cometchat_userscontent_ht-bar.outerHeight());
                    var percentScroll =  parseFloat(bar.css('top')) / railMinusBarHt;
                    var heightScrolled = parseFloat(percentScroll*fullheight)-(cometchat_userscontent_ht*percentScroll);
                    var userHeight = $('.cometchat_userlist').outerHeight();
                    var grpDividerHeight = $(".cometchat_subsubtitle").outerHeight(true);
                    var scrolltomsg;
                    $('.cometchat_userlist').each(function(){
                        var diff = 0;
                        if($(this).find('.cometchat_msgcount').length>0){
                            var userHeightFromTop = 0;
                            activeChatboxesHeight = ($(this).parents().prevAll('#cometchat_activechatboxes_popup').outerHeight());
                            if(typeof(activeChatboxesHeight) != "number"){
                                activeChatboxesHeight =0;
                            }
                            userHeightFromTop = (userHeight * ($(this).prevAll('.cometchat_userlist').length)) + (grpDividerHeight *$(this).prevAll('.cometchat_subsubtitle').length)+ (grpDividerHeight *$(this).parent().prevAll('.cometchat_subsubtitle').length) + activeChatboxesHeight;
                            diff = Math.round(heightScrolled - userHeightFromTop);
                            if((diff > 0 && diff < mindiff)||(diff > 0 && mindiff == 0)){
                                mindiff = Math.round(diff) ;
                                scrolltomsg = userHeightFromTop;
                            }
                        }
                    });
                    if(mindiff > 0){
                        scrolltomsg = (scrolltomsg  < 0)?0:scrolltomsg;
                        cometchat_userscontent.scrollTop(scrolltomsg);
                        var newpercentScroll = scrolltomsg/fullheight ;
                        var bartop = newpercentScroll*cometchat_userscontent_ht;
                        bartop = (bartop > railMinusBarHt)?railMinusBarHt:bartop;
                        bar.css('top',bartop+'px');
                        jqcc[settings.theme].calcPrevNoti();
                        jqcc[settings.theme].calcNextNoti();
                    }
                    jqcc[settings.theme].calcPrevNoti();
                    jqcc[settings.theme].calcNextNoti();
                });
                $('#cc_gotoNextNoti').click(function(event){
                    var mindiff = 0;
                    var bar =$('#cometchat_userstab_popup').find('.cometchat_tabcontent');
                    var cometchat_userslist = $('#cometchat_userslist');
                    var cometchat_activechatboxes_popup = $('#cometchat_activechatboxes_popup');
                    var fullheight = cometchat_userslist.outerHeight() + cometchat_activechatboxes_popup.outerHeight() - 75;
                    var cometchat_userscontent = $('#cometchat_userscontent');
                    var cometchat_userscontent_ht = cometchat_userscontent.outerHeight();
                    var railMinusBarHt =  (cometchat_userscontent_ht-bar.outerHeight());
                    var percentScroll =  parseFloat(bar.css('top')) / railMinusBarHt;
                    var heightScrolled = parseFloat(percentScroll*fullheight)+(cometchat_userscontent_ht*(1-percentScroll));
                    var userHeight = $('.cometchat_userlist').outerHeight();
                    var grpDividerHeight = $(".cometchat_subsubtitle").outerHeight(true);
                    var scrolltomsg = 0 ;
                    $('.cometchat_userlist').each(function(){
                        var diff = 0;
                        if($(this).find('.cometchat_msgcount').length>0){
                            var userHeightFromTop = 0;
                            activeChatboxesHeight = ($(this).parents().prevAll('#cometchat_activechatboxes_popup').outerHeight());
                            if(typeof(activeChatboxesHeight) != "number"){
                                activeChatboxesHeight =0;
                            }
                            userHeightFromTop = (userHeight * ($(this).prevAll('.cometchat_userlist').length)) + (grpDividerHeight *$(this).prevAll('.cometchat_subsubtitle').length)+ (grpDividerHeight *$(this).parent().prevAll('.cometchat_subsubtitle').length) + activeChatboxesHeight;
                            diff = Math.round(userHeightFromTop - heightScrolled + userHeight);
                            if((diff > 0 && diff < mindiff)||(diff > 0 && mindiff == 0)){
                                mindiff = diff;
                                scrolltomsg = userHeightFromTop;
                            }
                        }
                    });
                    if(mindiff >0){
                        scrolltomsg = (scrolltomsg  > fullheight)?fullheight:scrolltomsg;
                        cometchat_userscontent.scrollTop(scrolltomsg);
                        var newpercentScroll = scrolltomsg/fullheight ;
                        var bartop = newpercentScroll*cometchat_userscontent_ht;
                        bartop = (bartop > railMinusBarHt)?railMinusBarHt:bartop;
                        bar.css('top',bartop+'px');
                        jqcc[settings.theme].calcPrevNoti();
                        jqcc[settings.theme].calcNextNoti();
                    }
                });
                $('.cometchat_offline_overlay').click(function(){
                    $('.cometchat_offline_overlay').css('display','none');
                    if(jqcc.cometchat.getThemeVariable('offline')==1){
                        jqcc.cometchat.setThemeVariable('offline', 0);
                        jqcc.cometchat.setSessionVariable('offline', 0);
                        jqcc.cometchat.setThemeArray('buddylistStatus', jqcc.cometchat.getThemeVariable('userid'), 'available');
                        $('#cometchat_userstab_text').html(language[9]+' ('+jqcc.cometchat.getThemeVariable('lastOnlineNumber')+')');
                        jqcc.cometchat.chatHeartbeat(1);
                        jqcc.cometchat.sendStatus('available');
                        $('.cometchat_noactivity').css('display','block');
                        if(chatroomsonly == 1 && !($('#cometchat_chatroomstab_popup').hasClass("cometchat_tabopen"))){
                            $('#cometchat_chatroomstab_popup').addClass('cometchat_tabopen');
                        } else {
                            $('#cometchat_userstab').click();
                        }
                    }
                });
                if($.cookie(settings.cookiePrefix+"disablemessagereceipt")){
                    if($.cookie(settings.cookiePrefix+"disablemessagereceipt")==1){
                        jqcc.cometchat.setExternalVariable('messagereceiptsetting', 1);
                    }
                }
                jqcc[settings.theme].calcPrevNoti();
                jqcc[settings.theme].calcNextNoti();
                $('#cometchat').find('.cometchat_closeboxsearch').click(function(e){
                    e.stopImmediatePropagation();
                    $('#cometchat').find('#cometchat_user_search').val('');
                    $('#cometchat').find('#cometchat_user_search').keyup();
                });

                $('#cometchat_righttab').on('click','.cometchat_userchatbox',function(){
                    var id = $(this).attr('id');
                    id = id.substring(15, id.length-6);
                    jqcc[settings.theme].hideLastseen(id);
                });

                $('#cometchat_righttab').on('mouseleave','.cometchat_userchatbox',function(){
                    var id = $(this).attr('id');
                    id = id.substring(15, id.length-6);
                    var buddylastseen = jqcc.cometchat.getThemeArray('buddylistLastseen', id);
                    var statusmsg = jqcc.cometchat.getThemeArray('buddylistStatus', id);
                    var lstsnSetting = jqcc.cometchat.getThemeArray('buddylistLastseensetting', id);
                    var ishidden = jqcc('.cometchat_userchatbox').find('#cometchat_messageElement_'+id).is(':hidden');
                    var cookievalue = $.cookie(settings.cookiePrefix+"disablelastseen");
                    var currentts = Math.floor(new Date().getTime()/1000);
                    if(ishidden && cookievalue == 'false' && ((statusmsg == 'away' || statusmsg == 'invisible' || statusmsg == 'busy' || statusmsg == 'offline') || currentts-buddylastseen > (60*10)) && lstsnSetting == '0' && $('#cometchat_messageElement_'+id).html() != ""){
                        $('#cometchat_messageElement_'+id).slideDown(500);
                    }else if(statusmsg != 'available' && buddylastseen == "" && $('#cometchat_messageElement_'+id).html() != ""){
                        $('#cometchat_messageElement_'+id).slideDown(500);
                    }
                });
                window.onoffline = function() {
                    jqcc.embedded.noInternetConnection(true);
                }
                window.ononline = function() {
                    jqcc.embedded.noInternetConnection(false);
                }
                function updateOnlineStatus() {
                    var noInternetConnection = navigator.onLine ? false : true;
                    jqcc.docked.noInternetConnection(noInternetConnection);
                }

                document.body.addEventListener("offline", function () { updateOnlineStatus() }, false);
                document.body.addEventListener("online", function () { updateOnlineStatus() }, false);
            },
            /*//////////////////////////////////functions/////////////////////////////////*/
            create_dropdownpopup: function(id, target, list,arrow,ismodule,toid){
                var divname = id+'_float_list';
                var count = 1;
                if(list.length == 2)
                    count = 3;
                var content = '<div id="'+divname+'" class="cometchat_float_list"><div class="cometchat_arrow-'+arrow+'"></div>';
                if(arrow == 'up' && jqcc.cometchat.getThemeArray('buddylistLink', toid)){
                    content += '<div class="cometchat_float_outer cometchat_view_profile"><div id="float_view_profile" class="float_list list_'+arrow+'">'+language["view_profile"]+'</div></div>';
                }
                $.each(list, function(i){
                    if(ismodule){
                        content += '<div class="cometchat_float_outer"><div id="cometchat_float_'+list[i]+'" class="ccplugins cometchat_cc'+list[i]+' float_list list_'+arrow+'" title="'+$['cc'+list[i]].getTitle()+'" name="cc'+list[i]+'" to="'+toid+'" chatroommode="0">'+$['cc'+list[i]].getTitle()+'</div></div>';
                    }else{
                        var pluginname = list[i].replace(/\s+/, "");
                        content += '<div class="cometchat_float_outer"><div id="cometchat_float_'+pluginname+'" class="float_list list_'+arrow+'">'+list[i]+'</div></div>';
                        count++;
                    }
                });
                return content+'</div>';
            },
            moveWindow: function(id,open){
                if (id.hasClass('visible')){
                    $('.cometchat_container').css('z-index','1000000');
                    if(rtl == 1){
                        id.animate({"right":'100%'}, "fast").removeClass('visible');
                    }else{
                        id.animate({"left":'100%'}, "fast").removeClass('visible');
                    }
                }else{
                    $('.cometchat_container').css('z-index','11');
                    var left = id.offset().left;
                    if(rtl == 1){
                        id.css({right:$(window).width()}).animate({"right":$(document).width()-300+'px'}, "fast").addClass('visible');
                    }else{
                        id.css({left:left}).animate({"left":$(document).width()-300+'px'}, "fast").addClass('visible');
                    }
                }
            },
            adjustIcons: function(width,cometchat_div,id){
                var chathtml = '';

                if(width < 300 && cometchat_div.find('#cometchat_audiochaticon').length > 0){
                    cometchat_div.find('#cometchat_audiochaticon').remove();
                }
                if(width < 276 && cometchat_div.find('#cometchat_videochaticon').length > 0){
                    cometchat_div.find('#cometchat_videochaticon').remove();
                }
                if(width > 276 && cometchat_div.find('#cometchat_videochaticon').length == 0 && $.inArray('avchat',settings.plugins) != -1){
                    var name = 'ccavchat';
                    chathtml = '<div class="ccplugins " id="cometchat_videochaticon" title="'+$[name].getTitle()+'" name="'+name+'" to="'+id+'" chatroommode="0"></div>';
                }
                if(width > 300 && cometchat_div.find('#cometchat_audiochaticon').length == 0 && $.inArray('audiochat',settings.plugins) != -1){
                    var name = 'ccaudiochat';
                    chathtml = '<div class="ccplugins" id="cometchat_audiochaticon" title="'+$[name].getTitle()+'" name="'+name+'" to="'+id+'" chatroommode="0"></div>';
                }
                cometchat_div.find('#cometchat_pluginsonheader').prepend(chathtml);
            },
            downWindow: function(e){
                if($(e.target).hasClass("cometchat_textarea")){
                    e.stopImmediatePropagation();
                }else {
                    if($('.cometchat_container').length > 0){
                        cometchat_container = $('.cometchat_container');
                        cometchat_container.each(function(){
                            var divid = $(this).attr('id');
                            id = divid.split("_");
                            if(id[2] == 'smilies' || id[2] == 'stickers' || id[2] == 'transliterate' || id[2] == 'voicenote'){
                                $(this).removeClass('visible').slideUp("slow");
                                jqcc("#cometchat_tooltip").css('display', 'none');
                                setTimeout(function() {
                                    $('#'+divid).remove();
                                    jqcc[settings.theme].windowResize();
                                },500);
                                window.onbeforeunload = null;
                            }
                        });
                    }
                }
            },
            create_side_window: function(id,title,content){
                var sidewindow = '<div id="'+id+'_window" class="cometchat_windows"><div id="cometchat_windowtitlebar">'+title+'<div id="'+id+'_closewindow" class="cometchat_closewindow" ><img src="<?php echo STATIC_CDN_URL; ?>layouts/<?php echo $layout;?>/images/removewhite.svg"/></div></div><div id="'+id+'_container" class="cometchat_side_container">'+content+'</div></div>';
                return sidewindow;
            },

            calcNextNoti: function(){
                var mindiff = 0;
                var bar =$('#cometchat_userstab_popup').find('.cometchat_tabcontent');
                var cometchat_userslist = $('#cometchat_userslist');
                var cometchat_activechatboxes_popup = $('#cometchat_activechatboxes_popup');
                var fullheight = cometchat_userslist.outerHeight() + cometchat_activechatboxes_popup.outerHeight() - 75;
                var cometchat_userscontent = $('#cometchat_userscontent');
                var cometchat_userscontent_ht = cometchat_userscontent.outerHeight();
                var railMinusBarHt =  (cometchat_userscontent_ht-bar.outerHeight());
                var percentScroll =  parseFloat(bar.css('top')) / railMinusBarHt;
                var heightScrolled = parseFloat(percentScroll*fullheight)+(cometchat_userscontent_ht*(1-percentScroll));
                var userHeight = $('.cometchat_userlist').outerHeight();
                var grpDividerHeight = $(".cometchat_subsubtitle").outerHeight(true);
                $('.cometchat_userlist').each(function(){
                    var diff = 0;
                    if($(this).find('.cometchat_msgcount').length>0){
                        var userHeightFromTop = 0;
                        activeChatboxesHeight = ($(this).parents().prevAll('#cometchat_activechatboxes_popup').outerHeight());
                        if(typeof(activeChatboxesHeight) != "number"){
                            activeChatboxesHeight =0;
                        }
                        userHeightFromTop = (userHeight * ($(this).prevAll('.cometchat_userlist').length)) + (grpDividerHeight *$(this).prevAll('.cometchat_subsubtitle').length)+ (grpDividerHeight *$(this).parent().prevAll('.cometchat_subsubtitle').length) + activeChatboxesHeight;
                        diff = Math.round(userHeightFromTop - heightScrolled);
                        if((diff > 0 && diff < mindiff && userHeightFromTop > cometchat_userscontent_ht)||(diff > 0 && mindiff == 0 && userHeightFromTop > cometchat_userscontent_ht)){
                            mindiff = diff;
                        }
                    }
                });
                if(mindiff<=0){
                    $("#cc_gotoNextNoti").hide();
                }else{
                    $("#cc_gotoNextNoti").show();
                }
            },
            calcPrevNoti: function(){
                var mindiff = 0;
                var bar =$('#cometchat_userstab_popup').find('.cometchat_tabcontent');
                var cometchat_userslist = $('#cometchat_userslist');
                var cometchat_activechatboxes_popup = $('#cometchat_activechatboxes_popup');
                var fullheight = cometchat_userslist.outerHeight() + cometchat_activechatboxes_popup.outerHeight() - 75;
                var cometchat_userscontent = $('#cometchat_userscontent');
                var cometchat_userscontent_ht = cometchat_userscontent.outerHeight();
                var railMinusBarHt =  (cometchat_userscontent_ht-bar.outerHeight());
                var percentScroll =  parseFloat(bar.css('top')) / railMinusBarHt;
                var heightScrolled = parseFloat(percentScroll*fullheight)-(cometchat_userscontent_ht*percentScroll);
                var userHeight = $('.cometchat_userlist').outerHeight();
                var grpDividerHeight = $(".cometchat_subsubtitle").outerHeight(true);
                $('.cometchat_userlist').each(function(){
                    var diff = 0;
                    if($(this).find('.cometchat_msgcount').length>0){

                        var userHeightFromTop = 0;
                        activeChatboxesHeight = ($(this).parents().prevAll('#cometchat_activechatboxes_popup').outerHeight());
                        if(typeof(activeChatboxesHeight) != "number"){
                            activeChatboxesHeight =0;
                        }
                        userHeightFromTop = (userHeight * ($(this).prevAll('.cometchat_userlist').length)) + (grpDividerHeight *$(this).prevAll('.cometchat_subsubtitle').length)+ (grpDividerHeight *$(this).parent().prevAll('.cometchat_subsubtitle').length) + activeChatboxesHeight;
                        diff = Math.round(heightScrolled - userHeightFromTop);
                        if((diff > 0 && diff < mindiff)||(diff > 0 && mindiff == 0)){
                            mindiff = Math.round(diff) ;
                        }
                    }
                });
                if(mindiff<=0){
                    $("#cc_gotoPrevNoti").hide();
                }else{
                    $("#cc_gotoPrevNoti").show();
                }
            },
            newAnnouncement: function(item){
                if($.cookie(settings.cookiePrefix+"popup")&&$.cookie(settings.cookiePrefix+"popup")=='true'){
                    tooltipPriority = 100;
                    var ann = jqcc.cometchat.getFromStorage('ann');
                    var arr = new Array(100);
                    if(ann){
                        arr[0] = ann;
                        arr[1] = item;
                        jqcc.cometchat.updateToStorage('ann',arr);
                    }else{
                        jqcc.cometchat.updateToStorage('ann',item);
                    }
                    message = '<div>'+item.m+'</div>';
                    $('#announcementcontent').append(message);
                    if(item.o){
                        var notifications = (item.o-1);
                        if(notifications>0){
                            message += '<div class="cometchat_notification" onclick="javascript:jqcc.cometchat.launchModule(\'announcements\')"><div class="cometchat_notification_message cometchat_notification_message_and">'+language[36]+notifications+language[37]+'</div><div style="clear:both" /></div>';
                        }
                    }else{
                        $.cookie(settings.cookiePrefix+"an", item.id, {path: '/', expires: 365});
                    }
                    if((typeof $.cookie(settings.cookiePrefix+"sound") == 'undefined' || $.cookie(settings.cookiePrefix+"sound") == null) || $.cookie(settings.cookiePrefix+"sound")=='true'){
                        jqcc[settings.theme].playSound(2);
                    }
                    jqcc[settings.theme].tooltip("cometchat_userstab", message, 0);
                    notificationTimer = setTimeout(function(){
                        $('#cometchat_tooltip').css('display', 'none');
                        tooltipPriority = 0;
                    }, settings.announcementTime);
                }
            },
            buddyList: function(item){
                var onlineNumber = 0;
                var totalFriendsNumber = 0;
                var groupNumber = 0;
                var tooltipMessage = '';
                var buddylisttemp = '';
                var buddylisttempavatar = '';
                $.each(item, function(i, buddy){
                    if(buddy.n == null || buddy.n == 'null' || buddy.n == '') {
                        return;
                    }
                    longname = buddy.n;
                    if(settings.lastseen == 1 && jqcc.cometchat.getThemeArray('buddylistLastseensetting',jqcc.cometchat.getUserID())==0){
                        if(buddy.lstn == 1 && $.cookie(settings.cookiePrefix+"disablelastseen") == 'true'){
                           lastseenflag = true;
                        }else if($.cookie(settings.cookiePrefix+"disablelastseen") == 'true'){
                            lastseenflag = true;
                        }else if(buddy.lstn == 1){
                            lastseenflag = true;
                        }
                        else{
                            lastseenflag = false;
                        }
                        if(lastseenflag){
                            jqcc[settings.theme].hideLastseen(buddy.id);
                        } else if(!lastseenflag){
                            if(Math.floor(new Date().getTime()/1000)-buddy.ls < (60*10) && buddy.s == 'available'){
                                    jqcc[settings.theme].hideLastseen(buddy.id);
                            }
                            else if(((buddy.s == 'away' || buddy.s == 'invisible' || buddy.s == 'busy' || buddy.s == 'offline') || Math.floor(new Date().getTime()/1000)-buddy.ls > (60*10)) && buddy.lstn == 0){

                                jqcc[settings.theme].showLastseen(buddy.id, buddy.ls);
                            }else{
                                $('#cometchat_messageElement_'+buddy.id).hide();
                            }
                        }
                    }

                    var usercontentstatus = buddy.s;
                    var icon = '';
                    if(buddy.d==1){
                        mobilestatus = 'mobile';
                        usercontentstatus = 'mobile cometchat_mobile_'+buddy.s;
                    }
                    if(chatboxOpened[buddy.id]!=null){
                        $("#cometchat_user_"+buddy.id+"_popup").find("span.cometchat_userscontentdot")
                            .removeClass("cometchat_available")
                            .removeClass("cometchat_busy")
                            .removeClass("cometchat_offline")
                            .removeClass("cometchat_away")
                            .removeClass("cometchat_blocked")
                            .removeClass("cometchat_mobile")
                            .removeClass("cometchat_mobile_available")
                            .removeClass("cometchat_mobile_busy")
                            .removeClass("cometchat_mobile_offline")
                            .removeClass("cometchat_mobile_away")
                            .addClass("cometchat_"+usercontentstatus);
                        if(icon == ''){
                            $("#cometchat_user_"+buddy.id+"_popup").find("div.cometchat_dot").remove();
                        }else if($("#cometchat_user_"+buddy.id+"_popup").find("div.cometchat_dot").length<1){
                            $("#cometchat_user_"+buddy.id+"_popup").find("span.cometchat_userscontentdot").append(icon);
                        }
                        if(buddy.s!='blocked'){
                             $("#cometchat_user_"+buddy.id+"_popup").find("div.cometchat_blocked_overlay").remove();
                        }
                        if($("#cometchat_user_"+buddy.id+"_popup").length>0){
                            $("#cometchat_user_"+buddy.id+"_popup").find("div.cometchat_userdisplaystatus").html(buddy.m);
                        }
                    }
                    if(buddy.s!='offline'){
                        onlineNumber++;
                    }
                    totalFriendsNumber++;
                    var group = '';

                    var overlay_div = '';
                    if(buddy.s=="blocked"){
                        overlay_div = '<div class="cometchat_blocked_overlay"></div>';
                    }

                    unreadmessagecount = 0;
                    msgcountercss = "display:none;";
                    if(typeof(jqcc.cometchat.getThemeArray('buddylistUnreadMessageCount', buddy.id)) != "undefined" && jqcc.cometchat.getThemeArray('buddylistUnreadMessageCount', buddy.id) != '') {
                        unreadmessagecount = jqcc.cometchat.getThemeArray('buddylistUnreadMessageCount', buddy.id);
                        msgcountercss = "";
                    }

                    if((buddy.s != 'offline' && settings.hideOffline == 1) || settings.hideOffline == 0){
                        buddylisttemp += group+'<div id="cometchat_userlist_'+buddy.id+'" class="cometchat_userlist" onmouseover="jqcc(this).addClass(\'cometchat_userlist_hover\');" onmouseout="jqcc(this).removeClass(\'cometchat_userlist_hover\');" amount="'+unreadmessagecount+'"><span class="cometchat_userscontentavatar">'+overlay_div+'<img class="cometchat_userscontentavatarimage" src="'+staticCDNUrl+'layouts/'+settings.theme+'/images/cometchat_'+buddy.s+'.png"></span><div class="cometchat_chatboxDisplayDetails"><div class="cometchat_userdisplayname">'+longname+'</div><span id="cometchat_buddylist_typing_'+buddy.id+'" class="cometchat_buddylist_typing"></span><span class="cometchat_userscontentdot cometchat_'+usercontentstatus+'">'+icon+'</span><div class="cometchat_userdisplaystatus">'+buddy.m+'</div></div><span class="cometchat_msgcount"><div class="cometchat_msgcounttext" style="'+msgcountercss+'">'+unreadmessagecount+'</div></span></div>';
                        buddylisttempavatar += group+'<div id="cometchat_userlist_'+buddy.id+'" class="cometchat_userlist" onmouseover="jqcc(this).addClass(\'cometchat_userlist_hover\');" onmouseout="jqcc(this).removeClass(\'cometchat_userlist_hover\');" amount="'+unreadmessagecount+'"><span class="cometchat_userscontentavatar">'+overlay_div+'<img class="cometchat_userscontentavatarimage" src="'+buddy.a+'"></span><div class="cometchat_chatboxDisplayDetails"><div class="cometchat_userdisplayname">'+longname+'</div><span id="cometchat_buddylist_typing_'+buddy.id+'" class="cometchat_buddylist_typing"></span><span class="cometchat_userscontentdot cometchat_'+usercontentstatus+'">'+icon+'</span><div class="cometchat_userdisplaystatus">'+buddy.m+'</div></div><span class="cometchat_msgcount"><div class="cometchat_msgcounttext" style="'+msgcountercss+'">'+unreadmessagecount+'</div></span></div>';
                    }
                    var message = '';
                    if(settings.displayOnlineNotification==1&&jqcc.cometchat.getExternalVariable('initialize')!=1&&jqcc.cometchat.getThemeArray('buddylistStatus', buddy.id)!=buddy.s&&buddy.s=='available'){
                        message = language[19];
                    }
                    if(settings.displayBusyNotification==1&&jqcc.cometchat.getExternalVariable('initialize')!=1&&jqcc.cometchat.getThemeArray('buddylistStatus', buddy.id)!=buddy.s&&buddy.s=='busy'){
                        message = language[21];
                    }
                    if(settings.displayOfflineNotification==1&&jqcc.cometchat.getExternalVariable('initialize')!=1&&jqcc.cometchat.getThemeArray('buddylistStatus', buddy.id)!='offline'&&buddy.s=='offline'){
                        message = language[20];
                    }
                    if(message!=''){
                        tooltipMessage += '<div class="cometchat_notification" onclick="javascript:jqcc.cometchat.chatWith(\''+buddy.id+'\')"><div class="cometchat_notification_avatar"><img class="cometchat_notification_avatar_image" src="'+buddy.a+'"></div><div class="cometchat_notification_message"><div class="cometchat_notification_uname">'+buddy.n+'&nbsp;</div>'+message+'</div><div class="cometchat_notification_status">'+buddy.m+'</div><div style="clear:both" /></div>';
                    }
                    jqcc.cometchat.addBuddy(buddy);
                });
                if(groupNumber>0){
                    $('.cometchat_subsubtitle_siteusers').css('display', 'none');
                }
                var bltemp = buddylisttempavatar;
                jqcc.cometchat.setThemeVariable('showAvatar','1');
                if(totalFriendsNumber>settings.thumbnailDisplayNumber){
                    bltemp = buddylisttemp;
                    jqcc.cometchat.setThemeVariable('showAvatar','0');
                }
                if(document.getElementById('cometchat_userslist')){
                    if(bltemp!=''){
                        document.getElementById('cometchat_userslist').style.display = 'block';
                        jqcc.cometchat.replaceHtml('cometchat_userslist', '<div>'+bltemp+'</div>');
                    }else{
                        $('#cometchat_userslist').html('<div class="cometchat_nofriends">'+language[14]+'</div>');
                        document.getElementById('cometchat_userslist').style.display = 'block';
                    }
                }
                if(totalFriendsNumber>settings.thumbnailDisplayNumber){
                    jqcc('#cometchat_userslist').find('.cometchat_blocked_overlay').remove();
                    jqcc('#cometchat_userslist').find('.cometchat_blocked').remove();
                }
                if(jqcc.cometchat.getSessionVariable('buddylist')==1){
                    $(".cometchat_userscontentavatar").find("img").each(function(){
                        if($(this).attr('original')){
                            $(this).attr("src", $(this).attr('original'));
                            $(this).removeAttr('original');
                        }
                    });
                }
                jqcc[settings.theme].activeChatBoxes();
                $("#cometchat_user_search").keyup();
                $('div.cometchat_userlist').die('click');
                $('div.cometchat_userlist').live('click', function(e){
                    jqcc.cometchat.userClick(this,0);
                });

                siteOnlineNumber = onlineNumber;
                jqcc.cometchat.setThemeVariable('lastOnlineNumber', onlineNumber+jqcc.cometchat.getThemeVariable('jabberOnlineNumber'));
                if(totalFriendsNumber+jqcc.cometchat.getThemeVariable('jabberOnlineNumber')>settings.searchDisplayNumber){
                    $('#cometchat_user_searchbar').css('display', 'block');
                }else{
                    $('#cometchat_user_searchbar').css('display', 'none');
                }
                if(tooltipMessage!=''&&!$('#cometchat_userstab_popup').hasClass('cometchat_tabopen')&&!$('#cometchat_optionsbutton_popup').hasClass('cometchat_tabopen')){
                    if(tooltipPriority<10){
                        if($.cookie(settings.cookiePrefix+"popup")&&$.cookie(settings.cookiePrefix+"popup")=='true'){
                            tooltipPriority = 108;
                            jqcc[settings.theme].tooltip("cometchat_userstab", tooltipMessage, 0);
                            clearTimeout(notificationTimer);
                            notificationTimer = setTimeout(function(){
                                $('#cometchat_tooltip').css('display', 'none');
                                tooltipPriority = 0;
                            }, settings.notificationTime);
                        }
                    }
                }
            },
            recentList: function(item){
                var groupNumber = 0;
                var tooltipMessage = '';
                var recentlisttemp = '';
                var userCountCss = "style='display:none'";
                var recentmessage = '';
                var crUnreadMessages = jqcc.cometchat.getChatroomVars('crUnreadMessages');
                var availablegroups = Object.keys(jqcc.cometchat.getChatroomVars('chatroomdetails'));
                if(jqcc.cometchat.getChatroomVars('showChatroomUsers') == 1){
                    userCountCss = '';
                }
                $.each(item, function(i, chat){
                    if(chat.n == null || chat.n == 'null' || chat.n == '' || typeof(chat.m) == "undefined") {
                        return;
                    }

                    recentmessage = chat.m;

                    longname = shortname = chat.n;

                    if(chat.grp) {
                        if(availablegroups.indexOf('_'+chat.id) == -1){
                            return;
                        }
                        var selected = '';
                        if(jqcc.cometchat.getChatroomVars('currentroom') == chat.id) {
                            selected = ' cometchat_chatroomselected';
                        }
                        var roomtype = '';
                        var crUnreadMessages = jqcc.cometchat.getChatroomVars('crUnreadMessages');
                        unreadmessagecount = 0;
                        msgcountercss = "display:none;";
                        if(typeof(crUnreadMessages[chat.id])!="undefined" && crUnreadMessages[chat.id]!=''){
                            unreadmessagecount = crUnreadMessages[chat.id];
                            msgcountercss = "";
                        }

                        /*if(chat.type == 1) {
                            roomtype = '<img src="'+staticCDNUrl+'layouts/'+settings.theme+'/images/lock.png" />';
                        }*/

                        if(chat.s == 2) {
                            chat.s = 1;
                        }

                        recentlisttemp += '<div id="cometchat_recentchatroomlist_'+chat.id+'" class="lobby_room'+selected+' cometchat_grouplist" onmouseover="jqcc(this).addClass(\'cometchat_userlist_hover\');" onmouseout="jqcc(this).removeClass(\'cometchat_userlist_hover\');" onclick="javascript:jqcc.cometchat.chatroom(\''+chat.id+'\',\''+cc_urlencode(shortname)+'\',\''+chat.type+'\',\''+chat.pass+'\',\''+chat.s+'\',\'1\',\'1\');" style="display:block !important;" amount="'+unreadmessagecount+'"><span class="cometchat_chatroommsgcount" style="'+msgcountercss+'"><div class="cometchat_chatroommsgcounttext" amount="'+unreadmessagecount+'">'+unreadmessagecount+'</div></span><span class="lobby_room_3">'+roomtype+'</span><span class="lobby_room_5" style="display:none;"></span><div><span class="cometchat_chatroomimage"><img src="<?php echo STATIC_CDN_URL; ?>layouts/<?php echo $layout;?>/images/group.svg"></span><span class="lobby_room_1"><span class="currentroomname">'+longname+'</span><span class="cometchat_grouprecentmessage">'+recentmessage+'</span></span></div></span></div>';
                    } else {
                        var usercontentstatus = chat.s;
                        var icon = '';
                        if(chatboxOpened[chat.id]!=null){
                            if(chat.s!='blocked'){
                                 $("#cometchat_user_"+chat.id+"_popup").find("div.cometchat_blocked_overlay").remove();
                            }
                        }

                        if(typeof(chat.a) == "undefined" || chat.a == ''){
                            chat.a = staticCDNUrl+'images/noavatar.png';
                        }

                        var overlay_div = '';
                        if(chat.s=="blocked"){
                            overlay_div = '<div class="cometchat_blocked_overlay"></div>';
                        }

                        unreadmessagecount = 0;
                        msgcountercss = "display:none;";
                        if(typeof(jqcc.cometchat.getThemeArray('buddylistUnreadMessageCount', chat.id)) != "undefined" && jqcc.cometchat.getThemeArray('buddylistUnreadMessageCount', chat.id) != '') {
                            unreadmessagecount = jqcc.cometchat.getThemeArray('buddylistUnreadMessageCount', chat.id);
                            msgcountercss = "";
                        }

                        if((chat.s != 'offline' && settings.hideOffline == 1) || settings.hideOffline == 0){
                            recentlisttemp += '<div id="cometchat_recentlist_'+chat.id+'" class="cometchat_userlist cometchat_recentchatlist" onmouseover="jqcc(this).addClass(\'cometchat_userlist_hover\');" onmouseout="jqcc(this).removeClass(\'cometchat_userlist_hover\');" amount="'+unreadmessagecount+'"><span class="cometchat_userscontentavatar">'+overlay_div+'<img class="cometchat_userscontentavatarimage" src="'+chat.a+'"></span><div class="cometchat_chatboxDisplayDetails"><div class="cometchat_userdisplayname">'+longname+'</div><span class="cometchat_recentmessage">'+recentmessage+'</span></div><span class="cometchat_msgcount"><div class="cometchat_msgcounttext" style="'+msgcountercss+'">'+unreadmessagecount+'</div></span></div>';
                        }
                    }
                });

                if(document.getElementById('cometchat_recentlist')){
                    if(recentlisttemp!=''){
                        jqcc.cometchat.replaceHtml('cometchat_recentlist', '<div>'+recentlisttemp+'</div>');
                    }else{
                        $('#cometchat_recentlist').html('<div class="cometchat_nofriends">'+language['no_recent_chats']+'</div>');
                    }
                }
                if(jqcc.cometchat.getSessionVariable('buddylist')==1){
                    $(".cometchat_userscontentavatar").find("img").each(function(){
                        if($(this).attr('original')){
                            $(this).attr("src", $(this).attr('original'));
                            $(this).removeAttr('original');
                        }
                    });
                }

                if(jqcc('#currentroom:visible').length<1){
                    var newMessagesCount = jqcc.cometchat.getChatroomVars('newMessages');
                    $('#cometchat_recentchatroomlist_'+jqcc.cometchat.getChatroomVars('currentroom')).find('.cometchat_chatroommsgcounttext').text(newMessagesCount);
                    if(newMessagesCount > 0){
                        $('#cometchat_recentchatroomlist_'+jqcc.cometchat.getChatroomVars('currentroom')).find('.cometchat_chatroommsgcount').show();
                    }
                }

                jqcc[settings.theme].activeChatBoxes();
                $("#cometchat_user_search").keyup();
                $('#cometchat_recentlist div.cometchat_recentchatlist').die('click');
                $('#cometchat_recentlist div.cometchat_recentchatlist').live('click', function(e){
                    jqcc.cometchat.userClick(this,1);
                });
            },
            botList: function(item) {
                var botlisttemp = '';
                var bot_title = language['bot_info'];
                var botwidth= '400';
                var bottop = '' ;
                var botleft = '';
                var bottom = '';


                $.each(item, function(i, bot){
                    botlisttemp += '<div id="cometchat_botlist_'+bot.id+'" class="cometchat_botlist" onmouseover="jqcc(this).addClass(\'cometchat_botlist_hover\');" onmouseout="jqcc(this).removeClass(\'cometchat_botlist_hover\');"><span class="cometchat_botscontentavatar"><img class="cometchat_botscontentavatarimage" src="'+bot.a+'"></span><div class="cometchat_chatboxDisplayDetails"><div class="cometchat_botdisplayname">'+bot.n+' <span id="botrule">@'+bot.n+'</span></div><div class="cometchat_botdisplaystatus">'+bot.d+'</div></div></div>';

                    jqcc.cometchat.setThemeArray('botlistName', bot.id, bot.n);
                    jqcc.cometchat.setThemeArray('botlistAvatar', bot.id, bot.a);
                    jqcc.cometchat.setThemeArray('botlistDescription', bot.id, bot.d);
                    jqcc.cometchat.setThemeArray('botlistApikey', bot.id, bot.api);
                });
                if(document.getElementById('cometchat_botslist')){
                    if(botlisttemp!=''){
                        jqcc.cometchat.replaceHtml('cometchat_botslist', '<div>'+botlisttemp+'</div>');
                    }else{
                        $('#cometchat_botslist').html('<div class="cometchat_nobots">'+language['no_bots']+'</div>');
                    }
                }
                $('div.cometchat_botlist').on('click', function(e){
                    bot_id = $(this).attr('id');
                    bot_id = bot_id.split('_')[2];
                    bot_name = jqcc.cometchat.getThemeArray('botlistName', bot_id);
                    bot_avatar = jqcc.cometchat.getThemeArray('botlistAvatar', bot_id);
                    bot_desc = jqcc.cometchat.getThemeArray('botlistDescription', bot_id);

                    if(bot_avatar.indexOf('size')!=-1){
                        var avatar_size = bot_avatar.split('=')[1];
                        if(avatar_size != '75x75'){
                            avatar_size = '100x100';
                            bot_avatar = bot_avatar.split('=')[0]+'='+avatar_size;
                        }
                    }

                    bottop = (($(window).height() - 100)/ 2) ;
                    botleft = (($(window).width() - botwidth) / 2) + $(window).scrollLeft();
                    if (botleft < 0) { botleft = 0; }
                    if (bottop < 0) { bottop = 0; }
                    botleft = 'left:'+botleft+'px;';
                    bottom = 'bottom:'+bottop+'px;';
                    bottop = 'top:'+bottop+'px;';


                    bot_viewinfo = '<div class="cometchat_botcontainer_'+bot_id+'" id="cometchat_botcontainer" style="'+bottop+botleft+'width:'+botwidth+'px;"><div class="cometchat_botcontainer_title" onmousedown="dragStart(event, \'cometchat_botcontainer\')"><span class="cometchat_botcontainer_name">'+bot_title+'</span><div class="cometchat_closebotsbox cometchat_tooltip" title="'+language[27]+'" id='+bot_id+'></div><div style="clear:both"></div></div><div class="cometchat_botcontainer_body"><div class="cometchat_bot_info"><div id="cometchat_botlist_'+bot_id+'" class="cometchat_botinfo"><div class="cometchat_botdata"><img class="cometchat_botavatarimage" src="'+bot_avatar+'"></div><div style="clear:both"></div></div><div class="cometchat_botname">'+bot_name+'</div><div class="cometchat_botdesc">'+bot_desc+'</div></div></div></div>';

                    if($('#cometchat_botcontainer').length == 0){
                        jqcc("body").append(bot_viewinfo);
                    }else{
                        jqcc("body").find('#cometchat_botcontainer').remove();
                        jqcc("body").append(bot_viewinfo);
                    }

                    if(mobileDevice){
                        $('#cometchat_botcontainer').find('#cometchat_botdesc').css('overflow-y','auto');
                    }

                    if(jqcc().slimScroll){
                        var cometchat_slimscroll_height = $('#cometchat_botcontainer').find('#cometchat_botcontainer_body').height();
                        if(cometchat_slimscroll_height > 143){
                            cometchat_slimscroll_height = 143;
                            var cometchat_botdesc_newwidth = '';
                            $('#cometchat_botcontainer').find('.cometchat_botcontainer_body').height(cometchat_slimscroll_height);
                            $('#cometchat_botcontainer').find('.cometchat_botcontainer_body').slimScroll({height: (cometchat_slimscroll_height+25)+'px'});
                        }
                    }
                    $('.cometchat_closebotsbox').on('click', function(e){
                        bid = $(this).attr('id');
                        jqcc("body").find('.cometchat_botcontainer_'+bid).remove();
                    });
                });
            },
            guestLogin: function(){
                document.cookie = jqcc.cometchat.getSettings().cookiePrefix+"guest_login=true;path=/";
                jqcc.cometchat.reinitialize();
            },
            loggedOut: function(){
                var guestlogin = '';
                if(typeof(guestsMode) != 'undefined' && guestsMode == 1){
                    guestlogin = '<div class="auth_options guest_auth_options" onclick="jqcc.embedded.guestLogin();" style="height: 38px;width: 215px;"><img src="'+jqcc.cometchat.getBaseUrl()+'images/guestavatar.png" style="width: 20px;top: 9px;"><span>Guest Login</span></div>';
                }
                document.title = jqcc.cometchat.getThemeVariable('documentTitle');
                cometchat_header = $('#cometchat_header').detach();
                cometchat_lefttab = $('#cometchat_leftbar').detach();
                cometchat_righttab = $('#cometchat_righttab').detach();
                if($('#more_window')) {
                    more_window = $('#more_window').detach();
                }
                if(settings.ccauth.enabled=="1"){
                    var ccauthpopup = '<div class="cc_overlay"></div><div id="cometchat_social_login"><div class="login_container"><div class="login_image_container"><p>'+language['log_in_using']+'</p>';
                    ccauthpopup +=guestlogin;
                    ccauthpopup += '<iframe width="100%" height="100%"  allowtransparency="true" frameborder="0"  scrolling="no"  src="'+jqcc.cometchat.getBaseUrl()+'functions/login/" />';
                    ccauthpopup += '</div></div></div>';
                    $('#cometchat').html(ccauthpopup);
                }else{
                    if ((mobileDevice == "iPhone" || jqcc.cometchat.getUserAgent()[0] == "Safari") && jqcc.cometchat.getBaseData() != 'null') {
                        jqcc.embedded.safariBrowserLogin();
                    }else {
                        $('#cometchat').html('<div id="cometchat_loggedout_container"><div id="cometchat_loggedout"><div><img class="cometchat_loggedout_icon" src="'+staticCDNUrl+'layouts/'+settings.theme+'/images/exclamation.png" /></div><div>'+language[8]+'</div></div></div>');
                    }
                }
                /* Changes for guest modal on chat.pcs START */
                    var controlparameters = {"type":"core", "name":"cometchat", "method":"customlogout", "params":{"to":"0"}};
                    controlparameters = JSON.stringify(controlparameters);
                    parent.postMessage('CC^CONTROL_'+controlparameters,'*');
                /* Changes for guest modal on chat.pcs END */
            },
            safariBrowserLogin: function() {
                $('#cometchat').html('<div id="cometchat_loggedout_container"><div id="cometchat_loggedout"><div><a class="btn btn-primary" style="border-radius:25px;background-color:#4285f4;" id="safariBrowserLogin" href="javascript:void();" target="_blank">Click here to login </a></div></div></div>');
                $("#safariBrowserLogin").click(function(){
                    var loginWindow = window.open(location.href, 'loginWindow', 'width=300, height=250');
                    loginWindow.focus();
                    var loginInterval = setInterval(
                        function(){
                            loginWindow.close();
                            clearInterval(loginInterval);
                            window.location.reload();
                        },
                    4000);
                 });
            },
            userStatus: function(item){
                var usercontentstatus = item.s;
                var icon = '';
                var count = 140-item.m.length;
                if(usercontentstatus=='invisible'){
                    usercontentstatus = 'offline';
                }
                if(item.d==1){
                    usercontentstatus = 'mobile cometchat_mobile_'+usercontentstatus;
                    icon = '<div class="cometchat_dot"></div>';
                }
                var userDetails = '<div id="cometchat_self"><span class="cometchat_userscontentavatar"><img class="cometchat_userscontentavatarimage" src="'+item.a+'"></span><div id="cometchat_selfDetails"><div class="cometchat_userdisplayname">'+item.n+'</div><span class="cometchat_userscontentdot cometchat_'+usercontentstatus+'">'+icon+'</span><div class="selfstatus cometchat_userdisplaystatus">'+item.m+'</div></div></div>';
                var cometchat_optionsbutton_popup = $('#cometchat_optionsbutton_popup');
                cometchat_optionsbutton_popup.find('textarea.cometchat_statustextarea').val(item.m);
                cometchat_optionsbutton_popup.find('#cometchat_selfdisplayname').html(item.n);
                cometchat_optionsbutton_popup.find('.cometchat_statusmessagecount').html(count);
                if(item.lastseensetting==1){
                    lastseenflag = true;
                }
                if(item.s=='offline'){
                    jqcc[settings.theme].goOffline(1);
                }else{
                    jqcc[settings.theme].removeUnderline();
                    jqcc[settings.theme].updateStatus(item.s);
                }
                if(item.s != 'away'){
                    jqcc.cometchat.setThemeVariable('currentStatus', item.s);
                     jqcc.cometchat.setThemeVariable('idleFlag', 0);
                }
                if(item.s == 'away') {
                    jqcc.cometchat.setThemeVariable('idleFlag', 1);
                }
                if(item.id>'<?php echo $firstguestID; ?>'){
                    jqcc.cometchat.setThemeVariable('displayname', item.n);
                    /*$("#guestsname").show();
                    $("#guestsname").find("input.cometchat_guestnametextbox").val((item.n).replace("<?php echo $guestnamePrefix;?>-", ""));
                    cometchat_optionsbutton_popup.find("div.cometchat_tabsubtitle").html(language[45]);*/
                    cometchat_optionsbutton_popup.find('#cometchat_selfdisplayname').attr("readonly", false);
                    cometchat_optionsbutton_popup.find('#cometchat_selfdisplayname').addClass("cometchat_guestname");
                    cometchat_optionsbutton_popup.find('.cometchat_guestname').val(item.n.replace("<?php echo $guestnamePrefix;?>-", ""));
                }
                jqcc.cometchat.setThemeVariable('userid', item.id);
                if(item.s != 'away'){
                    jqcc.cometchat.setThemeVariable('currentStatus', item.s);
                }
                jqcc.cometchat.addBuddy(item);
                $('#cometchat_self_left').html(userDetails);
                $('#cometchat_welcome_username').text(item.n);
            },
            typingTo: function(item){
                if(typeof item['fromid'] != 'undefined'){

                    var id = item['fromid'];
                    var fromavatar = '';
                    $("#cometchat_buddylist_typing_"+id).css('display', 'block');
                    fromname = jqcc.cometchat.getThemeArray('buddylistName', id);
                    if(jqcc.cometchat.getThemeArray('buddylistAvatar', id)!=""){
                        fromavatar = '<span class="cometchat_other_avatar"><img class="cometchat_userscontentavatarsmall" src="'+jqcc.cometchat.getThemeArray('buddylistAvatar', id)+'"></span>';
                    }
                    var notify_typing = '<div class="cometchat_typingbox"><div class="typing_dots"></div><div class="typing_dots"></div><div class="typing_dots"></div></div>';
                    var msg = '<div id="cometchat_istyping_'+id+'" class="cometchat_messagebox"><div class="cometchat_istypingbox"><div  class="cometchat_chatboxmessage_typing"><span class="cometchat_chatboxmessagecontent">'+notify_typing+'</span>'+fromavatar+'</div></div></div><div style="clear:both"></div>';
                    if($("#cometchat_istyping_"+id).length < 1){
                        $("#cometchat_user_"+id+"_popup").find("div.cometchat_tabcontenttext").find('.cometchat_message_container').append(msg);
                    }
                    jqcc.embedded.scrollDown(id);
                    typingReceiverFlag[id] = item['typingtime'];
                }

               if(typeof typingRecieverTimer == 'undefined' || typingRecieverTimer == null || typingRecieverTimer == ''){
                    typingRecieverTimer = setTimeout(function(){
                        typingRecieverTimer = '';
                        var counter = 0;
                        $.each(typingReceiverFlag, function(typingid,typingtime){
                            if(((parseInt(new Date().getTime()))+jqcc.cometchat.getThemeVariable('timedifference')) - typingtime > 5000){
                                $("#cometchat_istyping_"+typingid).remove();
                                $("#cometchat_buddylist_typing_"+typingid).css('display', 'none');
                                delete typingReceiverFlag[typingid];
                            }else{
                                counter++;
                            }

                        });
                        if(counter > 0){
                            jqcc[settings.theme].typingTo(1);
                        }

                    }, 5000);
                }

            },
            typingStop: function(item){
                $("#cometchat_typing_"+item['fromid']).css('display', 'none');
                $("#cometchat_buddylist_typing_"+item['fromid']).css('display', 'none');

                if($("#cometchat_istyping_"+item['fromid']).length == 1){
                    $("#cometchat_istyping_"+item['fromid']).remove();
                }
            },
            sentMessageNotify: function(item){
                var size = 0, key;
                for (key in item) {
                    if(typeof item[key] == 'object'){
                        jqcc[settings.theme].sentMessageNotify(item[key]);
                    }
                }
                if(typeof item['id'] != 'undefined' && $("#cometchat_chatboxseen_"+item['id']).parent().hasClass('cometchat_messagebox_self')){
                    $("#cometchat_chatboxseen_"+item['id']).addClass('cometchat_sentnotification');
                }
            },
            deliveredMessageNotify: function(item){
                if($("#cometchat_message_"+item['message']).length == 0){
                    undeliveredmessages.push(item['message']);
                } else if(typeof item['fromid'] != 'undefined' && $("#cometchat_chatboxseen_"+item['message']).parent().hasClass('cometchat_messagebox_self')){
                    $("#cometchat_chatboxseen_"+item['message']).addClass('cometchat_deliverednotification');
                }
            },
            readMessageNotify: function(item){
                if(jqcc.cometchat.checkReadReceiptSetting(item.fromid) == 1){
                    if($("#cometchat_message_"+item['fromid']).length == 0){
                        unreadmessages.push(item['fromid']);
                    }
                    jqcc("#cometchat_user_"+item['fromid']+"_popup span.cometchat_deliverednotification").addClass('cometchat_readnotification');
                }
            },
            deliveredReadMessageNotify: function(item){
                if(jqcc.cometchat.checkReadReceiptSetting(item.fromid) == 1){
                    if($("#cometchat_message_"+item['message']).length == 0){
                        undeliveredmessages.push(item['message']);
                        unreadmessages.push(item['message']);
                    } else if(typeof item['fromid'] != 'undefined' && $("#cometchat_chatboxseen_"+item['message']).parent().hasClass('cometchat_messagebox_self') ){
                        $("#cometchat_chatboxseen_"+item['message']).addClass('cometchat_readnotification');
                    }
                }
            },
            createChatboxData: function(id, name, status, message, avatar, link, isdevice, silent, tryOldMessages, restored){
                if(typeof(restored)=="undefined"){
                    var restored = 0;
                }
                if(settings.enableType!=1 && embeddedchatroomid==0){
                    jqcc[settings.theme].hideMenuPopup();
                    if(hasChatroom == 1 && jqcc.cometchat.getThemeVariable('trayOpen')!='chatrooms'){
                        $('#currentroom').hide();
                        jqcc.cometchat.setChatroomVars('currentroom',0);
                    }
                    if(restored!=1){
                        jqcc.cometchat.updateChatBoxState({id:id,s:silent});
                    }
                    var cometchat_user_popup = $("#cometchat_user_"+id+"_popup");

                    if(typeof(cometchat_user_popup)=='undefined' || cometchat_user_popup.length<1){
                        shortname = name;
                        longname = name;
                        var usercontentstatus = status;
                        var icon = '';
                        if(jqcc.cometchat.getThemeArray('buddylistIsDevice', id) == '1'){
                            usercontentstatus = 'mobile cometchat_mobile_'+status;
                            icon = '<div class="cometchat_dot"></div>';
                        }
                        var hasFlash = false;
                        try {
                            hasFlash = Boolean(new ActiveXObject('ShockwaveFlash.ShockwaveFlash'));
                        } catch(exception) {
                            hasFlash = ('undefined' != typeof navigator.mimeTypes['application/x-shockwave-flash']);
                        }
                        if(hasFlash == false && mobileDevice){
                            var index = settings.plugins.indexOf('games');
                            if(settings.plugins.indexOf('games') != -1){
                                settings.plugins.splice(index, 1);
                            }
                        }
                        if(mobileDevice){
                            var index = settings.plugins.indexOf('screenshare');
                            if(settings.plugins.indexOf('screenshare') != -1){
                                settings.plugins.splice(index, 1);
                            }
                        }
                        var avchathtml = '';
                        var headerplugin = '<div id="cometchat_pluginsonheader">';
                        var audiochat = '';
                        var smilieshtml = '';
                        var voicenotehtml = '';
                        var filetransferhtml = '';

                        if(jqcc.cometchat.getThemeArray('isJabber', id)!=1){
                            var pluginslength = settings.plugins.length;
                            if(pluginslength>0){
                                for(var i = 0; i<pluginslength; i++){
                                    var name = 'cc'+settings.plugins[i];
                                    if(settings.plugins[i] == 'audiochat'){
                                        audiochat = '<div class="ccplugins" id="cometchat_audiochaticon" title="'+$[name].getTitle()+'" name="'+name+'" to="'+id+'" chatroommode="0"></div>';
                                        if($(window).width() < 300){
                                            audiochat = '';
                                        }
                                    }else if(settings.plugins[i]=='avchat'){
                                        avchathtml = '<div class="ccplugins " id="cometchat_videochaticon" title="'+$[name].getTitle()+'" name="'+name+'" to="'+id+'" chatroommode="0"></div>';
                                        if($(window).width() < 270){
                                            avchathtml = '';
                                        }
                                    }else if(settings.plugins[i]=='smilies'){
                                        smilieshtml = '<div id="smileyicon" class="ccplugins cometchat_smilies" title="'+$[name].getTitle()+'" name="'+name+'" to="'+id+'" chatroommode="0" ><img src="<?php echo STATIC_CDN_URL; ?>layouts/<?php echo $layout;?>/images/smileyicon.svg"></div>';
                                    }else if(settings.plugins[i]=='filetransfer'){
                                        filetransferhtml='<img src="'+staticCDNUrl+'layouts/'+settings.theme+'/images/attach.svg" class="ccplugins" title="'+$[name].getTitle()+'" name="'+name+'" to="'+id+'" chatroommode="0"/>';
                                    }else if(settings.plugins[i]=='voicenote'){
                                        /*voicenotehtml='<img src="'+staticCDNUrl+'layouts/'+settings.theme+'/images/recorder.svg" class="ccplugins" title="'+$[name].getTitle()+'" name="'+name+'" to="'+id+'" chatroommode="0"/>';*/
                                    }
                                }
                            }
                        }
                        var plugin_divider = '<div id="vline"><img src="<?php echo STATIC_CDN_URL; ?>layouts/<?php echo $layout;?>/images/vline.svg"/ style="margin-top:12px;"></div>';
                        if (audiochat == '' && avchathtml == '') {
                            plugin_divider = '';
                        }
                        headerplugin = headerplugin+avchathtml+audiochat+plugin_divider+'<div  class="cometchat_user_closebox"><img src="<?php echo STATIC_CDN_URL; ?>layouts/<?php echo $layout;?>/images/remove.svg"/></div></div>';
                        var startlink = '';
                        var endlink = '';
                        if(link!=''){
                            if(jqcc.cometchat.getCcvariable().callbackfn=='desktop'){
                                startlink='';
                                endlink
                            }else{
                                startlink = '<a href="'+link+'" target="_blank">';
                                endlink = '</a>';
                            }
                        }
                        if(typeof(silent) == "undefined" || silent == ""){
                            silent = 2;
                        }
                        if(silent == 1) {
                            jqcc.cometchat.setThemeVariable('openChatboxId',[id]);
                        }
                        var tabstateclass = (silent == 2)?'tabhidden':'tabopen';
                        var prepend = '';
                        if(jqcc.cometchat.getThemeVariable('prependLimit') != '0'){
                            prepend = '<div class=\"cometchat_prependMessages\" onclick\="jqcc.embedded.prependMessagesInit('+id+')\" id = \"cometchat_prependMessages_'+id+'\">'+language[83]+'</div>';
                        }
                        var avatarsrc = '';
                        var overlay_div = '';
                        if(status=="blocked"){
                            overlay_div = '<div class="cometchat_blocked_overlay"></div>';
                        }

                        if(avatar!=''){
                            avatarsrc = '<div class="cometchat_userscontentavatar">'+startlink+overlay_div+'<img src="'+avatar+'" class="cometchat_userscontentavatarimage" />'+endlink+'</div>';
                        }

                        selectlang = '<select id="selectlanguage_'+id+'" class="selectlanguage"><option value="no">None</option></select>';

                        $("<div/>").attr("id", "cometchat_user_"+id+"_popup").addClass("cometchat_userchatbox").addClass("cometchat_tabpopup cometchat_"+tabstateclass).html('<div class="cometchat_userchatarea"><div class="cometchat_tabsubtitle"><div class="cometchat_chatboxLeftDetails">'+avatarsrc+'<div class="cometchat_chatboxDisplayDetails"><div style="position: absolute;margin-left: 20px;" title="'+longname+'"><div class="cometchat_username">'+longname+'</div><span id="cometchat_typing_'+id+'" class="cometchat_typing"></span><div id="cometchat_pluginrightarrow_'+id+'" class="cometchat_pluginrightarrow"></div><div style="margin:0px;padding:0px;" class="cometchat_userdisplaystatus" title="'+message+'">'+message+'</div></div></div></div>'+headerplugin+'</div><div class="cometchat_messageElement cometchat_lastseenmessage" id="cometchat_messageElement_'+id+'"></div><div class="cometchat_prependMessages_container"></div><div class="cometchat_tabcontent"><div class="cometchat_tabcontenttext" id="cometchat_tabcontenttext_'+id+'" onscroll="jqcc.'+settings.theme+'.chatScroll(\''+id+'\');"><div class="cometchat_message_container">'+prepend+'</div></div><div id="cometchat_tabinputcontainer"><div id="downplugins"><div id="cometchat_pluginuparrow_'+id+'" class="cometchat_pluginuparrow"><img class="cometchat_pluginuparrowimage" src="'+staticCDNUrl+'layouts/'+settings.theme+'/images/circledown.svg"/></div><textarea id="cometchat_textarea_'+id+'" class="cometchat_textarea" placeholder="'+language[85]+'"></textarea>'+smilieshtml+'<div id="cometchat_attachements">'+filetransferhtml+'</div></div></div>'+selectlang+'</div></div></div>').appendTo($("#cometchat_righttab"));

                        // if(lastseenflag){
                        //     jqcc[settings.theme].hideLastseen(id);
                        // } else if(!lastseenflag){
                        //     if((jqcc.cometchat.getThemeArray('buddylistStatus', id) == 'available')||(jqcc.cometchat.getThemeArray('buddylistStatus', id) == 'offline' && jqcc.cometchat.getThemeArray('buddylistLastseensetting', id) == 1)){
                        //         jqcc[settings.theme].hideLastseen(id);
                        //     }
                        //     else if(jqcc.cometchat.getThemeArray('buddylistStatus', id) == 'offline' && jqcc.cometchat.getThemeArray('buddylistLastseensetting', id) == 0){
                        //         jqcc[settings.theme].showLastseen(id, jqcc.cometchat.getThemeArray('buddylistLastseen', id));
                        //     }
                        // }
                        if(restored!=1){
                            jqcc.embedded.addPopup(id, 0, 0);
                            jqcc.cometchat.updateChatBoxState({id:id,s:silent});
                        }


                        if(settings.lastseen == 1 && jqcc.cometchat.getThemeArray('buddylistLastseensetting',jqcc.cometchat.getUserID())==0){
                            if(lastseenflag){
                                    jqcc[settings.theme].hideLastseen(id);
                            } else if(!lastseenflag){
                                var statusmsg = jqcc.cometchat.getThemeArray('buddylistStatus', id);
                                var lstsnSetting = jqcc.cometchat.getThemeArray('buddylistLastseensetting', id);
                                var buddylastseen = jqcc.cometchat.getThemeArray('buddylistLastseen', id);

                                if((Math.floor(new Date().getTime()/1000)-buddylastseen < (60*10)) && statusmsg == 'available'){
                                        jqcc[settings.theme].hideLastseen(id);
                                }
                                else if(((statusmsg == 'away' || statusmsg == 'invisible' || statusmsg == 'busy' || statusmsg == 'offline') || Math.floor(new Date().getTime()/1000)-buddylastseen > (60*10)) && lstsnSetting == 0){
                                    jqcc[settings.theme].showLastseen(id, buddylastseen);
                                }else{
                                    $('#cometchat_messageElement_'+id).hide();
                                }
                            }
                        }

                        cometchat_user_popup = $("#cometchat_user_"+id+"_popup");
                        var cometchat_user_popup1 = document.getElementById("cometchat_user_"+id+"_popup");
                        if(jqcc().slimScroll && mobileDevice == null){
                            cometchat_user_popup.find(".cometchat_tabcontenttext").slimScroll({height: 'auto',width: 'auto'});
                        }
                        cometchat_user_popup.find("textarea.cometchat_textarea").on('paste input',function(){
                            if($(this).val().length > 380){
                                $(this).height(75);
                                $(this).find(".cometchat_textarea").slimScroll({scroll: '1'});
                                jqcc[settings.theme].windowResize();
                            }
                        });
                        cometchat_user_popup.find("textarea.cometchat_textarea").focus(function(event){
                            setTimeout(function(){
                                var textvalue = cometchat_user_popup.find("textarea.cometchat_textarea").val();
                                if(mobileDevice && textvalue != ''){
                                    var sendicon = '<div id="cometchat_send"><img src="'+staticCDNUrl+'layouts/'+settings.theme+'/images/send.svg" class="cometchat_tabcontentsubmit" /></div>';
                                    $('#cometchat_user_'+id+'_popup').find('#cometchat_attachements').replaceWith(sendicon);
                                }else if(mobileDevice && textvalue == ''){
                                    var attachicon = '<div id="cometchat_attachements"><img src="'+staticCDNUrl+'layouts/'+settings.theme+'/images/attach.svg" class="ccplugins" title="'+$['ccfiletransfer'].getTitle()+'" name="ccfiletransfer" to="'+id+'" chatroommode="0"/></div>';
                                    cometchat_user_popup.find('#cometchat_send').replaceWith(attachicon);
                                    cometchat_user_popup.find("textarea.cometchat_textarea").height(20);
                                }
                            },100);
                        });
                        cometchat_user_popup.find("textarea.cometchat_textarea").keydown(function(event){
                            if(typingSenderFlag != 0){
                                jqcc.cometchat.typingTo({id:id,method:'typingTo'});
                                typingSenderFlag = 0;
                                clearTimeout(typingSenderTimer);
                                typingSenderTimer = setTimeout(function(){
                                    typingSenderFlag = 1;
                                },4000);
                            }
                            return jqcc[settings.theme].chatboxKeydown(event, this, id);
                        });
                        cometchat_user_popup.on('click','.cometchat_view_profile',function(event){
                            window.open(link,'_blank');
                        });
                        /*Uncomment for drag and drop*/
                        if(!cometchat_user_popup.find('#cometchat_uploadfile_'+id).length) {
                            var uploadf = document.createElement("INPUT");
                            uploadf.setAttribute("type", "file");
                            uploadf.setAttribute("class", "cometchat_fileupload");
                            uploadf.setAttribute("id", 'cometchat_uploadfile_'+id);
                            uploadf.setAttribute("name", "Filedata");
                            uploadf.setAttribute("multiple", "true");
                            cometchat_user_popup.find(".cometchat_tabcontent").append(uploadf);
                            uploadf.addEventListener("change", jqcc.ccfiletransfer.FileSelectHandler(cometchat_user_popup,id,0), false);
                        }


                        cometchat_user_popup.find('.cometchat_prependMessages').show();

                        /*var drag = $(".cometchat_file_drag");

                        var xhr = new XMLHttpRequest();
                        if(xhr.upload) {
                            cometchat_user_popup1.addEventListener("dragover", jqcc.ccfiletransfer.FileDragHover(cometchat_user_popup,id,1), false);
                            cometchat_user_popup1.addEventListener("dragleave", jqcc.ccfiletransfer.FileDragHover(cometchat_user_popup,id,0), false);
                            cometchat_user_popup1.addEventListener("drop", jqcc.ccfiletransfer.FileSelectHandler(cometchat_user_popup,id,0), false);
                            drag.on("dragover", jqcc.ccfiletransfer.FileDragHover(cometchat_user_popup,id,2));
                            drag.on("dragleave", jqcc.ccfiletransfer.FileDragHover(cometchat_user_popup,id,3));
                            drag.on("drop", jqcc.ccfiletransfer.FileSelectHandler(cometchat_user_popup,id,0));
                        }*/

                        cometchat_user_popup.find("textarea.cometchat_textarea").blur(function(event){
                            var textvalue = cometchat_user_popup.find("textarea.cometchat_textarea").val();
                            if(textvalue == '' && mobileDevice){
                                var attachicon = '<div id="cometchat_attachements"><img src="'+staticCDNUrl+'layouts/'+settings.theme+'/images/attach.svg" class="ccplugins" title="'+$['ccfiletransfer'].getTitle()+'" name="ccfiletransfer" to="'+id+'" chatroommode="0"/></div>';
                                $('#cometchat_user_'+id+'_popup').find('#cometchat_send').replaceWith(attachicon);
                            }
                            jqcc.cometchat.typingTo({id:id,method:'typingStop'});
                        });

                        cometchat_user_popup.find("div#cometchat_tabinputcontainer").on('click','#cometchat_send',function(event){
                            jqcc[settings.theme].chatboxKeydown(event, cometchat_user_popup.find("textarea.cometchat_textarea"), id, 1);
                        });
                        cometchat_user_popup.find("textarea.cometchat_textarea").keyup(function(event){
                            var textvalue = cometchat_user_popup.find("textarea.cometchat_textarea").val();
                            if(mobileDevice && textvalue != '' && cometchat_user_popup.find('#cometchat_send').length == 0 ){
                                var sendicon = '<div id="cometchat_send"><img src="'+staticCDNUrl+'layouts/'+settings.theme+'/images/send.svg" class="cometchat_tabcontentsubmit" /></div>';
                                $('#cometchat_user_'+id+'_popup').find('#cometchat_attachements').replaceWith(sendicon);
                            }else if(mobileDevice && textvalue == '' && cometchat_user_popup.find('#cometchat_send').length == 1){
                                var attachicon = '<div id="cometchat_attachements"><img src="'+staticCDNUrl+'layouts/'+settings.theme+'/images/attach.svg" class="ccplugins" title="'+$['ccfiletransfer'].getTitle()+'" name="ccfiletransfer" to="'+id+'" chatroommode="0"/></div>';
                                $('#cometchat_user_'+id+'_popup').find('#cometchat_send').replaceWith(attachicon);
                            }
                            jqcc[settings.theme].textboxresize(this,cometchat_user_popup,id);
                        });

                        var cometchat_user_id = $("#cometchat_user_"+id);
                        cometchat_user_popup.on('click','.ccplugins',function(event){
                            event.stopImmediatePropagation();
                            jqcc[settings.theme].hideMenuPopup();
                            var name = $(this).attr('name');
                            var to = $(this).attr('to');
                            var chatroommode = $(this).attr('chatroommode');
                            var winHt = $(window).innerHeight();
                            var winWidth = $(window).innerWidth();
                            $('#main_container').find('.cometchat_pluginrightarrow').rotate(0);
                            if(!mobileDevice){
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
                                    var controlparameters = {"to":to, "chatroommode":chatroommode};
                                    jqcc[name].init(controlparameters);
                                }
                            }else{
                                var controlparameters = {"to":to, "chatroommode":chatroommode};
                                jqcc[name].init(controlparameters);
                            }
                            $('.cometchat_float_list').hide();
                            $('#cometchat_newcompose').removeClass('rotated');
                            $('.cometchat_pluginrightarrow').removeClass('rotated').css({'transform' :'rotate(0deg)'});
                            if(name != 'ccstickers' && name != 'cctransliterate' && name != 'ccsmilies' && name != 'ccvoicenote') {
                                $('.cometchat_pluginuparrow').removeClass('rotated').css({'transform' :'rotate(0deg)'});
                            }
                        });

                        cometchat_user_popup.find('div.cometchat_user_closebox').click(function(){
                            var chatboxid = cometchat_user_popup.attr('id').split('_')[2];
                            jqcc.cometchat.updateChatBoxState({id:chatboxid,s:0});
                            var closeflag = 1;
                            if($('.cometchat_container').hasClass('visible')){
                                confirmmsg = confirm(language['close_chatbox']);
                                if(confirmmsg)
                                    jqcc('.cometchat_closebox').click();
                                else
                                    closeflag = 0;
                            }
                            if(closeflag){
                                $('#cometchat_userlist_'+chatboxid).show();
                                setTimeout(function(){
                                    cometchat_user_popup.css('position','relative');
                                    cometchat_user_popup.remove();
                                },500);
                                if($('#cometchat_container_smilies').length == 1){
                                    jqcc[settings.theme].closeModule('smilies');
                                }
                                if($('#cometchat_container_stickers').length == 1){
                                    jqcc[settings.theme].closeModule('stickers');
                                }
                                var chatBoxOrder = jqcc.cometchat.getThemeVariable('chatBoxOrder');
                                var nextChatBox = chatBoxOrder[chatBoxOrder.length-1];
                                jqcc.cometchat.setThemeVariable('openedChatbox',nextChatBox);
                                if(chatBoxOrder == ''){
                                    jqcc.cometchat.setThemeVariable('openChatboxId',[]);
                                }else{
                                    jqcc.cometchat.setThemeVariable('openChatboxId',[nextChatBox]);
                                }
                                if(nextChatBox) {
                                    if(!isNaN(nextChatBox) && nextChatBox.charAt(0) != '_') {
                                        setTimeout(function(){
                                            $("#cometchat_user_"+nextChatBox+"_popup").addClass('cometchat_tabopen');
                                        }, 500);
                                        jqcc.cometchat.updateChatBoxState({id:nextChatBox,s:1});
                                        jqcc[settings.theme].addPopup(nextChatBox,0,0);
                                        if(jqcc.cometchat.getThemeVariable('currentStatus')!='invisible'){
                                           if(typeof $("#cometchat_user_"+nextChatBox+"_popup .cometchat_message_container").find('.cometchat_chatboxmessage ').last().attr('id') != 'undefined'){
                                                var messageid = $("#cometchat_user_"+nextChatBox+"_popup .cometchat_message_container").find('.cometchat_chatboxmessage ').last().attr('id').split('_')[2];
                                            }
                                            var message = {"id": messageid, "from": nextChatBox, "self": 0};
                                            if(typeof jqcc.cometchat.getCcvariable().lastmessagereadstatus[nextChatBox] != "undefined" && jqcc.cometchat.getCcvariable().lastmessagereadstatus[nextChatBox]==0 && jqcc.cometchat.getExternalVariable('messagereceiptsetting') == 0){
                                                    jqcc.cometchat.sendReceipt(message, 'readMessageNotify');
                                            }
                                        }
                                    }else {
                                        nextChatBox = nextChatBox.replace('_','');
                                        jqcc.cometchat.silentroom(nextChatBox,'','',1,0);
                                    }
                                }
                                $('.cometchat_noactivity').css('display','block');
                                jqcc[settings.theme].activeChatBoxes();
                                jqcc.cometchat.orderChatboxes();
                            }
                        });
                        /*cometchat_user_popup.find('.cometchat_pluginsOption').click(function(){
                            var winHt = $(window).innerHeight();
                            var winWidth = $(window).innerWidth();
                            var tabsubtitleHt = $(".cometchat_userchatarea").find('.cometchat_tabsubtitle').outerHeight(true);
                            if((winWidth > winHt) && mobileDevice){
                                cometchat_user_popup.find('#plugin_container').css('max-height',(winHt-tabsubtitleHt-5));
                            } else{
                                cometchat_user_popup.find('#plugin_container').css('max-height','');
                            }
                            $(this).find('.cometchat_menuOptionIcon').toggleClass('cometchat_menuOptionIconClick');
                            $('.cometchat_plugins').toggleClass('cometchat_tabopen');
                        });*/
                        jqcc[settings.theme].scrollDown(id);
                        if(jqcc.cometchat.getInternalVariable('updatingsession')!=1){
                            cometchat_user_popup.find("textarea.cometchat_textarea").focus();
                        }
                        if(jqcc.cometchat.getExternalVariable('initialize')!=1||isNaN(id)){
                            jqcc[settings.theme].updateChatbox(id);
                        }
                        $('.cometchat_noactivity').css('display','block');
                    }
                    if(jqcc.cometchat.getThemeVariable('openChatboxId').indexOf(id) > -1&&jqcc.cometchat.getThemeVariable('trayOpen')!='chatrooms'){
                        $('.cometchat_userchatbox').removeClass('cometchat_tabopen').addClass('cometchat_tabhidden');
                        if(openedChatbox = jqcc.cometchat.getThemeVariable('openedChatbox') && restored!=1) {
                            jqcc.embedded.addPopup(id, 0, 0);
                            jqcc.cometchat.updateChatBoxState({id:openedChatbox,s:2});
                        }
                        if(restored!=1){
                          jqcc.cometchat.updateChatBoxState({id:id,s:1});
                        }
                        jqcc.cometchat.setThemeVariable('openedChatbox',id);
                        cometchat_user_popup.addClass('cometchat_tabopen').removeClass('cometchat_tabhidden');
                        $('.cometchat_noactivity').css('display','none');
                    }

                    var extensions_list = settings.extensions;
                    if(extensions_list.indexOf('ads') > -1){
                        jqcc.ccads.init(id);
                    }

                    jqcc.cometchat.setThemeArray('chatBoxesOrder', chromeReorderFix+id, 0);
                    chatboxOpened[id] = 0;
                    jqcc.cometchat.orderChatboxes();
                    jqcc[settings.theme].activeChatBoxes();
                    jqcc.cometchat.setThemeArray('trying', id, 5);
                    jqcc[settings.theme].scrollDown(id);
                    if($('#cometchat_container_smilies').length != 1) {
                        jqcc[settings.theme].windowResize();
                    }
                    jqcc[settings.theme].updateReadMessages(id);
                }
                if(typeof(jqcc.cometchat.checkInternetConnection) && !jqcc.cometchat.checkInternetConnection()) {
                    jqcc.embedded.noInternetConnection(true);
                }
            },
            addProgressBar: function(){
            },
            activeChatBoxes: function(){
                $('.cometchat_menuOptionIcon').removeClass('cometchat_menuOptionIconClick');
                var chatBoxesOrder = jqcc.cometchat.getThemeVariable('chatBoxesOrder');
                var openChatboxId = jqcc.cometchat.getThemeVariable('openChatboxId')[0];
                var oneononeflag = '0';
                var cometchat_activechatboxes = '';
                for(chatBoxId in chatBoxesOrder){
                    chatBoxId = chatBoxId.replace('_','');
                    oneononeflag = '1';
                    var userstatus = jqcc.cometchat.getThemeArray('buddylistStatus', chatBoxId);
                    var usercontentstatus = userstatus;
                    var icon = '';
                    if(jqcc.cometchat.getThemeArray('buddylistIsDevice', chatBoxId)==1){
                        mobilestatus = 'mobile';
                        usercontentstatus = 'mobile cometchat_mobile_'+userstatus;
                        icon = '<div class="cometchat_dot"></div>';
                    }
                    var overlay_div = '';
                    if(userstatus=="blocked"){
                        overlay_div = '<div class="cometchat_blocked_overlay"></div>';
                    }
                    cometchat_activechatboxes = '<div id="cometchat_activech_'+chatBoxId+'" class="cometchat_userlist" onmouseover="jqcc(this).addClass(\'cometchat_userlist_hover\');" onmouseout="jqcc(this).removeClass(\'cometchat_userlist_hover\');"><span class="cometchat_userscontentavatar">'+overlay_div+'<img class="cometchat_userscontentavatarimage" src="'+jqcc.cometchat.getThemeArray('buddylistAvatar', chatBoxId)+'"></span><div class="cometchat_chatboxDisplayDetails"><div class="cometchat_userdisplayname">'+jqcc.cometchat.getThemeArray('buddylistName', chatBoxId)+'</div><span class="cometchat_userscontentdot cometchat_'+usercontentstatus+'">'+icon+'</span><div class="cometchat_userdisplaystatus">'+jqcc.cometchat.getThemeArray('buddylistMessage', chatBoxId)+'</div></div></div>'+cometchat_activechatboxes;
                }
                if(oneononeflag=='1'){
                    if($('#cometchat_allusers').length<1){
                        /*$('#cometchat_userslist').prepend('<div class="recentchat">ACTIVE USERS</div>');*/
                    }
                }else{
                    $('#cometchat_allusers').remove();
                }
            },
            addMessages: function(item,silent){
                var todaysdate = new Date();
                var tdmonth  = todaysdate.getMonth();
                var tddate  = todaysdate.getDate();
                var tdyear = todaysdate.getFullYear();
                var today_date_class = tdmonth+"_"+tddate+"_"+tdyear;
                var ydaysdate = new Date((new Date()).getTime() - 3600000 * 24);
                var ydmonth  = ydaysdate.getMonth();
                var yddate  = ydaysdate.getDate();
                var ydyear = ydaysdate.getFullYear();
                var yday_date_class = ydmonth+"_"+yddate+"_"+ydyear;
                var d = '';
                var month = '';
                var date  = '';
                var year = '';
                var msg_date_class = '';
                var msg_date = '';
                var date_class = '';
                var msg_date_format = '';
                var msg_time = '';
                var jabber = '';
                var prepend = '';
                var currenttime = Math.floor(new Date().getTime()/1000);
                var messagewrapperid = '';
                var trayIcons = jqcc.cometchat.getTrayicon();
                var isRealtimetranslateEnabled = trayIcons.hasOwnProperty('realtimetranslate');

                $.each(item, function(i, incoming){
                    incoming.message = jqcc.cometchat.htmlEntities(incoming.message);
                    if( (!incoming.hasOwnProperty('id') || incoming.id == '') && (!incoming.hasOwnProperty('localmessageid') || incoming.localmessageid == '') && (incoming.message+'').indexOf('CC^CONTROL_')==-1){
                        return;
                    }
                    if(typeof(incoming.self) ==='undefined' && typeof(incoming.old) ==='undefined' && typeof(incoming.sent) ==='undefined'){
                        incoming.sent = Math.floor(new Date().getTime()/1000);
                        incoming.old = 0;
                        incoming.self = 1;
                    }
                    if(typeof(incoming.m)!== 'undefined'){
                        incoming.message = incoming.m;
                    }
                    var message = jqcc.cometchat.processcontrolmessage(incoming);

                    if(incoming.hasOwnProperty('botid') && incoming.botid > 0){
                        incoming.self = 0;
                    }

                    if(message == null || message == ""){
                        return;
                    }

                    if(isRealtimetranslateEnabled && jqcc.cookie(settings.cookiePrefix+'rttlang') && incoming.self == 0 && incoming.message.indexOf('CC^CONTROL_') == -1){
                        incoming.message = message;
                        text_translate(incoming);
                    }

                    /** START: Audio/ Video Chat */
                    var calldisplay = '';
                    if(message.indexOf('avchat_webaction=initiate')!=-1 || message.indexOf('avchat_webaction=acceptcall')!=-1){
                        calldisplay = "style='display:none;'";
                        var avchat_data = message.split('|');
                        avchat_data.push('videocall');
                    }
                    if(message.indexOf('audiochat_webaction=initiate')!=-1 || message.indexOf('audiochat_webaction=acceptcall')!=-1){
                        calldisplay = "style='display:none;'";
                        var audiochat_data = message.split('|');
                        audiochat_data.push('audiocall');
                    }
                    /** END: Audio/ Video Chat */

                    if(typeof(incoming.nopopup) === "undefined" || incoming.nopopup =="") {
                        incoming.nopopup = 0;
                    }

                    if(typeof(incoming.broadcast) == "undefined" || incoming.broadcast != 0){
                        if(incoming.self ==1 ){
                            incoming.nopopup = 1;
                        }
                    }

                    if(incoming.jabber == 1 && typeof(incoming.selfadded) != "undefined" && incoming.selfadded != null) {
                       msg_time = incoming.id;
                       jabber = 1;
                    }else{
                      msg_time = incoming.sent;
                      jabber = 0;
                    }

                    msg_time = msg_time+'';

                    if (msg_time.length == 10){
                        msg_time = parseInt(msg_time * 1000);
                    }

                    months_set = new Array(
                        language['jan'],
                        language['feb'],
                        language['mar'],
                        language['apr'],
                        language['may'],
                        language['jun'],
                        language['jul'],
                        language['aug'],
                        language['sep'],
                        language['oct'],
                        language['nov'],
                        language['dec']
                    );

                    d               = new Date(parseInt(msg_time));
                    month           = d.getMonth();
                    date            = d.getDate();
                    year            = d.getFullYear();
                    msg_date_class  = month+"_"+date+"_"+year;
                    msg_date        = months_set[month]+" "+date+", "+year;
                    date_class      = "";

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
                        msg_date = language['today'];
                    }else  if(msg_date_class == yday_date_class){
                        date_class = "yesterday";
                        msg_date = language['yesterday'];
                    }

                    checkfirstmessage = ($("#cometchat_tabcontenttext_"+incoming.from+" .cometchat_chatboxmessage").length) ? 0 : 1;

                    if(message.indexOf('CC^CONTROL_PLUGIN_AUDIOCHAT_ENDCALL')!=-1 || message.indexOf('CC^CONTROL_PLUGIN_AVCHAT_ENDCALL')!=-1){
                        message ='This call has been ended';
                    }

                    if(jqcc.cometchat.getThemeArray('trying', incoming.from)==undefined){
                        if(typeof (jqcc[settings.theme].createChatbox)!=='undefined' && incoming.nopopup == 0 && silent != 1){
                            jqcc[settings.theme].createChatbox(incoming.from, jqcc.cometchat.getThemeArray('buddylistName', incoming.from), jqcc.cometchat.getThemeArray('buddylistStatus', incoming.from), jqcc.cometchat.getThemeArray('buddylistMessage', incoming.from), jqcc.cometchat.getThemeArray('buddylistAvatar', incoming.from), jqcc.cometchat.getThemeArray('buddylistLink', incoming.from), jqcc.cometchat.getThemeArray('buddylistIsDevice', incoming.from), 1, 1);
                        }
                    }

                    var jabber = jqcc.cometchat.getThemeArray('isJabber', incoming.from);

                    if(incoming.hasOwnProperty('id')) {
                        messagewrapperid = incoming.id;
                    }else if(incoming.hasOwnProperty('localmessageid') ) {
                        messagewrapperid = incoming.localmessageid;
                    }

                    if(settings.autoPopupChatbox==1 && incoming.self==0 && incoming.old!=1 && $('#cometchat_user_'+incoming.from+'_popup .cometchat_prependMessages').length==0) {
                        jqcc('#cometchat_userslist').find('#cometchat_userlist_'+incoming.from).click();
                    }

                    /** START: Audio/ Video Chat */
                    if(message.indexOf('avchat_webaction=initiate')!=-1){
                        jqcc[settings.theme].generateIncomingAvchatData(incoming,avchat_data,currenttime);
                    }else if(message.indexOf('avchat_webaction=acceptcall')!=-1) {
                        var controlparameters = {"to":incoming.from, "grp":avchat_data[2], "start_url":''};
                        if(incoming.sent > currenttime - 15){
                            jqcc.ccavchat.accept_fid(controlparameters);
                        }
                    }

                    if(message.indexOf('audiochat_webaction=initiate')!=-1){
                        jqcc[settings.theme].generateIncomingAvchatData(incoming,audiochat_data,currenttime);
                    }else if(message.indexOf('audiochat_webaction=acceptcall')!=-1) {
                        var controlparameters = {"to":incoming.from, "grp":audiochat_data[2], "start_url":''};
                        if(incoming.sent > currenttime - 15){
                            jqcc.ccaudiochat.accept_fid(controlparameters);
                        }
                    }
                    /** END: Audio/ Video Chat */
                    var alreadyreceivedunreadmessages = jqcc.cometchat.getFromStorage('receivedunreadmessages');

                    var chatboxopen = 0;
                    var alreadyreceivedunreadmessages = jqcc.cometchat.getFromStorage('receivedunreadmessages');
                    if(incoming.self!=1&&incoming.old!=1 && ((typeof(alreadyreceivedunreadmessages[incoming.from])!='undefined'&& alreadyreceivedunreadmessages[incoming.from]<incoming.id) || typeof(alreadyreceivedunreadmessages[incoming.from])=='undefined')){
                        if (incoming.self != 1 && settings.messageBeep == 1) {
                            if ((typeof $.cookie(settings.cookiePrefix+"sound") == 'undefined' || $.cookie(settings.cookiePrefix+"sound") == null) || $.cookie(settings.cookiePrefix + "sound") == 'true') {
                                jqcc.embedded.playSound(1);
                            }
                        }
                        var openBox = jqcc.cometchat.getThemeVariable('openChatboxId');
                        if(openBox[0] != incoming.from){
                            jqcc.embedded.addPopup(incoming.from, 1, 1);
                            jqcc.cometchat.updateChatBoxState({id:incoming.from,c:1});
                        }
                    }

                    if(incoming.self !=1 && incoming.old!=1 && $('#cometchat_user_'+incoming.from+'_popup').length==0 )
                    {

                    } else {
                        jqcc.cometchat.sendReceipt(incoming);
                        var selfstyle           = '';
                        var fromavatar          = '';
                        var selfmessage         = '';
                        var imagemsg_avtar      = '';
                        var imagemsg_rightcss   = '';
                        var smileycount         = (message.match(/cometchat_smiley/g) || []).length;
                        var smileymsg           = message.replace(/<img[^>]*>/g,"");
                        smileymsg               = smileymsg.trim();

                        if(smileycount == 1 && smileymsg == '') {
                            message = message.replace('height="20"', 'height="64px"');
                            message = message.replace('width="20"', 'width="64px"');
                        }

                        if(message.indexOf('<img')!=-1){
                            if(incoming.self != 1){
                                imagemsg_rightcss = 'position:relative;margin-left:-20px !important';
                                if(smileymsg == '' || smileymsg.indexOf('class="cometchat_hw_lang"') != -1 || smileymsg.indexOf('mediatype="1"') != -1){
                                    imagemsg_avtar = 'margin-top:-40px;';
                                    imagemsg_rightcss = 'position:relative;margin-left:-20px !important';
                                }
                            }else if(incoming.self == 1 && smileymsg == ''){
                            }
                            if(message.indexOf('cometchat_smiley') != -1 && smileycount != 1 && smileymsg == ''){
                                imagemsg_avtar = '';
                                if(incoming.self != 1){
                                    imagemsg_rightcss = 'position:relative;margin-top:5px !important;margin-left:-20px !important';
                                }
                            }
                            if(smileymsg != '' && smileycount > 0 && incoming.self != 1){
                                imagemsg_rightcss = '';
                            }
                        }

                        if(parseInt(incoming.self)==1 && (!incoming.hasOwnProperty('botid') || (typeof incoming.botid == "undefined" && incoming.botid == 0))){
                            fromname = language[10];
                            selfstyle = ' cometchat_self';
                            selfmessage = 'cometchat_messagebox_self';
                        } else {
                            fromname = jqcc.cometchat.getThemeArray('buddylistName', incoming.from);
                            fromavatar = '<span class="cometchat_other_avatar" style="'+imagemsg_avtar+'"><img class="cometchat_userscontentavatarsmall" src="'+jqcc.cometchat.getThemeArray('buddylistAvatar', incoming.from)+'"></span>';
                            if(typeof incoming.botid != "undefined" && incoming.botid != 0){
                                fromname = jqcc.cometchat.getThemeArray('botlistName', incoming.botid);
                                if(jqcc.cometchat.getThemeArray('botlistAvatar', incoming.botid)!=""){
                                    fromavatar = '<span class="cometchat_other_avatar" style="'+imagemsg_avtar+'"><img class="cometchat_userscontentavatarsmall" src="'+jqcc.cometchat.getThemeArray('botlistAvatar', incoming.botid)+'"></span>';
                                }
                            }
                        }
                        if(incoming.old!=1 && incoming.self!=1){
                            if((typeof $.cookie(settings.cookiePrefix+"sound") == 'undefined' || $.cookie(settings.cookiePrefix+"sound") == null) || $.cookie(settings.cookiePrefix+"sound")=='true'){
                                jqcc[settings.theme].playSound(1);
                            }
                        }
                        if($("#cometchat_message_"+messagewrapperid).length>0){
                            $("#cometchat_message_"+messagewrapperid).find(".cometchat_chatboxmessagecontent").html(message);
                        }else{
                            var add_bg = 'cometchat_chatboxmessage'+selfstyle;
                            if((message.indexOf('<img')!=-1 && message.indexOf('src')!=-1 && message.indexOf('cometchat_smiley') == -1) || (smileycount > 0 && smileymsg == '')){
                               if( incoming.self === 1 ) {
                                    add_bg = 'cometchat_chatboxselfmedia';
                                }else {
                                    add_bg = 'cometchat_chatboxmedia';
                                }
                            }
                            sentdata = '';
                            if(incoming.sent!=null){d
                                var ts = incoming.sent;
                                sentdata = jqcc[settings.theme].getTimeDisplay(ts, incoming.from,parseInt(incoming.self));
                            }


                            var msg = '<div class="cometchat_time cometchat_time_'+msg_date_class+' '+date_class+'" msg_format="'+msg_date_format+'">'+msg_date+'</div><div class="cometchat_messagebox '+selfmessage+'"><div class="'+add_bg+'" id="cometchat_message_'+messagewrapperid+'"><span class="cometchat_chatboxmessagecontent" style="'+imagemsg_rightcss+'">'+message+'</span>'+fromavatar+'</div><span id="cometchat_chatboxseen_'+messagewrapperid+'"></span>'+sentdata+'</div></div><div style="clear:both"></div>';
                            var msg1 = '<div class="cometchat_time cometchat_time_'+msg_date_class+' '+date_class+'" msg_format="'+msg_date_format+'">'+msg_date+'</div>';
                            var msg2 = '<div '+calldisplay+' class="cometchat_messagebox '+selfmessage+'"><div class="'+add_bg+'" id="cometchat_message_'+messagewrapperid+'"><span class="cometchat_chatboxmessagecontent" style="'+imagemsg_rightcss+'">'+message+'</span>'+fromavatar+'</div><span id="cometchat_chatboxseen_'+messagewrapperid+'"></span>'+sentdata+'</div></div><div style="clear:both"></div>';
                            var msg = msg1+msg2;

                            if(incoming.hasOwnProperty('id')&&incoming.hasOwnProperty('localmessageid')&&$("#cometchat_message_"+incoming.localmessageid).length>0){
                                $("#cometchat_message_"+incoming.localmessageid).parent().after(msg);
                                $("#cometchat_message_"+incoming.localmessageid).parent().remove();
                                var offlinemessages = jqcc.cometchat.getFromStorage('offlinemessagesqueue');
                                if(offlinemessages.hasOwnProperty(incoming.localmessageid)) {
                                    delete offlinemessages[incoming.localmessageid];
                                    jqcc.cometchat.updateToStorage('offlinemessagesqueue',offlinemessages);
                                }
                            }else{
                                $("#cometchat_user_"+incoming.from+"_popup").find('.cometchat_message_container').append(msg);
                            }

                            $("#cometchat_typing_"+incoming.from).css('display', 'none');
                            if(message.indexOf('<img')!=-1 && message.indexOf('src')!=-1){
                                $( "#cometchat_message_"+messagewrapperid+" img" ).load(function() {
                                    jqcc[settings.theme].scrollDown(incoming.from);
                                });
                            }else{
                                jqcc[settings.theme].scrollDown(incoming.from);
                            }
                            if(typeof(messagewrapperid) != 'undefined' && !jqcc.isNumeric(messagewrapperid) &&  messagewrapperid.indexOf('_')>-1) {
                                $("#cometchat_chatboxseen_"+messagewrapperid).addClass('cometchat_offlinemessage');
                            }
                            if(undeliveredmessages.indexOf(messagewrapperid) >= 0){
                                $("#cometchat_chatboxseen_"+messagewrapperid).addClass('cometchat_deliverednotification');
                                undeliveredmessages.pop(messagewrapperid);
                            }
                            if(unreadmessages.indexOf(messagewrapperid) >= 0){
                                $("#cometchat_chatboxseen_"+messagewrapperid).addClass('cometchat_readnotification');
                                unreadmessages.pop(messagewrapperid);
                            }
                            var nowTime = new Date();
                            var idleDifference = Math.floor(nowTime.getTime()/1000)-jqcc.cometchat.getThemeVariable('idleTime');
                            if(idleDifference>5){
                                if(settings.windowTitleNotify==1){
                                    document.title = language[15];
                                }
                            }
                        }
                    }

                    jqcc[settings.theme].groupbyDate(incoming.from,jabber);
                    jqcc[settings.theme].updateReceivedUnreadMessages(incoming.from,messagewrapperid);

                    if(jqcc.cometchat.getThemeVariable('prependLimit') != '0' && jabber != 1){
                        prepend = '<div class=\"cometchat_prependMessages\" onclick\="jqcc.embedded.prependMessagesInit('+incoming.from+')\" id = \"cometchat_prependMessages_'+incoming.from+'\">'+language[83]+'</div>';
                    }
                    if($('#cometchat_user_'+incoming.from+'_popup .cometchat_prependMessages').length != 1){
                        $('#cometchat_tabcontenttext_'+incoming.from).prepend(prepend);
                    }
                    /*Notification for AV Chat*/
                    if(message.indexOf('avchat_webaction=initiate')!=-1 || message.indexOf('avchat_webaction=acceptcall')!=-1){
                        message = jqcc.ccavchat.getLanguage('video_call');
                    }
                    if(message.indexOf('audiochat_webaction=initiate')!=-1 || message.indexOf('audiochat_webaction=acceptcall')!=-1){
                        message = jqcc.ccaudiochat.getLanguage('video_call');
                    }
                    /*Notification for AV Chat*/
                    var newMessage = 0;
                    if((jqcc.cometchat.getThemeVariable('isMini')==1||(jqcc.cometchat.getThemeVariable('openChatboxId').indexOf(incoming.from) > -1))&&incoming.self!=1&&settings.desktopNotifications==1&&incoming.old==0){
                        var callChatboxEvent = function(){
                            if(typeof incoming.from!='undefined'){
                                for(x in desktopNotifications){
                                    for(y in desktopNotifications[x]){
                                        desktopNotifications[x][y].close();
                                    }
                                }
                                desktopNotifications = {};
                                if(jqcc.cometchat.getThemeVariable('isMini')==1){
                                    window.top.focus();
                                }
                                jqcc.cometchat.chatWith(incoming.from);
                            }
                        };
                        if(typeof desktopNotifications[incoming.from]!='undefined'){
                            var newMessageCount = 0;
                            for(x in desktopNotifications[incoming.from]){
                                ++newMessageCount;
                                desktopNotifications[incoming.from][x].close();
                            }
                            jqcc.cometchat.notify((++newMessageCount)+' '+language[46]+' '+jqcc.cometchat.getThemeArray('buddylistName', incoming.from), jqcc.cometchat.getThemeArray('buddylistName', incoming.from), language[47], callChatboxEvent, incoming.from, messagewrapperid);
                        }else{
                            jqcc.cometchat.notify(language[48]+' '+jqcc.cometchat.getThemeArray('buddylistName', incoming.from), jqcc.cometchat.getThemeArray('buddylistAvatar', incoming.from), message, callChatboxEvent, incoming.from, messagewrapperid);
                        }
                    }
                    jqcc[settings.theme].updateReadMessages(incoming.from);
                    if(settings.cometserviceEnabled == 1 && settings.messagereceiptEnabled == 1 && jqcc.cometchat.getCcvariable().callbackfn != "mobilewebapp" && (settings.transport == 'cometservice' || settings.transport == 'cometserviceselfhosted')  && incoming.old == 0 && incoming.self == 1 && incoming.direction == 0){
                        jqcc[settings.theme].sentMessageNotify(incoming);
                    }

                    if(settings.disableRecentTab == 0){
                        message = jqcc.cometchat.processRecentmessages(message);
                        var params = {'chatid':incoming.from,'isgroup':0,'timestamp':incoming.sent,'m':message,'msgid':messagewrapperid,'force':0,'del':0};
                        jqcc.cometchat.updateRecentChats(params);
                    }
                    jqcc[settings.theme].windowResize();
                });
            },
            updateReadMessages: function(id){
                if($('#cometchat_user_'+id+'_popup:visible').find('.cometchat_chatboxmessage:not(.cometchat_self):last').length){
                    if(typeof (jqcc.cometchat.updateToStorage)!=='undefined'){
                        var alreadyreadmessages = jqcc.cometchat.getFromStorage('readmessages');
                        var messageboxid_text =  $('#cometchat_user_'+id+'_popup').find('.cometchat_chatboxmessage[id]:not(.cometchat_self):last').attr('id');
                        var messageboxid_media =  $('#cometchat_user_'+id+'_popup').find('.cometchat_chatboxmedia[id]:not(.cometchat_self):last').attr('id');
                        if(typeof messageboxid_media != 'undefined' && messageboxid_media !=false){
                            var messageboxid = (messageboxid_text > messageboxid_media) ? messageboxid_text : messageboxid_media;
                        } else{
                            var messageboxid = messageboxid_text;
                        }
                        if(typeof messageboxid != 'undefined' && messageboxid !=false){
                            var lastid = parseInt(messageboxid.replace('cometchat_message_',''));
                            if((typeof(alreadyreadmessages[id])!='undefined' && parseInt(alreadyreadmessages[id])<parseInt(lastid)) || typeof(alreadyreadmessages[id])=='undefined' || alreadyreadmessages[id] == null){
                                var readmessages={};
                                readmessages[id]= parseInt(lastid);
                                jqcc.cometchat.updateToStorage('readmessages',readmessages);
                            }
                        }
                    }
                }
            },
            noInternetConnection: function(flag) {
                if(flag) {
                    $('.cometchat_messageElement').removeClass("cometchat_lastseenmessage");
                    $('.cometchat_messageElement').addClass("cometchat_showOffline");
                    $('.cometchat_messageElement').slideDown(300);
                    $('.cometchat_messageElement').html(language['check_internet']);
                }else {
                    $('.cometchat_messageElement').removeClass("cometchat_showOffline");
                    $('.cometchat_messageElement').hide();
                    $(".cometchat_messageElement").each(function(){
                        var id = parseInt($(this).attr('id').replace('cometchat_messageElement_',''));
                        var buddylastseen = jqcc.cometchat.getThemeArray('buddylistLastseen', id);
                        var statusmsg = jqcc.cometchat.getThemeArray('buddylistStatus', id);
                        var lstsnSetting = jqcc.cometchat.getThemeArray('buddylistLastseensetting', id);
                        var currentts = Math.floor(new Date().getTime()/1000);
                        if(((statusmsg == 'away' || statusmsg == 'invisible' || statusmsg == 'busy' || statusmsg == 'offline') || currentts-buddylastseen > (60*10)) && lstsnSetting == '0'){
                            jqcc[settings.theme].showLastseen(id,jqcc.cometchat.getThemeArray('buddylistLastseen',id));
                            $('.cometchat_messageElement').addClass("cometchat_lastseenmessage");
                        }else{
                            $('#cometchat_messageElement_'+id).slideUp(300);
                            $('#cometchat_messageElement_'+id).html("");
                        }
                    })
                }
            },
            updateReceivedUnreadMessages: function(id,lastid){
                if(typeof (jqcc.cometchat.updateToStorage)!=='undefined'){
                    var alreadyreceivedmessages = jqcc.cometchat.getFromStorage('receivedunreadmessages');
                    if((typeof(alreadyreceivedmessages[id])!='undefined' && alreadyreceivedmessages[id] != 'null' && parseInt(alreadyreceivedmessages[id])<parseInt(lastid)) || typeof(alreadyreceivedmessages[id])=='undefined'){
                        var receivedmessages={};
                        receivedmessages[id]= parseInt(lastid);
                        jqcc.cometchat.updateToStorage('receivedunreadmessages',receivedmessages);
                    }
                }
            },
            statusSendMessage: function(){
                var message = $("#cometchat_optionsbutton_popup").find("textarea.cometchat_statustextarea").val();
                var oldMessage = jqcc.cometchat.getThemeArray('buddylistMessage', jqcc.cometchat.getThemeVariable('userid'));
                if(message!=''&&oldMessage!=message){
                    $('div.cometchat_statusbutton').html('<img src="'+staticCDNUrl+'images/loader.gif" width="16">');
                    jqcc.cometchat.setThemeArray('buddylistMessage', jqcc.cometchat.getThemeVariable('userid'), message);
                    jqcc.cometchat.statusSendMessageSet(message);
                }else{
                    $('div.cometchat_statusbutton').text('<?php echo $language[57]; ?>');
                    setTimeout(function(){
                        $('div.cometchat_statusbutton').text('<?php echo $language[22]; ?>');
                    }, 1500);
                }
            },
            statusSendMessageSuccess: function(){
                setTimeout(function(){
                    $('div.cometchat_statusbutton').text('<?php echo $language[49]; ?>');
                }, 1800);
                setTimeout(function(){
                    $('div.cometchat_statusbutton').text('<?php echo $language[22]; ?>');
                    $('#cometchat_selfDetails .cometchat_userdisplaystatus').text($('.cometchat_statustextarea').val());
                }, 2500);
            },
            statusSendMessageError: function(){
                setTimeout(function(){
                    $('div.cometchat_statusbutton').text('<?php echo $language[50]; ?>');
                }, 1800);
                setTimeout(function(){
                    $('div.cometchat_statusbutton').text('<?php echo $language[22]; ?>');
                }, 2500);
            },
            setGuestName: function(){
                var optionspopup = $('#cometchat_optionsbutton_popup');
                if(optionspopup.find('.cometchat_guestname').length){
                    var guestname = optionspopup.find('.cometchat_guestname').val();
                }
                var oldguestname = jqcc.cometchat.getThemeArray('buddylistName', jqcc.cometchat.getThemeVariable('userid'));
                oldguestname = oldguestname.replace("<?php echo $guestnamePrefix;?>-", "");
                if(guestname != '' && oldguestname != guestname){
                    jqcc.cometchat.setThemeArray('buddylistName', jqcc.cometchat.getThemeVariable('userid'), guestname);
                    jqcc.cometchat.setGuestNameSet(guestname);
                }
            },
            setGuestNameSuccess: function(){
                var guestname = $("#cometchat_optionsbutton_popup .cometchat_guestname").val();
                $('#cometchat_selfDetails .cometchat_userdisplayname').text("<?php echo $guestnamePrefix;?>-"+guestname);
                $('#cometchat_welcome_username').text("<?php echo $guestnamePrefix;?>-"+guestname);
            },
            resetGuestName:function(callback){
                $('#cometchat_optionsbutton_popup').find('.cometchat_guestname').val(jqcc.cometchat.getThemeVariable('displayname').replace("<?php echo $guestnamePrefix;?>-", ""));
                if (typeof callback == 'function') {
                    callback();
                }
            },
            removeUnderline: function(){
                $("#cometchat_optionsbutton_popup").find("span.busy").css('text-decoration', 'none');
                $("#cometchat_optionsbutton_popup").find("span.invisible").css('text-decoration', 'none');
                $("#cometchat_optionsbutton_popup").find("span.offline").css('text-decoration', 'none');
                $("#cometchat_optionsbutton_popup").find("span.available").css('text-decoration', 'none');
                jqcc[settings.theme].removeUnderline2();
            },
            removeUnderline2: function(){
                $("#cometchat_self .cometchat_userscontentdot").removeClass('cometchat_available');
                $("#cometchat_self .cometchat_userscontentdot").removeClass('cometchat_busy');
                $("#cometchat_self .cometchat_userscontentdot").removeClass('cometchat_invisible');
                $("#cometchat_self .cometchat_userscontentdot").removeClass('cometchat_offline');
                $("#cometchat_self .cometchat_userscontentdot").removeClass('cometchat_away');
            },
            updateStatus: function(status){
                $("#cometchat_self .cometchat_userscontentdot").addClass('cometchat_'+status);
                $('span.cometchat_optionsstatus.'+status).css('text-decoration', 'underline');
                var userid = jqcc.cometchat.getUserID();
                $('#cometchat_selfDetails .cometchat_userdisplaystatus').text(jqcc.cometchat.getThemeArray('buddylistMessage', userid));
                $('input:radio[id=cometchat_status'+status+'_radio]').prop('checked', true);
            },
            goOffline: function(silent){
                jqcc.cometchat.setThemeVariable('offline', 1);
                if(silent!=1){
                    jqcc.cometchat.sendStatus('offline');
                }else{
                    jqcc[settings.theme].updateStatus('offline');
                }
                if(hasChatroom== 1){
                    jqcc[settings.theme].chatroomOffline();
                }
                $('#cometchat_userstab_popup').removeClass('cometchat_tabopen');
                $('#cometchat_userstab').removeClass('cometchat_tabclick');
                $('#cometchat_optionsbutton_popup').removeClass('cometchat_tabopen');
                $('#cometchat_optionsbutton').removeClass('cometchat_tabclick');
                var chatBoxesOrder = jqcc.cometchat.getThemeVariable('chatBoxesOrder');
                for(chatBoxId in chatBoxesOrder){
                    $("#cometchat_user"+chatBoxId+"_popup").remove();
                    jqcc.cometchat.unsetThemeArray('chatBoxesOrder',chatBoxId);
                }
                $('#currentroom').find('div.cometchat_user_closebox').click();
                jqcc.cometchat.orderChatboxes();
                jqcc.cometchat.setThemeVariable('openChatboxId', []);
                jqcc.cometchat.setSessionVariable('openChatboxId', '');
                $('.cometchat_offline_overlay').css('display','table');
                if(typeof window.cometuncall_function=='function'){
                    cometuncall_function(jqcc.cometchat.getThemeVariable('cometid'));
                }
                $('.cometchat_noactivity').css('display','none');
                if(typeof jqcc.cometchat.setChatroomVars=='function'){
                    jqcc.cometchat.setChatroomVars('newMessages',0);
                }
                jqcc.embedded.activeChatBoxes();
            },
            tryAddMessages: function(id, atleastOneNewMessage){
                if(jqcc.cometchat.getThemeArray('buddylistName', id)==null||jqcc.cometchat.getThemeArray('buddylistName', id)==''){
                    if(jqcc.cometchat.getThemeArray('trying', id)<5){
                        setTimeout(function(){
                            if(typeof (jqcc[settings.theme].tryAddMessages)!=='undefined'){
                                jqcc[settings.theme].tryAddMessages(id, atleastOneNewMessage);
                            }
                        }, 1000);
                    }
                }else{
                    $("#cometchat_typing_"+id).css('display', 'none');
                    jqcc[settings.theme].scrollDown(id);
                    chatboxOpened[id] = 1;
                    if(atleastOneNewMessage==1){
                        var nowTime = new Date();
                        var idleDifference = Math.floor(nowTime.getTime()/1000)-jqcc.cometchat.getThemeVariable('idleTime');
                        if(idleDifference>5){
                            document.title = language[15];
                        }
                    }
                    if($.cookie(settings.cookiePrefix+"sound")&&$.cookie(settings.cookiePrefix+"sound")=='true'){
                    }else{
                        if(atleastOneNewMessage==1){
                            jqcc[settings.theme].playSound();
                        }
                    }
                }
            },
            countMessage: function(){
                if(jqcc.cometchat.getThemeVariable('loggedout')==0){
                    var cc_state = $.cookie(settings.cookiePrefix+'state');
                    jqcc.cometchat.setInternalVariable('updatingsession', '1');
                    if(cc_state!=null){
                        var cc_states = cc_state.split(/:/);
                        if(jqcc.cometchat.getThemeVariable('offline')==0){
                            var value = 0;
                            if(cc_states[0]!=' '&&cc_states[0]!=''){
                                value = cc_states[0];
                            }
                            if((value==0&&$('#cometchat_userstab').hasClass("cometchat_tabclick"))||(value==1&&!($('#cometchat_userstab').hasClass("cometchat_tabclick")))){
                                $('#cometchat_userstab').click();
                            }
                            value = '';
                            if(cc_states[1]!=' '&&cc_states[1]!=''){
                                value = cc_states[1];
                            }
                            if(value==jqcc.cometchat.getSessionVariable('activeChatboxes')){
                                var newActiveChatboxes = {};
                                if(value!=''){
                                    var badge = 0;
                                    var chatboxData = value.split(/,/);
                                    for(i = 0; i<chatboxData.length; i++){
                                        var chatboxIds = chatboxData[i].split(/\|/);
                                        newActiveChatboxes[chatboxIds[0]] = chatboxIds[1];
                                        badge += parseInt(chatboxIds[1]);
                                    }
                                    favicon.badge(badge);
                                }
                            }
                        }
                    }
                }
            },
            resynch: function(){
                if(jqcc.cometchat.getThemeVariable('loggedout')==0){
                    var cc_state = jqcc.cometchat.getCcvariable().internalVars.chatboxstates;
                    var msgcount = 0;
                    if(cc_state!=null){
                        for(key in cc_state){
                            var state = cc_state[key].split('|');
                            if(key.indexOf('_') == -1 && !isNaN(parseInt(state[2]))) {
                                msgcount += parseInt(state[2]);
                            }
                        }
                        jqcc.cometchat.setThemeVariable('newMessages',msgcount);
                        if(jqcc.cometchat.getThemeVariable('newMessages')>0){
                            if(settings.windowFavicon==1){
                                jqcc[settings.theme].countMessage();
                            }
                            if(document.title==language[15]){
                                document.title = jqcc.cometchat.getThemeVariable('documentTitle');
                            }else{
                                if(settings.windowTitleNotify==1){
                                    document.title = language[15];
                                }
                            }
                        }else{
                            var nowTime = new Date();
                            var idleDifference = Math.floor(nowTime.getTime()/1000)-jqcc.cometchat.getThemeVariable('idleTime');
                            if(idleDifference<5){
                                document.title = jqcc.cometchat.getThemeVariable('documentTitle');
                                if(settings.windowFavicon==1){
                                    favicon.badge(0);
                                }
                            }
                        }
                    }
                    clearTimeout(resynchTimer);
                    resynchTimer = setTimeout(function(){
                        jqcc[settings.theme].resynch();
                    }, 5000);
                }
            },
            setModuleAlert: function(id, number){
            },
            textboxresize: function(chattextarea,cometchat_user_popup,id){
                var difference = $(chattextarea).innerHeight() - $(chattextarea).height();
                var container_height = cometchat_user_popup.find('#cometchat_tabinputcontainer').outerHeight();
                if ($(chattextarea).innerHeight < chattextarea.scrollHeight ) {

                } else if($(chattextarea).height() < 75 || event.keyCode == 8) {
                    $(chattextarea).height(20);
                    if(chattextarea.scrollHeight - difference >= 75){
                        $(chattextarea).height(75);
                    }else if(chattextarea.scrollHeight - difference>20){
                        $(chattextarea).height(chattextarea.scrollHeight - difference);
                    }
                    jqcc[settings.theme].windowResize();
                    if($('#cometchat_container_smilies').length == 1 || $('#cometchat_container_stickers').length == 1 || $('#cometchat_container_transliterate').length == 1 || $('#cometchat_container_voicenote').length == 1){
                        $('.cometchat_container').css('bottom',cometchat_user_popup.find('#cometchat_tabinputcontainer').outerHeight(true)+1);
                    }
                    var newcontainerheight = cometchat_user_popup.find('#cometchat_tabinputcontainer').outerHeight();
                    if(container_height != newcontainerheight){
                        jqcc[settings.theme].scrollDown(id);
                    }
                }else{
                    if(mobileDevice){
                        $(chattextarea).css({'overflow-y': 'auto'});
                    }else{
                        $(chattextarea).slimScroll({scroll: '1'});
                    }
                    $(chattextarea).focus();
                }
            },
            addPopup: function(id, amount, add){
                if(typeof(id)=='string'){
                    id = id.replace( /^\D+/g, '');
                }

                if(jqcc.cometchat.getThemeArray('buddylistName', id)==null||jqcc.cometchat.getThemeArray('buddylistName', id)==''){
                    if(jqcc.cometchat.getThemeArray('trying', id)===undefined){
                        jqcc[settings.theme].createChatbox(id, null, null, null, null, null, null, 1, null);
                    }
                    if(jqcc.cometchat.getThemeArray('trying', id)<5){
                        setTimeout(function(){
                            jqcc[settings.theme].addPopup(id, amount, add);
                        }, 5000);
                    }
                }else{

                    if(add == 1){
                        if(settings.disableRecentTab == 0 && jqcc('#cometchat_recentlist_'+id).length > 0){
                            amount = parseInt(jqcc('#cometchat_recentlist_'+id).attr('amount'))+parseInt(amount);
                        } else {
                            amount = parseInt(jqcc('#cometchat_userlist_'+id).attr('amount'))+parseInt(amount);
                        }
                    }

                    var cometchat_user_id = $("#cometchat_userlist_"+id);
                    if(amount > 0){
                        if((typeof $.cookie(settings.cookiePrefix+"sound") == 'undefined' || $.cookie(settings.cookiePrefix+"sound") == null) || $.cookie(settings.cookiePrefix+"sound")=='true'){
                            jqcc[settings.theme].playSound(0);
                        }

                        cometchat_user_id.addClass('cometchat_new_message').attr('amount', amount).find('div.cometchat_msgcounttext').html(amount).show();
                        jqcc('#cometchat_recentlist_'+id).addClass('cometchat_new_message').attr('amount', amount).find('div.cometchat_msgcounttext').html(amount).show();
                        jqcc('#cometchat_userlist_'+id).addClass('cometchat_new_message').attr('amount', amount).find('div.cometchat_msgcounttext').html(amount).show();
                    } else {
                        cometchat_user_id.removeClass('cometchat_new_message').attr('amount', 0).find('div.cometchat_msgcounttext').html(0).hide();
                        jqcc('#cometchat_recentlist_'+id).removeClass('cometchat_new_message').attr('amount', 0).find('div.cometchat_msgcounttext').html(0).hide();
                        jqcc('#cometchat_userlist_'+id).removeClass('cometchat_new_message').attr('amount', 0).find('div.cometchat_msgcounttext').html(0).hide();
                    }

                }
                jqcc.cometchat.setThemeArray('buddylistUnreadMessageCount', id, amount);
            },
            getTimeDisplay: function(ts, id, dir){
                ts = parseInt(ts);
                if((ts+"").length == 10){
                    ts = ts*1000;
                }
                var cometchat_self_ts = '';
                if(dir == 1){
                    cometchat_self_ts = 'cometchat_self_ts';
                }
                var time = getTimeDisplay(ts);
                var timeDataStart = "<span class=\"cometchat_ts "+cometchat_self_ts+" \">"+time.hour+":"+time.minute+time.ap;
                var timeDataEnd = "</span>";
                if(ts<jqcc.cometchat.getThemeVariable('todays12am')){
                    return timeDataStart+" "+time.date+time.type+" "+time.month+timeDataEnd;
                }else{
                    return timeDataStart+timeDataEnd;
                }
            },
            createChatbox: function(id, name, status, message, avatar, link, isdevice, silent, tryOldMessages, restored){
                if((jqcc.cometchat.getThemeArray('buddylistName', id)==null||jqcc.cometchat.getThemeArray('buddylistName', id)=='') && (typeof(id) != "undefined" && id !=0)){
                    if(jqcc.cometchat.getThemeArray('trying', id)===undefined){
                        jqcc.cometchat.setThemeArray('trying', id, 1);
                        if(!isNaN(id)){
                            jqcc.cometchat.createChatboxSet(id, name, status, message, avatar, link, isdevice, silent, tryOldMessages, restored);
                        }else{
                            setTimeout(function(){
                                if(typeof (jqcc[settings.theme].createChatbox)!=='undefined'){
                                    jqcc[settings.theme].createChatbox(id, jqcc.cometchat.getThemeArray('buddylistName', id), jqcc.cometchat.getThemeArray('buddylistStatus', id), jqcc.cometchat.getThemeArray('buddylistMessage', id), jqcc.cometchat.getThemeArray('buddylistAvatar', id), jqcc.cometchat.getThemeArray('buddylistLink', id), jqcc.cometchat.getThemeArray('buddylistIsDevice', id), silent, tryOldMessages, restored);
                                }
                            }, 5000);
                        }
                    }else{
                        if(jqcc.cometchat.getThemeArray('trying', id)<5){
                            jqcc.cometchat.incrementThemeVariable('trying['+id+']');
                            setTimeout(function(){
                                if(typeof (jqcc[settings.theme].createChatbox)!=='undefined'){
                                    jqcc[settings.theme].createChatbox(id, jqcc.cometchat.getThemeArray('buddylistName', id), jqcc.cometchat.getThemeArray('buddylistStatus', id), jqcc.cometchat.getThemeArray('buddylistMessage', id), jqcc.cometchat.getThemeArray('buddylistAvatar', id), jqcc.cometchat.getThemeArray('buddylistLink', id), jqcc.cometchat.getThemeArray('buddylistIsDevice', id), silent, tryOldMessages, restored);
                                }
                            }, 5000);
                        }
                    }
                }else if(typeof(id) != "undefined" && id !=0){
                    if(typeof (jqcc[settings.theme].createChatboxData)!=='undefined'){
                        jqcc[settings.theme].createChatboxData(id, jqcc.cometchat.getThemeArray('buddylistName', id), jqcc.cometchat.getThemeArray('buddylistStatus', id), jqcc.cometchat.getThemeArray('buddylistMessage', id), jqcc.cometchat.getThemeArray('buddylistAvatar', id), jqcc.cometchat.getThemeArray('buddylistLink', id), jqcc.cometchat.getThemeArray('buddylistIsDevice', id), silent, tryOldMessages, restored);
                    }
                }

            },
            createChatboxSuccess: function(data, silent, tryOldMessages){
            },
            tooltip: function(id, message, orientation){
                var cometchat_tooltip = $('#cometchat_tooltip');
                $('#cometchat_tooltip').find(".cometchat_tooltip_content").html(message);
                cometchat_tooltip.css('display', 'block');
            },
            moveBar: function(relativePixels){
                if(jqcc.cometchat.getThemeVariable('openChatboxId').length>0){
                    $('#cometchat_user_'+jqcc.cometchat.getThemeVariable('openChatboxId')[0]+'_popup').removeClass('cometchat_tabopen');
                    $('#cometchat_user_'+jqcc.cometchat.getThemeVariable('openChatboxId')[0]).removeClass('cometchat_tabclick').removeClass("cometchat_usertabclick");
                }
                $("#cometchat_chatboxes_wide").find("span.cometchat_tabalert").css('display', 'none');
                var ms = settings.scrollTime;
                if(jqcc.cometchat.getExternalVariable('initialize')==1){
                    ms = 0;
                }
                $("#cometchat_chatboxes").scrollToCC(relativePixels, ms, function(){
                    if(jqcc.cometchat.getThemeVariable('openChatboxId').length>0){
                        if(($("#cometchat_user_"+jqcc.cometchat.getThemeVariable('openChatboxId')[0]).offset().left<($("#cometchat_chatboxes").offset().left+$("#cometchat_chatboxes").width()))&&($("#cometchat_user_"+jqcc.cometchat.getThemeVariable('openChatboxId')[0]).offset().left-$("#cometchat_chatboxes").offset().left)>=0){
                            $("#cometchat_user_"+jqcc.cometchat.getThemeVariable('openChatboxId')[0]).click();
                        }else{
                            jqcc.cometchat.setSessionVariable('openChatboxId', '');
                        }
                        jqcc.cometchat.setSessionVariable('openChatboxId', jqcc.cometchat.getThemeVariable('openChatboxId')[0]);

                    }
                    jqcc[settings.theme].checkPopups();
                });
            },
            chatTab: function(){
                var cometchat_user_search = $("#cometchat_user_search");
                var cometchat_userscontent = $('#cometchat_userscontent');
                cometchat_user_search.click(function(){
                    var searchString = $(this).val();
                    if(searchString==language[18]){
                        cometchat_user_search.val('');
                        cometchat_user_search.addClass('cometchat_search_light');
                    }
                });
                cometchat_user_search.blur(function(){
                    var searchString = $(this).val();
                    if(searchString==''){
                        cometchat_user_search.addClass('cometchat_search_light');
                        cometchat_user_search.val(language[18]);
                    }
                });
                cometchat_user_search.keyup(function(){
                    jqcc.embedded.searchUserList({"name":$(this).val()});
                });
                var cometchat_userstab = $('#cometchat_chatstab');
                var cometchat_chatroomstab = $('#cometchat_groupstab');
                cometchat_userstab.click(function(){
                    jqcc[settings.theme].hideMenuPopup();
                    if(typeof(newmesscr)!="undefined"){
                        clearInterval(newmesscr);
                    }
                    newmesscr = setInterval(function(){
                        if($("#cometchat_groupstab.cometchat_tabclick").length<1){
                            if(hasChatroom == 1){
                                var newCrMessages = jqcc.cometchat.getChatroomVars('newMessages');
                                if(newCrMessages>0){
                                    $('#cometchat_chatroomstab_text').text(language[88]+' ('+newCrMessages+')');
                                }
                                setTimeout(function(){
                                        jqcc.crembedded.updateChatroomsTabtext();
                                },2000);
                            }
                        }else{
                            if(typeof(newmesscr)!='undefined'){
                                clearInterval(newmesscr);
                            }
                        }
                    },4000);
                    jqcc.cometchat.setSessionVariable('buddylist', '1');
                    $("#cometchat_tooltip").css('display', 'none');
                    $(".cometchat_userscontentavatar").find('img').each(function(){
                        if($(this).attr('original')){
                            $(this).attr("src", $(this).attr('original'));
                            $(this).removeAttr('original');
                        }
                    });
                    $(this).addClass("cometchat_tabclick");
                    cometchat_chatroomstab.removeClass("cometchat_tabclick");
                    $('#cometchat_chatroomstab_popup').removeClass("cometchat_tabopen");
                    $('#cometchat_userstab_popup').addClass("cometchat_tabopen");
                    jqcc[settings.theme].windowResize();
                });
                if(hasChatroom == 1){
                    jqcc.crembedded.chatroomTab();
                }
            },
            searchUserList: function(params){
                if (params.hasOwnProperty('name')) {
                    var searchString = params.name;
                    var cometchat_user_search = $("#cometchat_user_search");
                    var cometchat_userscontent = $('#cometchat_userscontent');
                    if($('#cometchat_leftbar').find('#cometchat_chatstab').hasClass('tab_click')){
                        if(searchString.length>0&&searchString!=language[18]){
                            $('#cometchat').find('#close_user_search').css('display','block');
                            cometchat_userscontent.find('div.cometchat_userlist').hide();
                            cometchat_userscontent.find('.cometchat_subsubtitle').hide();
                            cometchat_userscontent.find('#cometchat_activechatboxes_popup').hide();
                            var searchcount = cometchat_userscontent.find('div.cometchat_userdisplayname:icontains('+searchString+')').length + cometchat_userscontent.find('span.cometchat_userscontentname:icontains('+searchString+')').length;
                            if(searchcount >= 1 ){
                                cometchat_userscontent.find('div.cometchat_userlist').hide();
                                $('div.cometchat_userdisplayname:icontains('+searchString+')').parents('div.cometchat_userlist').show();
                                $('span.cometchat_userscontentname:icontains('+searchString+')').parents('div.cometchat_userlist').show();
                                $(document).find('#cometchat_userscontent').find('.cc_nousers').remove();
                            } else {
                                if($(document).find('.cc_nousers').length == 0){
                                    $(document).find('#cometchat_userscontent').append('<div class="cc_nousers" style= "padding-top:8px;text-align:center;">'+language[58]+'</div>');
                                }else if($(document).find('.cc_nousers').css("display") == "none") {
                                    $(document).find('.cc_nousers').show();
                                }
                            }
                            cometchat_user_search.removeClass('cometchat_search_light');
                        }else{
                            $('#cometchat').find('#close_user_search').css('display','none');
                            cometchat_userscontent.find('div.cometchat_userlist').show();
                            cometchat_userscontent.find('.cometchat_subsubtitle').show();
                            cometchat_userscontent.find('#cometchat_activechatboxes_popup').show();
                            cometchat_userscontent.find('.cc_nousers').hide();
                        }
                    } else if($('#cometchat_leftbar').find('#cometchat_groupstab').hasClass('tab_click')){
                        lobby_rooms = $('#lobby_rooms');
                        $(document).find('.cc_nousers').remove();
                        if(searchString.length>0 && searchString!='Search'){
                            $('#cometchat').find('#close_user_search').css('display','block');
                            lobby_rooms.find('div.lobby_room').hide();
                            var count = lobby_rooms.find('span.currentroomname:icontains('+searchString+')');
                            if(count['length'] >= 1){
                                lobby_rooms.find('span.currentroomname:icontains('+searchString+')').parents('div.lobby_room').show();
                                lobby_rooms.find('.cc_nogroups').remove();
                            }else{
                                if($(document).find('.cc_nogroups').length == 0){
                                    lobby_rooms.append('<div class="cc_nogroups" style="padding-top: 8px; text-align:center; display: block; color: #4A4848; "><?php echo $language[114];?></div>');
                                    $('#lobby_rooms').find('#cometchat_joinedgroups').hide();
                                    $('#lobby_rooms').find('#cometchat_othergroups').hide();
                                }else if($(document).find('.cc_nogroups').css("display") == "none") {
                                    $(document).find('.cc_nogroups').show();
                                    $(document).find('.cc_nousers').remove();
                                }
                            }
                            cometchat_user_search.removeClass('cometchat_search_light');
                        }else{
                            $('#cometchat').find('#close_user_search').css('display','none');
                            $('#lobby_rooms').find('#cometchat_joinedgroups').show();
                            $('#lobby_rooms').find('#cometchat_othergroups').show();
                            lobby_rooms.find('div.lobby_room').show();
                            lobby_rooms.find('.cc_nogroups').hide();
                        }
                    } else if($('#cometchat_leftbar').find('#cometchat_recenttab').hasClass('tab_click')){
                        if(searchString.length>0&&searchString!=language[18]){
                            $('#cometchat').find('#close_user_search').css('display','block');
                            cometchat_userscontent.find('#cometchat_recentlist .cometchat_recentchatlist').hide();
                            cometchat_userscontent.find('#cometchat_recentlist .cometchat_grouplist').hide();
                            cometchat_userscontent.find('.cometchat_subsubtitle').hide();
                            cometchat_userscontent.find('#cometchat_activechatboxes_popup').hide();
                            var searchcount1 = cometchat_userscontent.find('#cometchat_recentlist .cometchat_userdisplayname:icontains('+searchString+')').length;
                            var searchcount2 = cometchat_userscontent.find('#cometchat_recentlist .currentroomname:icontains('+searchString+')').length;
                            if((searchcount1+searchcount2) >= 1 ){
                                cometchat_userscontent.find('#cometchat_recentlist .cometchat_recentchatlist').hide();
                                cometchat_userscontent.find('#cometchat_recentlist .cometchat_grouplist').hide();
                                $('#cometchat_recentlist .cometchat_userdisplayname:icontains('+searchString+')').parents('#cometchat_recentlist .cometchat_recentchatlist').show();
                                $('#cometchat_recentlist .currentroomname:icontains('+searchString+')').parents('#cometchat_recentlist .cometchat_grouplist').show();
                                $(document).find('#cometchat_userscontent').find('.cc_nousers').remove();
                            } else {
                                if($(document).find('.cc_nousers').length == 0){
                                    $(document).find('#cometchat_userscontent').append('<div class="cc_nousers" style= "padding-top:8px;text-align:center;">'+language["no_chats_found"]+'</div>');
                                }else if($(document).find('.cc_nousers').css("display") == "none") {
                                    $(document).find('.cc_nousers').show();
                                }
                            }
                            cometchat_user_search.removeClass('cometchat_search_light');
                        }else{
                            $('#cometchat').find('#close_user_search').css('display','none');
                            cometchat_userscontent.find('#cometchat_recentlist .cometchat_recentchatlist').show();
                            cometchat_userscontent.find('#cometchat_recentlist .cometchat_grouplist').show();
                            cometchat_userscontent.find('.cometchat_subsubtitle').show();
                            cometchat_userscontent.find('#cometchat_activechatboxes_popup').show();
                            cometchat_userscontent.find('.cc_nousers').hide();
                        }
                    }

                }
            },
            optionsButton: function(){
                var cometchat_optionsbutton_popup = $("#cometchat_optionsbutton_popup");
                cometchat_optionsbutton_popup.find('.cometchat_optionstyle_container').click(function(e){
                    e.stopPropagation();
                });
                cometchat_optionsbutton_popup.find("span.cometchat_gooffline").click(function(){
                    jqcc[settings.theme].goOffline();
                });
                $(".cometchat_soundnotifications_div").click(function(event){
                    event.stopPropagation();
                    var notification = 'false';
                    if($("#cometchat_soundnotifications").is(":checked")){
                        $("#cometchat_soundnotifications").attr("checked", false);
                        notification = 'false';
                    }else{
                        $("#cometchat_soundnotifications").attr("checked", true);
                        notification = 'true';
                    }
                    $.cookie(settings.cookiePrefix+"sound", notification, {path: '/', expires: 365});
                });
                $(".cometchat_popupnotifications_div").click(function(event){
                    event.stopPropagation();
                    var notification = 'false';
                    if($("#cometchat_popupnotifications").is(":checked")){
                        $("#cometchat_popupnotifications").attr("checked", false);
                        notification = 'false';
                    }else{
                        $("#cometchat_popupnotifications").attr("checked", true);
                        notification = 'true';
                    }
                    $.cookie(settings.cookiePrefix+"popup", notification, {path: '/', expires: 365});
                });
                $(".cometchat_readreceipt_div").click(function(event){
                    event.stopPropagation();
                    var notification = 'false';
                    if($("#cometchat_readreceipt").is(":checked")){
                        $("#cometchat_readreceipt").attr("checked", false);
                        notification = 'false';
                    }else{
                        $("#cometchat_readreceipt").attr("checked", true);
                        notification = 'true';
                    }
                    jqcc.cometchat.updateReadReceipt(notification);
                    $.cookie(settings.cookiePrefix+"read", notification, {path: '/', expires: 365});
                });

                $(".cometchat_disablelastseen").click(function(event){
                    event.stopPropagation();
                    lastseenflag = false;

                    if($("#cometchat_disablelastseen").is(":checked")){
                        $("#cometchat_disablelastseen").attr("checked", false);
                        lastseenflag = true;
                    }else{
                        $("#cometchat_disablelastseen").attr("checked", true);
                        lastseenflag = false;
                    }
                    $.ajax({
                        url: baseUrl+"cometchat_send.php",
                        data: {lastseenSettingsFlag: lastseenflag},
                        dataType: 'jsonp',
                        success: function(data){
                        }
                    });
                    $.cookie(settings.cookiePrefix+"disablelastseen", lastseenflag, {path: '/'});

                    $(".cometchat_lastseenmessage").each(function(){
                        id = parseInt($(this).attr('id').replace('cometchat_messageElement_',''));
                        var buddylastseen = jqcc.cometchat.getThemeArray('buddylistLastseen', id);
                        var statusmsg = jqcc.cometchat.getThemeArray('buddylistStatus', id);
                        var lstsnSetting = jqcc.cometchat.getThemeArray('buddylistLastseensetting', id);
                        var currentts = Math.floor(new Date().getTime()/1000);
                        if(lastseenflag){
                            jqcc[settings.theme].hideLastseen(id);
                        }else{
                            if(((statusmsg == 'away' || statusmsg == 'invisible' || statusmsg == 'busy' || statusmsg == 'offline') || currentts-buddylastseen > (60*10)) && lstsnSetting == '0')
                            jqcc[settings.theme].showLastseen(id,jqcc.cometchat.getThemeArray('buddylistLastseen',id));
                        }
                    })

                });

                $("#cometchat_lastseen").click(function(event){
                    event.stopPropagation();
                    lastseenflag = false;
                    var lastseen = jqcc.cometchat.getThemeArray('buddylistLastseen', jqcc.cometchat.getThemeVariable('openChatboxId')[0]);
                    var dt=eval(lastseen*1000);
                    var myDate = new Date(dt);
                    var year = myDate.getFullYear();
                    var day = myDate.getDate();
                    var month = myDate.getMonth()+1;
                    var h = myDate.getHours();
                    var m = myDate.getMinutes();

                    if(lastseenflag){
                        jqcc[settings.theme].hideLastseen(jqcc.cometchat.getThemeVariable('openChatboxId')[0]);
                    } else if(!lastseenflag){
                        if((jqcc.cometchat.getThemeArray('buddylistStatus', jqcc.cometchat.getThemeVariable('openChatboxId')[0]) == 'available')||(jqcc.cometchat.getThemeArray('buddylistStatus', jqcc.cometchat.getThemeVariable('openChatboxId')[0]) == 'offline' && jqcc.cometchat.getThemeArray('buddylistLastseensetting', jqcc.cometchat.getThemeVariable('openChatboxId')[0]) == 1)){
                            jqcc[settings.theme].hideLastseen(jqcc.cometchat.getThemeVariable('openChatboxId')[0]);
                        }
                        else if(jqcc.cometchat.getThemeArray('buddylistStatus', jqcc.cometchat.getThemeVariable('openChatboxId')[0]) == 'offline' && jqcc.cometchat.getThemeArray('buddylistLastseensetting', jqcc.cometchat.getThemeVariable('openChatboxId')[0]) == 0){
                            jqcc[settings.theme].showLastseen(jqcc.cometchat.getThemeVariable('openChatboxId')[0], jqcc.cometchat.getThemeArray('buddylistLastseen', jqcc.cometchat.getThemeVariable('openChatboxId')[0]));
                        }
                    }

                    jqcc.cometchat.setExternalVariable('lastseensetting', 'false');
                    if($("#cometchat_lastseen").is(":checked")){
                        lastseenflag = true;
                        if($("#cometchat_messageElement_"+jqcc.cometchat.getThemeVariable('openChatboxId')[0]).length == 1){
                            jqcc(".cometchat_lastseenmessage").remove();
                            $('#cometchat_user_'+jqcc.cometchat.getThemeVariable('openChatboxId')[0]+'_popup .cometchat_userdisplayname').css('padding','6px 0px 2px 0px');
                        }
                        jqcc.cometchat.setExternalVariable('lastseensetting', 'true');
                    }
                    $.ajax({
                        url: baseUrl+"cometchat_send.php",
                        data: {lastseenSettingsFlag: lastseenflag},
                        dataType: 'jsonp',
                        success: function(data){
                        }
                    });


                    $.cookie(settings.cookiePrefix+"disablelastseen", lastseenflag, {path: '/', expires: 365});
                });
                $("#cometchat_messagereceipt").click(function(event){
                    event.stopPropagation();
                    messagereceiptflag = 0;
                    jqcc.cometchat.setExternalVariable('messagereceiptsetting', messagereceiptflag);
                    if($("#cometchat_messagereceipt").is(":checked")){
                        messagereceiptflag = 1;
                    }
                    jqcc.cometchat.setExternalVariable('messagereceiptsetting', messagereceiptflag);

                    $.cookie(settings.cookiePrefix+"disablemessagereceipt", messagereceiptflag, {path: '/', expires: 365});
                });
                cometchat_optionsbutton_popup.find('input[name=cometchat_statusoptions]').on("change", function(e){
                    var status = $(this).attr('value');
                    jqcc.cometchat.sendStatus(status);
                });

                cometchat_optionsbutton_popup.find(".cometchat_statustextarea").blur(function(){
                    var statusmessage = cometchat_optionsbutton_popup.find('#cometchat_statusmessageinput .cometchat_statustextarea').val();
                    if(jqcc.cometchat.getThemeVariable('statusmessage') != statusmessage) {
                        jqcc[settings.theme].statusSendMessage();
                    }
                });
                cometchat_optionsbutton_popup.find("#cometchat_selfdisplayname").on("blur keypress",function(e){
                    if (e.type == "keypress" && e.keyCode == '13') {
                         e.preventDefault();
                    }
                    if (e.type == "blur") {
                        jqcc[settings.theme].setGuestName();
                    }
                });

                cometchat_optionsbutton_popup.find("textarea.cometchat_statustextarea").keydown(function(event){
                    return jqcc.cometchat.statusKeydown(event, this);
                });
                cometchat_optionsbutton_popup.find("input.cometchat_guestnametextbox").keydown(function(event){
                    return jqcc.cometchat.guestnameKeydown(event, this);
                });
                $('#cometchat_optionsbutton').mouseover(function(){
                    if(!cometchat_optionsbutton_popup.hasClass("cometchat_tabopen")){
                        $(this).addClass("cometchat_tabmouseover");
                    }
                });
                $('#cometchat_optionsbutton').mouseout(function(){
                    $(this).removeClass("cometchat_tabmouseover");
                    if(tooltipPriority==0){
                        $("#cometchat_tooltip").css('display', 'none');
                    }
                });
                var auth_logout = $('#cometchat_header').find("div#cometchat_authlogout");
            },
            logout_click: function(){
                    if(settings.ccauth.enabled=="1"){
                        jqcc.cometchat.sociallogout();
                    }else{
                        jqcc.ccdesktop.logout();
                    }

            },
            chatboxKeyup: function(event, chatboxtextarea, id){
            },
            chatboxKeydown: function(event, chatboxtextarea, id, force){
                var condition = 1;
                if((event.keyCode==13&&event.shiftKey==0)||force==1){
                    var message = $(chatboxtextarea).val();
                    message = message.replace(/^\s+|\s+$/g, "");
                    $(chatboxtextarea).val('');
                    $(chatboxtextarea).css('height', '20px');
                    $(chatboxtextarea).css('overflow-y', 'hidden');
                    $(chatboxtextarea).focus();
                    if(settings.floodControl){
                        condition = ((Math.floor(new Date().getTime()))-lastmessagetime>2000);
                    }
                    jqcc.cometchat.typingTo({id:id,method:'typingStop'});
                    if(message!=''){
                        if(condition){
                            var messageLength = message.length;
                            lastmessagetime = Math.floor(new Date().getTime());
                            if(jqcc.cometchat.getThemeArray('isJabber', id)!=1){
                                jqcc.cometchat.chatboxKeydownSet(id, message);
                            }else{
                                jqcc.ccjabber.sendMessage(id, message);
                            }
                        }else{
                            alert(language[53]);
                        }
                    }
                    return false;
                }
            },
            scrollDown: function(id){
                if(jqcc().slimScroll && mobileDevice == null){
                    $('#cometchat_tabcontenttext_'+id).slimScroll({scroll: '1'});
                }else{
                    setTimeout(function(){
                        $("#cometchat_tabcontenttext_"+id).scrollTop(50000);
                    }, 100);
                }
            },
            updateChatbox: function(id){
                if(jqcc.cometchat.getThemeArray('isJabber', id)!=1){
                    jqcc.cometchat.updateChatboxSet(id);
                }else{
                    jqcc.ccjabber.getRecentData(id);
                }
            },
            updateChatboxSuccess: function(id, data){
                var name = jqcc.cometchat.getThemeArray('buddylistName', id);
                if(typeof (jqcc[settings.theme].addMessages)!=='undefined'&&data.hasOwnProperty('messages')){
                    jqcc[settings.theme].addMessages(data['messages']);
                    if(jqcc.cometchat.getThemeVariable('currentStatus')!='invisible'){
                       if(typeof $("#cometchat_user_"+id+"_popup").find("div.cometchat_chatboxmessage:last-child").attr('id') != 'undefined'){
                            var messageid = $("#cometchat_user_"+id+"_popup").find("div.cometchat_chatboxmessage:last-child").attr('id').split('_')[2];
                        }
                        var message = {"id": messageid, "from": id, "self": 0};
                        if(typeof jqcc.cometchat.getCcvariable().lastmessagereadstatus[id] != "undefined" && jqcc.cometchat.getCcvariable().lastmessagereadstatus[id]==0){
                                jqcc.cometchat.sendReceipt(message, 'readMessageNotify');
                        }
                    }
                }
                jqcc[settings.theme].scrollDown(id);
            },
            applyChatBoxStates: function(params) {
                if(jqcc.cometchat.getThemeVariable('loggedout') == 0){
                    if(params != null){
                        $.each(params, function(index, state){
                            var id = state.id;
                            var silent = 0;
                            var count = 0;
                            if (state.hasOwnProperty('s')) {
                                silent = state.s;
                            }
                            if (state.hasOwnProperty('c')) {
                                count = state.c;
                            }
                            if(state.hasOwnProperty('g')&& state.g==1) {
                                if(typeof(jqcc.cometchat.silentroom)=='function'){
                                    if(silent>0) {
                                        jqcc.cometchat.silentroom(id, '', '', silent, count);
                                    } else if(count > 0) {
                                        jqcc.crembedded.addMessageCounter(id,count,0);
                                    }
                                }
                            }else {
                                if(typeof (jqcc[settings.theme].createChatbox)!=='undefined'){
                                    if(silent > 0){
                                        jqcc[settings.theme].createChatbox(
                                            id,
                                            jqcc.cometchat.getThemeArray('buddylistName', id),
                                            jqcc.cometchat.getThemeArray('buddylistStatus', id),
                                            jqcc.cometchat.getThemeArray('buddylistMessage', id),
                                            jqcc.cometchat.getThemeArray('buddylistAvatar', id),
                                            jqcc.cometchat.getThemeArray('buddylistLink', id),
                                            jqcc.cometchat.getThemeArray('buddylistIsDevice', id),
                                            silent,
                                            null,
                                            1
                                        );
                                    }
                                }
                                if(count>0) {
                                    jqcc[settings.theme].addPopup(id,count,0);
                                }
                            }
                        });
                    }
                }
            },

            openChatTab: function(opentab, restored) {
                if(opentab > 2 || opentab < 0 || typeof(opentab)!="number" || typeof(opentab)=="undefined" || isNaN(opentab)) {
                    opentab = 0;
                }
                if (opentab == 0 && settings.disableRecentTab == 1) {
                    $('#'+$(".cometchat_tab").first().attr('id')).click();
                    return;
                }
                if (opentab == 1 && settings.disableContactsTab == 1) {
                    $('#'+$(".cometchat_tab").first().attr('id')).click();
                    return;
                }
                if (opentab == 2 && hasChatroom == 0) {
                    $('#'+$(".cometchat_tab").first().attr('id')).click();
                    return;
                }
                if(opentab == 0) {
                    $('#cometchat_recenttab').click();
                } else if(opentab == 1) {
                    $('#cometchat_chatstab').click();
                } else if(opentab == 2) {
                    $('#cometchat_groupstab').click();
                }
            },

            windowResize: function(silent){
                if(settings.enableType == 1 && chatroomsonly != 1){
                    $('#cometchat').find('#cometchat_righttab #currentroom').find('.cometchat_user_closebox').remove();
                    $('#cometchat').find('#cometchat_righttab #currentroom').find('#vline').remove();
                    $('#cometchat').find('#cometchat_header').remove();
                    $('#cometchat').find('#cometchat_leftbar').remove();
                }else if(chatroomsonly == 1){
                    $('#cometchat').find('#cometchat_header').remove();
                }else{
                    $('.cometchat_float_list').hide();
                    $('#cometchat_smallmenu_float_list').remove();
                    $('#cometchat_newcompose_float_list').remove();
                    if($('#cometchat_container_stickers').length != 1 && $('#cometchat_container_voicenote').length != 1){
                        $('.cometchat_pluginuparrow').removeClass('rotated').css({'transform' : 'rotate(0deg)','-ms-transform': 'rotate(0deg)','-webkit-transform': 'rotate(0deg)'});
                    }
                    $('.cometchat_pluginrightarrow').removeClass('rotated').css({'transform' : 'rotate(0deg)','-ms-transform': 'rotate(0deg)','-webkit-transform': 'rotate(0deg)'});
                    var w=window,d=document,e=d.documentElement,g=d.getElementsByTagName('body')[0],winWidth=w.innerWidth||e.clientWidth||g.clientWidth,winHt=w.innerHeight||e.clientHeight||g.clientHeight;
                    var searchbar_Height = $("#searchbar").outerHeight(true);
                    var jabber_Height = $('#jabber_login').is(':visible') ? $('#jabber_login').outerHeight(true) : 0;
                    var usercontentHeight = winHt-$('#cometchat_header').outerHeight(true)-$('#cometchat_chatstab').outerHeight(true)-$('#cometchat_trayicons').outerHeight(true)-searchbar_Height-jabber_Height-21+'px';
                    var useSlimscroll = jqcc().slimScroll && mobileDevice == null;
                    var landscapeMobile = (winWidth > winHt)&&(mobileDevice);
                    var tabsubtitleHt = $(".cometchat_userchatarea").find('.cometchat_tabsubtitle').outerHeight(true);
                    var floatheight = $('.cometchat_float_list').height();
                    if($(window).height() < floatheight){
                        $(".cometchat_float_list").slimScroll({height: $(window).height()-50});
                    }
                    if(landscapeMobile){
                        /*$("html, body").scrollTop($(document).height());*/
                    }
                    if(useSlimscroll){
                        if($( ".right_footer" ).length == 1){
                            usercontentHeight = parseInt(usercontentHeight)-20+'px';
                        }
                        $('#cometchat_userscontent').parent('.slimScrollDiv').css('height',usercontentHeight);
                    }
                    $('#cometchat_userscontent').css('height',usercontentHeight);
                    var openChatboxId = jqcc.cometchat.getThemeVariable('openChatboxId')[0];
                    var openChatbox;
                    if(typeof jqcc('#cometchat_user_'+openChatboxId+'_popup').css('z-index') != 'undefined' && jqcc('#cometchat_user_'+openChatboxId+'_popup').css('z-index') > 0){
                        openChatbox = $("#cometchat_user_"+openChatboxId+"_popup");
                    } else{
                        openChatbox = $("#currentroom");
                    }
                    if($('#cometchat_righttab').css('top') == "0px"){
                        var chatboxHeight = winHt-openChatbox.find('.cometchat_tabsubtitle').outerHeight(true)-openChatbox.find("#cometchat_tabinputcontainer").outerHeight(true)-openChatbox.find('.cometchat_prependMessages_container').outerHeight(true);
                    }else{
                        var chatboxHeight = winHt-openChatbox.find('.cometchat_tabsubtitle').outerHeight(true)-openChatbox.find("#cometchat_tabinputcontainer").outerHeight(true)-$('#cometchat_self_container').outerHeight(true)-openChatbox.find('.cometchat_prependMessages_container').outerHeight(true);
                    }



                    if(useSlimscroll){
                        $(".cometchat_userchatbox").find(".cometchat_tabcontent").find("div.slimScrollDiv").css('height', (chatboxHeight)+'px');
                        $(".cometchat_textarea").parent("div.slimScrollDiv").css('height','auto');
                    }

                    $(".cometchat_userchatbox").find("div.cometchat_tabcontenttext").css('height',(chatboxHeight)+'px');
                    if(iOSmobileDevice && window.top != window.self){
                        $('#cometchat_user_'+openChatboxId+'_popup').find('.cometchat_userchatarea').css('display','block');
                        $('#cometchat_tabcontenttext_'+openChatboxId).css('height',$(window).height()-(jqcc('#cometchat_header').outerHeight(true)+jqcc('#cometchat_user_'+openChatboxId+'_popup').find('.cometchat_tabsubtitle').outerHeight()+jqcc('#cometchat_user_'+openChatboxId+'_popup').find('.cometchat_prependMessages').outerHeight(true)+jqcc('#cometchat_user_'+openChatboxId+'_popup').find("#cometchat_tabinputcontainer").outerHeight(true) - 100));
                    }
                    jqcc[settings.theme].adjustIcons($(window).width(),jqcc('#cometchat_user_'+openChatboxId+'_popup'),openChatboxId);
                    if($('#cometchat_container_stickers').length == 1 && mobileDevice){
                        jqcc[settings.theme].stickersKeyboard(winWidth,winHt,openChatboxId);
                        jqcc[settings.theme].keyboardResize('stickers',winHt,openChatbox);
                    } else if($('#cometchat_container_smilies').length == 1 && mobileDevice){
                        jqcc[settings.theme].smiliesKeyboard(winWidth,winHt,openChatboxId);
                        jqcc[settings.theme].keyboardResize('smilies',winHt,openChatbox);
                    }
                    if(hasChatroom == 1){
                        jqcc.crembedded.chatroomWindowResize();
                    }
                    var tab = $('#cometchat_righttab').width();
                    $('.cometchat_textarea').css('width',tab - 140);
                    if($('.cometchat_container').length == 0 ){
                        $('#cometchat_righttab').removeAttr('style');
                    }
                    if(document.activeElement.tagName == "INPUT" && mobileDevice){
                        window.setTimeout(function(){
                         document.activeElement.scrollIntoViewIfNeeded();
                     },0);
                    }
                    if(($('#cometchat_container_smilies').length == 1 || $('#cometchat_container_stickers').length == 1 || $('#cometchat_container_transliterate').length == 1 || $('#cometchat_container_voicenote').length == 1)){
                        $('.cometchat_container').css('width',$('#cometchat_righttab').width());
                        if(jqcc('#cometchat_user_'+openChatboxId+'_popup').css('display') == 'block'){
                            messagecontainer = $('#cometchat_tabcontenttext_'+openChatboxId);
                        } else{
                            messagecontainer = $("#currentroom").find("#currentroom_convo");
                        }
                        messagecontainer.css({'height':chatboxHeight- 200});
                        if(!mobileDevice){
                            messagecontainer.parent().css({'height':messagecontainer.height()});
                            messagecontainer.slimScroll({scroll: '1'});
                        }else{
                            messagecontainer.css({'overflow-y':'auto'});
                        }
                        jqcc[settings.theme].scrollDown(openChatboxId);
                    }
                    var newcompose = '';
                    var notificationicon = '';
                    if(trayicon.announcements){
                        notificationicon = '<div id="cometchat_notification" style="padding-top:6px;"><div id="cometchat_notification_icon" class="cometchat_notificationimages"></div></div>';
                    }
                    if(jqcc.cometchat.getChatroomVars('allowUsers') == 1 || trayicon.broadcastmessage) {
                        newcompose = '<div id = "cometchat_newcompose"><div id = "newcomposeimages"></div></div>';
                    }
                    var moreoption = '<div id = "moreoption" style="padding-top: 20px;"><div id="cometchat_more_icon" class="cometchat_moreimages"></div></div>';
                    var auth_logout = '';
                    var headericon = '';
                    if($(document).width() <= 414 ){
                        headericon = '<div id = "cometchat_smallmenu"><img src="<?php echo STATIC_CDN_URL; ?>layouts/<?php echo $layout;?>/images/menu.svg" height="24" width="24" class="smallmenuimages"/></div>';
                    }else{
                        headericon = newcompose+moreoption+notificationicon;
                    }
                    if(settings.ccauth.enabled=="1" || jqcc.cometchat.getCcvariable().callbackfn=='desktop'){
                        auth_logout = '<div id = "cometchat_authlogout" style="padding-top: 2px;"><div id="cometchat_logout_icon" class="cometchat_logoutimages" title="'+language[80]+'" onclick="javascript:jqcc.embedded.logout_click()"></div></div>';
                    }
                    $('#cometchat_header').find('#cometchat_self_right').html(headericon+auth_logout);
                }
            },
            chatWith: function(id){
                jqcc('#cometchat_userlist_'+id+" .cometchat_msgcount").remove();
                jqcc[settings.theme].calcPrevNoti();
                jqcc[settings.theme].calcNextNoti();
                if(jqcc.cometchat.getThemeVariable('loggedout')==0 && jqcc.cometchat.getUserID() != id){
                    if(jqcc.cometchat.getThemeVariable('offline')==1){
                        jqcc.cometchat.setThemeVariable('offline', 0);
                        jqcc.cometchat.chatHeartbeat(1);
                        jqcc.cometchat.sendStatus('available');
                    }
                    jqcc.cometchat.setThemeVariable('trayOpen','');
                    jqcc.cometchat.setThemeVariable('chatroomOpen','');
                    jqcc.cometchat.setThemeVariable('openChatboxId', [id]);
                    if(jqcc.cometchat.getThemeVariable('currentStatus')!='invisible'){
                       if(typeof $("#cometchat_user_"+id+"_popup .cometchat_message_container").find('.cometchat_chatboxmessage ').last().attr('id') != 'undefined'){
                            var messageid = $("#cometchat_user_"+id+"_popup .cometchat_message_container").find('.cometchat_chatboxmessage ').last().attr('id').split('_')[2];
                        }
                        var message = {"id": messageid, "from": id, "self": 0};
                        if(typeof jqcc.cometchat.getCcvariable().lastmessagereadstatus[id] != "undefined" && jqcc.cometchat.getCcvariable().lastmessagereadstatus[id]==0){
                                jqcc.cometchat.sendReceipt(message, 'readMessageNotify');
                        }
                    }
                    if(typeof (jqcc[settings.theme].createChatbox)!=='undefined'){
                        jqcc[settings.theme].createChatbox(id, jqcc.cometchat.getThemeArray('buddylistName', id), jqcc.cometchat.getThemeArray('buddylistStatus', id), jqcc.cometchat.getThemeArray('buddylistMessage', id), jqcc.cometchat.getThemeArray('buddylistAvatar', id), jqcc.cometchat.getThemeArray('buddylistLink', id), jqcc.cometchat.getThemeArray('buddylistIsDevice', id));
                    }
                }
            },
            scrollFix: function(){
                var elements = ['cometchat_tabcontainer', 'cometchat_userstab_popup', 'cometchat_optionsbutton_popup', 'cometchat_tooltip', 'cometchat_hidden'];
                if(jqcc.cometchat.getThemeVariable('openChatboxId').length>0){
                    elements.push('cometchat_user_'+jqcc.cometchat.getThemeVariable('openChatboxId')[0]+'_popup');
                }
                for(x in elements){
                    $('#'+elements[x]).css('position', 'absolute');
                    var bottom = parseInt($('#'+elements[x]).css('bottom'));
                    if(x==0){
                        bottom = 0;
                    }
                    var height = parseInt($('#'+elements[x]).height());
                    if(windowHeights[elements[x]]&&x!=3){
                        height = windowHeights[elements[x]];
                    }else{
                        windowHeights[elements[x]] = height;
                    }
                    $('#'+elements[x]).css('top', (parseInt($(window).height())-bottom-height+parseInt($(window).scrollTop()))+'px');
                }
            },
            checkPopups: function(silent){

            },
            launchModule: function(id){
                if($('#cometchat_container_'+id).length == 0){
                    $("#cometchat_trayicon_"+id).click();
                }
            },
            toggleModule: function(id){
                if($('#cometchat_container_'+id).length == 0){
                    $("#cometchat_trayicon_"+id).click();
                }
            },
            closeModule: function(id){
                if(jqcc(document).find('#cometchat_closebox_'+id).length > 0){
                    jqcc(document).find('#cometchat_closebox_'+id)[0].click();
                }
            },
            closeAllModule: function(){
                if(settings.showModules==1){
                    trayicon = jqcc.cometchat.getTrayicon();
                    for(x in trayicon){
                        if(x!='home' && x!='scrolltotop' && x!='chatrooms'){
                            if(jqcc('#cometchat_container_'+x).length > 0){
                                jqcc('#cometchat_container_'+x).detach();
                            }
                        }
                    }
                }
            },
            joinChatroom: function(roomid, inviteid, roomname){
                jqcc.cometchat.chatroom(roomid,roomname,0,inviteid,1,1);
            },
            closeTooltip: function(){
                $("#cometchat_tooltip").css('display', 'none');
            },
            scrollToTop: function(){
                $("html,body").animate({scrollTop: 0}, {"duration": "slow"});
            },
            reinitialize: function(){
                if(jqcc.cometchat.getThemeVariable('loggedout')==1){
                    $('#cometchat').html(cometchat_header);
                    $('#cometchat').append(cometchat_lefttab);
                    $('#cometchat').append(cometchat_righttab);
                    if(typeof(more_window) != "undefined") {
                        $('#cometchat').append(more_window);
                    }
                    jqcc[settings.theme].windowResize();
                    jqcc.cometchat.setThemeVariable('loggedout', 0);
                    jqcc.cometchat.setExternalVariable('initialize', '1');
                    jqcc.cometchat.chatHeartbeat();
                }
            },
            updateHtml: function(id, temp){
                if($("#cometchat_user_"+id+"_popup").length>0){
                    $("#cometchat_user_"+id+"_popup").find("#cometchat_tabcontenttext_"+id).find('.cometchat_message_container').html('<div>'+temp+'</div>');
                    jqcc[settings.theme].scrollDown(id);
                }else{
                    if(jqcc.cometchat.getThemeArray('trying', id)===undefined||jqcc.cometchat.getThemeArray('trying', id)<5){
                        setTimeout(function(){
                            $.cometchat.updateHtml(id, temp);
                        }, 1000);
                    }
                }
            },
            updateJabberOnlineNumber: function(number){
                jqcc.cometchat.setThemeVariable('jabberOnlineNumber', number);
                jqcc.cometchat.setThemeVariable('lastOnlineNumber', jqcc.cometchat.getThemeVariable('jabberOnlineNumber')+siteOnlineNumber);
                if(jqcc.cometchat.getThemeVariable('offline')==0){
                    $('#cometchat_userstab_text').html(language[9]+' ('+jqcc.cometchat.getThemeVariable('lastOnlineNumber')+')');
                }
                if(jqcc.cometchat.getThemeVariable('jabberOnlineNumber')>settings.searchDisplayNumber){
                    $('#cometchat_user_searchbar').css('display', 'block');
                }
            },
            userClick: function(listing,isrecent){
                var id = $(listing).attr('id');
                if(typeof(id) != "undefined"){
                    var list = id.split("_");
                    id = list[list.length-1];
                }
                jqcc.cometchat.setThemeVariable('trayOpen','');
                jqcc.cometchat.setThemeVariable('chatroomOpen','');
                jqcc.cometchat.setThemeVariable('openChatboxId', [id]);
                if(jqcc.cometchat.getThemeVariable('currentStatus')!='invisible'){

                    if(typeof $("#cometchat_user_"+id+"_popup .cometchat_message_container").find('.cometchat_chatboxmessage ').last().attr('id') != 'undefined'){
                        var messageid = $("#cometchat_user_"+id+"_popup .cometchat_message_container").find('.cometchat_chatboxmessage ').last().attr('id').split('_')[2];
                    }
                }
                if(typeof (jqcc[settings.theme].createChatbox)!=='undefined'){
                    jqcc[settings.theme].createChatbox(
                        id,
                        jqcc.cometchat.getThemeArray('buddylistName', id),
                        jqcc.cometchat.getThemeArray('buddylistStatus', id),
                        jqcc.cometchat.getThemeArray('buddylistMessage', id),
                        jqcc.cometchat.getThemeArray('buddylistAvatar', id),
                        jqcc.cometchat.getThemeArray('buddylistLink', id),
                        jqcc.cometchat.getThemeArray('buddylistIsDevice', id)
                    );
                }

                /*$("#cometchat_userlist_"+id).find(".cometchat_msgcount").remove();
                $("#cometchat_activech_"+id).find(".cometchat_msgcount").remove();
                $(listing).find(".cometchat_msgcount").remove();*/

                jqcc[settings.theme].calcPrevNoti();
                jqcc[settings.theme].calcNextNoti();
                jqcc[settings.theme].hideMenuPopup();
                if($('.cometchat_container').length > 0){
                    jqcc('.cometchat_container').remove();
                    jqcc[settings.theme].windowResize();
                }
            },
            hideMenuPopup: function(){
                $('#cometchat_plugins').removeClass('cometchat_tabopen');
                $('.cometchat_pluginsOption').find('.cometchat_menuOptionIcon').removeClass('cometchat_menuOptionIconClick');
                $('#cometchat_moderator_opt').removeClass('cometchat_tabopen');
                $('.cometchat_chatroomModOption').find('.cometchat_menuOptionIcon').removeClass('cometchat_menuOptionIconClick');
                $('#chatroomusers_popup').removeClass('cometchat_tabopen');
                $('.cometchat_chatroomUsersOption').find('.cometchat_menuOptionIcon').removeClass('cometchat_menuOptionIconClick');
                $('.menuOptionPopup.cometchat_tabpopup.cometchat_tabopen').removeClass('cometchat_tabopen');
            },
            messageBeep: function(staticCDNUrl){
                $('<audio id="messageBeep" style="display:none;"><source src="'+staticCDNUrl+'sounds/beep.mp3" type="audio/mpeg"><source src="'+staticCDNUrl+'sounds/beep.ogg" type="audio/ogg"><source src="'+staticCDNUrl+'sounds/beep.wav" type="audio/wav"></audio>').appendTo($("body"));
                $('<audio id="messageOpenBeep" style="display:none;"><source src="'+staticCDNUrl+'sounds/openbeep.mp3" type="audio/mpeg"><source src="'+staticCDNUrl+'sounds/openbeep.ogg" type="audio/ogg"><source src="'+staticCDNUrl+'sounds/openbeep.wav" type="audio/wav"></audio>').appendTo($("body"));
                $('<audio id="announcementBeep" style="display:none;"><source src="'+staticCDNUrl+'sounds/announcementbeep.mp3" type="audio/mpeg"><source src="'+staticCDNUrl+'sounds/announcementbeep.ogg" type="audio/ogg"><source src="'+staticCDNUrl+'sounds/announcementbeep.wav" type="audio/wav"></audio>').appendTo($("body"));
                $('<audio id="incommingcall" style="display:none;"><source src="'+staticCDNUrl+'sounds/incomingcallringtone.mp3" type="audio/mpeg"><source src="'+staticCDNUrl+'sounds/incomingcallringtone.ogg" type="audio/ogg"><source src="'+staticCDNUrl+'sounds/incomingcallringtone.wav" type="audio/wav"></audio>').appendTo($("body"));
                $('<audio id="outgoingcall" style="display:none;"><source src="'+staticCDNUrl+'sounds/outgoingcallringtone.mp3" type="audio/mpeg"><source src="'+staticCDNUrl+'sounds/outgoingcallringtone.ogg" type="audio/ogg"><source src="'+staticCDNUrl+'sounds/outgoingcallringtone.wav" type="audio/wav"></audio>').appendTo($("body"));
            },
            ccClicked: function(id){
                $(id).click();
            },
            ccAddClass: function(id, classadded){
                $(id).addClass(classadded);
            },
            moveLeft: function(){
                jqcc[settings.theme].moveBar("-=152px");
            },
            moveRight: function(){
                jqcc[settings.theme].moveBar("+=152px");
            },
            minimizeAll: function(){
                jqcc[settings.theme].hideMenuPopup();
                if(hasChatroom == 1){
                    jqcc[settings.theme].minimizeChatrooms();
                }
                jqcc('#cometchat_optionsbutton_popup.cometchat_tabopen').removeClass('cometchat_tabopen');
                jqcc('.cometchat_user_closebox,.cometchat_closebox').click();
            },
            iconNotFound: function(image, name){
                $('.'+name+'icon').attr({'src': staticCDNUrl+'modules/'+name+'/icon.png', 'width': '16px'});
            },
            minimizeOpenChatbox: function(){
                jqcc('.cometchat_tabpopup.cometchat_tabopen[id!=cometchat_userstab_popup]').find('.cometchat_minimizebox').click()[0];
            },
            prependMessagesInit: function(id){
                var messages = jqcc('#cometchat_tabcontenttext_'+id).find('.cometchat_chatboxmessage');
                $('#cometchat_prependMessages_'+id).text(language[41]);
                jqcc('#cometchat_prependMessages_'+id).attr('onclick','');
                if(messages.length > 0){
                    jqcc('#scrolltop_'+id).remove();
                    prepend = messages[0].id.split('_')[2];
                }else{
                    prepend = -1;
                }
                jqcc.cometchat.updateChatboxSet(id,prepend);
            },
            prependMessages:function(id,data){
                var oldMessages = '';
                var count = 0;
                var todaysdate = new Date();
                var tdmonth  = todaysdate.getMonth();
                var tddate  = todaysdate.getDate();
                var tdyear = todaysdate.getFullYear();
                var today_date_class = tdmonth+"_"+tddate+"_"+tdyear;
                var ydaysdate = new Date((new Date()).getTime() - 3600000 * 24);
                var ydmonth  = ydaysdate.getMonth();
                var yddate  = ydaysdate.getDate();
                var ydyear = ydaysdate.getFullYear();
                var yday_date_class = ydmonth+"_"+yddate+"_"+ydyear;
                var d = '';
                var month = '';
                var date  = '';
                var year = '';
                var msg_date_class = '';
                var msg_date = '';
                var date_class = '';
                var msg_date_format = '';
                var msg_time = '';
                var jabber = '';
                var prepend = '';

                $.each(data, function(type, item){
                    if(type=="messages"){
                        $.each(item, function(i, incoming){
                            count = count+1;
                            var selfstyle = '';
                            var selfmessage = '';
                            var imagemsg_avtar = '';
                            var imagemsg_rightcss = '';
                            var fromname = jqcc.cometchat.getThemeArray('buddylistName', incoming.from);
                            var fromavatar = '';
                            var messagewrapperid = '';
                            incoming.message = jqcc.cometchat.htmlEntities(incoming.message);
                            var message = jqcc.cometchat.processcontrolmessage(incoming);
                            if( (!incoming.hasOwnProperty('id') || incoming.id == '') && (!incoming.hasOwnProperty('localmessageid') || incoming.localmessageid == '') && (incoming.message).indexOf('CC^CONTROL_')==-1){
                                return;
                            }

                            if(incoming.hasOwnProperty('botid') && incoming.botid > 0){
                                incoming.self = 0;
                            }
                            if(message == null){
                                return;
                            }

                            /** START: Audio/ Video Chat */
                            if(message.indexOf('avchat_webaction=initiate')!=-1 || message.indexOf('avchat_webaction=acceptcall')!=-1){
                                return;
                            }
                            if(message.indexOf('audiochat_webaction=initiate')!=-1 || message.indexOf('audiochat_webaction=acceptcall')!=-1){
                                return;
                            }
                            /** END: Audio/ Video Chat */

                            if( incoming.hasOwnProperty('id') && !incoming.hasOwnProperty('localmessageid') ) {
                                messagewrapperid = incoming.id;
                            }else if( !incoming.hasOwnProperty('id') && incoming.hasOwnProperty('localmessageid') ) {
                                messagewrapperid = incoming.localmessageid;
                            }else{
                                messagewrapperid = incoming.id;
                                if($("#cometchat_message_"+incoming.localmessageid).length>0){
                                    $("#cometchat_message_"+incoming.localmessageid).attr('id',"cometchat_message_"+incoming.id);
                                    $("#cometchat_chatboxseen_"+incoming.localmessageid).attr('id',"cometchat_chatboxseen_"+incoming.id).removeClass("cometchat_offlinemessage");
                                    $("#message_"+incoming.localmessageid).attr('id','message_'+incoming.id).html(message);
                                    var offlinemessages = jqcc.cometchat.getFromStorage('offlinemessagesqueue');
                                    if(offlinemessages.hasOwnProperty(incoming.localmessageid)) {
                                        delete offlinemessages[incoming.localmessageid];
                                        jqcc.cometchat.updateToStorage('offlinemessagesqueue',offlinemessages);
                                    }
                                    return;
                                }
                            }

                            var smileycount = (message.match(/cometchat_smiley/g) || []).length;
                            var smileymsg = message.replace(/<img[^>]*>/g,"");
                            smileymsg = smileymsg.trim();

                            if(smileycount == 1 && smileymsg == '') {
                                message = message.replace('height="20"', 'height="64px"');
                                message = message.replace('width="20"', 'width="64px"');
                            }
                            if(message.indexOf('<img')!=-1){
                                if(incoming.self != 1){
                                    imagemsg_rightcss = 'position:relative;margin-left:-20px !important';
                                    if(smileymsg == '' || smileymsg.indexOf('class="cometchat_hw_lang"') != -1 || smileymsg.indexOf('mediatype="1"') != -1){
                                        imagemsg_avtar = 'margin-top:-40px;';
                                        imagemsg_rightcss = 'position:relative;margin-left:-20px !important';
                                    }
                                }else if(incoming.self == 1 && smileymsg == ''){
                                }
                                if(message.indexOf('cometchat_smiley') != -1 && smileycount != 1 && smileymsg == ''){
                                    imagemsg_avtar = '';
                                    if(incoming.self != 1){
                                        imagemsg_rightcss = 'position:relative;margin-top:5px !important;margin-left:-20px !important';
                                    }
                                }
                                if(smileymsg != '' && smileycount > 0 && incoming.self != 1){
                                    imagemsg_rightcss = '';
                                }
                            }
                            if(parseInt(incoming.self)==1 && (!incoming.hasOwnProperty('botid') || (typeof incoming.botid == "undefined" && incoming.botid == 0))){
                                fromname = language[10];
                                selfstyle = ' cometchat_self';
                                selfmessage = ' cometchat_messagebox_self';
                            }else{
                                fromavatar = '<span class="cometchat_other_avatar" style="'+imagemsg_avtar+'"><img class="cometchat_userscontentavatarsmall" src="'+jqcc.cometchat.getThemeArray('buddylistAvatar', incoming.from)+'"></span>';
                            }

                            if(incoming.jabber == 1 && typeof(incoming.selfadded) != "undefined" && incoming.selfadded != null) {
                                 msg_time = messagewrapperid;
                                 jabber = 1;
                            }else{
                                 msg_time = incoming.sent;
                                 jabber = 0;
                            }
                            msg_time = msg_time+'';

                            if (msg_time.length == 10){
                                msg_time = parseInt(msg_time * 1000);
                            }

                            months_set = new Array(language['jan'],language['feb'],language['mar'],language['apr'],language['may'],language['jun'],language['jul'],language['aug'],language['sep'],language['oct'],language['nov'],language['dec']);

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
                                msg_date = language['today'];
                            }else  if(msg_date_class == yday_date_class){
                                date_class = "yesterday";
                                msg_date = language['yesterday'];
                            }

                           if(incoming.sent!=null){
                                var ts = incoming.sent;
                                sentdata = jqcc[settings.theme].getTimeDisplay(ts, incoming.from,parseInt(incoming.self));
                            }


                            var add_bg = 'cometchat_chatboxmessage'+selfstyle;
                            if((message.indexOf('<img')!=-1 && message.indexOf('src')!=-1 && message.indexOf('cometchat_smiley') == -1) || (smileycount > 0 && smileymsg == '')){
                               if( incoming.self === 1 ) {
                                    add_bg = 'cometchat_chatboxselfmedia';
                                }else {
                                    add_bg = 'cometchat_chatboxmedia';
                                }
                            }
                            var msg = '<div class="cometchat_time cometchat_time_'+msg_date_class+' '+date_class+'" msg_format="'+msg_date_format+'">'+msg_date+'</div><div class="cometchat_messagebox '+selfmessage+'"><div class="'+add_bg+'" id="cometchat_message_'+messagewrapperid+'"><span class="cometchat_chatboxmessagecontent" style="'+imagemsg_rightcss+'">'+message+'</span>'+fromavatar+'</div><span id="cometchat_chatboxseen_'+messagewrapperid+'"></span>'+sentdata+'</div><div style="clear:both"></div>';
                            oldMessages+=msg;
                            if($("#cometchat_message_"+messagewrapperid).length>0){
                                $('#cometchat_message_'+messagewrapperid).html(message);
                            }
                        });
                    }
                });

                var current_top_element  = jqcc('#cometchat_tabcontenttext_'+id+' .cometchat_chatboxmessage:first');
                jqcc('#cometchat_tabcontenttext_'+id).find('.cometchat_message_container').prepend(oldMessages);

                jqcc[settings.theme].groupbyDate(id,jabber);

                $('#cometchat_user_'+id+'_popup .cometchat_prependMessages').remove();
                if(jqcc.cometchat.getThemeVariable('prependLimit') != '0'){
                    prepend = '<div class=\"cometchat_prependMessages\" onclick\="jqcc.embedded.prependMessagesInit('+id+')\" id = \"cometchat_prependMessages_'+id+'\">'+language[83]+'</div>';
                }

                if($('#cometchat_user_'+id+'_popup .cometchat_prependMessages').length != 1){
                        $('#cometchat_tabcontenttext_'+id).prepend(prepend);
                }

                if((count - parseInt(jqcc.cometchat.getThemeVariable('prependLimit')) < 0)){
                    $('#cometchat_user_'+id+'_popup').find('#cometchat_prependMessages_'+id).text(language[84]);
                    $('#cometchat_user_'+id+'_popup').find('#cometchat_prependMessages_'+id).attr('onclick','');
                    $('#cometchat_user_'+id+'_popup').find('#cometchat_prependMessages_'+id).css('cursor','default');
                }else{
                    $('#cometchat_user_'+id+'_popup').find('#cometchat_prependMessages_'+id).attr('onclick','jqcc.embedded.prependMessagesInit('+id+')');
                }

                jqcc[settings.theme].windowResize();
                if(jqcc().slimScroll && mobileDevice == null){
                    if(current_top_element.length>0){
                        var offsetheight = 0;
                        var offsetheight = current_top_element.offset().top - jqcc('#cometchat_tabcontenttext_'+id+' .cometchat_chatboxmessage:first').offset().top+jqcc('.cometchat_time').height()+jqcc('#cometchat_prependMessages_'+id).height()+100;
                        var height = offsetheight-jqcc('#cometchat_tabcontenttext_'+id).height();
                        $('#cometchat_tabcontenttext_'+id).slimScroll({scrollTo: height+'px'});
                    }else{
                        $('#cometchat_tabcontenttext_'+id).slimScroll({scroll: 1});
                    }
                }
            },
            groupbyDate: function(id,j){
                    if(j == '0' ){
                     $('#cometchat_user_'+id+'_popup .cometchat_time').hide();
                     $.each($('#cometchat_user_'+id+'_popup .cometchat_time'),function (i,divele){
                        var classes = $(divele).attr('class').split(/\s+/);
                        for(var i in classes){
                            if(typeof classes[i] == 'string'){
                                if(classes[i].indexOf('cometchat_time_') === 0){
                                    $('#cometchat_user_'+id+'_popup .'+classes[i]+':first').css('display','table');
                                }
                            }
                        }
                    });
                 }else{
                    $('#cometchat_tabcontenttext_'+id+' .cometchat_time').hide();
                    $.each($('#cometchat_tabcontenttext_'+id+' .cometchat_time'),function (i,divele){
                        var classes = $(divele).attr('class').split(/\s+/);
                        for(var i in classes){
                            if(typeof classes[i] == 'string'){
                                    if(classes[i].indexOf('cometchat_time_') === 0){
                                        $('#cometchat_tabcontenttext_'+id+' .'+classes[i]+':first').css('display','table');
                                    }

                            }
                        }
                    });
                }
            },
            showLastseen:function(id,lastseen){
                var lastseen = lastseen;
                var timest = getTimeDisplay(lastseen);
                if(lastseen != "" && lastseen != 0){
                    if(timest.ytt != ""){
                        lastseenDIV = timest.hour+":"+timest.minute+timest.ap+" <span>"+timest.ytt+"</span>";
                    }else{
                        lastseenDIV = timest.hour+":"+timest.minute+timest.ap+' '+timest.date+timest.type+' '+timest.month;
                    }
                    if($('#cometchat_messagElement_'+id).attr('class') != undefined){
                        if($('#cometchat_messagElement_'+id).attr('class').indexOf('cometchat_showOffline') == -1){
                           $('#cometchat_messagElement_'+id).html(language['last_seen']+' '+lastseenDIV);
                           $('#cometchat_messagElement_'+id).addClass('cometchat_lastseenmessage');
                        }
                    }
                    if(jqcc('#cometchat_messagElement_'+id).is(":hidden")){
                        $('#cometchat_messagElement_'+id).slideDown(500);
                    }
                }
            },
            hideLastseen:function(id){
                $('#cometchat').find(function(){
                    if(jqcc('#cometchat_messagElement_'+id).is(":visible") && jqcc('#cometchat_messagElement_'+id).attr('class').indexOf('cometchat_showOffline') == -1)
                        $('#cometchat_messagElement_'+id).slideUp(500);
                });
            },
            formatlang: function(str) {
                return str[0].toUpperCase()+str.substr(1).toLowerCase();
            },
            changeLanguage: function() {
                setCookie('{$cookiePrefix}language','',0);
                location.href = 'index.php?cc_layout={$cc_layout}&caller={$caller}&id={$toId}&embed={$embed}&basedata={$baseData}&chatroommode={$chatroommode}';
            },
            setCookie: function(cookie_name, cookie_value, cookie_life) {
                var today = new Date();
                var expiry = new Date(today.getTime() + cookie_life * 24*60*60*1000);
                var cookie_string =cookie_name + "=" + encodeURI(cookie_value);
                if(cookie_life){ cookie_string += "; expires=" + expiry.toGMTString();}
                cookie_string += "; path=/";
                document.cookie = cookie_string;
            },
            stickersKeyboard: function(winWidth,winHt,id) {
            },
            smiliesKeyboard: function(winWidth,winHt,id) {
            },
            keyboardResize: function(plugin,winHt,openChatbox){
            },
            chatScroll: function(id){
                if($('#scrolltop_'+id).length == 0){
                    $("#cometchat_tabcontenttext_"+id).prepend('<div id="scrolltop_'+id+'" class="cometchat_scrollup"><img src="'+staticCDNUrl+'images/arrowtop.svg" class="cometchat_scrollimg" /></div>');
                }
                if($('#scrolldown_'+id).length == 0){
                    $("#cometchat_tabcontenttext_"+id).append('<div id="scrolldown_'+id+'" class="cometchat_scrolldown"><img src="'+staticCDNUrl+'images/arrowbottom.svg" class="cometchat_scrollimg" /></div>');
                }
                $('#cometchat_tabcontenttext_'+id).unbind('wheel');
                $('#cometchat_tabcontenttext_'+id).on('wheel',function(event){
                    var scrollTop = $(this).scrollTop();
                    if(event.originalEvent.deltaY != 0){
                        clearTimeout($.data(this, 'scrollTimer'));
                        if(event.originalEvent.deltaY > 0){
                            $('#scrolltop_'+id).hide();
                            var down = jqcc("#cometchat_tabcontenttext_"+id)[0].scrollHeight-250-50;
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

                $('#scrolltop_'+id).on("click",function(){
                    $('#scrolltop_'+id).hide();
                    $('#cometchat_tabcontenttext_'+id).slimScroll({scroll: 0});
                });

                $('#scrolldown_'+id).click(function(){
                    $('#scrolldown_'+id).hide();
                    $('#cometchat_tabcontenttext_'+id).slimScroll({scroll: 1});
                });
            },
            generateOutgoingAvchatData: function(id,grp){
                var userdata = {
                    name: jqcc.cometchat.getThemeArray('buddylistName', id),
                    avatar: jqcc.cometchat.getThemeArray('buddylistAvatar', id)
                }
                var controlparameters = {
                    type: "core",
                    name: "libraries",
                    method: "outgoingCall",
                    params: {
                        id: id,
                        grp: grp,
                        userdata: userdata
                    }
                }
                var messagetopost = "CC^CONTROL_"+ JSON.stringify(controlparameters);
                jqcc[settings.theme].postAVControlMessage(messagetopost);
            },
            generateIncomingAvchatData: function(incoming,avchat_data,currenttime){
                var userdata = {
                    name: jqcc.cometchat.getThemeArray('buddylistName', incoming.from),
                    avatar: jqcc.cometchat.getThemeArray('buddylistAvatar', incoming.from)
                }
                var controlparameters = {
                    type: "core",
                    name: "libraries",
                    method: "incomingCall",
                    params: {
                        incoming: incoming,
                        avchat_data: avchat_data,
                        currenttime: currenttime,
                        userdata: userdata
                    }
                }
                var messagetopost = "CC^CONTROL_"+ JSON.stringify(controlparameters);
                jqcc[settings.theme].postAVControlMessage(messagetopost);
            },
            postAVControlMessage:function(message){
               try{
                    if(typeof(parent.jqcc.cometchat.getSettings) == "function"){
                        parent.postMessage(message,'*');
                    }else{
                        postMessage(message,'*');
                    }
               }catch(e){
                    postMessage(message,'*');
               }
            },
            removeAVchatContainer: function(id) {
                var controlparameters = {
                    type: "core",
                    name: "libraries",
                    method: "removeCallContainer",
                    params: {
                        id: id
                    }
                }
                var messagetopost = "CC^CONTROL_"+ JSON.stringify(controlparameters);
                jqcc[settings.theme].postAVControlMessage(messagetopost);
            }
        };
    })();
})(jqcc);

if(typeof(jqcc.embedded) === "undefined"){
    jqcc.embedded=function(){};
}

jqcc.extend(jqcc.embedded, jqcc.ccembedded);
