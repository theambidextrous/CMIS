<?php
//Import the PHPMailer class into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
?>
<script language="javascript" type="text/javascript">
<!--
//Define page title
document.title = "<?=SYSTEM_SHORT_NAME?> | Students";
//-->
</script>

<div class="row">
  <div class="col-lg-12">
    <h1 class="page-header">Students</h1>
  </div>
  <!-- /.col-lg-12 --> 
</div>
<!-- /.row -->

<div class="row">
  <div class="col-lg-12">
    <div class="panel panel-default">
      <div class="panel-heading"> <i class="fa fa-users fa-fw"></i> Manage Students </div>
      <!-- /.panel-heading -->
      <div class="panel-body" id="multi-tabs">
			
        <ul class="nav nav-tabs cookie">
          <li class="active"><a data-toggle="tab" href="#tabs-1" title="Students"><span>Students</span></a></li>
          <li><a data-toggle="tab" href="#tabs-2" title="Announcements"><span>Announcements</span></a></li>
          <li><a data-toggle="tab" href="#tabs-3" title="Login History"><span>Login History</span></a></li>
        </ul>
        <div class="tab-content">
          <div id="tabs-1" class="tab-pane active">
            <!--Begin Forms-->
            <?php
            $a = isset($_GET["task"])?$_GET["task"]:"";
            $recid = intval(! empty($_GET['recid']))?$_GET['recid']:0;            
            $LoginID = !empty($_GET['studentID'])?$_GET['studentID']:"";
            
            switch ($a) {
            case "add":
              addrec();
              break;
            case "view":
              viewrec($recid);
              break;
            case "edit":
              editrec($recid);
              break;
            case "del":
              deleterec($recid);
              break;
            default:
              select();
              break;
            }		
            ?>
            <!--End Forms-->
          </div>
          <div id="tabs-2" class="tab-pane">
            <!--Begin Forms-->        
            <?php manage_announcements("Student"); ?>
            <!--End Forms-->
          </div>
          <div id="tabs-3" class="tab-pane">
            <!--Begin Forms-->
            <?php show_loginhistory(); ?>
            <!--End Forms-->
          </div>
        </div>
        <!-- /.tab-content -->
      </div>
      <!-- /.panel-body --> 
    </div>
    <!-- /.panel-default --> 
  </div>
  <!-- /.col-lg-12 --> 
</div>
<!-- /.row -->

<?php 
function select(){
	global $a;
	global $filter;
	global $filterfield;
	
	if ($a == "reset") {
		$filter = "";
		$filterfield = "";
	}
	
	$res = sql_select();
	$count = sql_getrecordcount();	
	
	if(isset($_GET['enable']) && isset($_GET['eid'])){
		$disabledFlag = intval(! empty($_GET['enable']))?$_GET['enable']:0;
		$editID = intval(! empty($_GET['eid']))?$_GET['eid']:0;
		
		sql_update_status($disabledFlag, $editID);
	}
?>
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li class="active">Available Students</li></ol>

<div id="hideMsg"><?php if(isset($_SESSION['MSG'])) echo $_SESSION['MSG'];?></div>

<?php showpagenav($pagecount); ?>
<table width="100%" class="display table table-striped table-bordered table-hover">
<thead>
<tr>
<th class="rid">#</th>
<th>Student ID</th>
<th>Student Name</th>
<th>Courses</th>
<th>Phone</th>
<th class="no-sort">Email</th>
<th class="no-sort">Active</th>
<th class="no-sort">Approved</th>
<th class="no-sort">Actions</th>
</tr>
</thead>
<tbody>
<?php
for ($i = 0; $i < $count; $i++){
	$row = db_fetch_array($res);
?>
<tr>
<td><?=$row["UID"]?></td>
<td><?=$row["StudentID"]?></td>
<td><?=$row["StudentName"]?></td>
<td><?=$row["Courses"]?></td>
<td><?=$row["Phone"]?></td>
<td><?="<a href=\"admin.php?tab=7&amp;task=add&amp;email=".$row['Email']."\" title=\"Send Email\"><img border=\"0\" src=\"".IMAGE_FOLDER."/icons/mail.png\" height=\"22\" width=\"22\" alt=\"Send\"></a>";?></td>
<?php
if($row['disabledFlag'] == 0){
	echo "<td align=\"center\"><a href=\"admin.php?tab=5&enable=1&eid=".$row['UID']."\" title=\"Click to disable ".$row['StudentName']."\"><img border=\"0\" src=\"".IMAGE_FOLDER."/icons/yes.png\" height=\"12\" width=\"12\" alt=\"Disable ".$row['StudentName']."\"></a></td>";
}else{
	echo "<td align=\"center\"><a href=\"admin.php?tab=5&enable=0&eid=".$row['UID']."\" title=\"Click to enable ".$row['StudentName']."\"><img border=\"0\" src=\"".IMAGE_FOLDER."/icons/no.png\" height=\"12\" width=\"12\" alt=\"Enable ".$row['StudentName']."\"></a></td>";
}
?>
<?php
if($row['approved'] == 0){
	echo "<td align=\"center\"><img border=\"0\" src=\"".IMAGE_FOLDER."/icons/no.png\" height=\"12\" width=\"12\" alt=\"disapprove ".$row['StudentName']."\"></td>";
}else{
	echo "<td align=\"center\"><img border=\"0\" src=\"".IMAGE_FOLDER."/icons/yes.png\" height=\"12\" width=\"12\" alt=\"approve ".$row['StudentName']."\"></td>";
}
?>
<td><a href="admin.php?tab=5&task=view&recid=<?=$i ?>&studentID=<?=$row['StudentID'] ?>">Manage</a> | <a href="admin.php?tab=5&task=edit&recid=<?=$i ?>&studentID=<?=$row['StudentID'] ?>">Edit</a> | <a href="admin.php?tab=5&task=del&recid=<?=$i ?>&studentID=<?=$row['StudentID'] ?>">Delete</a></td>
</tr>        
<?php
}
db_free_result($res);
?>
</tbody>
</table>
<?php 
showpagenav($pagecount);
unset($_SESSION['MSG']);
} 
?>
<?php function showrow($row, $recid){?>
<div class="table-responsive">
<table class="table table-bordered table-striped">
<tr>
<td width="30%">Student ID</td>
<td><?=$row["StudentID"]; ?></td>
</tr>
<tr>
<td>Student Name</td>
<td><?=$row["StudentName"]; ?></td>
</tr>
</table>
</div>
<?php } ?>

<?php 
function showrowdetailed($row, $recid){
global $conn,$class_dir;
?>
<div id="hideMsg"><?php if(sizeof($ERRORS['MSG'])>0) echo $ERRORS['MSG'];?></div>

<div class="head-details">
<h2 class="text-uppercase text-primary"><?=$row["StudentName"]; ?> <span class="small text-muted"><?=$row["StudentID"]; ?></span></h2>
</div>

<div id="adv-tab-container">
  <ul class="nav nav-tabs">
      <li class="active"><a data-toggle="tab" href="#sub-tabs-1" title="<?=SYSTEM_SHORT_NAME?> | Student Details">Student Details</a></li>
      <li><a data-toggle="tab" href="#sub-tabs-2" title="<?=SYSTEM_SHORT_NAME?> | Course Details">Course Details</a></li>
			<li><a data-toggle="tab" href="#sub-tabs-3" title="<?=SYSTEM_SHORT_NAME?> | Upoads">Uploads</a></li>
  </ul>
  <div class="tab-content">
    <!--sub-tabs-1-->
    <div id="sub-tabs-1" class="tab-pane active">
      <h3>Student Details</h3>
      <div class="row">
        <div class="col-md-6">
          <table class="table table-bordered table-striped">
          <tr><td><strong>Portal Status:</strong> </td><td><?=$row["Status"]; ?></td></tr>
          <tr><td><strong>Registration Date:</strong> </td><td><?=fixdatelong($row["RegDate"]); ?></td></tr>
          <tr><td><strong>Courses:</strong> </td><td><?=$row["Courses"]; ?></td></tr>        
          <tr><td><strong>Email:</strong> </td><td><a href="admin.php?tab=7&task=add&email=<?=$row["Email"]; ?>" title="Send Email"><?=$row["Email"]; ?></a></td></tr>
          <tr><td><strong>Phone:</strong> </td><td><?=$row["Phone"]; ?></td></tr>
          <tr><td><strong>Gender:</strong> </td><td><?=$row["Gender"]; ?></td></tr>
          <tr><td><strong>Birthday:</strong> </td><td><?=fixdateshortdob($row['DOB'])?></td></tr>
          </table>
        </div>
        <div class="col-md-6">
          <table class="table table-bordered table-striped">
          <tr><td valign="top"><strong>Address:</strong> </td><td><?=decode($row["Address"]); ?></td></tr>
          <tr><td><strong>City/Town:</strong> </td><td><?=$row["City"]; ?></td></tr>
          <tr><td><strong>State/County:</strong> </td><td><?=$row["State"]; ?></td></tr>
          <tr><td><strong>Zip/Postal Code:</strong> </td><td><?=$row["PostCode"]; ?></td></tr>
          <tr><td><strong>Country:</strong> </td><td><?=get_country($row["Country"]); ?></td></tr>
          </table>
        </div>
      </div>
    </div>
    <!--sub-tabs-2-->
    <div id="sub-tabs-2" class="tab-pane">
      <h3>Course Details</h3>
	  	<?php			
			if(!empty($row["Courses"])){
				$CourseIDs = explode(",", $row['Courses']);
				//Use the form select multiple inputs in a SQL Query using IN		
				$CourseIDStr = str_replace(",", "','", $row["Courses"]);
				
				reset($CourseIDs);
      
				//Actions
				$Action = !empty($_REQUEST['action'])?$_REQUEST['action']:"";
				$Action = strtolower($Action);
				if(!empty($Action) && !empty($row["StudentID"])){
						$selectedCourseID = !empty($_GET['courseID'])?$_GET['courseID']:'';
						$Token = md5(time());
						$mail = new PHPMailer;
						//Action
						switch($Action){
								case "registered":
								case "pass":
								case "fail":
								if(!empty($_POST['regUnitIDs'])){
										foreach($_POST['regUnitIDs'] as $UnitID){
											// Register Unit
											$sqlUdateRegUnit = sprintf("UPDATE `".DB_PREFIX."units_registered` SET `Status` = '%s' WHERE `StudentID` = '%s' AND `UnitID` = '%s'", ucwords($Action), $row["StudentID"], $UnitID);
											//Run query
											db_query($sqlUdateRegUnit,DB_NAME,$conn);							
										}
										// Confirm
										$_SESSION['MSG'] = ConfirmMessage("Selected units have been updated!");
										redirect("?tab=5&task=view&recid=$recid&studentID=".$row['StudentID']."#sub-tabs-2");
								}
								break;
								case "drop":
								if(!empty($_POST['regUnitIDs'])){
										foreach($_POST['regUnitIDs'] as $UnitID){
											// Drop Unit
											$sqlDropRegUnit = sprintf("DELETE FROM `".DB_PREFIX."units_registered` WHERE `StudentID` = '%s' AND `UnitID` = '%s'", $row["StudentID"], $UnitID);
											//Run query
											db_query($sqlDropRegUnit,DB_NAME,$conn);
										}
										// Confirm
										$_SESSION['MSG'] = ConfirmMessage("Selected units have been dropped!");
										redirect("?tab=5&task=view&recid=$recid&studentID=".$row['StudentID']."#sub-tabs-2");
								}
								break;
								case "add";
								if(isset($_POST['Add']) && !empty($_POST['UnitIDs'])){
									$CourseID = "";
									$UnitID = "";
									$Status = secure_string($_POST['Status']);
									
									foreach($_POST['UnitIDs'] as $UnitID){
										$CourseID = getUnitCourseID($UnitID);
										//Check if this student ID is already registered	
										$checkDuplicateSql = sprintf("SELECT `StudentID` FROM `".DB_PREFIX."units_registered` WHERE `StudentID` = '%s' AND `CourseID` = '%s AND `UnitID` = '%s", $row["StudentID"], $CourseID, $UnitID);
										//check if any results were returned
										if(!checkDuplicateEntry($checkDuplicateSql)){
											//Add unit
											$sqlUpdate = sprintf("INSERT INTO `".DB_PREFIX."units_registered` (`StudentID`, `CourseID`, `UnitID`, `Status`) VALUES ('%s', '%s', '%s', '%s')", $row["StudentID"], $CourseID, $UnitID, $Status);
											db_query($sqlUpdate,DB_NAME,$conn);
										}
											
									}
									// Confirm
									$_SESSION['MSG'] = ConfirmMessage("New units have been added!");
									redirect("?tab=5&task=view&recid=$recid&studentID=".$row['StudentID']."#sub-tabs-2");
								}
								break;
								case "approve":									
								if( !empty($selectedCourseID) ){
									require_once("$class_dir/mpdf/autoload.php");																				
									//Send a message to user
									$Subject = $row['StudentID']." - Admission Letter";
									$bodyemail = '<html><head>
									<title>'.$Subject.'</title>
									</head><body>
									<div style="background-color:#E1CDB7; color:#000; width:600px; margin:0 auto;">
									<div style="background:#C60; color:#FFF; min-width:584px; padding:8px;">
									<h2 style="font-size:15px;font-weight:700;line-height:25px;"><em>'.strtoupper(SYSTEM_NAME).' ADMISSION LETTER</em></h2>
									</div>
									<div style="padding:15px;">
									<h3 style="color:#333;">Dear '.$row['StudentName'].',</h3>
									<p style="text-align:justify;">We at <a href="'.PARENT_HOME_URL.'" target="_blank">'.SYSTEM_NAME.'</a> would like to thank you for applying to study with us. Your details and qualifications were verified and you are hereby admitted to persue the selected course. Open the attached admission letter and read it carefully.</p>
									<p style="text-align:justify;">Below are your portal access details:<br />
									Name: '.$row['StudentName'].'<br />
									Portal URL: <a href="'.SYSTEM_URL.'" target="_blank">'.SYSTEM_URL.'</a><br />
									StudentID: '.$row['StudentID'].'<br />
									Username: '.$row['StudentID'].'<br />
									Password: <strong><em>Use the password sent via email</em></strong></p>
									<h3>What next?</h3>
									<p style="text-align:justify;">Use the link above to login to the portal and select the units you want to pursue.</p>
									<p>Admissions Office,<br />
									'.SYSTEM_NAME.',<br />
									'.COMPANY_ADDRESS.'<br />
									TEL: '.COMPANY_PHONE.'<br />
									EMAIL: '.INFO_EMAIL.'<br />
									WEBSITE: '.PARENT_HOME_URL.'</p>
									</div>
									</div>
									</body></html>';
									
									$pdf_header = '<!--mpdf
									<htmlpageheader name="letterheader">
									<table width="100%" style=" font-family: sans-serif;">
									<tr>
									<td width="60%" style="color:#000000;">
									<span style="font-weight: bold; font-size: 14pt;">'.strtoupper(SYSTEM_NAME).' ADMISSIONS</span><br />
									'.COMPANY_ADDRESS.'<br /><span style="font-size: 15pt;">☎</span> '.COMPANY_PHONE.'</td>
									<td width="40%" style="text-align: right; vertical-align: top;">
									<img src="'.SYSTEM_LOGO_URL.'" style="width:160px; margin:0; padding:0; height:auto;"/><br/>
									{DATE jS F Y}</td></tr></table>
									<div style="margin-top: 0.5cm; text-align: left; font-family: sans-serif;"><span>Dear '.$row['FName'].',<br /></span></div>
									<div style="margin-top: 0.5cm; margin-bottom: 3cm; text-align: center; font-family: sans-serif;">
									<span style="font-weight: bold; font-size: 12pt; text-decoration: underline;">OFFER OF ADMISSION FOR '.strtoupper($row['StudentName']).' ('.$row['StudentID'].')</span>
									</div>
									</htmlpageheader>
									<htmlpagefooter name="letterfooter2">
									<div style="border-top: 1px solid #000000; font-size: 9pt; text-align: center; padding-top: 3mm; font-family: sans-serif; ">Page {PAGENO} of {nbpg}</div>
									</htmlpagefooter>
									<htmlpagefooter name="letterfooter4">
									<div style="border-top: 1px solid #000000; font-size: 9pt; text-align: center; padding-top: 3mm; font-family: sans-serif; ">Website: '.PARENT_HOME_URL.' | Email: '.INFO_EMAIL.'</div>
									</htmlpagefooter>
									mpdf-->
									<style>
									@page {
									margin-top: 2.5cm;
									margin-bottom: 2.5cm;
									margin-left: 2cm;
									margin-right: 2cm;
									footer: html_letterfooter2;
									background-color: lightblue;
									text-align: justify;
									}
									@page :first {
									margin-top: 7cm;
									margin-bottom: 2cm;
									header: html_letterheader;
									footer: html_letterfooter4;
									resetpagenum: 1;
									background-color: lightblue;
									text-align: justify;
									}
									@page letterhead {
									margin-top: 2.5cm;
									margin-bottom: 2.5cm;
									margin-left: 2cm;
									margin-right: 2cm;
									footer: html_letterfooter2;
									background-color: pink;
									text-align: justify;
									}
									@page letterhead :first {
									margin-top: 4cm;
									margin-bottom: 4cm;
									header: html_letterheader;
									footer: _blank;
									resetpagenum: 1;
									background-color: lightblue;
									text-align: justify;
									}
									</style>';
									$pdf_content = '<h3>Congratulations!</h3>
									<p>I am pleased to inform you that following your application to pursue <strong>'.getCourseName($selectedCourseID).'</strong> with us, we have offered you admission to study this course. Your student number is <strong>'.$row['StudentID'].'</strong>. The programme is offered in <strong>'.getCourseDepartmentName($selectedCourseID).'</strong>. Your study mode will be <strong>'.getStudentStudyMode($row['StudentID']).'</strong>.</p>
									<h3>E-learning System Access</h3>
									<p><strong>'.SYSTEM_NAME.'</strong> is a modern institution with a state-of-the-art E-learning system to facilitate your education. Your coursework, assignments, CATs and some exams will be administered through this system. Majority of the interaction with administration and/or lecturers will be facilitated through the system. For you to enjoy this resource, you need a confidential access which is as follows:<br>
									Go to <a href="'.SYSTEM_URL.'">'.SYSTEM_URL.'</a> and login using the credentials provided on email.</p>
									<h3>Starting of classes & Access to Lessons</h3>
									<p>Classes start immediately and you will access lessons once you pay <b>fees</b>. Fees are payable in two ways; <b>one-off</b> or in <b>installments</b></p>
									<h3>Fees Payable</h3>
									<p>The fee payable will be as follows:<br>
									<div style="width:80%;margin:0 auto;">
									'.getCourseFeesStructure($selectedCourseID, $row['StudyMode']).'
									</div>
									Fee payment should be made by card or mobile money transfer within the system once you login.</p>
									<h3>Course Units</h3>
									<p>For you to be certified as having completed the above course, you must pass all the '.getCourseUnits($selectedCourseID).' units. These units are:</p>
									<ol>
									'.getCourseUnitList($selectedCourseID).'
									</ol>
									<br><img src ="'.EMAIL_SIGNATURE_STAMP.'" style="float:right; width:160px; height:auto;"/>
									Yours truly,<br><br>
									<img src ="'.EMAIL_SIGNATURE_IMG.'" style="width:92px; height:auto;"/><br><br>
									'.EMAIL_SIGNATURE_NAME.'<br>
									'.EMAIL_SIGNATURE_TITLE.'.';
									
									$mpdf = new \Mpdf\Mpdf();
									
									$mpdf->WriteHTML($pdf_header);
									$mpdf->WriteHTML($pdf_content);
									$filename = friendlyName($row['StudentName'])."-admission-".date('dmYHis');
									$mpdf->Output(UPLOADS_PATH.'admissions'.DIRECTORY_SEPARATOR.$filename.'.pdf','F');			
									
									$body = preg_replace('/\\\\/','', $bodyemail); //Strip backslashes
									
									switch(MAILER){
									case 'smtp':
									$mail->isSMTP(); // telling the class to use SMTP
									$mail->SMTPDebug = 0;
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
									$mail->Subject = $Subject;
									$mail->AltBody = "To view the message, please use an HTML compatible email viewer!";
									$mail->msgHTML($body);
									$mail->addAddress($row['Email'], $row['StudentName']);
									$mail->addBCC(INFO_EMAIL, INFO_NAME);
									$mail->addAttachment(UPLOADS_PATH.'admissions'.DIRECTORY_SEPARATOR.$filename.'.pdf', '', $encoding = 'base64', $type = 'application/pdf');
									
									if(!$mail->Send()) {
										//failure
										$_SESSION['MSG'] = ErrorMessage("Your attempt to approve ".$row['StudentName']." to persue ".$selectedCourseID." has failed due to an error caused by the MAIL function. An admission letter could not be sent. Please try again later.");
										redirect("?tab=5&task=view&recid=$recid&studentID=".$row['StudentID']."#sub-tabs-2");
									}
									else{
										//Approve
										UpdateApproved($row['StudentID']);
										//success
										$_SESSION['MSG'] = ConfirmMessage("You have approved ".$row['StudentName']." to persue ".$selectedCourseID.". An admission letter has been sent successfully.");
										redirect("?tab=5&task=view&recid=$recid&studentID=".$row['StudentID']."#sub-tabs-2");
									}
								}
								break;
								case "reject":
									//Send a message to user
									$Subject = $row['StudentID']." - Application Rejected";
									$bodyemail = '<html><head>
									<title>$Subject</title>
									</head><body>
									<div style="background-color:#E1CDB7; color:#000; width:600px; margin:0 auto;">
									<div style="background:#C60; color:#FFF; min-width:584px; padding:8px;">
									<h2 style="font-size:15px;font-weight:700;line-height:25px;"><em>'.strtoupper(SYSTEM_NAME).' COURSE APPLICATION</em></h2>
									</div>
									<div style="padding:15px;">
									<h3 style="color:#333;">Dear '.$row['StudentName'].',</h3>
									<p style="text-align:justify;">We at <a href="'.PARENT_HOME_URL.'" target="_blank">'.SYSTEM_NAME.'</a> would like to thank you for applying to study with us. Your details and qualifications were verified but we regret to inform you that you were not qualified for admission to persue <strong>'.getCourseName($selectedCourseID).'</strong>. You however have the opportunity to select another course from the online portal.</p>
									<p style="text-align:justify;">Below are your portal access details:<br />
									Name: '.$row['StudentName'].'<br />
									Portal URL: <a href="'.SYSTEM_URL.'" target="_blank">'.SYSTEM_URL.'</a><br />
									StudentID: '.$row['StudentID'].'<br />
									Username: '.$row['StudentID'].'<br />
									Password: <strong><em>Use the password sent via email</em></strong></p>
									<h3>What next?</h3>
									<p style="text-align:justify;">Use the link above to login to the portal and select another course that you want to pursue.</p>
									<p>Admissions Office,<br />
									'.SYSTEM_NAME.',<br />
									'.COMPANY_ADDRESS.'<br />
									TEL: '.COMPANY_PHONE.'<br />
									EMAIL: '.INFO_EMAIL.'<br />
									WEBSITE: '.PARENT_HOME_URL.'</p>
									</div>
									</div>
									</body></html>';
									
									$body = preg_replace('/\\\\/','', $bodyemail); //Strip backslashes
									
									switch(MAILER){
									case 'smtp':
									$mail->isSMTP(); // telling the class to use SMTP
									$mail->SMTPDebug = 0;
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
									$mail->Subject = $row['StudentID']." - Application Rejected";
									$mail->AltBody = "To view the message, please use an HTML compatible email viewer!";
									$mail->msgHTML($body);
									$mail->addAddress($row['Email'], $row['StudentName']);
									$mail->addBCC(INFO_EMAIL, INFO_NAME);
									
									if(!$mail->Send()) {
										//failure
										$_SESSION['MSG'] = ErrorMessage("Your attempt to reject ".$row['StudentName']." from persuing ".$selectedCourseID." has failed due to an error caused by the MAIL function. A reject letter could not be sent. Please try again later.");
										redirect("?tab=5&task=view&recid=$recid&studentID=".$row['StudentID']."#sub-tabs-2");
									}else{
										//Reject
										UpdateRejected($row['StudentID']);
										//success											
										$_SESSION['MSG'] = ConfirmMessage("You have rejected ".$row['StudentName']." from persuing ".$selectedCourseID.". A rejection letter has been sent successfully.");
										redirect("?tab=5&task=view&recid=$recid&studentID=".$row['StudentID']."#sub-tabs-2");
									}
								break;
						}
														
				}
				?>
				<script>
				//<!--
				function checkSelUnits(field){
						if(document.units.sel.checked == true){
								for(var i=0; i < field.length; i++){
										field[i].checked=true;
								}
						}
						else{
								for(var i=0; i < field.length; i++){
										field[i].checked=false;
								}
						}
				}
				//-->
				</script>
				<div class="modal fade" id="addUnits" tabindex="-1" role="dialog" aria-labelledby="addUnitsLabel">
					<div class="modal-dialog modal-lg" role="document">
						<form class="form-inline" name="assign-lectures" method="post" action="admin.php?tab=5&task=view&recid=<?=$recid?>&studentID=<?=$row["StudentID"]?>&action=add#sub-tabs-2">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title" id="addUnitsLabel">Add new units</h4>
							</div>
							<div class="modal-body">
								<h2>Add units to <?=$row["StudentID"];?> for the following courses: <?=$row["Courses"]?></h2>
						
								<div class="form-group">
									<label>Hold Ctrl button on your keyboard to select multiple.</label>
									<?php echo sqlOptionMulti("SELECT `UnitID`,CONCAT(`UnitID`,' (',`UName`,')') AS `UName` FROM `".DB_PREFIX."units` AS U WHERE NOT EXISTS (SELECT `UnitID` FROM `".DB_PREFIX."units_registered` AS UR WHERE U.`UnitID` = UR.`UnitID`) AND U.`CourseID` IN ('" . $CourseIDStr . "') AND U.`disabledFlag` = 0 AND U.`deletedFlag` = 0 ORDER BY `UnitID` ASC","UnitIDs",$UnitIDs);?>
								</div>
								<div class="form-group">
									<select name="Status" class="form-control">
									<option value="Pending">Pending</option>
									<option value="Registered">Registered</option>
									</select>                      
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
								<input type="submit" class="btn btn-primary" name="Add" value="Add" />
							</div>
						</div><!-- /.modal-content -->
						</form>
					</div><!-- /.modal-dialog -->
				</div><!-- /.modal -->
				
				<form name="units" method="post">
				<div id="hideMsg"><?php if(isset($_SESSION['MSG'])) echo $_SESSION['MSG'];?></div>
				<p class="text-right"><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addUnits">Add Units</button></p>
				<table width="100%" class="table table-striped table-bordered table-hover">
				<thead>
				<tr>
				<th width="20">#</th>
				<th>UnitID</th>
				<th>Unit Name</th>
				<th>Year/Semester</th>
				<th>Status</th>
				<th style="text-align:center"><input type="checkbox" name="sel" title="Check All" onclick="checkSelUnits(document.getElementsByName('regUnitIDs[]'));" value="" /></th>
				</tr>
				</thead>
				<tbody>
				<?php			
				//Loop foreach($CourseIDs as &$CourseID)
				while(list(, $CourseID) = each($CourseIDs)) {
					echo "<tr><td colspan=\"6\"><a class=\"btn btn-sm btn-success\" href=\"admin.php?tab=5&task=view&action=approve&recid=$recid&studentID={$row['StudentID']}&courseID=$CourseID\">Approve</a> <a class=\"btn btn-sm btn-danger\" href=\"admin.php?tab=5&task=view&action=reject&recid=$recid&studentID={$row['StudentID']}&courseID=$CourseID\">Reject</a> <strong>($CourseID) ".getCourseName($CourseID)."</strong></td></tr>";
			
					$resUnits = registeredUnits($row["StudentID"], $CourseID);			
					if(db_num_rows($resUnits)>0){				
						$count = 1;
						while($units = db_fetch_array($resUnits)){
							echo "<tr>
							<td>".$count."</td>
							<td>".$units['UnitID']."</td>
							<td>".$units['UName']."</td>
							<td>".get_year_trimesters($units['YrTrim'])."</td>
							<td>".$units['Status']."</td>
							<td align=\"center\"><input type=\"checkbox\" id=\"selectedIDs\" name=\"regUnitIDs[]\" value=\"".$units['UnitID']."\"></td>
							</tr>";
							$count++;
						}
					}else{
						echo "<tr><td colspan=\"6\">This student has not been registered for any units under this course</td></tr>";
					}
				}
				?>
				</tbody>
				<tfoot>
				<tr><td align="right" colspan="6">
				<div class="form-inline">
					<div class="form-group">
						<label>With selected:</label>&nbsp;<select name="action" class="form-control">
						<option value="registered">Register</option>
						<option value="pass">Pass</option>
						<option value="fail">Fail</option>
						<option value="drop">Drop</option>
						</select>&nbsp;<input class="btn btn-default" type="submit" name="Update" value="Update" />
					</div>
				</div>
				</td></tr>
				</tfoot>
				</table>
				</form>
				<?php
				unset($_SESSION['MSG']);
      }else{
				echo "<p>This student is not registered to any courses. Click edit button below to register the student to a course.</p>";
      }
      ?>
    </div>
		
		<!--sub-tabs-3-->
    <div id="sub-tabs-3" class="tab-pane">
		  <h3>Student Identity</h3>
			<?php
			$IdentityImage = !empty($row["IdentityImage"])?'<a href="'. $row['IdentityImage'] .'" target="_blank">View/Download</a>':'N/A';
			$PassportPhoto = !empty($row["PassportPhoto"])?'<a href="'. $row['PassportPhoto'] .'" target="_blank">View/Download</a>':'N/A';
			
			echo 'Identity Document: '. $IdentityImage .'<br>';
			echo 'Passport Photo: '. $PassportPhoto .'<br>';
			
			
			$getQualifications = sprintf("SELECT `Institution`,`Certificate`,`Period`,`GradeMark`,`CertFile` FROM `".DB_PREFIX."ac_qualifications` WHERE `StudentID` = '%s'", $row["StudentID"]);
			//run the query
			$result = db_query($getQualifications,DB_NAME,$conn);
			?>
			<h3>Student Qualifications</h3>
			<table width="100%" class="display table table-striped table-bordered table-hover">
			<thead>
			<tr>
			<th>Institution</th>
			<th>Certificate</th>
			<th>Period</th>
			<th>Grade Mark</th>
			<th class="no-sort">Certificate File</th>
			</tr>
			</thead>
			<?php
			//check if any rows returned
			if(db_num_rows($result)>0){
				echo "<tbody>";
				while($row = db_fetch_array($result)){
					$CertFile = !empty($row["CertFile"])?'<a href="'. $row['CertFile'] .'" target="_blank">View/Download</a>':'N/A';
					echo "<tr>
					<td>".$row['Institution']."</td>
					<td>".$row['Certificate']."</td>
					<td>".$row['Period']."</td>
					<td>".$row['GradeMark']."</td>
					<td>". $CertFile ."</td>
					</tr>";		
				}
				echo "</tbody>";
			}else{
				
			}
		  ?>
			</table>
			<p>&nbsp;</p>
		</div>
    
    <div class="quick-nav btn-group">
      <a class="btn btn-default" href="admin.php?tab=5&task=add"><i class="fa fa-file-o fa-fw"></i>Add Student</a>
      <a class="btn btn-default" href="admin.php?tab=5&task=edit&recid=<?=$recid ?>"><i class="fa fa-pencil-square-o fa-fw"></i>Edit Student</a>
      <a class="btn btn-default" href="admin.php?tab=5&task=del&recid=<?=$recid ?>"><i class="fa fa-trash-o fa-fw"></i>Delete Student</a>
    </div>
    
  </div>
</div>

<?php } ?>
<?php 
function showroweditor($row, $iseditmode, $ERRORS){
global $a;
?>
<p class="text-center lead"><strong><?=strtoupper($a)?> STUDENT DETAILS</strong></p>
<p class="text-center small"><span class="text-danger">FIELDS MARKED WITH ASTERISKS (*) ARE REQUIRED</span></p>

<h2>Student Information</h2>

<div class="row">
  <div class="col-md-4">
    <div class="form-group">
    <label for="">First Name: <span class="text-danger">*</span></label>
    <input <?=$ERRORS['FName']?> type="text" value="<?=$row['FName']; ?>" name="FName" class="form-control required" />
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
    <label for="">Middle Name:</label>
    <input <?=$ERRORS['MName']?> type="text" value="<?=$row['MName']; ?>" name="MName" class="form-control" />
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
    <label for="">Last Name: <span class="text-danger">*</span></label>
    <input <?=$ERRORS['LName']?> type="text" value="<?=$row['LName']; ?>" name="LName" class="form-control required" />
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-4">
    <div class="form-group">
    <label for="">Phone: <span class="text-danger">*</span></label>
    <input <?=$ERRORS['Phone']?> type="text" value="<?=$row['Phone']; ?>" name="Phone" class="form-control required" />
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
    <label for="">Email: <span class="text-danger">*</span></label>
    <input <?=$ERRORS['Email']?> type="text" value="<?=$row['Email']; ?>" name="Email" class="form-control required email" />
    </div>
  </div>
  <div class="col-md-4">
    
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
        <label for="">Date of Birth: <span class="text-danger">*</span></label>
        <input <?=$ERRORS['DOB']?> class="form-control datepicker required" type="text" value="<?=$row['DOB']; ?>" name="DOB" />
        </div>
      </div>
    
      <div class="col-md-6">
        <div class="form-group">
        <label for="">Gender:</label>
        <select <?=$ERRORS['Gender']?> name="Gender" class="form-control">
        <option value="None">--Select--</option>
        <?php
        foreach(list_gender_status() as $k => $v){
            if($k == $row['Gender']){
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
    </div>
    
  </div>
</div>

<div class="row">
  <div class="col-md-6">

  <div class="form-group">
  <label for="">Address:</label>
  <textarea name="Address" class="form-control" rows="4"><?=decode($row['Address'])?></textarea>
  </div>
  
  <div class="form-group">
  <label for="">City/Town: <span class="text-danger">*</span></label>
  <input <?=$ERRORS['City']?> type="text" value="<?=$row['City']; ?>" name="City" class="form-control required" />
  </div>
  
  <div class="form-group">
  <label for="">State/County:</label>
  <input type="text" value="<?=$row['State']; ?>" name="State" class="form-control" />
  </div>
  
  <div class="form-group">
  <label for="">Zip/Postal Code:</label>
  <input <?=$ERRORS['PostCode']?> type="text" value="<?=$row['PostCode']; ?>" name="PostCode" class="form-control" maxlength="5" />
  </div>
  
  <div class="form-group">
  <label for="">Country: <span class="text-danger">*</span></label>
  <select <?=$ERRORS['Country']?> name="Country" class="form-control">
  <option value="None">--Select--</option>
  <?php
  foreach(list_countries() as $k => $v){
      if($k == $row['Country']){
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
  <div class="col-md-6">

  <h2>Login Information</h2>
  <p class="small">Required to login to the student portal</p>
  
  <div class="form-group">
  <label for="">Student ID: <span class="text-danger">*</span></label>
  <strong><?=!empty($row['StudentID'])?$row['StudentID']:"New student ID will be generated automatically"; ?></strong>
  </div>
  
  <div class="form-group">
  <label for="">Assign Password:</label>
  <input type="password" value="<?=$row['Password']; ?>" name="Password" class="form-control" /><span class="text-danger"><?=$ERRORS['Password'];?></span>
  </div>
  
  <div class="form-group">
  <label for="">Verify Password:</label>
  <input type="password" value="<?=$row['VerifyPass']; ?>" name="VerifyPass" class="form-control" /><span class="text-danger"><?=$ERRORS['VerifyPass'];?></span>
  </div>
  
  <div class="form-group">
  <label for="">Courses:</label>
  <?php echo sqlOptionMulti("SELECT `CourseID`,CONCAT(`CourseID`,' (',`CName`,')') AS `CName` FROM `".DB_PREFIX."courses` WHERE `disabledFlag` = 0 AND `deletedFlag` = 0 ORDER BY `CourseID` ASC","CourseIDs",$row['Courses']);?>
  </div>

  </div>
</div>
<?php } ?>

<?php
function showpagenav() {
?>
<div class="quick-nav btn-group">
<a class="btn btn-primary" href="admin.php?tab=5&task=add">Add Student</a>
<a class="btn btn-default" href="admin.php?tab=5&task=reset">Reset Filters</a>
</div>
<?php } ?>

<?php function showrecnav($a, $recid, $count) { ?>
<div class="quick-nav btn-group">
<a class="btn btn-default" href="admin.php?tab=5"><i class="fa fa-undo fa-fw"></i> Back to Students</a>
<?php if ($recid > 0) { ?>
<a class="btn btn-default" href="admin.php?tab=5&task=<?=$a ?>&recid=<?=$recid - 1 ?>"><i class="fa fa-arrow-left fa-fw"></i> Prior Record</a>
<?php } if ($recid < $count - 1) { ?>
<a class="btn btn-default" href="admin.php?tab=5&task=<?=$a ?>&recid=<?=$recid + 1 ?>"><i class="fa fa-arrow-right fa-fw"></i> Next Record</a>
<?php } ?>
</div>
<?php } ?>

<?php 
function viewrec($recid){
	
	$res = sql_select();
	$count = sql_getrecordcount();
	db_data_seek($res, $recid);
	$row = db_fetch_array($res); 
	
	if($row['disabledFlag'] == 0){
		$row["Status"] = "Enabled";
	}else{
		$row["Status"] = "Disabled";
	}	 
	?>
	<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?tab=5">Students</a></li><li class="active">View Student</li></ol>
	<?php 
	showrecnav("view", $recid, $count);
	showrowdetailed($row, $recid);
	db_free_result($res);
} 
?>

<?php 
function addrec() {
	global $class_dir,$conn;
	require_once("$class_dir/class.validator.php3");
	
	// Variables
	$ERRORS = array();
	$FIELDS = array();
	$ERR = 'id="highlight"';//Error highlighter
	
	// Commands
	if(isset($_POST["Add"])){
		// Student info		
		$FIELDS['FName'] = secure_string(ucwords($_POST['FName']));
		$FIELDS['MName'] = secure_string(ucwords($_POST['MName']));
		$FIELDS['LName'] = secure_string(ucwords($_POST['LName']));
		$FIELDS['Gender'] = secure_string($_POST['Gender']);
		$FIELDS['Courses'] = "";
		if(!empty($_POST['CourseIDs'])){$FIELDS['Courses'] = implode(",", $_POST['CourseIDs']);}
		$FIELDS['Email'] = secure_string(strtolower($_POST['Email']));
		$FIELDS['DOB'] = secure_string($_POST['DOB']);
		$FIELDS['Phone'] = secure_string($_POST['Phone']);
		$FIELDS['Address'] = secure_string($_POST['Address']);			
		$FIELDS['City'] = secure_string($_POST['City']);
		$FIELDS['State'] = secure_string($_POST['State']);
		$FIELDS['PostCode'] = secure_string($_POST['PostCode']);
		$FIELDS['Country'] = secure_string($_POST['Country']);
		//$FIELDS['StudentID'] = secure_string(whitespace_trim(strtoupper($_POST['StudentID'])));
		$FIELDS['Password'] = isset($_POST['Password'])?secure_string($_POST['Password']):"";
		$FIELDS['VerifyPass'] = isset($_POST['VerifyPass'])?secure_string($_POST['VerifyPass']):"";
		$FIELDS['EncryptPass'] = hashedPassword($FIELDS['VerifyPass']);
		$FIELDS['RegDate'] = date('Y-m-d');
		
		// Validator data
		$check = new validator();
		// validate entry		
		// validate "FName" field
		if(!$check->is_String($FIELDS['FName']))
		$ERRORS['FName'] = $ERR;
		// validate "LName" field
		if(!$check->is_String($FIELDS['LName']))
		$ERRORS['LName'] = $ERR;
		// validate "Email" field
		if(!$check->is_email($FIELDS['Email']))
		$ERRORS['Email'] = $ERR;
		// validate "DOB" field
		if(!empty($FIELDS['DOB'])){
			$SplitDate = explode('/', $FIELDS['DOB']);// Split date by '/'
			//checkdate($month, $day, $year)
			if(checkdate($SplitDate[0],$SplitDate[1],$SplitDate[2])){			
				$FIELDS['dbDOB'] = db_fixdate($FIELDS['DOB']);// YYYY-dd-mm
			}else{
				$ERRORS['DOB'] = $ERR;
			}
		}
		// validate "Phone" field
		if(!$check->is_phone($FIELDS['Phone']))
		$ERRORS['Phone'] = $ERR;
		// validate "City" field
		if(!$check->is_String($FIELDS['City']))
		$ERRORS['City'] = $ERR;
		// validate "PostCode" field
		if(!isset($FIELDS['PostCode']) && !$check->is_zipcode($FIELDS['PostCode']))
		$ERRORS['PostCode'] = $ERR;
		// validate "Country" field
		if($FIELDS['Country'] == "None")
		$ERRORS['Country'] = $ERR;
		// validate "Password" field
		if(!empty($FIELDS['Password'])){
			// validate "Password" field
			if(!$check->is_password($FIELDS['Password']))
			$ERRORS['Password'] = "Password must be at least 7 letters mixed with digits and symbols";
			// validate "VerifyPass" field
			if(!$check->cmp_string($FIELDS['VerifyPass'],$FIELDS['Password']))
			$ERRORS['VerifyPass'] = "Passwords entered do not match";
		}		
		
		//Check if this email address is already registered	
		$checkDuplicateSql2 = sprintf("SELECT `Email` FROM `".DB_PREFIX."students` WHERE `Email` = '%s'", $FIELDS['Email']);
		//check if any results were returned
		if(checkDuplicateEntry($checkDuplicateSql2)){
			$ERRORS['Email'] = "This email ".$FIELDS['Email']." is already attached to another student.";
		}
		
		// check for errors
		if(sizeof($ERRORS) > 0){
			$ERRORS['MSG'] = ErrorMessage("PLEASE CORRECT HIGHLIGHTED FIELDS!");			
		}
		else{
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
			
			//Format: FE/COURSEID/STDID/2018
			$FIELDS['StudentID'] = "FE" ."/". $FIELDS['Courses'] ."/". $MUID ."/". date('Y');
			
			if(sql_insert($FIELDS)){
				
				$mail = new PHPMailer;
				//Send a message to user
				$Subject = $FIELDS['StudentID']." - Account Created";
				$bodyemail = '<html><head>
				<title>'.$Subject.'</title>
				</head><body><div style="background-color:#E1CDB7; color:#000; width:600px; margin:0 auto;">
				<div style="background:#C60; color:#FFF; min-width:584px; padding:8px;">
				<h2 style="font-size:15px;font-weight:700;line-height:25px;"><em>'.strtoupper(SYSTEM_NAME).' ACCOUNT CREATED</em></h2>
				</div>
				<div style="padding:15px;">
				<h3 style="color:#333;">Dear '.$FIELDS['StudentName'].',</h3>
				<p style="text-align:justify;">We at <a href="'.PARENT_HOME_URL.'" target="_blank">'.SYSTEM_NAME.'</a> would like to thank you for applying to study with us. Your account has been created and you should now be able to access our online portal.</p>
				<p style="text-align:justify;">Below are your portal access details:<br />
				Name: '.$FIELDS['StudentName'].'<br />
				Portal URL: <a href="'.SYSTEM_URL.'" target="_blank">'.SYSTEM_URL.'</a><br />
				StudentID: '.$FIELDS['StudentID'].'<br />
				Username: '.$FIELDS['StudentID'].'<br />
				Password: <a href=\"'.SYSTEM_URL.'/portal/?do=activate&token='.$FIELDS['Token'].'\" target=\"_blank\"><strong>Click here to set your account password</strong></a></p>
				<h3>What next?</h3>
				<p style="text-align:justify;">Use the link above to login to the portal and select the units you want to pursue.</p>
				<p>Admissions Office,<br />
				'.SYSTEM_NAME.',<br />
				'.COMPANY_ADDRESS.'<br />
				TEL: '.COMPANY_PHONE.'<br />
				EMAIL: '.INFO_EMAIL.'<br />
				WEBSITE: '.PARENT_HOME_URL.'</p>
				</div></div>
				</body></html>';
				
				$body = preg_replace('/\\\\/','', $bodyemail); //Strip backslashes
										
				switch(MAILER){
				case 'smtp':
				$mail->isSMTP(); // telling the class to use SMTP
				$mail->SMTPDebug = 0;
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
				$mail->Subject = $Subject;
				$mail->AltBody = "To view the message, please use an HTML compatible email viewer!";
				$mail->msgHTML($body);
				$mail->addAddress($FIELDS['Email'], $FIELDS['StudentName']);
				//$mail->addBCC(INFO_EMAIL, INFO_NAME);
				
				if(!$mail->Send()) {
					//Display Confirmation Message
					$_SESSION['MSG'] = ConfirmMessage("New student has been created successfully.");
					redirect("admin.php?tab=5");
				}else{
					//Display Confirmation Message
					$_SESSION['MSG'] = ConfirmMessage("New student has been created and emailed successfully.");
					redirect("admin.php?tab=5");
				}
			}else{
				//Display Error Message
				$ERRORS['MSG'] = ErrorMessage("Failed to create new student. Check to confirm if all fields are well populated and try again.");
			}
			
		}
	}
		
	$row["FName"] = !empty($FIELDS['FName'])?$FIELDS['FName']:"";
	$row["MName"] = !empty($FIELDS['MName'])?$FIELDS['MName']:"";
	$row["LName"] = !empty($FIELDS['LName'])?$FIELDS['LName']:"";
	$row["Gender"] = !empty($FIELDS['Gender'])?$FIELDS['Gender']:"";
	$row["Courses"] = !empty($FIELDS['Courses'])?$FIELDS['Courses']:"";
	$row["Email"] = !empty($FIELDS['Email'])?$FIELDS['Email']:"";
	$row["DOB"] = !empty($FIELDS['DOB'])?$FIELDS['DOB']:"";
	$row["Phone"] = !empty($FIELDS['Phone'])?$FIELDS['Phone']:"";
	$row["Address"] = !empty($FIELDS['Address'])?$FIELDS['Address']:"";
	$row["City"] = !empty($FIELDS['City'])?$FIELDS['City']:"";
	$row["State"] = !empty($FIELDS['State'])?$FIELDS['State']:"";
	$row["PostCode"] = !empty($FIELDS['PostCode'])?$FIELDS['PostCode']:"";
	$row["Country"] = !empty($FIELDS['Country'])?$FIELDS['Country']:"KE";
	$row["StudentID"] = !empty($FIELDS['StudentID'])?$FIELDS['StudentID']:"";
?>
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?tab=5">Students</a></li><li class="active">Add Student</li></ol>

<a class="btn btn-default" href="admin.php?tab=5"><i class="fa fa-undo fa-fw"></i> Back to Students</a>

<p class="text-center"><?php if(sizeof($ERRORS['MSG'])>0) echo $ERRORS['MSG'];?></p>
<form id="validateform" enctype="multipart/form-data" action="admin.php?tab=5&task=add" method="post">
<input type="hidden" name="sql" value="insert" />
<?php
showroweditor($row, false, $ERRORS);
?>
<p class="text-center">
<input class="btn btn-primary" type="submit" name="Add" value="Save" />
<input class="btn btn-default" type="button" name="cancel" value="Cancel" onclick="javascript:location.href='admin.php?tab=5'" />
</p>
</form>
<?php } ?>

<?php 
function editrec($recid){
	global $class_dir;
	require_once("$class_dir/class.validator.php3");
	
	// Variables
	$ERRORS = array();
	$FIELDS = array();
	$ERR = 'id="highlight"';//Error highlighter
	
	// Commands
	if(isset($_POST["Edit"])){
		// Student info		
		$FIELDS['FName'] = secure_string(ucwords($_POST['FName']));
		$FIELDS['MName'] = secure_string(ucwords($_POST['MName']));
		$FIELDS['LName'] = secure_string(ucwords($_POST['LName']));
		$FIELDS['StudentID'] = $_POST['StudentID'];
		$FIELDS['StudyMode'] = $_POST['StudyMode'];
		$FIELDS['StudentName'] = $FIELDS['FName']." ".$FIELDS['LName'];
		$FIELDS['Gender'] = secure_string($_POST['Gender']);
		$FIELDS['Courses'] = "";
		if(!empty($_POST['CourseIDs'])){$FIELDS['Courses'] = implode(",", $_POST['CourseIDs']);}
		$FIELDS['Email'] = secure_string($_POST['Email']);
		$FIELDS['DOB'] = secure_string($_POST['DOB']);
		$FIELDS['Phone'] = secure_string($_POST['Phone']);
		$FIELDS['Address'] = secure_string($_POST['Address']);			
		$FIELDS['City'] = secure_string($_POST['City']);
		$FIELDS['State'] = secure_string($_POST['State']);
		$FIELDS['PostCode'] = secure_string($_POST['PostCode']);
		$FIELDS['Country'] = secure_string($_POST['Country']);		
		$FIELDS['Password'] = isset($_POST['Password'])?secure_string($_POST['Password']):"";
		$FIELDS['VerifyPass'] = isset($_POST['VerifyPass'])?secure_string($_POST['VerifyPass']):"";
		$FIELDS['EncryptPass'] = hashedPassword($FIELDS['VerifyPass']);
		$FIELDS['Token'] = md5(time());
		
		// Validator data
		$check = new validator();
		// validate entry		
		// validate "FName" field
		if(!$check->is_String($FIELDS['FName']))
		$ERRORS['FName'] = $ERR;
		// validate "LName" field
		if(!$check->is_String($FIELDS['LName']))
		$ERRORS['LName'] = $ERR;
		// validate "Email" field
		if(!$check->is_email($FIELDS['Email']))
		$ERRORS['Email'] = $ERR;
		// validate "DOB" field
		if(!empty($FIELDS['DOB'])){
			$SplitDate = explode('/', $FIELDS['DOB']);// Split date by '/'
			//checkdate($month, $day, $year)
			if(checkdate($SplitDate[0],$SplitDate[1],$SplitDate[2])){
				$FIELDS['dbDOB'] = db_fixdate($FIELDS['DOB']);// YYYY-dd-mms
			}else{
				$ERRORS['DOB'] = $ERR;
			}
		}
		// validate "Phone" field
		if(!$check->is_phone($FIELDS['Phone']))
		$ERRORS['Phone'] = $ERR;
		// validate "City" field
		if(!$check->is_String($FIELDS['City']))
		$ERRORS['City'] = $ERR;
		// validate "PostCode" field
		if(!isset($FIELDS['PostCode']) && !$check->is_zipcode($FIELDS['PostCode']))
		$ERRORS['PostCode'] = $ERR;
		// validate "Country" field
		if($FIELDS['Country'] == "None")
		$ERRORS['Country'] = $ERR;
		// validate "Password" field
		if(!empty($FIELDS['Password'])){
			// validate "Password" field
			if(!$check->is_password($FIELDS['Password']))
			$ERRORS['Password'] = "Password must be at least 7 letters mixed with digits and symbols";
			// validate "VerifyPass" field
			if(!$check->cmp_string($FIELDS['VerifyPass'],$FIELDS['Password']))
			$ERRORS['VerifyPass'] = "Passwords entered do not match";
		}
		
		// check for errors
		if(sizeof($ERRORS) > 0){
			$ERRORS['MSG'] = ErrorMessage("PLEASE CORRECT HIGHLIGHTED FIELDS!");			
		}
		else{			
			if(sql_update($FIELDS)) {
				$mail = new PHPMailer;
				//Send a message to user
				$Subject = $FIELDS['StudentID']." - Account Updated";
				$bodyemail = '<html><head>
				<title>'.$Subject.'</title>
				</head><body><div style="background-color:#E1CDB7; color:#000; width:600px; margin:0 auto;">
				<div style="background:#C60; color:#FFF; min-width:584px; padding:8px;">
				<h2 style="font-size:15px;font-weight:700;line-height:25px;"><em>'.strtoupper(SYSTEM_NAME).' ACCOUNT UPDATE</em></h2>
				</div>
				<div style="padding:15px;">
				<h3 style="color:#333;">Dear '.$FIELDS['StudentName'].',</h3>
				<p style="text-align:justify;">We at <a href="'.PARENT_HOME_URL.'" target="_blank">'.SYSTEM_NAME.'</a> would like to thank you for applying to study with us. Your account has been updated and you should now be able to access our online portal.</p>
				<p style="text-align:justify;">Below are your portal access details:<br />
				Name: '.$FIELDS['StudentName'].'<br />
				Portal URL: <a href="'.SYSTEM_URL.'" target="_blank">'.SYSTEM_URL.'</a><br />
				StudentID: '.$FIELDS['StudentID'].'<br />
				Username: '.$FIELDS['StudentID'].'<br />';
				if(empty($FIELDS['VerifyPass'])){
					$bodyemail .= 'Password: (Password not changed)';
				}else{
					$bodyemail .= 'Password: <a href=\"'.SYSTEM_URL.'/portal/?do=activate&token='.$FIELDS['Token'].'\" target=\"_blank\"><strong>Click here to set your account password</strong></a>';
				}
				$bodyemail .= '</p>
				<h3>What next?</h3>
				<p style="text-align:justify;">Use the link above to login to the portal and select the units you want to pursue.</p>
				<p>Admissions Office,<br />
				'.SYSTEM_NAME.',<br />
				'.COMPANY_ADDRESS.'<br />
				TEL: '.COMPANY_PHONE.'<br />
				EMAIL: '.INFO_EMAIL.'<br />
				WEBSITE: '.PARENT_HOME_URL.'</p>
				</div></div>
				</body></html>';
				
				$body = preg_replace('/\\\\/','', $bodyemail); //Strip backslashes
										
				switch(MAILER){
				case 'smtp':
				$mail->isSMTP(); // telling the class to use SMTP
				$mail->SMTPDebug = 0;
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
				$mail->Subject = $Subject;
				$mail->AltBody = "To view the message, please use an HTML compatible email viewer!";
				$mail->msgHTML($body);
				$mail->addAddress($FIELDS['Email'], $FIELDS['StudentName']);
				//$mail->addBCC(INFO_EMAIL, INFO_NAME);
				
				if(!$mail->Send()) {
					//Display Confirmation Message
					$_SESSION['MSG'] = ConfirmMessage("Student details have been updated successfully");
					redirect("admin.php?tab=5");
				}else{
					//Display Confirmation Message
					$_SESSION['MSG'] = ConfirmMessage("Student details have been updated and emailed successfully.");
					redirect("admin.php?tab=5");
				}
			}
			else{
				//Display Error Message
				$ERRORS['MSG'] = ErrorMessage("No changes made. Check to confirm if all fields are well populated and try again.");
			}
		}
  	}
	
	$res = sql_select();
	$count = sql_getrecordcount();
	db_data_seek($res, $recid);
	$row = db_fetch_array($res);
		
	$row["FName"] = !empty($FIELDS['FName'])?$FIELDS['FName']:$row['FName'];
	$row["MName"] = !empty($FIELDS['MName'])?$FIELDS['MName']:$row['MName'];
	$row["LName"] = !empty($FIELDS['LName'])?$FIELDS['LName']:$row['LName'];
	$row["StudentName"] = !empty($FIELDS['StudentName'])?$FIELDS['StudentName']:$row['StudentName'];
	$row["Gender"] = !empty($FIELDS['Gender'])?$FIELDS['Gender']:$row['Gender'];
	$row["Courses"] = !empty($FIELDS['Courses'])?$FIELDS['Courses']:$row['Courses'];
	$row["Email"] = !empty($FIELDS['Email'])?$FIELDS['Email']:$row['Email'];
	$row["DOB"] = !empty($FIELDS['DOB'])?$FIELDS['DOB']:fixdatepicker($row['DOB']);
	$row["Phone"] = !empty($FIELDS['Phone'])?$FIELDS['Phone']:$row['Phone'];
	$row["Address"] = !empty($FIELDS['Address'])?$FIELDS['Address']:$row['Address'];
	$row["City"] = !empty($FIELDS['City'])?$FIELDS['City']:$row['City'];
	$row["State"] = !empty($FIELDS['State'])?$FIELDS['State']:$row['State'];
	$row["PostCode"] = !empty($FIELDS['PostCode'])?$FIELDS['PostCode']:$row['PostCode'];
	$row["Country"] = !empty($FIELDS['Country'])?$FIELDS['Country']:$row['Country'];
	$row["StudentID"] = !empty($FIELDS['StudentID'])?$FIELDS['StudentID']:$row['StudentID'];
?>
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?tab=5">Students</a></li><li class="active">Edit Student</li></ol>
<?php showrecnav("edit", $recid, $count); ?>
<form id="validateform" enctype="multipart/form-data" action="admin.php?tab=5&task=edit&recid=<?=$recid?>" method="post">
<p class="text-center"><?php if(sizeof($ERRORS['MSG'])>0) echo $ERRORS['MSG'];?></p>
<input type="hidden" name="sql" value="update" />
<input type="hidden" name="eid" value="<?=$row["UID"] ?>" />
<input type="hidden" name="StudentID" value="<?=$row["StudentID"] ?>" />
<input type="hidden" name="StudyMode" value="<?=$row["StudyMode"] ?>" />
<?php showroweditor($row, true, $ERRORS); ?>
<p class="text-center">
<input class="btn btn-primary" type="submit" name="Edit" value="Save" />
<input class="btn btn-default" type="button" name="cancel" value="Cancel" onclick="javascript:location.href='admin.php?tab=5'" />
</p>
</form>
<?php
db_free_result($res);
} 
?>

<?php 
function deleterec($recid){
	
	// Commands
	if(isset($_POST["Delete"])){
		sql_delete();
	}
  
	$res = sql_select();
	$count = sql_getrecordcount();
	db_data_seek($res, $recid);
	$row = db_fetch_array($res);  
?>
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?tab=5">Students</a></li><li class="active">Delete Student</li></ol>
<?php showrecnav("del", $recid, $count); ?>
<form action="admin.php?tab=5&task=del&recid=<?=$recid?>" method="post">
<input type="hidden" name="sql" value="delete" />
<input type="hidden" name="eid" value="<?=$row["UID"] ?>" />
<?php showrow($row, $recid) ?>
<strong>Are you sure you want to delete this record? </strong><div class="btn-group"><input class="btn btn-primary" type="submit" name="Delete" value="Yes" /> <input class="btn btn-default" type="button" name="Ignore" value="No" onclick="javascript:history.go(-1)" /></div>
</form>
<?php
db_free_result($res);
}
?>

<?php
function manage_announcements($UserType){
	global $conn;
	global $_POST, $_GET;
	
	// Variables
	$ERRORS = array();
	$subtab = isset($_GET["subtab"])?$_GET["subtab"]:"";
	$action = isset($_GET["action"])?$_GET["action"]:"view";
	$action = strtolower($action);
	
	$editID = intval(! empty($_GET['eid']))?$_GET['eid']:0;
	
	$Title = "";
	$Announcement = "";
	$PublishFrom = "";
	$PublishTo = "";
	
	switch ($action) {
		case "add":
		case "edit":
			if(isset($_POST["Save"])){
				$Title = secure_string($_POST['Title']);
				$Announcement = secure_string($_POST['Announcement']);
				$PublishFrom = secure_string($_POST['PublishFrom']);
				$PublishTo = secure_string($_POST['PublishTo']);
				
				// Validate Fields
				// validate "Announcement" field
				if(empty($Announcement))
				$ERRORS['Announcement'] = "Announcement cannot be left empty";
				// validate "PublishFrom" field
				if(!empty($PublishFrom)){
					$SplitDate = explode('/', $PublishFrom);// Split date by '/'
					//checkdate($month, $day, $year)
					if(checkdate($SplitDate[0],$SplitDate[1],$SplitDate[2])){
						$dbPublishFrom = db_fixdatetime($PublishFrom);// YYYY-dd-mms
					}else{
						$ERRORS['PublishFrom'] = "A valid date is required";
					}
				}
				// validate "PublishTo" field
				if(!empty($PublishTo)){
					$SplitDate = explode('/', $PublishTo);// Split date by '/'
					//checkdate($month, $day, $year)
					if(checkdate($SplitDate[0],$SplitDate[1],$SplitDate[2])){
						$dbPublishTo = db_fixdatetime($PublishTo);// YYYY-dd-mms
					}else{
						$ERRORS['PublishTo'] = "A valid date is required";
					}
				}
				
				// check for errors
				if(sizeof($ERRORS) > 0){			
					$ERRORS['MSG'] = ErrorMessage("PLEASE CORRECT HIGHLIGHTED FIELDS!");
				}else{
					if($action == "add"){
						//Add announcement
						$sqlAdd = sprintf("INSERT INTO `".DB_PREFIX."announcements` (`Title`, `Announcement`, `UserType`, `PublishFrom`, `PublishTo`) VALUES ('%s', '%s', '%s', '%s', '%s')", $Title, $Announcement, $UserType, $dbPublishFrom, $dbPublishTo);
						db_query($sqlAdd,DB_NAME,$conn);
						//Check if added
						if(db_affected_rows($conn)>0){
							$_SESSION['MSG'] = ConfirmMessage("Announcement added successfully");
							//Redirect
							redirect("admin.php?tab=5#tabs-2");
						}else{
							$ERRORS['MSG'] = ErrorMessage("Failed to add new announcement. Please try again later.");
						}
					}else{
						//Update record
						$sqlUpdate = sprintf("UPDATE `".DB_PREFIX."announcements` SET `Title`='%s', `Announcement`='%s', `PublishFrom`='%s', `PublishTo`='%s' WHERE `UID` = '%s'", $Title, $Announcement, $dbPublishFrom, $dbPublishTo, $editID);
						db_query($sqlUpdate,DB_NAME,$conn);
						//Check if updated
						if(db_affected_rows($conn)>0){
							$_SESSION['MSG'] = ConfirmMessage("Announcement updated successfully");
							//Redirect
							redirect("admin.php?tab=5#tabs-2");
						}else{
							$ERRORS['MSG'] = WarnMessage("No changes made!");
						}
					}
				}
			}
			
			if(!empty($editID)){
				//Get data
				$resGetSql = sprintf("SELECT `Title`,`Announcement`,`PublishFrom`,`PublishTo` FROM `".DB_PREFIX."announcements` WHERE `UID` = %d LIMIT %d;", $editID, 1);
				//run the query
				$result = db_query($resGetSql,DB_NAME,$conn);
				$rowAnnounce = db_fetch_array($result);
				
				$Title = !empty($Title)?$Title:$rowAnnounce['Title'];
				$Announcement = !empty($Announcement)?$Announcement:$rowAnnounce['Announcement'];
				$PublishFrom = !empty($PublishFrom)?$PublishFrom:fixdatepicker($rowAnnounce['PublishFrom']);
				$PublishTo = !empty($PublishTo)?$PublishTo:fixdatepicker($rowAnnounce['PublishTo']);
			}
			?>
			<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?tab=5">Students</a></li><li><a href="admin.php?tab=5#tabs-2">Announcements</a></li><li class="active"><?=ucwords($action)?> Announcements</li></ol>
			<p class="text-center"><?php if(sizeof($ERRORS['MSG'])>0) echo $ERRORS['MSG'];?></p>
			<p class="text-center"><strong><?=strtoupper($action)?> ANNOUNCEMENT</strong></p>
			<p class="text-center"><span class="text-danger"><strong>FIELDS MARKED WITH ASTERISKS (*) ARE REQUIRED</strong></span></p>
			<div class="row">
				<div class="col-md-3"></div>
				<div class="col-md-6">
					<form id="validateform" enctype="multipart/form-data" action="admin.php?tab=5&subtab=announcements&action=<?=$action?>&eid=<?=$editID?>#tabs-2" method="post">										
					<div class="form-group">
						<label for="">Title:</label>
						<input class="form-control" type="text" value="<?=$Title; ?>" name="Title">
					</div>
					<div class="form-group">
						<label for="">Announcement: <span class="text-danger">*</span></label>
						<textarea class="form-control required" name="Announcement" cols="40" rows="5"><?=$Announcement; ?></textarea><br /><span class="text-danger"><?=$ERRORS['Announcement'];?></span>
					</div>					
					<div class="form-group">
					  <label for="">Publish Period: <span class="text-danger">*</span></label>
						<div class="input-group datepicker-daterange">
							<input type="text" class="form-control required" name="PublishFrom" value="<?=$PublishFrom?>" placeholder="mm/dd/YYYY">
							<div class="input-group-addon">to</div>
							<input type="text" class="form-control required" name="PublishTo" value="<?=$PublishTo?>" placeholder="mm/dd/YYYY">
						</div>
					</div>
					<div class="form-group">
					  <div class="text-center"><input class="btn btn-primary" type="submit" name="Save" value="Save">&nbsp;<input class="btn btn-default" type="button" name="cancel" value="Cancel" onclick="javascript:location.href='admin.php?tab=5#tabs-2'"></div>
					</div>
					</form>
			  </div>
				<div class="col-md-3"></div>
			</div>
			
			<?php
		break;
		default:
		//Delete selected announcements
		if(isset($_POST['DELETE']) && isset($_POST['announcementIDs']) && isSuperAdmin()){	
			foreach($_POST['announcementIDs'] as $selectedID){
				$sqlDelete = sprintf("DELETE FROM `".DB_PREFIX."announcements` WHERE `UID` = %d LIMIT %d", intval($selectedID), 1);
				//Run query
				db_query($sqlDelete,DB_NAME,$conn);
			}
			$_SESSION['MSG'] = ConfirmMessage("Selected announcements have been deleted!");
		}
		//Get announcements
		$resSql = sprintf("SELECT `UID` FROM `".DB_PREFIX."announcements` WHERE `UserType` = '%s' AND `deletedFlag` = 0", $UserType);
		//run the query
		$res = db_query($resSql,DB_NAME,$conn);	
		$ann_num_rows = db_num_rows($res);
		
		$resLimitedSql = sprintf("SELECT `UID`,`Title`,`Announcement`,`PublishFrom`,`PublishTo` FROM `".DB_PREFIX."announcements` WHERE `UserType` = '%s' AND `deletedFlag` = 0 LIMIT %d;", $UserType, 20);
		//run the query
		$result = db_query($resLimitedSql,DB_NAME,$conn);	
		?>
		<script>
		//<!--
		function checkDelAnnounce(field){
			if(document.announcements.del.checked == true){
				for(var i=0; i < field.length; i++){
					field[i].checked=true;
				}
			}
			else{
				for(var i=0; i < field.length; i++){
					field[i].checked=false;
				}
			}
		}
		//-->
		</script>
		<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?tab=5">Students</a></li><li class="active">Announcements</li></ol>
		<p>Announcements you add here will be published to all students who login to the portal. To send announcements to specific students, please use the messages tab.</p>
		<form name="announcements" method="post" action="">
		<div id="hideMsg"><?php if(isset($_SESSION['MSG'])) echo $_SESSION['MSG'];?></div>
		<a class="btn btn-primary" href="admin.php?tab=5&subtab=announcements&action=add#tabs-2">Add Announcement</a>        
		<p class="text-center">STUDENT ANNOUNCEMENTS</p>
		<table width="100%" class="display table table-striped table-bordered table-hover">				
		<thead>
		<tr>
		<th>Title</th>
		<th>Announcement</th>
		<th>Published</th>
		<th class="no-sort">Actions</th>
		<th class="no-sort" style="text-align:center"><input type="checkbox" name="del" title="Check All" onclick="checkDelAnnounce(document.getElementsByName('announcementIDs[]'));" value="" /></th>
		</tr>
        </thead>
		<?php
		//check if any rows returned
		if(db_num_rows($result)>0){
		  echo "<tbody>";
		  while($announce = db_fetch_array($result)){
			  echo "<tr>
			  <td>".$announce['Title']."</td>
			  <td>".$announce['Announcement']."</td>
			  <td>".$announce['PublishFrom']." to ".$announce['PublishTo']."</td>
			  <td><a href=\"?tab=5&subtab=announcements&action=view&eid=".$announce['UID']."#tabs-2\" title=\"View\">View</a> | <a href=\"?tab=5&subtab=announcements&action=edit&eid=".$announce['UID']."#tabs-2\" title=\"Edit\">Edit</a></td>
			  <td align=\"center\"><input type=\"checkbox\" id=\"selectedIDs\" name=\"announcementIDs[]\" value=\"".$announce['UID']."\"></td>
			  </tr>";		
		  }
		  echo "</tbody>";
		  ?>
		  <tfoot>
		  <tr>
		  <td colspan="5" align="right">
				<div class="form-inline">
					<div class="form-group">
					<label>With Selected:&nbsp;</label>
					<input type="submit" value="Delete" name="DELETE" class="btn btn-default" />
					</div>
				</div>
			</td>
		  </tr>
		  </tfoot>
		  <?php
		}
		?>
		</table>
		</form>
		<?php
		unset($_SESSION['MSG']);
		break;
	}
}
?>

<?php
function show_loginhistory(){
	global $a;
	global $conn;
	global $LoginID;	
	
	//Delete script
	if(isset($_POST['DELETE']) && isset($_POST['logsIDs']) && isSuperAdmin()){	
		foreach($_POST['logsIDs'] as $selectedID){
			$sqlDelete = sprintf("DELETE FROM `".DB_PREFIX."portal_logs` WHERE `LogID` = %d LIMIT %d", intval($selectedID), 1);
			//Run query
			db_query($sqlDelete,DB_NAME,$conn);
		}
		$UsrMSG = ConfirmMessage("Selected student login history deleted!");
	}
	
	//Display login history
	if(!empty($LoginID)){
		//Begin display script for selected student
		$sqlUsrLogins = sprintf("SELECT `PL`.`LogID` FROM `".DB_PREFIX."portal_logs` AS `PL` INNER JOIN `".DB_PREFIX."portal` AS `P` ON `PL`.`LoginID` = `P`.`LoginID` WHERE  `P`.`UserType` = 'Student' AND `PL`.`LoginID` = '%s'", $LoginID);
		$rowUsrResult = db_query($sqlUsrLogins,DB_NAME,$conn);
		$usr_num_rows = db_num_rows($rowUsrResult);
		//set sql
		$resSql = sprintf("SELECT `PL`.`LogID`, `PL`.`LoginID`, `PL`.`LoginDate`, `PL`.`Source` FROM `".DB_PREFIX."portal_logs` AS `PL` INNER JOIN `".DB_PREFIX."portal` AS `P` ON `PL`.`LoginID` = `P`.`LoginID` WHERE  `P`.`UserType` = 'Student' AND `PL`.`LoginID` = '%s' ORDER BY `LoginDate` DESC LIMIT %d;", $LoginID, 10);
	}
	else{
		//Begin normal display script
		$sqlUsrLogins = "SELECT `PL`.`LogID`, `PL`.`LoginID`, `PL`.`LoginDate`, `PL`.`Source` FROM `".DB_PREFIX."portal_logs` AS `PL` INNER JOIN `".DB_PREFIX."portal` AS `P` ON `PL`.`LoginID` = `P`.`LoginID` WHERE  `P`.`UserType` = 'Student'";
		$rowUsrResult = db_query($sqlUsrLogins,DB_NAME,$conn);
		$usr_num_rows = db_num_rows($rowUsrResult);
		//set sql
		$resSql = sprintf("SELECT `PL`.`LogID`, `PL`.`LoginID`, `PL`.`LoginDate`, `PL`.`Source` FROM `".DB_PREFIX."portal_logs` AS `PL` INNER JOIN `".DB_PREFIX."portal` AS `P` ON `PL`.`LoginID` = `P`.`LoginID` WHERE  `P`.`UserType` = 'Student' ORDER BY `LoginDate` DESC LIMIT %d;", 20);
	}		
	?>
    <script>
	//<!--
	function checkDelLogins(field){
		if(document.std_logins.del.checked == true){
			for(var i=0; i < field.length; i++){
				field[i].checked=true;
			}
		}
		else{
			for(var i=0; i < field.length; i++){
				field[i].checked=false;
			}
		}
	}
	//-->
	</script>
	<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?tab=5">Students</a></li><li class="active">Login History</li></ol>
	<form name="std_logins" method="post" action="#tabs-3">
	<div id="hideMsg"><?php if(isset($UsrMSG)) echo $UsrMSG;?></div>
	<p class="text-center">STUDENT LOGIN HISTORY</p>
    <table width="100%" class="display table table-striped table-bordered table-hover">
	<thead>
	<tr>
	<th>Student ID</th>
	<th>Login Date</th>
	<th>Source</th>
	<th class="no-sort" style="text-align:center"><input type="checkbox" name="del" title="Check All" onclick="checkDelLogins(document.getElementsByName('logsIDs[]'));" value="" /></th>
	</tr>
    </thead>    
	<?php
	//run the query
	$result = db_query($resSql,DB_NAME,$conn);
	//check if any rows returned
	if(db_num_rows($result)>0){
	  echo "<tbody>";
	  while($user_logs = db_fetch_array($result)){
		  echo "<tr>
		  <td>".$user_logs['LoginID']."</td>
		  <td>".fixdatetime($user_logs['LoginDate'])."</td>
		  <td>".$user_logs['Source']."</td>
		  <td align=\"center\"><input type=\"checkbox\" id=\"selectedIDs\" name=\"logsIDs[]\" value=\"".$user_logs['LogID']."\"></td>
		  </tr>";
	  }
	  echo "</tbody>";
	  ?>    
	  <tfoot>
	  <tr>
	  <td colspan="4" align="right">
      <div class="form-inline">
      <div class="form-group">
      <label>With Selected:&nbsp;</label>
      <input type="submit" value="Delete" name="DELETE" class="btn btn-default" />
      </div>
      </div>
      </td>
	  </tr>
	  </tfoot>
	<?php
	}
	?>
	</table>
	</form>
	<?php
}
?>

<?php
function sql_select(){
	global $conn;
	global $filter;
	global $filterfield;
	
	$filterstr = isset($filter) ? "%". $filter ."%" : "";	
	$sql = "SELECT *,CONCAT(`FName`,' ',`LName`) AS `StudentName` FROM `".DB_PREFIX."students`";	
	if(isset($filterstr) && $filterstr!='' && isset($filterfield) && $filterfield!='') {
	$sql .= " WHERE ". secure_string($filterfield) ." LIKE '". secure_string($filterstr) ."'";
	}
	$res = db_query($sql,DB_NAME,$conn);
	return $res;
}

function sql_getrecordcount(){
	global $conn;
	global $filter;
	global $filterfield;
	
	$filterstr = isset($filter) ? "%". $filter ."%" : "";	
	$sql = "SELECT COUNT(*) FROM `".DB_PREFIX."students`";
	if(isset($filterstr) && $filterstr!='' && isset($filterfield) && $filterfield!='') {
	$sql .= " WHERE ". secure_string($filterfield) ." LIKE '". secure_string($filterstr) ."'";
	}
	$res = db_query($sql,DB_NAME,$conn);
	$row = db_fetch_array($res);
	reset($row);
	return current($row);
}

function sql_insert($FIELDS){
	global $conn;
	
	//Add new student
	$sql = sprintf("INSERT INTO `".DB_PREFIX."students` (`StudentID`,`FName`,`MName`,`LName`,`Gender`,`RegDate`,`Courses`,`Email`,`DOB`,`Phone`,`Address`,`City`,`State`,`PostCode`,`Country`) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')", $FIELDS['StudentID'], $FIELDS['FName'], $FIELDS['MName'], $FIELDS['LName'], $FIELDS['Gender'], $FIELDS['RegDate'], $FIELDS['Courses'], $FIELDS['Email'], $FIELDS['dbDOB'], $FIELDS['Phone'], $FIELDS['Address'], $FIELDS['City'], $FIELDS['State'], $FIELDS['PostCode'], $FIELDS['Country']);	
	db_query($sql,DB_NAME,$conn);
	
	//Check if saved
	if(db_affected_rows($conn)>0 && !empty($FIELDS['EncryptPass'])){
		//Add password to allow access to portal
		sql_insert_password($FIELDS);
		return true;
	}else{	
		return false;
	}
}

function sql_update($FIELDS){
	global $conn;
	
	//Update student
	$sql = sprintf("UPDATE `".DB_PREFIX."students` SET `FName` = '%s',`MName` = '%s',`LName` = '%s',`Gender` = '%s',`Courses` = '%s', `Email` = '%s',`DOB` = '%s',`Phone` = '%s',`Address` = '%s',`City` = '%s',`State` = '%s',`PostCode` = '%s',`Country` = '%s' WHERE " .primarykeycondition(). "", $FIELDS['FName'], $FIELDS['MName'], $FIELDS['LName'], $FIELDS['Gender'], $FIELDS['Courses'], $FIELDS['Email'], $FIELDS['dbDOB'], $FIELDS['Phone'], $FIELDS['Address'], $FIELDS['City'], $FIELDS['State'], $FIELDS['PostCode'], $FIELDS['Country']);
	db_query($sql,DB_NAME,$conn);
	
	//Check if updated
	if(db_affected_rows($conn)>0 || !empty($FIELDS['EncryptPass'])){		
		//Add password to allow access to portal
		if(!sql_update_password($FIELDS)){	
			sql_insert_password($FIELDS);			
		}
		return true;
	}else{
		return false;
	}
}

function sql_insert_password($FIELDS){
	global $conn;
	
	//Add new student
	$sql = sprintf("INSERT INTO `".DB_PREFIX."portal` (`UserType`,`DisplayName`,`LoginID`,`Password`,`ApprovedFlag`) VALUES ('%s','%s','%s','%s',%d)", 'Student', $FIELDS['StudentName'], $FIELDS['StudentID'], $FIELDS['EncryptPass'], 1);
	db_query($sql,DB_NAME,$conn);
	
	//Check if updated
	if(db_affected_rows($conn)>0){
		return true;
	}else{
		return false;
	}
}

function sql_update_password($FIELDS){
	global $conn;
	
	//Add new student
	$sql = sprintf("UPDATE `".DB_PREFIX."portal` SET `Password` = '%s', `DisplayName` = '%s', `Token` = '%s' WHERE `LoginID` = '%s'", $FIELDS['EncryptPass'], $FIELDS['StudentName'], $FIELDS['Token'], $FIELDS['StudentID']);
	db_query($sql,DB_NAME,$conn);
	
	//Check if updated
	if(db_affected_rows($conn)>0){
		return true;
	}else{
		return false;
	}
}

function sql_update_status($disabledFlag, $editID){
	global $conn;
	
	//Update student
	$sql = sprintf("UPDATE `".DB_PREFIX."students` SET `disabledFlag` = %d WHERE `UID` = %d LIMIT 1", $disabledFlag, $editID);
	db_query($sql,DB_NAME,$conn);
	
	//Check if updated
	if(db_affected_rows($conn)>0){
		$_SESSION['MSG'] = ConfirmMessage("Student has been updated successfully.");
	}else{
		$_SESSION['MSG'] = WarnMessage("No changes made!");
	}
	redirect("admin.php?tab=5");
}

function sql_delete(){
	global $conn;
	
	$sql = "DELETE FROM `".DB_PREFIX."students` WHERE " .primarykeycondition();
	db_query($sql,DB_NAME,$conn);
	
	//Check if saved
	if(db_affected_rows($conn)>0){
		$_SESSION['MSG'] = ConfirmMessage("Student has been deleted successfully");
	}else{
		$_SESSION['MSG'] = ErrorMessage("Failed to delete selected student. Please try again later...");
	}
	redirect("admin.php?tab=5");
}

function primarykeycondition(){
	
	$pk = "";
	$pk .= "(`UID`";
	if (@$_POST["eid"] == "") {
		$pk .= " IS NULL";
	}else{
		$pk .= " = " .intval(@$_POST["eid"]);
	};
	$pk .= ")";
	return $pk;
}
?>