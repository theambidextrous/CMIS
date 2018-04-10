<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins.php");

if( checkplan('plugins','save') == 0){ exit;}
if(!checkMembershipAccess('save','plugins')){exit();}

$log = '';
$filename = $_POST['filename'];
$log .= $filename;
$log .= "\r\n------------------------------------------------------------------\r\n";
$log .= $_POST['content'];
header('Content-Description: File Transfer');
header('Content-Type: application/force-download');
header('Content-Disposition: attachment; filename="'.$filename.'.txt"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
echo $log;
