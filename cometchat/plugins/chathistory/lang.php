<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$addonfolder = str_replace(DIRECTORY_SEPARATOR.'lang.php','', __FILE__);
$addonarray = explode(DIRECTORY_SEPARATOR, $addonfolder);
$addonname = end($addonarray);
$addontype = rtrim(prev($addonarray),'s');

/* LANGUAGE */

${$addonname.'_language'}['title'] = setLanguageValue('title','View chat history',$lang,$addontype,$addonname);
${$addonname.'_language'}['me'] = setLanguageValue('me','Me',$lang,$addontype,$addonname);
${$addonname.'_language'}['chat_convo_with'] = setLanguageValue('chat_convo_with','Chat Conversation with',$lang,$addontype,$addonname);
${$addonname.'_language'}['lines'] = setLanguageValue('lines','lines',$lang,$addontype,$addonname);
${$addonname.'_language'}['at'] = setLanguageValue('at','at',$lang,$addontype,$addonname);
${$addonname.'_language'}['back'] = setLanguageValue('back','Back',$lang,$addontype,$addonname);
${$addonname.'_language'}['chat_history'] = setLanguageValue('chat_history','Chat History',$lang,$addontype,$addonname);
${$addonname.'_language'}['cr_chat_convo'] = setLanguageValue('cr_chat_convo','Chat Conversation in chatroom',$lang,$addontype,$addonname);
${$addonname.'_language'}['view_entire_convo'] = setLanguageValue('view_entire_convo','Click here to view entire conversation',$lang,$addontype,$addonname);
${$addonname.'_language'}['no_previous_convo'] = setLanguageValue('no_previous_convo','No previous conversations to view',$lang,$addontype,$addonname);
${$addonname.'_language'}['no_more_convo'] = setLanguageValue('no_more_convo','See more conversations',$lang,$addontype,$addonname);
${$addonname.'_language'}['video_call'] = setLanguageValue('video_call','Video call',$lang,$addontype,$addonname);


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

${$addonname.'_key_mapping'} = array(
	'0'		=>	'title',
	'1'		=>	'me',
	'2'		=>	'chat_convo_with',
	'3'		=>	'lines',
	'4'		=>	'at',
	'5'		=>	'back',
	'6'		=>	'chat_history',
	'7'		=>	'cr_chat_convo',
	'8'		=>	'view_entire_convo',
	'9'		=>	'no_previous_convo',
	'10'	=>	'no_more_convo'
	/**
	 * Please do not add indices here.
	 * Use the keys directly in the code.
	*/
);

${$addonname.'_language'} = mapLanguageKeys(${$addonname.'_language'},${$addonname.'_key_mapping'},$addontype,$addonname);
