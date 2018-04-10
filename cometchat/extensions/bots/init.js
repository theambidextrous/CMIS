<?php
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."config.php");
	if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php")) {
		include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php");
	}
	$width = 300;
	$height = 356;
?>

/*
 * CometChat
 * Copyright (c) 2016 Inscripts - support@cometchat.com | http://www.cometchat.com | http://www.inscripts.com
*/

(function($){
	$.ccbots = (function () {
		var title = 'Bots Extension';

        return {
			getTitle: function() {
				return title;
			},
			init: function () {
				if(isWindowOpen() || jqcc('#cometchat_container_'+name).length > 0) {
					alert("<?php echo $bots_language['popup_already_open'];?>");
					return;
				}
				var baseUrl = $.cometchat.getBaseUrl();
				var baseData = $.cometchat.getBaseData();
				loadCCPopup(baseUrl+'extensions/bots/index.php?action=index&basedata='+baseData, 'bots',"status=0,toolbar=0,menubar=0,directories=0,resizable=1,location=0,status=0,scrollbars=0, width=<?php echo $width;?>,height=<?php echo $height;?>",<?php echo $width;?>,<?php echo $height;?>,'<?php echo $bots_language["bots"];?>',1,1,0,1,0);

			}
        };
    })();
})(jqcc);
