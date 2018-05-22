<?php
//Import the PHPMailer class into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
?>
<script src="<?=SYSTEM_URL;?>/javascript/multifile.js"></script>
<script type="text/javascript">
<!--//
//Define page title
document.title = "<?=SYSTEM_SHORT_NAME?> - Faculty | My Messages";

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
		<h1 class="page-header">My Messages</h1>
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->

<div class="row">
	<div class="col-lg-12">
		<div class="cms-contents-grey">
			<!--Begin Forms-->
			<?php
      require_once("$class_dir/class.validator.php3");
	  
      $task = isset($_GET["task"])?$_GET["task"]:"view"; 
      $task = strtolower($task);
      switch($task) {		  		  
		  case "new":
		  case "reply":
		  case "replyall":
		  case "display":      
			//Set required variable
			//Array to store the error messages
			$ERR = 'id="highlight"';//Error highlighter
			$ERRORS = array();
			$CONFIRM = array();
			$To = isset($_GET["email"])?$_GET["email"]:NULL;
			$From = isset($faculty['Email'])?$faculty['Email']:NULL;
			$FromName = isset($faculty['FacultyName'])?$faculty['FacultyName']:NULL;
			$Cc = "";
			$Bcc = "";
			$Subject = isset($_GET["subject"])?$_GET["subject"]:NULL;
			$Message = "";
			$editID = intval(! empty($_GET['eid']))?$_GET['eid']:0;
			$folder = isset($_GET["folder"])?$_GET["folder"]:"inbox";
			
			if(isset($_POST['new']) || isset($_POST['reply']) || isset($_POST['replyall'])){								
				$From = secure_string($_POST['From']);
				$To = secure_string($_POST['To']);
				$Cc = secure_string($_POST['Cc']);
				$Bcc = secure_string($_POST['Bcc']);
				$Subject = $_POST['Subject'];
				$EncodedSubject = secure_string($Subject);
				$Message = $_POST['Message'];
				$EncodedMessage = encode(secure_string($Message));
				$DateSent = date('Y-m-d H:i:s');
				$Type = !empty($_POST['Type'])?$_POST['Type']:"Message";
				$Source = getUserIP();
				//Split emails
				$ToEmails = explode_trim($To, ',');
				$CcEmails = explode_trim($Cc, ',');
				$BccEmails = explode_trim($Bcc, ',');
				
				// Validator contractor
				$check = new validator();
				// validate "To" field
				foreach($ToEmails as $ToEmail){					
					if(!$check->is_email($ToEmail))
					$ERRORS['To'] = $ERR;
				}
				// validate "Cc" field
				if(!empty($Cc)){
					foreach($CcEmails as $CcEmail){
						if(!$check->is_email($CcEmail))
						$ERRORS['Cc'] = $ERR;
					}
				}
				// validate "Bcc" field
				if(!empty($Bcc)){
					foreach($BccEmails as $BccEmail){
						if(!$check->is_email($BccEmail))
						$ERRORS['Bcc'] = $ERR;
					}
				}
				// validate "Subject" field
				if(empty($Subject))
				$ERRORS['Subject'] = $ERR;
				//Validate file type
				$allowed_mimes = allowed_doc_mime_types();
				foreach($_FILES['file']['name'] as $key => $filename){
					if(!empty($filename) && !in_array($_FILES["file"]["type"][$key], $allowed_mimes)){
						$MsgErr['FileErr'] = "Invalid file type <em>$filename</em>! Attachment can only be a document or PDF file.";
					}
				}
				
				// check for errors
				if(sizeof($ERRORS) > 0){
					$ERRORS['MSG'] = ErrorMessage("ERRORS ENCOUNTERED!");
				}
				else{
					if(sizeof($ToEmails) > 0){
						// Add To recipients (without commas)
						$thisToEmails = implode(" ", $ToEmails);
						// Add Cc recipients (without commas)
						$thisCcEmails = implode(" ", $CcEmails);
						// Add Bcc recipients (without commas)
						$thisBccEmails = implode(" ", $BccEmails);
						
						$sqlInsertMsg = sprintf("INSERT INTO `".DB_PREFIX."messages` (`FromAdd`,`ToAdd`,`CcAdd`,`BccAdd`,`Subject`,`Message`,`DateSent`,`Type`,`Source`) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')", $From, $thisToEmails, $thisCcEmails, $thisBccEmails, $EncodedSubject, $EncodedMessage, $DateSent, $Type, $Source);
						//Run query
						db_query($sqlInsertMsg,DB_NAME,$conn);
						
						if(db_affected_rows($conn)){
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
							</head><body>".$Message."</body></html>";
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
							
							$mail->setFrom($From, $FromName);
							$mail->Subject = $Subject;
							$mail->AltBody = "To view the message, please use an HTML compatible email viewer!";		
							$mail->msgHTML($body);
							$mail->isHTML(true); // send as HTML
							//Grab recipient emails
							$ToRecipients = explode(" ", $thisToEmails);
							$thisToRecipients = array_unique($ToRecipients);
							// Add To recipients
							foreach($thisToRecipients as $sendToEmail){
								$mail->addAddress($sendToEmail);
							}
							$CcRecipients = explode(" ", $thisCcEmails);
							$thisCcRecipients = array_unique($CcRecipients);
							// Add Cc recipients
							foreach($thisCcRecipients as $sendCcEmail){
								if(!empty($sendCcEmail)){
									$mail->addCC($sendCcEmail);
								}
							}
							$BccRecipients = explode(" ", $thisBccEmails);
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
								redirect("?dispatcher=messages&folder=$folder");
							}else{
								$sent = true;
								//Display Confirmation Message
								$CONFIRM['MSG'] = ConfirmMessage("Message sent successfully");
								$_SESSION['MSG'] = $CONFIRM['MSG'];
								redirect("?dispatcher=messages&folder=$folder");
							}
						}
						else{
							$sent = false;
							//Display Error Message
							$ERRORS['MSG'] = ErrorMessage("Message not sent. Try again later...");
						}
					}else{
						$sent = false;
						$ERRORS['MSG'] = ErrorMessage("Failed to send your email. Please check if you entered correct emails.");
					}
				}
				$thisEmail = $Subject = $Message = $DateSent = $Type = $Source = "";
			}
			
			//Edit/Reply
			if(!empty($editID)){
				//Fetch
				$showMessageSql = sprintf("SELECT `UID`,`FromAdd`,`ToAdd`,`CcAdd`,`BccAdd`,`Subject`,`Message`,`DateSent`,`Type` FROM `".DB_PREFIX."messages` WHERE `UID` = %d AND `disabledFlag` = %d AND `deletedFlag` = %d", $editID, 0, 0);
				//Run the query
				$result = db_query($showMessageSql,DB_NAME,$conn);
				//Fetch Data
				$thisMessage = db_fetch_array($result);
				
				$DateSent = $thisMessage['DateSent'];
				$Type = $thisMessage['Type'];
				
				if($task == "display"){
					$From = $thisMessage['FromAdd'];
					$To = $thisMessage['ToAdd'];
					$Cc = $thisMessage['CcAdd'];
					$Bcc = $thisMessage['BccAdd'];
					$Subject = $thisMessage['Subject'];
					$Message = $thisMessage['Message'];
				}
				
				if($task == "reply"){
					//Take care of an outbox reply (or it will end up replying to the sender)
					if($faculty['Email'] == $thisMessage['FromAdd']){
						$To = $thisMessage['ToAdd'];
						$From = $faculty['Email'];
					}else{
						$To = $thisMessage['FromAdd'];
						$From = $faculty['Email'];
					}
					$Subject = "RE: ".$thisMessage['Subject'];
					$Message = "<br><br>
					<div style=\"border-left:#3366FF 1px solid; margin-left:10px; padding-left:5px; height:auto;\">
					-------- Original Message --------<br>
					Subject: ".$thisMessage['Subject']."<br>
					From: ".$thisMessage['FromAdd']."<br>
					Date: ".fixdatelong($DateSent)."<br>
					To: ".$thisMessage['ToAdd']."<br>";
					if(!empty($thisMessage['CcAdd'])) { $Message .= "Cc: ".$thisMessage['CcAdd']."<br>"; }
					if(!empty($thisMessage['BccAdd'])) { $Message .= "Bcc: ".$thisMessage['BccAdd']."<br>"; }
					$Message .= $thisMessage['Message']."</div><br>";
				}
				if($task == "replyall"){
					//Take care of an outbox reply (or it will end up replying to the sender)
					if($faculty['Email'] == $thisMessage['FromAdd']){
						$To = $thisMessage['ToAdd'];
					}else{
						$To = $thisMessage['FromAdd'];
					}
					
					$AddCcRecipients = explode(" ", $thisMessage['CcAdd']);
					// Add Cc recipients (comma separated)
					$Cc = implode(", ", $AddCcRecipients);
					$AddBccRecipients = explode(" ", $thisMessage['BccAdd']);
					// Add Bcc recipients (comma separated)
					$Bcc = implode(", ", $AddBccRecipients);
					
					$Subject = "RE: ".$thisMessage['Subject'];
					$Message = "<br><br>
					<div style=\"border-left:#3366FF 1px solid; margin-left:10px; padding-left:5px; height:auto;\">
					-------- Original Message --------<br>
					Subject: ".$thisMessage['Subject']."<br>
					From: ".$thisMessage['FromAdd']."<br>
					Date: ".fixdatelong($DateSent)."<br>
					To: ".$thisMessage['ToAdd']."<br>";
					if(!empty($thisMessage['CcAdd'])) { $Message .= "Cc: ".$thisMessage['CcAdd']."<br>"; }
					if(!empty($thisMessage['BccAdd'])) { $Message .= "Bcc: ".$thisMessage['BccAdd']."<br>"; }
					$Message .= $thisMessage['Message']."</div><br>";
				}
			}
			if($task == "new" || $task == "reply" || $task == "replyall"){
				if($sent){
					echo $CONFIRM['MSG'];
				}
				else{
				?>
				<form class="form-horizontal" name="NewMessage" method="post" action="?dispatcher=messages&amp;folder=<?=$folder?>&amp;action=6&amp;task=<?=$task?>&amp;eid=<?=$editID?>" enctype="multipart/form-data">
					<p class="text-center"><?php if(sizeof($ERRORS['MSG'])>0) echo $ERRORS['MSG'];?></p>
					<p class="text-center"><strong><?=strtoupper($task)?> MESSAGE</strong></p>
					<p class="text-center"><span class="text-danger">FIELDS MARKED WITH ASTERISKS (*) ARE REQUIRED</span></p>
					<p class="text-center small">You are sending the message as<?=$From?></p>
					<div class="form-group">
						<label class="control-label col-sm-2" for="">To: <span class="text-danger">*</span></label>
						<div class="col-sm-10">
							<input <?=$ERRORS['To']?> type="text" name="To" value="<?=$To?>" class="form-control" aria-describedby="helpBlockToAdd" style="width:40%;">
							<span id="helpBlockToAdd" class="help-block"><small>Separate multiple emails with commas e.g. test1@example.com, test2@example.com</small></span>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2" for="">Cc: </label>
						<div class="col-sm-10">
							<input <?=$ERRORS['Cc']?> type="text" name="Cc" value="<?=$Cc?>" class="form-control" aria-describedby="helpBlockCcAdd" style="width:40%;">
							<span id="helpBlockCcAdd" class="help-block"><small>Separate multiple emails with commas e.g. test1@example.com, test2@example.com</small></span>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2" for="">Bcc: </label>
						<div class="col-sm-10">
							<input <?=$ERRORS['Bcc']?> type="text" name="Bcc" value="<?=$Bcc?>" class="form-control" aria-describedby="helpBlockBccAdd" style="width:40%;">
							<span id="helpBlockBccAdd" class="help-block"><small>Separate multiple emails with commas e.g. test1@example.com, test2@example.com</small></span>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2" for="">Subject: <span class="text-danger">*</span></label>
						<div class="col-sm-10">
							<input <?=$ERRORS['Subject']?> type="text" name="Subject" value="<?=$Subject?>" class="form-control" style="width:40%;">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2" for="">Attachments: </label>
						<div class="col-sm-10">
							<input id="msg_attachments" type="file" name="file[]">
							<small>(Max 5 files)</small>
						</div>
						<div class="files-list">
							<div class="col-sm-2">
							</div>
							<div class="col-sm-10">
								<ul id="files_list_msg" class="nav">
								</ul>
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
						<div class="col-sm-12">
							<textarea id="message" name="Message" class="tinymce"><?=$Message?>
					</textarea>
						</div>
					</div>
					<input type="hidden" name="From" value="<?=$From?>">
					<input type="hidden" name="Type" value="<?=$Type?>">
					<input class="btn btn-primary" type="submit" name="<?=$task?>" value="Send">
					<input class="btn btn-default" type="button" name="cancel" value="Cancel" onclick='javascript:history.go(-1)'>
				</form>
				<?php
				}
			}
			//Display/Read
			if(!empty($editID) && $task=="display"){
			$ToMsgEmails = explode(" ", $To);
			$CcMsgEmails = explode(" ", $Cc);
			$BccMsgEmails = explode(" ", $Bcc);
			//Read
			$DateRead = date('Y-m-d H:i:s');
			$sqlUpdateMsg = sprintf("UPDATE `".DB_PREFIX."messages` SET `DateRead` = '%s' WHERE `UID` = %d", $DateRead, $editID);
			//Run query
			db_query($sqlUpdateMsg,DB_NAME,$conn);
			?>
			<table align="center" border="0" cellpadding="1" cellspacing="1" width="80%">
				<tr>
					<td align="center" colspan="2"><strong>MESSAGE</strong></td>
				</tr>
				<tr>
					<td width="15%">From: </td>
					<td align="left"><?=$From?></td>
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
					<td align="left"><?=fixdatelong($DateSent)?></td>
				</tr>
				<tr>
					<td>Subject: </td>
					<td align="left"><?=$Subject?></td>
				</tr>
				<?php
				if(list_attachments($editID)){
					echo "<tr><td align=\"left\" colspan=\"2\">".list_attachments($editID)."</td></tr>";
				}
				?>
				<tr>
					<td colspan="2" valign="top"><div class="message-body-display"><?=decode($Message)?></div></td>
				</tr>
				<tr>
					<td align="center" colspan="2"><div class="btn-group"><a class="btn btn-default" href="?dispatcher=messages&amp;task=view&amp;folder=<?=$folder?>">Back</a> <a class="btn btn-default" href="?dispatcher=messages&amp;task=reply&amp;eid=<?=$editID?>&amp;folder=<?=$folder?>">Reply</a> <a class="btn btn-default" href="?dispatcher=messages&amp;task=replyall&amp;eid=<?=$editID?>&amp;folder=<?=$folder?>">Reply All</a> <a class="btn btn-default" href="?dispatcher=messages&amp;task=delete&amp;eid=<?=$editID?>&amp;folder=<?=$folder?>">Delete</a></div></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
				</tr>
			</table>
			<?php
			}
		  break;
		  case "delete":
			  $editID = intval(! empty($_GET['eid']))?$_GET['eid']:0;
			  $folder = isset($_GET["folder"])?$_GET["folder"]:"inbox";
			  
			  if(!empty($editID)){
				  $sqlDelete = sprintf("UPDATE `".DB_PREFIX."messages` SET `deletedFlag` = %d WHERE `UID` = %d", 1, $editID);
				  //Run query
				  db_query($sqlDelete,DB_NAME,$conn);
				  if(db_affected_rows($conn)){
					  $deleted = true;
					  //Display Confirmation Message
					  $_SESSION['MSG'] = ConfirmMessage("Message moved to trash successfully");
					  redirect("?dispatcher=messages&folder=$folder");
				  }else{
					  $deleted = false;
				  }
			  }
			  //If redirect fails
			  if($deleted){
				  echo ConfirmMessage("Message deleted successfully. <a href=\"?dispatcher=messages&folder=$folder\">Click here to go back to messages</a>");
			  }else{
				  echo ErrorMessage("Failed to delete message. <a href=\"?dispatcher=messages&folder=$folder\">Click here to go back to messages</a> and try again.");
			  }
		  break;
		  case "view":		
			  //Begin short scripts
			  //Delete script
			  if(isset($_POST['DELETE']) && isset($_POST['actionID'])){	
				  foreach($_POST['actionID'] as $selectedID){
					  $sqlDelete = sprintf("UPDATE `".DB_PREFIX."messages` SET `deletedFlag` = %d WHERE `UID` = %d", 1, $selectedID);
					  //Run query
					  db_query($sqlDelete,DB_NAME,$conn);
				  }
				  $_SESSION['MSG'] = ConfirmMessage("Selected messages have been deleted!");
			  }
			  //End short scripts
			  
			  $folder = isset($_GET["folder"])?$_GET["folder"]:"inbox";
			  $folder = strtolower($folder);
			  //Logged In Email
			  $UsrEmail = $faculty['Email'];
			  //Select task
			  if($folder == "inbox"){
				  //set sql
				  $messageSql = "SELECT `UID`,`FromAdd`,`ToAdd`,`Subject`,`DateSent`,`DateRead`,`Type`,`Source` FROM `".DB_PREFIX."messages` WHERE (`ToAdd` = '$UsrEmail' OR `CcAdd` = '$UsrEmail' OR `BccAdd` = '$UsrEmail') AND `Type` = 'Message' AND `disabledFlag` = 0 AND `deletedFlag` = 0";
			  }
			  else{
				  //set sql for outbox
				  $messageSql = sprintf("SELECT `UID`,`FromAdd`,`ToAdd`,`Subject`,`DateSent`,`DateRead`,`Type`,`Source` FROM `".DB_PREFIX."messages` WHERE `FromAdd` = '%s' AND `Type` = 'Message' AND `disabledFlag` = 0 AND `deletedFlag` = 0", $UsrEmail);
			  }
			  ?>
			<div id="hideMsg"><?php if(isset($_SESSION['MSG'])) echo $_SESSION['MSG'];?></div>
			<div class="quick-nav btn-group">
				<a class="btn btn-primary" href="?dispatcher=messages&task=new">Add Message</a>
			</div>
			<div style="float:right;">
				<form class="form-inline" role="form">
					<div class="form-group">
						<label for="folder">Select Folder:</label>
						<select name="jumpFolder" id="jumpFolder" onchange="MM_jumpMenu('parent',this,0)" class="form-control">
							<?php if($folder == "outbox") $folder2 = ' selected="selected"'; else $folder1 = ' selected="selected"';?>
							<option<?=$folder1?> value="?dispatcher=messages&amp;task=view&amp;folder=inbox">Inbox</option>
							<option<?=$folder2?> value="?dispatcher=messages&amp;task=view&amp;folder=outbox">Outbox</option>
						</select>
					</div>
				</form>
			</div>
			<div class="table-responsive">
				<table width="100%" class="display table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th>Sent On</th>
							<?php 
							if($folder == "inbox") {
								echo "<th>From</th>";
							}else{
								echo "<th>To</th>";
							} ?>
							<th>Subject</th>
							<th>Type</th>
							<th class="no-sort text-center" width="40"><input type="checkbox" name="master" title="Check All" onclick="checkAll(document.getElementsByName('actionID[]'));" value=""></th>
						</tr>
					</thead>
					<tbody>
						<?php
						//run the query
						$result = db_query($messageSql,DB_NAME,$conn);
						//check if any rows returned
						if(db_num_rows($result)>0){		  
							while($message = db_fetch_array($result)){
								$hid = ($row['DateRead'] == "0000-00-00 00:00:00")?'active bold':'';
								echo "<tr class=\"$hid\">
								<td>".fixdatetime($message['DateSent'])."</td>";
								if($folder == "inbox"){
									echo "<td>".$message['FromAdd']."</td>";
								}else{
									echo "<td>".$message['ToAdd']." ".$message['CcAdd']."</td>";
								}
								echo "<td><a href=\"?dispatcher=messages&task=display&amp;eid=".$message['UID']."&folder=$folder\" title=\"Click to view message\">".$message['Subject']."</a></td>
								<td>".$message['Type']."</td>";
								echo "<td align=\"center\"><input type=\"checkbox\" id=\"actionID\" name=\"actionID[]\" value=\"".$message['UID']."\"></td>";
								echo "</tr>";
							}
						}
						?>
					</tbody>
				</table>
			</div>
			<?php
			  unset($_SESSION['MSG']);
		  break;
		  default:
			  echo ErrorMessage("Permision denied! No direct access!");
      }
      ?>
			<!--End Forms-->
		</div>
	</div>
</div>
<!-- /.row -->