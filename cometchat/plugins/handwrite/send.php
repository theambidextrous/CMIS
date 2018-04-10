<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

$data = explode(';',$_REQUEST['tid']);
$_GET['basedata'] = $data[1];

include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins.php");
if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php")) {
    include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php");
}
include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."extensions".DIRECTORY_SEPARATOR."mobileapp".DIRECTORY_SEPARATOR."config.php");

$data = explode(';',$_REQUEST['tid']);
$_REQUEST['tid'] = $data[0];
$_REQUEST['embed'] = $data[2];
$randomImage = md5(rand(0,9999999999).time());
if (!empty($_REQUEST['image'])) {
    $image = explode('data:image/png;base64,',$_REQUEST['image']);
    $png = base64_decode($image[1]);
} else {
    $inputSocket = fopen('php://input','rb');
    $png = stream_get_contents($inputSocket);
    fclose($inputSocket);
}

$filename = $randomImage.".png";
$unencryptedfilename=rawurlencode($filename);

if(defined('AWS_STORAGE') && AWS_STORAGE == '1') {
    include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."functions".DIRECTORY_SEPARATOR."storage".DIRECTORY_SEPARATOR."s3.php");
    if(empty($client)){
        $s3 = new S3(AWS_ACCESS_KEY, AWS_SECRET_KEY,false,$awsendpoint);
    }else{
        $s3 = new S3(AWS_ACCESS_KEY, AWS_SECRET_KEY);
    }
    if($uploadedObject = $s3->putObject($png, AWS_BUCKET, $bucket_path.'handwrite/'.$randomImage.".png", S3::ACL_PUBLIC_READ)) {
        $aws_bucket_url = !empty($client) ? "s3.amazonaws.com/".$aws_bucket_url : $aws_bucket_url;
        $linkToImage = '//'.$aws_bucket_url.'/'.$bucket_path.'handwrite/'.$randomImage.".png";
    }
    $hrefurl = $linkToImage;
    $server_url = BASE_URL;
}else {
    $file = fopen(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."writable".DIRECTORY_SEPARATOR."handwrite".DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR.$randomImage.".png","w");
    fwrite($file,$png);
    fclose($file);
    if(file_exists(dirname(dirname(dirname(__FILE__)))."/writable/handwrite/uploads/".$randomImage.".png")){
        $linkToImage = BASE_URL."writable/handwrite/uploads/".$randomImage.".png";
    }
    $server_url = '//'.$_SERVER['SERVER_NAME'].BASE_URL;
    $hrefurl = ''.$server_url.'plugins/handwrite/download.php?file='.$unencryptedfilename.'&amp;unencryptedfilename='.$filename.'';
    if(filter_var(BASE_URL, FILTER_VALIDATE_URL)){
        $server_url = BASE_URL;
    }
}

if(isset($linkToImage)) {
     $text = '<a class="mediamessage" pluginname="handwrite" filename="'.$filename.'" encfilename="'.$unencryptedfilename.'" mediatype="1" link="'.$server_url.'plugins/handwrite/download.php?file='.$unencryptedfilename.'&amp;unencryptedfilename='.$filename.'" href="'.$hrefurl.'" style="display:inline-block;margin-bottom:3px;margin-top:3px;max-width:100%;"><img class="cc_handwrite_image" src="'.$linkToImage.'" border="0" height="90" width="134"></a>';
    if (substr($_REQUEST['tid'],0,1) == 'c') {
        $_REQUEST['tid'] = substr($_REQUEST['tid'],1);
        sendChatroomMessage($_REQUEST['tid'],'<div class="cometchat_hw_lang" style="display:none;">'.$handwrite_language[3].'</div>'.$text,0,'handwrite');
    } else {
        $response = sendMessage($_REQUEST['tid'],'<div class="cometchat_hw_lang" style="display:none;">'.$handwrite_language[1].'</div>'.$text,0,'handwrite');
        $processedMessage = $_SESSION['cometchat']['user']['n'].": ".$handwrite_language[1];
        pushMobileNotification($_REQUEST['tid'],$response['id'],$processedMessage);

        if(USE_COMET == 1){
            $cometmessage = array();
            $cometresponse = array('to' => $_REQUEST['tid'],'message' => '<div class="cometchat_hw_lang" style="display:none;">'.$handwrite_language[1].'</div>'.$text, 'dir' => 0,'type' => "handwrite");
            array_push($cometmessage, $cometresponse);
            publishCometMessages($cometmessage,$response['id']);
        }
        /*Uncomment to enable push notifications for CometChat Legacy Apps*/
        /*if (isset($_REQUEST['sendername']) && $pushNotifications == 1) {
                pushMobileNotification($handwrite_language[2], $_REQUEST['sendername'], $_REQUEST['tid'], $_REQUEST['tid']);
        }*/
        /*Uncomment to enable push notifications for CometChat Legacy Apps*/
    }
}
$embed = '';
$embedcss = '';
$close = "setTimeout('window.close()',2000);";

if (!empty($_REQUEST['embed']) && $_REQUEST['embed'] == 'web') {
    $embed = 'web';
    $embedcss = 'embed';
    $close = "
        var controlparameters = {'type':'plugins', 'name':'handwrite', 'method':'closeCCPopup', 'params':{'name':'handwrite'}};
        controlparameters = JSON.stringify(controlparameters);
        if(typeof(parent) != 'undefined' && parent != null && parent != self){
            parent.postMessage('CC^CONTROL_'+controlparameters,'*');
        } else {
            window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');
        }";
}

if (!empty($_REQUEST['embed']) && $_REQUEST['embed'] == 'mobileapp') {
    $close = "setTimeout(
        function(){
            window.location = 'mobileapp:cc_close_webview'
        },
    100)";
}

if (!empty($_REQUEST['embed']) && $_REQUEST['embed'] == 'desktop') {
    $embed = 'desktop';
    $embedcss = 'embed';
    $close = "parentSandboxBridge.closeCCPopup('handwrite');";
}
if(!empty($_REQUEST['other']) && $_REQUEST['other'] == 1){
    echo $close;
} else {
    echo <<<EOD
<!DOCTYPE html>
<html>
<head>
<title>{$handwrite_language[0]} (closing)</title>
<script type="text/javascript">
    function closePopup(){
        var controlparameters = {'type':'plugins', 'name':'handwrite', 'method':'closeCCPopup', 'params':{'name':'handwrite'}};
        controlparameters = JSON.stringify(controlparameters);
        if(typeof(parent) != 'undefined' && parent != null && parent != self){
            parent.postMessage('CC^CONTROL_'+controlparameters,'*');
        } else {
            window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');
        }
    }
</script>
</head>
<body onload="closePopup();">
</body>
</html>
EOD;
}
?>
