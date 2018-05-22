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

$(document).ready(function() {
	var telInput = $("#phonenumber");
	var validateMsg = $("#validate-msg");
	
	// initialise plugin
	telInput.intlTelInput({
		autoPlaceholder: false,
		formatOnDisplay: true,
		geoIpLookup: function(callback) {
			jQuery.get('https://ipinfo.io', function() {}, "jsonp").always(function(resp) {
			var countryCode = (resp && resp.country) ? resp.country : "";
			callback(countryCode);
			});
		},
		initialCountry: "auto",
		nationalMode: false,
		preferredCountries: ['ke', 'ug', 'tz'],
		utilsScript: "<?=THEME_URL?>/vendor/int-tel-input/lib/libphonenumber/build/utils.js"
	});
	
	var reset = function() {
		telInput.removeClass("error");
		validateMsg.addClass("hide");
	};
	
	// on blur: validate
	telInput.blur(function() {
		reset();
		if ($.trim(telInput.val())) {
			if (telInput.intlTelInput("isValidNumber")) {
				validateMsg.addClass("hide");		
			} else {
				validateMsg.removeClass("hide");
				validateMsg.html( '<em id="phonenumber-error" class="error">Valid number is required.</em>' );
			}
		}
	});
	
	// on keyup / change flag: reset
	telInput.on("keyup change", reset);
});
//-->
</script>

<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="header-img"><a href="<?=PARENT_HOME_URL;?>"><img class="img-responsive" src="<?=SYSTEM_LOGO_URL;?>" alt="Logo"></a></div>
			<div class="activate-panel panel panel-default">
				<div class="panel-heading">
				<div class = "row">
					<div class= "col-md-3">
						<h3 class="panel-title">Application Form</h3>
					</div>
					<div class = "col-md-9">
					<?php echo getInnerMenu(PARENT_HOME_URL); ?>
					</div>
				</div>
				</div>
				<div class="panel-body">
					<h2>Register for a Free Course Now</h2>
					<p>*All fields marked with asteriks (*) are required to complete the application</p>
					<?php
					require "$incl_dir/recaptchalib.php";
					require_once("$incl_dir/mysqli.functions.php");
					require_once("$class_dir/class.OAuth.php");
					//Open database connection
					$conn = db_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
					//bGet requested task/default is add
					$task = isset($_GET['task'])?$_GET['task']:"add";
					
					$task = strtolower($task);
					switch($task) {
						case "add":
						// Array to store the error messages
						$FIELD = array();
						$ERRORS = array();
						$CONFIRM = array();
						// If course is avaialble in query string
						$FIELDS['course'] = isset($_GET['course'])?$_GET['course']:"";
						// Set default citizenship to Kenya 
						$FIELDS['citizenship'] = "KE";
						
						$saved = false;
						//Execute Commands
						if(isset($_POST["load"])){						 
							//Applicant Contact Info
							$FIELDS['surname'] = secure_string(ucwords($_POST['surname']));
							$FIELDS['firstname'] = secure_string(ucwords($_POST['firstname']));
							$FIELDS['middlename'] = secure_string(ucwords($_POST['middlename']));
							$FIELDS['physicaladdress'] = secure_string($_POST['physicaladdress']);				  
							$FIELDS['postalcode'] = secure_string($_POST['postalcode']);
							$FIELDS['city'] = secure_string($_POST['city']);
							$FIELDS['state'] = secure_string($_POST['state']);
							$FIELDS['phonenumber'] = secure_string($_POST['phonenumber']);
							$FIELDS['emailaddress'] = secure_string($_POST['emailaddress']);
							$FIELDS['verifyemailaddress'] = secure_string($_POST['verifyemailaddress']);//applicant university data
							$FIELDS['university'] = secure_string($_POST['university']);	
							$FIELDS['studno'] = secure_string($_POST['studno']);
							$FIELDS['enrolled'] = secure_string($_POST['enrolled']);
							$FIELDS['level'] = secure_string($_POST['level']);	

							$FIELDS['dob'] = secure_string($_POST['dob']);
							$FIELDS['citizenship'] = secure_string($_POST['citizenship']);							
							$FIELDS['gender'] = secure_string($_POST['gender']);
							$FIELDS['identityno'] = secure_string($_POST['identityno']);							
							$FIELDS['course'] = secure_string($_POST['course']);
							$FIELDS['StudyMode'] = secure_string($_POST['StudyMode']);
							$FIELDS['Training'] = secure_string($_POST['Training']);
							$FIELDS['trim'] = '1/1';//defaults to 1st yr/1st trimeste

							$FIELDS['source'] = secure_string($_POST['source']);
							$FIELDS['othersource'] = secure_string($_POST['othersource']);
							$FIELDS['declaration'] = secure_string($_POST['declaration']);

							$FullName = $FIELDS['surname']." ".$FIELDS['firstname']." ".$FIELDS['middlename'];

							$IndentityPhotoFile = $_FILES["identityimage"]["name"];
							$IndentityPhotoFileTemp = $_FILES["identityimage"]["tmp_name"];

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
							$ERRORS['middlename'] = "A valid middlename is required";				//valid univer
							if(!$check->is_String($FIELDS['university']))
							$ERRORS['university'] = "A valid University name is required";	  			  
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
									$FIELDS['dbDob'] = db_fixdatetime($FIELDS['dob']);// YYYY-dd-mm
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
							//valid student no
							if(empty($FIELDS['studno']))
							$ERRORS['studno'] = "Your university Student No. is required";
								//valid enrolled course
							if(empty($FIELDS['enrolled']))
								$ERRORS['enrolled'] = "This field is required";
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
							// validate "identityimage" upload file
							if(empty($IndentityPhotoFileTemp) || !in_array($_FILES["identityimage"]["type"], $allowed_mimes) || $_FILES["identityimage"]["size"] > 2000000)
							$ERRORS['identityimage'] = "Uploaded file must be a supported image or document not greater than 2MB";
							// validate "passportphoto" upload file
							if(empty($PhotoFileTemp) || !in_array($_FILES["passportphoto"]["type"], $allowed_mimes) || $_FILES["passportphoto"]["size"] > 2000000)
							$ERRORS['passportphoto'] = "Uploaded photo must be a supported image or document not greater than 2MB";
							// validate "course" field
							if($FIELDS['course'] == "None")
							$ERRORS['course'] = "Please select the course you want to persue";	
							//validate study mode
							if($FIELDS['StudyMode'] == "None")
							$ERRORS['StudyMode'] = "Please select your prefered Study Mode";

							if($FIELDS['Training'] == "None")
							$ERRORS['Training'] = "Please select your prefered Study Mode";
							
							if($FIELDS['declaration'] != 1)
							$ERRORS['declaration'] = "You need to accept this declaration";							
							// Validate Google reCAPTCHA
							if( !recapture_verify( $_POST["g-recaptcha-response"], $_SERVER["REMOTE_ADDR"] ) )
							$ERRORS['reCaptcha'] = "You're a robot. If not, please try again.";
								
							//Check if this candidate is already registered	
							$checkDuplicateSql = sprintf("SELECT `Email` FROM `".DB_PREFIX."students` WHERE `Email` = '%s'", $FIELDS['emailaddress']);
							//Set the result and run the query
							$result = db_query($checkDuplicateSql,DB_NAME,$conn);
							//check if any results were returned
							if(db_num_rows($result)>0){
								$ERRORS['emailaddress'] = "An account with this email address has already been registered.";
							}
							db_free_result($result);
							
							// check for errors
							if(sizeof($ERRORS) > 0){
								$ERRORS['MSG'] = ErrorMessage("PLEASE CORRECT HIGHLIGHTED FIELDS!");
							}
							else{
								//Upload file if any								
								$IndentityPhotoExt = findexts($IndentityPhotoFile);
								if(!empty($IndentityPhotoFileTemp)){
									$IndentityPhotoFileName = friendlyName($FullName)."-identity-".$UploadDate.".".$IndentityPhotoExt;
									$uploadIdentityPhotoPath = UPLOADS_PATH.$IndentityPhotoFileName;
									//Just incase of an internal problem
									if(move_uploaded_file($IndentityPhotoFileTemp, $uploadIdentityPhotoPath)){
										$IndentityPhotoFilePath = UPLOADS_FOLDER."/".$IndentityPhotoFileName;
									}else{
										$IndentityPhotoFilePath = "";
									}
								}else{
									$IndentityPhotoFilePath = "";
								}
								
								$PhotoExt = findexts($PhotoFile);
								if(!empty($PhotoFileTemp)){
									$PhotoFileName = friendlyName($FullName)."-photo-".$UploadDate.".".$PhotoExt;
									$uploadPhotoPath = UPLOADS_PATH.$PhotoFileName;
									//Just incase of an internal problem
									if(move_uploaded_file($PhotoFileTemp, $uploadPhotoPath)){
										$PhotoFilePath = UPLOADS_FOLDER."/".$PhotoFileName;
									}else{
										$PhotoFilePath = "";
									}
								}else{
									$PhotoFilePath = "";
								}

								$FIELDS['identityimage'] = $IndentityPhotoFilePath;
								$FIELDS['passportphoto'] = $PhotoFilePath;

								//GET CURRENT DB MAX ID
								$maxidsql = "SELECT MAX(`UID`) AS 'UID' FROM `".DB_PREFIX."students`";
								
								$maxidres = db_query($maxidsql,DB_NAME,$conn);	
								$maxidrow = db_fetch_array($maxidres);
								if ($maxidrow["UID"] == 0) {
									$MUID = 100;
								} else {
									$MUID = $maxidrow["UID"]+1;
								}
								$FIELDS['StudentID'] = "FE" ."/". $FIELDS['course'] ."/". $MUID ."/". date('Y');
								$hash_stud = md5($FIELDS['StudentID']);
								$FIELDS['sponsorship'] = "None";
								$FIELDS['sponsorname'] = "None";
								$FIELDS['sponsorcontact'] = 07;
								//Add new student
								$newClientSql = sprintf("INSERT INTO `".DB_PREFIX."students` (`StudentID`, `FName`, `MName`, `LName`, `Phone`, `Email`, `DOB`, `Gender`, `Address`, `City`, `State`, `PostCode`, `Country`, `IdentityNumber`, `IdentityImage`, `PassportPhoto`, `Courses`, `StudyMode`, `Training`, `YrTrim`, `Sponsorship`, `SponsorName`, `SponsorContact`) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')", $FIELDS['StudentID'], $FIELDS['firstname'], $FIELDS['middlename'], $FIELDS['surname'], $FIELDS['phonenumber'], $FIELDS['emailaddress'], $FIELDS['dbDob'], $FIELDS['gender'], $FIELDS['physicaladdress'], $FIELDS['city'], $FIELDS['state'], $FIELDS['postalcode'], $FIELDS['citizenship'], $FIELDS['identityno'], $FIELDS['identityimage'], $FIELDS['passportphoto'], $FIELDS['course'], $FIELDS['StudyMode'], $FIELDS['Training'], $FIELDS['trim'], $FIELDS['sponsorship'], $FIELDS['sponsorname'], $FIELDS['sponsorcontact']);
								//add new free registration
								$newClientSql2 = sprintf("INSERT INTO `".DB_PREFIX."freecourse_registration`(`StudentID`, `UniversityID`, `University`, `StudyLevel`, `Program`) VALUES ('%s','%s','%s','%s','%s')", $FIELDS['StudentID'], $FIELDS['studno'], $FIELDS['university'], $FIELDS['level'], $FIELDS['enrolled']);
								//Execute queries
								db_query($newClientSql,DB_NAME,$conn);
								db_query($newClientSql2,DB_NAME,$conn);
								
								//Check if saved
								if(db_affected_rows($conn)){
									//send email, echo message and exit
									$succ = ConfirmMessage('Dear '.$FullName.', Your Application has been received, we will send your account login details via the email you provided. In the meantime, please <a href="'.PARENT_HOME_URL.'">See more about Evarsity</a>');
									$subject = SYSTEM_NAME.' - Application Received';
									//make body
									$content='<html><head>
									<title>'.$subject.'</title>
									</head><body><div style="background-color:#E1CDB7; color:#000; width:600px; margin:0 auto;">
									<div style="background:#C60; color:#FFF; min-width:584px; padding:8px;">
									<h2 style="font-size:15px;font-weight:700;line-height:25px;"><em>'.strtoupper(SYSTEM_NAME).' APPLICATION</em></h2>
									</div>
									<div style="padding:15px;">
									<h3 style="color:#333;">Dear '.$FullName.',</h3>
									<p style="text-align:justify;">We at <a href="'.PARENT_HOME_URL.'">'.SYSTEM_NAME.'</a> would like to thank you for applying to study with us.</p>
									<p style=" text-align:justify;">We would like to let you know that your application was successfully submitted and that our administrators are going through the information you provided for verification purposes and we will get back to you with an  <strong>Acount Login details</strong> soonest.</p><br />
									<p style="color:#753b01;">All the best!<br /><br />
									Admissions Office,<br />
									'.SYSTEM_NAME.',<br />
									'.COMPANY_ADDRESS.'<br />
									TEL: '.COMPANY_PHONE.'<br />
									EMAIL: '.INFO_EMAIL.'<br />
									WEBSITE: '.PARENT_HOME_URL.'</p>
									</div></div>
									</body></html>';
									//get email func
									mail_config($FIELDS['emailaddress'], $FullName, $subject, $content);

									echo $succ;
									exit();
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
							<form id="applicationForm" name="application-form" method="post" action="?do=free&amp;task=<?=$task?>" enctype="multipart/form-data">
								<!-- Step 1 -->
								<div id="step-1" class="form-sec">
									<h2 class="text-primary">Personal Details</h2>
									<div class="row">
										<div class="form-group col-sm-3">
											<label for="surname" class="">Surname <abbr class="text-danger" title="required">*</abbr></label>
											<input type="text" class="form-control required" name="surname" id="surname" value="<?=$FIELDS['surname'];?>">
											<span class="text-danger"><?=$ERRORS['surname'];?></span>
										</div>
										<div class="form-group col-sm-3">
											<label for="firstname" class="">First Name <abbr class="text-danger" title="required">*</abbr></label>
											<input type="text" class="form-control required" name="firstname" id="firstname" value="<?=$FIELDS['firstname'];?>">
											<span class="text-danger"><?=$ERRORS['firstname'];?>
	</span>
										</div>
										<div class="form-group col-sm-3">
											<label for="middlename" class="">Middle Name <abbr class="text-danger" title="required">*</abbr></label>
											<input type="text" class="form-control required" name="middlename" id="middlename" value="<?=$FIELDS['middlename'];?>">
											<span class="text-danger"><?=$ERRORS['middlename'];?></span>
										</div>
										<div class="form-group col-sm-3">
											<label for="phonenumber" class="">Phone/Mobile No. <abbr class="text-danger" title="required">*</abbr></label>
											<input type="tel" class="form-control" autocomplete="off" name="phonenumber" id="phonenumber" value="<?=$FIELDS['phonenumber'];?>">
											<span id="validate-msg" class="text-danger"><?=$ERRORS['phonenumber'];?></span>
										</div>
									</div>
									<div class="row">
									<div class="form-group col-sm-3">
											<label for="emailaddress" class="">Email Address <abbr class="text-danger" title="required">*</abbr></label>
											<input type="email" class="form-control required email" name="emailaddress" id="emailaddress" value="<?=$FIELDS['emailaddress'];?>">
											<span class="text-danger"><?=$ERRORS['emailaddress'];?></span>
										</div>
										<div class="form-group col-sm-3">
											<label for="verifyemailaddress" class="">Verify Email <abbr class="text-danger" title="required">*</abbr></label>
											<input type="email" class="form-control required email" name="verifyemailaddress" id="verifyemailaddress" value="<?=$FIELDS['verifyemailaddress'];?>">
											<span class="text-danger"><?=$ERRORS['verifyemailaddress'];?></span>
										</div>
										<div class="form-group col-sm-3">
											<label for="university" class="">Your University/College. <abbr class="text-danger" title="required">*</abbr></label>
											<input type="text" class="form-control required university" name="university" id="university" value="<?=$FIELDS['university'];?>">
											<span class="text-danger"><?=$ERRORS['university'];?></span>
										</div>
										<div class="form-group col-sm-3">
											<label for="studno" class="">Your University Student No. <abbr class="text-danger" title="required">*</abbr></label>
											<input type="text" class="form-control required studno" name="studno" id="studno" value="<?=$FIELDS['studno'];?>">
											<span class="text-danger"><?=$ERRORS['studno'];?></span>
										</div>
									</div>
									<div class="row">
										<div class="form-group col-sm-4">
											<label for="enrolled" class="">Currently Enrolled Course at your University <abbr class="text-danger" title="">*</abbr></label>
											<input type="text" class="form-control required enrolled" name="enrolled" id="enrolled" value="<?=$FIELDS['enrolled'];?>">
											<span class="text-danger"><?=$ERRORS['enrolled'];?></span>
										</div>
										<div class="form-group col-sm-4">
											<label for="level" class="">Your Current Level of Study <abbr class="text-danger" title="required">*</abbr></label>
											<select name="level" class="form-control required">
												<option value="PhD">PhD</option>
												<option value="Masters" selected="selected">Masters</option>
												<option value="Bachelors">Bachelors</option>
												<option value="Diploma">Diploma</option>
												<option value="Certificate">Certificate</option>
												<option value="ShortCourse">Short Course</option>
											</select>
											<span class="text-danger"><?=$ERRORS['level'];?></span>
										</div>
										<div class="form-group col-sm-4">
											<label for="dob" class="">DoB <abbr class="text-danger" title="Date of birth is">*</abbr></label>
											<input type="text" class="form-control required date" name="dob" id="dob" value="<?=fixdatepicker($FIELDS['dob']);?>">
											<span class="text-danger"><?=$ERRORS['dob'];?></span>
										</div>
									</div>
									<div class="row">
										<div class="form-group col-sm-3">
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
										<div class="form-group col-sm-3">
											<label for="physicaladdress" class="">Physical Address <abbr class="text-danger" title="required">*</abbr></label>
											<input type="text" class="form-control required" name="physicaladdress" id="physicaladdress" value="<?=$FIELDS['physicaladdress'];?>">
											<span class="text-danger"><?=$ERRORS['physicaladdress'];?></span>
										</div>	
										<div class="form-group col-sm-3">
											<label for="city" class="">City/Town <abbr class="text-danger" title="required">*</abbr></label>
											<input type="text" class="form-control required" name="city" id="city" value="<?=$FIELDS['city'];?>">
											<span class="text-danger"><?=$ERRORS['city'];?></span>
										</div>
										<div class="form-group col-sm-3">
											<label for="state" class="">State/County <abbr class="text-danger" title="required">*</abbr></label>
											<input type="text" class="form-control required" name="state" id="state" value="<?=$FIELDS['state'];?>">
											<span class="text-danger"><?=$ERRORS['state'];?></span>
										</div>										
									</div>
									<div class="row">
										<div class="form-group col-sm-4">
											<label for="postalcode" class="">Postal Code <abbr class="text-danger" title="required">*</abbr></label>
											<input type="text" class="form-control required" name="postalcode" id="postalcode" value="<?=$FIELDS['postalcode'];?>">
											<span class="text-danger"><?=$ERRORS['postalcode'];?></span>
										</div>
										<div class="form-group col-sm-4">
											<label for="identityno" class="">ID/Passport No. <abbr class="text-danger" title="required">*</abbr></label>
											<input type="text" class="form-control required" name="identityno" id="identityno" value="<?=$FIELDS['identityno'];?>">
											<span class="text-danger"><?=$ERRORS['identityno'];?></span>
										</div>
										<div class="form-group col-sm-4">
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
										<div class="form-group col-sm-4">
											<label for="course" class="">Name of course applying for: <abbr class="text-danger" title="required">*</abbr></label> <?php echo sqlOption("SELECT `CourseID`,`CName` FROM `".DB_PREFIX."courses` WHERE `disabledFlag` = 0 AND `deletedFlag` = 0 AND `CourseType` != 'Paid'","course",$FIELDS['course'],"--Select course--");?>
											<span class="text-danger"><?=$ERRORS['course'];?></span>
										</div>
										<div class="form-group col-sm-4">
											<label for="StudyMode" class="">Prefered Study Mode: <abbr class="text-danger" title="required">*</abbr></label> <?php echo sqlOption("SELECT * FROM `".DB_PREFIX."study_modes` WHERE ModeStatus = 2","StudyMode",$FIELDS['StudyMode'],"--Select Study Mode--");?>
											<span class="text-danger"><?=$ERRORS['StudyMode'];?></span>
										</div>
										<div class="form-group col-sm-4">
											<label for="Training" class="">Training Attended: <abbr class="text-danger" title="required">*</abbr></label> <?php echo sqlOption("SELECT TrainingID, Training FROM `".DB_PREFIX."trainings` WHERE 1","Training",$FIELDS['Training'],"--Select Training--");?>
											<span class="text-danger"><?=$ERRORS['Training'];?></span>
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
									<div class="form-group">
										<label for="source" class="">How did you learn about us (please tick all that apply): </label>
										<div class="checkbox">
											<label><input type="checkbox" name="source[]" value="Evarsity website">Evarsity website</label>
											<label><input type="checkbox" name="source[]" value="Evarsity prospectus">Evarsity prospectus</label>
											<label><input type="checkbox" name="source[]" value="Colleague">Colleague</label>
											<label><input type="checkbox" name="source[]" value="Friend/family">Friend/family</label>
											<label><input type="checkbox" name="source[]" value="Poster/banner">Poster/banner</label>
											<label><input type="checkbox" name="source[]" value="Facebook">Facebook</label>
											<label><input type="checkbox" name="source[]" value="Twitter">Twitter</label>
											<label><input type="checkbox" name="source[]" value="Google">Google</label>
										</div>
									</div>
									<div class="row">
										<div class="form-group col-sm-6">
											<label for="othersource" class="">Others (please specify) </label>
											<input type="text" class="form-control" name="othersource" id="othersource" value="<?=$FIELDS["othersource"]?>">
										</div>
									</div>
									<h3>Declaration <span class="text-danger">*</span></h3>
									<div class="form-group checkbox">
										<label for="declaration" class="">
										<input type="checkbox" name="declaration" id="declaration" value="1" class="required">
										I have confirmed that the information I have given herein is correct.</label> <span class="text-danger"><?=$ERRORS['declaration'];?></span>
									</div>
									<h3>Email use Consent <span class="text-danger">*</span></h3>
									<div class="form-group checkbox">
										<label for="declaration" class="">
										<input type="checkbox" checked name="offers" id="offers" value="1" class="">
										Let me receive other offers from Finstock Evarsity</label> <span class="text-danger"><?=$ERRORS['declaration'];?></span>
									</div>
									<div class="form-group">
										<label for="securitycode">Security Code: <span class="text-danger">*</span></label>
										<?=recaptcha_get_html();?>
										<span class="text-danger"><?=$ERRORS['reCaptcha']?></span>
									</div>
									<div class="form-group">
										<button type="button" id="go-login" class="btn btn-default btn-lg" onclick="javascript:location.href='./'">Back to Login</button>
										<button type="submit" class="btn btn-primary btn-lg" name="load" id="load" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Processing Registration">Submit</button>
									</div>
								</div>
							</form>
						</div>
						<?php
						}
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
	
	//Validate form data	
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
	
	$("#sponsorship").on('change', function(e) {
		
		var selectedSponsorOption = $(this).val();
		
		if( selectedSponsorOption == "Self" ) {
			$("#requiresponsor").hide();
		}else {
			$("#requiresponsor").show();
		}
	});
	
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