<?php

include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'cometchat_init.php');
if(!empty($client)&&!empty($_REQUEST['ccactiveauth'])){
	$ccactiveauth = explode(',', $_REQUEST['ccactiveauth']);
}
$staticCDNUrl = STATIC_CDN_URL;

if(!empty($firebaseAPIKey)){
	$authHTML = '';
	foreach ($ccactiveauth as $key => $value) {
	$valueinlowercase = strtolower($value);
	$socialAuthHTML .= <<<EOD
	<div class="auth_options {$valueinlowercase}_auth_options" onclick="javascript:cometchat_socialauth_login({'AuthProvider':'{$value}'})" ;>
		<img src="{$staticCDNUrl}layouts/docked/images/login{$valueinlowercase}.svg">
		<span>{$value}</span>
	</div>
EOD;
	}
	$firebaseauthjstag =  getDynamicScriptAndLinkTags(array('type'=>'core','name'=>'firebaseauth','ext'=>'js'));
	$firebaseapijstag =  getDynamicScriptAndLinkTags(array('type'=>'core','name'=>'firebaseapi','ext'=>'js'));
	$socialAuthHead = <<<EOD
	{$firebaseauthjstag}
	{$firebaseapijstag}
	<style type="text/css">
		.auth_options {
			height: 38px;
			width: 215px;
			margin: 10px auto;
			border-radius: 29px;
			cursor: pointer;
			position: relative;
			display: table;
			text-align: center;
		}
		.auth_options span {
			font-family: "Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;
			font-size: 14px;
			color: #ffffff;
			text-transform: capitalize;
			display: table-cell;
			vertical-align: middle;
		}
		.auth_options img {
			position: absolute;
			left: 26px;
			top: 9px;
		}
		.facebook_auth_options {
		    background-color: #3B5998;
		}
		.google_auth_options {
		    background-color: #DD4B39;
		}
		.twitter_auth_options {
		    background-color: #55ACEE;
		}
	</style>
EOD;
}else{
	$activeauths = implode(',', $ccactiveauth);
	$socialAuthHTML =<<<EOD
		<iframe width="100%" height="100%"  allowtransparency="true" frameborder="0"  scrolling="no"  src="//10108.cometondemand.net/functions/login/?ccactiveauth={$activeauths}" />
EOD;
$socialAuthHead =<<<EOD
		<script type="text/javascript">
			var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
			var eventer = window[eventMethod];
			var messageEvent = eventMethod == "attachEvent" ? "onmessage" : "message";
			eventer(messageEvent,function(e) {
				if(typeof(e.data) != 'undefined' && typeof(e.data) == 'string') {
					if(e.data.indexOf('CC^CONTROL_')!== -1){
						parent.postMessage(e.data,'*');
					}
				}
			},false);
		</script>
EOD;
}

?>

<!DOCTYPE html>
<html>
<head>
	<title><?php echo $language['title'];?> Auth</title>
	<?php echo $socialAuthHead; ?>
	<style type="text/css">
		html,body{
			margin: 0px auto;
		}
	</style>
</head>
<body>
<?php echo $socialAuthHTML; ?>
</body>
</html>
