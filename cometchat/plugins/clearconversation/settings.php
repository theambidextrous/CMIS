<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

if (!defined('CCADMIN')) { echo "NO DICE"; exit; }

if (empty($_GET['process'])) {
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'config.php');
if ($isDelete == 1) {
	$isDeleteYes = 'checked="checked"';
	$isDeleteNo = '';
} else {
	$isDeleteNo = 'checked="checked"';
	$isDeleteYes = '';
}
$base_url = BASE_URL;
echo <<<EOD
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="shortcut icon" href="images/favicon.ico">
  <title>Generate Embed Code</title>
  {$GLOBALS['adminjstag']}
  {$GLOBALS['admincsstag']}
</head>
 <body class="navbar-fixed sidebar-nav fixed-nav" style="background-color: white;overflow-y:hidden;">
             <div class="col-sm-6 col-lg-6">
                <div class="card">
                <div class="card-block">
                  <form style="height:100%" action="?module=dashboard&action=loadexternal&type=plugin&name=clearconversation&process=true" method="post">

		          <div class="form-group row">
		            <div class="col-md-6">
		              <label for="ccyear">Do you permanently want to delete conversation?</label>
		            </div>
		             <div class="col-md-6">
		              <label class="">
			              <div style="position:relative;"><input style="position: absolute;" type="radio" name="isDelete" $isDeleteYes value="1""></div><span style="padding-left:25px;">Yes</span>
			            </label>
			            <label class="">
			              <div style="position:relative;"><input style="position: absolute;" type="radio" name="isDelete" $isDeleteNo value="0"></div><span style="padding-left:25px;">No</span>
			            </label>
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
	configeditor($_POST);
	header("Location:?module=dashboard&action=loadexternal&type=plugin&name=clearconversation");
}
