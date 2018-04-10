<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

function removeBOM($str = "") {
	if (substr($str, 0, 3) == pack("CCC",0xef,0xbb,0xbf)) {
		$str=substr($str, 3);
	}
	return $str;
}

function translate_languages(){
	return array(
	'af' => 'Afrikaans',
	'sq' => 'Albanian',
	'ar' => 'Arabic',
	'hy' => 'Armenian',
	'az' => 'Azerbaijani',
	'eu' => 'Basque',
	'be' => 'Belarusian',
	'bn' => 'Bengali',
	'bs' => 'Bosnian',
	'bg' => 'Bulgarian',
	'ca' => 'Catalan',
	'ceb' => 'Cebuano',
	'ny' => 'Chichewa',
	'zh-CN' => 'Chinese (Simpl)',
	'zh-TW' => 'Chinese (Trad)',
	'hr' => 'Croatian',
	'cs' => 'Czech',
	'da' => 'Danish',
	'nl' => 'Dutch',
	'en' => 'English',
	'eo' => 'Esperanto',
	'et' => 'Estonian',
	'tl' => 'Filipino',
	'fi' => 'Finnish',
	'fr' => 'French',
	'gl' => 'Galician',
	'ka' => 'Georgian',
	'de' => 'German',
	'el' => 'Greek',
	'gu' => 'Gujarati',
	'ht' => 'Haitian Creole',
	'ha' => 'Hausa',
	'iw' => 'Hebrew',
	'hi' => 'Hindi',
	'hmn' => 'Hmong',
	'hu' => 'Hungarian',
	'is' => 'Icelandic',
	'ig' => 'Igbo',
	'id' => 'Indonesian',
	'ga' => 'Irish',
	'it' => 'Italian',
	'ja' => 'Japanese',
	'jw' => 'Javanese',
	'kn' => 'Kannada',
	'kk' => 'Kazakh',
	'km' => 'Khmer',
	'ko' => 'Korean',
	'lo' => 'Lao',
	'la' => 'Latin',
	'lv' => 'Latvian',
	'lt' => 'Lithuanian',
	'mk' => 'Macedonian',
	'mg' => 'Malagasy',
	'ms' => 'Malay',
	'ml' => 'Malayalam',
	'mt' => 'Maltese',
	'mi' => 'Maori',
	'mr' => 'Marathi',
	'mn' => 'Mongolian',
	'my' => 'Myanmar',
	'ne' => 'Nepali',
	'no' => 'Norwegian',
	'fa' => 'Persian',
	'pl' => 'Polish',
	'pt' => 'Portuguese',
	'pa' => 'Punjabi',
	'ro' => 'Romanian',
	'ru' => 'Russian',
	'sr' => 'Serbian',
	'st' => 'Sesotho',
	'si' => 'Sinhala',
	'sk' => 'Slovak',
	'sl' => 'Slovenian',
	'so' => 'Somali',
	'es' => 'Spanish',
	'su' => 'Sudanese',
	'sw' => 'Swahili',
	'sv' => 'Swedish',
	'tg' => 'Tajik',
	'ta' => 'Tamil',
	'te' => 'Telugu',
	'th' => 'Thai',
	'tr' => 'Turkish',
	'uk' => 'Ukrainian',
	'ur' => 'Urdu',
	'uz' => 'Uzbek',
	'vi' => 'Vietnamese',
	'cy' => 'Welsh',
	'yi' => 'Yiddish',
	'yo' => 'Yoruba',
	'zu' => 'Zulu'
	);
}
function translate_text ($text, $from = 'en', $to = 'en') {

	global $googleKey;

	try {

		$url = 'https://www.googleapis.com/language/translate/v2/detect?key='.$googleKey.'&q='.urlencode($text);

		if(!function_exists('curl_version')){
			return false;
		}

		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$result = curl_exec($ch);
		curl_close($ch);

		$result = json_decode(removeBOM($result));

		$language = '';

		if (!empty($result->data->detections[0][0]->language)) {

			if ($result->data->detections[0][0]->confidence < 0.001) {
				return false;
			} else {
				$language = $result->data->detections[0][0]->language;
			}
		} else {
			return false;
		}

		if ($language == 'und' || $language == $to || empty($to) || empty($language)) {
			return false;
		}
		$url = 'https://www.googleapis.com/language/translate/v2?key='.$googleKey.'&q='.urlencode($text).'&source='.$language.'&target='.$to;

		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$result = curl_exec($ch);
		curl_close($ch);

		$result = json_decode(removeBOM($result));

		return $result->data->translations[0]->translatedText;

	} catch (Exception $e) {
		return false;
	}

}
