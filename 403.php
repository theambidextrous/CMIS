<?php
include "includes/config.php";
require_once("includes/functions.php");

$pagetitle = "ERROR 400 - Bad Request";

add_header();
?>
<script>
<!--//
//Define page title
document.title = "ERROR 403 - Forbidden";
//-->
</script>
<div class="container">
  <div class="row">
    <div class="col-md-4 col-md-offset-4">
      <div class="header-img"><img class="img-responsive" src="<?=SYSTEM_LOGO_URL;?>" alt="Logo"></div>
      <div class="login-panel panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title text-center">ERROR 403 - Forbidden</h3>
        </div>
        <div class="panel-body">
          <p class="text-center text-danger">The request does not specify the file name. Or the directory or the file does not have the permission that allows the pages to be viewed from the web.</p>
          <p class="text-center"><a class="btn btn-danger btn-lg" href="./">Get me out of here</a></p>
        </div>
      </div>
    </div>
  </div>
</div>
<?php 
add_footer();
?>