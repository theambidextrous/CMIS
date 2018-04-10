<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

if (!defined('CCADMIN')) { echo "NO DICE"; exit; }

function index() {
	global $body;
	global $color_original;
    global $layout_original;
    global $ts;

    $athemes = array();

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
			$activethemes .= '<tr><td id="'.$no.'" d1="'.$ti.'">'.stripslashes($title).$default.'</td><td><a data-toggle="tooltip" title="Generate Embed Code"  href="javascript:void(0)" onclick="javascript:themetype_embedcode(\''.$ti.'\')" ><img src="'.STATIC_CDN_URL.'admin/images/embed.png" ></a>&nbsp;&nbsp;<a href="../cometchat_embedded.php" target="_blank" data-toggle="tooltip" title="Direct link to Embedded" style="margin-right:5px;"><img src="'.STATIC_CDN_URL.'admin/images/link.png"></a></td></tr>';
		}else if(strtolower($ti) == 'docked'){
			$activethemes .= '<tr><td id="'.$no.'" d1="'.$ti.'">'.stripslashes($title).$default.'</td><td><a data-toggle="tooltip" title="Generate Footer Code" href="javascript:void(0)" onclick="javascript:themetype_embedcode(\''.$ti.'\')"><img src="'.STATIC_CDN_URL.'admin/images/embed.png"></a></td></tr>';
		} else {
			$activethemes .= '<tr><td id="'.$no.'" d1="'.$ti.'">'.stripslashes($title).$default.'</td><td><a data-toggle="tooltip" title="Edit '.$title.'" href="javascript:void(0)" onclick="javascript:themetype_configmodule(\''.$ti.'\')" style="margin-right:5px;"><img src="'.STATIC_CDN_URL.'admin/images/config.png"></a></td></tr>';
		}
	}

global $color;
global $colors;

$colorbox = '';
foreach($colors as $colorname => $val){
	$colordetails = unserialize($val[$colorname]);
	$colorbox .= '<div id="'.$colorname.'_'.$colordetails['primary'].'" style="background:#'.$colordetails['primary'].'" class="colorbox"><div class="tick" id="tick_'.$colorname.'_'.$colordetails['primary'].'"><img src="'.STATIC_CDN_URL.'admin/images/check.svg"/></div> </div>';
}
$colorval = unserialize($colors[$color][$color]);

$newcolorform = '';
$js = $colorUI ='';

$js .= <<<EOD
$('#primary').ColorPicker({
	color: '#{$colorval['primary']}',
	onShow: function (colpkr) {
		$(colpkr).fadeIn(500);
		return false;
	},
	onHide: function (colpkr) {
		$(colpkr).fadeOut(500);
		return false;
	},
	onChange: function (hsb, hex, rgb) {
		$('#primary div').css('backgroundColor', '#' + hex);
		$('#primary').attr('newcolor','#'+hex);
		document.getElementById('primary_field').setAttribute('value','#'+hex);
		$('#primary_field').trigger("change");
	}
});

$('#secondary').ColorPicker({
	color: '#{$colorval['secondary']}',
	onShow: function (colpkr) {
		$(colpkr).fadeIn(500);
		return false;
	},
	onHide: function (colpkr) {
		$(colpkr).fadeOut(500);
		return false;
	},
	onChange: function (hsb, hex, rgb) {
		$('#secondary div').css('backgroundColor', '#' + hex);
		$('#secondary').attr('newcolor','#'+hex);
		document.getElementById('secondary_field').setAttribute('value','#'+hex);
		$('#secondary_field').trigger("change");
	}
});

$('#hover').ColorPicker({
	color: '#{$colorval['hover']}',
	onShow: function (colpkr) {
		$(colpkr).fadeIn(500);
		return false;
	},
	onHide: function (colpkr) {
		$(colpkr).fadeOut(500);
		return false;
	},
	onChange: function (hsb, hex, rgb) {
		$('#hover div').css('backgroundColor', '#' + hex);
		$('#hover').attr('newcolor','#'+hex);
		document.getElementById('hover_field').setAttribute('value','#'+hex);
		$('#hover_field').trigger("change");
	}
});
EOD;

$colorUI .= <<<EOD
	<script type="text/javascript">
		$(document).ready(function(){
			$('#{$color}_{$colorval['primary']}').find('.tick').css('display','table');
			$('#{$color}_{$colorval['primary']}').find('.tick').addClass('selected');
			$('.colorbox').click(function(){
				$('.tick').css('display','none');
				$('.tick').removeClass('selected');
				$('#'+this.id).find('.tick').css('display','table');
				$('#'+this.id).find('.tick').addClass('selected');
			});
			$('#submit_color').click(function(){
				var tickclass = $('.tick');
				$.each(tickclass,function(i,val){
					if($('#'+this.id).hasClass('selected')){
						var name = this.id.split('_');
						$("#color_text").val(name[1]);
					}
				});
			});
		});
		$(function() { $js });
	</script>
			{$colorbox}
		<form action="?module=appearance&action=updatecolorval&ts={$ts}" method="post">
		<input id="color_text"  type="hidden" name="color"/><br>
		<button type="submit" id="submit_color" class="btn btn-primary" >Update Color</button>
		</form>
EOD;



$body .= <<<EOD
<div class="row">
  <div class="col-sm-6 col-lg-6">
  	<div class="row">
	  	<div class="col-sm-12 col-lg-12">
		    <div class="card">
		      <div class="card-header">Appearance
		      </div>
		      <div class="card-block">
		        {$colorUI}
		      </div>
		    </div>
		</div>
	</div>
  </div>
	<div class="col-sm-6 col-lg-6">
		<div class="card">
		<div class="card-header">
			Add New Color
		</div>
		<div class="card-block">
		  	<form action="?module=appearance&action=addnewcolor&ts={$ts}" method="post">
	      		<div class="form-group row">
	      			<div class="col-md-12">
						<label class="form-control-label">Primary Color:</label>
					</div>
					<div class="col-md-1">
						<div class="colorSelector themeSettings" field="primary" id="primary" oldcolor="#{$colorval['primary']}" newcolor="#{$colorval['primary']}">
							<div style="background:#{$colorval['primary']}"></div>
						</div>
					</div>
					<div class="col-md-10">
						<input type="text" class="form-control" id="primary_field" name="primary" value="#{$colorval['primary']}" required="true"/>
					</div>
				</div>

	      		<div class="form-group row">
	      			<div class="col-md-12">
						<label class="form-control-label">Dark Color:</label>
					</div>
					<div class="col-md-1">
						<div class="colorSelector themeSettings" field="secondary" id="secondary" oldcolor="#{$colorval['secondary']}" newcolor="#{$colorval['secondary']}">
							<div style="background:#{$colorval['secondary']}"></div>
						</div>
					</div>
					<div class="col-md-10">
						<input type="text" class="form-control" id="secondary_field" name="secondary" value="#{$colorval['secondary']}" required="true"/>
					</div>
				</div>

	      		<div class="form-group row">
	      			<div class="col-md-12">
						<label class="form-control-label">Menu Color:</label>
					</div>
					<div class="col-md-1">
						<div class="colorSelector themeSettings" field="hover" id="hover" oldcolor="#{$colorval['hover']}" newcolor="#{$colorval['hover']}">
							<div style="background:#{$colorval['hover']}"></div>
						</div>
					</div>
					<div class="col-md-10">
						<input type="text" class="form-control" id="hover_field" name="hover" value="#{$colorval['hover']}" required="true"/>
					</div>
				</div>
				<button type="submit" id="add_color" class="btn btn-primary">Add Color</button>
			</form>
		    </div>
		</div>
	</div>

</div>
EOD;

	template();
}

function updatecolorval(){
	$color = $_POST['color'];
	global $ts;
	configeditor(array('color'=>$color));
	$_SESSION['cometchat']['error'] = 'Color updated successfully.';
	header('Location:?module=appearance&ts='.$ts);
}

function addnewcolor(){
	global $ts;
	global $colors;
	global $client;
	$primary = $_POST['primary'];
	$secondary = $_POST['secondary'];
	$hover = $_POST['hover'];

	if(substr($primary,0,1) == '#' && substr($secondary,0,1) == '#' && substr($hover,0,1) == '#'){
		$primary = substr($primary,1);
		$secondary = substr($secondary,1);
		$hover = substr($hover,1);
		$colordetails = array('primary' => $primary, 'secondary' => $secondary, 'hover' => $hover);
		$colorvalue = serialize($colordetails);
		$colorname = 'color9'.sql_real_escape_string($ts);

		foreach($colors as $name => $val){
			if($val[$name] == $colorvalue) {
				$_SESSION['cometchat']['error'] = 'Color already exists';
				header("Location:?module=appearance&ts={$ts}");
			}
		}
		$query = sql_query('admin_addnewcolor',array('color_key'=>$colorname, 'color_value'=>$colorvalue));
		$_SESSION['cometchat']['error'] = 'New color added successfully';
		removeCachedSettings($client.'cometchat_color');
		header("Location:?module=appearance&ts={$ts}");
	}
}

function removecolorprocess() {
    global $ts;
    global $client;
	$color = $_GET['data'];
	$color_array = array('docked','embedded');

	if (!in_array($color, $color_array) && !empty($color)) {
		sql_query('admin_removecolor',array('color'=>$color));
		removeCachedSettings($client.'cometchat_color');
		$_SESSION['cometchat']['error'] = 'Color scheme deleted successfully';
	} else {
		$_SESSION['cometchat']['error'] = 'Sorry, this color scheme cannot be deleted. Please manually remove the theme from the "themes/color" folder.';
	}
	header("Location:?module=appearance&ts={$ts}");
}
