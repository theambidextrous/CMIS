<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$addonfolder = str_replace(DIRECTORY_SEPARATOR.'lang.php','', __FILE__);
$addonarray = explode(DIRECTORY_SEPARATOR, $addonfolder);
$addonname = end($addonarray);
$addontype = rtrim(prev($addonarray),'s');

/* LANGUAGE */

$facebook_language['title'] = setLanguageValue('title','Facebook Fan Page',$lang,$addontype,$addonname);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

${$addonname.'_key_mapping'} = array(
	'100'	=>	'title'
	/**
	 * Please do not add indices here.
	 * Use the keys directly in the code.
	*/
);

$facebook_language = mapLanguageKeys($facebook_language,${$addonname.'_key_mapping'},'modules','facebook');
