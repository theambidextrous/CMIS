<?php

if(!isset($errors)){
   $errors='';
}

$content = <<<EOD

WITH upsert AS (UPDATE cometchat_settings SET value = '28', key_type = '1' WHERE setting_key = 'dbversion' RETURNING *) INSERT INTO cometchat_settings (setting_key , value, key_type) SELECT 'dbversion', '28', '1' WHERE NOT EXISTS (SELECT * FROM upsert);
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
