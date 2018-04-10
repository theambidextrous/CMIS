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
          //Open database connection
          $conn = db_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
          //Get requested task/default is add
          $task = isset($_GET['task'])?$_GET['task']:"add";

          $task = strtolower($task);
          switch($task) {
            case "add":
              // Array to store the messages
              $CONFIRM = array();
              //INITIATE CREDENTIALS
              $token = $params = NULL;
              $consumer_key = PESAPAL_CONSUMER_KEY;
              $consumer_secret = PESAPAL_CONSUMER_SECRET;
              $statusrequestAPI = PESAPAL_STATUS_API;
              $methodrequestAPI = PESAPAL_DETAILS_API;

              // Parameters sent to you by PesaPal IPN
              $pesapalNotification = $_GET['pesapal_notification_type'];
              $pesapalTrackingId = $_GET['pesapal_transaction_tracking_id'];
              $pesapal_merchant_reference = $_GET['pesapal_merchant_reference'];
              $signature_method = new OAuthSignatureMethod_HMAC_SHA1();

              if($pesapalNotification == "CHANGE" && $pesapalTrackingId != ''){
              ///
              }elseif($pesapalTrackingId != ''){
                $token = $params = NULL;
                $consumer = new OAuthConsumer($consumer_key, $consumer_secret);

                //get transaction status
                $request_status = OAuthRequest::from_consumer_and_token($consumer, $token, "GET", $statusrequestAPI, $params);
                $request_status->set_parameter("pesapal_merchant_reference", $pesapal_merchant_reference);
                $request_status->set_parameter("pesapal_transaction_tracking_id",$pesapalTrackingId);
                $request_status->sign_request($signature_method, $consumer, $token);

                //UPDATE PAYMENT STATUS TO PENDING AND ADD TRACKING_ID
                $stud_full_name=$_SESSION['STUD_FNAME'].' '.$_SESSION['STUD_LNAME'];
                $student_pay_ref=$_SESSION['STUD_ID_HASH'];
								$updateSQL = sprintf("UPDATE `".DB_PREFIX."payment_refs` SET `transaction_tracking_id`='%s', `pay_status`='%s' WHERE `student_pay_ref`='%s' AND `pay_status`='%s'", $pesapalTrackingId, 'PENDING', $student_pay_ref, 'Not Started');
                db_query($updateSQL,DB_NAME,$conn);

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
                  //BT JUST B4 THAT, CHECK IF THIS PAYMENT IS IN DB ALREADY!!
                  $checkDuplicateSql = sprintf("SELECT `student_pay_ref` FROM `".DB_PREFIX."payment_refs` WHERE `pay_status` != '%s' AND `pay_status` != '%s' AND `transaction_tracking_id` = '%s'", 'PENDING', 'Not Started', $pesapalTrackingId);
                  $result = db_query($checkDuplicateSql,DB_NAME,$conn);
                  //create success message
                  $CONFIRM['MSG'] = ConfirmMessage('Your payment '.$status.'. Check your email for further details. <a href="'.PARENT_HOME_URL.'">Click here to go back to the main page</a> or <a href="'.SYSTEM_URL.'">Click here to go back to your dashboard</a>');
                  //see if there is
                  if(db_num_rows($result)>0){                    
                    //go back home                   
                    redirect(PARENT_HOME_URL);
                  }
                  //db_free_result($result);

                  //update db with new status now
									$updateSQL = sprintf("UPDATE `".DB_PREFIX."payment_refs` SET `pay_status` = '%s', `pay_method` = '%s' WHERE `student_pay_ref` = '%s' AND `transaction_tracking_id` = '%s'", $status, $pay_method, $pesapal_merchant_reference, $pesapalTrackingId);
                  db_query($updateSQL,DB_NAME,$conn);
                }
                //reload if payment is pending
                if($status == "PENDING"){
                  redirect("?do=return_api&pesapal_transaction_tracking_id=$pesapalTrackingId&pesapal_merchant_reference=$pesapal_merchant_reference");
                }
                //if payment succeded
                if(db_query && $status != "PENDING" && $status != "FAILED" && $status != "INVALID"){
                  $resp="pesapal_notification_type=$pesapalNotification&pesapal_transaction_tracking_id=$pesapalTrackingId&pesapal_merchant_reference=$pesapal_merchant_reference";
                  //ob_start();
                  // echo $resp;
                  //ob_flush();
                  //update this student regfee payment in students relation
									//$updateSQL = sprintf("UPDATE `".DB_PREFIX."students` SET `regfee` = %d WHERE `payment_ref` = '%s'", 1, $pesapal_merchant_reference);
                  //db_query($updateSQL,DB_NAME,$conn);	
                  $_SESSION['MSG']=$CONFIRM['MSG'];
                  $_SESSION['STUD_FULLNAME']=$stud_full_name;
                  //redirect to thank you
                  redirect("?do=thanks");
                }
                //if payment didnt succeed
                elseif(db_query && $status != "COMPLETED" && $status != "PENDING"){
                  $resp="pesapal_notification_type=$pesapalNotification&pesapal_transaction_tracking_id=$pesapalTrackingId&pesapal_merchant_reference=$pesapal_merchant_reference";
                  //ob_start();
                  // echo $resp;
                  //ob_flush();
                if(isset($_SESSION['IS_FEE'])){
                  $ERROR['MSG'] = ErrorMessage('Your payment '.$status.'. Please <a href="?do=payment&action=fee" class="btn btn-primary">Go back and try again using a different method</a>');
                }else{
                  $ERROR['MSG'] = ErrorMessage('Your payment '.$status.'. Please <a href="?do=register&task=pay" class="btn btn-primary">Go back and try again using a different method</a>');
                }
                  //email user the failed payment issue
                  $amount = $_SESSION['AMOUNT'];
                  $name = $_SESSION['STUD_FULLNAME'];
									$subject = SYSTEM_NAME." - Payment Failed";
                  $content = 'Dear '.$name.',<br> Your payment to '.SYSTEM_NAME.' failed. Make sure you try again with alternative payment method.<br>'.SYSTEM_NAME.'<br>';
                  $email = $_SESSION['STUD_EMAIL'];
                  mail_config($email, $name, $subject, $content);
                  echo $ERROR['MSG'];
                  exit;
                }
              }
              //break if no task case and default.
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
