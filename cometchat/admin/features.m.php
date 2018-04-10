<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

if (!defined('CCADMIN')) { echo "NO DICE"; exit; }

function index() {
    global $body,$plugins,$crplugins,$navigation,$ts,$hideconfig,$client,$licensekey;
    $redirecturl = 'https://www.cometchat.com/upgrade';
    $plugins_core = setConfigValue('plugins_core',array());
    $con_feture_list = $chat_feture_list = $oneononeactive = $cractive = '';
    $chatfeturs = array('chathistory','clearconversation','block','save');
    $no = 0;

    if(checkLicenseVersion()){
        $redirecturl = 'https://secure.cometchat.com';
    }

    foreach ($plugins_core as $plugin => $plugininfo) {
            $no++;
            $ti = $plugin;
            $titles[$plugin] = $plugininfo;
            $config = '';
            if (file_exists(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$ti.DIRECTORY_SEPARATOR.'settings.php') && !in_array($ti, $hideconfig)) {
                $config = ' <a data-toggle="tooltip" title="Configure" href="javascript:void(0)" onclick="javascript:plugins_configplugin(\''.$ti.'\')" style="color:black;"><i class="fa fa-lg fa-cogs"></i></a>';
            }
            $notavailable = '';
            $tooltipmessagegrp = 'Add In Group Chat';
            $tooltipmessage = 'Add In One-on-one Chat';
            if(!file_exists(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$ti)){
                $notavailable = 'feature-unavailable';
                $tooltipmessage = $tooltipmessagegrp = 'Please force update your CometChat installation to avail this feature';
            }
            $pluginaction = '<a data-toggle="tooltip" title="'.$tooltipmessage.'" style="color:#008000;opacity: 0.2;" href="?module=features&amp;action=addplugin&amp;data='.$plugin.'&amp;ts='.$ts.'" class="'.$notavailable.'" id="oneonone_'.$plugin.'"><i class="fa fa-lg fa-user"></i></a>';
            $oneononeactive = "";
            if (in_array($plugin, $plugins)) {
                $oneononeactive = "active";
                $pluginaction = '<a data-toggle="tooltip" title="Remove From One-on-one Chat" style="color:#008000;" href="javascript:void(0)" onclick="javascript:plugins_removeplugin(\''.$no.'\')" id="oneonone_'.$plugin.'"><i class="fa fa-lg fa-user"></i></a>';
            }

            $crpluginaction = '<a data-toggle="tooltip" title="'.$tooltipmessagegrp.'" style="color:#008000;opacity: 0.2;" href="?module=features&amp;action=addchatroomplugin&amp;data='.$plugin.'&amp;ts='.$ts.'" class="'.$notavailable.'" id="group_'.$plugin.'"><i class="fa fa-lg fa-users"></i></a>';
            $cractive = '';
            if (in_array($plugin, $crplugins)) {
                $cractive = 'active';;
                $crpluginaction = '<a title="Remove From Group Chat" data-toggle="tooltip" style="color:#008000;opacity:15;" href="javascript:void(0)" onclick="javascript:plugins_removechatroomplugin(\''.$no.'\')" id="group_'.$plugin.'"><i class="fa fa-lg fa-users"></i></a>';
            }

            if($plugininfo[1] === 2){
                $pluginaction = '';
            }

            if($plugininfo[1] === 1){
                $crpluginaction = '';
            }

            $list = "con_feture_list";
            if (in_array($plugin, $chatfeturs)) {
                $list = "chat_feture_list";
            }
            $upgradelink = '';
            if((function_exists('checkplan') && checkplan('plugins',$plugin) != 1)) {
                $config = '';
                $upgradelink = 'style="cursor:pointer;" onclick="javascript:window.open(\''.$redirecturl.'\');"';
                $pluginaction = '<a data-toggle="tooltip" title="Please upgrade your plan" style="color:green;opacity:0.2;"><i class="fa fa-lg fa-user"></i></a>';
                $crpluginaction = '<a title="Please upgrade your plan" data-toggle="tooltip" style="color:green;opacity:0.2;"><i class="fa fa-lg fa-users"></i></a>';
            }

            if ($ti == "report" || $ti == "block") {
               $crpluginaction = "";
            }

            ${$list} .= '<tr '.$upgradelink.' group="'.$cractive.'" oneonone="'.$oneononeactive.'" id="'.$no.'" d1="'.$ti.'" rel="'.$ti.'"><td>'.ucwords($plugininfo[0]).'</td><td>'.$config.'</td><td align="center">'.$pluginaction.'</td><td align="center">'.$crpluginaction.'</td></tr>';
    }
/* START: Modules*/
    global $trayicon;
    $modules_core = setConfigValue('modules_core',array());
    if (empty($trayicon)) {
        $trayicon = array();
    }
    if (checkLicenseVersion()) {
        $hideconfig[] = "realtimetranslate";
    }
    unset($modules_core['themechanger']);
    unset($modules_core['chatrooms']);
    $moduleslist = $mactive ='';
    $embeddableModules = array();
        foreach ($modules_core as $module => $moduleinfo) {
            $ti = $moduleinfo;
            $ti[2] = (empty($ti[2])) ? '' : $ti[2];
            $ti[3] = (empty($ti[3])) ? '' : $ti[3];
            $ti[4] = (empty($ti[4])) ? '' : $ti[4];
            $ti[5] = (empty($ti[5])) ? '' : $ti[5];
            $ti[6] = (empty($ti[6])) ? '' : $ti[6];
            $ti[7] = (empty($ti[7])) ? '' : $ti[7];
            if (empty($ti[8])) {
                $ti[8] = '';
                $showhide = 'Show';
                $opacity='1';
            } else {
                $showhide = 'Hide';
                $opacity='0.5';
            }


                $titles[$module] = $moduleinfo;
                $modulehref = "";
                if (empty($trayicon[$module])) {
                    $modulehref = 'href="?module=features&action=addmodule&data='.base64_encode("\$trayicon['".$module."']=array('".implode("','", $moduleinfo)."');").'&ts='.$ts.'"';
                }

            $config = '';

            if (file_exists(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$ti[0].DIRECTORY_SEPARATOR.'settings.php') && !in_array($ti[0], $hideconfig)) {
                $config = '<a style="color:black;" data-toggle="tooltip" title="Configure" href="javascript:void(0)" onclick="javascript:modules_configmodule(\''.$ti[0].'\')"><i class="fa fa-lg fa-cogs"></i></a>';
            } else {
                $config = '';
            }

            $title = stripslashes($ti[1]);

            if (!empty($ti[7])) {
                $visible = "style=\"margin-left:5px;visibility:visible;\"";
            } else {
                $visible = "style=\"margin-left:5px;visibility:hidden;\"";
            }

            if (!empty($ti[9])) {
                $custom = $ti[9];
            } else {
                $custom = 0;
            }

            if (!empty($trayicon[$module])) {
               $mactive = ' active';
               $removemodule = '<a data-toggle="tooltip" title="Remove Feature" href="javascript:void(0)" onclick="javascript:modules_removemodule(\''.$ti[0].'\',\''.$custom.'\',this)" style="color:red;"><i class="fa fa-lg fa-minus-circle"></i></a>';
            } else {
                $mactive = '';
                $notavailable = '';
                $tooltipmessage = 'Add Feature';
                if(!file_exists(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$ti[0])){
                    $notavailable = 'feature-unavailable';
                    $tooltipmessage = 'Please force update your CometChat installation to avail this feature';
                }
                $removemodule = '<a data-toggle="tooltip" title="'.$tooltipmessage.'" '.$modulehref.' class="'.$notavailable.'" style="color:green;"><i class="fa fa-lg fa-plus-circle"></i></a>';
            }
            $embedlink = '';
            if(in_array($module, $embeddableModules)){
                $embedlink = '<a style="color:black;" data-toggle="tooltip" title="Embedded code" onclick="javascript:embed_link(\''.BASE_URL.''.$ti[2].'\',\''.$ti[4].'\',\''.$ti[5].'\');" href="javascript:void(0)" '.$visible.'><i class="fa fa-lg fa-code"></i></a>';
            }
            $title = ($title == 'Chatrooms') ?  'Groups' :  $title;
            $upgradelink = '';
            if((function_exists('checkplan') && checkplan('modules',$module) != 1)) {
                $config = $embedlink = '';
                $upgradelink = 'onclick="javascript:window.open(\''.$redirecturl.'\');"';
                $removemodule = '<a data-toggle="tooltip" title="Please upgrade your plan" style="color:green;opacity:0.5;"><i class="fa fa-plus-circle"></i></a>';
            }

            $moduleslist .= '<tr '.$upgradelink.' type="module'.$mactive.'"  id="'.$ti[0].'" d1="'.addslashes($ti[1]).'" d2="'.$ti[2].'" d3="'.$ti[3].'" d4="'.$ti[4].'" d5="'.$ti[5].'" d6="'.$ti[6].'" d7="'.$ti[7].'" d8="'.$ti[8].'" ><td id="'.$ti[0].'_title">'.ucwords($title).'</td><td align="center">'.$embedlink.'</td><td align="center">'.$config.'</td><td align="center">'.$removemodule.'</td></tr>';
        }
/* END: Modules*/
/* START: Extensions*/
    global $extensions;
    $extensions_core = setConfigValue('extensions_core',array());
    /*Depricated Jabber*/
    unset($extensions_core['jabber']);
    $extensionslist = '';
    $ext_config = $ext_action = '';
    foreach ($extensions_core as $extension => $extensioninfo) {
        $ti = $extension;
        $title = ucwords($extensioninfo);
        if($extension == 'desktop' || $extension == 'mobileapp' || $extension == 'bots'){
            continue;
        }
        $ext_config = '';
        if (file_exists(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'extensions'.DIRECTORY_SEPARATOR.$ti.DIRECTORY_SEPARATOR.'settings.php') && !in_array($ti, $hideconfig)) {
            $ext_config = '<a data-toggle="tooltip" title="Configure" href="javascript:void(0)" onclick="javascript:extensions_configextension(\''.$ti.'\')" style="color:black;"><i class="fa fa-lg fa-cogs"></i></a>';
        }
        $notavailable = '';
        $tooltipmessage = 'Add feature';
        if(!file_exists(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'extensions'.DIRECTORY_SEPARATOR.$extension)){
            $notavailable = 'feature-unavailable';
            $tooltipmessage = 'Please force update your CometChat installation to avail this feature';
        }
        $extensionhref = 'href="?module=features&action=addextension&data='.$extension.'&ts='.$ts.'"';
        $ext_action = '<a data-toggle="tooltip" title="'.$tooltipmessage.'" '.$extensionhref.' class="'.$notavailable.'" style="color:green;"><i class="fa fa-lg fa-plus-circle"></i></a>';
        $extactive = "";
        if (in_array($extension, $extensions)) {
            $extactive = " active";
            $ext_action = '<a data-toggle="tooltip" title="Remove feature"  href="javascript:void(0)" onclick="javascript:extensions_removeextension(\''.$ti.'\',this)" style="color:red;"><i class="fa fa-lg fa-minus-circle"></i></a>';
        }
        $upgradelink = '';
        if((function_exists('checkplan') && checkplan('extensions',$extension) != 1)) {
            $ext_config = '';
            $upgradelink = 'onclick="javascript:window.open(\''.$redirecturl.'\');"';
            $ext_action = '<a data-toggle="tooltip" title="Please upgrade your plan" style="color:green;opacity:0.5;"><i class="fa fa-plus-circle"></i></a>';
        }

        $extensionslist .= '<tr '.$upgradelink.' type="extensions'.$extactive.'" id="'.$ti.'" d1="'.$ti.'" rel="'.$ti.'"><td id="'.$ti.'_title">'.stripslashes(ucwords($title)).'</td><td></td><td>'.$ext_config.'</td><td>'.$ext_action.'</td></tr>';
    }
/* END: Extensions*/
$body = <<<EOD
<div class="row featurelist">
  <div class="col-sm-12 col-lg-6">
    <div class="row">
        <div class="col-sm-12 col-lg-12">
            <div class="card">
              <div class="card-header">
                Conversation Features
              </div>
              <div class="card-block">
                <table class="table" id="conversion_feature_list">
                  <thead>
                    <tr>
                      <th>Feature</th>
                       <th width="10">&nbsp</th>
                       <th width="10">&nbsp;</th>
                       <th width="10">&nbsp;</th>
                    </tr>
                  </thead>
                  <tbody>
                    $con_feture_list
                  </tbody>
                </table>
              </div>
            </div>
        </div>
        <div class="col-sm-12 col-lg-12">
            <div class="card">
              <div class="card-header">
                Other Features
              </div>
              <div class="card-block">
                <table class="table" id="other_feature_list">
                  <thead>
                    <tr>
                      <th>Feature</th>
                      <th width="5">&nbsp</th>
                      <th width="5">&nbsp</th>
                      <th width="5">&nbsp</th>
                    </tr>
                  </thead>
                  <tbody>
                  {$moduleslist}
                  {$extensionslist}
                  </tbody>
                </table>
              </div>
    </div>
        </div>
    </div>
  </div>
    <div class="col-sm-12 col-lg-6">
    <div class="card">
      <div class="card-header">
        Chat Features
      </div>
      <div class="card-block">
        <table class="table" id="chat_feature_list">
          <thead>
            <tr>
              <th>Feature</th>
               <th width="10">&nbsp</th>
               <th width="10">&nbsp;</th>
               <th width="10">&nbsp;</th>
            </tr>
          </thead>
          <tbody>
          {$chat_feture_list}
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
EOD;

    template();
}

function updateorder(){
    updateFeatureOrder('plugin');
}

function addplugin(){
    global $ts;
    global $plugins;
    global $api_response;
    if ($_GET['data'] == "voicenote" && !in_array('filetransfer', $plugins)) {
        $_SESSION['cometchat']['type'] = 'alert';
        $_SESSION['cometchat']['error'] = 'Please Enable Send A File feature in One-on-one to use Voice Note feature';
        header("Location:?module=features&ts={$ts}");
        exit();
    }
    if (!empty($_GET['data']) && !in_array($_GET['data'], $plugins)) {
        array_push($plugins, $_GET['data']);
        configeditor(array('plugins' => $plugins));
        $_SESSION['cometchat']['error'] = 'Plugin successfully activated!';
    }
    header("Location:?module=features&ts={$ts}");
}


function updatechatroomorder(){
    updateFeatureOrder('crplugin');
}

function addchatroomplugin(){
    global $ts;
    global $crplugins;
    global $api_response;
    if ($_GET['data'] == "voicenote" && !in_array('filetransfer', $crplugins)) {
        $_SESSION['cometchat']['type'] = 'alert';
        $_SESSION['cometchat']['error'] = 'Please Enable Send A File feature in Group to use Voice Note feature';
        header("Location:?module=features&ts={$ts}");
        exit();
    }
    if (!empty($_GET['data']) && !in_array($_GET['data'], $crplugins)) {
        array_push($crplugins, $_GET['data']);
        configeditor(array('crplugins' => $crplugins));
        $_SESSION['cometchat']['error'] = 'Plugin successfully activated!';
    }
    header("Location:?module=features&ts={$ts}");
}

function addmodule() {
    global $ts;
    global $trayicon;
    global $api_response;

    if (!empty($_GET['data'])) {
        $data = base64_decode($_GET['data']);
        eval($data);
        configeditor(array('trayicon' => $trayicon));
        $_SESSION['cometchat']['error'] = 'Module successfully activated!';
    }
    header("Location:?module=features&ts={$ts}");
}

function removecustommodules() {
    if (!empty($_REQUEST['module'])) {
        $dir = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR.$_REQUEST['module'];
        $files = scandir($dir);
        foreach ($files as $num => $fname){
            if (file_exists("$dir/$fname")) {
                @unlink("$dir/$fname");
            }
        }
        rmdir("$dir");
    }
}

function updatemoduleorder() {
    if (!empty($_POST['order'])) {
        configeditor(array('trayicon' => $_POST['order']));
    }else{
        configeditor(array('trayicon' => array()));
    }
    echo "1";
}

function addextension() {
    global $ts;
    global $extensions;
    global $api_response;
    if (!empty($_GET['data']) && !in_array($_GET['data'], $extensions)) {
        array_push($extensions, $_GET['data']);
        configeditor(array('extensions' => $extensions));
        $_SESSION['cometchat']['error'] = 'Extension successfully activated!';
    }
    header("Location:?module=features&ts={$ts}");
}

function updateFeatureOrder($feature){
    if(!isset($_POST['order'])||empty($feature)){
        echo 0;
    }else{
        $arraytoupdate = array();
        if (!empty($_POST['order'])) {
            $arraytoupdate = $_POST['order'];
        }
        configeditor(array($feature.'s' => $arraytoupdate));
        echo '1';
    }
}
function updateextensionorder() {
    updateFeatureOrder('extension');
}
