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
document.title = "<?=SYSTEM_SHORT_NAME?> - Portal | Application Form";
//-->
</script>

<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="header-img">
				<a href="<?=PARENT_HOME_URL;?>"><img class="img-responsive" src="<?=SYSTEM_LOGO_URL;?>" alt="Logo"></a>
			</div>
			<div class="activate-panel panel panel-default">
				<div class="panel-heading">
					<div class = "row">
						<div class= "col-md-3">
							<h3 class="panel-title">Evarsity Job Application Form</h3>
						</div>
						<div class = "col-md-9">
							<?php echo getInnerMenu(PARENT_HOME_URL); ?>
						</div>
					</div>
				</div>
				<div class="panel-body">
					<h2>Job Summary</h2> 
					<p>We are inviting you to share your knowledge with others through an online lecturing position at our Evarsity.<br>
					Qualification Level: Diploma or Degree or Masters or PhD.<br>
					Experience Level: Entry level <br>
					Experience Length: 1 year </p>

					<h2>Job Description</h2>

<p>1. To prepare schemes of work and  lesson  plans for online lecturing<br>
2. To lecture students on self selected courses using online tutorial aids such as Videos, Pdfs, Chats.<br>
3. To administer CATS, Quizzes and assignments online<br>
4. To administer exams online</p>

<h2>Requirements</h2>

<p>1. Be a diploma holder for you to qualify to lecture certificate students in your area of study<br>
2. Be a degree holder for you to qualify to lecture certificate and diploma students in your area of study<br>
3. Be a Masters holder for you to qualify to lecture certificate, diploma and degree  students in your area of study <br>
4. Be a Ph.D holder for you to qualify to lecture certificate, diploma, degree, Masters and Ph.D  students in your area of study<br> 

<h2>Extra Requirements</h2>

<p>1. Have a Certified Online Trainer (COT) qualification from Finstock Evarsity.</p>

<h2>Compensation</h2>

<p>The compensation rate is kes 1000 (usd 10) per hour per unit for short courses, international courses, HRMPEB courses.   The compensation rate is kes 500 ( usd 5)  per hour per unit for KASNEB courses and school programs (Diplomas and Certificates).</p>

					<h2>Apply Now</h2>
					<style>
					p{text-align: justify;}
					</style>
					<p>*All fields marked with asteriks (*) are required to complete this job application</p>
					<h4>Application Instructions</h4>
					<p>*At the end of this application, you will be asked whether you have a "<b>Certificate in Online Training</b>" from Finstock Evarsity. If you respond "yes", you will be requested to attach the certificate.</p>
					<p>*If you attach the certificate, you will automatically be allocated a faculty number and your application will be successful.</p>
					<p>*If you do not have the certificate, you will be asked whether you wish to apply and be trained on how to lecture online.  Should you choose "<b>yes</b>", you will be enrolled for the 10 hour course, to be undertaken at your convenience. At the same time, you shall be admitted as a member of faculty. </p>
					<p>*Please note that this job offer guarantees to engage you for a minimum 10 hours at a rate of kes 1000  (Usd 10) or a minimum of 15 hours at a rate of  kes 500 ( usd 5) to enable you to recoup your training costs under the Certified Online Trainer course. </p>
					<p>*A course application fee of KES 1000 will be required in order to complete your application and process your student details.</p>
					<p>*Should you be unwilling to undergo the COT certification training, the application will still be successful,and you will be allocated a faculty number. However, we cannot guarantee your ability to implement online training in our platform.</p>
					<div class="reg-wizard">
						<div id="reg-step-1" class="col-xs-3 reg-wizard-step active">
							<div class="text-center reg-wizard-stepnum">Step 1</div>
							<div class="progress">
								<div class="progress-bar">
								</div>
							</div>
							<a href="#" class="reg-wizard-dot" id="wizard-step-1"></a>
							<div class="reg-wizard-info text-center">Personal Information</div>
						</div>
						<div id="reg-step-2" class="col-xs-3 reg-wizard-step disabled">
							<div class="text-center reg-wizard-stepnum">Step 2</div>
							<div class="progress">
								<div class="progress-bar">
								</div>
							</div>
							<a href="#" class="reg-wizard-dot" id="wizard-step-2"></a>
							<div class="reg-wizard-info text-center">Academic &amp; Work Experience</div>
						</div>
						<div id="reg-step-3" class="col-xs-3 reg-wizard-step disabled">
							<div class="text-center reg-wizard-stepnum">Step 3</div>
							<div class="progress">
								<div class="progress-bar">
								</div>
							</div>
							<a href="#" class="reg-wizard-dot" id="wizard-step-3"></a>
							<div class="reg-wizard-info text-center">Additional Information &amp; Confirm</div>
						</div>
						<div id="reg-step-4" class="col-xs-3 reg-wizard-step disabled">
							<div class="text-center reg-wizard-stepnum">Step 4</div>
							<div class="progress">
								<div class="progress-bar">
								</div>
							</div>
							<a href="#" class="reg-wizard-dot" id="wizard-step-4"></a>
							<div class="reg-wizard-info text-center">Complete application & Pay</div>
						</div>
					</div>
					<?php
					require "$incl_dir/recaptchalib.php";
					require_once("$incl_dir/mysqli.functions.php");
					require_once("$class_dir/class.OAuth.php");
					//Open database connection
					$conn = db_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
					//Get requested task/default is add
					$task = isset($_GET['task'])?$_GET['task']:"add";
					//echo getCourseUnits("CHRPL001")[0]['UName'];
					// $units = (getCourseUnits("CHRPL001"));
					// foreach($units as $u):
					// 	echo $u['UID'].' '.$u['UnitID'].'<br>';
					// endforeach;
					$task = strtolower($task);
					switch($task) {
						case "add":
						// Array to store the error messages
						$FIELDS = array();
						$ERRORS = array();
						$CONFIRM = array();
						// If course is avaialble in query string
						//$FIELDS['cot'] = isset($_GET['cot'])?$_GET['cot']:"";
						
						$saved = false;
						//Execute Commands
						if(isset($_POST["load"])){						 
							//PERSONAL INFORMATION
							$FIELDS['surname'] = secure_string(ucwords($_POST['surname']));
							$FIELDS['firstname'] = secure_string(ucwords($_POST['firstname']));
							$FIELDS['middlename'] = secure_string(ucwords($_POST['middlename']));
							$FIELDS['physicaladdress'] = secure_string($_POST['physicaladdress']);				  
							$FIELDS['postalcode'] = secure_string($_POST['postalcode']);							
							$FIELDS['city'] = secure_string($_POST['city']);
							$FIELDS['state'] = secure_string($_POST['state']);
							$FIELDS['phonenumber'] = secure_string($_POST['phonenumber']);
							$FIELDS['emailaddress'] = secure_string($_POST['emailaddress']);
							$FIELDS['verifyemailaddress'] = secure_string($_POST['verifyemailaddress']);				  
							$FIELDS['dob'] = secure_string($_POST['dob']);
							$FIELDS['citizenship'] = secure_string($_POST['citizenship']);							
							$FIELDS['gender'] = secure_string($_POST['gender']);
							$FIELDS['identityno'] = secure_string($_POST['identityno']);
							//UNITS APPLYING TO TEACH:- A LIST OF UNIT IDs
							$FIELDS['course'] = $_POST['course'];
							$FIELDS['course'] = implode(",", $FIELDS['course']);
							//EDUCATION : ARRAYS
							$FIELDS['institution'] = $_POST['institution'];
							$FIELDS['certificate'] = $_POST['certificate'];
							$FIELDS['fromyear'] = $_POST['fromyear'];
							$FIELDS['toyear'] = $_POST['toyear'];
							$FIELDS['grade'] = $_POST['grade'];
							//WORK EXPERIENCE : ARRAYS
							$FIELDS['employer'] = $_POST['employer'];
							$FIELDS['job_title'] = $_POST['job_title'];
							$FIELDS['fromyr'] = $_POST['fromyr'];
							$FIELDS['toyr'] = $_POST['toyr'];
							$FIELDS['roles'] = $_POST['roles'];
							//ONLINE TRAINING COUSE
							$FIELDS['cot'] = secure_string($_POST['cot']);
							$FIELDS['pursue'] = secure_string($_POST['pursue']);
							//SOURCE TRACKING
							$FIELDS['sponsorname'] = secure_string($_POST['sponsorname']);
							$FIELDS['othersource'] = secure_string($_POST['othersource']);
							$FIELDS['declaration'] = secure_string($_POST['declaration']);
							//IMAGE PROCESSING
							$FullName = $FIELDS['surname']." ".$FIELDS['firstname']." ".$FIELDS['middlename'];
							//cot cert
							$CotCert = $_FILES["CotCert"]["name"];
							$CotCertTemp = $_FILES["CotCert"]["tmp_name"];
							//cv docs
							$cv = $_FILES["cv"]["name"];
							$cvTemp = $_FILES["cv"]["tmp_name"];
							//cover letter
							$coverletter = $_FILES["coverletter"]["name"];
							$coverletterTemp = $_FILES['coverletter']['tmp_name'];
							//certificates files
							$CertFile = $_FILES["certfile"]["name"];
							$CertFileTemp = $_FILES['certfile']['tmp_name'];
							//identity files
							$IndentityPhotoFile = $_FILES["identityimage"]["name"];
							$IndentityPhotoFileTemp = $_FILES["identityimage"]["tmp_name"];
							//passport size photo
							$PhotoFile = $_FILES["passportphoto"]["name"];
							$PhotoFileTemp = $_FILES['passportphoto']['tmp_name'];

							$UploadDate = date('dmYHis');
							$allowed_mimes = allowed_doc_mime_types();
							$Token = md5(time());				  
							
							// validator contractor
							$check = new validator();
							// validate entry	
							// validate "surname" field
							if(!$check->is_String($FIELDS['surname']))
							$ERRORS['surname'] = "A valid surname is required";
							// validate "firstname" field
							if(!$check->is_String($FIELDS['firstname']))
							$ERRORS['firstname'] = "A valid firstname is required";
							// validate "middlename" field
							if(!$check->is_String($FIELDS['middlename']))
							$ERRORS['middlename'] = "A valid middlename is required";					  			  
							// validate "emailaddress" field
							if(!$check->is_email($FIELDS['emailaddress']))
							$ERRORS['emailaddress'] = "A valid email address is required";				  
							// validate "verifyemailaddress" field
							if(!$check->cmp_string($FIELDS['verifyemailaddress'],$FIELDS['emailaddress']))
							$ERRORS['verifyemailaddress'] = "Emails entered do not match";			  
							// validate "phonenumber" field
							if(!$check->is_phone($FIELDS['phonenumber']))
							$ERRORS['phonenumber'] = "Phone number is required";
							// validate "dob" field
							if(!empty($FIELDS['dob'])){
								$SplitDate = explode('/', $FIELDS['dob']);// Split date by '/'
								//checkdate($month, $day, $year)
								if(checkdate($SplitDate[0],$SplitDate[1],$SplitDate[2])){			
									$FIELDS['dbDob'] = db_fixdate($FIELDS['dob']);// YYYY-dd-mm
								}else{
									$ERRORS['dob'] = "Date of birth is required";
								}
							}
							// validate "city" field
							if(!$check->is_String($FIELDS['city']))
							$ERRORS['city'] = "City/Town is required";
							// validate "city" field
							if(!$check->is_String($FIELDS['state']))
							$ERRORS['state'] = "State/County is required";
							// validate "postalcode" field
							if(empty($FIELDS['postalcode']))
							$ERRORS['postalcode'] = "Postal code is required";
							// validate "citizenship" field
							if($FIELDS['citizenship'] == "None")
							$ERRORS['citizenship'] = "Please select your citizenship";
							// validate "gender" field
							if($FIELDS['gender'] == "None")
							$ERRORS['gender'] = "Please select your gender";
							// validate "identityno" field
							if(empty($FIELDS['identityno']))
							$ERRORS['identityno'] = "ID/Passport number is required";
							// validate "cv" upload file
							if(empty($cvTemp) || !in_array($_FILES["cv"]["type"], $allowed_mimes) || $_FILES["cv"]["size"] > 800000)
							$ERRORS['cv'] = "Uploaded file must be a supported image or document not greater than 800KB";
							// validate "coverletter" upload file
							if(empty($coverletterTemp) || !in_array($_FILES["coverletter"]["type"], $allowed_mimes) || $_FILES["coverletter"]["size"] > 800000)
							$ERRORS['coverletter'] = "Uploaded photo must be a supported image or document not greater than 800KB";
							if(empty($IndentityPhotoFileTemp) || !in_array($_FILES["identityimage"]["type"], $allowed_mimes) || $_FILES["identityimage"]["size"] > 800000)
							$ERRORS['identityimage'] = "Uploaded file must be a supported image or document not greater than 800KB";
							// validate "passportphoto" upload file
							if(empty($PhotoFileTemp) || !in_array($_FILES["passportphoto"]["type"], $allowed_mimes) || $_FILES["passportphoto"]["size"] > 800000)
							$ERRORS['passportphoto'] = "Uploaded photo must be a supported image or document not greater than 800KB";
							// validate "cot" field
							if($FIELDS['cot'] == "")
							$ERRORS['cot'] = "Please select one";	
							// validate "declaration" field
							if($FIELDS['declaration'] != 1)
							$ERRORS['declaration'] = "You need to accept this declaration";							
							// Validate Google reCAPTCHA
							if( !recapture_verify( $_POST["g-recaptcha-response"], $_SERVER["REMOTE_ADDR"] ) )
							$ERRORS['reCaptcha'] = "You're a robot. If not, please try again.";
							
              //Check if this candidate is already registered	
							$checkDuplicateSql = sprintf("SELECT `Email` FROM `".DB_PREFIX."faculties` WHERE `Email` = '%s'", $FIELDS['emailaddress']);
							$result = db_query($checkDuplicateSql,DB_NAME,$conn);
						  if(db_num_rows($result)>0){
						  $ERRORS['emailaddress'] = "An account with this email address has already been registered.";
						  }
						  //db_free_result($result);
							
							// check for errors
							if(sizeof($ERRORS) > 0){
								$ERRORS['MSG'] = ErrorMessage("PLEASE CORRECT HIGHLIGHTED FIELDS!");
							}
							else{
								//RUN FILE UPLOADS
								//COTCERT
								if($FIELDS['cot']==1 && !empty($CotCertTemp)){
								$CotCertExt = findexts($CotCert);
								if(!empty($CotCertTemp)){
									$CotCertName = friendlyName($FullName)."-CotCert-".$UploadDate.".".$CotCertExt;
									$CotCertPath = UPLOADS_PATH.$CotCertName;
									if(move_uploaded_file($CotCertTemp, $CotCertPath)){
										$CotCertPath = UPLOADS_FOLDER."/".$CotCertName;
									}else{
										$CotCertPath = "uploaded but not saved";
									}
								}else{
									$CotCertPath = "not uploaded";
								}
							}else{ $CotCertPath = "not uploaded"; }
								//CV								
								$cvExt = findexts($cv);
								if(!empty($cvTemp)){
									$cvName = friendlyName($FullName)."-CV-".$UploadDate.".".$cvExt;
									$cvPath = UPLOADS_PATH.$cvName;
									if(move_uploaded_file($cvTemp, $cvPath)){
										$cvPath = UPLOADS_FOLDER."/".$cvName;
									}else{
										$cvPath = "";
									}
								}else{
									$cvPath = "";
								}
								//CL
								$coverletterExt = findexts($coverletter);
								if(!empty($coverletterTemp)){
									$coverletterName = friendlyName($FullName)."-CL-".$UploadDate.".".$coverletterExt;
									$coverletterPath = UPLOADS_PATH.$coverletterName;
									if(move_uploaded_file($coverletterTemp, $coverletterPath)){
										$coverletterPath = UPLOADS_FOLDER."/".$coverletterName;
									}else{
										$coverletterPath = "";
									}
								}else{
									$coverletterPath = "";
								}	
								//identity image								
								$IndentityPhotoExt = findexts($IndentityPhotoFile);
								if(!empty($IndentityPhotoFileTemp)){
									$IndentityPhotoFileName = friendlyName($FullName)."-identity-".$UploadDate.".".$IndentityPhotoExt;
									$uploadIdentityPhotoPath = UPLOADS_PATH.$IndentityPhotoFileName;
									if(move_uploaded_file($IndentityPhotoFileTemp, $uploadIdentityPhotoPath)){
										$IndentityPhotoFilePath = UPLOADS_FOLDER."/".$IndentityPhotoFileName;
									}else{
										$IndentityPhotoFilePath = "";
									}
								}else{
									$IndentityPhotoFilePath = "";
								}
								//passport sized photo
								$PhotoExt = findexts($PhotoFile);
								if(!empty($PhotoFileTemp)){
									$PhotoFileName = friendlyName($FullName)."-photo-".$UploadDate.".".$PhotoExt;
									$uploadPhotoPath = UPLOADS_PATH.$PhotoFileName;
									if(move_uploaded_file($PhotoFileTemp, $uploadPhotoPath)){
										$PhotoFilePath = UPLOADS_FOLDER."/".$PhotoFileName;
									}else{
										$PhotoFilePath = "";
									}
								}else{
									$PhotoFilePath = "";
								}					
								$FIELDS['cv'] = $cvPath;
								$FIELDS['coverletter'] = $coverletterPath;
								$FIELDS['certfile'] = $CertFilePath;
								$FIELDS['identityimage'] = $IndentityPhotoFilePath;
								$FIELDS['passportphoto'] = $PhotoFilePath;
							
								
								//GET REGISTRATION FEE
								$regsql = sprintf("SELECT `pay_amount` FROM `".DB_PREFIX."payment_categs` WHERE `payment_name` = 'registration'");
								//Set the result and run the query
								$result = db_query($regsql,DB_NAME,$conn);
								$row = db_fetch_array($result);
								$reg_fee = $row["pay_amount"];
								
								//GENERATE A TENDATIVE STAFF ID
								$maxidsql = "SELECT MAX(`UID`) AS 'UID' FROM `".DB_PREFIX."faculties`";
								
								$maxidres = db_query($maxidsql,DB_NAME,$conn);	
								$maxidrow = db_fetch_array($maxidres);
								if ($maxidrow["UID"] == 0) {
									$MUID = 100;
								} else {
									$MUID = $maxidrow["UID"]+1;
								}
								$FIELDS['code']="FA";
								$FIELDS['StaffID'] = "FE" ."/". $FIELDS['code'] ."/". $MUID ."/". date('y');
								
								//GENERATE A TENTATIVE STUDENT ID
								$maxidsql = "SELECT MAX(`UID`) AS 'UID' FROM `".DB_PREFIX."students`";
								
								$maxidres = db_query($maxidsql,DB_NAME,$conn);	
								$maxidrow = db_fetch_array($maxidres);
								if ($maxidrow["UID"] == 0) {
									$MUID = 100;
								} else {
									$MUID = $maxidrow["UID"]+1;
								}
								$FIELDS['StudentID'] = "FE/COT/". $MUID ."/". date('Y');
								$hash_stud = md5($FIELDS['StudentID']);
								
								//Add new as staff
								$FIELDS['Title'] = "Mr/Ms";
								$newStaffSql = sprintf("INSERT INTO `".DB_PREFIX."faculties` (`FacultyID`, `Title`, `FName`, `MName`, `LName`, `Gender`, `DOB`, `Email`, `WPhone`, `MPhone`, `Address`, `City`, `State`, `PostCode`, `Country`, `CotCertified`, `RequestedCot`, `PassportPhoto`, `CotCert`, `IdentityImage`) 
								VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s', %d, %d, '%s', '%s', '%s')", 
								$FIELDS['StaffID'], $FIELDS['Title'], $FIELDS['firstname'], $FIELDS['middlename'], $FIELDS['surname'], $FIELDS['gender'], $FIELDS['dbDob'], $FIELDS['emailaddress'], $FIELDS['phonenumber'], $FIELDS['phonenumber'], $FIELDS['physicaladdress'], $FIELDS['city'], $FIELDS['state'], $FIELDS['postalcode'], $FIELDS['citizenship'], $FIELDS['cot'], $FIELDS['pursue'], $FIELDS['passportphoto'], $CotCertPath, $FIELDS['identityimage']);
								db_query($newStaffSql,DB_NAME,$conn);

							//add to faculty applications db\
							$newStaffSql = sprintf("INSERT INTO `".DB_PREFIX."faculty_applications` (`FacultyID`, `FacultyEmail`, `CV`, `CoverLetter`, `UnitsApplied`) VALUES ('%s','%s','%s','%s','%s')", $FIELDS['StaffID'], $FIELDS['emailaddress'], $FIELDS['cv'], $FIELDS['coverletter'], $FIELDS['course']);
							db_query($newStaffSql,DB_NAME,$conn);
								
								//Add new as student if is_yes pursuer cot
								if($FIELDS['cot']==0 && $FIELDS['pursue']==1){
									$FIELDS['coz']="COT";
									$FIELDS['trim']= "1/1";
								$newStudentSql = sprintf("INSERT INTO `".DB_PREFIX."students` 
								(`StudentID`, `FName`, `MName`, `LName`, `Phone`, `Email`, `DOB`, `Gender`, `Address`, `City`, `State`, `PostCode`, `Country`, `IdentityNumber`, `IdentityImage`, `PassportPhoto`, `Courses`, `YrTrim`) 
								VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')", 
								$FIELDS['StudentID'], $FIELDS['firstname'], $FIELDS['middlename'], $FIELDS['surname'], $FIELDS['phonenumber'], $FIELDS['emailaddress'], $FIELDS['dbDob'], $FIELDS['gender'], $FIELDS['physicaladdress'], 
								$FIELDS['city'], $FIELDS['state'], $FIELDS['postalcode'], $FIELDS['citizenship'], $FIELDS['identityno'], $FIELDS['identityimage'], $FIELDS['passportphoto'], $FIELDS['coz'], $FIELDS['trim']);								
								db_query($newStudentSql,DB_NAME,$conn);
							 }
								
								//Check if saved
								if(db_affected_rows($conn)){
									//Add couses done before
									$count = count($FIELDS['institution']);
									for($i = 0; $i < $count; $i++){
										$FIELDS['period'] = $FIELDS['fromyear'][$i]."-".$FIELDS['toyear'][$i];
										
										$CertFileExt = findexts($CertFile[$i]);
										if (!empty($CertFileTemp[$i])) {
											$CertFileName = friendlyName($FullName)."-cert-".friendlyName($FIELDS['certificate'][$i])."-".$UploadDate.".".$CertFileExt;
											$uploadCertFilePath = UPLOADS_PATH.$CertFileName;
											//Just incase of an internal problem
											if(move_uploaded_file($CertFileTemp[$i], $uploadCertFilePath)){
												$CertFilePath[] = UPLOADS_FOLDER."/".$CertFileName;
											}else{
												$CertFilePath[] = "";
											}
										}else{
											$CertFilePath[] = "";
										}
										$FIELDS['englishproficiency'] = 1;
										$FIELDS['englishneeded']= 0;
										$newClientSql = sprintf("INSERT INTO `".DB_PREFIX."ac_qualifications` (`StudentID`, `Institution`, `Certificate`, `Period`, `GradeMark`, `EngProficiency`, `EngHelp`, `CertFile`) 
										VALUES ('%s','%s','%s','%s','%s','%s','%s','%s')", 
										$FIELDS['StudentID'], $FIELDS['institution'][$i], $FIELDS['certificate'][$i], $FIELDS['period'], $FIELDS['grade'][$i], $FIELDS['englishproficiency'], $FIELDS['englishneeded'], $CertFilePath[$i]);
										//Execute query
										db_query($newClientSql,DB_NAME,$conn);
									}
								///ad previous employments
									$count = count($FIELDS['employer']);
									for($i = 0; $i < $count; $i++){
										$FIELDS['period'] = $FIELDS['fromyr'][$i]."-".$FIELDS['toyr'][$i];
										$newClientSql = sprintf("INSERT INTO `".DB_PREFIX."faculty_work_experience` (`StaffID`, `PreviousEmployer`, `PreviousJobTitle`, `Period`, `PreviousRoles`) 
										VALUES ('%s','%s','%s','%s','%s')", 
										$FIELDS['StaffID'], secure_string($FIELDS['employer'][$i]), secure_string($FIELDS['job_title'][$i]), $FIELDS['period'], secure_string($FIELDS['roles'][$i]));
										//Execute query
										db_query($newClientSql,DB_NAME,$conn);
									}
									//Create sessions for reg fee payment GATEWAY if want to do cot
									if($FIELDS['cot']==0 && $FIELDS['pursue']==1){					  
									$_SESSION['STUD_ID_HASH'] = $hash_stud;
									$_SESSION['STUD_ID'] = $FIELDS['StudentID'];
									$_SESSION['AMOUNT'] = $reg_fee;
									$_SESSION['STUD_FNAME'] = $FIELDS['firstname'];
									$_SESSION['STUD_LNAME'] = $FIELDS['surname'];
									$_SESSION['STUD_EMAIL'] = $FIELDS['emailaddress'];
									$_SESSION['STUD_TEL'] = $FIELDS['phonenumber'];
									$_SESSION['COURSE_ID'] = $FIELDS['coz'];
									
									//Proceed to pay
									redirect("?do=apply&task=pay&token=$Token");

									}else{
									    echo '<script>alert("Application Sent!");</script>';
										redirect(PARENT_HOME_URL);
									}
								}				
								else{
									$saved = FALSE;
									$ERRORS['MSG'] = ErrorMessage("Failed to save successfully. Please try again later...");
								}
							}
						}
						
						if(!$saved){
						?>
					<div class="col-md-12">
						<div id="hideMsg">
							<?php if(sizeof($ERRORS['MSG'])>0) echo $ERRORS['MSG']; ?>
						</div>
						<!--  -->
						<form id="applicationForm" name="application-form" method="post" action="?do=apply&amp;task=<?=$task?>" enctype="multipart/form-data">
							<!-- Step 1 -->
							<div id="step-1" class="form-sec">
								<h2 class="text-primary">Personal Information</h2>
								<div class="row">
									<div class="form-group col-sm-4">
										<label for="surname" class="">Surname <abbr class="text-danger" title="required">*</abbr></label>
										<input type="text" class="form-control required" name="surname" id="surname" value="<?=$FIELDS['surname'];?>">
										<span class="text-danger"><?=$ERRORS['surname'];?></span>
									</div>
									<div class="form-group col-sm-4">
										<label for="firstname" class="">First Name <abbr class="text-danger" title="required">*</abbr></label>
										<input type="text" class="form-control required" name="firstname" id="firstname" value="<?=$FIELDS['firstname'];?>">
										<span class="text-danger"><?=$ERRORS['firstname'];?></span>
									</div>
									<div class="form-group col-sm-4">
										<label for="middlename" class="">Middle Name <abbr class="text-danger" title="required">*</abbr></label>
										<input type="text" class="form-control required" name="middlename" id="middlename" value="<?=$FIELDS['middlename'];?>">
										<span class="text-danger"><?=$ERRORS['middlename'];?></span>
									</div>
								</div>
								<div class="row">
									<div class="form-group col-sm-4">
										<label for="phonenumber" class="">Phone/Mobile No. <abbr class="text-danger" title="required">*</abbr></label>
										<input type="tel" class="form-control required phone" name="phonenumber" id="phonenumber" value="<?=$FIELDS['phonenumber'];?>">
										<span class="text-danger"><?=$ERRORS['phonenumber'];?></span>
									</div>
									<div class="form-group col-sm-4">
										<label for="emailaddress" class="">Email Address <abbr class="text-danger" title="required">*</abbr></label>
										<input type="email" class="form-control required email" name="emailaddress" id="emailaddress" value="<?=$FIELDS['emailaddress'];?>">
										<span class="text-danger"><?=$ERRORS['emailaddress'];?></span>
									</div>
									<div class="form-group col-sm-4">
										<label for="verifyemailaddress" class="">Verify Email <abbr class="text-danger" title="required">*</abbr></label>
										<input type="email" class="form-control required email" name="verifyemailaddress" id="verifyemailaddress" value="<?=$FIELDS['verifyemailaddress'];?>">
										<span class="text-danger"><?=$ERRORS['verifyemailaddress'];?></span>
									</div>
								</div>
								<div class="row">
									<div class="form-group col-sm-4">
										<label for="dob" class="">DoB <abbr class="text-danger" title="Date of birth is">*</abbr></label>
										<input type="text" class="form-control required date" name="dob" id="dob" value="<?=fixdatepicker($FIELDS['dob']);?>">
										<span class="text-danger"><?=$ERRORS['dob'];?></span>
									</div>
									<div class="form-group col-sm-4">
										<label for="citizenship" class="">Citizenship <abbr class="text-danger" title="required">*</abbr></label>
										<select name="citizenship" class="form-control required">
											<option value="None">--Select--</option>
											<?php
												foreach(list_countries() as $k => $v){													
													if($k == $FIELDS['citizenship']){
														$select = 'selected="selected"';
													}
													else{
														$select = "";
													}
													echo "<option $select value=\"$k\">$v</option>";
												}
												?>
										</select>
										<span class="text-danger"><?=$ERRORS['citizenship'];?></span>
									</div>
									<div class="form-group col-sm-4">
										<label for="physicaladdress" class="">Physical Address <abbr class="text-danger" title="required">*</abbr></label>
										<input type="text" class="form-control required" name="physicaladdress" id="physicaladdress" value="<?=$FIELDS['physicaladdress'];?>">
										<span class="text-danger"><?=$ERRORS['physicaladdress'];?></span>
									</div>
								</div>
								<div class="row">
									<div class="form-group col-sm-4">
										<label for="city" class="">City/Town <abbr class="text-danger" title="required">*</abbr></label>
										<input type="text" class="form-control required" name="city" id="city" value="<?=$FIELDS['city'];?>">
										<span class="text-danger"><?=$ERRORS['city'];?></span>
									</div>
									<div class="form-group col-sm-4">
										<label for="state" class="">State/County <abbr class="text-danger" title="required">*</abbr></label>
										<input type="text" class="form-control required" name="state" id="state" value="<?=$FIELDS['state'];?>">
										<span class="text-danger"><?=$ERRORS['state'];?></span>
									</div>
									<div class="form-group col-sm-4">
										<label for="postalcode" class="">Postal Code <abbr class="text-danger" title="required">*</abbr></label>
										<input type="text" class="form-control required" name="postalcode" id="postalcode" value="<?=$FIELDS['postalcode'];?>">
										<span class="text-danger"><?=$ERRORS['postalcode'];?></span>
									</div>
								</div>
								<div class="row">
									<div class="form-group col-sm-6">
										<label for="identityno" class="">ID/Passport No. <abbr class="text-danger" title="required">*</abbr></label>
										<input type="text" class="form-control required" name="identityno" id="identityno" value="<?=$FIELDS['identityno'];?>">
										<span class="text-danger"><?=$ERRORS['identityno'];?></span>
									</div>
									<div class="form-group col-sm-6">
										<label for="gender" class="">Gender <abbr class="text-danger" title="required">*</abbr></label>
										<select name="gender" class="form-control required">
											<option value="None">--Select--</option>
											<?php
												foreach(list_gender_status() as $k => $v){												
													if($k == $FIELDS['gender']){
														$select = 'selected="selected"';
													}
													else{
														$select = "";
													}
													echo "<option $select value=\"$k\">$v</option>";
												}
												?>
										</select>
										<span class="text-danger"><?=$ERRORS['gender'];?></span>
									</div>
								</div>
								<div class="row">
										<div class="form-group col-sm-6">
											<label for="identityimage" class="">Upload a scanned copy of the original ID/passport <abbr class="text-danger" title="required">*</abbr></label>
											<input type="file" class="form-control required" name="identityimage" id="identityimage" accept="image/*,application/pdf">
											<span class="text-danger"><?=$ERRORS['identityimage'];?></span>
										</div>
										<div class="form-group col-sm-6">
											<label for="passportphoto" class="">Upload a scanned copy of your passport size photograph (1" x 1") <abbr class="text-danger" title="required">*</abbr></label>
											<input type="file" class="form-control required" name="passportphoto" id="passportphoto" accept="image/*,application/pdf">
											<span class="text-danger"><?=$ERRORS['passportphoto'];?></span>
										</div>
									</div>
								<div class="row">
									<div class="form-group col-sm-6">
										<label for="cv" class="">Upload your CV(pdf) <abbr class="text-danger" title="required">*</abbr></label>
										<input type="file" class="form-control required" name="cv" id="cv" accept="application/pdf">
										<span class="text-danger"><?=$ERRORS['cv'];?></span>
									</div>
									<div class="form-group col-sm-6">
										<label for="coverletter" class="">Upload your Cover letter(pdf) <abbr class="text-danger" title="required">*</abbr></label>
										<input type="file" class="form-control required" name="coverletter" id="coverletter" accept="application/pdf">
										<span class="text-danger"><?=$ERRORS['coverletter'];?></span>
									</div>
								</div>
								<div class="form-group">
									<button type="button" id="go-login" class="btn btn-default btn-lg" onclick="javascript:location.href='https://finstockevarsity.com/'">Exit Application</button>
									<button type="button" id="go-step-2" class="btn btn-info btn-lg">Academic & Work Experience</button>
								</div>
							</div>
							<!-- Step 2 -->
							<div id="step-2" class="form-sec" style="display:none;">
								<h2 class="text-primary">Academic & Work Experience</h2> <h4>1. Academic Qualifications</h4>
								<div class="row">
									<div class="col-sm-12">
										<table class="table table-responsive" id="ac_qualifications">
											<thead>
												<tr>
													<th style="width:20%"><label for="institution" class="">Institution Attended <abbr class="text-danger" title="required">*</abbr></label></th>
													<th style="width:20%"><label for="certificate" class="">Certificate Attained <abbr class="text-danger" title="required">*</abbr></label></th>
													<th style="width:20%"><label for="fromtoyear" class="">From - To Year <abbr class="text-danger" title="required">*</abbr></label></th>
													<th style="width:20%"><label for="grademark" class="">Grade/Mark Attained <abbr class="text-danger" title="required">*</abbr></label></th>
													<th style="width:20%"><label for="grademark" class="">Upload Certificate <abbr class="text-danger" title="required">*</abbr></label></th>
												</tr>
											</thead>
											<tbody class="qualifications">
												<?php
													$count = !empty($FIELDS['institution'])?count($FIELDS['institution']):1;
													for($i = 0; $i < $count; $i++){
													?>
													<tr class="qualification">
														<td class="form-group"><input type="text" class="form-control required" name="institution[0]" id="institution_0" value="<?=$FIELDS["institution"][$i]?>" placeholder="Institution name"></td>
														<td class="form-group"><input type="text" class="form-control required" name="certificate[0]" id="certificate_0" value="<?=$FIELDS["certificate"][$i]?>" placeholder="Certificate awarded"></td>
														<td class="form-group"><div class="row">
																<div class="col-sm-6">
																	<input type="text" class="form-control required" name="fromyear[0]" id="fromyear_0" value="<?=$FIELDS["fromyear"][$i]?>" placeholder="From year">
																</div>
																<div class="col-sm-6">
																	<input type="text" class="form-control required" name="toyear[0]" id="toyear_0" value="<?=$FIELDS["toyear"][$i]?>" placeholder="To year">
																</div>
															</div></td>
														<td class="form-group"><input type="text" class="form-control required" name="grade[0]" id="grade_0" value="<?=$FIELDS["grade"][$i]?>" placeholder="Grade/mark"></td>
														<td class="form-group"><input type="file" class="form-control required" name="certfile[0]" id="certfile_0" value="<?=$FIELDS["certfile"][$i]?>" accept="image/*,application/pdf"></td>
													</tr>
													<?php
													}
													?>
											</tbody>
											<tfoot>
												<tr>
													<td colspan="5"><a href="#" class="add_row btn btn-success btn-sm">Add Qualification</a> <a href="#" class="remove_rows btn btn-danger btn-sm">Remove selected row(s)</a></td>
												</tr>
											</tfoot>
										</table>
									</div>
								</div>
								<h4>2. Work Experience</h4>
								<div class="row">
									<div class="col-sm-12">
										<table class="table table-responsive" id="job_experience">
											<thead>
												<tr>
													<th style="width:20%"><label for="institution" class="">Employer/Company Name <abbr class="text-danger" title="required">*</abbr></label></th>
													<th style="width:20%"><label for="certificate" class="">Job position/Title <abbr class="text-danger" title="required">*</abbr></label></th>
													<th style="width:20%"><label for="fromtoyear" class="">Year From - Year To<abbr class="text-danger" title="required">*</abbr></label></th>
													<th style="width:20%"><label for="grademark" class="">Describe your roles <abbr class="text-danger" title="required">*</abbr></label></th>
													<!-- <th style="width:20%"><label for="grademark" class="">Upload Certificate <abbr class="text-danger" title="required">*</abbr></label></th> --> 
												</tr>
											</thead>
											<tbody class="qualifications">
												<?php
												$count = !empty($FIELDS['employer'])?count($FIELDS['employer']):1;
												for($i = 0; $i < $count; $i++){
												?>
												<tr class="experience">
													<td class="form-group"><input type="text" class="form-control required" name="employer[0]" id="employer_0" value="<?=$FIELDS["employer"][$i]?>" placeholder="Employer name"></td>
													<td class="form-group"><input type="text" class="form-control required" name="job_title[0]" id="job_title_0" value="<?=$FIELDS["job_title"][$i]?>" placeholder="Job title"></td>
													<td class="form-group"><div class="row">
															<div class="col-sm-6">
																<input type="text" class="form-control required" name="fromyr[0]" id="fromyr_0" value="<?=$FIELDS["fromyr"][$i]?>" placeholder="From year">
															</div>
															<div class="col-sm-6">
																<input type="text" class="form-control required" name="toyr[0]" id="toyr_0" value="<?=$FIELDS["toyr"][$i]?>" placeholder="To year">
															</div>
														</div></td>
													<td class="form-group"><textarea class="form-control required" name="roles[0]" id="roles_0" value="<?=$FIELDS["roles"][$i]?>" placeholder="Job Roles"></textarea></td>
												</tr>
												<?php
												}
												?>
											</tbody>
											<tfoot>
												<tr>
													<td colspan="5"><a href="#" class="add_row1 btn btn-success btn-sm">Add Work Experience</a> <a href="#" class="remove_rows1 btn btn-danger btn-sm">Remove selected row(s)</a></td>
												</tr>
											</tfoot>
										</table>
									</div>
								</div>
								<div class="row">
									<div class="form-group col-sm-12">
										<label for="course" class="">Select unit(s) applying to teach under each course below: <abbr class="text-danger" title="required">*</abbr></label> <br>
										<!-- multiselect new  --> 
										<script type="text/javascript">
										$(document).ready(function() {
											$('#units-select').multiselect({
												enableCollapsibleOptGroups: true,
												enableFiltering: true,
												disableIfEmpty: true,
												buttonWidth: '100%'
											});
										});
										</script>
										<select id="units-select" name="course[]" multiple="multiple" class="form-control">
											<?php 
											// loop in courses
											foreach(getCourses() as $c): 
												//get units for each course
												$units = (getCourseUnits($c['CourseID']));
												//make sure to display only courses with units
												if(!empty($units)){
													?>
												<optgroup label="<?= $c['CName'] ?>">
												<?php 
													foreach($units as $u):
														echo '<option value="'.$u['UnitID'].'">'.$u['UName'].'</option>';
													endforeach;
													?>
												</optgroup>
												<?php 
												}
											endforeach; ?>
										</select>
										<!-- end multselect --> 
										<span class="text-danger"><?=$ERRORS['course'];?></span>
									</div>
									<!-- </div> -->
								</div>
								<div class="row">
									<div class="form-group col-sm-4">
										<label for="cot" class="">Do you have a certification in online Training? <abbr class="text-danger" title="required">*</abbr></label>
										<select name="cot" id="cot" class="form-control">
											<?php
											foreach(list_yesno_status() as $k => $v){												
												if($k == $FIELDS['cot']){
													$select = 'selected="selected"';
												}
												else{
													$select = "";
												}
												echo "<option $select value=\"$k\">$v</option>";
											}
											?>
										</select>
										<span class="text-danger"><?=$ERRORS['cot'];?></span>
									</div>
									<div class="form-group col-sm-8" id="requirecot">
										<label for="sponsor" class=""><h6>If no, you will be registered to study this course with us at the end of this application</h6> </label>
										<div class="row">
											<div class="col-sm-6">
												<input type="radio" name="pursue" value="1" checked>
												<label for="cot" class="">Yes, I would like to pursue COT</label>
											</div>
											<div class="col-sm-6">
												<input type="radio" name="pursue" value="0" >
												<label for="cot" class="">No, I don't want to pursue COT</label>
											</div>
										</div>
									</div>
									<div class="form-group col-sm-8" id="CotCert" style="display:none;">
										<label for="sponsor" class=""><h6>If Yes, Upload Your Certificate below</h6> </label>
										<div class="row">
											<div class="col-sm-12">
												<input type="file" name="CotCert">
											</div>
										</div>
									</div>
								</div>
								<div class="form-group">
									<button type="button" id="back-step-1" class="btn btn-info btn-lg">Personal Information</button>
									<button type="submit" id="go-step-3" class="btn btn-info btn-lg">Additional Information & Confirm</button>
								</div>
							</div>
							<!-- Step 3 -->
							<div id="step-3" class="form-sec" style="display:none">
								<h2 class="text-primary">Additional Information & Confirm</h2> <h4>Additional Information</h4>
								<div class="form-group">
									<label for="source" class="">How did you learn about us (please tick all that apply): </label>
									<div class="checkbox">
										<label><input type="checkbox" name="source[]" value="Evarsity website">Evarsity website</label> <label><input type="checkbox" name="source[]" value="Evarsity prospectus">Evarsity prospectus</label> <label><input type="checkbox" name="source[]" value="Colleague">Colleague</label> <label><input type="checkbox" name="source[]" value="Friend/family">Friend/family</label> <label><input type="checkbox" name="source[]" value="Poster/banner">Poster/banner</label> <label><input type="checkbox" name="source[]" value="Facebook">Facebook</label> <label><input type="checkbox" name="source[]" value="Twitter">Twitter</label> <label><input type="checkbox" name="source[]" value="Google">Google</label>
									</div>
								</div>
								<div class="row">
									<div class="form-group col-sm-6">
										<label for="othersource" class="">Others (please specify) </label>
										<input type="text" class="form-control" name="othersource" id="othersource" value="<?=$FIELDS["othersource"]?>">
									</div>
								</div>
								<h4>Confirm <span class="text-danger">*</span></h4>
								<div class="form-group checkbox">
									<label for="declaration" class="">
										<input type="checkbox" name="declaration" id="declaration" value="1" class="required">
										I have confirmed that the information I have given herein is correct.</label> <span class="text-danger"><?=$ERRORS['declaration'];?></span>
								</div>
								<div class="form-group">
									<label for="securitycode">Security Code: <span class="text-danger">*</span></label>
									<?=recaptcha_get_html();?>
									<span class="text-danger"><?=$ERRORS['reCaptcha']?></span>
								</div>
								<div class="form-group">
									<button type="button" id="back-step-2" class="btn btn-info btn-lg">Academic & Work Experience</button>
									<button type="submit" class="btn btn-primary btn-lg" name="load" id="load" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Processing Registration">Complete Application</button>
								</div>
							</div>
						</form>
					</div>
					<?php
						}
					break;
					case "pay":
						//INITIATE A PAYMENT IN DB
						$student_id = $_SESSION['STUD_ID'];
						$student_pay_ref = $_SESSION['STUD_ID_HASH'];
						$transaction_tracking_id = '';
						$payment_amount = $_SESSION['AMOUNT'];
						$pay_method = '';
						$stud_tel = $_SESSION['STUD_TEL'];
						$stud_full_name = $_SESSION['STUD_FNAME'].' '.$_SESSION['STUD_LNAME'];
						$stud_email = $_SESSION['STUD_EMAIL'];
						$pay_type = 'registration fee';
						$pay_status = 'Not Started';
						
						//$pay_status
						//RUN A DELETE FOR ALL NONE-STARTED PAYMENTS FOR THIS USER AND INSERT NEW.
						$delDuplicate = sprintf("DELETE FROM `".DB_PREFIX."payment_refs` WHERE `student_pay_ref` = '%s' AND `pay_status` = '%s'", $student_pay_ref, $pay_status);
						db_query($delDuplicate,DB_NAME,$conn);
						
						//INSERT A NEW ONE ON RELOAD ETC. 
						$newPaymentSql = sprintf("INSERT INTO `".DB_PREFIX."payment_refs` (`student_id`, `student_pay_ref`, `transaction_tracking_id`, `payment_amount`, `pay_method`, `stud_tel`, `stud_full_name`, `stud_email`, `pay_type`, `pay_status`) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')", $student_id, $student_pay_ref, $transaction_tracking_id, $payment_amount, $pay_method, $stud_tel, $stud_full_name, $stud_email, 'registration fee', 'Not Started');
						//execute qry
						db_query($newPaymentSql,DB_NAME,$conn);				  
						
						//INITIATE PAYMENT CREDENTIALS
						$token = $params = NULL;
						$consumer_key = PESAPAL_CONSUMER_KEY;
						$consumer_secret = PESAPAL_CONSUMER_SECRET;
						$signature_method = new OAuthSignatureMethod_HMAC_SHA1();
						$iframelink = PESAPAL_IFRAME_API;
						//get form details
						$amount = $_SESSION['AMOUNT'];
						$amount = number_format($amount, 2);//format amount to 2 decimal places
						
						$desc = SYSTEM_NAME." Fee Payment";
						$type = "MERCHANT"; //default value = MERCHANT
						$reference = $_SESSION['STUD_ID_HASH'];//unique order id of the transaction, generated by merchant
						$first_name = $_SESSION['STUD_FNAME'];
						$last_name = $_SESSION['STUD_LNAME'];
						$email = $_SESSION['STUD_EMAIL'];
						$phonenumber = $_SESSION['STUD_TEL'];//ONE of email or phonenumber is required
						
						$callback_url = SYSTEM_URL.'/portal/?do=return_api'; //redirect url, the page that will handle the response from pesapal.
						
						$post_xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?><PesapalDirectOrderInfo xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" Amount=\"".$amount."\" Description=\"".$desc."\" Type=\"".$type."\" Reference=\"".$reference."\" FirstName=\"".$first_name."\" LastName=\"".$last_name."\" Email=\"".$email."\" PhoneNumber=\"".$phonenumber."\" xmlns=\"http://www.pesapal.com\" />";
						$post_xml = htmlentities($post_xml);
						$consumer = new OAuthConsumer($consumer_key, $consumer_secret);
						
						//post transaction to pesapal
						$iframe_src = OAuthRequest::from_consumer_and_token($consumer, $token, "GET", $iframelink, $params);
						$iframe_src->set_parameter("oauth_callback", $callback_url);
						$iframe_src->set_parameter("pesapal_request_data", $post_xml);
						$iframe_src->sign_request($signature_method, $consumer, $token);						
						?>
					<!-- Step 4 -->
					<div id="step-4" class="form-sec">
						<iframe src="<?php echo $iframe_src;?>" width="100%" height="700px"  scrolling="no" frameBorder="0">
						<p>Browser unable to load iFrame</p>
						</iframe>
					</div>
					<?php
					break;
					default:
						echo ErrorMessage("Invalid request! The system failed to process your request. If the problem persists, please contact us.");
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
<script language="javascript" type="text/javascript">
<!--
$(document).ready(function() {
	
	// //Validate form data	
	var v = $("#applicationForm").validate({
		rules: {
			surname: "required",
			firstname: "required",
			middlename: "required",
			physicaladdress: "required",
			phonenumber: "required",
			emailaddress: {
				required: true,
				email: true
			},
			verifyemailaddress: {
				equalTo: "#emailaddress"
			}
		},
		errorElement: "em",
		errorPlacement: function ( error, element ) {
			// Add the `help-block` class to the error element
			error.addClass( "help-block" );
			$("#load").button('loading');

			if ( element.prop( "type" ) === "checkbox" ) {
				error.insertAfter( element.parent( "label" ) );
			} else {
				error.insertAfter( element );
			}
		},
		highlight: function ( element, errorClass, validClass ) {
			$( element ).parents( ".form-group" ).addClass( "has-error" ).removeClass( "has-success" );
		},
		unhighlight: function (element, errorClass, validClass) {
			$( element ).parents( ".form-group" ).addClass( "has-success" ).removeClass( "has-error" );
			$("#load").button('reset');
		}				
		
	});
	
	//Datepicker
	$( "#dob" ).datepicker({		
	  autoclose: true,
	  format: 'mm/dd/yyyy',
	  changeMonth: true,
	  changeYear: true
	});
	
	$("#cot").on('change', function(e) {
		
		var selectedSponsorOption = $(this).val();
		
		if( selectedSponsorOption == 1) {
			$("#requirecot").hide();
			$("#CotCert").show();
		}else {
			$("#requirecot").show();
			$("#CotCert").show();
		}
	});
	
	//add qualification row
	$('#ac_qualifications').on( 'click', 'a.add_row', function(e){
		e.preventDefault();
		var size = $('#ac_qualifications').find('tr.qualification').length;

		$('<tr class="qualification clickable-row">\
			  <td class="form-group"><input type="text" class="form-control required" name="institution[' + size + ']" id="institution_' + size + '" value="" placeholder="Institution name"></td>\
			  <td class="form-group"><input type="text" class="form-control required" name="certificate[' + size + ']" id="certificate_' + size + '" value="" placeholder="Certificate awarded"></td>\
			  <td class="form-group">\
				<div class="row">\
				  <div class="col-sm-6">\
					<input type="text" class="form-control required" name="fromyear[' + size + ']" id="fromyear_' + size + '" value="" placeholder="From year">\
				  </div>\
				  <div class="col-sm-6">\
					<input type="text" class="form-control required" name="toyear[' + size + ']" id="toyear_' + size + '" value="" placeholder="To year">\
				  </div>\
				</div>\
			  </td>\
			  <td class="form-group"><input type="text" class="form-control required" name="grade[' + size + ']" id="grade_' + size + '" value="" placeholder="Grade/mark"></td>\
				<td class="form-group"><input type="file" class="form-control required" name="certfile[' + size + ']" id="certfile_' + size + '" value="" accept="image/*,application/pdf"></td>\
			</tr>').appendTo("#ac_qualifications tbody");
		
		return false;
	});
	//end add qualification
	//remove qualification row
	$('#ac_qualifications').on( 'click', 'a.remove_rows', function(e){
		e.preventDefault();		
		
		if (confirm('Are you sure you want to remove the highlighted rows?')) {
			$("tr.bg-warning").remove();
		}
		
		return false;		
	});
	$('#ac_qualifications').on('click', '.clickable-row', function(e) {
		$(this).toggleClass("bg-warning");
	});
// end remove qualification

//add job experience
$('#job_experience').on( 'click', 'a.add_row1', function(e){
		e.preventDefault();
		var size = $('#job_experience').find('tr.experience').length;

		$('<tr class="experience clickable-row">\
			  <td class="form-group"><input type="text" class="form-control required" name="employer[' + size + ']" id="employer_' + size + '" value="" placeholder="Employer name"></td>\
			  <td class="form-group"><input type="text" class="form-control required" name="job_title[' + size + ']" id="job_title_' + size + '" value="" placeholder="Job title"></td>\
			  <td class="form-group">\
				<div class="row">\
				  <div class="col-sm-6">\
					<input type="text" class="form-control required" name="fromyr[' + size + ']" id="fromyr_' + size + '" value="" placeholder="From year">\
				  </div>\
				  <div class="col-sm-6">\
					<input type="text" class="form-control required" name="toyr[' + size + ']" id="toyr_' + size + '" value="" placeholder="To year">\
				  </div>\
				</div>\
			  </td>\
			  <td class="form-group"><textarea class="form-control required" name="roles[' + size + ']" id="roles_' + size + '" value="" placeholder="Job Roles"></textarea></td>\
			</tr>').appendTo("#job_experience tbody");
		
		return false;
	});
	//end add job experience
	// remove job exp rows
	$('#job_experience').on( 'click', 'a.remove_rows1', function(e){
		e.preventDefault();		
		
		if (confirm('Are you sure you want to remove the highlighted rows?')) {
			$("tr.bg-warning").remove();
		}
		
		return false;		
	});
	
	$('#job_experience').on('click', '.clickable-row', function(e) {
		$(this).toggleClass("bg-warning");
	});

	// end remove job experience
	
	$.validator.addMethod("pageRequired", function(value, element) {
		var $element = $(element)

			function match(index) {
				return current == index && $(element).parents("#step-" + (index + 1)).length;
			}
		if (match(0) || match(1) || match(2)) {
			return !this.optional(element);
		}
		return "dependency-mismatch";
	}, $.validator.messages.required)
		
	// Binding next button on first step
  $("#go-step-2").click(function(e) {
		if (v.form()) {
			e.preventDefault();
			$("#step-1").hide("fast");
			$("#step-2").show("slow");
			$("#reg-step-1").removeClass("active").addClass("complete");
			$("#reg-step-2").removeClass("disabled").addClass("active");		
		}
  });
	// Binding next button on second step
	$("#go-step-3").click(function(e) {
		if (v.form()) {
			e.preventDefault();
			$("#step-2").hide("fast");
			$("#step-3").show("slow");
			$("#reg-step-2").removeClass("active").addClass("complete");
			$("#reg-step-3").removeClass("disabled").addClass("active");
		}
  });
	
	// Binding back button on second step
  $("#back-step-1").click(function(e) {
		e.preventDefault();
		$("#step-2").hide("fast");
		$("#step-1").show("slow");
		$("#reg-step-1").removeClass("complete").addClass("active");
		$("#reg-step-2").removeClass("active").addClass("disabled");
		$("#reg-step-3").removeClass("active").addClass("disabled");
  });
 
     // Binding back button on third step
  $("#back-step-2").click(function(e) {
		 e.preventDefault();
		 $("#step-3").hide("fast");
		 $("#step-2").show("slow");
		 $("#reg-step-2").removeClass("complete").addClass("active");
		 $("#reg-step-3").removeClass("active").addClass("disabled");
	});
	 
	<?php
	if($task == "pay"){
	?>
	// Binding back button on fourth step
	$("#reg-step-1").removeClass("active").addClass("complete");
	$("#reg-step-2, #reg-step-3").removeClass("disabled").addClass("complete");
	$("#reg-step-4").removeClass("disabled").addClass("active");
	<?php } ?>
});

function getParameterByName(name, url) {
	if (!url) {
		url = window.location.href;
	}
	name = name.replace(/[\[\]]/g, "\\$&");
	var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
			results = regex.exec(url);
	if (!results) return null;
	if (!results[2]) return '';
	return decodeURIComponent(results[2].replace(/\+/g, " "));
}
//-->
</script>