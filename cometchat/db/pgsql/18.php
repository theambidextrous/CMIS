<?php

$content = <<<EOD
ALTER TABLE cometchat_status
ADD COLUMN readreceiptsetting int NOT NULL default '0';

CREATE TABLE IF NOT EXISTS cometchat_recentconversation (
	convo_id varchar(100) NOT NULL,
	id integer NOT NULL,
	"from" integer NOT NULL,
	"to" integer NOT NULL,
	message text NOT NULL,
	sent varchar(100) NOT NULL
);

ALTER TABLE cometchat_recentconversation
ADD CONSTRAINT convo_id UNIQUE (id,"from","to",message,sent);

"WITH upsert AS (UPDATE cometchat_settings SET value = '18', key_type = '1' WHERE setting_key = 'dbversion' RETURNING *) INSERT INTO cometchat_settings (setting_key , value, key_type) SELECT 'dbversion', '18', '1' WHERE NOT EXISTS (SELECT * FROM upsert);";
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
