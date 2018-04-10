<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

$addonfolder = str_replace(DIRECTORY_SEPARATOR.'lang.php','', __FILE__);
$addonarray = explode(DIRECTORY_SEPARATOR, $addonfolder);
$addonname = end($addonarray);
$addontype = rtrim(prev($addonarray),'s');
/* LANGUAGE */
${$addonname.'_language'}['bots'] = setLanguageValue('bots','Bots',$lang,$addontype,$addonname);
${$addonname.'_language'}['popup_already_open'] = setLanguageValue('popup_already_open','The popup is already open.',$lang,$addontype,$addonname);
${$addonname.'_language'}['no_bots'] = setLanguageValue('no_bots','No bots are available at the moment.',$lang,$addontype,$addonname);
${$addonname.'_language'}['default_desc'] = setLanguageValue('default_desc','Hi, I am a helper bot.',$lang,$addontype,$addonname);
