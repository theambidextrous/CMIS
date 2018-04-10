<?php
/*********************************************
Company:		Wits Technologies Ltd
Developer:	Sammy Mwaura Waweru
Mobile:			+254721428276
Email:			sammy@witstechnologies.co.ke
Website:		http://www.witstechnologies.co.ke/
*********************************************/

if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();

// NOTE: Requires PHP version 5 or later
if (version_compare(PHP_VERSION, '5.3.0', '>') ){
	# Load includes
	$incl_dir = "../includes";
	$class_dir = "../classes";
	$logs_dir = "../logs";
	
	include "$incl_dir/config.php";
	require_once("$incl_dir/mysqli.functions.php");
	require_once("$incl_dir/functions.php");
	require_once("admin.functions.php");
	
	require_once("$class_dir/phpmailer/src/Exception.php");
	require_once("$class_dir/phpmailer/src/PHPMailer.php");
	require_once("$class_dir/phpmailer/src/SMTP.php");
} 
else { 
	# PHP version not sufficient
	exit("This system will only run on PHP version 5.3 or higher!\n");
}

$dispatcher = isset($_GET['dispatcher'])?$_GET['dispatcher']:"dashboard";
$menu1 = $menu2 = $menu3 = $menu4 = $menu5 = $menu6 = $menu7 = $menu8 = "";

if(checkLoggedin()){
//Open database connection
$conn = db_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);

switch ($dispatcher) {
	case "dashboard":
break;
	case "departments": $menu2 = "active";
break;
	case "courses": $menu3 = "active";
break;
	case "units": $menu4 = "active";
break;
	case "students": $menu5 = "active";
break;
	case "faculties": $menu6 = "active";
break;
	case "messages": $menu7 = "active";
break;
	case "users": $menu8 = "active";
break;
	case "payments": $menu9 = "active";
break;
	case "exams": $menu10 = "active";
break;
	case "addressbook": $menu11 = "active";
break;
	case "sms": $menu12 = "active";
break;
	case "unitenrolments": $menu13 = "active";
break;
	case "courseenrolments": $menu14 = "active";
break;
}

add_header("admin");
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
	
	//Validator
	//$("#validateform").validate();
	
	//Timepicker
	$('.timepicker').timepicker({ 	  
	  'step': 15, 
	  'timeFormat': 'H:i' 
	});
	
	//Timepicker time range
	$('.timepickerrange').timepicker({ 
	  'minTime': '08:00',
      'maxTime': '22:00',
	  'step': 5,
	  'timeFormat': 'H:i' 
	});
	
	//Datepicker
	$( ".datepicker" ).datepicker({		
	  autoclose: true,
	  todayHighlight: true,
	  format: 'mm/dd/yyyy',
	  changeMonth: true,
	  changeYear: true
	});
	
	//Datepicker date range
	$('.datepicker-daterange input').each(function() {
	  $(this).datepicker({		  
		  autoclose: true,
		  todayHighlight: true,
		  format: 'mm/dd/yyyy',
		  changeMonth: true,
		  changeYear: true
	  });
	});
	
	$('#myModal').modal('show');
	
	// store the currently selected tab in the hash value
	$("ul.nav-tabs > li > a").on("shown.bs.tab", function(e) {
		var id = $(e.target).attr("href").substr(1);
		window.location.hash = id;
	});
	
	// on load of the page: switch to the currently selected tab
	var hash = window.location.hash;
	$('#multi-tabs a[href="' + hash + '"], #adv-tab-container a[href="' + hash + '"]').tab('show');
	
});

//Javascript Functions
function comfirmDelete(){
	return confirm('This operation will DELETE the selected records. Are you sure you want to delete?');
}
//
function checkAll(field){
	if(document.view.master.checked == true){
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
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
			<a class="navbar-brand" href="#"><img src="<?=SYSTEM_LOGO_URL?>" style="max-height:60px;" class="img-responsive"></a>
		</div>
		<!-- /.navbar-header -->
		
		<div class="text-right navbar-top-date">
			<?=date('l, F j, Y');?> | Logged in as: <strong><?=$_SESSION['sysUsername'];?></strong>
		</div>
		<ul class="nav navbar-top-links navbar-right">
			<li class="dropdown"> <a class="dropdown-toggle" data-toggle="dropdown" href="#"> <i class="fa fa-envelope fa-fw"></i> <i class="fa fa-caret-down"></i> </a>
			<ul class="dropdown-menu dropdown-messages">
				<?php echo list_message_snapshots($_SESSION['sysEmail']); ?>
				<li> <a class="text-center" href="?dispatcher=messages"> <strong>Read All Messages</strong> <i class="fa fa-angle-right"></i> </a> </li>
			</ul>
			<!-- /.dropdown-messages --> 
			</li>
			<!-- /.dropdown -->
			<li class="dropdown"> <a class="dropdown-toggle" data-toggle="dropdown" href="#"> <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i> </a>
			<ul class="dropdown-menu dropdown-user">
				<li><a href="?dispatcher=users&task=edit&eid=<?=$_SESSION['sysUserID'];?>"><i class="fa fa-user fa-fw"></i> User Profile</a> </li>
				<li><a href="?dispatcher=settings"><i class="fa fa-gear fa-fw"></i> Settings</a> </li>
				<li class="divider"></li>
				<li><a href="./?do=logout"><i class="fa fa-sign-out fa-fw"></i> Logout</a> </li>
			</ul>
			<!-- /.dropdown-user --> 
			</li>
			<!-- /.dropdown -->
		</ul>
		<!-- /.navbar-top-links -->
		
		<div class="navbar-default sidebar" role="navigation">
			<div class="sidebar-nav navbar-collapse">
			<ul class="nav" id="side-menu">
					<li> <a href="?dispatcher=dashboard" class="menu_link <?=$menu1?>"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a> </li>
					<li> <a href="?dispatcher=departments" class="menu_link <?=$menu2?>"><i class="fa fa-sitemap fa-fw"></i> Departments</a> </li>
					<li> <a href="?dispatcher=courses" class="menu_link <?=$menu3?>"><i class="fa fa-files-o fa-fw"></i> Courses</a> </li>
					<li> <a href="?dispatcher=units" class="menu_link <?=$menu4?>"><i class="fa fa-edit fa-fw"></i> Units</a> </li>
					<li> <a href="?dispatcher=exams" class="menu_link <?=$menu10?>"><i class="fa fa-book fa-fw"></i> Exams</a> </li>
					<li> <a href="?dispatcher=students" class="menu_link <?=$menu5?>"><i class="fa fa-users fa-fw"></i> Students</a> </li>
					<li> <a href="?dispatcher=unitenrolments" class="menu_link <?=$menu13?>"><i class="fa fa-graduation-cap fa-fw"></i> Unit Enrollments</a> </li>
					<!-- <li> <a href="?dispatcher=courseenrolments" class="menu_link <=//$menu14?>"><i class="fa fa fa-braille fa-fw"></i> Course Enrollments</a> </li> -->
					<li> <a href="?dispatcher=faculties" class="menu_link <?=$menu6?>"><i class="fa fa-group fa-fw"></i> Faculties</a> </li>
					<li> <a href="?dispatcher=payments" class="menu_link <?=$menu9?>"><i class="fa fa-credit-card fa-fw"></i> Payments</a> </li>
					<li> <a href="?dispatcher=addressbook" class="menu_link <?=$menu11?>"><i class="fa fa-address-book fa-fw"></i> Address Book</a> </li>
					<li> <a href="?dispatcher=sms" class="menu_link <?=$menu12?>"><i class="fa fa-weixin fa-fw"></i> SMS</a> </li>
					<li> <a href="?dispatcher=messages" class="menu_link <?=$menu7?>"><i class="fa fa-comments fa-fw"></i> Messages</a> </li>
					<li> <a href="?dispatcher=users" class="menu_link <?=$menu8?>"><i class="fa fa-users fa-fw"></i> Admin Users</a> </li>
				</ul>
			</div>
			<!-- /.sidebar-collapse -->
		</div>
		<!-- /.navbar-static-side --> 
	</nav>
	
	<!-- page wrapper -->
	<div id="page-wrapper">
		<?php
		if (isset($_REQUEST["filter"])) $filter = @$_REQUEST["filter"];
		if (isset($_REQUEST["filter_field"])) $filterfield = @$_REQUEST["filter_field"];
		
		if (!isset($filter) && isset($_SESSION["filter"][$dispatcher])) $filter = $_SESSION["filter"][$dispatcher];
    if (!isset($filterfield) && isset($_SESSION["filter_field"][$dispatcher])) $filterfield = $_SESSION["filter_field"][$dispatcher];
		
    switch ($dispatcher) {
			case "dashboard":
			require_once('admin.dashboard.php');
			break;
			case "departments":
			require_once('admin.departments.php');
			break;
			case "courses":
			require_once('admin.courses.php');
			break;
			case "units":
			require_once('admin.units.php');
			break;
			case "students":
			require_once('admin.students.php');
			break;
			case "faculties":
			require_once('admin.faculties.php');
			break;			
			case "messages":
			require_once('admin.messages.php');
			break;	
			case "users":
			require_once('admin.users.php');
			break;
			case "payments":
			require_once('admin.payments.php');
			break;
			case "exams":
			require_once('admin.exams.php');
			break;
			case "addressbook":
			require_once('admin.addressbook.php');
			break;
			case "sms":
			require_once('admin.sms.php');
			break;
			case "unitenrolments":
			require_once('admin.unit.enrolments.php');
			break;
			case "courseenrolments":
			require_once('admin.course.enrolments.php');
			break;
			case "settings":
			require_once('admin.settings.php');
			break;
		}
		
		if (isset($filter)) $_SESSION["filter"][$dispatcher] = $filter;
    if (isset($filterfield)) $_SESSION["filter_field"][$dispatcher] = $filterfield;
    ?>
	</div>
	<!-- /#page-wrapper -->
</div>
<!-- /#wrapper -->
<?php
add_footer("admin");

//Close connection
db_close($conn);
}
else{
	//Sorry! Your session has expired!
  $_SESSION['message'] = AttentionMessage("Your session has expired due to inactivity. Try login again.");
	redirect("./?url=".urlencode("admin.php?".$_SERVER['QUERY_STRING']));
}

ob_flush(); 
?>
