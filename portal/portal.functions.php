<?php
//require_once("$class_dir/new_phpmailer/src/PHPMailer.php"); 
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
//exam functions
function recordScore($params){
	$Sid = $params[0];
	$Eid = $params[1];
	$score = $params[2];
	$s = $params[3];
	$u = $params[4];
	global $conn;
	db_query(sprintf("UPDATE `".DB_PREFIX."student_exams` SET `Status`= '%s', `ExamScore`= '%s', `unmarked`= '%s' WHERE `StudentID` = '%s' AND `ExamID` = %d", $s, $score, $u, $Sid, $Eid),DB_NAME,$conn);
}
function changeExamState($ExamID, $StudentID, $State){
	global $conn;
	$sqlGet = sprintf("UPDATE `".DB_PREFIX."student_exams` SET `Status`= '%s' WHERE `StudentID` = '%s' AND `ExamID` = %d", $State, $StudentID, $ExamID);
	db_query($sqlGet,DB_NAME,$conn);
	return true;
}
function getStudentExams($StudentID){
	global $conn;
	$sqlGet = sprintf("SELECT * FROM `".DB_PREFIX."student_exams` WHERE `disabledFlag` = %d AND `deletedFlag` = %d AND `StudentID` = '%s' ", 0,0,$StudentID);
	//Execute the query
	$resultGet = db_query($sqlGet,DB_NAME,$conn);
	$Exams = [];
	if(db_num_rows($resultGet)>0){
		while($rowGet = db_fetch_array($resultGet)){
		array_push($Exams, $rowGet);
		}	
	}
	return $Exams;
}
function getStudentExamsDetails($ExamID){
	global $conn;
	$sqlGet = sprintf("SELECT * FROM `".DB_PREFIX."exams` WHERE `ExamID` = %d AND `deletedFlag` = %d", $ExamID,0);
	//Execute the query
	$resultGet = db_query($sqlGet,DB_NAME,$conn);
	$Exams = [];
	if(db_num_rows($resultGet)>0){
		while($rowGet = db_fetch_array($resultGet)){
		array_push($Exams, $rowGet);
		}	
	}
	return $Exams;
}
function dateFixedFromat($string){
	date_default_timezone_set("Africa/Nairobi");
	return $date = date("Y-m-d", strtotime($string));
}
function getExamQuestions($ExamID){
	global $conn;
	
	// $resultGet = sprintf("SELECT * FROM `".DB_PREFIX."exam_questions` WHERE `QuestionType` = 'Closed' AND `ExamID` = '%s' AND `deletedFlag` = %d", $ExamID, 0);	
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
//Create user sessions and cookies
function createsessions($username,$password) {
	//Add additional member to Session array as per requirement
	$_SESSION['usrusername'] = $username;
	$_SESSION['usrpassword'] = $password;
	$_SESSION['usrTimeout'] = time();
	
	if(isset($_POST['usrrem']) == 1){
        //Add additional member to cookie array as per requirement
        setcookie("usrusername", $_SESSION['usrusername'], time()+60*60*24*100, "/");
        setcookie("usrpassword", $_SESSION['usrpassword'], time()+60*60*24*100, "/");
        return;
    }
}
//Clear user sessions and cookies
function clearsessionscookies() {
	unset($_SESSION['usrtype']);
	unset($_SESSION['usrusername']);
	unset($_SESSION['usrpassword']);
	unset($_SESSION['usrTimeout']);
	unset($_SESSION['CourseID']);
	unset($_SESSION['UnitID']);
	
	//setcookie("gdusername", "",time()-60*60*24*100, "/"); 
    setcookie("usrpassword", "",time()-60*60*24*100, "/");
}
//
function activeSession(){
	// Session killer after a given period of inactive login
	$inactive = 7200; // Set timeout period in seconds i.e.(1800secs = 30min)

	if(isset($_SESSION['usrTimeout'])) {
		$session_life = time() - $_SESSION['usrTimeout'];
		if($session_life > $inactive) {
			//session_destroy();			
			markLoggedout();
			clearsessionscookies();
		    return false;
		}
		else{
			$_SESSION['usrTimeout'] = time();// Reset time
			return true;
		}
	}	
	else{
		return false;
	}
}
function getInnerMenu($base_url){
$menu = '
<nav class="navbar navbar-default" style="min-height:50px; background: transparent; border-color: transparent;">
	<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#innerMenu">
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span> 
	</button>
	<div class="collapse navbar-collapse" id="innerMenu">
	  <ul class="nav navbar-nav navbar-right">
			<li><a href="'.$base_url.'/">Home</a></li>
			<li><a href="'.$base_url.'/about.php">About</a></li>
			<li><a href="'.$base_url.'/schools">Schools</a></li>
			<li><a href="'.$base_url.'/short_courses.php">Short Courses</a></li>
			<li><a href="'.$base_url.'/kasneb">KASNEB</a></li>
			<li><a href="'.$base_url.'/cmis/portal/?do=apply">Jobs</a></li>
			<li><a href="'.$base_url.'/contact.php">Contact Us</a></li>
			<li><a href="'.$base_url.'/cmis/portal/?do=register">Register</a></li>
			<li><a href="'.$base_url.'/cmis/portal">Login</a></li>
	  </ul>
  </div>
</nav>';
return $menu;
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
	$query = sprintf("SELECT `UserType`,`LoginID`,`Password` FROM `".DB_PREFIX."portal` WHERE `LoginID` = '%s' AND `disabledFlag` = %d AND `deletedFlag` = %d AND `ApprovedFlag` = %d AND `LoggedIn` = %d", $user, 0, 0, 1, 0);
	//Execute the query
	$result = db_query($query,DB_NAME,$conn);
	
	//Check if any record returned
	if(db_num_rows($result)>0){	
		//Fetch data
		$row = db_fetch_array($result);
		
		$_SESSION['usrtype'] = $row['UserType'];
		$_SESSION['usrusername'] = $row['LoginID'];
		
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
	global $incl_dir;
  	require_once("$incl_dir/mysqli.functions.php");
  	//Open database connection
  	$conn = db_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
	
	if(activeSession()){
		$LogStatus = 0;
		$username = isset($_SESSION['usrusername'])?$_SESSION['usrusername']:"";
		
		//Set the query	
		$query = sprintf("SELECT `LoggedIn` FROM `".DB_PREFIX."portal` WHERE `LoginID` = '%s'", $username);
		$result = db_query($query,DB_NAME,$conn);
		//Check if any record returned
		if(db_num_rows($result)>0){	
			//Fetch data
			$row = db_fetch_array($result);
			$LogStatus = $row['LoggedIn'];
		}
		//End of check
		if(isset($_SESSION['usrusername']) && isset($_SESSION['usrpassword']) && $LogStatus==1)
			return true;
		elseif(isset($_COOKIE['usrusername']) && isset($_COOKIE['usrpassword'])){
			if(confirmUser($_COOKIE['usrusername'],$_COOKIE['usrpassword'])){
				createsessions($_COOKIE['usrusername'],$_COOKIE['usrpassword']); 
				markLoggedin($_COOKIE['usrusername']); 
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
	//Close the database connection	
	db_close($conn);
}
//Return TRUE if user not approved
function confirmUserApproved($username){
	global $incl_dir;
  	require_once("$incl_dir/mysqli.functions.php");
  	//Open database connection
  	$conn = db_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
	
	if(get_magic_quotes_gpc()) {
		$user = stripslashes($username);
	} else {
		$user = mysqli_real_escape_string($conn,$username);
	}
	//Set the query
	$query = sprintf("SELECT `ApprovedFlag` FROM `".DB_PREFIX."portal` WHERE `LoginID` = '%s' AND `ApprovedFlag` = %d AND `disabledFlag` = %d AND `deletedFlag` = %d", $user, 0, 0, 0);
	$result = db_query($query,DB_NAME,$conn);
	if(db_num_rows($result)>0)
		return true;
	else
		return false;
	//Close the database connection	
	db_close($conn);
}
//Return TRUE if user is disabled
function confirmUserEnabled($username){
	global $incl_dir;
  	require_once("$incl_dir/mysqli.functions.php");
  	//Open database connection
  	$conn = db_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
	
	if(get_magic_quotes_gpc()) {
		$user = stripslashes($username);
	} else {
		$user = mysqli_real_escape_string($conn,$username);
	}
	//Set the query
	$query = sprintf("SELECT `disabledFlag` FROM `".DB_PREFIX."portal` WHERE `LoginID` = '%s' AND `disabledFlag` = %d AND `deletedFlag` = %d", $user, 1, 0);
	$result = db_query($query,DB_NAME,$conn);
	if(db_num_rows($result)>0)
		return true;
	else
		return false;
	//Close the database connection	
	db_close($conn);
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
	$query = sprintf("SELECT `UserType`,`LoginID`,`Password` FROM `".DB_PREFIX."portal` WHERE `LoginID` = '%s' AND `LoggedIn` = %d", $user, 1);
	//Execute the query
	$result = db_query($query,DB_NAME,$conn);
	//Check if any record returned
	if(db_num_rows($result)>0){	
		//Fetch data
		$resetRow = db_fetch_array($result);
		$dbusername = $resetRow['LoginID'];	
		//Confirm user
		if($username == "$dbusername"){
			//set the query
			$query = sprintf("UPDATE `".DB_PREFIX."portal` SET `LoggedIn` = %d WHERE `LoginID` = '%s'", 0, $username);
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
	global $incl_dir;
  	require_once("$incl_dir/mysqli.functions.php");
  	//Open database connection
  	$conn = db_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
	
	$logindate = date("Y-m-d H:i:s",time());
	$source = getUserIP();
	
	//Set the query
	$query = sprintf("UPDATE `".DB_PREFIX."portal` SET `LoggedIn` = %d, `LoginDate` = '%s' WHERE `LoginID` = '%s'", 1, $logindate, $username);
	db_query($query,DB_NAME,$conn);
	//check if true and add this log
	if(db_affected_rows($conn)){
		$queryLog = sprintf("INSERT INTO `".DB_PREFIX."portal_logs` (`LoginID`, `LoginDate`, `Source`) VALUES ('%s', '%s', '%s')", $username, $logindate, $source);
		db_query($queryLog,DB_NAME,$conn);
	}
	//Close the database connection	
	db_close($conn);
}
//Mark User as loggedout
function markLoggedout(){	
	global $incl_dir;
  	require_once("$incl_dir/mysqli.functions.php");
  	//Open database connection
  	$conn = db_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
	
	$username = isset($_SESSION['usrusername'])?$_SESSION['usrusername']:"";
	
	if(!empty($username)){
		//Set the query
		$query = sprintf("UPDATE `".DB_PREFIX."portal` SET `LoggedIn` = %d WHERE `LoginID` = '%s'", 0, $username);
		//run the query
		db_query($query,DB_NAME,$conn);
	}
	//Close the database connection	
	db_close($conn);
}
//Verify if old password is correct before password reset
function verifyOldPassword($LoginID,$OldPassword){	
	global $conn;
	
	$sqlUser = sprintf("SELECT `LoginID`,`Password` FROM `".DB_PREFIX."portal` WHERE `LoginID` = '%s'", $LoginID);
	//Set the result and run the query
	$result = db_query($sqlUser,DB_NAME,$conn);
	//Check if any record returned
	if(db_num_rows($result)>0){	
		//Fetch data
		$row = db_fetch_array($result);
		
		if(password_verify($OldPassword, $row['Password']))
			return true;		
		else 
			return false;
	}else{
		return false;
	}
}
//Get LoginID given the Token ID
function getClientToken($TokenID){
	global $conn;
	
	//Set the query
	$sqlUsers = sprintf("SELECT `LoginID`,`Token` FROM `".DB_PREFIX."portal` WHERE `Token` = '%s' AND `disabledFlag` = 0 AND `deletedFlag` = 0", $TokenID);
	//Execute the query
	$resultUser = db_query($sqlUsers,DB_NAME,$conn);
	
	if(db_num_rows($resultUser)>0){
		$rowUser = db_fetch_array($resultUser);
		return $rowUser['LoginID'];
	}
	else{
		return false;
	}
}
function getPortalPasswordResetDetails($thisEmail,$thisUserType){
	global $conn;
	
	$sqlGetUser = '';
	switch($thisUserType){
		case "Student":
			$sqlGetUser = sprintf("SELECT `StudentID` AS `LoginID` FROM `".DB_PREFIX."students` RIGHT JOIN `".DB_PREFIX."portal` ON `".DB_PREFIX."students`.`StudentID` = `".DB_PREFIX."portal`.`LoginID` WHERE `Email` = '%s'", $thisEmail);
		break;
		case "Faculty":
			$sqlGetUser = sprintf("SELECT `FacultyID` AS `LoginID` FROM `".DB_PREFIX."faculties` RIGHT JOIN `".DB_PREFIX."portal` ON `".DB_PREFIX."faculties`.`FacultyID` = `".DB_PREFIX."portal`.`LoginID` WHERE `Email` =  '%s'", $thisEmail);
		break;
	}
		
	if(!empty($sqlGetUser)){
		$resultGet = db_query($sqlGetUser,DB_NAME,$conn);	
		if(db_num_rows($resultGet)>0){
			$rowGet = db_fetch_array($resultGet);
			return $rowGet['LoginID'];
		}
		else{
			return false;
		}
	}
}
// Get student name given the student ID
function getStudentName($StudentID){
	global $conn;
	
	$sqlGet = sprintf("SELECT CONCAT(`FName`,' ',`LName`) AS `StudentName` FROM `".DB_PREFIX."students` WHERE `StudentID` = '%s' AND `deletedFlag` = 0", $StudentID);
	//Execute the query
	$resultGet = db_query($sqlGet,DB_NAME,$conn);
	
	if(db_num_rows($resultGet)>0){
		$rowGet = db_fetch_array($resultGet);
		return $rowGet['StudentName'];
	}
	else{
		return "N/A";
	}
}
//Get student details given StudentID
function getStudentDetails($StudentID){
	global $conn;
	
	//Set the query
	$sqlGet = sprintf("SELECT *,CONCAT(`FName`,' ',`LName`) AS `StudentName` FROM `".DB_PREFIX."students` WHERE `StudentID` = '%s' AND `disabledFlag` = 0 AND `deletedFlag` = 0", $StudentID);
	//Execute the query
	$resultGet = db_query($sqlGet,DB_NAME,$conn);
	
	if(db_num_rows($resultGet)>0){
		return db_fetch_array($resultGet);
	}
	else{
		return array();
	}
	db_free_result($resultGet);
}
//Get faculty details given FacultyID
function getFacultyDetails($FacultyID){
	global $conn;
	
	//Set the query
	$sqlGet = sprintf("SELECT *,CONCAT(`Title`,' ',`FName`,' ',`LName`) AS `FacultyName` FROM `".DB_PREFIX."faculties` WHERE `FacultyID` = '%s' AND `disabledFlag` = 0 AND `deletedFlag` = 0", $FacultyID);
	//Execute the query
	$resultGet = db_query($sqlGet,DB_NAME,$conn);
	
	if(db_num_rows($resultGet)>0){
		return db_fetch_array($resultGet);
	}
	else{
		return array();
	}
	db_free_result($resultGet);
}
// Get course details given the course ID
function getCourseDetails($CourseID){
	global $conn;
	
	//Set the query
	$sqlGet = sprintf("SELECT `CName`,`Outline`,`Description`,`DeptID` FROM `".DB_PREFIX."courses` WHERE `CourseID` = '%s' AND `disabledFlag` = 0 AND `deletedFlag` = 0", $CourseID);
	//Execute the query
	$resultGet = db_query($sqlGet,DB_NAME,$conn);
	
	if(db_num_rows($resultGet)>0){
		return db_fetch_array($resultGet);
	}
	else{
		return array();
	}
	db_free_result($resultGet);
}
// Get course name given the course ID
function getCourseName($CourseID){
	global $conn;
	
	//Set the query
	$sqlGet = sprintf("SELECT `CName` FROM `".DB_PREFIX."courses` WHERE `CourseID` = '%s' AND `disabledFlag` = 0 AND `deletedFlag` = 0", $CourseID);
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
//
function getCourseFeesStructure($CourseID, $StudyMode){
	global $conn;
	
	if(!empty($CourseID)){
		$sqlGet = sprintf("
		SELECT `payment_name`,`pay_amount` FROM `".DB_PREFIX."payment_categs` WHERE `assoc_course` = 'All' AND `type` = 'Fee' OR `assoc_course` LIKE '%s' 
		UNION
		SELECT `payment_name`,`pay_amount` FROM `".DB_PREFIX."payment_categs` WHERE `pay_id` = %d", "%".$CourseID."%", $StudyMode);
		//mExecute the query
		$resultGet = db_query($sqlGet,DB_NAME,$conn);
		
		floatval($total);
		floatval($totalCourseMainFees);
		floatval($totalCourseFees);
		$total = 0;
		$returnHTML = '<div class="row">';
		$returnHTML .= '<div class="col-md-6 table-responsive">';
		$returnHTML .= '<table class="table table-bordered">';
		if(db_num_rows($resultGet)>0){
			while($rowGet = db_fetch_array($resultGet)){
				$returnHTML .= 
				"<tr><td><strong>".strtoupper($rowGet['payment_name'])."</strong></td><td class='text-right'>".number_format($rowGet['pay_amount'], 2)."</td></tr>";
				$total += floatval($rowGet['pay_amount']);
			}
			$totalCourseMainFees = $total;
			$totalTuitionFees = getCourseTuitionFees($CourseID);
			$totalCourseFees = floatval($totalCourseMainFees + $totalTuitionFees);
		}else{
			$totalTuitionFees = 0;
			$totalCourseFees = 0;
		}
		
		$returnHTML .= "<tr><td><strong>TUITION FEES</strong></td><td class='text-right'>".number_format($totalTuitionFees, 2)."</td></tr>";
		$returnHTML .= "<tr><td><strong>TOTAL</strong></td><td class='text-right'><strong>".number_format($totalCourseFees, 2)."</strong></td></tr>";
		$returnHTML .= "</table>";
		$returnHTML .= "</div>";
		$returnHTML .= "</div>";
		return $returnHTML;
	}
	
}
function getCourseFees($CourseID, $StudyMode){
	global $conn;
	
	//Set the query
	$sqlGet = sprintf("
	SELECT `pay_amount` FROM `".DB_PREFIX."payment_categs` WHERE `type` = 'Fee' AND `assoc_course` = 'All' OR `assoc_course` LIKE '%s'", "%".$CourseID."%");
	//Execute the query
	$resultGet = db_query($sqlGet,DB_NAME,$conn);
	
	floatval($total);
	$total = 0;
	if(db_num_rows($resultGet)>0){
		while($rowGet = db_fetch_array($resultGet)){
			$total += floatval($rowGet['pay_amount']);
		}	
	}
	
	return $total;
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
function getTotalPaid($StudentID){
	global $conn;
	
	//Set the query
	$sqlGet = sprintf("SELECT `payment_amount` FROM `".DB_PREFIX."payment_refs` WHERE `student_id` = '%s' AND `pay_status` = '%s'", $StudentID, 'COMPLETED');
	//Execute the query
	$resultGet = db_query($sqlGet,DB_NAME,$conn);
	
	floatval($TotalPaid);
	$TotalPaid = 0;
	if(db_num_rows($resultGet)>0){
		while($rowGet = db_fetch_array($resultGet)){
			$TotalPaid += floatval($rowGet['payment_amount']);
		}	
	}
	return $TotalPaid;
}
//
function getMinPayable($TotalDue,$TotalCourseFee){
	floatval($MinPayable);
	$MinPayable = 1000;
	
	if($TotalDue > 0 && $TotalDue >= ($TotalCourseFee/3)){
		$MinPayable = 1*($TotalDue/3);
		return $MinPayable;
	}else{
		return $MinPayable;
	}
}
//
function getOverPay($TotalPaid,$TotalCourseFee){
	$OverPay = 0;
	if(($TotalCourseFee-$TotalPaid)<0){
	$OverPay=($TotalPaid-$TotalCourseFee);
	//$OverPay= -1*($OverPay);
	return $OverPay;
	}else{
	return $OverPay;	
	}
}
//
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
//
function getPaymentStatus($TotalCourseFee, $TotalPaid){
	if($TotalPaid==0){
		return '<span class="text-danger">UNPAID</span>';
	}
	elseif($TotalCourseFee > $TotalPaid && $TotalPaid!=0){
		return '<span class="text-warning">STARTED</span>';
	}
	else{
		return '<span class="text-success">PAID</span>';
	}
}
//
function getCourseLevel($CourseID){
	global $conn;
	
	//Get course level
	$sqlGet = sprintf("SELECT `CLevel` FROM `".DB_PREFIX."courses` WHERE `CourseID` = '%s'", $CourseID);	
	//Execute the query
	$resultGet = db_query($sqlGet,DB_NAME,$conn);
	
	if(db_num_rows($resultGet)>0){
		$rowGet = db_fetch_array($resultGet);
		return $rowGet['CLevel'];
	}else{
		return false;
	}
}
//
function getCurrentAcademicID($currentDate){
	global $conn;
	
	$currentDate = isset($currentDate)?$currentDate:date('Y-m-d');
	//$currentDate = date('Y-m-d', strtotime($currentDate));
	
	$sqlGet = "SELECT `UID` FROM `".DB_PREFIX."academic_yrs` WHERE `RegDateOpen` > '".$currentDate."' AND `RegDateClosed` < '".$currentDate."'";
	//Execute the query
	$resultGet = db_query($sqlGet,DB_NAME,$conn);
	
	if(db_num_rows($resultGet)>0){
		$rowGet = db_fetch_array($resultGet);
		return $rowGet['UID'];
	}
	else{
		return 0;
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
// Get unit details given the unit ID
function getUnitDetails($UnitID){
	global $conn;
	
	//Set the query
	$sqlGet = sprintf("SELECT * FROM `".DB_PREFIX."units` WHERE `UnitID` = '%s' AND `disabledFlag` = 0 AND `deletedFlag` = 0", $UnitID);
	//Execute the query
	$resultGet = db_query($sqlGet,DB_NAME,$conn);
	
	if(db_num_rows($resultGet)>0){
		return db_fetch_array($resultGet);
	}
	else{
		return array();
	}
	db_free_result($resultGet);
}
//
//added three functions here : idd otuya
// get all units
function getUnits(){
	global $conn;
	$units = array();
	$unitsSql = sprintf("SELECT * FROM `".DB_PREFIX."units` WHERE `disabledFlag` = 0 AND `deletedFlag` = 0");
	$rtn = db_query($unitsSql,DB_NAME,$conn);	
	while($row = db_fetch_array($rtn)){ 
		array_push($units, $row);
	}
	return $units;
}
function getCourseUnits($course){
	global $conn;
	$courseunits = array();
	$unitsSql = sprintf("SELECT * FROM `".DB_PREFIX."units` WHERE `CourseID` = '%s' AND `disabledFlag` = 0 AND `deletedFlag` = 0", $course);
	$rtn = db_query($unitsSql,DB_NAME,$conn);	
	while($row = db_fetch_array($rtn)){ 
		array_push($courseunits, $row);
	}
	return $courseunits;
}
// get all courses
function getCourses(){
	global $conn;
	$courses = array();
	$unitsSql = sprintf("SELECT * FROM `".DB_PREFIX."courses` WHERE `disabledFlag` = 0 AND `deletedFlag` = 0");
	$rtn = db_query($unitsSql,DB_NAME,$conn);	
	while($row = db_fetch_array($rtn)){ 
		array_push($courses, $row);
	}
	return $courses;
}
//
function getUnitsByStatus($CourseID, $Status){
	global $conn;
	
	//set sql
	$unitsSql = sprintf("SELECT * FROM `".DB_PREFIX."units` AS U LEFT JOIN `".DB_PREFIX."units_registered` AS UR ON U.`UnitID` = UR.`UnitID` WHERE UR.`CourseID` = '%s' AND UR.`Status` = '%s' AND U.`disabledFlag` = 0 AND U.`deletedFlag` = 0 ORDER BY U.`YrTrim` ASC", $CourseID, $Status);
	return db_query($unitsSql,DB_NAME,$conn);	
}
//Get assigned units for given Faculty ID
function getFacultyUnits($FacultyID){
	global $conn;
	
	//Set the query
	$sqlGet = sprintf("SELECT `UT`.`UnitID`,`U`.`UName`,`U`.`Description` FROM `".DB_PREFIX."units_tutors` AS `UT` LEFT JOIN `".DB_PREFIX."units` AS `U` ON `UT`.`UnitID` = `U`.`UnitID` WHERE `UT`.`Status` = %d AND `UT`.`FacultyID` = '%s' AND `disabledFlag` = 0 AND `deletedFlag` = 0", 1, $FacultyID);
	//Execute the query
	return db_query($sqlGet,DB_NAME,$conn);
	
}
// List unit tutors assigned to the given unit ID 
function getUnitTutors($UnitID){
	global $conn;
	
	//Get registered units
	$sqlCheck = sprintf("SELECT `FacultyID` FROM `".DB_PREFIX."units_tutors` WHERE `UnitID` = '%s' AND `Status` = %d", $UnitID, 1);	
	//Execute the query
	$resultGet = db_query($sqlCheck,DB_NAME,$conn);
	$tutors = array();
	//Check if any records
	if(db_num_rows($resultGet)>0){		
		while($rowGet = db_fetch_array($resultGet)){
			$tutors[] = '<a href="#">'.getFacultyName($rowGet['FacultyID']).'</a>';			
		}
		$thisTutors = implode(", ", $tutors);
	}else{
		$thisTutors = 'N/A';
	}
	
	return $thisTutors;
}
// Get registered units for the given student ID
function getRegisteredStudentUnits($StudentID, $CourseID, $Status){
	global $conn;
	
	//Get registered units
	$sqlCheck = sprintf("SELECT `UR`.`UnitID`, `U`.`UName` FROM `".DB_PREFIX."units_registered` AS `UR` RIGHT JOIN `".DB_PREFIX."units` AS `U` ON `UR`.`UnitID` = `U`.`UnitID` WHERE `UR`.`StudentID` = '%s' AND `UR`.`CourseID` = '%s' AND `UR`.`Status` = '%s'", $StudentID, $CourseID, $Status);	
	//Execute the query
	$resultGet = db_query($sqlCheck,DB_NAME,$conn);
	
	$returnHTML = '';
	//Check if any records
	if(db_num_rows($resultGet)>0){
		$returnHTML .= '<div class="table-responsive">';
		$returnHTML .= '<table class="table table-bordered">';
		$returnHTML .= '<tr><th>Unit Code</th><th>Unit Name</th></tr>';
		while($rowGet = db_fetch_array($resultGet)){
			$returnHTML .= '<tr><td><strong>'.$rowGet['UnitID'].'</strong></td><td>'.$rowGet['UName'].'</td></tr>';
		}
		$returnHTML .= '</div>';
		$returnHTML .= '</table>';
	}else{
		$returnHTML = 'No units registered';
	}
	
	return $returnHTML;
}
// Get registered units for the given student ID
function registeredUnitStatus($StudentID, $UnitID){
	global $conn;
	
	//Get registered units
	$sqlCheck = sprintf("SELECT `Status` FROM `".DB_PREFIX."units_registered` WHERE `StudentID` = '%s' AND `UnitID` = '%s'", $StudentID, $UnitID);	
	//Execute the query
	$resultGet = db_query($sqlCheck,DB_NAME,$conn);
	
	//Check if any records
	if(db_num_rows($resultGet)>0){
		$rowGet = db_fetch_array($resultGet);
		return $rowGet['Status'];
	}else{
		return false;
	}
}
// Get department name given the department ID
function getDepartmentName($DeptID){
	global $conn;
	
	//Set the query
	$sqlGet = sprintf("SELECT `DName` FROM `".DB_PREFIX."departments` WHERE `DeptID` = '%s' AND `disabledFlag` = 0 AND `deletedFlag` = 0", $DeptID);
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
//
function getFacultyDepartments($Departments){
	if(!empty($Departments)){
		$DeptIDs = array();
		$DeptIDs = explode(",", $Departments);
		return count($DeptIDs);
	}else{
		return 0;
	}
}
//
function getFacultyLectures($UserID){
	global $conn;
	
	if( !empty($UserID) && isset($UserID) ){		
		$sql = sprintf("SELECT COUNT(*) FROM `".DB_PREFIX."units_tutors` WHERE `FacultyID` = '%s' AND `Status` = %d", $UserID, 1);
		$res = db_query($sql,DB_NAME,$conn);
		$row = db_fetch_array($res);
		reset($row);
		return current($row);
	}else{
		return 0;
	}
}
//
function getFacultyActiveStudents($UserID){
	return 0;
}
//
function getStudentCourses($Courses){
	if(!empty($Courses)){
		$CourseIDs = array();
		$CourseIDs = explode(",", $Courses);
		return count($CourseIDs);
	}else{
		return 0;
	}
}
//
function getStudentUnits($UserID, $Status=""){
	global $conn;
	
	if( !empty($UserID) && isset($UserID) ){		
		
		if( !empty($Status) ){
			$sql = sprintf("SELECT COUNT(*) FROM `".DB_PREFIX."units_registered` WHERE `StudentID` = '%s' AND `Status` = '%s'", $UserID, $Status);
		}else{
			$sql = sprintf("SELECT COUNT(*) FROM `".DB_PREFIX."units_registered` WHERE `StudentID` = '%s'", $UserID);
		}
		$res = db_query($sql,DB_NAME,$conn);
		$row = db_fetch_array($res);
		reset($row);
		return current($row);
	}else{
		return 0;
	}
}
//
function getStudentAssignments($UserID){
	return 0;
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
//List student courses for navigation
function list_student_courses_nav($Courses, $tab){
	$returnHTML = "";
	
	if(!empty($Courses)){
		$CourseIDs = array();
		$CourseIDs = explode(",", $Courses);		
		
		//$returnHTML .= "<ul class=\"dropdown-menu dropdown-courses\">";
		$divider = "<li class=\"divider\"></li>";
		if(!empty($CourseIDs)){
			foreach($CourseIDs as $CourseID){
				$returnHTML .= 
				"<li><a href=\"?tab=".$tab."&task=view&CourseID=".$CourseID."\">".$CourseID."</a></li>".$divider;
				//$divider = "";
			}			
			
		}else{
			$returnHTML .= "<li>N/A</li>";
		}
		//$returnHTML .= "</ul>";		
	}
	return $returnHTML;
}
//List student courses given CourseIDs
function list_student_courses($CourseIDs){
	$returnHTML = "";
	if(!empty($CourseIDs)){
	  $CourseIDs = explode(",", $CourseIDs);
			
	  reset($CourseIDs);
	  if(!empty($CourseIDs)){
		  $returnHTML .= "<ul class=\"list-unstyled\">";
		  //Loop foreach($CourseIDs as &$CourseID)
		  while (list(, $CourseID) = each($CourseIDs)) {			  
			  $returnHTML .= "<li><i class=\"fa fa-book fa-fw\"></i><a href=\"?tab=2&task=view&CourseID=".$CourseID."\">".$CourseID." (".getCourseName($CourseID).")</a></li>";			   
		  }
		  $returnHTML .= "</ul>";
	  }
	}else{
		$returnHTML .= "<p>You have not registered for any courses.</p>";
	}	
	return $returnHTML;
}
//List faculty departments
function list_faculty_departments($Departments){
	$returnHTML = "";
	if(!empty($Departments)){
		$DeptIDs = array();	
		$DeptIDs = explode(",", $Departments);				
				
		if(!empty($DeptIDs)){
			$returnHTML .= "<ul class=\"list-unstyled\">";
			foreach($DeptIDs as $DeptID){
				$returnHTML .= "<li><i class=\"fa fa-sitemap fa-fw\"></i><a href=\"#\">".getDepartmentName($DeptID)."</a></li>";
			}
			$returnHTML .= "</ul>";
		}else{
			$returnHTML .= "<p>You are not registered to any departments.</p>";
		}		
	}
	return $returnHTML;
}
//List faculty departments for navigation
function list_lecture_depts_nav($Departments){
	$returnHTML = "";
	if(!empty($Departments)){
		$DeptIDs = array();	
		$DeptIDs = explode(",", $Departments);				
		
		$returnHTML .= "<ul class=\"nav nav-second-level\">";
		if(!empty($DeptIDs)){
			foreach($DeptIDs as $DeptID){
				$returnHTML .= "<li><a href=\"#\">".$DeptID."</a></li>";
			}
		}else{
			$returnHTML .= "<li>N/A</li>";
		}
		$returnHTML .= "</ul>";
	}
	return $returnHTML;
}
//List lectures by given FacultyID for navigation
function list_faculty_lectures_nav($FacultyID,$tab){
	global $conn;
	
	//Execute the query
	$resultGet = getFacultyUnits($FacultyID);
	
	$returnHTML = "";
	
	if(db_num_rows($resultGet)>0){	
		
		//$returnHTML .= "<ul class=\"dropdown-menu dropdown-lectures\">";				
		while($rowGet = db_fetch_array($resultGet)){
			$divider = "<li class=\"divider\"></li>";
			$returnHTML .= 
			"<li><a href=\"?tab=".$tab."&UnitID=".$rowGet['UnitID']."\">".$rowGet['UName']."</a></li>".$divider;
			$divider = "";
		}
		
	}else{
		$returnHTML .= "<li>N/A</li>";
	}
	//$returnHTML .= "</ul>";

	return $returnHTML;
}
//List lecturer units given FacultyID
function list_lecture_units($FacultyID){
	global $conn;
	
	//Execute the query
	$resultGet = getFacultyUnits($FacultyID);
	
	$returnHTML = "";
	if(db_num_rows($resultGet)>0){
		$returnHTML .= "<ul class=\"list-unstyled\">";
		while($rowGet = db_fetch_array($resultGet)){
			$returnHTML .= "<li><i class=\"fa fa-book fa-fw\"></i><a href=\"?tab=3&task=view&UnitID=".$rowGet['UnitID']."\">".$rowGet['UName']."</a></li>";
		}
		$returnHTML .= "</ul>";		
	}
	else{
		$returnHTML .= "<p>No lectures have been assigned to your account.</p>";
	}	
	return $returnHTML;
	
	db_free_result($resultGet);
}
//List recent login history
function list_login_history($LoginID, $Limit = 5){
	global $conn;
	
	//Set the query
	$sqlGet = sprintf("SELECT `LoginDate`,`Source` FROM `".DB_PREFIX."portal_logs` WHERE `LoginID` = '%s' ORDER BY `LoginDate` DESC LIMIT %d", $LoginID, $Limit);
	//Execute the query
	$resultGet = db_query($sqlGet,DB_NAME,$conn);
	
	$returnHTML = "";
	if(db_num_rows($resultGet)>0){		
		while($rowGet = db_fetch_array($resultGet)){
			$returnHTML .= "<a href=\"#\" class=\"list-group-item\">";
			$returnHTML .= "<i class=\"fa fa-sign-in fa-fw\"></i> Login";
			$returnHTML .= "<span class=\"pull-right text-muted small\">".formatDateAgo($rowGet['LoginDate'])."</em></span>";
			$returnHTML .= "</a>";
		}
	}
	else{
		$returnHTML .= "<p>No recent activity recorded.</p>";
	}
	
	return $returnHTML;
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
					<a href="?tab=7">
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
//List announcements
function list_announcements($UserType){
	global $conn;
	
	//Get current date
	$currDateStr = date('Y-m-d');
	
	//Set the query
	$sqlGet = "SELECT `Title`,`Announcement` FROM `".DB_PREFIX."announcements` WHERE `UserType` = '$UserType' AND (DATE_FORMAT(PublishFrom,'%Y-%m-%d') <= DATE_FORMAT('$currDateStr','%Y-%m-%d') AND DATE_FORMAT(PublishTo,'%Y-%m-%d') >= DATE_FORMAT('$currDateStr','%Y-%m-%d')) AND `disabledFlag` = 0 AND `deletedFlag` = 0 ORDER BY `PublishFrom` ASC";
	//Execute the query
	$resultGet = db_query($sqlGet,DB_NAME,$conn);
	
	$returnHTML = "";
	if(db_num_rows($resultGet)>0){
		while($rowGet = db_fetch_array($resultGet)){
			if(!empty($rowGet['Title'])){
				$returnHTML .= "<h3>".$rowGet['Title']."</h3>";
			}
			$returnHTML .= "<p>".$rowGet['Announcement']."</p>";
		}		
	}
	else{
		$returnHTML .= "<p>Currently, we do not have any announcements published.</p>";
	}
	return $returnHTML;
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
//
function getCalendarEvents($UserType){	
	$eventsJSON = "{
		title: 'All Day Event',
		start: '2018-02-01'
	},
	{
		title: 'Long Event',
		start: '2018-02-07',
		end: '2018-02-10'
	},
	{
		id: 999,
		title: 'Repeating Event',
		start: '2018-02-09T16:00:00'
	},
	{
		id: 999,
		title: 'Repeating Event',
		start: '2018-02-16T16:00:00'
	},
	{
		title: 'Conference',
		start: '2018-02-11',
		end: '2018-02-13'
	},
	{
		title: 'Meeting',
		start: '2018-02-12T10:30:00',
		end: '2018-02-12T12:30:00'
	},
	{
		title: 'Lunch',
		start: '2018-02-12T12:00:00'
	},
	{
		title: 'Meeting',
		start: '2018-02-12T14:30:00'
	},
	{
		title: 'Happy Hour',
		start: '2018-02-12T17:30:00'
	},
	{
		title: 'Dinner',
		start: '2018-02-12T20:00:00'
	},
	{
		title: 'Birthday Party',
		start: '2018-02-13T07:00:00'
	},
	{
		title: 'Click for Google',
		url: 'http://google.com/',
		start: '2018-02-28'
	},
	{
		title: 'Valentines Day',				
		start: '2018-02-14'
	},";
		
	return $eventsJSON;
}
///notification function
function mail_config($email, $name, $subject, $content){
	$mail = new PHPMailer;
	$body = preg_replace('/\\\\/','', $content); //Strip backslashes
	
	switch(MAILER){
		case 'smtp':		
		$mail->isSMTP(); // telling the class to use SMTP
		$mail->SMTPDebug = 0;
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
	$mail->addReplyTo(INFO_EMAIL, INFO_NAME);
	$mail->addAddress($email, $name);
	$mail->addBCC(INFO_EMAIL, INFO_NAME);
	$mail->Subject = $subject;
	$mail->msgHTML($body);
	$mail->isHTML(true); // send as HTML
	$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';
	if (!$mail->send()) {
		return "Mailer Error: " . $mail->ErrorInfo;
	}
}
?>