<script>
<!--//
//Define page title
document.title = "<?=SYSTEM_SHORT_NAME?> - Faculty | Attendance";
//-->
</script>
<div class="row">
  <div class="col-lg-12">
    <h1 class="page-header">Attendance</h1>
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
			$UnitID = !empty($_GET['UnitID'])?$_GET['UnitID']:$_SESSION['UnitID'];
			if(!empty($UnitID)){
				$Unit = getUnitDetails($UnitID);
				?>
				<h2><?=$UnitID;?> <small>(<?=$Unit['UName'];?>)</small></h2>
				<h3>Overall attendance for this unit</h3>
				<p>Attendance data is not available at the moment</p>
				<?php
			}else{
				echo '<p>You need to select a unit to use this module</p>';
			}
			?>
			<!--End Forms-->
	  </div>
  </div>
</div>
<!-- /.row -->