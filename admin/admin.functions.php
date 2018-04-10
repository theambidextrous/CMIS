<?php 
/*********************************************
Company:		Wits Technologies Ltd
Developer:	Sammy Mwaura Waweru
Mobile:			+254721428276
Email:			sammy@witstechnologies.co.ke
Website:		http://www.witstechnologies.co.ke/
*********************************************/
//SMS FUNCTIONS
function TrimSentTo($string){
	$array = explode(",", trim($string));
	$count = count($array);
	if(count($array) > 2){
		return $array[0].', '.$array[1].' and '.$count.' others';
	}else{
		return $string;
	}
}

function getAddress($group){
	global $conn;
	$sql = sprintf("SELECT * FROM `".DB_PREFIX."addressbook` WHERE `deletedFlag` = %d AND `PhoneOne` !='' AND `ContactGroup` = '%s' ", 0,$group);
	$address = [];
	$res = db_query($sql,DB_NAME,$conn);
	while($row = db_fetch_array($res)){
		array_push($address, $row);
	}
	return $address;
}
function getAddressGroups($group = "All"){
	global $conn;
	$sql = sprintf("SELECT DISTINCT ContactGroup FROM `".DB_PREFIX."addressbook` WHERE `deletedFlag` = %d AND `PhoneOne` !=''", 0);
	$group = [];
	$res = db_query($sql,DB_NAME,$conn);
	while($row = db_fetch_array($res)){
		array_push($group, $row);
	}
	return $group;
}
function makeSMS($message){
	return str_replace("[NAME]",",".PHP_EOL,$message);
}
function makeSMSOne($name, $message){
	return str_replace("[NAME]",$name.",".PHP_EOL,$message);
}
function getCleanRecipientsOne($group){
	if(!empty($group)){
		global $conn;
		$keys = array();
		$values = array();
			$sql = sprintf("SELECT Fname AS name, PhoneOne FROM `".DB_PREFIX."addressbook` WHERE `deletedFlag` = %d AND `PhoneOne` !='' AND ContactGroup ='%s'", 0, $group);
			$res = db_query($sql,DB_NAME,$conn);
				while($row = db_fetch_array($res)){
					//remove spaces
					$row['PhoneOne'] = str_replace(" ", "",$row['PhoneOne']);
					if(!empty($row['name'])){
						array_push($keys, $row['name']);
					}else{
						array_push($keys, '.');
					}
					if( substr( $row['PhoneOne'], 0, 2 ) === "07" && strlen($row['PhoneOne']) == 10 ){
						$phone = '+254'.(int)$row['PhoneOne'];
						array_push($values, $phone);
					}elseif( substr( $row['PhoneOne'], 0, 4 ) === "2547" && strlen($row['PhoneOne']) == 12 ){
						$phone = '+'.$row['PhoneOne'];
						array_push($values, $phone);
					}elseif( substr( $row['PhoneOne'], 0, 5 ) === "25407" && strlen($row['PhoneOne']) == 13 ){
						$phone = strstr($row['PhoneOne'], '0');
						$phone = '+254'.(int)$phone;
						array_push($values, $phone);
					}elseif( substr( $row['PhoneOne'], 0, 6 ) === "+25407" && strlen($row['PhoneOne']) == 14 ){
						$phone = strstr($row['PhoneOne'], '0');
						$phone = '+254'.(int)$phone;
						array_push($values, $phone);
					}elseif( substr( $row['PhoneOne'], 0, 1 ) === "7" && strlen($row['PhoneOne']) == 9 ){
						$phone = '+254'.(int)$phone;
						array_push($values, $phone);
					}elseif( substr( $row['PhoneOne'], 0, 5 ) === "+2547" && strlen($row['PhoneOne']) == 13 ){
						$phone = '+254'.(int)$phone;
						array_push($values, $phone);
					}else{
						//array_push($values, $row['PhoneOne']);	
					}
				}
	//combine keys & values
	$recipients = array();
	foreach ($keys as $i => $key):
		$recipients[$key] = $values[$i];
	endforeach;
	return $recipients;
	}
}
function getCleanRecipients($group){
	if(!empty($group)){
		global $conn;
		$values = array();
			$sql = sprintf("SELECT PhoneOne FROM `".DB_PREFIX."addressbook` WHERE `deletedFlag` = %d AND `PhoneOne` !='' AND ContactGroup ='%s'", 0, $group);
			$res = db_query($sql,DB_NAME,$conn);
				while($row = db_fetch_array($res)){
					$row['PhoneOne'] = str_replace(" ", "",$row['PhoneOne']);
					if( substr( $row['PhoneOne'], 0, 2 ) === "07" && strlen($row['PhoneOne']) == 10 ){
						$phone = '+254'.(int)$row['PhoneOne'];
						array_push($values, $phone);
					}elseif( substr( $row['PhoneOne'], 0, 4 ) === "2547" && strlen($row['PhoneOne']) == 12 ){
						$phone = '+'.$row['PhoneOne'];
						array_push($values, $phone);
					}elseif( substr( $row['PhoneOne'], 0, 5 ) === "25407" && strlen($row['PhoneOne']) == 13){
						$phone = strstr($row['PhoneOne'], '0');
						$phone = '+254'.(int)$phone;
						array_push($values, $phone);
					}elseif( substr( $row['PhoneOne'], 0, 6 ) === "+25407" && strlen($row['PhoneOne']) == 14){
						$phone = strstr($row['PhoneOne'], '0');
						$phone = '+254'.(int)$phone;
						array_push($values, $phone);
					}
					elseif( substr( $row['PhoneOne'], 0, 1 ) === "7" && strlen($row['PhoneOne']) == 9){
						$phone = $row['PhoneOne'];
						$phone = '+254'.$phone;
						array_push($values, $phone);
					}else{
						//do nothing
					}
				}
	//return comma list of phones
	$recipients =implode(",",$values);
	return $recipients;
	}
}
//EXAM FUNCTIONS START
function getExamQuestions($ExamID){
	global $conn;
	
	$resultGet = sprintf("SELECT * FROM `".DB_PREFIX."exam_questions` WHERE `ExamID` = '%s' AND `deletedFlag` = %d", $ExamID, 0);	
	//Execute the query
	$questions = db_query($resultGet,DB_NAME,$conn);	
	return $questions;
}
function getExamFacultyID($exam_unit){
	global $conn;

	$resultGet = sprintf("SELECT * FROM `".DB_PREFIX."units_tutors` WHERE `UnitID` = '%s' AND `Status` = %d", $exam_unit, 1);	
	$facultyIDs = db_query($resultGet,DB_NAME,$conn);
	$IDs = array();
	if(db_num_rows($facultyIDs)>0){
		while($r = db_fetch_array($facultyIDs)){
			array_push($IDs, $r['FacultyID']);
		}
		}	
	return $IDs;
}
function getExamInstructions($examID){
	global $conn;

	$resultGet = sprintf("SELECT `ExamInstructions` FROM `".DB_PREFIX."exams` WHERE `ExamID` = %d", $examID);	
	$Exam = db_query($resultGet,DB_NAME,$conn);
	$r = db_fetch_array($Exam);	
	return $r['ExamInstructions'];
}
function getExamDuration($examID){
	global $conn;

	$resultGet = sprintf("SELECT `ExamDuration` FROM `".DB_PREFIX."exams` WHERE `ExamID` = %d", $examID);	
	$Exam = db_query($resultGet,DB_NAME,$conn);
	$r = db_fetch_array($Exam);	
	return $r['ExamDuration'];
}
function getExamName($examID){
	global $conn;

	$resultGet = sprintf("SELECT ExamName FROM `".DB_PREFIX."exams` WHERE `ExamID` = %d", $examID);	
	$Exam = db_query($resultGet,DB_NAME,$conn);
	$r = db_fetch_array($Exam);	
	return $r['ExamName'];
}
//EXAM FUNCTIONS END

//Create user sessions and cookies
function createsessions($username,$password) {
	//Add additional member to Session array as per requirement
	$_SESSION['sysusername'] = $username;
	$_SESSION['syspassword'] = $password;
	$_SESSION['sysTimeout'] = time();
	
	if(isset($_POST['urem']) == 1){
        //Add additional member to cookie array as per requirement
        setcookie("sysusername", $_SESSION['sysusername'], time()+60*60*24*100, "/");
        setcookie("syspassword", $_SESSION['syspassword'], time()+60*60*24*100, "/");
        return;
    }
}
//Clear user sessions and cookies
function clearsessionscookies() {
	unset($_SESSION['sysusername']);
	unset($_SESSION['syspassword']);
	unset($_SESSION['sysUserID']);
	unset($_SESSION['sysUsername']);
	unset($_SESSION['sysEmail']);
	unset($_SESSION['sysFullName']);
	unset($_SESSION['sysTimeout']);
	unset($_SESSION["folder"]);
	
	//setcookie("sysusername", "",time()-60*60*24*100, "/");
    setcookie("syspassword", "",time()-60*60*24*100, "/");
}
//
function activeSession(){
	// Session killer after a given period of inactive login
	$inactive = 3600; // Set timeout period in seconds i.e.(1800 = 30min)

	if(isset($_SESSION['sysTimeout'])) {
		$session_life = time() - $_SESSION['sysTimeout'];
		if($session_life > $inactive) {
			//session_destroy();			
			markLoggedout();
		    return false;
		}
		else{
			$_SESSION['sysTimeout'] = time();// Reset time
			return true;
		}
	}	
	else{
		return false;
	}
}
//Confirm User Login
function confirmUser($username,$password){
	global $incl_dir;
  	require_once("$incl_dir/mysqli.functions.php");
  	//Open database connection
  	$conn = db_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
	// Prevent SQL Injection
	// Reverse magic_quotes_gpc/magic_quotes_sybase effects on those vars if ON.
	if(get_magic_quotes_gpc()) {
		$user = stripslashes($username);
	} else {
		$user = mysqli_real_escape_string($conn,$username);
	}
	//Make a safe query
	$query = sprintf("SELECT `ID`,`Username`,`Password`,`Email`,`FirstName`,`LastName` FROM `".DB_PREFIX."sys_users` WHERE `Username` = '%s' AND `disabledFlag` = %d AND `deletedFlag` = %d AND `loggedIn` = %d", $user, 0, 0, 0);
	//Execute the query
	$result = db_query($query,DB_NAME,$conn);
	
	//Check if any record returned
	if(db_num_rows($result)>0){
		//Fetch data
		$row = db_fetch_array($result);
		
		$_SESSION['sysUserID'] = $row['ID'];
		$_SESSION['sysUsername'] = $row['Username'];
		$_SESSION['sysEmail'] = $row['Email'];
		$_SESSION['sysFullName'] = $row['FirstName']." ".$row['LastName'];
		
		if(password_verify($password, $row['Password']))
			return true;		
		else 
			return false;
	}else{
		return false;
	}
	//Close the database connection	
	db_close($conn);
}
//Check if user is loggen in  
function checkLoggedin() {
	if(activeSession()){
		$userid = isset($_SESSION['sysUserID'])?$_SESSION['sysUserID']:NULL;
		settype($userid, 'integer');
		//Open database connection
		global $incl_dir;
		require_once("$incl_dir/mysqli.functions.php");
		$conn = db_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
			
		$query = sprintf("SELECT `loggedIn` FROM `".DB_PREFIX."sys_users` WHERE `ID` = %d", $userid);
		$result = db_query($query,DB_NAME,$conn);
		//Check if any record returned
		if(db_num_rows($result)>0){	
			//Fetch data
			$row = db_fetch_array($result);
			$LogStatus = $row['loggedIn'];
		}
		//Close the database connection	
		db_close($conn);
		//End of check
		if(isset($_SESSION['sysusername']) && isset($_SESSION['syspassword']) && $LogStatus==1)
			return true;
		elseif(isset($_COOKIE['sysusername']) && isset($_COOKIE['syspassword'])){
			if(confirmUser($_COOKIE['sysusername'],$_COOKIE['syspassword'])){
				createsessions($_COOKIE['sysusername'],$_COOKIE['syspassword']); 
				markLoggedin($_COOKIE['sysusername']); 
				return true; 
			} 
			else{ 
				clearsessionscookies(); 
				return false; 
			}
		}
		else 
			return false;
	}
	else{
		return false;
	}
}
//Returns TRUE if reset was done
function resetLoginSession($username){
	global $incl_dir;
  	require_once("$incl_dir/mysqli.functions.php");
  	//Open database connection
  	$conn = db_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
	
	if(get_magic_quotes_gpc()) {
		$user = stripslashes($username);
	} else {
		$user = mysqli_real_escape_string($conn,$username);
	}
	//Make a safe query
	$query = sprintf("SELECT `ID`,`Username`,`Password` FROM `".DB_PREFIX."sys_users` WHERE `Username` = '%s' AND `loggedIn` = %d", $user, 1);
	//Execute the query
	$result = db_query($query,DB_NAME,$conn);
	//Check if any record returned
	if(db_num_rows($result)>0){	
		//Fetch data
		$resetRow = db_fetch_array($result);
		$dbusername = $resetRow['Username'];	
		//Confirm user
		if($username == "$dbusername"){
			//set the query
			$query = sprintf("UPDATE `".DB_PREFIX."sys_users` SET `loggedIn` = %d WHERE `Username` = '%s'", 0, $username);
			db_query($query,DB_NAME,$conn);
			
			if(db_affected_rows($conn))
				return true;			
			else
				return false;			
		}
		else{
			return false;		
		}
	}
	else{
		return false;
	}
	//Close the database connection	
	db_close($conn);
}
//Mark User as logged in
function markLoggedin($username){
	$userid = isset($_SESSION['sysUserID'])?$_SESSION['sysUserID']:NULL;
	settype($userid, 'integer');
    $logindate = date("Y-m-d H:i:s",time());
	$source = getUserIP();
	
	global $incl_dir;
  	require_once("$incl_dir/mysqli.functions.php");
  	//Open database connection
  	$conn = db_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
	//run the query
	$query = sprintf("UPDATE `".DB_PREFIX."sys_users` SET `loggedIn` = %d, `loginDate` = '%s' WHERE `Username` = '%s'", 1, $logindate, $username);
	db_query($query,DB_NAME,$conn);
	//check if true and add this log
	if(db_affected_rows($conn)){
		$queryLog = sprintf("INSERT INTO `".DB_PREFIX."sys_users_logs` (`userID`, `loginDate`, `source`) VALUES (%d, '%s', '%s')", $userid, $logindate, $source);
		db_query($queryLog,DB_NAME,$conn);
	}
	//close the database connection
	db_close($conn);
}
//Mark User as loggedout
function markLoggedout(){
	$userid = isset($_SESSION['sysUserID'])?$_SESSION['sysUserID']:NULL;
	settype($userid, 'integer');
	global $incl_dir;
  	require_once("$incl_dir/mysqli.functions.php");
  	//Open database connection
  	$conn = db_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
	
	$query = sprintf("UPDATE `".DB_PREFIX."sys_users` SET `loggedIn` = %d WHERE `ID` = %d", 0, $userid);
	//run the query
	db_query($query,DB_NAME,$conn);
	//close the database connection
	db_close($conn);
}
//Check if this user has all rights
function isSuperAdmin(){
	$userid = isset($_SESSION['sysUserID'])?$_SESSION['sysUserID']:NULL;
	settype($userid, 'integer');
	global $incl_dir;
  	require_once("$incl_dir/mysqli.functions.php");
  	//Open database connection
  	$conn = db_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
	
	$query = sprintf("SELECT `UserType`,`UserLevel` FROM `".DB_PREFIX."sys_users` WHERE `ID` = %d AND `disabledFlag` = 0 AND `deletedFlag` = 0", $userid);
	$result = db_query($query,DB_NAME,$conn);
	$rowData = db_fetch_array($result);
	if($rowData['UserType']=='Admin' && $rowData['UserLevel']=='Super')
		return true;
	else
		return false;
	//close the database connection
	db_close($conn);
}
//Check if this user has admin rights
function isSystemAdmin(){
	$userid = isset($_SESSION['sysUserID'])?$_SESSION['sysUserID']:NULL;
	settype($userid, 'integer');
	global $incl_dir;
  	require_once("$incl_dir/mysqli.functions.php");
  	//Open database connection
  	$conn = db_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
	
	$query = sprintf("SELECT `UserType`,`UserLevel` FROM `".DB_PREFIX."sys_users` WHERE `ID` = %d AND `disabledFlag` = 0 AND `deletedFlag` = 0", $userid);
	$result = db_query($query,DB_NAME,$conn);
	$rowData = db_fetch_array($result);
	if($rowData['UserType']=='Admin' && $rowData['UserLevel']=='System')
		return true;
	else
		return false;
	//close the database connection
	db_close($conn);
}
//Check if this user has dean rights
function isDeanAdmin(){
	$userid = isset($_SESSION['sysUserID'])?$_SESSION['sysUserID']:NULL;
	settype($userid, 'integer');
	global $incl_dir;
  	require_once("$incl_dir/mysqli.functions.php");
  	//Open database connection
  	$conn = db_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
	
	$query = sprintf("SELECT `UserType`,`UserLevel` FROM `".DB_PREFIX."sys_users` WHERE `ID` = %d AND `disabledFlag` = 0 AND `deletedFlag` = 0", $userid);
	$result = db_query($query,DB_NAME,$conn);
	$rowData = db_fetch_array($result);
	if($rowData['UserType']=='Admin' && $rowData['UserLevel']=='Dean')
		return true;
	else
		return false;
	//close the database connection
	db_close($conn);
}
//Check if this user has registrar rights
function isRegistrarAdmin(){
	$userid = isset($_SESSION['sysUserID'])?$_SESSION['sysUserID']:NULL;
	settype($userid, 'integer');
	global $incl_dir;
  	require_once("$incl_dir/mysqli.functions.php");
  	//Open database connection
  	$conn = db_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
	
	$query = sprintf("SELECT `UserType`,`UserLevel` FROM `".DB_PREFIX."sys_users` WHERE `ID` = %d AND `disabledFlag` = 0 AND `deletedFlag` = 0", $userid);
	$result = db_query($query,DB_NAME,$conn);
	$rowData = db_fetch_array($result);
	if($rowData['UserType']=='Admin' && $rowData['UserLevel']=='Registrar')
		return true;
	else
		return false;
	//close the database connection
	db_close($conn);
}
//Check if this user has finance rights
function isFinanceAdmin(){
	$userid = isset($_SESSION['sysUserID'])?$_SESSION['sysUserID']:NULL;
	settype($userid, 'integer');
	global $incl_dir;
  	require_once("$incl_dir/mysqli.functions.php");
  	//Open database connection
  	$conn = db_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
	
	$query = sprintf("SELECT `UserType`,`UserLevel` FROM `".DB_PREFIX."sys_users` WHERE `ID` = %d AND `disabledFlag` = 0 AND `deletedFlag` = 0", $userid);
	$result = db_query($query,DB_NAME,$conn);
	$rowData = db_fetch_array($result);
	if($rowData['UserType']=='Admin' && $rowData['UserLevel']=='Finance')
		return true;
	else
		return false;
	//close the database connection
	db_close($conn);
}
//
function checkAllowedSysUsers(){
	global $conn;
	
	settype($total, 'integer');
	
	//If limited users and
	//a limit has been set, restrict
	if(defined(LIMIT_USERS) && defined(LIMIT_ALLOWED)){
		$sqlCount = "SELECT COUNT(`ID`) AS `Total` FROM  `".DB_PREFIX."sys_users` WHERE `UserLevel` != 'Super' AND `deletedFlag` = 0";
		$result = db_query($sqlCount,DB_NAME,$conn);
		$rowData = db_fetch_array($result);
		
		$total = $rowData['Total'];
		
		if($total < LIMIT_ALLOWED)
			return true;
		else
			return false;
	}else{
		return true;
	}
}
//Get username given the system user ID
function getSysUsername($userID){
	global $conn;
	
	$sqlUsers = sprintf("SELECT `Username` FROM `".DB_PREFIX."sys_users` WHERE `ID` = '%d' AND `deletedFlag` = 0", $userID);
	//Execute the query
	$resultUser = db_query($sqlUsers,DB_NAME,$conn);
	
	if(db_num_rows($resultUser)>0){
		$rowUser = db_fetch_array($resultUser);
		return $rowUser['Username'];
	}
	else{
		return false;
	}
}
//Get user email given the system username
function getSysUserEmail($username){
	global $conn;
	
	$sqlUsers = sprintf("SELECT `Email` FROM `".DB_PREFIX."sys_users` WHERE `Username` = '%s' AND `deletedFlag` = 0", $username);
	//Execute the query
	$resultUser = db_query($sqlUsers,DB_NAME,$conn);
	
	if(db_num_rows($resultUser)>0){
		$rowUser = db_fetch_array($resultUser);
		return $rowUser['Email'];
	}
	else{
		return false;
	}
}
//Get username given the token ID
function getSysTokenUser($tokenID){
	global $conn;
	
	$sqlUsers = sprintf("SELECT `Username`,`token` FROM `".DB_PREFIX."sys_users` WHERE `token` = '%s' AND `deletedFlag` = 0", $tokenID);
	//Execute the query
	$resultUser = db_query($sqlUsers,DB_NAME,$conn);
	
	if(db_num_rows($resultUser)>0){
		$rowUser = db_fetch_array($resultUser);
		return $rowUser['Username'];
	}
	else{
		return false;
	}
}
function UpdateApproved($StudentID){
	global $conn;
	$query = sprintf("UPDATE `".DB_PREFIX."students` SET `approved` = %d WHERE `StudentID` = '%s'", 1, $StudentID);
		db_query($query,DB_NAME,$conn);
		if(db_affected_rows($conn))
			return true;			
		else
			return false;			
}
function UpdateRejected($StudentID){
	global $conn;
	$query = sprintf("UPDATE `".DB_PREFIX."students` SET `Courses` = '' WHERE `StudentID` = '%s'", $StudentID);
		db_query($query,DB_NAME,$conn);
		if(db_affected_rows($conn))
			return true;			
		else
			return false;			
}
//Get department name given the department ID
function getDepartmentName($DeptID){
	global $conn;
	
	$sqlGet = sprintf("SELECT `DeptID`,`DName` FROM `".DB_PREFIX."departments` WHERE `DeptID` = '%s' AND `deletedFlag` = 0", $DeptID);
	//Execute the query
	$resultGet = db_query($sqlGet,DB_NAME,$conn);
	
	if(db_num_rows($resultGet)>0){
		$rowGet = db_fetch_array($resultGet);
		return $rowGet['DName'];
	}
	else{
		return "N/A";
	}
}
// Get course name given the course ID
function getCourseName($CourseID){
	global $conn;
	
	$sqlGet = sprintf("SELECT `CourseID`,`CName` FROM `".DB_PREFIX."courses` WHERE `CourseID` = '%s' AND `deletedFlag` = 0", $CourseID);
	//Execute the query
	$resultGet = db_query($sqlGet,DB_NAME,$conn);
	
	if(db_num_rows($resultGet)>0){
		$rowGet = db_fetch_array($resultGet);
		return $rowGet['CName'];
	}
	else{
		return "N/A";
	}
}
// Get Faculty name given the Faculty ID
function getFacultyName($FacultyID){
	global $conn;
	
	$sqlGet = sprintf("SELECT CONCAT(`Title`,' ',`FName`,' ',`LName`) AS `FacultyName` FROM `".DB_PREFIX."faculties` WHERE `FacultyID` = '%s' AND `deletedFlag` = 0", $FacultyID);
	//Execute the query
	$resultGet = db_query($sqlGet,DB_NAME,$conn);
	
	if(db_num_rows($resultGet)>0){
		$rowGet = db_fetch_array($resultGet);
		return $rowGet['FacultyName'];
	}
	else{
		return "N/A";
	}
}
// Get Faculty APPLICATION INFO; CV, CL, UNITS
function getFacultyApplication($FacultyID){
	global $conn;
	
	$sqlGet = sprintf("SELECT * FROM `".DB_PREFIX."faculty_applications` WHERE `FacultyID` = '%s' AND `deletedFlag` = 0 LIMIT 1", $FacultyID);
	//Execute the query
	$resultGet = db_query($sqlGet,DB_NAME,$conn);
	
	if(db_num_rows($resultGet)>0){
		$rowGet = db_fetch_array($resultGet);
		return $rowGet;
	}
	else{
		return "N/A";
	}
}
// Get Faculty's student ID
function getFacultyStudentID($FacultyEmail){
	global $conn;
	
	$sqlGet = sprintf("SELECT StudentID FROM `".DB_PREFIX."students` WHERE `Email` = '%s' AND `deletedFlag` = 0 LIMIT 1", $FacultyEmail);
	//Execute the query
	$resultGet = db_query($sqlGet,DB_NAME,$conn);
	
	if(db_num_rows($resultGet)>0){
		$rowGet = db_fetch_array($resultGet);
		return $rowGet['StudentID'];
	}
	else{
		return "N/A";
	}
}
//Get department name for given course ID
function getCourseDepartmentName($CourseID){
	global $conn;
	
	$sqlGet = sprintf("SELECT D.`DeptID`,D.`DName` FROM `".DB_PREFIX."courses` AS C LEFT JOIN `".DB_PREFIX."departments` AS D ON D.`DeptID` = C.`DeptID` WHERE C.`CourseID` = '%s' AND D.`deletedFlag` = 0", $CourseID);
	//Execute the query
	$resultGet = db_query($sqlGet,DB_NAME,$conn);
	
	if(db_num_rows($resultGet)>0){
		$rowGet = db_fetch_array($resultGet);
		return $rowGet['DName'];
	}
	else{
		return "N/A";
	}
}
// Get student study mode given student ID
function getStudentStudyMode($StudentID){
	global $conn;
	
	$sqlGet = sprintf("SELECT PC.`payment_name` FROM `".DB_PREFIX."students` AS S LEFT JOIN `".DB_PREFIX."payment_categs` AS PC ON S.`StudyMode` = PC.`pay_id` WHERE PC.`type` = 'StudyMode' AND S.`StudentID` = '%s' AND S.`deletedFlag` = 0", $StudentID);
	//Execute the query
	$resultGet = db_query($sqlGet,DB_NAME,$conn);
	
	if(db_num_rows($resultGet)>0){
		$rowGet = db_fetch_array($resultGet);
		return $rowGet['payment_name'];
	}
	else{
		return "Online (Free Training)";
	}
}
function getCourseData($CourseID){
	global $conn;
	
	$sqlGet = sprintf("SELECT * FROM `".DB_PREFIX."courses`  WHERE `CourseID` = '%s' AND `deletedFlag` = 0", $CourseID);
	//Execute the query
	$resultGet = db_query($sqlGet,DB_NAME,$conn);
	
	if(db_num_rows($resultGet)>0){
		$rowGet = db_fetch_array($resultGet);
		return $rowGet;
	}
	else{
		return null;
	}
}
//
function getCourseTuitionFees($CourseID){
	global $conn;
	
	//Set the query
	$sqlGet = sprintf("SELECT `TuitionFee` FROM `".DB_PREFIX."units` WHERE `CourseID` = '%s' AND `disabledFlag` = 0 AND `deletedFlag` = 0", $CourseID);
	//Execute the query
	$resultGet = db_query($sqlGet,DB_NAME,$conn);
	
	floatval($total);
	$total = 0;
	if(db_num_rows($resultGet)>0){
		while($rowGet = db_fetch_array($resultGet)){
			$total += floatval($rowGet['TuitionFee']);
		}	
	}
	
	return $total;
}
//
function getCourseFeesStructure($CourseID, $StudyMode){
	global $conn;
	
	if(!empty($CourseID)){
		$sqlGet = sprintf("
		SELECT `payment_name`,`pay_amount` FROM `".DB_PREFIX."payment_categs` WHERE `assoc_course` = 'All' AND `type` = 'Fee' OR `assoc_course` LIKE '%s' 
		UNION
		 SELECT `payment_name`,`pay_amount` FROM `".DB_PREFIX."payment_categs` WHERE `pay_id` = %d AND `type` = 'StudyMode'", "%".$CourseID."%", $StudyMode);
		//mExecute the query
		$resultGet = db_query($sqlGet,DB_NAME,$conn);
		
		floatval($total);
		floatval($totalCourseMainFees);
		floatval($totalCourseFees);
		$total = 0;
		$returnHTML = '<table style="border:1px solid #222;border-collapse:collapse;width:100%;max-width:100%;margin-bottom:20px;">';
		if(db_num_rows($resultGet)>0){
			while($rowGet = db_fetch_array($resultGet)){
				$returnHTML .= 
				"<tr><td style=\"border:1px solid #222;padding:4px;line-height:1;vertical-align:top;\"><strong>".strtoupper($rowGet['payment_name'])."</strong></td><td style=\"border:1px solid #222;text-align:right;padding:4px;line-height:1;vertical-align:top;\">".number_format($rowGet['pay_amount'], 2)."</td></tr>";
				$total += floatval($rowGet['pay_amount']);
			}
			$totalCourseMainFees = $total;
			$totalTuitionFees = getCourseTuitionFees($CourseID);
			$totalCourseFees = floatval($totalCourseMainFees + $totalTuitionFees);
		}else{
			$totalTuitionFees = 0;
			$totalCourseFees = 0;
		}
		
		$returnHTML .= "<tr><td style=\"border:1px solid #222;padding:4px;line-height:1;vertical-align:top;\"><strong>TUITION FEES</strong></td><td style=\"border:1px solid #222;text-align:right;padding:4px;line-height:1;vertical-align:top;\">".number_format($totalTuitionFees, 2)."</td></tr>";
		$returnHTML .= "<tr><td style=\"border:1px solid #222;padding:4px;line-height:1;vertical-align:top;\"><strong>TOTAL</strong></td><td style=\"border:1px solid #222;text-align:right;padding:4px;line-height:1;vertical-align:top;\"><strong>".number_format($totalCourseFees, 2)."</strong></td></tr>";
		$returnHTML .= "</table>";
		return $returnHTML;
	}
	
}
// Get number of departments available in the database
function getAllDepartments(){
	global $conn;
	
	$sql = "SELECT COUNT(*) FROM `".DB_PREFIX."departments`";
	$res = db_query($sql,DB_NAME,$conn);
	$row = db_fetch_array($res);
	reset($row);
	return current($row);
}
//get enrollments per unit
function getEnrolments($UnitID){
	global $conn;
	
	$sql = "SELECT COUNT(Distinct StudentID) FROM `".DB_PREFIX."units_registered` WHERE UnitID = '$UnitID'";
	$res = db_query($sql,DB_NAME,$conn);
	$row = db_fetch_array($res);
	reset($row);
	return current($row);
}
// Get number of courses available in the database
function getAllCourses(){
	global $conn;
	
	$sql = "SELECT COUNT(*) FROM `".DB_PREFIX."courses`";
	$res = db_query($sql,DB_NAME,$conn);
	$row = db_fetch_array($res);
	reset($row);
	return current($row);
}
// Get number of units available in the database
function getAllUnits(){
	global $conn;
	
	$sql = "SELECT COUNT(*) FROM `".DB_PREFIX."units`";
	$res = db_query($sql,DB_NAME,$conn);
	$row = db_fetch_array($res);
	reset($row);
	return current($row);
}
// Get number of students available in the database
function getAllStudents(){
	global $conn;
	
	$sql = "SELECT COUNT(*) FROM `".DB_PREFIX."students`";
	$res = db_query($sql,DB_NAME,$conn);
	$row = db_fetch_array($res);
	reset($row);
	return current($row);
}
// Get number of faculties available in the database
function getAllFaculties(){
	global $conn;
	
	$sql = "SELECT COUNT(*) FROM `".DB_PREFIX."faculties`";
	$res = db_query($sql,DB_NAME,$conn);
	$row = db_fetch_array($res);
	reset($row);
	return current($row);
}
// Get number of messages for the logged in user
function getUserMessages($UserEmail){
	global $conn;
	
	if( !empty($UserEmail) && isset($UserEmail) ){		
		$sql = "SELECT COUNT(*) FROM `".DB_PREFIX."messages` WHERE (`ToAdd` = '$UserEmail' OR `CcAdd` = '$UserEmail' OR `BccAdd` = '$UserEmail')";		
		$res = db_query($sql,DB_NAME,$conn);
		$row = db_fetch_array($res);
		reset($row);
		return current($row);
	}else{
		return 0;
	}
}
// Get number of units in a given course ID
function getCourseUnits($CourseID){
	global $conn;
	
	$sqlGet = sprintf("SELECT COUNT(*) FROM `".DB_PREFIX."units` WHERE `CourseID` = '%s'", $CourseID);		
	$res = db_query($sqlGet,DB_NAME,$conn);
	$row = db_fetch_array($res);
	reset($row);
	return current($row);
}
//get list of units for a course
function getCourseUnitList($CourseID){
	global $conn;
	
	$sqlGet = sprintf("SELECT `UName` FROM `".DB_PREFIX."units` WHERE `CourseID` = '%s'", $CourseID);
	//Execute the query
	$resultGet = db_query($sqlGet,DB_NAME,$conn);	
	$units = array();
	$li_element = '';
	while($rowGet = db_fetch_array($resultGet)){
		$units[] =$rowGet['UName'];
	}
	foreach($units as $u):
		$li_element.='<li><b>'.$u.'</b></li>';
	endforeach;
	return $li_element;
}
function CreateCourseUnitList(array $unitIDs){
	$li_element = '';
	foreach($unitIDs as $u):
		$li_element.='<li><b>'.$u.':- '.getUnitName($u).'</b></li>';
	endforeach;
	return $li_element;
}
// Get number of courses in a given department ID
function getDepartmentCourses($DeptID){
	global $conn;
	
	$sqlGet = sprintf("SELECT COUNT(*) FROM `".DB_PREFIX."courses` WHERE `DeptID` = '%s'", $DeptID);		
	$res = db_query($sqlGet,DB_NAME,$conn);
	$row = db_fetch_array($res);
	reset($row);
	return current($row);
}
// Get CourseID of given Unit ID
function getUnitCourseID($UnitID){
	global $conn;
	
	$sqlGet = sprintf("SELECT `CourseID` FROM `".DB_PREFIX."units` WHERE `UnitID` = '%s'", $UnitID);
	//Execute the query
	$resultGet = db_query($sqlGet,DB_NAME,$conn);	
	
	if(db_num_rows($resultGet)>0){
		$rowGet = db_fetch_array($resultGet);
		return $rowGet['CourseID'];
	}
	else{
		return NULL;
	}
}
//get unit name given ID
function getUnitName($UnitID){
	global $conn;
	
	$sqlGet = sprintf("SELECT `UName` FROM `".DB_PREFIX."units` WHERE `UnitID` = '%s'", $UnitID);
	//Execute the query
	$resultGet = db_query($sqlGet,DB_NAME,$conn);	
	
	if(db_num_rows($resultGet)>0){
		$rowGet = db_fetch_array($resultGet);
		return $rowGet['UName'];
	}
	else{
		return NULL;
	}
}
function getStudentData($StudentID){
	global $conn;
	
	$sqlGet = sprintf("SELECT * FROM `".DB_PREFIX."students` WHERE `StudentID` = '%s'", $StudentID);
	//Execute the query
	$resultGet = db_query($sqlGet,DB_NAME,$conn);	
	$data = array();
	if(db_num_rows($resultGet)>0){
		while( $rowGet = db_fetch_array($resultGet) ){
		$data = $rowGet;
		}
		return $data;
	}
	else{
		return NULL;
	}
}
// function sql_select_enrollments($UnitID){
// 	global $conn;

// 	$sql = "SELECT * FROM `".DB_PREFIX."units_registered` WHERE `UnitID` = '$UnitID'";	
// 	$resultGet = db_query($sql,DB_NAME,$conn);	
// 	$data = array();
// 	if(db_num_rows($resultGet)>0){
// 		while( $rowGet = db_fetch_array($resultGet) ){
// 		$data = $rowGet;
// 		}
// 		return $data;
// 	}
// 	else{
// 		return NULL;
// 	}
// }
//Get registered units per course given student ID
function registeredUnits($StudentID, $CourseID){
	global $conn;
	
	//Get registered units
	$sqlRegUnits = sprintf("SELECT * FROM `".DB_PREFIX."units_registered` AS `UR` LEFT JOIN `".DB_PREFIX."units` AS `U` ON `UR`.`UnitID` = `U`.`UnitID` WHERE `UR`.`StudentID` = '%s' AND `UR`.`CourseID` = '%s'", $StudentID, $CourseID);	
	//Execute the query
	$resUnits = db_query($sqlRegUnits,DB_NAME,$conn);	
	return $resUnits;
}
//Get assigned units for given Faculty ID
function getFacultyUnits($FacultyID){
	global $conn;
	
	//Set the query
	$sqlGet = sprintf("SELECT `UT`.`UnitID`,`U`.`UName`,`U`.`Description`,`U`.`CourseID`,`U`.`YrTrim` FROM `".DB_PREFIX."units_tutors` AS `UT` LEFT JOIN `".DB_PREFIX."units` AS `U` ON `UT`.`UnitID` = `U`.`UnitID` WHERE `UT`.`Status` = %d AND `UT`.`FacultyID` = '%s' AND `disabledFlag` = 0 AND `deletedFlag` = 0", 1, $FacultyID);
	//Execute the query
	return db_query($sqlGet,DB_NAME,$conn);
	
}
//check if is already assigned
function checkTutorUnitAssignment($FacultyID, $UnitID){
	global $conn;
	$sqlGet = sprintf("SELECT * FROM `".DB_PREFIX."units_tutors` WHERE `UnitID` = '%s' AND `FacultyID` = '%s' AND `Status` = %d", $UnitID, $FacultyID, 1);
	//Execute the query
	$resultGet = db_query($sqlGet,DB_NAME,$conn);	
	if(db_num_rows($resultGet)>0){
		$rowGet = db_fetch_array($resultGet);
		return true;
	}
	else{
		return false;
	}
}
function UpdateAppliedUnits($FacultyID, array $RemovedUnits){
	global $conn;
	$AppliedUnits = array();
	$sqlGet = sprintf("SELECT `UnitsApplied` FROM `".DB_PREFIX."faculty_applications` WHERE `FacultyID` = '%s' ", $FacultyID);
	//Execute the query
	$resultGet = db_query($sqlGet,DB_NAME,$conn);	
	if(db_num_rows($resultGet)>0){
		$rowGet = db_fetch_array($resultGet);
		//convert comma list to array
		$AppliedUnits = explode(',', $rowGet['UnitsApplied']);
		// subract assigned units from applied units
		$Remaining = array_diff($AppliedUnits, $RemovedUnits);
		if(!empty($Remaining)){
			//convert the Remaining back to comma string
			$Remaining = implode(",",$Remaining);
		}else{
			$Remaining = "";	
		}
		//update faculty applications's applied units
		$Sql = sprintf("UPDATE `".DB_PREFIX."faculty_applications` SET `UnitsApplied` = '%s' WHERE `FacultyID` = '%s'", $Remaining,$FacultyID);
		db_query($Sql,DB_NAME,$conn);

		return true;
		
	}else{
		return false;
	}
}
function getFeesPayable($StudentID,$CourseID, $StudyMode){
	global $conn;	
	$sqlGet = sprintf("SELECT SUM(`TuitionFee`) as Fee FROM `".DB_PREFIX."units` AS U LEFT JOIN `".DB_PREFIX."units_registered` AS UR ON U.`UnitID` = UR.`UnitID` WHERE UR.`CourseID` = '%s' AND UR.`StudentID` = '%s' AND U.`disabledFlag` = 0 AND U.`deletedFlag` = 0", $CourseID, $StudentID);
	$resultGet = db_query($sqlGet,DB_NAME,$conn);
	floatval($total);
	$total = 0;
	if(db_num_rows($resultGet)>0){
		$rowGet = db_fetch_array($resultGet);
		$total = floatval($rowGet['Fee']);
			
	}
	//get mode fee
	$sqlGet = sprintf("SELECT `pay_amount` FROM `".DB_PREFIX."payment_categs` WHERE `pay_id` = %d", $StudyMode);
	$resultGet = db_query($sqlGet,DB_NAME,$conn);
	if(db_num_rows($resultGet)>0){
		$rowGet = db_fetch_array($resultGet);
		$total += floatval($rowGet['pay_amount']);
			
	}

	return $total;
}
// Get number of messages for the logged in user
function list_message_snapshots($UserEmail){
	global $conn;	
	$msgHTML = "";
	
	if( !empty($UserEmail) && isset($UserEmail) ){		
		$sqlGet = "SELECT `FromAdd`,`DateSent`,`Subject`,`Message` FROM `".DB_PREFIX."messages` WHERE (`ToAdd` = '$UserEmail' OR `CcAdd` = '$UserEmail' OR `BccAdd` = '$UserEmail') ORDER BY `DateSent` DESC LIMIT 5";
		$res = db_query($sqlGet,DB_NAME,$conn);
		if(db_num_rows($res)>0){
			while($message = db_fetch_array($res)){
				$msgHTML .= '
				<li>
					<a href="?dispatcher=messages">
						<div>
							<strong>'. $message['FromAdd'] .'</strong>
							<span class="pull-right text-muted">
								<em>'. formatDateAgo($message['DateSent']) .'</em>
							</span>
						</div>
						<div>'. truncate(decode($message['Message']), 80) .'</div>
					</a>
				</li>
				<li class="divider"></li>';
			}
		}
	}
	return $msgHTML;
}
//List message/note attachments
function list_attachments($MsgID){
	global $conn;

	$sqlAttachmentShow = sprintf("SELECT `FileName`,`DownloadPath` FROM `".DB_PREFIX."attachments` WHERE `MessageID` = %d", $MsgID);
	//Run the query
	$result = db_query($sqlAttachmentShow,DB_NAME,$conn);
	
	$attachmentList = "";
	if(db_num_rows($result)>0){
		$attachmentList .= "Attachments: ";
		while($attachment = db_fetch_array($result)){
			$attachmentList .= "<a href=\"".$attachment['DownloadPath']."\" href=\"_blank\">".$attachment['FileName']."</a>&nbsp;";
		}
	}
	return $attachmentList;
}
//Array of user levels
function list_user_levels(){
	return array(
	"Finance" => "Finance",
	"Registrar" => "Registrar",
	"Dean" => "Dean of Students",
	"System" => "System Admin",
	"Super" => "Super Administrator");
}
//Array of user types
function list_user_types(){
	return array(
	"Admin" => "Back-end User");
}
?>