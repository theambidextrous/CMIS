<?php

include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'config.php');

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/* SETTINGS START */

$login_background = setConfigValue('login_background','#FFFFFF');
$login_placeholder = setConfigValue('login_placeholder','#777788');
$login_button_pressed = setConfigValue('login_button_pressed','#002832');
$login_button_text = setConfigValue('login_button_text','#FFFFFF');
$login_foreground_text = setConfigValue('login_foreground_text','#000000');

/* SETTINGS END */

$forgot_url = setConfigValue('FORGOT_URL','');
$signUp_url = setConfigValue('SIGNUP_URL','');
$branded = setConfigValue('BRANDED',1);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
