<?php
//Import the PHPMailer class into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once("$class_dir/class.validator.php3");
?>
<script>
<!--//
//Define page title
document.title = "<?=SYSTEM_SHORT_NAME?> - Student | My Account";
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
			//Require Student ID to modify
			$EditID = !empty($student['StudentID'])?$student['StudentID']:NULL;
			//Get requested task/default is edit
			$task = isset($_GET['task'])?$_GET['task']:"edit";
			
			$task = strtolower($task);
			switch($task) {
				case "view":
					?>
					<div class="panel panel-default">
						<div class="panel-heading"><i class="fa fa-user fa-fw"></i> <?=$student['StudentName']?></div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-3">
									<?php
									if(!empty($student['PassportPhoto'])){
										echo '<img class="img-responsive img-circle img-left" style="max-height:200px;" src="'.$student['PassportPhoto'].'">';
									}else{
										echo '<img src="'.IMAGE_FOLDER.'/no-avatar.png" class="img-responsive img-circle img-left" alt="No image">';
									}
									?>
								</div>
								<div class="col-md-9">
									<p><strong>Gender: </strong><?=$student['Gender']?></p>
									<p><strong>Birthday: </strong><?=fixdateshortdob($student['DOB'])?></p>
									<p><strong>Email: </strong><?=$student['Email']?></p>
									<p><strong>Phone: </strong><?=$student['Phone']?></p>
									<p><strong>Address: </strong><?=$student['Address']." ".$student['City']?></p>
									<p><strong>Registration Date: </strong><?=fixdatelong($student['RegDate'])?></p>
									<p><a class="btn btn-primary" href="?tab=8?task=edit">Edit Profile</a></p>
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
						$FName = secure_string($_POST['FName']);
						$MName = secure_string($_POST['MName']);
						$LName = secure_string($_POST['LName']);
						$StudentName = $FName." ".$LName;
						$Email = isset($_POST['Email'])?secure_string($_POST['Email']):"";
						$Phone = isset($_POST['Phone'])?secure_string($_POST['Phone']):"";
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
						// validate "Phone" field
						if(!$check->is_phone($Phone))
						$ERRORS['Phone'] = "Valid phone number is required";						
						
						// check for errors
						if(sizeof($ERRORS) > 0){
							$MSG['ERROR'] = ErrorMessage("ERRORS ENCOUNTERED!");
						}
						else{
							//RUN FILE UPLOADS								
							$PassportPhotoExt = findexts($PassportPhoto);
							if(!empty($PassportPhotoTemp)){
								$PassportPhotoName = friendlyName($StudentName)."-PassportPhoto-".$UploadDate.".".$PassportPhotoExt;
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
							//Update Student
							$updateStudentSql = sprintf("UPDATE `".DB_PREFIX."students` SET `FName` = '%s', `LName` = '%s', `Email` = '%s', `Phone` = '%s', `Address` = '%s', `City` = '%s', `PassportPhoto` = '%s' WHERE `StudentID` = '%s'", $FName, $LName, $Email, $Phone, $Address, $City, $PassportPhotoPath, $EditID);
							
							//Execute the query or die if there is a problem
							db_query($updateStudentSql,DB_NAME,$conn);
							
							//Check if saved
							if(db_affected_rows($conn)){		
								// Set mail function
								$mail = new PHPMailer; // defaults to using php "mail()"
								
								$body = "<html><head>
								<title>$StudentName - Account Updated</title>
								</head><body><p>Dear $StudentName, <br><br>You received this email because you updated your account on ".SYSTEM_NAME." website.</p>
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
								$mail->Subject = "$StudentName - Account Updated";
								$mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; 		
								$mail->msgHTML($body);
								$mail->isHTML(true); // send as HTML
								$mail->addAddress($Email, $StudentName);
								
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
				<form id="editform" name="Edit" method="post" action="?tab=8&amp;task=<?=$task?>" enctype="multipart/form-data">
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
								<label for="FName">First Name: <span class="text-danger">*</span></label>
								<input type="text" value="<?=$student['FName']; ?>" name="FName" class="form-control required">
								&nbsp;<span class="text-danger"><?=$ERRORS['FName'];?></span>
							</div>
							<div class="form-group">
								<label for="MName">Middle Name: </label>
								<input type="text" value="<?=$student['MName']; ?>" name="MName" class="form-control">
								&nbsp;<span class="text-danger"><?=$ERRORS['MName'];?></span>
							</div>
							<div class="form-group">
								<label for="LName">Last Name: <span class="text-danger">*</span></label>
								<input type="text" value="<?=$student['LName']; ?>" name="LName" class="form-control required">
								&nbsp;<span class="text-danger"><?=$ERRORS['LName'];?></span>
							</div>
							<div class="form-group">
								<label for="PassportPhoto">Profile Photo: <span class="text-danger">*</span></label>
								<?php
								if(!empty($student['PassportPhoto'])){
									echo '<img class="img-responsive img-circle" style="margin:5px 0 10px 0;max-height:100px;" src="'.$student['PassportPhoto'].'">';
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
										if($k == $student['Gender']){
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
								<input type="email" value="<?=$student['Email']; ?>" name="Email" class="form-control required email">
								&nbsp;<span class="text-danger"><?=$ERRORS['Email'];?></span>
							</div>
							<div class="form-group">
								<label for="Phone">Phone: <span class="text-danger">*</span></label>
								<input id="phonenumber" type="tel" value="<?=$student['Phone']; ?>" name="Phone" class="form-control required">
								&nbsp;<span id="validate-msg" class="text-danger"><?=$ERRORS['Phone'];?></span>
							</div>
						</div>
						<div class="col-sm-12 col-md-6">
							<div class="form-group">
								<label for="Address">Address: </label>
								<textarea name="Address" class="form-control" rows="6"><?=decode($student['Address'])?></textarea>
							</div>
							<div class="form-group">
								<label for="City">City/Town: <span class="text-danger">*</span></label>
								<input type="text" value="<?=$student['City']; ?>" name="City" class="form-control required">
								&nbsp;<span class="text-danger"><?=$ERRORS['City'];?></span>
							</div>
							<div class="form-group">
								<label for="State">State/County: </label>
								<input type="text" value="<?=$student['State']; ?>" name="State" class="form-control">
							</div>
							<div class="form-group">
								<label for="PostCode">Zip/Postal Code: </label>
								<input type="text" value="<?=$student['PostCode']; ?>" name="PostCode" maxlength="5" class="form-control">
								&nbsp;<span class="text-danger"><?=$ERRORS['PostCode'];?></span>
							</div>
							<div class="form-group">
								<label for="Country">Country: <span class="text-danger">*</span></label>
								<select name="Country" class="form-control">
									<option value="None">--Select--</option>
									<?php
									foreach(list_countries() as $k => $v){
										if($k == $student['Country']){
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
						$StudentOldPassword = isset($_POST['OldPassword'])?secure_string($_POST['OldPassword']):"";
						$StudentEditPassword = isset($_POST['NewPassword'])?secure_string($_POST['NewPassword']):"";
						$VerifyStudentEditPassword = isset($_POST['VerifyPass'])?secure_string($_POST['VerifyPass']):"";
						$EncryptedPass = hashedPassword($VerifyStudentEditPassword);
						$Token = md5(time());
						
						// Validator contractor
						$check = new validator();
						// validate entry
						// validate "password" field
						if(!empty($StudentEditPassword) && !$check->is_password($StudentEditPassword)){
							$ERRORS['NewPassword'] = "Password must be at least 7 letters mixed with digits and symbols";
						}
						// validate "verifypass" field
						if(!empty($StudentEditPassword) && !$check->cmp_string($VerifyStudentEditPassword,$StudentEditPassword)){
							$ERRORS['VerifyPass'] = "Passwords entered do not match";
						}
						
						if(!verifyOldPassword($EditID,$StudentOldPassword)){
							$ERRORS['OldPassword'] = "Old password did not match with the one in our database";
						}
						
						// check for errors
						if(sizeof($ERRORS) > 0){
							$MSG['ERROR'] = ErrorMessage("ERRORS ENCOUNTERED!");
						}
						else{
							//Update Student Password
							$updateStudentSql = sprintf("UPDATE `".DB_PREFIX."portal` SET `Password` = '%s', `Token` = '%s' WHERE `LoginID` = '%s'", $EncryptedPass, $Token, $EditID);                    
							//Execute the query or die if there is a problem
							db_query($updateStudentSql,DB_NAME,$conn);
							
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
				<form id="resetform" name="Reset" method="post" action="?tab=8&amp;task=<?=$task?>">
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
								<label for="">Student ID: <span class="text-danger">*</span></label>
								<?=$student['StudentID']; ?>
							</div>
							<div class="form-group">
								<label for="">Old Password: <span class="text-danger">*</span></label>
								<input type="password" value="" name="OldPassword" class="form-control required">
								&nbsp;<span class="text-danger"><?=$ERRORS['OldPassword'];?></span>
							</div>
							<div class="form-group">
								<label for="">New Password: <span class="text-danger">*</span></label>
								<input type="password" value="" name="NewPassword" class="form-control required">
								&nbsp;<span class="text-danger"><?=$ERRORS['NewPassword'];?></span>
							</div>
							<div class="form-group">
								<label for="">Verify Password: <span class="text-danger">*</span></label>
								<input type="password" value="" name="VerifyPass" class="form-control required">
								&nbsp;<span class="text-danger"><?=$ERRORS['VerifyPass'];?></span>
							</div>
							<div class="form-group">
								<input type="hidden" value="<?=$student['StudentID']; ?>" name="StudentID">
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