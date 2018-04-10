<?php
/*********************************************
Company:		Wits Technologies Ltd
Developer:	Sammy Mwaura Waweru
Mobile:			+254721428276
Email:			sammy@witstechnologies.co.ke
Website:		http://www.witstechnologies.co.ke/
*********************************************/
?>
<script>
<!--
//Define page title
document.title = "<?=SYSTEM_SHORT_NAME?> | Portal Login";

//Basic submit validation
function Focus(){
	var username = document.userLogin.LoginID;
	username.focus();
}
function ValidateLogin(){
    var name= document.userLogin.LoginID;
	var password= document.userLogin.LoginPass;

    if (name.value == ""){
       name.style.color = "#FFFFFF";
	   name.style.background = "#CC0000";
	   window.alert("Enter your Login ID ");
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
      <div class="header-img"><a href="<?=PARENT_HOME_URL;?>"><img class="img-responsive" src="<?=SYSTEM_LOGO_URL;?>" alt="Logo"></a></div>
      <div class="login-panel panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title text-center">Portal Login</h3>
        </div>
        <div class="panel-body">
          <p><strong><?php if(isset($_SESSION['message'])){ echo $_SESSION['message']; }?></strong></p>
          <form role="form" name="userLogin" method="post" action="?do=login&amp;url=<?=$returnurl;?>">
            <fieldset>
              <div class="form-group">
                <input class="form-control" placeholder="Username" name="LoginID" type="text" value="<?=$username?>" autofocus>
              </div>
              <div class="form-group">
                <input class="form-control" placeholder="Password" name="LoginPass" type="password" value="">
              </div>
              <div class="checkbox">
                <label><input name="usrrem" type="checkbox" value="1">Remember Me </label>
              </div>
              <a href="?do=reset" title="Forgot Password">Forgot password?</a> | <a href="?do=register" title="New User">New User? Register Now.</a>
              <input type="submit" name="usrlogin" value="Login" class="btn btn-lg btn-success btn-block">
            </fieldset>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
