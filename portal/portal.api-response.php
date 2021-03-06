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
          <h3 class="panel-title">Processing payment...</h3>
        </div>
        <div class="panel-body">
          <?php
          require_once("$incl_dir/mysqli.functions.php");
          require_once("$class_dir/class.OAuth.php");
          require_once("$class_dir/EvarsitySMS.php");
          //Open database connection
          $conn = db_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
					
          //Get requested payment method
          $paymentmethod = isset($_GET['paymentmethod'])?$_GET['paymentmethod']:"";
          $paymentmethod = strtolower($paymentmethod);
          switch($paymentmethod) {
            case "pesapal":
              // Array to store the messages
              $CONFIRM = array();
              //INITIATE CREDENTIALS
              $token = $params = NULL;
              $consumer_key = PESAPAL_CONSUMER_KEY;
              $consumer_secret = PESAPAL_CONSUMER_SECRET;
              $statusrequestAPI = PESAPAL_STATUS_API;
              $methodrequestAPI = PESAPAL_DETAILS_API;
							$pay_method = "PESAPAL";
              $pesapalNotification = $_GET['pesapal_notification_type'];
              $pesapalTrackingId = $_GET['pesapal_transaction_tracking_id'];
              $pesapal_merchant_reference = $_GET['pesapal_merchant_reference'];
              $params = array ($pesapalTrackingId, $pesapal_merchant_reference, "INITIALIZED" );
              //update tracking ID
              updateTrackingID($params);
              $params = array("PENDING", $pesapal_merchant_reference, $pesapalTrackingId);
              updateStatus($params);
              $signature_method = new OAuthSignatureMethod_HMAC_SHA1();
              if($pesapalNotification == "CHANGE" && $pesapalTrackingId != ''){
                //listen to IPN
                $token = $params = NULL;
                $consumer = new OAuthConsumer($consumer_key, $consumer_secret);
                //get transaction status
                $request_status = OAuthRequest::from_consumer_and_token($consumer, $token, "GET", $statusrequestAPI, $params);
                $request_status->set_parameter("pesapal_merchant_reference", $pesapal_merchant_reference);
                $request_status->set_parameter("pesapal_transaction_tracking_id",$pesapalTrackingId);
                $request_status->sign_request($signature_method, $consumer, $token);
                //curl
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $request_status);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch, CURLOPT_HEADER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                if(defined('CURL_PROXY_REQUIRED')) if (CURL_PROXY_REQUIRED == 'True'){
                  $proxy_tunnel_flag = (defined('CURL_PROXY_TUNNEL_FLAG') && strtoupper(CURL_PROXY_TUNNEL_FLAG) == 'FALSE') ? false : true;
                  curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, $proxy_tunnel_flag);
                  curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
                  curl_setopt($ch, CURLOPT_PROXY, CURL_PROXY_SERVER_DETAILS);
                }
                //run curl
                $response = curl_exec($ch);
                $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
                $raw_header  = substr($response, 0, $header_size - 4);
                $headerArray = explode("\r\n\r\n", $raw_header);
                $header      = $headerArray[count($headerArray) - 1];
                //extract transaction status
                $elements = preg_split("/=/",substr($response, $header_size));
                $status = $elements[1];
                //close curl
                curl_close($ch);
                //get payment method
                  $request_method = OAuthRequest::from_consumer_and_token($consumer, $token, "GET", $methodrequestAPI, $params);
                  $request_method->set_parameter("pesapal_merchant_reference", $pesapal_merchant_reference);
                  $request_method->set_parameter("pesapal_transaction_tracking_id",$pesapalTrackingId);
                  $request_method->sign_request($signature_method, $consumer, $token);
                  $ch = curl_init();
                  curl_setopt($ch, CURLOPT_URL, $request_method);
                  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                  curl_setopt($ch, CURLOPT_HEADER, 1);
                  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                  if(defined('CURL_PROXY_REQUIRED')) if (CURL_PROXY_REQUIRED == 'True'){
                    $proxy_tunnel_flag = (defined('CURL_PROXY_TUNNEL_FLAG') && strtoupper(CURL_PROXY_TUNNEL_FLAG) == 'FALSE') ? false : true;
                    curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, $proxy_tunnel_flag);
                    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
                    curl_setopt($ch, CURLOPT_PROXY, CURL_PROXY_SERVER_DETAILS);
                  }
                  $method_r = curl_exec($ch);
                  $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
                  $raw_header  = substr($response, 0, $header_size - 4);
                  $headerArray = explode("\r\n\r\n", $raw_header);
                  $header      = $headerArray[count($headerArray) - 1];
                  $element = preg_split("/=/",substr($method_r, $header_size));
                  $method = $element[1];
                  $pay_method= explode(",",$method);
                  $pay_method= $pay_method[1];
                  curl_close($ch);
                  //update paymethod
                  $params = array($pay_method, $pesapal_merchant_reference, $pesapalTrackingId);
                  updatePayMethod($params);
                  //notify user on status change
                  $params = array($pesapalTrackingId);
                  $amount = getPayingUser($params)['payment_amount'];
                  $name = getPayingUser($params)['stud_full_name'];
                  $email = getPayingUser($params)['stud_email'];
                  $phone = getPayingUser($params)['stud_tel'];
                  $subject = SYSTEM_NAME." - Payment Status Change";
                  $content = 'Dear '.$name.',<br> Your payment of KES '.$amount.' to '.SYSTEM_NAME.'  Status has changed to <b>'.$status.'</b>. Thank you for being part of us.<br>'.SYSTEM_NAME.'<br>';
                  mail_config($email, $name, $subject, $content);
                //this check is not helping.. db update func required
                $params = array($status, $pesapal_merchant_reference, $pesapalTrackingId);
                if(updateStatus($params) == 1)
                   {
                      $sms = secure_string("Dear ".$name.", Your payment to ".SYSTEM_NAME." has been updated to STATUS '".$status."'. Thank you.");
                      notifypayer($sms, smsphoneformat($phone));

                      $resp="pesapal_notification_type=$pesapalNotification&pesapal_transaction_tracking_id=$pesapalTrackingId&pesapal_merchant_reference=$pesapal_merchant_reference";
                      ob_start();
                      echo $resp;
                      ob_flush();
                      exit;
                   }
              }
              elseif(!isset($pesapalNotification) && $pesapalTrackingId != ''){
                $token = $params = NULL;
                $consumer = new OAuthConsumer($consumer_key, $consumer_secret);
                //get transaction status
                $request_status = OAuthRequest::from_consumer_and_token($consumer, $token, "GET", $statusrequestAPI, $params);
                $request_status->set_parameter("pesapal_merchant_reference", $pesapal_merchant_reference);
                $request_status->set_parameter("pesapal_transaction_tracking_id",$pesapalTrackingId);
                $request_status->sign_request($signature_method, $consumer, $token);
                $params = array ($pesapalTrackingId, $pesapal_merchant_reference, "INITIALIZED" );
                updateTrackingID($params);
                $params = array("PENDING", $pesapal_merchant_reference, $pesapalTrackingId);
                updateStatus($params);

                //REQUEST PAYMENT STATUS FROM PESAPAL
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $request_status);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                //added a curl follow redirect urls till you get status.. funny!!
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch, CURLOPT_HEADER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                if(defined('CURL_PROXY_REQUIRED')) if (CURL_PROXY_REQUIRED == 'True'){
                  $proxy_tunnel_flag = (defined('CURL_PROXY_TUNNEL_FLAG') && strtoupper(CURL_PROXY_TUNNEL_FLAG) == 'FALSE') ? false : true;
                  curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, $proxy_tunnel_flag);
                  curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
                  curl_setopt($ch, CURLOPT_PROXY, CURL_PROXY_SERVER_DETAILS);
                }
                $response = curl_exec($ch);
                $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
                $raw_header  = substr($response, 0, $header_size - 4);
                $headerArray = explode("\r\n\r\n", $raw_header);
                $header      = $headerArray[count($headerArray) - 1];
                //GET transaction status
                $elements = preg_split("/=/",substr($response, $header_size));
                $status = $elements[1];
                //CLOSE REMOTE API EXECUTION
                curl_close($ch);

                //UPDATE DB TABLE WITH NEW STATUS FOR TRANSACTION WITH $pesapalTrackingId
                if($status != "PENDING"){
                  //get payment method
                  ###########################################################  
                  $request_method = OAuthRequest::from_consumer_and_token($consumer, $token, "GET", $methodrequestAPI, $params);
                  $request_method->set_parameter("pesapal_merchant_reference", $pesapal_merchant_reference);
                  $request_method->set_parameter("pesapal_transaction_tracking_id",$pesapalTrackingId);
                  $request_method->sign_request($signature_method, $consumer, $token);
									###########################################################
                  $ch = curl_init();
                  curl_setopt($ch, CURLOPT_URL, $request_method);
                  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                  curl_setopt($ch, CURLOPT_HEADER, 1);
                  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                  if(defined('CURL_PROXY_REQUIRED')) if (CURL_PROXY_REQUIRED == 'True'){                
                    $proxy_tunnel_flag = (defined('CURL_PROXY_TUNNEL_FLAG') && strtoupper(CURL_PROXY_TUNNEL_FLAG) == 'FALSE') ? false : true;
                    curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, $proxy_tunnel_flag);
                    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
                    curl_setopt($ch, CURLOPT_PROXY, CURL_PROXY_SERVER_DETAILS);
                  }
                  $method_r = curl_exec($ch);
                  $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
                  $raw_header  = substr($response, 0, $header_size - 4);
                  $headerArray = explode("\r\n\r\n", $raw_header);
                  $header      = $headerArray[count($headerArray) - 1];
                  //GET payment method
                  $element = preg_split("/=/",substr($method_r, $header_size));
                  $method = $element[1];
                  //print_r($element);
                  $pay_method= explode(",",$method);
                  $pay_method= $pay_method[1];
									curl_close($ch);
                  #############################################################
                  $CONFIRM['MSG'] = ConfirmMessage('Your payment\'s status is: <b>'.$status.'</b>. Check your email for further details. <a href="'.PARENT_HOME_URL.'">Click here to go back to the main page</a> or <a href="'.SYSTEM_URL.'">Click here to go back to your dashboard</a>');
                  //see if there is
                  if(db_num_rows($result)>0){                    
                    //go back home                   
                    redirect(PARENT_HOME_URL);
                  }
                  $params = array($status, $pesapal_merchant_reference, $pesapalTrackingId);
                  updateStatus($params);
                  $params = array($pay_method, $pesapal_merchant_reference, $pesapalTrackingId);
                  updatePayMethod($params);
                }
                if($status != "PENDING" && $status != "FAILED" && $status != "INVALID"){
                  $_SESSION['MSG']=$CONFIRM['MSG'];
                  //email/sms redirect to thank you
                  $params = array($pesapalTrackingId);
                  //print_r(getPayingUser($params));
                  $amount = getPayingUser($params)['payment_amount'];
                  $name = getPayingUser($params)['stud_full_name'];
                  $email = getPayingUser($params)['stud_email'];
                  $phone = getPayingUser($params)['stud_tel'];
									$subject = SYSTEM_NAME." - Payment Status";
                  $content = 'Dear '.$name.',<br> Your payment of KES '.$amount.' to '.SYSTEM_NAME.' Status is: <b>'.$status.'</b>. Thank you for being part of us.
                  <br><b>Payment Details</b>
                  Name: '.$name.'<br />
								Email: '.$email.'<br />
								Phone: '.$phone.'<br />
								Transaction Ref: '.getPayingUser($params)['student_id'].'</p>
								<br />
								<p style="color:#753b01;">Thank you<br /><br />
								 Accounts & Finance Office,<br />
								'.SYSTEM_NAME.',<br />
								'.COMPANY_ADDRESS.'<br />
								TEL: '.COMPANY_PHONE.'<br />
								EMAIL: '.INFO_EMAIL.'<br />
								WEBSITE: '.PARENT_HOME_URL.'</p>
                  ';
                  mail_config($email, $name, $subject, $content);
                  //2. sms
                  $sms = "Your payment of KES ".$amount." Is in processing. We will update. Thank you.";
                  notifypayer($sms, smsphoneformat($phone));
                  echo $CONFIRM['MSG'];
                  exit;
                  //redirect("?do=thanks");
                }
                //if payment didnt succeed
                elseif($status != "COMPLETED" && $status != "PENDING"){
                  $ERROR['MSG'] = ErrorMessage('Your payment '.$status.'. Please <a href="?do=register&task=pay" class="btn btn-primary">Go back and try again using a different method</a>');
                  //email/SMS user the failed payment issue
                  $params = array($pesapalTrackingId);
                  $amount = getPayingUser($params)['payment_amount'];
                  $name = getPayingUser($params)['stud_full_name'];
                  $email = getPayingUser($params)['stud_email'];
                  $phone = getPayingUser($params)['stud_tel'];
									$subject = SYSTEM_NAME." - Payment Status";
                  $content = 'Dear '.$name.',<br> Your payment of KES '.$amount.' to '.SYSTEM_NAME.' <b>'.$status.'</b>. Make sure you try again with alternative payment method.<br>'.SYSTEM_NAME.'<br>';
                  mail_config($email, $name, $subject, $content);
                  $sms = "Dear Admin, ".$name." tried paying but payment failed. click on ".smsphoneformat($phone)." to call and assist them.";
                  notifypayer($sms, smsphoneformat(COMPANY_PHONE));
                  echo $ERROR['MSG'];
                  exit;
                }
              }
              //break if no task case and default.
            break;
						case "mpesa":
						  require_once("$class_dir/class.mpesa.php");
							$MerchantRequestID = $_SESSION['MERCHANTID'];
							$CheckoutRequestID = $_SESSION['CHECKOUTREQUESTID'];
							
							$Mpesa = new Mpesa();
							
							$timestamp = date("Ymdhis");
        			$password = base64_encode(MPESA_SHORTCODE.MPESA_PASSKEY.$timestamp);
							$confirmquery = $Mpesa->STKPushQuery(MPESA_API_ENV, $CheckoutRequestID, MPESA_SHORTCODE, $password, $timestamp);
							
							$data = json_decode($confirmquery, true);						
							
							$status = "";
							$redirect = 0;
							$pay_method = "LIPA_NA_MPESA";							
							
							//[errorCode] => 500.001.1001, [errorMessage] => The transaction is being processed
							if($data['errorCode']){
								echo ErrorMessage("Response from M-Pesa server: [".$data['errorCode']."] ".$data['errorMessage'].". Try again or choose a different payment method.");
								echo "<p><a href=\"?do=payment&paymentmethod=mpesa\" class=\"btn btn-success\">Try Again</a> <a href=\"?do=payment&paymentmethod=pesapal\" class=\"btn btn-danger\">Pay with PesaPal</a></p>";
							}else{
								//[ResponseCode] => 0, [ResponseDescription] => The service request has been accepted successsfully
								//[MerchantRequestID] && [CheckoutRequestID] => SET
								if(intval($data['ResponseCode']) == 0 && isset($data['ResultCode']) && !empty($data['MerchantRequestID']) && !empty($data['CheckoutRequestID'])){
									switch(intval($data['ResultCode'])){
										//COMPLETED
										//[ResultCode] => 0, [ResultDesc] => The service request is processed successfully.
										case 0:
											$redirect = 1;
											$status = "COMPLETED";									
											$CONFIRM['MSG'] = ConfirmMessage('Your payment has COMPLETED. Check your email for further details. <a href="'.PARENT_HOME_URL.'">Click here to go back to the main page</a> or <a href="'.SYSTEM_URL.'">Click here to go back to your dashboard</a>');
										break;
										//FAILED
										//[ResultCode] => 1, [ResultDesc] => [MpesaCB - ]The balance is insufficient for the transaction.
										case 1:
											$status = "FAILED";
											echo AttentionMessage('Your payment has FAILED. The balance is insufficient for the transaction.');
											echo "<p><a href=\"?do=payment&paymentmethod=mpesa\" class=\"btn btn-success\">Try Again</a> <a href=\"?do=payment&paymentmethod=pesapal\" class=\"btn btn-danger\">Pay with PesaPal</a></p>";
										break;
										//PROCESSING
										//[ResultCode] => 1001, [ResultDesc] => [STK_CB - ]Unable to lock subscriber, a transaction is already in process for the current subscriber
										case 1001:
											$status = "PROCESSING";
											echo AttentionMessage('Your payment is PROCESSING. A transaction is already in process. Check your phone for M-Pesa popup and enter your PIN.');
											echo "<p><a href=\"?do=payment&paymentmethod=mpesa\" class=\"btn btn-success\">Try Again</a> <a href=\"?do=payment&paymentmethod=pesapal\" class=\"btn btn-danger\">Pay with PesaPal</a></p>";
										break;
										//CANCELLED
										//[ResultCode] => 1032, [ResultDesc] => STK_CBRequest cancelled by user
										case 1032:
											$status = "CANCELLED";
											echo AttentionMessage('Your payment has been CANCELLED. Try again or choose a different payment method.');
											echo "<p><a href=\"?do=payment&paymentmethod=mpesa\" class=\"btn btn-success\">Try Again</a> <a href=\"?do=payment&paymentmethod=pesapal\" class=\"btn btn-danger\">Pay with PesaPal</a></p>";
										break;
										//FAILED
										//[ResultCode] => 1037, [ResultDesc] => [STK_CB - ]DS timeout.
										case 1037:
											$status = "FAILED";
											echo AttentionMessage('Your payment FAILED. The system has timed out. Try again or choose a different payment method.');
											echo "<p><a href=\"?do=payment&paymentmethod=mpesa\" class=\"btn btn-success\">Try Again</a> <a href=\"?do=payment&paymentmethod=pesapal\" class=\"btn btn-danger\">Pay with PesaPal</a></p>";
										break;
										//FAILED										
										//[ResultCode] => 2001, [ResultDesc] => [MpesaCB - ]The initiator information is invalid.
										case 2001:
											$status = "FAILED";
											echo ErrorMessage('Your payment has FAILED. The details provided to M-Pesa were invalid. Try again or choose a different payment method.');
											echo "<p><a href=\"?do=payment&paymentmethod=mpesa\" class=\"btn btn-success\">Try Again</a> <a href=\"?do=payment&paymentmethod=pesapal\" class=\"btn btn-danger\">Pay with PesaPal</a></p>";
										break;
										//PENDING
										//For any other ResultCode
										default:
											//log errors
											$errorsSql = sprintf("INSERT INTO `".DB_PREFIX."mpesa_errorcodes` (`TrackingID`, `ErrorCode`, `ErrorDescription`) VALUES ('%s','%s','%s')", $data['CheckoutRequestID'], $data['ResultCode'], $data['ResultDesc']);
											db_query($errorsSql,DB_NAME,$conn);
											$redirect = 1;
											$status = "PENDING";
											$CONFIRM['MSG'] = AttentionMessage('Your payment is PENDING. Response from M-Pesa server: ['.$data['ResultCode'].'] '.$data['ResultDesc'].'. Check your email for further details. <a href="'.PARENT_HOME_URL.'">Click here to go back to the main page</a> or <a href="'.SYSTEM_URL.'">Click here to go back to your dashboard</a>');
										break;
									}
								}																								
								
								//Save update to DB	for different responses
								if($status){
									$updateSQL = sprintf("UPDATE `".DB_PREFIX."payment_refs` SET `pay_status` = '%s', `pay_method` = '%s' WHERE `student_pay_ref` = '%s' AND `transaction_tracking_id` = '%s'", $status, $pay_method, $data['MerchantRequestID'], $data['CheckoutRequestID']);
									db_query($updateSQL,DB_NAME,$conn);
									
									//temporary
									$amount = !empty($_SESSION['AMOUNT'])?$_SESSION['AMOUNT']:0;
									$formatted_amount = number_format($amount, 2);//format amount to 2 decimal places
									$phonenumber = !empty($_SESSION['STUD_TEL'])?$_SESSION['STUD_TEL']:'';
									$sms = "Your payment of KES ".$formatted_amount." to ".SYSTEM_NAME." was updated with status ".$status.". Thank you.";
                  notifypayer($sms, smsphoneformat($phonenumber));
									
									$_SESSION['MSG']=$CONFIRM['MSG'];
									if($redirect){
										redirect("?do=thanks");
									}
								}
							}																			
						break;
            default:
              echo ErrorMessage("Invalid request! The system failed to process your request. If the problem persists, please contact us.");
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
