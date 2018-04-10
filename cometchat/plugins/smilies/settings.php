<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."config.php");

if (!defined('CCADMIN')) { echo "NO DICE"; exit; }
$extrajs = '';
$baseUrl = BASE_URL;
if (isset($_SESSION['cometchat']['error']) && !empty($_SESSION['cometchat']['error'])) {
	$extrajs = <<<EOD
<style>
	#alert {
		overflow: hidden;
		width: 100%;
		text-align: center;
		position: fixed;
		top: 0;
		left: 0;
		background-color: #76B6D2;
		height: 0;
		color: #fff;
		font: 15px/30px arial, sans-serif;
		opacity: .9;
	}
</style>
<script>
	$(function() {
		$.fancyalert('{$_SESSION['cometchat']['error']}');
	});

	(function($){

		$.fancyalert = function(message){
			if ($("#alert").length > 0) {
				removeElement("alert");
			}

			var html = '<div id="alert">'+message+'</div>';
			$('body').append(html);
			alertelement = $('#alert');
			if(alertelement.length) {
				var alerttimer = window.setTimeout(function () {
					alertelement.trigger('click');
				}, 5000);
				alertelement.css('border-bottom','4px solid #76B6D2');
				alertelement.animate({height: alertelement.css('line-height') || '50px'}, 200)
				.click(function () {
					window.clearTimeout(alerttimer);
					alertelement.animate({height: '0'}, 200);
					alertelement.css('border-bottom','0px solid #333333');
				});
			}
		};
	})($);
</script>
EOD;
	unset($_SESSION['cometchat']['error']);
}
$base_url = BASE_URL;

if (empty($_GET['process'])) {
	global $uploaded_smileys;
	global $smileys;
	global $smlWidth;
	global $smlHeight;
	$extrajs .= '<script> var smileys = {};';
	foreach ($smileys as $code => $value) {
		$code = str_replace("\\","\\\\",$code);
		$extrajs .= 'smileys["'.$code.'"] = "'.$value.'";';

	}
	$extrajs .= '</script>';
	$used = array();
	$customSmilies = '';
	foreach ($uploaded_smileys as $pattern => $result) {
		if (!empty($used[$result])) {
		} else {
			$pattern2 = str_replace("'","\\'",$pattern);
			$title = str_replace("-"," ",ucwords(preg_replace("/\.(.*)/","",$result)));

			$customSmilies .= '<div class="smilies"><div class="sm-img"><img class="custom_smiley" width="100%" height="100%" src="'.BASE_URL.'writable/images/smileys/'.$result.'" /><input type="file" class="imgUpload" accept="image/x-png, image/gif, image/jpeg" onchange="imgUpload(this,\''.$pattern.'\');" /></div><div class="sm-code"><input type="text" value="'.$pattern.'" readonly orignal="'.$pattern.'" rel="'.$result.'"/></div><div class="sm-delete" rel="'.$pattern.'" imgUrl="'.$result.'"></div></div>';

			$used[$result] = 1;
		}
	}
$staticCDNUrl = STATIC_CDN_URL;
$jqueryjstag =  getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'jquery', 'ext' => 'js'));
$scrolljstag =  getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'scroll', 'ext' => 'js'));
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
		{$jqueryjstag}
		<style type="text/css">
			fieldset {
				border: 1px solid #ccc;
				padding: 10px 5px;
				width: 310px;
			}
			legend {
				font-size: 12px;
				color: #333;
			}
			#allSm {
				height: 200px;
				width: 100% !important;
				overflow: hidden;
			}
			.smilies {
				float: left;
				width: 65px;
				height: 55px;
				text-align: center;
				position: relative;
				margin: 5px;
				border: 1px solid #ccc;
				overflow: hidden;
			}
			.sm-img {
				display: inline-block;
				height: 20px;
				width: 20px;
				margin: 5px;
				position: relative;
				overflow: hidden;
			}
			.sm-code {
				display: inline-block;
				height: 20px;
				width: 100%;
			}
			.sm-code input {
				width: 90% !important;
				border: 0;
				outline: 0;
				background: transparent;
				margin: 0;
				height: 100%;
				padding: 0;
				text-align: center;
			}
			.sm-delete {
				position: absolute;
				background: url('../images/x_icon.gif') no-repeat;
				height:15px;
				width: 15px;
				background-size: 100%;
				top: 0px;
				right: 0px;
				display: none;
				cursor: pointer;
			}
			.smilies:hover .sm-delete {
				display: block;
			}
			.smilies:hover .newSmDelete {
				display: none !important;
			}
			.imgUpload {
				width: 100%;
				height: 100%;
				position: absolute;
				left: 0;
				top: 0;
				opacity: 0;
				cursor: pointer;
			}
			.enable {
				background: white !important;
				border: 1px solid #c6c6c6 !important;
			}
			.invalid {
				border: 1px solid red !important;
			}
			.valid {
				border: 1px solid green !important;
			}
			.sm-newImg {
				margin: 15px;
				height: 25px;
				width: 25px;
			}
			.config_error {
				font-family: "Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;
				font-size: 14px;
			}
		</style>
		<script type="text/javascript">
			$ = jQuery = jqcc;
			function resizeWindow() {window.resizeTo(650, ($('form').outerHeight(false)+window.outerHeight-window.innerHeight)); }
		</script>
	</head>
	<body class="navbar-fixed sidebar-nav fixed-nav" style="background-color: white;overflow-y:hidden;">
		<div class="col-sm-6 col-lg-6">
			<div class="card">
				<div class="card-block">
					<form style="height:100%" action="?module=dashboard&action=loadexternal&type=plugin&name=smilies&process=true" method="post" id="smilies" enctype="multipart/form-data">
						<div class="col-md-12">
							<div id="content" style="width:auto">
								<div id="centernav" style="width:380px;display:none;">
									<div class="title">Add New Smiley:</div>
									<div class="element"><input type="checkbox" class="inputbox" name="addSm" id="addSm" style="width: auto;"></div>
									<div style="clear:both;padding:10px;"></div>
								</div>
								<div style="overflow: hidden; display:none;" id="newSm">
									<div id="centernav" style="width:380px">
										<div class="title">Code:</div>
										<div class="element"><input type="text" class="inputbox" name="smCode"></div>
										<div style="clear:both;padding:10px;"></div>
										<div class="title">Upload Image:</div>
										<div class="element"><input type="file" class="inputbox" name="smImg" accept="image/x-png, image/gif, image/jpeg"></div>
									</div>
								</div>
								<div id="customSmilies">
									<div id="allSm">
										{$customSmilies}
									</div>
								</div>
							</div>
						</div>
						<div class="row col-md-4">
							<input type="submit" value="Add Smilies" class="btn btn-primary">
						</div>
					</form>
				</div>
			</div>
		</div>
	</body>
	{$scrolljstag}
	{$extrajs}
	<script type="text/javascript">
		$(function() {
			var addSmileyCode = '<div class="smilies" id="addedSm">'+
									'<div class="sm-img sm-newImg">'+
										'<img class="custom_smiley" width="100%" height="100%" src="{$staticCDNUrl}admin/images/plus.png" title="Upload New Smiley"/>'+
										'<input type="file" class="imgUpload newSmImg" accept="image/x-png, image/gif, image/jpeg" onchange="imgUpload(this,\'\',1);" title="Upload New Smiley" />'+
									'</div>'+
									'<div class="sm-code">'+
										'<input type="text" value="" readonly="" orignal="CC_SMILIES" rel="" class="newSmCode" />'+
									'</div>'+
									'<div class="sm-delete newSmDelete" rel="" imgurl=""></div>'+
								'</div>';
			var element = '';
			var newCode = '';
			var currCode = '';
			var currImg = '';
			var fileName = '';
			var regExp = /^[a-zA-Z0-9 ]*$/;

			$('#allSm').append(addSmileyCode).slimScroll({'width': '320px'});

			$('.sm-code input').live('focus', function() {
				if ($(this).hasClass('newSmCode')) {
					var newSmiley = $(this).parents('#addedSm').find('.imgUpload').get()[0].files.length;
					if (newSmiley > 0) {
						$(this).addClass('enable').removeAttr('readonly').removeClass('invalid');
					}
				} else {
					$(this).addClass('enable').removeAttr('readonly').removeClass('invalid');
				}
			});

			$('.sm-code input').live('keyup', function(e) {
				element = $(this);
				newCode = $.trim(element.val());
				currCode = element.attr('orignal');
				currImg = $('.imgUpload.active').parent().find('.custom_smiley').attr('name');
				fileName = $('.imgUpload.active').parent().find('.custom_smiley').attr('src');
				if (e.keyCode == 13) {
					e.preventDefault();
					$("form").submit();
				}
				var smCode = $.trim($(this).val());
				if (smCode == '' || smileys.hasOwnProperty(smCode) || regExp.test(smCode) == true) {
					$(this).removeClass('valid').addClass('invalid');
				} else {
					$(this).removeClass('invalid').addClass('valid');
				}
			});

			$("form").on('submit', function(e) {
				if ($('#addedSm').find('.sm-newImg').length <= 0) {
					if (newCode == '') {
						alert('Please enter valid code for new smiley.');
						return false;
					} else if (newCode.indexOf(" ") >= 0) {
						alert('The smiley code should not contain spaces.');
						return false;
					} else if (smileys.hasOwnProperty(newCode)) {
						alert('The smiley code is already exist. Please try with different code.');
						return false;
					} else if (regExp.test(newCode) == true) {
						alert('The smiley code should contain atleast 1 special character i.e; characters other than alphabets, numbers and spaces.');
						return false;
					} else if (currImg == '') {
						alert('Please upload image for new smiley.');
						return false;
					}
				}
				var newSmiley = 0;
				if (element.hasClass('newSmCode')) {
					newSmiley = element.parents('#addedSm').find('.imgUpload').get()[0].files.length;
				}
				element.removeClass('enable invalid valid').attr('readonly');
				if (newCode == '' && newSmiley > 0) {
					element.addClass('invalid').focus();
				} else if (newCode != currCode && newCode != '') {
					if (smileys.hasOwnProperty(newCode)) {
						element.addClass('invalid');
					} else {
						$.ajax({
							url: "?module=dashboard&action=loadexternal&type=plugin&name=smilies&process=true",
							type: "POST",
							data: {
								currCode: currCode,
								newCode: newCode,
								currImg: currImg,
								ajaxAction: 'code',
								fileName: fileName
							},
							success: function(res) {
								if (res) {
									$('.imgUpload').removeClass('active');
									element.attr('orignal', newCode);
									if (element.hasClass('newSmCode')) {
										element.parent().siblings().attr('rel', res).attr('imgurl', res).removeClass('newSmDelete');
										element.parents('.smilies').removeAttr('id');
										element.parents('.smilies').find('.sm-img input').removeClass('newSmImg');
										element.parents('.smilies').find('.sm-code input').removeClass('newSmCode');
										$('#allSm').append(addSmileyCode);
									}
								}
							}
						});
					}
				}
			});

			$('.sm-delete').live('click', function() {
				if (confirm("Are you sure you want to remove this smiley?")) {
					var element = $(this);
					var code = element.attr('rel');
					var imgUrl = element.attr('imgUrl');
					$.ajax({
						url: "?module=dashboard&action=loadexternal&type=plugin&name=smilies&process=true",
						type: "POST",
						data: {
							code: code,
							imgUrl: imgUrl,
							ajaxAction: 'del'
						},
						success: function(res) {
							if (res == 1) {
								element.parent().remove();
								delete smileys[code];
							}
						}
					});
				}
			});
		});

		function imgUpload(elem,code,newSmiley) {
			var fd = new FormData();
			fd.append("newImg", elem.files[0]);
			fd.append("ajaxAction", 'img');
			if (newSmiley == 1) {
				$('.newSmCode').attr('rel',elem.files[0].name).click().focus();
				code = 'CC_SMILIES';
			}
			$(elem).addClass('active').parent().removeClass('sm-newImg');
			if (code != '' && code != 'undefined') {
				fd.append("code", code);
				var xhr = new XMLHttpRequest();
				xhr.elem = elem;
				xhr.onreadystatechange=function(){
					if (xhr.readyState==4 && xhr.status==200){
						if(xhr.responseText=='CC^CONTROL_error'){
							document.body.innerHTML = '<div class="config_error">Unable to upload image to writable/images/smileys directory. Please check file permission of your writable/images/smileys directory. Please try 755/777/644</div>';
						}else{
							imgPreview(xhr.responseText);
						}
					}
				}
				xhr.open("POST", "?module=dashboard&action=loadexternal&type=plugin&name=smilies&process=true");
				xhr.send(fd);
			}
		}

		function imgPreview(input) {
			$('.imgUpload.active').parent().find('.custom_smiley').attr('src', '{$baseUrl}writable/images/smileys/'+input);
			$('.imgUpload.active').parent().find('.custom_smiley').attr('name', input);
		}
	</script>
EOD;
} else {
	global $uploaded_smileys;
	global $smileys;

	$error = 1;
	$timestamp = time();
	if (!empty($_POST['ajaxAction']) && $_POST['ajaxAction'] == 'code') {
		$error = 0;
		if ($_POST['currCode'] != 'CC_SMILIES') {
			$uploaded_smileys[$_POST['newCode']] = $uploaded_smileys[$_POST['currCode']];
			unset($uploaded_smileys[$_POST['currCode']]);
		} else {
			$uploaded_smileys[$_POST['newCode']] = $_POST['currImg'];
		}
	} elseif (!empty($_POST['ajaxAction']) && $_POST['ajaxAction'] == 'img') {
		$fileName = $timestamp.'_'.$_FILES['newImg']['name'];
		if (@move_uploaded_file($_FILES['newImg']['tmp_name'], dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."writable".DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."smileys".DIRECTORY_SEPARATOR.$fileName)) {
			if ($_POST['code'] != 'CC_SMILIES') {
				$uploaded_smileys[$_POST['code']] = $fileName;
				$error = 0;
			}
		} else {
			echo 'CC^CONTROL_error';
			exit;
		}
		echo $fileName;
	} elseif (!empty($_POST['ajaxAction']) && $_POST['ajaxAction'] == 'del') {
		unset($uploaded_smileys[$_POST['code']]);
		unlink(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."writable".DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."smileys".DIRECTORY_SEPARATOR.$_POST['imgUrl']);
		$error = 0;
		echo 1;
	}

	if (!$error) {
		configeditor(array('uploaded_smileys' => $uploaded_smileys));
		if (empty($_POST['ajaxAction'])) {
			$_SESSION['cometchat']['error'] = 'Smiley added successfully';
		}
	}
	if (!empty($_POST['smlWidth']) && !empty($_POST['smlHeight'])) {
		configeditor(array('smlWidth' => $_POST['smlWidth']));
		configeditor(array('smlHeight' => $_POST['smlHeight']));
	}
	if (empty($_POST['ajaxAction'])) {
		header("Location:?module=dashboard&action=loadexternal&type=plugin&name=smilies");
	}
}
