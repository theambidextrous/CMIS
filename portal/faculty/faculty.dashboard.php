<script>
<!--//
//Define page title
document.title = "<?=SYSTEM_SHORT_NAME?> - Faculty | My Dashboard";
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
            <div class="huge"><?php echo getFacultyDepartments($faculty['Departments']); ?></div>
            <div>Departments</div>
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
            <div class="huge"><?php echo getFacultyLectures($faculty['FacultyID']); ?></div>
            <div>Lectures</div>
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
          <div class="col-xs-3"> <i class="fa fa-group fa-5x"></i> </div>
          <div class="col-xs-9 text-right">
            <div class="huge"><?php echo getFacultyActiveStudents($faculty['FacultyID']); ?></div>
            <div>Active Students</div>
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
            <div class="huge"><?php echo getUserMessages($faculty['Email']); ?></div>
            <div>Messages</div>
          </div>
        </div>
      </div>
      <a href="?tab=7">
      <div class="panel-footer"> <span class="pull-left">View Details</span> <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
        <div class="clearfix"></div>
      </div>
      </a> 
    </div>
  </div>  
</div>
<!-- /.row -->

<div class="row">
  <div class="col-lg-8">
    <div class="panel panel-default">
      <div class="panel-heading">
          <i class="fa fa-clock-o fa-fw"></i> Welcome
      </div>
      <!-- /.panel-heading -->
      <div class="panel-body">
      
        <h1>Welcome, <?php echo $faculty['FacultyName']; ?></h1>
        <h2>Announcements</h2>
        <div class="list-announcements">
        <?php echo list_announcements("Faculty"); ?>
        </div>
        
        <h1>Your Lectures</h1>
        <div class="list-lectures">
        <?php echo list_lecture_units($faculty['FacultyID']); ?>
        </div>
      
      </div>
      <!-- /.panel-body -->
    </div>
    <!-- /.panel-default -->
  </div>

  <div class="col-lg-4">
    <div class="panel panel-default">
      <div class="panel-heading">
        <i class="fa fa-university fa-fw"></i> Departments
      </div>
      <!-- /.panel-heading -->
      <div class="panel-body">
        <?php echo list_faculty_departments($faculty['Departments']); ?>
      </div>
      <!-- /.panel-body -->
    </div>
    <!-- /.panel-default -->

    <div class="panel panel-default">
      <div class="panel-heading">
        <i class="fa fa-bell fa-fw"></i> Notifications Panel
      </div>
      <!-- /.panel-heading -->
      <div class="panel-body">        
        <div class="list-group">  
          <?php echo list_login_history($faculty['FacultyID']); ?>
        </div>
        <a href="#" class="btn btn-default btn-block">View All Alerts</a>
      </div>
      <!-- /.panel-body -->
    </div>
    <!-- /.panel-default -->

    <?php
		$resChatRooms = db_query("SELECT * FROM `".DB_PREFIX."chat_room` WHERE `chat_room_name` = 'Faculty'",DB_NAME,$conn);
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
					<input type="hidden" value="<?php echo $faculty['FacultyName']; ?>" id="usr">
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
	</script>  
</div>
<!-- /.row -->