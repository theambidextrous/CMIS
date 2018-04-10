<?php

$settingnames = array(
   'theme' => array('docked',1),
   'color' => array('color1',1)
);

if(!isset($error)){
   $error='';
}

$sql = ("select * from cometchat_settings");
if($query = sqlsrv_query($GLOBALS['dbh'],$sql)){
   $error .= sqlsrv_error($GLOBALS['dbh']);
}

while($oldsettings = sqlsrv_fetch_array($query)){
   if(array_key_exists($oldsettings['setting_key'], $settingnames)) {
      $settingsarray = array();
      $setting_key = $oldsettings['setting_key'];
      $value = $settingnames[$setting_key][0];
      if(!empty($value)) {
        $sql = "IF (EXISTS (SELECT * FROM [cometchat_settings] WHERE setting_key = '".$setting_key."'))
        begin
          UPDATE [cometchat_settings] SET value = '".$value."'  WHERE setting_key = '".$setting_key."'
        end
        else
        begin

          INSERT INTO [cometchat_settings] (setting_key, value, key_type) VALUES ('".$setting_key."', '".$value."', '".$settingnames[$setting_key][1]."')
        end ";       
        if(!sqlsrv_query($GLOBALS['dbh'],$sql)){
            $error .= sqlsrv_error($GLOBALS['dbh']);
         }
      }
   }
}

$sql = "IF ( EXISTS (SELECT * FROM [cometchat_settings] WHERE setting_key = 'dbversion'))
begin
  UPDATE [cometchat_settings] set value = '17' WHERE setting_key = 'dbversion'
end
else
begin
  INSERT cometchat_settings ([setting_key], [value], [key_type]) VALUES ('dbversion','17',1)
end";

if(!sqlsrv_query($GLOBALS['dbh'],$sql)){
   $error .= sqlsrv_error($GLOBALS['dbh']);
}
removeCachedSettings($client.'settings');
