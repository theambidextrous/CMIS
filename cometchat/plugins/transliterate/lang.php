<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$addonfolder = str_replace(DIRECTORY_SEPARATOR.'lang.php','', __FILE__);
$addonarray = explode(DIRECTORY_SEPARATOR, $addonfolder);
$addonname = end($addonarray);
$addontype = rtrim(prev($addonarray),'s');

/* LANGUAGE */

${$addonname.'_language'}['title'] 				= setLanguageValue('title','Write in your language',$lang,$addontype,$addonname);
${$addonname.'_language'}['select_language'] 	= setLanguageValue('select_language','Which language would you like to transliterate to?',$lang,$addontype,$addonname);
${$addonname.'_language'}['type_and_convert'] 	= setLanguageValue('type_and_convert','Type in English and use space to transliterate to your language',$lang,$addontype,$addonname);
${$addonname.'_language'}['send'] 				= setLanguageValue('send','Send',$lang,$addontype,$addonname);
${$addonname.'_language'}['change_language'] 	= setLanguageValue('change_language','(change language)',$lang,$addontype,$addonname);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

${$addonname.'_key_mapping'} = array(
	'0'		=>	'title',
	'1'		=>	'select_language',
	'2'		=>	'type_and_convert',
	'3'		=>	'send',
	'4'		=>	'change_language'
	/**
	 * Please do not add indices here.
	 * Use the keys directly in the code.
	*/
);


${$addonname.'_language'} = mapLanguageKeys(${$addonname.'_language'},${$addonname.'_key_mapping'},$addontype,$addonname);
