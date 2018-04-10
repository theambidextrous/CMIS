<?php
/*********************************************
Company:	Wits Technologies Ltd
Developer:	Sammy Mwaura Waweru
Mobile:		+254721428276
Email:		sammy@witstechnologies.co.ke
Website:	http://www.witstechnologies.co.ke/
*********************************************/

//Main class validator
//Validates all data entries
class validator{

	function is_String($string) {
		$stripStr = preg_replace('/\s/', '', $string);
		return preg_match("/^([-A-z0-9_.,&']){3,100}$/", $stripStr);
		//return !is_string($stripStr)?(TRUE):(FALSE);
	}
	
	function is_Numeric($int) {
		return is_numeric($int)?(TRUE):(FALSE);
	}
	
	function is_username($username) {
		return preg_match("/^([-A-z0-9_.]){3,20}$/", $username);
	}
	
	function is_password($password) {
		//Password must be at least 7 characters mixed with at least one lowercase, uppercase letter and digit
		//return preg_match("/^.*(?=.{7,})(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).*$/", $password);
		
		//Password must be at least 7 letters mixed with digits and symbols
		return preg_match("/^.*(?=.{7,})(?=.*\d)([a-zA-Z]).*$/", $password);
	}
	
	function cmp_string($string1,$string2) {
		return (strcmp($string1, $string2)==0)?(TRUE):(FALSE);
	}
	
	function is_email($email) {
		$regexp = "/^[^0-9][A-z0-9_.-]+([.][A-z0-9_-]+)*[@][A-z0-9_-]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/";
		return preg_match($regexp, $email);
	}
	
	function is_phone($phone) {
		$stripped = preg_replace("/(\(|\)|\-|\+)/","",preg_replace("/([  ]+)/","",$phone));
		return (!is_numeric($stripped) || ((strlen($stripped)<7) || (strlen($stripped)>13)))?FALSE:TRUE;
	}
	
	function is_zipcode($postal_code, $country_code="US") {
		 
		$ZIPREG=array(
			"US"=>"^\d{5}([\-]?\d{4})?$",
			"UK"=>"^(GIR|[A-Z]\d[A-Z\d]??|[A-Z]{2}\d[A-Z\d]??)[ ]??(\d[A-Z]{2})$",
			"DE"=>"\b((?:0[1-46-9]\d{3})|(?:[1-357-9]\d{4})|(?:[4][0-24-9]\d{3})|(?:[6][013-9]\d{3}))\b",
			"CA"=>"^([ABCEGHJKLMNPRSTVXY]\d[ABCEGHJKLMNPRSTVWXYZ])\ {0,1}(\d[ABCEGHJKLMNPRSTVWXYZ]\d)$",
			"FR"=>"^(F-)?((2[A|B])|[0-9]{2})[0-9]{3}$",
			"IT"=>"^(V-|I-)?[0-9]{5}$",
			"AU"=>"^(0[289][0-9]{2})|([1345689][0-9]{3})|(2[0-8][0-9]{2})|(290[0-9])|(291[0-4])|(7[0-4][0-9]{2})|(7[8-9][0-9]{2})$",
			"NL"=>"^[1-9][0-9]{3}\s?([a-zA-Z]{2})?$",
			"ES"=>"^([1-9]{2}|[0-9][1-9]|[1-9][0-9])[0-9]{3}$",
			"DK"=>"^([D-d][K-k])?( |-)?[1-9]{1}[0-9]{3}$",
			"SE"=>"^(s-|S-){0,1}[0-9]{3}\s?[0-9]{2}$",
			"BE"=>"^[1-9]{1}[0-9]{3}$"
		);
		 
		if ($ZIPREG[$country_code]) {
		 
			if (!preg_match("/".$ZIPREG[$country_code]."/i",$postal_code)){
				//Validation failed, provided zip/postal code is not valid.
				return false;
			} else {
				//Validation passed, provided zip/postal code is valid.
				return true;
			}
		 
		} else {		 
			//Validation not available
			return false;		 
		}
	}
	
	function is_date($month, $day, $year) {
		return checkdate($month, $day, $year);
	}
	
	function is_url($url) { //Thanks to 4ice for the fix.

        $urlregex = "((https?|ftp)\:\/\/)?"; // SCHEME 
		$urlregex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; // User and Pass 
		$urlregex .= "[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)*"; // Host or IP (http://localhost)
		//$urlregex .= "([a-z0-9-.]*)\.([a-z]{2,3})"; // Host or IP 
		$urlregex .= "(\:[0-9]{2,5})?"; // Port 
		$urlregex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; // Path 
		$urlregex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; // GET Query 
		$urlregex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; // Anchor 
        
        return preg_match("/^$urlregex$/", $url)?TRUE:FALSE; 
    }
	
	function is_host($host) {
		 //mail.xyz.com
		 $urlregex = "/^[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)*(\.[a-z]{2,4})$/";
		 
		 return preg_match($urlregex, $host)?TRUE:FALSE; 
	}
	
	function is_port($port){
		return preg_match("/^([0-9]){2,4}$/", $port);
	}

    function is_ip($ip) {
      
        if(!$ip or empty($ip))
            return false;
      
        $ip=trim($ip);
        if(preg_match("/^[0-9]{1,3}(.[0-9]{1,3}){3}$/",$ip)) {
            foreach(explode(".", $ip) as $block)
                if($block<0 || $block>255 )
                    return false;
            return true;
        }
        return false;
    }

}
?>