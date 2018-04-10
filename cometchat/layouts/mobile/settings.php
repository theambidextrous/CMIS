<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

if (!defined('CCADMIN')) { echo "NO DICE"; exit; }

if (empty($_GET['process'])) {
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'config.php');
	$options = array(
		"disableForMobileDevices"		=> array('choice','If set to yes, CometChat will be hidden in mobile browsers:')
	);
	$form = showSettingsUI($options);

	$base_url = BASE_URL;
	$alchkd = '';
	$zchkd = '';
	$ochkd = '';
if ($mobiletabConfirmOnAllMessages == 2) {
    $confirmOnAllMessagesYes = '';
    $confirmOnAllMessagesNo = '';
	$ochkd = "selected";
    $confirmNever = 'checked="checked"';
}else if ($mobiletabConfirmOnAllMessages == 1) {
    $confirmOnAllMessagesYes = 'checked="checked"';
    $confirmOnAllMessagesNo = '';
	$zchkd = "selected";
    $confirmNever = '';
} else {
    $confirmOnAllMessagesNo = 'checked="checked"';
	$alchkd = "selected";
    $confirmOnAllMessagesYes = '';
    $confirmNever = '';
}
if($enableMobileTab == 1){
    $enableMobileTabYes = 'checked="checked"';
    $enableMobileTabNo = '';
}else{
    $enableMobileTabNo = 'checked="checked"';
    $enableMobileTabYes = '';
}
if($mobileNewWindow == 1){
    $mobileNewWindowYes = 'checked="checked"';
    $mobileNewWindowNo = '';
}else{
    $mobileNewWindowNo = 'checked="checked"';
    $mobileNewWindowYes = '';
}
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
 <body class="navbar-fixed sidebar-nav fixed-nav" style="background-color:white;white;overflow-y:hidden;"">
             <div class="col-sm-12 col-lg-12">
                <div class="card-block">
                  <form style="height:100%" action="?module=dashboard&action=loadthemetype&type=layout&name=mobile&process=true" method="post">
                  $form


				<div class="form-group row">
					<div class="col-md-12">
			      	<label class="form-control-label">Enable Mobile theme:</label>
			      		<div class=""><label class=""><div style="position:relative;top:4px;"><input style="position: absolute;" name="enableMobileTab" value="1" $enableMobileTabYes type="radio" ></div><span style="padding-left:25px;">Yes</span></label><label class=""><div style="position:relative;top:4px;"><input style="position: absolute;left:8px;" name="enableMobileTab" value="0" $enableMobileTabNo type="radio"></div><span style="padding-left:36px;">No</span></label></div>
					</div>
				</div>

				<div class="form-group row">
					<div class="col-md-12">
			      	<label class="form-control-label">Open in new window:</label>
			      		<div class=""><label class=""><div style="position:relative;top:4px;"><input style="position: absolute;" name="mobileNewWindow" value="1" $mobileNewWindowYes type="radio" ></div><span style="padding-left:25px;">Yes</span></label><label class=""><div style="position:relative;top:4px;"><input style="position: absolute;left:8px;" name="mobileNewWindow" value="0" $mobileNewWindowNo type="radio"></div><span style="padding-left:36px;">No</span></label></div>
					</div>
				</div>

		          <div class="form-group row">
		            <div class="col-md-12">
		              <label for="ccyear">New messages notification:</label>
		              <select class="form-control" name="mobiletabConfirmOnAllMessages" id="pluginTypeSelector" >
							<option  value="1" $zchkd>Always </option>
							<option  value="0" $alchkd>Once</option>
							<option  value="2" $ochkd>Never</option>
						</select>
		            </div>
		          </div>
                    <div class="row col-md-4" style="">
                       <input type="submit" value="Update Settings" class="btn btn-primary">
                    </div>
                    </form>
                </div>
                </div>

EOD;

} else {
	configeditor($_POST);
	header("Location:?module=dashboard&action=loadthemetype&type=layout&name=mobile");
}
