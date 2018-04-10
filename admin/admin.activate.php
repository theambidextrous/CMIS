<?php if(isset($_GET['token'])){ ?>
<script>
<!--
//Define page title
document.title = "<?=SYSTEM_SHORT_NAME?> | Password Activation";

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
        <div class="panel-heading">
          <h3 class="panel-title text-center">Password Activation</h3>
        </div>
        <div class="panel-body">
		  <?php
          $thisToken = isset($_GET["token"])?$_GET["token"]:"";
		  
		  require "$incl_dir/recaptchalib.php";
		  require_once("$incl_dir/mysqli.functions.php");
		  //Open database connection
		  $conn = db_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
		  
          if(getSysTokenUser($thisToken)){
              $thisUser = getSysTokenUser($thisToken);
                            
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
                  // validate "password" field
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
                      $resetSQL = sprintf("UPDATE `".DB_PREFIX."sys_users` SET `Password` = '%s', `token` = NULL WHERE `Username` = '%s'", $encryptedpass, $thisUser);
                      //Run Query
                      db_query($resetSQL,DB_NAME,$conn);
                      //Done?
                      if(db_affected_rows($conn)){
                          //Success!
                          $saved = true;
                          //Display Confirmation Message
                          $CONFIRM['MSG'] = ConfirmMessage("Your account has been updated successfully. <a href=\"./\">Click here to login to the system.</a>");
                      }
                      else{
                          //Fail!
                          $saved = false;
                          //Display Error Message
                          $ERRORS['MSG'] = ErrorMessage("Failed to reset your password. Please try again later.");
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
                    <p class="text-danger text-center"><strong>FIELDS MARKED WITH ASTERISKS (*) ARE REQUIRED</strong></p>
                    <p class="text-center">Use this form to activate password for <strong><?=$thisUser?></strong></p>
                    <form id="passactivateform" name="PassActivate" method="post" action="?do=activate&amp;token=<?=$thisToken?>">
                      <fieldset>
                      <div class="form-group">
                        <label for="newpass">New Password: <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="password" class="form-control" value="<?=$_POST['password']?>"><br><span class="text-danger"><?=$ERRORS['password'];?></span>
                      </div>
                      <div class="form-group">
                        <label for="verifypass">Verify Password: <span class="text-danger">*</span></label>
                        <input type="password" name="verifypass" id="verifypass" class="form-control" value="<?=$_POST['verifypass']?>">&nbsp;<span class="text-danger"><?=$ERRORS['verifypass'];?></span>
                      </div>
                      <div class="form-group">
                        <label for="securitycode">Security Code: <span class="text-danger">*</span></label>
                        <?=recaptcha_get_html();?><br><span class="text-danger"><?=$ERRORS['reCaptcha']?></span>
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