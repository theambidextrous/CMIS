<?php
//Import the PHPMailer class into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if(isset($_GET['token'])){
	require_once("$incl_dir/mysqli.functions.php");
	//Open database connection
	$conn = db_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
			
	$thisToken = isset($_GET["token"])?$_GET["token"]:"";
	if(getClientToken($thisToken)){
		$thisUser = getClientToken($thisToken);
				
		//Set SQL
		$activateSQL = sprintf("UPDATE `".DB_PREFIX."portal` SET `ApprovedFlag` = %d WHERE `Username` = '%s'", 1, $thisUser);
		//Run Query
		db_query($activateSQL,DB_NAME,$conn);
		//Done?
		if(db_affected_rows($conn)){
			//SEND NOTIFICATION//
			// Mail function
			$mail = new PHPMailer; // defaults to using php "mail()"
			
			// Message
			$message = "<html><head>
			<title>SYSTEM_SHORT_NAME - Your Account Activation</title>
			</head><body>
			<p>Dear $thisUser, <br><br> You received this email because your account activation at ".SYSTEM_SHORT_NAME." was successful. <br>You will now be able to login to your account with the email and password you provided during sign up. If you did not initiate this activation, please contact us as soon as possible to secure your account.<br><br>If at a later stage you wish to update your details, visit our <a href=\"".SYSTEM_URL."/portal\">Portal</a> and log in to your user account. You will require entering your Login ID and your new password in order to access your profile page.<br><br>Sincerely,<br><br>".strtoupper(SYSTEM_SHORT_NAME)." ALERT NOTIFICATIONS<br>Email: ".INFO_EMAIL."<br>Website: ".SYSTEM_URL."</p>
			</body>
			</html>";
			
			$body = $message;
			$body = preg_replace('/\\\\/','', $body); //Strip backslashes
			
			switch(MAILER){
				case 'smtp':
				$mail->isSMTP(); // telling the class to use SMTP
				$mail->SMTPAuth = SMTP_AUTH; // enable SMTP authentication
				$mail->SMTPSecure = SMTP_SECU; // sets the prefix to the servier
				$mail->Host = SMTP_HOST; // SMTP server
				$mail->Port = SMTP_PORT; // set the SMTP port for the HOST server
				$mail->Username = SMTP_USER;
				$mail->Password = SMTP_PASS;
				break;
				case 'sendmail':
				$mail->isSendmail(); // telling the class to use SendMail transport
				break;
				case 'mail':
				$mail->isMail(); // telling the class to use mail function
				break;
			}
			
			$mail->setFrom(MAILER_FROM_EMAIL, MAILER_FROM_NAME);
			$mail->Subject = SYSTEM_SHORT_NAME." - Your Account Activation";	
			$mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; 		
			$mail->msgHTML($body);
			$mail->isHTML(true); // send as HTML		
			$mail->addAddress($thisUser);			
			
			$mail->Send();
			
			//Display Confirmation Message
			$_SESSION['message'] = ConfirmMessage("Your account ($thisUser) has been activated successfully");
			redirect("?username=".$thisUser);		
		}
		else{
			//Display Error Message
			$_SESSION['message'] = ErrorMessage("Activation Failed: Your account is either already activated or you might have clicked on an activation link that has already expired.");
			redirect("?error=Activation Failed");
		}		
	}else{
		$_SESSION['message'] = ErrorMessage("Invalid token! The token is only available for one time usage. Try reset your account again.");
		redirect("?error=Invalid Token");
	}
	//Close connection
	db_close($conn);
}else{
	//Display Error Message
	$_SESSION['message'] = ErrorMessage("Activation Failed! Link supplied returned an invalid request.");
	redirect("?error=Activation Failed");
}
?>