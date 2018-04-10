<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins.php");
include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."chathistory.php");

if( checkplan('plugins','chathistory') == 0){ exit;}
if(!checkMembershipAccess('chathistory','plugins')){exit();}
if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php")) {
    include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php");
}

$callbackfn='';
if(!empty($_REQUEST['callbackfn'])) {
    $callbackfn=$_REQUEST['callbackfn'];
}
if (empty($_GET['id']) && empty($_GET['history'])) { exit; }
$baseData = rawurlencode($_REQUEST['basedata']);
$body = '';
	if(!empty($_REQUEST['chatroommode'])) {
            $body = <<<EOD
                <script type="text/javascript">getChatLog({$_REQUEST['history']}, {$_REQUEST['chatroommode']}, '{$baseData}');</script>
EOD;
    template();
	} else {
            $body = <<<EOD
                <script type="text/javascript">getChatLog({$_REQUEST['history']}, '', '{$baseData}');</script>
EOD;
	template();
	}

function template() {

    global $body;
    global $callbackfn;
    global $chathistory_language;
    $embed = '';
    $embedcss = '';

    if (!empty($_GET['embed']) && $_GET['embed'] == 'web') {
        $embed = 'web';
        $embedcss = 'embed';
    } elseif (!empty($_GET['embed']) && $_GET['embed'] == 'desktop') {
        $embed = 'desktop';
        $embedcss = 'embed';
    }
    $csstag = getDynamicScriptAndLinkTags(array('type' => "plugin",'name' => 'chathistory','callbakcfn' => $callbakcfn,'ext' => 'css'));
    $jQuerytag = getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'jquery','callbakcfn' => $callbakcfn, 'ext' => 'js'));
    $jstag1 = getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'scroll','callbakcfn' => $callbakcfn, 'ext' => 'js'));
    $jstag2 = getDynamicScriptAndLinkTags(array('type' => "plugin",'name' => 'chathistory','callbakcfn' => $callbakcfn, 'ext' => 'js'));
    echo <<<EOD
    <!DOCTYPE html>
    <html>
        <head>
            <meta name="viewport" content="user-scalable=1,width=device-width, initial-scale=1.0" />
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
            <title>{$chathistory_language[6]}</title>
            $csstag
            $jQuerytag
            $jstag1
            $jstag2
            <script type="text/javascript"> var norecords = '{$chathistory_language[9]}';</script>
        </head>
        <body>
            <div class="cometchat_wrapper">
                <div class="container_title {$embedcss}" >{$chathistory_language[6]}</div>
                    <div class="container_body {$embedcss}">
                        <div class="container_body_chat">
                        {$body}
                        </div>
                    </div>
                </div>
            </div>
        </body>
    </html>
EOD;

exit;

}
