<script>
<!--
//Define page title
document.title = "<?=SYSTEM_SHORT_NAME?> | Administrator Login";

//Basic submit validation
function Focus(){
	var username = document.adminLogin.uname;
	username.focus();
}
function ValidateLogin(){
    var name= document.adminLogin.uname;
	var password= document.adminLogin.upass;

    if (name.value == ""){
	   name.style.color = "#FFFFFF";
       name.style.background = "#CC0000";
	   window.alert("Enter your username ");
       name.focus();
       return false;
    }
	if (password.value == ""){
       password.style.background = "#CC0000";
	   name.style.background = "#B4D17A";
	   window.alert("Enter your password ");
       password.focus();
       return false;
    }
    return true;
}
//-->
</script>
<div class="container">
  <div class="row">
    <div class="col-md-4 col-md-offset-4">
      <div class="header-img"><img class="img-responsive" src="<?=SYSTEM_LOGO_URL;?>" alt="Logo"></div>
      <div class="login-panel panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title text-center">Administrator Login</h3>
        </div>
        <div class="panel-body">
    	  <p><strong><?php if(isset($_SESSION['message'])){ echo $_SESSION['message']; }?></strong></p>
          <form name="adminLogin" method="post" action="?do=login&amp;url=<?=urldecode($url);?>">
            <fieldset>
              <div class="form-group">
                <input class="form-control" placeholder="Username" name="uname" type="text" value="<?=$username?>" autofocus>
              </div>
              <div class="form-group">
                <input class="form-control" placeholder="Password" name="upass" type="password" value="">
              </div>
              <div class="checkbox">
                <label><input name="urem" type="checkbox" value="1">Remember Me </label>
              </div>
              <a href="?do=reset" title="Forgot Password">Forgot password?</a>
              <input type="submit" name="ulogin" value="Login" class="btn btn-lg btn-success btn-block">
            </fieldset>
          </form>
	    </div>
        <!-- / .panel-body -->
      </div>
      <!-- / .login-panel -->
    </div>
    <!-- / .col-md-4 -->
  </div>
  <!-- / .row -->
</div>
<!-- / .container -->