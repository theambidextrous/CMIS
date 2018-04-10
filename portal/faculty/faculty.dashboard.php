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

  </div>	  
</div>
<!-- /.row -->