<?php
//Import the PHPMailer class into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
?>
<script type="text/javascript">
<!--//
//Define page title
document.title = "<?=SYSTEM_SHORT_NAME?> | SMS";
//-->
</script>

<div class="row">
  <div class="col-lg-12">
    <h1 class="page-header">SMS</h1>
  </div>
  <!-- /.col-lg-12 --> 
</div>
<!-- /.row -->

<div class="row">
  <div class="col-lg-12">
    <div class="panel panel-default">
      <div class="panel-heading"> <i class="fa fa-weixin fa-fw"></i> Manage SMS </div>
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
        case "reply":
          editrec($recid, "reply");
          break;
        case "replyall":
          editrec($recid, "replyall");
          break;
        case "del":
          deleterec($recid);
          break;
        default:
          select();
          break;
        }
        
        if (!empty($folder)) $_SESSION["folder"] = $folder;
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
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li>SMS</li></ol>
<div id="hideMsg"><?php if(isset($_SESSION['MSG'])) echo $_SESSION['MSG'];?></div>
<?php showpagenav($pagecount); ?>
<table width="100%" class="display table table-striped table-bordered table-hover">
<thead>
<tr>
<th>#ID</th>
<th>SMS</th>
<th>Sent From</th>
<th>Sent To</th>
<th>Sent By</th>
<th>Date Sent</th>
<th class="no-sort">Actions</th>
</tr>
</thead>
<tbody>
<?php
	while($row = db_fetch_array($res)){
	?>
	<tr>	
	<td><?=$row["UID"]?></td>
	<td><?=$row["SMS"]?></td>
	<td><?=$row["SentFrom"]?></td>
	<td><?=TrimSentTo($row['SentTo'])?></td>
	<td><?=$row['SentBy']?></td>
	<td><?=date("Y-m-d H:i:s a", strtotime($row["SentDate"]))?></td>
	<td><a href="admin.php?dispatcher=sms&task=view&recid=<?=$row['UID'] ?>">View</a> | <a href="admin.php?dispatcher=sms&task=del&recid=<?=$row['UID'] ?>">Resend</a></td>
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

 } 
 ?>

<?php 
function showrowdetailed($row, $recid){

} ?>

<?php 
function showroweditor($row, $iseditmode, $ERRORS){
global $a;  
?>
<p class="text-center lead"><strong>Compose New SMS</strong></p>
<p class="text-center small"><span class="text-danger">FIELDS MARKED WITH ASTERISKS (*) ARE REQUIRED</span></p>
<div class="row">

  <div class="col-md-6">
    <div class="form-group">
    <label for="">SMS Subject Line: <span class="text-danger">*</span></label>
    <input <?=$ERRORS['Subject']?> type="text" value="<?=$row['Subject']; ?>" name="Subject" class="form-control required" />
    </div>
  </div>

  <div class="col-md-12">
    <div class="form-group">
    <label for="">SMS Body(This is the content to be sent)</label>
    <textarea <?=$ERRORS['SMS']?> name="SMS" class="form-control" >Hi [NAME]</textarea>
    </div>
  </div>
  </div>

  <div class="row">
  <div class="col-md-6">
    <div class="form-group">
    <label for="">Sent By (Logged in user is default: <span class="text-danger">*</span></label>
    <input <?=$ERRORS['SentBy']?> type="text" value="<?=$_SESSION['sysUsername']; ?>" name="SentBy" class="form-control required" />
    </div>
  </div>

  <div class="col-md-6">
  <div class="form-group">
  <label for="">Sent From (This is the sender Shortcode)</label>
  <?php echo sqlOption("SELECT `UID`,`ShortCode` FROM `".DB_PREFIX."sms_shortcodes` WHERE 1","ShortCode",$row['SentFrom']);?>
  </div>
  </div>
  </div>
  <div class="row">
  <div class="col-md-12">
  <div class="form-group">
  <label for="">Sent To (Select recipients)</label>
  <!-- multiselect new  --> 
<script type="text/javascript">
	$(document).ready(function() {
		$('#recipients').multiselect({
			enableCollapsibleOptGroups: true,
			enableFiltering: true,
			disableIfEmpty: true,
			buttonWidth: '100%'
		});
	});
</script>
<select id="recipients" name="SentTo[]" multiple="multiple" class="form-control">
<?php 
// loop in phones
		foreach(getAddressGroups() as $a):
			echo '<option value="'.$a['ContactGroup'].'">'.$a['ContactGroup'].'</option>';
		endforeach;
	?>
</select>
  </div>
  </div>
</div>
<?php } ?>

<?php
function showpagenav() {
?>
<div class="quick-nav btn-group">
<a class="btn btn-primary" href="admin.php?dispatcher=sms&task=add">New SMS</a>
<a class="btn btn-default" href="admin.php?dispatcher=sms&task=reset">Reset Filters</a>
</div>
<?php } ?>

<?php function showrecnav($a, $recid, $count) { ?>
<div class="quick-nav btn-group">
<a class="btn btn-default" href="admin.php?dispatcher=sms"><i class="fa fa-undo fa-fw"></i> Back to SMS</a>
<?php if ($recid > 0) { ?>
<a class="btn btn-default" href="admin.php?dispatcher=sms&task=<?=$a ?>&recid=<?=$recid - 1 ?>"><i class="fa fa-arrow-left fa-fw"></i> Prior Record</a>
<?php } if ($recid < $count - 1) { ?>
<a class="btn btn-default" href="admin.php?dispatcher=sms&task=<?=$a ?>&recid=<?=$recid + 1 ?>"><i class="fa fa-arrow-right fa-fw"></i> Next Record</a>
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
	<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?dispatcher=sms">SMS</a></li><li>View SMS</li></ol>
	<?php 
	showrecnav("view", $recid, $count);
	showrowdetailed($row, $recid);
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
	$ERR = 'id="highlight"';//Error highlighter
	
	// Commands
	if(isset($_POST["Add"])){
		// sms data
		$FIELDS['Subject'] = secure_string($_POST['Subject']);	
		$FIELDS['SMS'] = secure_string($_POST['SMS']);
		$FIELDS['SentBy'] = secure_string(ucwords($_POST['SentBy']));
		$FIELDS['SentTo'] = $_POST['SentTo'];
		$FIELDS['SentFrom'] = secure_string($_POST['SentFrom']);
		$To = implode (", ", $FIELDS['SentTo']);
		
		// Validator data
		$check = new validator();
		// validate entry		
		if(empty($FIELDS['Subject']))
		$ERRORS['Subject'] = $ERR;
		if(empty($FIELDS['SMS']))
		$ERRORS['SMS'] = $ERR;
		
		if(sizeof($ERRORS) > 0){
			$ERRORS['MSG'] = ErrorMessage("PLEASE CORRECT HIGHLIGHTED FIELDS!");			
		}
		else{
			//talk to AfricasTalking. include the gateway
			require_once("$class_dir/EvarsitySMS.php");
			
			//prepare list of recipients
				$message = $FIELDS['SMS'];
				$gateway = new FinstockSMS(SMS_API_USER, SMS_API_KEY);
				$message_final = "";
				$issent = 0;
			foreach($FIELDS['SentTo'] as $group):
				$recipients = getCleanRecipients($group);
				$message_final= makeSMS($message);
				try 
					{ 
				$gateway->sendMessage($recipients, $message_final, "FinEvarsity");
					}
					catch ( FinstockSMSException $e )
					{
						$ERRORS['MSG'] = $e->getMessage();
					}
					$issent++;
			endforeach;
			//save the message sent once 
			$sql = sprintf("INSERT INTO `".DB_PREFIX."sms`(`SmsSubject`, `SMS`, `SentBy`, `SentTo`, `SentFrom`) VALUES ('%s', '%s', '%s', '%s', '%s')", $FIELDS['Subject'], $FIELDS['SMS'], $FIELDS['SentBy'], $To, $FIELDS['SentFrom']);
			db_query($sql,DB_NAME,$conn);

			if($issent > 0 ){
			$_SESSION['MSG'] = ConfirmMessage("SMS sent and a copy saved successfully");
			redirect("admin.php?dispatcher=sms");
			}else{
			$ERRORS['MSG'] = "Error sending message";
			}
			
		}
	}
		
	$row["Subject"] = !empty($FIELDS['Subject'])?$FIELDS['Subject']:"";
	$row["SMS"] = !empty($FIELDS['SMS'])?$FIELDS['SMS']:"";
	$row["SentBy"] = !empty($FIELDS['SentBy'])?$FIELDS['SentBy']:"";
	$row["SentTo"] = !empty($FIELDS['SentTo'])?$FIELDS['SentTo']:"";
	$row["SentFrom"] = !empty($FIELDS['SentFrom'])?$FIELDS['SentFrom']:"";
?>
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?dispatcher=sms">SMS</a></li><li class="active">New SMS</li></ol>

<a class="btn btn-default" href="admin.php?dispatcher=sms"><i class="fa fa-undo fa-fw"></i> Back to SMS</a>

<p class="text-center"><?php if(sizeof($ERRORS['MSG'])>0) echo $ERRORS['MSG'];?></p>
<form id="validateform" enctype="multipart/form-data" action="admin.php?dispatcher=sms&task=add" method="post">
<input type="hidden" name="sql" value="insert" />
<?php
showroweditor($row, false, $ERRORS);
?>
<p class="text-center">
<input class="btn btn-primary" type="submit" name="Add" value="Send" />
<input class="btn btn-default" type="button" name="cancel" value="Cancel" onclick="javascript:location.href='admin.php?dispatcher=sms'" />
</p>
</form>
<?php } ?>

<?php 
function editrec($recid, $action){

} 
?>

<?php 
function deleterec($recid){

}
?>
<?php
function sql_select(){
	global $conn;
		$sql = "SELECT * FROM `".DB_PREFIX."sms`";
		$res = db_query($sql,DB_NAME,$conn);
	return $res;
}

function sql_getrecordcount(){
	global $conn;
		$sql = "SELECT COUNT(*) FROM `".DB_PREFIX."sms`";		
		$res = db_query($sql,DB_NAME,$conn);
		$row = db_fetch_array($res);
		reset($row);	
	return current($row);
}

function sql_insert($FIELDS){
	
}

function sql_delete(){
	global $conn;
	
	$sql = "DELETE FROM `".DB_PREFIX."sms` WHERE " .primarykeycondition();
	db_query($sql,DB_NAME,$conn);
	
	//Check if saved
	if(db_affected_rows($conn)){
		$_SESSION['MSG'] = ConfirmMessage("SMS has been deleted successfully");
	}else{
		$_SESSION['MSG'] = ErrorMessage("Failed to delete selected SMS. Please try again later...");
	}
	redirect("admin.php?dispatcher=sms");
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