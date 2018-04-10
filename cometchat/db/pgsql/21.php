<?php

$content = <<<EOD
DELETE FROM cometchat_settings where setting_key='mobile_settings';


WITH upsert AS (UPDATE cometchat_settings SET value = '21', key_type = '1' WHERE setting_key = 'dbversion' RETURNING *) INSERT INTO cometchat_settings (setting_key , value, key_type) SELECT 'dbversion', '21', '1' WHERE NOT EXISTS (SELECT * FROM upsert);
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
