<?php

if(!isset($errors)){
   $errors='';
}

$content = <<<EOD

ALTER TABLE cometchat_users ADD INDEX('username');
ALTER TABLE cometchat_users ADD INDEX('role');
ALTER TABLE cometchat_users ADD INDEX('uid');
ALTER TABLE cometchat_status ADD INDEX('status');
ALTER TABLE cometchat_status ADD INDEX('lastactivity');
ALTER TABLE cometchat_status ADD INDEX('isdevice');
ALTER TABLE cometchat_recentconversation ADD INDEX('from');
ALTER TABLE cometchat_recentconversation ADD INDEX('to');
ALTER TABLE cometchat_chatrooms_users ADD INDEX('isbanned');
ALTER TABLE cometchat_chatrooms ADD INDEX('guid');

WITH upsert AS (UPDATE cometchat_settings SET value = '27', key_type = '1' WHERE setting_key = 'dbversion' RETURNING *) INSERT INTO cometchat_settings (setting_key , value, key_type) SELECT 'dbversion', '27', '1' WHERE NOT EXISTS (SELECT * FROM upsert);
EOD;

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
