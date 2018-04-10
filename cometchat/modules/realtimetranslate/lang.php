<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$addonfolder = str_replace(DIRECTORY_SEPARATOR.'lang.php','', __FILE__);
$addonarray = explode(DIRECTORY_SEPARATOR, $addonfolder);
$addonname = end($addonarray);
$addontype = rtrim(prev($addonarray),'s');

/* LANGUAGE */

${$addonname.'_language'}['title'] 				= setLanguageValue('title','Translate Conversations',$lang,$addontype,$addonname);
${$addonname.'_language'}['translating_convo'] 	= setLanguageValue('translating_convo','<b>Translating Conversations</b><br/>All future conversations will be translated...',$lang,$addontype,$addonname);
${$addonname.'_language'}['translating_to'] 	= setLanguageValue('translating_to','Translating to: ',$lang,$addontype,$addonname);
${$addonname.'_language'}['stop_translating'] 	= setLanguageValue('stop_translating','Stop translating',$lang,$addontype,$addonname);
${$addonname.'_language'}['real_time_translate'] 	= setLanguageValue('real_time_translate','Please configure this module using CometChat Administration Panel.',$lang,$addontype,$addonname);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

${$addonname.'_key_mapping'} = array(
	'100'	=>	'title',
	'0'		=>	'translating_convo',
	'1'		=>	'translating_to',
	'2'		=>	'stop_translating'
);

${$addonname.'_language'} = mapLanguageKeys(${$addonname.'_language'},${$addonname.'_key_mapping'},$addontype,$addonname);
