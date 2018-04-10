<?php

$DB_NAME = DB_NAME;
$addColumn = "";
if(!isset($errors)){
   $errors='';
}
$sql = "SELECT IF(count(*) = 1, 'Exist','Not Exist') AS result FROM information_schema.columns WHERE table_schema = '{$DB_NAME}'
    AND table_name = 'cometchat_users' AND column_name = 'uid'";

if($query = sql_query($sql,array(),1)){
   $errors .= sql_error($dbh);
}
$column = sql_fetch_assoc($query);

if ($column['result'] == 'Not Exist') {
   $addColumn =
   <<<EOD

		DO
		$$
		BEGIN
		IF not EXISTS (SELECT column_name
		               FROM information_schema.columns
		               WHERE table_schema='public' and table_name='cometchat_users' and column_name='uid') THEN
		alter table cometchat_users add column uid varchar(512) default NULL ;
		else
		raise NOTICE 'Already exists';
		END IF;
		END
		$$;

EOD;

}

$content = <<<EOD
{$addColumn}
WITH upsert AS (UPDATE cometchat_settings SET value = '20', key_type = '1' WHERE setting_key = 'dbversion' RETURNING *) INSERT INTO cometchat_settings (setting_key , value, key_type) SELECT 'dbversion', '20', '1' WHERE NOT EXISTS (SELECT * FROM upsert);
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
