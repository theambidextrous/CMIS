<?php
require_once("$class_dir/class.validator.php3");
?>
<script language="javascript" type="text/javascript">
//Define page title
document.title = "<?=SYSTEM_SHORT_NAME;?> | Global Settings";

//Clear file upload field
function clearField(id){
	if ($.browser.msie) {
		$('#'+id).replaceWith($('#'+id).clone());
	}
	else {
		$('#'+id).val('');
	}
}
</script>

<div class="row">
  <div class="col-lg-12">
    <h1 class="page-header">Global Settings</h1>
  </div>
  <!-- /.col-lg-12 --> 
</div>
<!-- /.row -->


<div class="row">
  <div class="col-lg-12">
    <div class="panel panel-default">
      <div class="panel-heading"> <i class="fa fa-gear fa-fw"></i> Manage Settings </div>
      <!-- /.panel-heading -->
      <div class="panel-body">
        <!--Begin Forms-->

        <div class="tabs-container">
          <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#tabs-1" title="Premade Messages"><span>Global Settings</span></a></li>
            <?php if(isSuperAdmin()){ ?>
            <li><a data-toggle="tab" href="#tabs-2" title="Email Templates"><span>Email Templates</span></a></li>
            <?php 
			}
			if(isSuperAdmin() || isSystemAdmin()){
			?>
            <li><a data-toggle="tab" href="#tabs-3" title="System Logs"><span>System Logs</span></a></li>
            <?php } ?>
          </ul>
          <div class="tab-content">
            <div id="tabs-1" class="tab-pane active">
              <h2>IMPORTANT NOTICE</h2>
              <p><strong>Only the super administrator has the rights to change and view the configurations and system log files.</strong></p>
              <p>Adding wrong values to the configuration files could bring down this system making it completely unaccessible. Make sure you're aware of the values you're changing to make sure you do not compromise the stability of the system.</p>
              
              <h2>System Credits</h2>
              <p>System Developers: Sammy M. Waweru, Idd Otuya<br>
              Company Name: Wits Technologies Ltd<br>
              Email: sammy@witstechnologies.co.ke<br>
              Phone: +254 721428276</p>
              
            </div>
            <?php if(isSuperAdmin()){ ?>
            <div id="tabs-2" class="tab-pane">
              <?php
              //Array to store the error messages
              $ERRORS = array();
              $ALERTS = array();
              
              $ALERTS['MSG'] = WarnMessage("Be careful while editing this configuration file. Click on the attention icons for details.");
              
              if(isset($_POST['Submit'])){
              
                  // verify if there were any errors by checking
                  // the number of elements in the $ERRORS array
              
                  if(sizeof($ERRORS)>0){
										//alert
										$ALERTS['MSG'] = ErrorMessage("ERRORS ENCOUNTERED!");
                  }
                  else{										
									  //alert
									  $ALERTS['MSG'] = ConfirmMessage("NEW SETTINGS WERE WRITTEN SUCCESSFULLY");
                  }
              }
              ?>
              <ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?tab=10&task=edit" title="Global Settings">Global Settings</a></li><li class="active">Email Templates</li></ol>
              <form>
							
							</form>
            </div>
          <?php
		  }
		  if(isSuperAdmin() || isSystemAdmin()){
		  ?>
            <div id="tabs-3" class="tab-pane">
              <ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?tab=10&task=edit" title="Global Settings">Global Settings</a></li><li class="active">System Logs</li></ol>
              <p><strong>PHP Version:</strong> <?=phpversion();?><br>
              <strong>MySQL Version:</strong> <?=db_version();?></p><hr>
              <?php
                  $filename = "$logs_dir/system_logs.txt";
                  
                  if(isset($_POST['ClearErrors'])){	
                      // Let's make sure the file exists and is writable first.
                      if (is_writable($filename)) {	
                          // Write the contents to the file, 
                          // using the FILE_APPEND flag to append the content to the end of the file
                          // and the LOCK_EX flag to prevent anyone else writing to the file at the same time
                          if(file_put_contents($filename, "", LOCK_EX) === FALSE){
                              echo "Cannot clear the file. Try again later";
                          }
                      }
                  }
                  
                  if (file_exists($filename)) {
                      $file = file_get_contents($filename, FILE_USE_INCLUDE_PATH);
                      
                      if($file){
                          echo "<form name=\"ClearForm\" method=\"post\" action=\"\"><input type=\"submit\" name=\"ClearErrors\" value=\"Clear Errors\"></form>";
                          echo $file;
                          echo "<form name=\"ClearForm\" method=\"post\" action=\"\"><input type=\"submit\" name=\"ClearErrors\" value=\"Clear Errors\"></form>";
                      }
                      else
                          echo "Error file is empty";
                  }else{
                      echo "The server claims that this file does not exist.";
                  }		
              ?>    
            </div>
            <?php
            }
            ?>
          </div>
          <!-- / .tab-content -->
        </div>
  		<!-- / .tabs-container -->

		<!--End Forms-->      
      </div>
      <!-- /.panel-body --> 
    </div>
    <!-- /.panel-default --> 
  </div>
  <!-- /.col-lg-12 --> 
</div>
<!-- /.row -->