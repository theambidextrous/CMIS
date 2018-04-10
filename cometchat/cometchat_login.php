<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."writable".DIRECTORY_SEPARATOR."custom".DIRECTORY_SEPARATOR."cometchat_login.php")){
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."writable".DIRECTORY_SEPARATOR."custom".DIRECTORY_SEPARATOR."cometchat_login.php");
} else {
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."cometchat_init.php");
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."extensions".DIRECTORY_SEPARATOR."desktop".DIRECTORY_SEPARATOR."config.php");
	if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."extensions".DIRECTORY_SEPARATOR."desktop".DIRECTORY_SEPARATOR."lang.php")){
		include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."extensions".DIRECTORY_SEPARATOR."desktop".DIRECTORY_SEPARATOR."lang.php");
	}


if(checkLicenseVersion()&& !empty($_REQUEST['callbackfn']) && $_REQUEST['callbackfn']=='desktop'){
	//while label desktop app validity
	$errPlatform = "White Labelled Desktop Apps";
	$platform = 'whitelabelleddesktopmessenger';
	if(!empty($_REQUEST['stockapp']) && $_REQUEST['stockapp'] ==1){
		$errPlatform = "Desktop Apps";
		$platform = 'desktopmessenger';
	}
	if(checkplan('platforms',$platform) == 0){
		echo $errPlatform." are not included in your plans, Please upgrade your plan.";
	    exit;
	}
}
if(!empty($_REQUEST['socialLogin'])){

	if(!empty($_REQUEST['callbackfn']) && $_REQUEST['callbackfn'] == 'mobileapp') {
		$social_details = json_decode($_REQUEST['social_details']);
	} else {
		$social_details = (object) $_REQUEST['social_details'];
	}

	$userid = socialLogin($social_details);
	sendCCResponse(json_encode(array('userid'=>$userid,'v'=>$currentversion,'basedata'=>encryptUserid($userid))));
	exit;
}
	if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."colors".DIRECTORY_SEPARATOR."color.php")){
		include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."colors".DIRECTORY_SEPARATOR."color.php");
	}

	global $ccactiveauth, $uniqueguestname;
	$forgot_link='';
	$signUp_link='';
	$logo_url='';
	$guest_mode='';
	$container='';
	$authpopup = '';
	$checkGuestName = 0;

	if (!empty($_REQUEST['guest_login'])) {
		$query = sql_query('checkGuestName',array('name'=>trim($_REQUEST['username'])));
		if(sql_num_rows($query) > 0 && $uniqueguestname == 1){
			$checkGuestName = 1;
		}
	}

	if((!empty($_GET['process']) && $_GET['process']=="1") || (!empty($_GET['guest_login']) && $_GET['guest_login']=='1')){
		if(!empty($_REQUEST['username']) && (!empty($_REQUEST['password']) || $_REQUEST['social_details'])&&(!empty($_REQUEST['callbackfn']) && in_array($_REQUEST['callbackfn'], array('desktop','mobileapp')))) {
			if (!$checkGuestName){
				$userid = chatLogin($_REQUEST['username'], $_REQUEST['password']);
				$userid = $userid == null ? "0" : $userid;
				$response = array();
				$response['basedata'] = strval($userid);
				$response['version'] = $currentversion;
				if(defined('ROLE_BASE_ACCESS') && ROLE_BASE_ACCESS == 1 && $userid>0){
					$platformType = $_REQUEST['callbackfn'];
					if (empty($platformType)) {
						$platformType = 'web';
					}
					$getRole = getRole($userid);
					if ($GLOBALS[$getRole."_disabled".$platformType] == 1) {
						$response = array("basedata"=>"0","error_msg"=>$language['membership_msg']);
					}
					if ($GLOBALS[$getRole."_disabledcc"] == 1) {
						$response = array("basedata"=>"0","error_msg"=>$language['membership_msg']);
					}
				}
			}else{
				$response = array("basedata"=>"0","error_msg"=>$_REQUEST['username']." ".$language['guest_already_exist']);
			}
		} else {
			$response = array("basedata"=>"0");
		}
		if(!empty($_GET['callback'])){
			header('Content-type: application/javascript; charset=utf-8');
			echo $_GET['callback'].'('.json_encode($response).');';
		}else{
			echo json_encode($response);
		}
		exit;
	}
	if(!empty($forgot_url)){
	$forgot_link = "
		<div id='forgotBox' class='divBox'>
			<span id='forgotPass' name='remember'>{$desktop_language[4]}</span>
		</div>";
	}
	if(!empty($signUp_url)){
	$signUp_link = "
		<div id='signUpBox'  class='divBox'>
			<span id='signUpSpan'>
			    <input type='submit' id='signUp' name='signUp' value='".$desktop_language[5]."'/>
			</span>
		</div>";
	}
	if($branded){
		$logo_url = "https://chat.phpchatsoftware.com/cometchat/extensions/desktop/images/logo_login.png";
	}else{
		$logo_url = STATIC_CDN_URL."writable/images/logo/logo_login.png";
	}
	if($guestsMode){
		$guest_mode = "<div id='clear'></div>
					<input type='submit' id='guest_enter' name='guest_enter' value='".$desktop_language[6]."'/>";
	}

	$baseurl=BASE_URL;

	if(checkAuthMode('social')){
		$container ="
			<script type='text/javascript'>
				window.location.href='{$baseurl}cometchat_embedded.php?callbackfn=desktop';
			</script>";
	}else{
		$container='<div id="companyLogoDiv"  unselectable="on">
					<div id="companyLogoSpan" unselectable="on"><img id="comapanyImage" src="'.$logo_url.'" width="150px" heigth="100px" alt="Company Logo" oncontextmenu="return false;" unselectable="on"/>
					</div>
				</div>
				<div id="loginInfoContainer">
					<div id="site_container">
						<div id="userNameBox" class="divBox">
							<input type="text" id="username" name="username" placeholder="'.$desktop_language[0].'"/>
						</div>
						<div id="passwordBox" class="divBox">
							<input type="password" id="password" name="password" placeholder="'.$desktop_language[1].'"/>
						</div>
						<div  class="divBox">
							<span class="rememberPasswordSpan">
								<input id="remember" type="checkbox" name="rem_pswd" value="password" />
							</span>
							<span id="remPass" name="remember" class="rememberPasswordSpan" style="line-height:20px;">'.$desktop_language[2].'</span>
						</div>
						<div id="signInBox"  class="divBox">
							<span id="signInSpan">
								<input type="submit" id="signIn" name="Login" value="'.$desktop_language[3].'"/>
								'.$guest_mode.'
							</span>
						</div>
					</div>
					<div id="guest_container">
						<div id="GuestBox">
							<div id="guestNameBox">
								<input type="text" id="guest_name" name="guest_name" placeholder="'. $desktop_language[7].'">
							</div>
							<div id="clear"></div>
							<input type="submit" id="guest_login" name="guest_login" value="'.$desktop_language[3].'"/>
						</div>
					</div>
					'.$forgot_link.'
					<div id="loadingDiv" class="divBox">
						<img id="loading" src="'.STATIC_CDN_URL.'extensions/desktop/images/loading.gif" alt="loading"/>
					</div>
					<div id="back">
						<img src="'.STATIC_CDN_URL.'extensions/desktop/images/back.png" height="25px" width="30px">
					</div>
					'.$signUp_link.'
				</div>';
	}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>CometChat Messenger</title>
	<?php echo getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'jquery', 'ext' => 'js')); ?>
	<style>
		body{
			height:100%;
			width: 100%;
			background-color: <?php echo $login_background; ?>;
		}

		input:-webkit-autofill, textarea:-webkit-autofill, select:-webkit-autofill{
			-webkit-appearance: none;
			margin: 0;
			background-color: #FFFFFF !important;
		    background-image: none;
		    color: <?php echo $layoutSettings['primary_color']; ?> !important;
		}
		#mainContainer {
			overflow: hidden;
			background: white;
			position: absolute;
			background-color: <?php echo $login_background; ?>;
		}
		#companyLogoDiv {
			float: left;
			width: 100%;
			margin-top: 40px;
			margin-bottom: 20px;
			margin-left: 15px;
		}
		#companyLogoSpan {
			margin: 0 auto;
		    width: 200px;
		}
		.divBox {
			float: left;
			width: 100%;
			margin:5px auto;
		}
		#forgotPasswordSpan {
			float: left;
			width: 100%;
			text-align: center;
			font-size: 11px;
		}
		#signInSpan {
			float: left;
			width: 100%;
			text-align: center;
		}
		#username {
			height: 18px;
			width: 160px;
			border: none;
			color: <?php echo $login_foreground_text; ?>;
			background-color: <?php echo $login_background; ?>;
		}
		#userNameBox{
			border: 0px solid <?php echo $layoutSettings['primary_color']; ?>;
		    border-bottom-width: 1px;
		    background-color: transparent;
		}
		#userNameSpan {
			height: 25px;
			line-height: 25px;
		}
		#password{
			height: 18px;
			width: 160px;
			border: none;
			color: <?php echo $login_foreground_text; ?>;
			background-color: <?php echo $login_background; ?>;
		}
		#passwordBox{
			border: 0px solid <?php echo $layoutSettings['primary_color']; ?>;
		    border-bottom-width: 1px;
		    background-color: transparent;
		}
		#passwordSpan {
			height: 25px;
			line-height: 25px;
		}
		#signIn {
			margin: 0 auto;
			width: 180px;
			border : none;
			padding: 5px;
			background-color: <?php echo $layoutSettings['primary_color']; ?>;
			color : <?php echo $login_button_text; ?>;
			font-family: "Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;
		    font-size: 13px;
		    padding: 6px 10px;
		    border-radius: 15px;
		}
		#loginInfoContainer {
			margin: 0 auto;
			width: 180px;
			height: 226px;
			font: 15px arial,sans-serif;
			font-size: 12px;
			color : <?php echo $login_button_text; ?>;
		}
		.rememberPasswordSpan {
			margin: 0 auto;
			float: left;
			height: 20px;
			color: <?php echo $layoutSettings['primary_color']; ?>
		}
		#companyLogoDiv img{
			margin-left: 6px;
		}
		#clear{
			margin-bottom: 10px;
		}
		#username,#password,#guest_name,#signIn:focus {
		  outline:none;
		}
		#loadingDiv{
			visibility: hidden;
		}
		img{
			display:block;
			margin: 0 auto;
			cursor: pointer;
		}
		#remember,#remPass,#signIn:hover,#forgot:hover,#signUp:hover,#forgotPass:hover {
			cursor: pointer;
		}
		#remember,#remPass,#signIn:hover,#forgot:hover,#signUp:hover,#forgotPass:hover {
			outline: none;
		}
		#remember{
			margin-left: 0px;
		}
		#forgot {
            margin: 0 auto;
            width: 180px;
            font-weight: bold;
            border : none;
            padding: 5px;
            background-color: <?php echo $layoutSettings['primary_color']; ?>;
            color : <?php echo $login_button_text; ?>;
        }
        #forgotBox {
        	text-align: center;
            color: <?php echo $layoutSettings['primary_color']; ?>
        }
        #signUpSpan {
            float: left;
            width: 100%;
            text-align: center;
        }
        #signUp {
            margin: 0 auto;
            width: 180px;
            padding: 5px;
            color: <?php echo $layoutSettings['primary_color']; ?>;
            background-color : <?php echo $login_button_text; ?>;
            border: 2px solid <?php echo $layoutSettings['primary_color']; ?>;
            font-family: "Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;
		    font-size: 13px;
		    padding: 6px 10px;
		    border-radius: 15px;
        }
		::-webkit-input-placeholder { /* WebKit, Blink, Edge */
		    color: <?php echo $login_placeholder; ?>;
		}
		/*****Guest Changes*****/
		#site_container{
			display: block;
		}
		#guest_container{
			display: none;
			position: relative;
    		margin-top: 145px;

		}
		#guestNameBox{
			border: 0px solid #000000;
		    border-bottom-width: 1px;
		    background-color: transparent;
		}
		#guest_enter{
			margin: 0 auto;
			width: 180px;
			border : none;
			padding: 5px;
			background-color: <?php echo $layoutSettings['primary_color']; ?>;
			color : #FFFFFF;
			cursor: pointer;
			font-family: "Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;
		    font-size: 13px;
		    padding: 6px 10px;
		    border-radius: 15px;
		}
		#guest_login{
			/*display: none;*/
			margin: 0 auto;
			width: 180px;
			border : none;
			padding: 5px;
			background-color: <?php echo $layoutSettings['primary_color']; ?>;
			color : #FFFFFF;
			cursor: pointer;
			font-family: "Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;
		    font-size: 13px;
		    padding: 6px 10px;
		    border-radius: 15px;
		}
		#guest_name{
			border: 0px solid #000000;
		    border-bottom-width: 1px;
		    background-color: transparent;
		}
		#back{
			display: none;
			width: 180px;
		    margin: 0px auto;
		}
		#guest_name{
			height: 18px;
			width: 160px;
			border: none;
			color: <?php echo $login_foreground_text; ?>;
			background-color: <?php echo $login_background; ?>;
		}
		/***********************/
	</style>
</head>
<body>
	<div id="mainContainer">
		<?php echo $container; ?>
	</div>
	<script>
	document.onkeydown = function (event) {
		var keyCode = event.keyCode;
		if (keyCode == 8 &&
			((event.target || event.srcElement).tagName != "TEXTAREA") &&
			((event.target || event.srcElement).tagName != "INPUT")) {
			return false;
		}
	};
	document.addEventListener("dragover",function(e){
	   e = e || event;
	   e.preventDefault();
	},false);
	document.addEventListener("drop",function(e){
		e = e || event;
		e.preventDefault();
	},false);
	</script>
	<script>
	var basepath = '<?php echo BASE_URL; ?>';
	var staticCDNUrl = '<?php echo STATIC_CDN_URL; ?>';
	jqcc(function() {
	    jqcc(window).on('resize', function resize()  {
	        jqcc(window).off('resize', resize);
	        setTimeout(function () {
	            var content = jqcc('#mainContainer');
	            var top = (window.innerHeight - content.height()) / 2;
	            var left = (window.innerWidth - content.width()) / 2;
	            content.css('top', Math.max(0, top) + 'px');
	            content.css('left', Math.max(0, left) + 'px');
	            jqcc(window).on('resize', resize);
	        }, 50);
	    }).resize();
	});
	jqcc("#password").keydown(function(e) {
		var message = jqcc('#site_url').val();
		if (e.keyCode == 13) {
			chatboxkeyDown(message,2);
		}
	});
	jqcc('#username, #password, #guest_name').keyup(function(e){
		if (e.keyCode != 13) {
			if(jqcc('#main_error').length>0){
				jqcc('#main_error').remove();
			}
		}
	})
	jqcc('#remPass').click(function(){
		if(jqcc("#remember").is(':checked')){
			jqcc('#remember').attr('checked', false);
		}else{
			jqcc('#remember').attr('checked', true);
		}
	});
	jqcc('#signIn').live('click',function(event){
		var message = jqcc('#site_url').val();
		chatboxkeyDown(message,2);

	});
	jqcc('#forgotPass').click(function(){
		var forgot_url = "<?php echo $forgot_url; ?>";
		var controlparameters = {"type":"extensions", "name":"desktop", "method":"forgot_pass", "params":{"forgot_url":forgot_url}};
		controlparameters = JSON.stringify(controlparameters);
		parent.postMessage('CC^CONTROL_'+controlparameters,'*');
	});
	jqcc('#signUp').live('click',function(event){
		var signUp_url = "<?php echo $signUp_url; ?>";
		var controlparameters = {"type":"extensions", "name":"desktop", "method":"signup", "params":{"signup_url":signUp_url}};
		controlparameters = JSON.stringify(controlparameters);
		parent.postMessage('CC^CONTROL_'+controlparameters,'*');
	});
	jqcc('#guest_enter').click(function(){
		jqcc('#site_container').css('display','none');
		jqcc('#forgotPass').css('display','none');
		jqcc('#signUp').css('display','none');
		jqcc('#guest_container').css('display','block');
		jqcc('#back').css('display','block');
		if(jqcc('#main_error').length>0){
			jqcc('#main_error').remove();
		}
	});
	jqcc('#guest_login').click(function(){
		jqcc('loadingDiv').css('display', 'block');
	})
	jqcc('#back').click(function(){
		jqcc('#site_container').css('display','block');
		jqcc('#guest_container').css('display','none');
		jqcc('#forgotPass').css('display','block');
		jqcc('#signUp').css('display','block');
		jqcc('#back').css('display','none');
		if(jqcc('#main_error').length>0){
			jqcc('#main_error').remove();
		}
	});
	jqcc('#guest_login').click(function(){
		var guestName = jqcc('#guest_name').val();
		chatboxkeyDown(guestName,1);
	});
	jqcc("#guest_name").keydown(function(e) {
		var guestName = jqcc('#guest_name').val();
		if (e.keyCode == 13) {
			chatboxkeyDown(guestName,1);
		}
	});
	function chatboxkeyDown(message,textbox) {
		if(textbox==1){
			var uName = message;
			if(uName!=""){
				jqcc("#loadingDiv").css('visibility','visible');
				jqcc('#back').css('display','none');
				setTimeout(function(){
					jqcc.ajax({
						data:{username: uName, password: 'CC^CONTROL_GUEST', 'guest_login':1,  callbackfn:'desktop'},
						success: function(data){
							data = JSON.parse(data);
							error_msg = data.error_msg;
							data = data.basedata;
							if(data!=0){
								var controlparameters = {"type":"extensions", "name":"desktop", "method":"guest_login", "params":{"guest_id":data}};
			            		controlparameters = JSON.stringify(controlparameters);
			            		parent.postMessage('CC^CONTROL_'+controlparameters,'*');
								window.location.href=basepath+'cometchat_embedded.php?basedata='+data+'&callbackfn=desktop';
							}else{
								jqcc("#loadingDiv").css('visibility','hidden');
								jqcc("#back").css('display','block');
								jqcc("#guest_name").focus();
								alert(error_msg);
							}
						},error: function(e){
							console.log(e);
						}
					});
				},3000);
			}else{
				if(jqcc('#main_error').length==0){
					jqcc('#guestNameBox').append('<div id="main_error" style="display:inline"><div id="error" style="float:right; display:inline-block;height:16px;width:16px;"><img src="'+staticCDNUrl+'extensions/desktop/images/error.png" height="16px" width="16px"></img></div><div class="arrow-up" style="width: 0;height: 0;border-left: 5px solid transparent;border-right: 5px solid transparent;border-bottom: 5px solid red;position: relative; float: right;right: 3px;"><hr style="border-color: red;border-style: inset;border-width: 1px;margin: 0px;width: 142px;float: right;position: relative;top: 6px;right:-8px;"><div id="error" style="float:right; display:inline-block;position: relative;height: auto;width: 138px;color: #fff;background-color: #000;z-index: 10000;padding:3px;top:6px;right:-8px;"><?php echo $desktop_language[12];?></div></div>');
				}
			}
		}else{
			var uName = jqcc('#username').val();
			var password = jqcc('#password').val();
			if(uName!="" && password==""){
				jqcc('#password').val('');
				if(jqcc('#main_error').length==0){
					jqcc('#passwordBox').append('<div id="main_error" style="display:inline"><div id="error" style="float:right; display:inline-block;height:16px;width:16px;"><img src="'+staticCDNUrl+'extensions/desktop/images/error.png" height="16px" width="16px"></img></div><div class="arrow-up" style="width: 0;height: 0;border-left: 5px solid transparent;border-right: 5px solid transparent;border-bottom: 5px solid red;position: relative; float: right;right: 3px;"><hr style="border-color: red;border-style: inset;border-width: 1px;margin: 0px;width: 132px;float: right;position: relative;top: 6px;right:-8px;"><div id="error" style="float:right; display:inline-block;position: relative;height: auto;width: 128px;color: #fff;background-color: #000;z-index: 10000;padding:3px;top:6px;right:-8px;"><?php echo $desktop_language[9];?></div></div>');
				}
			}else if(uName=="" && password!=""){
				if(jqcc('#main_error').length==0){
					jqcc('#userNameBox').append('<div id="main_error" style="display:inline"><div id="error" style="float:right; display:inline-block;height:16px;width:16px;"><img src="'+staticCDNUrl+'extensions/desktop/images/error.png" height="16px" width="16px"></img></div><div class="arrow-up" style="width: 0;height: 0;border-left: 5px solid transparent;border-right: 5px solid transparent;border-bottom: 5px solid red;position: relative; float: right;right: 3px;"><hr style="border-color: red;border-style: inset;border-width: 1px;margin: 0px;width: 136px;float: right;position: relative;top: 6px;right:-8px;"><div id="error" style="float:right; display:inline-block;position: relative;height: auto;width: 132px;color: #fff;background-color: #000;z-index: 10000;padding:3px;top:6px;right:-8px;"><?php echo $desktop_language[10];?></div></div>');
				}
			}else if(uName!="" && password!=""){
				jqcc("#loadingDiv").css('visibility','visible');
				setTimeout(function(){
					jqcc.ajax({
						data:{username: uName, password: password, process: 1, callbackfn:'desktop'},
						success: function(data){
							data = JSON.parse(data);
							errorMsg=data.error_msg;
							data=data.basedata;
							if(data!=0){
								if(jqcc("#remember").is(':checked')){
									var controlparameters = {"type":"extensions", "name":"desktop", "method":"login", "params":{"dm_id":data}};
			            			controlparameters = JSON.stringify(controlparameters);
			            			parent.postMessage('CC^CONTROL_'+controlparameters,'*');
		            			}
		            			window.location.href=basepath+'cometchat_embedded.php?basedata='+data+'&callbackfn=desktop';
							}else{
								if (typeof(errorMsg) != 'undefined') {
									alert(errorMsg);
								}
								jqcc("#loadingDiv").css('visibility','hidden');
								jqcc('#username').val('');
								jqcc('#password').val('');
								if(jqcc('#main_error').length==0){
								jqcc('#userNameBox').append('<div id="main_error" style="display:inline"><div id="error" style="float:right; display:inline-block;height:16px;width:16px;"><img src="'+staticCDNUrl+'extensions/desktop/images/error.png" height="16px" width="16px"></img></div><div class="arrow-up" style="width: 0;height: 0;border-left: 5px solid transparent;border-right: 5px solid transparent;border-bottom: 5px solid red;position: relative; float: right;right: 3px;"><hr style="border-color: red;border-style: inset;border-width: 1px;margin: 0px;width: 134px;float: right;position: relative;top: 6px;right:-8px;"><div id="error" style="float:right; display:inline-block;position: relative;height: auto;width: 130px;color: #fff;background-color: #000;z-index: 10000;padding:3px;top:6px;right:-8px;"><?php echo $desktop_language[8];?></div></div>');
								}
							}
						}
					});
				},3000);
			}else{
				jqcc('#username').val('');
				jqcc('#password').val('');
				if(jqcc('#main_error').length==0){
				jqcc('#userNameBox').append('<div id="main_error" style="display:inline"><div id="error" style="float:right; display:inline-block;height:16px;width:16px;"><img src="'+staticCDNUrl+'extensions/desktop/images/error.png" height="16px" width="16px"></img></div><div class="arrow-up" style="width: 0;height: 0;border-left: 5px solid transparent;border-right: 5px solid transparent;border-bottom: 5px solid red;position: relative; float: right;right: 3px;"><hr style="border-color: red;border-style: inset;border-width: 1px;margin: 0px;width: 132px;float: right;position: relative;top: 6px;right:-8px;"><div id="error" style="float:right; display:inline-block;position: relative;height: auto;width: 128px;color: #fff;background-color: #000;z-index: 10000;padding:3px;top:6px;right:-8px;"><?php echo $desktop_language[11];?></div></div>');
				}
			}
		}
	}
		</script>
	</body>
</html>

<?php
}
?>
