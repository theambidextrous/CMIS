<?php

include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'config.php');

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$synergy_settings = setConfigValue('synergy_settings',array('enableType' => '0'));

/* SETTINGS START */

foreach ($synergy_settings as $key => $value) {
	$$key = $value;
}

/* SETTINGS END */
$barPadding = '20';
$iPhoneView = '0';
$showSettingsTab = '1';
$showOnlineTab = '1';
$showModules = '1';
$chatboxHeight = '420';
$chatboxWidth = '350';

?>
