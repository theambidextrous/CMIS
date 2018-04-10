<?php

$settingnames = array(
   'trayicon' => array('trayicon',2),
   'extensions' => array('extensions',2),
   'theme' => array('docked',1),
   'color' => array('color1',1)
);

if(!isset($error)){
   $error='';
}
$sql = "select * from cometchat_settings";
if($query = sqlsrv_query($GLOBALS['dbh'],$sql)){
   $error .= sqlsrv_error($GLOBALS['dbh']);
}

while($oldsettings = sqlsrv_fetch_array($query)){
   if(array_key_exists($oldsettings['setting_key'], $settingnames)) {
        $settingsarray = array();
        $setting_key = $oldsettings['setting_key'];
        $value = $settingnames[$setting_key][0];
        if($settingnames[$setting_key][1] == 2 && !empty($oldsettings['value'])) {
         $check = 0;
         if($setting_key == 'trayicon' || $setting_key == 'extensions'){
            $settingsarray = unserialize($oldsettings['value']);
            $unsetplugins = array('themechanger','jabber','mobilewebapp');
            foreach ($settingsarray as $key => $value) {
               if(is_array($value)){
                  $search = $key;
               }else{
                  $search = $value;
                  $check = 1;
               }
               if(in_array($search, $unsetplugins)) {
                  unset($settingsarray[$key]);
               }
            }
         }
         if($check){
            $settingsarray = array_values($settingsarray);
         }
         $value = serialize($settingsarray);
        }
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
  UPDATE [cometchat_settings] set value = '15' WHERE setting_key = 'dbversion'
end
else
begin
  INSERT cometchat_settings ([setting_key], [value], [key_type]) VALUES ('dbversion','15',1)
end";

if(!sqlsrv_query($GLOBALS['dbh'],$sql)){
   $error .= sqlsrv_error($GLOBALS['dbh']);
}
removeCachedSettings($client.'settings');
