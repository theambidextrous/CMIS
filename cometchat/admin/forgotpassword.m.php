<?php
/* 
* Send reset passwork email on client's email id & reset password 
*/

if (!defined('CCADMIN')) { echo "NO DICE"; exit; }

include_once (dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."config.php");
include_once (dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."cometchat_shared.php");
include_once (dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."php4functions.php");
include_once (dirname(__FILE__).DIRECTORY_SEPARATOR."shared.php");

/*
For Default Username & Password
*/
if(ADMIN_USER=='cometchat' && ADMIN_PASS=='cometchat'){
	header("Location: https://support.cometchat.com/kb/php/general-queries/forgot-cometchat-administrator-password/");
}

function forgotpassword($param=array()){
	/*
	* Forgot password form
	* To load html of forgot password
	* Params: none
	* Returns/Results load html body
	*/
	$staticCDNUrl = STATIC_CDN_URL;
	global $body;
	$body=<<<EOD
	<div class="outerframe">
		<div class="middleform">
			<div class="cometchat_logo_div">
				<img class="cometchat_logo_image" src="{$staticCDNUrl}/admin/images/logo.png" style="height:50px;" />
			</div>
				<div class="module form-module">
					<div class="form" >
						<h2 >Forgot Password</h2>
						<p>To reset your password, enter your email address and we will send you an email with instructions.</p>
						<form method="post" action="index.php?module=forgotpassword&action=sendemail">
							<input type="email" name="email" placeholder="Your email" required="true" />
							<button type="submit" value="Submit">Submit</button>
							<input type="hidden" name="currentTime" class="login_inputbox currentTime" />
							<div class="cometchat_forgotpwd"><a href="index.php">Cancel</a></div>
						</form>
					</div>
				</div>
			</div>
		</div>
EOD;
	template(1);
}

function resetpassword($param=array()){
	/*
	* Reset password form
	* to load html of Reset password
	* Params: none
	* Returns/Results load html body
	*/
	$staticCDNUrl = STATIC_CDN_URL;
	if($_REQUEST['key']==base64_encode(ADMIN_USER) && (time()-$_REQUEST['ts'] <='1800')){
		$_SESSION['cometchat']['reseturl']=$_SERVER['REQUEST_URI'];
		global $body;
		$body=<<<EOD
		<div class="outerframe">
			<div class="middleform">
				<div class="cometchat_logo_div">
					<img class="cometchat_logo_image" src="{$staticCDNUrl}/admin/images/logo.png" style="height:50px;" />
				</div>
				<div class="module form-module">
					<div class="form" >
						<h2 >Reset Password</h2>
						<form method="post" action="index.php?module=forgotpassword&action=resetpasswordprocess">
							<input type="password" name="ADMIN_PASS" placeholder="Password" required="true" />
							<input type="password" name="repassword" placeholder="Re-enter password" required="true" />
							<button type="submit" value="reset">Reset</button>
							<input type="hidden" name="currentTime" class="login_inputbox currentTime" />
							<div class="cometchat_forgotpwd"><a href="index.php">Cancel</a></div>
						</form>
					</div>
				</div>
			</div>
		</div>
EOD;
	template(1);
	}else{
		$_SESSION['cometchat']['error'] = 'Invalid URL';
		$_SESSION['cometchat']['type'] = 'alert';
		header("Location:index.php");
	}
}

function sendemail($param=array()){
	/*
	* Sending reset password mail
	* Send mail
	* Params: none
	* Returns/Results load html body
	*/
	if($_POST['email']==ADMIN_USER){
		$resetlink		 = $_SERVER['HTTP_HOST'] . strtok($_SERVER["REQUEST_URI"],'?') . '?module=forgotpassword&action=resetpassword&key=' . base64_encode(ADMIN_USER) . '&ts=' . time();
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$to			= ADMIN_USER;
		$subject = "Reset CometChat Admin Panel Password";
		$emailtemplate=emailtemplate($resetlink);

		$status = mail($to, $subject, $emailtemplate, $headers);
		if ($status){
			$_SESSION['cometchat']['error'] = 'Email sent successfully.';
			header("Location:index.php");
		}else{
			$_SESSION['cometchat']['error'] = 'Invalid Email OR SMTP not configured.';
			$_SESSION['cometchat']['type'] = 'alert';
			header("Location:index.php");
		}
	}else{
		$_SESSION['cometchat']['error'] = 'Email not matched.';
		$_SESSION['cometchat']['type'] = 'alert';
		header("Location:index.php");
	}
}

function resetpasswordprocess($param=array()){
	/*
	* Reset password process
	* Update admin password
	* Params: none
	* Returns/Results Update admin password
	*/
	if($_POST['ADMIN_PASS']==$_POST['repassword']){
		if(!empty($_POST['ADMIN_PASS'])){
			$_SESSION['cometchat']['error'] = 'Password successfully modified';
			configeditor(array('ADMIN_PASS' => sha1($_POST['ADMIN_PASS'])));
			header("Location:index.php");
		}
	}
	else{
		$_SESSION['cometchat']['error'] = 'Re-entered password does not match.';
		$_SESSION['cometchat']['type'] = 'alert';
		$link=$_SESSION['cometchat']['reseturl'];
		header("Location:".$link);
	}
}

function emailtemplate($resetlink){
	/*
	* Eemail template
	* To send email in proper html format
	* Params: 1, $resetpasswordlink= link to resetpassword
	* Returns/Results load html body
	*/

	return <<<EOD
<!DOCTYPE html">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="width=device-width" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>CometChat Admin Panel password reset</title>
<style>
	* {
		margin: 0;
		font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
		box-sizing: border-box;
		font-size: 14px;
	}

	img {
		max-width: 100%;
	}

	body {
		-webkit-font-smoothing: antialiased;
		-webkit-text-size-adjust: none;
		width: 100% !important;
		height: 100%;
		line-height: 1.6em;
	}

	table td {
		vertical-align: top;
	}

	body {
		background-color: #f6f6f6;
	}

	.body-wrap {
		background-color: #f6f6f6;
		width: 100%;
	}

	.container {
		display: block !important;
		max-width: 600px !important;
		margin: 0 auto !important;
		clear: both !important;
	}

	.content {
		max-width: 600px;
		margin: 0 auto;
		display: block;
		padding: 20px;
	}

	.main {
		background-color: #fff;
		border: 1px solid #e9e9e9;
		border-radius: 3px;
	}

	.content-wrap {
		padding: 20px;
	}

	.content-block {
		padding: 0 0 20px;
	}

	.header {
		width: 100%;
		margin-bottom: 20px;
	}

	.footer {
		width: 100%;
		clear: both;
		color: #999;
		padding: 20px;
	}
	.footer p, .footer a, .footer td {
		color: #999;
		font-size: 12px;
	}

	h1, h2, h3 {
		font-family: "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
		color: #000;
		margin: 40px 0 0;
		line-height: 1.2em;
		font-weight: 400;
	}

	h1 {
		font-size: 32px;
		font-weight: 500;
	}

	h2 {
		font-size: 24px;
	}

	h3 {
		font-size: 18px;
	}

	h4 {
		font-size: 14px;
		font-weight: 600;
	}

	p, ul, ol {
		margin-bottom: 10px;
		font-weight: normal;
	}
	p li, ul li, ol li {
		margin-left: 5px;
		list-style-position: inside;
	}

	a {
		color: #348eda;
		text-decoration: underline;
	}

	.btn-primary {
		text-decoration: none;
		color: #FFF;
		background-color: #348eda;
		border: solid #348eda;
		border-width: 10px 20px;
		line-height: 2em;
		font-weight: bold;
		text-align: center;
		cursor: pointer;
		display: inline-block;
		border-radius: 5px;
		text-transform: capitalize;
	}

	.last {
		margin-bottom: 0;
	}

	.first {
		margin-top: 0;
	}

	.aligncenter {
		text-align: center;
	}

	.alignright {
		text-align: right;
	}

	.alignleft {
		text-align: left;
	}

	.clear {
		clear: both;
	}

	.alert {
		font-size: 16px;
		color: #fff;
		font-weight: 500;
		padding: 20px;
		text-align: center;
		border-radius: 3px 3px 0 0;
	}
	.alert a {
		color: #fff;
		text-decoration: none;
		font-weight: 500;
		font-size: 16px;
	}
	.alert.alert-warning {
		background-color: #FF9F00;
	}
	.alert.alert-bad {
		background-color: #D0021B;
	}
	.alert.alert-good {
		background-color: #68B90F;
	}

	/* -------------------------------------
			RESPONSIVE AND MOBILE FRIENDLY STYLES
	------------------------------------- */
	@media only screen and (max-width: 640px) {
		body {
			padding: 0 !important;
		}

		h1, h2, h3, h4 {
			font-weight: 800 !important;
			margin: 20px 0 5px !important;
		}

		h1 {
			font-size: 22px !important;
		}

		h2 {
			font-size: 18px !important;
		}

		h3 {
			font-size: 16px !important;
		}

		.container {
			padding: 0 !important;
			width: 100% !important;
		}

		.content {
			padding: 0 !important;
		}

		.content-wrap {
			padding: 10px !important;
		}

		.invoice {
			width: 100% !important;
		}
	}
</style>
</head>
<body itemscope itemtype="http://schema.org/EmailMessage">
	<table class="body-wrap">
		<tr>
			<td></td>
			<td class="container" width="600">
				<div class="content">
					<table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction">
						<tr>
							<td class="content-wrap">
								<meta itemprop="name" content="Confirm Email"/>
								<table width="100%" cellpadding="0" cellspacing="0">
									<tr>
										<td class="content-block aligncenter">
											<img src='https://www.cometchat.com/public/img/newsletter_logo.png'" />
										</td>
									</tr>
									<tr>
										<td class="content-block">
										 Hello,
										</td>
									</tr>
									<tr>
										<td class="content-block">
											Please reset your CometChat Admin Panel password by clicking the reset password button below.
										</td>
									</tr>
									<tr>
										<td class="content-block">
											This email is only valid for next 30 minutes. If you did not request a password reset, please ignore this email. 
										</td>
									</tr>
									<tr>
										<td class="content-block" itemprop="handler" itemscope itemtype="http://schema.org/HttpActionHandler">
											<a href="{$resetlink}" class="btn-primary" itemprop="url">Reset Your Password</a>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</div>
			</td>
			<td></td>
		</tr>
	</table>
</body>
</html>
EOD;
}
