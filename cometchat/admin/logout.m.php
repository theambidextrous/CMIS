<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

if (!defined('CCADMIN')) { echo "NO DICE"; exit; }


function index() {
	session_destroy();
	header("Location: ".ADMIN_URL."\r\n");
	exit;
}
