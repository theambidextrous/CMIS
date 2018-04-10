<?php
 include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'config.php');
 ?>

/*
 * CometChat
 * Copyright (c) 2016 Inscripts - support@cometchat.com | http://www.cometchat.com | http://www.inscripts.com
 */

(function($){
    $.ccmobiletab = (function(){
        var mobileappdetails = {};
        var settings = {};
        var title = "<?php echo $language[51]; ?>",
        logintext = "<?php echo $language[82]; ?>",
        mobileNewWindow = "<?php echo $mobileNewWindow; ?>",
        timestamp = 0,
        amount = 0,
        cookiePrefix = '<?php echo $cookiePrefix; ?>',
        mobileDevice = navigator.userAgent.match(/ipad|ipod|iphone|android|windows ce|Windows Phone|blackberry|palm|symbian/i);
        var setalert = parseInt('<?php echo $mobiletabConfirmOnAllMessages; ?>');
        return{
            initialize: function(){
                settings = jqcc.cometchat.getSettings();
                mobileappdetails = jqcc.cometchat.getMobileappdetails();
                if(mobileDevice||location.href.match('extensions/mobiletab/')){
                    $('#cometchat').css('display', 'none');
                    if($('.cometchat_ccmobiletab_redirect').length<=0){
                        $('body').append('<div class="cometchat_ccmobiletab_redirect tri-right btm-left-in"></div>');
                    }
                    jqcc.ccmobiletab.tabalertScale();
                    $(".cometchat_ccmobiletab_redirect ,.cometchat_ccmobiletab_tabalert").live('click', function(){
                        amount = 0;
                        $(".cometchat_ccmobiletab_redirect .cometchat_ccmobiletab_tabalert").remove();
                        url = jqcc.cometchat.getBaseUrl()+'cometchat_embedded.php?cookiePrefix='+cookiePrefix+'&basedata='+jqcc.cometchat.getBaseData()+'&ccmobileauth='+jqcc.cometchat.getThemeVariable('ccmobileauth');
                        jqcc.ccmobiletab.openWebapp(url);
                    });
                }

                $(".cometchat_embed_chatroom_container").each(function(i,j) {
                    var src = $(this).attr('iframe_src');
                    var queryStringSeparator='&';
                    if(src.indexOf('?')<0){
                        queryStringSeparator='?';
                    }
                    src+= queryStringSeparator+"basedata="+jqcc.cometchat.getBaseData();
                    var width = $(this).attr('iframe_width'),
                    height = $(this).attr('iframe_height'),
                    name = $(this).attr('iframe_name'),
                    frameborder = $(this).attr('iframe_frameborder'),
                    class_name = $(this).attr('iframe_class');
                    $('.cometchat_embed_chatroom_container').html("<iframe class = "+class_name+" src = "+src+" width = "+width+" height = "+height+" name = "+name+" frameborder = "+frameborder+" ></iframe>");
                });
            },
            reinitialize: function(){
                if(mobileDevice||location.href.match('extensions/mobiletab/')){
                    $(".cometchat_ccmobiletab_redirect ,.cometchat_ccmobiletab_tabalert").die('click');
                    $(".cometchat_ccmobiletab_redirect, #mobile_social_login, .cc_overlay, .cometchat_ccmobiletab_tabalert").remove();
                    jqcc.ccmobiletab.initialize();
                    jqcc.cometchat.chatHeartbeat();
                }
            },
            tabalertScale: function(){
                var w=window,d=document,e=d.documentElement,g=d.getElementsByTagName('body')[0],winWidth=w.innerWidth||e.clientWidth||g.clientWidth,winHt=w.innerHeight||e.clientHeight||g.clientHeight;
                var cctabfontht = (winHt*0.04);
                if(winWidth>winHt){
                    cctabfontht = (winHt*0.06);
                }
                if(cctabfontht>35){
                    cctabfontht = 30;
                }
                $('.cometchat_ccmobiletab_redirect').css('font-size',cctabfontht+"px");
                if(mobileDevice){
                    var w=window,d=document,e=d.documentElement,g=d.getElementsByTagName('body')[0],winWidth=w.innerWidth||e.clientWidth||g.clientWidth,winHt=w.innerHeight||e.clientHeight||g.clientHeight;

                    if(winWidth<winHt){
                        $('#mobile_social_login').css('top','36%');
                        if(winWidth>800){
                            winWidth = 800;
                        }
                        $('#mobile_social_login .login_container').css({'width':(winWidth*0.6)+'px'});
                        $('#mobile_social_login img').css({'width':(winWidth*0.6)+'px',height:'auto',left:'10%'});
                    }else{
                        $('#mobile_social_login').css('top','20%');
                        if(winHt>450){
                            winHt = 450;
                        }
                        $('#mobile_social_login .login_container').css({'width':(winHt*0.8)+'px'});
                        $('#mobile_social_login img').css({'height':(winHt*0.6/4)+'px',width:'auto',left:'10%'});
                    }

                if(winWidth<=200){
                        $(".cometchat_ccmobiletab_redirect .cometchat_ccmobiletab_tabalert").css('right', '17%');
                    }else if(winWidth>200&&winWidth<=300){
                        $(".cometchat_ccmobiletab_redirect .cometchat_ccmobiletab_tabalert").css('right', '25%');
                    }else if(winWidth>300&&winWidth<=400){
                        $(".cometchat_ccmobiletab_redirect .cometchat_ccmobiletab_tabalert").css('right', '30%');
                    }else if(winWidth>400&&winWidth<=600){
                        $(".cometchat_ccmobiletab_redirect .cometchat_ccmobiletab_tabalert").css('right', '33%');
                    }else if(winWidth>600&&winWidth<=1000){
                        $(".cometchat_ccmobiletab_redirect .cometchat_ccmobiletab_tabalert").css('right', '24%');
                    }else if(winWidth>1000){
                        $(".cometchat_ccmobiletab_redirect .cometchat_ccmobiletab_tabalert").css('right', '28%');
                    }
                }
            },
            notify: function(totmsg,toid){
                if(mobileDevice){
                    if(typeof (totmsg)!="undefined"){
                        amount = totmsg;
                    }
                    if(amount===0){
                        $(".cometchat_ccmobiletab_redirect .cometchat_ccmobiletab_tabalert").remove();
                    }else{
                        if(setalert==1||setalert==2){
                            jqcc.cookie(cookiePrefix+"confirmOnMsg", null, {path: '/', expires: -1});
                        }
                        if(jqcc.cookie(cookiePrefix+"confirmOnMsg")!=1&&setalert!=2){
                        if( typeof(jqcc.cookie("cc_mobilewebapp_open"))=="undefined" || parseInt(jqcc.cookie("cc_mobilewebapp_open")) != 1 ){
                            if(confirm("<?php echo $language[52]; ?>")){
                                amount = 0;
                                $(".cometchat_ccmobiletab_redirect .cometchat_ccmobiletab_tabalert").remove();
                                url = jqcc.cometchat.getBaseUrl()+'cometchat_embedded.php?cookiePrefix='+cookiePrefix+'&basedata='+jqcc.cometchat.getBaseData()+'&ccmobileauth='+jqcc.cometchat.getThemeVariable('ccmobileauth')+'#user-'+toid;
                                jqcc.ccmobiletab.openWebapp(url);
                            }else{
                                if($(".cometchat_ccmobiletab_redirect .cometchat_ccmobiletab_tabalert").length>0){
                                    $(".cometchat_ccmobiletab_redirect .cometchat_ccmobiletab_tabalert").html(amount);
                                }else{
                                    $("<span/>").addClass("cometchat_ccmobiletab_tabalert ccbadge-danger ccbadge").html(amount).appendTo($('.cometchat_ccmobiletab_redirect'));
                                }
                                jqcc.ccmobiletab.tabalertScale();
                            }
                            }else{
                                if($(".cometchat_ccmobiletab_redirect .cometchat_ccmobiletab_tabalert").length>0){
                                    $(".cometchat_ccmobiletab_redirect .cometchat_ccmobiletab_tabalert").html(amount);
                                }else{
                                    $("<span/>").addClass("cometchat_ccmobiletab_tabalert ccbadge-danger ccbadge").html(amount).appendTo($('.cometchat_ccmobiletab_redirect'));
                                }
                                jqcc.ccmobiletab.tabalertScale();
                            }
                            if(setalert==0){
                                jqcc.cookie(cookiePrefix+"confirmOnMsg", "1", {path: '/', expires: 365});
                            }
                        }else{
                            if($(".cometchat_ccmobiletab_redirect .cometchat_ccmobiletab_tabalert").length>0){
                                $(".cometchat_ccmobiletab_redirect .cometchat_ccmobiletab_tabalert").html(amount);
                            }else{
                                $("<span/>").addClass("cometchat_ccmobiletab_tabalert ccbadge-danger ccbadge").html(amount).appendTo($('.cometchat_ccmobiletab_redirect'));
                            }
                            jqcc.ccmobiletab.tabalertScale();
                        }
                    }
                }
            },
            buddyList: function(data){
                if(mobileDevice){
                    var buddyCount = 0;
                    $.each(data, function(index, user){
                        if(user.s!='offline'){
                            buddyCount++;
                        }
                    });
                    $("#ccmobiletab_buddycount").html(buddyCount);
                }
            },
            addMessages: function(data){
                if(mobileDevice){
                    $.each(data, function(i, incoming){
                        if(typeof(incoming.self) ==='undefined' && typeof(incoming.old) ==='undefined' && typeof(incoming.sent) ==='undefined'){
                            incoming.sent = Math.floor(new Date().getTime()/1000);
                            incoming.old = incoming.self = 1;
                        }
                        if(typeof(incoming.m)!== 'undefined'){
                            incoming.message = incoming.m;
                        }
                        if(typeof (incoming.id)=='undefined'){
                            if(i=='self'){
                                var self = incoming;
                            }
                            if(i=='sent'&&incoming!='undefined'){
                                var sent = incoming;
                                if(self==0||typeof (self)=='undefined'){
                                    if(sent>timestamp){
                                        amount++;
                                        jqcc.ccmobiletab.notify(amount,incoming.from);
                                        timestamp = sent;
                                    }
                                }
                            }
                        }else{
                            if(incoming.self==0&&incoming.old!=1){
                                if(incoming.id>timestamp){
                                    amount++;
                                    jqcc.ccmobiletab.notify(amount,incoming.from);
                                    timestamp = incoming.id;
                                }
                            }
                        }
                    });
                }
            },
            chatWith: function(id) {
                if(mobileDevice && typeof(id)!='undefined'){
                    amount = 0;
                    $(".cometchat_ccmobiletab_redirect .cometchat_ccmobiletab_tabalert").remove();
                    url = jqcc.cometchat.getBaseUrl()+'cometchat_embedded.php?cookiePrefix='+cookiePrefix+'&basedata='+jqcc.cometchat.getBaseData()+'&ccmobileauth='+jqcc.cometchat.getThemeVariable('ccmobileauth')+'&user='+id;
                    jqcc.ccmobiletab.openWebapp(url);
                }
            },
            loggedOut: function(){
                if(mobileDevice){
                    var settings = jqcc.cometchat.getSettings();
                    if(settings.ccauth.enabled==0){
                        $(".cometchat_ccmobiletab_redirect").hide(0);
                        jqcc.cookie(cookiePrefix+"confirmOnMsg", null, {path: '/', expires: -1});
                    }else{
                        $(".cometchat_ccmobiletab_redirect ,.cometchat_ccmobiletab_tabalert").die('click');
                        $(".cometchat_ccmobiletab_redirect").html(logintext);
                        var ccauthpopup = '<div id="mobile_social_login"><div class="login_container">';
                        var authactive = settings.ccauth.active;
                        authactive.forEach(function(auth) {
                            ccauthpopup += '<img onclick="window.open(\''+jqcc.cometchat.getBaseUrl()+'functions/login/signin.php?network='+auth.toLowerCase()+'\',\'socialwindow\')" src="'+jqcc.cometchat.getStaticCDNUrl()+'layouts/mobile/images/login'+auth.toLowerCase()+'.png" class="auth_options"></img>';
                        });
                        ccauthpopup += '</div></div>';
                        $(".cometchat_ccmobiletab_redirect ,.cometchat_ccmobiletab_tabalert").live('click', function(){
                            $("body").append('<div class="cc_overlay" onclick=""></div>'+ccauthpopup);
                            jqcc.ccmobiletab.tabalertScale();
                        });
                        $(".cc_overlay").live('click', function(){
                            $('#mobile_social_login').remove();
                            $(this).remove();
                        });
                    }
                }
            },
            openWebapp: function(url){
                var usemobileapp = false;
                if(mobileappdetails.mobileappOption == "1" && (mobileDevice == "Android" || mobileDevice == "iPhone")) {
                    usemobileapp = confirm("<?php echo $language['use_mobileapp']; ?>");
                    if(usemobileapp && (mobileDevice == "Android" || mobileDevice == "iPhone")){
                        if(mobileDevice == "Android"){
                            if(mobileappdetails.useWhitelabelledapp == '1') {
                                url = settings.ccsiteurl+jqcc.cometchat.getBaseUrl()+"cometchat_checkmobileapp.php?platform="+mobileDevice;
                            } else {
                                url = "https://chat.phpchatsoftware.com/cometchat_checkmobileapp.php?platform="+mobileDevice;
                            }
                            window.location = url;
                        } else if(mobileDevice == "iPhone") {
                            setTimeout(function () { window.location = mobileappdetails.mobileappAppstore; }, 25);
                            window.location = mobileappdetails.mobileappBundleid+"://";
                        }
                    }
                }

                if(!usemobileapp) {
                    if (mobileNewWindow == 0) {
                        window.location = url;
                    } else {
                        var x = '';
                        if(typeof(mobiletabwindow)!='undefined' && mobileDevice == "iPhone"){
                            var x = Math.floor((Math.random() * 10) + 1);
                            mobiletabwindow.close();
                        }
                        mobiletabwindow = window.open(url, 'mobiletab'+x, '_blank');
                        mobiletabwindow.focus();
                        var controlparameters = {"type":"extensions", "name":"mobilewebapp", "method":"checkResponse", "params":{"timeOut":timeOut}};
                        controlparameters = JSON.stringify(controlparameters);
                        mobiletabwindow.postMessage('CC^CONTROL_'+controlparameters,'*');
                    }
                }
            }
        };
    })();
    window.onresize = function(){
        jqcc.ccmobiletab.tabalertScale();
    };
    window.onload = function(){
        jqcc.ccmobiletab.tabalertScale();
    };
})(jqcc);
