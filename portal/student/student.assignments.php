<script>
<!--//
//Define page title
document.title = "<?=SYSTEM_SHORT_NAME?> - Student | My Assignments";
//-->
</script>
<div class="row">
  <div class="col-lg-12">
    <h1 class="page-header">My Assignments</h1>
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
      $EditID = !empty($_GET['eid'])?$_GET['eid']:0;
      $UnitID = !empty($_GET['unitid'])?$_GET['unitid']:'';
      $CourseID = !empty($_GET['CourseID'])?$_GET['CourseID']:$_SESSION['CourseID'];
			if(!empty($CourseID)){
        $collapse = '';
        $StudentID = $student['StudentID'];
        $StudentName = $student['StudentName'];

        //Actions
				$action = isset($_GET["action"])?ucwords($_GET["action"]):"";
				if($action == 'Upload' && !empty($UnitID) && !empty($EditID)){
          $collapse = 'in';
          
          if(isset($_POST['Upload'])){            
            //Get POST data          
            $Remarks = $_POST['Remarks'];            
            $Assignment = $_FILES["Assignment"]["name"];
						$AssignmentTemp = $_FILES["Assignment"]["tmp_name"];
						$UploadDate = date('dmYHis');
            $allowed_mimes = allowed_doc_mime_types("documents");

            //Validate
            // validate "passportphoto" upload file
            if(empty($AssignmentTemp) || !in_array($_FILES["Assignment"]["type"], $allowed_mimes) || $_FILES["Assignment"]["size"] > 800000)
            $ERRORS['Assignment'] = "Uploaded assignment must be a supported document type not greater than 800KB";
            
            if(sizeof($ERRORS) > 0){
              $ERRORS['MSG'] = ErrorMessage("PLEASE CORRECT HIGHLIGHTED FIELDS!");
            }
            else{
              //RUN FILE UPLOADS								
              $AssignmentExt = findexts($Assignment);
              if(!empty($AssignmentTemp)){
                $AssignmentName = friendlyName($StudentName)."-assignment-".$UploadDate.".".$AssignmentExt;
                $AssignmentPath = ASSIGNMENT_PATH.$AssignmentName;
                //Just incase of an internal problem              
                if(move_uploaded_file($AssignmentTemp, $AssignmentPath)){
                  $AssignmentPath = ASSIGNMENT_FOLDER."/".$AssignmentName;
                }else{
                  $AssignmentPath = "";
                }
              }else{
                $AssignmentPath = "";
              }

              //Save to database
              $uploadAssignment = sprintf("INSERT INTO `".DB_PREFIX."assignment_uploads` (`AssignmentID`, `StudentID`, `UploadPath`, `Remarks`) VALUES (%d,'%s','%s','%s')", $EditID, $StudentID, $AssignmentPath, $Remarks);
              db_query($uploadAssignment,DB_NAME,$conn);
					
              //Check if saved
              if(db_affected_rows($conn)){
                $collapse = "";
                echo ConfirmMessage("Your assignment has been uploaded successfully.");
              }else{
                echo ErrorMessage("Failed to upload your assignment. Please try again later...");
              }
            }
          }
        }              
				?>
				<h2><?=$CourseID;?> <small>(<?=getCourseName($CourseID);?>)</small></h2>
				<h3>Available assignments for this course</h3>
        <form id="uploadassignmentform" class="collapse <?=$collapse;?>" method="post" action="" enctype="multipart/form-data">
				<div class="panel panel-primary">
          <div class="panel-heading">
            <h3>Upload assignment for <?php echo $UnitID; ?></h3>
          </div>
          <div class="panel-body">
            <div class="row">							
              <div class="col-md-12">
              
                <div class="form-group">
                  <label for="">Upload: <span class="text-danger">*</span></label>
                  <input type="file" name="Assignment" class="form-control required"><span class="text-danger"><?=$ERRORS['Assignment'];?></span>
                </div>							
                
                <div class="form-group">
                  <label for="">Remarks: <small>(notes to your faculty)</small></label>
                  <textarea name="Remarks" class="form-control"><?=$Remarks;?></textarea>
                </div>
                
                <div class="form-group">
                  <input type="submit" name="Upload" value="Upload Assignment" class="btn btn-primary">
                </div>
                
              </div>							
            </div>
          </div>  
				</div>
				</form>
        <?php
        $UnitIDs = array();
        $resGetRegisteredUnits = getUnitsByStatus($CourseID, "Registered");

        if(db_num_rows($resGetRegisteredUnits)>0){
          while($rowRegistered = db_fetch_array($resGetRegisteredUnits)){            
            array_push($UnitIDs, $rowRegistered['UnitID']);
          }
        }
        
        if(is_array($UnitIDs) && !empty($UnitIDs)){
          $Units = implode("','",$UnitIDs);
          $sqlGetAssignments = sprintf("SELECT * FROM `".DB_PREFIX."assignments` WHERE `UnitID` IN ('%s') AND `disabledFlag` = %d AND `deletedFlag` = %d ORDER BY `DateDue` DESC", $Units, 0, 0);
          //Execute the query or die if there is a problem
          $resultGetAssignments = db_query($sqlGetAssignments,DB_NAME,$conn);
          
          //check if any rows returned
          if(db_num_rows($resultGetAssignments)>0){
            $count = 1;
            $expired = 0;
            echo '<div class="panel-group" id="accordion">';
            while($assignment = db_fetch_array($resultGetAssignments)){
              //Show due date
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

              //Check if already submitted
              //TODO
              ?>							
                <div class="panel panel-<?php echo $class; ?>">
                  <div class="panel-heading">
                    <h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $count; ?>" title="Click here to toggle collapse">(<?php echo $assignment['UnitID']; ?>) - <?php echo $assignment['Title']; ?></a> 
                    <span class="right" style="float:right;">
                    <small class="text-<?php echo $class; ?>"><?php echo $due.timeago($assignment['DateDue']); ?></small>
                    <?php if(!$expired){ ?>
                    <a href="?tab=4&action=upload&unitid=<?php echo $assignment['UnitID']; ?>&eid=<?php echo $assignment['UID']; ?>" title="Upload assignment"><i class="fa fa-upload"></i></a>
                    <?php } ?>
                    </span></h4>
                    
                  </div>
                  <div id="collapse<?php echo $count; ?>" class="panel-collapse collapse">
                    <div class="panel-body">
                      <h3><?php echo $assignment['Title']; ?></h3>
                      <p>Credits: <?php echo $assignment['Credits']; ?></p>
                      <p>Date Due: <?php echo fixdatelong($assignment['DateDue']); ?></p>
                      <h3>Assignment Content</h3>
                      <?php echo decode($assignment['Description']); ?>
                      <h3>Upload Status</h3>
                      <p>Assignment not uploaded</p>
                    </div>
                  </div>
                </div>															
              <?php
              ++$count;
            }
            echo '</div>';
          }
        }else{
          ?>
          <p>No assignments are available at the moment</p>
          <?php
        }
			}else{
				echo '<p>You need to select a course to use this module</p>';
			}
			?>
      <!--End Forms-->
	</div>
  </div>
</div>
<!-- /.row -->