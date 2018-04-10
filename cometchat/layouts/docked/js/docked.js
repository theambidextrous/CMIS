
(function($){
    $.ccdocked = (function(){
        var settings = {};
        var baseUrl;
        var staticCDNUrl;
        var basedata;
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
        var allChatboxes = {};
        var chatboxDistance = 10;
        var visibleTab = [];
        var blinkInterval;
        var trayWidth = 0;
        var siteOnlineNumber = 0;
        var olddata = {};
        var tooltipPriority = 0;
        var desktopNotifications = {};
        var webkitRequest = 0;
        var chatbottom = [];
        var resynch = 0;
        var reload = 0;
        var lastmessagetime = Math.floor(new Date().getTime());
        var favicon;
        var msg_beep = '';
        var side_bar = '';
        var option_button = '';
        var user_tab = '';
        var chat_boxes = '';
        var chat_left = '';
        var unseen_users = '';
        var usertab2 = '';
        var checkfirstmessage;
        var chatboxHeight = parseInt('<?php echo $chatboxHeight; ?>');
        var chatboxWidth = parseInt('<?php echo $chatboxWidth; ?>');
        var bannedMessage = '<?php echo $bannedMessage;?>';
        var lastseen = 0;
        var lastseenflag = false;
        var barVisiblelimit = (chatboxWidth + chatboxDistance + 14);
        var messagereceiptflag = 0;
        var hasChatroom = 0;
        var allowGuests = <?php echo $allowGuests; ?>;
        var crguestsMode = <?php echo $crguestsMode; ?>;
        var guestsMode = <?php echo $guestsMode; ?>;
        var mobileDevice = navigator.userAgent.match(/ipad|ipod|iphone|android|windows ce|Windows Phone|blackberry|palm|symbian/i);
        var timer;
        var disableLayout=0;
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
                basedata = jqcc.cometchat.getBaseData();
                language = jqcc.cometchat.getLanguage();
                trayicon = jqcc.cometchat.getTrayicon();
                jqcc.cometchat.setThemeVariable('dockedAlignment', settings.dockedAlignToLeft == 1 ? 'left' : 'right');
                if (settings.disableDockedLayout == '1' && settings.forceDockedEnable == '0') {
                    return false;
                }
                var modules = '';
                var login_mode = '';
                var announcementmodule = '';
                if(settings.windowFavicon==1){
                    favicon = new Favico({
                        animation: 'pop'
                    });
                }

                if(settings.usebots == 1){
                    var trayiconclick = 'onclick="jqcc.'+settings.theme+'.showBots();"';
                    modules = '<div id="cometchat_module_bots" class="cometchat_lightdisplay cometchat_module" '+trayiconclick+'>'+language['bots']+'</div>';
                }

                if(settings.ccauth.enabled == "1"){
                     login_mode = jqcc[settings.theme].authLogin();
                }else{
                    login_mode = '<div id="cometchat"></div><div id="cometchat_hidden"><div id="cometchat_hidden_content"></div></div><div id="cometchat_tooltip"><div class="cometchat_tooltip_content"></div></div>';
                }
                $("body").append(login_mode);

                var hasBroadcastMessage = 0;
                if(settings.showModules==1){
                    var listedmodules = ['chatrooms', 'announcements', 'broadcastmessage'];
                    var trayiconclick = '';
                    for(x in trayicon){
                        if(trayicon.hasOwnProperty(x)){
                            if(x=='home') {
                                trayiconclick = 'onclick="javascript:jqcc.cometchat.goToHomePage()"';
                            } else if(x=='scrolltotop') {
                                trayiconclick = 'onclick="javascript:jqcc.cometchat.scrollToTop()"';
                            } else {
                                trayiconclick = 'onclick="jqcc.cometchat.lightbox(\''+x+'\')"';
                            }
                            if(listedmodules.indexOf(x) == -1) {
                                modules += '<div id="cometchat_module_'+x+'" class="cometchat_lightdisplay cometchat_module" '+trayiconclick+'>'+trayicon[x][1]+'</div>';
                            }
                        }
                        if(x=='announcements'){
                            announcementmodule = '<div id="cometchat_alertsicon" class="cometchat_tabicons"></div>';
                        } else if(x=='broadcastmessage'){
                            hasBroadcastMessage = 1;
                        }
                    }
                }
                if(settings.disableGroupTab == 0) {
                     hasChatroom = 1;
                }
                var usertab = '';
                var usertabpop = '';
                var findUser = '<div id="cometchat_searchbar" class=""><div id="cometchat_searchbar_icon"></div><div class="cometchat_closeboxsearch cometchat_tooltip" id="close_user_search" title="'+language[115]+'"></div><input id="cometchat_search" type="text" name="cometchat_search" class="cometchat_search cometchat_search_light textInput" placeholder="'+language[18]+'"></div>';

                var statusmessagecount = 140;
                var blockeduserscount = 0;
                var optionsbuttonpop = '';
                var manageblockedusers = '';
                var sociallogout = '';
                var titlewidth = '75%';
                if(settings.ccauth.enabled == 1){
                    titlewidth = '70%';
                    sociallogout = '<div class="cometchat_authlogoutimage cometchat_tooltip" id="cometchat_authlogout" title="'+language[80]+'"></div>';
                }
                var readreceipthtml = '';
                if(settings.cometserviceEnabled == 1){
                    readreceipthtml = '<div><div class="cometchat_lightdisplay"><span class="cometchat_checkbox">'+language['show_read_receipt']+'</span><label class="cometchat_checkboxcontrol cometchat_checkboxouter" id="cometchat_readreceipt_label"><input type="checkbox" class="cometchat_checkbox" name="cometchat_readreceipt" id="cometchat_readreceipt"><div class="cometchat_controlindicator"></div><div class="cometchat_checkindicator"></div></label></div></div>';
                }
                if(jqcc.inArray('block',settings.plugins) != -1){
                    manageblockedusers = '<div id="cometchat_blockedusersoptions"><div class="cometchat_lightdisplay"><span style="padding-right:22px;">'+language['blocked_users']+'</span><span class="cometchat_arrowright"></span><span id="cometchat_blockeduserscount">'+blockeduserscount+'</span></div></div>';
                }
                if(settings.showSettingsTab==1 || 1){

                    var lastseenoption = '';

                    if(settings.lastseen == 1){
                       lastseenoption = '<div class="cometchat_lightdisplay"><span class="cometchat_checkbox">'+language[108]+'</span><label class="cometchat_checkboxcontrol cometchat_checkboxouter" id="cometchat_disablelastseen_label"><input type="checkbox" class="cometchat_checkbox" name="cometchat_disablelastseen" id="cometchat_disablelastseen"><div class="cometchat_controlindicator"></div><div class="cometchat_checkindicator"></div></label></div>';
                    } else{
                        lastseenflag = true;
                    }

                    optionsbuttonpop = '<div id="cometchat_optionsbutton_popup" class="cometchat_tabpopup cometchat_tabhidden"><div class="cometchat_userstabtitle"><div class="cometchat_userstabtitletext" style="width: '+titlewidth+';text-align: center;">'+language['more']+'</div><div class="cometchat_closebox cometchat_tooltip" id="cometchat_minimize_optionsbuttonpopup" title="'+language[63]+'"></div>'+sociallogout+'</div><div id="cometchat_optionscontent" class="cometchat_tabcontent cometchat_optionstyle" style="overflow:hidden;"><form id="cometchat_optionsform"><div id="cometchat_selfname"><div class="cometchat_chats_labels">'+language[43].toUpperCase()+'</div><textarea readonly id="cometchat_displayname" class="cometchat_lightdisplay"></textarea></div><div id="cometchat_statusmessage"><div class="cometchat_chats_labels">'+language[2].toUpperCase()+'</div><div id="cometchat_statusmessageinput"><textarea class="cometchat_statustextarea" maxlength="140"></textarea><div class="cometchat_statusmessagecount">'+statusmessagecount+'</div></div></div><div class="cometchat_statusinputs"><div class="cometchat_chats_labels">'+language[23].toUpperCase()+'</div><div><div class="cometchat_optionsstatus available cometchat_lightdisplay"><div class="cometchat_optionsstatus2 cometchat_user_available"></div>'+language['available']+'<label class="cometchat_statusradio"><input id="cometchat_statusavailable_radio" type="radio" name="cometchat_statusoptions" value="available" checked><span class="cometchat_radio_outer"><span class="cometchat_radio_inner"></span></span></label></div><div class="cometchat_optionsstatus away cometchat_lightdisplay"><div class="cometchat_optionsstatus2 cometchat_user_away" ></div>'+language['away']+'<label class="cometchat_statusradio"><input id="cometchat_statusaway_radio" type="radio" name="cometchat_statusoptions" value="away"><span class="cometchat_radio_outer"><span class="cometchat_radio_inner"></span></span></label></div><div class="cometchat_optionsstatus busy cometchat_lightdisplay"><div class="cometchat_optionsstatus2 cometchat_user_busy"></div>'+language['busy']+'<label class="cometchat_statusradio"><input id="cometchat_statusbusy_radio" type="radio" name="cometchat_statusoptions" value="busy"><span class="cometchat_radio_outer"><span class="cometchat_radio_inner"></span></span></label></div><div class="cometchat_optionsstatus cometchat_gooffline cometchat_lightdisplay"><div class="cometchat_optionsstatus2 cometchat_user_invisible"></div>'+language['invisible']+'<label class="cometchat_statusradio"><input id="cometchat_statusinvisible_radio" type="radio" name="cometchat_statusoptions" value="invisible"><span class="cometchat_radio_outer"><span class="cometchat_radio_inner"></span></span></label></div></div></div><div class="cometchat_options_disable"><div class="cometchat_chats_labels">'+language['notifications'].toUpperCase()+'</div><div><div class="cometchat_lightdisplay"><span class="cometchat_checkbox">'+language[13]+'</span><label class="cometchat_checkboxcontrol cometchat_checkboxouter" id="cometchat_soundnotifications_label"><input type="checkbox" class="cometchat_checkbox" name="cometchat_soundnotifications" id="cometchat_soundnotifications"><div class="cometchat_controlindicator"></div><div class="cometchat_checkindicator"></div></label></div><div class="cometchat_lightdisplay"><span  class="cometchat_checkbox">'+language[24]+'</span><label class="cometchat_checkboxcontrol cometchat_checkboxouter" id="cometchat_popupnotifications_label"><input type="checkbox" class="cometchat_checkbox" name="cometchat_popupnotifications" id="cometchat_popupnotifications"><div class="cometchat_controlindicator"></div><div class="cometchat_checkindicator"></div></label></div>'+readreceipthtml+lastseenoption+'</div></div><div class="cometchat_chats_labels"></div>'+manageblockedusers+'<div class="cometchat_chats_labels"></div><div id="cometchat_moduleslist">'+modules+'</div></form></div></div>';
                }

                var buddylist = '';
                var recentchats = '';
                var groups = '';
                var tabcount = 0;
                var tabcontent = '';
                var newchatoption = '';
                var groupstab = '';
                var recenttab = '';
                var contactstab = '';

                if(settings.disableRecentTab == 0) {
                    recenttab = '<div id="cometchat_recenttab" class="cometchat_tab" unselectable="on"><span id="cometchat_recenttab_text" class="cometchat_tabstext">'+language['recent_chats']+'</span></div>';
                    recentchats = '<div id="cometchat_recentlist" class="cometchat_tabhidden"><div id="cometchat_recentlist_content"><div class="cometchat_recentlisttext">'+language['no_recent_chats']+'</div></div></div>';
                    tabcount++;
                }

                if(settings.disableContactsTab == 0) {
                    contactstab = '<div id="cometchat_contactstab" class="cometchat_tab cometchat_tab_clicked" unselectable="on"><span id="cometchat_chatstab_text" class="cometchat_tabstext">'+language['contacts']+'</span></div>';
                    buddylist = '<div id="cometchat_contactslist" class="cometchat_tabopen"><div id="cometchat_userslist_content"><div class="cometchat_chatlisttext">'+language[28]+'</div></div></div>';
                    tabcount++;
                }

                if(hasChatroom) {
                    groupstab = '<div id="cometchat_groupstab" class="cometchat_tab" unselectable="on"><span id="cometchat_groupstab_text" class="cometchat_tabstext">'+language['groups']+'</span></div>';
                    groups = '<div id="cometchat_groupslist" class="cometchat_tabhidden"><div id="cometchat_groupslist_content"><div class="cometchat_chatlisttext">'+language['no_groups']+'</div></div></div>';
                    tabcount++;
                }

                if((hasBroadcastMessage == 1 && settings.disableContactsTab == 0) || (jqcc.cometchat.getChatroomVars('allowUsers') == 1 && hasChatroom == 1)){
                    newchatoption = '<div id="cometchat_newchat" class="cometchat_tabicons"></div>';
                }
                if(settings.showOnlineTab==1){
                    tabcontent = '<div class=\'cometchat_tablerow\'>'+recenttab+contactstab+groupstab+'</div>';

                    usertab = '<span id="cometchat_userstab" class="cometchat_tab"><span id="cometchat_userstab_text">'+language[9]+'</span></span>';

                    usertabpop = '<div id="cometchat_userstab_popup" class="cometchat_tabpopup cometchat_tabhidden"><div class="cometchat_userstabtitle"><div class="cometchat_userstabtitletext cometchat_tabtitle_header">'+language[9]+'</div><div class="cometchat_closebox cometchat_tooltip" id="cometchat_minimize_userstabpopup" title="'+language[62]+'"></div><div class="cometchat_vline"></div>'+newchatoption+announcementmodule+'<div id="cometchat_moreicon" class="cometchat_tabicons"></div></div><div id="cometchat_tabcontainer">'+tabcontent+'</div>'+findUser+'<div class="cometchat_tabcontent cometchat_tabstyle"><div id="cometchat_userscontent">'+recentchats+buddylist+groups+'</div></div></div>';
                }
                var loggedout = '<div id="loggedout" class="cometchat_tab cometchat_tooltip" title="'+language[8]+'"></div>';
                    loggedout = '';
                /*var baseCode = '<div id="cometchat_base">'+loggedout+'<div id="cometchat_sidebar">'+usertabpop+'</div>'+optionsbuttonpop+''+usertab+'<div id="cometchat_chatboxes"><div id="cometchat_chatboxes_wide" class="cometchat_floatR"></div></div><div id="cometchat_chatbox_left" class="cometchat_tab"><div class="cometchat_tabalertlr" style="display:none;"></div><div class="cometchat_tabtext">0</div><div id="cometchat_unseenUserCount"></div><div id="cometchat_chatbox_left_border_fix"></div></div><div id="cometchat_unseenUsers"></div></div>';*/

                var baseCode = '<div id="cometchat_base">'+loggedout+'<div id="cometchat_sidebar">'+usertabpop+'</div>'+optionsbuttonpop+''+usertab+'<div id="cometchat_chatboxes"><div id="cometchat_chatboxes_wide" class="cometchat_floatR"></div></div><div id="cometchat_chatbox_left" class="cometchat_tab"><div class="cometchat_tabalertlr" style="display:none;"></div><div class="cometchat_tabtext">0</div><div id="cometchat_unseenUserCount"></div><div id="cometchat_unseenchatboxes" style="display:none;"></div><div id="cometchat_chatbox_left_border_fix"></div><div id="cometchat_unseenUsers"></div></div>';

                document.getElementById('cometchat').innerHTML = baseCode;

                if(hasChatroom == 1){
                    jqcc.crdocked.chatroomInit();
                }
                if(settings.showSettingsTab==0){
                    $('#cometchat_userstab').addClass('cometchat_extra_width');
                    $('#cometchat_userstab_popup').find('div.cometchat_tabstyle').addClass('cometchat_border_bottom');
                }

                if($.cookie(settings.cookiePrefix+"sound")){
                    if($.cookie(settings.cookiePrefix+"sound")=='true'){
                        $("#cometchat_soundnotifications").attr("checked", true);
                        $('#cometchat_soundnotifications_label').find('.cometchat_checkindicator').css('display','block');
                    }else{
                        $("#cometchat_soundnotifications").attr("checked", false);
                        $('#cometchat_soundnotifications_label').find('.cometchat_checkindicator').css('display','none');
                    }
                } else {
                    $.cookie(settings.cookiePrefix+"sound", 'true',{path:'/'});
                    $("#cometchat_soundnotifications").attr("checked", true);
                    $('#cometchat_soundnotifications_label').find('.cometchat_checkindicator').css('display','block');
                }

                if($.cookie(settings.cookiePrefix+"popup")){
                    if($.cookie(settings.cookiePrefix+"popup")=='true'){
                        $("#cometchat_popupnotifications").attr("checked", true);
                        $('#cometchat_popupnotifications_label').find('.cometchat_checkindicator').css('display','block');
                    }else{
                        $("#cometchat_popupnotifications").attr("checked", false);
                        $('#cometchat_popupnotifications_label').find('.cometchat_checkindicator').css('display','none');
                    }
                } else {
                    $.cookie(settings.cookiePrefix+"popup", 'true',{path:'/'});
                    $("#cometchat_popupnotifications").attr("checked", true);
                    $('#cometchat_popupnotifications_label').find('.cometchat_checkindicator').css('display','block');
                }
                if($.cookie(settings.cookiePrefix+"disablelastseen")){
                    if($.cookie(settings.cookiePrefix+"disablelastseen")=='false'){
                        $("#cometchat_disablelastseen").attr("checked", true);
                        $('#cometchat_disablelastseen_label').find('.cometchat_checkindicator').css('display','block');
                    }else{
                        $("#cometchat_disablelastseen").attr("checked", false);
                        $('#cometchat_disablelastseen_label').find('.cometchat_checkindicator').css('display','none');
                    }
                } else {
                    $.cookie(settings.cookiePrefix+"disablelastseen", 'false',{path: '/'});
                    $("#cometchat_disablelastseen").attr("checked", true);
                    $('#cometchat_disablelastseen_label').find('.cometchat_checkindicator').css('display','block');
                }



                setTimeout(function(){
                    var sidebar = jqcc('#cometchat_sidebar');
                    var usertabpopheight = (sidebar.outerHeight(false) - sidebar.find('.cometchat_userstabtitle').outerHeight(true) - sidebar.find('#cometchat_tabcontainer').outerHeight(true) - sidebar.find('#cometchat_searchbar').outerHeight(true))+"px";
                    var optionsbuttonpopup = jqcc('#cometchat_optionsbutton_popup');
                    var optionsbuttonpopheight = (optionsbuttonpopup.outerHeight(false) - optionsbuttonpopup.find('.cometchat_userstabtitle').outerHeight(true))+"px";
                    if(jqcc().slimScroll){
                        /*$('#cometchat_userscontent').slimScroll({height: usertabpopheight});*/
                        /*$('#cometchat_optionscontent').slimScroll();*/
                    }

                },300);

                jqcc('#cometchat_userstab').click(function(e){
                    if(settings.disableRecentTab == 1 && settings.disableContactsTab == 1 && settings.disableGroupTab == 1) {
                        $("#cometchat_moreicon").click();
                    } else {
                        jqcc('#cometchat').find('#cometchat_userstab_popup').removeClass('cometchat_tabhidden').addClass('cometchat_tabopen');
                        jqcc[settings.theme].loadChatTab();
                        jqcc.cometchat.setSessionVariable('chats', 1);
                    }
                });

                jqcc('#cometchat_optionsimages_ccauth').click(function(e){
                    jqcc('#cometchat_auth_popup').css('display','block');
                });

                jqcc('#cometchat_minimize_auth_popup').click(function(e){
                    jqcc('#cometchat_auth_popup').css('display','none');
                });

                $('#cometchat_hidden').mouseover(function(){
                    if(tooltipPriority==0){
                        if(settings.ccauth.enabled=="0"){
                            jqcc[settings.theme].tooltip('cometchat_hidden', language[8], 0);
                        }
                    }
                    $(this).addClass("cometchat_tabmouseover");
                });


                $('#cometchat_chatbox_left').bind('click', function(){
                    $(this).toggleClass('cometchat_unseenList_open');
                    $('#cometchat_unseenUsers').toggle();
                    $('#cometchat_chatbox_left_border_fix').toggle();
                    if($('#cometchat_chatbox_left').hasClass('cometchat_unseenList_open')){
                        $('#cometchat_chatbox_left').css('color','#fff');
                    }else{
                        $('#cometchat_chatbox_left').css('color','#333');
                    }
                });
                //jqcc[settings.theme].windowResize();
                jqcc[settings.theme].scrollBars();
                $('#cometchat_chatbox_left').mouseover(function(){
                    $(this).addClass("cometchat_chatbox_lr_mouseover");
                });
                $('#cometchat_chatbox_left').mouseout(function(){
                    $(this).removeClass("cometchat_chatbox_lr_mouseover");
                });
                $('#cometchat_unseenUsers .cometchat_unseenClose').live('click',function(e){
                    e.stopImmediatePropagation();
                    var count = '';
                    var parentElem = $(this).parent();
                    var typeid = $(this).attr('uid');
                    var id = $(this).attr('id');
                    if(typeof(typeid) != "undefined"){
                        if(typeid.split('_')[1] == 'user'){
                            jqcc.docked.closeChatbox(id);
                     }else if(typeid.split('_')[1] == 'group'){
                            jqcc.crdocked.closeChatroom(id);
                        }
                    }

                    $('#cometchat_unseenUsers').find('#'+typeid).remove();
                    count = $('#cometchat_unseenchatboxes').children().length;
                    if(typeof(count) != "undefined"){
                        $('#cometchat_chatbox_left').find('.cometchat_tabtext').text(parseInt(count));
                        if(count == 0){
                            $('#cometchat_chatbox_left').hide();
                        }
                    }
                });
                $('#cometchat_unseenUsers .cometchat_unseenUserList').live('click',function(){
                    var typeid = $(this).attr('id');
                    if($('#cometchat_chatboxes_wide').width() > chatboxWidth){
                        jqcc[settings.theme].swapTab(typeid,1);
                    }
                    if($('#cometchat_chatbox_left').hasClass('cometchat_unseenList_open')){
                        $('#cometchat_chatbox_left').css('color','#fff');
                    }else{
                        $('#cometchat_chatbox_left').css('color','#333');
                    }
                });
                $('#cometchat_hidden').mouseout(function(){
                    $(this).removeClass("cometchat_tabmouseover");
                    if(tooltipPriority==0){
                        $("#cometchat_tooltip").css('display', 'none');
                    }
                    $(this).addClass("cometchat_tabmouseout");
                });

                jqcc('#cometchat_moreicon').click(function(e){
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
                        $('#cometchat_readreceipt_label').find('.cometchat_checkindicator').css('display','block');
                    }else{
                        $('#cometchat_readreceipt_label').find('.cometchat_checkindicator').css('display','none');
                    }
                    jqcc('#cometchat').find('#cometchat_userstab_popup').removeClass('cometchat_tabopen').addClass('cometchat_tabhidden');
                    jqcc('#cometchat').find('#cometchat_optionsbutton_popup').removeClass('cometchat_tabhidden').addClass('cometchat_tabopen');
                    var optionsbuttonpopup = jqcc('#cometchat_optionsbutton_popup');
                    var optionsbuttonpopheight = (optionsbuttonpopup.outerHeight(false)-optionsbuttonpopup.find('.cometchat_userstabtitle').outerHeight(true))+"px";

                    if(mobileDevice){
                        $('#cometchat_optionscontent').css({'height': optionsbuttonpopheight,'overflow-y':'auto'});
                    }else if(jqcc().slimScroll){
                        $('#cometchat_optionscontent').css({'height': optionsbuttonpopheight});
                        $('#cometchat_optionscontent').slimScroll({height: optionsbuttonpopheight});
                    }
                });

                jqcc('#cometchat_newchat').click(function(e){
                    var broadcastoption = '';
                    var creategroup = '';
                    if(!jqcc('#cometchat_newcompose_option').length){
                        if(hasBroadcastMessage == 1 && settings.disableContactsTab == 0) {
                            broadcastoption = '<div class="cometchat_outer_option_box"><div id="cometchat_newBroadcast" class="cometchat_option_list list_up">'+language[113]+'</div></div>';
                        }
                        if(jqcc.cometchat.getChatroomVars('allowUsers') == 1 && hasChatroom == 1){
                            creategroup = '<div class="cometchat_outer_option_box"><div id="cometchat_createGroup" class="cometchat_option_list list_up">'+language['new_group']+'</div></div>';
                        }
                        if(broadcastoption != '' || creategroup != '') {
                            var newcompose = '<div id="cometchat_newcompose_option" class="cometchat_newcompose_option floatactive"><div class="cometchat_arrow_mark"></div>'+creategroup+broadcastoption+'</div>';
                            jqcc('#cometchat_userstab_popup .cometchat_tabcontent').append(newcompose);
                        }

                        jqcc('#cometchat_newBroadcast').click(function(e){
                            if (jqcc.cometchat.membershipAccess('broadcastmessage','modules')){
                                jqcc[settings.theme].composenewBroadcast();
                            }
                        });
                        jqcc('#cometchat_createGroup').click(function(e){
                            if (!(jqcc.cometchat.membershipAccess('chatrooms','modules'))){
                                return;
                            }
                            if(jqcc.cometchat.getCcvariable().hasOwnProperty('loggedinusertype') && jqcc.cometchat.getCcvariable().loggedinusertype == 'guestuser' && allowGuests == 0){
                                alert(language['membership_msg']);
                                return;
                            }
                            jqcc('#cometchat_tabcontainer').find('.cometchat_tab').css("border-bottom","2px solid <?php echo setColorValue('primary','#439FE0'); ?>");
                            jqcc.crdocked.createChatroomPopup();
                        });
                    } else {
                        jqcc('#cometchat_newcompose_option').remove();
                        jqcc('#cometchat_tabcontainer').find('.cometchat_tab').css("border-bottom","2px solid <?php echo setColorValue('primary','#439FE0'); ?>");
                    }
                });

                jqcc('#cometchat_alertsicon').click(function(e){
                    if (jqcc.cometchat.membershipAccess('announcements','modules')){
                        jqcc[settings.theme].showAnnouncements();
                    }
                });

                jqcc('#cometchat_blockedusersoptions').click(function(e){
                    if (jqcc.cometchat.membershipAccess('block','plugins')){
                        jqcc[settings.theme].manageBlockedUsers();
                    }
                });

                jqcc('#cometchat_minimize_optionsbuttonpopup').click(function(e){
                    if(settings.disableGroupTab == 1 && settings.disableContactsTab == 1 && settings.disableGroupTab == 1) {
                        $("#cometchat_optionsbutton_popup").removeClass('cometchat_tabopen').addClass('cometchat_tabhidden');
                    } else {
                        jqcc('#cometchat').find('#cometchat_optionsbutton_popup').removeClass('cometchat_tabopen').addClass('cometchat_tabhidden');
                        jqcc('#cometchat').find('#cometchat_userstab_popup').removeClass('cometchat_tabhidden').addClass('cometchat_tabopen');
                    }
                });

                jqcc('#cometchat_authlogout').click(function(e){
                    jqcc[settings.theme].authLogout();
                });

                jqcc('#cometchat_minimize_userstabpopup').click(function(e){
                    if($('#cometchat_optionsimages_ccauth').length == 1){
                        $('#cometchat_auth_popup').css('display','none');
                        $('#cometchat_optionsimages_ccauth').css('display','none');
                    }
                    jqcc('#cometchat').find('#cometchat_userstab_popup').removeClass('cometchat_tabopen').addClass('cometchat_tabhidden');
                    jqcc.cometchat.setSessionVariable('chats', 0);
                });

                jqcc('#cometchat_recenttab').click(function(e){
                    jqcc[settings.theme].loadChatTab(0);
                    $("#cometchat_search").val('');
                    $('#cometchat_userscontent').find('div.cometchat_userlist').show();
                    $('#cometchat_userstab_popup').find('.cometchat_closeboxsearch').css('display','none');
                    $('#cometchat_nousers_found').remove();
                });

                jqcc('#cometchat_groupstab').click(function(e){
                    if (!(jqcc.cometchat.membershipAccess('chatrooms','modules'))){
                        return;
                    }
                    if(jqcc.cometchat.getCcvariable().hasOwnProperty('loggedinusertype') && jqcc.cometchat.getCcvariable().loggedinusertype == 'guestuser' && crguestsMode == 0){
                        alert(language['access_group_guest']);
                        return;
                    }
                    jqcc[settings.theme].loadChatTab(2);
                    $("#cometchat_search").val('');
                    $('#cometchat_userscontent').find('div.cometchat_grouplist').show();
                    $('#cometchat_userstab_popup').find('.cometchat_closeboxsearch').css('display','none');
                    $('#cometchat_nousers_found').remove();
                });

                jqcc('#cometchat_contactstab').click(function(e){
                    jqcc[settings.theme].loadChatTab(1);
                    $("#cometchat_search").val('');
                    $('#cometchat_userscontent').find('div.cometchat_userlist').show();
                    $('#cometchat_userstab_popup').find('.cometchat_closeboxsearch').css('display','none');
                    $('#cometchat_nousers_found').remove();
                });

                document.onmousemove = function(){
                    var nowTime = new Date();
                    jqcc.cometchat.setThemeVariable('idleTime', Math.floor(nowTime.getTime()/1000));
                };

                $('.cometchat_statustextarea').keyup(function(){
                    $('.cometchat_statusmessagecount').show();
                    statusmessagecount = $(this).attr('maxlength')-$(this).val().length;
                    $('.cometchat_statusmessagecount').html(statusmessagecount);
                });
                $('.cometchat_statustextarea').mouseup(function(){
                    $('.cometchat_statusmessagecount').show();
                    statusmessagecount = $(this).attr('maxlength')-$(this).val().length;
                    $('.cometchat_statusmessagecount').html(statusmessagecount);
                });
                $('.cometchat_statustextarea').mousedown(function(){
                    $('.cometchat_statusmessagecount').show();
                    statusmessagecount = $(this).attr('maxlength')-$(this).val().length;
                    $('.cometchat_statusmessagecount').html(statusmessagecount);
                });
                $('.cometchat_statustextarea').blur(function() {
                    $('.cometchat_statusmessagecount').hide();
                });

                var cometchat_optionsbutton_popup = $('#cometchat_optionsbutton_popup');
                cometchat_optionsbutton_popup.find('#cometchat_selfname #cometchat_displayname').on("blur keypress",function(e){
                    if (e.type == "keypress" && e.keyCode == '13') {
                         e.preventDefault();
                    }
                    if (e.type == "blur") {
                        var displayname = cometchat_optionsbutton_popup.find('#cometchat_selfname #cometchat_displayname').val();

                        if(jqcc.cometchat.getThemeVariable('displayname') != "<?php echo $guestnamePrefix;?>-"+displayname) {
                            jqcc.cometchat.setGuestNameSet(displayname);
                        }
                    }
                });

                cometchat_optionsbutton_popup.find('#cometchat_statusmessageinput .cometchat_statustextarea').blur(function(){
                    var statusmessage = cometchat_optionsbutton_popup.find('#cometchat_statusmessageinput .cometchat_statustextarea').val();
                    if(jqcc.cometchat.getThemeVariable('statusmessage') != statusmessage) {
                        jqcc.cometchat.statusSendMessageSet(statusmessage);
                    }
                });

                cometchat_optionsbutton_popup.find('.cometchat_statusradio').on("change", function(e){
                    var status = $(this).find('input').attr('value');
                    jqcc.cometchat.sendStatus(status);
                });

                var cometchat_search = $("#cometchat_search");
                var cometchat_userscontent = $('#cometchat_userscontent');
                cometchat_search.blur(function(){
                   var searchString = $(this).val();
                    if(searchString==''){
                        cometchat_search.val(language[18]).addClass('cometchat_search_light');
                        $('#cometchat_nousers_found').remove();
                    }
                });

                cometchat_search.click(function(){
                    $(this).val('');
                    cometchat_search.addClass('cometchat_search_light');
                    if($('#cometchat_userstab_popup').find('#cometchat_contactstab').hasClass('cometchat_tab_clicked')){
                        cometchat_userscontent.find('div.cometchat_userlist').css('display','block');
                    } else if($('#cometchat_userstab_popup').find('#cometchat_groupstab').hasClass('cometchat_tab_clicked')){
                        cometchat_userscontent.find('div.cometchat_grouplist').css('display','block');
                    }else if($('#cometchat_userstab_popup').find('#cometchat_botstab').hasClass('cometchat_tab_clicked')){
                        cometchat_userscontent.find('div.cometchat_botlist').css('display','block');
                    }

                    $('#cometchat_nousers_found').remove();
                    $('#cometchat_userstab_popup').find('.cometchat_closeboxsearch').css('display','none');
                });
                cometchat_search.keyup(function(event){
                    event.stopImmediatePropagation();
                    if(event.keyCode==27) {
                        $(this).val('').blur();
                    }
                    var searchString = $(this).val();
                    if($('#cometchat_userstab_popup').find('#cometchat_contactstab').hasClass('cometchat_tab_clicked')){
                        if(searchString.length>0&&searchString!=language[18]){
                            $('#cometchat_userstab_popup').find('.cometchat_closeboxsearch').css('display','block');
                            cometchat_userscontent.find("div.cometchat_userlist").hide();
                            cometchat_userscontent.find('.cometchat_subsubtitle').hide();
                            var searchResult = cometchat_userscontent.find('#cometchat_contactslist div.cometchat_userscontentname:icontains('+searchString+')').parentsUntil("cometchat_userlist").show();
                            var matchLength = searchResult.length;
                            if(matchLength == 0){
                                if($('#cometchat_nousers_found').length == 0) {
                                    $('#cometchat_contactslist').prepend('<div id="cometchat_nousers_found"><div class="chatmessage"><div class="search_nouser">'+language[58]+'</div></div></div></div>');
                                }
                            } else {
                                $('#cometchat_nousers_found').remove();
                            }
                            cometchat_search.removeClass('cometchat_search_light');
                        }else{
                            cometchat_userscontent.find('div.cometchat_userlist').show();
                            $('#cometchat_nousers_found').remove();
                            $('#cometchat_userstab_popup').find('.cometchat_closeboxsearch').css('display','none');
                        }
                    } else if($('#cometchat_userstab_popup').find('#cometchat_groupstab').hasClass('cometchat_tab_clicked')){
                        if(searchString.length>0&&searchString!=language[18]){
                            $('#cometchat_userstab_popup').find('.cometchat_closeboxsearch').css('display','block');
                            cometchat_userscontent.find("div.cometchat_grouplist").hide();
                            cometchat_userscontent.find('.cometchat_subsubtitle').hide();
                            var searchResult = cometchat_userscontent.find('#cometchat_groupslist div.cometchat_groupscontentname:icontains('+searchString+')').parentsUntil("cometchat_grouplist").show();
                            var matchLength = searchResult.length;
                            if(matchLength == 0){
                                if($('#cometchat_nousers_found').length == 0) {
                                    $('#cometchat_groupslist').prepend('<div id="cometchat_nousers_found"><div class="chatmessage"><div class="search_nouser">'+language[114]+'</div></div></div></div>');
                                }
                            } else {
                                $('#cometchat_nousers_found').remove();
                            }
                            cometchat_search.removeClass('cometchat_search_light');
                        }else{
                            cometchat_userscontent.find('div.cometchat_grouplist').show();
                            $('#cometchat_nousers_found').remove();
                            $('#cometchat_userstab_popup').find('.cometchat_closeboxsearch').css('display','none');
                        }
                    }else if($('#cometchat_userstab_popup').find('#cometchat_recenttab').hasClass('cometchat_tab_clicked')){
                        if(searchString.length>0&&searchString!=language[18]){
                            $('#cometchat_userstab_popup').find('.cometchat_closeboxsearch').css('display','block');
                            cometchat_userscontent.find("div.cometchat_recentchatlist").hide();
                            cometchat_userscontent.find("div.cometchat_recentgrouplist").hide();
                            cometchat_userscontent.find('.cometchat_subsubtitle').hide();
                            searchResult1 = cometchat_userscontent.find('#cometchat_recentlist div.cometchat_groupscontentname:icontains('+searchString+')').parentsUntil("cometchat_recentgrouplist").show();
                            searchResult2 = cometchat_userscontent.find('#cometchat_recentlist div.cometchat_userscontentname:icontains('+searchString+')').parentsUntil("cometchat_recentchatlist").show();
                            var matchLength = (searchResult1.length)+(searchResult2.length);
                            if(matchLength == 0){
                                if($('#cometchat_nousers_found').length == 0) {
                                    $('#cometchat_recentlist').prepend('<div id="cometchat_nousers_found"><div class="chatmessage"><div class="search_nouser">'+language["no_chats_found"]+'</div></div></div></div>');
                                }
                            } else {
                                $('#cometchat_nousers_found').remove();
                            }
                            cometchat_search.removeClass('cometchat_search_light');
                        }else{
                            cometchat_userscontent.find('div.cometchat_recentchatlist').show();
                            cometchat_userscontent.find('div.cometchat_recentgrouplist').show();
                            $('#cometchat_nousers_found').remove();
                            $('#cometchat_userstab_popup').find('.cometchat_closeboxsearch').css('display','none');
                        }
                    }
                });

                $("#cometchat_soundnotifications").click(function(event){
                    var notification = 'false';
                    if($("#cometchat_soundnotifications").is(":checked")){
                        $('#cometchat_soundnotifications_label').find('.cometchat_checkindicator').css('display','block');
                        notification = 'true';
                    }else{
                         $('#cometchat_soundnotifications_label').find('.cometchat_checkindicator').css('display','none');
                    }
                    $.cookie(settings.cookiePrefix+"sound", notification, {path: '/', expires: 365});
                });

                $("#cometchat_readreceipt").click(function(event){
                    var notification = 'false';
                    if($("#cometchat_readreceipt").is(":checked")){
                        $('#cometchat_readreceipt_label').find('.cometchat_checkindicator').css('display','block');
                        notification = 'true';
                    }else{
                         $('#cometchat_readreceipt_label').find('.cometchat_checkindicator').css('display','none');
                    }
                    jqcc.cometchat.updateReadReceipt(notification);
                    $.cookie(settings.cookiePrefix+"read", notification, {path: '/', expires: 365});
                });

                $("#cometchat_popupnotifications").click(function(event){
                    var notification = 'false';
                    if($("#cometchat_popupnotifications").is(":checked")){
                        $('#cometchat_popupnotifications_label').find('.cometchat_checkindicator').css('display','block');
                        notification = 'true';
                    }else{
                        $('#cometchat_popupnotifications_label').find('.cometchat_checkindicator').css('display','none');
                    }
                    $.cookie(settings.cookiePrefix+"popup", notification, {path: '/', expires: 365});
                });
                $("#cometchat_disablelastseen").click(function(event){
                    lastseenflag = true;

                    if(lastseenflag){
                        jqcc[settings.theme].hideLastseen(jqcc.cometchat.getThemeVariable('openChatboxId'));
                    } else if(!lastseenflag){
                        if((jqcc.cometchat.getThemeArray('buddylistStatus', jqcc.cometchat.getThemeVariable('openChatboxId')) == 'available')||(jqcc.cometchat.getThemeArray('buddylistStatus', jqcc.cometchat.getThemeVariable('openChatboxId')) == 'offline' && jqcc.cometchat.getThemeArray('buddylistLastseensetting', jqcc.cometchat.getThemeVariable('openChatboxId')) == 1)){
                            jqcc[settings.theme].hideLastseen(jqcc.cometchat.getThemeVariable('openChatboxId'));
                        }
                        else if(jqcc.cometchat.getThemeArray('buddylistStatus', jqcc.cometchat.getThemeVariable('openChatboxId')) == 'offline' && jqcc.cometchat.getThemeArray('buddylistLastseensetting', jqcc.cometchat.getThemeVariable('openChatboxId')) == 0){
                            jqcc[settings.theme].showLastseen(jqcc.cometchat.getThemeVariable('openChatboxId'), jqcc.cometchat.getThemeArray('buddylistLastseen', jqcc.cometchat.getThemeVariable('openChatboxId')));
                        }
                    }

                    if($("#cometchat_disablelastseen").is(":checked")){
                        $('#cometchat_disablelastseen_label').find('.cometchat_checkindicator').css('display','block');
                        var id=0;
                        $(".cometchat_lastseenmessage").each(function(){
                            id = parseInt($(this).attr('id').replace('cometchat_messagElement_',''));
                            var buddylastseen = jqcc.cometchat.getThemeArray('buddylistLastseen', id);
                            var statusmsg = jqcc.cometchat.getThemeArray('buddylistStatus', id);
                            var lstsnSetting = jqcc.cometchat.getThemeArray('buddylistLastseensetting', id);
                            var currentts = Math.floor(new Date().getTime()/1000);
                            if(((statusmsg == 'away' || statusmsg == 'invisible' || statusmsg == 'busy' || statusmsg == 'offline') || currentts-buddylastseen > (60*10)) && lstsnSetting == '0')
                                jqcc[settings.theme].showLastseen(id,jqcc.cometchat.getThemeArray('buddylistLastseen',id));
                        })
                        lastseenflag = false;
                    }else{
                        $('#cometchat_disablelastseen_label').find('.cometchat_checkindicator').css('display','none');
                        $(".cometchat_lastseenmessage").slideUp(300);
                        lastseenflag = true;
                    }

                    $.ajax({
                        url: baseUrl+"cometchat_send.php",
                        data: {lastseenSettingsFlag: lastseenflag},
                        dataType: 'jsonp',
                        success: function(data){
                        }
                    });

                    $.cookie(settings.cookiePrefix+"disablelastseen", lastseenflag, {path:'/'});
                });

                $('#cometchat_userstab_popup').find('.cometchat_closeboxsearch').click(function(e){
                    e.stopImmediatePropagation();
                    $('#cometchat_userstab_popup').find('#cometchat_search').val('');
                    cometchat_search.keyup();
                });

                window.onoffline = function() {
                    jqcc.docked.noInternetConnection(true);
                }
                window.ononline = function() {
                    jqcc.docked.noInternetConnection(false);
                }

                function updateOnlineStatus() {
                    var noInternetConnection = navigator.onLine ? false : true;
                    jqcc.docked.noInternetConnection(noInternetConnection);
                }

                document.body.addEventListener("offline", function () { updateOnlineStatus() }, false);
                document.body.addEventListener("online", function () { updateOnlineStatus() }, false);
            },
            resetGuestName:function(callback){
                $('#cometchat_optionsbutton_popup').find('.cometchat_guestname').val(jqcc.cometchat.getThemeVariable('displayname').replace("<?php echo $guestnamePrefix;?>-", ""));
                if (typeof callback == 'function') {
                    callback();
                }
            },
            tooltip: function(id,message, orientation){
                if(disableLayout == 1){return};
                var cometchat_tooltip = $('#cometchat_tooltip');
                cometchat_tooltip.css('display', 'none').removeClass("cometchat_tooltip_left").css('left', '-100000px').find(".cometchat_tooltip_content").html(message);
                var pos = $('#'+id).offset();
                var width = $('#'+id).width();
                var tooltipWidth = cometchat_tooltip.width();
                var displayheight = $(window).outerHeight();
                var cometchat_totalheight='';
                var popup_top='';
                var leftposition='';
                var cometchat_tooltip_height = $(cometchat_tooltip).outerHeight();
                var cometchat_userstab_height = '';
                var cometchat_userstab_width='';
                if(orientation==1){
                    cometchat_tooltip.css('left', (pos.left+width)-9).addClass("cometchat_tooltip_left");
                }else{
                    var leftposition = (pos.left+width)-tooltipWidth;
                    leftposition += 16;
                    cometchat_tooltip.removeClass("cometchat_tooltip_left").css('left', leftposition);
                    if($('#cometchat_userstab_popup').hasClass('cometchat_tabhidden')){
                        cometchat_userstab_height = $('#cometchat_userstab').outerHeight();
                        cometchat_userstab_width = $('#cometchat_userstab').width();
                        leftposition += 5;
                        cometchat_totalheight = cometchat_tooltip_height+cometchat_userstab_height;
                        popup_top = displayheight-cometchat_totalheight;
                        leftposition = (pos.left+cometchat_userstab_width)-tooltipWidth-10;
                        $(cometchat_tooltip).css('top', popup_top);
                        cometchat_tooltip.addClass("cometchat_tooltip_left").css('left',leftposition);
                    }else if($('#cometchat_userstab_popup').hasClass('cometchat_tabopen')){
                        cometchat_userstab_height = $('#cometchat_userstab_popup').outerHeight();
                        cometchat_userstab_width = $('#cometchat_userstab_popup').width();
                        leftposition += 5;
                        cometchat_totalheight = cometchat_tooltip_height+cometchat_userstab_height;
                        popup_top= displayheight-cometchat_totalheight;
                        leftposition = (pos.left+cometchat_userstab_width)-tooltipWidth-10;
                        $(cometchat_tooltip).css('top', popup_top);
                        cometchat_tooltip.addClass("cometchat_tooltip_left").css('left',leftposition);
                    }else{
                        var logoutbox_height = $('#'+id).outerHeight();
                        var logout_notification = pos.top+logoutbox_height-57;
                        cometchat_tooltip.css('top', logout_notification);
                        cometchat_tooltip.addClass("cometchat_tooltip_left").css('left',leftposition-12);
                    }
                cometchat_tooltip.css('display', 'block');
                }
            },
            userStatus: function(item){
                var cometchat_optionsbutton_popup = $('#cometchat_optionsbutton_popup');
                var count = 140-item.m.length;

                cometchat_optionsbutton_popup.find('#cometchat_selfname #cometchat_displayname').text(item.n);
                cometchat_optionsbutton_popup.find('textarea.cometchat_statustextarea').val(item.m);
                cometchat_optionsbutton_popup.find('.cometchat_statusmessagecount').html(count);
                cometchat_optionsbutton_popup.find('#cometchat_status'+item.s+'_radio').click();

                jqcc.cometchat.setThemeVariable('displayname', item.n);
                jqcc.cometchat.setThemeVariable('statusmessage', item.m);

                if(item.s=='offline'){
                    cometchat_optionsbutton_popup.find('#cometchat_statusinvisible_radio').click();
                    jqcc[settings.theme].goOffline(1);
                }

                if(item.s != 'away'){
                    jqcc.cometchat.setThemeVariable('currentStatus', item.s);
                    jqcc.cometchat.setThemeVariable('idleFlag', 0);
                }
                if(item.s == 'away') {
                    jqcc.cometchat.setThemeVariable('idleFlag', 1);
                }
                if(item.lstn==1){
                    lastseenflag = true;
                }

                if(parseInt(item.id) > <?php echo $firstguestID; ?>){
                    cometchat_optionsbutton_popup.find('#cometchat_displayname').attr("readonly", false);
                    cometchat_optionsbutton_popup.find('#cometchat_displayname').addClass("cometchat_guestname");
                    cometchat_optionsbutton_popup.find('.cometchat_guestname').val(item.n.replace("<?php echo $guestnamePrefix;?>-", ""));
                }
                /*if(typeof item.b != 'undefined' && item.b == '1') {
                    jqcc[settings.theme].loggedOut();
                    jqcc.cometchat.setThemeVariable('banned', '1');
                    jqcc("#loggedout").attr("title",bannedMessage);
                }*/
                jqcc.cometchat.setThemeVariable('userid', item.id);
                jqcc.cometchat.addBuddy(item);
                if(item.lstn == 1){
                    $("#cometchat_disablelastseen").attr("checked", false);
                    $('#cometchat_disablelastseen_label').find('.cometchat_checkindicator').css('display','none');
                    $.cookie(settings.cookiePrefix+"disablelastseen",true,{path:'/'});
                }
                jqcc('#cometchat_hidden').hide();
            },
            goOffline: function(silent){
                jqcc.cometchat.setThemeVariable('offline', 1);

                if(silent!=1){
                    jqcc.cometchat.sendStatus('offline');
                }
                $('#cometchat_userstab').removeClass('cometchat_userstabclick cometchat_tabclick');
                $('div.cometchat_tabopen').removeClass('cometchat_tabopen');
                $('span.cometchat_tabclick').removeClass('cometchat_tabclick');
                jqcc.cometchat.setSessionVariable('chats', 0);
                $('#cometchat_userstab_text').html(language[17]);
                for(var chatbox in jqcc.cometchat.getThemeVariable('chatBoxesOrder')){
                    if(jqcc.cometchat.getThemeVariable('chatBoxesOrder').hasOwnProperty(chatbox)){
                        if(jqcc.cometchat.getThemeVariable('chatBoxesOrder')[chatbox]!==null){
                            $("#cometchat_user_"+chatbox).find(".cometchat_closebox_bottom").click();
                        }
                    }
                }
                $('.cometchat_container').remove();
                if(typeof window.cometuncall_function=='function'){
                    cometuncall_function(jqcc.cometchat.getThemeVariable('cometid'));
                }
            },
            composenewBroadcast:function(){
                var cc_basedata = jqcc.cometchat.getBaseData();
                var ncbframe = '<div><iframe id="cometchat_trayicon_newchat_iframe" src="'+baseUrl+'modules/broadcastmessage/index.php?cc_layout=docked&basedata='+cc_basedata+'" height="316" width="100%" style="border:0px;"></div>';
                var newchatpopup = '<div id="cometchat_newchat_popup" class="cometchat_tabpopup cometchat_tabhidden"><div class="cometchat_userstabtitle"><div class="cometchat_userstabtitletext" style="width: 75%;text-align: center;margin-left:20px;">'+language[117]+'</div><div class="cometchat_closebox cometchat_tooltip" id="cometchat_minimize_newchatpopup" title="'+language[27]+'"></div></div><div id="cometchat_newchat_content" class="cometchat_tabcontent cometchat_optionstyle" style="overflow:hidden;">'+ncbframe+'</div></div>';

                jqcc('#cometchat_sidebar').append(newchatpopup);
                jqcc('#cometchat').find('#cometchat_userstab_popup').removeClass('cometchat_tabopen').addClass('cometchat_tabhidden');
                jqcc('#cometchat_newcompose_option').remove();
                jqcc('#cometchat_tabcontainer').find('.cometchat_tab').css("border-bottom","2px solid <?php echo setColorValue('primary','#439FE0'); ?>");
                jqcc('#cometchat').find('#cometchat_newchat_popup').removeClass('cometchat_tabhidden').addClass('cometchat_tabopen');

                jqcc('#cometchat_minimize_newchatpopup').click(function(e){
                    jqcc('#cometchat').find('#cometchat_newchat_popup').removeClass('cometchat_tabopen').addClass('cometchat_tabhidden');
                    jqcc('#cometchat').find('#cometchat_userstab_popup').removeClass('cometchat_tabhidden').addClass('cometchat_tabopen');
                    jqcc('#cometchat').find('#cometchat_newchat_popup').remove();

                });
            },
            newAnnouncement: function(item){
                if($.cookie(settings.cookiePrefix+"popup")&&$.cookie(settings.cookiePrefix+"popup")=='true'){
                    tooltipPriority = 100;
                    message = '<div class="cometchat_announcement">'+item.m+'</div>';
                    if(item.o){
                        var notifications = (item.o-1);
                        if(notifications>0){
                            message += '<div class="cometchat_notification"><div class="cometchat_notification_message cometchat_notification_message_and">'+language[36]+notifications+language[37]+'</div><div style="clear:both" /></div>';
                        }
                    }else{
                        $.cookie(settings.cookiePrefix+"an", item.id, {path: '/', expires: 365});
                    }
                    if ((typeof $.cookie(settings.cookiePrefix+"sound") == 'undefined' || $.cookie(settings.cookiePrefix+"sound") == null) || $.cookie(settings.cookiePrefix + "sound") == 'true') {
                        jqcc.docked.playSound(2);
                    }
                    jqcc[settings.theme].tooltip("cometchat_userstab", message, 0);
                    clearTimeout(notificationTimer);
                    notificationTimer = setTimeout(function(){
                        $('#cometchat_tooltip').css('display', 'none');
                        tooltipPriority = 0;
                    }, settings.announcementTime);
                }
            },
            showAnnouncements: function(){
                var anframe = '<div><iframe id="cometchat_trayicon_announcements_iframe" src="'+baseUrl+'modules/announcements/index.php?cc_layout=docked" height="316" width="100%" style="border:0px;"></div>';
                var announcementspopup = '<div id="cometchat_announcements_popup" class="cometchat_tabpopup cometchat_tabhidden"><div class="cometchat_userstabtitle"><div class="cometchat_userstabtitletext" style="width: 75%;text-align: center;margin-left:20px;">'+language['announcements']+'</div><div class="cometchat_closebox cometchat_tooltip" id="cometchat_minimize_announcementspopup" title="'+language[74]+'"></div></div><div id="cometchat_announcements_content" class="cometchat_tabcontent cometchat_optionstyle" style="overflow:hidden;">'+anframe+'</div></div>';

                jqcc('#cometchat_sidebar').append(announcementspopup);
                jqcc('#cometchat').find('#cometchat_userstab_popup').removeClass('cometchat_tabopen').addClass('cometchat_tabhidden');
                jqcc('#cometchat').find('#cometchat_announcements_popup').removeClass('cometchat_tabhidden').addClass('cometchat_tabopen');

                jqcc('#cometchat_minimize_announcementspopup').click(function(e){
                    jqcc('#cometchat').find('#cometchat_announcements_popup').removeClass('cometchat_tabopen').addClass('cometchat_tabhidden');
                    jqcc('#cometchat').find('#cometchat_userstab_popup').removeClass('cometchat_tabhidden').addClass('cometchat_tabopen');
                    jqcc('#cometchat').find('#cometchat_announcements_popup').remove();
                });
            },
            manageBlockedUsers: function(){
                var blockeduserframe = '<div><iframe id="cometchat_blockedusers_iframe" src="'+baseUrl+'plugins/block/index.php?basedata='+basedata+'&cc_layout=docked" height="316" width="100%" style="border:0px;"></div>';
                var blockeduserspopup = '<div id="cometchat_blockedusers_popup" class="cometchat_tabpopup cometchat_tabhidden"><div class="cometchat_userstabtitle"><div class="cometchat_userstabtitletext" style="width: 75%;text-align: center;margin-left:20px;">'+language['manage_blocked_users']+'</div><div class="cometchat_closebox cometchat_tooltip" id="cometchat_minimize_blockeduserspopup" title="'+language[74]+'"></div></div><div id="cometchat_blockedusers_content" class="cometchat_tabcontent cometchat_optionstyle" style="overflow:hidden;position:absolute">'+blockeduserframe+'</div></div>';

                jqcc('#cometchat_sidebar').append(blockeduserspopup);
                jqcc('#cometchat').find('#cometchat_optionsbutton_popup').removeClass('cometchat_tabopen').addClass('cometchat_tabhidden');
                jqcc('#cometchat').find('#cometchat_blockedusers_popup').removeClass('cometchat_tabhidden').addClass('cometchat_tabopen');

                jqcc('#cometchat_minimize_blockeduserspopup').click(function(e){
                    jqcc('#cometchat').find('#cometchat_blockedusers_popup').removeClass('cometchat_tabopen').addClass('cometchat_tabhidden');
                    jqcc('#cometchat').find('#cometchat_optionsbutton_popup').removeClass('cometchat_tabhidden').addClass('cometchat_tabopen');
                    jqcc('#cometchat').find('#cometchat_blockedusers_popup').remove();
                });
            },
            showBots: function(){
                if (jqcc.cometchat.membershipAccess('bots','extensions')){
                    var showBotsFrame = '<div><iframe id="cometchat_bots_iframe" src="'+baseUrl+'extensions/bots/index.php?basedata='+basedata+'&cc_layout=docked" height="316" width="100%" style="border:0px;"></div>';
                    var botspopup = '<div id="cometchat_bots_popup" class="cometchat_tabpopup cometchat_tabhidden"><div class="cometchat_userstabtitle"><div class="cometchat_userstabtitletext" style="width: 70%;text-align: center;margin-left:20px;">'+language["bots"]+'</div><div class="cometchat_closebox cometchat_tooltip" id="cometchat_minimize_botspopup" title="'+language["bots"]+'"></div></div><div id="cometchat_blockedusers_content" class="cometchat_tabcontent cometchat_optionstyle" style="overflow:hidden;">'+showBotsFrame+'</div></div>';

                    jqcc('#cometchat_sidebar').append(botspopup);
                    jqcc('#cometchat').find('#cometchat_optionsbutton_popup').removeClass('cometchat_tabopen').addClass('cometchat_tabhidden');
                    jqcc('#cometchat').find('#cometchat_bots_popup').removeClass('cometchat_tabhidden').addClass('cometchat_tabopen');

                    jqcc('#cometchat_minimize_botspopup').click(function(e){
                        jqcc('#cometchat').find('#cometchat_bots_popup').removeClass('cometchat_tabopen').addClass('cometchat_tabhidden');
                        jqcc('#cometchat').find('#cometchat_optionsbutton_popup').removeClass('cometchat_tabhidden').addClass('cometchat_tabopen');
                        jqcc('#cometchat').find('#cometchat_bots_popup').remove();
                    });
                }
            },
            loadChatTab: function(type, restored){
                if (type > 2 || type < 0 || typeof(type)!="number" || typeof(type)=="undefined" || isNaN(type)) {
                    type = 0;
                }
                if(typeof(restored) == "undefined"){
                    var restored = 0;
                }

                if((type == 1 && settings.disableContactsTab == 1) || (type == 0 && settings.disableRecentTab == 1) || (type == 2 && hasChatroom == 0)){
                    if(settings.disableContactsTab == 0) {
                        type = 1;
                    } else if(settings.disableRecentTab == 0) {
                        type = 0;
                    } else if(hasChatroom == 1) {
                        type = 2;
                    } else {
                        return;
                    }
                }

                var tabs = ['recent','contacts','groups'];
                jqcc('#cometchat_tabcontainer .cometchat_tab').removeClass('cometchat_tab_clicked');
                jqcc('#cometchat_userscontent .cometchat_tabopen').removeClass('cometchat_tabopen').addClass('cometchat_tabhidden');
                jqcc('#cometchat_'+tabs[type]+'tab').addClass('cometchat_tab_clicked');
                jqcc('#cometchat_'+tabs[type]+'list').removeClass('cometchat_tabhidden').addClass('cometchat_tabopen');
                if(restored != 1){
                    jqcc.cometchat.setSessionVariable('openedtab',type);
                }
            },
            buddyList: function(item){
                var onlineNumber = 0;
                var totalFriendsNumber = 0;
                var lastGroup = '';
                var groupNumber = 0;
                var tooltipMessage = '';
                var buddylisttemp = '';
                var buddylisttempavatar = '';
                var unreadmessagecount;
                var msgcountercss;
                $.each(item, function(i, buddy){
                    if(buddy.n == null || buddy.n == 'null' || buddy.n == '' || jqcc.cometchat.getThemeVariable('banned', '1')) {
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
                                $('#cometchat_messagElement_'+buddy.id).hide();
                            }
                        }
                    }
                    var statusclass = 'cometchat_user_'+buddy.s;
                    if(buddy.d==1){
                        statusclass = 'cometchat_mobile cometchat_mobile_'+buddy.s;
                    }

                    if(buddy.s!='offline'){
                        onlineNumber++;
                    }
                    totalFriendsNumber++;
                    var group = '';
                    var statusIndicator = '';

                    if(buddy.s=='invisible'){
                        buddy.s = 'offline';
                    }

                    unreadmessagecount = jqcc.cometchat.getUnreadMessageCount({contacts: [parseInt(buddy.id)]});
                    msgcountercss = "display:none;";
                    if(unreadmessagecount > 0) {
                        msgcountercss = "";
                    }
                    statusIndicator = '<div><div class="cometchat_userscontentdot '+statusclass+'"></div><div class="cometchat_buddylist_status" title="'+buddy.m+'">'+buddy.m+'</div></div>';

                    /*Audio Call Icon in BuddyList*/
                    var buddyListAudioCallIcon = '';
                    if(settings.dockedChatListAudioCall == 1){
                        buddyListAudioCallIcon = '<svg version="1.1" id="cometchat_audiocall_'+buddy.id+'" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" class="cometchat_audiocallsvg_icon" onclick="jqcc.ccaudiochat.init({to:\''+buddy.id+'\',chatroommode:\'0\'})" viewBox="0 0 64 64" enable-background="new 0 0 64 64" xml:space="preserve" > <path d="M50,63c0.266,0,0.52-0.105,0.707-0.293l10-10c0.391-0.391,0.391-1.023,0-1.414l-14-14c-0.391-0.391-1.023-0.391-1.414,0L36,46.586L17.414,28l9.293-9.293c0.391-0.391,0.391-1.023,0-1.414l-14-14c-0.391-0.391-1.023-0.391-1.414,0l-10,10C1.105,13.48,1,13.735,1,14C1,41.019,22.981,63,50,63z M12,5.414L24.586,18l-9.293,9.293c-0.391,0.391-0.391,1.023,0,1.414l20,20c0.391,0.391,1.023,0.391,1.414,0L46,39.414L58.586,52l-8.998,8.998C23.998,60.777,3.223,40.002,3.002,14.413L12,5.414z"/></svg>'
                    }
                    if((buddy.s != 'offline' && settings.hideOffline == 1) || settings.hideOffline == 0){
                        buddylisttemp += group+'<div id="cometchat_userlist_'+buddy.id+'" class="cometchat_userlist" onmouseover="jqcc(this).addClass(\'cometchat_userlist_hover\');" onmouseout="jqcc(this).removeClass(\'cometchat_userlist_hover\');" amount="'+unreadmessagecount+'"><div style="display:inline-block;"><div class="cometchat_userscontentname">'+longname+'</div><div id="cometchat_buddylist_typing_'+buddy.id+'" class="cometchat_buddylist_typing"></div></div>'+statusIndicator+'<div class="cometchat_unreadCount cometchat_floatR" style="'+msgcountercss+'">'+unreadmessagecount+'</div>'+buddyListAudioCallIcon+'</div>';
                        buddylisttempavatar += group+'<div id="cometchat_userlist_'+buddy.id+'" class="cometchat_userlist" onmouseover="jqcc(this).addClass(\'cometchat_userlist_hover\');" onmouseout="jqcc(this).removeClass(\'cometchat_userlist_hover\');" amount="'+unreadmessagecount+'"><div class="cometchat_userscontentavatar"><img class="cometchat_userscontentavatarimage" original="'+buddy.a+'"></div><div style="display:inline-block;"><div class="cometchat_userscontentname">'+longname+'</div><div id="cometchat_buddylist_typing_'+buddy.id+'" class="cometchat_buddylist_typing"></div></div>'+statusIndicator+'<div class="cometchat_unreadCount cometchat_floatR" style="'+msgcountercss+'">'+unreadmessagecount+'</div>'+buddyListAudioCallIcon+'</div>';
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
                        tooltipMessage += '<div class="cometchat_notification" onclick="javascript:jqcc.cometchat.chatWith(\''+buddy.id+'\')"><div class="cometchat_notification_avatar"><img class="cometchat_notification_avatar_image" src="'+buddy.a+'"></div><div class="cometchat_notification_message"><div class="cometchat_notification_uname">'+buddy.n+'&nbsp;</div>'+message+'<br/><span class="cometchat_notification_status">'+buddy.m+'</span></div><div style="clear:both" /></div>';
                    }

                    jqcc.cometchat.addBuddy(buddy);
                });

                var bltemp = buddylisttempavatar;
                if(totalFriendsNumber>settings.thumbnailDisplayNumber){
                    bltemp = buddylisttemp;
                }
                if(document.getElementById('cometchat_contactslist')){
                    if(bltemp!=''){
                        jqcc.cometchat.replaceHtml('cometchat_contactslist', '<div>'+bltemp+'</div>');
                    }else{
                        $('#cometchat_contactslist').html('<div class="cometchat_nofriends">'+language[14]+'</div>');
                    }
                }

                $("div.cometchat_userscontentavatar").find("img").each(function(){
                    if($(this).attr('original')){
                        $(this).attr("src", $(this).attr('original'));
                        $(this).removeAttr('original');
                    }
                });

                if(totalFriendsNumber>settings.searchDisplayNumber) {
                    $('#cometchat_searchbar').show();
                    jqcc.cometchat.setThemeVariable('hasSearchbox', 1);
                } else {
                    $('#cometchat_searchbar').hide();

                }

                /*Slim Scroll issue is here*/
                var userstabpopup = jqcc('#cometchat_userstab_popup');
                //var chatlistheight = (userstabpopup.outerHeight(false)-userstabpopup.find('.cometchat_userstabtitle').outerHeight(true)-userstabpopup.find('#cometchat_tabcontainer').outerHeight(true)-userstabpopup.find('#cometchat_searchbar').outerHeight(true))+"px";
                if(jqcc.cometchat.getThemeVariable('hasSearchbox')){
                    var chatlistheight = ($( ".right_footer" ).length == 1) ? "240px" : "259px";
                } else {
                    var chatlistheight = ($( ".right_footer" ).length == 1) ? "270px" : "286px";
                }

                if(mobileDevice){
                    userstabpopup.find('div#cometchat_userscontent').css('height',chatlistheight);
                    userstabpopup.find('#cometchat_userscontent #cometchat_contactslist > div').css({'height': chatlistheight});
                    userstabpopup.find('#cometchat_userscontent #cometchat_contactslist > div').css('overflow-y','auto');
                }else if(jqcc().slimScroll){
                    userstabpopup.find('div#cometchat_userscontent').css('height',chatlistheight);
                    userstabpopup.find('#cometchat_userscontent #cometchat_contactslist > div').css({'height': chatlistheight});
                    userstabpopup.find('#cometchat_userscontent #cometchat_contactslist > div').slimScroll({height: chatlistheight});
                }


                /*End*/

                $("#cometchat_search").keyup();
                $('#cometchat_contactslist div.cometchat_userlist').unbind('click');
                $('#cometchat_contactslist div.cometchat_userlist').live('click', function(e){
                    jqcc.cometchat.userClick(e.currentTarget,0);
                });
                siteOnlineNumber = onlineNumber;
                if(tooltipMessage!=''&& (!$('#cometchat_userstab_popup').hasClass('cometchat_tabopen') || $('#cometchat_userstab_popup').hasClass('cometchat_tabopen'))){
                    if(tooltipPriority<10){
                        if($.cookie(settings.cookiePrefix+"popup")&&$.cookie(settings.cookiePrefix+"popup")=='true'){
                            tooltipPriority = 108;
                            jqcc.docked.tooltip("cometchat_userstab", tooltipMessage, 0);
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
                var onlineNumber = 0;
                var totalFriendsNumber = 0;
                var lastGroup = '';
                var groupNumber = 0;
                var tooltipMessage = '';
                var chatlisttemp = '';
                if(jqcc.cometchat.getChatroomVars('showChatroomUsers') == 1){
                    userCountCss = '';
                }
                $.each(item, function(i, chat){
                    if(chat.n == null || chat.n == 'null' || chat.n == '' || typeof(chat.m) == "undefined") {
                        return;
                    }
                    var unreadmessagecount;
                    var msgcountercss = "display:none;";
                    var userCountCss = "style='display:none'";
                    var recentmessage = '';
                    var availablegroups = Object.keys(jqcc.cometchat.getChatroomVars('chatroomdetails'));
                    recentmessage = '<div class="cometchat_recentmessage">'+chat.m+'</div>';

                    if(chat.grp) {
                        var roomtype = '';
                        if(chat.n == null || chat.n == 'null' || chat.n == '' || typeof(chat.m) == "undefined") {
                           jqcc.cometchat.getChatroomDetails({id:chat.id,loadroom:1,force:1,msgcount:0  });
                        }

                        if(availablegroups.indexOf('_'+chat.id) == -1){
                            return;
                        }
                        longname = shortname = chat.n;

                        unreadmessagecount = jqcc.cometchat.getUnreadMessageCount({groups: [parseInt(chat.id)]});
                        if(unreadmessagecount>0){
                            msgcountercss = '';
                        }

                        if(chat.type == 1) {
                            roomtype = '<div class="cometchat_grouptype"><img src="'+staticCDNUrl+'layouts/'+settings.theme+'/images/lock.png" class="cometchat_grouptypeimage" /></div>';
                        }

                        if(chat.s == 2) {
                            chat.s = 1;
                        }

                        chatlisttemp += '<div id="cometchat_recentgrouplist_'+chat.id+'" class="cometchat_grouplist cometchat_recentgrouplist" onmouseover="jqcc(this).addClass(\'cometchat_grouplist_hover\');" onmouseout="jqcc(this).removeClass(\'cometchat_grouplist_hover\');" onclick="javascript:jqcc.cometchat.chatroom(\''+chat.id+'\',\''+cc_urlencode(shortname)+'\',\''+chat.type+'\',\''+chat.pass+'\',\''+chat.s+'\',\'1\',\'1\');" amount="'+unreadmessagecount+'"><div class="cometchat_groupscontentavatar"><img class="cometchat_groupscontentavatarimage" src="'+staticCDNUrl+'layouts/'+settings.theme+'/images/group.svg"></div><div><div class="cometchat_groupscontentname">'+longname+'</div></div>'+roomtype+recentmessage+'<div class="cometchat_unreadCount cometchat_floatR" style="'+msgcountercss+'">'+unreadmessagecount+'</div></div>';
                    } else {

                        if(chat.n == null || chat.n == 'null' || chat.n == '' || typeof(chat.m) == "undefined") {
                           jqcc[settings.theme].createChatbox(chat.id, null, null, null, null, null, null, null, null, 1);
                           return;
                       }

                        longname = chat.n;
                        var statusclass = 'cometchat_user_'+chat.s;
                        if(chat.d==1){
                            statusclass = 'cometchat_mobile cometchat_mobile_'+chat.s;
                        }

                        if(!chat.hasOwnProperty('a') || chat.a == ''){
                            chat.a = staticCDNUrl+'images/noavatar.png';
                        }

                        if(chat.s!='offline'){
                            onlineNumber++;
                        }
                        totalFriendsNumber++;

                        if(chat.s=='invisible'){
                            chat.s = 'offline';
                        }
                        unreadmessagecount = jqcc.cometchat.getUnreadMessageCount({contacts: [parseInt(chat.id)]});
                        if(unreadmessagecount>0) {
                            msgcountercss = '';
                        }
                        if((chat.s != 'offline' && settings.hideOffline == 1) || settings.hideOffline == 0){
                            chatlisttemp += '<div id="cometchat_recentlist_'+chat.id+'" class="cometchat_userlist cometchat_recentchatlist" onmouseover="jqcc(this).addClass(\'cometchat_userlist_hover\');" onmouseout="jqcc(this).removeClass(\'cometchat_userlist_hover\');" amount="'+unreadmessagecount+'"><div class="cometchat_recentcontentavatar"><img class="cometchat_recentcontentavatarimage" src="'+chat.a+'"></div><div style="display:inline-block;"><div class="cometchat_userscontentname">'+longname+'</div></div>'+recentmessage+'<div class="cometchat_unreadCount cometchat_floatR" style="'+msgcountercss+'">'+unreadmessagecount+'</div></div>';
                        }
                    }
                });

                var chatlist = chatlisttemp;

                if(document.getElementById('cometchat_recentlist')){
                    if(chatlist!=''){
                        jqcc.cometchat.replaceHtml('cometchat_recentlist', '<div>'+chatlist+'</div>');
                    }else{
                        $('#cometchat_recentlist').html('<div class="cometchat_nofriends">'+language['no_recent_chats']+'</div>');
                        jqcc[settings.theme].loadChatTab(1);
                    }
                }

                var userstabpopup = jqcc('#cometchat_userstab_popup');
                if(jqcc.cometchat.getThemeVariable('hasSearchbox')){
                    var chatlistheight = ($( ".right_footer" ).length == 1) ? "240px" : "259px";
                } else {
                    var chatlistheight = ($( ".right_footer" ).length == 1) ? "270px" : "286px";
                }

                if(mobileDevice){
                    userstabpopup.find('div#cometchat_userscontent').css('height',chatlistheight);
                    userstabpopup.find('#cometchat_userscontent #cometchat_recentlist > div').css({'height': chatlistheight});
                    userstabpopup.find('#cometchat_userscontent #cometchat_recentlist > div').css('overflow-y','auto');
                }else if(jqcc().slimScroll){
                    userstabpopup.find('div#cometchat_userscontent').css('height',chatlistheight);
                    userstabpopup.find('#cometchat_userscontent #cometchat_recentlist > div').css({'height': chatlistheight});
                    // userstabpopup.find('#cometchat_userscontent #cometchat_recentlist > div').slimScroll({height: chatlistheight});
                }

                $("#cometchat_search").keyup();
                $('#cometchat_recentlist div.cometchat_recentchatlist').unbind('click');
                $('#cometchat_recentlist div.cometchat_recentchatlist').live('click', function(e){
                    jqcc.cometchat.userClick(e.currentTarget,1);
                });
            },
            botList: function(item) {
                var botlisttemp = '';
                var bot_id = '';
                var bot_name = '';
                var bot_desc = '';
                var bot_structure = '';
                var bot_title = language['bot_info'];
                var bot_viewinfo = '';
                var descbox = '';
                var bot_avatar = '';

                $.each(item, function(i, bot){
                    if(bot.a == '' || typeof(bot.a) == "undefined"){
                        bot_avatar = staticCDNUrl+"layouts/docked/images/noavatar.png";
                    }else{
                        bot_avatar = bot.a;
                    }
                    botlisttemp += '<div id="cometchat_botlist_'+bot.id+'" class="cometchat_botlist" onmouseover="jqcc(this).addClass(\'cometchat_botlist_hover\');" onmouseout="jqcc(this).removeClass(\'cometchat_botlist_hover\');"><div class="cometchat_botscontentavatar"><img class="cometchat_botscontentavatarimage" src="'+bot_avatar+'"></div><div><div class="cometchat_botscontentname">'+bot.n+' <span class="cometchat_botrule"> @'+bot.n+'</span></div><div><div class="cometchat_botslist_desc">'+bot.d+'</div></div></div></div>';
                    jqcc.cometchat.setThemeArray('botlistName', bot.id, bot.n);
                    jqcc.cometchat.setThemeArray('botlistAvatar', bot.id, bot.a);
                    jqcc.cometchat.setThemeArray('botlistDescription', bot.id, bot.d);
                    jqcc.cometchat.setThemeArray('botlistApikey', bot.id, bot.api);
                });

                var userstabpopup = $('#cometchat_userstab_popup');
                if(document.getElementById('cometchat_botslist')){
                    if(botlisttemp!=''){
                        jqcc.cometchat.replaceHtml('cometchat_botslist', '<div>'+botlisttemp+'</div>');
                    }else{
                        $('#cometchat_botslist').html('<div class="cometchat_nobots">'+language['no_bots']+'</div>');
                    }
                }
                var botlistheight = userstabpopup.find('div#cometchat_userscontent').height();
                if(mobileDevice){
                    userstabpopup.find('#cometchat_botslist > div').css({'height': botlistheight});
                    userstabpopup.find('#cometchat_userscontent #cometchat_botslist > div').css('overflow-y','auto');
                }else if(jqcc().slimScroll){
                    userstabpopup.find('div#cometchat_userscontent').css('height',botlistheight);
                    userstabpopup.find('#cometchat_userscontent #cometchat_botslist > div').css({'height': botlistheight});
                    userstabpopup.find('#cometchat_userscontent #cometchat_botslist > div').slimScroll({height: botlistheight});
                }

                $('div.cometchat_botlist').on('click', function(e){
                    bot_id = $(this).attr('id');
                    bot_id = bot_id.split('_')[2];
                    bot_name = jqcc.cometchat.getThemeArray('botlistName', bot_id);
                    bot_avatar = jqcc.cometchat.getThemeArray('botlistAvatar', bot_id);
                    bot_desc = jqcc.cometchat.getThemeArray('botlistDescription', bot_id);

                    var botwidth= '400';
                    var bottop = '' ;
                    var botleft = '';
                    var bottom = '';

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
                    bottop = 'top:'+bottop+'px;';
                    bottom = 'bottom:'+bottop+'px;';

                    bot_viewinfo = '<div class="cometchat_botcontainer_'+bot_id+'" id="cometchat_botcontainer" style="'+bottop+bottom+botleft+'width:'+botwidth+'px;"><div class="cometchat_botcontainer_title" onmousedown="dragStart(event, \'cometchat_botcontainer\')"><span class="cometchat_botcontainer_name">'+bot_title+'</span><div class="cometchat_closebotsbox cometchat_tooltip" title="'+language[27]+'" id='+bot_id+'></div><div style="clear:both"></div></div><div class="cometchat_botcontainer_body"><div class="cometchat_bot_info"><div id="cometchat_botlist_'+bot_id+'" class="cometchat_botinfo"><div class="cometchat_botdata"><img class="cometchat_botavatarimage" src="'+bot_avatar+'"></div><div style="clear:both"></div></div><div class="cometchat_botname">'+bot_name+'</div><div class="cometchat_botdesc">'+bot_desc+'</div></div></div></div>';

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
                        var cometchat_slimscroll_height = $('#cometchat_botcontainer').find('.cometchat_botcontainer_body').height();
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
            chatWith: function(id){
                if(jqcc.cometchat.getThemeVariable('loggedout')==0 && jqcc.cometchat.getUserID() != id){
                    if(jqcc.cometchat.getThemeVariable('offline')==1){
                        jqcc.cometchat.setThemeVariable('offline', 0);
                        $('#cometchat_userstab_text').html(language[9]+' ('+jqcc.cometchat.getThemeVariable('lastOnlineNumber')+')');
                        jqcc.cometchat.chatHeartbeat(1);
                        $("#cometchat_optionsbutton_popup").find("span.available").click();
                    }
                    jqcc[settings.theme].createChatbox(
                        id,
                        jqcc.cometchat.getThemeArray('buddylistName', id),
                        jqcc.cometchat.getThemeArray('buddylistStatus', id),
                        jqcc.cometchat.getThemeArray('buddylistMessage', id),
                        jqcc.cometchat.getThemeArray('buddylistAvatar', id),
                        jqcc.cometchat.getThemeArray('buddylistLink', id),
                        jqcc.cometchat.getThemeArray('buddylistIsDevice', id),
                        1,
                        null
                    );
                }
            },
            userClick: function(listing,isrecent){
                if(typeof(isrecent) != "undefined" && isrecent == 1){
                    var id = $(listing).attr('id');
                    id = id.substr(21);
                } else {
                    var id = $(listing).attr('id');
                    id = id.substr(19);
                }

                if(typeof (jqcc[settings.theme].createChatbox)!=='undefined'){
                    jqcc[settings.theme].createChatbox(
                        id,
                        jqcc.cometchat.getThemeArray('buddylistName', id),
                        jqcc.cometchat.getThemeArray('buddylistStatus', id),
                        jqcc.cometchat.getThemeArray('buddylistMessage', id),
                        jqcc.cometchat.getThemeArray('buddylistAvatar', id),
                        jqcc.cometchat.getThemeArray('buddylistLink', id),
                        jqcc.cometchat.getThemeArray('buddylistIsDevice', id),
                        1,
                        null
                    );
                }
            },
            createChatbox: function(id, name, status, message, avatar, link, isdevice, chatboxstate, unreadmessagecount, restored){
                if(id==null||id==''){
                    return;
                }
                if(jqcc.cometchat.getThemeArray('buddylistName', id)==null||jqcc.cometchat.getThemeArray('buddylistName', id)==''){
                    jqcc.cometchat.createChatboxSet(id, name, status, message, avatar, link, isdevice, chatboxstate, unreadmessagecount, restored);
                }else{
                    if(typeof (jqcc[settings.theme].createChatboxData)!=='undefined'){
                            jqcc[settings.theme].createChatboxData(id, jqcc.cometchat.getThemeArray('buddylistName', id), jqcc.cometchat.getThemeArray('buddylistStatus', id), jqcc.cometchat.getThemeArray('buddylistMessage', id), jqcc.cometchat.getThemeArray('buddylistAvatar', id), jqcc.cometchat.getThemeArray('buddylistLink', id), jqcc.cometchat.getThemeArray('buddylistIsDevice', id), chatboxstate, unreadmessagecount, restored);
                    }
                }
            },
            createChatboxData: function(id, name, status, message, avatar, link, isdevice, chatboxstate, unreadmessagecount, restored){
                var cometchat_chatboxes = $("#cometchat_chatboxes");
                if(typeof(restored) == "undefined"){
                    var restored = 0;
                }
                if(chatboxOpened[id]!=null){
                    if(!$("#cometchat_user_"+id).hasClass('cometchat_tabclick')&&chatboxstate!=1){
                        if($('#cometchat_unseenUsers').find('#cometchat_user_'+id).length != 0) {
                            uid = 'cometchat_user_'+id;
                            jqcc[settings.theme].swapTab(uid,1);
                        } else if(restored!=1){
                            jqcc.cometchat.updateChatBoxState({id:id,s:chatboxstate});
                        }
                    }else if(chatboxstate == 1){
                        $("#cometchat_user_"+id).click();
                    }
                    jqcc[settings.theme].scrollBars();
                }else{
                var widthavailable = (jqcc(window).width() - jqcc('#cometchat_chatboxes').outerWidth() - chatboxWidth - chatboxDistance);
                if(widthavailable < (chatboxWidth+chatboxWidth)){
                    jqcc[settings.theme].rearrange(1);
                }else{
                    $('#cometchat_chatboxes_wide').width($('#cometchat_chatboxes_wide').width()+chatboxWidth+chatboxDistance);
                    $('#cometchat_chatboxes').css(jqcc.cometchat.getThemeVariable('dockedAlignment'),$('#cometchat_userstab').outerWidth(true)+'px');
                }
                var isMobile = '';
                /*if(isdevice == 1) {
                     isMobile = '<div class="cometchat_isMobile cometchat_isMobile_'+status+' cometchat_floatL"><div class="cometchat_mobileDot"></div></div>';
                }*/
                shortname = name;
                longname = name;
                var userAvatar = '';
                if(settings.dockedChatBoxAvatar == 1) {
                     userAvatar = '<div class="cometchat_userschatboxavatar"><img class="chatbox_avatar" src="'+jqcc.cometchat.getThemeArray('buddylistAvatar', id)+'" title="'+jqcc.cometchat.getThemeArray('buddylistName', id)+'"></div>';
                }
                if(jqcc('#cometchat_user_'+id).length == 0){
                    var chatBoxInlineCss = {'margin-right':chatboxDistance+'px','width':chatboxWidth+'px'};
                    if(settings.dockedAlignToLeft == 1){
                        chatBoxInlineCss = {'margin-left':chatboxDistance+'px','width':chatboxWidth+'px'};
                    }
                    $("<span/>").attr("id", "cometchat_user_"+id).attr("amount", 0).attr("userid", id).addClass("cometchat_tab").addClass('cometchat_tabopen_bottom').css(chatBoxInlineCss).html(isMobile +'<div class="cometchat_user_shortname">'+userAvatar+shortname+'</div><div class="cometchat_closebox cometchat_tooltip" title="'+language[74]+'" id="cometchat_closebox_bottom_'+id+'" style="margin-right:5px;"></div><div class="cometchat_unreadCount cometchat_floatR" style="display:none;"></div>').prependTo($("#cometchat_chatboxes_wide"));
                }
                var startlink = '';
                var endlink = '';
                var pluginstophtml = '';
                var pluginsbottomhtml='';
                var avchathtml = '';
                var audiochathtml = '';
                var smiliehtml = '';
                var pluginslength = settings.plugins.length;

                if((jqcc.cometchat.getThemeArray('isJabber', id)!=1)){
                    if(link != '' || pluginslength>0){
                        pluginstophtml += '<div class="cometchat_pluginstop">';
                        if(link != ''){
                            pluginstophtml += '<div class="cometchat_plugins_dropdownlist" name="cc_viewprofile" to="'+id+'" chatroommode="0" title="'+language['view_profile']+'" ><div class="cometchat_plugins_name <?php echo $cometchat_float;?>">'+language['view_profile']+'</div></div>';
                        }
                    }
                    if(pluginslength>0){
                        pluginsbottomhtml += '<div class="cometchat_pluginsbottom">';
                        for(var i = 0; i<pluginslength; i++){
                            var name = 'cc'+settings.plugins[i];
                            if(mobileDevice && (settings.plugins[i]=='transliterate' || settings.plugins[i]=='screenshare')) {
                                continue;
                            }

                            if(typeof ($[name])=='object'){
                                if(settings.plugins[i]=='avchat') {
                                    if(mobileDevice){
                                            pluginsbottomhtml += '<div class="cometchat_plugins_openuplist" name="'+name+'" to="'+id+'" chatroommode="0" title="'+$[name].getTitle()+'" ><div class="cometchat_plugins_name <?php echo $cometchat_float;?>">'+$[name].getTitle()+'</div></div>';
                                    } else {
                                        avchathtml = '<div id="cometchat_'+settings.plugins[i]+'_'+id+'" class="cometchat_tooltip cometchat_tabicons cometchat_'+settings.plugins[i]+'" name="'+name+'" to="'+id+'" chatroommode="0" title="'+$[name].getTitle()+'"></div>';
                                    }
                                } else if(settings.plugins[i]=='audiochat') {
                                    if(mobileDevice){
                                            pluginsbottomhtml += '<div class="cometchat_plugins_openuplist" name="'+name+'" to="'+id+'" chatroommode="0" title="'+$[name].getTitle()+'" ><div class="cometchat_plugins_name <?php echo $cometchat_float;?>">'+$[name].getTitle()+'</div></div>';
                                    } else {
                                        audiochathtml = '<div id="cometchat_'+settings.plugins[i]+'_'+id+'" class="cometchat_tooltip cometchat_tabicons cometchat_'+settings.plugins[i]+'" name="'+name+'" to="'+id+'" chatroommode="0" title="'+$[name].getTitle()+'"></div>';
                                    }
                                } else if(settings.plugins[i]=='smilies') {
                                    smiliehtml = '<div class="cometchat_'+settings.plugins[i]+'" name="'+name+'" to="'+id+'" chatroommode="0" title="'+$[name].getTitle()+'"></div>';
                                } else if(settings.plugins[i]=='clearconversation' || settings.plugins[i]=='report' || settings.plugins[i]=='chathistory' || settings.plugins[i]=='block' || settings.plugins[i]=='save'){
                                        pluginstophtml += '<div class="cometchat_plugins_dropdownlist" name="'+name+'" to="'+id+'" chatroommode="0" title="'+$[name].getTitle()+'" ><div class="cometchat_plugins_name <?php echo $cometchat_float;?>">'+$[name].getTitle()+'</div></div>';
                                }else{
                                        pluginsbottomhtml += '<div class="cometchat_plugins_openuplist" name="'+name+'" to="'+id+'" chatroommode="0" title="'+$[name].getTitle()+'" ><div class="cometchat_plugins_name <?php echo $cometchat_float;?>">'+$[name].getTitle()+'</div></div>';
                                }
                            }
                        }
                        pluginsbottomhtml += '</div>';
                    }
                    if(link != '' || pluginslength>0){
                        pluginstophtml += '</div>';
                    }
                }

                if(typeof(chatboxstate)=="undefined" || chatboxstate == ''){
                    chatboxstate = 1;
                }
                var tabstateclass = (chatboxstate == 2)?'tabhidden':'tabopen';
                var prepend = '';
                var jabber = jqcc.cometchat.getThemeArray('isJabber', id);
                var plugins_openup_css = '';
                var inner_container_margin = '';
                var send_message_box = '';
                var cometchat_textarea = '';
                if(pluginsbottomhtml=='<div class="cometchat_pluginsbottom"></div>') {
                    plugins_openup_css = 'display:none';
                    inner_container_margin = 'margin-left:0px !important';
                }
                if(jqcc.cometchat.getThemeVariable('prependLimit') != '0' && jabber != 1){
                    prepend = '<div class="cometchat_prependMessages" onclick="jqcc.docked.prependMessagesInit('+id+')" id = "cometchat_prependMessages_'+id+'" style="display:block;">'+language[83]+'</div>';
                }
                if(mobileDevice){
                    cctextarea_width = "width:140px !important";
                    send_message_box = '<div id="cometchat_sendmessagebtn"></div>';
                }else{
                    cctextarea_width = "width:"+(chatboxWidth-65)+"px !important";
                }
                cometchat_textarea_struct = '<div class="cometchat_inner_container" style="'+inner_container_margin+'"><textarea class="cometchat_textarea" style="'+cctextarea_width+'"; placeholder="'+language[85]+'"></textarea>'+send_message_box+smiliehtml+'</div>';

                var plugin_divider = '<div class="cometchat_vline"></div>';
                if (audiochathtml == '' && avchathtml == '') {
                    plugin_divider = '';
                }

                $("<div/>").attr("id", "cometchat_user_"+id+"_popup").addClass('cometchat_tabpopup').addClass('cometchat_'+tabstateclass).html('<div class="cometchat_tabtitle">'+isMobile+'<span id="cometchat_typing_'+id+'" class="cometchat_typing"></span>'+userAvatar+'<div class="cometchat_name" title="'+longname+'">'+longname+'</div><div id="cometchat_closebox_'+id+'" title="'+language[74]+'" class="cometchat_closebox cometchat_floatR cometchat_tooltip"></div>'+plugin_divider+audiochathtml+avchathtml+'<div class="cometchat_plugins_dropdown"><div class="cometchat_plugins_dropdown_icon cometchat_tooltip" id="cometchat_plugins_dropdown_icon_'+id+'" title="'+language[73]+'"></div><div class="cometchat_popup_plugins">'+pluginstophtml+'</div></div></div></div><div class="cometchat_tabcontent"><div class = "cometchat_messagElement cometchat_lastseenmessage" id="cometchat_messagElement_'+id+'"></div><div class="cometchat_tabcontenttext" id="cometchat_tabcontenttext_'+id+'" onscroll="jqcc.'+settings.theme+'.chatScroll(\''+id+'\');"></div><div class="cometchat_tabcontentinput"><div class="cometchat_plugins_openup cometchat_floatL" style="'+plugins_openup_css+'"><div class="cometchat_plugins_openup_icon cometchat_tooltip" id="cometchat_plugins_openup_icon_'+id+'" title="'+language[73]+'"></div><div class="cometchat_popup_convo_plugins">'+pluginsbottomhtml+'</div></div>'+cometchat_textarea_struct+'</div></div>').appendTo($('#cometchat_user_'+id));

                if(restored!=1){
                    jqcc.cometchat.updateChatBoxState({id:id,s:chatboxstate});
                }
                jqcc.docked.addPopup(id);
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
                            $('#cometchat_messagElement_'+id).hide();
                        }
                    }
                }

                var cometchat_user_popup = $("#cometchat_user_"+id+"_popup");
                var cometchat_user_popup1 = document.getElementById("cometchat_user_"+id+"_popup");

                cometchat_user_popup.find('.cometchat_tabcontenttext').click(function(){
                    if(cometchat_user_popup.find(".cometchat_tabcontent .cometchat_chatboxpopup_"+id).length){
                        closeChatboxCCPopup(id,0);
                    }
                });

                if(cometchat_user_popup.find(".cometchat_prependMessages").length == 0){
                    $('#cometchat_user_'+id+'_popup').find('#cometchat_tabcontenttext_'+id).append(prepend);
                    $('#cometchat_user_'+id+'_popup').find(".cometchat_prependMessages").css("display","block");
                }

                jqcc.cometchat.setThemeArray('chatBoxesOrder', id, 0);
                chatboxOpened[id] = 1;
                allChatboxes[id] = 0;
                var temp = [];
                jqcc.each(chatboxOpened, function(key, value) {
                    if(value == 1) {
                        temp.push(parseInt(key));
                    }
                });
                jqcc.cometchat.setThemeVariable('openChatboxId', temp);
                var cometchat_user_id = $("#cometchat_user_"+id);

                if(!cometchat_user_popup.find('cometchat_uploadfile_'+id).length) {
                    var uploadf = document.createElement("INPUT");
                    uploadf.setAttribute("type", "file");
                    uploadf.setAttribute("class", "cometchat_fileupload");
                    uploadf.setAttribute("id", 'cometchat_uploadfile_'+id);
                    uploadf.setAttribute("name", "Filedata");
                    uploadf.setAttribute("multiple", "true");
                    cometchat_user_popup.find(".cometchat_tabcontent").append(uploadf);
                    uploadf.addEventListener("change", jqcc.ccfiletransfer.FileSelectHandler(cometchat_user_popup.find('.cometchat_tabcontent'),id,0), false);
                }

                cometchat_user_popup.click(function(){
                    var id = $(this).attr('id');
                    id = id.substring(15, id.length-6);
                    var classname = cometchat_user_popup.find('#cometchat_messagElement_'+id).attr('class')  == "cometchat_messagElement cometchat_lastseenmessage";
                    if(classname){
                        jqcc[settings.theme].hideLastseen(id);
                    }
                });

                cometchat_user_popup.mouseleave(function(){
                    var id = $(this).attr('id');
                    id = id.substring(15, id.length-6);
                    var classname = cometchat_user_popup.find('#cometchat_messagElement_'+id).attr('class')  == "cometchat_messagElement cometchat_lastseenmessage";
                    if(settings.lastseen && $.cookie(settings.cookiePrefix+"disablelastseen") == 'false' && classname){
                        var buddylastseen = jqcc.cometchat.getThemeArray('buddylistLastseen', id);
                        var statusmsg = jqcc.cometchat.getThemeArray('buddylistStatus', id);
                        var lstsnSetting = jqcc.cometchat.getThemeArray('buddylistLastseensetting', id);
                        var ishidden = cometchat_user_popup.find('#cometchat_messagElement_'+id).is(':hidden');
                        var cookievalue = $.cookie(settings.cookiePrefix+"disablelastseen");
                        var currentts = Math.floor(new Date().getTime()/1000);

                        if(ishidden && cookievalue == 'false' && ((statusmsg == 'away' || statusmsg == 'invisible' || statusmsg == 'busy' || statusmsg == 'offline') || currentts-buddylastseen > (60*10)) && lstsnSetting == '0' && $('#cometchat_messagElement_'+id).html() != ""){
                            $('#cometchat_messagElement_'+id).slideDown(500);
                        }else if(statusmsg != 'available' && buddylastseen == "" && $('#cometchat_messagElement_'+id).html() != ""){
                            $('#cometchat_messagElement_'+id).slideDown(500);
                        }
                    }
                });

                cometchat_user_popup.find('.cometchat_plugins_dropdown').click(function(e){
                    e.stopImmediatePropagation();
                    if(cometchat_user_popup.find(".cometchat_tabcontent .cometchat_chatboxpopup_"+id).length){
                        closeChatboxCCPopup(id);
                    }
                    if(cometchat_user_popup.find('.cometchat_plugins_openup').hasClass('cometchat_plugins_openup_active')) {
                        cometchat_user_popup.find('.cometchat_plugins_openup').toggleClass('cometchat_plugins_openup_active').find('div.cometchat_popup_convo_plugins').slideToggle('fast');
                        if($(this).hasClass('cometchat_plugins_openup_active')){
                            cometchat_user_popup.find('#cometchat_plugins_openup_icon_'+id).addClass('cometchat_pluginsopenup_arrowrotate');
                        } else {
                            cometchat_user_popup.find('#cometchat_plugins_openup_icon_'+id).removeClass('cometchat_pluginsopenup_arrowrotate');
                        }
                    }
                    $(this).toggleClass('cometchat_plugins_dropdown_active').find('div.cometchat_popup_plugins').slideToggle('fast');

                    if($(this).hasClass('cometchat_plugins_dropdown_active')){
                        cometchat_user_popup.find('#cometchat_plugins_dropdown_icon_'+id).addClass('cometchat_pluginsdropdown_arrowrotate');
                    } else {
                        cometchat_user_popup.find('#cometchat_plugins_dropdown_icon_'+id).removeClass('cometchat_pluginsdropdown_arrowrotate');
                    }

                    if(mobileDevice){
                        cometchat_user_popup.find('.cometchat_pluginstop').css('overflow-y','auto');
                    }
                    if(jqcc().slimScroll){
                        var cometchat_slimscroll_height = cometchat_user_popup.find('.cometchat_pluginstop').height();
                        var cometchat_slimscroll_width = cometchat_user_popup.find('.cometchat_pluginstop').width();
                        cometchat_user_popup.find('.cometchat_pluginstop').slimScroll({height: (cometchat_slimscroll_height)+'px', width: (cometchat_slimscroll_width)+'px'});
                        cometchat_user_popup.find('.cometchat_popup_plugins').find('.slimScrollDiv').css({'box-shadow': '0px 5px 8px -3px #D1D1D1'});
                    }
                });

                cometchat_user_popup.find('.cometchat_plugins_openup').click(function(){
                    if(cometchat_user_popup.find(".cometchat_tabcontent .cometchat_chatboxpopup_"+id).length){
                        closeChatboxCCPopup(id);
                    } else {
                        if(cometchat_user_popup.find('.cometchat_plugins_dropdown').hasClass('cometchat_plugins_dropdown_active')) {
                            cometchat_user_popup.find('.cometchat_plugins_dropdown').toggleClass('cometchat_plugins_dropdown_active').find('div.cometchat_popup_plugins').slideToggle('fast');
                            if($(this).hasClass('cometchat_plugins_dropdown_active')){
                                cometchat_user_popup.find('#cometchat_plugins_dropdown_icon_'+id).addClass('cometchat_pluginsdropdown_arrowrotate');
                            } else {
                                cometchat_user_popup.find('#cometchat_plugins_dropdown_icon_'+id).removeClass('cometchat_pluginsdropdown_arrowrotate');
                            }
                        }
                        $(this).toggleClass('cometchat_plugins_openup_active').find('div.cometchat_popup_convo_plugins').slideToggle('fast');

                        if($(this).hasClass('cometchat_plugins_openup_active')){
                            cometchat_user_popup.find('#cometchat_plugins_openup_icon_'+id).addClass('cometchat_pluginsopenup_arrowrotate');
                        } else {
                            cometchat_user_popup.find('#cometchat_plugins_openup_icon_'+id).removeClass('cometchat_pluginsopenup_arrowrotate');
                        }
                    }

                    if(mobileDevice){
                        cometchat_user_popup.find('.cometchat_pluginsbottom').css('overflow-y','auto');
                    }else if(jqcc().slimScroll){
                        var cometchat_slimscroll_height = cometchat_user_popup.find('.cometchat_pluginsbottom').height();
                        var cometchat_slimscroll_width = cometchat_user_popup.find('.cometchat_pluginsbottom').width();
                        if(cometchat_user_popup.find('.cometchat_pluginsbottom').parent().hasClass('slimScrollDiv')){
                            cometchat_user_popup.find('.cometchat_popup_convo_plugins').find("div.slimScrollDiv").css('height', (cometchat_slimscroll_height+1)+'px');
                            cometchat_user_popup.find('.cometchat_popup_convo_plugins').find("div.slimScrollDiv").css('width', (cometchat_slimscroll_width+1)+'px');
                        }else{
                            cometchat_user_popup.find('.cometchat_pluginsbottom').slimScroll({height: (cometchat_slimscroll_height+1)+'px', width: (cometchat_slimscroll_width+1)+'px'});
                        }
                        var scrolltop_height = parseInt(285 - cometchat_slimscroll_height);
                        cometchat_user_popup.find('.cometchat_popup_convo_plugins').find('.slimScrollDiv').css({'top':scrolltop_height,'box-shadow': '0px -4px 10px -3px #d1d1d1'});
                    }
                });

                cometchat_user_popup.find('.cometchat_plugins_openuplist, .cometchat_plugins_dropdownlist, .cometchat_smilies, .cometchat_avchat, .cometchat_audiochat').click(function(e){
                    e.stopImmediatePropagation();
                    var name = $(this).attr('name');
                    var to = $(this).attr('to');
                    var chatroommode = $(this).attr('chatroommode');
                    var controlparameters = {"to":to, "chatroommode":chatroommode};
                    if(cometchat_user_popup.find('.cometchat_plugins_openup').hasClass('cometchat_plugins_openup_active')){
                        cometchat_user_popup.find('.cometchat_plugins_openup').toggleClass('cometchat_plugins_openup_active').find('div.cometchat_popup_convo_plugins').slideToggle('fast');
                        if($(this).hasClass('cometchat_plugins_openup_active')){
                            cometchat_user_popup.find('#cometchat_plugins_openup_icon_'+id).addClass('cometchat_pluginsopenup_arrowrotate');
                        } else {
                            cometchat_user_popup.find('#cometchat_plugins_openup_icon_'+id).removeClass('cometchat_pluginsopenup_arrowrotate');
                        }
                    }
                    if(cometchat_user_popup.find('.cometchat_plugins_dropdown').hasClass('cometchat_plugins_dropdown_active')){
                        cometchat_user_popup.find('.cometchat_plugins_dropdown').toggleClass('cometchat_plugins_dropdown_active').find('div.cometchat_popup_plugins').slideToggle('fast');
                        if($(this).hasClass('cometchat_plugins_dropdown_active')){
                            cometchat_user_popup.find('#cometchat_plugins_dropdown_icon_'+id).addClass('cometchat_pluginsdropdown_arrowrotate');
                        } else {
                            cometchat_user_popup.find('#cometchat_plugins_dropdown_icon_'+id).removeClass('cometchat_pluginsdropdown_arrowrotate');
                        }
                    }
                    if(name == 'cc_viewprofile'){
                        location.href = jqcc.cometchat.getThemeArray('buddylistLink', id);
                    } else {
                        jqcc[name].init(controlparameters);
                    }
                });

                cometchat_user_id.find('.chatbox_avatar').click(function(e){
                    if (jqcc.cometchat.getThemeArray('buddylistLink', id) != '') {
                        location.href = jqcc.cometchat.getThemeArray('buddylistLink', id);
                    }
                });

                cometchat_user_id.find('.cometchat_closebox').click(function(e){
                    e.stopImmediatePropagation();
                    jqcc.docked.closeChatbox(id);
                });

                cometchat_user_popup.find('.cometchat_tabtitle').click(function(e){
                    e.stopImmediatePropagation();
                    cometchat_user_id.find(cometchat_user_popup).removeClass('cometchat_tabopen').addClass('cometchat_tabhidden');
                    chatboxOpened[id] = 0;
                    jqcc.cometchat.updateChatBoxState({id:id,s:2});
                });

                cometchat_user_id.off("click").click(function(e){
                    cometchat_user_id.find(cometchat_user_popup).removeClass('cometchat_tabhidden').addClass('cometchat_tabopen');
                    $.each($('#cometchat_user_'+id+'_popup .cometchat_chatboxmessage'),function (i,divele){
                        if($(this).find(".cometchat_ts") != ''){
                            var msg_containerHeight = $(this).find(".cometchat_ts_margin").outerHeight()-8;
                            var cometchat_ts_margin_right = $(this).find(".cometchat_ts_margin").outerWidth(true)+5;
                            jqcc(this).find('.cometchat_ts').css('margin-top',msg_containerHeight);
                            jqcc(this).find('.cometchat_ts').css('margin-right',cometchat_ts_margin_right);
                        }
                    });
                    jqcc.cometchat.setThemeArray('chatBoxesOrder', id, 0);
                    chatboxOpened[id] = 1;
                    jqcc.cometchat.updateChatBoxState({id:id,s:1,c:0});
                    jqcc.docked.addPopup(id);
                });

                cometchat_user_popup.find("textarea.cometchat_textarea").blur(function(event){
                    jqcc.cometchat.typingTo({id:id,method:'typingStop'});
                });

                var cometchat_textarea = $("#cometchat_user_"+id+'_popup').find("textarea.cometchat_textarea");
                cometchat_textarea.keydown(function(event){
                    if(typingSenderFlag != 0 ) {
                        jqcc.cometchat.typingTo({id:id,method:'typingTo'});
                        typingSenderFlag = 0;
                        clearTimeout(typingSenderTimer);
                        typingSenderTimer = setTimeout(function(){
                            typingSenderFlag = 1;
                            jqcc.cometchat.typingTo({id:id,method:'typingStop'});
                        },5000);
                    }
                    return jqcc[settings.theme].chatboxKeydown(event, this, id);
                });
                cometchat_textarea.keyup(function(event){
                    jqcc.docked.resizeinputTextarea(cometchat_user_popup,this,id,event);
                    return jqcc[settings.theme].chatboxKeyup(event, this, id);
                });
                var extensions_list = settings.extensions;
                if(extensions_list.indexOf('ads') > -1){
                    jqcc.ccads.init(id);
                }
                jqcc('#cometchat_sendmessagebtn').click(function(e){
                    var message = cometchat_textarea.val();
                    message = message.replace(/^\s+|\s+$/g, "");
                    jqcc.cometchat.sendMessage(id, message);
                    cometchat_textarea.val('');
                    cometchat_textarea.addClass('cometchat_placeholder');
                    $(cometchat_textarea).attr('style', 'height: 15px !important;width:140px !important;');
                    cometchat_user_popup.find('.cometchat_inner_container').height(20);
                    if(cometchat_user_popup.find('.cometchat_tabcontent .cometchat_chatboxpopup_'+id).length == 0){
                        cometchat_user_popup.find('.cometchat_tabcontenttext').height(chatboxHeight-75);
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
                                cometchat_user_popup.find('.cometchat_iframe')[0].contentWindow.postMessage('CC^CONTROL_'+JSON.stringify(paramstoresizeIframe),'*');
                            }else{
                                cometchat_user_popup.find('.cometchat_iframe').height(default_height);////143 is the default height of sketch popup
                            }
                        }
                    }
                });
                if(olddata[id]!=1&&(jqcc.cometchat.getExternalVariable('initialize')!=1||isNaN(id))){
                    jqcc.cometchat.updateChatboxSet(id);
                    olddata[id] = 1;
                }
                attachPlaceholder("#cometchat_user_"+id+'_popup');
                cometchat_user_id.find('.cometchat_closebox').click(function(e){
                    e.stopImmediatePropagation();
                    jqcc.docked.closeChatbox(id);
                });

                cometchat_user_popup.find('.cometchat_tabtitle').click(function(e){
                    e.stopImmediatePropagation();
                    cometchat_user_id.find(cometchat_user_popup).removeClass('cometchat_tabopen').addClass('cometchat_tabhidden');
                    chatboxOpened[id] = 0;
                    if(restored!=1){
                        jqcc.cometchat.updateChatBoxState({id:id,s:2});
                    }
                });

                jqcc.docked.updateReadMessages(id);
                //jqcc.docked.rearrange();
                }
                if(typeof(jqcc.cometchat.checkInternetConnection) && !jqcc.cometchat.checkInternetConnection()) {
                    jqcc.docked.noInternetConnection(true);
                }
            },
            resizeinputTextarea: function(cometchat_user_popup,chatboxtextarea,id,event){
                var forced = 1;
                var difference = $(chatboxtextarea).innerHeight() - $(chatboxtextarea).height();
                var cctabcontenttext_resize = '';
                var container_height = cometchat_user_popup.find('.cometchat_inner_container').outerHeight();
                var textAreaResizeWidth = chatboxWidth-68;
                if ($(chatboxtextarea).innerHeight < chatboxtextarea.scrollHeight ) {
                } else if(event.keyCode != 13) {
                    if($(chatboxtextarea).height() < 50 || event.keyCode == 8) {
                        if(mobileDevice){
                            $(chatboxtextarea).attr('style', 'height: 15px !important;width:140px !important;');
                        }else{
                            $(chatboxtextarea).attr('style', 'height: 15px !important;width:'+textAreaResizeWidth+'px !important;');
                        }
                        cometchat_user_popup.find('.cometchat_inner_container').height(20);
                        if(chatboxtextarea.scrollHeight - difference >= 47){
                                if(mobileDevice){
                                    $(chatboxtextarea).attr('style', 'height: 50px !important;width:140px !important;');
                                    cometchat_user_popup.find('.cometchat_inner_container').height((chatboxtextarea.scrollHeight - difference) + 12);
                                    $(chatboxtextarea).css('overflow-y','auto');
                                }else{
                                    if($(chatboxtextarea).parent().attr('class') != 'slimScrollDiv'){
                                        $(chatboxtextarea).slimScroll({scroll: '1'});
                                    }
                                    $(chatboxtextarea).attr('style', 'height: 50px !important;width:'+textAreaResizeWidth+'px !important;');
                                    cometchat_user_popup.find('.cometchat_inner_container').height((chatboxtextarea.scrollHeight - difference) + 12);
                                    cometchat_user_popup.find('.cometchat_inner_container .slimScrollDiv').css({'float':'left','width':(textAreaResizeWidth+8)+'px'});
                                }
                                $(chatboxtextarea).focus();
                                cometchat_user_popup.find('.cometchat_inner_container').height(56);

                        }else if(chatboxtextarea.scrollHeight - difference>20){
                            if(mobileDevice){
                                $(chatboxtextarea).attr('style', 'height: '+(chatboxtextarea.scrollHeight - difference)+'px !important;width:140px !important;');
                            }else{
                                $(chatboxtextarea).attr('style', 'height: '+(chatboxtextarea.scrollHeight - difference)+'px !important;width:'+textAreaResizeWidth+'px !important;');
                            }
                            cometchat_user_popup.find('.cometchat_inner_container').height((chatboxtextarea.scrollHeight - difference) + 7);
                        }
                        var newcontainerheight = cometchat_user_popup.find('.cometchat_inner_container').outerHeight();
                        if(container_height!=(newcontainerheight)){
                            cctabcontenttext_resize = (cometchat_user_popup.find('.cometchat_tabcontent').height() - cometchat_user_popup.find('.cometchat_inner_container').height() - 10);
                            if(cometchat_user_popup.find('.cometchat_tabcontent .cometchat_chatboxpopup_'+id).length == 0){
                                cometchat_user_popup.find('.cometchat_tabcontenttext').height(cctabcontenttext_resize - 1);
                                if($('#cometchat_tabcontenttext_'+id).parent().hasClass('slimScrollDiv') == true){
                                    $('#cometchat_tabcontenttext_'+id).parent().height(cctabcontenttext_resize);
                                }
                                jqcc[settings.theme].scrollDown(id);
                            }else{
                                var iframe_name = jqcc('.cometchat_iframe').attr('id');
                                var default_height = 0;//default height of sketch popup of handwrite
                                if (iframe_name == 'cometchat_trayicon_smilies_iframe'){
                                    default_height = 108;
                                }else if(iframe_name == 'cometchat_trayicon_stickers_iframe'){
                                    default_height = 102;
                                }else if(iframe_name == 'cometchat_trayicon_handwrite_iframe'){
                                    default_height = 143;
                                }
                                if(default_height!=0){
                                    var new_height = (cometchat_user_popup.find('.cometchat_tabcontentinput').height()-22);
                                    var paramstoresizeIframe = {
                                        type:"plugin",
                                        name:"smilies",
                                        method: 'resizeContainerbody',
                                        params:{
                                            height:default_height-new_height
                                        }
                                    };
                                    cometchat_user_popup.find('.cometchat_iframe')[0].contentWindow.postMessage('CC^CONTROL_'+JSON.stringify(paramstoresizeIframe),'*');
                                }
                            }
                            var inputheight = cometchat_user_popup.find('.cometchat_tabcontentinput').outerHeight();
                            cometchat_user_popup.find('.cometchat_popup_convo_plugins').css('bottom',inputheight);
                            var scrolltop_height = parseInt(cometchat_user_popup.find('.cometchat_popup_convo_plugins').outerHeight() - cometchat_user_popup.find('.cometchat_pluginsbottom').outerHeight());
                            cometchat_user_popup.find('.cometchat_popup_convo_plugins').find('.slimScrollDiv').css({'top':scrolltop_height});

                        }
                    }
                }else{
                    if(mobileDevice){
                        $(chatboxtextarea).attr('style', 'height: 15px !important;width:140px !important;');
                    }else{
                        $(chatboxtextarea).attr('style', 'height: 15px !important;width:165px !important;');
                    }
                    cometchat_user_popup.find('.cometchat_inner_container').height(20);
                    if(cometchat_user_popup.find('.cometchat_tabcontent .cometchat_chatboxpopup_'+id).length == 0){
                        cometchat_user_popup.find('#cometchat_tabcontenttext_'+id).height(chatboxHeight-75);
                        if($('#cometchat_tabcontenttext_'+id).parent().hasClass('slimScrollDiv') == true){
                            $('#cometchat_tabcontenttext_'+id).parent().height(chatboxHeight-75);
                        }
                        jqcc[settings.theme].scrollDown(id);
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
                                cometchat_user_popup.find('.cometchat_iframe')[0].contentWindow.postMessage('CC^CONTROL_'+JSON.stringify(paramstoresizeIframe),'*');
                            }else{
                                cometchat_user_popup.find('.cometchat_iframe').height(default_height);////143 is the default height of sketch popup
                            }
                        }
                    }
                    cometchat_user_popup.find('.cometchat_popup_convo_plugins').css('bottom',29);
                    var scrolltop_height = parseInt(cometchat_user_popup.find('.cometchat_popup_convo_plugins').outerHeight() - cometchat_user_popup.find('.cometchat_pluginsbottom').outerHeight());
                    cometchat_user_popup.find('.cometchat_popup_convo_plugins').find('.slimScrollDiv').css({'top':scrolltop_height});

                }
            },
            closeChatbox: function(id,from){
                var cometchat_user_popup = $("#cometchat_user_"+id+"_popup");

                if(cometchat_user_popup.length != 0){
                    jqcc.cometchat.setThemeArray('chatBoxesOrder', id, null);
                    delete(chatboxOpened[id]);
                    delete(allChatboxes[id]);
                    olddata[id] = 0;
                    jqcc.cometchat.updateChatBoxState({id:id,s:0,r:from});
                }

                if($('#cometchat_unseenchatboxes').find("#cometchat_user_"+id).length == 1) {
                    cometchat_user_popup.remove();
                    $("#cometchat_user_"+id).remove();
                    $('#cometchat_chatbox_left').click();
                    return;
                } else {
                    cometchat_user_popup.remove();
                    $("#cometchat_user_"+id).remove();
                }

               var cometchat_bot_popup = $("#cometchat_bot_"+id+"_popup");
               if(cometchat_bot_popup.length != 0){
                   cometchat_bot_popup.remove();
                   $("#cometchat_bot_"+id).remove();
                   $('#cometchat_chatboxes_wide').width($('#cometchat_chatboxes_wide').width()-chatboxWidth-chatboxDistance);
               }

                if($('#cometchat_unseenUsers').children().length > 0){
                    jqcc[settings.theme].popoutUnseenuser();
                }else{
                    $('#cometchat_chatboxes_wide').width($('#cometchat_chatboxes_wide').width()-chatboxWidth-chatboxDistance);
                }
            },
            updateChatboxSuccess: function(id, data){
                var name = jqcc.cometchat.getThemeArray('buddylistName', id);
                $("#cometchat_tabcontenttext_"+id).html('');
                if(jqcc.cometchat.getThemeVariable('prependLimit') != '0' && $('#cometchat_tabcontenttext_'+id).find(".cometchat_prependMessages").length == 0){
                    var prepend = '<div class=\"cometchat_prependMessages\" onclick\="jqcc.docked.prependMessagesInit('+id+')\" id = \"cometchat_prependMessages_'+id+'\">'+language[83]+'</div>';
                    $('#cometchat_tabcontenttext_'+id).append(prepend);
                    $('#cometchat_tabcontenttext_'+id).find(".cometchat_prependMessages").css('display','block');
                }
                if(typeof (jqcc[settings.theme].addMessages)!=='undefined'&&data.hasOwnProperty('messages')){
                    jqcc[settings.theme].addMessages(data['messages']);
                }
                jqcc[settings.theme].scrollDown(id);
            },
            addMessages: function(item){
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
                var msg_time = '';
                var msg_date_format = '';
                var jabber = '';
                var hw_ts = '';
                var cc_dir = '<?php if ($rtl == 1) { echo 1; } else { echo 0; }?>';
                var prepend = '';
                var currenttime = Math.floor(new Date().getTime()/1000);
                var messagewrapperid = '';
                var trayIcons = jqcc.cometchat.getTrayicon();
                var isRealtimetranslateEnabled = trayIcons.hasOwnProperty('realtimetranslate');

                $.each(item, function(i, incoming){
                    incoming.message = jqcc.cometchat.htmlEntities(incoming.message);
                    if(typeof (incoming.message) =='undefined'){
                        return
                    }
                    if( (!incoming.hasOwnProperty('id') || incoming.id == '') && (!incoming.hasOwnProperty('localmessageid') || incoming.localmessageid == '') && (incoming.message).indexOf('CC^CONTROL_')==-1){
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
                    if(message == null){
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
                    months_set = new Array(language['jan'],language['feb'],language['mar'],language['apr'],language['may'],language['jun'],language['jul'],language['aug'],language['sep'],language['oct'],language['nov'],language['dec']);
                    d = new Date(parseInt(msg_time));
                    month  = d.getMonth();
                    date  = d.getDate();
                    year = d.getFullYear();
                    msg_date_class = month+"_"+date+"_"+year;
                    msg_date = months_set[month]+" "+date+", "+year;
                    date_class = "";

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
                    if(settings.autoPopupChatbox==1 && incoming.self==0 && incoming.old !=1) {
                        jqcc.cometchat.chatWith(incoming.from);
                    }
                    var chatboxopen = 0;
                    var alreadyreceivedunreadmessages = jqcc.cometchat.getFromStorage('receivedunreadmessages');
                    if(incoming.self != 1 && incoming.old != 1 && jqcc.cometchat.getCcvariable()['timestamp']<incoming.id){
                        jqcc.cometchat.getCcvariable()['timestamp'] = incoming.id;
                        jqcc.docked.addPopup(incoming.from, 1);
                    }
                    if(typeof(incoming.calledfromsend) === 'undefined'){
                        jqcc.docked.updateReceivedUnreadMessages(incoming.from,incoming.id);
                    }
                    if(incoming.hasOwnProperty('id')){
                        jqcc.cometchat.sendReceipt(incoming);
                        jqcc.cometchat.setThemeArray("lastmessageid",incoming.from,incoming.id);
                    }
                    var selfstyleAvatar = "";
                    var avatar = staticCDNUrl+"layouts/docked/images/noavatar.png";
                    var smileymsg = message.replace(/<img[^>]*>/g,"").trim();
                    var single_smiley_avatar = '';
                    var smileycount = (message.match(/cometchat_smiley/g) || []).length;

                    if(smileycount == 1 && smileymsg == '') {
                        message = message.replace('height="20"', 'height="64px"');
                        message = message.replace('width="20"', 'width="64px"');
                        single_smiley_avatar = "margin-top:10px";
                    }

                    if(parseInt(incoming.self)==1 && (!incoming.hasOwnProperty('botid') || (typeof incoming.botid == "undefined" && incoming.botid == 0))){
                        fromname = language[10];
                    }else{
                        if(typeof incoming.botid != "undefined" && incoming.botid != 0){
                            fromname = jqcc.cometchat.getThemeArray('botlistName', incoming.botid);
                            if(jqcc.cometchat.getThemeArray('botlistAvatar', incoming.botid)!=""){
                                avatar = jqcc.cometchat.getThemeArray('botlistAvatar', incoming.botid);
                            }
                        } else {
                            fromname = jqcc.cometchat.getThemeArray('buddylistName', incoming.from);
                            if(jqcc.cometchat.getThemeArray('buddylistAvatar', incoming.from)!=""){
                                avatar = jqcc.cometchat.getThemeArray('buddylistAvatar', incoming.from);
                            }
                        }
                        selfstyleAvatar = '<a class="cometchat_floatL" href="'+jqcc.cometchat.getThemeArray('buddylistLink', incoming.from)+'"><img class="ccmsg_avatar" style="'+single_smiley_avatar+'" src="'+avatar+'" title="'+fromname+'"/></a>';
                    }

                    if( incoming.hasOwnProperty('id')) {
                        messagewrapperid = incoming.id;
                    }else if(incoming.hasOwnProperty('localmessageid') ) {
                        messagewrapperid = incoming.localmessageid;
                    }

                    if($("#message_"+messagewrapperid).length>0){
                        $('#message_'+messagewrapperid).html(message);
                    }else{
                        sentdata = '';
                        if(incoming.sent!=null){
                            var ts = incoming.sent;
                            sentdata = jqcc.docked.getTimeDisplay(ts);
                        }
                        var msg = '';
                        var msg1 = '';
                        var msg2 = '';
                        var addMessage = 0;
                        var avatar = staticCDNUrl+"layouts/docked/images/noavatar.png";
                        var add_bg = '';
                        var add_arrow_class = '';
                        var add_style = "";//for images and smileys
                        var jabber = jqcc.cometchat.getThemeArray('isJabber', incoming.from);

                        if(jqcc.cometchat.getThemeVariable('prependLimit') != '0' && jabber != 1){
                            var prepend = '<div class=\"cometchat_prependMessages\" onclick\="jqcc.docked.prependMessagesInit('+incoming.from+')\" id = \"cometchat_prependMessages_'+incoming.from+'\">'+language[83]+'</div>';
                        }
                        if(parseInt(incoming.self)==1 && (!incoming.hasOwnProperty('botid') || (typeof incoming.botid == "undefined" && incoming.botid == 0))){
                           var sentdata_box = "<span class=\"cometchat_ts\">"+sentdata+"</span>";
                           if((message.indexOf('<img')==-1 && message.indexOf('src')==-1) || (smileycount > 0 && smileymsg != '')){
                                add_bg = 'cometchat_chatboxmessagecontent cometchat_self';
                                add_arrow_class = '<div class="selfMsgArrow"><div class="after"></div></div>';
                           }else{
                                if(message.indexOf('cometchat_smiley')!=-1) {
                                    if(smileycount > 1){
                                        add_style = "margin-right:13px;max-width:135px;";
                                    }else{
                                        add_style = "margin-right:13px";
                                    }
                                }else if(message.indexOf('cometchat_hw_lang')!=-1){
                                    add_style = "margin-right:18px;margin-left:4px";
                                }else{
                                    add_style = "margin-right:4px;margin-left:4px";
                                }
                            }
                            msg1 = '<div class="cometchat_time cometchat_time_'+msg_date_class+' '+date_class+'" msg_format="'+msg_date_format+'">'+msg_date+'</div>';
                            msg2 = '<div class="cometchat_chatboxmessage" id="cometchat_message_'+messagewrapperid+'"><div class="'+add_bg+' '+'cometchat_ts_margin cometchat_self_msg cometchat_floatR" style="'+add_style+'"><span id="message_'+messagewrapperid+'">'+message+'</span></div>'+add_arrow_class+' '+sentdata_box+'</div><span id="cometchat_chatboxseen_'+messagewrapperid+'"></span>';
                            msg =msg1+msg2;
                            addMessage = 1;

                        }else{
                            if(message.indexOf('cometchat_hw_lang')!=-1){
                                hw_ts = 'margin-left: 4px;';
                            }

                            var sentdata_box = "<span class=\"cometchat_ts_other\" style='"+hw_ts+"'>"+sentdata+"</span>";

                            if((message.indexOf('<img')==-1 && message.indexOf('src')==-1) || (smileycount > 0 && smileymsg != '')){
                                add_bg = 'cometchat_chatboxmessagecontent';
                                add_arrow_class = '<div class="msgArrow"><div class="after"></div></div>';
                            }else{
                                if(message.indexOf('cometchat_smiley')!=-1) {
                                    if(smileycount == 1 && smileymsg == ''){
                                        add_style = "margin:-4px 0px 0px 4px";
                                    }else{
                                        if(smileycount > 1){
                                            add_style = "margin:5px 5px 0px 8px;max-width:135px";
                                        }else{
                                            add_style = "margin:5px 5px 0px 8px";
                                        }
                                    }
                                }else if(message.indexOf('cometchat_hw_lang')!=-1){
                                    add_style = "margin:0px 0px 0px 8px";
                                }else{
                                    add_style = "margin:-6px 0px 0px 8px";
                                }
                            }

                            /** START: Audio/ Video Chat */
                            if(message.indexOf('avchat_webaction=initiate')!=-1){
                                jqcc.docked.generateIncomingAvchatData(incoming,avchat_data,currenttime);
                            }else if(message.indexOf('avchat_webaction=acceptcall')!=-1) {
                                var controlparameters = {"to":incoming.from, "grp":avchat_data[2], "start_url":''};
                                if(incoming.sent > currenttime - 15){
                                    jqcc.ccavchat.accept_fid(controlparameters);
                                }
                            }

                            if(message.indexOf('audiochat_webaction=initiate')!=-1){
                                jqcc.docked.generateIncomingAvchatData(incoming,audiochat_data,currenttime);
                            }else if(message.indexOf('audiochat_webaction=acceptcall')!=-1) {
                                var controlparameters = {"to":incoming.from, "grp":audiochat_data[2], "start_url":''};
                                if(incoming.sent > currenttime - 15){
                                    jqcc.ccaudiochat.accept_fid(controlparameters);
                                }
                            }
                            /** END: Audio/ Video Chat */

                            msg1 = '<div class="cometchat_time cometchat_time_'+msg_date_class+' '+date_class+'" msg_format="'+msg_date_format+'">'+msg_date+'</div>';
	                        msg2 = '<div '+calldisplay+' class="cometchat_chatboxmessage" id="cometchat_message_'+messagewrapperid+'">'+selfstyleAvatar+'<div class="'+add_bg+' '+'cometchat_ts_margin cometchat_floatL" style="'+add_style+'"><span id="message_'+messagewrapperid+'" class="cometchat_msg">'+message+'</span></div>'+add_arrow_class+' '+sentdata_box+'</div>';
        	                msg =msg1+msg2;

                            addMessage = 1;
                        }
                        if(incoming.hasOwnProperty('id') && incoming.hasOwnProperty('localmessageid') && $("#cometchat_message_"+incoming.localmessageid).length>0){
                            $("#cometchat_message_"+incoming.localmessageid).after(msg);
                            $("#cometchat_message_"+incoming.localmessageid).remove();
                            $("#cometchat_chatboxseen_"+incoming.localmessageid).remove();
                            var offlinemessages = jqcc.cometchat.getFromStorage('offlinemessagesqueue');
                            if(offlinemessages.hasOwnProperty(incoming.localmessageid)) {
                                delete offlinemessages[incoming.localmessageid];
                                jqcc.cometchat.updateToStorage('offlinemessagesqueue',offlinemessages);
                            }
                        }else if(addMessage==1&&chatboxopen==0){
                            $("#cometchat_tabcontenttext_"+incoming.from).append(msg);
                        }

                        if($("#cometchat_message_"+messagewrapperid).find(".cometchat_ts") != ''){
                           var msg_containerHeight = $("#cometchat_message_"+messagewrapperid+" .cometchat_ts_margin").outerHeight()-8;
                           var cometchat_ts_margin_right = $("#cometchat_message_"+messagewrapperid+" .cometchat_ts_margin").outerWidth(true)+5;
                           jqcc('#cometchat_message_'+messagewrapperid).find('.cometchat_ts').css('margin-top',msg_containerHeight);
                           jqcc('#cometchat_message_'+messagewrapperid).find('.cometchat_ts').css('margin-right',cometchat_ts_margin_right);
                        }

                        if($("#cometchat_message_"+messagewrapperid).find(".cometchat_ts_other") != ''){
                           var cometchat_ts_other_margin_left = $("#cometchat_message_"+messagewrapperid+" .cometchat_ts_margin").outerWidth(true)+30;
                           if($("#cometchat_message_"+messagewrapperid+" .cometchat_ts_margin").outerWidth() >= 135){
                                jqcc('#cometchat_message_'+messagewrapperid).find('.cometchat_ts_other');
                           }else if(cc_dir == 1){
                           jqcc('#cometchat_message_'+messagewrapperid).find('.cometchat_ts_other').css('margin-left',cometchat_ts_other_margin_left);
                             }
                        }
                        $("#cometchat_istyping_"+incoming.from).remove();

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
                            if(settings.windowTitleNotify==1 && disableLayout == 0){
                                document.title = language[15];
                            }
                        }
                        jqcc.docked.groupbyDate(incoming.from,jabber);

                        if($('#cometchat_user_'+incoming.from+'_popup .cometchat_prependMessages').length != 1){
                                $('#cometchat_tabcontenttext_'+incoming.from).prepend(prepend);
                        }

                        if(message.indexOf('<img')!=-1 && message.indexOf('src')!=-1){
                            $( "#cometchat_message_"+messagewrapperid+" img" ).load(function() {
                                jqcc.docked.scrollDown(incoming.from);
                                var cometchat_ts_margin_right = $("#cometchat_message_"+messagewrapperid+" .cometchat_ts_margin").outerWidth(true)+5;
                                jqcc('#cometchat_message_'+messagewrapperid).find('.cometchat_ts').css({'margin-right':cometchat_ts_margin_right});
                            });
                        }else{
                            jqcc.docked.scrollDown(incoming.from);
                        }
                    }

                    var newMessage = 0;
                    var isActiveChatBox = $('#cometchat_user_'+incoming.from+'_popup').find('textarea.cometchat_textarea').is(':focus');
                    /*Notification for AV Chat*/
                    if(message.indexOf('avchat_webaction=initiate')!=-1 || message.indexOf('avchat_webaction=acceptcall')!=-1){
                        message = jqcc.ccavchat.getLanguage('video_call');
                    }
                    if(message.indexOf('audiochat_webaction=initiate')!=-1 || message.indexOf('audiochat_webaction=acceptcall')!=-1){
                        message = jqcc.ccaudiochat.getLanguage('video_call');
                    }
                    /*Notification for AV Chat*/
                    if((jqcc.cometchat.getThemeVariable('isMini')==1||!isActiveChatBox)&&incoming.self!=1&&incoming.old==0&&settings.desktopNotifications==1){
                        var callChatboxEvent = function(){
                            if(typeof incoming.from!='undefined'){
                                for(x in desktopNotifications){
                                    for(y in desktopNotifications[x]){
                                        desktopNotifications[x][y].close();
                                    }
                                }
                                desktopNotifications = {};
                                if(jqcc.cometchat.getThemeVariable('isMini')==1){
                                    window.focus();
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
                    var totalHeight = 0;
                    $("#cometchat_tabcontenttext_"+incoming.from).children().each(function(){
                        totalHeight = totalHeight+$(this).outerHeight(false);
                    });
                    if(newMessage>0){
                        if($('#cometchat_tabcontenttext_'+incoming.from).outerHeight(false)<totalHeight){
                            $('#cometchat_tabcontenttext_'+incoming.from).append('<div class="cometchat_new_message_unread"><a herf="javascript:void(0)" onClick="javascript:jqcc.docked.scrollDown('+incoming.from+');jqcc(\'#cometchat_tabcontenttext_'+incoming.from+' .cometchat_new_message_unread\').remove();">&#9660 '+language[54]+'</a></div>');
                        }
                        if ((typeof $.cookie(settings.cookiePrefix+"sound") == 'undefined' || $.cookie(settings.cookiePrefix+"sound") == null) || $.cookie(settings.cookiePrefix + "sound") == 'true') {
                            jqcc.docked.playSound(0);
                        }
                    }
                    if(incoming.old != 1 && incoming.self != 1){
                        if((typeof $.cookie(settings.cookiePrefix+"sound") == 'undefined' || $.cookie(settings.cookiePrefix+"sound") == null) || $.cookie(settings.cookiePrefix+"sound")=='true'){
                            jqcc[settings.theme].playSound(1);
                        }
                    }
                    if(visibleTab.indexOf(incoming.from) == -1) {
                        var unreadUnseenCount = $('#cometchat_unseenUsers').find('.unread_msg').length;
                        if(unreadUnseenCount > 0) {
                            $('#cometchat_unseenUserCount').html(unreadUnseenCount).show();
                        } else {
                            $('#cometchat_unseenUserCount').hide();
                        }
                    }
                    jqcc.docked.updateReadMessages(incoming.from);
                    if(settings.cometserviceEnabled == 1 && settings.messagereceiptEnabled == 1 && jqcc.cometchat.getCcvariable().callbackfn != "mobilewebapp" && (settings.transport == 'cometservice' || settings.transport == 'cometserviceselfhosted')  && incoming.old == 0 && incoming.self == 1 && incoming.direction == 0){
                        jqcc.docked.sentMessageNotify(incoming);
                    }
                    if(settings.disableRecentTab == 0){
                        message = jqcc.cometchat.processRecentmessages(message);
                        var params = {'chatid':incoming.from,'isgroup':0,'timestamp':incoming.sent,'m':message,'msgid':messagewrapperid,'force':0,'del':0};
                        jqcc.cometchat.updateRecentChats(params);
                    }
                });
            },
            addPopup: function(id, amount){
                if (typeof(amount) == 'undefined') {
                    amount = 0;
                }
                if($('#cometchat_user_'+id+'_popup:visible').length != 0){
                    amount = 0;
                }
                amount = jqcc.cometchat.updateChatBoxState({id: parseInt(id), c: parseInt(amount)});
                var cometchat_user_id = jqcc("#cometchat_user_"+id+', #cometchat_recentlist_'+id+', #cometchat_userlist_'+id);

                if(amount > 0){
                    cometchat_user_id.addClass('cometchat_new_message').attr('amount', amount).find('div.cometchat_unreadCount').html(amount);
                    if ((typeof jqcc.cookie(settings.cookiePrefix+"sound") == 'undefined' || jqcc.cookie(settings.cookiePrefix+"sound") == null) || $.cookie(settings.cookiePrefix + "sound") == 'true') {
                        jqcc.docked.playSound(0);
                    }
                    cometchat_user_id.find('div.cometchat_unreadCount').show();
                }else{
                    cometchat_user_id.find('div.cometchat_unreadCount').hide();
                }
            },
            getTimeDisplay: function(ts){
                ts = parseInt(ts);
                var time = getTimeDisplay(ts);
                if((ts+"").length == 10){
                    ts = ts*1000;
                }
                var timeDataStart = time.hour+":"+time.minute+" "+time.ap;
                if(ts<jqcc.cometchat.getThemeVariable('todays12am')){
                    return timeDataStart+" "+time.date+time.type+" "+time.month;
                }else{
                    return timeDataStart;
                }
            },
            groupbyDate: function(id,j){
                if(j == '0' ){
                   $('#cometchat_user_'+id+'_popup .cometchat_time').hide();
                   $.each($('#cometchat_user_'+id+'_popup .cometchat_time'),function (i,divele){
                        var classes = $(divele).attr('class').split(/\s+/);
                        for(var i in classes){
                            if(typeof classes[i] == 'string') {
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
                            if(typeof classes[i] == 'string') {
                                if(classes[i].indexOf('cometchat_time_') === 0){
                                    $('#cometchat_tabcontenttext_'+id+' .'+classes[i]+':first').css('display','table');
                                }
                            }
                        }
                    });
                }
            },
            updateReadMessages: function(id){
                if($('#cometchat_user_'+id+'_popup:visible').find('.cometchat_chatboxmessage:not(.cometchat_self):last').length){
                    if(typeof (jqcc.cometchat.updateToStorage)!=='undefined'){
                        var alreadyreadmessages = jqcc.cometchat.getFromStorage('readmessages');
                        var lastid = parseInt($('#cometchat_user_'+id+'_popup').find('.cometchat_chatboxmessage[id]:not(.cometchat_self):last').attr('id').replace('cometchat_message_',''));
                        var messageboxid =  $('#cometchat_user_'+id+'_popup').find('.cometchat_chatboxmessage:not(.cometchat_self):last').attr('id');
                        if(typeof messageboxid != 'undefined' && messageboxid !=false){
                            if((typeof(alreadyreadmessages[id])!='undefined' && parseInt(alreadyreadmessages[id])<parseInt(lastid)) || typeof(alreadyreadmessages[id])=='undefined' || alreadyreadmessages[id]==null){
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
                    $('.cometchat_messagElement').removeClass("cometchat_lastseenmessage");
                    $('.cometchat_messagElement').addClass("cometchat_showOffline");
                    $('.cometchat_messagElement').slideDown(300);
                    $('.cometchat_messagElement').html(language['check_internet']);
                }else {
                    $('.cometchat_messagElement').removeClass("cometchat_showOffline");
                    $('.cometchat_messagElement').hide();
                    if(settings.lastseen == 1 && jqcc.cometchat.getThemeArray('buddylistLastseensetting',jqcc.cometchat.getUserID())==0){
                        $(".cometchat_messagElement").each(function(){
                            id = parseInt($(this).attr('id').replace('cometchat_messagElement_',''));
                            var buddylastseen = jqcc.cometchat.getThemeArray('buddylistLastseen', id);
                            var statusmsg = jqcc.cometchat.getThemeArray('buddylistStatus', id);
                            var lstsnSetting = jqcc.cometchat.getThemeArray('buddylistLastseensetting', id);
                            var currentts = Math.floor(new Date().getTime()/1000);
                            if(((statusmsg == 'away' || statusmsg == 'invisible' || statusmsg == 'busy' || statusmsg == 'offline') || currentts-buddylastseen > (60*10)) && lstsnSetting == '0'){
                                jqcc[settings.theme].showLastseen(id,jqcc.cometchat.getThemeArray('buddylistLastseen',id));
                                $('.cometchat_messagElement').addClass("cometchat_lastseenmessage");
                            }else{
                                $('#cometchat_messagElement_'+id).slideUp(300);
                                $('#cometchat_messagElement_'+id).html("");
                            }
                        })
                    }
                }
            },
            createUnseenUser: function(flag) {
                var detachElement = '';
                var typeid = '';
                if(flag == 1){
                    typeid = $('#cometchat_chatboxes').find('span.cometchat_tabopen_bottom:nth-child(2)').attr('id');
                }else{
                   typeid = $('#cometchat_chatboxes').find('span.cometchat_tabopen_bottom:first-child').attr('id');
                }

                if(typeof(typeid) == "undefined"){
                    return;
                }
                if(typeid.split('_')[1] == 'user'){
                    detachElement = $('#cometchat_chatboxes_wide').find('#'+typeid).detach();
                    id = typeid.split('_')[2];
                    chatboxOpened[id] = 0;
                }else if(typeid.split('_')[1] == 'group'){
                    detachElement =  $('#cometchat_chatboxes_wide').find('#'+typeid).detach();
                    id = typeid.split('_')[2];
                    var chatroomsOpened = jqcc.cometchat.getChatroomVars('chatroomsOpened');
                    chatroomsOpened[id] = 0;
                    jqcc.cometchat.setChatroomVars('chatroomsOpened',chatroomsOpened);
                }

                var bubble_align = ($('#cometchat_chatboxes_wide').outerWidth(true)+chatboxWidth+(3*chatboxDistance)) +'px';
                $('#cometchat_chatbox_left').css(jqcc.cometchat.getThemeVariable('dockedAlignment'),bubble_align);
                $('#cometchat_chatbox_left').show();

                var unseenUserHtml = '';
                $('#cometchat_unseenchatboxes').append(detachElement);
                $('#cometchat_unseenchatboxes').children().each(function(index){
                    var currentElem = $(this).attr('id');
                    var listid = '';
                    var unreadMsg = '';
                    var countVisible = '';
                    var amount = '';
                    var name = '';
                    if(currentElem.split('_')[1] == 'user'){
                        listid = $(this).attr('userid');
                        name = $(this).find('.cometchat_user_shortname').text();
                    }else if(currentElem.split('_')[1] == 'group'){
                        listid = $(this).attr('groupid');
                        name = $(this).find('div .cometchat_groupname').text();
                    }
                    unseenUserHtml += '<div id="'+currentElem+'" class="cometchat_unseenUserList"><div class="cometchat_unreadCount cometchat_floatL" '+countVisible+'>'+amount+'</div><div class="cometchat_userName cometchat_floatL">'+name+'</div><div class="cometchat_unseenClose cometchat_floatR" id="'+listid+'" uid="'+currentElem+'" ></div></div>';

                });
                if(unseenUserHtml == ''){
                    $('#cometchat_chatbox_left').find(".cometchat_unseenList_open").click();
                } else {
                    $('#cometchat_unseenUsers').html(unseenUserHtml);
                    $('#cometchat_chatbox_left').find(".cometchat_unseenList_open").click();
                }

                var count = $('#cometchat_unseenchatboxes').children().length;
                if(typeof(count) != "undefined"){
                    $('#cometchat_chatbox_left').find('.cometchat_tabtext').text(parseInt(count));
                    if(count == 0){
                        $('#cometchat_chatbox_left').hide();
                    }
                }
                $.docked.rearrange();
            },
            updateReceivedUnreadMessages: function(id,lastid){
                if(typeof (jqcc.cometchat.updateToStorage)!=='undefined'){
                    var alreadyreceivedmessages = jqcc.cometchat.getFromStorage('receivedunreadmessages');
                    if((typeof(alreadyreceivedmessages[id])!='undefined' && parseInt(alreadyreceivedmessages[id])<parseInt(lastid)) || typeof(alreadyreceivedmessages[id])=='undefined' || alreadyreceivedmessages[id]==null){
                        var receivedmessages={};
                        receivedmessages[id]= parseInt(lastid);
                        jqcc.cometchat.updateToStorage('receivedunreadmessages',receivedmessages);
                    }
                }
            },
            chatboxKeyup: function(event, chatboxtextarea, id){
                if(event.keyCode==27){
                    event.stopImmediatePropagation();
                    $(chatboxtextarea).val('');
                     $("#cometchat_user_"+id+"_popup").find('div.cometchat_tabtitle').click();
                }
                var adjustedHeight = chatboxtextarea.clientHeight;
                var maxHeight = 94;
                if(maxHeight>adjustedHeight){
                    adjustedHeight = Math.max(chatboxtextarea.scrollHeight, adjustedHeight);
                    if(maxHeight)
                        adjustedHeight = Math.min(maxHeight, adjustedHeight);
                }else{
                    $(chatboxtextarea).css('overflow-y', 'auto');
                }
            },
            chatboxKeydown: function(event, chatboxtextarea, id, force){
                var condition = 1;
                if((event.keyCode==13&&event.shiftKey==0)||force==1 && !$(chatboxtextarea).hasClass('cometchat_placeholder')){
                    var message = $(chatboxtextarea).val();
                    message = message.replace(/^\s+|\s+$/g, "");
                    $(chatboxtextarea).val('');
                    $(chatboxtextarea).css('overflow-y', 'hidden');
                    $(chatboxtextarea).focus();
                    if(settings.floodControl){
                        condition = ((Math.floor(new Date().getTime()))-lastmessagetime>2000);
                    }
                    if(settings.cometserviceEnabled == 1 && settings.istypingEnabled == 1 && settings.transport == 'cometservice'){
                        jqcc.cometchat.typingTo({id:id,method:'typingStop'});
                    }
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
            if(mobileDevice){
                 $('#cometchat_tabcontenttext_'+id).css('overflow-y','auto');
                 $('#cometchat_tabcontenttext_'+id).scrollTop(10000000);
            }else if(jqcc().slimScroll){
                    $('#cometchat_tabcontenttext_'+id).slimScroll({scroll: '1',railAlwaysVisible: true});
                }else{
                    setTimeout(function(){
                        $("#cometchat_tabcontenttext_"+id).scrollTop(50000);
                    }, 100);
                }
            },
            swapTab: function(typeid,mode) {
                if(typeof(typeid) != "undefined"){
                    var id;
                    $('#cometchat_unseenUsers').show();

                    if($('#cometchat_chatbox_left').hasClass('cometchat_unseenList_open')){
                        $('#cometchat_chatbox_left').removeClass('cometchat_unseenList_open');
                        $('#cometchat_chatbox_left').css('color','#333');
                    }

                    if(typeid.split('_')[1] == 'user'){
                        id = typeid.split('_')[2];
                        chatboxOpened[id] = 1;
                    }else if(typeid.split('_')[1] == 'group'){
                        id = typeid.split('_')[2];
                        var chatroomsOpened = jqcc.cometchat.getChatroomVars('chatroomsOpened');
                        chatroomsOpened[id] = 1;
                        jqcc.cometchat.setChatroomVars('chatroomsOpened',chatroomsOpened);
                    }
                    $('#cometchat_unseenUsers').find('#'+typeid).remove();
                    var appendElem = $('#cometchat_unseenchatboxes').find('#'+typeid).detach();
                    $('#cometchat_chatboxes_wide').prepend(appendElem);

                    if(mode){
                        $.docked.createUnseenUser(1);
                    }
                }
            },
            windowResize: function(silent){
                $.docked.scrollBars(silent);
                $.docked.closeTooltip();
                if(silent){
                    $.docked.rearrange();
                }
            },
            scrollBars: function(silent){
            },
            joinChatroom: function(roomid, inviteid, roomname){
                jqcc.cometchat.silentroom(roomid, inviteid, roomname, 0, 0);
            },
            closeTooltip: function(){
                $("#cometchat_tooltip").css('display', 'none');
            },
            popoutUnseenuser: function(flag){
                if(flag == 2){
                    $.each($('#cometchat_unseenUsers').children(),function (i,divele){
                        var widthavailable = (jqcc(window).width() - jqcc('#cometchat_chatboxes').outerWidth() - chatboxWidth - chatboxDistance);
                        if(widthavailable > (chatboxWidth+chatboxWidth)){
                            var popupid = $('#cometchat_unseenUsers').find('div.cometchat_unseenUserList:first-child').attr('id');
                            $.docked.swapTab(popupid,0);
                            $('#cometchat_chatboxes_wide').width($('#cometchat_chatboxes_wide').width()+chatboxWidth+chatboxDistance);
                        }
                    });
                }else{
                    if($('#cometchat_unseenUsers').children().length > 0){
                        var popupid = $('#cometchat_unseenUsers').find('div.cometchat_unseenUserList:first-child').attr('id');
                        $.docked.swapTab(popupid,0);
                    }
                }
                var count = $('#cometchat_unseenchatboxes').children().length;
                if(typeof(count) != "undefined"){
                $('#cometchat_chatbox_left').find('.cometchat_tabtext').text(parseInt(count));
                if(count == 0){
                        $('#cometchat_chatbox_left').hide();
                    }
                }
            },
            rearrange: function(force){
                var widthavailable = (jqcc(window).width() - jqcc('#cometchat_chatboxes').outerWidth() - chatboxWidth - chatboxDistance);
                if(force){
                    $.docked.createUnseenUser();
                }else{
                    if(widthavailable > (chatboxWidth+chatboxWidth)){
                        $.docked.popoutUnseenuser(2);
                        var bubble_align = ($('#cometchat_chatboxes_wide').outerWidth(true)+chatboxWidth+(3*chatboxDistance)) +'px';
                        $('#cometchat_chatbox_left').css(jqcc.cometchat.getThemeVariable('dockedAlignment'),bubble_align);
                    }else if(jqcc(window).width() <= $('#cometchat_chatboxes_wide').width()+chatboxWidth+chatboxWidth +chatboxDistance){
                        $('#cometchat_chatboxes_wide').width($('#cometchat_chatboxes_wide').width()-chatboxWidth-chatboxDistance);
                        $.docked.createUnseenUser();
                    }
                }
                var height = $('#cometchat_unseenUsers').height();
                    $('#cometchat_chatbox_left').find('#cometchat_unseenUsers').slimScroll({height:353});
                    var bottom = 374;
                    if($('#cometchat_unseenUsers').parent().hasClass('slimScrollDiv')){
                    if(settings.dockedAlignToLeft == 1){
                        $('#cometchat_unseenUsers').parent().css({'bottom':bottom,'width':'130px','left':'-7px'});
                    }else{
                        $('#cometchat_unseenUsers').parent().css({'bottom':bottom,'width':'130px','right':'97px'});
                    }
                     $('#cometchat_chatbox_left').find('#cometchat_unseenUsers').css('bottom','0');
                 }
            },

            loggedOut: function(){
                document.title = jqcc.cometchat.getThemeVariable('documentTitle');
                if(settings.ccauth.enabled=="1"){
                }else{
                    $("#loggedout").addClass("cometchat_optionsimages_exclamation");
                    $("#loggedout").attr("title",language[8]);
                }
                /* Changes for guest modal on chat.pcs START */
                    var controlparameters = {"type":"core", "name":"cometchat", "method":"customlogout", "params":{"to":"0"}};
                    controlparameters = JSON.stringify(controlparameters);
                    parent.postMessage('CC^CONTROL_'+controlparameters,'*');
                /* Changes for guest modal on chat.pcs END */
                $("#loggedout").show();
                $("#cometchat_hidden").css('display','block');
                msg_beep = $("#messageBeep").detach();
                side_bar = $("#cometchat_sidebar").detach();
                option_button = $("#cometchat_optionsbutton_popup").detach();
                user_tab = $("#cometchat_userstab_popup").detach();
                chat_boxes = $("#cometchat_chatboxes").detach();
                chat_left = $("#cometchat_chatbox_left").detach();
                unseen_users = $("#cometchat_unseenUsers").detach();
                usertab2 = $("#cometchat_userstab").detach();
                $("span.cometchat_tabclick").removeClass("cometchat_tabclick");
                $("div.cometchat_tabopen").removeClass("cometchat_tabopen");
                jqcc.cometchat.setThemeVariable('loggedout', 1);
                $.cookie(settings.cookiePrefix+"loggedin", null, {path: '/'});
                $.cookie(settings.cookiePrefix+"state", null, {path: '/'});
                if($.cookie(settings.cookiePrefix+"crstate")){
                    $.cookie(settings.cookiePrefix+"crstate", null, {path: '/'});
                }
            },
            countMessage: function(){
                return;
                /*if(jqcc.cometchat.getThemeVariable('loggedout')==0){
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
                }*/
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
            reinitialize: function(){
                if(jqcc.cometchat.getThemeVariable('loggedout')==1){
                    $('#loggedout').removeClass('cometchat_optionsimages_exclamation');
                    $('#loggedout').removeClass('cometchat_optionsimages_ccauth');
                    $('#loggedout').removeClass('cometchat_tabclick');
                    $('#loggedout').hide();
                    $("body").append(msg_beep);
                    $("#cometchat_base").append(side_bar);
                    $("#cometchat_base").append(option_button);
                    $("#cometchat_base").append(usertab2);
                    $("#cometchat_base").append(user_tab);
                    $("#cometchat_base").append(chat_boxes);
                    $("#cometchat_base").append(chat_left);
                    $("#cometchat_base").append(unseen_users);
                    $("#cometchat_optionsbutton,#cometchat_sidebar").show();
                    $("#cometchat_userstab").addClass('cometchat_userstabclick');
                    $("#cometchat_userstab").show();
                    jqcc.cometchat.setThemeVariable('loggedout', 0);
                    jqcc.cometchat.setExternalVariable('initialize', '1');
                    jqcc.cometchat.chatHeartbeat();
                    $('#cometchat_optionsbutton.cometchat_tabclick').click();
                }
            },
            minimizeAll: function(){
                $("div.cometchat_tabpopup").each(function(index){
                    if($(this).hasClass('cometchat_tabopen')){
                        $(this).find('div.cometchat_tabtitle').click();
                    }
                });
                $("#cometchat_minimize_userstabpopup").click();
            },
            prependMessagesInit: function(id){
                var messages = jqcc('#cometchat_tabcontenttext_'+id).find('.cometchat_chatboxmessage');
                $('#cometchat_prependMessages_'+id).text(language[41]);
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
                var messageid = [];
                var cc_dir = '<?php if ($rtl == 1) { echo 1; } else { echo 0; }?>';
                var prepend = '';

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

                $.each(data, function(type, item){
                    if(type=="messages"){
                        $.each(item, function(i, incoming){
                            count = count+1;
                            var messagewrapperid = '';
                            incoming.message = jqcc.cometchat.htmlEntities(incoming.message);
                            var message = jqcc.cometchat.processcontrolmessage(incoming);

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
                            if(incoming.sent!=null){
                                var ts = incoming.sent;
                                sentdata = jqcc[settings.theme].getTimeDisplay(ts);
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

                            var msg = '';
                            var sentdata = '';
                            var add_bg = '';
                            var add_arrow_class = '';
                            var add_style = "";
                            var smileycount = (message.match(/cometchat_smiley/g) || []).length;
                            var smileymsg = message.replace(/<img[^>]*>/g,"");
                            smileymsg = smileymsg.trim();
                            var single_smiley_avatar = '';

                            if(smileycount == 1 && smileymsg == '') {
                                message = message.replace('height="20"', 'height="64px"');
                                message = message.replace('width="20"', 'width="64px"');
                                single_smiley_avatar = "margin-top:10px";
                            }

                            var avatar = staticCDNUrl+"layouts/docked/images/noavatar.png";
                            if(jqcc.cometchat.getThemeArray('buddylistAvatar', incoming.from)!=""){
                                avatar = jqcc.cometchat.getThemeArray('buddylistAvatar', incoming.from);
                            }
                            fromname = jqcc.cometchat.getThemeArray('buddylistName', incoming.from);
                            selfstyleAvatar = '<a class="cometchat_floatL" href="'+jqcc.cometchat.getThemeArray('buddylistLink', incoming.from)+'"><img class="ccmsg_avatar" style="'+single_smiley_avatar+'" src="'+avatar+'" title="'+fromname+'"/></a>';

                            if(incoming.sent!=null){
                                var ts = incoming.sent;
                                sentdata = jqcc.docked.getTimeDisplay(ts);
                            }

                            if(parseInt(incoming.self)==1){
                                if((message.indexOf('<img')==-1 && message.indexOf('src')==-1) || (smileycount > 0 && smileymsg != '')){
                                    add_bg = 'cometchat_chatboxmessagecontent cometchat_self';
                                    add_arrow_class = '<div class="selfMsgArrow"><div class="after"></div></div>';
                                }else{
                                    if(message.indexOf('cometchat_smiley')!=-1) {
                                        add_style = "margin-right:13px";
                                    }else{
                                        add_style = "margin-right:4px;margin-left:4px;";
                                    }
                                }
                                var sentdata_box = "<span class=\"cometchat_ts\">"+sentdata+"</span>";
                                msg = '<div class="cometchat_time cometchat_time_'+msg_date_class+' '+date_class+'" msg_format="'+msg_date_format+'">'+msg_date+'</div><div class="cometchat_chatboxmessage" id="cometchat_message_'+messagewrapperid+'"><div class="'+add_bg+' '+'cometchat_ts_margin cometchat_self_msg cometchat_floatR" style="'+add_style+'" title="'+sentdata+'"><span id="message_'+messagewrapperid+'">'+message+'</span><span id="cometchat_chatboxseen_'+messagewrapperid+'"></span></div>'+add_arrow_class+' '+sentdata_box+'</div>';
                                addMessage = 1;

                            }else{
                                if(message.indexOf('cometchat_hw_lang')!=-1){
                                  var hw_ts = 'margin-left: 4px';
                              }
                                var sentdata_box = "<span class=\"cometchat_ts_other\" style='"+hw_ts+"'>"+sentdata+"</span>";

                                if((message.indexOf('<img')==-1 && message.indexOf('src')==-1) || (smileycount > 0 && smileymsg != '')){
                                    add_bg = 'cometchat_chatboxmessagecontent';
                                    add_arrow_class = '<div class="msgArrow"><div class="after"></div></div>';
                                }else{
                                    if(message.indexOf('cometchat_smiley')!=-1) {
                                        if(smileycount == 1 && smileymsg == ''){
                                            add_style = "margin:-4px 0px 0px 4px";
                                        }else{
                                            add_style = "margin:5px 5px 0px 8px";
                                        }
                                    }else if(message.indexOf('cometchat_hw_lang')!=-1){
                                        add_style = "margin:0px 0px 0px 8px";
                                    }else{
                                        add_style = "margin:-6px 0px 0px 8px";
                                    }
                                }
                                msg = '<div class="cometchat_time cometchat_time_'+msg_date_class+' '+date_class+'" msg_format="'+msg_date_format+'">'+msg_date+'</div><div class="cometchat_chatboxmessage" id="cometchat_message_'+messagewrapperid+'">'+selfstyleAvatar+'<div class="'+add_bg+' '+'cometchat_ts_margin cometchat_floatL" style="'+add_style+'" title="'+sentdata+'"><span id="message_'+messagewrapperid+'" class="cometchat_msg">'+message+'</span></div>'+add_arrow_class+' '+sentdata_box+'</div>';
                                addMessage = 1;
                            }
                            oldMessages+=msg;
                        });

                    }
                });

                var current_top_element  = jqcc('#cometchat_tabcontenttext_'+id+' .cometchat_chatboxmessage:first');
                jqcc('#cometchat_user_'+id+'_popup').find('.cometchat_tabcontenttext').prepend(oldMessages);

                if(mobileDevice){
                    $('#cometchat_tabcontenttext_'+id).css('overflow-y','auto');
                }else{
                    var offsetheight = 0;
                    if(current_top_element.length>0){
                        var offsetheight = current_top_element.offset().top - jqcc('#cometchat_tabcontenttext_'+id+' .cometchat_chatboxmessage:first').offset().top+jqcc('.cometchat_time').height()+jqcc('#cometchat_prependMessages_'+id).height()+100;
                        var height = offsetheight-jqcc('#cometchat_tabcontenttext_'+id).height();
                        $('#cometchat_tabcontenttext_'+id).slimScroll({scrollTo: height+'px'});
                    }else{
                        $('#cometchat_tabcontenttext_'+id).slimScroll({scroll: 1});
                    }
                }

                $.each($('#cometchat_user_'+id+'_popup .cometchat_chatboxmessage'),function (i,divele){
                    if($(this).find(".cometchat_ts") != ''){
                       var msg_containerHeight = $(this).find(".cometchat_ts_margin").outerHeight()-8;
                       var cometchat_ts_margin_right = $(this).find(".cometchat_ts_margin").outerWidth(true)+5;
                       jqcc(this).find('.cometchat_ts').css('margin-top',msg_containerHeight);
                       jqcc(this).find('.cometchat_ts').css('margin-right',cometchat_ts_margin_right);
                   }
               });
                if(cc_dir == 1){
                    $('#cometchat_user_'+id+'_popup').find('.cometchat_ts_other').each(function(){
                        var cometchat_ts_other_margin_left = $(this).parent().find('.cometchat_ts_margin').outerWidth(true)+30;
                        $(this).css('margin-left',cometchat_ts_other_margin_left);
                    });
                }
                jqcc[settings.theme].groupbyDate(id,jabber);

                if(jqcc.cometchat.getThemeVariable('prependLimit') != '0'){
                    prepend = '<div class=\"cometchat_prependMessages\" onclick\="jqcc.docked.prependMessagesInit('+id+')\" id = \"cometchat_prependMessages_'+id+'\">'+language[83]+'</div>';
                }
                $('#cometchat_user_'+id+'_popup .cometchat_prependMessages').remove();
                var prependLimit = jqcc.cometchat.getThemeVariable('prependLimit');
                var message_count = count - parseInt(jqcc.cometchat.getThemeVariable('prependLimit'));

                if($('#cometchat_user_'+id+'_popup .cometchat_prependMessages').length != 1){
                        $('#cometchat_tabcontenttext_'+id).prepend(prepend);
                }

                if(parseInt(message_count) < 0){
                    $('#cometchat_prependMessages_'+id).text(language[84]);
                    jqcc('#cometchat_prependMessages_'+id).attr('onclick','');
                    jqcc('#cometchat_prependMessages_'+id).css('cursor','default');
                }
            },
            messageBeep: function(baseUrl){
                if(!$('#messageBeep').length){
                    $('<audio id="messageBeep" style="display:none;"><source src="'+baseUrl+'sounds/beep.mp3" type="audio/mpeg"><source src="'+baseUrl+'sounds/beep.ogg" type="audio/ogg"><source src="'+baseUrl+'sounds/beep.wav" type="audio/wav"></audio>').appendTo($("body"));
                }
                if(!$('#messageOpenBeep').length){
                    $('<audio id="messageOpenBeep" style="display:none;"><source src="'+baseUrl+'sounds/openbeep.mp3" type="audio/mpeg"><source src="'+baseUrl+'sounds/openbeep.ogg" type="audio/ogg"><source src="'+baseUrl+'sounds/openbeep.wav" type="audio/wav"></audio>').appendTo($("body"));
                }
                if(!$('#announcementBeep').length){
                    $('<audio id="announcementBeep" style="display:none;"><source src="'+baseUrl+'sounds/announcementbeep.mp3" type="audio/mpeg"><source src="'+baseUrl+'sounds/announcementbeep.ogg" type="audio/ogg"><source src="'+baseUrl+'sounds/announcementbeep.wav" type="audio/wav"></audio>').appendTo($("body"));
                }
                if(!$('#incommingcall').length){
                    $('<audio id="incommingcall" style="display:none;"><source src="'+baseUrl+'sounds/incomingcallringtone.mp3" type="audio/mpeg"><source src="'+baseUrl+'sounds/incomingcallringtone.ogg" type="audio/ogg"><source src="'+baseUrl+'sounds/incomingcallringtone.wav" type="audio/wav"></audio>').appendTo($("body"));
                }
                if(!$('#outgoingcall').length){
                    $('<audio id="outgoingcall" style="display:none;"><source src="'+baseUrl+'sounds/outgoingcallringtone.mp3" type="audio/mpeg"><source src="'+baseUrl+'sounds/outgoingcallringtone.ogg" type="audio/ogg"><source src="'+baseUrl+'sounds/outgoingcallringtone.wav" type="audio/wav"></audio>').appendTo($("body"));
                }
            },
            typingTo: function(item){
                if(typeof item['fromid'] != 'undefined'){

                    var id = item['fromid'];

                    $("#cometchat_typing_"+id).css('display', 'block');
                    $("#cometchat_buddylist_typing_"+id).css('display', 'block');

                   fromname = jqcc.cometchat.getThemeArray('buddylistName', id);
                    if(jqcc.cometchat.getThemeArray('buddylistAvatar', id)!=""){
                        avatar = jqcc.cometchat.getThemeArray('buddylistAvatar', id);
                    }
                    selfstyleAvatar = '<a class="cometchat_floatL" href="'+jqcc.cometchat.getThemeArray('buddylistLink', id)+'"><img class="ccmsg_avatar" src="'+avatar+'" title="'+fromname+'"/></a>';


                    var notify_typing = '<div class="cometchat_typingbox"><div class="typing_dots"></div><div class="typing_dots"></div><div class="typing_dots"></div></div>';

                    if($("#cometchat_istyping_"+id).length == 0){
                        msg = '<div class="cometchat_chatboxmessage_typing" id="cometchat_istyping_'+id+'">'+selfstyleAvatar+'<div class="cometchat_chatboxmessagecontent cometchat_floatL"><span class="cometchat_msg">'+notify_typing+'</span></div><div class="msgArrow"><div class="after"></div></div></div>';
                        $("#cometchat_tabcontenttext_"+id).append(msg);
                        jqcc.docked.scrollDown(id);

                    }

                    typingReceiverFlag[id] = item['typingtime'];
                }

               if(typeof typingRecieverTimer == 'undefined' || typingRecieverTimer == null || typingRecieverTimer == ''){

                    typingRecieverTimer = setTimeout(function(){
                        typingRecieverTimer = '';
                        var counter = 0;
                        $.each(typingReceiverFlag, function(typingid,typingtime){
                            if(((parseInt(new Date().getTime()))+jqcc.cometchat.getThemeVariable('timedifference')) - typingtime > 5000){
                                $("#cometchat_typing_"+typingid).css('display', 'none');
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
                    if (typeof item[key] == 'object'){
                        jqcc[settings.theme].sentMessageNotify(item[key]);
                    }
                }
                if(typeof item['id'] != 'undefined' && $("#cometchat_chatboxseen_"+item['id']).prev().find('.cometchat_chatboxmessagecontent').hasClass('cometchat_self')){
                    $("#cometchat_chatboxseen_"+item['id']).addClass('cometchat_sentnotification');
                }
            },
            deliveredMessageNotify: function(item){
                if($("#cometchat_message_"+item['message']).length == 0){
                    undeliveredmessages.push(item['message']);
                } else if(typeof item['fromid'] != 'undefined' && $("#cometchat_chatboxseen_"+item['message']).prev().find('.cometchat_chatboxmessagecontent').hasClass('cometchat_self')){
                    $("#cometchat_chatboxseen_"+item['message']).addClass('cometchat_deliverednotification');
                }
            },
            readMessageNotify: function(item){
                if(jqcc.cometchat.checkReadReceiptSetting(item.fromid) == 1){
                    if($("#cometchat_message_"+item['fromid']).length == 0 ){
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
                    } else if(typeof item['fromid'] != 'undefined' && $("#cometchat_chatboxseen_"+item['message']).prev().find('.cometchat_chatboxmessagecontent').hasClass('cometchat_self')){
                        $("#cometchat_chatboxseen_"+item['message']).addClass('cometchat_readnotification');
                    }
                }
            },
            updateSettings: function(){
                var guestname = '';
                var statusmessage = '';
                var status = '';
                var lastseensetting = 0;
                var readreceiptsetting = 0;
                var optionspopup = $('#cometchat_optionsbutton_popup');

                if(optionspopup.find('.cometchat_guestname').length){
                    guestname = optionspopup.find('.cometchat_guestname').val();
                }
                if(optionspopup.find('#cometchat_statusmessageinput').length){
                    statusmessage = optionspopup.find('#cometchat_statusmessageinput > textarea').val();
                }
                if(optionspopup.find('.cometchat_statusinputs').length){
                    status = optionspopup.find('input[name=cometchat_statusoptions]:checked', '#cometchat_optionsform').val();
                }
                jqcc.cometchat.updateSettings(guestname, statusmessage, status, lastseensetting, readreceiptsetting);

                /*this needs to be done in success of jqcc.cometchat.updateSettings*/
                if(status == 'away'){
                    jqcc.cometchat.setThemeVariable('currentStatus', status);
                    jqcc.cometchat.setThemeVariable('idleFlag', 1);
                } else {
                    jqcc.cometchat.setThemeVariable('idleFlag', 0);
                }
                /*end*/
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
            openMainContainer: function(){
                if(!($('#cometchat_userstab').hasClass("cometchat_tabclick"))){
                    jqcc('#cometchat').find('#cometchat_userstab_popup').removeClass('cometchat_tabhidden').addClass('cometchat_tabopen');
                }
            },
            openChatTab: function(openedtab, restored){
                if(typeof(openedtab) != "undefined") {
                    if(typeof(openedtab) == "undefined"){
                        var restored = 0;
                    }
                    jqcc[settings.theme].loadChatTab(parseInt(openedtab), restored);
                }
            },
            scrollToTop: function(){
                $("html,body").animate({scrollTop: 0}, {"duration": "slow"});
            },
            applyChatBoxStates: function(statestoapply){
                jqcc.each(statestoapply, function(i, state){
                    var id = state.id;
                    var silent = 0;
                    var count = 0;
                    if (state.hasOwnProperty('s')) {
                        silent = state.s;
                    }
                    if (state.hasOwnProperty('c')) {
                        count = state.c;
                    }
                    if(state.hasOwnProperty('g')&& state.g==1){
                        if(typeof(jqcc.cometchat.silentroom)=='function'){
                            if(silent>0){
                                jqcc.cometchat.silentroom(id, '', '', silent, count)
                            }else if(count>0){
                                jqcc.crdocked.addMessageCounter(id);
                            }
                        }
                    } else {
                        if(typeof(jqcc.docked.createChatbox)!=='undefined'){
                            if(silent>0){
                                jqcc[settings.theme].createChatbox(
                                    id,
                                    jqcc.cometchat.getThemeArray('buddylistName', id),
                                    jqcc.cometchat.getThemeArray('buddylistStatus', id),
                                    jqcc.cometchat.getThemeArray('buddylistMessage', id),
                                    jqcc.cometchat.getThemeArray('buddylistAvatar', id),
                                    jqcc.cometchat.getThemeArray('buddylistLink', id),
                                    jqcc.cometchat.getThemeArray('buddylistIsDevice', id),
                                    silent,
                                    count,
                                    1
                                );
                            } else if(count>0){
                                jqcc.docked.addPopup(id);
                            }
                        }
                    }
                });
            },
            authLogout: function(){
                jqcc.cometchat.sociallogout();
            },
            authLogin: function(){
                var guestlogin = '';
                if(typeof(guestsMode) != 'undefined' && guestsMode == 1){
                    guestlogin = '<div id="guest_login" onclick="jqcc.docked.guestLogin();" class="auth_options" style="background: darkgray;margin-bottom: -11px;"><img  src="'+jqcc.cometchat.getBaseUrl()+'images/guestavatar.png" style="width: 21px;"><span>Guest Login</span></div>';
                }
                var ccauthpopup = '<div id="cometchat_auth_popup" class="cometchat_tabpopup" style="display:none">'+
                                    '<div class="cometchat_socialuserstabtitle">'+
                                        '<div class="cometchat_userstabtitletext">'+language[51]+'</div>'+
                                        '<div class="cometchat_minimizebox cometchat_tooltip" id="cometchat_minimize_auth_popup" title="'+language[27]+'"></div>'+
                                        '<br clear="all"/>'+
                                        '</div><div class="cometchat_tabcontent cometchat_optionstyle">'+guestlogin+
                                        '<div id="social_login">'+
                                            '<iframe width="100%" height="100%"  allowtransparency="true" frameborder="0"  scrolling="no"  src="'+jqcc.cometchat.getBaseUrl()+'functions/login/" />';
                                        '</div>'+
                                    '</div>'+
                                '</div>';
                return '<div id="cometchat"></div><div id="cometchat_optionsimages_ccauth"><span id="cometchat_ccauth_text">'+language[51]+'</span></div>'+ccauthpopup;
            },
            guestLogin: function(){
                document.cookie = jqcc.cometchat.getSettings().cookiePrefix+"guest_login=true;path=/";
                jqcc.cometchat.reinitialize();
            },
            generateOutgoingAvchatData: function(id,grp,calltype){
                var userdata = {
                    name: jqcc.cometchat.getThemeArray('buddylistName', id),
                    avatar: jqcc.cometchat.getThemeArray('buddylistAvatar', id)
                }
                if(((typeof(parent) != 'undefined' && parent != null && parent != self) || window.top != window.self) && jqcc.cometchat.getCcvariable().callbackfn!='desktop'){
                    try{
                        parent.outgoingCall(id,grp,userdata,calltype);
                    }catch(e){
                        var controlparameters = {
                            type: "core",
                            name: "libraries",
                            method: "outgoingCall",
                            params: {
                                id: id,
                                grp: grp,
                                userdata: userdata,
                                calltype: calltype
                            }
                        }
                        var messagetopost = "CC^CONTROL_"+ JSON.stringify(controlparameters);
                        parent.postMessage(messagetopost,'*');
                    };
                } else {
                    outgoingCall(id,grp,userdata,calltype);
                }
            },
            generateIncomingAvchatData: function(incoming,avchat_data,currenttime){
                if(disableLayout == 1){return;}
                var userdata = {
                    name: jqcc.cometchat.getThemeArray('buddylistName', incoming.from),
                    avatar: jqcc.cometchat.getThemeArray('buddylistAvatar', incoming.from)
                }
                if(((typeof(parent) != 'undefined' && parent != null && parent != self) || window.top != window.self) && jqcc.cometchat.getCcvariable().callbackfn!='desktop'){
                    try{
                        parent.incomingCall(incoming,avchat_data,currenttime,userdata);
                    }catch(e){
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
                        parent.postMessage(messagetopost,'*');
                    };
                }else{
                    incomingCall(incoming,avchat_data,currenttime,userdata);
                }
            },
            removeAVchatContainer: function(id) {
                if(((typeof(parent) != 'undefined' && parent != null && parent != self) || window.top != window.self) && jqcc.cometchat.getCcvariable().callbackfn!='desktop'){
                    parent.removeCallContainer(id);
                }else{
                    removeCallContainer(id);
                }
            },
            disableLayout: function(){
                jqcc('#cometchat').hide();
                jqcc('#incommingcall, #outgoingcall, #announcementBeep ,#messageOpenBeep ,#messageBeep').remove();
                clearTimeout(resynchTimer);
                disableLayout =1;
            },
        };
    })();
})(jqcc);

if(typeof(jqcc.docked) === "undefined"){
    jqcc.docked=function(){};
}

jqcc.extend(jqcc.docked, jqcc.ccdocked);

jqcc(window).resize(function(){
    jqcc.docked.windowResize(1);
});

/* code for Cloud Mobileapp compatibilty to Hide CometChat bar. */
jqcc(document).ready(function() {
    var platform = jqcc.cookie('cc_platform_cod');
    if(platform == 'android' || platform == 'ios' || platform == 'dm') {
        var hideInterval = setInterval(function(){
            if(jqcc('.cometchat_ccmobiletab_redirect').length>0||jqcc('#cometchat').length>0){
                jqcc('#cometchat').hide();
                jqcc('.cometchat_ccmobiletab_redirect').hide();
                clearTimeout(hideInterval);
            }
        },500);
    }
});

/* for IE8 */
if(!Array.prototype.indexOf){
    Array.prototype.indexOf = function(obj, start){
        for(var i = (start||0), j = this.length; i<j; i++){
            if(this[i]===obj){
                return i;
            }
        }
        return -1;
    }
}

if(!Array.prototype.forEach){
    Array.prototype.forEach = function(fun)
    {
        var len = this.length;
        if(typeof fun!="function")
            throw new TypeError();
        var thisp = arguments[1];
        for(var i = 0; i<len; i++)
        {
            if(i in this)
                fun.call(thisp, this[i], i, this);
        }
    };
}
