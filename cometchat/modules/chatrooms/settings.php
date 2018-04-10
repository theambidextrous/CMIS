<?php

if (!defined('CCADMIN')) { echo "NO DICE"; exit; }

if(!empty($_REQUEST['embedcode'])){
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'config.php');
	$querystring = 'chatroomsonly=1';
	$cmscode = getCometChatEmbedCode(array());
	if(!empty($_REQUEST['crid'])){
		$querystring = 'crid='.$_REQUEST['crid'];
		$cmscode = getCometChatEmbedCode(array('groupid' => $_REQUEST['crid']));
	}
	if(!(method_exists($GLOBALS['integration'], 'isPluginActive') && $GLOBALS['integration']->isPluginActive('cometchat'))){
		$cmscode = '';
	}
	$synergyembedscript = getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'embedcode', 'ext' => 'js')).'<script>var iframeObj = {};iframeObj.module="chatrooms";iframeObj.style="min-height:420px;min-width:300px;";iframeObj.src="'.BASE_URL.'cometchat_embedded.php?'.$querystring.'";iframeObj.width="600";iframeObj.height="300";if(typeof(addEmbedIframe)=="function"){addEmbedIframe(iframeObj);}</script>';

	echo <<<EOD
        <!DOCTYPE html>
        <html>
            <head>
				{$GLOBALS['adminjstag']}
				{$GLOBALS['admincsstag']}
            </head>
            <body style="background-color: white;overflow-y:hidden;">
            	{$cmscode}
            	<div>
            		<h6 id="admin-modal-title" class="modal-title" style="padding: 10px 0px 10px 0px;">HTML Embed Code</h6>
            		<textarea readonly class="form-control" style="width:100%;height:170px;border-radius: 0.25rem;"><div id="cometchat_embed_chatrooms_container" style="display:inline-block; border:1px solid #CCCCCC;"></div>{$synergyembedscript}</textarea>
            	</div>
            </body>
        </html>
EOD;

	exit;
}
