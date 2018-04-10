<?php
/*********************************************
Company:		Wits Technologies Ltd
Developer:	Sammy Mwaura Waweru, Idd Otuya
Mobile:			+254721428276
Email:			sammy@witstechnologies.co.ke
Website:		http://www.witstechnologies.co.ke/
*********************************************/
?>
<script language="javascript" type="text/javascript">
<!--
document.title = "<?=SYSTEM_SHORT_NAME?> - Portal | Secure Payment Confirmation";
//-->
</script>

<div class="container">
  <div class="row">
    <div class="col-md-12">
      <div class="header-img"><img class="img-responsive" src="<?=SYSTEM_LOGO_URL;?>" alt="Logo"></div>
      <div class="activate-panel panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Thank You</h3>
        </div>
        <div class="panel-body">
          <?php
          //initiate mailer
          //Get requested task/default is add
          $task = isset($_GET['task'])?$_GET['task']:"add";
          $task = strtolower($task);
          switch($task) {
            case "add":
              ///thank the user and inform them of successful payment.
              $succ = $_SESSION['MSG'];
              $paycode = $_SESSION['STUD_ID'];
              $amount = $_SESSION['AMOUNT'];
              $name = $_SESSION['STUD_FNAME'].' '.$_SESSION['STUD_LNAME'];
              $email = $_SESSION['STUD_EMAIL'];
              $phonenumber = $_SESSION['STUD_TEL'];
              $courseID = $_SESSION['COURSE_ID'];
              //ALTERNATE EMAIL DEPENDING ON PAYMENT
            	if($_SESSION['PAY_TYPE'] == 'Registration'){
								$subject = SYSTEM_NAME.' - Application Received';
								//make body
								$content='<html><head>
								<title>'.$subject.'</title>
								</head><body><div style="background-color:#E1CDB7; color:#000; width:600px; margin:0 auto;">
								<div style="background:#C60; color:#FFF; min-width:584px; padding:8px;">
								<h2 style="font-size:15px;font-weight:700;line-height:25px;"><em>'.strtoupper(SYSTEM_NAME).' APPLICATION</em></h2>
								</div>
								<div style="padding:15px;">
								<h3 style="color:#333;">Dear '.$name.',</h3>
								<p style="text-align:justify;">We at <a href="'.PARENT_HOME_URL.'">'.SYSTEM_NAME.'</a> would like to thank you for applying to study with us.</p>
								<p style="text-align:justify;">We acknowledge reception of KES '.$amount.', registration fee which you paid through our online system. Below are your transaction details:<br />
								Name: '.$name.'<br />
								Email: '.$email.'<br />
								Phone: '.$phonenumber.'<br />
								Transaction Ref: '.$paycode.'</p>
								<p style=" text-align:justify;">We would like to let you know that your application was successfully submitted and that our administrators are going through your papers for verification purposes and we will get back to you with an  <strong>Admission Letter</strong> soonest.</p><br />
								<p style="color:#753b01;">All the best!<br /><br />
								Admissions Office,<br />
								'.SYSTEM_NAME.',<br />
								'.COMPANY_ADDRESS.'<br />
								TEL: '.COMPANY_PHONE.'<br />
								EMAIL: '.INFO_EMAIL.'<br />
								WEBSITE: '.PARENT_HOME_URL.'</p>
								</div></div>
								</body></html>';
								//get email func
								mail_config($email, $name, $subject, $content);
            	}else{
								$subject = SYSTEM_NAME.' - Fee payment Acknowledgement';
								//make body
								$content='<html><head>
								<title>'.$subject.'</title>
								</head><body><div style="background-color:#E1CDB7; color:#000; width:600px; margin:0 auto;">
								<div style="background:#C60; color:#FFF; min-width:584px; padding:8px;">
								<h2 style="font-size:15px;font-weight:700;line-height:25px;"><em>'.strtoupper(SYSTEM_NAME).' FEE PAYMENT</em></h2>
								</div>
								<div style="padding:15px;">
								<h3 style="color:#333;">Dear '.$name.',</h3>
								<p style="text-align:justify;">We at <a href="'.PARENT_HOME_URL.'">'.SYSTEM_NAME.'</a> would like to thank you for being part of us.</p>
								<p style="text-align:justify;">We acknowledge reception of KES '.$amount.', Tution fee which you paid through our online system. Below are your transaction details:<br />
								Name: '.$name.'<br />
								Email: '.$email.'<br />
								Phone: '.$phonenumber.'<br />
								Transaction Ref: '.$paycode.'</p>
								<br />
								<p style="color:#753b01;">Thank you<br /><br />
								Accounts Office,<br />
								'.SYSTEM_NAME.',<br />
								'.COMPANY_ADDRESS.'<br />
								TEL: '.COMPANY_PHONE.'<br />
								EMAIL: '.INFO_EMAIL.'<br />
								WEBSITE: '.PARENT_HOME_URL.'</p>
								</div></div>
								</body></html>';
								//get email func
								mail_config($email, $name, $subject, $content);
							}
              echo $succ;
              //kill all sessions
              //session_destroy();
              unset($_SESSION['PAY_TYPE']);
							unset($_SESSION['MSG']);
              unset($_SESSION['STUD_ID']);
							unset($_SESSION['STUD_ID_HASH']);
              unset($_SESSION['AMOUNT']);
							unset($_SESSION['STUD_FNAME']);
							unset($_SESSION['STUD_LNAME']);
              unset($_SESSION['STUD_FULLNAME']);
              unset($_SESSION['STUD_EMAIL']);
              unset($_SESSION['STUD_TEL']);
							unset($_SESSION['COURSE_ID']);
							unset($_SESSION['MERCHANTID']);
							unset($_SESSION['CHECKOUTREQUESTID']);
              //break if no task case and default.
            break;
            default:
              echo ErrorMessage("Invalid request! The system failed to process your request. If the problem persists, please contact us.");
						break;
          }
          ?>
        </div>
        <!-- / .panel-body -->
      </div>
      <!-- / .login-panel -->
    </div>
    <!-- / .col-md-4 -->
  </div>
  <!-- / .row -->
</div>
