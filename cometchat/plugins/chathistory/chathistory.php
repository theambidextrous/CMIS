<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/
include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins.php");

if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php")) {
    include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php");
}

$history = intval($_REQUEST['history']);
function logs() {
    $usertable = TABLE_PREFIX.DB_USERTABLE;
    $usertable_username = DB_USERTABLE_NAME;
    $usertable_userid = DB_USERTABLE_USERID;
    global $history;
    global $userid;
    global $chathistory_language;
    global $guestsMode;
    global $guestnamePrefix;
    global $response;

    if (!empty($_REQUEST['history'])) {
        $currentroom = $_REQUEST['history'];
    }
    $guestpart = "";
    if (!empty($_REQUEST['chatroommode'])) {
        if ($guestsMode == '1') {
            $guestpart = sql_getQuery('groupchathistory_guestpart',array('guestnamePrefix'=>$guestnamePrefix, 'chatroomid'=>$history));
        }
        
        $sql = sql_getQuery('groupchathistory',array('usertable_username'=>$usertable_username, 'usertable_userid'=>$usertable_userid, 'usertable'=>$usertable, 'chatroomid'=>$history, 'guestpart'=>$guestpart));
    } else {
        if ($guestsMode == '1') {
            $guestpart = sql_getQuery('chathistory_guestpart',array('usertable_username'=>$usertable_username, 'usertable_userid'=>$usertable_userid, 'usertable'=>$usertable, 'chatroomid'=>$history, 'guestnamePrefix'=>$guestnamePrefix, 'userid'=>$userid));
		}

        $sql = sql_getQuery('chathistory',array('usertable_username'=>$usertable_username, 'usertable_userid'=>$usertable_userid, 'usertable'=>$usertable, 'chatroomid'=>$history, 'userid'=>$userid, 'guestpart'=>$guestpart));
    }
        $query = sql_query($sql, array(), 1);
        $previd = 1000000;
        if (sql_num_rows($query)>0) {
		 while ($chat = sql_fetch_assoc($query)) {
                     if (function_exists('processName')) {
                         $chat['fromu'] = processName($chat['fromu']);
                         if (empty($_REQUEST['chatroommode'])) {
                             $chat['tou'] = processName($chat['tou']);
                             }
                    }
                    if (empty($_REQUEST['chatroommode'])) {

                        if ($chat['from'] == $userid) {
                            $chat['fromu'] = $chathistory_language[1];
                        }
                        } else {
                            if ($chat['userid'] == $userid) {
                                $chat['fromu'] = $chathistory_language[1];
                            }
                        }
                        if((strpos($chat['message'],'CC^CONTROL_')) !== false){
                            $controlparameters = str_replace('CC^CONTROL_', '', $chat['message']);
                            if((strpos($controlparameters,'deletemessage_')) <= -1){
                                $chatmsg = $chat['message'];
    			             }
                        }else{
                            $chatmsg = $chat['message'];
                        }
			if ($chat['id'] == $previd) {
                            $previd = 1000000;
			}
            $read = 0;
            if(empty($chat['read'])){
                $read = 1;
            } else {
                $read = $chat['read'];
            }
			$response[] = array('id' => $chat['id'], 'previd' => $previd, 'from' => $chat['fromu'], 'message' => $chatmsg, 'sent' =>  $chat['sent']*1000, 'old' => $read);
                        $previd = $chat['id'];
                }
                echo json_encode($response); exit;
        } else {
            echo '0'; exit;
        }
}

function logview() {
    $usertable = TABLE_PREFIX.DB_USERTABLE;
    $usertable_username = DB_USERTABLE_NAME;
    $usertable_userid = DB_USERTABLE_USERID;
    global $history;
    global $userid;
    global $chathistory_language;
    global $guestsMode;
    global $guestnamePrefix;
    global $limit;
    global $response;
    $requester = '';
    $limit = 13;
    $preuserid = 0;

	if (!empty($_REQUEST['range'])) {
        $range = $_REQUEST['range'];
    }

    if (!empty($_REQUEST['history'])) {
        $history = $_REQUEST['history'];
    }

    $range = intval($range);

    if (!empty($_REQUEST['lastidfrom'])) {
        $lastidfrom = $_REQUEST['lastidfrom'];
    }
    $guestpart= "";
    if (!empty($_REQUEST['chatroommode'])) {
        if ($guestsMode == '1') {
            $guestpart = sql_getQuery('chatroomviewlog_guestpart',array('chatroomid'=>$history, 'id'=>$range, 'guestnamePrefix'=>$guestnamePrefix));
        }
        $sql = sql_getQuery('chatroomviewlog',array('usertable_username'=>$usertable_username, 'usertable'=>$usertable, 'usertable_userid'=>$usertable_userid, 'chatroomid'=>$history, 'id'=>$range, 'limit'=>$limit, 'guestpart'=>$guestpart));
    } else {
        if ($guestsMode == '1') {
            $guestpart = sql_getQuery('viewlog_guestpart',array('usertable_username'=>$usertable_username, 'usertable'=>$usertable, 'usertable_userid'=>$usertable_userid, 'chatroomid'=>$history, 'userid'=>$userid, 'id'=>$range, 'guestnamePrefix'=>$guestnamePrefix));
        }
        $sql = sql_getQuery('viewlog',array('usertable_username'=>$usertable_username, 'usertable'=>$usertable, 'usertable_userid'=>$usertable_userid, 'chatroomid'=>$history, 'userid'=>$userid, 'id'=>$range, 'guestpart'=>$guestpart, 'limit'=>$limit));
    }
    $query = sql_query($sql, array(), 1);
    $previd = '';
    $lines = 0;
    $s = 0;
	if (sql_num_rows($query)>0) {
		while ($chat = sql_fetch_assoc($query)) {
			if (function_exists('processName')) {
				$chat['fromu'] = processName($chat['fromu']);
				if (empty($_REQUEST['chatroommode'])) {
					$chat['tou'] = processName($chat['tou']);
				}
			}
			if ($s == 0) {
                            $s = $chat['sent'];
			}
            if($chat['from'] == $history) {
                $requester = $chat['fromu'];
            } else {
                $requester = $chat['tou'];
            }
                        if (!empty($_REQUEST['chatroommode'])) {
                            $chathistory_language[2]=$chathistory_language[7];
                            $requester=$chat['chatroom'];
                            if ($chat['userid']==$userid) {
                                $chat['fromu'] = $chathistory_language[1];
                            }
                            if($chat['userid'] == $preuserid) {
                                $chat['fromu']= '';
                            }
                            $preuserid = $chat['userid'];
			} else {
                            if ($chat['from'] == $userid) {
                                    $chat['fromu'] = $chathistory_language[1];
                            }
			}
            if((strpos($chat['message'],'CC^CONTROL_')) !== false){
                $controlparameters = str_replace('CC^CONTROL_', '', $chat['message']);
                if((strpos($controlparameters,'deletemessage_')) <= -1){
                    $chatmes = $chat['message'];
                 }
            }elseif((strpos($chat['message'],'avchat_webaction=initiate')) !== false || (strpos($chat['message'],'avchat_webaction=acceptcall')) !== false){
                    $chatmes = $chathistory_language['video_call'];
            }else{
                $chatmes = $chat['message'];
            }
                        if (!empty($_REQUEST['chatroommode'])) {
                            if (!empty($_REQUEST['lastidfrom']) && $lastidfrom == $chat['userid']) {
                                $chat['fromu'] = '';
                            }
			} else	{
                            if (!empty($_REQUEST['lastidfrom']) && $lastidfrom == $chat['from']) {
                                $chat['fromu'] = '';
                            }
			}
			$lines++;
                        $previd = 1000000;
			if (!empty($chat['userid'])) {
                            $lastidfrom = $chat['userid'];
			} else if(!empty($chat['from'])) {
                            $lastidfrom = $chat['from'];
			}
            $read = 0;
            if(empty($chat['read'])){
                $read = 1;
            } else {
                $read = $chat['read'];
            }
		$response['_'.$chat['id']] = array('id' => $chat['id'], 'previd' => $previd, 'from' => $chat['fromu'], 'requester' => $requester, 'message' => $chatmes, 'sent' =>  $chat['sent']*1000, 'userid' => $lastidfrom, 'old' => $read);
	}
        echo json_encode($response);
        exit;
        } else {
            echo '0'; exit;

        }
}
$allowedActions = array('logs','logview');

if (!empty($_GET['action']) && in_array($_GET['action'],$allowedActions)) {
    call_user_func($_GET['action']);
}
?>
