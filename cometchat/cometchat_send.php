<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

include_once dirname(__FILE__).DIRECTORY_SEPARATOR."cometchat_init.php";

if(empty($_SESSION['cometchat'])||empty($_SESSION['cometchat']['user'])){
	getStatus();
}

if(isset($_REQUEST['status'])) {

	if ($userid > 0) {
		$message = sql_real_escape_string($_REQUEST['status']);
		$query = sql_query('insertCometStatus',array('userid'=>$userid, 'message'=>sanitize_core($message)));

		if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }

		$_SESSION['cometchat']['user']['s'] = $message;

		if ($message == 'offline') {
			$_SESSION['cometchat']['cometchat_sessionvars']['buddylist'] = 0;
		}

		if (function_exists('hooks_activityupdate')) {
			hooks_activityupdate($userid,$message);
		}
	}

	if (!empty($_GET['callback'])) {
		header('content-type: application/json; charset=utf-8');
		echo $_GET['callback'].'(1)';
	} else {
		echo "1";
	}
	exit(0);
}

if(isset($_REQUEST['lastseenSettingsFlag']) && !empty($_REQUEST['lastseenSettingsFlag'])) {
    $message = $_REQUEST['lastseenSettingsFlag'];
   	setLastseensettings($message);
   	if (!empty($_GET['callback'])) {
		header('content-type: application/json; charset=utf-8');
		echo $_GET['callback'].'(1)';
	} else {
		echo "1";
	}
	exit(0);
}

if(isset($_REQUEST['readreceiptsetting']) && !empty($_REQUEST['readreceiptsetting'])) {
    $message = $_REQUEST['readreceiptsetting'];
   	setReadReceiptsettings($message);
   	if (!empty($_GET['callback'])) {
		header('content-type: application/json; charset=utf-8');
		echo $_GET['callback'].'(1)';
	} else {
		echo "1";
	}
	exit(0);
}

if (isset($_REQUEST['guestname']) && $userid > 0) {
	$guestname = sql_real_escape_string(sanitize_core($_REQUEST['guestname']));
	$isUpdate = 0;
	if($guestname != ''){
		$query = sql_query('checkGuestName',array('name'=>$guestname));
		if(sql_num_rows($query) == 0 || $uniqueguestname == 0){
			$query = sql_query('updateGuestName',array('name'=>$guestname, 'id'=>$userid));
			if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }
			if(empty($_SESSION['cometchat'])){
				$_SESSION['cometchat'] = array();
			}
			if(empty($_SESSION['cometchat']['user'])){
				$_SESSION['cometchat']['user']=array('id'=>$userid);
			}
			$_SESSION['cometchat']['user']['n'] = $_SESSION['cometchat']['username'] =  $guestnamePrefix.$guestname;
			$isUpdate = 1;
			$response['error'] = 0;
			$response['message'] = $language['guest_updated_success'];
		} else{
			$response['error'] = 1;
			$response['message'] = $guestname.' '.$language['guest_already_exist'];
		}

		/* START: Backward compatibility CometChat Version 6.9.22  29th Dec 2017*/
		if (!empty($GLOBALS['appversion']) && cc_version_compare($GLOBALS['appversion'],'6.9.23') == -1) {
			if (!empty($_GET['callback'])) {
				header('content-type: application/json; charset=utf-8');
				echo $_GET['callback'].'('.$isUpdate.')';
			} else {
				echo $isUpdate;
			}
			exit();
		}
		/* END: Backward compatibility CometChat Version 6.9.22  29th Dec 2017*/
		sendCCResponse(json_encode($response));
		exit();
	}
	exit(0);
}

if (isset($_REQUEST['statusmessage'])) {
	$message = $_REQUEST['statusmessage'];
	if (empty($_SESSION['cometchat']['statusmessage']) || ($_SESSION['cometchat']['statusmessage'] != $message)) {
		$query = sql_query('insertStatusMessage',array('userid'=>$userid, 'message'=>sanitize_core($message)));
		if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }

		$_SESSION['cometchat']['statusmessage'] = $message;

		if (function_exists('hooks_statusupdate')) {
			hooks_statusupdate($userid,$message);
		}
	}

	if (!empty($_GET['callback'])) {
		header('content-type: application/json; charset=utf-8');
		echo $_GET['callback'].'(1)';
	} else {
		echo "1";
	}

	exit(0);
}


if ( (!empty($_REQUEST['to']) && isset($_REQUEST['message']) && $_REQUEST['message']!='')||(!empty($_REQUEST['broadcast']))) {

	if(!empty($_REQUEST['broadcast'])){
		$broadcasttemp = $_REQUEST['broadcast'];
		$broadcast = array();
		$broadcast_toids = array();
		$bsize =sizeof($broadcasttemp);
		foreach ($broadcasttemp as $key => $value) {
			$value["dir"] = 0;
			$value["localmessageid"] = $key;
			array_push($broadcast, $value);
		}
	}else{
		$to = sql_real_escape_string($_REQUEST['to']);
		$message = str_ireplace('CC^CONTROL_','',$_REQUEST['message']);
	}
	if ($userid > 0) {

		if (!in_array($userid,$bannedUserIDs) && !in_array($_SERVER['REMOTE_ADDR'],$bannedUserIPs)) {

			$response = array();
			if(empty($_REQUEST['broadcast'])){
				$response = sendMessage($to,$message,0);
			}else{
				broadcastMessage($broadcast);
			}
			if(!defined('DEV_MODE') || DEV_MODE == '0'){
				header('content-type: application/json; charset=utf-8');
				sendCCResponse(json_encode($response));
			}

			if(empty($_REQUEST['broadcast']) && !empty($response)){
				if(strpos($response['m'],'@') === 0 && $usebots) {
					checkBotMessage($to, $message, 0);
				}
				$response['push'] = 'Do not push as message seems to be duplicate';
				if(empty($response['donotpush'])){
					$response['push'] = 'Missing username in Session';
					if(!empty($_SESSION['cometchat'])&&!empty($_SESSION['cometchat']['user'])&&!empty($_SESSION['cometchat']['user']['n'])){
						$response['push'] = pushMobileNotification($to,$response['id'],$_SESSION['cometchat']['user']['n'].": ".$response['m']);
					}
				}
			}

			if(defined('DEV_MODE') && DEV_MODE == '1'){
				header('content-type: application/json; charset=utf-8');
				sendCCResponse(json_encode($response));
			}

		} else if(empty($_REQUEST['broadcast'])){
			$query = sql_query('insertMessage',array('userid'=>$userid, 'to'=>$to, 'message'=>sanitize($bannedMessage), 'timestamp'=>getTimeStamp(), 'old'=>0, 'dir'=>2));
			if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }


			if (!empty($_GET['callback'])) {
				header('content-type: application/json; charset=utf-8');
				echo $_GET['callback'].'()';
			}
		}
	}

	exit(0);
}
