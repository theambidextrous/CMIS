<script>
<!--//
//Define page title
document.title = "<?=SYSTEM_SHORT_NAME?> - Student | My Dashboard";
//-->
</script>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Dashboard</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->

<div class="row">
  <div class="col-lg-3 col-md-6">
    <div class="panel panel-primary">
      <div class="panel-heading">
        <div class="row">
          <div class="col-xs-3"> <i class="fa fa-sitemap fa-5x"></i> </div>
          <div class="col-xs-9 text-right">
            <div class="huge"><?php echo getStudentCourses($student['Courses']); ?></div>
            <div>Courses</div>
          </div>
        </div>
      </div>
      <a href="?tab=2">
      <div class="panel-footer"> <span class="pull-left">View Details</span> <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
        <div class="clearfix"></div>
      </div>
      </a> 
    </div>
  </div>
  <div class="col-lg-3 col-md-6">
    <div class="panel panel-green">
      <div class="panel-heading">
        <div class="row">
          <div class="col-xs-3"> <i class="fa fa-graduation-cap fa-5x"></i> </div>
          <div class="col-xs-9 text-right">
            <div class="huge"><?php echo getStudentUnits($student['StudentID'],'Registered'); ?></div>
            <div>Registered Units</div>
          </div>
        </div>
      </div>
      <a href="?tab=3">
      <div class="panel-footer"> <span class="pull-left">View Details</span> <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
        <div class="clearfix"></div>
      </div>
      </a> 
    </div>
  </div>
  <div class="col-lg-3 col-md-6">
    <div class="panel panel-red">
      <div class="panel-heading">
        <div class="row">
          <div class="col-xs-3"> <i class="fa fa-edit fa-5x"></i> </div>
          <div class="col-xs-9 text-right">
            <div class="huge"><?php echo getStudentAssignments($student['StudentID']); ?></div>
            <div>Assignments</div>
          </div>
        </div>
      </div>
      <a href="?tab=6">
      <div class="panel-footer"> <span class="pull-left">View Details</span> <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
        <div class="clearfix"></div>
      </div>
      </a> 
    </div>
  </div>
  <div class="col-lg-3 col-md-6">
    <div class="panel panel-yellow">
      <div class="panel-heading">
        <div class="row">
          <div class="col-xs-3"> <i class="fa fa-comments fa-5x"></i> </div>
          <div class="col-xs-9 text-right">
            <div class="huge"><?php echo getUserMessages($student['Email']); ?></div>
            <div>Messages</div>
          </div>
        </div>
      </div>
      <a href="?tab=5">
      <div class="panel-footer"> <span class="pull-left">View Details</span> <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
        <div class="clearfix"></div>
      </div>
      </a> 
    </div>
  </div>  
</div>
<!-- /.row -->

<div class="row">
  <div class="col-lg-7">
    <div class="panel panel-default">
      <div class="panel-heading"><i class="fa fa-clock-o fa-fw"></i> Welcome</div>
      <!-- /.panel-heading -->
      <div class="panel-body">
      
        <h1>Welcome, <?php echo $student['StudentName']; ?></h1>
        <a href="?tab=2" class = "btn btn-primary"><h1>Register Units Here</h1></a>
        <h2>Announcements</h2>
        <div class="list-announcements">
          <?php echo list_announcements("Student"); ?>
        </div>
        
        <h1>Your Courses</h1>
        <div class="list-lectures">
          <?php echo list_student_courses($student['Courses']); ?>
        </div>            
      
      </div>
      <!-- /.panel-body -->
    </div>
    <!-- /.panel-default -->
  </div>
  <div class="col-lg-5">
    <div class="panel panel-default">
      <div class="panel-heading"><i class="fa fa-money fa-fw"></i> Payment Summary</div>
      <!-- /.panel-heading -->
      <div class="panel-body">        
				<?php
				floatval($CourseFees);
				floatval($CourseTuitionFees);
				floatval($TotalCourseFee);
        floatval($TotalPaid);
        $CourseFees = 0;
        $CourseTuitionFees = 0;
        $FeesForAllRegisteredCourses=0;
        $TotalCourseFee = 0;
        $TotalPaid = getTotalPaid($student['StudentID']);
        
        $CourseIDs = !empty($CourseID)?$CourseID:$student['Courses'];
        if(!empty($CourseIDs)){
          $CourseIDs = explode(",", $CourseIDs);
            
          reset($CourseIDs);
          if(!empty($CourseIDs)){           
            //Loop foreach($CourseIDs as &$CourseID)
            while (list(, $CourseID) = each($CourseIDs)) {			  
              $CourseFees += getCourseFees($CourseID, $student['StudyMode']);
              $CourseTuitionFees += getFeesPayable($student['StudentID'], $CourseID, $student['StudyMode']);
              $FeesForAllRegisteredCourses += getCourseTuitionFees($CourseID);
            }            
          }
        }
        $FeesForAllRegisteredCourses = $FeesForAllRegisteredCourses + $CourseFees;
        $TotalCourseFee = $CourseFees + $CourseTuitionFees;
        $TotalDue =0;
        if(($TotalCourseFee - $TotalPaid) >0){
          $TotalDue = $TotalCourseFee - $TotalPaid;
        }
				
        //Create sessions for reg fee payment GATEWAY	
        $_SESSION['STUD_ID_HASH'] = md5($student['StudentID']);;
				$_SESSION['STUD_ID'] = $student['StudentID'];
				$_SESSION['STUD_FNAME'] = $student['FName'];
				$_SESSION['STUD_LNAME'] = $student['LName'];
				$_SESSION['STUD_EMAIL'] = $student['Email'];
				$_SESSION['STUD_TEL'] = $student['Phone'];
        $_SESSION['COURSE_ID'] = $CourseID;
        //create amount to pay only if form post was detected
        if(isset($_POST['pay'])){	
          $_SESSION['AMOUNT'] = 0;
          $amt = secure_string($_POST['amt']);
          if(!is_numeric($amt)){
            $_SESSION['AMOUNT'] = $TotalDue;
          }
					else{
						if($amt == ""){
								$_SESSION['AMOUNT'] = $TotalDue;
							}elseif($amt >= getMinPayable($TotalDue,$TotalCourseFee)){
								$_SESSION['AMOUNT'] = $amt; 
							}
							elseif($amt < getMinPayable($TotalDue,$TotalCourseFee)){
								$_SESSION['AMOUNT'] = $TotalDue; 
							}
					}
					//proceed to pay
					echo redirect('../?do=payment&action=fee');
        }
        //echo '<h4>Total Fees for your: '. getPaymentStatus($TotalCourseFee, $TotalPaid) .'</h4>';
				echo '<h4>Payment Status: '. getPaymentStatus($TotalCourseFee, $TotalPaid) .'</h4>';
				echo '<p class="pay_summary_total"><strong>Total Fees for registered units:</strong> Ksh.'. number_format($TotalCourseFee, 2) .'</p>';
        echo '<p class="pay_summary_paid"><strong>Total Fees Paid:</strong> Ksh.'. number_format($TotalPaid, 2) .'</p>';
        if(getOverPay($TotalPaid,$TotalCourseFee) > 0){
          echo '<p class="pay_summary_paid"><strong>Total Fees Over-paid:</strong> Ksh.'. number_format(getOverPay($TotalPaid,$TotalCourseFee)) .'</p>'; 
        }else{
          echo '<p class="pay_summary_paid"><strong>Total Fees Pending(not paid):</strong> Ksh.'. number_format($TotalDue, 2) .'</p>';
        }
        echo '<form method="post">
        <p class="text-center"><input name="amt" onkeyup="topay()" type="number" class="form-control" required="required" placeholder="Please enter amount e.g. '.number_format($TotalDue, 2).'" id="amt" autocomplete="off">
        </p>';
        echo '<p class="text-center" id="topay"></p>';
        if($TotalDue!=0){
          echo '<p class="text-center"><button id="btn-pay" type="submit" name="pay" class="btn btn-danger">Pay Now</button></p>';
        }
        echo '</form>';
				?>
      </div>
      <!-- /.panel-body -->
    </div>
    <!-- /.panel-default -->
    
    <div class="panel panel-default">
      <div class="panel-heading"><i class="fa fa-bell fa-fw"></i> Notifications Panel</div>
      <!-- /.panel-heading -->
      <div class="panel-body">
        <div class="list-group">  
          <?php echo list_login_history($student['StudentID']); ?>          
        </div>
        <a href="#" class="btn btn-default btn-block">View All Alerts</a>
      </div>
      <!-- /.panel-body -->
    </div>
    <!-- /.panel-default -->

    <?php
		$resChatRooms = db_query("SELECT * FROM `".DB_PREFIX."chat_room` WHERE `chat_room_name` = 'Student'",DB_NAME,$conn);
		while( $row = db_fetch_array($resChatRooms) ){
		?>
		<div class="chat-panel panel panel-default">
			<div class="panel-heading">
				<i class="fa fa-comments fa-fw"></i> Chat
				<div class="btn-group pull-right">
					<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
					<ul class="dropdown-menu slidedown">
						<li><a href="#"><i class="fa fa-refresh fa-fw"></i> Refresh</a></li>
						<li><a href="#"><i class="fa fa-check-circle fa-fw"></i> Available</a></li>
						<li><a href="#"><i class="fa fa-times fa-fw"></i> Busy</a></li>
						<li><a href="#"><i class="fa fa-clock-o fa-fw"></i> Away</a></li>
						<li class="divider"></li>
						<li><a href="#"><i class="fa fa-sign-out fa-fw"></i> Sign Out</a></li>
					</ul>
				</div>
			</div>
			<!-- /.panel-heading -->
			<div id="result" class="panel-body">
			</div>
			<!-- /.panel-body -->
			<div class="panel-footer">
				<form id="chatForm">
				<div class="input-group">
					<input type="hidden" value="<?php echo $row['chat_room_id']; ?>" id="id">
					<input type="hidden" value="<?php echo $student['StudentName']; ?>" id="usr">
					<input type="text" id="msg" class="form-control input-sm" placeholder="Type your message here...">
					<span class="input-group-btn"><button type="button" id="send_msg" class="btn btn-warning btn-sm">Send</button></span>
				</div>
				</form>
			</div>
			<!-- /.panel-footer -->
		</div>
		<!-- /.panel .chat-panel -->
		<?php
		}
		?>

  </div>
	<script type="text/javascript">

	$(document).ready(function(){
		/***** START CHAT SCRIPT	*****/
		setInterval(function() {
			displayResult();
			var elem = document.getElementById('result');
			elem.scrollTop = elem.scrollHeight;
		}, 2000); // every 2 seconds
		
		/* Send Message	*/			
		$('#send_msg').on('click', function(){
			if($('#msg').val() == ""){
				alert('Please write message first');
			}else{
				$msg = $('#msg').val();
				$id = $('#id').val();
				$usr = $('#usr').val();
				$.ajax({
					type: "POST",
					url: "<?php echo $incl_dir; ?>/chat/chat.php",
					data: {
						msg: $msg,
						id: $id,
						usr: $usr,
					},
					success: function(){
						displayResult();
						$('#chatForm').trigger("reset");
					}
				});
			}	
		});
	
	});
	
	function displayResult(){
		$id = $('#id').val();
		$usr = $('#usr').val();
		$.ajax({
			url: '<?php echo $incl_dir; ?>/chat/chat.php',
			type: 'POST',
			async: false,
			data:{
				id: $id,
				usr: $usr,
				res: 1,
			},
			success: function(response){
				$('#result').html(response);
			}
		});
	}	
	/***** END CHAT SCRIPT	*****/
	
	$("#btn-pay").attr("disabled", "disabled");
  // autopopuplate amount to pay
	function topay() {
			var x = document.getElementById("amt").value;
			if(x< <?php echo getMinPayable($TotalDue,$TotalCourseFee);?>){			
				$("#btn-pay").attr("disabled", "disabled");
				document.getElementById("topay").innerHTML = "The least you can pay is Ksh.<?php echo number_format(getMinPayable($TotalDue,$TotalCourseFee), 0);?>";
			}
			else{
				$("#btn-pay").removeAttr("disabled");
				document.getElementById("topay").innerHTML = 'You are about to pay KES '+x;
			}
			if(x==""){
				$("#btn-pay").removeAttr("disabled");
				document.getElementById("topay").innerHTML = 'You are about to pay KES '+<?php echo number_format($TotalDue, 0); ?>+' To Finstock Evarsity';
			}
	}
	</script>
</div>    
<!-- /.row -->