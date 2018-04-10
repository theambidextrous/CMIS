<?php

$content = <<<EOD

ALTER TABLE cometchat_chatrooms ALTER COLUMN invitedusers type text;

WITH upsert AS (UPDATE cometchat_settings SET value = '22', key_type = '1' WHERE setting_key = 'dbversion' RETURNING *) INSERT INTO cometchat_settings (setting_key , value, key_type) SELECT 'dbversion', '22', '1' WHERE NOT EXISTS (SELECT * FROM upsert);
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
