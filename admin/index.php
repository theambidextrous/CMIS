<?php
/*********************************************
Company:		Wits Technologies Ltd
Developer:	Sammy Mwaura Waweru
Mobile:			+254721428276
Email:			sammy@witstechnologies.co.ke
Website:		http://www.witstechnologies.co.ke/
*********************************************/
//Import the PHPMailer class into the global namespace
//use PHPMailer\PHPMailer\PHPMailer;
//use PHPMailer\PHPMailer\Exception;

if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();

$incl_dir = "../includes";
$class_dir = "../classes";
$logs_dir = "../logs";

include "$incl_dir/config.php";
require_once("$incl_dir/functions.php");
require_once("admin.functions.php");
require_once("$class_dir/class.validator.php3");

require_once("$class_dir/phpmailer/src/Exception.php");
require_once("$class_dir/phpmailer/src/PHPMailer.php");
require_once("$class_dir/phpmailer/src/SMTP.php");

add_header("admin");

//check if we have a return URL
$url = isset($_GET["url"])?$_GET["url"]:"admin.php"; 
$returnurl = urldecode($url);
//$username = isset($_GET["username"])?$_GET["username"]:"";
$username = isset($_COOKIE["sysusername"])?$_COOKIE["sysusername"]:"";
//$password = isset($_COOKIE["syspassword"])?$_COOKIE["syspassword"]:"";

$do = isset($_GET["do"])?$_GET["do"]:""; 
$do = strtolower($do); 
switch($do) {
case "":
	if(checkLoggedin()){
		if(!empty($returnurl)){
			redirect($returnurl);
		}else{
			if(isSuperAdmin() || isSystemAdmin()){
				$returnurl = "admin.php";
				redirect($returnurl);
			}else{
				$_SESSION['message'] = ErrorMessage("Access denied: Your account is not assigned to any role. Contact the system administrator to have this issue resolved.");
				markLoggedout();
				clearsessionscookies();
			}
		}
    }else{
		require_once('admin.login.php');
	}
break;
case "activate":
	require_once('admin.activate.php');
break;
case "reset":
	require_once('admin.reset.php');
break;
case "login": 
	$username = isset($_POST["uname"])?strtolower(trim($_POST["uname"])):""; 
	$password = isset($_POST["upass"])?trim($_POST["upass"]):"";
	//Check if username or password fields have been set
	if ($username == "" || $password == ""){
		$_SESSION['message'] = ErrorMessage("Username or password is blank"); 
		redirect("?error=invalid_login"); 
	}else{ 
			if(confirmUser($username,$password)){ 
				createsessions($username,$password);
				markLoggedin($username);
				redirect("?url=".$returnurl);
			}else{
		//If can't login, attempt these procedures...else...chase the user OUT!
		if(!resetLoginSession($username)){
			$_SESSION['message'] = ErrorMessage("Invalid username and/or password");
			clearsessionscookies(); 
			redirect("?url=".$returnurl); 
		}else{
			if(confirmUser($username,$password)){
				createsessions($username,$password);
				markLoggedin($username);
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

add_footer("admin");

ob_flush();
?>