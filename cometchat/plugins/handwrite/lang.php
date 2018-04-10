<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$addonfolder = str_replace(DIRECTORY_SEPARATOR.'lang.php','', __FILE__);
$addonarray = explode(DIRECTORY_SEPARATOR, $addonfolder);
$addonname = end($addonarray);
$addontype = rtrim(prev($addonarray),'s');

/* LANGUAGE */

${$addonname.'_language'}['title'] 				= setLanguageValue('title','Handwrite a message',$lang,$addontype,$addonname);
${$addonname.'_language'}['sent_message_other'] = setLanguageValue('sent_message_other','has sent you a handwritten message',$lang,$addontype,$addonname);
${$addonname.'_language'}['sent_message_self'] 	= setLanguageValue('sent_message_self','has successfully sent a handwritten message',$lang,$addontype,$addonname);
${$addonname.'_language'}['sent_message'] 		= setLanguageValue('sent_message','has shared a handwritten message',$lang,$addontype,$addonname);
${$addonname.'_language'}['close'] = setLanguageValue('close','Close',$lang,$addontype,$addonname);
${$addonname.'_language'}['download'] = setLanguageValue('download','Download',$lang,$addontype,$addonname);
${$addonname.'_language'}['handwrite_title'] = setLanguageValue('download','Handwrite',$lang,$addontype,$addonname);
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

${$addonname.'_key_mapping'} = array(
	'0'		=>	'title',
	'1'		=>	'sent_message_other',
	'2'		=>	'sent_message_self',
	'3'		=>	'sent_message'
	/**
	 * Please do not add indices here.
	 * Use the keys directly in the code.
	*/
);

${$addonname.'_language'} = mapLanguageKeys(${$addonname.'_language'},${$addonname.'_key_mapping'},$addontype,$addonname);
