<?php

include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'config.php');

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/* DOCKED LAYOUT SETTINGS */

$iPhoneView = '0';				// iPhone style messages in chatboxes?
$barPadding = setConfigValue('barPadding', '20');
$showSettingsTab = setConfigValue('showSettingsTab', '1');
$showOnlineTab = setConfigValue('showOnlineTab', '1');
$showModules = setConfigValue('showModules', '1');
$chatboxHeight = setConfigValue('chatboxHeight', '350');
$chatboxWidth = setConfigValue('chatboxWidth', '230');

if($chatboxHeight < '350'){
	$chatboxHeight = '350';
}

if($chatboxWidth < '230'){
	$chatboxWidth = '230';
}

?>
