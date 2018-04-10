<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

if (!defined('CCADMIN')) { echo "NO DICE"; exit; }

global $getstylesheet;
include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'config.php');
$branded_yes = $branded_no = "";
if ($branded) {
	$branded_no = "checked";
}else{
	$branded_yes = "checked";
}
$base_url = BASE_URL;

if (empty($_GET['process'])) {
	$brandedhtml = "";
	if (isset($_REQUEST['branded']) && $_REQUEST['branded']==1) {
		$brandedhtml = <<<EOD
			<div class="col-md-12" style="padding-top:7px;">Branded :</div>
			<div class="col-md-12">
				<input  name="BRANDED" value="1" {$branded_no}  type="radio" style="margin-top:8px;" />&nbsp;&nbsp;&nbsp;No
				&nbsp;&nbsp;&nbsp;
				<input name="BRANDED"  value="0" {$branded_yes} type="radio" />&nbsp;&nbsp;Yes
			</div>
EOD;
}

/*
	To add branded logo, signup and forgot password link.
*/

$brandedOptions = "";
if($branded==0){
	$brandedOptions=<<<EOD
		<div class="col-md-12" style="padding-top:7px;">Sign Up Link :</div>
		<div class="col-md-12">
			<input  name="SIGNUP_URL" value="{$signUp_url }" class="form-control"  type="txt" style="margin-top:8px;" />
		</div>
		<div class="col-md-12" style="padding-top:7px;">Forgot Password Link :</div>
		<div class="col-md-12">
			<input  name="FORGOT_URL" value="{$forgot_url }" class="form-control" type="txt" style="margin-top:8px;" />
		</div>
		<div class="col-md-12" style="padding-top:7px;">Upload Logo :</div>
		<div class="col-md-12">
			<input  name="file" class="form-control" id="logo" type="file" style="margin-top:8px;" />
		</div>
EOD;
}
$jqueryjstag =  getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'jquery', 'ext' => 'js'));
echo <<<EOD
	<!DOCTYPE html>
	{$jqueryjstag}
	<script>
	  $ = jQuery = jqcc;
	</script>
	{$GLOBALS['adminjstag']}
    {$GLOBALS['admincsstag']}
	<script type="text/javascript" language="javascript">
	    function resizeWindow() {
	    	window.resizeTo((510), (($('form').outerHeight(false)+window.outerHeight-window.innerHeight)));
	    }
	    function reset_colors(){
	    	$('#login_background_field').val('#FFFFFF');
	    	$('#login_placeholder_field').val('#777788');
	    	$('#login_button_pressed_field').val('#002832');
	    	$('#login_button_text_field').val('#FFFFFF');
	    	$('#login_foreground_text_field').val('#000000');
	    	$('#dm_details').submit()
	    }

	    var arr = ['#login_background','#login_placeholder','#login_button_pressed','#login_button_text','#login_foreground_text'];
	    var arrColor = ['$login_background','$login_placeholder','$login_button_pressed','$login_button_text','$login_foreground_text'];
	    $(function() {

	    	$.each(arr,function(i,val){
	    		$(val).ColorPicker({
					color: arrColor[i],
					onShow: function (colpkr) {
						$(colpkr).fadeIn(500);
						return false;
					},
					onHide: function (colpkr) {
						$(colpkr).fadeOut(500);
						return false;
					},
					onChange: function (hsb, hex, rgb) {
						$(val+' div').css('backgroundColor', '#' + hex);
						$(val).attr('newcolor','#'+hex);
						$(val+'_field').val('#'+hex.toUpperCase());
					}
				});
	    	}) ;

			setTimeout(function(){
				resizeWindow();
			},200);
		});
	</script>
 <body class="navbar-fixed sidebar-nav fixed-nav" style="background-color: white;">
		<form id="dm_details" action="?module=dashboard&action=loadexternal&type=extension&name=desktop&process=true" method="post" enctype="multipart/form-data">
		<div class="row col-md-12">
	    	<div class="row">

	    	{$brandedhtml}
	    	{$brandedOptions}

				<div class="col-md-12" style="padding-top:7px;">Login Color :</div>
				<div class="col-md-1">
					<div class="colorSelector themeSettings" field="login_background" id="login_background">
						<div style="background:$login_background"></div>
					</div>
				</div>
				<div class="col-md-11">
					<input type="text" class="form-control themevariables" id="login_background_field" name="login_background" value="$login_background" required="true">
				</div>
				<div class="col-md-12" style="padding-top:7px;">Login text hint:</div>
				<div class="col-md-1">
					<div class="colorSelector themeSettings" field="login_placeholder" id="login_placeholder">
						<div style="background:$login_placeholder"></div>
					</div>
				</div>
				<div class="col-md-11">
					<input type="text" class="form-control" id="login_placeholder_field" name="login_placeholder" value="$login_placeholder" required="true">
				</div>
				<div class="col-md-12" style="padding-top:7px;">Login button text:</div>
				<div class="col-md-1">
					<div class="colorSelector themeSettings" field="login_button_text" id="login_button_text">
						<div style="background:$login_button_text"></div>
					</div>
				</div>
				<div class="col-md-11">
					<input type="text" class="form-control" id="login_button_text_field" name="login_button_text" value="$login_button_text" required="true">
				</div>

				<div class="col-md-12" style="padding-top:7px;">Login text:</div>
				<div class="col-md-1">
					<div class="colorSelector themeSettings" field="login_foreground_text" id="login_foreground_text">
						<div style="background:$login_foreground_text"></div>
					</div>
				</div>
				<div class="col-md-11">
					<input type="text" class="form-control" id="login_foreground_text_field" name="login_foreground_text" value="$login_foreground_text" required="true">
				</div>

		    </div>
		    	<div class="row col-md-12" style="padding-bottom:5px;padding-top: 5px;">
	      			<input type="submit" value="Update Settings" class="btn btn-primary">
	    		</div>
		   </div>

EOD;
} else {
	if(isset($_POST)){
		configeditor($_POST);

		if(!empty($_FILES["file"]["name"])){
			/* Logo Upload */
			$allowedExts = array("png");
			$folderarray=array("size");
			$size = array(200,60);
			$flag = 1;

			$filename = $_FILES["file"]["name"];
			$filesize = getimagesize($_FILES["file"]["tmp_name"]);
			if(($filesize[0] == $size[0]) && ($filesize[1] == $size[1])){
				$temp = explode(".", $filename);
				$extension = end($temp);
				if (!in_array($extension, $allowedExts)) {
					$flag = 0;
				}
			}else{
				$flag = 0;
			}

			if ($_FILES["file"]["error"] > 0 || $flag == 0) {
				$_SESSION['cometchat']['error'] = "Invalid logo format or size. Please upload 200X60 png image";
				$_SESSION['cometchat']['type'] = 'alert';
			}else{

				$logoUploadpath = dirname(dirname(dirname(__FILE__)))."/writable/images/logo/";

				if (!is_dir($logoUploadpath)){
  					mkdir($logoUploadpath);
				}

				if (file_exists(dirname(__FILE__)."/images/logo_login.png")) {
					unlink(dirname(dirname(dirname(__FILE__)))."/writable/images/logo/logo_login.png");
				}
				if (file_exists(dirname(__FILE__)."/images/logo_login.png.jpg")) {
					unlink(dirname(dirname(dirname(__FILE__)))."/writable/images/logo/logo_login.jpg");
				}
				if (file_exists(dirname(__FILE__)."/images/logo_login.jpeg")) {
					unlink(dirname(dirname(dirname(__FILE__)))."/writable/images/logo/logo_login.jpeg");
				}
				if(move_uploaded_file($_FILES["file"]["tmp_name"],dirname(dirname(dirname(__FILE__)))."/writable/images/logo/logo_login.$extension")){
					$_SESSION['cometchat']['error'] = 'Logo uploaded successfully';
	        	}
			}

			if(!empty($_SESSION['cometchat']['error'])){
				echo '<script type="text/javascript">parent.location.reload();</script>'; exit;
			}

		}
		header("Location:?module=dashboard&action=loadexternal&type=extension&name=desktop");
	}
}
