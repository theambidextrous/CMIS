<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

if (!defined('CCADMIN')) { echo "NO DICE"; exit; }

if(empty($_REQUEST['mailProcess'])){
	$cc_flag = '<div class="note note-success">Please enter the e-mail where you would like to send the incident reports.</div>';
}else{
	$cc_flag = <<<EOD
		<div class="note note-success">
		Please enter the e-mail where you would like to send the incident reports.<br>
		<span id="report_error" style="font-size:12px;">Error: Invalid E-mail ID OR SMTP not configured properly.</span>
		</div>

EOD;
}
$base_url = BASE_URL;
if (empty($_GET['process'])) {
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'config.php');
echo <<<EOD
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="shortcut icon" href="images/favicon.ico">
  <title>Setting</title>
  {$GLOBALS['adminjstag']}
  {$GLOBALS['admincsstag']}
</head>
 <body class="navbar-fixed sidebar-nav fixed-nav" style="background-color: white;overflow-y:hidden;">
             <div class="col-sm-6 col-lg-6">
                <div class="card">
                <div class="card-block">
                 <form style="height:100%" action="?module=dashboard&action=loadexternal&type=plugin&name=report&process=true" method="post">
                  	{$cc_flag}
                  	<br>
		            <div class="form-group row">
		            <div class="col-md-6">
		              <label for="ccyear">E-mail:</label>
		            </div>
		             <div class="col-md-6">
			              <input class="form-control" style="width:75%;" type="text" name="reportEmail" value="$reportEmail">
		            </div>
		          </div>

                    <div class="row col-md-4" style="">
                       <input type="submit" value="Update Settings" class="btn btn-primary">
                    </div>
                    </form>
                </div>
                </div>
              </div>
EOD;
} else {
	if(function_exists('cc_mail')){
		$to = $_POST['reportEmail'];
		$subject = 'E-mail Configuration for Report Conversation';
		$message = 'The E-mail ID provided by you has been successfully configured for Report Conversation plugin in CometChat.';
		$headers = 'From: bounce@chat.com' . "\r\n" .
		'Reply-To: bounce@chat.com' . "\r\n" .
		'X-Mailer: PHP/' . phpversion();

		if(cc_mail($to, $subject, $message, $headers,'')){
			configeditor($_POST);
			header("Location:?module=dashboard&action=loadexternal&type=plugin&name=report");
		}else{
			configeditor(array('reportEmail'=>''));
			header("Location:?module=dashboard&action=loadexternal&type=plugin&name=report&mailProcess=false");
		}
	}
}
