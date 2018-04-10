<script language="javascript" type="text/javascript">
//Define page title
document.title = "<?=SYSTEM_SHORT_NAME?> | Courses";

$(document).ready(function() {
	//Load TinyMCE	
	tinymce.init({		
		selector: 'textarea.tinymce',
		height: 250,
		theme: 'modern',
		menubar: false,	
		plugins: [
			'advlist autolink lists link image charmap print preview anchor textcolor',
			'searchreplace visualblocks code fullscreen',
			'insertdatetime media table contextmenu paste code help wordcount'
		],
		toolbar: 'formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent | removeformat | help',
		content_css: "<?=SYSTEM_URL;?>/styles/tinymce.editor.css"
	});
	
});
</script>

<div class="row">
  <div class="col-lg-12">
    <h1 class="page-header">Courses</h1>
  </div>
  <!-- /.col-lg-12 --> 
</div>
<!-- /.row -->

<div class="row">
  <div class="col-lg-12">
    <div class="panel panel-default">
      <div class="panel-heading"> <i class="fa fa-files-o fa-fw"></i> Manage Courses </div>
      <!-- /.panel-heading -->
      <div class="panel-body">
				<!--Begin Forms-->
				<?php
				$a = isset($_GET["task"])?$_GET["task"]:"";
				$recid = intval(! empty($_GET['recid']))?$_GET['recid']:0;
				
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
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li class="active">Available Courses</li></ol>

<div id="hideMsg"><?php if(isset($_SESSION['MSG'])) echo $_SESSION['MSG'];?></div>

<?php showpagenav($pagecount); ?>
<table width="100%" class="display table table-striped table-bordered table-hover">
<thead>
<tr>
<th class="rid">#</th>
<th>Course ID</th>
<th>Course Name</th>
<th>Course Level</th>
<th>Department</th>
<th>Units</th>
<th class="no-sort">Enable</th>
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
<td><?=$row["CourseID"]?></td>
<td><?=$row["CName"]?></td>
<td><?=$row["CLevel"]?></td>
<td><?=$row["DeptID"]?></td>
<td style="text-align:center"><a href="?dispatcher=units&filter_field=CourseID&filter=<?=$row["CourseID"]?>"><?=getCourseUnits($row["CourseID"])?></a></td>
<?php
if($row['disabledFlag'] == 0){
	echo "<td align=\"center\"><a href=\"admin.php?dispatcher=courses&enable=1&eid=".$row['UID']."\" title=\"Click to disable ".$row['CName']."\"><img border=\"0\" src=\"".IMAGE_FOLDER."/icons/yes.png\" height=\"12\" width=\"12\" alt=\"Disable ".$row['CName']."\"></a></td>";
}else{
	echo "<td align=\"center\"><a href=\"admin.php?dispatcher=courses&enable=0&eid=".$row['UID']."\" title=\"Click to enable ".$row['CName']."\"><img border=\"0\" src=\"".IMAGE_FOLDER."/icons/no.png\" height=\"12\" width=\"12\" alt=\"Enable ".$row['CName']."\"></a></td>";
}
?>
<td><a href="admin.php?dispatcher=courses&task=view&recid=<?=$i ?>">View</a> | <a href="admin.php?dispatcher=courses&task=edit&recid=<?=$i ?>">Edit</a> | <a href="admin.php?dispatcher=courses&task=del&recid=<?=$i ?>">Delete</a></td>
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
<div class="head-details">
<h2 class="text-uppercase text-primary"><?=$row["CName"]; ?> <span class="small text-muted"><?=$row["CourseID"]; ?></span></h2>
</div>
<div id="adv-tab-container">
  <ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#sub-tabs-1" title="<?=SYSTEM_SHORT_NAME?> | Course Details">Course Details</a></li>
    <li><a data-toggle="tab" href="#sub-tabs-2" title="<?=SYSTEM_SHORT_NAME?> | Course Outline">Course Outline</a></li>
		<li><a data-toggle="tab" href="#sub-tabs-3" title="<?=SYSTEM_SHORT_NAME?> | Course Description">Course Description</a></li>
  </ul>
  <div class="tab-content">
    <div id="sub-tabs-1" class="tab-pane active">
		  <div class="tab-container">
				<div class="table-responsive">
					<table class="table table-bordered table-striped">
					<tr>
					<td>Course Level</td>
					<td><?=$row["CLevel"]; ?></td>
					</tr>
					<tr>
					<td>Department</td>
					<td><?=getDepartmentName($row["DeptID"])." (".$row["DeptID"].")"; ?></td>
					</tr>
					<tr>
					<td>Number of Units</td>
					<td><a href="?dispatcher=units&filter_field=CourseID&filter=<?=$row["CourseID"]?>"><?=getCourseUnits($row["CourseID"])?></a></td>
					</tr>
					<tr>
					<td valign="top">Course Fee</td>
					<td><?=$row["Fee"]; ?></td>
					</tr>
					</table>
					<h3>Fee Breakdown</h3>
					<?php echo getCourseFeesStructure($row["CourseID"], $StudyMode=14);?>
				</div>
			</div>
		</div>
		<div id="sub-tabs-2" class="tab-pane">
			<div class="tab-container">
			  <?=$row["Outline"]; ?>
			</div>
		</div>
		<div id="sub-tabs-3" class="tab-pane">
			<div class="tab-container">
			  <?=$row["Description"]; ?>
			</div>
		</div>
	</div>
</div>
<?php } ?>

<?php 
function showroweditor($row, $iseditmode, $ERRORS){
  global $a;
?>
<p class="text-center lead"><strong><?=strtoupper($a)?> COURSE</strong></p>
<p class="text-center small"><span class="text-danger">FIELDS MARKED WITH ASTERISKS (*) ARE REQUIRED</span></p>

<div class="row">
  <div class="col-md-4">
    <div class="form-group">
    <label for="">Course Department:</label>
    <?php echo sqlOption("SELECT `DeptID`,`DName` FROM `".DB_PREFIX."departments` WHERE `disabledFlag` = 0 AND `deletedFlag` = 0","DeptID",$row['DeptID'],"--Select Department--");?>
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
    <label for="">Course ID: <span class="text-danger">*</span></label>
    <input type="text" value="<?=$row['CourseID']; ?>" name="CourseID" class="form-control required" /><span class="text-danger"><?=$ERRORS['CourseID'];?></span>
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
    <label for="">Course Type: <span class="text-danger">*</span></label>
	<?php echo sqlOption("SELECT `TypeID`,`Type` FROM `".DB_PREFIX."course_types` WHERE 1","CourseType",$row['CourseType'],"--Select Course Type--");?>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6">
    <div class="form-group">
    <label for="">Course Name: <span class="text-danger">*</span></label>
    <input type="text" value="<?=$row['CName']; ?>" name="CName" class="form-control required" /><span class="text-danger"><?=$ERRORS['CName'];?></span>
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
    <label for="">Course Level: <span class="text-danger">*</span></label>
    <select class="form-control" <?=$ERRORS['CLevel']?> name="CLevel">
    <option value="None">--Select--</option>
    <?php
    foreach(list_education_levels() as $k => $v){
        if($k == $row['CLevel']){
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
      <label for="">Additional Fees: <span class="text-danger">*</span></label>
	  <script>
      jQuery(function($) {
          var max_fields      = 10; //maximum input boxes allowed
          var wrapper         = $(".input-fields-wrapper"); //Fields wrapper
          var add_button      = $(".add_field_button"); //Add button ID
          
          var x = 1; //initlal text box count
          $(add_button).click(function(e){ //on add input button click
              e.preventDefault();
              if(x < max_fields){ //max input box allowed
                  x++; //text box increment
                  $(wrapper).append('<div class="row multi-fields"><span class="col-xs-5"><input class="form-control" type="text" name="ApplicableFeeTitle[]" placeholder="Fee Title"></span><span class="col-xs-5"><input class="form-control" type="text" name="ApplicableFeeAmount[]" placeholder="Fee Amount"></span><a href="#" class="remove_field col-xs-2 btn btn-sm btn-danger">X</a></div>'); //add input box
              }
          });
          
          $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
              e.preventDefault(); $(this).parent('div').remove(); x--;
          })
      });
      </script>
      <div class="input-fields-wrapper">        
        <div class="row multi-fields"><span class="col-xs-5"><input class="form-control" type="text" name="ApplicableFeeTitle[]" placeholder="Fee Title"></span><span class="col-xs-5"><input class="form-control" type="text" name="ApplicableFeeAmount[]" placeholder="Fee Amount"></span><span class="col-xs-2"></span></div>
        <?php
        // We do the loop from db here for edit purposes
		?>
      </div>
      <a href="#" class="add_field_button">+ Extra Fees</a>
    </div>
  </div>
  <div class="clear-fix"></div>
</div>

<div class="row">
  <div class="col-md-12">
  <div class="form-group">
  <label for="">Course Outline:</label>
  <textarea class="form-control tinymce" name="Outline" rows="6"><?=$row['Outline'];?></textarea>
  </div>
  
  <div class="form-group">
  <label for="">Course Description:</label>
  <textarea class="form-control tinymce" name="Description" rows="10"><?=$row['Description'];?></textarea><span class="text-danger"><?=$ERRORS['Description'];?></span>
  </div>
  
  </div>
</div>
<?php } ?>

<?php
function showpagenav() {
?>
<div class="quick-nav btn-group">
<a class="btn btn-primary" href="admin.php?dispatcher=courses&task=add">Add Course</a>
<a class="btn btn-default" href="admin.php?dispatcher=courses&task=reset">Reset Filters</a>
</div>
<?php } ?>

<?php function showrecnav($a, $recid, $count) { ?>
<div class="quick-nav btn-group">
<a class="btn btn-default" href="admin.php?dispatcher=courses"><i class="fa fa-undo fa-fw"></i> Back to Courses</a>
<?php if ($recid > 0) { ?>
<a class="btn btn-default" href="admin.php?dispatcher=courses&task=<?=$a ?>&recid=<?=$recid - 1 ?>"><i class="fa fa-arrow-left fa-fw"></i> Prior Record</a>
<?php } if ($recid < $count - 1) { ?>
<a class="btn btn-default" href="admin.php?dispatcher=courses&task=<?=$a ?>&recid=<?=$recid + 1 ?>"><i class="fa fa-arrow-right fa-fw"></i> Next Record</a>
<?php } ?>
</div>
<?php } ?>

<?php 
function viewrec($recid){
  
  $res = sql_select();
  $count = sql_getrecordcount();
  db_data_seek($res, $recid);
  $row = db_fetch_array($res);  
?>
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?dispatcher=courses">Courses</a></li><li class="active">View Course</li></ol>
<?php 
showrecnav("view", $recid, $count);
showrow($row, $recid);
?>
<div class="quick-nav btn-group">
<a class="btn btn-default" href="admin.php?dispatcher=courses&task=add"><i class="fa fa-file-o fa-fw"></i> Add Course</a>
<a class="btn btn-default" href="admin.php?dispatcher=courses&task=edit&recid=<?=$recid ?>"><i class="fa fa-pencil-square-o fa-fw"></i> Edit Course</a>
<a class="btn btn-default" href="admin.php?dispatcher=courses&task=del&recid=<?=$recid ?>"><i class="fa fa-trash-o fa-fw"></i> Delete Course</a>
</div>
<?php
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
	
	// Commands
	if(isset($_POST["Add"])){
		// Course info
		$FIELDS['DeptID'] = secure_string($_POST['DeptID']);
		$FIELDS['CourseID'] = secure_string(whitespace_trim(strtoupper($_POST['CourseID'])));
		$FIELDS['CName'] = secure_string(ucwords($_POST['CName']));
		$FIELDS['CourseType'] = secure_string($_POST['CourseType']);
		$FIELDS['CLevel'] = secure_string($_POST['CLevel']);
		$FIELDS['ApplicableFeeTitle'] = secure_string($_POST['ApplicableFeeTitle']);
		$FIELDS['ApplicableFeeAmount'] = secure_string($_POST['ApplicableFeeAmount']);		
		$FIELDS['Outline'] = encode(secure_string($_POST['Outline']));
		$FIELDS['Description'] = encode(secure_string($_POST['Description']));		
		
		// Validator data
		$check = new validator();
		// validate entry
		// validate "CName" field
		if(!$check->is_String($FIELDS['CourseID']))
		$ERRORS['CourseID'] = "Valid course ID is required";
		// validate "CName" field
		if(!$check->is_String($FIELDS['CName']))
		$ERRORS['CName'] = "Valid course name is required";		
		//Check if this course is already registered	
		$checkDuplicateSql = sprintf("SELECT `CourseID` FROM `".DB_PREFIX."courses` WHERE `CourseID` = '%s'", $FIELDS['CourseID']);
		//check if any results were returned
		if(checkDuplicateEntry($checkDuplicateSql)){
			$ERRORS['CourseID'] = "A course with similar ID already exists!";
		}
		//Check if this course is already registered	
		$checkDuplicateSql2 = sprintf("SELECT `CName` FROM `".DB_PREFIX."courses` WHERE `CName` = '%s'", $FIELDS['CName']);
		//check if any results were returned
		if(checkDuplicateEntry($checkDuplicateSql2)){
			$ERRORS['CName'] = "A course with similar name already exists!";
		}
		
		// check for errors
		if(sizeof($ERRORS) > 0){
			$ERRORS['MSG'] = ErrorMessage("ERRORS ENCOUNTERED!");
		}
		else{
			sql_insert($FIELDS);
		}
	}
	
	$row["DeptID"] = !empty($FIELDS['DeptID'])?$FIELDS['DeptID']:"";
	$row["CourseID"] = !empty($FIELDS['CourseID'])?$FIELDS['CourseID']:"";
	$row["CName"] = !empty($FIELDS['CName'])?$FIELDS['CName']:"";
	$row["CLevel"] = !empty($FIELDS['CLevel'])?$FIELDS['CLevel']:"";
	$row["Outline"] = !empty($FIELDS['Outline'])?decode($FIELDS['Outline']):"";
	$row["Description"] = !empty($FIELDS['Description'])?decode($FIELDS['Description']):"";
	$row['CourseType'] = !empty($FIELDS['CourseType'])?$FIELDS['CourseType']:"";
?>
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?dispatcher=courses">Courses</a></li><li class="active">Add Course</li></ol>

<a class="btn btn-default" href="admin.php?dispatcher=courses"><i class="fa fa-undo fa-fw"></i> Back to Courses</a>

<p class="text-center"><?php if(sizeof($ERRORS['MSG'])>0) echo $ERRORS['MSG'];?></p>
<form id="validateform" enctype="multipart/form-data" action="admin.php?dispatcher=courses&task=add" method="post">
<input type="hidden" name="sql" value="insert" />
<?php
showroweditor($row, false, $ERRORS);
?>
<p class="text-center">
<input class="btn btn-primary" type="submit" name="Add" value="Save" />
<input class="btn btn-default" type="button" name="cancel" value="Cancel" onclick="javascript:location.href='admin.php?dispatcher=courses'" />
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
	
	// Commands
	if(isset($_POST["Edit"])){
		// Course info
		$FIELDS['DeptID'] = secure_string($_POST['DeptID']);
		$FIELDS['CourseType'] = secure_string($_POST['CourseType']);
		$FIELDS['CourseID'] = secure_string(whitespace_trim(strtoupper($_POST['CourseID'])));
		$FIELDS['CName'] = secure_string(ucwords($_POST['CName']));
		$FIELDS['CLevel'] = secure_string($_POST['CLevel']);
		$FIELDS['ApplicableFeeTitle'] = secure_string($_POST['ApplicableFeeTitle']);
		$FIELDS['ApplicableFeeAmount'] = secure_string($_POST['ApplicableFeeAmount']);
		$FIELDS['Outline'] = encode(secure_string($_POST['Outline']));
		$FIELDS['Description'] = encode(secure_string($_POST['Description']));
		
		// Validator data
		$check = new validator();
		// validate entry
		// validate "CourseID" field
		if(!$check->is_String($FIELDS['CourseID']))
		$ERRORS['CourseID'] = "Valid course ID is required";
		// validate "CName" field
		if(!$check->is_String($FIELDS['CName']))
		$ERRORS['CName'] = "Valid course name is required";
		
		// check for errors
		if(sizeof($ERRORS) > 0){
			$ERRORS['MSG'] = ErrorMessage("ERRORS ENCOUNTERED!");
		}
		else{
			sql_update($FIELDS);
		}
  	}
	
	$res = sql_select();
	$count = sql_getrecordcount();
	db_data_seek($res, $recid);
	$row = db_fetch_array($res); 
	
	$row["DeptID"] = !empty($FIELDS['DeptID'])?$FIELDS['DeptID']:$row["DeptID"];
	$row["CourseID"] = !empty($FIELDS['CourseID'])?$FIELDS['CourseID']:$row["CourseID"];
	$row["CName"] = !empty($FIELDS['CName'])?$FIELDS['CName']:$row["CName"];
	$row["CLevel"] = !empty($FIELDS['CLevel'])?$FIELDS['CLevel']:$row["CLevel"];
	$row["Outline"] = !empty($FIELDS['Outline'])?decode($FIELDS['Outline']):$row["Outline"];
	$row["Description"] = !empty($FIELDS['Description'])?decode($FIELDS['Description']):$row["Description"];
	$row['CourseType'] = !empty($FIELDS['CourseType'])?$FIELDS['CourseType']:$row["CourseType"];
?>
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?dispatcher=courses">Courses</a></li><li class="active">Edit Course</li></ol>
<?php showrecnav("edit", $recid, $count); ?>
<form id="validateform" enctype="multipart/form-data" action="admin.php?dispatcher=courses&task=edit&recid=<?=$recid?>" method="post">
<p class="text-center"><?php if(sizeof($ERRORS['MSG'])>0) echo $ERRORS['MSG'];?></p>
<input type="hidden" name="sql" value="update" />
<input type="hidden" name="eid" value="<?=$row["UID"] ?>" />
<?php showroweditor($row, true, $ERRORS); ?>
<p class="text-center">
<input class="btn btn-primary" type="submit" name="Edit" value="Save" />
<input class="btn btn-default" type="button" name="cancel" value="Cancel" onclick="javascript:location.href='admin.php?dispatcher=courses'" />
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
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?dispatcher=courses">Courses</a></li><li class="active">Delete Course</li></ol>
<?php showrecnav("del", $recid, $count); ?>
<form action="admin.php?dispatcher=courses&task=del&recid=<?=$recid?>" method="post">
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
function sql_select(){
	global $conn;
	global $filter;
	global $filterfield;
	
	$filterstr = isset($filter) ? "%". $filter ."%" : "";	
	$sql = "SELECT * FROM `".DB_PREFIX."courses`";
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
	$sql = "SELECT COUNT(*) FROM `".DB_PREFIX."courses`";
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
	
	//Add new course
	$sql = sprintf("INSERT INTO `".DB_PREFIX."courses` (`CourseID`,`CName`,`CLevel`,`Description`,`Outline`,`CourseType`, `DeptID`) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s')", $FIELDS['CourseID'], $FIELDS['CName'], $FIELDS['CLevel'], $FIELDS['Description'], $FIELDS['Outline'], $FIELDS['CourseType'], $FIELDS['DeptID']);	
	db_query($sql,DB_NAME,$conn);
	
	//Check if saved
	if(db_affected_rows($conn)){
		$_SESSION['MSG'] = ConfirmMessage("New course has been added successfully");
	}else{
		$_SESSION['MSG'] = ErrorMessage("Failed to save successfully. Please try again later...");
	}
	redirect("admin.php?dispatcher=courses");
}

function sql_update($FIELDS){
	global $conn;
	
	//Update course
	$sql = sprintf("UPDATE `".DB_PREFIX."courses` SET `CourseID` = '%s', `CName` = '%s', `CLevel` = '%s', `Outline` = '%s', `Description` = '%s', `CourseType` = '%s', `DeptID` = '%s' WHERE " .primarykeycondition(). "", $FIELDS['CourseID'], $FIELDS['CName'], $FIELDS['CLevel'], $FIELDS['Outline'], $FIELDS['Description'], $FIELDS['CourseType'], $FIELDS['DeptID']);		
	db_query($sql,DB_NAME,$conn);
	
	//Check if updated
	if(db_affected_rows($conn)){
		$_SESSION['MSG'] = ConfirmMessage("Course has been updated successfully.");
	}else{
		$_SESSION['MSG'] = WarnMessage("No changes made!");
	}
	redirect("admin.php?dispatcher=courses");
}

function sql_update_status($disabledFlag, $editID){
	global $conn;
	
	//Update course
	$sql = sprintf("UPDATE `".DB_PREFIX."courses` SET `disabledFlag` = %d WHERE `UID` = %d LIMIT 1", $disabledFlag, $editID);
	db_query($sql,DB_NAME,$conn);
	
	//Check if updated
	if(db_affected_rows($conn)){
		$_SESSION['MSG'] = ConfirmMessage("Course has been updated successfully.");
	}else{
		$_SESSION['MSG'] = WarnMessage("No changes made!");
	}
	redirect("admin.php?dispatcher=courses");
}

function sql_delete(){
	global $conn;
	
	$sql = "DELETE FROM `".DB_PREFIX."courses` WHERE " .primarykeycondition();
	db_query($sql,DB_NAME,$conn);
	
	//Check if saved
	if(db_affected_rows($conn)){
		$_SESSION['MSG'] = ConfirmMessage("Course has been deleted successfully");
	}else{
		$_SESSION['MSG'] = ErrorMessage("Failed to delete selected course. Please try again later...");
	}
	redirect("admin.php?dispatcher=courses");
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