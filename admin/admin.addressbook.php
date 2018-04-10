<?php
//Import the PHPMailer class into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
?>
<script type="text/javascript">
<!--//
//Define page title
document.title = "<?=SYSTEM_SHORT_NAME?> | Address Book";
//-->
</script>

<div class="row">
  <div class="col-lg-12">
    <h1 class="page-header">Address Book</h1>
  </div>
  <!-- /.col-lg-12 --> 
</div>
<!-- /.row -->

<div class="row">
  <div class="col-lg-12">
    <div class="panel panel-default">
      <div class="panel-heading"> <i class="fa fa-address-book fa-fw"></i> Manage Address Book </div>
      <!-- /.panel-heading -->
      <div class="panel-body">
        <!--Begin Forms-->
		<?php
	// 	//some code to filter phones
	// 	$sql = sprintf("SELECT * FROM `".DB_PREFIX."addressbook` WHERE 1");
	// 	$res = db_query($sql,DB_NAME,$conn);
	// 	//define values to update db
	// 	$airtel = "";
	// 	$saf =  "";
	// 	$ext = "";
	// 	$other = "";
	// 	$id = 0;
	// 	while($row = db_fetch_array($res)){
	// 		//get phone string
	// 		$phoneString = $row['PhoneString'];
	// 		//TESTS
	// 		//remove -
	// 		$phoneString= str_replace("-","",$phoneString);
	// 		//remove double comma,
	// 		$phoneString= str_replace(",,",",",$phoneString);
	// 		//remove space
	// 		$phoneString= str_replace(" ","",$phoneString);
	// 		//remove noninumeric and .
	// 		$phoneString = preg_replace("/[^0-9.()]/", ",", $phoneString);
  //   //explode by comma
	// 		$array = explode(',', trim($phoneString));
	// 	//	print_r(array_unique($array));
	//  //for each array item check if is saf or airtel or ext
	// // echo '<br>';
	// 	foreach(array_unique($array) as $no):
	// 		if( substr( $no, 0, 3 ) === "073" && strlen($no) == 10){
	// 		//	echo $no." is airtel<br>";
	// 		$airtel = $no;
	// 		}
	// 		elseif(  substr( $no, 0, 2 ) === "07" && strlen($no) == 10){
	// 			if(substr( $no, 0, 3 ) !== "073"){
	// 		//	echo $no." is saf<br>";
	// 		$saf = $no;
	// 			}
	// 		}elseif( substr( $no, 0, 2 ) !== "07" && strlen($no) < 10){
	// 		//echo $no." is extension<br>";
	// 		$ext = $no;
	// 		}elseif( substr( $no, 0, 4 ) === "2547"){
	// 			if(substr( $no, 0, 5 ) !== "25473"){
	// 			$saf = $no;
	// 			}else{
	// 				$airtel = $no;	
	// 			}
	// 		}else{
	// 		//	echo " is not phone<br>";
	// 		$other = $no;
	// 		}
	// 	endforeach;
	// 	$id = $row['UID'];
	// 	if(empty($saf)){
	// 		$saf = $airtel;
	// 	}
	// 	if(empty($airtel)){
	// 	$airtel = $saf;
	// 	}
	// 	//update db
	// 	// $sql = sprintf("UPDATE `".DB_PREFIX."addressbook` SET  `PhoneOne` = '%s', `PhoneTwo` = '%s', `PhoneThree` = '%s', `Other` = '%s' WHERE `UID`= %d ", $saf, $airtel, $ext, $other, $id);
	// 	db_query(sprintf("UPDATE `".DB_PREFIX."addressbook` SET  `PhoneOne` = '%s', `PhoneTwo` = '%s', `PhoneThree` = '%s', `Other` = '%s' WHERE `UID`= %d ", $saf, $airtel, $ext, $other, $id),DB_NAME,$conn);
		
	// 	//unset values
	// 	$airtel = "";
	// 	$saf =  "";
	// 	$ext = "";
	// 	$other = "";
	// 	$id = 0;

	// } // END WHILE

//exit;
        $a = isset($_GET["task"])?$_GET["task"]:"";
        $recid = intval(! empty($_GET['recid']))?$_GET['recid']:0;
        
        switch ($a) {
        case "add":
          addrec();
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
	global $conn;
	$res = sql_select();
	$count = sql_getrecordcount();	
?>
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li>Address Book</li></ol>
<div id="hideMsg"><?php if(isset($_SESSION['MSG'])) echo $_SESSION['MSG'];?></div>
<?php showpagenav($pagecount); ?>
<table width="100%" class="display table table-striped table-bordered table-hover">
<thead>
<tr>
<th>First Name</th>
<th>Other Name</th>
<th>Email</th>
<th>Phone(1)</th>
<th>Phone(2)</th>
<th>Phone(3)</th>
<th class="no-sort">Actions</th>
</tr>
</thead>
<tbody>
<?php
	for ($i = 0; $i < $count; $i++){
	$row = db_fetch_array($res);
	?>
	<tr>	
	<td><?=$row["Fname"]?></td>
	<td><?=$row["Mname"]?></td>
	<td><?=$row["Email"]?></td>
	<td><?=$row['PhoneOne']?></td>
	<td><?=$row['PhoneTwo']?></td>
	<td><?=$row["PhoneThree"]?></td>
	<td><a href="admin.php?dispatcher=addressbook&task=view&recid=<?=$row['UID'] ?>">View</a> | <a href="admin.php?dispatcher=addressbook&task=del&recid=<?=$row['UID'] ?>">Delete</a></td>
	</tr>
	<?php
	}
	db_free_result($res);
}
?>
</tbody>
</table>
<?php 
showpagenav($pagecount);
unset($_SESSION['MSG']);
?>
<?php 
function showrow($row, $recid){
$ToMsgEmails = explode(" ", $row["ToAdd"]);
?>
<div class="table-responsive">
<table class="table table-bordered table-striped">
<tr>
<td>From</td>
<td><?=$row["FromAdd"]; ?></td>
</tr>
<tr>
<td>To</td>
<td><?=implode(", ", $ToMsgEmails)?></td>
</tr>
<tr>
<td>Subject</td>
<td><?=$row["Subject"]; ?></td>
</tr>
</table>
</div>
<?php } ?>

<?php 
function showrowdetailed($row, $recid){

} ?>

<?php 
function showroweditor($row, $iseditmode, $ERRORS){
global $a;  
?>
<p class="text-center lead"><strong><?=strtoupper($a)?> Contact</strong></p>
<p class="text-center small"><span class="text-danger">FIELDS MARKED WITH ASTERISKS (*) ARE REQUIRED</span></p>
<p class="text-center small">You are sending the message as <?=$row['FromAdd']?></p>

<div class="form-group">  
  <label class="control-label col-sm-2" for="">To: <span class="text-danger">*</span></label>
  <div class="col-sm-10"><input <?=$ERRORS['ToAdd']?> type="text" name="ToAdd" value="<?=$row['ToAdd']?>" class="form-control" aria-describedby="helpBlockToAdd" style="width:40%;"><span id="helpBlockToAdd" class="help-block"><small>Separate multiple emails with commas e.g. test1@example.com, test2@example.com</small></span></div>
</div>

<div class="form-group">
  <label class="control-label col-sm-2" for="">Cc:</label>
  <div class="col-sm-10"><input <?=$ERRORS['CcAdd']?> type="text" name="CcAdd" value="<?=$row['CcAdd']?>" class="form-control" aria-describedby="helpBlockCcAdd" style="width:40%;"><span id="helpBlockCcAdd" class="help-block"><small>Separate multiple emails with commas e.g. test1@example.com, test2@example.com</small></span></div>
</div>

<div class="form-group">
  <label class="control-label col-sm-2" for="">Bcc:</label>
  <div class="col-sm-10"><input <?=$ERRORS['BccAdd']?> type="text" name="BccAdd" value="<?=$row['BccAdd']?>" class="form-control" aria-describedby="helpBlockBccAdd" style="width:40%;"><span id="helpBlockBccAdd" class="help-block"><small>Separate multiple emails with commas e.g. test1@example.com, test2@example.com</small></span></div>
</div>

<div class="form-group">
  <label class="control-label col-sm-2" for="">Subject: <span class="text-danger">*</span></label>
  <div class="col-sm-10"><input <?=$ERRORS['Subject']?> type="text" name="Subject" value="<?=$row['Subject']?>" class="form-control" style="width:40%;"></div>
</div>

<div class="form-group">
  <label class="control-label col-sm-2" for="">Attachments:</label>
  <div class="col-sm-10"><input id="msg_attachments" type="file" name="file[]"><span id="helpBlockBccAdd" class="help-block"><small>(Max 5 files)</small></span></div>
  <div class="files-list">
    <div class="col-sm-2"></div>
    <div class="col-sm-10">
      <ul id="files_list_msg" class="nav"></ul>
    </div>
  </div>  
</div> 
<div class="form-group">
  <div class="col-sm-12"><textarea id="message" name="Message" class="tinymce"><?=$row['Message']?></textarea></div>
</div>

<?php } ?>

<?php
function showpagenav() {
  global $folder;
?>
<div class="quick-nav btn-group">
<a class="btn btn-primary" href="admin.php?dispatcher=addressbook&task=add">Add Contact</a>
<a class="btn btn-default" href="admin.php?dispatcher=addressbook&task=reset">Reset Filters</a>
</div>
<?php } ?>

<?php function showrecnav($a, $recid, $count) { ?>
<div class="quick-nav btn-group">
<a class="btn btn-default" href="admin.php?dispatcher=addressbook"><i class="fa fa-undo fa-fw"></i> Back to Address book</a>
<?php if ($recid > 0) { ?>
<a class="btn btn-default" href="admin.php?dispatcher=addressbook&task=<?=$a ?>&recid=<?=$recid - 1 ?>"><i class="fa fa-arrow-left fa-fw"></i> Prior Record</a>
<?php } if ($recid < $count - 1) { ?>
<a class="btn btn-default" href="admin.php?dispatcher=addressbook&task=<?=$a ?>&recid=<?=$recid + 1 ?>"><i class="fa fa-arrow-right fa-fw"></i> Next Record</a>
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
	<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?dispatcher=addressbook">Address book</a></li><li>View Cotact</li></ol>
	<?php 
	showrecnav("view", $recid, $count);
	showrowdetailed($row, $recid);
	db_free_result($res);
} 
?>

<?php 
function addrec() {

} ?>

<?php 
function editrec($recid, $action){

} 
?>

<?php 
function deleterec($recid){
	global $conn;
		$sql = "UPDATE `".DB_PREFIX."addressbook` SET `deletedFlag` = 1 WHERE `UID` = '$recid' ";
		db_query($sql,DB_NAME,$conn);
		$_SESSION['MSG'] = ConfirmMessage("Record Deleted");
			redirect("admin.php?dispatcher=addressbook");
	return null;
}
?>
<?php
function sql_select(){
	global $conn;
		$sql = "SELECT * FROM `".DB_PREFIX."addressbook` WHERE `deletedFlag` = 0";
		$res = db_query($sql,DB_NAME,$conn);
	return $res;
}

function sql_getrecordcount(){
	global $conn;
		$sql = "SELECT COUNT(*) FROM `".DB_PREFIX."addressbook` WHERE `deletedFlag` = 0";		
		$res = db_query($sql,DB_NAME,$conn);
		$row = db_fetch_array($res);
		reset($row);	
	return current($row);
}

function sql_insert($FIELDS){
	
}

function sql_delete(){
	global $conn;
	$sql = "DELETE FROM `".DB_PREFIX."addressbook` WHERE " .primarykeycondition();
	db_query($sql,DB_NAME,$conn);
	
	//Check if saved
	if(db_affected_rows($conn)){
		$_SESSION['MSG'] = ConfirmMessage("Record has been deleted successfully");
	}else{
		$_SESSION['MSG'] = ErrorMessage("Failed to delete selected record. Please try again later...");
	}
	redirect("admin.php?dispatcher=addressbook");
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