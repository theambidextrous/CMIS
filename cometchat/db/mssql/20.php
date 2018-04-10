<?php

$DB_NAME = DB_NAME;
$addColumn = "";
if(!isset($errors)){
   $errors='';
}
$content = <<<EOD
IF NOT EXISTS (SELECT
                     column_name
               FROM
                     INFORMATION_SCHEMA.columns
               WHERE
                     table_schema = '{DB_NAME}' and
                     table_name = 'cometchat_users'
                     and column_name = 'uid');

ALTER table cometchat_users ADD uid varchar(255) not null DEFAULT ''; 

IF ( EXISTS (SELECT * FROM [cometchat_settings] WHERE setting_key = 'dbversion'))
begin
  UPDATE [cometchat_settings] set value = '20' WHERE setting_key = 'dbversion'
end
else
begin
  INSERT cometchat_settings ([setting_key], [value], [key_type]) VALUES ('dbversion','20 ',1)
end;

EOD;
$q = preg_split('/;[\r\n]+/',$content);
if(!isset($errors)){
   $errors='';
}
foreach ($q as $query) {
  if (strlen($query) > 4) {
    if (!sqlsrv_query($GLOBALS['dbh'],$query)) {
      $errors .= sqlsrv_error($GLOBALS['dbh'])."<br/>\n";
    }
  }
}
removeCachedSettings($client.'settings');
