<script language="javascript" type="text/javascript">
//Define page title
document.title = "<?=SYSTEM_SHORT_NAME?> | Payments";
</script>

<div class="row">
  <div class="col-lg-12">
    <h1 class="page-header">Payments</h1>
  </div>
  <!-- /.col-lg-12 --> 
</div>
<!-- /.row -->

<div class="row">
  <div class="col-lg-12">
    <div class="panel panel-default">
      <div class="panel-heading"> <i class="fa fa-credit-card fa-fw"></i> Manage Payments </div>
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
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li class="active">Available Payments</li></ol>

<div id="hideMsg"><?php if(isset($_SESSION['MSG'])) echo $_SESSION['MSG'];?></div>

<?php showpagenav($pagecount); ?>
<table width="100%" class="display table table-striped table-bordered table-hover">
<thead>
<tr>
<th class="rid">#</th>
<th>Student ID</th>
<th>Description</th>
<th>Amount Paid</th>
<th>Amount Due</th>
<th>Payment Method</th>
<th>Date Paid</th>
<th>Status</th>
<th class="no-sort">Actions</th>
</tr>
</thead>
<tbody>
<?php
for ($i = 0; $i < $count; $i++){
	$row = db_fetch_array($res);
?>
<tr>
<td><?=$i+1;?></td>
<td><a href="?tab=5&filter_field=StudentID&filter=<?=$row["student_id"]?>"><?=$row["student_id"]?></a></td>
<td><?=$row["pay_type"]?></td>
<td style="text-align:right"><?=$row["payment_amount"]?></td>
<td style="text-align:right"><?=$row["payment_due"]?></td>
<td><?=$row["pay_method"]?></td>
<td><?=fixdatelong($row["pay_time"])?></td>
<td><?=$row["pay_status"]?></td>
<td><a href="admin.php?tab=9&task=view&recid=<?=$i ?>">View</a></td>
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
<td width="30%">Amount Paid</td>
<td><?=$row["payment_amount"]; ?></td>
</tr>
<tr>
<td width="30%">Amount Due</td>
<td><?=$row["payment_due"]; ?></td>
</tr>
<tr>
<td>Payment Ref</td>
<td><?=$row["transaction_tracking_id"]; ?></td>
</tr>
<tr>
<td>Payment For</td>
<td><?=$row["student_id"]; ?></td>
</tr>
<tr>
<td>Payment Method</td>
<td><?=$row["pay_method"]; ?></td>
</tr>
<tr>
<td>Payment Status</td>
<td><?=$row["pay_status"]; ?></td>
</tr>
<tr>
<td>Date Paid</td>
<td><?=$row["pay_time"]; ?></td>
</tr>
</table>
</div>
<?php } ?>

<?php 
function showroweditor($row, $iseditmode, $ERRORS){
  global $a;  
?>
<p class="text-center lead"><strong><?=strtoupper($a)?> PAYMENT</strong></p>
<p class="text-center small"><span class="text-danger">FIELDS MARKED WITH ASTERISKS (*) ARE REQUIRED</span></p>


<p class="text-center">This module is pending completion... Please check back again.</p>

<?php } ?>

<?php
function showpagenav() {
?>
<div class="quick-nav btn-group">
<a class="btn btn-primary" href="admin.php?tab=9&task=add">Add Payment</a>
<a class="btn btn-default" href="admin.php?tab=9&task=reset">Reset Filters</a>
</div>
<?php } ?>

<?php function showrecnav($a, $recid, $count) { ?>
<div class="quick-nav btn-group">
<a class="btn btn-default" href="admin.php?tab=9"><i class="fa fa-undo fa-fw"></i> Back to Payments</a>
<?php if ($recid > 0) { ?>
<a class="btn btn-default" href="admin.php?tab=9&task=<?=$a ?>&recid=<?=$recid - 1 ?>"><i class="fa fa-arrow-left fa-fw"></i> Prior Record</a>
<?php } if ($recid < $count - 1) { ?>
<a class="btn btn-default" href="admin.php?tab=9&task=<?=$a ?>&recid=<?=$recid + 1 ?>"><i class="fa fa-arrow-right fa-fw"></i> Next Record</a>
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
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?tab=9">Payments</a></li><li class="active">View Payment</li></ol>
<?php 
showrecnav("view", $recid, $count);
showrow($row, $recid);
?>
<div class="quick-nav btn-group">
<a class="btn btn-default" href="admin.php?tab=9&task=add"><i class="fa fa-file-o fa-fw"></i> Add Payment</a>
<a class="btn btn-default" href="admin.php?tab=9&task=edit&recid=<?=$recid ?>"><i class="fa fa-pencil-square-o fa-fw"></i> Edit Payment</a>
<a class="btn btn-default" href="admin.php?tab=9&task=del&recid=<?=$recid ?>"><i class="fa fa-trash-o fa-fw"></i> Delete Payment</a>
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
		// Payment info
		$FIELDS['DeptID'] = secure_string(whitespace_trim(strtoupper($_POST['DeptID'])));
		$FIELDS['DName'] = secure_string(ucwords($_POST['DName']));
		$FIELDS['Description'] = secure_string($_POST['Description']);
		$FIELDS['HOD'] = secure_string($_POST['HOD']);
		
		// Validator data
		$check = new validator();
		// validate entry
		// validate "DName" field
		if(!$check->is_String($FIELDS['DeptID']))
		$ERRORS['DeptID'] = "Valid payment ID is required";
		// validate "DName" field
		if(!$check->is_String($FIELDS['DName']))
		$ERRORS['DName'] = "Valid payment name is required";
		//Check if this payment is already registered	
		$checkDuplicateSql = sprintf("SELECT `DeptID` FROM `".DB_PREFIX."payment_refs` WHERE `DeptID` = '%s'", $FIELDS['DeptID']);
		//check if any results were returned
		if(checkDuplicateEntry($checkDuplicateSql)){
			$ERRORS['DeptID'] = "A payment with similar ID already exists!";
		}
		//Check if this payment is already registered	
		$checkDuplicateSql2 = sprintf("SELECT `DName` FROM `".DB_PREFIX."payment_refs` WHERE `DName` = '%s'", $FIELDS['DName']);
		//check if any results were returned
		if(checkDuplicateEntry($checkDuplicateSql2)){
			$ERRORS['DName'] = "A payment with similar name already exists!";
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
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?tab=9">Payments</a></li><li class="active">Add Payment</li></ol>

<a class="btn btn-default" href="admin.php?tab=9"><i class="fa fa-undo fa-fw"></i> Back to Payments</a>

<p class="text-center"><?php if(sizeof($ERRORS['MSG'])>0) echo $ERRORS['MSG'];?></p>
<form id="validateform" enctype="multipart/form-data" action="admin.php?tab=9&task=add" method="post">
<input type="hidden" name="sql" value="insert">
<?php
showroweditor($row, false, $ERRORS);
?>

<p class="text-center">
<input class="btn btn-primary" type="submit" name="Add" value="Save">
<input class="btn btn-default" type="button" name="cancel" value="Cancel" onclick="javascript:location.href='admin.php?tab=9'">
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
		// Payment info
		$FIELDS['DeptID'] = secure_string(whitespace_trim(strtoupper($_POST['DeptID'])));
		$FIELDS['DName'] = secure_string(ucwords($_POST['DName']));
		$FIELDS['Description'] = secure_string($_POST['Description']);
		$FIELDS['HOD'] = secure_string($_POST['HOD']);
		
		// Validator data
		$check = new validator();
		// validate entry
		// validate "DeptID" field
		if(!$check->is_String($FIELDS['DeptID']))
		$ERRORS['DeptID'] = "Valid payment ID is required";
		// validate "DName" field
		if(!$check->is_String($FIELDS['DName']))
		$ERRORS['DName'] = "Valid payment name is required";
		
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
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?tab=9">Payments</a></li><li class="active">Edit Payment</li></ol>
<?php showrecnav("edit", $recid, $count); ?>
<form id="validateform" enctype="multipart/form-data" action="admin.php?tab=9&task=edit&recid=<?=$recid?>" method="post">
<p class="text-center"><?php if(sizeof($ERRORS['MSG'])>0) echo $ERRORS['MSG'];?></p>
<input type="hidden" name="sql" value="update">
<input type="hidden" name="eid" value="<?=$row["UID"] ?>">
<?php showroweditor($row, true, $ERRORS); ?>
<p class="text-center">
<input class="btn btn-primary" type="submit" name="Edit" value="Save">
<input class="btn btn-default" type="button" name="cancel" value="Cancel" onclick="javascript:location.href='admin.php?tab=9'">
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
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?tab=9">Payments</a></li><li>Delete Payment</li></ol>
<?php showrecnav("del", $recid, $count); ?>
<form action="admin.php?tab=9&task=del&recid=<?=$recid?>" method="post">
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
		
	$sql = "SELECT `pay_s_id`, `student_id`, `student_pay_ref`, `transaction_tracking_id`, `payment_amount`, `pay_method`, `stud_tel`, `stud_full_name`, `stud_email`, `pay_type`, `pay_status`, `pay_time` FROM `".DB_PREFIX."payment_refs` ORDER BY `pay_time` DESC";	
	$res = db_query($sql,DB_NAME,$conn);	
	return $res;
}

function sql_getrecordcount(){
	global $conn;
	
	$sql = "SELECT COUNT(*) FROM `".DB_PREFIX."payment_refs`";	
	$res = db_query($sql,DB_NAME,$conn);
	$row = db_fetch_array($res);
	reset($row);
	return current($row);
}

function sql_insert($FIELDS){
	global $conn;
	

}

function sql_update($FIELDS){
	global $conn;
	
	
}

function sql_update_status($disabledFlag, $editID){
	global $conn;
	
	
}

function sql_delete(){
	global $conn;
	
	
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