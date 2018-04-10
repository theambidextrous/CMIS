<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

define('CC_CRON', '1');

if (!empty($_REQUEST['url'])) {
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."modules.php");
}

$auth = md5(md5(ADMIN_USER).md5(ADMIN_PASS));

if (!empty($_REQUEST['auth']) && !empty($auth) && $_REQUEST['auth'] == $auth ) {
	if ((!empty($_REQUEST['cron']['type']) && $_REQUEST['cron']['type'] == "all") || !empty($_REQUEST['cron']['core'])) {
		//generateReport();
		clearMessageEntries();
		clearGuestEntries();
	} else {
		if (!empty($_REQUEST['cron']['messages'])) {
			clearMessageEntries();
		}
		if (!empty($_REQUEST['cron']['guest'])) {
			clearGuestEntries();
		}
	}
	clearModulesData();
	clearPluginsData();
} else {
	echo 'Sorry you don`t have permissions to execute cron.';
}

function clearModulesData() {
	global $trayicon;
	global $chatroomTimeout;
	foreach ($trayicon as $t) {
		if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$t[0].DIRECTORY_SEPARATOR.'cron.php')) {
			include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$t[0].DIRECTORY_SEPARATOR.'cron.php');
		}
	}
}

function clearPluginsData() {
	global $plugins;
	foreach ($plugins as $p) {
		if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$p.DIRECTORY_SEPARATOR.'cron.php') && (!empty($_REQUEST['cron'][$p]) || (!empty($_REQUEST['cron']['plugins'])) || (!empty($_REQUEST['cron']['type']) && $_REQUEST['cron']['type'] == "all"))) {
			include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$p.DIRECTORY_SEPARATOR.'cron.php');
		}
	}
}

function clearMessageEntries() {
	$query = sql_query('archive_privatemessage',array('time'=>getTimeStamp()));
	$query = sql_query('clearPrivateMessage',array('time'=>getTimeStamp()));
	if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }
}

function clearGuestEntries() {
	$sql 	= sql_query('archive_guestentries',array('time'=>getTimeStamp(),'firstguestid'=>$GLOBALS['firstguestID']));
	$sql 	= sql_query('archive_gueststatus',array('time'=>getTimeStamp(),'firstguestid'=>$GLOBALS['firstguestID']));
	$sql = sql_getQuery('clearGuestEntries',array('time'=>getTimeStamp()));
	sql_multi_query($GLOBALS['dbh'],$sql);
	if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }
}
