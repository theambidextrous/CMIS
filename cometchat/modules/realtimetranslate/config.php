<?php

include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'config.php');

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

global $bingClientID;
global $bingClientSecret;
global $googleKey;
global $client;

/* SETTINGS START */

$bingClientID = setConfigValue('bingClientID','');
$bingClientSecret = setConfigValue('bingClientSecret','');
$useGoogle = setConfigValue('useGoogle','1');

if (empty($client)) {
	$googleKey = setConfigValue('googleKey','');
}
/* SETTINGS END */

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
