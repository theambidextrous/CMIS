<?php
/*********************************************
Company:		Wits Technologies Ltd
Developer:	Sammy Mwaura Waweru
Mobile:			+254721428276
Email:			sammy@witstechnologies.co.ke
Website:		http://www.witstechnologies.co.ke/
*********************************************/
function db_connect($dbhost, $dbuser, $dbpass, $dbname) {
	if(!strlen($dbuser) || !strlen($dbpass) || !strlen($dbhost))
		return NULL;

	$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	if($conn && $dbname)
		db_select_database($conn, $dbname);
	//set desired encoding just in case mysql charset is not UTF-8 - Thanks to FreshMedia
	if($conn) {
		mysqli_query($conn, 'SET NAMES "UTF8"');
		mysqli_query($conn, 'SET COLLATION_CONNECTION=utf8_general_ci');
	}
	return $conn;	
}

function db_close($conn){
	return mysqli_close($conn);
}

function db_select_database($conn, $dbname) {
	return mysqli_select_db($conn, $dbname);
}

function db_version(){
	global $conn;
	return mysqli_get_server_version($conn);
}

// execute sql query
function db_query($query, $dbname, $conn){

	$response = mysqli_query($conn, $query);

	if(!$response) { //error reporting
		$ErrPage = curPageURL();
		$msg = '['.$query.']'."\n\n".db_error($conn);
		if(Error_alertAdmin('MySQL DB Error #'.db_errno($conn), $msg, $ErrPage, $reply_to)){
			echo "CONNECTION ERROR: The site administrator has been notified and will rectify the problem ASAP.";
			exit();
		}
		else{
			echo "CONNECTION ERROR: Please notify the site administrator if this error persists.";
			exit();
		}

		//echo $msg; #uncomment during debuging or dev.
	}
	return $response;
}

function db_squery($query){ //smart db query...utilizing args and sprintf
	global $conn;

	$args  = func_get_args();
	$query = array_shift($args);
	$query = str_replace("?", "%s", $query);
	$args  = array_map('mysqli_real_escape_string', $args);
	array_unshift($args,$query);
	$query = call_user_func_array('sprintf',$args);
	return db_query($query,$dbname,$conn);
}

function db_count($query){
		list($count) = db_fetch_row(db_query($query));
		return $count;
}

function db_fetch_array($result, $mode=false) {
	if($mode){
	  /* numeric array */
	  return ($result)?db_output($row = mysqli_fetch_array($result, MYSQLI_NUM)):NULL;
	}
	else {
	  /* associative array */
	  return ($result)?db_output($row = mysqli_fetch_array($result, MYSQLI_ASSOC)):NULL;
	}
}

function db_fetch_row($result) {
	return ($result)?db_output(mysqli_fetch_row($result)):NULL;
}

function db_fetch_fields($result) {
	return mysqli_fetch_field($result);
}

function db_assoc_array($result){
	if($result && db_num_rows($result)){
		while ($row = db_fetch_array($result,$mode))
			$results[] = $row;
	}
	return $results;
}

function db_num_rows($result) {
	return ($result)?mysqli_num_rows($result):0;
}

function db_affected_rows($conn) {
	return mysqli_affected_rows($conn);
}

function db_data_seek($result, $row_number) {
	return mysqli_data_seek($result, $row_number);
}

function db_data_reset($result){
	return mysqli_data_seek($result,0);
}

function db_insert_id() {
	global $conn;
	return mysqli_insert_id($conn);
}

function db_free_result($result) {
	return mysqli_free_result($result);
}

function db_output($param) {

	if(!function_exists('get_magic_quotes_runtime') || !get_magic_quotes_runtime()) //Sucker is NOT on - thanks.
		return $param;

	if (is_array($param)) {
		reset($param);
		while(list($key, $value) = each($param)) {
			$param[$key] = db_output($value);
		}
		return $param;
	}elseif(!is_numeric($param)) {
		$param = trim(stripslashes($param));
	}

	return $param;
}

//Do not call this function directly...use db_input
function db_real_escape($val, $quote=false){
	global $conn;

	//Magic quotes crap is taken care of in main.inc.php
	$val = mysqli_real_escape_string($conn, $val);

	return ($quote)?"'$val'":$val;
}

function db_input($param, $quote=true) {
	//is_numeric doesn't work all the time...9e8 is considered numeric..which is correct...but not expected.
	if($param && preg_match("/^\d+(\.\d+)?$/",$param))
		return $param;

	if($param && is_array($param)){
		reset($param);
		while (list($key, $value) = each($s)) {
			$param[$key] = db_input($value, $quote);
		}
		return $param;
	}
	return db_real_escape($param, $quote);
}

function db_error($conn){
	return mysqli_error($conn);
}

function db_errno($conn){
	return mysqli_errno($conn);
}
?>