<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$addonfolder = str_replace(DIRECTORY_SEPARATOR.'lang.php','', __FILE__);
$addonarray = explode(DIRECTORY_SEPARATOR, $addonfolder);
$addonname = end($addonarray);
$addontype = rtrim(prev($addonarray),'s');

/* LANGUAGE */

${$addonname.'_language'}['title'] = setLanguageValue('title','Block User',$lang,$addontype,$addonname);
${$addonname.'_language'}['confirm_block_user'] = setLanguageValue('confirm_block_user','Are you sure you want to block this user?',$lang,$addontype,$addonname);
${$addonname.'_language'}['user_blocked'] = setLanguageValue('user_blocked','User has been blocked successfully.',$lang,$addontype,$addonname);
${$addonname.'_language'}['accept_request'] = setLanguageValue('accept_request','Manage blocked users',$lang,$addontype,$addonname);
${$addonname.'_language'}['unblock_user'] = setLanguageValue('unblock_user','Blocked Users',$lang,$addontype,$addonname);
${$addonname.'_language'}['no_blocked_users'] = setLanguageValue('no_blocked_users','No blocked users',$lang,$addontype,$addonname);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

${$addonname.'_key_mapping'} = array(
	'0'		=>	'title',
	'1'		=>	'confirm_block_user',
	'2'		=>	'user_blocked',
	'3'		=>	'accept_request',
	'4'		=>	'unblock_user',
	'5'		=>	'no_blocked_users'
	/**
	 * Please do not add indices here.
	 * Use the keys directly in the code.
	*/
);

${$addonname.'_language'} = mapLanguageKeys(${$addonname.'_language'},${$addonname.'_key_mapping'},$addontype,$addonname);
