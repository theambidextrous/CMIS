<?php 
/********************************************* 
Company:		Wits Technologies Ltd
Developer:	Sammy Mwaura Waweru
Mobile:			+254721428276
Email:			sammy@witstechnologies.co.ke
Website:		http://www.witstechnologies.co.ke/
*********************************************/ 

#Handle Errors
//ini_set('error_reporting', E_ALL);
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
define("EXAM_URL", SYSTEM_URL."/portal/student/");
define('SYSTEM_LOGO_URL', 'https://finstockevarsity.com/cmis/images/logo.png');
define('SYSTEM_BG_URL', 'https://finstockevarsity.com/cmis/images');
define('TVT_REG_NO', 'TVETA/PRIVATE/TVC/0031/2018');
define('QR_URL', 'https://finstockevarsity.com/cmis/qrs/');
//COMET API
define("COMET_API_KEY", "51235xe8ae55772de441288663bbf9c04d3415");
define("COMET_API_URL", "https://api.cometondemand.net/api/v2/");
define("COMET_CHAT_KEY", "COMETCHAT-C0T10-625TX-KW0I9-WWEOU");
define("COMET_APP_ID", "51235");
//sms templates
define("ADMIN_SMS_LINES", "+254719347374,+254703313722,+254796871644,+254705007984");
define('WELCOME', 'Dear [NAME], welcome to Finstock Evarsity portal. To register units, go to "Your courses" and click "Register Units Now". Select all units for your current trimester and click on "Register Now" button at the bottom.');
define('UNIT_REGISTRATION', 'Dear [NAME] Registration of your units was successful. Please proceed to "Dashboard", enter amount  under "Payment Summary" and click on "Pay Now".');
define('UNITS_APPROVAL_REQUEST','Dear Admin, [STUDENTID] has selected units for registeration, please login to approve.');
//administration
define('ACADEMIC_AFFAIRS', 'Caroline Kagwi, Academic Affairs');
define('DIRECTOR', 'CPA John Kimani, Director');
//Fees
define("REG_FEE", 1000);
define("CLASS_MODE", 10000);
define("EXECUTIVE_MODE", 20000);
define("ONLINE_MODE", 0);
#Site Contacts
define('COMPANY_PHONE', '+254703313722');
define('COMPANY_ADDRESS', 'P.O. Box 102280-00101<br />Town House 2nd Flr, Suite 12, Kaunda Street, <br />Nairobi, Kenya.');
#Folders
define('SYS_PATH', dirname(__DIR__));
define('IMAGE_FOLDER', SYSTEM_URL.'/images');
define('GRAPHS_PATH', SYS_PATH.'/images/graphs/');
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
define('INFO_NAME', 'INFO');
define('INFO_EMAIL', 'info@finstockevarsity.com');
define('SUPPORT_NAME', 'WITS SUPPORT');
define('SUPPORT_EMAIL', 'support@finstockevarsity.com');
define('DEVELOPER_NAME', 'Sammy M. Waweru');
define('DEVELOPER_EMAIL', 'support@finstockevarsity.com');
#Email Signature
define('EMAIL_SIGNATURE_IMG', IMAGE_FOLDER.'/signature.png');
define('EMAIL_SIGNATURE_STAMP', IMAGE_FOLDER.'/stamp.png');
define('EMAIL_SIGNATURE_NAME', 'CPA John Githii Kimani (Ph.D Econ. Cand.)');
define('EMAIL_SIGNATURE_TITLE', 'Director, Finstock Evarsity College');
#Database Settings
define('DB_HOST', 'localhost');
define('DB_USER', 'finstoc1_usrport');
define('DB_PASS', 'vT^Fw+wbXS-%');
define('DB_NAME', 'finstoc1_portal');
define('DB_PREFIX', 'mis_');
#Mailer Settings
define('MAILER_FROM_NAME', 'Finstock Evarsity');
define('MAILER_FROM_EMAIL', 'notification@finstockevarsity.com');
define('MAILER', 'mail');
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
#Timezone Settings 
date_default_timezone_set('Africa/Nairobi');
#SMS API Details
define("SMS_API_KEY", "166a80588b402987fe9a0104a406bf866f1e8c08f9b46b7bde47f85c96c435e3");
define("SMS_API_USER", "FinEvarsity");
#PesaPal API Details
define('PESAPAL_CONSUMER_KEY', 'J96G4GrsN0pw//NoX2Oz0FJhu52ApJLG');
define('PESAPAL_CONSUMER_SECRET', 'usr6IYisV9K+qGv1/3v/+GMmK54=');
#PesaPal API URLs
define('PESAPAL_IFRAME_API', 'https://www.pesapal.com/api/PostPesapalDirectOrderV4');
define('PESAPAL_STATUS_API', 'https://www.pesapal.com/api/querypaymentstatus');
define('PESAPAL_DETAILS_API', 'https://www.pesapal.com/api/QueryPaymentDetails');
#MPESA API Details
define("MPESA_API_ENV","live"); // Either "sandbox" or "live"
define("MPESA_CONSUMER_KEY","8E1tGJmu5THzdSBwDpiH4PhJxCJnHYZ7");
define("MPESA_CONSUMER_SECRET","Xg3CNmWS05JI9cDH");
define("MPESA_SHORTCODE","566339");
define("MPESA_PASSKEY","58e1104f12e4488a82cdc6d16a1b91cee9f2972307e6c83c30a353fcee843ed5");
#reCAPTCHA keys 
define('RECAPTCHA_PUBLIC_KEY', '6LfIGA8UAAAAAJdWrQcf3nLxeNMLFmcLCb2lVMNJ');
define('RECAPTCHA_PRIVATE_KEY', '6LfIGA8UAAAAAHNeEzqY6HAjjH_Trrj-kr0KuFv0');
#Google Analytics 
define('GOOGLE_ANALYTICS_ID', 'UA-89131132-1');
#System Metas 
define('META_KEYS', 'finstock evarsity, finstock evarsity portal, online evarsity portal');
define('META_DESC', 'Welcome to Finstock Evarsity Portal, you can use this portal to manage your courses, faculties, students details and many more online learning capabilities.');
//AES
define("AES_KEY", "603deb1015ca71be2b73aef0857d77811f352c073b61");
?>
