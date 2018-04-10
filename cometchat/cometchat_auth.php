<?php
/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/



/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
global $cc_auth_table_prefix, $cc_auth_db_usertable, $cc_auth_db_usertable_userid, $cc_auth_db_usertable_username, $cc_auth_db_usertable_name, $cc_auth_db_avatartable, $cc_auth_db_avatarfield, $cc_auth_db_linkfield, $cc_auth_db_groupfield;

$cc_auth_table_prefix = ''								;
$cc_auth_db_usertable =	'cometchat_users'				;
$cc_auth_db_usertable_userid = 'userid'					;
$cc_auth_db_usertable_username = 'username'				;
$cc_auth_db_usertable_name = 'displayname'				;
$cc_auth_db_avatartable = ' '							;
$cc_auth_db_avatarfield = ' '.$cc_auth_db_usertable.'.avatar '	;
$cc_auth_db_linkfield = 'link '						;
$cc_auth_db_groupfield = 'grp '						;

/* COMETCHAT'S SOCIAL AUTHENTICATION CLASS */

class CCAuth{

	function __construct(){
		$this->defineFromGlobal('table_prefix');
		$this->defineFromGlobal('db_usertable');
		$this->defineFromGlobal('db_usertable_userid');
		$this->defineFromGlobal('db_usertable_username');
		$this->defineFromGlobal('db_usertable_name');
		$this->defineFromGlobal('db_avatartable');
		$this->defineFromGlobal('db_avatarfield');
		$this->defineFromGlobal('db_linkfield');
		$this->defineFromGlobal('db_groupfield');
	}

	function defineFromGlobal($key){
		if(isset($GLOBALS['cc_auth_'.$key])&&!defined(strtoupper($key))){
			define(strtoupper($key), $GLOBALS['cc_auth_'.$key]);
			unset($GLOBALS['cc_auth_'.$key]);
		}
	}

	function getUserID() {
		$userid = 0;


		if (!empty($_SESSION['basedata']) && $_SESSION['basedata'] != 'null') {
			   $_REQUEST['basedata'] = $_SESSION['basedata'];
		   }

		if (!empty($_REQUEST['basedata'])) {
		   $userid = $_REQUEST['basedata'];
		}

		if (!empty($_SESSION['cometchat']['userid']) && !empty($_SESSION['cometchat']['ccauth'])){
			$userid = $_SESSION['cometchat']['userid'];
		}

		return $userid;
	}

	function chatLogin($userName,$userPass) {
		global $guestsMode;
		$userid = 0;
		if(!empty($userName) && !empty($_REQUEST['social_details'])) {
			$social_details = json_decode($_REQUEST['social_details']);
			$userid = socialLogin($social_details);
		}
		if(!empty($_REQUEST['guest_login']) && $userPass == "CC^CONTROL_GUEST" && $guestsMode == 1){
			$userid = getGuestID($userName);
		}
		return $userid;
	}

	function getFriendsList($userid,$time) {
		global $hideOffline;
		$offlinecondition = '';
		if ($hideOffline) {
			$offlinecondition = " (cometchat_status.lastactivity > ".(sql_real_escape_string($time)-ONLINE_TIMEOUT*2)." OR cometchat_status.isdevice = 1) and ";
		}
		$sql = sql_getQuery('auth_getFriendsList', array('db_groupfield'=>DB_GROUPFIELD,'timestampCondition'=>$offlinecondition));
		return $sql;
	}

	function getUserDetails($userid) {
		$sql =  sql_getQuery('auth_getUserDetails',array('userid'=>$userid));
		return $sql;
	}

	function getActivechatboxdetails($userids) {
		$sql = sql_getQuery('auth_getActivechatboxdetails',array('userids'=>$userids, 'db_groupfield'=>DB_GROUPFIELD));
		return $sql;
	}

	function fetchLink($link) {
	   return $link;
	}

	function getAvatar($data) {
		if(empty($data)){
			return BASE_URL.'images/noavatar.png';
		}
		return $data;
	}

	function getTimeStamp() {
		return time();
	}

	function processTime($time) {
		return $time;
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/* HOOKS */

	function hooks_statusupdate($userid,$statusmessage) {

	}

	function hooks_forcefriends() {

	}

	function hooks_activityupdate($userid,$status) {

	}

	function hooks_message($userid,$to,$unsanitizedmessage) {

	}
}
