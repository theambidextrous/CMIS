<?php

	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."config.php");
	$adCode = str_replace("'", "\'", $adCode);
	$adCode = str_replace("\r\n", "", $adCode);
?>

/*
 * CometChat
 * Copyright (c) 2016 Inscripts - support@cometchat.com | http://www.cometchat.com | http://www.inscripts.com
*/

(function($){
	$.ccads = (function () {
		var title = 'Advertisements Extension';
		var height = '<?php echo $adHeight;?>';

        return {
			getTitle: function() {
				return title;
			},
			init: function (id,isgroup) {
				var baseUrl = $.cometchat.getBaseUrl();
				var ad = '<iframe src="'+baseUrl+'extensions/ads/embed.php" frameborder="0" width="100%" height="'+height+'">';
				var activeId = $.cometchat.getActiveId();
				if(isgroup == 1){
					$("#cometchat_chatboxes_wide").find('#cometchat_group_'+id+'_popup').find('.cometchat_tabcontent').before('<div class="cometchat_ad" style= "height:'+height+'px;">'+ad+'</div>');
				}else{
					$("#cometchat_chatboxes_wide").find('#cometchat_user_'+id+'_popup').find('.cometchat_tabcontent').before('<div class="cometchat_ad" style= "height:'+height+'px;">'+ad+'</div>');
				}
				var openChatbox = $('#cometchat_righttab').find('#cometchat_user_'+id+'_popup').find(' #cometchat_tabinputcontainer');
				var openChatroom = $('#cometchat_righttab ').find('#currentroom').find(' #cometchat_tabinputcontainer');
				if(openChatbox.find('.cometchat_ad').length == 0){
					openChatbox.append('<div class="cometchat_ad" style= "height:'+height+'px;">'+ad+'</div>');
				}
				if(openChatroom.find('.cometchat_ad').length == 0){
					openChatroom.append('<div class="cometchat_ad" style= "height:'+height+'px;">'+ad+'</div>');
				}
			}
        };
    })();
})(jqcc);
