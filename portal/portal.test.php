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
document.title = "<?=SYSTEM_SHORT_NAME?> - Portal | Test";
//-->
</script>

<div class="container">
  <div class="row">
    <div class="col-md-12">
      <div class="header-img"><img class="img-responsive" src="<?=SYSTEM_LOGO_URL;?>" alt="Logo"></div>
      <div class="activate-panel panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Test</h3>
        </div>
        <div class="panel-body">
          <?php
					define("MPESA_API_ENV","live"); // Either "sandbox" or "live"
					define("MPESA_CONSUMER_KEY","8E1tGJmu5THzdSBwDpiH4PhJxCJnHYZ7");
					define("MPESA_CONSUMER_SECRET","Xg3CNmWS05JI9cDH");
					define("MPESA_SHORTCODE","566339");
					define("MPESA_PASSKEY","58e1104f12e4488a82cdc6d16a1b91cee9f2972307e6c83c30a353fcee843ed5");
					define("MPESA_CALLBACKURL","https://finstockevarsity.com/cmis/portal/?do=test&action=callback");
					
					require_once("$class_dir/class.OAuth.php");
					require_once("$class_dir/class.mpesa.php");										
					
					$Mpesa = new Mpesa();
					
					$action = isset($_GET["action"])?$_GET["action"]:"request";
					
					switch ($action) {
            case "request":
							$TransactionType = "CustomerPayBillOnline";
							$Amount = 10; 
							$PartyA = "254721280285";
							$PartyB = MPESA_SHORTCODE;
							$PhoneNumber = "254721280285";
							$CallBackURL = MPESA_CALLBACKURL;
							$AccountReference = "INV101"; 
							$TransactionDesc = $TransactionType;
							$Remark = "Sample Live Test";
							
							$response = $Mpesa->STKPushSimulation(MPESA_SHORTCODE, MPESA_PASSKEY, $TransactionType, $Amount, $PartyA, $PartyB, $PhoneNumber, $CallBackURL, $AccountReference, $TransactionDesc, $Remark);							
							
							/*
							echo "<pre>";
							print_r($response);
							echo "</pre>";
							*/
							
							$data = json_decode($response, true);
							//Save to DB														
							//set up critical sesions
							$_SESSION['MERCHANTID'] = $data['MerchantRequestID'];
							$_SESSION['CHECKOUTREQUESTID'] = $data['CheckoutRequestID'];
							//redirect(MPESA_CALLBACKURL);
							//echo 'MerchantRequestID -> '.$_SESSION['MERCHANTID'].'<br/>';
							//echo 'CheckoutRequestID -> '.$_SESSION['CHECKOUTREQUESTID'].'<br/>';
						break;
						case "callback":
							$timestamp = date("Ymdhis");
        			$password = base64_encode(MPESA_SHORTCODE.MPESA_PASSKEY.$timestamp);
							$confirmquery = $Mpesa->STKPushQuery(MPESA_API_ENV, $_SESSION['CHECKOUTREQUESTID'], MPESA_SHORTCODE, $password, $timestamp);
							
							echo 'MerchantRequestID -> '.$_SESSION['MERCHANTID'].'<br/>';
							echo 'CheckoutRequestID -> '.$_SESSION['CHECKOUTREQUESTID'].'<br/>';
							
							echo "<pre>";
							print_r($confirmquery);
							echo "</pre>";
							
							$data = json_decode($confirmquery, true);
							//Save update to DB
							//set up critical sesions
							//$_SESSION['RESULTCODE'] = $data['ResultCode'];
							//wait for 20 seconds					
							if( !empty($_SESSION['CHECKOUTREQUESTID']) && intval($data['ResultCode']) != 0 ){
								//Pending:- notify user the error status								
								echo "Response from MPesa server: [".$data['ResultCode']."] ".$data['ResultDesc']."<br/>";
								echo '<a href="'.MPESA_CALLBACKURL.'" class="btn btn-lg btn-primary">Check Status</a>';
							}elseif( !empty($_SESSION['CHECKOUTREQUESTID']) && intval($data['errorCode']) != 0 ){
								echo "Response from MPesa server: [".$data['errorCode']."] ".$data['errorMessage']."<br/>";
							}else{
								//Successful:- redirect to thankyou page
								echo $data['ResultDesc'];
							}
						break;
						case 'reverse':
							$CommandID = 'TransactionReversal';
							$Initiator = 'apitest361';
							$SecurityCredential = '361reset';
							$TransactionID = 'MCX704XQ2Y6';
							$Amount = '10';
							$ReceiverParty = MPESA_SHORTCODE;
							$RecieverIdentifierType = 11;
							$ResultURL = MPESA_CALLBACKURL;
							$QueueTimeOutURL = MPESA_CALLBACKURL;
							$Remarks = 'Test reversal';
							$Occasion = '';
							
							$reversal = $Mpesa->reversal($CommandID, $Initiator, $SecurityCredential, $TransactionID, $Amount, $ReceiverParty, $RecieverIdentifierType, $ResultURL, $QueueTimeOutURL, $Remarks, $Occasion);
							
							echo "<pre>";
							print_r($reversal);
							echo "</pre>";
							
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