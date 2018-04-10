<?php 
/********************************************* 
Company:		Wits Technologies Ltd
Developer:	Sammy Mwaura Waweru
Mobile:			+254721428276
Email:			sammy@witstechnologies.co.ke
Website:		http://www.witstechnologies.co.ke/
*********************************************/ 
#Handle Errors 
error_reporting(E_ALL ^ E_NOTICE); 
header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1 
header('Expires: Sun, 17 Jan 1982 08:52:00 GMT'); // Date in the past
#Enable Sessions 
session_start(); 
//mpesa stk settings
define("MPESA_API_ENV","sandbox"); // Either "sandbox" or "live"
define("MPESA_CONSUMER_KEY","TDoogvt6SA8HW8Wm9ZWwjPH2p60ZJCIb");
define("MPESA_CONSUMER_SECRET","woNeFywaXc1yU0R8");
define("MPESA_SHORTCODE","174379");
define("MPESA_PASSKEY","bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919");
define("MPESA_CALLBACKURL","http://localhost/evs/portal/?tab=9&action=callback");
#Site Settings 
define('SYSTEM_NAME', 'FINSTOCK EVARSITY');
define('SYSTEM_SHORT_NAME', 'EVARSITY SYSTEM');
define('PARENT_HOME_URL', 'http://localhost/finstock.com');
define('SYSTEM_URL', 'http://localhost/finstock.com');
define('SYSTEM_LOGO_URL', 'http://localhost/finstock.com/images/logo.png');
#Site Contacts
define('COMPANY_PHONE', '+254721428276');
define('COMPANY_ADDRESS', 'OFFICE: TOWN HOUSE 2nd Floor, Suite 12<br />Kaunda Street, next to Trattoria Restaurant/Lonrho House<br />P. O. Box 102280-00101 Nairobi, Kenya.');
#Folders
define('SYS_PATH', dirname(__DIR__));
define('IMAGE_FOLDER', SYSTEM_URL.'/images');
define('FILE_PATH', dirname(__DIR__). DIRECTORY_SEPARATOR ."files". DIRECTORY_SEPARATOR);
define('UPLOADS_PATH', dirname(__DIR__). DIRECTORY_SEPARATOR ."uploads". DIRECTORY_SEPARATOR);
define('ATTACHMENT_PATH', dirname(__DIR__). DIRECTORY_SEPARATOR ."attachments". DIRECTORY_SEPARATOR);
define('FILE_FOLDER', SYSTEM_URL.'/files');
define('UPLOADS_FOLDER', SYSTEM_URL.'/uploads');
define('ATTACHMENT_FOLDER', SYSTEM_URL.'/attachments');
define('THEME_FOLDER', SYSTEM_URL.'/themes');
#Theme
define('THEME_NAME', 'startbootstrap-sb-admin-2');
define('THEME_DIR', SYS_PATH.DIRECTORY_SEPARATOR."themes".DIRECTORY_SEPARATOR.THEME_NAME);
define('THEME_URL', THEME_FOLDER.'/'.THEME_NAME);
#Emails used for alarts/notifications
define('INFO_NAME', 'INFO');
define('INFO_EMAIL', 'info@finstockevarsity.com');
define('SUPPORT_NAME', 'WITS SUPPORT');
define('SUPPORT_EMAIL', 'supporst@finstockevarsity.com');
define('DEVELOPER_NAME', 'Sammy M. Waweru');
define('DEVELOPER_EMAIL', 'support@finstockevarsity.com');
#Database Settings
define('DB_HOST', 'localhost');
define('DB_USER', 'finstoc1_usrport');
define('DB_PASS', 'vT^Fw+wbXS-%');
define('DB_NAME', 'finstoc1_portal');
define('DB_PREFIX', 'mis_');
#Mailer Settings
define('MAILER_FROM_NAME', 'Finstock Evarsity Admissions');
define('MAILER_FROM_EMAIL', 'notification@finstockevarsity.com');
define('MAILER', 'smtp');
define('SENDMAIL', '/usr/sbin/sendmail');
define('SMTP_AUTH', TRUE);
define('SMTP_SECU', 'tls');
define('SMTP_USER', 'notification@finstockevarsity.com');
define('SMTP_PASS', '3b9qIHn6b6');
define('SMTP_HOST', 'finstockevarsity.com');
define('SMTP_PORT', 587);
#Permission Settings
define('LIMIT_USERS', FALSE);
define('LIMIT_ALLOWED', 2);
//SMS settings live
define("SMS_API_KEY", "166a80588b402987fe9a0104a406bf866f1e8c08f9b46b7bde47f85c96c435e3");
define("SMS_API_USER", "FinEvarsity");
#Timezone Settings 
date_default_timezone_set('Africa/Nairobi');
#PesaPal API Details
// define('PESAPAL_CONSUMER_KEY', 'J96G4GrsN0pw//NoX2Oz0FJhu52ApJLG');
// define('PESAPAL_CONSUMER_SECRET', 'usr6IYisV9K+qGv1/3v/+GMmK54=');
define('PESAPAL_CONSUMER_KEY', 'FZRF1tBdjeETLKT1h+zI7OwDX8pzEPn2');//DEMO ONLY
define('PESAPAL_CONSUMER_SECRET', 'OvRKBwRPge4xD7eyPnVDD/Px0tQ=');//DEMO ONLY
#PesaPal API URLs
// define('PESAPAL_IFRAME_API', 'https://www.pesapal.com/api/PostPesapalDirectOrderV4');
// define('PESAPAL_STATUS_API', 'https://www.pesapal.com/api/querypaymentstatus');
// define('PESAPAL_DETAILS_API', 'https://www.pesapal.com/api/QueryPaymentDetails');
define('PESAPAL_IFRAME_API', 'https://demo.pesapal.com/api/PostPesapalDirectOrderV4');//DEMO ONLY
define('PESAPAL_STATUS_API', 'https://demo.pesapal.com/api/querypaymentstatus');//DEMO ONLY
define('PESAPAL_DETAILS_API', 'https://demo.pesapal.com/api/QueryPaymentDetails');//DEMO ONLY
#reCAPTCHA keys 1. live
// define('RECAPTCHA_PUBLIC_KEY', '6LfIGA8UAAAAAJdWrQcf3nLxeNMLFmcLCb2lVMNJ');
// define('RECAPTCHA_PRIVATE_KEY', '6LfIGA8UAAAAAHNeEzqY6HAjjH_Trrj-kr0KuFv0');
#reCAPTCHA keys 2. local
define('RECAPTCHA_PUBLIC_KEY', '6LeQjT0UAAAAAOqHYOaCEODifc36MOvHYpD1SwgN');
define('RECAPTCHA_PRIVATE_KEY', '6LeQjT0UAAAAAOPWUrQWwK-bzVbq_TNR8ZzSokIN');
#Google Analytics 
define('GOOGLE_ANALYTICS_ID', '');
#SEO Settings 
define('META_KEYS', 'finstock evarsity, finstock evarsity system, evarsity system');
define('META_DESC', 'Welcome to Finstock Evarsity System, we use this system to manage our student details.');
?>