<?php

if(!isset($errors)){
   $errors='';
}

$content = <<<EOD
ALTER TABLE `cometchat_chatrooms` ADD `createdon` INT(11) NOT NULL AFTER `createdby`;
ALTER TABLE `cometchat_chatrooms_archive` ADD `createdon` INT(11) NOT NULL AFTER `createdby`;
CREATE table cometchat_status_archive LIKE cometchat_status;

REPLACE INTO `cometchat_settings` (setting_key,value,key_type) values ('dbversion','30','1');

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
