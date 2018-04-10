<script>
<!--//
//Define page title
document.title = "<?=SYSTEM_SHORT_NAME?> - Faculty | My Lectures";

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
    <h1 class="page-header">My Lectures</h1>
  </div>
  <!-- /.col-lg-12 -->
</div>
<!-- /.row -->

<div class="row">
  <div class="col-lg-12">
    <div class="cms-contents-grey">
      <!--Begin Forms-->
      <?php
			$EditID = !empty($_GET['eid'])?$_GET['eid']:0;
      $UnitID = !empty($_GET['unitid'])?$_GET['unitid']:"";			
      //Fetch unit details
			$unit = getUnitDetails($UnitID);
			
      //Get requested task
      $task = isset($_GET['task'])?$_GET['task']:"";
      
      $task = strtolower($task);
      switch($task) {
				case "view":
			  	echo "<h2>Unit Details</h2>";
					echo '<h3 class="text-uppercase text-primary">'. $unit["UName"] .' <span class="small text-muted">'. $unit["UnitID"] .'</span></h3>';
			  
			  break;
				case "attendance":
					echo "<h2>Manage Attendance</h2>";					
					echo '<h3 class="text-uppercase text-primary">'. $unit["UName"] .' <span class="small text-muted">'. $unit["UnitID"] .'</span></h3>';
					?>
					<form name="AttendanceForm" method="post" action="">
					<table width="100%" class="display table table-striped table-bordered table-hover">
					<thead>
					<tr>
					<th width="20">#</th>
					<th>Student ID</th>
					<th>Student Name</th>
					<th class="no-sort text-center" width="40"><input type="checkbox" name="master" title="Check All" onclick="checkAll(document.getElementsByName('StudentID[]'));" value=""></th>
					</tr>
					</thead>
					<tbody>
					<?php          
					//Set sql
					$regUnitsSql = sprintf("SELECT * FROM `".DB_PREFIX."units_registered` WHERE `UnitID` = '%s' AND `Status` = '%s'", $UnitID, 'Registered');
					
					$resRegUnits = db_query($regUnitsSql,DB_NAME,$conn);
					
					//check if any rows returned
					if(db_num_rows($resRegUnits)>0){
						$count = 1;
						while($row = db_fetch_array($resRegUnits)){
							echo "<tr>
							<td>".$count."</td>
							<td>".$row['StudentID']."</td>
							<td>".getStudentName($row['StudentID'])."</td>
							<td align=\"center\"><input type=\"checkbox\" id=\"StudentID\" name=\"StudentID[]\" value=\"".$row['StudentID']."\"></td>
							</tr>";
							++$count;
						}
					}
					?>
					<tbody>
					</table>
								
					<div class="form-group">
					<label for="Title">Mark selected students present for the period:</label>
					<div class="row">              
						<div class="col-md-3">                  
							<input type="text" value="<?=date('m/d/Y')?>" class="form-control datepicker">
						</div>              
						<div class="col-md-3">
							<input type="submit" name="EditAttendance" value="Submit" class="btn btn-primary">
						</div>
					</div>
					</div>					
					</form>
					<?php
				break;
				case "coursework":										
					//Actions
					$action = isset($_GET["action"])?ucwords($_GET["action"]):"Add";
					
					$collapse = '';
					
					if($action == 'Edit' && !empty($UnitID) && !empty($EditID)){
						$collapse = 'in';
						
						$sqlGetLessons = sprintf("SELECT `PLID`, `UnitID`, `Title`, `Description`, `UploadType`, `UploadContent` FROM `".DB_PREFIX."lessons` WHERE `LID` = %d", $EditID);
						//run the query
						$resGetLessons = db_query($sqlGetLessons,DB_NAME,$conn);
						$rowGetLessons = db_fetch_array($resGetLessons);						
						
						$PLID = isset($_POST['PLID'])?secure_string($_POST['PLID']):$rowGetLessons['PLID'];
						$Title = isset($_POST['Title'])?secure_string($_POST['Title']):$rowGetLessons['Title'];
						$Description = isset($_POST['Description'])?secure_string($_POST['Description']):$rowGetLessons['Description'];
						$UploadType = isset($_POST['UploadType'])?secure_string($_POST['UploadType']):$rowGetLessons['UploadType'];						
						$UploadContent = isset($_POST['UploadContent'])?$_POST['UploadContent']:decode($rowGetLessons['UploadContent']);
						$EncodedUploadContent = encode(secure_string($UploadContent));
						
						if($PLID == "None"){
							$PLID = 0;
						}
						
						if(isset($_POST['Edit']) && !empty($EditID)){
							$sqlEditLesson = sprintf("UPDATE `".DB_PREFIX."lessons` SET `PLID`='%s', `UnitID`='%s', `Title`='%s', `Description`='%s', `UploadType`='%s', `UploadContent`='%s' WHERE `LID`=%d",$PLID, $UnitID, $Title, $Description, $UploadType, $EncodedUploadContent, $EditID);
							
							//Execute the query or die if there is a problem
							db_query($sqlEditLesson,DB_NAME,$conn);
							
							//Check if saved
							if(db_affected_rows($conn)){								
								redirect("?tab=3&task=coursework&unitid=$UnitID&update=true");
							}else{
								echo ErrorMessage("Failed to update the selected lesson.");
							}
						}
						
					}
					
					if(isset($_POST['Add']) && !empty($UnitID)){
						$PLID = isset($_POST['PLID'])?secure_string($_POST['PLID']):0;
						$Title = isset($_POST['Title'])?secure_string($_POST['Title']):"";
						$Description = isset($_POST['Description'])?secure_string($_POST['Description']):"";
						$UploadType = isset($_POST['UploadType'])?secure_string($_POST['UploadType']):"";						
						$UploadContent = isset($_POST['UploadContent'])?$_POST['UploadContent']:"";
						$EncodedUploadContent = encode(secure_string($UploadContent));
						$UploadBy = $faculty['FacultyID'];
						$UploadDate = date('Y-m-d H:i:s');
						
						if($PLID == "None"){
							$PLID = 0;
						}
						
						$sqlAddLesson = sprintf("INSERT INTO `".DB_PREFIX."lessons` (`PLID`, `UnitID`, `Title`, `Description`, `UploadType`, `UploadContent`, `UploadBy`, `UploadDate`) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s')",$PLID, $UnitID, $Title, $Description, $UploadType, $EncodedUploadContent, $UploadBy, $UploadDate);
						
						//Execute the query or die if there is a problem
						db_query($sqlAddLesson,DB_NAME,$conn);
						
						//Check if saved
						if(db_affected_rows($conn)){							
							echo ConfirmMessage("New lesson has been added successfully.");
						}else{
							echo ErrorMessage("Failed to add a new lesson.");
						}
					}
					
					$update = ($_GET["update"]==='true')?ConfirmMessage("The selected lesson has been updated successfully."):"";
					echo $update;
					?>
			  	<h2>Manage Coursework</h2>
					<h3 class="text-uppercase text-primary"><?=$unit["UName"];?> <span class="small text-muted"><?=$unit["UnitID"];?></span></h3>
					<p class="text-right"><button data-toggle="collapse" data-target="#newlessonform" class="btn btn-success">Add New Lesson</button></p>
					<form id="newlessonform" class="collapse <?=$collapse;?>" method="post" action="" enctype="multipart/form-data">
					<div id="newlesson">
					  <div class="row">							
							<div class="col-md-12">
							
								<div class="form-group">
								  <label for="">Title: <span class="text-danger">*</span></label>
								  <input type="text" value="<?=$Title;?>" name="Title" class="form-control required"><span class="text-danger"><?=$ERRORS['Title'];?></span>
								</div>
								
								<div class="form-group">
								  <label for="">Description <small>about this lesson (max. 200 characters)</small></label>
								  <textarea name="Description" rows="6" class="form-control"><?=$Description;?></textarea><span class="text-danger"><?=$ERRORS['Description'];?></span>
								</div>
								
								<div class="form-group">
								  <label for="">Upload Type: <span class="text-danger">*</span></label>
								  <select name="UploadType" class="form-control required">
									<?php
									foreach(list_lesson_uploadtypes() as $k => $v){												
										if($k == $UploadType){
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
									<label for="">Lesson Content: <span class="text-danger">*</span> <small>(content, link, or video)</small></label>
								  <textarea id="ut_content" name="UploadContent" class="form-control tinymce"><?=$UploadContent;?></textarea>
								</div>
								
								<div class="form-group">
								  <label for="">Parent Lesson: </label>
								  <?php echo sqlOption("SELECT `LID`,`Title` FROM `".DB_PREFIX."lessons` WHERE `disabledFlag` = 0 AND `deletedFlag` = 0","PLID",$PLID,"--Select Parent Lessons--");?>
								</div>

								<div class="form-group">
								  <input type="submit" name="<?=$action;?>" value="<?=$action;?> Lesson" class="btn btn-primary">
								</div>
								
							</div>							
						</div>
					</div>
					</form>
					<?php
					$sqlGetLessons = sprintf("SELECT * FROM `".DB_PREFIX."lessons` WHERE `UnitID` = '%s' AND `deletedFlag` = %d", $UnitID, 0);
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
										<h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $count; ?>" title="Click here to toggle collapse"><?php echo $row['Title']; ?></a> 
										<span class="right" style="float:right;">
										<a href="?tab=3&task=coursework&action=edit&unitid=<?php echo $row['UnitID']; ?>&eid=<?php echo $row['LID']; ?>" title="Edit this lesson"><i class="fa fa-edit"></i></a>&nbsp;
										<?php if($row['disabledFlag'] == 0){?>
										<a href="?tab=3&task=coursework&action=hide&unitid=<?php echo $row['UnitID']; ?>&eid=<?php echo $row['LID']; ?>" title="Hide from students"><i class="fa fa-eye-slash"></i></a>&nbsp;
										<?php }else{ ?>
										<a href="?tab=3&task=coursework&action=show&unitid=<?php echo $row['UnitID']; ?>&eid=<?php echo $row['LID']; ?>" title="Show from students"><i class="fa fa-eye"></i></a> &nbsp;
										<?php } ?>
										<a href="?tab=3&task=coursework&action=remove&unitid=<?php echo $row['UnitID']; ?>&eid=<?php echo $row['LID']; ?>" title="Remove this lesson"><i class="fa fa-trash"></i></a>
										</span></h4>
										
									</div>
									<div id="collapse<?php echo $count; ?>" class="panel-collapse collapse">
										<div class="panel-body">
										<h3>About this lesson</h3>
										<?php echo $row['Description']; ?>
										<h3>Lesson Content</h3>
										<?php echo decode($row['UploadContent']); ?>
										</div>
									</div>
								</div>															
							<?php
							++$count;
						}
						echo '</div>';
					}
        break;
				default:
					//Execute the query
					$resultGetUnits = getFacultyUnits($faculty['FacultyID']);
					
					//check if any rows returned
					if(db_num_rows($resultGetUnits)>0){
						while($unit = db_fetch_array($resultGetUnits)){
							//$unit = getUnitDetails($row['UnitID']);
							echo "<div class=\"UnitDetails\">";
							echo "<h2>".$unit['UnitID']."</h2>";
							echo "<h3 class=\"text-uppercase text-primary\">".$unit['UName']."</h3>";
							echo "<p>".$unit['Description']."</p>";
							echo "<p align=\"right\"><a href=\"?tab=3&task=view&unitid=".$unit['UnitID']."\">View Unit Details</a> | <a href=\"?tab=3&task=coursework&unitid=".$unit['UnitID']."\">Add Coursework</a> | <a href=\"?tab=5&UnitID=".$unit['UnitID']."\">Add Assignment</a> | <a href=\"?tab=3&task=attendance&unitid=".$unit['UnitID']."\">Check Attendance</a></p><hr>";
							echo "</div>";
						}
					}else{
						echo "<p>No lectures have been assigned to your account</p>";
					}
          break;
      }
      ?>
      <!--End Forms-->
	  </div>
  </div>
</div>
<!-- /.row -->