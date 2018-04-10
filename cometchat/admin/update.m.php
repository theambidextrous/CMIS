<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

if (!defined('CCADMIN')) { echo "NO DICE"; exit; }
global $marketplace, $update;
if($marketplace == 1) { echo "NO DICE"; exit; }

include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'CometChatUpdate.php');
function index() {
	global $body, $cms, $ts, $currentversion, $update,$latest_vesion,$token_key;

if (!empty($_REQUEST['fupdate']) && $_REQUEST['fupdate'] == 1) {
	configeditor(array('LATEST_VERSION' => $currentversion));
	header("Location:?module=update&ts={$ts}");
	exit();
}
if($update->checkAvailableZip()){
	header("Location:?module=update&action=updateNow&ts={$ts}");
	exit();
}
$method = 'getKey();';

$body .= <<<EOD
<div class="row">
  	<div class="col-sm-8 col-lg-8">
  		<div class="row">
	  		<div class="col-sm-12 col-lg-12">
		    	<div class="card">
		      		<div class="card-header">
		        		<h6>Downloading the CometChat</h6>
		        		<h6>Version number: {$latest_vesion}</h6>
		        	</div>
		        	<div class="card-block">
		        	<div style="height:500px;">
						<div id="error-msg" class="col-sm-12 col-lg-12" style="display:none;">
							<div class="card card-inverse card-danger">
								<div class="pb-0" style="padding:16px;">
									<p id="msg"></p>
								</div>
							</div>
						</div>

			        	<div id="loading-img" style="text-align:center;display:none;">
			        		<img align="center" src="images/downloading.gif"/><br/><b style="font-size:20px;">Downloading...</b>
			        	</div>
		        	</div>
						<div id="cancel-btn" class="row col-md-12" style="display:none;">
							<a href="?module=dashboard&ts={$ts}" class="btn btn-primary">Cancel</a>
					    </div>
		        	</div>
		      	</div>
	    	</div>
	  	</div>
	</div>
	<div class="col-sm-4 col-lg-4">
		<div class="card">
		<div class="card-block"><br>
		        <div class="note note-success">
					<ol style="padding-left:10px;">
			    		<li>If you face any issue with the update, please contact our <a target="_blank" href="https://support.cometchat.com">support team</a> to assist you.</li>
			    		<li>This feature is currently in beta. We recommend proceeding only after you have taken a complete backup of your server/site.</li>
			    	</ol>
		        </div>
	        </div>
		</div>
	</div>
</div>
<script>
	jQuery(function() {
		$('#loading-img').show();
		{$method}
		function getKey(){
			$.ajax({
				url: 'index.php?module=update&action=callUpdateMethod&api=getTokenKey',
				type:'get',
				dataType:'json',
				contentType:'application/json;charset=utf-8',
				success: function(data) {
					if(data.status==1){
						downloadPackage();
					}else if(data.hasOwnProperty('status') && data.hasOwnProperty('data') && data.data.token){
						var date = new Date();
					    var days = days || 365;
					    // Get unix milliseconds at current time plus number of days
					    date.setTime(+ date + (days * 86400000)); //24 * 60 * 60 * 1000
					    window.document.cookie = 'token' + "=" + data.data.token + "; expires=" + date.toGMTString() + "; path=/";
						downloadPackage();
					}else{
						$('#loading-img').hide();
						$('#error-msg').show();
						$('#cancel-btn').show();
						$("#msg").html(data.message);
					}
				}
			});
		}
		function downloadPackage(){
			$.ajax({
				url: 'index.php?module=update&action=callUpdateMethod&api=downloadLatestPackage',
				type: 'get',
				dataType:'json',
				contentType:'application/jsonp;charset=utf-8',
				success: function(data){
					if(data.error==0){
						window.location.href='index.php?module=update&action=updateNow';
						document.cookie = "token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";  
					}else{
						$('#loading-img').hide();
						$('#error-msg').show();
						$('#cancel-btn').show();
						$("#msg").html(data.message);
					}
				}
			});
		}
	});
</script>
EOD;
template();

}

function forceUpdate(){
	global $update, $settings, $body, $ts;
	$body .= <<<EOD
	<div class="col-sm-12 col-lg-12">
		<div class="card">
		  	<div class="card-header">
		    	Force update
		  	</div>
		  	<div class="card-block">
	        <div class="note note-success">
	           	If you have any customization in CometChat it will be deleted.<br>
				This feature is provided to achieve the following:
				<ol style="padding-left:25px;">
		    		<li>Forcefully update to same version and same edition of CometChat.</li>
		    		<li>Upgrade to higher edition of CometChat.</li>
		    		<li>Before upgrading to higher edition of CometChat, please make sure you have updated license key. To update license key <a href="?module=settings&action=generalsettings">Click here</a>.</li>
		    	</ol>
	        </div>
			<div class="row col-md-12"><br>
				<a href="?module=update&fupdate=1&ts={$ts}" class="btn btn-primary"><i class="fa fa-download"></i> Download</a>
				<a href="?module=dashboard&ts={$ts}" class="btn btn-danger">Cancel</a>
		    </div>

		    </div>
		</div>
	</div>
EOD;
template();
}

function updateNow(){
	global $body, $cms, $ts, $update, $currentversion, $licensekey, $settings, $latest_vesion;
	ini_set('max_execution_time', 300);
	if (empty($latest_vesion) || !($update->checkAvailableZip())) {
		header("Location:?module=dashboard&ts={$ts}");
		exit();
	}

	$body .= <<<EOD
	<div class="row">
	  	<div class="col-sm-8 col-lg-8">
	  		<div class="row">
		  		<div class="col-sm-12 col-lg-12">
			    	<div class="card">
			      		<div class="card-header">
			        		<h6>The new version is ready to be installed</h6>
			        		<h6>Version number: {$latest_vesion}</h6>
			        	</div>
			        	<div class="card-block">
			        	<div style="min-height:300px;padding-top:60px;">
					      	<div id="wizard" class="form_wizard wizard_horizontal">
								<ul class="wizard_steps">
								    <li>
								      <a style="text-decoration:none;" href="#step-1" id="step_no_1">
								        <span class="step_no" >1</span>
								        <span class="step_descr">
								            <small>Generating hash</small>
								        </span>
								      </a>
								    </li>
								    <li>
								      <a style="text-decoration:none;" href="#step-2" id="step_no_2">
								        <span class="step_no">2</span>
								        <span class="step_descr">
								            <small>Comparing hash</small>
								        </span>
								      </a>
								    </li>
								    <li>
								      <a style="text-decoration:none;" href="#step-3" id="step_no_3">
								        <span class="step_no" >3</span>
								        <span class="step_descr">
								            <small>Taking backup of files <br> and tables</small>
								        </span>
								      </a>
								    </li>
								    <li>
								      <a style="text-decoration:none;" href="#step-4" id="step_no_4">
								        <span class="step_no" >4</span>
								        <span class="step_descr">
								            <small>Extracting new files</small>
								        </span>
								      </a>
								    </li>
								    <li>
								      <a style="text-decoration:none;" href="#step-5" id="step_no_5">
								        <span class="step_no" >5</span>
								        <span class="step_descr">
								            <small>Applying changes</small>
								        </span>
								      </a>
								    </li>
								</ul>
							</div>

							<div id="loader" style="text-align:center;display:none;">
								<img src="images/simpleloading.gif" height="200" width ="200" />
								<div id="msg" style="font-size:16px;"></div>
							</div>
							<div id="error" style="color:red;padding-top:30px;display:none;">
								If you face any issue with the update, please contact our support team to assist you.
							</div>


			        	</div>
							<div class="row col-md-12">
								<input id='updatenow' type="button" value="Update Now"  class="btn btn-primary">
								<input id='continue' style="display:none;" type="button" value="Continue"  class="btn btn-success">
								<a style="display:none;" href="?module=dashboard&ts={$ts}" class="btn btn-success go-to-dashboard">Go to Dashboard</a>
								<a style="display:none;" id="cancel-btn" href="?module=dashboard&ts={$ts}" class="btn btn-primary">Cancel</a>
						    </div>
			        	</div>
			      	</div>
		    	</div>
		  	</div>
		</div>
		<div class="col-sm-4 col-lg-4">
			<div class="card">
			<div class="card-block"><br>
			        <div class="note note-success">
						<ol style="padding-left:10px;">
				    		<li>If you face any issue with the update, please contact our <a target="_blank" href="https://support.cometchat.com">support team</a> to assist you.</li>
				    		<li>This feature is currently in beta. We recommend proceeding only after you have taken a complete backup of your server/site.</li>
				    	</ol>
			        </div>
		        </div>
			</div>
		</div>
	</div>
<script>
	jQuery(function() {
		jQuery("#updatenow").click(function(){
			$("#step_no_1").addClass('selected');
			$("#loader").show();
			$("#updatenow").remove();
			$("#msg").text('Generating Hashes....');
			$.ajax({
				url: 'index.php?module=update&action=callUpdateMethod&api=createHash',
				type: 'get',
				dataType:'json',
				contentType:'application/jsonp;charset=utf-8',
				success: function(data){
					if(data.error == 0){
						$("#step_no_1").addClass('done');
						compareHash();
					}else{
						$('#loader').hide();
						$('#cancel-btn').show();
						$("#error").show().html(data.message);
					}
				}
			});
		});

		function compareHash(){
			$("#step_no_2").addClass('selected');
			$("#msg").text('Comparing Hashes....');
			$.ajax({
				url: 'index.php?module=update&action=callUpdateMethod&api=compareHashes',
				type: 'get',
				dataType:'json',
				contentType:'application/jsonp;charset=utf-8',
				success: function(data){
					if(data.error == 0){
						backupFiles();
					} else if(data.modify == 1){
						$('#error').show().html(data.message);
						$('#loader').hide();
						$('#continue').show();
						$('#cancel-btn').show();
					} else {
						$('#continue').remove();
						$('#cancel-btn').show();
						$("#error").show().html(data.message);
					}
				}
			});
		}

		jQuery("#continue").click(function(){
			if(confirm("Are you sure you want to continue !")){
				backupFiles();
			}
		});
		jQuery("#cancel-btn").click(function(){
			$('#error_msg').hide();
			alert('Please take the help of our support team (https://support.cometchat.com/) to update.');
		});

		function backupFiles(){
			$("#step_no_3").addClass('selected');
			$("#loader").show();
			$("#msg").text('Taking backup of core file.....');
			$('#error').hide().html('');
			$('#continue').remove();
			$('#cancel-btn').hide();
			$.ajax({
				url: 'index.php?module=update&action=callUpdateMethod&api=backupFiles',
				type:'get',
				success: function(data){
					if(data.error == 0){
						extractZip();
					} else {
						$('#cancel-btn').show();
						$("#error").show().html(data.message);
					}
				}
			});
		}

		function extractZip(){
			$("#step_no_4").addClass('selected');
			$("#loader").show();
			$("#msg").text('Extract the new file...');
			$('#error').hide().html('');
			$.ajax({
				url: 'index.php?module=update&action=callUpdateMethod&api=extractZip',
				type:'get',
				success: function(data){
					if(data.error == 0){
						applyChanges();
					}else{
						$('#cancel-btn').show();
						$("#loader").hide();
						$("#error").show().html(data.message);
					}
				}
			});
		}

		function applyChanges(){
			$("#loader").show();
			$("#msg").text('Applying changes...');
			$('#error').hide().html('');
			$.ajax({
				url: 'index.php?module=update&action=callUpdateMethod&api=applyChanges',
				type:'get',
				success: function(data){
					$("#loader").hide();
					if (data.error == 0) {
						$("#step_no_5").addClass('selected');
						$(".go-to-dashboard").show();
						$("#error").css('color' ,'green').html('Successfully updated CometChat').show();
					}else{
						$('#cancel-btn').show();
						$("#error").show().html(data.message);
					}
				}
			});
		}
	});
</script>
EOD;
	template();
}

function callUpdateMethod () {
	global $update ,$token_key;
	$api = empty($_REQUEST['api']) ? '' : $_REQUEST['api'];
	if (defined('DEV_MODE') && DEV_MODE == '0') {
		ini_set('display_errors','Off');
	}

	if(!empty($_COOKIE['token'])){
		$token_key = $_COOKIE['token'];
	}

	switch ($api) {
		case 'getTokenKey':
			$update -> getTokenKey();
			break;
		case 'downloadLatestPackage':
			$update -> downloadLatestPackage($token_key);
			break;
		case 'createHash':
			$update -> createHash();
			break;
		case 'compareHashes':
			$update -> compareHashes();
			break;
		case 'backupFiles':
			$update -> backupFiles();
			break;
		case 'extractZip':
			$update -> extractZip();
			break;
		case 'applyChanges':
			$update -> applyChanges();
			$oldversion = !empty($_SESSION['cometchat']['old_version']) ? $_SESSION['cometchat']['old_version'] : '' ;
		 	configeditor(array('LATEST_VERSION'=>'','latest_update_token'=>'','OLD_VERSION'=> $oldversion));
			clearcachejscss(dirname(dirname(__FILE__)));
			break;
		case 'generateHash':
			$update -> generateHash();
			break;
		default:
			echo json_encode(array('error' => 1, 'message' => "Invalid API request"));
			exit();
			break;
	}
}
