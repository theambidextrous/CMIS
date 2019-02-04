<?php 
/********************************************* 
Company:		Finstock IT
Developer:	    Sammy/Iddris
Mobile:			(+254)721428276/0705007984
Email:			/idd.otuya@outlook.com/sammy@witstechnologies.co.ke
Website:		http://www.witstechnologies.co.ke/https://jacidd.com
*********************************************/ 

#Handle Errors 
error_reporting(E_ALL ^ E_NOTICE); 
header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1 
header('Expires: Sun, 17 Jan 1982 08:52:00 GMT'); // Date in the past
#Enable Sessions 
session_start(); 

#Site Settings 
define('SYSTEM_NAME', 'FINSTOCK EVARSITY');
define('SYSTEM_SHORT_NAME', 'FINSTOCK EVARSITY');
define('PARENT_HOME_URL', 'https://finstockevarsity.com');
define('SYSTEM_URL', 'https://finstockevarsity.com/cmis');
define('SYSTEM_LOGO_URL', 'https://finstockevarsity.com/cmis/images/logo.png');
#Site Contacts
define('COMPANY_PHONE', '');
define('COMPANY_ADDRESS', '');
#Folders
define('SYS_PATH', dirname(__DIR__));
define('IMAGE_FOLDER', SYSTEM_URL.'/images');
define('FILE_PATH', SYS_PATH.DIRECTORY_SEPARATOR ."files". DIRECTORY_SEPARATOR);
define('UPLOADS_PATH', SYS_PATH.DIRECTORY_SEPARATOR ."uploads". DIRECTORY_SEPARATOR);
define('ATTACHMENT_PATH', SYS_PATH.DIRECTORY_SEPARATOR ."attachments". DIRECTORY_SEPARATOR);
define('ASSIGNMENT_PATH', SYS_PATH.DIRECTORY_SEPARATOR ."assignments". DIRECTORY_SEPARATOR);
define('FILE_FOLDER', SYSTEM_URL.'/files');
define('UPLOADS_FOLDER', SYSTEM_URL.'/uploads');
define('ATTACHMENT_FOLDER', SYSTEM_URL.'/attachments');
define('ASSIGNMENT_FOLDER', SYSTEM_URL.'/assignments');
define('THEME_FOLDER', SYSTEM_URL.'/themes');
#Theme
define('THEME_NAME', 'startbootstrap-sb-admin-2');
define('THEME_DIR', SYS_PATH.DIRECTORY_SEPARATOR."themes".DIRECTORY_SEPARATOR.THEME_NAME);
define('THEME_URL', THEME_FOLDER.'/'.THEME_NAME);
#Emails used for alarts/notifications
define('INFO_NAME', '');
define('INFO_EMAIL', '');
define('SUPPORT_NAME', '');
define('SUPPORT_EMAIL', '');
define('DEVELOPER_NAME', '');
define('DEVELOPER_EMAIL', '');
#Email Signature
define('EMAIL_SIGNATURE_IMG', IMAGE_FOLDER.'/signature.png');
define('EMAIL_SIGNATURE_STAMP', IMAGE_FOLDER.'/stamp.png');
define('EMAIL_SIGNATURE_NAME', '');
define('EMAIL_SIGNATURE_TITLE', '');
#Database Settings
define('DB_HOST', 'localhost');
define('DB_USER', 'dbuser');
define('DB_PASS', 'dbpass');
define('DB_NAME', 'db');
define('DB_PREFIX', 'dbprefix_');
#Mailer Settings
define('MAILER_FROM_NAME', 'Finstock Evarsity');
define('MAILER_FROM_EMAIL', 'notification@finstockevarsity.com');
define('MAILER', 'mail');
define('SENDMAIL', '/usr/sbin/sendmail');
define('SMTP_AUTH', TRUE);
define('SMTP_SECU', 'tls');
define('SMTP_USER', 'notification@finstockevarsity.com');
define('SMTP_PASS', 'smtppass');
define('SMTP_HOST', 'finstockevarsity.com');
define('SMTP_PORT', 587);
#Permission Settings
define('LIMIT_USERS', FALSE);
define('LIMIT_ALLOWED', 2);
#Timezone Settings 
date_default_timezone_set('Africa/Nairobi');
#SMS API Details
define("SMS_API_KEY", "smskey");
define("SMS_API_USER", "sms shortcode");
#PesaPal API Details
define('PESAPAL_CONSUMER_KEY', 'consumer key');
define('PESAPAL_CONSUMER_SECRET', 'consumer secret');
#PesaPal API URLs
define('PESAPAL_IFRAME_API', 'https://www.pesapal.com/api/PostPesapalDirectOrderV4');
define('PESAPAL_STATUS_API', 'https://www.pesapal.com/api/querypaymentstatus');
define('PESAPAL_DETAILS_API', 'https://www.pesapal.com/api/QueryPaymentDetails');
#MPESA API Details
define("MPESA_API_ENV","live/demo/test"); // Either "sandbox" or "live"
define("MPESA_CONSUMER_KEY","key");
define("MPESA_CONSUMER_SECRET","secret");
define("MPESA_SHORTCODE","Pay bill number");
define("MPESA_PASSKEY","use live pass key to communicate with mpesa portal");
#reCAPTCHA keys 
define('RECAPTCHA_PUBLIC_KEY', 'key');
define('RECAPTCHA_PRIVATE_KEY', 'private key');
#Google Analytics 
define('GOOGLE_ANALYTICS_ID', '');
#System Metas 
define('META_KEYS', 'finstock evarsity, finstock evarsity portal, online evarsity portal');
define('META_DESC', 'Welcome to Finstock Evarsity Portal, you can use this portal to manage your courses, faculties, students details and many more online learning capabilities.');
?>
