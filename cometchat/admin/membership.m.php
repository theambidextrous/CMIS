<?php

function index() {
	global $body, $rt;
	global $body,$coreFeature,$plugins,$crplugins,$ts,$hideconfig,$client,$settings;
	$plugins_core 	 = unserialize($settings['plugins_core']['value']);
	$modules_core 	 = unserialize($settings['modules_core']['value']);
	$extensions_core = unserialize($settings['extensions_core']['value']);
	$unsetextensions = array('ads' => 'Advertisements','mobileapp' => 'Mobile App', 'desktop' => 'Desktop App');
	$creditFeaturs   = array('core','avchat','audiochat');
	$extensions_core = array_diff($extensions_core,$unsetextensions);
	$members_roles 	 = getRolesDetails();
	$UI = $creditUI = '';
	$i = 1;
	$hideCreditUI = 'style="display:none;"';
	if(defined('ENABLED_CREDIT') && ENABLED_CREDIT=='1'){
		$hideCreditUI = '';
	}
	$total_count =  count($coreFeature)+count($plugins_core)+count($modules_core)+count($extensions_core);
	$record_per_col = intval($total_count/4)+1;
	foreach ($members_roles as $member => $value) {
		global ${$member.'_core'}, ${$member.'_plugins'} , ${$member.'_modules'}, ${$member.'_extensions'}, ${$member.'_disabledweb'}, ${$member.'_disabledmobileapp'}, ${$member.'_disableddesktop'}, ${$member.'_disabledcc'};
		$memberCore     	= ${$member.'_core'};
		$memberPlugins     	= ${$member.'_plugins'};
		$memberModules     	= ${$member.'_modules'};
		$memberExtensions  	= ${$member.'_extensions'};
		$webCheck      		= (${$member.'_disabledweb'} == 1) ? 'checked' : '';
		$mobileCheck   		= (${$member.'_disabledmobileapp'} == 1) ? 'checked' : '';
		$desktopCheck  		= (${$member.'_disableddesktop'} == 1) ? 'checked' : '';
		$ccCheck  			= (${$member.'_disabledcc'} == 1) ? 'checked' : '';

		$title 			= $value['name'];
		/*$open 			= ($i == 1) ? 'in' : '';*/
		$open 			= '';
		$UI .= <<<EOD
		    <div class="panel panel-default">
		      <div class="panel-heading">
		        <h4 class="panel-title">
		          <a style="display:block;" data-toggle="collapse" data-parent="#accordion" href="#collapse_{$member}">{$title}</a>
		        </h4>
		      </div>
		      <div id="collapse_{$member}" class="panel-collapse collapse {$open}">
		        <div class="panel-body">
		        <div class="form-group row">
		        	<div class="col-sm-12 col-lg-12">
		        		Disable CometChat completely for this role
		        		<hr>
						<div class="checkbox checkbox-success">
			                <input name="{$member}_disabledcc" $ccCheck value="1" id="{$member}_disabledcc" type="checkbox">
			                <label for="{$member}_disabledcc">
			                    Yes
			                </label>
			            </div>

		        	</div>
		        </div>
				<div class="row">
		        	<div class="col-sm-12 col-lg-12">
		        		Disable Specific Features of CometChat for this role
		        		<hr>
		        	</div>
		        </div>

			        <div class="form-group row">
EOD;
	$k = 0;
	foreach ($coreFeature as $cKey => $cVal) {
		if ($k == 0 || $k == $record_per_col) {
			$UI .= '<div class="col-sm-3 col-lg-3">';
		}
		$check = (empty($memberCore[$cKey])) ? "checked" : '';
		$featureTitle = ucwords($cVal['name']);
		$creditsToDeduct   = $memberCore[$cKey]['credit']['creditsToDeduct'];
		$deductionInterval = $memberCore[$cKey]['credit']['deductionInterval'];
		$creditUI .= <<<EOD
						<div class="form-group row" $hideCreditUI>
							<div class="col-md-12">
								<div class="col-sm-2">
					      			<label style="padding-top:7px;" class="control-label"> $featureTitle</label>
					      		</div>
					      		<div class="col-sm-6">
					      			<div style="padding:0px;" class="col-sm-1">
					      				<label style="padding-top:7px;"> Charge</label>
					      			</div>
					      			<div style="padding:0px;" class="col-sm-2">
					      				<input class="form-control" placeholder="Credits" type="text" maxlength="5"  name="{$member}_core[{$cKey}][credit][creditsToDeduct]" id="credit_{$member}_{$cKey}" value="$creditsToDeduct">
					      			</div>
					      			<div class="col-sm-3">
					      				<label style="padding-top:7px;">credits every</label>
					      			</div>

					      			<div style="padding:0px;" class="col-sm-2">
					      				<input class="form-control" placeholder="Minutes" type="text" maxlength="2" name="{$member}_core[{$cKey}][credit][deductionInterval]" id="deductionInterval_{$member}_{$cKey}" value="$deductionInterval">
					      			</div>
					      			<div class="col-sm-1">
					      				<label style="padding-top:7px;"> minutes</label>
					      			</div>

					      		</div>
							</div>
						</div>
EOD;
		$UI .= <<<EOD
				<div class="form-group">
		            <div class="checkbox checkbox-success">
		                <input name="{$member}_core[{$cKey}][inactive]" value="{$cKey}" $check id="checkbox_{$member}_{$cKey}" type="checkbox">
		                <label for="checkbox_{$member}_{$cKey}">
		                    {$featureTitle}
		                </label>
		                <input name="{$member}_core[{$cKey}][name]" value="{$featureTitle}"  type="hidden">
		            </div>
		        </div>
EOD;
	$k++;
		if ($k == $record_per_col) {
			$UI .= '</div>';
			$k = 0;
		}
	}
	foreach ($plugins_core as $pKey => $pVal) {
		if ($k == 0 || $k == $record_per_col) {
			$UI .= '<div class="col-sm-3 col-lg-3">';
		}
		$check = (empty($memberPlugins[$pKey])) ? "checked" : '';
		$featureTitle = str_replace('Document', '', ucwords($pVal[0]));
		$creditsToDeduct   = $memberPlugins[$pKey]['credit']['creditsToDeduct'];
		$deductionInterval = $memberPlugins[$pKey]['credit']['deductionInterval'];
		if (in_array($pKey, $creditFeaturs)) {
			$creditUI .= <<<EOD
						<div class="form-group row" $hideCreditUI>
							<div class="col-md-12">
								<div class="col-sm-2">
					      			<label style="padding-top:7px;" class="control-label"> $featureTitle</label>
					      		</div>
					      		<div class="col-sm-6">
					      			<div style="padding:0px;" class="col-sm-1">
					      				<label style="padding-top:7px;"> Charge</label>
					      			</div>
					      			<div style="padding:0px;" class="col-sm-2">
					      				<input class="form-control" placeholder="Credit" type="text" maxlength="5" name="{$member}_plugins[{$pKey}][credit][creditsToDeduct]" id="credit_{$member}_{$pKey}" value="$creditsToDeduct">
					      			</div>
					      			<div class="col-sm-3">
					      				<label style="padding-top:7px;">credits every</label>
					      			</div>
					      			<div style="padding:0px;" class="col-sm-2">
					      				<input class="form-control" placeholder="Minutes" type="text" maxlength="2" name="{$member}_plugins[{$pKey}][credit][deductionInterval]" id="deductionInterval_{$member}_{$pKey}" value="$deductionInterval">
					      			</div>
					      			<div class="col-sm-1">
					      				<label style="padding-top:7px;"> minutes</label>
					      			</div>
					      		</div>
							</div>
						</div>
EOD;
		}

		$UI .= <<<EOD
				<div class="form-group">
		            <div class="checkbox checkbox-success">
		                <input name="{$member}_plugins[{$pKey}][inactive]" value="{$pKey}" $check id="checkbox_{$member}_{$pKey}" type="checkbox">
		                <label for="checkbox_{$member}_{$pKey}">
		                    {$featureTitle}
		                </label>
		                <input name="{$member}_plugins[{$pKey}][name]" value="{$featureTitle}"  type="hidden">
		            </div>
		        </div>
EOD;
	$k++;
		if ($k == $record_per_col) {
			$UI .= '</div>';
			$k = 0;
		}
	}
	foreach ($modules_core as $mKey => $mVal) {
		if ($k == 0 || $k == $record_per_col) {
			$UI .= '<div class="col-sm-3 col-lg-3">';
		}
		$check = (empty($memberModules[$mKey])) ? "checked" : '';
		$featureTitle = ucwords($mVal[1]);
		$creditsToDeduct   = ucwords($memberModules[$mKey]['credit']['creditsToDeduct']);
		$deductionInterval = ucwords($memberModules[$mKey]['credit']['deductionInterval']);
		$UI .= <<<EOD
				<div class="form-group">
		            <div class="checkbox checkbox-success">
		                <input name="{$member}_modules[{$mKey}][inactive]" $check value="{$mKey}" id="checkbox_{$member}_{$mKey}" type="checkbox">
		                <label for="checkbox_{$member}_{$mKey}">
		                    {$featureTitle}
		                </label>
		                <input name="{$member}_modules[{$mKey}][name]" value="{$featureTitle}"  type="hidden">
		            </div>
		        </div>
EOD;
	$k++;
		if ($k == $record_per_col) {
			$UI .= '</div>';
			$k = 0;
		}
	}

	foreach ($extensions_core as $eKey => $eVal) {
		if ($k == 0 || $k == $record_per_col) {
			$UI .= '<div class="col-sm-3 col-lg-3">';
		}
		$check = (empty($memberExtensions[$eKey])) ? "checked" : '';
		$featureTitle = ucwords($eVal);
		$creditsToDeduct   = $memberExtensions[$eKey]['credit']['creditsToDeduct'];
		$deductionInterval = $memberExtensions[$eKey]['credit']['deductionInterval'];
		$UI .= <<<EOD
				<div class="form-group">
                    <div class="checkbox checkbox-success">
                        <input name="{$member}_extensions[{$eKey}][inactive]" $check value="{$eKey}" id="checkbox_{$member}_{$eKey}" type="checkbox">
                        <label for="checkbox_{$member}_{$eKey}">
                            {$featureTitle}
                        </label>
             			<input name="{$member}_extensions[{$eKey}][name]" value="{$featureTitle}"  type="hidden">
                    </div>
		        </div>
EOD;
	$k++;
		if ($k == $record_per_col) {
			$UI .= '</div>';
			$k = 0;
		}
	}

		$UI .= <<<EOD
		        </div>
		        </div>
		        <div class="form-group row">
		        	<div class="col-sm-12 col-lg-12">
		        		Disable Specific Platforms for this role
		        		<hr>
						<div class="checkbox checkbox-success">
			                <input name="{$member}_disabledweb" $webCheck value="1" id="{$member}_disabledweb" type="checkbox">
			                <label for="{$member}_disabledweb">
			                    Web
			                </label>
			            </div>
						<div class="checkbox checkbox-success">
			                <input name="{$member}_disabledmobileapp" $mobileCheck value="1" id="{$member}_disabledmobileapp" type="checkbox">
			                <label for="{$member}_disabledmobileapp">
			                    Mobile
			                </label>
			            </div>
						<div class="checkbox checkbox-success">
			                <input name="{$member}_disableddesktop" $desktopCheck value="1" id="{$member}_disableddesktop" type="checkbox">
			                <label for="{$member}_disableddesktop">
			                    Desktop
			                </label>
			            </div>
		        	</div>
		        </div>

		        <div class="form-group row" $hideCreditUI>
		        	<div class="col-sm-12 col-lg-12">
		        		Charge Credits when using certain features for this role
		        		<hr>
						$creditUI
		        	</div>
		        </div>

		        </div>
		      </div>
		    </div>
EOD;
		$i++;
		$creditUI = "";
	}

$body = <<<EOD
<div class="row">
	<div class="col-sm-12 col-lg-12">
    <div class="card">
		<div class="card-header">
			Role Based Permissions
		</div>
		<div class="card-block">
			<form action="?module=membership&action=updatemembership&ts={$ts}" method="post">
				<div class="panel-group" id="accordion">
					{$UI}
			  	</div>
				<div class="form-actions">
					<input type="submit" value="Update" class="btn btn-primary">
				</div>
			</form>
		</div>
    </div>
  	</div>
</div>

EOD;
	template();
}

function updatemembership(){
	global $plugins,$ts,$client,$settings,$coreFeature;
	$members_roles 	 = getRolesDetails();
	$plugins_core 	 = setCreditKey(unserialize($settings['plugins_core']['value']),'0');
	$modules_core 	 = setCreditKey(unserialize($settings['modules_core']['value']),'1');
	$extensions_core = setCreditKey(unserialize($settings['extensions_core']['value']),'');
	$unsetextensions = array('ads','mobileapp', 'desktop');
	$extensions_core = array_diff($extensions_core,$unsetextensions);
	foreach ($members_roles as $member => $value) {
		$disableCore     	= empty($_POST[$member.'_core']) ? array() : $_POST[$member.'_core'];
		$disablePlugins     = empty($_POST[$member.'_plugins']) ? array() : $_POST[$member.'_plugins'];
		$disableModules     = empty($_POST[$member.'_modules']) ? array() : $_POST[$member.'_modules'];
		$disableExtensions  = empty($_POST[$member.'_extensions']) ? array() : $_POST[$member.'_extensions'];
	    $memberCore     	= checkEnabledFeature($coreFeature,$disableCore);
		$memberPlugins     	= checkEnabledFeature($plugins_core,$disablePlugins);
		$memberModules     	= checkEnabledFeature($modules_core,$disableModules);
		$memberExtensions  	= checkEnabledFeature($extensions_core,$disableExtensions);
		configeditor(array(
			$member.'_core' => $disableCore,
			$member.'_plugins' => $memberPlugins,
			$member.'_modules' => $memberModules,
			$member.'_extensions' => $memberExtensions
			)
		);
		configeditor(array(
			$member.'_disabledweb' 		=> empty($_POST[$member.'_disabledweb']) ? 0 : 1,
			$member.'_disabledmobileapp' 	=> empty($_POST[$member.'_disabledmobileapp']) ? 0 : 1,
			$member.'_disableddesktop' 	=> empty($_POST[$member.'_disableddesktop']) ? 0 : 1,
			$member.'_disabledcc' 	=> empty($_POST[$member.'_disabledcc']) ? 0 : 1,
			)
		);
	}
	if(method_exists($GLOBALS['integration'], 'updateUserRoles')){
		$GLOBALS['integration']->updateUserRoles();
	}
	$_SESSION['cometchat']['error'] = 'Permissions applied successfully';
	header("Location:?module=membership&ts={$ts}");
	exit();
}
