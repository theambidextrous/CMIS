<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$addonfolder = str_replace(DIRECTORY_SEPARATOR.'lang.php','', __FILE__);
$addonarray = explode(DIRECTORY_SEPARATOR, $addonfolder);
$addonname = end($addonarray);
$addontype = rtrim(prev($addonarray),'s');

/* LANGUAGE */

${$addonname.'_language'}['username'] 			= setLanguageValue('username','Username',$lang,$addontype,$addonname);
${$addonname.'_language'}['password'] 			= setLanguageValue('password','Password',$lang,$addontype,$addonname);
${$addonname.'_language'}['remember_me']			= setLanguageValue('remember_me','Remember Me',$lang,$addontype,$addonname);
${$addonname.'_language'}['login'] 				= setLanguageValue('login','Login',$lang,$addontype,$addonname);
${$addonname.'_language'}['forgot_password']		= setLanguageValue('forgot_password','Forgot Password',$lang,$addontype,$addonname);
${$addonname.'_language'}['sign_up'] 			= setLanguageValue('sign_up','Sign Up',$lang,$addontype,$addonname);
${$addonname.'_language'}['guest_login'] 			= setLanguageValue('guest_login','Guest Login',$lang,$addontype,$addonname);
${$addonname.'_language'}['guest_name'] 			= setLanguageValue('guest_name','Guest Name',$lang,$addontype,$addonname);
${$addonname.'_language'}['username_pass_blank_err'] 			= setLanguageValue('username_pass_blank_err','Username or password cannot be blank',$lang,$addontype,$addonname);
${$addonname.'_language'}['enter_password'] 			= setLanguageValue('enter_password','Please enter password',$lang,$addontype,$addonname);
${$addonname.'_language'}['check_username'] 			= setLanguageValue('check_username','Check your username',$lang,$addontype,$addonname);
${$addonname.'_language'}['username_blank_err'] 			= setLanguageValue('username_blank_err','Username cannot be blank',$lang,$addontype,$addonname);
${$addonname.'_language'}['guestname_blank_err'] 			= setLanguageValue('guestname_blank_err','Guest name cannot be blank',$lang,$addontype,$addonname);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

${$addonname.'_key_mapping'} = array(
	'0'		=>	'username',
	'1'		=>	'password',
	'2'		=>	'remember_me',
	'3'		=>	'login',
	'4'		=>	'forgot_password',
	'5'		=>	'sign_up',
	'6'		=> 	'guest_login',
	'7'		=>	'guest_name',
	'8'		=>	'username_pass_blank_err',
	'9'		=>	'enter_password',
	'10'	=>	'check_username',
	'11'	=>	'username_blank_err',
	'12'	=>	'guestname_blank_err'
	/**
	 * Please do not add indices here.
	 * Use the keys directly in the code.
	*/
);

${$addonname.'_language'} = mapLanguageKeys(${$addonname.'_language'},${$addonname.'_key_mapping'},$addontype,$addonname);
