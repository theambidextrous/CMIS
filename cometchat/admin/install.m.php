<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

if (!defined('CCADMIN')) { echo "NO DICE"; exit; }

function index() {
	global $body,$color_original,$layout_original,$ts,$client,$availableIntegrations,$cms,$login_url,$logout_url,$protocol,$pluginkey,$settings,$licensekey;
	$staticCDNUrl = STATIC_CDN_URL;
	$addcometchat = '';
    $athemes = array();
    $step2 = "";

	if ($handle = opendir(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'layouts')) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != ".." && $file != "base" && $file !="mobile" && is_dir(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'layouts'.DIRECTORY_SEPARATOR.$file) && file_exists(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'layouts'.DIRECTORY_SEPARATOR.$file.DIRECTORY_SEPARATOR.'config.php')) {
				if($file == 'embedded' || $file == 'docked') {
					$athemes[] = $file;
				}
			}
		}
		closedir($handle);
	}
	asort($athemes);
	array_push($athemes, "mobile");

	$activethemes = '';
	$no = 0;

	foreach ($athemes as $ti) {
		$title = ucwords($ti);
		++$no;
		$default = '';
		$opacity = '0.5';
		$setdefault = '';

		if (strtolower($layout_original) == strtolower($ti)) {
			$opacity = '1;cursor:default';
			$setdefault = '';
        }

        if (strtolower($ti) == 'mobile' || strtolower($ti) == 'synergy' || strtolower($ti) == 'embedded') {
			$Default = ' (Default)';
			$opacity = '1;cursor:default';
			$setdefault = '';
		}

		if(strtolower($ti) == 'embedded'){
			$default = '';
		}

		if (strtolower($ti) == 'embedded'){
			$activethemes .= '<tr><td id="'.$no.'" d1="'.$ti.'">'.stripslashes($title).$default.'</td><td><a style="color:black;" data-toggle="tooltip" title="Generate Embed Code"  href="javascript:void(0)" onclick="javascript:themetype_embedcode(\''.$ti.'\')" ><i class="fa fa-lg fa-code"></i></a></td><td><a href="../cometchat_embedded.php" target="_blank" data-toggle="tooltip" title="Direct link to Embedded" style="color:black;"><i class="fa fa-lg fa-external-link-square"></i></a></td></tr>';
		}else if(strtolower($ti) == 'docked'){
			$activethemes .= '<tr><td id="'.$no.'" d1="'.$ti.'">'.stripslashes($title).$default.'</td><td><a style="color:black;" data-toggle="tooltip" title="Generate Footer Code" href="javascript:void(0)" onclick="javascript:themetype_embedcode(\''.$ti.'\')"><i class="fa fa-lg fa-code"></i></a></td><td></td></tr>';
		} else {
			continue;
		}
	}

	if(!empty($client)) {
		$code =	$options = $site_url = $httpsy = $httpsn = '';


	    foreach ($availableIntegrations as $key => $value) {
	    	$selected = "";
			if($key==$cms){
				$selected = "selected";
			}
	    	$options .=  '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
	    }

		if (!empty($protocol) && $protocol == 'https:') {
			$httpsy = "checked";
		} else {
			$httpsn = "checked";
		}
		if(file_exists(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'cloud'.DIRECTORY_SEPARATOR.'addcometchat'.DIRECTORY_SEPARATOR.$cms.'.php')) {
			$code = include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'cloud'.DIRECTORY_SEPARATOR.'addcometchat'.DIRECTORY_SEPARATOR.$cms.'.php');
		}
		$configure_vars = '';
		if(!empty($code)) {
			$configure_vars = '<div class="row">'.$code.'</div>';
			$step2 = "Step 2: ";
		}

	$addcometchat = <<<EOD
		<div class="row">
		  	<div class="col-sm-12 col-lg-12">
		  		<div class="row">
		  			<div class="col-sm-12 col-lg-12">
					   	<div class="card">
					   		<div class="card-header">
					   			Install CometChat on your site
					   		</div>
					   		<div class="card-block">
					   			<form action="?module=install&action=saveplatform&ts={$ts}" method="post">
					   				<div class="form-group row">
										<div class="col-md-12">
											<label class="form-control-label">Select Platform:</label>
											<select id="cms" name="cms" class="form-control">
												{$options}
											</select>
										</div>
									</div>
					   				<div class="row col-md-10" style="padding-bottom:5px;"><br>
					   					<input type="submit" value="Update"  class="btn btn-primary">
					   				</div>
					   			</form>
					   		</div>
					   	</div>
					</div>
				</div>
			</div>
		</div>
EOD;
}
if(checkLicenseVersion()){
	$addcometchat = "";
}
	$body .= <<<EOD
	{$addcometchat}
	{$configure_vars}
		<div class="row">
		  	<div class="col-sm-12 col-lg-12">
		  		<div class="row">
		  			<div class="col-sm-12 col-lg-12">
					   	<div class="card">
					   		<div class="card-header">
								{$step2} Add CometChat
								<h4><small>Select a layout of your choice and click on "Add To Site" button to proceed.</small></h4>
							</div>
					   	</div>
					</div>
				</div>
			</div>
		</div>
	<div class="row">
	  	<div class="col-sm-6 col-lg-6">
		    <div class="card">
		    	<div class="card-header">
		    		Docked Layout
		    	</div>
			    <div class="card-block">
			    <img src="{$staticCDNUrl}/admin/images/docked.png" width="100%">
			     <div class="card-footer" style="padding-left:5px;">
		        <a class="btn btn-primary" href="javascript:void(0);" onclick="javascript:themetype_embedcode('docked')">Add To Site</a>
			   </div>
			    </div>
		    </div>
		</div>
	  	<div class="col-sm-6 col-lg-6">
		    <div class="card">
		    	<div class="card-header">
		    		Embedded Layout
		    	</div>
			    <div class="card-block">
			    <img src="{$staticCDNUrl}/admin/images/embedded.png" width="100%">
			     <div class="card-footer" style="padding-left:5px;">
		        <a class="btn btn-primary" href="javascript:void(0);" onclick="javascript:themetype_embedcode('embedded')">Add To Site</a>

		        <a class="btn btn-primary" style="float:right;border-radius:25px;" href="../cometchat_embedded.php" target="_blank">Direct Link To Embedded Layout <i class="fa fa-external-link"></i></a>
			   </div>
			    </div>
		    </div>
		</div>

	</div>
		  <script type="text/javascript">
	  $(function() {
	    $("#install").addClass('active');
	  });
	</script>
EOD;

	template();
}

function saveplatform() {
	if (empty($GLOBALS['client'])) { header("Location:?module=dashboard&ts=".$GLOBALS['ts']); exit; }
	global $client;
	global $ts;

	configeditor($_POST);
	$_SESSION['cometchat']['error'] = 'Platform updated successfully';
	header("Location:?module=install&ts={$ts}");
}

function is_valid_domain_name($domain_name){
	if (empty($GLOBALS['client'])) { header("Location:?module=dashboard&ts=".$GLOBALS['ts']); exit; }

	$domain_name = preg_replace('#((?:https?://)?[^/]*)(?:/.*)?$#', '$1', $domain_name);
	return preg_match("/^([a-z](-*[a-z0-9])*)(\.([a-z0-9](-*[a-z0-9])*))*$/i", $domain_name);
}
