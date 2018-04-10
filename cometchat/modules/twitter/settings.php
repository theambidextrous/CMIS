<?php
$errorjs = '';
if (!defined('CCADMIN')) { echo "NO DICE"; exit; }
	$errorMsg = '';
if (isset($_SESSION['cometchat']['error']) && !empty($_SESSION['cometchat']['error'])) {
	$errorMsg = "<h2 id='errormsg' style='font-size: 14px; color: rgb(255, 0, 0);'>".$_SESSION['cometchat']['error']."</h2>";
	unset($_SESSION['cometchat']['error']);
}

if (empty($_GET['process'])) {
	global $client;
	$keysettings = $infomessage ='';
	$base_url = BASE_URL;
	$innercontent = '"';
include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'config.php');
	if(empty($client)) {
		$keysettings = <<<EOD
		          <div class="form-group row">
		            <div class="col-md-6">
		              <label for="ccyear">Number of Tweets:</label>
		            </div>
		             <div class="col-md-6">
		             <input type="text" class="form-control" name="notweets" value="$notweets" style="width:75%;">
		            </div>
		          </div>
		          <div class="form-group row">
		            <div class="col-md-6">
		              <label for="ccyear">Consumer key:</label>
		            </div>
		             <div class="col-md-6">
		             <input type="text" class="form-control" name="consumerkey" value="$consumerkey" style="width:75%;">
		            </div>
		          </div>

		          <div class="form-group row">
		            <div class="col-md-6">
		              <label for="ccyear">Consumer Secret:</label>
		            </div>
		             <div class="col-md-6">
		             <input type="text" class="form-control" name="consumersecret" value="$consumersecret" style="width:75%;">
		            </div>
		          </div>

		          <div class="form-group row">
		            <div class="col-md-6">
		              <label for="ccyear">Access token:</label>
		            </div>
		             <div class="col-md-6">
		             <input type="text" class="form-control" name="accesstoken" value="$accesstoken" style="width:75%;">
		            </div>
		          </div>

		          <div class="form-group row">
		            <div class="col-md-6">
		              <label for="ccyear">Access token secret:</label>
		            </div>
		             <div class="col-md-6">
		             <input type="text" class="form-control" name="accesstokensecret" value="$accesstokensecret" style="width:75%;">
		            </div>
		          </div>
EOD;
	}
echo <<<EOD
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="shortcut icon" href="images/favicon.ico">
	<title>Twitter Settings</title>
	{$GLOBALS['adminjstag']}
  	{$GLOBALS['admincsstag']}
</head>
<body class="navbar-fixed sidebar-nav fixed-nav" style="background-color: white;overflow-y:hidden;">
	<div class="col-sm-6 col-lg-6">
		<div class="card">
		<div class="card-block">
			<form action="?module=dashboard&action=loadexternal&type=module&name=twitter&process=true" method="post">
			{$errorMsg}
			<div class="form-group row">
				<div class="col-md-6">
					<div class="note note-success">
						If you are unsure about any value, please skip them.
					</div>
				</div>
			</div>
			<div class="form-group row">
				<div class="col-md-6">
					<label for="ccyear">Twitter Username:</label>
				</div>
				<div class="col-md-6">
					<input type="text" class="form-control" name="twitteruser" value="$twitteruser" style="width:75%;">
				</div>
			</div>
			{$keysettings}
			<div class="row col-md-4" style="">
			<input type="submit" value="Update Settings" class="btn btn-primary">
			</div>
			</form>
		</div>
		</div>
	</div>
</body>
EOD;
} else {
	$dataerror = 0;
	$data = '';
	$_POST['notweets'] = $_POST['notweets'] ? $_POST['notweets'] : 0;

	if(empty($_POST['twitteruser']) || (empty($GLOBALS['client']) && (empty($_POST['consumerkey']) || empty($_POST['consumersecret']) || empty($_POST['accesstoken']) || empty($_POST['accesstokensecret'])))){
		$dataerror = 1;
	}

	if($dataerror) {
		$_SESSION['cometchat']['error'] = 'Please enter all the configuration details.';
	} else {
		include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'twitteroauth'.DIRECTORY_SEPARATOR.'twitteroauth.php');
		if(!empty($GLOBALS['client'])) {
			$connection = new TwitterOAuth($GLOBALS['consumerkey'], $GLOBALS['consumersecret'], $GLOBALS['accesstoken'], $GLOBALS['accesstokensecret']);
		}else {
			$connection = new TwitterOAuth($_POST['consumerkey'], $_POST['consumersecret'], $_POST['accesstoken'], $_POST['accesstokensecret']);
		}
		$followers = $connection->get("https://api.twitter.com/1.1/followers/list.json?cursor=-1&screen_name=".$_POST['twitteruser']."&count=1");
		if(isset($followers->errors)) {
			$_SESSION['cometchat']['error'] = 'Twitter authentication failed.';
		} else {
			$_SESSION['cometchat']['error'] = 'Twitter details updated successfully.';
			configeditor($_POST);
		}
	}
	header("Location:?module=dashboard&action=loadexternal&type=module&name=twitter");
}
