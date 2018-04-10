<?php

$content = <<<EOD


DO
$$
BEGIN
IF not EXISTS (SELECT column_name
               FROM information_schema.columns
               WHERE table_schema='public' and table_name='cometchat_users' and column_name='roleid') THEN
alter table cometchat_users add column roleid varchar(512) default NULL ;
else
raise NOTICE 'Already exists';
END IF;
END
$$;

WITH upsert AS (UPDATE cometchat_settings SET value = '24', key_type = '1' WHERE setting_key = 'dbversion' RETURNING *) INSERT INTO cometchat_settings (setting_key , value, key_type) SELECT 'dbversion', '24', '1' WHERE NOT EXISTS (SELECT * FROM upsert);
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
