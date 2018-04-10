<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."modules.php");
include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."config.php");

if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php")) {
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php");
}
if(!checkMembershipAccess('announcements','modules')){exit();}
function datify($ts) {
	if(!ctype_digit($ts)) {
		$ts = strtotime($ts);
	}
	$diff = time() - $ts;
	$date = date('l, F j, Y',$ts).' at '.date('g:ia',$ts);
	if($diff == 0) {
		return array ('now',$date);
	} elseif($diff > 0) {
		$day_diff = floor($diff / 86400);
		if($day_diff == 0) {
			if($diff < 60) return array('just now',$date);
			if($diff < 120) return array ('1 minute ago',$date);
			if($diff < 3600) return array (floor($diff / 60) . ' minutes ago',$date);
			if($diff < 7200) return array ('1 hour ago',$date);
			if($diff < 86400) return array (floor($diff / 3600) . ' hours ago',$date);
		}
		if($day_diff == 1) { return array ('Yesterday at '.date('g:ia',$ts),$date); }

		if (date('Y') == date('Y',$ts)) {
			return array (date('F jS',$ts).' at '.date('g:ia',$ts),$date);
		} else {
			return array (date('F jS, Y',$ts).' at '.date('g:ia',$ts),$date);
		}
	} else {
	return array (date('F jS, Y',$ts).' at '.date('g:ia',$ts),$date);
	}
}

$extra = "";

if (!empty($userid)) {
	$extra = sql_getQuery('announcement_datifyextra',array('userid'=>$userid));
}
$query = sql_query('announcement_datify',array('extra'=>$extra, 'limitClause'=>$noOfAnnouncements));

if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }

$announcementdata = '';
$announcementJson = array();
while ($announcement = sql_fetch_assoc($query)) {
	$time = $announcement['time'];
	$timehover=datify($time);
	$class = 'highlight';

	if ($announcement['to'] == 0 || $announcement['to'] == -1) {
		$class = '';
	}
	$ann = array();

	$ann['id'] =  $announcement['id'];
	$ann['m'] =  $announcement['announcement'];
	$ann['t'] =  $announcement['time'];

	$announcementJson["_".$announcement['id']] = $ann;
	$announcement['announcement'] = utf8_decode($announcement['announcement']);
	$announcement['announcement'] = str_replace(
        array('\r\n','\\'),
        array('<br>', ''),
        $announcement['announcement']
    );
	$announcementdata .= <<<EOD
	<li class="announcement"><span class="{$class}">{$announcement['announcement']}</span><div><small class="chattime" timestamp="{$timehover['0']}" timehover="{$timehover['1']}"></small></div><br/></li>
EOD;

}

if (empty($announcementdata)) {
	$announcementdata = '<li class="announcement no-announcement">'.$announcements_language[0].'</li>';
}

$extrajs = "";
if ($sleekScroller == 1) {
	$extrajs = getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'scroll', 'ext' => 'js'));
}

if(empty($_REQUEST['callbackfn']) || $_REQUEST['callbackfn']<>'mobileapp')
{

	$jQuerytag = getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'jquery', 'ext' => 'js'));
	$jstag = getDynamicScriptAndLinkTags(array('type' => "module",'name' => 'announcements', 'ext' => 'js'));
	$css = getDynamicScriptAndLinkTags(array('type' => "module",'name' => 'announcements','ext' => 'css'));

	if($layout == 'embedded'){
		echo <<<EOD
		<!DOCTYPE html>
		<html>
			<head>
				<title>{$announcements_language[100]}</title>
				<meta name="viewport" content="user-scalable=1,width=device-width, initial-scale=1.0" />
				<meta http-equiv="cache-control" content="no-cache">
				<meta http-equiv="pragma" content="no-cache">
				<meta http-equiv="expires" content="-1">
				<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
				$css
				$jQuerytag
				<script> $ = jQuery = jqcc;	</script>
				$jstag
				{$extrajs}
			</head>
			<body>
				<div style="width:100%;margin:0 auto;margin-top: 0px;height: 100%;overflow-y: auto;">
					<div class="cometchat_wrapper">
						<div class="announcements" style="width: 100%; height: 300px;overflow:auto">
							<ul>
								<ul>{$announcementdata}</ul>
							</ul>
						</div>
						<div style="clear:both">&nbsp;</div>
					</div>
				</div>
			</body>
		</html>
EOD;

	}else{
		echo <<<EOD
		<!DOCTYPE html>
		<html>
			<head>
				<title>{$announcements_language[100]}</title>
				<meta name="viewport" content="user-scalable=1,width=device-width, initial-scale=1.0" />
				<meta http-equiv="cache-control" content="no-cache">
				<meta http-equiv="pragma" content="no-cache">
				<meta http-equiv="expires" content="-1">
				<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
				$css
				$jQuerytag
				<script>
				  $ = jQuery = jqcc;
				</script>
				$jstag
				{$extrajs}
			</head>
			<body>
				<div style="width:100%;margin:0 auto;margin-top: 0px;height: 100%;overflow-y: auto;">
					<div class="cometchat_wrapper">
						<div class="announcements" style="width: 100%; height: 300px;overflow:auto">
							<ul>
								<ul>{$announcementdata}</ul>
							</ul>
						</div>
						<div style="clear:both">&nbsp;</div>
					</div>
				</div>
			</body>
		</html>
EOD;
}
} else{
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($announcementJson);
}
?>
