<?php

$query = <<<EOD
ALTER TABLE cometchat_settings CHANGE name setting_key varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Configuration setting name. It can be PHP constant, variable or array',CHANGE value value text COLLATE utf8_unicode_ci NOT NULL COMMENT 'Value of the key.',ADD key_type tinyint(4) NOT NULL DEFAULT '1' COMMENT 'States whether the key is: 0 = PHP constant, 1 = atomic variable or 2 = serialized associative array.';

EOD;
if(!isset($errors)){
	$errors='';
}
if (!sql_query($query, array(), 1)) {
  $errors .= sql_error($dbh)."<br/>\n";
}

?>
