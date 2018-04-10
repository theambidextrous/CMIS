<script type="text/javascript">
<!--//
//Define page title
document.title = "<?=SYSTEM_SHORT_NAME?> | Dashboard";
//-->
</script>

<div class="row">
  <div class="col-lg-12">
    <h1 class="page-header">Dashboard</h1>
  </div>
  <!-- /.col-lg-12 --> 
</div>
<!-- /.row -->

<div class="row">
  <div class="col-lg-3 col-md-6">
    <div class="panel panel-primary">
      <div class="panel-heading">
        <div class="row">
          <div class="col-xs-3"> <i class="fa fa-sitemap fa-5x"></i> </div>
          <div class="col-xs-9 text-right">
            <div class="huge"><?php echo getAllDepartments(); ?></div>
            <div>Departments</div>
          </div>
        </div>
      </div>
      <a href="?dispatcher=departments">
      <div class="panel-footer"> <span class="pull-left">View Details</span> <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
        <div class="clearfix"></div>
      </div>
      </a> </div>
  </div>
  <div class="col-lg-3 col-md-6">
    <div class="panel panel-green">
      <div class="panel-heading">
        <div class="row">
          <div class="col-xs-3"> <i class="fa fa-graduation-cap fa-5x"></i> </div>
          <div class="col-xs-9 text-right">
            <div class="huge"><?php echo getAllCourses(); ?></div>
            <div>Courses</div>
          </div>
        </div>
      </div>
      <a href="?dispatcher=courses">
      <div class="panel-footer"> <span class="pull-left">View Details</span> <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
        <div class="clearfix"></div>
      </div>
      </a> </div>
  </div>
  <div class="col-lg-3 col-md-6">
    <div class="panel panel-yellow">
      <div class="panel-heading">
        <div class="row">
          <div class="col-xs-3"> <i class="fa fa-users fa-5x"></i> </div>
          <div class="col-xs-9 text-right">
            <div class="huge"><?php echo getAllStudents(); ?></div>
            <div>Students</div>
          </div>
        </div>
      </div>
      <a href="?dispatcher=students">
      <div class="panel-footer"> <span class="pull-left">View Details</span> <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
        <div class="clearfix"></div>
      </div>
      </a> </div>
  </div>
  <div class="col-lg-3 col-md-6">
    <div class="panel panel-red">
      <div class="panel-heading">
        <div class="row">
          <div class="col-xs-3"> <i class="fa fa-group fa-5x"></i> </div>
          <div class="col-xs-9 text-right">
            <div class="huge"><?php echo getAllFaculties(); ?></div>
            <div>Faculties</div>
          </div>
        </div>
      </div>
      <a href="?dispatcher=faculties">
      <div class="panel-footer"> <span class="pull-left">View Details</span> <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
        <div class="clearfix"></div>
      </div>
      </a> </div>
  </div>
</div>
<!-- /.row -->

<div class="row">
  <div class="col-lg-12">
    <div class="panel panel-default">
      <div class="panel-heading"> <i class="fa fa-dashboard fa-fw"></i> Quick Links </div>
      <!-- /.panel-heading -->
      <div class="panel-body"> 
        <a class="btn btn-primary" href="admin.php?dispatcher=departments&task=add">Add Department</a> 
        <a class="btn btn-primary" href="admin.php?dispatcher=courses&task=add">Add Course</a> 
        <a class="btn btn-primary" href="admin.php?dispatcher=students&task=add">Add Student</a> 
        <a class="btn btn-primary" href="admin.php?dispatcher=faculties&task=add">Add Faculty</a> 
        <a class="btn btn-primary" href="admin.php?dispatcher=exams&task=edit">Global Settings</a>
      </div>
      <!-- /.panel-body --> 
    </div>
    <!-- /.panel-default --> 
  </div>
  <!-- /.col-lg-12 --> 
</div>
<!-- /.row -->

<?php if(isSuperAdmin() || isSystemAdmin){ ?>
<div class="row">
  <div class="col-lg-12">
    <div class="panel panel-default">
      <div class="panel-heading"> <i class="fa fa-calendar fa-fw"></i> Academic Years </div>
      <!-- /.panel-heading -->
      <div class="panel-body">
        <?php
				$a = isset($_GET["task"])?$_GET["task"]:"";
				$eid = !empty($_GET['eid'])?$_GET['eid']:"";
				
				switch ($a){
				case "add":
				case "edit":
				// Variables
				$ERRORS = array();
				$FIELDS = array();
				
				$FIELDS['AcYear'] = secure_string($_POST['AcYear']);
				$FIELDS['AcName'] = secure_string($_POST['AcName']);
				$FIELDS['AcTerm'] = secure_string($_POST['AcTerm']);
				$FIELDS['DateStart'] = secure_string($_POST['DateStart']);
				$FIELDS['DateEnd'] = secure_string($_POST['DateEnd']);
				$FIELDS['RegDateOpen'] = secure_string($_POST['RegDateOpen']);
				$FIELDS['RegDateClosed'] = secure_string($_POST['RegDateClosed']);
				$FIELDS['RegDetails'] = secure_string($_POST['RegDetails']);
				
				// Validator data
				require_once("$class_dir/class.validator.php3");
				$check = new validator();				
				
				if(isset($_POST['Add'])){
					// validate "AcName" field
					if(strlen($FIELDS['AcName']) > 3 && $FIELDS['AcName'] == "")
					$ERRORS['AcName'] = "Valid academic name is required";
					// validate "DateStart" field
					if(!empty($FIELDS['DateStart'])){
						$SplitDateStart = explode('/', $FIELDS['DateStart']);// Split date by '/'
						//checkdate($month, $day, $year)
						if($check->is_date($SplitDateStart[0],$SplitDateStart[1],$SplitDateStart[2])){
							$FIELDS['dbDateStart'] = db_fixdate($FIELDS['DateStart']);// YYYY-mm-dd
						}else{
							$ERRORS['TermPeriod'] = "Valid start date is required";
						}
					}
					// validate "DateEnd" field
					if(!empty($FIELDS['DateEnd'])){
						$SplitDateEnd = explode('/', $FIELDS['DateEnd']);// Split date by '/'
						//checkdate($month, $day, $year)
						if($check->is_date($SplitDateEnd[0],$SplitDateEnd[1],$SplitDateEnd[2])){
							$FIELDS['dbDateEnd'] = db_fixdate($FIELDS['DateEnd']);// YYYY-mm-dd
						}else{
							$ERRORS['TermPeriod'] = "Valid end date is required";
						}
					}
					// validate "RegDateOpen" field
					if(!empty($FIELDS['RegDateOpen'])){
						$SplitRegDateOpen = explode('/', $FIELDS['RegDateOpen']);// Split date by '/'
						//checkdate($month, $day, $year)
						if($check->is_date($SplitRegDateOpen[0],$SplitRegDateOpen[1],$SplitRegDateOpen[2])){
							$FIELDS['dbRegDateOpen'] = db_fixdate($FIELDS['RegDateOpen']);// YYYY-mm-dd
						}else{
							$ERRORS['RegWindow'] = "Valid start date is required";
						}
					}
					// validate "RegDateClosed" field
					if(!empty($FIELDS['RegDateClosed'])){
						$SplitRegDateClosed = explode('/', $FIELDS['RegDateClosed']);// Split date by '/'
						//checkdate($month, $day, $year)
						if($check->is_date($SplitRegDateClosed[0],$SplitRegDateClosed[1],$SplitRegDateClosed[2])){
							$FIELDS['dbRegDateClosed'] = db_fixdate($FIELDS['RegDateClosed']);// YYYY-mm-dd
						}else{
							$ERRORS['RegWindow'] = "Valid end date is required";
						}
					}
					
					// check for errors
					if(sizeof($ERRORS) > 0){
						$ERRORS['MSG'] = ErrorMessage("PLEASE CORRECT HIGHLIGHTED FIELDS!");
					}
					else{
						$sql_insert = sprintf("INSERT INTO `".DB_PREFIX."academic_yrs` (`AcYear`,`AcName`,`AcTerm`,`DateStart`,`DateEnd`,`RegDateOpen`,`RegDateClosed`,`RegDetails`) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s')", $FIELDS['AcYear'],$FIELDS['AcName'],$FIELDS['AcTerm'],$FIELDS['dbDateStart'],$FIELDS['dbDateEnd'],$FIELDS['dbRegDateOpen'],$FIELDS['dbRegDateClosed'],$FIELDS['RegDetails']);
						db_query($sql_insert,DB_NAME,$conn);
			
						//Check if saved
						if(db_affected_rows($conn)){
							$_SESSION['MSG'] = ConfirmMessage("New academic details have been added successfully.");
							redirect("admin.php?dispatcher=dashboard");
						}else{
							$ERRORS['MSG'] = ErrorMessage("Failed to add new academic details. Please try again.");
						}
					}
				}
				
				if(isset($_POST['Edit']) && !empty($eid)){
					// validate "AcName" field
					if(strlen($FIELDS['AcName']) > 3 && $FIELDS['AcName'] == "")
					$ERRORS['AcName'] = "Valid academic name is required";
					// validate "DateStart" field
					if(!empty($FIELDS['DateStart'])){
						$SplitDateStart = explode('/', $FIELDS['DateStart']);// Split date by '/'
						//checkdate($month, $day, $year)
						if($check->is_date($SplitDateStart[0],$SplitDateStart[1],$SplitDateStart[2])){
							$FIELDS['dbDateStart'] = db_fixdate($FIELDS['DateStart']);// YYYY-mm-dd
						}else{
							$ERRORS['TermPeriod'] = "Valid start date is required";
						}
					}
					// validate "DateEnd" field
					if(!empty($FIELDS['DateEnd'])){
						$SplitDateEnd = explode('/', $FIELDS['DateEnd']);// Split date by '/'
						//checkdate($month, $day, $year)
						if($check->is_date($SplitDateEnd[0],$SplitDateEnd[1],$SplitDateEnd[2])){
							$FIELDS['dbDateEnd'] = db_fixdate($FIELDS['DateEnd']);// YYYY-mm-dd
						}else{
							$ERRORS['TermPeriod'] = "Valid end date is required";
						}
					}
					// validate "RegDateOpen" field
					if(!empty($FIELDS['RegDateOpen'])){
						$SplitRegDateOpen = explode('/', $FIELDS['RegDateOpen']);// Split date by '/'
						//checkdate($month, $day, $year)
						if($check->is_date($SplitRegDateOpen[0],$SplitRegDateOpen[1],$SplitRegDateOpen[2])){
							$FIELDS['dbRegDateOpen'] = db_fixdate($FIELDS['RegDateOpen']);// YYYY-mm-dd
						}else{
							$ERRORS['RegWindow'] = "Valid start date is required";
						}
					}
					// validate "RegDateClosed" field
					if(!empty($FIELDS['RegDateClosed'])){
						$SplitRegDateClosed = explode('/', $FIELDS['RegDateClosed']);// Split date by '/'
						//checkdate($month, $day, $year)
						if($check->is_date($SplitRegDateClosed[0],$SplitRegDateClosed[1],$SplitRegDateClosed[2])){
							$FIELDS['dbRegDateClosed'] = db_fixdate($FIELDS['RegDateClosed']);// YYYY-mm-dd
						}else{
							$ERRORS['RegWindow'] = "Valid end date is required";
						}
					}
					
					// check for errors
					if(sizeof($ERRORS) > 0){
						$ERRORS['MSG'] = ErrorMessage("PLEASE CORRECT HIGHLIGHTED FIELDS!");
					}
					else{
						$sql_update = sprintf("UPDATE `".DB_PREFIX."academic_yrs` SET `AcYear`='%s', `AcName`='%s', `AcTerm`='%s', `DateStart`='%s', `DateEnd`='%s', `RegDateOpen`='%s', `RegDateClosed`='%s', `RegDetails`='%s' WHERE `UID` = %d", $FIELDS['AcYear'],$FIELDS['AcName'],$FIELDS['AcTerm'],$FIELDS['dbDateStart'],$FIELDS['dbDateEnd'],$FIELDS['dbRegDateOpen'],$FIELDS['dbRegDateClosed'],$FIELDS['RegDetails'],$eid);
						db_query($sql_update,DB_NAME,$conn);
						//Check if updated
						if(db_affected_rows($conn)){
							$_SESSION['MSG'] = ConfirmMessage("Academic details have been updated successfully.");
							redirect("admin.php?dispatcher=dashboard");
						}else{
							$ERRORS['MSG'] = WarnMessage("No changes made!");
						}				
					}
				}
				
				if(!empty($eid)){
					$sqlFetch = sprintf("SELECT * FROM `".DB_PREFIX."academic_yrs` WHERE `UID` = %d", $eid);
					//Execute the query
					$resFetch = db_query($sqlFetch,DB_NAME,$conn);
					//Get row
					$row = db_fetch_array($resFetch);						
				}
				
				//Display on the form
				$FIELDS['AcYear'] = !empty($FIELDS['AcYear'])?$FIELDS['AcYear']:$row['AcYear'];
				$FIELDS['AcName'] = !empty($FIELDS['AcName'])?$FIELDS['AcName']:$row['AcName'];
				$FIELDS['AcTerm'] = !empty($FIELDS['AcTerm'])?$FIELDS['AcTerm']:$row['AcTerm'];
				$FIELDS['DateStart'] = !empty($FIELDS['DateStart'])?$FIELDS['DateStart']:$row['DateStart'];
				$FIELDS['DateEnd'] = !empty($FIELDS['DateEnd'])?$FIELDS['DateEnd']:$row['DateEnd'];
				$FIELDS['RegDateOpen'] = !empty($FIELDS['RegDateOpen'])?$FIELDS['RegDateOpen']:$row['RegDateOpen'];
				$FIELDS['RegDateClosed'] = !empty($FIELDS['RegDateClosed'])?$FIELDS['RegDateClosed']:$row['RegDateClosed'];
				$FIELDS['RegDetails'] = !empty($FIELDS['RegDetails'])?$FIELDS['RegDetails']:$row['RegDetails'];
				?>
        <!-- Start Modal -->
        <div id="myModal" class="modal fade" role="dialog">
          <div class="modal-dialog">
        
            <!-- Modal content-->
            <div class="modal-content">
              <form name="academic" method="post" action="admin.php?dispatcher=dashboard&task=add&eid=<?=$eid?>">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?=ucwords($a);?> Academic Year</h4>
              </div>
              <div class="modal-body">
                <div id="hideMsg"><?php if(isset($ERRORS['MSG'])) echo $ERRORS['MSG'];?></div>
                <div class="row">
                  <div class="col-lg-6">
                    <h3><?=ucwords($a);?> Academic Year</h3>
                    <div class="form-group">
                      <label for="year">Year:</label>
                      <input type="number" name="AcYear" value="<?=$FIELDS['AcYear']?>" placeholder="Enter academic year e.g. <?=date('Y')?>" class="form-control">
                    </div>
                    <div class="form-group">
                      <label for="academicname">Academic Name:</label>
                      <input type="text" class="form-control" name="AcName" value="<?=$FIELDS['AcName']?>" placeholder="Trimester 1/<?=date('Y')?>">&nbsp;<span class="text-danger"><?=$ERRORS['AcName'];?></span>
                    </div>
                    <div class="form-group">
                      <label for="term">Term:</label>
                      <div class="radio">
                        <?php
                        if($FIELDS['AcTerm'] == "Semester"){ $Semester = ' checked'; }
												if($FIELDS['AcTerm'] == "Trimester"){ $Trimester = ' checked'; }
												?>
                        <label><input<?=$Semester;?> type="radio" name="AcTerm" value="Semester" class="radio">Semester </label>
                        <label><input<?=$Trimester;?> type="radio" name="AcTerm" value="Trimester" class="radio">Trimester </label>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="termperiod">Term Period:</label>
                      <div class="input-group datepicker-daterange">
                        <input type="text" class="form-control" name="DateStart" value="<?=fixdatepicker($FIELDS['DateStart'])?>" placeholder="mm/dd/YYYY">
                        <div class="input-group-addon">to</div>
                        <input type="text" class="form-control" name="DateEnd" value="<?=fixdatepicker($FIELDS['DateEnd'])?>" placeholder="mm/dd/YYYY">
                      </div>
                      <span class="text-danger"><?=$ERRORS['TermPeriod'];?></span>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <h3>Registration</h3>
                    <div class="form-group">
                      <label for="regwindow">Registration Window:</label>
                      <div class="input-group datepicker-daterange">
                        <input type="text" class="form-control" name="RegDateOpen" value="<?=fixdatepicker($FIELDS['RegDateOpen'])?>" placeholder="mm/dd/YYYY">
                        <div class="input-group-addon">to</div>
                        <input type="text" class="form-control" name="RegDateClosed" value="<?=fixdatepicker($FIELDS['RegDateClosed'])?>" placeholder="mm/dd/YYYY">
                      </div>
                      <span class="text-danger"><?=$ERRORS['RegWindow'];?></span>
                    </div>
                    <div class="form-group">
                      <label for="regdetails">Registration Details:</label>
                      <textarea class="form-control" name="RegDetails" cols="40" rows="6"><?=$FIELDS['RegDetails']?>
    </textarea>
                    </div>                    
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <input class="btn btn-primary" type="submit" name="<?=ucwords($a)?>" value="Apply">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
              </form>
            </div>
        
          </div>
        </div>
        <!-- End Modal -->
        <?php
				break;
				}
				?>
        <div id="hideMsg"><?php if(isset($_SESSION['MSG'])) echo $_SESSION['MSG'];?></div>
        <p><a class="btn btn-primary" href="admin.php?dispatcher=dashboard&task=add">Add New</a> Add the academic year and terms to match with your curriculum calendar.</p>
        
        <table width="100%" class="display table table-striped table-bordered table-hover">
          <thead>
            <tr>
              <th>Year</th>
              <th>Name</th>
              <th>Term</th>
              <th>Term Period</th>
              <th>Registration Window</th>
              <th class="no-sort">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
						//Set sql
						$academicSql = sprintf("SELECT * FROM `".DB_PREFIX."academic_yrs` WHERE `AcYear` = '%s' ORDER BY `DateStart` ASC", date('Y'));
							
						$resAcademic = db_query($academicSql,DB_NAME,$conn);
						
						//check if any rows returned
						if(db_num_rows($resAcademic)>0){
							$counter = 1;			
							while($row = db_fetch_array($resAcademic)){
								echo "<tr>
								<td>".$row['AcYear']."</td>
								<td>".$row['AcName']."</td>
								<td>".$row['AcTerm']."</td>
								<td>".fixdatelong($row['DateStart'])." &mdash; ".fixdatelong($row['DateEnd'])."</td>
								<td>".fixdatelong($row['RegDateOpen'])." &mdash; ".fixdatelong($row['RegDateClosed'])."</td>
								<td><a href=\"admin.php?dispatcher=dashboard&task=edit&eid=".$row['UID']."\">Edit</a></td>
								</tr>";
								++$counter;
							}
						}
						
						unset($_SESSION['MSG']);
						?>
          </tbody>
        </table>
      </div>
      <!-- /.panel-body --> 
    </div>
    <!-- /.panel-default --> 
  </div>
  <!-- /.col-lg-12 --> 
</div>
<!-- /.row -->
<?php } ?>
