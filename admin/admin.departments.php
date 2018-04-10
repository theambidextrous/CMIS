<script language="javascript" type="text/javascript">
//Define page title
document.title = "<?=SYSTEM_SHORT_NAME?> | Departments";
</script>

<div class="row">
  <div class="col-lg-12">
    <h1 class="page-header">Departments</h1>
  </div>
  <!-- /.col-lg-12 --> 
</div>
<!-- /.row -->

<div class="row">
  <div class="col-lg-12">
    <div class="panel panel-default">
      <div class="panel-heading"> <i class="fa fa-sitemap fa-fw"></i> Manage Departments </div>
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
	
	$res = sql_select();
	$count = sql_getrecordcount();	
	
	if(isset($_GET['enable']) && isset($_GET['eid'])){
		$disabledFlag = intval(! empty($_GET['enable']))?$_GET['enable']:0;
		$editID = intval(! empty($_GET['eid']))?$_GET['eid']:0;
		
		sql_update_status($disabledFlag, $editID);
	}
?>
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li class="active">Available Departments</li></ol>

<div id="hideMsg"><?php if(isset($_SESSION['MSG'])) echo $_SESSION['MSG'];?></div>

<?php showpagenav($pagecount); ?>
<table width="100%" class="display table table-striped table-bordered table-hover">
<thead>
<tr>
<th class="rid">#</th>
<th>Department ID</th>
<th>Department Name</th>
<th>Courses</th>
<th>HOD</th>
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
<td><?=$row["DeptID"]?></td>
<td><?=$row["DName"]?></td>
<td style="text-align:center"><a href="?dispatcher=courses&filter_field=DeptID&filter=<?=$row["DeptID"]?>"><?=getDepartmentCourses($row["DeptID"])?></a></td>
<td><?=getFacultyName($row["HOD"])?></td>
<?php
if($row['disabledFlag'] == 0){
	echo "<td align=\"center\"><a href=\"admin.php?dispatcher=departments&enable=1&eid=".$row['UID']."\" title=\"Click to disable ".$row['DName']."\"><img border=\"0\" src=\"".IMAGE_FOLDER."/icons/yes.png\" height=\"12\" width=\"12\" alt=\"Disable ".$row['DName']."\"></a></td>";
}else{
	echo "<td align=\"center\"><a href=\"admin.php?dispatcher=departments&enable=0&eid=".$row['UID']."\" title=\"Click to enable ".$row['DName']."\"><img border=\"0\" src=\"".IMAGE_FOLDER."/icons/no.png\" height=\"12\" width=\"12\" alt=\"Enable ".$row['DName']."\"></a></td>";
}
?>
<td><a href="admin.php?dispatcher=departments&task=view&recid=<?=$i ?>">View</a> | <a href="admin.php?dispatcher=departments&task=edit&recid=<?=$i ?>">Edit</a> | <a href="admin.php?dispatcher=departments&task=del&recid=<?=$i ?>">Delete</a></td>
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
<td width="30%">Department ID</td>
<td><?=$row["DeptID"]; ?></td>
</tr>
<tr>
<td>Department Name</td>
<td><?=$row["DName"]; ?></td>
</tr>
<tr>
<td>Head of Department</td>
<td><?=$row["HOD"]; ?></td>
</tr>
<tr>
<td>Description</td>
<td><?=$row["Description"]; ?></td>
</tr>
</table>
</div>
<?php } ?>

<?php 
function showroweditor($row, $iseditmode, $ERRORS){
  global $a;  
?>
<p class="text-center lead"><strong><?=strtoupper($a)?> DEPARTMENT</strong></p>
<p class="text-center small"><span class="text-danger">FIELDS MARKED WITH ASTERISKS (*) ARE REQUIRED</span></p>

<div class="row">
  <div class="col-md-3">
    <div class="form-group">
    <label for="">Department ID: <span class="text-danger">*</span></label>
    <input type="text" value="<?=$row['DeptID']; ?>" name="DeptID" class="form-control required"><span class="text-danger"><?=$ERRORS['DeptID'];?></span>
    </div>
  </div>

  <div class="col-md-3">
    <div class="form-group">
    <label for="">Department Name: <span class="text-danger">*</span></label>
    <input type="text" value="<?=$row['DName']; ?>" name="DName"  class="form-control required"><span class="text-danger"><?=$ERRORS['DName'];?></span>
    </div>
  </div>
  
  <div class="col-md-3">
    <div class="form-group">
    <label for="">HOD:</label>
    <?php echo sqlOption("SELECT `FacultyID`,CONCAT(`FName`,' ',`LName`) AS `FullName` FROM `".DB_PREFIX."faculties` WHERE `disabledFlag` = 0 AND `deletedFlag` = 0","HOD",$row['HOD'],"--Select HOD--");?>
    </div>
  </div>
  
</div>

<div class="row">
  <div class="col-md-12">
    <div class="form-group">
    <label for="">Short Description</label>
    <textarea name="Description" rows="6" class="form-control"><?=$row['Description'];?></textarea><span class="text-danger"><?=$ERRORS['Description'];?></span>
    </div>
  </div>
</div>
<?php } ?>

<?php
function showpagenav() {
?>
<div class="quick-nav btn-group">
<a class="btn btn-primary" href="admin.php?dispatcher=departments&task=add">Add Department</a>
<a class="btn btn-default" href="admin.php?dispatcher=departments&task=reset">Reset Filters</a>
</div>
<?php } ?>

<?php function showrecnav($a, $recid, $count) { ?>
<div class="quick-nav btn-group">
<a class="btn btn-default" href="admin.php?dispatcher=departments"><i class="fa fa-undo fa-fw"></i> Back to Departments</a>
<?php if ($recid > 0) { ?>
<a class="btn btn-default" href="admin.php?dispatcher=departments&task=<?=$a ?>&recid=<?=$recid - 1 ?>"><i class="fa fa-arrow-left fa-fw"></i> Prior Record</a>
<?php } if ($recid < $count - 1) { ?>
<a class="btn btn-default" href="admin.php?dispatcher=departments&task=<?=$a ?>&recid=<?=$recid + 1 ?>"><i class="fa fa-arrow-right fa-fw"></i> Next Record</a>
<?php } ?>
</div>
<br />
<?php } ?>

<?php 
function viewrec($recid){
  
  $res = sql_select();
  $count = sql_getrecordcount();
  db_data_seek($res, $recid);
  $row = db_fetch_array($res);  
?>
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?dispatcher=departments">Departments</a></li><li class="active">View Department</li></ol>
<?php 
showrecnav("view", $recid, $count);
showrow($row, $recid);
?>
<div class="quick-nav btn-group">
<a class="btn btn-default" href="admin.php?dispatcher=departments&task=add"><i class="fa fa-file-o fa-fw"></i> Add Department</a>
<a class="btn btn-default" href="admin.php?dispatcher=departments&task=edit&recid=<?=$recid ?>"><i class="fa fa-pencil-square-o fa-fw"></i> Edit Department</a>
<a class="btn btn-default" href="admin.php?dispatcher=departments&task=del&recid=<?=$recid ?>"><i class="fa fa-trash-o fa-fw"></i> Delete Department</a>
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
		// Department info
		$FIELDS['DeptID'] = secure_string(whitespace_trim(strtoupper($_POST['DeptID'])));
		$FIELDS['DName'] = secure_string(ucwords($_POST['DName']));
		$FIELDS['Description'] = secure_string($_POST['Description']);
		$FIELDS['HOD'] = secure_string($_POST['HOD']);
		
		// Validator data
		$check = new validator();
		// validate entry
		// validate "DName" field
		if(!$check->is_String($FIELDS['DeptID']))
		$ERRORS['DeptID'] = "Valid department ID is required";
		// validate "DName" field
		if(!$check->is_String($FIELDS['DName']))
		$ERRORS['DName'] = "Valid department name is required";
		//Check if this department is already registered	
		$checkDuplicateSql = sprintf("SELECT `DeptID` FROM `".DB_PREFIX."departments` WHERE `DeptID` = '%s'", $FIELDS['DeptID']);
		//check if any results were returned
		if(checkDuplicateEntry($checkDuplicateSql)){
			$ERRORS['DeptID'] = "A department with similar ID already exists!";
		}
		//Check if this department is already registered	
		$checkDuplicateSql2 = sprintf("SELECT `DName` FROM `".DB_PREFIX."departments` WHERE `DName` = '%s'", $FIELDS['DName']);
		//check if any results were returned
		if(checkDuplicateEntry($checkDuplicateSql2)){
			$ERRORS['DName'] = "A department with similar name already exists!";
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
	$row["DName"] = !empty($FIELDS['DName'])?$FIELDS['DName']:"";
	$row["Description"] = !empty($FIELDS['Description'])?$FIELDS['Description']:"";
	$row["HOD"] = !empty($FIELDS['HOD'])?$FIELDS['HOD']:0;
?>
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?dispatcher=departments">Departments</a></li><li class="active">Add Department</li></ol>

<a class="btn btn-default" href="admin.php?dispatcher=departments"><i class="fa fa-undo fa-fw"></i> Back to Departments</a>

<p class="text-center"><?php if(sizeof($ERRORS['MSG'])>0) echo $ERRORS['MSG'];?></p>
<form id="validateform" enctype="multipart/form-data" action="admin.php?dispatcher=departments&task=add" method="post">
<input type="hidden" name="sql" value="insert">
<?php
showroweditor($row, false, $ERRORS);
?>

<p class="text-center">
<input class="btn btn-primary" type="submit" name="Add" value="Save">
<input class="btn btn-default" type="button" name="cancel" value="Cancel" onclick="javascript:location.href='admin.php?dispatcher=departments'">
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
		// Department info
		$FIELDS['DeptID'] = secure_string(whitespace_trim(strtoupper($_POST['DeptID'])));
		$FIELDS['DName'] = secure_string(ucwords($_POST['DName']));
		$FIELDS['Description'] = secure_string($_POST['Description']);
		$FIELDS['HOD'] = secure_string($_POST['HOD']);
		
		// Validator data
		$check = new validator();
		// validate entry
		// validate "DeptID" field
		if(!$check->is_String($FIELDS['DeptID']))
		$ERRORS['DeptID'] = "Valid department ID is required";
		// validate "DName" field
		if(!$check->is_String($FIELDS['DName']))
		$ERRORS['DName'] = "Valid department name is required";
		
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
	$row["DName"] = !empty($FIELDS['DName'])?$FIELDS['DName']:$row["DName"];
	$row["Description"] = !empty($FIELDS['Description'])?$FIELDS['Description']:$row["Description"];
	$row["HOD"] = !empty($FIELDS['HOD'])?$FIELDS['HOD']:$row["HOD"];
?>
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?dispatcher=departments">Departments</a></li><li class="active">Edit Department</li></ol>
<?php showrecnav("edit", $recid, $count); ?>
<form id="validateform" enctype="multipart/form-data" action="admin.php?dispatcher=departments&task=edit&recid=<?=$recid?>" method="post">
<p class="text-center"><?php if(sizeof($ERRORS['MSG'])>0) echo $ERRORS['MSG'];?></p>
<input type="hidden" name="sql" value="update">
<input type="hidden" name="eid" value="<?=$row["UID"] ?>">
<?php showroweditor($row, true, $ERRORS); ?>
<p class="text-center">
<input class="btn btn-primary" type="submit" name="Edit" value="Save">
<input class="btn btn-default" type="button" name="cancel" value="Cancel" onclick="javascript:location.href='admin.php?dispatcher=departments'">
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
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?dispatcher=departments">Departments</a></li><li>Delete Department</li></ol>
<?php showrecnav("del", $recid, $count); ?>
<form action="admin.php?dispatcher=departments&task=del&recid=<?=$recid?>" method="post">
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
function sql_select(){
	global $conn;
		
	$sql = "SELECT `UID`,`DeptID`,`DName`,`Description`,`HOD`,`disabledFlag` FROM `".DB_PREFIX."departments`";	
	$res = db_query($sql,DB_NAME,$conn);	
	return $res;
}

function sql_getrecordcount(){
	global $conn;
	
	$sql = "SELECT COUNT(*) FROM `".DB_PREFIX."departments`";	
	$res = db_query($sql,DB_NAME,$conn);
	$row = db_fetch_array($res);
	reset($row);
	return current($row);
}

function sql_insert($FIELDS){
	global $conn;
	
	//Add new department
	$sql = sprintf("INSERT INTO `".DB_PREFIX."departments` (`DeptID`,`DName`,`Description`,`HOD`) VALUES ('%s', '%s', '%s', '%s')", $FIELDS['DeptID'], $FIELDS['DName'], $FIELDS['Description'], $FIELDS['HOD']);	
	db_query($sql,DB_NAME,$conn);
	
	//Check if saved
	if(db_affected_rows($conn)){
		$_SESSION['MSG'] = ConfirmMessage("New department has been added successfully");
	}else{
		$_SESSION['MSG'] = ErrorMessage("Failed to save successfully. Please try again later...");
	}
	redirect("admin.php?dispatcher=departments");
}

function sql_update($FIELDS){
	global $conn;
	
	//Update department
	$sql = sprintf("UPDATE `".DB_PREFIX."departments` SET `DeptID` = '%s', `DName` = '%s', `Description` = '%s', `HOD` = '%s' WHERE " .primarykeycondition(). "", $FIELDS['DeptID'], $FIELDS['DName'], $FIELDS['Description'], $FIELDS['HOD']);		
	db_query($sql,DB_NAME,$conn);
	
	//Check if updated
	if(db_affected_rows($conn)){
		$_SESSION['MSG'] = ConfirmMessage("Department has been updated successfully.");
	}else{
		$_SESSION['MSG'] = WarnMessage("No changes made!");
	}
	redirect("admin.php?dispatcher=departments");
}

function sql_update_status($disabledFlag, $editID){
	global $conn;
	
	//Update department
	$sql = sprintf("UPDATE `".DB_PREFIX."departments` SET `disabledFlag` = %d WHERE `UID` = %d LIMIT 1", $disabledFlag, $editID);
	db_query($sql,DB_NAME,$conn);
	
	//Check if updated
	if(db_affected_rows($conn)){
		$_SESSION['MSG'] = ConfirmMessage("Department has been updated successfully.");
	}else{
		$_SESSION['MSG'] = WarnMessage("No changes made!");
	}
	redirect("admin.php?dispatcher=departments");
}

function sql_delete(){
	global $conn;
	
	$sql = "DELETE FROM `".DB_PREFIX."departments` WHERE " .primarykeycondition();
	db_query($sql,DB_NAME,$conn);
	
	//Check if saved
	if(db_affected_rows($conn)){
		$_SESSION['MSG'] = ConfirmMessage("Department has been deleted successfully");
	}else{
		$_SESSION['MSG'] = ErrorMessage("Failed to delete selected department. Please try again later...");
	}
	redirect("admin.php?dispatcher=departments");
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