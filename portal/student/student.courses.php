<?php
//Import the PHPMailer class into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
$io = new IORating($student['StudentID'], $UnitID, $row['Title'], "modal", DB_NAME, $conn);
?>
<script>
<!--//
//Define page title
document.title = "<?=SYSTEM_SHORT_NAME?> - Student | My Courses";
//-->
</script>

<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">My Courses</h1>
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->

<div class="row">
	<div class="col-lg-12">
		<div class="cms-contents-grey">
			<!--Begin Forms-->
			<?php
      //Require Course ID
      $CourseID = !empty($_GET['CourseID'])?$_GET['CourseID']:$_SESSION['CourseID'];
      $Course = getCourseDetails($CourseID);
      //Get requested task
      $task = isset($_GET['task'])?$_GET['task']:"";
      
      $task = strtolower($task);
      switch($task) {
				case "view":
					if(!empty($CourseID)){
						echo "<h2>Course Details</h2>";
						echo '<h3 class="text-uppercase text-primary">'. $Course['CName'] .' <span class="small text-muted">'. $CourseID .'</span></h3>';
						?>
						<div class="CourseDetails">
							<h3>Course Fees</h3>
							<p><?=getCourseFeesStructure($CourseID, $student['StudyMode']);?></p>
							<h3>Course Outline</h3>
							<p><?=decode($Course['Outline']);?></p>
							<h3>Course Description</h3>
							<p><?=decode($Course['Description']);?></p>
							<p><a class="btn btn-default" href="?dispatcher=courses&task=courseunits&CourseID=<?=$CourseID;?>">View Course Units</a></p>
						</div>
						<?php
						$resGetPendingUnits = getStudentUnitsByStatus($student['StudentID'], $CourseID, "Pending");				
						//check if any rows returned
						if(db_num_rows($resGetPendingUnits)>0){						
							?>
							<h3>Wait Listed Units</h3>
							<table width="100%" class="display table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<td>UnitID</td>
										<td>Unit Name</td>
									</tr>
								</thead>
								<tbody>
									<?php
									while($rowPending = db_fetch_array($resGetPendingUnits)){						
										echo "<tr>
										<td>".$rowPending['UnitID']."</td>
										<td><a href=\"?dispatcher=courses&task=unitdetails&CourseID=".$CourseID."&UnitID=".$rowPending['UnitID']."\" title=\"View unit details\">".$rowPending['UName']."</a></td>
										</tr>";
									}
									?>
								</tbody>
							</table>
							<?php
						}
						
						$resGetRegisteredUnits = getStudentUnitsByStatus($student['StudentID'], $CourseID, "Registered");				
						//check if any rows returned
						if(db_num_rows($resGetRegisteredUnits)>0){
							?>
							<h3>Registered Units</h3>
							<table width="100%" class="display table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<td>UnitID</td>
										<td>Unit Name</td>
										<td>Days</td>
										<td>Time</td>
										<td>Bldg/Room</td>
									</tr>
								</thead>
								<tbody>
									<?php
										while($rowRegistered = db_fetch_array($resGetRegisteredUnits)){					
											echo "<tr>
											<td>".$rowRegistered['UnitID']."</td>
											<td><a href=\"?dispatcher=courses&task=unitdetails&CourseID=".$CourseID."&UnitID=".$rowRegistered['UnitID']."\" title=\"View unit details\">".$rowRegistered['UName']."</a></td>
											<td>".$rowRegistered['Days']."</td>
											<td>".$rowRegistered['StartTime']." &mdash; ".$rowRegistered['EndTime']."</td>
											<td>".$rowRegistered['RoomID']."</td>
											</tr>";
										}
										?>
								</tbody>
							</table>
							<?php
						}else{
							echo '<p>You have not registered for any units.<br><a class="btn btn-sm btn-primary" href="?dispatcher=courses&task=register&CourseID='.$CourseID.'">Register Now</a></p>';
						}
					}
				break;		  
				case "unitdetails":
					$UnitID = !empty($_GET['UnitID'])?$_GET['UnitID']:"";
					if(!empty($UnitID)){
						$Unit = getUnitDetails($UnitID);
						$DateUnitRegistered = getUnitRegistrationDate($student['StudentID'], $UnitID);
						$CurrentAcademicPeriod = getCurrentAcademicPeriod( db_fixdate($DateUnitRegistered) );
						$AcademicID = $CurrentAcademicPeriod['UID'];
						$UnitEnrollment = getCurrentAcademicUnitEnrollment($AcademicID, $UnitID, 'Registered');
						echo "<h2>Unit Details</h2>";
						echo '<h3 class="text-uppercase text-primary">'. $Unit['UName'] .' <span class="small text-muted">'. $UnitID .'</span></h3>';
						?>
						<div class="UnitDetails">
							<h3>Meeting Details</h3>
							<p><strong>Dates: </strong><?=fixdatelong($Unit['StartDate'])?> &mdash; <?=fixdatelong($Unit['EndDate'])?><br>
								<strong>Days: </strong><?=$Unit['Days']?><br>
								<strong>Time: </strong><?=$Unit['StartTime']?> &mdash; <?=$Unit['EndTime']?><br>
								<strong>Building/Room: </strong><?=$Unit['RoomID']?><br>
								<strong>Instructors: </strong><?=getUnitTutors($UnitID)?>
							</p>
							<h3>Seat Availability</h3>
							<p><strong>Size: </strong><?=$Unit['ClassLimit']?><br>
								<strong>Enrolled: </strong><?=$UnitEnrollment?> <strong>Seats Remaining: </strong><?=$Unit['ClassLimit']-$UnitEnrollment?><br>
								<strong>Academic: </strong><?=$CurrentAcademicPeriod['AcName']?><br>
								<strong>Term: </strong><?=$CurrentAcademicPeriod['AcTerm']?><br>
								<strong>Term Period: </strong><?=fixdatelong($CurrentAcademicPeriod['DateStart'])?> &mdash; <?=fixdatelong($CurrentAcademicPeriod['DateEnd'])?>
							</p>
							<h3>Tuition Fee (Approximate)</h3>
							<p><?=$Unit['TuitionFee']?></p>
							<h3>Course Level</h3>
							<p><?=getCourseLevel($Unit['CourseID'])?></p>
							<h3>Description</h3>
							<p><?=$Unit['Description']?></p>
						</div>
					<?php
					}else{
						echo "<p>Please select a unit to view its details.";
					}
				break;
				case "unitlessons";
					$UnitID = !empty($_GET['UnitID'])?$_GET['UnitID']:"";
					if(!empty($UnitID)){
						$Unit = getUnitDetails($UnitID);
						echo "<h2>Unit Lessons</h2>";
						echo '<h3 class="text-uppercase text-primary">'. $Unit['UName'] .' <span class="small text-muted">'. $UnitID .'</span></h3>';
						
						$sqlGetLessons = sprintf("SELECT * FROM `".DB_PREFIX."lessons` WHERE `UnitID` = '%s' AND `disabledFlag` = %d AND `deletedFlag` = %d", $UnitID, 0, 0);
						//Execute the query or die if there is a problem
						$resultGetLessons = db_query($sqlGetLessons,DB_NAME,$conn);
						
						//check if any rows returned
						if(db_num_rows($resultGetLessons)>0){
							$count = 1;
							echo '<div class="panel-group" id="accordion">';
							while($row = db_fetch_array($resultGetLessons)){
								?>							
									<div class="panel panel-default">
										<div class="panel-heading">
											<h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $count; ?>"><?php echo $row['Title']; ?></a>
											<div class="pull-right">
											<?php echo showLessonRating($row['LID']);?>
											</div>
											</h4>
										</div>
										<div id="collapse<?php echo $count; ?>" class="panel-collapse collapse">
											<div class="panel-body">
											<h3>About this lesson</h3>
											<?php echo $row['Description']; ?>
											<h3>Lesson Content</h3>
											<?php echo decode($row['UploadContent']); ?>
											<p style="padding:20px;">
											<?php
											rate($student['StudentID'], $row['LID']);
											if( isRated($row['LID'], $student['StudentID']) == 1 ){
												echo $io ->LaunchModal("Rate lesson");
											}else{
												echo $io ->Rated("You already rated this lesson");
											}
												?>
											</p>
											</div>
										</div>
									</div>															
								<?php
								++$count;
							}

							echo '</div>';
						}
					}else{
						echo "<p>Please select a unit to view its lessons.";
					}
				break;
				case "register":					
					if(!empty($CourseID)){
						//Begin short scripts
						//Register script
						$SelectedUnitIDs = array();
						$DateRegistered = date('Y-m-d');
						$CurrentAcademicPeriod = getCurrentAcademicPeriod($DateRegistered);
						$AcademicID = $CurrentAcademicPeriod['UID'];
						if(isset($_POST['REGISTER']) && isset($_POST['UnitID'])){	
							foreach($_POST['UnitID'] as $SelectedUnitID){
								//Check if this student has already registered for this unit
								$checkDuplicateSql = sprintf("SELECT `StudentID` FROM `".DB_PREFIX."units_registered` WHERE `StudentID` = '%s' AND `CourseID` = '%s' AND `UnitID` = '%s'", $student['StudentID'], $CourseID, $SelectedUnitID);
								//Check if any results were returned
								if(!empty($SelectedUnitID) && !checkDuplicateEntry($checkDuplicateSql)){
			$sqlRegUnits = sprintf("INSERT INTO `".DB_PREFIX."units_registered` (`AcademicID`,`StudentID`,`CourseID`,`UnitID`) VALUES (%d,'%s','%s','%s')", $AcademicID, $student['StudentID'],$CourseID,$SelectedUnitID);						  
									//Run query
									db_query($sqlRegUnits,DB_NAME,$conn);							  							  							  
								}
							}
							
							$resGetRegisteredUnits = getStudentUnitsByStatus($student['StudentID'], $CourseID, "Registered");
							if(db_num_rows($resGetRegisteredUnits)>0){
								while($rowRegistered = db_fetch_array($resGetRegisteredUnits)){
									$Total = $Total+$rowRegistered['TuitionFee'];
								}
							}														
							
							$mail = new PHPMailer;
							$Subject = $student['StudentID']." - Units Registration";
							//Send a message to user
							$content='<html><head>
							<title>'.$Subject.'</title>
							</head><body><div style="background-color:#E1CDB7; color:#000; width:600px; margin:0 auto;">
							<div style="background:#C60; color:#FFF; min-width:584px; padding:8px;">
							<h2 style="font-size:15px;font-weight:700;line-height:25px;"><em>'.strtoupper(SYSTEM_NAME).' UNITS REGISTRATION</em></h2>
							</div>
							<div style="padding:15px;">
							<h3 style="color:#333;">Dear '.$student['StudentName'].',</h3>
							<p style="text-align:justify;">We would like to let you know that your selection for units was successfully submitted and is under review. You have requested to register for the following units:</p>
							<h3>Course: '.getCourseName($CourseID).'</h3>
							'.getRegisteredStudentUnitsTable($student['StudentID'], $CourseID, 'Pending').'							
							<h3>Total Course Fees:</h3>
							<p>'.getCourseFeesStructure($CourseID, $student['StudyMode']).'</p>
							<h3>What next?</h3>
							<p style=" text-align:justify;">If any of the selected units are approved, you will receive an <strong>Admission Letter</strong>.</p><br />
							<p style="color:#753b01;">All the best!<br /><br />
							Admissions Office,<br />
							'.SYSTEM_NAME.',<br />
							'.COMPANY_ADDRESS.'<br />
							TEL: '.COMPANY_PHONE.'<br />
							EMAIL: '.INFO_EMAIL.'<br />
							WEBSITE: '.PARENT_HOME_URL.'</p>
							</div></div>
							</body></html>';
				
							$body = preg_replace('/\\\\/','', $content); //Strip backslashes
							
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
							$mail->addAddress($student['Email'], $student['StudentName']);
							
							
							if(!$mail->Send()) {
								$_SESSION['MSG'] = ConfirmMessage("Selected units have been submitted for registration");
							}else{
								$_SESSION['MSG'] = ConfirmMessage("Selected units have been submitted for registration. Check your email to the fee break down.");
							}
						}
						//End short scripts
						
						//set sql
						$unitsSql = sprintf("SELECT * FROM `".DB_PREFIX."units` WHERE `CourseID` = '%s' AND `disabledFlag` = 0 AND `deletedFlag` = 0 ORDER BY `YrTrim` ASC", $CourseID);
						
						echo "<h2>Course Registration</h2>";
						echo '<h3 class="text-uppercase text-primary">'. $Course['CName'] .' <span class="small text-muted">'. $CourseID .'</span></h3>';
						?>
						<script>
						//<!--
						function checkSelUnits(field){
							if(document.RegisterUnitsForm.UnitIDs.checked == true){
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
						<div class="CourseUnits">
							<p><strong>Units available for this course</strong></p>
							<p>Registration period is currently open until <?php echo fixdatelong($CurrentAcademicPeriod['RegDateClosed']); ?></p>
							<div id="hideMsg"><?php if(isset($_SESSION['MSG'])) echo $_SESSION['MSG'];?></div>
							<form name="RegisterUnitsForm" method="post" action="?dispatcher=courses&task=register&CourseID=<?=$CourseID;?>">
								<input type="hidden" name="CourseID" value="<?=$CourseID;?>">
								<table width="100%" class="display table table-striped table-bordered table-hover">
									<thead>
										<tr>
											<th width="20">#</th>
											<th>UnitID</th>
											<th>Unit Name</th>
											<th>Tuition Fee</th>
											<th>Year/Semester</th>
											<th class="no-sort text-center"><input type="checkbox" name="UnitIDs" title="Check All" onclick="checkSelUnits(document.getElementsByName('UnitID[]'));" value="" /></th>
										</tr>
									</thead>
									<tbody>
										<?php
										$resultGetUnits = db_query($unitsSql,DB_NAME,$conn);
										
										//check if any rows returned
										if(db_num_rows($resultGetUnits)>0){
												$count = 1;				
												while($row = db_fetch_array($resultGetUnits)){
														echo "<tr>
														<td>".$count."</td>
														<td><a href='?dispatcher=courses&task=unitdetails&UnitID=".$row['UnitID']."'>".$row['UnitID']."</a></td>
														<td>".$row['UName']."</td>
														<td>".$row['TuitionFee']."</td>
														<td>".get_year_trimesters($row['YrTrim'])."</td>";
														echo "<td align=\"center\">";
														$RegUnit = registeredUnitStatus($student['StudentID'], $row['UnitID']);
														if($RegUnit){
																echo $RegUnit;
														}else{
																echo "<input type=\"checkbox\" id=\"selectedIDs\" name=\"UnitID[]\" value=\"".$row['UnitID']."\">";
														}
														echo "</td>";
														echo "</tr>";                      
														++$count;
												}
										}
										?>
									</tbody>
								</table>
								<input type="submit" name="REGISTER" value="REGISTER NOW" class="btn btn-danger">
							</form>
							
							<?php
							$Total = 0;
							floatval($Total);							
							$resGetRegisteredUnits = getStudentUnitsByStatus($student['StudentID'], $CourseID, "Registered");
							if(db_num_rows($resGetRegisteredUnits)>0){
								?>
								<h3>Registered units and tuition fees</h3>
								<p>You can drop until before <?php echo fixdatelong($CurrentAcademicPeriod['DateStart']); ?></p>
								<table width="100%" class="table table-striped table-bordered table-hover">
								<thead>
								<th>Unit Name</th>
								<th style="text-align:right;">Tuition Fee</th>								
								</thead>
								<tbody>
								<?php
								while($rowRegistered = db_fetch_array($resGetRegisteredUnits)){
									echo "<tr>
									<td>".$rowRegistered['UName']."</td>
									<td style=\"text-align:right;\">".number_format($rowRegistered['TuitionFee'], 2)."</td>
									</tr>";
									$Total = $Total+$rowRegistered['TuitionFee'];
								}							
								?>
								</tbody>
								<tfoot>
								<tr>
								<th style="text-align:right;" colspan="">Total</th>
								<th style="text-align:right;"><?php echo number_format($Total, 2); ?></th>
								</tr>
								</tfoot>
								</table>
								<?php
							}
							?>
						</div>
						<?php
					}else{
						echo "<p>Please select a course to view its details.</p>";
					}
					unset($_SESSION['MSG']);
				break;
				case "courseunits":
					if(!empty($CourseID)){
						//set sql
						$unitsSql = sprintf("SELECT * FROM `".DB_PREFIX."units` WHERE `CourseID` = '%s' AND `disabledFlag` = 0 AND `deletedFlag` = 0 ORDER BY  `YrTrim` ASC", $CourseID);
						
						echo "<h2>Course Units</h2>";
						echo '<h3 class="text-uppercase text-primary">'. $Course['CName'] .' <span class="small text-muted">'. $CourseID .'</span></h3>';
						?>
						<div class="CourseWork">
							<div id="hideMsg"><?php if(isset($_SESSION['MSG'])) echo $_SESSION['MSG'];?></div>
							<table width="100%" class="display table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<th width="20">#</th>
										<th>UnitID</th>
										<th>Unit Name</th>
										<th>Tuition Fee</th>
										<th>Instructors</th>
										<th>Year/Semester</th>
										<th>Status</th>
									</tr>
								</thead>
								<tbody>
								<?php
								$resultGetUnits = db_query($unitsSql,DB_NAME,$conn);
								
								//check if any rows returned
								if(db_num_rows($resultGetUnits)>0){
									$count = 1;				
									while($row = db_fetch_array($resultGetUnits)){
										echo "<tr>
										<td>".$count."</td>
										<td><a href='?dispatcher=courses&task=unitdetails&UnitID=".$row['UnitID']."'>".$row['UnitID']."</a></td>
										<td>".$row['UName']."</td>
										<td>".$row['TuitionFee']."</td>
										<td>".getUnitTutors($row['UnitID'])."</td>
										<td>".get_year_trimesters($row['YrTrim'])."</td>";
										echo "<td align=\"center\">";
										$RegUnit = registeredUnitStatus($student['StudentID'], $row['UnitID']);
										if($RegUnit){
											echo $RegUnit;
										}else{
											echo "Not Enrolled";
										}
										echo "</td>";
										echo "</tr>";
										++$count;
									}
								}
								?>
								</tbody>
							</table>
						</div>
						<?php
					}else{
						echo "<p>Please select a course to view its units.</p>";
					}
				break;
				case "coursework":
					if(!empty($CourseID)){
						echo "<h2>Course Work</h2>";
						echo '<h3 class="text-uppercase text-primary">'. $Course['CName'] .' <span class="small text-muted">'. $CourseID .'</span></h3>';												
						?>
						<div class="CourseWork">
							<div id="hideMsg"><?php if(isset($_SESSION['MSG'])) echo $_SESSION['MSG'];?></div>
							<table width="100%" class="display table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<th>#</th>
										<th>Unit ID</th>
										<th>Unit Name</th>
										<th>Credit</th>
										<th>Instructors</th>
										<th>Lessons</th>
									</tr>
								</thead>
								<tbody>
								<?php
								$resultGetRegesteredUnits = getStudentUnitsByStatus($student['StudentID'], $CourseID, 'Registered');
								if(db_num_rows($resultGetRegesteredUnits)>0){
									$count = 1;				
									while($row = db_fetch_array($resultGetRegesteredUnits)){
										echo "<tr>
										<td>".$count."</td>										
										<td>".$row['UnitID']."</td>
										<td>".$row['UName']."</td>
										<td>".$row['Credit']."</td>
										<td>".getUnitTutors($row['UnitID'])."</td>
										<td><a href='?dispatcher=courses&task=unitlessons&UnitID=".$row['UnitID']."'>View lessons</a></td>
										</td>
										</tr>";
										++$count;
									}
								}
								?>
								</tbody>
							</table>
						</div>
						<?php
					}else{
						echo "<p>Please select a course to view its course work.</p>";
					}
				break;				
				default:
					$CourseIDs = array();
					$CourseIDs = explode(",", $student['Courses']);
					if(!empty($CourseIDs)){
						echo '<div class="AvailableCourses">';
						echo "<h2>Courses Applied</h2>";
						//Loop foreach($CourseIDs as $CourseID)
						while (list(, $CourseID) = each($CourseIDs)) {
							$Course = getCourseDetails($CourseID);			
							echo '<h3 class="text-uppercase text-primary">'. $Course['CName'] .' <span class="small text-muted">'. $CourseID .'</span></h3>';
							?>							
							<div class="CourseDetails">
								<h3>Course Outline</h3>
								<p><?=decode($Course['Outline']);?></p>
								<h3>Course Description</h3>
								<p><?=decode($Course['Description']);?></p>
								<p> <a class="btn btn-default" href="?dispatcher=courses&task=courseunits&CourseID=<?=$CourseID;?>">View Units</a> <a class="btn btn-default" href="?dispatcher=courses&task=register&CourseID=<?=$CourseID;?>">Register Now</a> <a class="btn btn-default" href="?dispatcher=courses&task=coursework&CourseID=<?=$CourseID;?>">Course Work</a> </p>
							</div>
							<?php
						}
						echo '</div>';
					}else{
						echo '<p>You have not registered for any courses.</p>';
					}
        break;
      }
      ?>
			<!--End Forms-->
		</div>
	</div>
</div>
<!-- /.row -->