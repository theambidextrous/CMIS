<script>
<!--//
//Define page title
document.title = "<?=SYSTEM_SHORT_NAME?> - Student | My Attendance";
//-->
</script>
<div class="row">
  <div class="col-lg-12">
    <h1 class="page-header">My Attendance</h1>
  </div>
  <!-- /.col-lg-12 -->
</div>
<!-- /.row -->

<div class="row">
  <div class="col-lg-12">
    <div class="cms-contents-grey">
      <!--Begin Forms-->
      <?php
      //Require Course ID
      $CourseID = !empty($_GET['CourseID'])?$_GET['CourseID']:$_SESSION['CourseID'];
	  if(!empty($CourseID)){		  
		  $Course = getCourseDetails($CourseID);
		  ?>
		  <h2><?=$CourseID;?> <small>(<?=$Course['CName'];?>)</small></h2>
		  <h3>Your recorded attendance for this course</h3>
		  <div id="hideMsg"><?php if(isset($_SESSION['MSG'])) echo $_SESSION['MSG'];?></div>
		  <table width="100%" class="display table table-striped table-bordered table-hover">
		  <thead>
		  <tr>
		  <th>#</th>
		  <th>Unit Name</th>
		  <th>Period</th>
		  <th>Total Hours</th>
		  <th>Absent Hours</th>
		  <th>Percent Absent</th>
		  <th>Details</th>
		  </tr>
		  </thead>
		  <tbody>
		  </tbody>
		  </table>
          <?php
	  }else{
		  echo '<p>You need to select a course to use this module</p>';
	  }
	  ?>
      <!--End Forms-->
	</div>
  </div>
</div>
<!-- /.row -->