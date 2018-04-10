<?php

if(!isset($errors)){
   $errors='';
}

$content = <<<EOD

CREATE TABLE cometchat_status_archive ( like cometchat_status );
CREATE TABLE cometchat_guests_archive ( like cometchat_guests );
CREATE TABLE cometchat_chatroommessages_archive ( like cometchat_chatroommessages );
CREATE TABLE cometchat_chatrooms_archive ( like cometchat_chatrooms );
CREATE TABLE cometchat_chatrooms_users_archive ( like cometchat_chatrooms_users );

WITH upsert AS (UPDATE cometchat_settings SET value = '29', key_type = '1' WHERE setting_key = 'dbversion' RETURNING *) INSERT INTO cometchat_settings (setting_key , value, key_type) SELECT 'dbversion', '29', '1' WHERE NOT EXISTS (SELECT * FROM upsert);
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
