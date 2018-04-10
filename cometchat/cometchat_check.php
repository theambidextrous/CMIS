<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."cometchat_init.php");

$return = 0;

if ($userid > 0) {
	$return = 1;
}

if ((in_array($userid,$bannedUserIDs)) || (in_array($_SERVER['REMOTE_ADDR'],$bannedUserIPs))) {
	$return = 0;
}

if (function_exists('hooks_displaybar')) {
	$return = hooks_displaybar($return);
}

if (!empty($_GET['callback'])) {
	echo $_GET['callback'].'('.json_encode($return).')';
} else {
	echo json_encode($return);
}
exit;
