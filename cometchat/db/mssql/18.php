<?php
if(!isset($errors)){
   $error='';
}
$content = <<<EOD
ALTER TABLE cometchat_status
	ADD readreceiptsetting int DEFAULT '1' NOT NULL;

IF  NOT EXISTS (SELECT * FROM sys.objects
  WHERE object_id = OBJECT_ID(N'[dbo].[cometchat_recentconversation]') AND type in (N'U'))
BEGIN
CREATE TABLE [cometchat_recentconversation] (
  [convo_id] varchar(100) NOT NULL UNIQUE,
  [id] int NOT NULL,
  [from] int NOT NULL,
  [to] int NOT NULL,
  [message] text NOT NULL,
  [sent] varchar(100) NOT NULL
)
END;

IF ( EXISTS (SELECT * FROM [cometchat_settings] WHERE setting_key = 'dbversion'))
begin
  UPDATE [cometchat_settings] set value = '18' WHERE setting_key = 'dbversion'
end
else
begin
  INSERT cometchat_settings ([setting_key], [value], [key_type]) VALUES ('dbversion','18',1)
end;

EOD;
$q = preg_split('/;[\r\n]+/',$content);
if(!isset($errors)){
   $errors='';
}
foreach ($q as $query) {
  if (strlen($query) > 4) {
    if (!sqlsrv_query($GLOBALS['dbh'],$query)) {
      $error .= sqlsrv_error($GLOBALS['dbh'])."<br/>\n";
    }
  }
}
removeCachedSettings($client.'settings');
