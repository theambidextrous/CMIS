<?php

include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."cometchat_init.php");
include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR."announcements".DIRECTORY_SEPARATOR."config.php");
if(!empty($_REQUEST['userid'])){
	$userid = $_REQUEST['userid'];
}
$channels = array();
$channels['user'] = array('userid'=>$userid,'channel'=>'C_'.md5($channelprefix."USER_".$userid.BASE_URL).getPlatformSuffix($pushplatformsuffix));
$channels['announcements'] = array('channels'=>$announcementpushchannel.getPlatformSuffix($pushplatformsuffix));

$groups = array();

$query = sql_query('getJoinedGroups',array('userid'=>$userid));
while ($group = sql_fetch_assoc($query)) {
	$groups['_'.$group['id']] = array('groupid'=>$group['id'],'C_'.md5($channelprefix."CHATROOM_".$group['id'].BASE_URL).getPlatformSuffix($pushplatformsuffix));
}
if(!empty($groups)){
	$channels['groups'] = $groups;
}
sendCCResponse(json_encode($channels));
