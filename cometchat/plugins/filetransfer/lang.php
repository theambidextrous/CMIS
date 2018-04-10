<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$addonfolder = str_replace(DIRECTORY_SEPARATOR.'lang.php','', __FILE__);
$addonarray = explode(DIRECTORY_SEPARATOR, $addonfolder);
$addonname = end($addonarray);
$addontype = rtrim(prev($addonarray),'s');

/* LANGUAGE */

${$addonname.'_language'}['title'] = setLanguageValue('title','Send a file',$lang,$addontype,$addonname);
${$addonname.'_language'}['file_type'] = setLanguageValue('file_type','Which file would you like to send?',$lang,$addontype,$addonname);
${$addonname.'_language'}['select_a_file'] = setLanguageValue('select_a_file','Please select a file by using the button below.',$lang,$addontype,$addonname);
${$addonname.'_language'}['copyright_warning'] = setLanguageValue('copyright_warning','<b>WARNING:</b> Do not send copyrighted material for which you don\'t own the rights or have permission from the owner.',$lang,$addontype,$addonname);
${$addonname.'_language'}['select_file'] = setLanguageValue('select_file','Select file',$lang,$addontype,$addonname);
${$addonname.'_language'}['sent_a_file'] = setLanguageValue('sent_a_file','has sent a file',$lang,$addontype,$addonname);
${$addonname.'_language'}['download_file'] = setLanguageValue('download_file','Click here to download the file',$lang,$addontype,$addonname);
${$addonname.'_language'}['cr_chat_convo'] = setLanguageValue('cr_chat_convo','has successfully sent a file',$lang,$addontype,$addonname);
${$addonname.'_language'}['view_entire_convo'] = setLanguageValue('view_entire_convo','File sent successfully. Closing Window.',$lang,$addontype,$addonname);
${$addonname.'_language'}['shared_a_file'] = setLanguageValue('shared_a_file','has shared a file',$lang,$addontype,$addonname);
${$addonname.'_language'}['err_no_file_found'] = setLanguageValue('err_no_file_found','Sorry, we are unable to find the file.',$lang,$addontype,$addonname);
${$addonname.'_language'}['save'] = setLanguageValue('save','Save',$lang,$addontype,$addonname);
${$addonname.'_language'}['close'] = setLanguageValue('close','Close',$lang,$addontype,$addonname);
${$addonname.'_language'}['download'] = setLanguageValue('download','Download',$lang,$addontype,$addonname);
${$addonname.'_language'}['uploaded'] = setLanguageValue('uploaded','File uploaded successfully',$lang,$addontype,$addonname);
${$addonname.'_language'}['upload_stopped'] = setLanguageValue('upload_stopped','File upload stopped',$lang,$addontype,$addonname);
${$addonname.'_language'}['dropfiles'] = setLanguageValue('dropfiles','Drop your files to upload',$lang,$addontype,$addonname);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

${$addonname.'_key_mapping'} = array(
	'0'		=>	'title',
	'1'		=>	'file_type',
	'2'		=>	'select_a_file',
	'3'		=>	'copyright_warning',
	'4'		=>	'select_file',
	'5'		=>	'sent_a_file',
	'6'		=>	'download_file',
	'7'		=>	'cr_chat_convo',
	'8'		=>	'view_entire_convo',
	'9'		=>	'shared_a_file',
	'10'	=>	'err_no_file_found',
	'11'	=>	'save',
	'12'	=>	'close',
	'13'	=>	'download'
	/**
	 * Please do not add indices here.
	 * Use the keys directly in the code.
	*/
);

${$addonname.'_language'} = mapLanguageKeys(${$addonname.'_language'},${$addonname.'_key_mapping'},$addontype,$addonname);
