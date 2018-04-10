<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."modules.php");
include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."config.php");

if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php")){
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php");
}
unset($_SESSION['cometchat']['cometchat_chatroomslist']);
if (!empty($_REQUEST['basedata'])) {
	$_SESSION['basedata'] = $_REQUEST['basedata'];
}
if ($userid == 0 || in_array($userid,$bannedUserIDs) || in_array($_SERVER['REMOTE_ADDR'],$bannedUserIPs) || ($userid > $firstguestID && !$crguestsMode)){
	if (in_array($userid,$bannedUserIDs)) {
		$chatrooms_language[0] = $bannedMessage;
	}
	$baseUrl = BASE_URL;
	$loggedOut = $chatrooms_language[0];
	if(checkAuthMode('social')){
		$loggedOut .= ' <a href="javascript:void(0);" class="socialLogin">'.$chatrooms_language[65].'</a> '.$chatrooms_language[66];
	}
	$groupcsstag = getDynamicScriptAndLinkTags(array('type' => 'module','name' => 'chatrooms' 'ext' => 'css'));
	$jqueryjstag = getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'jquery', 'ext' => 'js'));
	echo <<<EOD
	<!DOCTYPE html>
	<html>
		<head>
			<title>{$chatrooms_language[100]}</title>
			<meta http-equiv="cache-control" content="no-cache">
			<meta http-equiv="pragma" content="no-cache">
			<meta http-equiv="expires" content="-1">
			<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
			<meta name="viewport" content="user-scalable=0,width=device-width, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0" />
			{$groupcsstag}
			{$jqueryjstag}
			<script type="text/javascript">
				$ = jQuery = jqcc;
				$('.socialLogin').live('click',function(){
					if(typeof(parent) != 'undefined' && parent != null && parent != self){
						var controlparameters = {"type":"functions", "name":"socialauth", "method":"login", "params":{"url":"{$baseUrl}functions/login/loginOptions.php"}};
						controlparameters = JSON.stringify(controlparameters);
						parent.postMessage('CC^CONTROL_'+controlparameters,'*');
					} else {

					}
			    });
			</script>
		</head>
		<body>
			<div class="containermessage">
			{$loggedOut}
			</div>
		</body>
	</html>
EOD;
} else {
	$joinroom = '';
	$dynamicChatroom = 0;
	$leaveroom = "";
	if((!empty($_REQUEST['action']) && $_REQUEST['action']='dynamicChatroom') && (!empty($_REQUEST['name']))){
		global $userid;
		global $cookiePrefix;
		$name = sql_real_escape_string($_REQUEST['name']);
		$type = '3';
		$query = sql_query('getChatrooms',array('name'=>sanitize_core($name)));
		$result = sql_fetch_assoc($query);
		if(empty($result['id'])) {
			if ($userid > 0) {
				$password = '';
				$query = sql_query('insertChatroom',array('name'=>sanitize_core($name), 'createdby'=>$userid, 'lastactivity'=>getTimeStamp(), 'password'=>sanitize_core($password), 'type'=>'3'));
				$currentroom = sql_insert_id('cometchat_chatrooms');
				$_GET['id'] = $currentroom;
			}
		}elseif($result['type'] == 3){
			$_GET['id'] =$result['id'];
		}
		$leaveroom = "setTimeout(function(){\$('.welcomemessage a:first, span:first').remove();},500);";
		$dynamicChatroom = 1;
	}

	if (!empty($_GET['roomid'])) {
		$joinroom = "jqcc.cometchat.silentroom('{$_GET['roomid']}','{$_GET['inviteid']}','{$_GET['roomname']}');";
		$autoLogin = 0;
	}
	if (empty($_GET['id']) && !empty($autoLogin)) {
		$_GET['id'] = $autoLogin;
	}
	if (!empty($_GET['id'])) {
		$query = sql_query('getChatroom',array('id'=>$_GET['id']));
		$room = sql_fetch_assoc($query);
		if ($room['id'] > 0) {
			$roomname = base64_encode($room['name']);
			$joinroom = "jqcc.cometchat.silentroom('{$_GET['id']}','','{$roomname}');";
		}
	}
	$loadjs = "$(function() {
					".$joinroom."
					".$leaveroom."
				});";

	if (defined('USE_COMET') && USE_COMET == 1) {
		$loadjs = "function chatroomready(){
					".$loadjs."
				   }";
	}
	$ccauthlogout = '';
	if(checkAuthMode('social')){
    	$ccauthlogout = '<div class="cometchat_tooltip" id="cometchat_authlogout" title="'.$language[80].'"></div>';
	}

	$listItems = "";
	if ($dynamicChatroom == 0) {
		$listItems .= <<<EOD
				<div id="lobbytab" class="tabs tab_selected" title="{$chatrooms_language[3]}">
					<a href="javascript:void(0);" onclick="jqcc[jqcc.cometchat.getChatroomVars('calleeAPI')].loadLobby()">{$chatrooms_language[3]}</a>
				</div>
EOD;
		if (($allowUsers == 1 && $userid < $firstguestID) || ($allowGuests == 1 && $userid > $firstguestID)) {
			$listItems .= <<<EOD
				<div id="createtab" class="tabs" title="{$chatrooms_language[2]}">
					<a href="javascript:void(0);" onclick="javascript:jqcc[jqcc.cometchat.getChatroomVars('calleeAPI')].createChatroom()">{$chatrooms_language[2]}</a>
				</div>
EOD;
		}
	}

	$groupcsstag = getDynamicScriptAndLinkTags(array('type' => 'module','name' => 'chatrooms' 'ext' => 'css'));
	$jqueryjstag = getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'jquery', 'ext' => 'js'));
	$jstoragejstag = getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'storage', 'ext' => 'js'));
	$scrolljstag = getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'scroll', 'ext' => 'js'));
	$groupjsstag = getDynamicScriptAndLinkTags(array('type' => "module",'name' => 'chatrooms', 'ext' => 'jss'));
	echo <<<EOD
	<!DOCTYPE html>
		<html>
			<head>
				<title>{$chatrooms_language[35]}</title>
				<meta name="viewport" content="user-scalable=0,width=device-width, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0" />
				<meta http-equiv="cache-control" content="no-cache">
				<meta http-equiv="pragma" content="no-cache">
				<meta http-equiv="expires" content="-1">
				<meta charset="UTF-8">
				<meta http-equiv="content-type" content="text/html; charset="utf-8"/>
				{$groupcsstag}
				{$jqueryjstag}
				{$jstoragejstag}
				{$scrolljstag}
				{$groupjsstag}
				<script type="text/javascript">
					$ = jQuery = jqcc;
					{$loadjs}
				</script>
				<script type="text/javascript">
					var controlparameters = {"type":"modules", "name":"cometchat", "method":"chatWith", "params":{}};
		            controlparameters = JSON.stringify(controlparameters);
		            if(typeof(parent) != 'undefined' && parent != null && parent != self){
		                parent.postMessage('CC^CONTROL_'+controlparameters,'*');
		            } else if(typeof(window.opener) != 'undefined' && (window.opener) != null) {
		                window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');
		            }
		            var cookiePrefix = '<?php echo $cookiePrefix; ?>';
		            $(document).ready(function(){
			            var auth_logout = $("div#cometchat_authlogout");
			            var baseUrl = jqcc.cometchat.getBaseUrl();
			            var staticCDNUrl = jqcc.cometchat.getStaticCDNUrl();
			            auth_logout.mouseenter(function(){
		                    auth_logout.css('opacity','1');
		                });
		                auth_logout.mouseleave(function(){
		                    auth_logout.css('opacity','0.5');
		                });
	                    auth_logout.click(function(event){
	                    	auth_logout.unbind('click');
	                        event.stopPropagation();
	                        auth_logout.css('background','url('+staticCDNUrl+'layouts/docked/images/loading.gif) no-repeat top left');
	                        jqcc.ajax({
	                            url: baseUrl+'functions/login/logout.php',
	                            dataType: 'jsonp',
	                            success: function(){
	                            	auth_logout.css('background','url('+staticCDNUrl+'layouts/docked/images/logout.png) no-repeat top left');
	                            	if(typeof(jqcc.cometchat.getThemeVariable) != 'undefined') {
		                                $("#cometchat_user_"+jqcc.cometchat.getThemeVariable('openChatboxId')).find('.cometchat_closebox_bottom').click();
		                                jqcc.cometchat.setSessionVariable('openChatboxId', '');
		                            }
	                                jqcc.cookie(cookiePrefix+"loggedin", null, {path: '/'});
	                                jqcc.cookie(cookiePrefix+"state", null, {path: '/'});
	                                jqcc.cookie(cookiePrefix+"crstate", null, {path: '/'});
	                                jqcc.cookie(cookiePrefix+"jabber", null, {path: '/'});
	                                jqcc.cookie(cookiePrefix+"jabber_type", null, {path: '/'});
	                                jqcc.cookie(cookiePrefix+"hidebar", null, {path: '/'});
	                                var controlparameters = {"type":"themes", "name":"cometchat", "method":"loggedout", "params":{}};
						            controlparameters = JSON.stringify(controlparameters);
						            if(typeof(parent) != 'undefined' && parent != null && parent != self){
						            	parent.postMessage('CC^CONTROL_'+controlparameters,'*');
						            } else {
						                window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');
						            }
	                            },
	                            error: function(){
	                            	alert(language[81]);
	                            }
	                        });
	                    });
						jqcc( "#password" ).keyup(function() {
							if(jqcc("#password").val() == ' '){
								alert("{$chatrooms_language[82]}");
								jqcc("#password").val('');
							}
						});

					});
					function closeChatroomPopup(){
						var popoutmode = 0;
						if(window.top!=window.self){
							popoutmode = 1;
						}
						var controlparameters = {"type":"modules", "name":"cometchat", "method":"closeCRPopout", "params":{"allowed":"1", "popoutmode":popoutmode}};
			            controlparameters = JSON.stringify(controlparameters);
			            if(typeof(parent) != 'undefined' && parent != null && parent != self){
			                parent.postMessage('CC^CONTROL_'+controlparameters,'*');
			            } else if(typeof(window.opener) != 'undefined' && (window.opener) != null) {
			                window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');
			            }
					}
				</script>
			</head>
			<body onunload="closeChatroomPopup()">
				<div id="cometchat_wrapper">
					<div class="topbar">
						{$listItems}
					    <div id="currentroomtab" style="display:none"> </div>
					    {$ccauthlogout}
						<div style="clear:both"></div>
						<div class="topbar_text">
							<div class="welcomemessage">{$chatrooms_language[1]}</div>
							<div id="plugins"></div>
						</div>
						<div style="clear:both"></div>
					</div>
					<div style="clear:both"></div>
					<div id="lobby">
						<div class="lobby_rooms content_div" id="lobby_rooms"></div>
					</div>
					<div class="content_div" id="currentroom" style="display:none">
						<div id="currentroom_left" class="content_div">
							<div class="cometchat_prependMessages_container"><div class="cometchat_prependMessages">{$chatrooms_language[74]}</div></div>
							<div id="currentroom_convo">
								<div id="currentroom_convotext"></div>
							</div>
							<div style="clear:both"></div>
							<div class="cometchat_tabcontentinput">
								<textarea class="cometchat_textarea" placeholder='$chatrooms_language[64]'></textarea>
								<div class="cometchat_tabcontentsubmit"></div>
							</div>
						</div>
						<div id="currentroom_right" class="content_div">
							<div id="currentroom_users" class="content_div"></div>
						</div>
					</div>
					<div class="content_div" id="create" style="display:none">
						<div id="currentroom_left" class="content_div">
							<form class="create" onsubmit="javascript:jqcc.cometchat.createChatroomSubmit(); return false;">
								<div style="clear:both;padding-top:10px"></div>
								<div class="create_name">{$chatrooms_language[27]}</div>
								<div class="create_value">
									<input type="text" id="name" class="create_input" placeholder="{$chatrooms_language[63]}" />
								</div>
								<div style="clear:both;padding-top:10px"></div>
								<div class="create_name">{$chatrooms_language[28]}</div>
								<div class="create_value" >
									<select id="type" onchange="jqcc[jqcc.cometchat.getChatroomVars('calleeAPI')].crcheckDropDown(this)" class="create_input">
										<option value="0">{$chatrooms_language[29]}</option>
										<option value="1">{$chatrooms_language[30]}</option>
										<option value="2">{$chatrooms_language[31]}</option>
									</select>
								</div>
								<div style="clear:both;padding-top:10px"></div>
								<div class="create_name password_hide">{$chatrooms_language[32]}</div>
								<div class="create_value password_hide">
									<input id="password" type="password" autocomplete="off" class="create_input" />
								</div>
								<div style="clear:both;padding-top:10px"></div>
								<div class="create_name">&nbsp;</div>
								<div class="create_value">
									<input type="submit" class="invitebutton" value="{$chatrooms_language[33]}" />
								</div>
							</form>
						</div>
					</div>
				</div>
				<script>
				</script>
			</body>
		</html>
EOD;
}
?>
