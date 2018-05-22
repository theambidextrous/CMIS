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
				require_once("$class_dir/EvarsitySMS.php");
				//Open database connection
				$conn = db_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
				
				//get form details
				$student_id = $_SESSION['STUD_ID'];
				$reference = $_SESSION['STUD_ID_HASH'];
				$transaction_tracking_id = '';
				$amount = !empty($_SESSION['AMOUNT'])?$_SESSION['AMOUNT']:0;
				$formatted_amount = number_format($amount, 2);//format amount to 2 decimal places
				$pay_method = '';
				$phonenumber = !empty($_SESSION['STUD_TEL'])?$_SESSION['STUD_TEL']:'';
				$first_name = secure_string($_SESSION['STUD_FNAME']);
				$last_name = secure_string($_SESSION['STUD_LNAME']);
				$full_name = secure_string($_SESSION['STUD_FNAME'].' '.$_SESSION['STUD_LNAME']);
				$email = $_SESSION['STUD_EMAIL'];
				$pay_type = isset($_SESSION['PAY_TYPE'])?$_SESSION['PAY_TYPE']:'Registration';
				$pay_status = 'INITIALIZED';
				$desc = SYSTEM_NAME." Fee Payment";								

				//Get requested payment method
				$paymentmethod = isset($_GET['paymentmethod'])?$_GET['paymentmethod']:"";
				$paymentmethod = strtolower($paymentmethod);
        switch($paymentmethod) {
					case "pesapal":						
						//remove abandoned payments.
						removeAbandonedPayment($reference, $pay_status)	;				
						$type = "MERCHANT";
						//initiate a payment in db
						$params = array($student_id,$reference,$transaction_tracking_id,$amount,$pay_method,$phonenumber,$full_name,$email,$pay_type,$pay_status);
						recordPayment($params);
						//initiate payment gateway
						$token = $params = NULL;
						$consumer_key = PESAPAL_CONSUMER_KEY;
						$consumer_secret = PESAPAL_CONSUMER_SECRET;
						$signature_method = new OAuthSignatureMethod_HMAC_SHA1();
						$iframelink = PESAPAL_IFRAME_API;						
						$callback_url = SYSTEM_URL.'/portal/?do=return_api&paymentmethod=pesapal'; //redirect url, the page that will handle the response from pesapal.
						$post_xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?><PesapalDirectOrderInfo xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" Amount=\"".$formatted_amount."\" Description=\"".$desc."\" Type=\"".$type."\" Reference=\"".$reference."\" FirstName=\"".$first_name."\" LastName=\"".$last_name."\" Email=\"".$email."\" PhoneNumber=\"".$phonenumber."\" xmlns=\"http://www.pesapal.com\" />";
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
						<p class="text-center text-danger"><a href="javascript:history.go(-1)">Not able to pay with this payment option? Go back and try another method or call us for assistance.</a></p>
						<?php
					break;
					case "mpesa":
						require_once("$class_dir/class.mpesa.php");
						$Mpesa = new Mpesa();
						
						$TransactionType = "CustomerPayBillOnline";//Defaults to CustomerPayBillOnline
						$Amount = number_format($amount,0,"","" );//The amount to be transacted.
						$PartyA = $Mpesa->properMSISDN($phonenumber);//The MSISDN sending the funds.
						$PartyB = MPESA_SHORTCODE;//The organization shortcode receiving the funds
						$PhoneNumber = $Mpesa->properMSISDN($phonenumber);//The MSISDN sending the funds.
						$CallBackURL = SYSTEM_URL."/portal/?do=return_api&paymentmethod=mpesa";//The url to where responses from M-Pesa will be sent to.
						$AccountReference = $student_id;//Used with M-Pesa PayBills.
						$TransactionDesc = $pay_type;//A description of the transaction.
						$Remark = $desc;//Comments that are sent along with the transaction.
						
						echo "<h2>You will be billed Ksh. ".$Amount." on M-Pesa number ".$PhoneNumber."</h2>";
						echo "<p>A popup will appear on your M-PESA phone promting you to enter your PIN. If the popup does not appear, <a href=\"\">press here to try again.</a></p>";
						
						$response = $Mpesa->STKPushSimulation(MPESA_SHORTCODE, MPESA_PASSKEY, $TransactionType, $Amount, $PartyA, $PartyB, $PhoneNumber, $CallBackURL, $AccountReference, $TransactionDesc, $Remark);												
						
						$data = json_decode($response, true);
						
						$pay_method = "LIPA_NA_MPESA";
						
						//[errorCode] => 400.002.02, [errorMessage] => Bad Request - Invalid PhoneNumer
						//[errorCode] => 400.002.02, [errorMessage] => Bad Request - Invalid Amount
						//[errorCode] => 500.001.1001, [errorMessage] => [CBS - ] No ICCID found (non m-pesa number)
						if($data['errorCode']){
							echo ErrorMessage("Response from M-Pesa server: [".$data['errorCode']."] ".$data['errorMessage']);
							echo "<p><a href=\"?do=payment&paymentmethod=mpesa\" class=\"btn btn-success\">Try Again</a> <a href=\"?do=payment&paymentmethod=pesapal\" class=\"btn btn-danger\">Pay with PesaPal</a></p>";
						}else{
							//[MerchantRequestID] && [CheckoutRequestID] => SET
							//[ResponseCode] => 0, [ResponseDescription] => Success. Request accepted for processing
							//[CustomerMessage] => Success. Request accepted for processing
							if(intval($data['ResponseCode']) == 0 && !empty($data['MerchantRequestID']) && !empty($data['CheckoutRequestID'])){
								//Success. Request accepted for processing
								//set up critical sesions					
								$_SESSION['MERCHANTID'] = $data['MerchantRequestID'];
								$_SESSION['CHECKOUTREQUESTID'] = $data['CheckoutRequestID'];
								
								//RUN A DELETE FOR ALL NONE-STARTED PAYMENTS FOR THIS USER AND INSERT NEW.
								$delDuplicate = sprintf("DELETE FROM `".DB_PREFIX."payment_refs` WHERE `student_pay_ref` = '%s' AND `transaction_tracking_id` = '%s'", $data['MerchantRequestID'], $data['CheckoutRequestID']);
								db_query($delDuplicate,DB_NAME,$conn);
								
								//Save transaction to DB
								$newPaymentSql = sprintf("INSERT INTO `".DB_PREFIX."payment_refs` (`student_id`, `student_pay_ref`, `transaction_tracking_id`, `payment_amount`, `pay_method`, `stud_tel`, `stud_full_name`, `stud_email`, `pay_type`, `pay_status`) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')", $student_id, $data['MerchantRequestID'], $data['CheckoutRequestID'], $amount, $pay_method, $phonenumber, $full_name, $email, $pay_type, $pay_status);
								//execute qry
								db_query($newPaymentSql,DB_NAME,$conn);
								
								//User gets popup to enter PIN
								echo AttentionMessage( $data['ResponseDescription'].". Wait for a popup on your phone and enter the correct M-Pesa PIN. Then press the check status button below.");
								echo '<p><a href="'.$CallBackURL.'" class="btn btn-lg btn-primary">Check Status</a></p>';
							}
						}
						?>
						<p class="text-center text-danger"><a href="javascript:history.go(-1)">Not able to pay with this payment option? Go back and try another method or call us for assistance.</a></p>
						<?php
					break;
					default:					
						?>
						<div class="col-md-12">
							<h2>You are about to pay Ksh.<?php echo $_SESSION['AMOUNT']; ?></h2>
							<h3>Select a payment method</h3>
							<p>We support the following payment methods. Click on your preferred payment method:</p>
							
							<div class="row">								
								<div class="col-md-6">
									<h3>Lipa na M-Pesa</h3>
									<a href="?do=payment&paymentmethod=mpesa&paytype=<?php echo $pay_type; ?>" title="Click to pay with M-Pesa"><img class="img-responsive" style="max-width:260px;" src="<?php echo IMAGE_FOLDER; ?>/payment_methods/lipa-na-mpesa.png" alt="Lina na M-Pesa"></a>
								</div>
								<div class="col-md-6">
									<h3>PesaPal Payment</h3>
									<a href="?do=payment&paymentmethod=pesapal&paytype=<?php echo $pay_type; ?>" title="Click to pay with PesaPal"><img class="img-responsive" style="max-width:260px;" src="<?php echo IMAGE_FOLDER; ?>/payment_methods/pesapal.jpg" alt="PesaPal Payment"></a>
								</div>
							</div>
							
							<p class="text-center text-danger"><a href="javascript:history.go(-1)">Go Back</a></p>
							
						</div>
						<?php
					break;
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
