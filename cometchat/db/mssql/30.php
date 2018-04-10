<?php

if(!isset($errors)){
   $errors='';
}

$content = <<<EOD

ALTER TABLE [cometchat_chatrooms] ADD createdon INT;
ALTER TABLE [cometchat_chatrooms_archive] ADD createdon INT;
SELECT * INTO #cometchat_status_archive FROM cometchat_status;

IF ( EXISTS (SELECT * FROM [cometchat_settings] WHERE setting_key = 'dbversion'))
begin
  UPDATE [cometchat_settings] set value = '30' WHERE setting_key = 'dbversion'
end
else
begin
  INSERT cometchat_settings ([setting_key], [value], [key_type]) VALUES ('dbversion','30',1)
end;

EOD;
$q = preg_split('/;[\r\n]+/',$content);
foreach ($q as $query) {
  if (strlen($query) > 4) {
    if (!sqlsrv_query($GLOBALS['dbh'],$query)) {
      $errors .= sqlsrv_error($GLOBALS['dbh'])."<br/>\n";
    }
  }
}
removeCachedSettings($client.'settings');
