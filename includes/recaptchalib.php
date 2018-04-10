<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
<script type="text/javascript">
var onloadCallback = function() {
	grecaptcha.render('recaptcha_element_id', {
		'sitekey' : '<?=RECAPTCHA_PUBLIC_KEY;?>'
	});
};
</script>
<?php
function recaptcha_get_html(){        
	echo '<div id="recaptcha_element_id"></div>';
}
function recapture_verify( $recaptcha_resp, $serv_address ){
	//https://www.google.com/recaptcha/api/siteverify
	$recaptcha_resp = urlencode($recaptcha_resp);
	$serv_address = isset($serv_address)?$serv_address:$_SERVER['REMOTE_ADDR'];
	
	$response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".RECAPTCHA_PRIVATE_KEY."&response=".$recaptcha_resp."&remoteip=".$serv_address), true);
	
	return $response['success']; //true|false
}
?>