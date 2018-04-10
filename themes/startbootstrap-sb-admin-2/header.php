<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="keywords" content="<?=META_KEYS;?>">
<meta name="description" content="<?=META_DESC;?>">
<meta name="author" content="Sammy M. Waweru">
<meta name="second author" content="Iddris J. Otuya">
<title><?php echo add_title(SYSTEM_NAME, $PageTitle); ?></title>
<link rel="icon" type="image/png" href="<?=SYSTEM_LOGO_URL;?>" />
<!-- Bootstrap Core CSS -->
<link rel="stylesheet" type="text/css" href="<?=THEME_URL;?>/vendor/bootstrap/css/bootstrap.min.css">
<!-- MetisMenu CSS -->
<link rel="stylesheet" type="text/css" href="<?=THEME_URL;?>/vendor/metisMenu/metisMenu.min.css">
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="<?=THEME_URL;?>/vendor/datatables/datatables.min.css"/>
<!-- jQuery Timepicker CSS --> 
<link rel="stylesheet" type="text/css" href="<?=THEME_URL;?>/vendor/jquery-timepicker/jquery.timepicker.min.css">
<!-- Bootstrap Datepicker CSS --> 
<link rel="stylesheet" type="text/css" href="<?=THEME_URL;?>/vendor/bootstrap-datepicker/css/bootstrap-datepicker.min.css">
<!-- Multiselect lib -->
<link rel="stylesheet" type="text/css" href="<?=THEME_URL;?>/vendor/bootstrap-multiselect/css/bootstrap-multiselect.css">
<!-- Custom Fonts -->
<link rel="stylesheet" type="text/css" href="<?=THEME_URL;?>/vendor/font-awesome/css/font-awesome.min.css">
<!-- jQuery UI Theme -->
<link rel="stylesheet" type="text/css" href="<?=THEME_URL;?>/vendor/jquery-ui/black-tie/jquery-ui.min.css">
<!-- jQuery Fullcalendar CSS -->
<link rel="stylesheet" type="text/css" href="<?=THEME_URL;?>/vendor/fullcalendar/fullcalendar.min.css">
<!-- jQuery Fullcalendar Print CSS -->
<link rel="stylesheet" type="text/css" href="<?=THEME_URL;?>/vendor/fullcalendar/fullcalendar.print.min.css" media="print">
<!-- jQuery International phone number format -->
<link rel="stylesheet" type="text/css" href="<?=THEME_URL;?>/vendor/int-tel-input/build/css/intlTelInput.css">
<!-- Custom CSS -->
<link rel="stylesheet" type="text/css" href="<?=THEME_URL;?>/dist/css/sb-admin-2.min.css" media="all">
<!-- CometChat CSS -->
<link rel="stylesheet" type="text/css" href="<?=SYSTEM_URL;?>/cometchat/css.php" media="all">
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
<!-- jQuery -->
<script type="text/javascript" src="<?=SYSTEM_URL;?>/javascript/jquery.min.js"></script>
<!-- jQuery Cookie -->
<script type="text/javascript" src="<?=SYSTEM_URL;?>/javascript/jquery.cookie.js"></script>
<!-- jQuery Validator -->
<script type="text/javascript" src="<?=SYSTEM_URL;?>/javascript/jquery-validate/jquery.validate.min.js"></script>
<!-- jQuery Tinymce -->
<script type="text/javascript" src="<?=SYSTEM_URL;?>/javascript/tinymce/jquery.tinymce.min.js"></script>
<script type="text/javascript" src="<?=SYSTEM_URL;?>/javascript/tinymce/tinymce.min.js"></script>
<!-- jQuery Fullcalendar -->
<script type="text/javascript" src="<?=THEME_URL;?>/vendor/fullcalendar/lib/moment.min.js"></script> 
<script type="text/javascript" src="<?=THEME_URL;?>/vendor/fullcalendar/fullcalendar.min.js"></script>
<!-- CometChat JS -->
<script type="text/javascript" src="<?=SYSTEM_URL;?>/cometchat/js.php"></script>
</head>

<body>
<?php
if( !empty(trim(GOOGLE_ANALYTICS_ID)) ){
	echo add_google_analytics( GOOGLE_ANALYTICS_ID );
}
?>