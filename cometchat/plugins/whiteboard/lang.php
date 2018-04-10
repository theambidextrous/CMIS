<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$addonfolder = rtrim(__FILE__,DIRECTORY_SEPARATOR.'lang.php');
$addonarray = explode(DIRECTORY_SEPARATOR, $addonfolder);
$addonname = end($addonarray);
$addontype = rtrim(prev($addonarray),'s');

/* LANGUAGE */

${$addonname.'_language'}['title'] 					= setLanguageValue('title','Share a Whiteboard',$lang,$addontype,$addonname);
${$addonname.'_language'}['wait_message'] 			= setLanguageValue('wait_message','Please wait atleast 10 seconds before trying to share again.',$lang,$addontype,$addonname);
${$addonname.'_language'}['shared_whiteboard_other']= setLanguageValue('shared_whiteboard_other','has shared his/her whiteboard with you.',$lang,$addontype,$addonname);
${$addonname.'_language'}['click_to_view_1'] 		= setLanguageValue('click_to_view_1','Click here to view',$lang,$addontype,$addonname);
${$addonname.'_language'}['ignore_message'] 		= setLanguageValue('ignore_message','or simply ignore this message.',$lang,$addontype,$addonname);
${$addonname.'_language'}['shared_whiteboard_self'] = setLanguageValue('shared_whiteboard_self','has successfully shared his/her whiteboard.',$lang,$addontype,$addonname);
${$addonname.'_language'}['viewing_whiteboard'] 	= setLanguageValue('viewing_whiteboard','is now viewing your whiteboard.',$lang,$addontype,$addonname);
${$addonname.'_language'}['shared_whiteboard'] 		= setLanguageValue('shared_whiteboard','has shared a whiteboard.',$lang,$addontype,$addonname);
${$addonname.'_language'}['click_to_view_2'] 		= setLanguageValue('click_to_view_2','Click here to view',$lang,$addontype,$addonname);
${$addonname.'_language'}['whiteboard'] 			= setLanguageValue('whiteboard','Whiteboard',$lang,$addontype,$addonname);
${$addonname.'_language'}['boundary_exceeded'] 		= setLanguageValue('boundary_exceeded','Text exceeds boundary limit',$lang,$addontype,$addonname);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

${$addonname.'_key_mapping'} = array(
	'0'		=>	'title',
	'1'		=>	'wait_message',
	'2'		=>	'shared_whiteboard_other',
	'3'		=>	'click_to_view_1',
	'4'		=>	'ignore_message',
	'5'		=>	'shared_whiteboard_self',
	'6'		=>	'viewing_whiteboard',
	'7'		=>	'shared_whiteboard',
	'8'		=>	'click_to_view_2',
	'9'		=>	'whiteboard',
	'10'	=>	'boundary_exceeded'
	/**
	 * Please do not add indices here.
	 * Use the keys directly in the code.
	*/
);


${$addonname.'_language'} = mapLanguageKeys(${$addonname.'_language'},${$addonname.'_key_mapping'},$addontype,$addonname);
