<?php
//Import the PHPMailer class into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
?>
<script src="<?=SYSTEM_URL;?>/javascript/multifile.js"></script>
<script type="text/javascript">
<!--//
//Define page title
document.title = "<?=SYSTEM_SHORT_NAME?> - Student | My Messages";

function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}

//-->
</script>
<div class="row">
  <div class="col-lg-12">
    <h1 class="page-header">Lipa Na Mpesa</h1>
  </div>
  <!-- /.col-lg-12 -->
</div>
<!-- /.row -->

<div class="row">
  <div class="col-lg-12">
    <div class="cms-contents-grey">
      <!--Begin Forms-->
      <?php
     require_once("$class_dir/class.OAuth.php");
     require_once("$class_dir/class.mpesa.php");
     $instructions = '';									
     $Mpesa = new Mpesa();
     $action = isset($_GET["action"])?$_GET["action"]:"request";
     switch ($action) {

//SEND REQUEST TO MPESA
        case "request":
        if( isset($_POST['stk']) && !empty($_POST['phone'])){
            $phone = secure_string($_POST['phone']);
            if( strlen($phone) == 10 && substr( $phone, 0, 2 ) === "07" ){
                $phone = '254'.(int)$phone;
                $fullName = $student['FName'].' '.$student['LName'];
                $TransactionType = "CustomerPayBillOnline";
                $Amount = $_SESSION['AMOUNT']; 
                $PartyA = $phone;
                $PartyB = MPESA_SHORTCODE;
                $PhoneNumber = $phone;
                $CallBackURL = MPESA_CALLBACKURL;
                $AccountReference = $student['StudentID']; 
                $TransactionDesc = "Fees payment by lipa na mpesa";
                $Remark = "Payment by Safaricom quick STK";
                $pay_method = "LNMPSA";
                $pay_type = "Fees Payment";
                //send pay
                $response = $Mpesa->STKPushSimulation(MPESA_SHORTCODE, MPESA_PASSKEY, $TransactionType, $Amount, $PartyA, $PartyB, $PhoneNumber, $CallBackURL, $AccountReference, $TransactionDesc, $Remark);
                $data = json_decode($response, true);
                print_r($data);
            //     if( array_key_exists('CheckoutRequestID', $data) ){
            //         // print  instruction
            //     $instructions = '<h2>Check your phone</h2><p>Dear customer, <br> A payment has been sent to your phone, kindly check your phone to confirm it. This payment will be cancelled in 30 seconds if you dont enter your <b>pin</b></p>';
            //     $_SESSION['MERCHANTID'] = $data['MerchantRequestID'];
            //     $_SESSION['CHECKOUTREQUESTID'] = $data['CheckoutRequestID'];
            //     //initialize payment in DB
            //     $newPaymentSql = sprintf("INSERT INTO `".DB_PREFIX."payment_refs` (`student_id`, `student_pay_ref`, `transaction_tracking_id`, `payment_amount`, `pay_method`, `stud_tel`, `stud_full_name`, `stud_email`, `pay_type`, `pay_status`) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')", $AccountReference, $_SESSION['MERCHANTID'], $_SESSION['CHECKOUTREQUESTID'], $Amount, $pay_method, $student['Phone'], $fullName, $student['Email'], $pay_type, 'PENDING');
            //     db_query($newPaymentSql,DB_NAME,$conn);
            //     }else{
            //         print_r($response);
            //    // echo redirect('?tab=9&action=request&error=Payment gateway error');
            //     }
            }else{
                echo redirect('?tab=9&action=request&error=Invalid Phone number');
            }
        }
         break;
//CHECK STATUS OF THE  REQUEST SENT TO MPESA
         case "callback":
            $timestamp = date("Ymdhis");
            $password = base64_encode(MPESA_SHORTCODE.MPESA_PASSKEY.$timestamp);
            $confirmquery = $Mpesa->STKPushQuery(MPESA_API_ENV, $_SESSION['CHECKOUTREQUESTID'], MPESA_SHORTCODE,$password, $timestamp);
            $data = json_decode($confirmquery, true);
            if( array_key_exists('ResultCode', $data) && $data['ResultCode'] == '0'){ 
                //there was definitely a payment
                $updateSQL = sprintf("UPDATE `".DB_PREFIX."payment_refs` SET `transaction_tracking_id`='%s', `pay_status`='%s' WHERE `student_pay_ref`='%s' AND `pay_status`='%s'", $_SESSION['CHECKOUTREQUESTID'], 'COMPLETED', $_SESSION['MERCHANTID'], 'PENDING');
                db_query($updateSQL,DB_NAME,$conn);
                //HEAD OVER TO SUCCESS
                echo redirect('?tab=9&action=success&success=Payment completed Successfully!');
            }elseif(  array_key_exists('ResultCode', $data) && $data['ResultCode'] != '0' ){
                //there is a chance the payment is still processing
                $instructions = $data['ResultDesc'];
                echo redirect('?tab=9&action=callback'); 
            }elseif (  array_key_exists('errorCode', $data) ){
                echo redirect('?tab=9&action=callback&error='.$data['errorMessage']);
            }
         break;
//CANCEL /REVERSE MPESA REQUEST
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
            
             
         break;
//success
         case 'success':
         $message = $_GET['success'];
         echo '<h2>Payment processed!</h2> <div class="alert alert-success"><p>'.$message.'</p></div>';
         break;
     }										
     
     ?>					
      <!--End Forms-->
      <div class="row">
        <div class="col-md-12">
        <?php if(!empty($instructions)){ ?>
        <div class="alert alert-success">
        <?php  echo $instructions;  ?>
        </div>
        <?php } ?>
        <?php if(!empty($_GET['error'])){ ?>
        <div class="alert alert-danger">
            <?php echo $_GET['error'];  ?>
        </div>
        <?php } ?>
            <form id ="payment" class="form-inline"  method = "post">
            <input class="form-control" type="text" size="40" name ="phone" placeholder ="Enter phone number as 0705007984"/>
            <input type = "submit" class="btn btn-primary" value ="Pay Now" name ="stk"/>
            </form>
        </div>
    </div>
  </div>
</div>
<!-- /.row -->