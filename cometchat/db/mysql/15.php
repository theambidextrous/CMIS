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
$sql = "select * from `cometchat_settings`";
if($query = sql_query($sql, array(), 1)){
   $error .= sql_error($dbh);
}

while($oldsettings = sql_fetch_assoc($query)){
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
         $sql = "insert into `cometchat_settings` set `setting_key`='".$setting_key."',`value`='".$value."',`key_type`='".$settingnames[$setting_key][1]."' on duplicate key update `value`='".$value."',`key_type`='".$settingnames[$setting_key][1]."'";
        if(!sql_query($sql, array(), 1)){
            $error .= sql_error($dbh);
         }
      }
   }
}
$sql = ("REPLACE INTO `cometchat_settings` (setting_key,value,key_type) values ('dbversion','15','1')");
if(!sql_query($sql, array(), 1)){
   $error .= sql_error($dbh);
}
removeCachedSettings($client.'settings');
