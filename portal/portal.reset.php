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
?>
<script language="javascript" type="text/javascript">
<!--
document.title = "<?=SYSTEM_SHORT_NAME?> - Portal | Password Reset";

//JQuery Functions
$(document).ready(function(){
	//Validate form data
	$("#passresetform").validate({
		rules: {
			email: {
				required: true
			}
		},
		messages: {
			email: {
				required: 'Please enter a valid email address'
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
        <div class="panel-heading">
          <h3 class="panel-title text-center">Password Reset</h3>
        </div>
        <div class="panel-body">
          <?php
					require "$incl_dir/recaptchalib.php";
					require_once("$incl_dir/mysqli.functions.php");
					//Open database connection
					$conn = db_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);		  
					//Set required variable
					//Array to store the error messages
					$ERRORS = array();
					$CONFIRM = array();
					//Get this user details
					//Update
					if(isset($_POST['Reset'])){
						//User information
						$thisEmail = $_POST['email'];
						$thisUserType = $_POST['usertype'];
						$thisToken = md5(time());
						
						$portalLoginID = getPortalPasswordResetDetails($thisEmail,$thisUserType);										
						
						//validator contractor
						$check = new validator();
						// validate entry	
						// validate "email" field
						if(!$check->is_email($thisEmail))
						$ERRORS['email'] = 'Valid email required';
						if(!$portalLoginID)
						$ERRORS['email'] = "This email was not found on our database";
						// Validate Google reCAPTCHA
						if( !recapture_verify( $_POST["g-recaptcha-response"], $_SERVER["REMOTE_ADDR"] ) )
						$ERRORS['reCaptcha'] = "You're a robot. If not, please try again.";	
						
						
						// if current details approved, then check for errors
						if(sizeof($ERRORS) > 0){
							$ERRORS['MSG'] = ErrorMessage("ERRORS ENCOUNTERED!");
						}
						else{
							
							//Update account (add new token)
							$updateSql = sprintf("UPDATE `".DB_PREFIX."portal` SET `ApprovedFlag` = %d, `Token` = '%s' WHERE `LoginID` = '%s' LIMIT 1", 0, $thisToken, $portalLoginID);
							//Run Query
							db_query($updateSql,DB_NAME,$conn);
							
							if(db_affected_rows($conn)){
								//SEND RESET LINK//
								// Mail function
								$mail = new PHPMailer; // defaults to using php "mail()"
								
								// Message
								$message = "<html><head>
								<title>".SYSTEM_SHORT_NAME." - Reset Your Account</title>
								</head><body>
								<p>Dear $thisEmail, <br><br> You received this email because you requested an activation of your account at ".SYSTEM_SHORT_NAME." website. To activate your account, you'll need to set a new password by clicking on the link provided below. <br><br><strong>One Time Password Reset Link: </strong> <a href=\"".SYSTEM_URL."/portal/?do=activate&token=".$thisToken."\" target=\"_blank\"><strong>Click here to activate your account</strong></a><br><br>If you experience any problems accessing your account, please contact us and we shall be happy to help you.<br><br>Sincerely,<br><br>".strtoupper(SYSTEM_SHORT_NAME)." ALERT NOTIFICATIONS<br>Email: ".INFO_EMAIL."<br>Website: ".SYSTEM_URL."</p>
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
								$mail->Subject = SYSTEM_SHORT_NAME." - Reset Your Account";	
								$mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; 		
								$mail->msgHTML($body);
								$mail->isHTML(true); // send as HTML		
								$mail->addAddress($thisEmail);
								
								
								if(!$mail->Send()) {
									//Fail!
									//Display Error Message
									$ERRORS['MSG'] = ErrorMessage("Failed to send an activation link to the email address provided. Please verify your email and try again.");
								}else{
									//Success!
									//Display Confirmation Message
									$_SESSION['message'] = ConfirmMessage("An activation link has been sent to your email account. Please check your email and click on the link provided.");
									redirect("./");
								}
							}else{
								//Fail!
								//Display Error Message
								$ERRORS['MSG'] = ErrorMessage("Failed to reset your account. Please try again later.");
							}		
						}
					}
					
					//Capture Errors if any
					if(sizeof($ERRORS['MSG'])>0) echo $ERRORS['MSG'];
					if(isset($_SESSION['message'])){ echo $_SESSION['message']; }
					?>
          <form id="passresetform" name="PassReset" method="post" action="?do=reset">
            <fieldset>
              <div class="form-group">
                <label for="email">Your Email: <span class="text-danger">*</span></label>
                <input type="text" name="email" size="20" value="<?=$_POST['email']?>" class="form-control required email" autofocus aria-describedby="helpBlockEmail"><span id="helpBlockEmail" class="help-block"><small>This should be the email address you used during registration.</small></span><br><span class="text-danger"><?=$ERRORS['email'];?></span>
              </div>
              <div class="form-group">
							  <label for="email">Select your user type: <span class="text-danger">*</span></label>
								<select name="usertype" id="usertype" class="form-control" aria-describedby="helpBlockUserType">
									<?php
									foreach(list_portal_user_types() as $k => $v){												
										if($k == $_POST['usertype']){
											$select = 'selected="selected"';
										}
										else{
											$select = "";
										}
										echo "<option $select value=\"$k\">$v</option>";
									}
									?>
								</select><span id="helpBlockUserType" class="help-block"><small>Please specify your user type to proceed.</small></span><br><span class="text-danger"><?=$ERRORS['usertype'];?></span>
							</div>
							<div class="form-group">
                <label for="securitycode">Security Code: <span class="text-danger">*</span></label>
                <?=recaptcha_get_html();?><br><span class="text-danger"><?=$ERRORS['reCaptcha']?></span>
              </div>
              <input type="submit" name="Reset" value="Send" class="btn btn-primary">
              <input type="button" name="Cancel" value="Cancel" onclick="javascript:location.href='./'" class="btn btn-default">
            </fieldset>
          </form>
          <?php
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