<?php

if(!isset($errors)){
   $errors='';
}

$content = <<<EOD
DELETE FROM [cometchat_settings] WHERE setting_key = 'mobile_settings';

IF ( EXISTS (SELECT * FROM [cometchat_settings] WHERE setting_key = 'dbversion'))
begin
  UPDATE [cometchat_settings] set value = '21' WHERE setting_key = 'dbversion'
end
else
begin
  INSERT cometchat_settings ([setting_key], [value], [key_type]) VALUES ('dbversion','21',1)
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
