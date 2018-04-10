function getCookie(name) { var value = "; " + document.cookie;
var parts = value.split("; " + name + "=");
if (parts.length == 2) return parts.pop().split(";").shift();
}
function getURLParameter (name,url) {
	if(typeof(url)=="undefined"){
		url = location.search
	}
	return decodeURI((RegExp(name + '=' + '(.+?)(&|$)').exec(url)||[,null])[1]);
}
function addEmbedIframe(iframeObj,callbackfn) {
	if(iframeObj.module=="synergy" || iframeObj.module=="chatrooms"){
		cc_synergy_enabled = 1;
		cc_embedded_enabled = 1;
		var cbfn='';
		if(typeof(callbackfn)!='undefined'){
			cbfn=callbackfn;
		}
		var dynamicCDNUrl = '<?php echo DYNAMIC_CDN_URL; ?>';
		if(jqcc('link[href*="'+dynamicCDNUrl+'cometchatcss.php"]').length<=0 && cbfn!='desktop') {
			jqcc('head').append('<?php echo getDynamicScriptAndLinkTags(array('layout'=>'embedded', 'ext'=>'css'));?>');
			addScript('<?php echo getDynamicScriptAndLinkTags(array('urlonly'=>1,'layout'=>'embedded','type'=>'core', 'name'=>'embedcode','ext'=>'js'));?>');
		}else{
			jqcc('link[href*="'+dynamicCDNUrl+'cometchatcss.php"]').attr('href','<?php echo getDynamicScriptAndLinkTags(array('layout'=>$layout,'urlonly'=>1, 'ext'=>'css'));?>');
		}
	}
	if(typeof(iframeObj.width)=="undefined"){
		iframeObj.width="100%";
	}
	if(typeof(iframeObj.height)=="undefined"){
		iframeObj.height="100%";
	}
	if(typeof(iframeObj.style)=="undefined"){
		iframeObj.style="";
	}
	<?php
		include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'config.php');
	?>
	var basedata = getURLParameter('basedata',iframeObj.src);
	var cookiePrefix = '<?php echo $cookiePrefix;?>';
	if(basedata!=null && (basedata+"").toLowerCase()!="null" && basedata!=""){
		document.cookie='<?php echo $cookiePrefix;?>data='+basedata;
	}else if(typeof(getCookie(cookiePrefix+'data'))!="undefined"){
		basedata = getCookie(cookiePrefix+'data');
	}
	var container = document.getElementById("cometchat_embed_"+iframeObj.module+"_container");
	var queryStringSeparator='&';
	if(iframeObj.src.indexOf('?')<0){
		queryStringSeparator='?';
	}
	iframeObj.src+= queryStringSeparator+"basedata="+basedata;
	var iframe = document.createElement('iframe');
	iframe.style.cssText = iframeObj.style;
	iframe.src = iframeObj.src;
	iframe.width = iframeObj.width;
	iframe.height = iframeObj.height;
	iframe.name = 'cometchat_'+iframeObj.module+'_iframe';
	iframe.id = 'cometchat_'+iframeObj.module+'_iframe';
	iframe.scrolling = 'no';
	iframe.setAttribute('class','cometchat_'+iframeObj.module+'_iframe');
	iframe.setAttribute('frameborder',0);
	iframe.setAttribute('nwfaketop','');
	iframe.setAttribute('allow','geolocation; microphone; camera; midi; encrypted-media;');
	container.appendChild(iframe);
}
function addScript(src) {
	var s = document.createElement( 'script' );
	s.setAttribute( 'id', 'cc_script' );
	s.setAttribute( 'src', src );
	document.head.appendChild( s );
}

(function($){
	$.cometchat = (function(){
		return {
			chatWith: function(uid){
				var controlparameters = {"type":"modules", "name":"cometchat", "method":"chatWith", "params":{"uid":uid}};
				controlparameters = JSON.stringify(controlparameters);
   				if(typeof(jqcc('#cometchat_synergy_iframe')[0]) != 'undefined'){
   					jqcc('#cometchat_synergy_iframe')[0].contentWindow.postMessage('CC^CONTROL_'+controlparameters,'*');
   				}
			},
			chatWithUID: function(uid = 0){
				var controlparameters = {"type":"modules", "name":"cometchat", "method":"chatWithUID", "params":{"uid":uid}};
				controlparameters = JSON.stringify(controlparameters);
   				if(typeof(jqcc('#cometchat_synergy_iframe')[0]) != 'undefined'){
   					jqcc('#cometchat_synergy_iframe')[0].contentWindow.postMessage('CC^CONTROL_'+controlparameters,'*');
   				}
			}
		};
	})();
})(jqcc);
