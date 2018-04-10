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
					echo redirect('../?do=payment&paytype=fee');
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

  </div>
	<script type="text/javascript">
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