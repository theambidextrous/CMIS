<?php
session_start();
	# Load includes
	$incl_dir = "../includes";
	$class_dir = "../classes";
	
	include "$incl_dir/config.php";
    require_once("portal.functions.php");
    activeSession(LIFE);
    
    ?>