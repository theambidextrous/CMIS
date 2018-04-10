<?php

if (!defined('CCADMIN')) { echo "NO DICE"; exit; }
global $layout, $client, $jslink, $csslink;
$base_url = BASE_URL;
if(!empty($_GET['process']) && $_GET['process'] == true) {
    include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'config.php');
    $embed_code = getDynamicScriptAndLinkTags(array('ext' => 'js'));
    $embed_code .= getDynamicScriptAndLinkTags(array('ext' => 'css'));
    $cmscode = '';
    if(method_exists($GLOBALS['integration'], 'isPluginActive') && $GLOBALS['integration']->isPluginActive('cometchat')){
        $cmscode = getDockedCode();
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
            <h6 id="admin-modal-title" class="modal-title" style="padding: 20px 0px 3px 0px;">HTML Docked Code</h6>
            <textarea readonly="" class="form-control" style="width:100%;height:90px">{$embed_code}</textarea>
        </body>
        </html>
EOD;

}
?>
