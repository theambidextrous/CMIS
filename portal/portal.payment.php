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
document.title = "<?=SYSTEM_SHORT_NAME?> - Portal | Secure Payment page";
//-->
</script>

<div class="container">
  <div class="row">
    <div class="col-md-12">
      <div class="header-img"><img class="img-responsive" src="<?=SYSTEM_LOGO_URL;?>" alt="Logo"></div>
      <div class="activate-panel panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Process Payment</h3>
        </div>
        <div class="panel-body">
				<?php
				require_once("$incl_dir/mysqli.functions.php");
				require_once("$class_dir/class.OAuth.php");
				//Open database connection
				$conn = db_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
				//Get requested task/default is add
				$task = isset($_GET['task'])?$_GET['task']:"add";
				
				$task = strtolower($task);
				switch($task) {
					case "add":
						$_SESSION['IS_FEE'] = 1;
						//INITIATE A PAYMENT IN DB
						$student_id = $_SESSION['STUD_ID'];
						$student_pay_ref = $_SESSION['STUD_ID_HASH'];
						$transaction_tracking_id = '';
						$payment_amount = $_SESSION['AMOUNT'];
						$pay_method = '';
						$stud_tel = $_SESSION['STUD_TEL'];
						$stud_full_name = $_SESSION['STUD_FNAME'].' '.$_SESSION['STUD_LNAME'];
						$stud_email = $_SESSION['STUD_EMAIL'];
						$pay_type = 'registration fee';
						$pay_status = 'Not Started';
						
						//$pay_status
						//RUN A DELETE FOR ALL NONE-STARTED PAYMENTS FOR THIS USER AND INSERT NEW.
						$delDuplicate = sprintf("DELETE FROM `".DB_PREFIX."payment_refs` WHERE `student_pay_ref` = '%s' AND `pay_status` = '%s'", $student_pay_ref, $pay_status);
						db_query($delDuplicate,DB_NAME,$conn);
						
						//INSERT A NEW ONE ON RELOAD ETC. 
						$newPaymentSql = sprintf("INSERT INTO `".DB_PREFIX."payment_refs` (`student_id`, `student_pay_ref`, `transaction_tracking_id`, `payment_amount`, `pay_method`, `stud_tel`, `stud_full_name`, `stud_email`, `pay_type`, `pay_status`) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')", $student_id, $student_pay_ref, $transaction_tracking_id, $payment_amount, $pay_method, $stud_tel, $stud_full_name, $stud_email, 'registration fee', 'Not Started');
						//execute qry
						db_query($newPaymentSql,DB_NAME,$conn);				  
						
						//INITIATE PAYMENT CREDENTIALS
						$token = $params = NULL;
						$consumer_key = PESAPAL_CONSUMER_KEY;
						$consumer_secret = PESAPAL_CONSUMER_SECRET;
						$signature_method = new OAuthSignatureMethod_HMAC_SHA1();
						$iframelink = PESAPAL_IFRAME_API;
						//get form details
						$amount = $_SESSION['AMOUNT'];
						$amount = number_format($amount, 2);//format amount to 2 decimal places
						
						$desc = SYSTEM_NAME." Fee Payment";
						$type = "MERCHANT"; //default value = MERCHANT
						$reference = $_SESSION['STUD_ID_HASH'];//unique order id of the transaction, generated by merchant
						$first_name = $_SESSION['STUD_FNAME'];
						$last_name = $_SESSION['STUD_LNAME'];
						$email = $_SESSION['STUD_EMAIL'];
						$phonenumber = $_SESSION['STUD_TEL'];//ONE of email or phonenumber is required
						
						$callback_url = SYSTEM_URL.'/portal/?do=return_api'; //redirect url, the page that will handle the response from pesapal.
						
						$post_xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?><PesapalDirectOrderInfo xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" Amount=\"".$amount."\" Description=\"".$desc."\" Type=\"".$type."\" Reference=\"".$reference."\" FirstName=\"".$first_name."\" LastName=\"".$last_name."\" Email=\"".$email."\" PhoneNumber=\"".$phonenumber."\" xmlns=\"http://www.pesapal.com\" />";
						$post_xml = htmlentities($post_xml);
						$consumer = new OAuthConsumer($consumer_key, $consumer_secret);
						
						//post transaction to pesapal
						$iframe_src = OAuthRequest::from_consumer_and_token($consumer, $token, "GET", $iframelink, $params);
						$iframe_src->set_parameter("oauth_callback", $callback_url);
						$iframe_src->set_parameter("pesapal_request_data", $post_xml);
						$iframe_src->sign_request($signature_method, $consumer, $token);
						?>
						<div class="col-md-12">
						<iframe src="<?php echo $iframe_src;?>" width="100%" height="700px"  scrolling="no" frameBorder="0">
						<p>Browser unable to load iFrame</p>
						</iframe>						
						</div>
						<?php
					break;
					default:
						echo ErrorMessage("Invalid request! The system failed to process your request. If the problem persists, please contact us.");
				}
				
				//Close connection
				db_close($conn);
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
