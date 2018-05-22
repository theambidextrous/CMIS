<?php
/*********************************************
Company:		Wits Technologies Ltd
Developer:	Sammy Mwaura Waweru
Mobile:			+254721428276
Email:			sammy@witstechnologies.co.ke
Website:		http://www.witstechnologies.co.ke/
*********************************************/

if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();

$incl_dir = "../../includes";
$class_dir = "../../classes";

include "$incl_dir/config.php";
require_once("$incl_dir/mysqli.functions.php");
require_once("$incl_dir/functions.php");
require_once('../portal.functions.php');
//rating
require_once("$class_dir/IO/IO.Rating.class.php");

//mailer
require_once("$class_dir/phpmailer/src/Exception.php");
require_once("$class_dir/phpmailer/src/PHPMailer.php");
require_once("$class_dir/phpmailer/src/SMTP.php");

$tab = intval(! empty($_GET['tab']))?$_GET['tab']:1;
$_SESSION['CourseID'] = isset($_GET['CourseID'])?$_GET['CourseID']:$_SESSION['CourseID'];
$CourseID = $_SESSION['CourseID'];

if(checkLoggedin() && !empty($_SESSION['usrusername'])){

//Open database connection
$conn = db_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
//Get Student ID from the login session
$EditID = !empty($_SESSION['usrusername'])?$_SESSION['usrusername']:"";

//Collect data for display
//Fetch student data
$student = getStudentDetails($EditID);

$_SESSION['CALENDAR_EVENTS'] = !empty($_SESSION['CALENDAR_EVENTS'])?$_SESSION['CALENDAR_EVENTS']:array();
//array_push($_SESSION['CALENDAR_EVENTS'], array('title' => 'Happy Birthday', 'start' => $student['DOB']));
//array_push($_SESSION['CALENDAR_EVENTS'], array('title' => 'Happy Birthday', 'start' => '2018-03-18'));
//array_push($_SESSION['CALENDAR_EVENTS'], array('title' => 'New Day', 'start' => '2018-03-20'));

switch ($tab) {
	case 1: $menu1 = "active";
break;
	case 2: $menu2 = "active";
break;
	case 3: $menu3 = "active";
break;
	case 4: $menu4 = "active";
break;
	case 5: $menu5 = "active";
break;
	case 6: $menu6 = "active";
break;
	case 7: $menu7 = "active";
break;
	case 10: $menu10 = "active";
break;
	case 8: $menu8 = "active";
break;
}

add_header();
//echo (new IORating("FE234", "UNIT002", "Lesson001", "sv", DB_NAME, $conn))->SearchDB($conn);
?>
<style>
 .rating {
            float:left;
            width:300px;
        }
        .rating span { float:right; position:relative; }
        .rating span input {
            position:absolute;
            top:0px;
            left:0px;
            opacity:0;
        }
        .rating span label {
            display:inline-block;
            width:30px;
            height:30px;
            text-align:center;
            color:#FFF;
            background:#ccc;
            font-size:30px;
            margin-right:2px;
            line-height:30px;
            border-radius:50%;
            -webkit-border-radius:50%;
        }
        .rating span:hover ~ span label,
        .rating span:hover label,
        .rating span.checked label,
        .rating span.checked ~ span label {
            background:#F90;
            color:#FFF;
        }
				.img-thumbnail {
				max-width: 100%;
				height: 50px!important;
		}
</style>
<script>
//JQuery Functions
$(document).ready(function() {
	// International Phone format with validator
	var telInput = $("input[type='tel']");
	var validateMsg = $("#validate-msg");
	
	// initialise plugin
	telInput.intlTelInput({
		autoPlaceholder: false,
		formatOnDisplay: true,
		geoIpLookup: function(callback) {
			jQuery.get('https://ipinfo.io', function() {}, "jsonp").always(function(resp) {
			var countryCode = (resp && resp.country) ? resp.country : "";
			callback(countryCode);
			});
		},
		initialCountry: "auto",
		nationalMode: false,
		preferredCountries: ['ke', 'ug', 'tz'],
		utilsScript: "<?=THEME_URL?>/vendor/int-tel-input/lib/libphonenumber/build/utils.js"
	});
	
	var reset = function() {
		telInput.removeClass("error");
		validateMsg.addClass("hide");
	};
	
	// on blur: validate
	telInput.blur(function() {
		reset();
		if ($.trim(telInput.val())) {
			if (telInput.intlTelInput("isValidNumber")) {
				validateMsg.addClass("hide");		
			} else {				
				validateMsg.removeClass("hide");
				validateMsg.html( '<em id="phonenumber-error" class="error">Valid number is required.</em>' );
			}
		}
	});
	
	// on keyup / change flag: reset
	telInput.on("keyup change", reset);
	
	//Timepicker
	$('.timepicker').timepicker({
		'showDuration': true,
		'timeFormat': 'g:ia'
	});
	
	//Datepicker	
	$('.datepicker').datepicker({
		'format': 'm/d/yyyy',
		'autoclose': true
	});

});
//
function comfirmDelete(){
	return confirm("This operation will DELETE the selected records. Are you sure you want to delete?");
}
//
function timeMsg(){
	window.setTimeout("clearMsg()",10000);//10secs
}
//
function clearMsg(){
	document.getElementById("hideMsg").innerHTML = "";
}
//
function checkAll(field){
	if(document.display.master.checked == true){
		for(var i=0; i < field.length; i++){
			field[i].checked=true;
		}
	}
	else{
		for(var i=0; i < field.length; i++){
			field[i].checked=false;
		}
	}
}

//-->
$(document).ready(function(){
//  Check Radio-box
    $(".rating input:radio").attr("checked", false);
    $('.rating input').click(function () {
        $(".rating span").removeClass('checked');
        $(this).parent().addClass('checked');
    });

    $('input:radio').change(
    function(){
        var userRating = this.value;
        //alert(userRating);
    }); 
});
</script>
<div class="wrapper">
<?php echo (new IORating("FE234", "UNIT002", "Lesson001", "sv", DB_NAME, $conn))->CeateSurveyModal(); ?>
  <!-- Navigation -->
  <nav class="navbar navbar-default navbar-static-top" role="navigation">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#"><img src="<?=SYSTEM_LOGO_URL?>" class="img-responsive"></a>
    </div>
    <div class="text-right navbar-top-date"><?=date('l, F j, Y');?> | Logged in as: <strong><?=$student['StudentID'];?></strong></div>
    
    <ul class="nav navbar-top-links navbar-right">
      <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
          <i class="fa fa-bars fa-fw"></i> Select Course <i class="fa fa-caret-down"></i>
        </a>
        <ul class="dropdown-menu dropdown-courses">
				<?php echo list_student_courses_nav($student['Courses'],$tab); ?>
          <li>
            <a class="text-center" href="?tab=2"><strong>View All Courses</strong><i class="fa fa-angle-right"></i></a>
          </li>
        </ul>
      </li>
      <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
          <i class="fa fa-envelope fa-fw"></i> <i class="fa fa-caret-down"></i>
        </a>
        <ul class="dropdown-menu dropdown-messages">
          <?php echo list_message_snapshots($student['Email']); ?>
          <li>
            <a class="text-center" href="?tab=7"><strong>Read All Messages</strong><i class="fa fa-angle-right"></i></a>
          </li>
        </ul>
        <!-- /.dropdown-messages -->
      </li>
      <!-- /.dropdown -->
      <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
          <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
        </a>
        <ul class="dropdown-menu dropdown-user">                        
          <li><a href="?tab=8&task=view"><i class="fa fa-user fa-fw"></i> User Profile</a></li>
          <li class="divider"></li>
          <li><a href="../?do=logout"><i class="fa fa-sign-out fa-fw"></i> Logout</a></li>
        </ul>
        <!-- /.dropdown-user -->
      </li>
      <!-- /.dropdown -->
    </ul>
    <!-- /.navbar-top-links -->
    
    <div class="navbar-default sidebar" role="navigation">
      <div class="sidebar-nav navbar-collapse">
        <ul class="nav" id="side-menu">                  
          <li><a  href="?tab=1" class="<?=$menu1?>"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a></li>
          <?php if(!empty($CourseID)){?>
          <li>
          <a href="#" class="<?=$menu2?>"><i class="fa fa-graduation-cap fa-fw"></i> Course Details<span class="fa arrow"></span></a>
            <ul class="nav nav-second-level">
              <li><a href="?tab=2&task=view&CourseID=<?=$CourseID?>">Course Outline</a></li>
              <li><a href="?tab=2&task=register&CourseID=<?=$CourseID?>">Register Now</a></li>
							<li><a href="?tab=2&task=courseunits&CourseID=<?=$CourseID?>">Registered Units</a></li>
              <li><a href="?tab=2&task=coursework&CourseID=<?=$CourseID?>">Course Work</a></li>
            </ul>
          </li>
          <?php } ?>          
          <li><a href="?tab=3" class="<?=$menu3?>"><i class="fa fa-calendar fa-fw"></i> Calendar</a></li>
          <li><a href="?tab=4" class="<?=$menu4?>"><i class="fa fa-edit fa-fw"></i> Assignments</a></li>
          <li><a href="?tab=5" class="<?=$menu5?>"><i class="fa fa-bell fa-fw"></i> Attendance</a></li>
					<li><a href="?tab=10" class="<?=$menu10?>"><i class="fa fa-book fa-fw"></i> Upcoming Exams</a></li>
          <li><a href="?tab=6" class="<?=$menu6?>"><i class="fa fa-file fa-fw"></i> Resources</a></li>
          <li><a href="?tab=7" class="<?=$menu7?>"><i class="fa fa-comments fa-fw"></i> Messages</a></li>
          <li>
            <a href="#" class="<?=$menu8?>"><i class="fa fa-user fa-fw"></i> Account<span class="fa arrow"></span></a>
            <ul class="nav nav-second-level">
              <li><a href="?tab=8&task=view">View Profile</a></li>
              <li><a href="?tab=8&task=edit">Edit Profile</a></li>
              <li><a href="?tab=8&task=reset">Change Password</a></li>
            </ul>
            <!-- /.nav-second-level -->
          </li>                  
          <li><a href="../?do=logout"><i class="fa fa-sign-out fa-fw"></i> Log Out</a></li>
        </ul>           
      </div>
      <!-- /.sidebar-collapse -->
    </div>    
    <!-- /.navbar-static-side -->
  </nav>
    
  <!-- page wrapper -->
  <div id="page-wrapper">
	<?php      
	$tab = intval(! empty($_GET['tab']))?$_GET['tab']:0;
	switch ($tab) {
    case 1:
      require_once('student.dashboard.php');
	  break;
	  case 2:
	  	require_once('student.courses.php');
	  break;
	  case 3:
	  	require_once('student.calendar.php');
		break;
		case 10:
	  	require_once('student.exams.php');
	  break;
	  case 4:
	  	require_once('student.assignments.php');
	  break;
	  case 5:
	  	require_once('student.attendance.php');
	  break;
	  case 6:
	  	require_once('student.files.php');
	  break;
	  case 7:
	  	require_once('student.messages.php');
	  break;
	  case 8:
	  	require_once('student.account.php');
	  break;
	  default:
	  	require_once('student.dashboard.php');
	  break;
	}
    ?>
  </div>
  <!-- /#page-wrapper -->
</div>
<!-- /#wrapper -->
<?php 
add_footer();

//Close connection
db_close($conn);
}else{	
	//Sorry! Your session has expired!
  $_SESSION['message'] = AttentionMessage("Your session has expired due to inactivity. Try login again.");
	redirect("../?url=".urlencode("?".$_SERVER['QUERY_STRING']));
}
ob_flush();
?>