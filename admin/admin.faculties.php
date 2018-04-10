<?php
//Import the PHPMailer class into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
?>
<script language="javascript" type="text/javascript">
<!--
//Define page title
document.title = "<?=SYSTEM_SHORT_NAME?> | Faculties";
//-->
</script>

<div class="row">
  <div class="col-lg-12">
    <h1 class="page-header">Faculties</h1>
  </div>
  <!-- /.col-lg-12 --> 
</div>
<!-- /.row -->

<div class="row">
  <div class="col-lg-12">
    <div class="panel panel-default">
      <div class="panel-heading"> <i class="fa fa-group fa-fw"></i> Manage Faculties </div>
      <!-- /.panel-heading -->
      <div class="panel-body">

        <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" href="#tabs-1" title="Faculties"><span>Faculties</span></a></li>
          <li><a data-toggle="tab" href="#tabs-2" title="Announcements"><span>Announcements</span></a></li>
          <li><a data-toggle="tab" href="#tabs-3" title="Login History"><span>Login History</span></a></li>
        </ul>
        <div class="tab-content">
          <div id="tabs-1" class="tab-pane active">
            <!--Begin Forms-->
            <?php
            $a = isset($_GET["task"])?$_GET["task"]:"";
            $recid = intval(! empty($_GET['recid']))?$_GET['recid']:0;
            $LoginID = !empty($_GET['facultyID'])?$_GET['facultyID']:"";
            
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
            <?php manage_announcements("Faculty"); ?>
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
	
	$res = sql_select();
	$count = sql_getrecordcount();
	
	if(isset($_GET['enable']) && isset($_GET['eid'])){
		$disabledFlag = intval(! empty($_GET['enable']))?$_GET['enable']:0;
		$editID = intval(! empty($_GET['eid']))?$_GET['eid']:0;
		
		sql_update_status($disabledFlag, $editID);
	}
?>
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li class="active">Available Faculties</li></ol>

<div id="hideMsg"><?php if(isset($_SESSION['MSG'])) echo $_SESSION['MSG'];?></div>

<?php showpagenav($pagecount); ?>
<table width="100%" class="display table table-striped table-bordered table-hover">
<thead>
<tr>
<th class="rid">#</th>
<th>Faculty ID</th>
<th>Faculty Name</th>
<th>Departments</th>
<th>Work Phone</th>
<th class="no-sort">Email</th>
<th class="no-sort">Active</th>
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
<td><?=$row["FacultyID"]?></td>
<td><?=$row["Title"]." ".$row["FacultyName"]?></td>
<td><?=$row["Departments"]?></td>
<td><?=$row["WPhone"]?></td>
<td><?="<a href=\"admin.php?dispatcher=messages&amp;task=add&amp;email=".$row['Email']."\" title=\"Send Email\"><img border=\"0\" src=\"".IMAGE_FOLDER."/icons/mail.png\" height=\"22\" width=\"22\" alt=\"Send\"></a>";?></td>
<?php
if($row['disabledFlag'] == 0){
	echo "<td align=\"center\"><a href=\"admin.php?dispatcher=faculties&enable=1&eid=".$row['UID']."\" title=\"Click to disable ".$row['LName']."\"><img border=\"0\" src=\"".IMAGE_FOLDER."/icons/yes.png\" height=\"12\" width=\"12\" alt=\"Disable ".$row['LName']."\"></a></td>";
}else{
	echo "<td align=\"center\"><a href=\"admin.php?dispatcher=faculties&enable=0&eid=".$row['UID']."\" title=\"Click to enable ".$row['LName']."\"><img border=\"0\" src=\"".IMAGE_FOLDER."/icons/no.png\" height=\"12\" width=\"12\" alt=\"Enable ".$row['LName']."\"></a></td>";
}
?>
<td><a href="admin.php?dispatcher=faculties&task=view&recid=<?=$i ?>&facultyID=<?=$row['FacultyID'] ?>">Manage</a> | <a href="admin.php?dispatcher=faculties&task=edit&recid=<?=$i ?>&facultyID=<?=$row['FacultyID'] ?>">Edit</a> | <a href="admin.php?dispatcher=faculties&task=del&recid=<?=$i ?>&facultyID=<?=$row['FacultyID'] ?>">Delete</a></td>
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
<td width="30%">Faculty ID</td>
<td><?=$row["FacultyID"]; ?></td>
</tr>
<tr>
<td>Faculty Name</td>
<td><?=$row["Title"]." ".$row["FacultyName"]; ?></td>
</tr>
</table>
</div>
<?php } ?>

<?php 
function showrowdetailed($row, $recid){
	global $conn,$class_dir;
	
	$Action = !empty($_REQUEST['action'])?$_REQUEST['action']:"";
	$Action = strtolower($Action);
	$DateReg = date('Y-m-d H:i:s');
	//Actions
	if(isset($_POST['Add']) && !empty($_POST['UnitIDs'])){
		$UnitIDs = "";
		$UnitIDs = implode(",", $_POST['UnitIDs']);				
		
		foreach($_POST['UnitIDs'] as $UnitID){
			//Update unit
			$sqlUpdate = sprintf("INSERT INTO `".DB_PREFIX."units_tutors` (`FacultyID`, `UnitID`, `DateReg`, `Status`) VALUES ('%s','%s','%s',%d)", $row["FacultyID"],$UnitID,$DateReg,1);
			db_query($sqlUpdate,DB_NAME,$conn);
		}
		// Confirm update
		$_SESSION['MSG'] = ConfirmMessage("New lectures have been added!");
		redirect("?dispatcher=faculties&task=view&recid=$recid&facultyID=".$row["FacultyID"]."#sub-tabs-2");						
	}
	
	if($Action == 'drop' && !empty($_GET['unitid'])){
		$UnitID = !empty($_GET['unitid'])?secure_string($_GET['unitid']):'';
		//remove selected units
		$sqlDelete = sprintf("DELETE FROM `".DB_PREFIX."units_tutors` WHERE `FacultyID` = '%s' AND `UnitID` = '%s'", $row["FacultyID"], $UnitID);
		db_query($sqlDelete,DB_NAME,$conn);
		// Confirm remove
		$_SESSION['MSG'] = ConfirmMessage("Selected lectures have been removed!");
		redirect("?dispatcher=faculties&task=view&recid=$recid&facultyID=".$row["FacultyID"]."#sub-tabs-2");	
	}

	if(isset($_POST['Update'])){	
		if(!empty($Action) && !empty($row["FacultyID"])){
			//Action
			//echo $Action;
			switch($Action){
				case "approve":				
				if(!empty($_POST['appliedUnitIDs'])){
					//RUN DB INSERTS FOR EACH UNIT
					$there_was_update = 0;
					$approved_units = array();
					foreach($_POST['appliedUnitIDs'] as $UnitID){
						//check if this tutor has this unit assigned already
						if(checkTutorUnitAssignment($row["FacultyID"], $UnitID) != "True"){
						array_push($approved_units, $UnitID);
						db_query(sprintf("INSERT INTO `".DB_PREFIX."units_tutors`(`FacultyID`, `UnitID`, `Status`, `DateReg`) VALUES ('%s','%s',%d,'%s')", $row["FacultyID"],$UnitID,1,$DateReg), DB_NAME, $conn);
						$there_was_update++;
						}
					}
					//send email only if there was an approval of units
					if($there_was_update > 0){
						require_once("$class_dir/mpdf/autoload.php");
						//INITIATE MAILER & APPROVAL LETTER
						$mail = new PHPMailer;
						$subject = $row["FacultyID"]." - Appointment Letter";
						$bodyemail='<html><head>
						<title>'.$subject.'</title>
						</head><body><div style="background-color:#E1CDB7; color:#000; width:600px; margin:0 auto;">
						<div style="background:#C60; color:#FFF; min-width:584px; padding:8px;">
						<h2 style="font-size:15px;font-weight:700;line-height:25px;"><em>'.strtoupper(SYSTEM_NAME).' APPOINTMENT LETTER</em></h2>
						</div>
						<div style="padding:15px;">
						<h3 style="color:#333;">Dear '.$row['FName'].',</h3>
						<p style="text-align:justify;">We at <a href="'.PARENT_HOME_URL.'" target="_blank">'.SYSTEM_NAME.'</a> would like to thank you for applying to work with us as an online tutor/lecturer. Your details and qualifications were verified and you are hereby awarded a conditional offer. Please find attached letter of appointment containing details of this offer.</p>
						<p>Best Regards </p>
						<p><b>Careers Office</b><br />
						'.SYSTEM_NAME.',<br />
						'.COMPANY_ADDRESS.'<br />
						TEL: '.COMPANY_PHONE.'<br />
						EMAIL: '.INFO_EMAIL.'<br />
						WEBSITE: '.PARENT_HOME_URL.'</p>
						</div></div>
						</body></html>';
						
						$pdf_header = '<!--mpdf
						<htmlpageheader name="letterheader">
						<table width="100%" style=" font-family: sans-serif;">
						<tr>
						<td width="60%" style="color:#000000;">
						<span style="font-weight: bold; font-size: 14pt;">'.strtoupper(SYSTEM_NAME).' CAREERS</span><br />
						'.COMPANY_ADDRESS.'<br /><span style="font-size: 15pt;">?</span> '.COMPANY_PHONE.'</td>
						<td width="40%" style="text-align: right; vertical-align: top;">
						<img src="'.SYSTEM_LOGO_URL.'" style="width:160px; margin:0; padding:0; height:auto;"/><br/>
						{DATE jS F Y}</td></tr></table>
						<div style="margin-top: 0.5cm; text-align: left; font-family: sans-serif;"><span>Dear '.$row['FName'].',<br /></span></div>
						<div style="margin-top: 0.5cm; margin-bottom: 3cm; text-align: center; font-family: sans-serif;">
						<span style="font-weight: bold; font-size: 12pt; text-decoration: underline;">APPOINTMENT TO THE POSITION OF ONLINE LECTURER</span>
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
						  p{text-align:justify;}
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
						<p>After reviewing your impressive academic credentials, Finstock Evarsity Board is of the opinion that you have unique and relevant knowledge that you can impart to others through our e-learning platform. You are hereby appointed to serve as a <b>Online Lecturer</b>  and your faculty number is  <b>'.$row["FacultyID"].'</b>. The position is on a part time basis.</p>
	
						<h3>Approved Units</h3>
						<p>Your application had indicated the units you are capable of teaching and based on this selection, we reviewied and approved the following units.
						<ol>
						'.CreateCourseUnitList($approved_units).'
						</ol>
						</p>
	
						<h3>Compensation*</h3>
						<p>The compensation rate is <b>KES 1000 (USD 10)</b> per hour per unit for short courses,  international courses & HRMPEB courses and <b>KES 500 (USD 5)</b> per hour per unit for KASNEB courses and school-based programs (Diplomas and Certificates).</p>
			
						<p>Please note that this job offer guarantees to engage you for a minimum 10 hours at a rate of kes 1000  (Usd 10) or a minimum of 15 hours at a rate of  kes 500 ( usd 5) to enable you to recoup your training costs under the Certified Online Trainer course.</p>
	
						<h3>COT Requirement</h3>
						<p>This appointment is conditional on demonstrating e-training competency by completing the <a href="'.SYSTEM_URL.'/portal/?do=register&course=COT">Certified Online Trainer (COT)</a> course offered at Finstock Evarsity.</p>
	
						<h3>Acceptance/Non acceptance </h3>
						<p>Please communicate your acceptance/non acceptance of these terms in the next 3 months and ensure that  you have completed the COT course before communicating your decision.</p>
						
						<h3>Terms and Conditions </h3>
						<p>*Terms and conditions apply, we reserve the right to amend these terms provided we give you adequate notice. </p>
	
						<br><img src ="'.EMAIL_SIGNATURE_STAMP.'" style="float:right; width:160px; height:auto;"/>
						Yours truly,<br><br>
						<img src ="'.EMAIL_SIGNATURE_IMG.'" style="width:92px; height:auto;"/><br><br>
						'.EMAIL_SIGNATURE_NAME.'<br>
						'.EMAIL_SIGNATURE_TITLE.'.';
						$mpdf = new \Mpdf\Mpdf();
						$mpdf->WriteHTML($pdf_header);
						$mpdf->WriteHTML($pdf_content);
						$filename = friendlyName($row['FacultyName'])."-appointment-".date('dmYHis');
						$mpdf->Output(UPLOADS_PATH.'appointments'.DIRECTORY_SEPARATOR.$filename.'.pdf','F');
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
						$mail->setFrom(MAILER_FROM_EMAIL, MAILER_FROM_NAME." Careers");
						$mail->Subject = $subject;
						$mail->AltBody = "To view the message, please use an HTML compatible email viewer!";
						$mail->msgHTML($body);
						$mail->addAddress($row['Email'], $row['FacultyName']);
						$mail->addBCC(INFO_EMAIL, INFO_NAME);
						$mail->addAttachment(UPLOADS_PATH.'appointments'.DIRECTORY_SEPARATOR.$filename.'.pdf', '', $encoding = 'base64', $type = 'application/pdf');
						if(!$mail->Send()) {
							//failure
							$_SESSION['MSG'] = ErrorMessage("An Appointment letter could not be sent. Please try again later.");
							redirect("?dispatcher=faculties&task=view&recid=$recid&facultyID=".$row["FacultyID"]."#sub-tabs-3");	
						}
						else{
							//success
							$_SESSION['MSG'] = ConfirmMessage("You have approved ".$row['FacultyName'].". An Appointment letter has been sent successfully.");
							redirect("?dispatcher=faculties&task=view&recid=$recid&facultyID=".$row["FacultyID"]."#sub-tabs-3");
						}	
							//end mailer	
					}//end if there was update
				}
				break;
				case "remove":
				if(!empty($_POST['appliedUnitIDs'])){
					//remove selected units
					if(UpdateAppliedUnits($row["FacultyID"], $_POST['appliedUnitIDs'])){
						// Confirm remove
						$_SESSION['MSG'] = ConfirmMessage("Selected lectures have been removed!");
						redirect("?dispatcher=faculties&task=view&recid=$recid&facultyID=".$row["FacultyID"]."#sub-tabs-3");	
					}
				}
				break;
			}
		}
	}
	
?>
<div id="hideMsg"><?php if(sizeof($ERRORS['MSG'])>0) echo $ERRORS['MSG'];?></div>

<div class="head-details">
<h2 class="text-uppercase text-primary"><?=$row["Title"]." ".$row["FacultyName"]; ?> <span class="small text-muted"><?=$row["FacultyID"]; ?></span></h2>
</div>

<div id="adv-tab-container">
  <ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#sub-tabs-1" title="<?=SYSTEM_SHORT_NAME?> | Faculty Details">Details</a></li>
    <li><a data-toggle="tab" href="#sub-tabs-2" title="<?=SYSTEM_SHORT_NAME?> | Assigned Lectures">Assigned Lectures</a></li>
		<li><a data-toggle="tab" href="#sub-tabs-3" title="<?=SYSTEM_SHORT_NAME?> | Applied Lectures">Applied Lectures</a></li>
		<li><a data-toggle="tab" href="#sub-tabs-4" title="<?=SYSTEM_SHORT_NAME?> | Faculty Qualifications">Faculty Qualifications</a></li>
  </ul>
  <div class="tab-content">
<!-- ####################################################################
################# Begin FACULTY DETAILS###################sub-tabs-1
#################################################################### -->
    <div id="sub-tabs-1" class="tab-pane active">
      <p>&nbsp;</p>
      <div class="row">
        <div class="col-md-6">
          <table class="table table-bordered table-striped">
          <tr><td><strong>Faculty ID:</strong> </td><td><?=$row["FacultyID"]; ?></td></tr>
          <tr><td><strong>Portal Status:</strong> </td><td><?=$row["Status"]; ?></td></tr>
          <tr><td><strong>Email:</strong> </td><td><a href="admin.php?dispatcher=messages&task=add&email=<?=$row["Email"]; ?>" title="Send Email"><?=$row["Email"]; ?></a></td></tr>
          <tr><td><strong>Work Phone:</strong> </td><td><?=$row["WPhone"]; ?></td></tr>
          <tr><td><strong>Gender:</strong> </td><td><?=$row["Gender"]; ?></td></tr>     
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
<!-- ####################################################################
############ Begin FACULTY ASSIGNED LECTURES, ASSIGN MORE#sub-tabs-2
#################################################################### -->
    <div id="sub-tabs-2" class="tab-pane">
      <p>&nbsp;</p>
			<!-- multiselect new  --> 
			<script type="text/javascript">
			$(document).ready(function() {
				$('#UnitIDs').multiselect({
					enableCollapsibleOptGroups: true,
					enableFiltering: true,
					disableIfEmpty: true,
					buttonWidth: '100%'
				});
			});
			</script>
      <div class="modal fade" id="addLectures" tabindex="-1" role="dialog" aria-labelledby="addLecturesLabel">
        <div class="modal-dialog modal-lg" role="document">
          <form name="assign-lectures" method="post" action="admin.php?dispatcher=faculties&task=view&recid=<?=$recid?>&action=add">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title" id="addLecturesLabel">Assign new lecture</h4>
            </div>
            <div class="modal-body">
              <h2>Assign lectures to <?=$row["FacultyID"];?></h2>          
              <div class="form-group">
                <label>Hold Ctrl button on your keyboard to select multiple.</label>
                <?php echo sqlOptionGroupMulti("SELECT u.`UnitID`,u.`UName`,u.`CourseID` FROM `".DB_PREFIX."units` u WHERE NOT EXISTS ( SELECT ut.`UnitID` FROM `".DB_PREFIX."units_tutors` ut WHERE u.`UnitID` = ut.`UnitID` AND ut.`FacultyID` = 'FE/FA/101/18' AND u.`disabledFlag` = 0 AND u.`deletedFlag` = 0 ORDER BY u.`CourseID` ASC )","UnitIDs",$UnitIDs);?>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <input type="submit" class="btn btn-primary" name="Add" value="Add">
            </div>
          </div><!-- /.modal-content -->
          </form>
        </div><!-- /.modal-dialog -->
      </div><!-- /.modal -->
      			
      <div id="hideMsg"><?php if(isset($_SESSION['MSG'])) echo $_SESSION['MSG'];?></div>
      <p class="text-right"><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addLectures">Assign Lectures</button></p>
      <table width="100%" class="display table table-striped table-bordered table-hover">      
      <thead>
			<tr>
			<th width="20">#</th>
      <th>UnitID</th>
      <th>Unit Name</th>
      <th>Course</th>
      <th>Year/Semester</th>
      <th class="no-sort">Actions</th>
      </tr>
			</thead>
			<tbody>
      <?php 
      $resUnits = getFacultyUnits($row["FacultyID"]);
      if(db_num_rows($resUnits)>0){            
          $count = 1;
          while($units = db_fetch_array($resUnits)){
              echo "<tr class=\"$style\">
              <td>".$count."</td>
              <td>".$units['UnitID']."</td>
              <td>".$units['UName']."</td>
              <td>".getCourseName($units['CourseID'])."</td>
              <td>".get_year_trimesters($units['YrTrim'])."</td>
              <td><a href=\"admin.php?dispatcher=faculties&task=view&recid=$recid&action=drop&unitid={$units['UnitID']}\">Remove</a></td>
              </tr>";
              $count++;
          }
      }else{
          echo "<tr><td align=\"center\" colspan=\"6\">No units have been assigned to this lecturer</td></tr>";
      }
      ?>
			</tbody>
      </table>
    </div>
		
<!-- ####################################################################
################# Begin FACULTY APPLIED LECTURES#######sub-tabs-3
#################################################################### -->
    <div id="sub-tabs-3" class="tab-pane">
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
			<p>&nbsp;</p>
			<form name="units" method="post" action="admin.php?dispatcher=faculties&task=view&recid=<?=$recid?>&facultyID=<?=$row["FacultyID"]?>">
			<div id="hideMsg"><?php if(isset($_SESSION['MSG'])) echo $_SESSION['MSG'];?></div>
      <table width="100%" class="display table table-striped table-bordered table-hover">      
      <thead>
			<tr>
			<th width="20">#</th>
			<th>UnitID</th>
			<th>Unit Name</th>
			<th>Course</th>
			<th style="text-align:center"><input type="checkbox" name="sel" title="Check All" onclick="checkSelUnits(document.getElementsByName('appliedUnitIDs[]'));" value="" /></th>
			</tr>
			</thead>
			<?php
			$unitsAll = array();
			//get units
			if(!empty(getFacultyApplication($row["FacultyID"])['UnitsApplied'])){
				$unitsAll = explode(",", getFacultyApplication($row["FacultyID"])['UnitsApplied']);
			}			
			
			if( !empty($unitsAll )){
				echo "<tbody>";
				$unit_count = 1;
				foreach($unitsAll as $u):
					echo "<tr>
					<td>".$unit_count."</td>
					<td>".$u."</td>
					<td>".getUnitName($u)."</td>
					<td>".getUnitCourseID($u)."</td>
					<td align=\"center\"><input type=\"checkbox\" id=\"selectedIDs\" name=\"appliedUnitIDs[]\" value=\"".$u."\"></td>
					</tr>";	
					$unit_count++;
				endforeach;	
				echo "</tbody>";
			}
	  	?>	
			<tfoot>
			<tr><td align="right" colspan="5">
			<div class="form-inline">
				<div class="form-group">
					<label>With selected:</label>&nbsp;<select name="action" class="form-control">
					<option value="approve">Approve</option>
					<option value="remove">Remove</option>										
					</select>&nbsp;<input class="btn btn-default" type="submit" name="Update" value="Ok" />
				</div>
			</div>
			</td></tr>
			</tfoot>
			</table>	
			</form>		
		</div>
		
<!-- ####################################################################
################# Begin FACULTY QUALIFICATIONS###############sub-tabs-4
#################################################################### -->
    <div id="sub-tabs-4" class="tab-pane">
			<h3>Faculty Identity Documents</h3>
			<?php
			$IdentityImage = !empty($row["IdentityImage"])?'<a href="'. $row['IdentityImage'] .'" target="_blank">View/Download</a>':'N/A';
			$PassportPhoto = !empty($row["PassportPhoto"])?'<a href="'. $row['PassportPhoto'] .'" target="_blank">View/Download</a>':'N/A';
			
			echo 'Identity Document: '. $IdentityImage .'<br>';
			echo 'Passport-Size Photo: '. $PassportPhoto .'<br>';
			?>

			<h3>Faculty CV &amp; Cover Letter</h3>
			<?php
			$cv = !empty(getFacultyApplication($row["FacultyID"])['CV'])?'<a href="'. getFacultyApplication($row["FacultyID"])['CV'] .'" target="_blank">View/Download</a>':'N/A';
			$cl = !empty(getFacultyApplication($row["FacultyID"])['CoverLetter'])?'<a href="'. getFacultyApplication($row["FacultyID"])['CoverLetter'] .'" target="_blank">View/Download</a>':'N/A';
			
			echo 'Curriculum Vitae: '. $cv .'<br>';
			echo 'Cover Letter: '. $cl .'<br>';
			?>
			<?php	
			//ACADEMIC
			$getQualifications = sprintf("SELECT `Institution`,`Certificate`,`Period`,`GradeMark`,`CertFile` FROM `".DB_PREFIX."ac_qualifications` WHERE `StudentID` = '%s'", getFacultyStudentID($row["Email"]));
			//run the query
			$result = db_query($getQualifications,DB_NAME,$conn);
			//EXPERIENCE
			$workexp = sprintf("SELECT * FROM `".DB_PREFIX."faculty_work_experience` WHERE `StaffID` = '%s'", $row["FacultyID"]);
			//run the query
			$result1 = db_query($workexp,DB_NAME,$conn);
			?>
			<h3>Faculty Academic Qualifications</h3>
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
			<h3>Faculty Work Experience</h3>
			<table width="100%" class="display table table-striped table-bordered table-hover">
			<thead>
			<tr>
			<th>Employer</th>
			<th>Job title</th>
			<th>Period</th>
			<th>Job roles</th>
			</tr>
			</thead>
			<?php
			//check if any rows returned
			//print_r(getFacultyExperience("FE/FA/112/18"));
			if(db_num_rows($result1)>0){
				echo "<tbody>";
				while($row = db_fetch_array($result1)){
					//$CertFile = !empty($row["CertFile"])?'<a href="'. $row['CertFile'] .'" target="_blank">View/Download</a>':'N/A';
					echo "<tr>
					<td>".$row['PreviousEmployer']."</td>
					<td>".$row['PreviousJobTitle']."</td>
					<td>".$row['Period']."</td>
					<td>".$row['PreviousRoles']."</td>
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
      <a class="btn btn-default" href="admin.php?dispatcher=faculties&task=add"><i class="fa fa-file-o fa-fw"></i>Add Faculty</a>
      <a class="btn btn-default" href="admin.php?dispatcher=faculties&task=edit&recid=<?=$recid ?>"><i class="fa fa-pencil-square-o fa-fw"></i>Edit Faculty</a>
      <a class="btn btn-default" href="admin.php?dispatcher=faculties&task=del&recid=<?=$recid ?>"><i class="fa fa-trash-o fa-fw"></i>Delete Faculty</a>
    </div>
    
  </div>
</div>
<?php 
unset($_SESSION['MSG']);
} 
?>

<?php 
function showroweditor($row, $iseditmode, $ERRORS){
global $a; 
?>
<p class="text-center lead"><?=strtoupper($a)?> FACULTY DETAILS</p>
<p class="text-center small"><span class="text-danger">FIELDS MARKED WITH ASTERISKS (*) ARE REQUIRED</span></p>

<h2>Faculty Information</h2>

<div class="row">
  <div class="col-md-3">
    <div class="form-group">
    <label for="">Title:</label>
	<select <?=$ERRORS['Title']?> name="Title" class="form-control">
	<option value="None">--Select--</option>
	<?php
    foreach(list_title_status() as $k => $v){
        if($k == $row['Title']){
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
  <div class="col-md-3">
    <div class="form-group">
    <label for="">First Name: <span class="text-danger">*</span></label>
    <input <?=$ERRORS['FName']?> type="text" value="<?=$row['FName']; ?>" name="FName" class="form-control required">
    </div>
  </div>
  <div class="col-md-3">
    <div class="form-group">
    <label for="">Middle Name:</label>
    <input <?=$ERRORS['MName']?> type="text" value="<?=$row['MName']; ?>" name="MName" class="form-control">
    </div>
  </div>
  <div class="col-md-3">
    <div class="form-group">
    <label for="">Last Name: <span class="text-danger">*</span></label>
    <input <?=$ERRORS['LName']?> type="text" value="<?=$row['LName']; ?>" name="LName" class="form-control required">
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-4">
    <div class="form-group">
    <label for="">Email: <span class="text-danger">*</span></label>
    <input <?=$ERRORS['Email']?> type="text" value="<?=$row['Email']; ?>" name="Email" class="form-control required email">
    </div>
  </div>
  <div class="col-md-4">    
    <div class="form-group">
    <label for="">Date of Birth: </label>
    <input <?=$ERRORS['DOB']?> class="form-control datepicker" type="text" value="<?=$row['DOB']; ?>" name="DOB">
    </div>    
  </div>
  <div class="col-md-4">
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

<div class="row">
  <div class="col-md-6">    
    
    <div class="form-group">
    <label for="">Work Phone: <span class="text-danger">*</span></label>
    <input <?=$ERRORS['WPhone']?> type="tel" value="<?=$row['WPhone']; ?>" name="WPhone" class="form-control required">
    </div>
    
    <div class="form-group">
    <label for="">Mobile Phone:</label>
    <input <?=$ERRORS['MPhone']?> type="tel" value="<?=$row['MPhone']; ?>" name="MPhone" class="form-control">
    </div>
    
    <div class="form-group">
    <label for="">Office Ext:</label>
    <input <?=$ERRORS['OfficeExt']?> type="text" value="<?=$row['OfficeExt']; ?>" name="OfficeExt" class="form-control">
    </div>
    
    <div class="form-group">
    <label for="">Address:</label>
    <textarea name="Address" class="form-control" rows="4"><?=decode($row['Address'])?></textarea>
    </div>
    
    <div class="form-group">
    <label for="">City/Town: <span class="text-danger">*</span></label>
    <input <?=$ERRORS['City']?> type="text" value="<?=$row['City']; ?>" name="City" class="form-control required">
    </div>
    
    <div class="form-group">
    <label for="">State/County:</label>
    <input type="text" value="<?=$row['State']; ?>" name="State" class="form-control">
    </div>
    
    <div class="form-group">
    <label for="">Zip/Postal Code:</label>
    <input <?=$ERRORS['PostCode']?> type="text" value="<?=$row['PostCode']; ?>" name="PostCode" maxlength="5" class="form-control">
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
    <p class="small">Required to login to the faculty portal</p>

    <div class="form-group">
		<label for="">Faculty ID: <span class="text-danger">*</span></label>
		<strong><?=!empty($row['FacultyID'])?$row['FacultyID']:"New faculty ID will be generated automatically"; ?></strong>
		</div>
    
    <div class="form-group">
    <label for="">Assign Password:</label>
    <input type="password" value="<?=$row['Password']; ?>" name="Password" class="form-control"><span class="text-danger"><?=$ERRORS['Password'];?></span>
    </div>
    
    <div class="form-group">
    <label for="">Verify Password:</label>
    <input type="password" value="<?=$row['VerifyPass']; ?>" name="VerifyPass" class="form-control"><span class="text-danger"><?=$ERRORS['VerifyPass'];?></span>
    </div>
    
    <div class="form-group">
    <label for="">Departments:</label>
    <?php echo sqlOptionMulti("SELECT `DeptID`,`DName` FROM `".DB_PREFIX."departments` WHERE `disabledFlag` = 0 AND `deletedFlag` = 0","DeptIDs",$row['Departments']);?>
    </div>

  </div>
</div>

<?php } ?>

<?php
function showpagenav() {
?>
<div class="quick-nav btn-group">
<a class="btn btn-primary" href="admin.php?dispatcher=faculties&task=add">Add Faculty</a>
<a class="btn btn-default" href="admin.php?dispatcher=faculties&task=reset">Reset Filters</a>
</div>
<?php } ?>

<?php function showrecnav($a, $recid, $count) { ?>
<div class="quick-nav btn-group">
<a class="btn btn-default" href="admin.php?dispatcher=faculties"><i class="fa fa-undo fa-fw"></i> Back to Faculties</a>
<?php if ($recid > 0) { ?>
<a class="btn btn-default" href="admin.php?dispatcher=faculties&task=<?=$a ?>&recid=<?=$recid - 1 ?>"><i class="fa fa-arrow-left fa-fw"></i> Prior Record</a>
<?php } if ($recid < $count - 1) { ?>
<a class="btn btn-default" href="admin.php?dispatcher=faculties&task=<?=$a ?>&recid=<?=$recid + 1 ?>"><i class="fa fa-arrow-right fa-fw"></i> Next Record</a>
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
	<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?dispatcher=faculties">Faculties</a></li><li class="active">View Faculty</li></ol>
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
		// Faculty info		
		$FIELDS['Title'] = secure_string($_POST['Title']);
		$FIELDS['FName'] = secure_string(ucwords($_POST['FName']));
		$FIELDS['MName'] = secure_string(ucwords($_POST['MName']));
		$FIELDS['LName'] = secure_string(ucwords($_POST['LName']));
		$FIELDS['FacultyName'] = $FIELDS['FName']." ".$FIELDS['LName'];
		$FIELDS['DOB'] = secure_string($_POST['DOB']);
		$FIELDS['Gender'] = secure_string($_POST['Gender']);
		$FIELDS['Departments'] = "";
		if(!empty($_POST['DeptIDs'])){$FIELDS['Departments'] = implode(",", $_POST['DeptIDs']);}
		$FIELDS['Email'] = secure_string($_POST['Email']);
		$FIELDS['WPhone'] = secure_string($_POST['WPhone']);
		$FIELDS['MPhone'] = secure_string($_POST['MPhone']);
		$FIELDS['OfficeExt'] = secure_string($_POST['OfficeExt']);
		$FIELDS['Address'] = secure_string($_POST['Address']);			
		$FIELDS['City'] = secure_string($_POST['City']);
		$FIELDS['State'] = secure_string($_POST['State']);
		$FIELDS['PostCode'] = secure_string($_POST['PostCode']);
		$FIELDS['Country'] = secure_string($_POST['Country']);
		$FIELDS['FacultyID'] = secure_string(whitespace_trim(strtoupper($_POST['FacultyID'])));
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
		// validate "WPhone" field
		if(!$check->is_phone($FIELDS['WPhone']))
		$ERRORS['WPhone'] = $ERR;
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
			//GENERATE NEW FACULTY ID
			//GET CURRENT DB MAX ID
			$maxidsql = "SELECT MAX(`UID`) AS 'UID' FROM `".DB_PREFIX."faculties`";
			
			$maxidres = db_query($maxidsql,DB_NAME,$conn);	
			$maxidrow = db_fetch_array($maxidres);
			if ($maxidrow["UID"] == 0) {
				$MUID = 100;
			} else {
				$MUID = $maxidrow["UID"]+1;
			}
			
			//Format: FE/FID/2018
			$FIELDS['FacultyID'] = "FE" ."/". $MUID ."/". date('Y');
			
			if(sql_insert($FIELDS)){
				//Display Confirmation Message
				$_SESSION['MSG'] = ConfirmMessage("New faculty has been added successfully");
				redirect("admin.php?dispatcher=faculties");
			}else{
				//Display Error Message
				$ERRORS['MSG'] = ErrorMessage("Failed to create new faculty. Check to confirm if all fields are well populated and try again.");
			}
		}
	}
	
	$row["Title"] = !empty($FIELDS['Title'])?$FIELDS['Title']:"";
	$row["FName"] = !empty($FIELDS['FName'])?$FIELDS['FName']:"";
	$row["MName"] = !empty($FIELDS['MName'])?$FIELDS['MName']:"";
	$row["LName"] = !empty($FIELDS['LName'])?$FIELDS['LName']:"";
	$row["DOB"] = !empty($FIELDS['DOB'])?$FIELDS['DOB']:"";
	$row["Gender"] = !empty($FIELDS['Gender'])?$FIELDS['Gender']:"";
	$row["Departments"] = !empty($FIELDS['Departments'])?$FIELDS['Departments']:"";
	$row["Email"] = !empty($FIELDS['Email'])?$FIELDS['Email']:"";
	$row["WPhone"] = !empty($FIELDS['WPhone'])?$FIELDS['WPhone']:"";
	$row["MPhone"] = !empty($FIELDS['MPhone'])?$FIELDS['MPhone']:"";
	$row["OfficeExt"] = !empty($FIELDS['OfficeExt'])?$FIELDS['OfficeExt']:"";
	$row["Address"] = !empty($FIELDS['Address'])?$FIELDS['Address']:"";
	$row["City"] = !empty($FIELDS['City'])?$FIELDS['City']:"";
	$row["State"] = !empty($FIELDS['State'])?$FIELDS['State']:"";
	$row["PostCode"] = !empty($FIELDS['PostCode'])?$FIELDS['PostCode']:"";
	$row["Country"] = !empty($FIELDS['Country'])?$FIELDS['Country']:"KE";
	$row["FacultyID"] = !empty($FIELDS['FacultyID'])?$FIELDS['FacultyID']:"";
?>
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?dispatcher=faculties">Faculties</a></li><li class="active">Add Faculty</li></ol>

<a id="back" href="admin.php?dispatcher=faculties"><i class="fa fa-undo fa-fw"></i> Back to Faculties</a>

<p class="text-center"><?php if(sizeof($ERRORS['MSG'])>0) echo $ERRORS['MSG'];?></p>
<form id="validateform" enctype="multipart/form-data" action="admin.php?dispatcher=faculties&task=add" method="post">
<input type="hidden" name="sql" value="insert">
<?php
showroweditor($row, false, $ERRORS);
?>
<p class="text-center">
<input class="btn btn-primary" type="submit" name="Add" value="Save">
<input class="btn btn-default" type="button" name="cancel" value="Cancel" onclick="javascript:location.href='admin.php?dispatcher=faculties'">
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
		// Faculty info	
		$FIELDS['Title'] = secure_string($_POST['Title']);
		$FIELDS['FName'] = secure_string(ucwords($_POST['FName']));
		$FIELDS['MName'] = secure_string(ucwords($_POST['MName']));
		$FIELDS['LName'] = secure_string(ucwords($_POST['LName']));
		$FIELDS['FacultyName'] = $FIELDS['FName']." ".$FIELDS['LName'];
		$FIELDS['DOB'] = secure_string($_POST['DOB']);
		$FIELDS['Gender'] = secure_string($_POST['Gender']);
		$FIELDS['Departments'] = "";
		if(!empty($_POST['DeptIDs'])){$FIELDS['Departments'] = implode(",", $_POST['DeptIDs']);}
		$FIELDS['Email'] = secure_string(strtolower($_POST['Email']));
		$FIELDS['WPhone'] = secure_string($_POST['WPhone']);
		$FIELDS['MPhone'] = secure_string($_POST['MPhone']);
		$FIELDS['OfficeExt'] = secure_string($_POST['OfficeExt']);
		$FIELDS['Address'] = secure_string($_POST['Address']);			
		$FIELDS['City'] = secure_string($_POST['City']);
		$FIELDS['State'] = secure_string($_POST['State']);
		$FIELDS['PostCode'] = secure_string($_POST['PostCode']);
		$FIELDS['Country'] = secure_string($_POST['Country']);
		$FIELDS['FacultyID'] = secure_string(whitespace_trim(strtoupper($_POST['FacultyID'])));
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
				$FIELDS['dbDOB'] = db_fixdate($FIELDS['DOB']);// YYYY-dd-mm
			}else{
				$ERRORS['DOB'] = $ERR;
			}
		}
		// validate "WPhone" field
		if(!$check->is_phone($FIELDS['WPhone']))
		$ERRORS['WPhone'] = $ERR;
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
			if(sql_update($FIELDS)){
				$mail = new PHPMailer;
				//Send a message to user
				$Subject = $FIELDS['FacultyID']." - Account Updated";
				$bodyemail = '<html><head>
				<title>'.$Subject.'</title>
				</head><body><div style="background-color:#E1CDB7; color:#000; width:600px; margin:0 auto;">
				<div style="background:#C60; color:#FFF; min-width:584px; padding:8px;">
				<h2 style="font-size:15px;font-weight:700;line-height:25px;"><em>'.strtoupper(SYSTEM_NAME).' ACCOUNT UPDATE</em></h2>
				</div>
				<div style="padding:15px;">
				<h3 style="color:#333;">Dear '.$FIELDS['FacultyName'].',</h3>
				<p style="text-align:justify;">We at <a href="'.PARENT_HOME_URL.'" target="_blank">'.SYSTEM_NAME.'</a> would like to thank you for applying to teach with us. Your account has been updated and you should now be able to access your lecturer portal.</p>
				<p style="text-align:justify;">Below are your portal access details:<br />
				Name: '.$FIELDS['FacultyName'].'<br />
				Portal URL: <a href="'.SYSTEM_URL.'" target="_blank">'.SYSTEM_URL.'</a><br />
				Faculty Number: '.$FIELDS['FacultyID'].'<br />
				Username: '.$FIELDS['FacultyID'].'<br />';
				if(empty($FIELDS['VerifyPass'])){
					$bodyemail .= 'Password: (Password not changed)';
				}else{
					$bodyemail .= 'Password: <a href=\"'.SYSTEM_URL.'/portal/?do=activate&token='.$FIELDS['Token'].'\" target=\"_blank\"><strong>Click here to set your account password</strong></a>';
				}
				$bodyemail .= '</p>
				<h3>What next?</h3>
				<p style="text-align:justify;">Use the link above to login to the portal and get started.</p>
				<p>Careers Office,<br />
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
				$mail->addAddress($FIELDS['Email'], $FIELDS['FacultyName']);
				if(!$mail->Send()) {
					//Display Confirmation Message
					$_SESSION['MSG'] = ConfirmMessage("Faculty details have been updated successfully");
					redirect("admin.php?dispatcher=faculties");
				}else{
					//Display Confirmation Message
				//Display Confirmation Message
				$_SESSION['MSG'] = ConfirmMessage("Faculty has been updated emailed successfully.");
				redirect("admin.php?dispatcher=faculties");
				}
				
			}else{
				//Display Error Message
				$ERRORS['MSG'] = WarnMessage("No changes made. Check to confirm if all fields are well populated and try again.");
			}		
		}
  	}
	
	$res = sql_select();
	$count = sql_getrecordcount();
	db_data_seek($res, $recid);
	$row = db_fetch_array($res);
		
	$row["Title"] = !empty($FIELDS['Title'])?$FIELDS['Title']:$row['Title'];
	$row["FName"] = !empty($FIELDS['FName'])?$FIELDS['FName']:$row['FName'];
	$row["MName"] = !empty($FIELDS['MName'])?$FIELDS['MName']:$row['MName'];
	$row["LName"] = !empty($FIELDS['LName'])?$FIELDS['LName']:$row['LName'];	
	$row["FacultyName"] = !empty($FIELDS['FacultyName'])?$FIELDS['FacultyName']:$row['FacultyName'];
	$row["DOB"] = !empty($FIELDS['DOB'])?$FIELDS['DOB']:fixdatepicker($row['DOB']);
	$row["Gender"] = !empty($FIELDS['Gender'])?$FIELDS['Gender']:$row['Gender'];
	$row["Departments"] = !empty($FIELDS['Departments'])?$FIELDS['Departments']:$row['Departments'];
	$row["Email"] = !empty($FIELDS['Email'])?$FIELDS['Email']:$row['Email'];
	$row["WPhone"] = !empty($FIELDS['WPhone'])?$FIELDS['WPhone']:$row['WPhone'];
	$row["MPhone"] = !empty($FIELDS['MPhone'])?$FIELDS['MPhone']:$row['MPhone'];
	$row["OfficeExt"] = !empty($FIELDS['OfficeExt'])?$FIELDS['OfficeExt']:$row['OfficeExt'];	
	$row["Address"] = !empty($FIELDS['Address'])?$FIELDS['Address']:$row['Address'];
	$row["City"] = !empty($FIELDS['City'])?$FIELDS['City']:$row['City'];
	$row["State"] = !empty($FIELDS['State'])?$FIELDS['State']:$row['State'];
	$row["PostCode"] = !empty($FIELDS['PostCode'])?$FIELDS['PostCode']:$row['PostCode'];
	$row["Country"] = !empty($FIELDS['Country'])?$FIELDS['Country']:$row['Country'];
	$row["FacultyID"] = !empty($FIELDS['FacultyID'])?$FIELDS['FacultyID']:$row['FacultyID'];
?>
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?dispatcher=faculties">Faculties</a></li><li class="active">Edit Faculty</li></ol>
<?php showrecnav("edit", $recid, $count); ?>
<form id="validateform" enctype="multipart/form-data" action="admin.php?dispatcher=faculties&task=edit&recid=<?=$recid?>" method="post">
<p class="text-center"><?php if(sizeof($ERRORS['MSG'])>0) echo $ERRORS['MSG'];?></p>
<input type="hidden" name="sql" value="update">
<input type="hidden" name="eid" value="<?=$row["UID"] ?>">
<input type="hidden" name="FacultyID" value="<?=$row["FacultyID"] ?>" />
<?php showroweditor($row, true, $ERRORS); ?>
<p class="text-center">
<input class="btn btn-primary" type="submit" name="Edit" value="Save">
<input class="btn btn-default" type="button" name="cancel" value="Cancel" onclick="javascript:location.href='admin.php?dispatcher=faculties'">
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
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?dispatcher=faculties">Faculties</a></li><li class="active">Delete Faculty</li></ol>
<?php showrecnav("del", $recid, $count); ?>
<form action="admin.php?dispatcher=faculties&task=del&recid=<?=$recid?>" method="post">
<input type="hidden" name="sql" value="delete">
<input type="hidden" name="eid" value="<?=$row["UID"] ?>">
<?php showrow($row, $recid) ?>
<strong>Are you sure you want to delete this record? </strong><div class="btn-group"><input class="btn btn-primary" type="submit" name="Delete" value="Yes"> <input class="btn btn-default" type="button" name="Ignore" value="No" onclick="javascript:history.go(-1)"></div>
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
							redirect("admin.php?dispatcher=faculties#tabs-2");
						}else{
							$ERRORS['MSG'] = ErrorMessage("Failed to add new announcement. Please try again later.");
						}
					}else{
						//Update announcement
						$sqlUpdate = sprintf("UPDATE `".DB_PREFIX."announcements` SET `Title`='%s', `Announcement`='%s', `PublishFrom`='%s', `PublishTo`='%s' WHERE `UID` = '%s'", $Title, $Announcement, $dbPublishFrom, $dbPublishTo, $editID);
						db_query($sqlUpdate,DB_NAME,$conn);
						//Check if updated
						if(db_affected_rows($conn)>0){
							$_SESSION['MSG'] = ConfirmMessage("Announcement updated successfully");
							//Redirect
							redirect("admin.php?dispatcher=faculties#tabs-2");
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
            <ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?dispatcher=faculties">Faculties</a></li><li><a href="admin.php?dispatcher=faculties#tabs-2">Announcements</a></li><li class="active"><?=ucwords($action)?> Announcements</li></ol>
            <p class="text-center"><?php if(sizeof($ERRORS['MSG'])>0) echo $ERRORS['MSG'];?></p>
            <form id="validateform" enctype="multipart/form-data" action="admin.php?dispatcher=faculties&subtab=announcements&action=<?=$action?>&eid=<?=$editID?>#tabs-2" method="post">
            <table align="center" border="0" cellpadding="1" cellspacing="1">
            <tr><td style="text-align:center" colspan="2"><strong><?=strtoupper($action)?> ANNOUNCEMENT</strong></td></tr>
            <tr><td style="text-align:center" colspan="2"><span class="text-danger"><strong>FIELDS MARKED WITH ASTERISKS (*) ARE REQUIRED</strong></span></td></tr>
            <tr>
            <td align="right">Title:</td>
            <td><input type="text" value="<?=$Title; ?>" name="Title"></td>
            </tr>
            <tr>
            <td align="right" valign="top">Announcement: <span class="text-danger">*</span></td>
            <td><textarea name="Announcement" cols="40" rows="5" class="required"><?=$Announcement; ?></textarea><br><span class="text-danger"><?=$ERRORS['Announcement'];?></span></td>
            </tr>
            <tr>
            <td align="right">Publish From: <span class="text-danger">*</span></td>
            <td><input class="datepickerfrom required" type="text" value="<?=$PublishFrom; ?>" name="PublishFrom"><span class="text-danger"><?=$ERRORS['PublishFrom'];?></span></td>
            </tr>
            <tr>
            <td align="right">Publish To: <span class="text-danger">*</span></td>
            <td><input class="datepickerto required" type="text" value="<?=$PublishTo; ?>" name="PublishTo"><span class="text-danger"><?=$ERRORS['PublishTo'];?></span></td>
            </tr>
            <tr>
            <td style="text-align:center" colspan="2"><input type="submit" name="Save" value="Save"> <input class="btn btn-default" type="button" name="cancel" value="Cancel" onclick="javascript:location.href='admin.php?dispatcher=faculties#tabs-2'"></td>
            </tr>
            </table>
            </form>
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
		<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?dispatcher=faculties">Faculties</a></li><li class="active">Announcements</li></ol>
    	<p>Announcements you add here will be published to all faculties who login to the portal. To send announcements to specific faculties, please use the messages tab.</p>
		<form name="announcements" method="post" action="#tabs-2">
        <div id="hideMsg"><?php if(isset($_SESSION['MSG'])) echo $_SESSION['MSG'];?></div>
        <a class="btn btn-primary" href="admin.php?dispatcher=faculties&subtab=announcements&action=add#tabs-2">Add Announcement</a>
        <p class="text-center lead">FACULTY ANNOUNCEMENTS</p>
		<table width="100%" class="display table table-striped table-bordered table-hover">
		<thead>
        <tr>
		<th>Title</th>
		<th>Announcement</th>
		<th>Published</th>
		<th class="no-sort">Actions</th>
		<th class="no-sort" style="text-align:center"><input type="checkbox" name="del" title="Check All" onclick="checkDelAnnounce(document.getElementsByName('announcementIDs[]'));" value=""></th>
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
			  <td><a href=\"?dispatcher=faculties&subtab=announcements&action=view&eid=".$announce['UID']."#tabs-2\" title=\"View\">View</a> | <a href=\"?dispatcher=faculties&subtab=announcements&action=edit&eid=".$announce['UID']."#tabs-2\" title=\"View\">Edit</a></td>
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
          <input type="submit" value="Delete" name="DELETE" class="btn btn-default">
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
		$UsrMSG = ConfirmMessage("Selected faculty login history deleted!");
	}
	
	//Display login history
	if(!empty($LoginID)){
		//Begin display script for selected faculty
		$sqlUsrLogins = sprintf("SELECT `PL`.`LogID` FROM `".DB_PREFIX."portal_logs` AS `PL` INNER JOIN `".DB_PREFIX."portal` AS `P` ON `PL`.`LoginID` = `P`.`LoginID` WHERE  `P`.`UserType` = 'Faculty' AND `PL`.`LoginID` = '%s'", $LoginID);
		$rowUsrResult = db_query($sqlUsrLogins,DB_NAME,$conn);
		$usr_num_rows = db_num_rows($rowUsrResult);
		//set sql
		$resSql = sprintf("SELECT `PL`.`LogID`, `PL`.`LoginID`, `PL`.`LoginDate`, `PL`.`Source` FROM `".DB_PREFIX."portal_logs` AS `PL` INNER JOIN `".DB_PREFIX."portal` AS `P` ON `PL`.`LoginID` = `P`.`LoginID` WHERE  `P`.`UserType` = 'Faculty' AND `PL`.`LoginID` = '%s' ORDER BY `LoginDate` DESC LIMIT %d;", $LoginID, 10);
	}
	else{
		//Begin normal display script
		$sqlUsrLogins = "SELECT `PL`.`LogID`, `PL`.`LoginID`, `PL`.`LoginDate`, `PL`.`Source` FROM `".DB_PREFIX."portal_logs` AS `PL` INNER JOIN `".DB_PREFIX."portal` AS `P` ON `PL`.`LoginID` = `P`.`LoginID` WHERE  `P`.`UserType` = 'Faculty'";
		$rowUsrResult = db_query($sqlUsrLogins,DB_NAME,$conn);
		$usr_num_rows = db_num_rows($rowUsrResult);
		//set sql
		$resSql = sprintf("SELECT `PL`.`LogID`, `PL`.`LoginID`, `PL`.`LoginDate`, `PL`.`Source` FROM `".DB_PREFIX."portal_logs` AS `PL` INNER JOIN `".DB_PREFIX."portal` AS `P` ON `PL`.`LoginID` = `P`.`LoginID` WHERE  `P`.`UserType` = 'Faculty' ORDER BY `LoginDate` DESC LIMIT %d;", 20);
	}		
	?>
    <script>
	//<!--
	function checkDelLogins(field){
		if(document.fac_logins.del.checked == true){
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
	<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?dispatcher=faculties">Faculties</a></li><li class="active">Login History</li></ol>
	<form name="fac_logins" method="post" action="#tabs-3">
    <div id="hideMsg"><?php if(isset($UsrMSG)) echo $UsrMSG;?></div>
    <p class="text-center lead">FACULTY LOGIN HISTORY</p>
	<table width="100%" class="display table table-striped table-bordered table-hover">
	<thead>
    <tr>
	<th>Faculty ID</th>
	<th>Login Date</th>
	<th>Source</th>
	<th class="no-sort" style="text-align:center"><input type="checkbox" name="del" title="Check All" onclick="checkDelLogins(document.getElementsByName('logsIDs[]'));" value=""></th>
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
      <input type="submit" value="Delete" name="DELETE" class="btn btn-default">
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
	
	$sql = "SELECT *, CONCAT(`FName`,' ',`LName`) AS `FacultyName` FROM `".DB_PREFIX."faculties`";	
	$res = db_query($sql,DB_NAME,$conn);
	return $res;
}

function sql_getrecordcount(){
	global $conn;
	
	$sql = "SELECT COUNT(*) FROM `".DB_PREFIX."faculties`";
	$res = db_query($sql,DB_NAME,$conn);
	$row = db_fetch_array($res);
	reset($row);
	return current($row);
}

function sql_insert($FIELDS){
	global $conn;
	
	//Add new faculty
	$sql = sprintf("INSERT INTO `".DB_PREFIX."faculties` (`FacultyID`,`Title`,`FName`,`MName`,`LName`,`Gender`,`DOB`,`Departments`,`Email`,`WPhone`,`MPhone`,`OfficeExt`,`Address`,`City`,`State`,`PostCode`,`Country`,`RegDate`) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')", $FIELDS['FacultyID'], $FIELDS['Title'], $FIELDS['FName'], $FIELDS['MName'], $FIELDS['LName'], $FIELDS['Gender'], $FIELDS['dbDOB'], $FIELDS['Departments'], $FIELDS['Email'], $FIELDS['WPhone'], $FIELDS['MPhone'], $FIELDS['OfficeExt'], $FIELDS['Address'], $FIELDS['City'], $FIELDS['State'], $FIELDS['PostCode'], $FIELDS['Country'], $FIELDS['RegDate']);	
	db_query($sql,DB_NAME,$conn);
	//Check if saved
	if(db_affected_rows($conn)>0){
		//Add password to allow access to portal
		if(!empty($FIELDS['VerifyPass'])){
			sql_insert_password($FIELDS);
		}
		return true;
	}else{
		return false;
	}	
}

function sql_update($FIELDS){
	global $conn;
	
	//Update faculty
	$sql = sprintf("UPDATE `".DB_PREFIX."faculties` SET `FacultyID` = '%s',`Title` = '%s',`FName` = '%s',`MName` = '%s',`LName` = '%s',`Gender` = '%s',`DOB` = '%s',`Departments` = '%s',`Email` = '%s',`WPhone` = '%s',`MPhone` = '%s',`OfficeExt` = '%s',`Address` = '%s',`City` = '%s',`State` = '%s',`PostCode` = '%s',`Country` = '%s' WHERE " .primarykeycondition(). "", $FIELDS['FacultyID'], $FIELDS['Title'], $FIELDS['FName'], $FIELDS['MName'], $FIELDS['LName'], $FIELDS['Gender'], $FIELDS['dbDOB'], $FIELDS['Departments'], $FIELDS['Email'], $FIELDS['WPhone'], $FIELDS['MPhone'], $FIELDS['OfficeExt'], $FIELDS['Address'], $FIELDS['City'], $FIELDS['State'], $FIELDS['PostCode'], $FIELDS['Country']);
	db_query($sql,DB_NAME,$conn);
	
	//Add password to allow access to portal
	if(!empty($FIELDS['VerifyPass'])){
		if(!sql_update_password($FIELDS)){	
			sql_insert_password($FIELDS);			
		}
	}
	//Check if updated
	if(db_affected_rows($conn)>0){		
		return true;
	}else{
		return false;
	}
		
}

function sql_insert_password($FIELDS){
	global $conn;
	
	//Add new faculty
	$sql = sprintf("INSERT INTO `".DB_PREFIX."portal` (`UserType`,`DisplayName`,`LoginID`,`Password`,`ApprovedFlag`) VALUES ('%s','%s','%s','%s',%d)", 'Faculty', $FIELDS['FacultyName'], $FIELDS['FacultyID'], $FIELDS['EncryptPass'], 1);
	
	//Check if inserted
	if(db_query($sql,DB_NAME,$conn)){
		return true;
	}else{
		return false;
	}
}

function sql_update_password($FIELDS){
	global $conn;
	
	//Add new faculty
	$sql = sprintf("UPDATE `".DB_PREFIX."portal` SET `Password` = '%s', `DisplayName` = '%s', `Token` = '%s' WHERE `LoginID` = '%s'", $FIELDS['EncryptPass'], $FIELDS['FacultyName'], $FIELDS['Token'], $FIELDS['FacultyID']);
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
	
	//Update faculty
	$sql = sprintf("UPDATE `".DB_PREFIX."faculties` SET `disabledFlag` = %d WHERE `UID` = %d LIMIT 1", $disabledFlag, $editID);	
	db_query($sql,DB_NAME,$conn);
	
	//Check if updated
	if(db_affected_rows($conn)>0){
		$_SESSION['MSG'] = ConfirmMessage("Faculty has been updated successfully.");
	}else{
		$_SESSION['MSG'] = WarnMessage("No changes made!");
	}
	redirect("admin.php?dispatcher=faculties");
}

function sql_delete(){
	global $conn;
	
	$sql = "DELETE FROM `".DB_PREFIX."faculties` WHERE " .primarykeycondition();
	db_query($sql,DB_NAME,$conn);
	
	//Check if deleted
	if(db_affected_rows($conn)>0){
		$_SESSION['MSG'] = ConfirmMessage("Faculty has been deleted successfully");
	}else{
		$_SESSION['MSG'] = ErrorMessage("Failed to delete selected faculty. Please try again later...");
	}
	redirect("admin.php?dispatcher=faculties");
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