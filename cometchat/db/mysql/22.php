<?php

$content = <<<EOD
ALTER TABLE cometchat_chatrooms MODIFY invitedusers TEXT;

REPLACE INTO `cometchat_settings` (setting_key,value,key_type) values ('dbversion','22','1');

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
