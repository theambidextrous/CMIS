<?php
//Import the PHPMailer class into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
?>
<script src="<?=SYSTEM_URL;?>/javascript/multifile.js"></script>
<script type="text/javascript">
<!--//
//Define page title
document.title = "<?=SYSTEM_SHORT_NAME?> | Messages";

function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}

$(document).ready(function() {
	//Load TinyMCE	
	tinymce.init({		
		selector: 'textarea.tinymce',
		height: 500,
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
//-->
</script>

<div class="row">
  <div class="col-lg-12">
    <h1 class="page-header">Messages</h1>
  </div>
  <!-- /.col-lg-12 --> 
</div>
<!-- /.row -->

<div class="row">
  <div class="col-lg-12">
    <div class="panel panel-default">
      <div class="panel-heading"> <i class="fa fa-comments fa-fw"></i> View Messages </div>
      <!-- /.panel-heading -->
      <div class="panel-body">
        <!--Begin Forms-->
		<?php
        $a = isset($_GET["task"])?$_GET["task"]:"";
        $recid = intval(! empty($_GET['recid']))?$_GET['recid']:0;
        
        //Set Defaults
        //Get folder
        $folder = isset($_GET["folder"])?$_GET["folder"]:"";
        $folder = strtolower($folder);
        //Check is folder changed
        if (empty($folder) && isset($_SESSION["folder"])) $folder = $_SESSION["folder"];
        //Grab email of logged in Email user
        $SysEmail = $_SESSION['sysEmail'];			
        
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
	global $folder;
	
	if ($a == "reset") {
		$folder = "inbox";
		unset($_SESSION["folder"]);
	}
	
	$res = sql_select();
	$count = sql_getrecordcount();	
?>
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li>Available Messages</li></ol>

<div id="hideMsg"><?php if(isset($_SESSION['MSG'])) echo $_SESSION['MSG'];?></div>

<?php showpagenav($pagecount); ?>
<table width="100%" class="display table table-striped table-bordered table-hover">
<thead>
<tr>
<th>Sent On</th>
<?php if($folder == "outbox") {?>
<th>To</th>
<?php }else{ ?>
<th>From</th>
<?php } ?>
<th>Subject</th>
<th>Type</th>
<th>Source</th>
<th class="no-sort">Actions</th>
</tr>
</thead>
<tbody>
<?php
for ($i = 0; $i < $count; $i++){
	$row = db_fetch_array($res);
	$hid = ($row['DateRead'] == "0000-00-00 00:00:00")?'active bold':'';
	?>
	<tr class="<?=$hid; ?>">
    <td class="defaultsort"><?=fixdatetime($row['DateSent'])?></td>
	<?php
	if($folder == "outbox"){
		echo "<td>".$row['ToAdd']." ".$row['CcAdd']."</td>";
	}else{
		echo "<td>".$row['FromAdd']."</td>";	
	}
	?>	
	<td><a href="admin.php?tab=7&task=view&recid=<?=$i ?>"><?=$row["Subject"]?></a></td>
	<td><?=$row["Type"]?></td>
	<td><?=$row['Source']?></td>
	<td><a href="admin.php?tab=7&task=view&recid=<?=$i ?>">View</a> | <a href="admin.php?tab=7&task=del&recid=<?=$i ?>">Delete</a></td>
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
global $conn;

$ToMsgEmails = explode(" ", $row["ToAdd"]);
$CcMsgEmails = explode(" ", $row["CcAdd"]);
$BccMsgEmails = explode(" ", $row["BccAdd"]);
//Read
$MyEmail = isset($_SESSION['sysEmail'])?$_SESSION['sysEmail']:INFO_EMAIL;
$DateRead = date('Y-m-d H:i:s');
$sqlUpdateMsg = sprintf("UPDATE `".DB_PREFIX."messages` SET `DateRead` = '%s' WHERE `UID` = %d AND `ToAdd` = '%s'", $DateRead, $row["UID"], $MyEmail);
//Run query
db_query($sqlUpdateMsg,DB_NAME,$conn);
?>
<p class="text-center"><strong>MESSAGE</strong></p>

<div class="table-responsive">
<table class="table">
<tr>
<td width="15%">From: </td>
<td align="left"><?=$row["FromAdd"]?></td>
</tr>
<tr>
<td>To: </td>
<td align="left"><?=implode(", ", $ToMsgEmails)?></td>
</tr>
<?php
$CcMsgEmails = implode(", ", $CcMsgEmails);
if(!empty($CcMsgEmails)){
    echo "<tr><td>Cc: </td><td align=\"left\">$CcMsgEmails</td></tr>";
}
$BccMsgEmails = implode(", ", $BccMsgEmails);
if(!empty($BccMsgEmails)){
    echo "<tr><td>Bcc: </td><td align=\"left\">$BccMsgEmails</td></tr>";
}
?>
<tr>
<td>Date Sent: </td>
<td align="left"><?=fixdatelong($row["DateSent"])?></td>
</tr>
<tr>
<td>Subject: </td>
<td align="left"><?=$row["Subject"]?></td>
</tr>
<td colspan="2">
<?php
//Check if resume included
if(!empty($row['ResumePath'])){
	echo "<a href=\"".SYSTEM_URL."/files/".$row['ResumePath']."\">".$row['ResumePath']."</a>&nbsp;";
}
//List other attachments
echo list_attachments($row['UID']);
?>
</td>
<tr>
<td colspan="2" valign="top"><div class="message-body-display"><?=decode($row["Message"])?></div></td>
</tr>
<tr>
<td style="text-align:center" colspan="2">
<div class="btn-group">
<a class="btn btn-default" href="admin.php?tab=7&amp;task=reply&amp;recid=<?=$recid?>">Reply</a>
<a class="btn btn-default" href="admin.php?tab=7&amp;task=replyall&amp;recid=<?=$recid?>">Reply All</a>
<a class="btn btn-default" href="admin.php?tab=7&amp;task=del&amp;recid=<?=$recid?>">Delete</a>
</div>
</td>
</tr>
</table>
</div>
<?php } ?>

<?php 
function showroweditor($row, $iseditmode, $ERRORS){
global $a;  
?>
<p class="text-center lead"><strong><?=strtoupper($a)?> MESSAGE</strong></p>
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
<script language="javascript">
  <!-- Create an instance of the multiSelector class, pass it the output target and the max number of files -->
  var multi_selector = new MultiSelector( document.getElementById( 'files_list_msg' ), 5 );
  <!-- Pass in the file element -->
  multi_selector.addElement( document.getElementById( 'msg_attachments' ) );
</script> 

<div class="form-group">
  <div class="col-sm-12"><textarea id="message" name="Message" class="tinymce"><?=$row['Message']?></textarea></div>
</div>

<?php } ?>

<?php
function showpagenav() {
  global $folder;
?>
<div class="quick-nav btn-group">
<a class="btn btn-primary" href="admin.php?tab=7&task=add">Add Message</a>
<a class="btn btn-default" href="admin.php?tab=7&task=reset">Reset Filters</a>
</div>
<div style="float:right;">
<form class="form-inline" role="form">
<div class="form-group">
<label for="folder">Select Folder:</label> <select name="jumpFolder" id="jumpFolder" onchange="MM_jumpMenu('parent',this,0)" class="form-control">
<?php if($folder == "outbox") $folder2 = ' selected="selected"'; else $folder1 = ' selected="selected"';?>
<option<?=$folder1?> value="admin.php?tab=7&amp;folder=inbox">Inbox</option>
<option<?=$folder2?> value="admin.php?tab=7&amp;folder=outbox">Outbox</option>
</select>
</div>
</form>
</div>
<?php } ?>

<?php function showrecnav($a, $recid, $count) { ?>
<div class="quick-nav btn-group">
<a class="btn btn-default" href="admin.php?tab=7"><i class="fa fa-undo fa-fw"></i> Back to Messages</a>
<?php if ($recid > 0) { ?>
<a class="btn btn-default" href="admin.php?tab=7&task=<?=$a ?>&recid=<?=$recid - 1 ?>"><i class="fa fa-arrow-left fa-fw"></i> Prior Record</a>
<?php } if ($recid < $count - 1) { ?>
<a class="btn btn-default" href="admin.php?tab=7&task=<?=$a ?>&recid=<?=$recid + 1 ?>"><i class="fa fa-arrow-right fa-fw"></i> Next Record</a>
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
	<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?tab=7">Messages</a></li><li>View Message</li></ol>
	<?php 
	showrecnav("view", $recid, $count);
	showrowdetailed($row, $recid);
	db_free_result($res);
} 
?>

<?php 
function addrec() { 
  	global $conn,$class_dir,$folder,$recid;
		
	require_once("$class_dir/class.validator.php3");
	
	// Variables
	$ERRORS = array();
	$FIELDS = array();
	$ERR = 'id="highlight"';//Error highlighter
	
	$FIELDS['ToAdd'] = isset($_GET["email"])?$_GET["email"]:NULL;
	$FIELDS['FromAdd'] = isset($_SESSION['sysEmail'])?$_SESSION['sysEmail']:INFO_EMAIL;
	$FIELDS['FromName'] = isset($_SESSION['sysFullName'])?$_SESSION['sysFullName']:INFO_NAME;
	$FIELDS['CcAdd'] = "";
	$FIELDS['BccAdd'] = "";
	$FIELDS['Subject'] = isset($_GET["subject"])?$_GET["subject"]:NULL;
	$FIELDS['Message'] = "";
	
	// Commands
	if(isset($_POST["Add"])){
		// Message info
		$FIELDS['FromAdd'] = secure_string($_POST['FromAdd']);
		$FIELDS['ToAdd'] = secure_string($_POST['ToAdd']);
		$FIELDS['CcAdd'] = secure_string($_POST['CcAdd']);
		$FIELDS['BccAdd'] = secure_string($_POST['BccAdd']);
		$FIELDS['Subject'] = $_POST['Subject'];
		$FIELDS['EncodedSubject'] = secure_string($FIELDS['Subject']);
		$FIELDS['Message'] = $_POST['Message'];
		$FIELDS['EncodedMessage'] = encode(secure_string($FIELDS['Message']));
		$FIELDS['DateSent'] = date('Y-m-d H:i:s');
		$FIELDS['Type'] = !empty($_POST['Type'])?$_POST['Type']:"Message";
		$FIELDS['Source'] = getUserIP();
		//Split emails
		$FIELDS['ToEmails'] = explode_trim($FIELDS['ToAdd'], ',');
		$FIELDS['CcEmails'] = explode_trim($FIELDS['CcAdd'], ',');
		$FIELDS['BccEmails'] = explode_trim($FIELDS['BccAdd'], ',');
		
		// Validator contractor
		$check = new validator();
		// validate "ToEmails" field
		foreach($FIELDS['ToEmails'] as $ToEmail){
			if(!$check->is_email($ToEmail))
			$ERRORS['ToAdd'] = $ERR;
		}
		// validate "CcEmails" field
		if(!empty($FIELDS['CcAdd'])){
			foreach($FIELDS['CcEmails'] as $CcEmail){
				if(!$check->is_email($CcEmail))
				$ERRORS['CcAdd'] = $ERR;
			}
		}
		// validate "BccEmails" field
		if(!empty($FIELDS['BccAdd'])){
			foreach($FIELDS['BccEmails'] as $BccEmail){
				if(!$check->is_email($BccEmail))
				$ERRORS['BccAdd'] = $ERR;
			}
		}
		// validate "Subject" field
		if(empty($FIELDS['Subject']))
		$ERRORS['Subject'] = $ERR;
		//Validate file type
		$allowed_mimes = allowed_doc_mime_types();
		foreach ($_FILES["file"]["error"] as $key => $error) {
			if (!$error == UPLOAD_ERR_OK) {
				$MsgErr['FileErr'] = "Attachment file cannot be uploaded. Attachment can only be a document or PDF file.";
			}
			if (!in_array($_FILES["file"]["type"][$key], $allowed_mimes)) {
				$MsgErr['FileErr'] = "Invalid file type <em>". $_FILES["file"]["type"][$key] ."</em>! Attachment can only be a document or PDF file.";
			}
		}
		
		// check for errors
		if(sizeof($ERRORS) > 0){
			$ERRORS['MSG'] = ErrorMessage("PLEASE CORRECT HIGHLIGHTED FIELDS!");
		}
		else{
			if(sizeof($FIELDS['ToEmails']) > 0){
				// Add To recipients (without commas)
				$FIELDS['thisToEmails'] = implode(" ", $FIELDS['ToEmails']);
				// Add Cc recipients (without commas)
				$FIELDS['thisCcEmails'] = implode(" ", $FIELDS['CcEmails']);
				// Add Bcc recipients (without commas)
				$FIELDS['thisBccEmails'] = implode(" ", $FIELDS['BccEmails']);
				
				//Run Query
				if(sql_insert($FIELDS)){
					//Get last generated message ID
					$messageID = db_insert_id();
					//Upload files if any attached
					$thisDownloadPaths = "";
					foreach ($_FILES["file"]["error"] as $key => $error) {
						if ($error == UPLOAD_ERR_OK) {
							$tmp = $_FILES['file']['tmp_name'][$key];
							$filetype = $_FILES["file"]["type"][$key];
							$filename = basename($_FILES["file"]["name"][$key]);
							$attachmentPath = ATTACHMENT_PATH.$filename;
							$downloadPath = ATTACHMENT_FOLDER."/".$filename;
							//Just incase of an internal problem
							if(move_uploaded_file($tmp, $attachmentPath)){
								$newAttachmentSql = sprintf("INSERT INTO `".DB_PREFIX."attachments` (`MessageID`,`FileName`,`MimeType`,`FilePath`,`DownloadPath`) VALUES (%d, '%s', '%s', '%s', '%s')", $messageID, secure_string($filename), secure_string($filetype), secure_string($attachmentPath), secure_string($downloadPath));
								//Run query
								db_query($newAttachmentSql,DB_NAME,$conn);
							}else{
								$thisDownloadPaths = "";
							}
						}
						$tmp='';
						$thisDownloadPaths .= $attachmentPath." ";
					}
	
					//SEND NOTIFICATION//
					// Set mail function
					$mail = new PHPMailer; // defaults to using php "mail()"
					
					$body = "<html><head>
					<title>$Subject</title>
					</head><body>".$FIELDS['Message']."</body></html>";
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
					
					$mail->setFrom($FIELDS['FromAdd'], $FIELDS['FromName']);
					$mail->Subject = $FIELDS['Subject'];
					$mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; 		
					$mail->msgHTML($body);
					$mail->isHTML(true); // send as HTML
					//Grab recipient emails
					$ToRecipients = explode(" ", $FIELDS['thisToEmails']);
					$thisToRecipients = array_unique($ToRecipients);
					// Add To recipients
					foreach($thisToRecipients as $sendToEmail){
						$mail->addAddress($sendToEmail);
					}
					$CcRecipients = explode(" ", $FIELDS['thisCcEmails']);
					$thisCcRecipients = array_unique($CcRecipients);
					// Add Cc recipients
	
					foreach($thisCcRecipients as $sendCcEmail){
						if(!empty($sendCcEmail)){
							$mail->addCC($sendCcEmail);
						}
					}
					$BccRecipients = explode(" ", $FIELDS['thisBccEmails']);
					$thisBccRecipients = array_unique($BccRecipients);
					// Add Bcc recipients
					foreach($thisBccRecipients as $sendBccEmail){
						if(!empty($sendBccEmail)){
							$mail->addBCC($sendBccEmail);
						}
					}
					
					// Add Attachments
					$thisAttachments = explode(" ", $thisDownloadPaths);
					foreach($thisAttachments as $thisDownloadPath){
						if(!empty($thisDownloadPath)){
							$mail->addAttachment($thisDownloadPath);
						}
					}
					
					if(!$mail->Send()) {
						$sent = false;
						$ErrPage = curPageURL();
						$MailERR = $mail->ErrorInfo;
						Error_alertAdmin("PHP Mailer","Failed to send new message-".$MailERR,$ErrPage,$Email);
						//Display Warning Message
						$_SESSION['MSG'] = AttentionMessage("The message was saved successfully but the system failed to send your email to the specified recipients. However, once they login to the system, they should be able to see the message under the messages tab on their dashboard.");
						redirect("admin.php?tab=7&recid=$recid");
					}else{
						$sent = true;
						//Display Confirmation Message
						$_SESSION['MSG'] = ConfirmMessage("Message sent successfully");
						redirect("admin.php?tab=7&recid=$recid");
					}					
				}else{
					$sent = false;
					//Display Error Message
					$ERRORS['MSG'] = ErrorMessage("Message not sent. Try again later...");
				}
			}else{
				$ERRORS['MSG'] = ErrorMessage("Message not sent! Check if all email addresses are correctly formatted.");
			}
		}
	}
		
	$row['Type'] = !empty($FIELDS['Type'])?$FIELDS['Type']:"Message";
	$row["FromAdd"] = !empty($FIELDS['FromAdd'])?$FIELDS['FromAdd']:$row['FromAdd'];
	$row["ToAdd"] = !empty($FIELDS['ToAdd'])?$FIELDS['ToAdd']:$row['ToAdd'];
	$row["CcAdd"] = !empty($FIELDS['CcAdd'])?$FIELDS['CcAdd']:$row['CcAdd'];
	$row["BccAdd"] = !empty($FIELDS['BccAdd'])?$FIELDS['BccAdd']:$row['BccAdd'];
	$row["Subject"] = !empty($FIELDS['Subject'])?$FIELDS['Subject']:$row['Subject'];
	$row["Message"] = !empty($FIELDS['Message'])?$FIELDS['Message']:$row['Message'];
?>
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?tab=7">Messages</a></li><li class="active">Add Message</li></ol>

<a class="btn btn-default" href="admin.php?tab=7"><i class="fa fa-undo fa-fw"></i> Back to Messages</a>

<p class="text-center"><?php if(sizeof($ERRORS['MSG'])>0) echo $ERRORS['MSG'];?></p>
<form class="form-horizontal" id="validateform" enctype="multipart/form-data" action="admin.php?tab=7&task=add" method="post">
<input type="hidden" name="sql" value="insert">
<?php
showroweditor($row, false, $ERRORS);
?>
<p class="text-center">
<input type="hidden" name="FromAdd" value="<?=$row['FromAdd']?>">
<input type="hidden" name="Type" value="<?=$row['Type']?>">
<input class="btn btn-primary" type="submit" name="Add" value="Send">
<input class="btn btn-default" type="button" name="cancel" value="Cancel" onclick="javascript:location.href='admin.php?tab=7'">
</p>
</form>
<?php } ?>

<?php 
function editrec($recid, $action){
  	global $conn,$class_dir,$folder,$recid;
		
	require_once("$class_dir/class.validator.php3");
	
	// Variables
	$ERRORS = array();
	$FIELDS = array();
	$ERR = 'id="highlight"';//Error highlighter
	$FIELDS['FromAdd'] = isset($_SESSION['sysEmail'])?$_SESSION['sysEmail']:INFO_EMAIL;
	$FIELDS['FromName'] = isset($_SESSION['sysFullName'])?$_SESSION['sysFullName']:INFO_NAME;
	
	// Commands
	if(isset($_POST["Reply"])){
		// Message info
		$FIELDS['FromAdd'] = secure_string($_POST['FromAdd']);
		$FIELDS['ToAdd'] = secure_string($_POST['ToAdd']);
		$FIELDS['CcAdd'] = secure_string($_POST['CcAdd']);
		$FIELDS['BccAdd'] = secure_string($_POST['BccAdd']);
		$FIELDS['Subject'] = $_POST['Subject'];
		$FIELDS['EncodedSubject'] = secure_string($FIELDS['Subject']);
		$FIELDS['Message'] = $_POST['Message'];
		$FIELDS['EncodedMessage'] = encode(secure_string($FIELDS['Message']));
		$FIELDS['DateSent'] = date('Y-m-d H:i:s');
		$FIELDS['Type'] = !empty($_POST['Type'])?$_POST['Type']:"Message";
		$FIELDS['Source'] = getUserIP();
		//Split emails
		$FIELDS['ToEmails'] = explode_trim($FIELDS['ToAdd'], ',');
		$FIELDS['CcEmails'] = explode_trim($FIELDS['CcAdd'], ',');
		$FIELDS['BccEmails'] = explode_trim($FIELDS['BccAdd'], ',');
		
		// Validator contractor
		$check = new validator();
		// validate "ToAdd" field
		foreach($FIELDS['ToEmails'] as $ToEmail){
			if(!$check->is_email($ToEmail))
			$ERRORS['ToAdd'] = $ERR;
		}
		// validate "CcAdd" field
		if(!empty($FIELDS['CcAdd'])){
			foreach($FIELDS['CcEmails'] as $CcEmail){
				if(!$check->is_email($CcEmail))
				$ERRORS['CcAdd'] = $ERR;
			}
		}
		// validate "BccAdd" field
		if(!empty($FIELDS['BccAdd'])){
			foreach($FIELDS['BccEmails'] as $BccEmail){
				if(!$check->is_email($BccEmail))
				$ERRORS['BccAdd'] = $ERR;
			}
		}
		// validate "Subject" field
		if(empty($FIELDS['Subject']))
		$ERRORS['Subject'] = $ERR;
		//Validate file type
		$allowed_mimes = allowed_doc_mime_types();
		foreach ($_FILES["file"]["error"] as $key => $error) {
			if (!$error == UPLOAD_ERR_OK) {
				$MsgErr['FileErr'] = "Attachment file cannot be uploaded. Attachment can only be a document or PDF file.";
			}
			if (!in_array($_FILES["file"]["type"][$key], $allowed_mimes)) {
				$MsgErr['FileErr'] = "Invalid file type <em>". $_FILES["file"]["type"][$key] ."</em>! Attachment can only be a document or PDF file.";
			}
		}
		
		// check for errors
		if(sizeof($ERRORS) > 0){
			$ERRORS['MSG'] = ErrorMessage("PLEASE CORRECT HIGHLIGHTED FIELDS!");
		}
		else{
			if(sizeof($FIELDS['ToEmails']) > 0){
				// Add To recipients (without commas)
				$FIELDS['thisToEmails'] = implode(" ", $FIELDS['ToEmails']);
				// Add Cc recipients (without commas)
				$FIELDS['thisCcEmails'] = implode(" ", $FIELDS['CcEmails']);
				// Add Bcc recipients (without commas)
				$FIELDS['thisBccEmails'] = implode(" ", $FIELDS['BccEmails']);
				
				//Run Query
				if(sql_insert($FIELDS)){
					//Get last generated message ID
					$messageID = db_insert_id();
					//Upload files if any attached
					$thisDownloadPaths = "";
					foreach ($_FILES["file"]["error"] as $key => $error) {
						if ($error == UPLOAD_ERR_OK) {
							$tmp = $_FILES['file']['tmp_name'][$key];
							$filetype = $_FILES["file"]["type"][$key];
							$filename = basename($_FILES["file"]["name"][$key]);
							$attachmentPath = ATTACHMENT_PATH.$filename;
							$downloadPath = ATTACHMENT_FOLDER."/".$filename;
							//Just incase of an internal problem
							if(move_uploaded_file($tmp, $attachmentPath)){
								$newAttachmentSql = sprintf("INSERT INTO `".DB_PREFIX."attachments` (`MessageID`,`FileName`,`MimeType`,`FilePath`,`DownloadPath`) VALUES (%d, '%s', '%s', '%s', '%s')", $messageID, secure_string($filename), secure_string($filetype), secure_string($attachmentPath), secure_string($downloadPath));
								//Run query
								db_query($newAttachmentSql,DB_NAME,$conn);
							}else{
								$thisDownloadPaths = "";
							}
						}
						$tmp='';
						$thisDownloadPaths .= $attachmentPath." ";
					}					
	
					//SEND NOTIFICATION//
					// Set mail function
					$mail = new PHPMailer; // defaults to using php "mail()"
					
					$body = "<html><head>
					<title>$Subject</title>
					</head><body>".$FIELDS['Message']."</body></html>";
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
					
					$mail->setFrom($FIELDS['FromAdd'], $FIELDS['FromName']);
					$mail->Subject = $FIELDS['Subject'];
					$mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; 		
					$mail->msgHTML($body);
					$mail->isHTML(true); // send as HTML
					//Grab recipient emails
					$ToRecipients = explode(" ", $FIELDS['thisToEmails']);
					$thisToRecipients = array_unique($ToRecipients);
					// Add To recipients
					foreach($thisToRecipients as $sendToEmail){
						$mail->addAddress($sendToEmail);
					}
					$CcRecipients = explode(" ", $FIELDS['thisCcEmails']);
					$thisCcRecipients = array_unique($CcRecipients);
					// Add Cc recipients
	
					foreach($thisCcRecipients as $sendCcEmail){
						if(!empty($sendCcEmail)){
							$mail->addCC($sendCcEmail);
						}
					}
					$BccRecipients = explode(" ", $FIELDS['thisBccEmails']);
					$thisBccRecipients = array_unique($BccRecipients);
					// Add Bcc recipients
					foreach($thisBccRecipients as $sendBccEmail){
						if(!empty($sendBccEmail)){
							$mail->addBCC($sendBccEmail);
						}
					}
					
					// Add Attachments
					$thisAttachments = explode(" ", $thisDownloadPaths);
					foreach($thisAttachments as $thisDownloadPath){
						if(!empty($thisDownloadPath)){
							$mail->addAttachment($thisDownloadPath);
						}
					}
					
					if(!$mail->Send()) {
						$sent = false;
						$ErrPage = curPageURL();
						$MailERR = $mail->ErrorInfo;
						Error_alertAdmin("PHP Mailer","Failed to send new message-".$MailERR,$ErrPage,$Email);
						//Display Error Message
						$ERRORS['MSG'] = ErrorMessage("Failed to send your email. Please try again later.");
					}else{
						$sent = true;
						//Display Confirmation Message
						$_SESSION['MSG'] = ConfirmMessage("Message sent successfully");
						redirect("admin.php?tab=7&recid=$recid");
					}
				}else{
					$sent = false;
					//Display Error Message
					$ERRORS['MSG'] = ErrorMessage("Message not sent. Try again later...");
				}
			}else{
				$ERRORS['MSG'] = ErrorMessage("Message not sent! Check if all email addresses are correctly formatted.");
			}
		}
	}
	
	$res = sql_select();
	$count = sql_getrecordcount();
	db_data_seek($res, $recid);
	$row = db_fetch_array($res);
		
	$row['Type'] = !empty($FIELDS['Type'])?$FIELDS['Type']:$row["Type"];
	
	if($action == "reply"){
		//Take care of an outbox reply (or it will end up replying to the sender)
		if($_SESSION['sysEmail'] == $row['FromAdd']){
			$row['ToAdd'] = $row['ToAdd'];
			$row['FromAdd'] = $_SESSION['sysEmail'];
		}else{
			$row['ToAdd'] = $row['FromAdd'];
			$row['FromAdd'] = $_SESSION['sysEmail'];			
		}
		
		$row['Subject'] = "RE: ".$row['Subject'];
		$Message = "<br><br>
		<div style=\"border-left:#3366FF 1px solid; margin-left:10px; padding-left:5px; height:auto;\">
		-------- Original Message --------<br>
		Subject: ".$row['Subject']."<br>
		From: ".$row['FromAdd']."<br>
		Date: ".fixdatelong($row['DateSent'])."<br>
		To: ".$row['ToAdd']."<br>";
		if(!empty($row['CcAdd'])) { $Message .= "Cc: ".$row['CcAdd']."<br>"; }
		if(!empty($row['BccAdd'])) { $Message .= "Bcc: ".$row['BccAdd']."<br>"; }
		$Message .= $row['Message']."</div><br>";
		
		$row['Message'] = $Message;
		
		$row['CcAdd'] = "";
		$row['BccAdd'] = "";
	}
	if($action == "replyall"){
		//Take care of an outbox reply (or it will end up replying to the sender)
		if($_SESSION['sysEmail'] == $row['FromAdd']){
			$row['ToAdd'] = $row['ToAdd'];
		}else{
			$row['ToAdd'] = $row['FromAdd'];
		}
		
		$AddCcRecipients = explode(" ", $row['CcAdd']);
		// Add Cc recipients (comma separated)
		$row['CcAdd'] = implode(", ", $AddCcRecipients);
		$AddBccRecipients = explode(" ", $row['BccAdd']);
		// Add Bcc recipients (comma separated)
		$row['BccAdd'] = implode(", ", $AddBccRecipients);
		
		$row['Subject'] = "RE: ".$row['Subject'];
		$Message = "<br><br>
		<div style=\"border-left:#3366FF 1px solid; margin-left:10px; padding-left:5px; height:auto;\">
		-------- Original Message --------<br>
		Subject: ".$row['Subject']."<br>
		From: ".$row['FromAdd']."<br>
		Date: ".fixdatelong($row['DateSent'])."<br>
		To: ".$row['ToAdd']."<br>";
		if(!empty($row['CcAdd'])) { $Message .= "Cc: ".$row['CcAdd']."<br>"; }
		if(!empty($row['BccAdd'])) { $Message .= "Bcc: ".$row['BccAdd']."<br>"; }
		$Message .= $row['Message']."</div><br>";
		
		$row['Message'] = $Message;
	}
	
?>
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?tab=7">Messages</a></li><li class="active">Read Message</li></ol>

<a class="btn btn-default" href="admin.php?tab=7&task=view&recid=<?=$recid?>"><i class="fa fa-undo fa-fw"></i> Back to Messages</a>

<form class="form-horizontal" id="validateform" enctype="multipart/form-data" action="admin.php?tab=7&task=<?=$action?>&recid=<?=$recid?>" method="post">
<p class="text-center"><?php if(sizeof($ERRORS['MSG'])>0) echo $ERRORS['MSG'];?></p>
<input type="hidden" name="sql" value="update">
<input type="hidden" name="eid" value="<?=$row["UID"] ?>">
<?php showroweditor($row, true, $ERRORS); ?>
<p class="text-center">
<input type="hidden" name="FromAdd" value="<?=$row['FromAdd']?>">
<input type="hidden" name="Type" value="<?=$row['Type']?>">
<input class="btn btn-primary" type="submit" name="Reply" value="<?=ucwords($action)?>">
<input class="btn btn-default" type="button" name="cancel" value="Cancel" onclick="javascript:location.href='admin.php?tab=7'">
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
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?tab=7">Messages</a></li><li class="active">Delete Message</li></ol>
<?php showrecnav("del", $recid, $count); ?>
<form action="admin.php?tab=7&task=del&recid=<?=$recid?>" method="post">
<input type="hidden" name="sql" value="delete">
<input type="hidden" name="eid" value="<?=$row["UID"] ?>">
<?php showrow($row, $recid) ?>
<div class="cms-list-table-navigation"><strong>Are you sure you want to delete this record? </strong><input class="btn btn-primary" type="submit" name="Delete" value="Yes"><input class="btn btn-default" type="button" name="Ignore" value="No" onclick="javascript:history.go(-1)"></div>
</form>
<?php
db_free_result($res);
}
?>
<?php
function sql_select(){
	global $conn;
	global $SysEmail;
	global $folder;
	
	if($folder == "outbox"){
		$sql = sprintf("SELECT `UID`,`FromAdd`,`ToAdd`,`CcAdd`,`BccAdd`,`Subject`,`Message`,`DateSent`,`DateRead`,`Type`,`Source` FROM `".DB_PREFIX."messages` WHERE `FromAdd` = '%s'", $SysEmail);
		$res = db_query($sql,DB_NAME,$conn);
	}else{		
		$sql = "SELECT `UID`,`FromAdd`,`ToAdd`,`CcAdd`,`BccAdd`,`Subject`,`Message`,`DateSent`,`DateRead`,`Type`,`Source` FROM `".DB_PREFIX."messages` WHERE (`ToAdd` = '$SysEmail' OR `CcAdd` = '$SysEmail' OR `BccAdd` = '$SysEmail')";
		$res = db_query($sql,DB_NAME,$conn);
	}	
	return $res;
}

function sql_getrecordcount(){
	global $conn;
	global $SysEmail;
	global $folder;
	
	if($folder == "outbox"){		
		$sql = sprintf("SELECT COUNT(*) FROM `".DB_PREFIX."messages` WHERE `FromAdd` = '%s'", $SysEmail);		
		$res = db_query($sql,DB_NAME,$conn);
		$row = db_fetch_array($res);
		reset($row);	
	}else{		
		$sql = "SELECT COUNT(*) FROM `".DB_PREFIX."messages` WHERE (`ToAdd` = '$SysEmail' OR `CcAdd` = '$SysEmail' OR `BccAdd` = '$SysEmail')";		
		$res = db_query($sql,DB_NAME,$conn);
		$row = db_fetch_array($res);
		reset($row);
	}
	return current($row);
}

function sql_insert($FIELDS){
	global $conn;
	
	//Add new client
	$sql = sprintf("INSERT INTO `".DB_PREFIX."messages` (`FromAdd`,`ToAdd`,`CcAdd`,`BccAdd`,`Subject`,`Message`,`DateSent`,`Type`,`Source`) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')", $FIELDS['FromAdd'], $FIELDS['thisToEmails'], $FIELDS['thisCcEmails'], $FIELDS['thisBccEmails'], $FIELDS['EncodedSubject'], $FIELDS['EncodedMessage'], $FIELDS['DateSent'], $FIELDS['Type'], $FIELDS['Source']);
	db_query($sql,DB_NAME,$conn);
	
	//Check if saved
    if(db_affected_rows($conn)){
		return true;
	}else{
		return false;
	}
}

function sql_delete(){
	global $conn;
	
	$sql = "DELETE FROM `".DB_PREFIX."messages` WHERE " .primarykeycondition();
	db_query($sql,DB_NAME,$conn);
	
	//Check if saved
	if(db_affected_rows($conn)){
		$_SESSION['MSG'] = ConfirmMessage("Message has been deleted successfully");
	}else{
		$_SESSION['MSG'] = ErrorMessage("Failed to delete selected client. Please try again later...");
	}
	redirect("admin.php?tab=7");
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