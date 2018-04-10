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
					require_once("$class_dir/class.OAuth.php");
					
					$url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
					$curl = curl_init();
					curl_setopt($curl, CURLOPT_URL, $url);
					$credentials = base64_encode('TDoogvt6SA8HW8Wm9ZWwjPH2p60ZJCIb:woNeFywaXc1yU0R8');
					curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization: Basic '.$credentials)); //setting a custom header
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($curl, CURLOPT_HEADER, false);
					curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);					
					
					$curl_response = curl_exec($curl);
					$data = json_decode($curl_response, true);

					
					$url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
					
					$curl = curl_init();
					curl_setopt($curl, CURLOPT_URL, $url);
					curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$data['access_token'])); //setting custom header
					$Shortcode = "174379";
					$Passkey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
					$Timestamp = date('Ymdhis');
					$Password = base64_encode($Shortcode.$Passkey.$Timestamp);
					
					$curl_post_data = array(
						"BusinessShortCode" => $Shortcode,
						"Password" => $Password,
						"Timestamp" => $Timestamp,
						"TransactionType" => "CustomerPayBillOnline",
						"Amount" => "10",
						"PartyA" => "254721428276",
						"PartyB" => $Shortcode,
						"PhoneNumber" => "254721428276",
						"CallBackURL" => "https://developer.safaricom.co.ke",
						"AccountReference" => "Sample Test",
						"TransactionDesc" => "Service desc"
					);
					
					echo "<pre>";
					print_r($curl_post_data);
					echo "</pre>";
					
					$data_string = json_encode($curl_post_data);
					
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($curl, CURLOPT_POST, true);
					curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
					
					$curl_response = curl_exec($curl);
					
					echo "<pre>";
					print_r($curl_response);
					echo "</pre>";
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