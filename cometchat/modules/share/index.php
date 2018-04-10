<?php
	include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."modules.php");
	if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php")) {
		include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php");
	}
	if(!checkMembershipAccess('share','modules')){exit();}
	$layout = 'docked';
	if(!empty($_REQUEST['cc_layout'])){
		$layout = $_REQUEST['cc_layout'];
	}

	$jstag = getDynamicScriptAndLinkTags(array('type' => "module",'name' => 'share','layout' => $layout, 'ext' => 'js'));
	$jQuerytag = getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'jquery', 'ext' => 'js'));
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo "$share_language[100]" ?></title>
		<meta name="viewport" content="user-scalable=1,width=device-width, initial-scale=1.0" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<?php echo $jstag;?>
		<script>
			var controlparameters = {"type":"modules", "name":"share", "method":"setTitle", "params":{}};
			controlparameters = JSON.stringify(controlparameters);
			parent.postMessage('CC^CONTROL_'+controlparameters,'*');
		</script>
		<script type="text/javascript" src="//s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4f38dbd865c1cfe2"></script>
		<?php echo $jQuerytag;?>
		<script>
			jqcc(document).ready(function() {
				var controlparameters = {"type":"modules", "name":"share", "method":"getParentURL", "params":{}};
				controlparameters = JSON.stringify(controlparameters);
				if(typeof(parent) != 'undefined' && parent != null && parent != self){
					parent.postMessage('CC^CONTROL_'+controlparameters,'*');
				} else {
					window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');
				}
			});
			var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
			var eventer = window[eventMethod];
			var messageEvent = eventMethod == "attachEvent" ? "onmessage" : "message";
			eventer(messageEvent,function(e) {
	    		if(typeof(e.data) != 'undefined' && typeof(e.data) == 'string') {
	    			if(e.data.indexOf('CC^CONTROL_')!== -1){
	    				var controlparameters = e.data.slice(11);
		                controlparameters = JSON.parse(controlparameters);
		                var theUrl = controlparameters.theUrl;
		                var title = controlparameters.title;
						addthis.toolbox(".addthis_toolbox", null, {title: title, url: theUrl});
	    			}
	    		}
	    	},false);
		</script>
	</head>

	<body>
		<div id="wrapper">
			<div class="addthis_toolbox addthis_32x32_style addthis_default_style">
				<a class="addthis_button_facebook shareclass"></a>
				<a class="addthis_button_twitter shareclass"></a>
				<a class="addthis_button_google_plusone shareclass" g:plusone:count="false" g:plusone:annotation="none" style="margin-top:4px;width:38px;"></a>
				<a class="addthis_button_linkedin shareclass" title="LinkedIn"></a>
				<a class="addthis_button_stumbleupon shareclass"></a>
				<a class="addthis_button_reddit shareclass"></a>
				<a class="addthis_button_delicious shareclass"></a>
				<a class="addthis_button_digg shareclass" title="Digg This"></a>
				<a class="addthis_button_favorites shareclass" title="Bookmark this Page"></a>
			</div>
		</div>
		<style>
			.gc-reset {
				display: none !important;
			}
		</style>
	</body>
</html>
