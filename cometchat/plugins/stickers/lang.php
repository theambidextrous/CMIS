<?php

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/* LANGUAGE */

$addonfolder = str_replace(DIRECTORY_SEPARATOR.'lang.php','', __FILE__);
$addonarray = explode(DIRECTORY_SEPARATOR, $addonfolder);
$addonname = end($addonarray);
$addontype = rtrim(prev($addonarray),'s');

${$addonname.'_language'}['title'] 				= setLanguageValue('title','Send a sticker',$lang,$addontype,$addonname);
${$addonname.'_language'}['select_sticker'] 	= setLanguageValue('select_sticker','Which sticker would you like?',$lang,$addontype,$addonname);
${$addonname.'_language'}['sticker_received'] 	= setLanguageValue('sticker_received','has sent you a sticker.',$lang,$addontype,$addonname);

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

${$addonname.'_key_mapping'} = array(
	'0'		=>	'title',
	'1'		=>	'select_sticker',
	'2'		=>	'sticker_received'
	/**
	 * Please do not add indices here.
	 * Use the keys directly in the code.
	*/
);

${$addonname.'_language'} = mapLanguageKeys(${$addonname.'_language'},${$addonname.'_key_mapping'},$addontype,$addonname);
