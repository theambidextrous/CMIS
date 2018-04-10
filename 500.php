<?php
include "includes/config.php";
require_once("includes/functions.php");

$pagetitle = "ERROR 500 - Internal Server Error";

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
          <h3 class="panel-title text-center">ERROR 500 - Internal Server Error</h3>
        </div>
        <div class="panel-body">
          <p class="text-center text-danger">In most cases, this error is a result of a problem with the code or program you are calling rather than with the web server itself.</p>
          <p class="text-center"><a class="btn btn-danger btn-lg" href="./">Get me out of here</a></p>
        </div>
      </div>
    </div>
  </div>
</div>
<?php 
add_footer();
?>