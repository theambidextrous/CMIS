<?php
/*********************************************
Company:		Wits Technologies Ltd
Developer:	Sammy Mwaura Waweru
Mobile:			+254721428276
Email:			sammy@witstechnologies.co.ke
Website:		http://www.witstechnologies.co.ke/
*********************************************/
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();

// NOTE: Requires PHP version 5 or later
if (version_compare(PHP_VERSION, '5.3.0', '>') ){
	# Load includes
	$incl_dir = "../includes";
	$class_dir = "../classes";
	$logs_dir = "../logs";
	
	include "$incl_dir/config.php";
	require_once("$incl_dir/functions.php");
	require_once("portal.functions.php");
	require_once("$class_dir/class.validator.php3");

	require_once("$class_dir/phpmailer/src/Exception.php");
	require_once("$class_dir/phpmailer/src/PHPMailer.php");
	require_once("$class_dir/phpmailer/src/SMTP.php");


} 
else { 
	# PHP version not sufficient
	exit("This system will only run on PHP version 5.3 or higher!\n");
}

add_header();

//check if we have a return URL
$url = isset($_GET["url"])?$_GET["url"]:""; 
$returnurl = urldecode($url);
//$username = isset($_GET["username"])?$_GET["username"]:"";
$username = isset($_COOKIE["usrusername"])?$_COOKIE["usrusername"]:$_GET["username"];
//$password = isset($_COOKIE["usrpassword"])?$_COOKIE["usrpassword"]:"";

$do = isset($_GET["do"])?$_GET["do"]:""; 
$do = strtolower($do); 
switch($do) {
case "": 
	if(checkLoggedin()){
		switch($_SESSION['usrtype']) {
			case "Student":
				redirect("student/".$returnurl);
			break;
			case "Faculty":
				redirect("faculty/".$returnurl);
			break;
		}
    }else{
		require_once('portal.login.php');
	}
break;
case "register":
	require_once('portal.registration.php');
break;
case "test":
	require_once('portal.test.php');
break;
case "payment":
	require_once('portal.payment.php');
break;
case "return_api":
	require_once('portal.api-response.php');
break;
case "thanks":
	require_once('portal.api-thank-u.php');
break;
case "reset":
	require_once('portal.reset.php');
break;
case "activate":
	require_once('portal.activate.php');
break;
case "confirm":
	require_once('portal.confirm.php');
break;
case "apply":
  require_once('portal.application.php');
break;
case "free":
	require_once('portal.free-course.php');
break;
case "shortcourse":
	//require_once('portal.free-course.php?option=shortcourse');
break;
case "login": 
    $username = isset($_POST["LoginID"])?trim($_POST["LoginID"]):""; 
    $password = isset($_POST["LoginPass"])?trim($_POST["LoginPass"]):"";
	//Check if username or password fields have been set
    if ($username == "" || $password == ""){
		$_SESSION['message'] = ErrorMessage("Login ID or password is blank"); 
        redirect("?error=invalid_login"); 
    }else { 
        if(confirmUser($username,$password)){ 
			createsessions($username,$password);
			markLoggedin($username);
			redirect("?url=".$returnurl);
        }else{			
			//If can't login, attempt these procedures...else...login is invalid!						
			if(confirmUserApproved($username)){
				$_SESSION['message'] = ErrorMessage("Your account is not approved. You need to check your email for an activation email that was sent after registration. If you did not receive the email, please use this form to activate your account.");
				redirect("?do=reset&token=".md5(time()));
			}elseif(confirmUserEnabled($username)){
				$_SESSION['message'] = ErrorMessage("Access denied: This account is disabled.<br>Contact the Administrator for approval.");
				redirect("?error=access denied"); 
			}else{
				//Try reset the previous session
				resetLoginSession($username);
				
				if(confirmUser($username,$password)){
					createsessions($username,$password);
					markLoggedin($username);
					redirect("?url=".$returnurl);
				}else{
					$_SESSION['message'] = ErrorMessage("Invalid login ID and/or password"); 
					clearsessionscookies();
					redirect("?url=".$returnurl);
				}
			}
        } 
    } 
break;
case "logout":
	markLoggedout();
	clearsessionscookies();
    redirect("./"); 
break;
}
unset($_SESSION['message']);

add_footer();

ob_flush();
?>