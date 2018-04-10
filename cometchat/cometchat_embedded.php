<?php
/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

$ccbasedata=null;
if(!empty($_REQUEST['basedata'])) {
	$ccbasedata = $_REQUEST['basedata'];
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'cometchat_init.php');
	setcookie($cookiePrefix."data", $_REQUEST['basedata'], 0, "/");
	$_SESSION['basedata'] = $_REQUEST['basedata'];
}else{
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'config.php');
}

$callbackfn ='';

if(!empty($_REQUEST['callbackfn'])){
	$callbackfn = $_REQUEST['callbackfn'];
}

$id = 0;
if(!empty($_REQUEST['user'])){
	$id = $_REQUEST['user'];
}


if(!empty($_REQUEST['chatroomsonly'])) {
	$chatroomsonly = "&chatroomsonly=1";
} else {
	$chatroomsonly = "&chatroomsonly=0";
}

if(!empty($_REQUEST['crid'])) {
	$chatroomid = "&chatroomid=".$_REQUEST['crid'];
} else {
	$chatroomid = "&chatroomid=0";
}

?>

<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="user-scalable=0,width=device-width, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0" />
	<meta http-equiv="Content-Type" content="text/html" charset="UTF-8"/>
	<title><?php echo $language['title'];?></title>
	<script type="text/javascript">
		var embeddedchatroomid = <?php echo (!empty($_REQUEST['crid']) ? intval($_REQUEST['crid']) : '0').';'; ?>
		var chatroomsonly = <?php echo (!empty($_REQUEST['chatroomsonly']) ? '1' : '0').';'; ?>
	</script>
	<?php
		echo getDynamicScriptAndLinkTags(array('layout'=> 'embedded','callbackfn'=> $callbackfn,'ext'=> 'css','grouponly'=> 0));
		echo getDynamicScriptAndLinkTags(array('layout'=> 'embedded','callbackfn'=> $callbackfn,'ext'=> 'js'));
		echo getDynamicScriptAndLinkTags(array('type'=> 'plugin','name'=> 'transliterate','ext'=> 'js'));
		echo getDynamicScriptAndLinkTags(array('type'=> 'core','name'=> 'jquery','ext'=> 'js'));
	?>
	<script type="text/javascript">
		function initializeEmbeddedLayout(){
			var embeddedchatroomid = <?php echo (!empty($_REQUEST['crid']) ?  intval($_REQUEST['crid']) : '0').';'; ?>
			var cookiePrefix = '<?php echo (!empty($cookiePrefix) ? $cookiePrefix : 'cc_') ?>';
			var chatroomsonly = <?php echo (!empty($_REQUEST['chatroomsonly']) ? '1' : '0').';'; ?>
			jqcc(document).ready(function(){
				if (embeddedchatroomid != 0 && embeddedchatroomid !='null' && typeof(embeddedchatroomid) != 'undefined' && typeof(jqcc.cometchat.chatroomHeartbeat) == 'function') {
					jqcc.cometchat.checkChatroomPass(embeddedchatroomid,'',0,'',1,0,0,0);
				}

				<?php if(!empty($ccbasedata)){ ?>
					jqcc.cookie(cookiePrefix+'data','<?php echo $ccbasedata; ?>');
					jqcc.cometchat.reinitialize();
				<?php } ?>
				google.load('elements', '1', {
					packages: 'transliteration'
				});

				document.addEventListener('dragover',function(e){
					e = e || event;
					e.preventDefault();
				},false);

				document.addEventListener('drop',function(e){
					e = e || event;
					e.preventDefault();
				},false);

				var uid = '<?php echo $id; ?>';
				if(uid > 0){
					var controlparameters = {
						type: 'core',
						name: 'cometchat',
						method: 'chatWith',
						params: {
							uid: uid,
							embedded: 1
						}
					};
					console.log("USERID: "+uid);
					controlparameters = JSON.stringify(controlparameters);
					setTimeout(function(){
						parent.postMessage('CC^CONTROL_'+controlparameters,'*');
					},500);

				}
			});
		}
	</script>
</head>
<body style="overflow: hidden;">
</body>
</html>
