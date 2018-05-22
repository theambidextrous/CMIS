<?php 
/*********************************************
Company:		Wits Technologies Ltd
Developer:	Sammy Mwaura Waweru
Mobile:			+254721428276
Email:			sammy@witstechnologies.co.ke
Website:		http://www.witstechnologies.co.ke/
*********************************************/

//Import the PHPMailer class into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// FOR WINDOWS IIS
// Let's make sure the $_SERVER['DOCUMENT_ROOT'] variable is set
if(!isset($_SERVER['DOCUMENT_ROOT'])){ if(isset($_SERVER['SCRIPT_FILENAME'])){
$_SERVER['DOCUMENT_ROOT'] = str_replace( '\\', '/', substr($_SERVER['SCRIPT_FILENAME'], 0, 0-strlen($_SERVER['PHP_SELF'])));
}; };
if(!isset($_SERVER['DOCUMENT_ROOT'])){ if(isset($_SERVER['PATH_TRANSLATED'])){
$_SERVER['DOCUMENT_ROOT'] = str_replace( '\\', '/', substr(str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']), 0, 0-strlen($_SERVER['PHP_SELF'])));
}; };

//
function add_title($sitename,$pagetitle){
	if( !empty( $sitename ) && !empty( $pagetitle ) ) {
		return $sitename.' - '.$pagetitle;
	}else{
		return $sitename;
	}
}
//
function add_header( $section = "" ){
	
	if( !empty( $section ) ){
		require_once THEME_DIR.DIRECTORY_SEPARATOR.'header-'. $section .'.php';
	}else{
		require_once THEME_DIR.DIRECTORY_SEPARATOR.'header.php';
	}

}
//
function add_footer( $section = "" ){
	
	if( !empty( $section ) ){
		require_once THEME_DIR.DIRECTORY_SEPARATOR.'footer-'. $section .'.php';
	}else{
		require_once THEME_DIR.DIRECTORY_SEPARATOR.'footer.php';
	}
}

function add_google_analytics( $TrackingID ){
	return "
	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src=\"https://www.googletagmanager.com/gtag/js?id=".$TrackingID."\"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());
		gtag('config', '".$TrackingID."');
	</script>
	";
}

/* Confirm/Success Message */
function ConfirmMessage($str){
	return "<div class=\"alert alert-success alert-dismissible\" role=\"alert\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button><i class=\"fa fa-thumbs-up fa-fw\"></i> $str </div>";
}
/* Attention/Info Message */
function AttentionMessage($str){
	return "<div class=\"alert alert-info alert-dismissible\" role=\"alert\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button><i class=\"fa fa-exclamation-triangle fa-fw\"></i> $str </div>";
}
/* Warning Message */
function WarnMessage($str){
	return "<div class=\"alert alert-warning alert-dismissible\" role=\"alert\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button><i class=\"fa fa-exclamation-triangle fa-fw\"></i> $str </div>";
}
/* Error/Danger Message */
function ErrorMessage($str){
	return "<div class=\"alert alert-danger alert-dismissible\" role=\"alert\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button><i class=\"fa fa-thumbs-down fa-fw\"></i> $str </div>";	
}

/* Reverse magic_quotes_gpc/magic_quotes_sybase effects on those vars if ON. */
if (get_magic_quotes_gpc()) {
	$_GET = array_map("strip_slashes_recursive", $_GET);
	$_POST = array_map("strip_slashes_recursive", $_POST);
	$_SESSION = array_map("strip_slashes_recursive", $_SESSION);
	$_COOKIE = array_map("strip_slashes_recursive", $_COOKIE);
}
//
function add_slashes_recursive( $variable ){
	if(is_string($variable)){
		return addslashes($variable);
	}
	elseif(is_array($variable)){
		foreach($variable as $i => $value){
			$variable[$i] = add_slashes_recursive($value);
		}
	}
	return $variable;
}
//
function strip_slashes_recursive($variable){
	if(is_string($variable)){
		return stripslashes($variable);
	}
	elseif(is_array($variable)){
		foreach($variable as $i => $value){
			$variable[$i] = strip_slashes_recursive($value);
		}
	}
	return $variable; 
}
//Recursive trimming with array support
function trimming($arr){
	return is_array($arr) ? array_map('trimming', strip_slashes_recursive($arr)) : trim(strip_slashes_recursive($arr));
}
//DB Secure String
function secure_string($string){
	//ADDED ARRAY CHECK
	$trimmedStr = trimming($string);
	///proceed to magic q
	if (get_magic_quotes_gpc()) {
		return $trimmedStr;
	}else{
		return add_slashes_recursive($trimmedStr);
	}	
}
//
function encode($string){
	return htmlentities($string, ENT_QUOTES);
}
//
function decode($string){
	return html_entity_decode($string, ENT_QUOTES);
}
// Truncate a string to given length
function truncate($value,$length){
	if(strlen($value)>$length){
		$value=substr($value,0,$length);
		$n=0;
		while(substr($value,-1)!=chr(32)){
			$n++;
			$value=substr($value,0,$length-$n);
		}
		$value=$value." ...";
	}
	return clean_string($value);
	//return $value;
}
function clean_string($string){
	//$string = preg_replace('/\s*$^\s*/m', "\n", $string);
	//return preg_replace('/[ \t]+/', ' ', $string);
	return preg_replace('/[ \t]+/', ' ', preg_replace('/\s*$^\s*/m', "\n", $string));
}

// Performs explode() on a string with the given delimiter and trims all whitespace for the elements
function explode_trim($str, $delimiter = ',') { 
    if ( is_string($delimiter) ) { 
        $str = trim(preg_replace('|\\s*(?:' . preg_quote($delimiter) . ')\\s*|', $delimiter, $str)); 
        return explode($delimiter, $str); 
    } 
    return $str; 
} 
// Performs a whitespace cleanup
function whitespace_trim($str){
	$string = preg_replace('/\s+/', '', $str);
	return $string;
}
//format date("d-m-Y")
function fixdate($date){
	if(empty($date) || $date == "0000-00-00" || $date == "0000-00-00 00:00:00"){
		return NULL;
	}
	else{
		return date("d-m-Y", strtotime($date));
	}
}
//DOB short date format dS, M
function fixdateshortdob($date){
	if(empty($date) || $date == "0000-00-00" || $date == "0000-00-00 00:00:00"){
		return NULL;
	}
	else{
		return date("d\<\s\u\p\>S\<\/\s\u\p\>, M", strtotime($date));
	}
}
//format date("d/m/Y")
function fixdateshort($date){
	if(empty($date) || $date == "0000-00-00" || $date == "0000-00-00 00:00:00"){
		return NULL;
	}
	else{
		return date("d/m/Y", strtotime($date));
	}
}
//format date("M j, Y")
function fixdatelong($date){
	if(empty($date) || $date == "0000-00-00" || $date == "0000-00-00 00:00:00"){
		return NULL;
	}
	else{
		return date("M j, Y", strtotime($date));
	}
}
//format datetime date("d-m-Y H:i:s")
function fixdatetime($datetime){
	if(empty($datetime) || $datetime == "0000-00-00 00:00:00" || $datetime == "0000-00-00"){
		return NULL;
	}
	else{
		return date("d-m-Y H:i:s", strtotime($datetime));
	}
}
//format datetime date("M j, Y H:i:s");
function fixdatetimelong($datetime){
	if(empty($datetime) || $datetime == "0000-00-00 00:00:00" || $datetime == "0000-00-00"){
		return NULL;
	}
	else{
		return date("M j, Y H:i:s", strtotime($datetime));
	}
}
//format date("d/m/Y")
function fixdatepicker($date){
	if(empty($date) || $date == "0000-00-00" || $date == "0000-00-00 00:00:00"){
		return NULL;
	}
	else{
		return date("m/d/Y", strtotime($date));
	}
}
//format date("Y-m-d")
function db_fixdate($date){
	if(empty($date)){
		return NULL;
	}
	else{
		return date("Y-m-d", strtotime($date));
	}
}
//format datetime date("Y-m-d H:i:s")
function db_fixdatetime($datetime){
	if(empty($datetime)){
		return NULL;
	}
	else{
		return date("Y-m-d H:i:s", strtotime($datetime));
	}
}
//show how long ago, given datetime format date("Y-m-d H:i:s")
function formatDateAgo($value){
	if(!empty($value)){
		$time = strtotime($value);
		$d = new \DateTime($value);
	
		$weekDays = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
		$months = array('January', 'February', 'March', 'April',' May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
	
		if ($time > strtotime('-2 minutes')){
			return 'A few seconds ago';
		}
		elseif ($time > strtotime('-30 minutes')){
			return floor((strtotime('now') - $time)/60) . ' mins ago';
		}
		elseif ($time > strtotime('today')){
			return $d->format('G:i');
		}
		elseif ($time > strtotime('yesterday')){
			return 'Yesterday, ' . $d->format('G:i');
		}
		elseif ($time > strtotime('this week')){
			return $weekDays[$d->format('N') - 1] . ', ' . $d->format('G:i');
		}
		else{
			return $d->format('j') . ' ' . $months[$d->format('n') - 1] . ', ' . $d->format('G:i');
		}
	}else{
		return "unknown";
	}
}
function timeago($date) {	
	$timestamp = strtotime($date);	
	
	$strTime = array("second", "minute", "hour", "day", "month", "year");
	$length = array("60","60","24","30","12","10");
	
	$currentTime = time();
	if($currentTime >= $timestamp) {	 
		$diff = time()- $timestamp;
		for($i = 0; $diff >= $length[$i] && $i < count($length)-1; $i++) {
			$diff = $diff / $length[$i];
		}
		
		$diff = round($diff);
		return $diff . " " . $strTime[$i] . "(s) ago ";
	}else{
		return fixdatetimelong($date);
	}
}
//Generate password
function hashedPassword($str_pass) {
	return password_hash($str_pass, PASSWORD_DEFAULT);
}
//Redirect function
function redirect($to) {
    if (!headers_sent())
        header('Location: '.$to);
    else {
        return '<script type="text/javascript">
        window.location.href="'.$to.'";
        </script>
        <noscript>
        <meta http-equiv="refresh" content="0;url='.$to.'">
        </noscript>';
    }
	exit();
}
//This function separates the extension from the rest of the file name and returns it
//For Instance, jpg, gif or png
function findexts($filename) { 
	$filename = strtolower($filename); 
	if(!empty($filename)){
		/*
		//option 1
		return substr(strrchr($filename,'.'),1);	
		//option 2
		$exts = explode('.', $filename);
		$exts = end($exts);
		return $exts;
		*/
		$exts = pathinfo($filename, PATHINFO_EXTENSION);
		return $exts; 
	}		
}
//Remove apostrophes on names
function removeApostrophe($str){
	$stripped = strip_slashes_recursive($str);
	return str_replace("'", "", $stripped);
}
//Return current page URL
function curPageURL() {
	$pageURL = 'http';
	if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}
// Get user IP
function getUserIP() {
    if( array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
        if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')>0) {
            $addr = explode(",",$_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($addr[0]);
        } else {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
    }
    else {
        return $_SERVER['REMOTE_ADDR'];
    }
}
//Saves error logs to a folder within the website (logs). System admin can view these logs at the back-end
function saveSysErrLogs($err_log){
	global $logs_dir;
	
	$filename = "$logs_dir/system_logs.txt";
	
	// Let's make sure the file exists and is writable first.
	if (is_writable($filename)) {	
		// Write the contents to the file, 
		// using the FILE_APPEND flag to append the content to the end of the file
		// and the LOCK_EX flag to prevent anyone else writing to the file at the same time
		if(file_put_contents($filename, $err_log, FILE_APPEND | LOCK_EX) === FALSE){
			return false;
		}
		return true;
	}else{
		return false;
	}
}
//Capture error and send
function Error_alertAdmin($error_type, $error_msg, $page, $reply_to){
	//Variables
	global $class_dir;
	include "config.php";
	require_once("$class_dir/phpmailer/src/Exception.php");
	require_once("$class_dir/phpmailer/src/PHPMailer.php");
	require_once("$class_dir/phpmailer/src/SMTP.php");
	
	$Subject = SYSTEM_SHORT_NAME." Error Report Generated ".date('d_m_Y');
	$Log_date = date('d-m-Y H:i:s');
	$Source = getUserIP();
	$MySQL_Version = db_version();
	$PHP_Version = phpversion();

	//SAVE ERROR LOG TO FILE//
	$ErrorLog = "<p><strong>Log Date:</strong> $Log_date<br>
	<strong>Error Type:</strong> $error_type<br>
	<strong>Page:</strong> $page<br>
	<strong>Error Captured:</strong> $error_msg</p>";
	
	saveSysErrLogs($ErrorLog);
	
	//SEND ERROR ALERTS//
	// Mail function
	$mail = new PHPMailer; // defaults to using php "mail()"
	
	//safe error capture
		$Message = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
	<title>$Subject</title>
	</head><body><p>Dear Administrator, <br><br>The following error was reported on '.SYSTEM_SHORT_NAME.' website. <br>Error Type: '.$error_type.'<br>Page: '.$page.'<br>Log Date: '.$Log_date.'<br>MySQL Version: '.$MySQL_Version.'<br>PHP Version: '.$PHP_Version.'<br>Error Captured: <br><strong>'.$error_msg.'</strong><br><br>'.strtoupper(SYSTEM_SHORT_NAME).' ERROR NOTIFICATIONS<br>Website: '.PARENT_HOME_URL.'</p></body>
	</html>';
	
	$body = preg_replace('/\\\\/','', $Message); //Strip backslashes
	
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
	
	$mail->setFrom(INFO_EMAIL, INFO_NAME);	
	$mail->Subject = $Subject;
	$mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; 		
	$mail->msgHTML($body);
	$mail->isHTML(true); // send as HTML
	//$mail->addAddress(SUPPORT_EMAIL, SUPPORT_NAME); //Notify Support Team
	$mail->addAddress(DEVELOPER_EMAIL, DEVELOPER_NAME); //Notify Webmaster
	
	if(isset($reply_to)) { $mail->addReplyTo($reply_to); } //Add REPLY-TO if provided
	
	// Send email to the website administrator
	if($mail->Send()){
		return true;
	}else{
		return false;
	}
	
}
//Allowed documents for upload
function allowed_doc_mime_types($type="all"){
	switch($type){
		case "all":
		return array(
			'image/gif',
			'image/jpg',
			'image/jpeg',
			'image/png',
			'image/psd',
			'image/bmp',
			'image/tiff',
			'image/jp2',
			'image/iff',
			'image/vnd.wap.wbmp',
			'image/xbm',
			'image/vnd.microsoft.icon',
			'image/webp',
			'application/octet-stream',
			'application/x-shockwave-flash',
			'text/richtext',
			'application/pdf',
			'application/msword',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'application/vnd.ms-excel',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'application/vnd.ms-powerpoint',
			'application/vnd.openxmlformats-officedocument.presentationml.presentation');
		break;
		case "documents":
		return array(
			'text/richtext',
			'application/pdf',
			'application/msword',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'application/vnd.ms-excel',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'application/vnd.ms-powerpoint',
			'application/vnd.openxmlformats-officedocument.presentationml.presentation');
		break;
		case "images":
		return array(
			'image/gif',
			'image/jpg',
			'image/jpeg',
			'image/png',
			'image/psd',
			'image/bmp',
			'image/tiff',
			'image/jp2',
			'image/iff',
			'image/vnd.wap.wbmp',
			'image/xbm',
			'image/vnd.microsoft.icon',
			'image/webp',
			'application/octet-stream',
			'application/x-shockwave-flash');
		break;
		case "videos":
		break;
	}	
}
//Generate a friendly name
function friendlyName($name){//post slug
	$name= mb_strtolower(replace_accents($name), 'UTF-8');
	return preg_replace(array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'), array('','-',''),$name);
}
//Help generate friendly name
function replace_accents($var){ //replace for accents catalan spanish and more
    $a = array('?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', '?', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', '?', '?', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', '?', '?', 'L', 'l', 'N', 'n', 'N', 'n', 'N', 'n', '?', 'O', 'o', 'O', 'o', 'O', 'o', '?', '?', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', '?', '?', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', '?', 'Z', 'z', 'Z', 'z', '?', '?', '?', '?', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', '?', '?', '?', '?', '?', '?');
    $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
    $var= str_replace($a, $b,$var);
    return $var;
}
//Check for duplicate entry
function checkDuplicateEntry($sqlCheck){
	global $conn;
	//Set the result and run the query
	$resultCheck = db_query($sqlCheck,DB_NAME,$conn);
	//check if any results were returned
	if(db_num_rows($resultCheck)>0){
		return true;
	}else{
		return false;
	}
}
//generates a select tag with the values specified on the sql, 2nd parameter name for the combo, , 3rd value selected if there's
function sqlOption($query,$name,$option,$empty="",$error="",$required=""){
	global $conn;
	
	$result = db_query($query,DB_NAME,$conn);//$query: 1 value needs to be the ID, second the Name, if there's more doens't work	
	$sqloption = "<select ".$error." name=\"".$name."\" id=\"".$name."\" class=\"form-control ".$required."\">";
	$sqloption .= !empty($empty)?"<option value=\"None\">".$empty."</option>":"";
	if(db_num_rows($result)>0) {
	  while($row = db_fetch_array($result,$mode=true)){
		  if ($option==$row[0]) { $sel="selected=selected";}
		  $sqloption .=  '<option '.$sel.' value="'.$row[0].'">' .$row[1]. '</option>';
		  $sel="";
	  }
	}
	$sqloption .= "</select>";
	return $sqloption;
}
//generates a select tag with the values specified on the sql, 2nd parameter name for the combo, , 3rd value selected if there's
function sqlOptionGroup($query,$name,$option,$empty="",$error="",$required=""){
	global $conn;
	
	$result = db_query($query,DB_NAME,$conn);//$query: 1 value needs to be the ID, second the Name, 3rd is the group
	//echo $sql;
	$sqloption = "<select ".$error." name=\"".$name."\" id=\"".$name."\" class=\"form-control ".$required."\">";
    $sqloption .= !empty($empty)?"<option value=\"None\">".$empty."</option>":"";
	$lastLabel = "";
	if(db_num_rows($result)>0) {
	  while($row = db_fetch_array($result,$mode=true)){
  
		  if($lastLabel != $row[2]){
			  if($lastLabel != ""){
				  $sqloption .= "</optgroup>";
			  }
			  $sqloption .= "<optgroup label='$row[2]'>";
			  $lastLabel = $row[2];
		  }
  
		  if ($option==$row[0]) { $sel="selected=selected";}
		  $sqloption .=  '<option '.$sel.' value="'.$row[0].'">' .$row[1]. '</option>';
		  $sel="";
	  }
	  $sqloption .= "</optgroup>";
	}
	$sqloption .= "</select>";
	return $sqloption;
}
//generates a select with multi select options
function sqlOptionMulti($query,$name,$options,$error="",$required=""){
	global $conn;
	
	$result = db_query($query,DB_NAME,$conn);//$query: 1 value needs to be the ID, second the Name, if there's more doens't work
	$sqloption = "<select ".$error." name=\"".$name."[]\" id=\"".$name."\" class=\"form-control ".$required."\" multiple=\"multiple\" rows=\"20\">";
	while($row = db_fetch_array($result,$mode=true)){		
		$values = explode(",", $options);
		if(in_array($row[0], $values)){
			$sel='selected="selected"';
		}else{
			$sel="";
		}
		$sqloption .=  '<option '.$sel.' value="'.$row[0].'">' .$row[1]. '</option>';
		$sel="";
	}
	$sqloption .= "</select>";
	return $sqloption;
}
//generates a select tag with the values specified on the sql, 2nd parameter name for the combo, , 3rd value selected if there's
function sqlOptionGroupMulti($query,$name,$option,$empty="",$error="",$required=""){
	global $conn;
	
	$result = db_query($query,DB_NAME,$conn);//$query: 1 value needs to be the ID, second the Name, 3rd is the group
	//echo $sql;
	$sqloption = "<select ".$error." name=\"".$name."[]\" id=\"".$name."\" class=\"form-control ".$required."\" multiple=\"multiple\" rows=\"20\">";
    $sqloption .= !empty($empty)?"<option value=\"None\">".$empty."</option>":"";
	$lastLabel = "";
	if(db_num_rows($result)>0) {
	  while($row = db_fetch_array($result,$mode=true)){
  
		  if($lastLabel != $row[2]){
			  if($lastLabel != ""){
				  $sqloption .= "</optgroup>";
			  }
			  $sqloption .= "<optgroup label='$row[2]'>";
			  $lastLabel = $row[2];
		  }
  
		  if ($option==$row[0]) { $sel="selected=selected";}
		  $sqloption .=  '<option '.$sel.' value="'.$row[0].'">' .$row[1]. '</option>';
		  $sel="";
	  }
	  $sqloption .= "</optgroup>";
	}
	$sqloption .= "</select>";
	return $sqloption;
}
//
function get_yesno_status($status){
	foreach(list_yesno_status() as $k => $v){
		if($k == $status){
			return $v;
		}
	}
}
function get_experience_years($experience){
	foreach(list_experience_years() as $k => $v){
		if($k == $experience){
			return $v;
		}
	}
}
//Array of yes/no status
function list_yesno_status(){
	return array(
	"1" => "Yes",
	"0" => "No");
}
//get user types
function list_portal_user_types(){
	return array(
	"Student" => "Student",
	"Faculty" => "Faculty");
}
//Array of enable/ isable status
function list_enable_status(){
	return array(
	"0" => "Yes",
	"1" => "No");
}
//Array of title status
function list_gender_status(){
	return array(	
	"Male" => "Male",
	"Female" => "Female");
}
//Array of title status
function list_title_status(){
	return array(	
	"Dr." => "Dr.",
	"Prof." => "Prof.",
	"Fr." => "Fr.",	
	"Mr." => "Mr.",
	"Mrs." => "Mrs.",
	"Miss." => "Miss.",
	"Rev." => "Rev.",
	"Pr." => "Pr.");
}
//Array of sponsorships
function list_sponsorships(){
	return array(
	"Self" => "Self",
	"Guardian" => "Guardian",
	"Sponsor" => "Sponsor");
}
//Array of education levels
function list_education_levels(){
	return array(
	"CERTIFICATE" => "CERTIFICATE",
	"DIPLOMA" => "DIPLOMA",
	"INTERNATIONAL COURSE" => "INTERNATIONAL COURSE",
	"HRMPEB" => "HRMPEB",
  "SHORT COURSE" => "SHORT COURSE",
	"KASNEB" => "KASNEB");
}
//Get trimester name given the trimester shortcode
function get_year_trimesters($yr_trim){
	foreach(list_year_trimesters() as $k => $v){
		if($k == $yr_trim){
			return $v;
		}
	}
}
//Array of academic trimesters
function list_year_trimesters(){
	return array(
	"1/1" => "1st yr/1st trimester",
	"1/2" => "1st yr/2nd trimester",
	"1/3" => "1st yr/3rd trimester",
	"2/1" => "2nd yr/1st trimester",
	"2/2" => "2nd yr/2nd trimester",
	"2/3" => "2nd yr/3rd trimester",
	"3/1" => "3rd yr/1st trimester",
	"3/2" => "3rd yr/2nd trimester",
	"3/3" => "3rd yr/3rd trimester",
	"4/1" => "4th yr/1st trimester",
	"4/2" => "4th yr/2nd trimester",
	"4/3" => "4th yr/3rd trimester",
	"5/1" => "5th yr/1st trimester",
	"5/2" => "5th yr/2nd trimester",
	"5/3" => "5th yr/3rd trimester");
}
//Array of lesson upload types
function list_lesson_uploadtypes(){
	return array(
	"ut_content" => "Content (e.g. Article)",
	"ut_video" => "Video (e.g. YouTube)",
	"ut_link" => "Link (e.g. Reference, Document)");
}
//Array of navigation list
function list_display_limiter(){
	return array(
	"10" => "10",
	"30" => "30",
	"50" => "50",
	"100" => "100",
	"150" => "150",
	"200" => "200");
}
//Get country name given the country shortcode
function get_country($shortcode){
	foreach(list_countries() as $k => $v){
		if($k == $shortcode){
			return $v;
		}
	}
}
//Array of countries in the world
function list_countries(){
	return array(
	"AF" => "Afghanistan",
	"AL" => "Albania",
	"DZ" => "Algeria",
	"AS" => "American Samoa",
	"AD" => "Andorra",
	"AO" => "Angola",
	"AI" => "Anguilla",
	"AQ" => "Antarctica",
	"AG" => "Antigua And Barbuda",
	"AR" => "Argentina",
	"AM" => "Armenia",
	"AW" => "Aruba",
	"AU" => "Australia",
	"AT" => "Austria",
	"AZ" => "Azerbaijan",
	"BS" => "Bahamas",
	"BH" => "Bahrain",
	"BD" => "Bangladesh",
	"BB" => "Barbados",
	"BY" => "Belarus",
	"BE" => "Belgium",
	"BZ" => "Belize",
	"BJ" => "Benin",
	"BM" => "Bermuda",
	"BT" => "Bhutan",
	"BO" => "Bolivia",
	"BA" => "Bosnia And Herzegowina",
	"BW" => "Botswana",
	"BV" => "Bouvet Island",
	"BR" => "Brazil",
	"IO" => "British Indian Ocean Territory",
	"BN" => "Brunei Darussalam",
	"BG" => "Bulgaria",
	"BF" => "Burkina Faso",
	"BI" => "Burundi",
	"KH" => "Cambodia",
	"CM" => "Cameroon",
	"CA" => "Canada",
	"CV" => "Cape Verde",
	"KY" => "Cayman Islands",
	"CF" => "Central African Republic",
	"TD" => "Chad",
	"CL" => "Chile",
	"CN" => "China",
	"CX" => "Christmas Island",
	"CC" => "Cocos (Keeling) Islands",
	"CO" => "Colombia",
	"KM" => "Comoros",
	"CG" => "Congo",
	"CD" => "Congo, The Democratic Republic Of The",
	"CK" => "Cook Islands",
	"CR" => "Costa Rica",
	"CI" => "Cote D'Ivoire",
	"HR" => "Croatia (Local Name: Hrvatska)",
	"CU" => "Cuba",
	"CY" => "Cyprus",
	"CZ" => "Czech Republic",
	"DK" => "Denmark",
	"DJ" => "Djibouti",
	"DM" => "Dominica",
	"DO" => "Dominican Republic",
	"TP" => "East Timor",
	"EC" => "Ecuador",
	"EG" => "Egypt",
	"SV" => "El Salvador",
	"GQ" => "Equatorial Guinea",
	"ER" => "Eritrea",
	"EE" => "Estonia",
	"ET" => "Ethiopia",
	"FK" => "Falkland Islands (Malvinas)",
	"FO" => "Faroe Islands",
	"FJ" => "Fiji",
	"FI" => "Finland",
	"FR" => "France",
	"FX" => "France, Metropolitan",
	"GF" => "French Guiana",
	"PF" => "French Polynesia",
	"TF" => "French Southern Territories",
	"GA" => "Gabon",
	"GM" => "Gambia",
	"GE" => "Georgia",
	"DE" => "Germany",
	"GH" => "Ghana",
	"GI" => "Gibraltar",
	"GR" => "Greece",
	"GL" => "Greenland",
	"GD" => "Grenada",
	"GP" => "Guadeloupe",
	"GU" => "Guam",
	"GT" => "Guatemala",
	"GN" => "Guinea",
	"GW" => "Guinea-Bissau",
	"GY" => "Guyana",
	"HT" => "Haiti",
	"HM" => "Heard And Mc Donald Islands",
	"VA" => "Holy See (Vatican City State)",
	"HN" => "Honduras",
	"HK" => "Hong Kong",
	"HU" => "Hungary",
	"IS" => "Iceland",
	"IN" => "India",
	"ID" => "Indonesia",
	"IR" => "Iran (Islamic Republic Of)",
	"IQ" => "Iraq",
	"IE" => "Ireland",
	"IL" => "Israel",
	"IT" => "Italy",
	"JM" => "Jamaica",
	"JP" => "Japan",
	"JO" => "Jordan",
	"KZ" => "Kazakhstan",
	"KE" => "Kenya",
	"KI" => "Kiribati",
	"KP" => "Korea, Democratic People's Republic Of",
	"KR" => "Korea, Republic Of",
	"KW" => "Kuwait",
	"KG" => "Kyrgyzstan",
	"LA" => "Lao People's Democratic Republic",
	"LV" => "Latvia",
	"LB" => "Lebanon",
	"LS" => "Lesotho",
	"LR" => "Liberia",
	"LY" => "Libyan Arab Jamahiriya",
	"LI" => "Liechtenstein",
	"LT" => "Lithuania",
	"LU" => "Luxembourg",
	"MO" => "Macau",
	"MK" => "Macedonia, Former Yugoslav Republic Of",
	"MG" => "Madagascar",
	"MW" => "Malawi",
	"MY" => "Malaysia",
	"MV" => "Maldives",
	"ML" => "Mali",
	"MT" => "Malta",
	"MH" => "Marshall Islands",
	"MQ" => "Martinique",
	"MR" => "Mauritania",
	"MU" => "Mauritius",
	"YT" => "Mayotte",
	"MX" => "Mexico",
	"FM" => "Micronesia, Federated States Of",
	"MD" => "Moldova, Republic Of",
	"MC" => "Monaco",
	"MN" => "Mongolia",
	"MS" => "Montserrat",
	"MA" => "Morocco",
	"MZ" => "Mozambique",
	"MM" => "Myanmar",
	"NA" => "Namibia",
	"NR" => "Nauru",
	"NP" => "Nepal",
	"NL" => "Netherlands",
	"AN" => "Netherlands Antilles",
	"NC" => "New Caledonia",
	"NZ" => "New Zealand",
	"NI" => "Nicaragua",
	"NE" => "Niger",
	"NG" => "Nigeria",
	"NU" => "Niue",
	"NF" => "Norfolk Island",
	"MP" => "Northern Mariana Islands",
	"NO" => "Norway",
	"OM" => "Oman",
	"PK" => "Pakistan",
	"PW" => "Palau",
	"PA" => "Panama",
	"PG" => "Papua New Guinea",
	"PY" => "Paraguay",
	"PE" => "Peru",
	"PH" => "Philippines",
	"PN" => "Pitcairn",
	"PL" => "Poland",
	"PT" => "Portugal",
	"PR" => "Puerto Rico",
	"QA" => "Qatar",
	"RE" => "Reunion",
	"RO" => "Romania",
	"RU" => "Russian Federation",
	"RW" => "Rwanda",
	"KN" => "Saint Kitts And Nevis",
	"LC" => "Saint Lucia",
	"VC" => "Saint Vincent And The Grenadines",
	"WS" => "Samoa",
	"SM" => "San Marino",
	"ST" => "Sao Tome And Principe",
	"SA" => "Saudi Arabia",
	"SN" => "Senegal",
	"SC" => "Seychelles",
	"SL" => "Sierra Leone",
	"SG" => "Singapore",
	"SK" => "Slovakia (Slovak Republic)",
	"SI" => "Slovenia",
	"SB" => "Solomon Islands",
	"SO" => "Somalia",
	"ZA" => "South Africa",
	"GS" => "South Georgia, South Sandwich Islands",
	"ES" => "Spain",
	"LK" => "Sri Lanka",
	"SH" => "St. Helena",
	"PM" => "St. Pierre And Miquelon",
	"SD" => "Sudan",
	"SR" => "Suriname",
	"SJ" => "Svalbard And Jan Mayen Islands",
	"SZ" => "Swaziland",
	"SE" => "Sweden",
	"CH" => "Switzerland",
	"SY" => "Syrian Arab Republic",
	"TW" => "Taiwan",
	"TJ" => "Tajikistan",
	"TZ" => "Tanzania, United Republic Of",
	"TH" => "Thailand",
	"TG" => "Togo",
	"TK" => "Tokelau",
	"TO" => "Tonga",
	"TT" => "Trinidad And Tobago",
	"TN" => "Tunisia",
	"TR" => "Turkey",
	"TM" => "Turkmenistan",
	"TC" => "Turks And Caicos Islands",
	"TV" => "Tuvalu",
	"UG" => "Uganda",
	"UA" => "Ukraine",
	"AE" => "United Arab Emirates",
	"GB" => "United Kingdom",
	"US" => "United States",
	"UM" => "United States Minor Outlying Islands",
	"UY" => "Uruguay",
	"UZ" => "Uzbekistan",
	"VU" => "Vanuatu",
	"VE" => "Venezuela",
	"VN" => "Viet Nam",
	"VG" => "Virgin Islands (British)",
	"VI" => "Virgin Islands (U.S.)",
	"WF" => "Wallis And Futuna Islands",
	"EH" => "Western Sahara",
	"YE" => "Yemen",
	"YU" => "Yugoslavia",
	"ZM" => "Zambia",
	"ZW" => "Zimbabwe");
}