<?php

if(!isset($errors)){
   $errors='';
}

$content = <<<EOD

SELECT * INTO cometchat_archive FROM cometchat;
SELECT * INTO cometchat_guests_archive FROM cometchat_guests;
SELECT * INTO cometchat_chatroommessages_archive FROM cometchat_chatroommessages;
SELECT * INTO cometchat_chatrooms_archive FROM cometchat_chatrooms;
SELECT * INTO cometchat_chatrooms_users_archive FROM cometchat_chatrooms_users;

IF ( EXISTS (SELECT * FROM [cometchat_settings] WHERE setting_key = 'dbversion'))
begin
  UPDATE [cometchat_settings] set value = '29' WHERE setting_key = 'dbversion'
end
else
begin
  INSERT cometchat_settings ([setting_key], [value], [key_type]) VALUES ('dbversion','29',1)
end;

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
