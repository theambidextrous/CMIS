<?php
//Import the PHPMailer class into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
?>
<script language="javascript" type="text/javascript">
<!--
//Define page title
document.title = "<?=SYSTEM_SHORT_NAME?> | Students";
//-->
</script>

<div class="row">
  <div class="col-lg-12">
    <h1 class="page-header">Unit Enrollments</h1>
  </div>
  <!-- /.col-lg-12 --> 
</div>
<!-- /.row -->

<div class="row">
  <div class="col-lg-12">
    <div class="panel panel-default">
      <div class="panel-heading"> <i class="fa fa-graduation-cap fa-fw"></i> Unit Enrollments </div>
      <!-- /.panel-heading -->
      <div class="panel-body" id="multi-tabs">
			
        <ul class="nav nav-tabs cookie">
          <li class="active"><a data-toggle="tab" href="#tabs-1" title="Unit Enrollments"><span>Unit Enrollments</span></a></li>
        </ul>
        <div class="tab-content">
          <div id="tabs-1" class="tab-pane active">
            <!--Begin Forms-->
            <?php
            $a = isset($_GET["task"])?$_GET["task"]:"";
            $recid = intval(! empty($_GET['recid']))?$_GET['recid']:0;            
            $UnitID = !empty($_GET['UnitID'])?$_GET['UnitID']:"";
            
            switch ($a) {
            case "add":
              //addrec();
              break;
            case "view":
              viewrec($UnitID, $recid);
              break;
            case "edit":
              //editrec($recid);
              break;
            case "del":
              //deleterec($recid);
              break;
            default:
              select();
              break;
            }		
            ?>
            <!--End Forms-->
          </div>
        </div>
        <!-- /.tab-content -->
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
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li class="active">Unit Enrollments</li></ol>

<div id="hideMsg"><?php if(isset($_SESSION['MSG'])) echo $_SESSION['MSG'];?></div>

<table width="100%" class="display table table-striped table-bordered table-hover">
<thead>
<tr>
<th class="rid">#Count</th>
<th>Unit ID</th>
<th>Unit</th>
<th>Unit Course</th>
<th>Enrolments</th>
</tr>
</thead>
<tbody>
<?php
for ($i = 0; $i < $count; $i++){
	$row = db_fetch_array($res);
	if(getEnrolments($row["UnitID"])>0){
?>
<tr>
<td><?=$row["UID"]?></td>
<td><?=$row["UnitID"]?></td>
<td><?=$row["UName"]?></td>
<td><?=$row["CourseID"]?></td>
<td><a href="admin.php?dispatcher=unitenrolments&task=view&recid=<?=$row["UID"]?>&UnitID=<?=$row['UnitID'] ?>">View Enrolments(<?=getEnrolments($row["UnitID"])?>)</a></td>
</tr>        
<?php
	}
}
db_free_result($res);
?>
</tbody>
</table>
<?php 
unset($_SESSION['MSG']);
} 

// function showrowdetailed(array $row, $recid){
// global $conn,$class_dir;
// } 
?>

<?php function showrecnav($a, $recid, $count) { ?>
<div class="quick-nav btn-group">
<a class="btn btn-default" href="admin.php?dispatcher=unitenrolments"><i class="fa fa-undo fa-fw"></i> Back to Enrollments</a>
<?php if ($recid > 0) { ?>
<a class="btn btn-default" href="admin.php?dispatcher=unitenrolments&task=<?=$a ?>&recid=<?=$recid - 1 ?>"><i class="fa fa-arrow-left fa-fw"></i> Prior Record</a>
<?php } if ($recid < $count - 1) { ?>
<a class="btn btn-default" href="admin.php?dispatcher=unitenrolments&task=<?=$a ?>&recid=<?=$recid + 1 ?>"><i class="fa fa-arrow-right fa-fw"></i> Next Record</a>
<?php } ?>
</div>
<?php } ?>

<?php 
function viewrec($UnitID, $recid){
	global $conn,$class_dir;
	$count = 0;
	$row = array();
	$sql = "SELECT * FROM `".DB_PREFIX."units_registered` WHERE `UnitID` = '$UnitID'";	
	$resultGet = db_query($sql,DB_NAME,$conn);	
		while( $rowGet = db_fetch_array($resultGet) ){
		$row[] = $rowGet;
		}
	$count = count($row);
	?>
	<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?dispatcher=unitenrolments">Unit Enrollments</a></li><li class="active">View Unit Enrollments</li></ol>
	<?php 
	showrecnav("view", $recid, $count);
	//showrowdetailed(sql_select_enrollments($UnitID), $recid);
	?>
	<div id="hideMsg"><?php if(sizeof($ERRORS['MSG'])>0) echo $ERRORS['MSG'];?></div>

<div class="head-details">
<h2 class="text-uppercase text-primary"><?=getUnitName($UnitID); ?> Enrollments</h2>
</div>
<div id="adv-tab-container">
  <ul class="nav nav-tabs">
      <li class="active"><a data-toggle="tab" href="#sub-tabs-1" title="<?=SYSTEM_SHORT_NAME?>">Enrollments</a></li>
  </ul>
  <div class="tab-content">
    <!--sub-tabs-1-->
    <div id="sub-tabs-1" class="tab-pane active">
      <h3>Enrollments</h3>
      <div class="row">
        <div class="col-md-12">
			<table width="100%" class="display table table-striped table-bordered table-hover">
			<thead>
			<tr>
			<th class="rid">#Code</th>
			<th>Student ID</th>
			<th>Student Name</th>
			<th>Email</th>
			<th>Course</th>
			<th>Enrollment Status</th>
			<th>Action</th>
			</tr>
			</thead>
			<tbody>
			<?php
			//print_r($row);
			 foreach( $row as $r): 
				if( $r['Status'] == 'Pending'){
					$manage = '<a href="">Manage</a>';
				}else{ $manage = '<a href="\">Manage</a>'; }
				?>
			<tr>
			<td><?=$r["UID"]?></td>
			<td><?=$r["StudentID"]?></td>
			<td><?=getStudentData($r["StudentID"])['FName'].' '.getStudentData($r["StudentID"])['LName']?></td>
			<td><?=getStudentData($r["StudentID"])['Email']?></td>
			<td><?=getStudentData($r["StudentID"])['Courses']?></td>
			<td><?=$r["Status"]?></td>
			<td><?php echo $manage; ?></td>
			</tr>
			<?php endforeach; ?>        
			</tbody>
			</table>
        </div>
      </div>
    </div>
  </div>
</div>
	<?php
	//db_free_result($res);
} 

function sql_select(){
	global $conn;
	global $filter;
	global $filterfield;
	
	$filterstr = isset($filter) ? "%". $filter ."%" : "";	
	$sql = "SELECT * FROM `".DB_PREFIX."units` WHERE disabledFlag = 0 AND deletedFlag = 0";	
	$res = db_query($sql,DB_NAME,$conn);
	return $res;
}

function sql_getrecordcount(){
	global $conn;
	global $filter;
	global $filterfield;
	
	$filterstr = isset($filter) ? "%". $filter ."%" : "";	
	$sql = "SELECT COUNT(*) FROM `".DB_PREFIX."units`";
	$res = db_query($sql,DB_NAME,$conn);
	$row = db_fetch_array($res);
	reset($row);
	return current($row);
}

function sql_update_status($disabledFlag, $editID){
	global $conn;
	
	//Update student
	$sql = sprintf("UPDATE `".DB_PREFIX."students` SET `disabledFlag` = %d WHERE `UID` = %d LIMIT 1", $disabledFlag, $editID);
	db_query($sql,DB_NAME,$conn);
	
	//Check if updated
	if(db_affected_rows($conn)>0){
		$_SESSION['MSG'] = ConfirmMessage("Student has been updated successfully.");
	}else{
		$_SESSION['MSG'] = WarnMessage("No changes made!");
	}
	redirect("admin.php?dispatcher=students");
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