<?php

$settingnames = array(
   'theme' => array('docked',1),
   'color' => array('color1',1)
);

if(!isset($error)){
   $error='';
}

$sql = ("select * from `cometchat_settings`");
if($query = sql_query($sql, array(), 1)){
   $error .= sql_error($dbh);
}

while($oldsettings = sql_fetch_assoc($query)){
   if(array_key_exists($oldsettings['setting_key'], $settingnames)) {
      $settingsarray = array();
      $setting_key = $oldsettings['setting_key'];
      $value = $settingnames[$setting_key][0];
      if(!empty($value)) {
         $sql = ("insert into `cometchat_settings` set `setting_key`='".$setting_key."',`value`='".$value."',`key_type`='".$settingnames[$setting_key][1]."' on duplicate key update `value`='".$value."',`key_type`='".$settingnames[$setting_key][1]."'");
         if(!sql_query($sql,array(),1)){
            $error .= sql_error($dbh);
         }
      }
   }
}

$sql = ("REPLACE INTO `cometchat_settings` (setting_key,value,key_type) values ('dbversion','17','1')");
if(!sql_query($sql, array(), 1)){
   $error .= sql_error($dbh);
}
removeCachedSettings($client.'settings');
