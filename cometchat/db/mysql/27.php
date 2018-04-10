<?php

if(!isset($errors)){
   $errors='';
}

$content = <<<EOD

ALTER TABLE `cometchat_users` ADD INDEX(`username`);
ALTER TABLE `cometchat_users` ADD INDEX(`role`);
ALTER TABLE `cometchat_users` ADD INDEX(`uid`);
ALTER TABLE `cometchat_status` ADD INDEX(`status`);
ALTER TABLE `cometchat_status` ADD INDEX(`lastactivity`);
ALTER TABLE `cometchat_status` ADD INDEX(`isdevice`);
ALTER TABLE `cometchat_recentconversation` ADD INDEX(`from`);
ALTER TABLE `cometchat_recentconversation` ADD INDEX(`to`);
ALTER TABLE `cometchat_chatrooms_users` ADD INDEX(`isbanned`);
ALTER TABLE `cometchat_chatrooms` ADD INDEX(`guid`);

REPLACE INTO `cometchat_settings` (setting_key,value,key_type) values ('dbversion','27','1');

EOD;
$q = preg_split('/;[\r\n]+/',$content);
foreach ($q as $query) {
  if (strlen($query) > 4) {
    if (!sql_query($query, array(), 1)) {
      $errors .= sql_error($dbh)."<br/>\n";
    }
  }
}
removeCachedSettings($client.'settings');
