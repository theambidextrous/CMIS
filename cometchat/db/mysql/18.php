<?php

$content = <<<EOD
ALTER TABLE `cometchat_status`
add column(
`readreceiptsetting` int(1) unsigned NOT NULL default '1'
);

CREATE TABLE IF NOT EXISTS `cometchat_recentconversation` (
`convo_id` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
`id` int(10) unsigned NOT NULL,
`from` int(10) unsigned NOT NULL,
`to` int(10) unsigned NOT NULL,
`message` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
`sent` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
UNIQUE KEY `convo_id` (`convo_id`),
KEY `fromid` (`from`),
KEY `toid` (`to`),
KEY `fromid_toid` (`from`,`to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

REPLACE INTO `cometchat_settings` (setting_key,value,key_type) values ('dbversion','18','1');

EOD;
$q = preg_split('/;[\r\n]+/',$content);
if(!isset($errors)){
   $errors='';
}
foreach ($q as $query) {
  if (strlen($query) > 4) {
    if (!sql_query($query, array(), 1)) {
      $errors .= sql_error($dbh)."<br/>\n";
    }
  }
}
removeCachedSettings($client.'settings');
