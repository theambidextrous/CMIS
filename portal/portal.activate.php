<?php
/*********************************************
Company:		Wits Technologies Ltd
Developer:	Sammy Mwaura Waweru
Mobile:			+254721428276
Email:			sammy@witstechnologies.co.ke
Website:		http://www.witstechnologies.co.ke/
*********************************************/

//Import the PHPMailer class into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if(isset($_GET['token'])){ 
?>
<script language="javascript" type="text/javascript">
<!--
document.title = "<?=SYSTEM_SHORT_NAME?> - Portal | Password Activation";

//JQuery Functions
$(document).ready(function(){
	//Validate form data
	$("#passactivateform").validate({
		rules: {
			password: {
				required: true,
				minlength: 7
			},
			verifypass: {
				equalTo: "#password"
			}
		}
	});

});
//-->
</script>

<div class="container">
	<div class="row">
		<div class="col-md-4 col-md-offset-4">
			<div class="header-img"><img class="img-responsive" src="<?=SYSTEM_LOGO_URL;?>" alt="Logo"></div>
			<div class="login-panel panel panel-default">
				<div class="panel-heading"><h3 class="panel-title text-center">Password Activation</h3></div>
				<div class="panel-body">
				<?php
				$thisToken = isset($_GET["token"])?$_GET["token"]:"";
		
				require "$incl_dir/recaptchalib.php";
				require_once("$incl_dir/mysqli.functions.php");
				//Open database connection
				$conn = db_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
			
				if(getClientToken($thisToken)){
					$thisUser = getClientToken($thisToken);                        
					
					//Set required variable
					//Array to store the error messages
					$ERRORS = array();
					$CONFIRM = array();
					//Get this user details
					//Update
					if(isset($_POST['Activate'])){
							//User information
							$password = secure_string($_POST['password']); // new password
							$verifypass = secure_string($_POST['verifypass']); // verified password
							$encryptedpass = hashedPassword($verifypass);
							
							//validator contractor
							$check = new validator();
							// validate entry	
							// validate "email" field
							if(!$check->is_password($password))
							$ERRORS['password'] = 'Password must be at least 7 letters mixed with digits and symbols';
							// validate "verifypass" field
							if(!$check->cmp_string($verifypass,$password))
							$ERRORS['verifypass'] = 'Passwords entered do not match';	
							// Validate Google reCAPTCHA
							if( !recapture_verify( $_POST["g-recaptcha-response"], $_SERVER["REMOTE_ADDR"] ) )
							$ERRORS['reCaptcha'] = "You're a robot. If not, please try again.";	
							
							// if current details approved, then check for errors
							if(sizeof($ERRORS) > 0){
									$ERRORS['MSG'] = ErrorMessage("ERRORS ENCOUNTERED!");
							}
							else{
									//Set SQL
									$activateSQL = sprintf("UPDATE `".DB_PREFIX."portal` SET `Password` = '%s', `ApprovedFlag` = %d, `Token` = NULL WHERE `LoginID` = '%s'", $encryptedpass, 1, $thisUser);
									//Run Query
									db_query($activateSQL,DB_NAME,$conn);
									//Done?
									if(db_affected_rows($conn)){
											//Yes: Confirm
											//SEND NOTIFICATION//
											// Mail function
											$mail = new PHPMailer; // defaults to using php "mail()"
											
											// Message
											$message = "<html><head>
											<title>".SYSTEM_SHORT_NAME." - Your Account Details Updated</title>
											</head><body>
											<p>Dear $thisUser, <br><br> You received this email because you activated your account with ".SYSTEM_SHORT_NAME.". <br>You will now be able to access your account with the new password. If you did not initiate this change, please contact us as soon as possible to secure your account.<br><a href=\"".SYSTEM_URL."/client/\" target=\"_blank\">Click here to login to the system.</a><br><br>If you have applied to a currently advertised position we will review your background and benchmark your experience against the requirements of the position. If you are short-listed for the position, we will contact you to discuss your application.<br><br>Sincerely,<br><br>".strtoupper(SYSTEM_SHORT_NAME)." ALERT NOTIFICATIONS<br>Email: ".INFO_EMAIL."<br>Website: ".SYSTEM_URL."</p>
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
											$mail->Subject = SYSTEM_SHORT_NAME." - Your Account Details Updated";	
											$mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; 		
											$mail->msgHTML($body);
											$mail->isHTML(true); // send as HTML		
											$mail->addAddress($thisUser);
											
											
											if(!$mail->Send()) {
													//Not sent...but has been saved
													$saved = true;
													$CONFIRM['MSG'] = ConfirmMessage("Your account ($thisUser) has been updated successfully. <a href=\"?username=".$thisUser."\">Click here to login to the system.</a>");
											}
											else{
													//Saved and sent
													$saved = true;
													//Display Confirmation Message
													$CONFIRM['MSG'] = ConfirmMessage("Your account ($thisUser) has been updated successfully and a notification sent to your email account. <a href=\"?username=".$thisUser."\">Click here to login to the system.</a>");
											}			
									}
									else{
											//Not saved
											$saved = false;
											//Display Error Message
											$ERRORS['MSG'] = ErrorMessage("No changes were made to $thisUser. Please try again.");
									}
							}
					}
					//Check if saved
					if($saved){
							echo $CONFIRM['MSG'];
					}
					else{
					//Capture Errors if any
					if(sizeof($ERRORS['MSG'])>0) echo $ERRORS['MSG'];
					if(isset($_SESSION['message'])){ echo $_SESSION['message']; }
					?>
					<p class="text-danger text-center"><strong>FIELDS MARKED WITH ASTERISKS (*) ARE REQUIRED</strong></p>
					<p class="text-center">Use this form to activate password for <strong><?=$thisUser?>
</strong></p>
					<form id="passactivateform" name="PassActivate" method="post" action="?do=activate&amp;token=<?=$thisToken?>">
						<fieldset>
							<div class="form-group">
								<label for="newpass">New Password: <span class="text-danger">*</span></label>
								<input type="password" name="password" id="password" class="form-control" value="<?=$_POST['password']?>">
								<br>
								<span class="text-danger"><?=$ERRORS['password'];?>
</span>
							</div>
							<div class="form-group">
								<label for="verifypass">Verify Password: <span class="text-danger">*</span></label>
								<input type="password" name="verifypass" id="verifypass" class="form-control" value="<?=$_POST['verifypass']?>">
								&nbsp;<span class="text-danger"><?=$ERRORS['verifypass'];?>
</span>
							</div>
							<div class="form-group">
								<label for="securitycode">Security Code: <span class="text-danger">*</span></label>
								<?=recaptcha_get_html();?>
								<br>
								<span class="text-danger"><?=$ERRORS['reCaptcha']?>
</span>
							</div>
							<input type="submit" name="Activate" value="Activate Password" class="btn btn-primary">
							<input type="button" name="Cancel" value="Cancel" onclick="javascript:location.href='./'" class="btn btn-default">
						</fieldset>
					</form>
					<?php
					}			
				}else{
					echo ErrorMessage("Invalid token! The token is only available for one time usage. Try reset your account again.");
					echo '<p class="text-center"><a class="btn btn-primary" href="?do=reset" title="Reset Account">Reset Account</a></p>';
				}
				//Close connection
				db_close($conn);
				?>
				</div>
				<!-- / .panel-body -->
			</div>
			<!-- / .login-panel -->
		</div>
		<!-- / .col-md-4 -->
	</div>
	<!-- / .row -->
</div>
<!-- / .container -->
<?php
}else{
	echo ErrorMessage("Invalid request! This link has either expired or is incorrect.");
}
?>
