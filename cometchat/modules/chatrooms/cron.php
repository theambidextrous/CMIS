<?php

if (!defined('CC_CRON')) { echo "NO DICE"; exit; }

include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."config.php");

if ((!empty($_REQUEST['cron']['type']) && $_REQUEST['cron']['type'] == "all") || !empty($_REQUEST['cron']['modules'])) {
	chatrooms();
	chatroommessages();
	chatroomsusers();
} else {
	if(!empty($_REQUEST['cron']['inactiverooms'])){chatrooms();}
	if(!empty($_REQUEST['cron']['chatroommessages'])){chatroommessages();}
	if(!empty($_REQUEST['cron']['inactiveusers'])){chatroomsusers();}
}

function chatrooms() {
	$query = sql_query('archive_groups',array('lastactivity'=>getTimeStamp(), 'timeout'=>$GLOBALS['chatroomTimeout']));
	$query = sql_query('cron_groups',array('lastactivity'=>getTimeStamp(), 'timeout'=>$GLOBALS['chatroomTimeout']));
	if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }
}

function chatroommessages() {
	$query = sql_query('archive_groupmessages',array('sent'=>getTimeStamp()));
	$query = sql_query('cron_groupmessages',array('sent'=>getTimeStamp()));
	if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }
}

function chatroomsusers() {
	$query = sql_query('archive_groupusers',array('lastactivity'=>getTimeStamp()));
	$query = sql_query('cron_groupusers',array('lastactivity'=>getTimeStamp()));
	if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }
}
