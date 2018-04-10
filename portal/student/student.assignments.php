<script>
<!--//
//Define page title
document.title = "<?=SYSTEM_SHORT_NAME?> - Student | My Assignments";
//-->
</script>
<div class="row">
  <div class="col-lg-12">
    <h1 class="page-header">My Assignments</h1>
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
				<h3>Available assignments for this course</h3>
				<p>No assignments are available at the moment</p>
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