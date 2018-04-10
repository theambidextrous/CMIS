<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

if(!empty($guestnamePrefix)){ $guestnamePrefix .= '-'; }

function getGuestID($guestName) {

	$_SESSION['cometchat']['guestMode'] = 1;

	global $cookiePrefix;

	$userid = 0;

	if(function_exists('hooks_guestLogin')){
		$userid = hooks_guestLogin(array('guestname' => $guestName));
	}

	if($userid == 0) {
		if (!empty($_COOKIE[$cookiePrefix.'guest'])) {
			$checkId = base64_decode($_COOKIE[$cookiePrefix.'guest']);

			$query = sql_query('getGuestID',array('id'=>$checkId));
			$result = sql_fetch_assoc($query);

			if (!empty($result['id'])) {
				$userid = $result['id'];
			}
		}
		if(!empty($guestName) && !empty($userid)){
			$query = sql_query('updateGuestName',array('id'=>$userid,'name'=>$guestName));
		}
		if (empty($userid) && ((empty($_REQUEST['callbackfn']) || (!empty($_REQUEST['callbackfn']) && $_REQUEST['callbackfn'] != 'desktop') || !empty($_REQUEST['guest_login'])))) {
			if(empty($guestName)){
				$guestName = rand(10000,99999);
			}
			$query = sql_query('insertGuest',array('name'=>$guestName));
			$userid = sql_insert_id('cometchat_guests');
			setcookie($cookiePrefix.'guest', base64_encode($userid), time()+3600*24*365, "/");
		}
		if (isset($_REQUEST['callbackfn']) && $_REQUEST['callbackfn'] == 'mobileapp') {
	        sql_query('insertStatus',array('userid'=>$userid));
	    }
	}
	return $userid;
}

function getGuestsList($userid,$time,$originalsql) {
	global $guestsList;
	global $guestsUsersList;
	global $guestnamePrefix;
	global $firstguestID;

	$guest_sql = sql_getQuery('getGuestsList',array('guestnamePrefix'=>$guestnamePrefix, 'time'=>$time));

	if ($userid < $firstguestID) {
		if ($guestsUsersList == 2) {
			$sql = $originalsql;
		} else if ($guestsUsersList == 3) {
			$sql = $guest_sql." UNION ".$originalsql;
		}else{
			$sql = $guest_sql;
		}
	} else {
		if ($guestsList == 2) {
			$sql = $originalsql;
		} else if ($guestsList == 3) {
			$sql = $guest_sql." UNION ".$originalsql;
		}else{
			$sql = $guest_sql;
		}
	}
	return $sql;
}

function getChatroomGuests($chatroomid,$time,$originalsql) {
	global $guestnamePrefix;

	$sql = sql_getQuery('getChatroomGuests',array('guestnamePrefix'=>$guestnamePrefix, 'chatroomid'=>$chatroomid, 'time'=>$time, 'originalsql'=>$originalsql));

	return $sql;
}

function getChatroomBannedGuests($chatroomid,$time,$originalsql) {
	global $guestnamePrefix;

   $sql = sql_getQuery('getChatroomBannedGuests',array('guestnamePrefix'=>$guestnamePrefix, 'chatroomid'=>$chatroomid, 'originalsql'=>$originalsql));

   return $sql;
}

function getGuestDetails($userid) {
	global $guestnamePrefix;

	$sql = sql_getQuery('getGuestDetails',array('guestnamePrefix'=>$guestnamePrefix, 'userid'=>$userid));

	return $sql;
}
