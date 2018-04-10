<?php
 /*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/
 global $trayicon;
 $callbackfn = '';
 if(!empty($_GET['callbackfn']) && $_GET['callbackfn'] == 'desktop'){
    $desktopmode = 1;
 }else{
    $desktopmode = 0;
 }
 include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'comet.js');
 ?>
var cometid = '';
var cc_translate_use_google = 0;
function initializeCometService(){
    if(typeof comet === "undefined"){
        comet = COMET.init({
            'desktop': '<?php echo $desktopmode;?>',
            'baseurl': '<?php echo BASE_URL;?>',
            'KEY_A': '<?php echo KEY_A; ?>',
            'KEY_B': '<?php echo KEY_B; ?>',
            'KEY_C': '<?php echo KEY_C; ?>',
            'ssl': (window.location.protocol=='https:') ? true : false
        });
    }
};

<?php
if(file_exists(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'realtimetranslate'.DIRECTORY_SEPARATOR.'config.php')) {
    include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'realtimetranslate'.DIRECTORY_SEPARATOR.'config.php');
    if($useGoogle == 1 && !empty($googleKey) && !empty($trayicon['realtimetranslate'])){
?>
    cc_translate_use_google = 1;
<?php
    }
}
?>
calleeAPI = 'cc_docked';
function cometcall_function(id, td, calleeapi){
    cometid = id;
    calleeAPI = calleeapi;
    comet.subscribe({
        channel: id
    },cometcall_callback);
}

cometcall_callback = function(incoming){
    incoming.selfadded=0;
    incoming.old=0;
    if(typeof (jqcc[calleeAPI].addMessages)=="function"){
        jqcc[calleeAPI].addMessages([incoming]);
    }
}

function chatroomcall_function(id,userid){
    comet.subscribe({
        channel: id
    }, chatroomcall_callback);
}

chatroomcall_callback =  function(incoming){
    incoming.calledfromsend=0;
    jqcc.cometchat.setChatroomVars('newMessages', jqcc.cometchat.getChatroomVars('newMessages')+1);
    jqcc[jqcc.cometchat.getChatroomVars('calleeAPI')].addChatroomMessage(incoming);
}

function cometuncall_function(id){
    comet.unsubscribe({channel: id});
}

function cometstop_function(){
    if(typeof comet !== 'undefined' && typeof(comet.terminate) == 'function'){
        comet.terminate();
    }
}
