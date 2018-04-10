<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/
header('Access-Control-Allow-Origin: *');
include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins.php");
if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php")) {
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php");
}
include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."extensions".DIRECTORY_SEPARATOR."mobileapp".DIRECTORY_SEPARATOR."config.php");
$errormessage = '';
$mediauploaded = 1;
$filename = '';
$isImage = false;
$isVideo = false;
$isAudio = false;
$mediaType = 0;
$error = 0;
$imageFormats = array("jpg", "jpeg", "png", "gif");
$videoFormats = array("3gp", "mp4", "wmv", "avi", "mov", "flv", "mpg", "webm");
$audioFormats = array("aac", "mp3", "wav", "wma", "ogg");
if (!empty($_REQUEST['callbackfn']) && $_REQUEST['callbackfn'] == 'mobileapp') {
	$filename = preg_replace("/[^a-zA-Z0-9\. ]/", "", sql_real_escape_string($_POST['name']));
	$isImage = (strpos($_POST['name'], 'MG-'))? true : false;
	$isVideo = (strpos($_POST['name'], 'ID-'))? true : false;
	$width = $_POST['imagewidth'];
	$height = $_POST['imageheight'];
	$path = pathinfo($filename);
	$ext = pathinfo($filename, PATHINFO_EXTENSION);
	if (in_array(strtolower($ext), $imageFormats)) {
		$mediaType = 1;
		$isImage = true;
	}
	if (in_array(strtolower($ext), $videoFormats)) {
		$mediaType = 2;
		$isVideo = true;
	}
	if (in_array(strtolower($ext), $audioFormats)) {
		$mediaType = 3;
		$isAudio = true;
	}
} else {
	$filename = preg_replace("/[^a-zA-Z0-9\. ]/", "", sql_real_escape_string($_FILES['Filedata']['name']));
	$filename = str_replace(" ", "_",$filename);
	$path = pathinfo($filename);
	$ext = pathinfo($filename, PATHINFO_EXTENSION);
	if (in_array(strtolower($ext), $imageFormats)) {
		$isImage = true;
		$mediaType = 1;
		if(!empty($_FILES['Filedata'])&&!empty($_FILES['Filedata']['tmp_name'])){
			list($width, $height) = getimagesize($_FILES['Filedata']['tmp_name']);
		}else{
			$width = "512";
			$height = "512";
		}
	} else if (in_array(strtolower($ext), $videoFormats)) {
		$width = "512";
		$height = "512";
		$isVideo = true;
		$mediaType = 2;
	} else if (in_array(strtolower($ext), $audioFormats)) {
		$width = "512";
		$height = "512";
		$isAudio = true;
		$mediaType = 3;
	}
}

$md5filename = md5(str_replace(" ", "_",str_replace(".","",$filename))."cometchat".time()).".".strtolower($path['extension']);
$unencryptedfilename=rawurlencode($filename);

if(defined('AWS_STORAGE') && AWS_STORAGE == '1' && !empty($_FILES['Filedata'])) {
	include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."functions".DIRECTORY_SEPARATOR."storage".DIRECTORY_SEPARATOR."s3.php");
	if(empty($client)){
        $s3 = new S3(AWS_ACCESS_KEY, AWS_SECRET_KEY,false,$awsendpoint);
    }else{
        $s3 = new S3(AWS_ACCESS_KEY, AWS_SECRET_KEY);
    }
	if(!$s3->putObject($s3->inputFile($_FILES['Filedata']['tmp_name'], false), AWS_BUCKET, $bucket_path.'filetransfer/'.$md5filename, S3::ACL_PUBLIC_READ)) {
		$error = 1;
	}
	$aws_bucket_url = !empty($client) ? "s3.amazonaws.com/".$aws_bucket_url : $aws_bucket_url;
	$linkToFile = '//'.$aws_bucket_url.'/'.$bucket_path.'filetransfer/'.$md5filename;
	$hrefurl = $linkToFile;
	$server_url = BASE_URL;
}else if(!empty($_FILES['Filedata']) && is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
	if (!move_uploaded_file($_FILES['Filedata']['tmp_name'], dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'writable'.DIRECTORY_SEPARATOR.'filetransfer'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.$md5filename)) {
		$error = 1;
	}
	$linkToFile = BASE_URL.'writable/filetransfer/uploads/'.$md5filename;
	$server_url = '//'.$_SERVER['HTTP_HOST'].BASE_URL;
	if(filter_var(BASE_URL, FILTER_VALIDATE_URL)){
		$server_url = BASE_URL;
	}
	$hrefurl = ''.$server_url.'plugins/filetransfer/download.php?file='.$md5filename.'&amp;unencryptedfilename='.$unencryptedfilename.'';
}
if(!empty($error)) {
	$errormessage = 'An error has occurred. Please contact administrator. Closing Window.';
	$mediauploaded = 0;
}

if (!empty($isImage) && $isImage) {
	$imgHeight = "";
	if ($width >= $height && $height >= 50 ) {
		$imgHeight = '70px';
	} else if ($width <= $height && $height >=50 && $height <= 100) {
		$imgHeight = '50px';
	} else if ($width <= $height &&  $height >= 100) {
		$imgHeight = '170px';
	} else {
		$imgHeight = '70px';
	}

	$imgtag = '<img class="file_image" type="image" src="'.$linkToFile.'" style="max-height:'.$imgHeight.';"/>';
} else if (!empty($isVideo) && $isVideo) {
	$imgtag = '<div class="cometchat_filevideo">('.$filename.')</div><img class="file_video" type="video" src="'.STATIC_CDN_URL.'images/videoicon.png"/>';
} else if (!empty($isAudio) && $isAudio) {
	$imgtag = '<div class="cometchat_fileaudio">('.$filename.')</div><img class="file_audio" type="audio" src="'.STATIC_CDN_URL.'images/audioicon.png"/>';
}
$pluginpath = "/plugins/filetransfer/";
if (empty($errormessage)) {
	$insertedId = "";
	$localmessageid = !empty($_REQUEST['localmessageid'])? $_REQUEST['localmessageid']: ((!empty($_GET['callback'])) ? $_GET['callback'] : '');
	if (!empty($_POST['chatroommode'])) {
		if ((!empty($isImage) && $isImage) || (!empty($isVideo) && $isVideo) || (!empty($isAudio) && $isAudio) ) {
			$message = '<div style="display:none;">'.$filetransfer_language[9].' ('.$filename.'). </div><br/><a class="imagemessage mediamessage" pluginname ="filetransfer" filename="'.$unencryptedfilename.'" encfilename="'.$md5filename.'" mediatype="'.$mediaType.'" link="'.$hrefurl.'" href="'.$hrefurl.'" imageheight="'.$height.'" imagewidth="'.$width.'" pluginpath="'.$pluginpath.'">'.$imgtag.'</a>';
		} else {
			$message = $filetransfer_language[9].' ('.$filename.'). <a href="'.$hrefurl.'" target="_blank" mediaType="0">'.$filetransfer_language[6].'</a>';
		}
		$response = sendChatroomMessage($_POST['to'],$message,0);
	} else {
		if ((!empty($isImage) && $isImage) || (!empty($isVideo) && $isVideo) || (!empty($isAudio) && $isAudio) ) {
			//The message is kept as display none as it is needed for save conversation plugin
			$message = '<div style="display:none;">'.$filetransfer_language[5].' ('.$filename.'). </div><br/><a class="imagemessage mediamessage" pluginname ="filetransfer" filename="'.$unencryptedfilename.'" encfilename="'.$md5filename.'" mediatype="'.$mediaType.'" link="'.$hrefurl.'" href="'.$hrefurl.'" pluginpath="'.$pluginpath.'">'.$imgtag.'</a>';
		} else {
			$message = $filetransfer_language[5].' ('.$filename.'). <a class="imagemessage" href="'.$hrefurl.'" target="_blank" mediatype="'.$mediaType.'">'.$filetransfer_language[6].'</a>';
		}
		$response = sendMessage($_POST['to'],$message,0,'filetransfer');
		$insertedId = $response['id'];
		$processedMessage = $_SESSION['cometchat']['user']['n'].": ".$filetransfer_language[5];
		$response['push'] = pushMobileNotification($_POST['to'],$response['id'],$processedMessage);
	}
	if(USE_COMET == 1){
		$cometmessage = array();
		$cometresponse = array('to' => $_POST['to'],'message' => $message, 'localmessageid' => $localmessageid ,'dir' => 0,'type' => "filetransfer");
		array_push($cometmessage, $cometresponse);
		publishCometMessages($cometmessage,$response['id']);
	}
	if (!empty($_REQUEST['callbackfn']) && $_REQUEST['callbackfn'] == 'mobileapp') {
		if (!empty($_REQUEST['callback'])) {
			echo $_REQUEST['callback'].'('.json_encode(array('id'=>$response['id'])).')';
		}else {
			echo json_encode($response);
		}
		exit;
	}
	$errormessage = $filetransfer_language[8];
}

$embed = '';
$embedcss = '';
$close = "setTimeout('closePopup();',2000);";

if (!empty($_GET['embed']) && $_GET['embed'] == 'web') {
	$embed = 'web';
	$embedcss = 'embed';
} else if (!empty($_GET['embed']) && $_GET['embed'] == 'desktop') {
	$embed = 'desktop';
	$embedcss = 'embed';
	$close = "setTimeout('parentSandboxBridge.closeCCPopup(\"filetransfer\");',2000);";
}
if (!empty($_REQUEST['callbackfn']) && $_REQUEST['callbackfn'] == 'mobileapp') {
	echo $mediauploaded;
}
