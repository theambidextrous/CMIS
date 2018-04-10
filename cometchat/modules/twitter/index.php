<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."modules.php");

include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."config.php");

if(!checkMembershipAccess('twitter','modules')){exit();}
if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php")){
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php");
}

require_once("twitteroauth/twitteroauth.php"); //Path to twitteroauth library

$connection = new TwitterOAuth($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);
function auto_link_text($text) {
   $pattern  = '#\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))#';
   $callback = create_function('$matches', '
       $url       = array_shift($matches);

       return sprintf(\'<a target="_blank" href="%s">%s</a>\', $url, $url);
   ');

   return preg_replace_callback($pattern, $callback, $text);
}
$followers = $connection->get("https://api.twitter.com/1.1/followers/list.json?cursor=-1&screen_name=".$twitteruser."&count=48");
$followersHTML = '';
$tweetsHTML = '';

if(isset($followers->errors)) {
	echo "<div style='background: white;'>Please configure this module using CometChat Administration Panel.</div>"; exit;
} else {
	foreach ($followers->users as $follower) {
		$followersHTML .= '<a target="_blank" href="http://www.twitter.com/'.$follower->screen_name.'"><img width=24 height=24 src="'.str_replace('normal', 'mini', $follower->profile_image_url).'" alt="'.$follower->name.'" title="'.$follower->name.'"></a>';
	}

	$tweets = $connection->get("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=".$twitteruser."&count=".$notweets);

	foreach ($tweets as $tweet) {
		$tweetsHTML .= '<li class="tweet">'.auto_link_text($tweet->text).'<br /><small class="chattime" timestamp="'.strtotime($tweet->created_at).'"></small></li>';

	}
}

$extrajs = '';
if ($sleekScroller == 1) {
	$extrajs =  getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'scroll', 'ext' => 'js'));
}

$cc_layout = 'docked';
if(!empty($_REQUEST['cc_layout'])){
  $cc_layout = $_REQUEST['cc_layout'];
}
$jquerytag = getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'jquery', 'ext' => 'js'));
$twitterjstag = getDynamicScriptAndLinkTags(array('type' => "module",'name' => 'twitter', 'ext' => 'js'));
$twittercsstag = getDynamicScriptAndLinkTags(array('type' => "module",'name' => 'twitter','ext' => 'css'));
$staticCDNUrl = STATIC_CDN_URL;
echo <<<EOD
<!DOCTYPE html>
<html>
	<head>
		<title>{$twitter_language[100]}</title>
		<meta name="viewport" content="user-scalable=1,width=device-width, initial-scale=1.0" />
		<meta http-equiv="cache-control" content="no-cache">
		<meta http-equiv="pragma" content="no-cache">
		<meta http-equiv="expires" content="-1">
		<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
		{$twittercsstag}
		{$jquerytag}
		<script>$ = jQuery = jqcc;</script>
		{$twitterjstag}
		{$extrajs}
		<script>
		$(function() {
			var content_width = '323px';
			var content_height = '310px';
			var tweets_height = '290px';
			if (jQuery().slimScroll) {
				if("{$cc_layout}"=="embedded"){
					content_width = $('.cometchat_wrapper').width()+'px';
					tweets_height = content_height = $('.cometchat_wrapper').outerHeight()-$('.followme').outerHeight()-5+'px';
				}
				$('#tweets').slimScroll({height: content_height,width: content_width,allowPageScroll: false});
				$("#tweets").css("height",tweets_height);
				$('#tweets_wrapper').css("height",content_height);
			}
		});
		</script>
	</head>
	<body>
		<div style="width:100%;margin:0 auto;margin-top: 0px;height: 100%;">
			<div class="cometchat_wrapper">
				<div class="followme">
					<a target="_blank" href="http://www.twitter.com/{$twitteruser}"><img src="{$staticCDNUrl}modules/twitter/layouts/{$layout}/images/follow.png"></a><br>
					<div id="followers">{$followersHTML}</div>
				</div>
				<div id="tweets_wrapper" style="width: 324px;height:300px;overflow:auto">
					<ul id="tweets" style="width: auto;">
						{$tweetsHTML}
					</ul>
				</div>
				<div style="clear:both"></div>
			</div>
		</div>
	</body>
</html>
EOD;
?>
