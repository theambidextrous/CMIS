<?php

if(!isset($errors)){
   $errors='';
}

$content = <<<EOD

ALTER TABLE `cometchat_languages` DROP INDEX `lang_index`, ADD UNIQUE `lang_index` (`lang_key`(50), `code`, `type`, `name`(20)) USING BTREE;

ALTER TABLE `cometchat_languages`
ENGINE = MYISAM;

REPLACE INTO `cometchat_settings` (setting_key,value,key_type) values ('dbversion','28','1');

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
