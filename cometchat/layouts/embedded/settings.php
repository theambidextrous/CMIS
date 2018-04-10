<?php

if (!defined('CCADMIN')) { echo "NO DICE"; exit; }

$options = array(
    "chatboxWidth"  => array('textbox','Set the Width of the Chat (Minimum Width can be 350px)'),
    "chatboxHeight" => array('textbox','Set the Height of the Chat (Minimum Height can be 420px)'),
);

if (empty($_GET['process']) && empty($_GET['updatesettings'])) {
    include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'config.php');
    $base_url = BASE_URL;
    $form = '';
    if(empty($generateembedcodesettings)) {
        $curl = 0;
        $errorMsg = '';

        $chatroom = '';
        $private = '';
        $none = '';

        if ($enableType == '0') {
            $none = "selected";
        } else if ($enableType == '1') {
            $chatroom = "selected";
        } else if ($enableType == '2') {
            $private = "selected";
        }
        $jqueryjstag =  getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'jquery', 'ext' => 'js'));
        echo <<<EOD
    <!DOCTYPE html>
    <html>
    <head>
    {$jqueryjstag}
    <script>
        $ = jQuery = jqcc;
        function resizeWindow() {
            window.resizeTo(($("form").outerWidth(false)+window.outerWidth-$("form").outerWidth(false)), ($('form').outerHeight(false)+window.outerHeight-window.innerHeight));
        }
    </script>
    </head>
    <body>
        <form name="themesettings" style="height:100%;" action="?module=dashboard&action=loadthemetype&type=layout&name=synergy&updatesettings=true" method="post">
        <div id="content" style="width:auto;">
                <h2>Settings</h2><br />
                <h3 id='data'>You can enable/disable Private chat or Chatroom.</h3>
                <div style="margin-bottom:10px;">
                        <div class="title">Enable :</div>
                        <div class="element" id="">
                            <select name="enableType" id="TypeSelector">
                                <option value="0" $none>Both</option>
                                <option value="1" $chatroom>Only Chatroom</option>
                                <option value="2" $private>Only One-on-one Chat</option>
                            </select>
                        </div>
                        <div style="clear:both;padding:10px;"></div>

                    <div style="clear:both;padding:5px;"></div>
                </div>
                <input type="submit" value="Update Settings" class="button">&nbsp;&nbsp;or <a href="javascript:window.close();">cancel or close</a>
        </div>
        </form>
        <script type="text/javascript" language="javascript">
            $(function() {
                setTimeout(function(){
                    resizeWindow();
                },200);
            });
        </script>
    </body>
    </html>
EOD;
    } else {
        foreach ($options as $option => $result) {
            $req = '';
            if($option == 'chatboxHeight' OR $option == 'chatboxWidth') {
                $req = 'required';
            }
                $form .= '<div class="form-group row"><div class="col-md-6"><label>'.$result[1].'</label>
                          </div><div class="col-md-6">';
            if ($result[0] == 'textbox') {
                $form .=  '<input type="text" class="form-control" id="'.$option.'" name="'.$option.'" value="'.${$option}.'" '.$req.'>';
            }
            $form .= '</div></div>';
        }
echo <<<EOD
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="images/favicon.ico">
    <title>Generate Embed Code</title>
    {$GLOBALS['adminjstag']}
    {$GLOBALS['admincsstag']}
</head>
 <body class="navbar-fixed sidebar-nav fixed-nav" style="background-color: white;overflow-y:hidden;">
    <div class="col-sm-6 col-lg-6">
        <div class="card">
        <div class="card-block">
             <form action="?module=dashboard&action=loadthemetype&type=layout&name=embedded&process=true" onsubmit="return validate();" method="post">
            {$form}

            <div class="checkbox checkbox-success">
                <input id="onlygroup" name="onlygroup" type="checkbox">
                <label for="onlygroup">
                    Only Show Groups(One-on-one Chat Will be Hidden)
                </label>
            </div>

            <div class="row col-md-4" style="">
                <input type="submit" value="Generate Code" class="btn btn-primary">
            </div>
            </form>
        </div>
        </div>
    </div>
    <script>
        function validate(){
            var cbHeight = parseInt($("#chatboxHeight").val());
            $("#chatboxHeight").val(cbHeight)
            var cbWidth = parseInt($("#chatboxWidth").val());
            $("#chatboxWidth").val(cbWidth);
            if(cbHeight < 420) {
                alert('Height must be greater than 420');
                return false;
            } else if(cbWidth < 350){
                alert('Width must be greater than 350');
                return false;
            } else {
                return true;
            }
        }
    </script>
</body>
EOD;

            }
        } else if (!empty($_GET['updatesettings']) && $_GET['updatesettings'] == true) {
            if (isset($_POST['enableType'])) {
                configeditor(array('synergy_settings' => $_POST));
            }
            header("Location:?module=dashboard&action=loadthemetype&type=layout&name=synergy");
        } else {
        	include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'config.php');
            $base_url = BASE_URL;
            $chatboxWidth  = !empty($_POST['chatboxWidth'])  ? $_POST['chatboxWidth']  : 350;
            $chatboxHeight = !empty($_POST['chatboxHeight']) ? $_POST['chatboxHeight'] : 420;
            if (empty($_POST['onlygroup'])) {
                $embed_code = '&lt;div id="cometchat_embed_synergy_container" style="width:'.$_POST['chatboxWidth'].'px;height:'.$_POST['chatboxHeight'].'px;max-width:100%;border:1px solid #CCCCCC;border-radius:5px;overflow:hidden;" &gt;&lt;/div&gt;&lt;script src="'.getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'embedcode','urlonly'=>1, 'ext' => 'js')).'" type="text/javascript"&gt;&lt;/script&gt;&lt;script&gt;var iframeObj = {};iframeObj.module="synergy";iframeObj.style="min-height:420px;min-width:350px;";iframeObj.width="'.$_POST['chatboxWidth'].'px";iframeObj.height="'.$_POST['chatboxHeight'].'px";iframeObj.src="'.BASE_URL.'cometchat_embedded.php"; if(typeof(addEmbedIframe)=="function"){addEmbedIframe(iframeObj);}&lt;/script&gt;';
                $cmscode = '';
                if(method_exists($GLOBALS['integration'], 'isPluginActive') && $GLOBALS['integration']->isPluginActive('cometchat')){
                    $cmscode = getCometChatEmbedCode(array('width' => $chatboxWidth, 'height' =>  $chatboxHeight));
                }
            } else {
                $querystring = 'chatroomsonly=1';
                $cmscode = getCometChatEmbedCode(array());
                if(!empty($_REQUEST['crid'])){
                    $querystring = 'crid='.$_REQUEST['crid'];
                    $cmscode = getCometChatEmbedCode(array('groupid' => $_REQUEST['crid']));
                }
                if(!(method_exists($GLOBALS['integration'], 'isPluginActive') && $GLOBALS['integration']->isPluginActive('cometchat'))){
                    $cmscode = '';
                }
                $embed_code = '<div id="cometchat_embed_chatrooms_container" style="display:inline-block; border:1px solid #CCCCCC;"></div>'.getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'embedcode', 'ext' => 'js')).'<script>var iframeObj = {};iframeObj.module="chatrooms";iframeObj.style="min-height:420px;min-width:300px;";iframeObj.src="'.BASE_URL.'cometchat_embedded.php?'.$querystring.'";iframeObj.width="'.$_POST['chatboxWidth'].'";iframeObj.height="'.$_POST['chatboxHeight'].'";if(typeof(addEmbedIframe)=="function"){addEmbedIframe(iframeObj);}</script>';

            }
            echo <<<EOD
                <!DOCTYPE html>
                <html>
                    <head>
                        {$GLOBALS['adminjstag']}
                        {$GLOBALS['admincsstag']}
                        <script type="text/javascript" language="javascript">
                            $(function() {
                                setTimeout(function(){
                                    resizeWindow();
                                },200);
                            });
                            function resizeWindow() {
                                window.resizeTo((520), (190+window.outerHeight-window.innerHeight));
                            }
                        </script>
                    </head>
                    <body style="background-color: white;overflow-y:hidden;">
                        {$cmscode}
                        <h6 id="admin-modal-title" class="modal-title" style="padding: 20px 0px 3px 0px;">HTML Embed Code</h6>
                        <textarea readonly="" class="form-control" style="width:100%;height:180px">{$embed_code}</textarea>
                    </body>
                </html>
EOD;
       }
?>
