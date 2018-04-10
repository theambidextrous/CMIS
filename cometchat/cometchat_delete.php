<?php
/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

include_once dirname(__FILE__).DIRECTORY_SEPARATOR."cometchat_init.php";

$userid = sql_real_escape_string($_REQUEST['from']);
$to = sql_real_escape_string($_REQUEST['to']);

$_SESSION['cometchat']['cometchat_user_'.$to] =array();

$query = sql_query('cometchatdelete_sql1',array('from'=>$userid, 'to'=>$to));
$query = sql_query('cometchatdelete_sql2',array('from'=>$to, 'to'=>$userid));
$query = sql_query('cometchatdelete_sql3',array('from'=>$to, 'to'=>$userid));
$query = sql_query('cometchatdelete_sql4',array('from'=>$userid, 'to'=>$to));

$error = sql_error($GLOBALS['dbh']);

$response = array();
$response['id'] = $to;
if (!empty($error) ) {
	$response['result'] = "0";
	header('content-type: application/json; charset=utf-8');
	$response['error'] = sql_error($GLOBALS['dbh']);
	echo json_encode($response);
	exit;
}

header('content-type: application/json; charset=utf-8');

$response['result'] = "1";
echo json_encode($response);

?>
