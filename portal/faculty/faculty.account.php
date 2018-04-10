<?php
//Import the PHPMailer class into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once("$class_dir/class.validator.php3");
?>
<script>
<!--//
//Define page title
document.title = "<?=SYSTEM_SHORT_NAME?> - Faculty | My Account";
//-->
</script>

<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">My Account</h1>
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->

<div class="row">
	<div class="col-lg-12">
		<div class="cms-contents-grey">
			<!--Begin Forms-->
			<?php
      //Array to store the error messages
			$MSG = array();
			$ERRORS = array();
			//$CONFIRM = array();
			//Require Faculty ID to modify
      $EditID = !empty($faculty['FacultyID'])?$faculty['FacultyID']:NULL;
      //Get requested task/default is edit
      $task = isset($_GET['task'])?$_GET['task']:"edit";
	  
      $task = strtolower($task);
      switch($task) {
				case "view":
					?>
					<div class="panel panel-default">
						<div class="panel-heading"><i class="fa fa-user fa-fw"></i> <?=$faculty['FacultyName']?></div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-3">
									<?php
									if(!empty($faculty['PassportPhoto'])){
										echo '<img class="img-responsive img-circle img-left" style="max-height:200px;" src="'.$faculty['PassportPhoto'].'">';
									}else{
										echo '<img src="'.IMAGE_FOLDER.'/no-avatar.png" class="img-responsive img-circle img-left" alt="No image">';
									}
									?>
								</div>
								<div class="col-md-9">
									<p><strong>Gender: </strong><?=$faculty['Gender']?></p>
									<p><strong>Email: </strong><?=$faculty['Email']?></p>
									<p><strong>Work Phone: </strong><?=$faculty['WPhone']?></p>
									<p><strong>Mobile Phone: </strong><?=$faculty['MPhone']?></p>
									<p><strong>Address: </strong><?=$faculty['Address']." ".$faculty['City']?></p>
									<p><strong>Registration Date: </strong><?=fixdatelong($faculty['RegDate'])?></p>
									<p><a class="btn btn-primary" href="?tab=9&amp;task=edit">Edit Profile</a></p>
								</div>
							</div>
						</div>
						<!-- /.panel-body -->
					</div>
					<!-- /.panel-default -->
					<?php
				break;
				case "edit":			  
					//Execute Commands
					if(isset($_POST['edit']) && !empty($EditID)){        
						$Title = secure_string($_POST['Title']);
						$FName = secure_string($_POST['FName']);
						$MName = secure_string($_POST['MName']);
						$LName = secure_string($_POST['LName']);
						$FacultyName = $FName." ".$LName;
						$Email = isset($_POST['Email'])?secure_string($_POST['Email']):"";
						$WPhone = isset($_POST['WPhone'])?secure_string($_POST['WPhone']):"";
						$MPhone = isset($_POST['MPhone'])?secure_string($_POST['MPhone']):"";
						$Address = isset($_POST['Address'])?secure_string($_POST['Address']):"";
						$City = isset($_POST['City'])?secure_string($_POST['City']):"";
						//image processor
						$PassportPhoto = $_FILES["PassportPhoto"]["name"];
						$PassportPhotoTemp = $_FILES["PassportPhoto"]["tmp_name"];
						$UploadDate = date('dmYHis');
						$allowed_mimes = allowed_doc_mime_types();
						
						// Validator contractor
						$check = new validator();
						// validate entry
						// validate "FName" field
						if(!$check->is_String($FName))
						$ERRORS['FName'] = "Valid first name is required";
						// validate "LName" field
						if(!$check->is_String($LName))
						$ERRORS['LName'] = "Valid last name is required";
						// validate "Email" field
						if(!$check->is_email($Email))
						$ERRORS['Email'] = "Valid email address is required";
						// validate "WPhone" field
						if(!$check->is_phone($WPhone))
						$ERRORS['WPhone'] = "Valid work phone number is required";
						// validate "MPhone" field
						if(!empty($MPhone) && !$check->is_phone($MPhone))
						$ERRORS['MPhone'] = "Valid mobile phone number is required";						
						
						// check for errors
						if(sizeof($ERRORS) > 0){
							$MSG['ERROR'] = ErrorMessage("ERRORS ENCOUNTERED!");
						}
						else{
							//RUN FILE UPLOADS								
							$PassportPhotoExt = findexts($PassportPhoto);
							if(!empty($PassportPhotoTemp)){
								$PassportPhotoName = friendlyName($FacultyName)."-PassportPhoto-".$UploadDate.".".$PassportPhotoExt;
								$PassportPhotoPath = UPLOADS_PATH.$PassportPhotoName;
								//Just incase of an internal problem
								if(move_uploaded_file($PassportPhotoTemp, $PassportPhotoPath)){
									$PassportPhotoPath = UPLOADS_FOLDER."/".$PassportPhotoName;
								}else{
									$PassportPhotoPath = "";
								}
							}else{
								$PassportPhotoPath = "";
							}
							//Update Faculty
							$updateFacultySql = sprintf("UPDATE `".DB_PREFIX."faculties` SET `Title` = '%s', `FName` = '%s', `LName` = '%s', `Email` = '%s', `PassportPhoto` = '%s', `WPhone` = '%s', `MPhone` = '%s', `Address` = '%s', `City` = '%s' WHERE `FacultyID` = '%s'", $Title, $FName, $LName, $Email, $PassportPhotoPath, $WPhone, $MPhone, $Address, $City, $EditID);
							
							//Execute the query or die if there is a problem
							db_query($updateFacultySql,DB_NAME,$conn);
							
							//Check if saved
							if(db_affected_rows($conn)){
								// Set mail function
								$mail = new PHPMailer; // defaults to using php "mail()"
								
								$body = "<html><head>
								<title>$FacultyName - Account Updated</title>
								</head><body><p>Dear $FacultyName, <br><br>You received this email because you updated your account on ".SYSTEM_NAME." website.</p>
								<p>If you did not make the change, please report the action to us immediately. Otherwise, you can just ignore this email if you're aware of this update on your account.</p>								
								<p>Sincerely,<br><br>".strtoupper(SYSTEM_SHORT_NAME)." ALERT NOTIFICATIONS<br>Email: ".INFO_EMAIL."<br>Website: ".SYSTEM_URL."</p></body></html>";
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
								$mail->Subject = "$FacultyName - Account Updated";
								$mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; 		
								$mail->msgHTML($body);
								$mail->isHTML(true); // send as HTML
								$mail->addAddress($Email, $FacultyName);
								
								$mail->Send();
								
								$saved = true;
								$MSG['CONFIRM'] = ConfirmMessage("Your account has been updated successfully.");
								
							}else{
								$saved = false;
								$MSG['ERROR'] = WarnMessage("No changes were made on your account details.");
							}
						}	
					}
					?>
				<form id="editform" name="Edit" method="post" action="?tab=9&amp;task=<?=$task?>" enctype="multipart/form-data">
					<div class="row">
						<div class="col-lg-12">
							<p>Please confirm your details below and update where necesary.</p>
							<?php 
							if(sizeof($MSG)>0) {
								foreach($MSG as $MESSAGE){
									echo $MESSAGE;
								}					
							}
							?>
							<p class="text-center"><strong><?=strtoupper($task)?> MY DETAILS</strong></p>
							<p class="text-center text-danger"><strong>FIELDS MARKED WITH ASTERISKS (*) ARE REQUIRED</strong></p>
						</div>
						<div class="col-sm-12 col-md-6">
							<div class="form-group">
								<label for="Title">Title: &nbsp;</label>
								<select name="Title" class="form-control">
									<option value="None">--Select--</option>
									<?php
									foreach(list_title_status() as $k => $v){											
										if($k == $faculty['Title']){
											$select = 'selected="selected"';
										}
										else{
											$select = "";
										}
										echo "<option $select value=\"$k\">$v</option>";
									}
									?>
								</select>
								&nbsp;<span class="text-danger"><?=$ERRORS['Title'];?></span>
							</div>
							<div class="form-group">
								<label for="FName">First Name: <span class="text-danger">*</span></label>
								<input type="text" value="<?=$faculty['FName']; ?>" name="FName" class="form-control required">
								&nbsp;<span class="text-danger"><?=$ERRORS['FName'];?></span>
							</div>
							<div class="form-group">
								<label for="MName">Middle Name: </label>
								<input type="text" value="<?=$faculty['MName']; ?>" name="MName" class="form-control">
								&nbsp;<span class="text-danger"><?=$ERRORS['MName'];?></span>
							</div>
							<div class="form-group">
								<label for="LName">Last Name: <span class="text-danger">*</span></label>
								<input type="text" value="<?=$faculty['LName']; ?>" name="LName" class="form-control required">
								&nbsp;<span class="text-danger"><?=$ERRORS['LName'];?></span>
							</div>
							<div class="form-group">
								<label for="PassportPhoto">Profile Photo: <span class="text-danger">*</span></label>
								<?php
								if(!empty($faculty['PassportPhoto'])){
									echo '<img class="img-responsive img-circle" style="margin:5px 0 10px 0;max-height:100px;" src="'.$faculty['PassportPhoto'].'">';
								}
								?>
								<input type="file" name="PassportPhoto">
								&nbsp;<span class="text-danger"><?=$ERRORS['PassportPhoto'];?></span>
							</div>
							<div class="form-group">
								<label for="Gender">Gender: </label>
								<select name="Gender" class="form-control">
									<option value="None">--Select--</option>
									<?php
									foreach(list_gender_status() as $k => $v){
										if($k == $faculty['Gender']){
											$select = 'selected="selected"';
										}
										else{
											$select = "";
										}
										echo "<option $select value=\"$k\">$v</option>";
									}
									?>
								</select>
							</div>
							<div class="form-group">
								<label for="Email">Email: <span class="text-danger">*</span></label>
								<input type="email" value="<?=$faculty['Email']; ?>" name="Email" class="form-control required email">
								&nbsp;<span class="text-danger"><?=$ERRORS['Email'];?></span>
							</div>
							<div class="form-group">
								<label for="WPhone">Work Phone: <span class="text-danger">*</span></label>
								<input type="tel" value="<?=$faculty['WPhone']; ?>" name="WPhone" class="form-control required">
								&nbsp;<span class="text-danger"><?=$ERRORS['WPhone'];?></span>
							</div>
							<div class="form-group">
								<label for="MPhone">Mobile Phone: &nbsp;</label>
								<input type="tel" value="<?=$faculty['MPhone']; ?>" name="MPhone" class="form-control required">
								&nbsp;<span id="validate-msg" class="text-danger"><?=$ERRORS['MPhone'];?></span>
							</div>
						</div>
						<div class="col-sm-12 col-md-6">
							<div class="form-group">
								<label for="Address">Address: </label>
								<textarea name="Address" class="form-control" rows="6"><?=decode($faculty['Address'])?>
	</textarea>
							</div>
							<div class="form-group">
								<label for="City">City/Town: <span class="text-danger">*</span></label>
								<input type="text" value="<?=$faculty['City']; ?>" name="City" class="form-control required">
								&nbsp;<span class="text-danger"><?=$ERRORS['City'];?></span>
							</div>
							<div class="form-group">
								<label for="State">State/County: </label>
								<input type="text" value="<?=$faculty['State']; ?>" name="State" class="form-control">
							</div>
							<div class="form-group">
								<label for="PostCode">Zip/Postal Code: </label>
								<input type="text" value="<?=$faculty['PostCode']; ?>" name="PostCode" maxlength="5" class="form-control">
								&nbsp;<span class="text-danger"><?=$ERRORS['PostCode'];?></span>
							</div>
							<div class="form-group">
								<label for="Country">Country: <span class="text-danger">*</span></label>
								<select name="Country" class="form-control">
									<option value="None">--Select--</option>
									<?php
									foreach(list_countries() as $k => $v){
										if($k == $faculty['Country']){
											$select = 'selected="selected"';
										}
										else{
											$select = "";
										}
										echo "<option $select value=\"$k\">$v</option>";
									}
									?>
								</select>
								&nbsp;<span class="text-danger"><?=$ERRORS['Country'];?></span>
							</div>
						</div>
						<div class="col-lg-12 text-center">
							<input type="submit" name="<?=$task?>" value="Save" class="btn btn-primary">
						</div>
					</div>
				</form>
				<?php
				break;
				case "reset":            
					//Execute Commands
					if(isset($_POST['reset']) && !empty($EditID)){ 
						$FacultyOldPassword = isset($_POST['OldPassword'])?secure_string($_POST['OldPassword']):"";
						$FacultyEditPassword = isset($_POST['NewPassword'])?secure_string($_POST['NewPassword']):"";
						$VerifyFacultyEditPassword = isset($_POST['VerifyPass'])?secure_string($_POST['VerifyPass']):"";
						$EncryptedPass = hashedPassword($VerifyFacultyEditPassword);
						$Token = md5(time());
						
						// Validator contractor
						$check = new validator();
						// validate entry
						// validate "password" field
						if(!empty($FacultyEditPassword) && !$check->is_password($FacultyEditPassword)){
							$ERRORS['FacultyEditPassword'] = "Password must be at least 7 letters mixed with digits and symbols";
						}
						// validate "verifypass" field
						if(!empty($FacultyEditPassword) && !$check->cmp_string($VerifyFacultyEditPassword,$FacultyEditPassword)){
							$ERRORS['VerifyFacultyEditPassword'] = "Passwords entered do not match";
						}
						
						if(!verifyOldPassword($EditID,$FacultyOldPassword)){
							$ERRORS['FacultyOldPassword'] = "Old password did not match with the one in our database";
						}
						
						// check for errors
						if(sizeof($ERRORS) > 0){
							$MSG['ERROR'] = ErrorMessage("ERRORS ENCOUNTERED!");
						}
						else{
							//Update Faculty Password
							$updateFacultySql = sprintf("UPDATE `".DB_PREFIX."portal` SET `Password` = '%s', `Token` = '%s' WHERE `LoginID` = '%s'", $EncryptedPass, $Token, $EditID);                    
							//Execute the query or die if there is a problem
							db_query($updateFacultySql,DB_NAME,$conn);
							
							//Check if saved
							if(db_affected_rows($conn)){
								$saved = true;
								$MSG['CONFIRM'] = ConfirmMessage("Your account password has been updated successfully.");
							}else{
								$saved = false;
								$MSG['ERROR'] = WarnMessage("No changes were made on your account details.");  
							}
						}
					}
					?>
				<form id="resetform" name="Reset" method="post" action="?tab=9&amp;task=<?=$task?>">
					<div class="row">
						<div class="col-lg-12">
							<p>To reset your password, please fill in the following fields.</p>
							<?php 
							if(sizeof($MSG)>0) {
								foreach($MSG as $MESSAGE){
									echo $MESSAGE;
								}					
							}
							?>
							<p><strong>PASSWORD RESET FORM</strong></p>
							<p class="text-danger"><strong>FIELDS MARKED WITH ASTERISKS (*) ARE REQUIRED</strong></p>
						</div>
						<div class="col-sm-12 col-md-6">
							<div class="form-group">
								<label for="">Faculty ID: <span class="text-danger">*</span></label>
								<?=$faculty['FacultyID']; ?>
							</div>
							<div class="form-group">
								<label for="">Old Password: <span class="text-danger">*</span></label>
								<input type="password" value="<?=$faculty['OldPassword']; ?>" name="OldPassword" class="form-control required">
								&nbsp;<span class="text-danger"><?=$ERRORS['OldPassword'];?></span>
							</div>
							<div class="form-group">
								<label for="">New Password: <span class="text-danger">*</span></label>
								<input type="password" value="<?=$faculty['NewPassword']; ?>" name="NewPassword" class="form-control required">
								&nbsp;<span class="text-danger"><?=$ERRORS['NewPassword'];?></span>
							</div>
							<div class="form-group">
								<label for="">Verify Password: <span class="text-danger">*</span></label>
								<input type="password" value="<?=$faculty['VerifyPass']; ?>" name="VerifyPass" class="form-control required">
								&nbsp;<span class="text-danger"><?=$ERRORS['VerifyPass'];?></span>
							</div>
							<div class="form-group">
								<input type="hidden" value="<?=$faculty['FacultyID']; ?>" name="FacultyID">
								<input type="submit" name="<?=$task?>" value="Save" class="btn btn-primary">
							</div>
						</div>
					</div>
				</form>
				<?php
				break;
				default:
					echo ErrorMessage("Invalid request! The system failed to process your request. If the problem persists, please contact us.");
      }
      ?>
			<!--End Forms-->
		</div>
	</div>
</div>
<!-- /.row -->