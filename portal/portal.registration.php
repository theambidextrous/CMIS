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
			<div class="activate-panel panel panel-default">
				<div class="panel-heading">
				<div class = "row">
					<div class= "col-md-3">
					<div class="header-img" style = "margin-top:1px;"><a href="<?=PARENT_HOME_URL;?>"><img class="img-responsive" src="<?=SYSTEM_LOGO_URL;?>" alt="Logo"></a></div>
					</div>
					<div class = "col-md-9">
					<?php echo getInnerMenu(PARENT_HOME_URL); ?>
					</div>
				</div>
				</div>
				<div class="panel-body">
					<h2>Course Registration Form</h2>
					<p>*All fields marked with asteriks (*) are required to complete the application</p>
					<p>*At the end of the application, you will be required to pay registration fee of <b>KES 1,000</b></p>
					<h2>Application Guide</h2>
					<div class= "row">
					<div class= "col-md-12">
					<iframe class="embed-responsive-item" style="width:100%; height: 350px;"src="https://www.youtube.com/embed/tZWMoNcpN1s?rel=0&amp;controls=0&amp;showinfo=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
					<!-- <h4>Finstock Evarsity Course Application Guide</h4> -->
					</div>
					</div>
					<div class="reg-wizard">
						<div id="reg-step-1" class="col-xs-3 reg-wizard-step active">
							<div class="text-center reg-wizard-stepnum">Step 1</div>
							<div class="progress">
								<div class="progress-bar"></div>
							</div>
							<a href="#" class="reg-wizard-dot" id="wizard-step-1"></a>
							<div class="reg-wizard-info text-center">Personal Details</div>
						</div>
						<div id="reg-step-2" class="col-xs-3 reg-wizard-step disabled">
							<div class="text-center reg-wizard-stepnum">Step 2</div>
							<div class="progress">
								<div class="progress-bar"></div>
							</div>
							<a href="#" class="reg-wizard-dot" id="wizard-step-2"></a>
							<div class="reg-wizard-info text-center">Academic and Course Details</div>
						</div>
						<div id="reg-step-3" class="col-xs-3 reg-wizard-step disabled">
							<div class="text-center reg-wizard-stepnum">Step 3</div>
							<div class="progress">
								<div class="progress-bar"></div>
							</div>
							<a href="#" class="reg-wizard-dot" id="wizard-step-3"></a>
							<div class="reg-wizard-info text-center">Confirm and Submit</div>
						</div>
						<div id="reg-step-4" class="col-xs-3 reg-wizard-step disabled">
							<div class="text-center reg-wizard-stepnum">Step 4</div>
							<div class="progress">
								<div class="progress-bar"></div>
							</div>
							<a href="#" class="reg-wizard-dot" id="wizard-step-4"></a>
							<div class="reg-wizard-info text-center">Complete Payment</div>
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
					
					$task = strtolower($task);
					switch($task) {
						case "add":
						// Array to store the error messages
						$FIELDS = array();
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
							$FIELDS['verifyemailaddress'] = secure_string($_POST['verifyemailaddress']);				  
							$FIELDS['dob'] = secure_string($_POST['dob']);
							$FIELDS['citizenship'] = secure_string($_POST['citizenship']);							
							$FIELDS['gender'] = secure_string($_POST['gender']);
							$FIELDS['identityno'] = secure_string($_POST['identityno']);							
							$FIELDS['institution'] = secure_string($_POST['institution']);
							$FIELDS['certificate'] = secure_string($_POST['certificate']);
							$FIELDS['fromyear'] = secure_string($_POST['fromyear']);
							$FIELDS['toyear'] = secure_string($_POST['toyear']);
							$FIELDS['grade'] = secure_string($_POST['grade']);
							$FIELDS['course'] = secure_string($_POST['course']);
							$FIELDS['StudyMode'] = secure_string($_POST['StudyMode']);
							$FIELDS['trim'] = '1/1';//defaults to 1st yr/1st trimester
							$FIELDS['englishproficiency'] = secure_string($_POST['englishproficiency']);
							$FIELDS['englishneeded'] = secure_string($_POST['englishneeded']);
							$FIELDS['sponsorship'] = secure_string($_POST['sponsorship']);
							$FIELDS['sponsorname'] = secure_string($_POST['sponsorname']);
							$FIELDS['sponsorcontact'] = secure_string($_POST['sponsorcontact']);
							$FIELDS['source'] = secure_string($_POST['source']);
							$FIELDS['othersource'] = secure_string($_POST['othersource']);
							$FIELDS['declaration'] = secure_string($_POST['declaration']);
							//$FIELDS['period'] = $FIELDS['fromyear']."-".$FIELDS['toyear'];
							$FullName = $FIELDS['surname']." ".$FIELDS['firstname']." ".$FIELDS['middlename'];
							$IndentityPhotoFile = $_FILES["identityimage"]["name"];
							$IndentityPhotoFileTemp = $_FILES["identityimage"]["tmp_name"];
							$PhotoFile = $_FILES["passportphoto"]["name"];
							$PhotoFileTemp = $_FILES['passportphoto']['tmp_name'];
							$CertFile = $_FILES["certfile"]["name"];
							$CertFileTemp = $_FILES['certfile']['tmp_name'];
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
							if(empty($IndentityPhotoFileTemp) || !in_array($_FILES["identityimage"]["type"], $allowed_mimes) || $_FILES["identityimage"]["size"] > 800000)
							$ERRORS['identityimage'] = "Uploaded file must be a supported image or document not greater than 800KB";
							// validate "passportphoto" upload file
							if(empty($PhotoFileTemp) || !in_array($_FILES["passportphoto"]["type"], $allowed_mimes) || $_FILES["passportphoto"]["size"] > 800000)
							$ERRORS['passportphoto'] = "Uploaded photo must be a supported image or document not greater than 800KB";
							// validate "course" field
							if($FIELDS['course'] == "None")
							$ERRORS['course'] = "Please select the course you want to persue";	
							//validate study mode
							if($FIELDS['StudyMode'] == "None")
							$ERRORS['StudyMode'] = "Please select your prefered Study Mode";						
							// validate "englishproficiency" field
							if($FIELDS['englishproficiency'] == "None")
							$ERRORS['englishproficiency'] = "Please specify your english proficiency";
							// validate "sponsorship" field
							if($FIELDS['sponsorship'] == "None")
							$ERRORS['sponsorship'] = "Please specify who will you finance your studies";
							// validate "declaration" field
							if($FIELDS['declaration'] != 1)
							$ERRORS['declaration'] = "You need to accept this declaration";							
							// Validate Google reCAPTCHA
							if( !recapture_verify( $_POST["g-recaptcha-response"], $_SERVER["REMOTE_ADDR"] ) )
							$ERRORS['reCaptcha'] = "You're a robot. If not, please try again.";
								
							//Check if this candidate is already registered	
							$checkDuplicateSql = sprintf("SELECT `Email` FROM `".DB_PREFIX."students` WHERE `Email` = '%s'", $FIELDS['emailaddress']);
							//Set the result and run the query
							$result = db_query($checkDuplicateSql,DB_NAME,$conn);
							//Check if any results were returned
							if(db_num_rows($result)>0){
								$_SESSION['message'] = ErrorMessage("An account with this email address has already been registered.");
								redirect("?do=register&task=recover&token=$Token");
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
								$FIELDS['certfile'] = $CertFilePath;
								
								//GET REGISTRATION FEE
								$regsql = sprintf("SELECT `pay_amount` FROM `".DB_PREFIX."payment_categs` WHERE `payment_name` = 'Registration'");
								//Set the result and run the query
								$result = db_query($regsql,DB_NAME,$conn);
								$row = db_fetch_array($result);
								$reg_fee = $row["pay_amount"];
								
								//GENERATE NEW STUDENT ID
								//GET CURRENT DB MAX ID
								$maxidsql = "SELECT MAX(`UID`) AS 'UID' FROM `".DB_PREFIX."students`";
								
								$maxidres = db_query($maxidsql,DB_NAME,$conn);	
								$maxidrow = db_fetch_array($maxidres);
								if ($maxidrow["UID"] == 0) {
									$MUID = 100;
								} else {
									$MUID = $maxidrow["UID"]+1;
								}
								//Format: 01STD001/2014
								//Format: FE05STD1/2017
								//$FIELDS['StudentID'] = date('m') ."STD" . $MUID . "/" . date('Y');
								$FIELDS['StudentID'] = "FE" ."/". $FIELDS['course'] ."/". $MUID ."/". date('Y');
								$hash_stud = md5($FIELDS['StudentID']);
								//Add new student
								$newClientSql = sprintf("INSERT INTO `".DB_PREFIX."students` (`StudentID`, `FName`, `MName`, `LName`, `Phone`, `Email`, `DOB`, `Gender`, `Address`, `City`, `State`, `PostCode`, `Country`, `IdentityNumber`, `IdentityImage`, `PassportPhoto`, `Courses`, `StudyMode`, `YrTrim`, `Sponsorship`, `SponsorName`, `SponsorContact`) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')", $FIELDS['StudentID'], $FIELDS['firstname'], $FIELDS['middlename'], $FIELDS['surname'], $FIELDS['phonenumber'], $FIELDS['emailaddress'], $FIELDS['dbDob'], $FIELDS['gender'], $FIELDS['physicaladdress'], $FIELDS['city'], $FIELDS['state'], $FIELDS['postalcode'], $FIELDS['citizenship'], $FIELDS['identityno'], $FIELDS['identityimage'], $FIELDS['passportphoto'], $FIELDS['course'], $FIELDS['StudyMode'], $FIELDS['trim'], $FIELDS['sponsorship'], $FIELDS['sponsorname'], $FIELDS['sponsorcontact']);
								//Execute query
								db_query($newClientSql,DB_NAME,$conn);
								
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
										
										$newClientSql = sprintf("INSERT INTO `".DB_PREFIX."ac_qualifications` (`StudentID`, `Institution`, `Certificate`, `Period`, `GradeMark`, `EngProficiency`, `EngHelp`, `CertFile`) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s')", $FIELDS['StudentID'], $FIELDS['institution'][$i], $FIELDS['certificate'][$i], $FIELDS['period'], $FIELDS['grade'][$i], $FIELDS['englishproficiency'], $FIELDS['englishneeded'], $CertFilePath[$i]);
										//Execute query
										db_query($newClientSql,DB_NAME,$conn);
									}
									
									//Create sessions for reg fee payment GATEWAY					  
									$_SESSION['STUD_ID_HASH'] = $hash_stud;
									$_SESSION['STUD_ID'] = $FIELDS['StudentID'];
									$_SESSION['AMOUNT'] = $reg_fee;
									$_SESSION['STUD_FNAME'] = $FIELDS['firstname'];
									$_SESSION['STUD_LNAME'] = $FIELDS['surname'];
									$_SESSION['STUD_EMAIL'] = $FIELDS['emailaddress'];
									$_SESSION['STUD_TEL'] = $FIELDS['phonenumber'];
									$_SESSION['COURSE_ID'] = $FIELDS['course'];
									
									//Proceed to pay
									redirect("?do=register&task=pay&token=$Token");
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
							<form id="applicationForm" name="application-form" method="post" action="?do=register&amp;task=<?=$task?>" enctype="multipart/form-data">
								<!-- Step 1 -->
								<div id="step-1" class="form-sec">
									<h2 class="text-primary">Personal Details</h2>
									<div class="row">
										<div class="form-group col-sm-4">
											<label for="surname" class="">Surname <abbr class="text-danger" title="required">*</abbr></label>
											<input type="text" class="form-control required" name="surname" id="surname" value="<?=$FIELDS['surname'];?>">
											<span class="text-danger"><?=$ERRORS['surname'];?></span>
										</div>
										<div class="form-group col-sm-4">
											<label for="firstname" class="">First Name <abbr class="text-danger" title="required">*</abbr></label>
											<input type="text" class="form-control required" name="firstname" id="firstname" value="<?=$FIELDS['firstname'];?>">
											<span class="text-danger"><?=$ERRORS['firstname'];?>
	</span>
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
											<input type="tel" class="form-control" autocomplete="off" name="phonenumber" id="phonenumber" value="<?=$FIELDS['phonenumber'];?>">
											<span id="validate-msg" class="text-danger"><?=$ERRORS['phonenumber'];?></span>
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
									<div class="form-group">
										<button type="button" id="go-step-2" class="btn btn-info btn-lg">Proceed to Step 2</button>
										<button type="button" id="go-login" class="btn btn-default btn-lg" onclick="javascript:location.href='./'">Back to Login</button>
									</div>
								</div>
								<!-- Step 2 -->
								<div id="step-2" class="form-sec" style="display:none;">
									<h2 class="text-primary">Academic and Course Details</h2> <h3>Academic Qualifications</h3>
									<div class="row">
										<div class="col-sm-12">
											<table class="table table-responsive" id="ac_qualifications">
												<thead>
													<tr>
														<th style="width:20%"><label for="institution" class="">Institution Attended <abbr class="text-danger" title="required">*</abbr></label></th>
														<th style="width:20%"><label for="certificate" class="">Certificate Attained <abbr class="text-danger" title="required">*</abbr></label></th>
														<th style="width:20%"><label for="fromtoyear" class="">From - To Year <abbr class="text-danger" title="required">*</abbr></label></th>
														<th style="width:20%"><label for="grademark" class="">Grade/Mark Attained <abbr class="text-danger" title="required">*</abbr></label></th>
														<th style="width:20%"><label for="grademark" class="">Upload Cert <abbr class="text-danger" title="required">*</abbr></label></th>
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
														<td colspan="5"><a href="#" class="add_row btn btn-success btn-sm">Add row</a> <a href="#" class="remove_rows btn btn-danger btn-sm">Remove selected row(s)</a></td>
													</tr>
												</tfoot>
											</table>
										</div>
									</div>
									<div class="row">
										<div class="form-group col-sm-6">
											<label for="course" class="">Name of course applying for: <abbr class="text-danger" title="required">*</abbr></label> <?php echo sqlOption("SELECT `CourseID`,`CName` FROM `".DB_PREFIX."courses` WHERE `disabledFlag` = 0 AND `deletedFlag` = 0","course",$FIELDS['course'],"--Select course--");?>
											<span class="text-danger"><?=$ERRORS['course'];?></span>
										</div>
										<div class="form-group col-sm-6">
											<label for="StudyMode" class="">Prefered Study Mode: <abbr class="text-danger" title="required">*</abbr></label> <?php echo sqlOption("SELECT `pay_id`, `payment_name` FROM `".DB_PREFIX."payment_categs` WHERE type = 'StudyMode'","StudyMode",$FIELDS['StudyMode']);?>
											<span class="text-notice" style="font-size: 10px;">Note: <b>Online</b> mode means you do not go for physical classes, no extra cost. <b>Online & Class</b> mode means you do both online and going for physical classes. It attracts an extra KES 10,000(USD.100). <b>Online & Executive</b> mode means you can do it online and also attend classes at Hotel or Restaurant e.g. Nairobi Club. You get refreshments during classes. It attracts extra KES 20,000(USD.200)</span>
											<span class="text-danger"><?=$ERRORS['StudyMode'];?></span>
										</div>
									</div>
									<h3>English Proficiency</h3>
									<div class="row">
										<div class="form-group col-sm-6">
											<label for="englishproficiency" class="">Are you proficient in English? <abbr class="text-danger" title="required">*</abbr></label>
											<select name="englishproficiency" id="englishproficiency" class="form-control">
												<option value="None">Select your proficiency…</option>
												<?php
												foreach(list_yesno_status() as $k => $v){												
													if($k == $FIELDS['englishproficiency']){
														$select = 'selected="selected"';
													}
													else{
														$select = "";
													}
													echo "<option $select value=\"$k\">$v</option>";
												}
												?>
											</select>
											<span class="text-danger"><?=$ERRORS['englishproficiency'];?></span>
										</div>
										<div class="form-group col-sm-6">
											<label for="englishneeded" class="">Do you need help to improve your English Proficiency? </label>
											<select name="englishneeded" id="englishneeded" class="form-control">
												<?php
												foreach(list_yesno_status() as $k => $v){												
													if($k == $FIELDS['englishneeded']){
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
									</div>
									<h3>Sponsorship</h3>
									<div class="row">
										<div class="form-group col-sm-4">
											<label for="sponsorship" class="">How will you finance your studies? <abbr class="text-danger" title="required">*</abbr></label>
											<select name="sponsorship" id="sponsorship" class="form-control">
												<option value="None">Select your sponsorship…</option>
												<?php
												foreach(list_sponsorships() as $k => $v){												
													if($k == $FIELDS['sponsorship']){
														$select = 'selected="selected"';
													}
													else{
														$select = "";
													}
													echo "<option $select value=\"$k\">$v</option>";
												}
												?>
											</select>
											<span class="text-danger"><?=$ERRORS['sponsorship'];?></span>
										</div>
										<div class="form-group col-sm-8" id="requiresponsor">
											<label for="sponsor" class="">If not self-sponsored provide the following details: </label>
											<div class="row">
												<div class="col-sm-3">
													<label for="sponsorname" class="">Guardian/Sponsor Name </label>
												</div>
												<div class="col-sm-3">
													<input type="text" class="form-control" name="sponsorname" id="sponsorname" value="<?=$FIELDS["sponsorname"]?>">
												</div>
												<div class="col-sm-3">
													<label for="sponsorcontact" class="">Guardian/Sponsor Contact </label>
												</div>
												<div class="col-sm-3">
													<input type="text" class="form-control" name="sponsorcontact" id="sponsorcontact" value="<?=$FIELDS["sponsorcontact"]?>">
												</div>
											</div>
										</div>
									</div>
									<div class="form-group">
										<button type="button" id="back-step-1" class="btn btn-info btn-lg">Back</button>
										<button type="submit" id="go-step-3" class="btn btn-info btn-lg">Proceed to Step 3</button>
									</div>
								</div>
								<!-- Step 3 -->
								<div id="step-3" class="form-sec" style="display:none">
									<h2 class="text-primary">Confirm and Submit</h2>
									<h3>Next of kin information</h3>

									<div class="row">
												<div class="col-sm-3">
												<label for="nxtkin" class="">Next of kin Name <abbr class="text-danger" title="required">*</abbr> </label>
												<input type="text" class="form-control required" name="KinName" id="KinName" value="<?=$FIELDS["KinName"]?>">
												<span class="text-danger"><?=$ERRORS['KinName'];?></span>
												</div>

												<div class="col-sm-3">
												<label for="KinRelation" class="">Next of kin Relationship <abbr class="text-danger" title="required">*</abbr></label>
												<input type="text" class="form-control required" name="KinRelation" id="KinRelation" value="<?=$FIELDS["KinRelation"]?>">
												<span class="text-danger"><?=$ERRORS['KinRelation'];?></span>
												</div>

												<div class="col-sm-3">
												<label for="KinContact" class="">Next of kin Phone <abbr class="text-danger" title="required">*</abbr></label>
												<input type="text" class="form-control required" name="KinContact" id="KinContact" value="<?=$FIELDS["KinContact"]?>">
												<span class="text-danger"><?=$ERRORS['KinContact'];?></span>
												</div>

												<div class="col-sm-3">
												<label for="KinEmail" class="">Next of kin Email <abbr class="text-danger" title="required">*</abbr></label>
												<input type="email" class="form-control" name="KinEmail" id="KinEmail" value="<?=$FIELDS["KinEmail"]?>">
												<span class="text-danger"><?=$ERRORS['KinEmail'];?></span>
												</div>
											</div>

											<h3>Additional Information</h3>
	
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
										<button type="button" id="back-step-2" class="btn btn-info btn-lg">Back</button>
										<button type="submit" class="btn btn-primary btn-lg" name="load" id="load" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Processing Registration">Submit and Pay</button>
									</div>
								</div>
							</form>
						</div>
						<?php
						}
					break;
					case "pay":
						?>
						<div class="col-md-12">
							<h2>You are about to pay Ksh.<?php echo $_SESSION['AMOUNT']; ?></h2>
							<h3>Select a payment method</h3>
							<p>We support the following payment methods. Click on your preferred payment method:</p>
							
							<div class="row">
								<!--
								<div class="col-md-6">
									<h3>Lipa na MPesa</h3>
									<a href="?do=payment&paymentmethod=mpesa&paytype=Registration" title="Click to pay with MPesa"><img class="img-responsive" style="max-width:260px;" src="<?php echo IMAGE_FOLDER; ?>/payment_methods/lipa-na-mpesa.png" alt="Lina na MPesa"></a>
								</div>
								-->
								<div class="col-md-6">
									<h3>PesaPal Payment</h3>
									<a href="?do=payment&paymentmethod=pesapal&paytype=Registration" title="Click to pay with PesaPal"><img class="img-responsive" style="max-width:260px;" src="<?php echo IMAGE_FOLDER; ?>/payment_methods/pesapal.jpg" alt="PesaPal Payment"></a>
								</div>
							</div>
						</div>
						<?php
					break;
					case "recover":					
					?>
					<!-- Step 4 -->
					<div id="step-4" class="form-sec">
						<div id="hideMsg">
							<?php if(sizeof($_SESSION['message'])>0) echo $_SESSION['message']; ?>
						</div>
						<p>Your account is already registered with us. We can help you regain access to your account by choosing one of the following options.</p>
						<ul>
						  <li><a href="?do=reset">Forgot your password? Click here to request a password reset.</a></li>
							<li><a href="?do=login">You registered but did not pay registration fee? Click here to make payment now.</a></li>
							<li><a href="?do=login">You registered and paid registration fee but did not receive instructions on how to access the portal? Click here for assistance.</a></li>
							<li><a href="#">For any other help, please contact us.</a></li>
						</ul>
					</div>
					<?php
					break;
					default:
						echo ErrorMessage("Invalid request! The system failed to process your request. If the problem persists, please contact us.");
					break;
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
	
	var selectedSponsorOption = $('#sponsorship').val();
		
	if( selectedSponsorOption == "Self" ) {
		$("#requiresponsor").hide();
	}else {
		$("#requiresponsor").show();
	}

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
	 
	<?php
	if($task == "pay" || $task == "recover"){
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