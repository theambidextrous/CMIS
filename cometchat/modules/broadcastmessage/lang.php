<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$addonfolder = str_replace(DIRECTORY_SEPARATOR.'lang.php','', __FILE__);
$addonarray = explode(DIRECTORY_SEPARATOR, $addonfolder);
$addonname = end($addonarray);
$addontype = rtrim(prev($addonarray),'s');

/* LANGUAGE */

${$addonname.'_language'}['title'] 						= 	setLanguageValue('title','Broadcast Message',$lang,$addontype,$addonname);
${$addonname.'_language'}['reload_userlist'] 			=   setLanguageValue('reload_userlist','Refresh Users',$lang,$addontype,$addonname);
${$addonname.'_language'}['no_users_available'] 		=   setLanguageValue('no_users_available','Sorry, there are no users available at the moment.',$lang,$addontype,$addonname);
${$addonname.'_language'}['select_one_user'] 			=   setLanguageValue('select_one_user','Please select atleast one user before sending.',$lang,$addontype,$addonname);
${$addonname.'_language'}['find_a_user'] 				=   setLanguageValue('find_a_user','Find a user',$lang,$addontype,$addonname);
${$addonname.'_language'}['select_all'] 				=   setLanguageValue('select_all','Select All',$lang,$addontype,$addonname);
${$addonname.'_language'}['deselect_all'] 				=   setLanguageValue('deselect_all','Deselect All',$lang,$addontype,$addonname);
${$addonname.'_language'}['login_to_broadcast'] 		=   setLanguageValue('login_to_broadcast','Please login to broadcast messages.',$lang,$addontype,$addonname);
${$addonname.'_language'}['message_sent'] 				=   setLanguageValue('message_sent','Message sent',$lang,$addontype,$addonname);
${$addonname.'_language'}['send_msg_confirmation'] 		=   setLanguageValue('send_msg_confirmation','Are you sure you want to send this message?',$lang,$addontype,$addonname);
${$addonname.'_language'}['userlist_reloaded'] 			=  	setLanguageValue('userlist_reloaded','Userlist reloaded',$lang,$addontype,$addonname);
${$addonname.'_language'}['type_message'] 				=  	setLanguageValue('type_message','Type your message',$lang,$addontype,$addonname);
${$addonname.'_language'}['no_results_found'] 			=  	setLanguageValue('no_results_found','No results found',$lang,$addontype,$addonname);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

${$addonname.'_key_mapping'} = array(
	'100'	=>	'title',
	'1'		=>	'reload_userlist',
	'2'		=>	'no_users_available',
	'3'		=>	'select_one_user',
	'4'		=>	'find_a_user',
	'5'		=>	'select_all',
	'6'		=>	'deselect_all',
	'7'		=>	'login_to_broadcast',
	'8'		=>	'message_sent',
	'9'		=>	'send_msg_confirmation',
	'10'	=>	'userlist_reloaded',
	'11'	=>	'type_message',
	'12'	=>	'no_results_found'
	/**
	 * Please do not add indices here.
	 * Use the keys directly in the code.
	*/
);


${$addonname.'_language'} = mapLanguageKeys(${$addonname.'_language'},${$addonname.'_key_mapping'},$addontype,$addonname);
