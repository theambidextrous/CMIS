<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

if (!defined('CCADMIN')) { echo "NO DICE"; exit; }
global $client, $cms, $cmswithfriends, $MsgCnt,$api_response;
$MsgCnt = isset($_SESSION['cometchat']['MsgCnt']) ? $_SESSION['cometchat']['MsgCnt'] : 0;
$botsvg = '<svg style="position:relative;left:2px;top:2px;" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 475.764 475.764" style="enable-background:new 0 0 475.764 475.764;" xml:space="preserve" width="16px" height="16px">
  <g>
	<path d="M421.201,87.097c20.631-3.57,36.376-21.586,36.376-43.221C457.577,19.683,437.894,0,413.701,0     c-24.193,0-43.876,19.683-43.876,43.876c0,21.635,15.745,39.651,36.376,43.221v34.156h-88.066V66.689H157.63v54.564H69.564     V87.097c20.631-3.57,36.376-21.586,36.376-43.221C105.94,19.683,86.258,0,62.064,0C37.87,0,18.188,19.683,18.188,43.876     c0,21.635,15.745,39.651,36.376,43.221v125.097H12.125v87.752h42.439v175.818H421.2V299.946h42.438v-87.752h-42.437V87.097z      M54.564,284.946H27.125v-57.752h27.439V284.946z M172.63,81.689h57.752v22.813h15V81.689h57.752v39.564H172.63V81.689z      M33.188,43.876C33.188,27.954,46.142,15,62.064,15S90.94,27.954,90.94,43.876S77.987,72.752,62.064,72.752     C46.141,72.752,33.188,59.798,33.188,43.876z M69.564,460.765V136.254H406.2v324.511H69.564z M413.7,72.752     c-15.922,0-28.876-12.954-28.876-28.876S397.778,15,413.7,15s28.876,12.954,28.876,28.876S429.622,72.752,413.7,72.752z      M448.639,227.194v57.752h-27.438v-57.752H448.639z" fill="#222222"/>
	<path d="M209.006,237.883c0-30.88-25.122-56.002-56.001-56.002c-30.879,0-56.001,25.122-56.001,56.002     c0,30.879,25.122,56.001,56.001,56.001C183.884,293.884,209.006,268.762,209.006,237.883z M112.003,237.883     c0-22.608,18.393-41.002,41.001-41.002c22.608,0,41.001,18.394,41.001,41.002c0,22.608-18.393,41.001-41.001,41.001     C130.396,278.884,112.003,260.491,112.003,237.883z" fill="#222222"/>
	<path d="M322.76,181.881c-30.879,0-56.001,25.122-56.001,56.002c0,30.879,25.122,56.001,56.001,56.001     c30.879,0,56.001-25.122,56.001-56.001C378.762,207.003,353.64,181.881,322.76,181.881z M322.76,278.884     c-22.608,0-41.001-18.393-41.001-41.001c0-22.608,18.393-41.002,41.001-41.002c22.608,0,41.001,18.394,41.001,41.002     C363.761,260.491,345.369,278.884,322.76,278.884z" fill="#222222"/>
	<path d="M334.886,351.636H140.879c-10.821,0-19.625,8.804-19.625,19.626v24.251c0,10.821,8.804,19.625,19.625,19.625h194.006     c10.821,0,19.625-8.804,19.625-19.625v-24.251C354.511,360.439,345.707,351.636,334.886,351.636z M334.886,400.138H140.879     c-2.507,0-4.625-2.118-4.625-4.625v-24.251c0-2.508,2.118-4.626,4.625-4.626h194.006c2.507,0,4.625,2.118,4.625,4.626v24.251     h0.001C339.511,398.02,337.393,400.138,334.886,400.138z" fill="#222222"/>
  </g>
</svg>';

$nav = array(
  'Dashboard'   => array(
	'type'      => 'nav-item',
	'icon'      => 'icon-home'
  ),
  'FAVORITES'   => array(
	'type'      => 'nav-title'
  ),
  'Install_top'   => array(
	'type'      => 'nav-item',
	'icon'      => 'icon-wrench'
  ),
  'Announcements' => array(
	'type'        => 'nav-item',
	'icon'        => 'icon-volume-2'
  ),
  'Groups'      => array(
	'type'      => 'nav-item',
	'icon'      => 'icon-people',
  ),
  'Monitor'     => array(
	'type'      => 'nav-item',
	'icon'      => 'icon-directions'
  ),
  'Clear Cache'     => array(
	'type'      => 'nav-item',
	'icon'      => 'fa fa-cogs',
	'link'      => '?module=settings&action=clearcachefilesprocess&ts='.$ts
  ),
  'Customize & Install' => array(
	'type'      => 'nav-title'
  ),
  'Bots'    => array(
	'type'  => 'nav-item',
	'icon'  => 'fa fa-meh-o',
	'svg'   => $botsvg,
	'link'  => is_dir(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'extensions'.DIRECTORY_SEPARATOR.'bots') ? '?module=bots&ts='.$ts : 'https://www.cometchat.com/buy',
	'target'  => is_dir(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'extensions'.DIRECTORY_SEPARATOR.'bots') ? '' : '_blank',
  ),
	'Appearance'    => array(
	'type'          => 'nav-item',
	'icon'          => 'icon-pencil',
    'link'          => '?module=appearance&ts='.$ts
  ),
	'Features'     => array(
	'type'         => 'nav-item',
	'icon'         => 'icon-equalizer'
  ),
  'Localize'    => array(
	'type'      => 'nav-item',
	'icon'      => 'fa fa-language'
  ),
  'Logs'    => array(
	'type'      => 'nav-item',
	'icon'      => 'icon-layers',
	'link' => '?module=logs&ts='.$ts
  ),
  'Settings'    => array(
	'type'      => 'nav-item nav-dropdown',
	'icon'      => 'icon-settings',
	'suboption' => array(
        'General' => array('type' => 'nav-item','icon' => '','id' => 'general','link' => '?module=settings&action=generalsettings&ts='.$ts),
        'API Key' => array('type' => 'nav-item','icon' => '','id' => 'apikey','link' => '?module=settings&action=apikey&ts='.$ts),
        'Web'     => array('type' => 'nav-item','icon' => '','id' => 'web','link' => '?module=settings&ts='.$ts),
		'Mobile'  => array('type' => 'nav-item','icon' => '','id' => 'mobile','link' => '?module=settings&action=mobile&ts='.$ts),
		'Desktop' => array('type' => 'nav-item','icon' => '','id' => 'desktop','link' => '?module=settings&action=desktop&ts='.$ts),
		'CometService' => array('type' => 'nav-item','icon' => '','id' => 'comet','link' => '?module=settings&action=comet&ts='.$ts),
		'Install CometChat' => array('type' => 'nav-item','icon' => '','id' => 'install','link' => '?module=install&ts='.$ts),
		'Editor'    => array('type' => 'nav-item','icon' => '','id' => 'editor','link' => '?module=editor&ts='.$ts),
		'Clean Up'    => array('type' => 'nav-item','icon' => '','id' => 'cron','link' => '?module=settings&action=cron&ts='.$ts)
	),
  ),
  'More'   => array(
  'type'   => 'nav-title',
  ),
  'Documentation'     => array(
	'type'      => 'nav-item',
	'icon'      => 'icon-docs',
	'target'    => '_blank',
	'link'      => 'https://docs.cometchat.com'
  ),
  'Support'     => array(
	'type'      => 'nav-item',
	'icon'      => 'icon-support',
	'target'    => '_blank',
	'link'      => 'https://support.cometchat.com'
  ),
  'Logout'    => array(
	'type'      => 'nav-item',
	'icon'      => 'fa fa-power-off'
  )
);
if(!empty($api_response['cometservice'])){
	unset($nav['Settings']['suboption']['CometService']);
}
if ($MsgCnt == 0) {
	unset($nav['Settings']['suboption']['Install CometChat']);
} else {
	unset($nav['Install_top']);
}
if (method_exists($GLOBALS['integration'], 'isPluginActive') && $GLOBALS['integration']->isPluginActive('buddypress')) {
	$nav['Settings']['suboption']['Inbox Sync'] = array('type' => 'nav-item','icon' => '','id' => 'ccinboxsync','link' => '?module=settings&action=ccinboxsync&ts='.$ts);
}
if ((defined('ROLE_BASE_ACCESS') && ROLE_BASE_ACCESS == 1)) {
	$nav['Settings']['suboption']['Role Based Access'] = array('type' => 'nav-item','icon' => '','id' => '','link' => '?module=membership&ts='.$ts);
}
if(!empty($client)) {
  unset($nav['Settings']['suboption']['CometService']);
  unset($nav['Settings']['suboption']['Clean Up']);
}
$tab = '';
foreach ($nav as $key => $value) {
  $key = ($key == "Install_top") ? "Install CometChat" : $key;
  $type = $value['type'];
  $tabslug = strtolower($key);
  $tabslug = str_replace(" ","",$tabslug);
  $tabslug = str_replace("/","",$tabslug);
  $tabslug = ($tabslug == 'installcometchat') ? 'install' : $tabslug;
  $href    = '?module='.$tabslug.'&ts='.$ts;
  $open    = ($module == $tabslug) ? 'open' : '';
  $status  = ($module == $tabslug) ? 'active' : '';
  $open    = ($module == 'editor') ? 'open' : $open;
  $open    = ($module == 'membership') ? 'open' : $open;
  $open    = ($module == 'install' && $MsgCnt > 0) ? 'open' : $open;
  $href    = (!isset($value['link']) && empty($value['link'])) ? $href : $value['link'];
  $target  = (isset($value['target'])) ? 'target="'.$value['target'].'"' : '';
  if ($type == 'nav-title') {
	  $tab .='<li class="'.$type.'">'.$key.'</li>';
  } else if($type == 'nav-item') {
	  $icon = $value['icon'];
	  $svg  = '';
	  if (!empty($value['svg'])) {
		$icon = "";
		$svg  = $value['svg'];
	  }
      $tab .='<li class="nav-item"><a '.$target.' class="nav-link '.$status.'" href="'.$href.'"><i class="'.$icon.'">'.$svg.'</i> '.$key.'</a></li>';
  } else {
	  $icon = $value['icon'];
	  $suboption = $value['suboption'];
	  $tab .='<li class="nav-item nav-dropdown '.$open.'">
		  <a class="nav-link nav-dropdown-toggle" href="#"><i class="'.$icon.'"></i> '.$key.'</a>
		  <ul class="nav-dropdown-items">';
	  $temp = '';
	  foreach ($suboption as $k => $val) {
		$active = ($val['id']==$action)? "active" : '';
		$icon = $val['icon'];
		$link = (!isset($val['link']) && empty($val['link'])) ? $href : $val['link'];
		$temp .='<li class="nav-item"><a '.$target.' id="'.$val['id'].'" class="nav-link '.$active.'" href="'.$link.'"><i class="'.$icon.'"></i> '.$k.'</a></li>';
	  }
	  $tab .= $temp.'</ul></li>';
  }

}
$navigationbar = <<<EOD
 <nav class="sidebar-nav">
	  <ul class="nav">
	  {$tab}
	  </ul>
	</nav>
EOD;
