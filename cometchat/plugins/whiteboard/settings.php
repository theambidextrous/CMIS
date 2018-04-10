<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

if (!defined('CCADMIN')) { echo "NO DICE"; exit; }

if (empty($_GET['process'])) {
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'config.php');
echo <<<EOD
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="shortcut icon" href="images/favicon.ico">
  <title>Setting</title>
  {$GLOBALS['adminjstag']}
  {$GLOBALS['admincsstag']}
</head>
 <body class="navbar-fixed sidebar-nav fixed-nav" style="background-color: white;overflow-y:hidden;">
             <div class="col-sm-6 col-lg-6">
                <div class="card">
                <div class="card-block">
                 <form style="height:100%" action="?module=dashboard&action=loadexternal&type=plugin&name=whiteboard&process=true" method="post">

		            <div class="form-group row">
		            <div class="col-md-6">
		              <label for="ccyear">Width:</label>
		            </div>
		             <div class="col-md-6">
			              <input class="form-control" style="width:75%;" type="text"  name="whitebWidth" value="$whitebWidth">
		            </div>
		          </div>

		            <div class="form-group row">
		            <div class="col-md-6">
		              <label for="ccyear">Height:</label>
		            </div>
		             <div class="col-md-6">
			              <input class="form-control" style="width:75%;" type="text" name="whitebHeight" value="$whitebHeight">
		            </div>
		          </div>

                    <div class="row col-md-4" style="">
                       <input type="submit" value="Update Settings" class="btn btn-primary">
                    </div>
                    </form>
                </div>
                </div>
              </div>
EOD;
} else {
	configeditor($_POST);
	header("Location:?module=dashboard&action=loadexternal&type=plugin&name=whiteboard");
}
