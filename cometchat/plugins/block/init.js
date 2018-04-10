<?php
	if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php")) {
		include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php");
	}
?>

/*
 * CometChat
 * Copyright (c) 2016 Inscripts - support@cometchat.com | http://www.cometchat.com | http://www.inscripts.com
*/
function manageBlockList() {
	if((typeof(parent) != 'undefined' && parent != null && parent != self) || window.top != window.self){
		var controlparameters = {"type":"plugins", "name":"ccblock", "method":"blockList", "params":{}};
		controlparameters = JSON.stringify(controlparameters);
		parent.postMessage('CC^CONTROL_'+controlparameters,'*');
	} else {
		jqcc.ccblock.blockList(0);
	}
}

(function($){

	$.ccblock = (function () {

		var mobileDevice = navigator.userAgent.match(/ipad|ipod|iphone|android|windows ce|Windows Phone|IEMobile|blackberry|palm|symbian/i);

        return {

			getTitle: function() {
				return jqcc.ccblock.getLanguage('title');
			},

			init: function (params) {
				var id = params.to;
				var chatroommode = params.chatroommode;
				var baseUrl      = $.cometchat.getBaseUrl();
				var baseData 	 = $.cometchat.getBaseData();
				if (jqcc.cometchat.membershipAccess('block','plugins')){
					var result = confirm(jqcc.ccblock.getLanguage('confirm_block_user'));
					if (result) {
						jqcc.ajax({
							url: baseUrl+'plugins/block/index.php?action=block',
							data: {to: id, basedata: baseData},
				            dataType: 'jsonp',
				            type: 'POST',
		                    success: function(data){
		                		if(data['result']==2){
		                			manageBlockList();
		                		} else {
			                		alert(jqcc.ccblock.getLanguage('user_blocked'));
			                		jqcc.cometchat.closeChatbox(id);
									setTimeout(function() {
										if ($('#cometchat_user_'+id).length > 0) {
											$('#cometchat_user_'+id+' .cometchat_closebox_bottom').click();
										}
										if($('#cometchat_user_'+id+'_popup .cometchat_user_closebox').length>0){
											jqcc('.cometchat_closebox').click();
											setTimeout(function(){
												$('#cometchat_user_'+id+'_popup .cometchat_user_closebox').click();
											},700);
										}
									}, 1000);
										jqcc.cometchat.chatHeartbeat();
		                		}
		                    },
				            error: function(data){
				            }
						});
					}
				}
			},

			addCode: function() {
                    $('#cometchat_optionsbutton_popup .cometchat_optionstyle').append('<a class="cometchat_manage_blocklist" href="javascript:void(0);" onclick="manageBlockList()" style="margin:5px;">'+jqcc.ccblock.getLanguage('accept_request')+'</a>');
			},

			blockList: function (params) {
				if(typeof(params.windowMode) == "undefined") {
					params.windowMode = 0;
				} else {
					params.windowMode = 1;
				}
				if(mobileDevice){
					params.windowMode = 1;
				}
				jqcc.ccblock.loadblockList(params);
			},

			getLanguage: function(id) {
				block_language =  <?php echo json_encode($block_language); ?>;
				if(typeof id==undefined){
					return block_language;
				}else{
					return block_language[id];
				}
			},

			loadblockList: function(params){
				baseUrl = $.cometchat.getBaseUrl();
				baseData = $.cometchat.getBaseData();

				loadCCPopup(baseUrl+'plugins/block/index.php?basedata='+baseData+'&embed=web', 'blocks',"status=0,toolbar=0,menubar=0,directories=0,resizable=0,location=0,status=0,scrollbars=0, width=500,height=150",500,150,jqcc.ccblock.getLanguage('accept_request'),0,0,0,0,params.windowMode);
			}
        };
    })();

})(jqcc);
