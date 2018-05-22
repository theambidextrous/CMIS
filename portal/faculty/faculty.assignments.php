<script>
<!--//
//Define page title
document.title = "<?=SYSTEM_SHORT_NAME?> - Faculty | Assignments";

$(document).ready(function() {
	//Load TinyMCE	
	tinymce.init({		
		selector: 'textarea.tinymce',
		height: 500,
		theme: 'modern',
		plugins: 'print preview searchreplace autolink directionality visualblocks visualchars fullscreen image link media codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists textcolor wordcount imagetools contextmenu colorpicker textpattern help',
		toolbar1: 'formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent | removeformat',
		image_advtab: true,
		content_css: "<?=SYSTEM_URL;?>/styles/tinymce.editor.css"
	});
	
	//$('#collapse1').addClass('in');
	
});
//-->
</script>
<div class="row">
  <div class="col-lg-12">
    <h1 class="page-header">Assignments</h1>
  </div>
  <!-- /.col-lg-12 -->
</div>
<!-- /.row -->

<div class="row">
  <div class="col-lg-12">
    <div class="cms-contents-grey">
      <!--Begin Forms-->        
      <?php
	  	//Required
			require_once("$class_dir/EvarsitySMS.php");
			$EditID = !empty($_GET['eid'])?$_GET['eid']:NULL;
			$StudentID = !empty($_GET['stdid'])?$_GET['stdid']:NULL;
      $UnitID = !empty($_GET['UnitID'])?$_GET['UnitID']:$_SESSION['UnitID'];			
			if(!empty($UnitID)){
				$Unit = getUnitDetails($UnitID);
				
				//Actions
				$action = isset($_GET["action"])?ucwords($_GET["action"]):"Add";
				
				$collapse = '';
				$disableAddBtn = '';
				
				if($action == 'Edit' && !empty($UnitID) && !empty($EditID)){					
					$collapse = 'in';
					$disableAddBtn = "disabled";
					
					$sqlGetAssignments = sprintf("SELECT `UnitID`,`Title`,`Description`,`Credits`,`DateDue` FROM `".DB_PREFIX."assignments` WHERE `UID` = %d", $EditID);
					//run the query
					$resGetAssignments = db_query($sqlGetAssignments,DB_NAME,$conn);
					$rowGetAssignments = db_fetch_array($resGetAssignments);						
					
					$Title = isset($_POST['Title'])?secure_string($_POST['Title']):$rowGetAssignments['Title'];
					$Description = isset($_POST['Description'])?$_POST['Description']:decode($rowGetAssignments['Description']);
					$EncodedDescription = encode(secure_string($Description));
					$Credits = isset($_POST['Credits'])?secure_string($_POST['Credits']):$rowGetAssignments['Credits'];
					$DateDue = isset($_POST['DateDue'])?$_POST['DateDue']:$rowGetAssignments['DateDue'];
					$dbDateDue = db_fixdate($DateDue);
					
					if($PLID == "None"){
						$PLID = 0;
					}
					
					if(isset($_POST['Edit']) && !empty($EditID)){
						$sqlEditAssignment = sprintf("UPDATE `".DB_PREFIX."assignments` SET `UnitID`='%s', `Title`='%s', `Description`='%s', `Credits`='%s', `DateDue`='%s' WHERE `UID`=%d", $UnitID, $Title, $EncodedDescription, $Credits, $dbDateDue, $EditID);
						
						//Execute the query or die if there is a problem
						db_query($sqlEditAssignment,DB_NAME,$conn);
						
						//Check if saved
						if(db_affected_rows($conn)){
							//notify
							$message = "Dear Student, a previously added assignment has been amended by your tutor, please login to view changes";
							manageAssignment($UnitID, 1);
							//goto								
							redirect("?dispatcher=assignments&task=coursework&unitid=$UnitID&update=true");
						}else{
							echo ErrorMessage("Failed to update the selected assignment.");
						}
					}
					
				}								
				
				if(isset($_POST['Add']) && !empty($UnitID)){
					$Title = isset($_POST['Title'])?secure_string($_POST['Title']):"";				
					$Description = isset($_POST['Description'])?$_POST['Description']:"";
					$EncodedDescription = encode(secure_string($Description));
					$Credits = isset($_POST['Credits'])?secure_string($_POST['Credits']):"";
					$DateDue = isset($_POST['DateDue'])?$_POST['DateDue']:"";
					$dbDateDue = db_fixdate($DateDue);
					
					$sqlAddAssignment = sprintf("INSERT INTO `".DB_PREFIX."assignments` (`UnitID`,`Title`,`Description`,`Credits`,`DateDue`) VALUES ('%s','%s','%s','%s','%s')", $UnitID, $Title, $EncodedDescription, $Credits, $dbDateDue);
					
					//Execute the query or die if there is a problem
					db_query($sqlAddAssignment,DB_NAME,$conn);
					
					//Check if saved
					if(db_affected_rows($conn)){
						//notify
						$message = "Dear Student, a new assignment requiring your attention has been added to your Finstock portal account";
						manageAssignment($UnitID, 0, $message);
						//goto							
						echo ConfirmMessage("New assignment has been added successfully.");
					}else{
						echo ErrorMessage("Failed to add a new assignment.");
					}
				}
				
				if($action == 'Grade'){
					
					if(isset($_POST['AssignGrade'])){
						$StudentID = secure_string($_POST['StudentID']);
						$AssignmentID = intval($_POST['AssignmentID']);
						$FacultyID = secure_string($_POST['FacultyID']);
						$Grade = intval($_POST['Grade']);
						$DateGraded = date('Y-m-d H:i:s');
						
						$sqlUpdateAssignment = sprintf("UPDATE `".DB_PREFIX."assignment_uploads` SET `Status` = '%s', `GradeAttained` = %d, `DateGraded` = '%s', `FacultyID` = '%s' WHERE `AssignmentID` = %d AND `StudentID` = '%s'", 'Graded', $Grade, $DateGraded, $FacultyID, $AssignmentID, $StudentID);
						
						//Execute the query or die if there is a problem
						db_query($sqlUpdateAssignment,DB_NAME,$conn);
						
						//Check if saved
						if(db_affected_rows($conn)){						
							echo ConfirmMessage("Grade updated successfully to the selected assignment.");
						}else{
							echo ErrorMessage("Failed to assign grade to the selected assignment.");
						}
					}else{
						echo ErrorMessage("Failed to assign grade to the selected assignment.");
					}
				}
				
				$update = ($_GET["update"]==='true')?ConfirmMessage("The selected assignment has been updated successfully."):"";
				echo $update;
				?>
				<h2><?=$UnitID;?> <small>(<?=$Unit['UName'];?>)</small></h2>
				<h3>Available assignments for this unit</h3>
				<p class="text-right"><button data-toggle="collapse" data-target="#newassignmentform" class="btn btn-success" <?=$disableAddBtn;?>>Add New Assignment</button></p>				
				<form id="newassignmentform" class="collapse <?=$collapse;?>" method="post" action="" enctype="multipart/form-data">
				<div id="newassignment">
					<div class="row">							
						<div class="col-md-12">
						
							<div class="form-group">
								<label for="">Title: <span class="text-danger">*</span></label>
								<input type="text" value="<?=$Title;?>" name="Title" class="form-control required"><span class="text-danger"><?=$ERRORS['Title'];?></span>
							</div>
							
							<div class="row">							
							  <div class="col-md-6">
									<div class="form-group">
										<label for="">Credits: <span class="text-danger">*</span></label>
										<input type="text" value="<?=$Credits;?>" name="Credits" class="form-control required"><span class="text-danger"><?=$ERRORS['Credits'];?></span>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="">Date Due: <span class="text-danger">*</span></label>
										<input type="text" value="<?=$DateDue;?>" name="DateDue" class="form-control datepicker"><span class="text-danger"><?=$ERRORS['DateDue'];?></span>
									</div>
							  </div>
							</div>
							
							<div class="form-group">
								<label for="">Assignment Content: <span class="text-danger">*</span> <small>(content, link, or video)</small></label>
								<textarea id="ut_content" name="Description" class="form-control tinymce"><?=$Description;?></textarea>
							</div>
							
							<div class="form-group">
								<input type="submit" name="<?=$action;?>" value="Save Changes" class="btn btn-primary">
								<input type="button" name="Cancel" value="Cancel" onclick="javascript:location.href='?dispatcher=assignments'" class="btn btn-default">
							</div>
							
						</div>							
					</div>
				</div>
				</form>
				<?php
				$sqlGetAssignments = sprintf("SELECT * FROM `".DB_PREFIX."assignments` WHERE `UnitID` = '%s' AND `deletedFlag` = %d ORDER BY `DateDue` ASC", $UnitID, 0);
				//Execute the query or die if there is a problem
				$resGetAssignments = db_query($sqlGetAssignments,DB_NAME,$conn);
				
				//check if any rows returned
				if(db_num_rows($resGetAssignments)>0){
					$count = 1;
					$expired = 0;
					echo '<div class="panel-group" id="accordion">';
					while($assignment = db_fetch_array($resGetAssignments)){
						$date_today = new DateTime();
						$date_due = new DateTime($assignment['DateDue']);						
						$diff = (int)$date_today->diff($date_due)->format("%r%a");
						//echo $diff;
						if($diff >= 14){
							$expired = 0;
							$class = "default";
						}elseif($diff >= 7){
							$expired = 0;
							$class = "success";
						}elseif($diff >= 3){
							$expired = 0;
							$class = "warning";
						}elseif($diff >= 0){
							$expired = 0;
							$class = "danger";
						}else{							
							$expired = 1;
							$class = "danger";
						}
						
						$due = $expired==1?"Expired: ":"Due: ";
						?>							
							<div class="panel panel-<?php echo $class; ?>">
								<div class="panel-heading">
									<h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $count; ?>" title="Click here to toggle collapse"><?php echo $assignment['Title']; ?></a> 
									<span class="right" style="float:right;">
									<small class="text-<?php echo $class; ?>"><?php echo $due.timeago($assignment['DateDue']); ?></small>
									<a href="?dispatcher=assignments&task=coursework&action=edit&unitid=<?php echo $assignment['UnitID']; ?>&eid=<?php echo $assignment['UID']; ?>" title="Edit this assignment"><i class="fa fa-edit"></i></a>&nbsp;
									<?php if($assignment['disabledFlag'] == 0){?>
									<a href="?dispatcher=assignments&task=coursework&action=hide&unitid=<?php echo $assignment['UnitID']; ?>&eid=<?php echo $assignment['UID']; ?>" title="Hide from students"><i class="fa fa-eye-slash"></i></a>&nbsp;
									<?php }else{ ?>
									<a href="?dispatcher=assignments&task=coursework&action=show&unitid=<?php echo $assignment['UnitID']; ?>&eid=<?php echo $assignment['UID']; ?>" title="Show from students"><i class="fa fa-eye"></i></a> &nbsp;
									<?php } ?>
									<a href="?dispatcher=assignments&task=coursework&action=remove&unitid=<?php echo $assignment['UnitID']; ?>&eid=<?php echo $assignment['UID']; ?>" title="Remove this assignment"><i class="fa fa-trash"></i></a>
									</span></h4>
									
								</div>
								<div id="collapse<?php echo $count; ?>" class="panel-collapse collapse">
									<div class="panel-body">
										<h3><?php echo $assignment['Title']; ?></h3>
										<p>Credits: <?php echo $assignment['Credits']; ?></p>
										<p>Date Due: <?php echo fixdatelong($assignment['DateDue']); ?></p>
										<h3>Assignment Content</h3>
										<?php echo decode($assignment['Description']); ?>
										<h3>Assignment Submissions</h3>
										<?php
										$sqlGetAssignmentsUploaded = sprintf("SELECT `AssignmentID`,`StudentID`,`UploadPath`,`DateUploaded`,`Status`,`GradeAttained`,`FacultyID` FROM `".DB_PREFIX."assignment_uploads` WHERE `AssignmentID` = %d", $assignment['UID']);
										
										//Execute the query or die if there is a problem
										$resGetAssignmentsUploaded = db_query($sqlGetAssignmentsUploaded,DB_NAME,$conn);
										
										//check if any rows returned
										if(db_num_rows($resGetAssignmentsUploaded)>0){
											?>											
											<table width="100%" class="display table table-striped table-bordered table-hover">
												<thead>
													<tr>
														<td>Student ID</td>														
														<td>Date Uploaded</td>
														<td>Accessed By</td>														
														<td>Status</td>
														<td>Grade</td>
														<td class="no-sort">Actions</td>
													</tr>
												</thead>
												<tbody>
												<?php
												while($upload = db_fetch_array($resGetAssignmentsUploaded)){
													$tad_id = md5($upload['StudentID'].$upload['DateUploaded']);
													echo "<tr>
													<td>".getStudentName($upload['StudentID'])."</td>													
													<td>".fixdatetime($upload['DateUploaded'])."</td>
													<td>".$upload['FacultyID']."</td>													
													<td>".$upload['Status']."</td>
													<td>".$upload['GradeAttained']."</td>
													<td>
														<a style=\"float:left;\" href=\"".$incl_dir."/file.download.php?type=assignment&file=".$upload['UploadPath']."\" class=\"btn btn-sm btn-info\">Download</a>";
														if($upload['Status'] != 'Graded'){
														echo "<form style=\"float:left; padding-left:5px;\" id=\"".$tad_id."\" class=\"form-inline\" name=\"assign-grade-".$tad_id."\" method=\"post\" action=\"?dispatcher=assignments&action=grade\">															
															<input type=\"hidden\" name=\"StudentID\" value=\"".$upload['StudentID']."\">
															<input type=\"hidden\" name=\"AssignmentID\" value=\"".$upload['AssignmentID']."\">
															<input type=\"hidden\" name=\"FacultyID\" value=\"".$faculty['FacultyID']."\">
															<div class=\"input-group\">
																<input type=\"number\" name=\"Grade\" value=\"0\" min=\"0\" max=\"".$assignment['Credits']."\" class=\"form-control input-sm\">
																<span class=\"input-group-btn\">
																	<input type=\"submit\" name=\"AssignGrade\" value=\"Save Grade\" class=\"btn btn-sm btn-danger\">
																</span>
															</div>
														</form>";
														}
														echo "
													</td>
													</tr>";
												}
												?>
												</tbody>
											</table>
											
											<?php
										}else{
											echo "<p>No assignments have been uploaded yet</p>";
										}
										?>
									</div>
								</div>
							</div>															
						<?php
						++$count;
					}
					echo '</div>';
				}
				
			}else{
				echo '<p>You need to select a unit to use this module</p>';
			}
			?>
      <!--End Forms-->
	</div>
  </div>
</div>
<!-- /.row -->