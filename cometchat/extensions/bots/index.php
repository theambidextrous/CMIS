<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/


include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins.php");
include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."config.php");
include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'colors'.DIRECTORY_SEPARATOR.'color.php');
if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php")){
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php");
}
if(!checkMembershipAccess('bots','extensions')){exit();}
$baseUrl = BASE_URL;
$botcontent = array();
global $userid;
if (!empty($_REQUEST['basedata'])) {
	$basedata = $_REQUEST['basedata'];
}
$layout = 'docked';
if(!empty($_REQUEST['cc_layout'])){
	$layout = $_REQUEST['cc_layout'];
}
$caller="";
if(!empty($_REQUEST['caller'])){
	$caller = $_REQUEST['caller'];
}
$botlist = '';
$bothtml = '';
if(function_exists('getBotList')){
	$botlist = getBotList();
}

$imageHeight = '120px';
if($layout == 'docked'){
	$imageHeight = '80px';
}
foreach ($botlist as $key => $value) {
	$botactiveid = $key;
	if(empty($value['d'])){
		$botlist[$key]['d'] = $value['d'] = $bots_language['default_desc'];
	}
	$botsId       = $value['id'];
	$botsName     = $value['n'];
	$botsKeyword = str_replace(" ", "", $botsName);
	$botsIcon     = $value['a'];
	$botsDetails  = preg_replace('#\"{1}(.*?)\"{1}#', '<b>$1</b>', $value['d']);
	$botsDetails  = str_replace("\r\n", "\r\n<br />", $botsDetails);

	$botlist[$key]['d'] = $botsDetails;
	$botcontent[$key] = <<<EOD
		<div id="cometchat_botcontainer_{$botsId}" class="cometchat_botcontainer" style="width:94%;">
			<div class="cometchat_botcontainer_body">
				<div class="cometchat_bot_info">
					<div class="cometchat_botdata">
						<img class="cometchat_botavatarimage" src="{$botsIcon}" style="border-radius: 100px;width:{$imageHeight}">
					</div>
					<div style="clear:both"></div>
				</div>
					<div class="desc">
						<div class="cometchat_botname"></div>
						<div class="cometchat_botdesc">$botsDetails</div>
					</div>
				</div>
			</div>
		</div>
EOD;

	$bothtml .= <<<EOD
	<div botid="{$botsId}" id="cometchat_botlist_{$botsId}" class="cometchat_botlist" >
		<div class="cometchat_botscontentavatar">
			<img class="cometchat_botscontentavatarimage" src="{$botsIcon}">
				<div class="cometchat_bots_desc">
					<div class="cometchat_botscontentname">{$botsName}
						<span class="cometchat_botrule"> @{$botsKeyword}</span>
					</div>
					<div class="cometchat_botslist_desc">{$botsDetails}</div>
				</div>
		</div>
	</div>
EOD;
}

if(isset($_REQUEST['callbackfn']) && $_REQUEST['callbackfn'] == 'mobileapp'){
	if(!empty($bothtml)){
		$botlh = md5(serialize($botlist));
		$response['botlist'] = $botlist;
		$response['botlh'] = $botlh;
		echo json_encode($response);exit;
	}else{
		$response['nobots'] = $bots_language['no_bots'];
		echo json_encode($response);exit;
	}
}
if(empty($bothtml)){
	$bothtml = '<div class="cometchat_nobots">'.$bots_language['no_bots'].'</div>';
}
$botcontent = empty($botcontent)?'{}':json_encode($botcontent);
$botlist = empty($botlist)?'{}':json_encode($botlist);
$popoutmode = 0;

$jstag1 = getDynamicScriptAndLinkTags(array('ext' => 'js'));
$jstag2 = getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'scroll', 'ext' => 'js'));

if(empty($userid) || 1){
	echo <<<EOD
	<!DOCTYPE html>
	<html>
	<head>
		<style type="text/css">
			.botdescription{
				width: 79%;
				float: right;
				margin-top: 20px;
			}
			.cometchat_botavatarimage{
				margin: 0 auto;
				display: block;
			}
			.center{
				text-align: center;
			}
			.cometchat_botcontainer{
				padding: 8px;
				margin-top: 10px;
			}
			.cometchat_botname{
				margin: 10px;
				font-weight: bolder;
			}
			.cometchat_userlist_hover, .cometchat_botlist_hover {
				background-color: {$layoutSettings['hover_color']} !important;
			}
			.cometchat_botlist {
				cursor: pointer;
				height: 45px;
				line-height: 100%;
				padding: 2px 8px 2px 5px;
				border-bottom: 1px solid;
				border-color: #E6E7EA;
				clear: both;
				padding: 7px 5px 6px 5px;
				background-color: #ffffff;

			}
			.cometchat_botcontentname{
				text-overflow: ellipsis;
				text-transform: capitalize;
				max-width: 195px;
				padding-top: 10px;
				white-space: nowrap;
			}
			.cometchat_botrule {
				text-transform: lowercase;
				color: #56a8e3;
			}
			.cometchat_bots_desc{
				float: right;
				margin-left: 15px;
				margin-top:10px;
			}
			.cometchat_botscontentname{
				margin-top: -6px;
				margin-bottom: 5px;
			}
			.cometchat_botlist:hover{
				background-color: {$layoutSettings['hover_color']} !important;
			}
			.cometchat_botslist_desc{
				font-size: 12px;
				color: #aaaaaa;
				max-width: 200px;
				width: 150px;
				text-overflow: ellipsis;
				white-space: nowrap;
				overflow: hidden;
				line-height: 13px;
				float: left;
				height: 20px;
			}
			.cometchat_botscontentavatar {
				display: block;
				float: left;
				padding-bottom: 1px;
				padding-top: 2px;
				position: relative;
			}
			.cometchat_botscontentavatarimage {
				height: 40px;
				width: 40px;
				position: relative;
				border-radius: 50%;
			}
			.cometchat_nobots{
				font-family: {$layoutSettings['font_family']};
				font-size: 13px;
				line-height: 1.3em;
				padding: 10px;
				color: {$layoutSettings['text_color']};
				text-align: center;
			}
		</style>
		$jstag1
		$jstag2
		<script type="text/javascript">
			function showBotlist(){
				jqcc('.cometchat_bots_wrapper').html(jqcc('#botlist_container').val());
			}
			jqcc(function() {
				var botcontent = $botcontent;
				var botlist = $botlist;
				var botListHtml = jqcc('.cometchat_bots_wrapper').html();
				jqcc('.cometchat_botlist').live('click', function() {
					var botid = '{$chromeReorderFix}'+jqcc('#' + this.id).attr('botid');
					jqcc('.cometchat_bots_wrapper').html(botcontent[botid]);

					var controlparameters = {"type":"core", "name":"libraries", "method":"toggleBotsAction", "params":{"botlist":botlist, "botid":botid,"bots_language":"{$bots_language['bots']}",baseUrl:"{$baseUrl}"}};
					controlparameters = JSON.stringify(controlparameters);
					if(typeof(parent) != 'undefined' && parent != null && parent != self){
						parent.postMessage('CC^CONTROL_'+controlparameters,'*');
					} else {
						window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');
					}
				});
				jqcc('.cometchat_bots_wrapper').slimScroll({
					height: 'auto'
				});
			});
		</script>
	</head>
	<body style="margin:0px;">
	<div style="background: #FFF;font-family: Tahoma,Verdana,Arial,'Bitstream Vera Sans',sans-serif;font-size: 13px;height: 100vh;" class="cometchat_bots_wrapper">
		{$bothtml}
	</div>
	<textarea id="botlist_container" style="display:none;">{$bothtml}</textarea>
	<body>
	</html>
EOD;
	exit;
}
