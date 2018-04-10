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

$tab = intval(! empty($_GET['tab']))?$_GET['tab']:0;
$menu1 = $menu2 = $menu3 = $menu4 = $menu5 = $menu6 = $menu7 = $menu8 = "";

if(checkLoggedin()){
//Open database connection
$conn = db_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);

switch ($tab) {
	case 0:
	case 1:
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
	case 8: $menu8 = "active";
break;
	case 9: $menu9 = "active";
break;
	case 10: $menu10 = "active";
break;
	case 11: $menu11 = "active";
break;
	case 12: $menu12 = "active";
break;
}

add_header("admin");
?>
<script>
<!--
//JQuery Functions
$(document).ready(function() {
	
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
				<li> <a class="text-center" href="?tab=7"> <strong>Read All Messages</strong> <i class="fa fa-angle-right"></i> </a> </li>
			</ul>
			<!-- /.dropdown-messages --> 
			</li>
			<!-- /.dropdown -->
			<li class="dropdown"> <a class="dropdown-toggle" data-toggle="dropdown" href="#"> <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i> </a>
			<ul class="dropdown-menu dropdown-user">
				<li><a href="?tab=8&task=edit&eid=<?=$_SESSION['sysUserID'];?>"><i class="fa fa-user fa-fw"></i> User Profile</a> </li>
				<li><a href="?tab=10&task=edit"><i class="fa fa-gear fa-fw"></i> Settings</a> </li>
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
					<li> <a href="?tab=1" class="menu_link <?=$menu1?>"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a> </li>
					<li> <a href="?tab=2" class="menu_link <?=$menu2?>"><i class="fa fa-sitemap fa-fw"></i> Departments</a> </li>
					<li> <a href="?tab=3" class="menu_link <?=$menu3?>"><i class="fa fa-files-o fa-fw"></i> Courses</a> </li>
					<li> <a href="?tab=4" class="menu_link <?=$menu4?>"><i class="fa fa-edit fa-fw"></i> Units</a> </li>
					<li> <a href="?tab=10" class="menu_link <?=$menu10?>"><i class="fa fa-book fa-fw"></i> Exams</a> </li>
					<li> <a href="?tab=5" class="menu_link <?=$menu5?>"><i class="fa fa-users fa-fw"></i> Students</a> </li>
					<li> <a href="?tab=6" class="menu_link <?=$menu6?>"><i class="fa fa-group fa-fw"></i> Faculties</a> </li>
					<li> <a href="?tab=9" class="menu_link <?=$menu9?>"><i class="fa fa-credit-card fa-fw"></i> Payments</a> </li>
					<li> <a href="?tab=11" class="menu_link <?=$menu11?>"><i class="fa fa-address-book fa-fw"></i> Address Book</a> </li>
					<li> <a href="?tab=12" class="menu_link <?=$menu12?>"><i class="fa fa-weixin fa-fw"></i> SMS</a> </li>
					<li> <a href="?tab=7" class="menu_link <?=$menu7?>"><i class="fa fa-comments fa-fw"></i> Messages</a> </li>
					<li> <a href="?tab=8" class="menu_link <?=$menu8?>"><i class="fa fa-users fa-fw"></i> Admin Users</a> </li>
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
		
		if (!isset($filter) && isset($_SESSION["filter"][$tab])) $filter = $_SESSION["filter"][$tab];
    if (!isset($filterfield) && isset($_SESSION["filter_field"][$tab])) $filterfield = $_SESSION["filter_field"][$tab];
		
    switch ($tab) {			
			case 0:
			case 1:
			require_once('admin.dashboard.php');
			break;
			case 2:
			require_once('admin.departments.php');
			break;
			case 3:
			require_once('admin.courses.php');
			break;
			case 4:
			require_once('admin.units.php');
			break;
			case 5:
			require_once('admin.students.php');
			break;
			case 6:
			require_once('admin.faculties.php');
			break;			
			case 7:
			require_once('admin.messages.php');
			break;	
			case 8:
			require_once('admin.users.php');
			break;
			case 9:
			require_once('admin.payments.php');
			break;
			case 10:
			require_once('admin.exams.php');
			break;
			case 11:
			require_once('admin.addressbook.php');
			break;
			case 12:
			require_once('admin.sms.php');
			break;
			case 13:
			require_once('admin.settings.php');
			break;
		}
		
		if (isset($filter)) $_SESSION["filter"][$tab] = $filter;
    if (isset($filterfield)) $_SESSION["filter_field"][$tab] = $filterfield;
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
