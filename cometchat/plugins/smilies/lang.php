<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$addonfolder = str_replace(DIRECTORY_SEPARATOR.'lang.php','', __FILE__);
$addonarray = explode(DIRECTORY_SEPARATOR, $addonfolder);
$addonname = end($addonarray);
$addontype = rtrim(prev($addonarray),'s');

/* LANGUAGE */

${$addonname.'_language'}['title']			= setLanguageValue('title','Add a smiley',$lang,$addontype,$addonname);
${$addonname.'_language'}['select_smiley'] 	= setLanguageValue('select_smiley','Which smiley would you like?',$lang,$addontype,$addonname);
${$addonname.'_language'}['people'] 		= setLanguageValue('people','People',$lang,$addontype,$addonname);
${$addonname.'_language'}['nature'] 		= setLanguageValue('nature','Nature',$lang,$addontype,$addonname);
${$addonname.'_language'}['objects'] 		= setLanguageValue('objects','Objects',$lang,$addontype,$addonname);
${$addonname.'_language'}['places'] 		= setLanguageValue('places','Places',$lang,$addontype,$addonname);
${$addonname.'_language'}['symbols'] 		= setLanguageValue('symbols','Symbols',$lang,$addontype,$addonname);
${$addonname.'_language'}['more'] 			= setLanguageValue('more','More',$lang,$addontype,$addonname);

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

${$addonname.'_key_mapping'} = array(
	'0'		=>	'title',
	'1'		=>	'select_smiley',
	'2'		=>	'people',
	'3'		=>	'nature',
	'4'		=>	'objects',
	'5'		=>	'places',
	'6'		=>	'symbols',
	'7'		=>	'more'
	/**
	 * Please do not add indices here.
	 * Use the keys directly in the code.
	*/
);

${$addonname.'_language'} = mapLanguageKeys(${$addonname.'_language'},${$addonname.'_key_mapping'},$addontype,$addonname);
