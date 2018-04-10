<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

global $access_token;

function translate_gettoken () {

	global $bingClientID;
	global $bingClientSecret;
	global $access_token;

	if (!empty($access_token)) {
		return $access_token;
	}

	if (empty($bingClientID) || empty($bingClientSecret)) {
		return;
	}

	if(!function_exists('curl_version')){
		return;
	}

	$url = 'https://datamarket.accesscontrol.windows.net/v2/OAuth2-13';

	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_POST,1);
	curl_setopt($ch,CURLOPT_POSTFIELDS,'client_id='.urlencode($bingClientID).'&'.'client_secret='.urlencode($bingClientSecret).'&'.'scope='.urlencode('http://api.microsofttranslator.com').'&'.'grant_type='.'client_credentials');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$result = curl_exec($ch);
	curl_close($ch);
	$data = json_decode($result);
	$access_token = $data->access_token;

	return $access_token;
}

function removeBOM($str = "") {
	if (substr($str, 0, 3) == pack("CCC",0xef,0xbb,0xbf)) {
		$str=substr($str, 3);
	}
	return $str;
}


function translate_text ($text, $from = 'en', $to = 'en') {

	global $bingClientID;
	global $bingClientSecret;

	try {
		$token = translate_gettoken();

		if (empty($token)) {
			return false;
		}

		if(!function_exists('curl_version')){
			return false;
		}

		$url = 'http://api.microsofttranslator.com/v2/Ajax.svc/Detect?text='.urlencode($text).'&appId=';

		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Authorization: Bearer ' . $token));
		$result = curl_exec($ch);
		curl_close($ch);

		$language = removeBOM(str_replace('"', '', $result));

		if ($language == $to || empty($to) || empty($language)){
			return false;
		}

		$url = 'http://api.microsofttranslator.com/v2/Ajax.svc/GetTranslations?text='.urlencode($text).'&appId=&from='.$language.'&to='.$to.'&maxTranslations=1';

		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Authorization: Bearer ' . $token));
		$result = curl_exec($ch);
		curl_close($ch);

		$result = json_decode(removeBOM($result));

		return $result->Translations[0]->TranslatedText;

	} catch (Exception $e) {
		return false;
	}

}


function translate_languages () {

	global $bingClientID;
	global $bingClientSecret;

	try {
		$token = translate_gettoken();

		if (empty($token)) {
			return false;
		}

		if(!function_exists('curl_version')){
			return false;
		}

		$url = 'http://api.microsofttranslator.com/v2/Ajax.svc/GetLanguagesForTranslate?appId=';

		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Authorization: Bearer ' . $token));
		$result = curl_exec($ch);
		curl_close($ch);

		$languages = json_decode(removeBOM($result));

		$languagestring = '["'.implode('","',$languages).'"]';

		$url = 'http://api.microsofttranslator.com/v2/Ajax.svc/GetLanguageNames?locale=en&appId=&languageCodes='.urlencode($languagestring);

		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Authorization: Bearer ' . $token));
		$result = curl_exec($ch);
		curl_close($ch);

		$result = json_decode(removeBOM($result));

		$return = array();

		foreach ($result as $id => $value) {
			$return[$languages[$id]] = $value;
		}

		return $return;

	} catch (Exception $e) {
		return false;
	}

}
