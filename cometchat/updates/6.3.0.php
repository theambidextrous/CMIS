<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'config.php');
cometchatDBConnect();

$content = sql_getQuery('update_content_630');

$q = preg_split('/;[\r\n]+/',$content);

foreach ($q as $query) {
	if (strlen($query) > 4) {
		$result = sql_query($query, array(), 1);
		if (!$result) {
			$rollback = 1;
			$errors .= sql_error($GLOBALS['dbh'])."<br/>\n";
		}
	}
}

?>
