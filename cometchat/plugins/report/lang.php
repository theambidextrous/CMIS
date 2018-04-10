<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$addonfolder = str_replace(DIRECTORY_SEPARATOR.'lang.php','', __FILE__);
$addonarray = explode(DIRECTORY_SEPARATOR, $addonfolder);
$addonname = end($addonarray);
$addontype = rtrim(prev($addonarray),'s');

/* LANGUAGE */

${$addonname.'_language'}['title'] = setLanguageValue('title','Report Conversation',$lang,$addontype,$addonname);
${$addonname.'_language'}['reason'] = setLanguageValue('reason','Reason as to why you are reporting this user?',$lang,$addontype,$addonname);
${$addonname.'_language'}['report_user'] = setLanguageValue('report_user','Report User',$lang,$addontype,$addonname);
${$addonname.'_language'}['report_successful'] = setLanguageValue('report_successful','Thank you for reporting this user',$lang,$addontype,$addonname);
${$addonname.'_language'}['closing_window'] = setLanguageValue('closing_window','Closing window shortly',$lang,$addontype,$addonname);
${$addonname.'_language'}['empty_conversation'] = setLanguageValue('empty_conversation','Sorry, your conversation with this user is empty.',$lang,$addontype,$addonname);
${$addonname.'_language'}['fill_reason'] = setLanguageValue('fill_reason','Reason must be filled out.',$lang,$addontype,$addonname);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

${$addonname.'_key_mapping'} = array(
	'0'		=>	'title',
	'1'		=>	'reason',
	'2'		=>	'report_user',
	'3'		=>	'report_successful',
	'4'		=>	'closing_window',
	'5'		=>	'empty_conversation',
	'6'		=>	'fill_reason'
	/**
	 * Please do not add indices here.
	 * Use the keys directly in the code.
	*/
);

${$addonname.'_language'} = mapLanguageKeys(${$addonname.'_language'},${$addonname.'_key_mapping'},$addontype,$addonname);
