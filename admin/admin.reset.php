<?php
//Import the PHPMailer class into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
?>
<script language="javascript" type="text/javascript">
<!--
//Define page title
document.title = "<?=SYSTEM_SHORT_NAME?> | Password Reset";

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
          //Get this user details
          //Update
          if(isset($_POST['Reset'])){
              //User information
              $thisEmail = $_POST['email'];
              $thisToken = md5(time());
              
              //validator contractor
              $check = new validator();
              // validate entry	
              // validate "email" field
              if(!$check->is_email($thisEmail))
              $ERRORS['email'] = 'Valid email required';
              // Validate Google reCAPTCHA
							if( !recapture_verify( $_POST["g-recaptcha-response"], $_SERVER["REMOTE_ADDR"] ) )
							$ERRORS['reCaptcha'] = "You're a robot. If not, please try again.";							
              //Check if email address exists
              $confirmEmailSql = sprintf("SELECT `Email` FROM `".DB_PREFIX."sys_users` WHERE `Email` = '%s'", $thisEmail);
              //Set the result and run the query
              $result = db_query($confirmEmailSql,DB_NAME,$conn);
              //check if any results were returned
              if(!db_num_rows($result)>0){
                  $ERRORS['email'] = "This email was not found on our database";
              }
              db_free_result($result);
              
              // if current details approved, then check for errors
              if(sizeof($ERRORS) > 0){
                  $ERRORS['MSG'] = ErrorMessage("ERRORS ENCOUNTERED!");
              }
              else{
                  //Update account (add new token)
                  $updateSql = sprintf("UPDATE `".DB_PREFIX."sys_users` SET `token` = '%s' WHERE `Email` = '%s' LIMIT 1", $thisToken, $thisEmail);
                  db_query($updateSql,DB_NAME,$conn);
                  if(db_affected_rows($conn)){
                      //SEND RESET LINK//
                      // Mail function
                      $mail = new PHPMailer; // defaults to using php "mail()"
                      
                      // Message
                      $message = "<html><head>
                      <title>".SYSTEM_SHORT_NAME." - Reset Your Account</title>
                      </head><body>
                      <p>Dear $thisEmail, <br><br> You received this email because you requested an activation of your account at ".SYSTEM_SHORT_NAME." website. To activate your account, you'll need to set a new password by clicking on the link provided below. <br> <strong>For security reasons, please NEVER SHARE your password with anyone.</strong><br><br><strong>One Time Password Reset Link: </strong> <a href=\"".SYSTEM_URL."/admin/?do=activate&token=".$thisToken."\" target=\"_blank\"><strong>Click here to activate your account</strong></a><br><br>Sincerely,<br><br>".strtoupper(SYSTEM_SHORT_NAME)." ALERT NOTIFICATIONS<br>Email: ".INFO_EMAIL."<br>Website: ".PARENT_HOME_URL."</p>
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
                          //Reset Fail!
                          $saved = false;
                          //Display Error Message
                          $ERRORS['MSG'] = ErrorMessage("Failed to send an activation link to the email address provided. Please try again later.");
                      }
                      else{
                          //Reset Success!
                          $saved = true;
                          //Display Confirmation Message
                          $CONFIRM['MSG'] = ConfirmMessage("An activation link has been sent to your email account. Please check your email and click on the link provided.<br><a href=\"./\">Click here to go back to the login form.</a>");
                      }
                  }
                  else{
										//Fail!
										$saved = false;
										//Display Error Message
										$ERRORS['MSG'] = ErrorMessage("Failed to reset your account. Please try again later.");
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
						?>
						<p class="text-center">Enter the email address provided during sign up and you will receive an email with procedure on how to activate your account.</p>
						<p class="text-danger text-center"><strong>FIELDS MARKED WITH ASTERISKS (*) ARE REQUIRED</strong></p>
						<form id="passresetform" name="PassReset" method="post" action="?do=reset">
						<fieldset>
								<div class="form-group">
								<label for="email">Your Email: <span class="text-danger">*</span></label>
								<input type="text" name="email" size="20" value="<?=$_POST['email']?>" class="form-control required email" autofocus aria-describedby="helpBlockEmail">
								<span id="helpBlockEmail" class="help-block"><small>This should be the email address you used during registration.</small></span><br>
								<span class="text-danger"><?=$ERRORS['email'];?></span>
							</div>
								<div class="form-group">
								<label for="securitycode">Security Code: <span class="text-danger">*</span></label>
								<?=recaptcha_get_html();?>
								<br>
								<span class="text-danger"><?=$ERRORS['reCaptcha']?></span>
							</div>
								<input type="submit" name="Reset" value="Send" class="btn btn-primary">
								<input type="button" name="Cancel" value="Cancel" onclick="javascript:location.href='./'" class="btn btn-default">
							</fieldset>
					</form>
						<?php
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