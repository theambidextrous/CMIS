<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$addonfolder = str_replace(DIRECTORY_SEPARATOR.'lang.php','', __FILE__);
$addonarray = explode(DIRECTORY_SEPARATOR, $addonfolder);
$addonname = end($addonarray);
$addontype = rtrim(prev($addonarray),'s');

/* LANGUAGE */

${$addonname.'_language'}['title']				= setLanguageValue('title','Save Conversation to Desktop',$lang,$addontype,$addonname);
${$addonname.'_language'}['log_empty'] 			= setLanguageValue('log_empty','Sorry, your conversation log is empty.',$lang,$addontype,$addonname);
${$addonname.'_language'}['sticker_received'] 	= setLanguageValue('sticker_received','has sent you a sticker.',$lang,$addontype,$addonname);
${$addonname.'_language'}['sticker_sent']		= setLanguageValue('sticker_sent','has sent a sticker.',$lang,$addontype,$addonname);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

${$addonname.'_key_mapping'} = array(
	'0'		=>	'title',
	'1'		=>	'log_empty',
	'2'		=>	'sticker_received',
	'3'		=>	'sticker_sent'
	/**
	 * Please do not add indices here.
	 * Use the keys directly in the code.
	*/
);

${$addonname.'_language'} = mapLanguageKeys(${$addonname.'_language'},${$addonname.'_key_mapping'},$addontype,$addonname);
