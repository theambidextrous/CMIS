<script language="javascript" type="text/javascript">
//Define page title
document.title = "<?=SYSTEM_SHORT_NAME?> | Units";

</script>

<div class="row">
  <div class="col-lg-12">
    <h1 class="page-header">Units</h1>
  </div>
  <!-- /.col-lg-12 --> 
</div>
<!-- /.row -->

<div class="row">
  <div class="col-lg-12">
    <div class="panel panel-default">
      <div class="panel-heading"> <i class="fa fa-edit fa-fw"></i> Manage Units </div>
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
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li class="active">Available Units</li></ol>

<div id="hideMsg"><?php if(isset($_SESSION['MSG'])) echo $_SESSION['MSG'];?></div>

<?php showpagenav($pagecount); ?>
<table width="100%" class="display table table-striped table-bordered table-hover">
<thead>
<tr>
<th class="rid">#</th>
<th>Unit ID</th>
<th>Unit Name</th>
<th>Course</th>
<th>Year/Semester</th>
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
<td><?=$row["UnitID"]?></td>
<td><?=$row["UName"]?></td>
<td><?=$row["CourseID"]?></td>
<td><?=get_year_trimesters($row["YrTrim"])?></td>
<?php
if($row['disabledFlag'] == 0){
	echo "<td align=\"center\"><a href=\"admin.php?tab=4&enable=1&eid=".$row['UID']."\" title=\"Click to disable ".$row['UName']."\"><img border=\"0\" src=\"".IMAGE_FOLDER."/icons/yes.png\" height=\"12\" width=\"12\" alt=\"Disable ".$row['UName']."\"></a></td>";
}else{
	echo "<td align=\"center\"><a href=\"admin.php?tab=4&enable=0&eid=".$row['UID']."\" title=\"Click to enable ".$row['UName']."\"><img border=\"0\" src=\"".IMAGE_FOLDER."/icons/no.png\" height=\"12\" width=\"12\" alt=\"Enable ".$row['UName']."\"></a></td>";
}
?>
<td><a href="admin.php?tab=4&task=view&recid=<?=$i ?>">View</a> | <a href="admin.php?tab=4&task=edit&recid=<?=$i ?>">Edit</a> | <a href="admin.php?tab=4&task=del&recid=<?=$i ?>">Delete</a></td>
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
<?php 
function showrow($row, $recid){
global $conn;
?>
<div class="head-details">
<h2 class="text-uppercase text-primary"><?=$row["UName"]; ?> <span class="small text-muted"><?=$row["UnitID"]; ?></span></h2>
</div>

<div id="adv-tab-container">
  <ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#sub-tabs-1" title="<?=SYSTEM_SHORT_NAME?> | Unit Details">Unit Details</a></li>
		<li><a data-toggle="tab" href="#sub-tabs-2" title="<?=SYSTEM_SHORT_NAME?> | Unit Lessons">Unit Lessons</a></li>
  </ul>
  <div class="tab-content">
    <div id="sub-tabs-1" class="tab-pane active">
			<div class="tab-container">
				<div class="table-responsive">
					<table class="table table-bordered table-striped">
					<tr>
					<td>Course</td>
					<td><?=getCourseName($row["CourseID"])." (".$row["CourseID"].")"; ?></td>
					</tr>
					<tr>
					<td valign="top">Year/Semester</td>
					<td><?=get_year_trimesters($row["YrTrim"]); ?></td>
					</tr>
					<tr>
					<td valign="top">Prerequisites</td>
					<td><?=$row["Prerequisites"]; ?></td>
					</tr>
					<tr>
					<td valign="top">Corequisites</td>
					<td><?=$row["Corequisites"]; ?></td>
					</tr>
					<tr>
					<td valign="top">Description</td>
					<td><?=$row["Description"]; ?></td>
					</tr>
					</table>
				</div>
			</div>
		</div>
		<div id="sub-tabs-2" class="tab-pane">
			<div class="tab-container">
			  <?php
				$sqlGetLessons = sprintf("SELECT * FROM `".DB_PREFIX."lessons` WHERE `UnitID` = '%s'", $row["UnitID"]);
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
				?>
			</div>
		</div>
	</div>
</div>
<?php } ?>

<?php 
function showroweditor($row, $iseditmode, $ERRORS){
  global $a; 
?>
<p class="text-center lead"><strong><?=strtoupper($a)?> UNIT</strong></p>
<p class="text-center small"><span class="text-danger">FIELDS MARKED WITH ASTERISKS (*) ARE REQUIRED</span></p>
<div class="row">
  <div class="col-md-6">
  
  <h2>Unit Information</h2>
  
  <div class="form-group">
  <label for="">Unit Course:</label>
  <?php echo sqlOption("SELECT `CourseID`,`CName` FROM `".DB_PREFIX."courses` WHERE `disabledFlag` = 0 AND `deletedFlag` = 0","CourseID",$row['CourseID'],"--Select Course--");?>
  </div>
  
  <div class="form-group">
  <label for="">Unit ID: <span class="text-danger">*</span></label>
  <input type="text" value="<?=$row['UnitID']; ?>" name="UnitID" class="form-control required"><span class="text-danger"><?=$ERRORS['UnitID'];?></span>
  </div>
  
  <div class="form-group">
  <label for="">Unit Name: <span class="text-danger">*</span></label>
  <input type="text" value="<?=$row['UName']; ?>" name="UName" class="form-control required"><span class="text-danger"><?=$ERRORS['UName'];?></span>
  </div>
  
  <div class="form-group">
  <label for="">Credit:</label>
  <input type="text" value="<?=$row['Credit']; ?>" name="Credit" class="form-control">
  </div>
  
  <div class="form-group">
  <label for="">Tuition Fee:</label>
  <input type="text" value="<?=$row['TuitionFee']; ?>" name="TuitionFee" class="form-control">
  </div>
  
  </div>
  <div class="col-md-6">
  
  <h2>Timetable Information</h2>
  
  <div class="form-group">
  <label for="">Year/Trimester: <span class="text-danger">*</span></label>
  <select name="YrTrim" class="form-control">
  <?php
  foreach(list_year_trimesters() as $k => $v){
      if($row['YrTrim'] == $k){
          $selected = ' selected="selected"';
      }else{
          $selected = '';
      }
      echo '<option'.$selected.' value="'.$k.'">'.$v.'</option>';
  }
  ?>
  </select>
  </div>
  
  <div class="form-group">
  <label for="">Dates:</label>
    <div class="row datepicker-daterange">
      <div class="col-sm-6">
      <input type="text" name="StartDate" value="<?=$row['StartDate']; ?>" class="form-control">
      </div>
      <div class="col-sm-6">
      <input type="text" name="EndDate" value="<?=$row['EndDate']; ?>" class="form-control">
      </div>
    </div>
  </div>
  
  <div class="form-group">
  <label for="">Time:</label>
    <div class="row">
      <div class="col-sm-6">
      <input type="text" value="<?=$row['StartTime']; ?>" name="StartTime" class="form-control timepickerrange start">
      </div>
      <div class="col-sm-6">
      <input type="text" value="<?=$row['EndTime']; ?>" name="EndTime" class="form-control timepickerrange end">
      </div>
    </div>
  </div>
  
  <div class="form-group">
  <label for="">Days:</label>
    <div class="checkbox">
    <?php
    $values = explode(",", $row['Days']);
    if($values[0] == "Mon"){ $mon = ' checked="checked"'; }
    if($values[1] == "Tue"){ $tue = ' checked="checked"'; }
    if($values[2] == "Wed"){ $wed = ' checked="checked"'; }
    if($values[3] == "Thu"){ $thu = ' checked="checked"'; }
    if($values[4] == "Fri"){ $fri = ' checked="checked"'; }
    if($values[5] == "Sat"){ $sat = ' checked="checked"'; }
    ?>
    <label><input<?=$mon; ?> type="checkbox" name="Days[]" value="Mon"> Mon</label>
    <label><input<?=$tue; ?> type="checkbox" name="Days[]" value="Tue"> Tue</label>
    <label><input<?=$wed; ?> type="checkbox" name="Days[]" value="Wed"> Wed</label>
    <label><input<?=$thu; ?> type="checkbox" name="Days[]" value="Thu"> Thu</label>
    <label><input<?=$fri; ?> type="checkbox" name="Days[]" value="Fri"> Fri</label>
    <label><input<?=$sat; ?> type="checkbox" name="Days[]" value="Sat"> Sat</label>
    </div>
  </div>
  
  </div>
</div>

<h2>Dependencies</h2>
<div class="row">
  <div class="col-md-6">
    <div class="form-group">
    <label for="">Prerequisites:</label>
    <?php echo sqlOptionMulti("SELECT `UnitID`,`UName` FROM `".DB_PREFIX."units` WHERE `UnitID` != '".$row["UnitID"]."' AND `CourseID` = '".$row["CourseID"]."' AND `disabledFlag` = 0 AND `deletedFlag` = 0","PreUnitIDs",$row['Prerequisites']);?>
    </div>

  </div>
  <div class="col-md-6">
    <div class="form-group">
    <label for="">Corequisites:</label>
    <?php echo sqlOptionMulti("SELECT `UnitID`,`UName` FROM `".DB_PREFIX."units` WHERE `UnitID` != '".$row["UnitID"]."' AND `CourseID` = '".$row["CourseID"]."' AND `disabledFlag` = 0 AND `deletedFlag` = 0","CoUnitIDs",$row['Corequisites']);?>
    </div>
  </div>
</div>

<div class="row">  
  <div class="col-md-12">
    <h2>Unit Description</h2>
    <div class="form-group">
    <textarea name="Description" class="form-control" rows="10"><?=$row['Description'];?></textarea><span class="text-danger"><?=$ERRORS['Description'];?></span>
    </div>
  </div>
</div>

<?php } ?>

<?php
function showpagenav() {
?>
<div class="quick-nav btn-group">
<a class="btn btn-primary" href="admin.php?tab=4&task=add">Add Unit</a>
<a class="btn btn-default" href="admin.php?tab=4&task=reset">Reset Filters</a>
</div>
<?php } ?>

<?php function showrecnav($a, $recid, $count) { ?>
<div class="quick-nav btn-group">
<a class="btn btn-default" href="admin.php?tab=4"><i class="fa fa-undo fa-fw"></i> Back to Units</a>
<?php if ($recid > 0) { ?>
<a class="btn btn-default" href="admin.php?tab=4&task=<?=$a ?>&recid=<?=$recid - 1 ?>"><i class="fa fa-arrow-left fa-fw"></i> Prior Record</a>
<?php } if ($recid < $count - 1) { ?>
<a class="btn btn-default" href="admin.php?tab=4&task=<?=$a ?>&recid=<?=$recid + 1 ?>"><i class="fa fa-arrow-right fa-fw"></i> Next Record</a>
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
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?tab=4">Units</a></li><li class="active">View Unit</li></ol>
<?php 
showrecnav("view", $recid, $count);
showrow($row, $recid);
?>
<div class="quick-nav btn-group">
<a class="btn btn-default" href="admin.php?tab=4&task=add"><i class="fa fa-file-o fa-fw"></i>Add Unit</a>
<a class="btn btn-default" href="admin.php?tab=4&task=edit&recid=<?=$recid ?>"><i class="fa fa-pencil-square-o fa-fw"></i>Edit Unit</a>
<a class="btn btn-default" href="admin.php?tab=4&task=del&recid=<?=$recid ?>"><i class="fa fa-trash-o fa-fw"></i>Delete Unit</a>
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
		// Unit info
		$FIELDS['CourseID'] = secure_string($_POST['CourseID']);
		$FIELDS['UnitID'] = secure_string(whitespace_trim(strtoupper($_POST['UnitID'])));
		$FIELDS['UName'] = secure_string(ucwords($_POST['UName']));
		$FIELDS['Credit'] = secure_string($_POST['Credit']);
		$FIELDS['TuitionFee'] = secure_string($_POST['TuitionFee']);
		$FIELDS['StartDate'] = db_fixdate($_POST['StartDate']);
		$FIELDS['EndDate'] = db_fixdate($_POST['EndDate']);
		$FIELDS['StartTime'] = secure_string($_POST['StartTime']);
		$FIELDS['EndTime'] = secure_string($_POST['EndTime']);
		$FIELDS['Days'] = "";		
		if(!empty($_POST['Days'])){$FIELDS['Days'] = implode(",", $_POST['Days']);}
		$FIELDS['YrTrim'] = secure_string($_POST['YrTrim']);
		$FIELDS['RoomID'] = secure_string($_POST['RoomID']);
		$FIELDS['ClassLimit'] = secure_string($_POST['ClassLimit']);		
		$FIELDS['Description'] = secure_string($_POST['Description']);
		$FIELDS['Prerequisites'] = "";
		if(!empty($_POST['PreUnitIDs'])){$FIELDS['Prerequisites'] = implode(",", $_POST['PreUnitIDs']);}
		$FIELDS['Corequisites'] = "";
		if(!empty($_POST['CoUnitIDs'])){$FIELDS['Corequisites'] = implode(",", $_POST['CoUnitIDs']);}		
		
		// Validator data
		$check = new validator();
		// validate entry
		// validate "UName" field
		if(!$check->is_String($FIELDS['UnitID']))
		$ERRORS['UnitID'] = "Valid unit ID is required";
		// validate "UName" field
		if(!$check->is_String($FIELDS['UName']))
		$ERRORS['UName'] = "Valid unit name is required";
		//Check if this unit is already registered	
		$checkDuplicateSql = sprintf("SELECT `UnitID` FROM `".DB_PREFIX."units` WHERE `UnitID` = '%s'", $FIELDS['UnitID']);
		//check if any results were returned
		if(checkDuplicateEntry($checkDuplicateSql)){
			$ERRORS['UnitID'] = "A unit with similar ID already exists!";
		}
		//Check if this unit is already registered	
		$checkDuplicateSql2 = sprintf("SELECT `UName` FROM `".DB_PREFIX."units` WHERE `UName` = '%s'", $FIELDS['UName']);
		//check if any results were returned
		if(checkDuplicateEntry($checkDuplicateSql2)){
			$ERRORS['UName'] = "A unit with similar name already exists!";
		}
		
		// check for errors
		if(sizeof($ERRORS) > 0){
			$ERRORS['MSG'] = ErrorMessage("ERRORS ENCOUNTERED!");
		}
		else{
			sql_insert($FIELDS);
		}
	}
	
	$row["CourseID"] = !empty($FIELDS['CourseID'])?$FIELDS['CourseID']:"";
	$row["UnitID"] = !empty($FIELDS['UnitID'])?$FIELDS['UnitID']:"";
	$row["UName"] = !empty($FIELDS['UName'])?$FIELDS['UName']:"";
	$row["Credit"] = !empty($FIELDS['Credit'])?$FIELDS['Credit']:"";
	$row["TuitionFee"] = !empty($FIELDS['TuitionFee'])?$FIELDS['TuitionFee']:"";
	$row['StartDate'] = !empty($FIELDS['StartDate'])?$FIELDS['StartDate']:"";
	$row['EndDate'] = !empty($FIELDS['EndDate'])?$FIELDS['EndDate']:"";
	$row['StartTime'] = !empty($FIELDS['StartTime'])?$FIELDS['StartTime']:"";
	$row['EndTime'] = !empty($FIELDS['EndTime'])?$FIELDS['EndTime']:"";
	$row['Days'] = !empty($FIELDS['Days'])?$FIELDS['Days']:"";
	$row["YrTrim"] = !empty($FIELDS['YrTrim'])?$FIELDS['YrTrim']:"";
	$row['RoomID'] = !empty($FIELDS['RoomID'])?$FIELDS['RoomID']:"";
	$row['ClassLimit'] = !empty($FIELDS['ClassLimit'])?$FIELDS['ClassLimit']:"";
	$row["Description"] = !empty($FIELDS['Description'])?$FIELDS['Description']:"";
	$row["Prerequisites"] = !empty($FIELDS['Prerequisites'])?$FIELDS['Prerequisites']:"";
	$row["Corequisites"] = !empty($FIELDS['Corequisites'])?$FIELDS['Corequisites']:"";	
?>
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?tab=4">Units</a></li><li class="active">Add Unit</li></ol>

<a class="btn btn-default" href="admin.php?tab=4"><i class="fa fa-undo fa-fw"></i> Back to Units</a>

<p class="text-center"><?php if(sizeof($ERRORS['MSG'])>0) echo $ERRORS['MSG'];?></p>
<form id="validateform" enctype="multipart/form-data" action="admin.php?tab=4&task=add" method="post">
<input type="hidden" name="sql" value="insert">
<?php
showroweditor($row, false, $ERRORS);
?>
<p class="text-center">
<input class="btn btn-primary" type="submit" name="Add" value="Save">
<input class="btn btn-default" type="button" name="cancel" value="Cancel" onclick="javascript:location.href='admin.php?tab=4'">
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
		// Unit info
		$FIELDS['CourseID'] = secure_string($_POST['CourseID']);
		$FIELDS['UnitID'] = secure_string(whitespace_trim(strtoupper($_POST['UnitID'])));
		$FIELDS['UName'] = secure_string(ucwords($_POST['UName']));
		$FIELDS['Credit'] = secure_string($_POST['Credit']);
		$FIELDS['TuitionFee'] = secure_string($_POST['TuitionFee']);
		$FIELDS['StartDate'] = db_fixdate($_POST['StartDate']);
		$FIELDS['EndDate'] = db_fixdate($_POST['EndDate']);
		$FIELDS['StartTime'] = secure_string($_POST['StartTime']);
		$FIELDS['EndTime'] = secure_string($_POST['EndTime']);
		$FIELDS['Days'] = "";
		if(!empty($_POST['Days'])){$FIELDS['Days'] = implode(",", $_POST['Days']);}
		$FIELDS['YrTrim'] = secure_string($_POST['YrTrim']);
		$FIELDS['RoomID'] = secure_string($_POST['RoomID']);
		$FIELDS['ClassLimit'] = secure_string($_POST['ClassLimit']);
		$FIELDS['Description'] = secure_string($_POST['Description']);
		$FIELDS['Prerequisites'] = "";
		if(!empty($_POST['PreUnitIDs'])){$FIELDS['Prerequisites'] = implode(",", $_POST['PreUnitIDs']);}
		$FIELDS['Corequisites'] = "";
		if(!empty($_POST['CoUnitIDs'])){$FIELDS['Corequisites'] = implode(",", $_POST['CoUnitIDs']);}	
		
		// Validator data
		$check = new validator();
		// validate entry
		// validate "UnitID" field
		if(!$check->is_String($FIELDS['UnitID']))
		$ERRORS['UnitID'] = "Valid unit ID is required";
		// validate "UName" field
		if(!$check->is_String($FIELDS['UName']))
		$ERRORS['UName'] = "Valid unit name is required";
		
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
	
	$row["CourseID"] = !empty($FIELDS['CourseID'])?$FIELDS['CourseID']:$row["CourseID"];
	$row["UnitID"] = !empty($FIELDS['UnitID'])?$FIELDS['UnitID']:$row["UnitID"];
	$row["UName"] = !empty($FIELDS['UName'])?$FIELDS['UName']:$row["UName"];
	$row["Credit"] = !empty($FIELDS['Credit'])?$FIELDS['Credit']:$row["Credit"];
	$row["TuitionFee"] = !empty($FIELDS['TuitionFee'])?$FIELDS['TuitionFee']:$row["TuitionFee"];
	$row['StartDate'] = !empty($FIELDS['StartDate'])?$FIELDS['StartDate']:fixdatepicker($row["StartDate"]);
	$row['EndDate'] = !empty($FIELDS['EndDate'])?$FIELDS['EndDate']:fixdatepicker($row["EndDate"]);
	$row['StartTime'] = !empty($FIELDS['StartTime'])?$FIELDS['StartTime']:$row["StartTime"];
	$row['EndTime'] = !empty($FIELDS['EndTime'])?$FIELDS['EndTime']:$row["EndTime"];
	$row['Days'] = !empty($FIELDS['Days'])?$FIELDS['Days']:$row["Days"];
	$row["YrTrim"] = !empty($FIELDS['YrTrim'])?$FIELDS['YrTrim']:$row["YrTrim"];
	$row['RoomID'] = !empty($FIELDS['RoomID'])?$FIELDS['RoomID']:$row["RoomID"];
	$row['ClassLimit'] = !empty($FIELDS['ClassLimit'])?$FIELDS['ClassLimit']:$row["ClassLimit"];
	$row["Description"] = !empty($FIELDS['Description'])?$FIELDS['Description']:$row["Description"];
	$row["Prerequisites"] = !empty($FIELDS['Prerequisites'])?$FIELDS['Prerequisites']:$row["Prerequisites"];
	$row["Corequisites"] = !empty($FIELDS['Corequisites'])?$FIELDS['Corequisites']:$row["Corequisites"];
?>
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?tab=4">Units</a></li><li class="active">Edit Unit</li></ol>
<?php showrecnav("edit", $recid, $count); ?>
<form id="validateform" enctype="multipart/form-data" action="admin.php?tab=4&task=edit&recid=<?=$recid?>" method="post">
<p class="text-center"><?php if(sizeof($ERRORS['MSG'])>0) echo $ERRORS['MSG'];?></p>
<input type="hidden" name="sql" value="update">
<input type="hidden" name="eid" value="<?=$row["UID"] ?>">
<?php showroweditor($row, true, $ERRORS); ?>
<p class="text-center">
<input class="btn btn-primary" type="submit" name="Edit" value="Save">
<input class="btn btn-default" type="button" name="cancel" value="Cancel" onclick="javascript:location.href='admin.php?tab=4'">
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
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?tab=4">Units</a></li><li class="active">Delete Unit</li></ol>
<?php showrecnav("del", $recid, $count); ?>
<form action="admin.php?tab=4&task=del&recid=<?=$recid?>" method="post">
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
	global $filter;
	global $filterfield;
	
	$filterstr = isset($filter) ? $filterstr = "%" .$filter ."%" : "";	
	$sql = "SELECT `UID`, `UnitID`, `UName`, `Credit`, `TuitionFee`, `StartDate`, `EndDate`, `StartTime`, `EndTime`, `Days`, `RoomID`, `ClassLimit`, `Description`, `CourseID`, `Prerequisites`, `Corequisites`, `YrTrim`, `disabledFlag` FROM `".DB_PREFIX."units`";	
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
	
	$filterstr = isset($filter) ? $filterstr = "%" .$filter ."%" : "";
	$sql = "SELECT COUNT(*) FROM `".DB_PREFIX."units`";
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
	
	//Add new unit
	$sql = sprintf("INSERT INTO `".DB_PREFIX."units` (`UnitID`,`UName`,`Credit`,`TuitionFee`,`StartDate`,`EndDate`,`StartTime`,`EndTime`,`Days`,`RoomID`,`ClassLimit`,`Description`,`CourseID`,`Prerequisites`,`Corequisites`,`YrTrim`) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')", $FIELDS['UnitID'], $FIELDS['UName'], $FIELDS['Credit'], $FIELDS['TuitionFee'], $FIELDS['StartDate'], $FIELDS['EndDate'], $FIELDS['StartTime'], $FIELDS['EndTime'], $FIELDS['Days'], $FIELDS['RoomID'], $FIELDS['ClassLimit'], $FIELDS['Description'], $FIELDS['CourseID'], $FIELDS['Prerequisites'], $FIELDS['Corequisites'], $FIELDS['YrTrim']);	
	db_query($sql,DB_NAME,$conn);
	
	//Check if saved
	if(db_affected_rows($conn)){
		$_SESSION['MSG'] = ConfirmMessage("New unit has been added successfully");
	}else{
		$_SESSION['MSG'] = ErrorMessage("Failed to save successfully. Please try again later...");
	}
	redirect("admin.php?tab=4");
}

function sql_update($FIELDS){
	global $conn;
	
	//Update unit
	$sql = sprintf("UPDATE `".DB_PREFIX."units` SET `UnitID` = '%s',`UName` = '%s',`Credit` = '%s',`TuitionFee` = '%s',`StartDate` = '%s',`EndDate` = '%s',`StartTime` = '%s',`EndTime` = '%s',`Days` = '%s',`RoomID` = '%s',`ClassLimit` = '%s', `Description` = '%s',`CourseID` = '%s',`Prerequisites` = '%s',`Corequisites` = '%s',`YrTrim` = '%s' WHERE " .primarykeycondition(). "", $FIELDS['UnitID'], $FIELDS['UName'], $FIELDS['Credit'], $FIELDS['TuitionFee'], $FIELDS['StartDate'], $FIELDS['EndDate'], $FIELDS['StartTime'], $FIELDS['EndTime'], $FIELDS['Days'], $FIELDS['RoomID'], $FIELDS['ClassLimit'], $FIELDS['Description'], $FIELDS['CourseID'], $FIELDS['Prerequisites'], $FIELDS['Corequisites'], $FIELDS['YrTrim']);		
	db_query($sql,DB_NAME,$conn);
	
	//Check if updated
	if(db_affected_rows($conn)){
		$_SESSION['MSG'] = ConfirmMessage("Unit has been updated successfully.");
	}else{
		$_SESSION['MSG'] = WarnMessage("No changes made!");
	}
	redirect("admin.php?tab=4");
}

function sql_update_status($disabledFlag, $editID){
	global $conn;
	
	//Update unit
	$sql = sprintf("UPDATE `".DB_PREFIX."units` SET `disabledFlag` = %d WHERE `UID` = %d LIMIT 1", $disabledFlag, $editID);
	db_query($sql,DB_NAME,$conn);

	
	//Check if updated
	if(db_affected_rows($conn)){
		$_SESSION['MSG'] = ConfirmMessage("Unit has been updated successfully.");
	}else{
		$_SESSION['MSG'] = WarnMessage("No changes made!");
	}
	redirect("admin.php?tab=4");
}

function sql_delete(){
	global $conn;
	
	$sql = "DELETE FROM `".DB_PREFIX."units` WHERE " .primarykeycondition();
	db_query($sql,DB_NAME,$conn);
	
	//Check if saved
	if(db_affected_rows($conn)){
		$_SESSION['MSG'] = ConfirmMessage("Unit has been deleted successfully");
	}else{
		$_SESSION['MSG'] = ErrorMessage("Failed to delete selected unit. Please try again later...");
	}
	redirect("admin.php?tab=4");
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