<?php
    include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'config.php');
    include_once(dirname(dirname(dirname(dirname(__FILE__)))).DIRECTORY_SEPARATOR."modules.php");
    if (file_exists(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."lang.php")) {
        include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."lang.php");
    }
    $callbackfn = ''; if (!empty($_GET['callbackfn'])) { $callbackfn = $_GET['callbackfn']; }
?>
var gamessource = {};
var gamesheight = {};
var gameswidth = {};
var gamesname = {};
var keywords = "<?php echo $keywordlist; ?>";
var keywordmatch = '';
if(keywords != '') {
    keywordmatch = new RegExp(keywords.toLowerCase());
}
var apiAccess = 0;
var lightboxWindows = '<?php echo $lightboxWindows;?>';
var baseurl = '<?php echo BASE_URL; ?>';
var callbackfn='<?php echo $callbackfn; ?>';
var mobileDevice = navigator.userAgent.match(/cc_ios|cc_android|ipad|ipod|iphone|android|windows ce|Windows Phone|blackberry|palm|symbian/i);
$(function() {
    try {
        if (parent.jqcc.cometchat.ping() == 1) {
            apiAccess = 1;
        }
    } catch (e) {
    }
    $('#loader').css('display', 'block');
    var categoriesinfo = '';
    var firstcat = 0;
    gamesJson['categories'].sort();
    gamesJson['games'].sort(function(a,b){
        if(a.name.toLowerCase() < b.name.toLowerCase()) return -1;
        if(a.name.toLowerCase() > b.name.toLowerCase()) return 1;
        return 0;
    });
    for (var i = 0; i <= gamesJson['categories'].length-1; i++) {
        if (keywordmatch == '' || gamesJson['categories'][i].toLowerCase().match(keywordmatch) == null) {
            if (firstcat == 0) {
                firstcat = 'all games';
            }
            categoriesinfo += '<li id=\'' + gamesJson['categories'][i] + '\'>' + gamesJson['categories'][i] + '</li>';
        }
    }

    $('#loader').css('display', 'none');
    $('#optionList').find('ul').append(categoriesinfo);
    if (firstcat) {
        getCategory(firstcat);
    }

    $('#categories').click(function() {
        $(this).toggleClass('open');
        $('#optionList').toggleClass('openListHeight');
    });

    $('#optionList').on('click', 'li', function() {
        var currValue = $(this).html();
        $('.selected').html(currValue);
        $('#optionList').find('li').removeClass('active');
        $(this).addClass('active');
        getCategory(currValue);
    });

    resizeWindow();
});

function getCategory(catname) {
    gamessource = {};
    gamesheight = {};
    gameswidth = {};
    gamesname = {};
    gamescategory = {};
    if (jqcc().slimScroll && !mobileDevice) {
        $("#games").slimScroll({height: '263px', width: '100%', allowPageScroll: false});
        $(".slimScrollBar").css('top', '0px');
    }
    $("#games").scrollTop(0);
    $('#loader').css('display', 'block');
    var gamesList = '';
    catname = $.trim(catname);
    if (catname == 'all games') {
        $.each(gamesJson.games, function(cat, info) {
            if(typeof (gamesname[info.package_id]) != 'undefined') {
                return;
            }
            var name = info.name;
            if(keywordmatch == '' || name.toLowerCase().replace(/\./g,'').match(keywordmatch) == null){
                for(var i=0; i<=info.categories.length-1; i++){
                    if(keywordmatch != '' && info.categories[i].toLowerCase().match(keywordmatch) != null){
                        return;
                    }
                }
                var thumbnail = info.thumb_120;
                var height = 500;
                var aspect_ratio = info.aspect_ratio;
                var width = aspect_ratio*height;
                var orientation = info.orientation;
                var gameLink = info.package_id;
                var source = baseurl + 'modules/games/index.php?gameLink=' + gameLink + '&width=' + width + '&height=' + height + '&name=' + name;
                gamessource[gameLink] = source;
                gamesheight[gameLink] = height;
                gameswidth[gameLink] = width;
                gamesname[gameLink] = name;
                gamescategory[gameLink] = info.categories;
                gamesList += '<div class="gamelist ' + gameLink + '" onclick="javascript:loadGame(\'' + gameLink + '\')"><img src="' + thumbnail + '"/><br/><div class="title">' + name + '</div></div>';
            }
        });
    } else {
        $.each(gamesJson.games, function(key, val) {
            if(val.categories.indexOf(catname) >= 0) {
                var name = val.name;
                if (keywordmatch == '' || name.toLowerCase().replace(/\./g,'').match(keywordmatch) == null) {
                    for(var i=0; i<=val.categories.length-1; i++){
                        if(keywordmatch != '' && val.categories[i].toLowerCase().match(keywordmatch) != null){
                            return;
                        }
                    }
                    var thumbnail = val.thumb_120;
                    var height = 500;
                    var aspect_ratio = val.aspect_ratio;
                    var width = aspect_ratio*height;
                    var orientation = val.orientation;
                    var gameLink = val.package_id;
                    var source = baseurl + 'modules/games/index.php?gameLink=' + gameLink + '&width=' + width + '&height=' + height + '&name=' + name;
                    gamessource[gameLink] = source;
                    gamesheight[gameLink] = height;
                    gameswidth[gameLink] = width;
                    gamesname[gameLink] = name;
                    gamescategory[gameLink] = val.categories;
                    gamesList += '<div class="gamelist ' + gameLink + '" onclick="javascript:loadGame(\'' + gameLink + '\')"><img src="' + thumbnail + '"/><br/><div class="title">' + name + '</div></div>';
                }
            }
        });
    }
    $('#games').html(gamesList);
    $('#loader').css('display', 'none');

    if (jqcc().slimScroll && !mobileDevice) {
        $("#games").slimScroll({resize: '1'});
    }
}

function loadGame(id) {
    var url = gamessource[id];
    var category = gamescategory[id];
    var name = "singleplayergame";
    var width = parseInt(gameswidth[id])+20;
    var height = parseInt(gamesheight[id])+20;
    var properties = "status=0,toolbar=0,menubar=0,directories=0,resizable=0,location=0,status=0,scrollbars=0, width="+width+",height="+height+"";
    var title = gamesname[id];

    if (apiAccess == 1 && lightboxWindows == 1) {
        var controlparameters = {"type":"modules", "name":"core", "method":"loadCCPopup", "params":{"url": url, "name":name, "properties":properties, "width":width, "height":height, "title":title, "force":"1", "allowmaximize":"1", "allowresize":null, "allowpopout":null, "windowMode":null}};
       controlparameters = JSON.stringify(controlparameters);
       parent.postMessage('CC^CONTROL_'+controlparameters,'*');
    } else {
         if(callbackfn=='desktop'){
            var b=properties.split(',');
            var i;
            var nw=0;
            var nh=0;
            for(i=0;i<b.length;i++){
                if(b[i].indexOf('height')!=-1){
                  var h=b[i].split('=');
                  nh=h[1];
                }
                if(b[i].indexOf('width')!=-1){
                  var w=b[i].split('=');
                  nw=w[1];
                }
            }
            nh=parseInt(nh)+30;//For Desktop Messenger, module &plugin height issue
            nw=parseInt(nw)+15;//For Desktop Messenger, module &plugin width issue
            var w = window.open(url,name,properties);
            w.document.title=name;
            w.resizeTo(nw,nh);
            w.focus();
        } else if(callbackfn == 'mobileapp'){
        	window.open(url,"_self");
        }else{
            var w = window.open(url,name,properties);
            w.focus();
        }
    }
}

$(window).resize(function(){
    resizeWindow();
});

function resizeWindow(){
    var newHeight = $( document ).height() - 36;
    if (jqcc().slimScroll && !mobileDevice) {
        $(".slimScrollDiv").css('height',newHeight+'px');
    } else {
        $('.gamecontainer').css('height','100%');
    }
}
