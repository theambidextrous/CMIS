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

require_once("$class_dir/phpmailer/src/Exception.php");
require_once("$class_dir/phpmailer/src/PHPMailer.php");
require_once("$class_dir/phpmailer/src/SMTP.php");

$dispatcher = isset($_GET['dispatcher'])?$_GET['dispatcher']:"dashboard";
$nav[$dispatcher] = "active";

$_SESSION['UnitID'] = isset($_GET['UnitID'])?$_GET['UnitID']:$_SESSION['UnitID'];
$UnitID = $_SESSION['UnitID'];

if(checkLoggedin() && !empty($_SESSION['usrusername'])){

//Open database connection
$conn = db_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);

//Get Student ID from the login session
$EditID = !empty($_SESSION['usrusername'])?$_SESSION['usrusername']:"";

//Collect data for display
//Fetch faculty data
$faculty = getFacultyDetails($EditID);

$_SESSION['CALENDAR_EVENTS'] = !empty($_SESSION['CALENDAR_EVENTS'])?$_SESSION['CALENDAR_EVENTS']:array();
//array_push($_SESSION['CALENDAR_EVENTS'], array('title' => 'Happy Birthday', 'start' => '2018-03-30'));
//array_push($_SESSION['CALENDAR_EVENTS'], array('title' => 'New Day', 'start' => '2018-03-20'));

//Add header
add_header();
?>
<script>
<!--
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
function checkAll(field){
	if(document.getElementsByName('master').checked == true){
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
</script>
<div class="wrapper">  
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
    <!-- /.navbar-header -->
    
    <div class="text-right navbar-top-date"><?=date('l, F j, Y');?> | Logged in as: <strong><?=$faculty['FacultyID'];?></strong></div>
    
    <ul class="nav navbar-top-links navbar-right">
      <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
          <i class="fa fa-bars fa-fw"></i> Select Lecture <i class="fa fa-caret-down"></i>
        </a>
        <ul class="dropdown-menu dropdown-lectures">
          <?php echo list_faculty_lectures_nav($faculty['FacultyID'],$dispatcher); ?>
          <li>
            <a class="text-center" href="?dispatcher=lectures"><strong>View All Lectures</strong><i class="fa fa-angle-right"></i></a>
          </li>
        </ul>
      </li>
      <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
          <i class="fa fa-envelope fa-fw"></i> <i class="fa fa-caret-down"></i>
        </a>
        <ul class="dropdown-menu dropdown-messages">
		  <?php echo list_message_snapshots($faculty['Email']); ?>
          <li>
            <a class="text-center" href="?dispatcher=messages"><strong>Read All Messages</strong><i class="fa fa-angle-right"></i></a>
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
          <li><a href="?dispatcher=account&task=view"><i class="fa fa-user fa-fw"></i> User Profile</a></li>
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
          <li><a href="?dispatcher=dashboard" class="<?=$nav['dashboard']?>"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a></li>
          <li>
            <a href="#" class="<?=$nav['departments']?>"><i class="fa fa-sitemap fa-fw"></i> Departments<span class="fa arrow"></span></a>
            <?=list_lecture_depts_nav($faculty['Departments']);?>
            <!-- /.nav-second-level -->
          </li>
          <li><a href="?dispatcher=lectures" class="<?=$nav['lectures']?>"><i class="fa fa-table fa-fw"></i> Lectures</a></li>
          <li><a href="?dispatcher=calendar" class="<?=$nav['calendar']?>"><i class="fa fa-calendar fa-fw"></i> Calendar</a></li>
					<li><a href="?dispatcher=exams" class="<?=$nav['exams']?>"><i class="fa fa-book fa-fw"></i> Exams</a></li>
          <li><a href="?dispatcher=assignments" class="<?=$nav['assignments']?>"><i class="fa fa-edit fa-fw"></i> Assignments</a></li>
          <li><a href="?dispatcher=attendance" class="<?=$nav['attendance']?>"><i class="fa fa-bell fa-fw"></i> Attendance</a></li>
          <li><a href="?dispatcher=messages" class="<?=$nav['messages']?>"><i class="fa fa-comments fa-fw"></i> Messages</a></li>
          <li><a href="?dispatcher=files" class="<?=$nav['files']?>"><i class="fa fa-file fa-fw"></i> Resources</a></li>
					<li><a href="?dispatcher=forum" class="<?=$nav['forum']?>"><i class="fa fa-clipboard fa-fw"></i> Forum Board</a></li>
          <li>
            <a href="#" class="<?=$nav['account']?>"><i class="fa fa-user fa-fw"></i> Account<span class="fa arrow"></span></a>
            <ul class="nav nav-second-level">
              <li><a href="?dispatcher=account&task=view">View Profile</a></li>
              <li><a href="?dispatcher=account&task=edit">Edit Profile</a></li>
              <li><a href="?dispatcher=account&task=reset">Change Password</a></li>
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
	switch ($dispatcher) {
	  case "dashboard":
	  	require_once('faculty.dashboard.php');
	  break;
	  case "departments":
	  	require_once('faculty.departments.php');
	  break;
	  case "lectures":
	  	require_once('faculty.lectures.php');
	  break;
	  case "calendar":
	  	require_once('faculty.calendar.php');
		break;
		case "exams":
	  	require_once('faculty.exams.php');
	  break;
	  case "assignments":
	  	require_once('faculty.assignments.php');
	  break;
	  case "attendance":
	  	require_once('faculty.attendance.php');
	  break;
	  case "messages":
	  	require_once('faculty.messages.php');
	  break;
	  case "files":
	  	require_once('faculty.files.php');
	  break;
		case "forum":
	  	require_once('faculty.forum.php');
	  break;
	  case "account":
	  	require_once('faculty.account.php');
	  break;
	  default:
	  	require_once('faculty.dashboard.php');
	  break;
	}
    ?>    
  </div>
  <!-- /#page-wrapper -->
</div>
<!-- /#wrapper -->
<?php 
//Add footer
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