<?php
//Import the PHPMailer class into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
?>
<script language="javascript" type="text/javascript">
//Define page title
document.title = "<?=SYSTEM_SHORT_NAME?> | System User";
</script>

<div class="row">
  <div class="col-lg-12">
    <h1 class="page-header">System Users</h1>
  </div>
  <!-- /.col-lg-12 --> 
</div>
<!-- /.row -->

<div class="row">
  <div class="col-lg-12">
    <div class="panel panel-default">
      <div class="panel-heading"> <i class="fa fa-users fa-fw"></i> Manage Users </div>
      <!-- /.panel-heading -->
      <div class="panel-body">
      
        <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" href="#tabs-1" title="System Users"><span>System Users</span></a></li>
          <?php if(isSuperAdmin()) { ?>
          <li><a data-toggle="tab" href="#tabs-2"><span>Login History</span></a></li>
          <?php } ?>
        </ul>
        <div class="tab-content">
          <div id="tabs-1" class="tab-pane active">
            <!--Begin Forms-->
            <?php
              $a = isset($_GET["task"])?$_GET["task"]:"";
              $recid = intval(! empty($_GET['recid']))?$_GET['recid']:0;              
              $eid = intval(! empty($_GET['eid']))?$_GET['eid']:0; //User ID
              $user = isset($_GET['user'])?$_GET['user']:""; //User email address
              
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
              case "resetpass":
                resetrec($recid,$user);
                break;
              default:
                select();
                break;
              }		
            ?>
            <!--End Forms-->
          </div>
		  		<?php
          if(isSuperAdmin()) {
          ?>
          <div id="tabs-2" class="tab-pane">
            <!--Begin Forms-->
            <?php
              //Delete script
              if(isset($_POST['DELETE']) && isset($_POST['actionUsrID']) && isSuperAdmin()){	
                  foreach($_POST['actionUsrID'] as $selectedID){
                      $sqlDelete = sprintf("DELETE FROM `".DB_PREFIX."sys_users_logs` WHERE `logID` = %d", $selectedID);
                      //Run query
                      db_query($sqlDelete,DB_NAME,$conn);
                  }
                  $UsrMSG = ConfirmMessage("Selected user login history deleted!");
              }
              
              //Get login history for the selected user
              $userID = intval(! empty($_GET['userid']))?$_GET['userid']:0;		
              //Display login history
              if(!empty($userID)){
                  //Begin display script for selected user
                  $sqlUsrLogins = sprintf("SELECT `logID` FROM `".DB_PREFIX."sys_users_logs` WHERE `userID` = %d", $userID);
                  $rowUsrResult = db_query($sqlUsrLogins,DB_NAME,$conn);
                  $usr_num_rows = db_num_rows($rowUsrResult);
                  //set sql
                  $resSql = sprintf("SELECT `logID`,`userID`,`loginDate`,`source` FROM `".DB_PREFIX."sys_users_logs` WHERE `userID` = %d ORDER BY `loginDate` DESC LIMIT %d;", $userID, 10);
              }
              else{
                  //Begin normal display script
                  $sqlUsrLogins = "SELECT `logID` FROM `".DB_PREFIX."sys_users_logs`";
                  $rowUsrResult = db_query($sqlUsrLogins,DB_NAME,$conn);
                  $usr_num_rows = db_num_rows($rowUsrResult);
                  //set sql
                  $resSql = sprintf("SELECT `logID`,`userID`,`loginDate`,`source` FROM `".DB_PREFIX."sys_users_logs` ORDER BY `loginDate` DESC LIMIT %d;", 20);
              }		
              ?>
            <ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?tab=8">Manage Users</a></li><li class="active">Login History</li></ol>
            <div id="hideMsg"><?php if(isset($UsrMSG)) echo $UsrMSG;?></div>
            <p class="text-center">ADMIN LOGIN HISTORY</p>
            
            <form name="view" method="post" action="admin.php?tab=8&task=view&userid=<?=$userID;?>#tabs-2">
              <table width="100%" class="display table table-striped table-bordered table-hover">
                <thead>
                <tr>
                  <th>System User</th>
                  <th>Login Date</th>
                  <th>Source</th>
                  <th class="no-sort" style="text-align:center"><input type="checkbox" name="master" title="Check All" onclick="checkAll(document.getElementsByName('actionUsrID[]'));" value=""></th>
                </tr>
                </thead>                
                <?php
                  //run the query
                  $result = db_query($resSql,DB_NAME,$conn);
                  //check if any rows returned
                  if(db_num_rows($result)>0){
                  echo "<tbody>";
                  while($user_logs = db_fetch_array($result)){
                      echo "<tr>
                      <td>".getSysUsername($user_logs['userID'])."</td>
                      <td>".fixdatetime($user_logs['loginDate'])."</td>
                      <td>".$user_logs['source']."</td>
                      <td align=\"center\"><input type=\"checkbox\" id=\"actionUsrID\" name=\"actionUsrID[]\" value=\"".$user_logs['logID']."\"></td>
                      </tr>";
                  }
				  echo "</tbody>";
                ?>
                <tfoot>
                <tr>
                  <td colspan="4" align="right">                  
                  <div class="form-inline">
                  <div class="form-group">
                  <label>With Selected:&nbsp;</label>
                  <input type="submit" value="Delete" name="DELETE" class="btn btn-default">
                  </div>
                  </div>
                  </td>
                </tr>
                </tfoot>
                <?php
                  }
                ?>
                </tbody>
              </table>
            </form>
            <!--End Forms-->
          </div>
        <?php
        }
        ?>
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
	global $eid;

	$res = sql_select();
	$count = sql_getrecordcount();
	
	if(isset($_GET['enable']) && $eid){
		$disabledFlag = intval(! empty($_GET['enable']))?$_GET['enable']:0;
		
		sql_update_status($disabledFlag, $eid);
	}
?>
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?tab=8">Manage Users</a></li><li class="active">System Users</li></ol>

<div id="hideMsg"><?php echo !empty($_SESSION['MSG'])?$_SESSION['MSG']:""; ?></div>

<?php showpagenav($pagecount); ?>
<table width="100%" class="display table table-striped table-bordered table-hover">
<thead>
<tr>
<th>RID</th>
<th>Full Name</th>
<th>Username</th>
<th>User Level</th>
<th>Email</th>
<th>Logged In</th>
<th>Enabled</th>
<th class="no-sort">Actions</th>
</tr>
</thead>
<tbody>
<?php
for ($i = 0; $i < $count; $i++){
	$row = db_fetch_array($res);
?>
<tr>
<td><?=$row['ID']?></td>
<td><a href="admin.php?tab=8&task=view&recid=<?=$i ?>&userid=<?=$row['ID']?>"><?=$row['FullName']?></a></td>
<td><?=$row['Username']?></td>
<td><?=$row["UserLevel"]?></td>
<td><?=$row['Email']?></td>
<?php
if($row['loggedIn'] == 0){
	echo "<td align=\"center\"><img border=\"0\" src=\"".IMAGE_FOLDER."/icons/no.png\" height=\"12\" width=\"12\" alt=\"No\"></td>";
}else{
	echo "<td align=\"center\"><img border=\"0\" src=\"".IMAGE_FOLDER."/icons/yes.png\" height=\"12\" width=\"12\" alt=\"Yes\"></td>";
}
if(isSuperAdmin()) {
if($row['disabledFlag'] == 0){
	echo "<td align=\"center\"><a href=\"admin.php?tab=8&enable=1&eid=".$row['ID']."\" title=\"Click to disable ".$row['Username']."\"><img border=\"0\" src=\"".IMAGE_FOLDER."/icons/yes.png\" height=\"12\" width=\"12\" alt=\"Disable ".$row['Username']."\"></a></td>";
}else{
	echo "<td align=\"center\"><a href=\"admin.php?tab=8&enable=0&eid=".$row['ID']."\" title=\"Click to enable ".$row['Username']."\"><img border=\"0\" src=\"".IMAGE_FOLDER."/icons/no.png\" height=\"12\" width=\"12\" alt=\"Enable ".$row['Username']."\"></a></td>";
}
}else{
if($row['disabledFlag'] == 0){
	echo "<td align=\"center\"><img border=\"0\" src=\"".IMAGE_FOLDER."/icons/yes.png\" height=\"12\" width=\"12\" alt=\"Disable ".$row['Username']."\"></td>";
}else{
	echo "<td align=\"center\"><img border=\"0\" src=\"".IMAGE_FOLDER."/icons/no.png\" height=\"12\" width=\"12\" alt=\"Enable ".$row['Username']."\"></td>";
}
}
?>
<td><a href="admin.php?tab=8&task=view&recid=<?=$i ?>&userid=<?=$row['ID']?>">View</a> | 
<?php if(isSuperAdmin()) { ?>
 <a href="admin.php?tab=8&task=edit&recid=<?=$i ?>&userid=<?=$row['ID']?>">Edit</a> | <a href="admin.php?tab=8&task=del&recid=<?=$i ?>&userid=<?=$row['ID']?>">Delete</a>
<?php }else{ ?>
 Edit | Delete
<?php } ?>
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
<td width="30%">User ID</td>
<td><?=$row["ID"]; ?></td>
</tr>
<tr>
<td>Full Name</td>
<td><?=$row["FullName"]; ?></td>
</tr>
<tr>
<td>Username</td>
<td><?=$row["Username"]; ?></td>
</tr>
<tr>
<td>Email</td>
<td><?=$row["Email"]; ?></td>
</tr>
<tr>
<td>User Level</td>
<td><?=$row["UserLevel"]; ?></td>
</tr>
</table>
</div>
<?php } ?>

<?php 
function showroweditor($row, $iseditmode, $ERRORS){	
  global $a;
  ?>
  <p class="text-center lead"><strong><?=strtoupper($a)?> USER DETAILS</strong></p>
  <p class="text-center small"><span class="text-danger">FIELDS MARKED WITH ASTERISKS (*) ARE REQUIRED</span></p>  
  <div class="row">
    <div class="col-md-6">
      <?php if($a == 'add'){
          echo "<div class=\"form-group\">
          <label for=\"\">Username: <span class=\"text-danger\">*</span></label>
          <input ".$ERRORS['Username']." type=\"text\" value=\"".$row['Username']."\" name=\"Username\" class=\"form-control required\">&nbsp;<span class=\"text-danger\">".$ERRORS['Username']."</span>
          </div>";
      }else{
          echo "<div class=\"form-group\">
          <label for=\"\">Username: <span class=\"text-danger\">*</span></label>
          <strong>".$row['Username']."</strong>&nbsp;<span class=\"text-warning small\">(Username cannot be changed)</span>
          </div>";
      }
      ?>
      <div class="form-group">
        <label for="">First Name: <span class="text-danger">*</span></label>
        <input type="text" value="<?=$row['FirstName']; ?>" name="FirstName" class="form-control required">&nbsp;<span class="text-danger"><?=$ERRORS['FirstName'];?></span>
      </div>  
      <div class="form-group">
        <label for="">Last Name: <span class="text-danger">*</span></label>
        <input type="text" value="<?=$row['LastName']; ?>" name="LastName" class="form-control required">&nbsp;<span class="text-danger"><?=$ERRORS['LastName'];?></span>
      </div>  
      <div class="form-group">
        <label for="">Email: <span class="text-danger">*</span></label>
        <input type="text" value="<?=$row['Email']; ?>" name="Email" class="form-control required email">&nbsp;<span class="text-danger"><?=$ERRORS['Email'];?></span>
      </div>
    </div>
    <div class="col-md-6">
	  <?php
      if($a == 'add'){
          ?>
          <div class="form-group">
            <label for="">Assign Password: <span class="text-danger">*</span></label>
            <input type="password" value="<?=$row['Password']; ?>" name="Password" class="form-control required">&nbsp;<span class="text-danger"><?=$ERRORS['Password'];?></span>
          </div>
          <div class="form-group">
            <label for="">Confirm Password: <span class="text-danger">*</span></label>
            <input type="password" value="<?=$row['VerifyPass']; ?>" name="VerifyPass" class="form-control required">&nbsp;<span class="text-danger"><?=$ERRORS['VerifyPass'];?></span>
          </div>
          <?php 
      }
      else{
          ?>
          <div class="form-group">
            <label for="">Reset Password:</label>
            <span class=\"small\"><a href="admin.php?tab=8&amp;task=resetpass&amp;user=<?=$row['Email']?>">Click here to reset password for this account</a></span>
          </div>
          <?php
      }
      ?>
      
      <?php
      if(isSuperAdmin()){
          // Super users can change permissions
          ?>
          <div class="form-group">
            <label for="">Admin Level: <span class="text-danger">*</span></label>
            <select name="UserLevel" class="form-control">
            <option value="None">--Select--</option>
            <?php
            foreach(list_user_levels() as $k => $v){
                $row['UserLevel'] = !empty($row['UserLevel'])?$row['UserLevel']:"Office";
                if($k == $row['UserLevel']){
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
            <label for="">Enabled: &nbsp;</label>
            <select name="Enabled" class="form-control">
            <?php
            foreach(list_enable_status() as $k => $v){
                if($k == $row['Enabled']){
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
          <?php
      }
      else{
          // Limited users not allowed to change their permission (remains as is)
          echo "<input type=\"hidden\" name=\"UserLevel\" value=\"".$row['UserLevel']."\"><input type=\"hidden\" name=\"Enabled\" value=\"".$row['Enabled']."\">";
      }
      ?>
    </div>
  </div>
<?php
}
?>

<?php
function showpagenav() {
?>
<div class="quick-nav btn-group">
<a class="btn btn-primary" href="admin.php?tab=8&task=add">Add System User</a>
<a class="btn btn-default" href="admin.php?tab=8&task=reset">Reset Filters</a>
</div>
<?php } ?>

<?php function showrecnav($a, $recid, $count) { ?>
<div class="quick-nav btn-group">
<a class="btn btn-default" href="admin.php?tab=8"><i class="fa fa-undo fa-fw"></i> Back to Users</a>
<?php if ($recid > 0) { ?>
<a class="btn btn-default" href="admin.php?tab=8&task=<?=$a ?>&recid=<?=$recid - 1 ?>"><i class="fa fa-arrow-left fa-fw"></i> Prior Record</a>
<?php } if ($recid < $count - 1) { ?>
<a class="btn btn-default" href="admin.php?tab=8&task=<?=$a ?>&recid=<?=$recid + 1 ?>"><i class="fa fa-arrow-right fa-fw"></i> Next Record</a>
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
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?tab=8">Manage Users</a></li><li>View System User</li></ol>
<?php 
showrecnav("view", $recid, $count);
showrow($row, $recid);
?>
<table border="0" cellspacing="0" cellpadding="0">
<tr class="cms-list-table-navigation">
<td><a class="btn btn-default" href="admin.php?tab=8&task=add"><i class="fa fa-file-o fa-fw"></i>Add User</a></td>
<?php if(isSuperAdmin()) { ?>
<td><a class="btn btn-default" href="admin.php?tab=8&task=edit&recid=<?=$recid ?>&userid=<?=$row["ID"];?>"><i class="fa fa-pencil-square-o fa-fw"></i>Edit User</a></td>
<td><a class="btn btn-default" href="admin.php?tab=8&task=del&recid=<?=$recid ?>&userid=<?=$row["ID"];?>"><i class="fa fa-trash-o fa-fw"></i>Delete User</a></td>
<?php } ?>
</tr>
</table>
<?php
db_free_result($res);
} 
?>

<?php 
function addrec() { 
  	global $incl_dir,$class_dir;
	
	require_once("$class_dir/class.validator.php3");
	
	// Variables
	$ERRORS = array();
	$FIELDS = array();
	
	// Commands
	if(isset($_POST["Add"])){
		// System User info
		$FIELDS['FirstName'] = isset($_POST['FirstName'])?secure_string($_POST['FirstName']):"";
		$FIELDS['LastName'] = isset($_POST['LastName'])?secure_string($_POST['LastName']):"";
		$FIELDS['FullName'] = $FIELDS['FirstName']." ".$FIELDS['LastName'];
		$FIELDS['Username'] = isset($_POST['Username'])?secure_string($_POST['Username']):"";
		$FIELDS['Password'] = isset($_POST['Password'])?secure_string($_POST['Password']):"";
		$FIELDS['VerifyPass'] = isset($_POST['VerifyPass'])?secure_string($_POST['VerifyPass']):"";
		$FIELDS['EncryptPass'] = hashedPassword($FIELDS['VerifyPass']);
		$FIELDS['Email'] = isset($_POST['Email'])?secure_string($_POST['Email']):"";
		$FIELDS['UserLevel'] = isset($_POST['UserLevel'])?secure_string($_POST['UserLevel']):"";
		$FIELDS['Token'] = md5(time());
		$FIELDS['Enabled'] = intval(! empty($_POST['Enabled']))?secure_string($_POST['Enabled']):0;
		$FIELDS['DateCreated'] = date('Y-m-d');				
		
		// Validator data
		$check = new validator();
		// validate entry
		// validate "FirstName" field
		if(!$check->is_String($FIELDS['FirstName']))
		$ERRORS['FirstName'] = "Valid first name required";
		// validate "LastName" field
		if(!$check->is_String($FIELDS['LastName']))
		$ERRORS['LastName'] = "Valid last name required";
		// validate "Username" field
		if(!$check->is_String($FIELDS['Username']))
		$ERRORS['Username'] = "Valid username required";
		// validate "Email" field
		if (!$check->is_email($FIELDS['Email'])) 
		$ERRORS['Email'] = "A valid email address is required";
		// validate "Password" field
		if(!$check->is_password($FIELDS['Password']))
		$ERRORS['Password'] = "Password must be at least 7 letters mixed with digits and symbols";
		// validate "VerifyPass" field
		if(!$check->cmp_string($FIELDS['VerifyPass'],$FIELDS['Password']))
		$ERRORS['VerifyPass'] = "Passwords entered do not match";
		//Validate "UserLevel" field
		if($FIELDS['UserLevel'] == "None")
		$ERRORS['UserLevel'] = "At least one admin level needs to be assigned to the user";
		//Check if this username is already taken	
		$checkDuplicateSql = sprintf("SELECT `Username` FROM `".DB_PREFIX."sys_users` WHERE `Username` = '%s'", $FIELDS['Username']);
		//check if any results were returned
		if(checkDuplicateEntry($checkDuplicateSql)){
			$ERRORS['Username'] = "Username not available! Please choose another username and try again.";
		}
		
		//Check if this email address is already registered	
		$checkDuplicateSql2 = sprintf("SELECT `Email` FROM `".DB_PREFIX."sys_users` WHERE `Email` = '%s' AND `Username` != '%s'", $FIELDS['Email'], $FIELDS['Username']);
		//check if any results were returned
		if(checkDuplicateEntry($checkDuplicateSql2)){
			$ERRORS['Email'] = "This email ".$FIELDS['Email']." is already attached to another user.";
		}
		
		// check for errors
		if(sizeof($ERRORS) > 0){
			$ERRORS['MSG'] = ErrorMessage("ERRORS ENCOUNTERED!");
		}
		else{
			if(sql_insert($FIELDS)){
				//SEND NOTIFICATION//			
				// Mail function
				$mail = new PHPMailer; // defaults to using php "mail()"
				
				// Message
				$message = "<html><head>
				<title>".$FIELDS['FullName']." - Account Created</title>
				</head><body>
				<p>Dear ".$FIELDS['FullName'].", <br><br> You received this email because an account was created for you by ".SYSTEM_NAME." Administrator to allow you login to the back-end system. This email allows you to activate and set up a new password for your account by clicking on the link provided below.<br><br> <strong>User Details</strong> <br><br> Full Name: ".$FIELDS['FullName']." <br> Username: ".$FIELDS['Username']." <br><br> <strong>Activation Link</strong> <br> <a href=\"".SYSTEM_URL."/admin/?do=activate&token=".$FIELDS['Token']."\" target=\"_blank\"><strong>Click here to activate your account</strong></a><br>Once you login, check your account details and update accordingly.<br><br>Sincerely,<br><br>".strtoupper(SYSTEM_SHORT_NAME)." ALERT NOTIFICATIONS<br>Email: ".INFO_EMAIL."<br>Website: ".PARENT_HOME_URL."</p>
				</body>
				</html>";
				
				$body = preg_replace('/\\\\/','', $message); //Strip backslashes
				
				switch(MAILER){
					case 'smtp':
					$mail->isSMTP(); // telling the class to use SMTP

					$mail->SMTPAuth = SMTP_AUTH; // enable SMTP authentication
					$mail->SMTPSecure = SMTP_SECU; // sets the prefix to the servier
					$mail->Host = SMTP_HOST; // SMTP server
					$mail->Port = SMTP_PORT; // set the SMTP port for the HOST server
					$mail->Username = SMTP_USER;
					$mail->Password = SMTP_PASS;
					break;
					case 'sendmail':
					$mail->isSendmail(); // telling the class to use SendMail transport
					break;
					case 'mail':
					$mail->isMail(); // telling the class to use mail function
					break;
				}
				
				$mail->setFrom(MAILER_FROM_EMAIL, MAILER_FROM_NAME);
				$mail->Subject = $FIELDS['Username']." - Account Created";
				$mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; 		
				$mail->msgHTML($body);		
				$mail->addAddress($FIELDS['Email'], $FIELDS['FullName']);
				
				if(!$mail->Send()) {
					$saved = false;
					$ErrPage = curPageURL();
					//echo "Mailer Error: " . $mail->ErrorInfo;
					$ERRORS['MSG'] = ErrorMessage("A CRITICAL ERROR prevented the system from sending a notification to the email address (".$FIELDS['Email'].") provided.. Error: ". $mail->ErrorInfo ."<br>");
					$ERRORS['MSG'] .= ConfirmMessage("WHAT TO DO: The new account has been created successfully and you do not need to create another one. <br> Please activate the account and notify the user manually.");
					Error_alertAdmin("PHP Mailer",$ERRORS['MSG'],$ErrPage,$FIELDS['Email']);
				}
				else{
					$saved = true;
					//Display Confirmation Message
					$CONFIRM['MSG'] = ConfirmMessage("New System User has been added successfully");
					$CONFIRM['MSG'] .= ConfirmMessage("Activation link has been sent to the email address provided. The user will be prompted to change their password before they can access the system.");
					$_SESSION['MSG'] = $CONFIRM['MSG'];
					redirect("admin.php?tab=8");
				}
			}else{
				//Show error here
				$_SESSION['MSG'] = ErrorMessage("Failed to save successfully. Please try again later...");
				redirect("admin.php?tab=8");
			}
		}
	}
	
	$row["FirstName"] = !empty($FIELDS['FirstName'])?$FIELDS['FirstName']:"";
	$row["LastName"] = !empty($FIELDS['LastName'])?$FIELDS['LastName']:"";
	$row["Username"] = !empty($FIELDS['Username'])?$FIELDS['Username']:"";	
	$row["Email"] = !empty($FIELDS['Email'])?$FIELDS['Email']:"";
	$row["UserLevel"] = !empty($FIELDS['UserLevel'])?$FIELDS['UserLevel']:"";;
	$row["Enabled"] = !empty($FIELDS['Enabled'])?$FIELDS['Enabled']:0;
?>
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?tab=8">Manage Users</a></li><li class="active">Add System User</li></ol>

<a class="btn btn-default" href="admin.php?tab=8"><i class="fa fa-undo fa-fw"></i> Back to System Users</a>

<p class="text-center"><?php echo !empty($ERRORS['MSG'])?$ERRORS['MSG']:"";?></p>
<form id="validateform" enctype="multipart/form-data" action="admin.php?tab=8&task=add" method="post">
<input type="hidden" name="sql" value="insert">
<?php
showroweditor($row, false, $ERRORS);
?>
<p class="text-center">
<input class="btn btn-primary" type="submit" name="Add" value="Save">
<input class="btn btn-default" type="button" name="cancel" value="Cancel" onclick="javascript:location.href='admin.php?tab=8'">
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
		// System User info
		$FIELDS['FirstName'] = isset($_POST['FirstName'])?secure_string($_POST['FirstName']):"";
		$FIELDS['LastName'] = isset($_POST['LastName'])?secure_string($_POST['LastName']):"";
		$FIELDS['Email'] = isset($_POST['Email'])?secure_string($_POST['Email']):"";
		$FIELDS['UserLevel'] = isset($_POST['UserLevel'])?secure_string($_POST['UserLevel']):"";
		$FIELDS['Enabled'] = intval(! empty($_POST['Enabled']))?secure_string($_POST['Enabled']):0;
		
		// Validator data
		$check = new validator();
		// validate entry
		// validate "FirstName" field
		if(!$check->is_String($FIELDS['FirstName']))
		$ERRORS['FirstName'] = "Valid first name required";
		// validate "LastName" field
		if(!$check->is_String($FIELDS['LastName']))
		$ERRORS['LastName'] = "Valid last name required";
		// validate "Email" field
		if (!$check->is_email($FIELDS['Email'])) 
		$ERRORS['Email'] = "A valid email address is required";
		//Validate "UserLevel" field
		if($FIELDS['UserLevel'] == "None")
		$ERRORS['UserLevel'] = "At least one admin level needs to be assigned to the user";
		
		// check for errors
		if(sizeof($ERRORS) > 0){
			$ERRORS['MSG'] = ErrorMessage("ERRORS ENCOUNTERED!");
		}
		else{
			if(sql_update($FIELDS)){
			}else{
			}
		}
  	}
	
	$res = sql_select();
	$count = sql_getrecordcount();
	db_data_seek($res, $recid);
	$row = db_fetch_array($res);
	
	$row["FirstName"] = !empty($FIELDS['FirstName'])?$FIELDS['FirstName']:$row["FirstName"];
	$row["LastName"] = !empty($FIELDS['LastName'])?$FIELDS['LastName']:$row["LastName"];
	$row["Email"] = !empty($FIELDS['Email'])?$FIELDS['Email']:$row["Email"];
	$row["UserLevel"] = !empty($FIELDS['UserLevel'])?$FIELDS['UserLevel']:$row["UserLevel"];
	$row["Enabled"] = !empty($FIELDS['Enabled'])?$FIELDS['Enabled']:$row["Enabled"];
?>

<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?tab=8">Manage Users</a></li><li class="active">Edit System User</li></ol>
<?php showrecnav("edit", $recid, $count); ?>
<form id="validateform" enctype="multipart/form-data" action="admin.php?tab=8&task=edit&recid=<?=$recid?>&userid=<?=$row["ID"];?>" method="post">
<p class="text-center"><?php echo !empty($ERRORS['MSG'])?$ERRORS['MSG']:"";?></p>
<input type="hidden" name="sql" value="update">
<input type="hidden" name="eid" value="<?=$row["ID"]; ?>">
<?php showroweditor($row, true, $ERRORS); ?>
<p class="text-center">
<input class="btn btn-primary" type="submit" name="Edit" value="Save">
<input class="btn btn-default" type="button" name="cancel" value="Cancel" onclick="javascript:location.href='admin.php?tab=8'">
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
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?tab=8">Manage Users</a></li><li class="active">Delete System User</li></ol>
<?php showrecnav("del", $recid, $count); ?>
<form action="admin.php?tab=8&task=del&recid=<?=$recid?>&userid=<?=$row["ID"];?>" method="post">
<input type="hidden" name="sql" value="delete">
<input type="hidden" name="eid" value="<?=$row["ID"]; ?>">
<?php showrow($row, $recid) ?>
<strong>Are you sure you want to delete this record? </strong><div class="btn-group"><input class="btn btn-primary" type="submit" name="Delete" value="Yes"> <input class="btn btn-default" type="button" name="Ignore" value="No" onclick="javascript:history.go(-1)"></div>
</form>
<?php
db_free_result($res);
}
?>
<?php
function resetrec($recid, $user){
	global $incl_dir,$class_dir;
	
	require "$incl_dir/recaptchalib.php";
	
	// Permission allowed IFF the logged in user is:
	// (1). Account owner
	// (2). Super or System admin
	if(isSuperAdmin() || isSystemAdmin()){
		$allowed = true;
	}
	elseif($user == $_SESSION['sysEmail']){
		$allowed = true;
	}
	else{
		$allowed = false;
	}
	
	// Variables
	$ERRORS = array();
	// Generate a new token
	$thisToken = md5(time());
	
	//Retrieve info
	if($allowed && !empty($user)){
		// Commands
		if(isset($_POST["Reset"])){
			// Validate Google reCAPTCHA
			if( !recapture_verify( $_POST["g-recaptcha-response"], $_SERVER["REMOTE_ADDR"] ) ){
				$ERRORS['MSG'] = ErrorMessage("ERRORS ENCOUNTERED!");				
				$ERRORS['reCaptcha'] = "You're a robot. If not, please try again.";
			}
			else{
				if(sql_reset($thisToken, $user)){
					//SEND RESET LINK//
					// Mail function
					$mail = new PHPMailer; // defaults to using php "mail()"
					
					// Message
					$message = "<html><head>
					<title>".SYSTEM_SHORT_NAME." - Reset Your Accoun</title>
					</head><body>
					<p>Dear $user, <br><br> You received this email because your account was updated by ".SYSTEM_NAME." Administrator. Your account has been reset and you will not be able to login until you complete the procedure below. <br><br> This email allows you to set a new password for your account by clicking on the link provided below. <br><br><strong>One Time Password Reset Link: </strong> <a href=\"".SYSTEM_URL."/admin/?do=activate&token=".$thisToken."\" target=\"_blank\"><strong>Click here to activate your account</strong></a><br><br>Sincerely,<br><br>".strtoupper(SYSTEM_SHORT_NAME)." ALERT NOTIFICATIONS<br>Email: ".INFO_EMAIL."<br>Website: ".PARENT_HOME_URL."</p>
					</body>
					</html>";
					
					$body = $message;
					$body = preg_replace('/\\\\/','', $body); //Strip backslashes
					
					switch(MAILER){
						case 'smtp':
						$mail->isSMTP(); // telling the class to use SMTP
						$mail->SMTPAuth = SMTP_AUTH; // enable SMTP authentication
						$mail->SMTPSecure = SMTP_SECU; // sets the prefix to the servier
						$mail->Host = SMTP_HOST; // SMTP server
						$mail->Port = SMTP_PORT; // set the SMTP port for the HOST server
						$mail->Username = SMTP_USER;
						$mail->Password = SMTP_PASS;
						break;
						case 'sendmail':
						$mail->isSendmail(); // telling the class to use SendMail transport
						break;
						case 'mail':
						$mail->isMail(); // telling the class to use mail function
						break;
					}
					
					$mail->setFrom(MAILER_FROM_EMAIL, MAILER_FROM_NAME);
					$mail->Subject = SYSTEM_SHORT_NAME." - Reset Your Accoun";	
					$mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; 		
					$mail->msgHTML($body);
					$mail->isHTML(true); // send as HTML		
					$mail->addAddress($user);
					
					
					if(!$mail->Send()) {
						$ErrPage = curPageURL();
						//Display Error Message
						$ERRORS['MSG'] = ErrorMessage("Failed to send the Password Reset link. Error: ". $mail->ErrorInfo);
						Error_alertAdmin("PHP Mailer",$ERRORS['MSG'],$ErrPage,$user);
					}else{
						//Display Confirmation Message
						$_SESSION['MSG'] = ConfirmMessage("A password reset link has been sent to $user successfully. The link will allow the user to set a new password for this account.");
						redirect("admin.php?tab=8");
					}
					
				}else{
					//Display Error Message
					$ERRORS['MSG'] = ErrorMessage("Failed to save the password reset token.");
				}
			}
		}
?>
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?tab=8">Manage Users</a></li><li class="active">Reset System User</li></ol>
<form action="admin.php?tab=8&task=resetpass&recid=<?=$recid?>&user=<?=$user?>" method="post">
<input type="hidden" name="sql" value="resetpass">
<input type="hidden" name="user" value="<?=$user ?>">
<table align="center" border="0" cellpadding="1" cellspacing="5">
<tr><td style="text-align:center" colspan="2"><?php echo !empty($ERRORS['MSG'])?$ERRORS['MSG']:"";?></td></tr>
<tr>
<th style="text-align:center" colspan="2">PASSWORD RESET FORM</th>
</tr>
<tr>
<td style="text-align:center" colspan="2"><span class="text-danger"><strong>This action will Reset Password and send an Activation Link to <?=$user?>. The user will be able to set a new password by clicking on the link provided in the email. Enter the security code and click the Send button below.</strong></span></td>
</tr>
<tr>
<td style="text-align:center" colspan="2"><?=recaptcha_get_html();?><br><span class="text-danger"><?=$ERRORS['reCaptcha']?></span></td>
</tr>
<tr>
<td style="text-align:center" colspan="2">
<input class="btn btn-primary" type="submit" name="Reset" value="Reset">
<input class="btn btn-default" type="button" name="Cancel" value="Cancel" onclick='javascript:history.go(-1)'>
</td>
</tr>
</table>
</form>
<?php 
	}else{
		echo ErrorMessage("You do not have the privileges to access this area. To avoid this error, make sure your account has the privileges to manage system users.");
	}
} 
?>

<?php
function sql_select(){
	global $conn;
	global $eid;
			
	$sql = "SELECT `ID`,`FirstName`,`LastName`,CONCAT(`FirstName`,' ',`LastName`) AS `FullName`,`Username`,`Email`,`UserType`,`UserLevel`,`loggedIn`,`token`,`disabledFlag` FROM `".DB_PREFIX."sys_users`";
	if (!empty($eid)) {
		$sql .= sprintf(" WHERE `ID` = %d", $eid);
	}
	$res = db_query($sql,DB_NAME,$conn);
	return $res;
}

function sql_getrecordcount(){
	global $conn;
	global $eid;
	
	$sql = "SELECT COUNT(*) FROM `".DB_PREFIX."sys_users`";
	if (!empty($eid)) {
		$sql .= sprintf(" WHERE `ID` = %d", $eid);
	}
	$res = db_query($sql,DB_NAME,$conn);
	$row = db_fetch_array($res);
	reset($row);
	return current($row);
}

function sql_insert($FIELDS){
	global $conn;
	
	//Add new System User
	$sql = sprintf("INSERT INTO `".DB_PREFIX."sys_users` (`FirstName`,`LastName`,`Username`,`Password`, `Email`, `UserLevel`, `token`, `disabledFlag`) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', %d)", $FIELDS['FirstName'], $FIELDS['LastName'], $FIELDS['Username'], $FIELDS['EncryptPass'], $FIELDS['Email'], $FIELDS['UserLevel'], $FIELDS['Token'], $FIELDS['Enabled']);	
	db_query($sql,DB_NAME,$conn);
	
	//Check if saved
	/*
	if(db_affected_rows($conn)){
		$_SESSION['MSG'] = ConfirmMessage("New System User has been added successfully");
	}else{
		$_SESSION['MSG'] = ErrorMessage("Failed to save successfully. Please try again later...");
	}
	redirect("admin.php?tab=8");
	*/
    if(db_affected_rows($conn)){
		return TRUE;
	}else{
		return FALSE;
	}
}

function sql_update($FIELDS){
	global $conn;
	
	//Update System User
	$sql = sprintf("UPDATE `".DB_PREFIX."sys_users` SET `FirstName` = '%s', `LastName` = '%s', `Email` = '%s', `UserLevel` = '%s', `disabledFlag` = %d WHERE " .primarykeycondition(). "", $FIELDS['FirstName'], $FIELDS['LastName'], $FIELDS['Email'], $FIELDS['UserLevel'], $FIELDS['Enabled']);		
	db_query($sql,DB_NAME,$conn);
	
	//Check if updated
	/*
	if(db_affected_rows($conn)){
		$_SESSION['MSG'] = ConfirmMessage("System User has been updated successfully.");

	}else{
		$_SESSION['MSG'] = WarnMessage("No changes made!");
	}
	redirect("admin.php?tab=8");
	*/
	if(db_affected_rows($conn)){
		return TRUE;
	}else{
		return FALSE;
	}
}

function sql_update_status($disabledFlag, $editID){
	global $conn;
	
	//Update System User
	$sql = sprintf("UPDATE `".DB_PREFIX."sys_users` SET `disabledFlag` = %d WHERE `ID` = %d LIMIT 1", $disabledFlag, $editID);
	db_query($sql,DB_NAME,$conn);
	
	//Check if updated
	if(db_affected_rows($conn)){
		$_SESSION['MSG'] = ConfirmMessage("System User has been updated successfully.");
	}else{
		$_SESSION['MSG'] = WarnMessage("No changes made!");
	}
	redirect("admin.php?tab=8");
}

function sql_delete(){
	global $conn;
	
	$sql = "DELETE FROM `".DB_PREFIX."sys_users` WHERE " .primarykeycondition();
	db_query($sql,DB_NAME,$conn);
	
	//Check if saved
	if(db_affected_rows($conn)){
		$_SESSION['MSG'] = ConfirmMessage("System User has been deleted successfully");
	}else{
		$_SESSION['MSG'] = ErrorMessage("Failed to delete selected System User. Please try again later...");
	}
	redirect("admin.php?tab=8");
}
function sql_reset($thisToken, $thisUser){
	global $conn;
	
	$sql = sprintf("UPDATE `".DB_PREFIX."sys_users` SET `token` = '%s' WHERE `Username` = '%s' LIMIT 1", $thisToken, $thisUser);
	db_query($sql,DB_NAME,$conn);
	//Check if saved
	if(db_affected_rows($conn)){
		return true;
	}else{
		return false;
	}
}
function primarykeycondition(){
	
	$pk = "";
	$pk .= "(`ID`";
	if (@$_POST["eid"] == "") {
		$pk .= " IS NULL";
	}else{
		$pk .= " = " .intval(@$_POST["eid"]);
	};
	$pk .= ")";
	return $pk;
}
?>