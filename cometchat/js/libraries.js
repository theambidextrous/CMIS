/*
 * CometChat
 * Copyright (c) 2016 Inscripts - support@cometchat.com | http://www.cometchat.com | http://www.inscripts.com
*/
 <?php if ($windowFavicon == 1) { ?>

/**
 * @license MIT
 * @fileOverview Favico animations
 * @author Miroslav Magda, http://blog.ejci.net
 * @version 0.3.3
 */
!function(){var e=function(e){"use strict";function t(e){if(e.paused||e.ended||w)return!1;try{d.clearRect(0,0,h,s),d.drawImage(e,0,0,h,s)}catch(o){}setTimeout(t,U.duration,e),L.setIcon(c)}function o(e){var t=/^#?([a-f\d])([a-f\d])([a-f\d])$/i;e=e.replace(t,function(e,t,o,n){return t+t+o+o+n+n});var o=/^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(e);return o?{r:parseInt(o[1],16),g:parseInt(o[2],16),b:parseInt(o[3],16)}:!1}function n(e,t){var o,n={};for(o in e)n[o]=e[o];for(o in t)n[o]=t[o];return n}function r(){return document.hidden||document.msHidden||document.webkitHidden||document.mozHidden}e=e?e:{};var i,a,s,h,c,d,u,l,f,g,y,w,m,x={bgColor:"#d00",textColor:"#fff",fontFamily:"sans-serif",fontStyle:"bold",type:"circle",position:"down",animation:"slide",elementId:!1};m={},m.ff=/firefox/i.test(navigator.userAgent.toLowerCase()),m.chrome=/chrome/i.test(navigator.userAgent.toLowerCase()),m.opera=/opera/i.test(navigator.userAgent.toLowerCase()),m.ie=/msie/i.test(navigator.userAgent.toLowerCase())||/trident/i.test(navigator.userAgent.toLowerCase()),m.supported=m.chrome||m.ff||m.opera;var p=[];y=function(){},l=w=!1;var v=function(){if(i=n(x,e),i.bgColor=o(i.bgColor),i.textColor=o(i.textColor),i.position=i.position.toLowerCase(),i.animation=U.types[""+i.animation]?i.animation:x.animation,"up"===i.position)for(var t=0;t<U.types[""+i.animation].length;t++){var r=U.types[""+i.animation][t];r.y=r.y<.6?r.y-.4:r.y-2*r.y+(1-r.w),U.types[""+i.animation][t]=r}i.type=C[""+i.type]?i.type:x.type;try{a=L.getIcon(),c=document.createElement("canvas"),u=document.createElement("img"),a.hasAttribute("href")?(u.setAttribute("src",a.getAttribute("href")),u.onload=function(){s=u.height>0?u.height:32,h=u.width>0?u.width:32,c.height=s,c.width=h,d=c.getContext("2d"),b.ready()}):(u.setAttribute("src",""),s=32,h=32,u.height=s,u.width=h,c.height=s,c.width=h,d=c.getContext("2d"),b.ready())}catch(l){}},b={};b.ready=function(){l=!0,b.reset(),y()},b.reset=function(){p=[],f=!1,d.clearRect(0,0,h,s),d.drawImage(u,0,0,h,s),L.setIcon(c)},b.start=function(){if(l&&!g){var e=function(){f=p[0],g=!1,p.length>0&&(p.shift(),b.start())};p.length>0&&(g=!0,f?U.run(f.options,function(){U.run(p[0].options,function(){e()},!1)},!0):U.run(p[0].options,function(){e()},!1))}};var C={},M=function(e){return e.n=Math.abs(e.n),e.x=h*e.x,e.y=s*e.y,e.w=h*e.w,e.h=s*e.h,e};C.circle=function(e){e=M(e);var t=!1;e.n>9&&e.n<100?(e.x=e.x-.4*e.w,e.w=1.4*e.w,t=!0):e.n>=100&&(e.x=e.x-.65*e.w,e.w=1.65*e.w,t=!0),d.clearRect(0,0,h,s),d.drawImage(u,0,0,h,s),d.beginPath(),d.font=i.fontStyle+" "+Math.floor(e.h*(e.n>99?.85:1))+"px "+i.fontFamily,d.textAlign="center",t?(d.moveTo(e.x+e.w/2,e.y),d.lineTo(e.x+e.w-e.h/2,e.y),d.quadraticCurveTo(e.x+e.w,e.y,e.x+e.w,e.y+e.h/2),d.lineTo(e.x+e.w,e.y+e.h-e.h/2),d.quadraticCurveTo(e.x+e.w,e.y+e.h,e.x+e.w-e.h/2,e.y+e.h),d.lineTo(e.x+e.h/2,e.y+e.h),d.quadraticCurveTo(e.x,e.y+e.h,e.x,e.y+e.h-e.h/2),d.lineTo(e.x,e.y+e.h/2),d.quadraticCurveTo(e.x,e.y,e.x+e.h/2,e.y)):d.arc(e.x+e.w/2,e.y+e.h/2,e.h/2,0,2*Math.PI),d.fillStyle="rgba("+i.bgColor.r+","+i.bgColor.g+","+i.bgColor.b+","+e.o+")",d.fill(),d.closePath(),d.beginPath(),d.stroke(),d.fillStyle="rgba("+i.textColor.r+","+i.textColor.g+","+i.textColor.b+","+e.o+")",e.n>999?d.fillText((e.n>9999?9:Math.floor(e.n/1e3))+"k+",Math.floor(e.x+e.w/2),Math.floor(e.y+e.h-.2*e.h)):d.fillText(e.n,Math.floor(e.x+e.w/2),Math.floor(e.y+e.h-.15*e.h)),d.closePath()},C.rectangle=function(e){e=M(e);var t=!1;e.n>9&&e.n<100?(e.x=e.x-.4*e.w,e.w=1.4*e.w,t=!0):e.n>=100&&(e.x=e.x-.65*e.w,e.w=1.65*e.w,t=!0),d.clearRect(0,0,h,s),d.drawImage(u,0,0,h,s),d.beginPath(),d.font="bold "+Math.floor(e.h*(e.n>99?.9:1))+"px sans-serif",d.textAlign="center",d.fillStyle="rgba("+i.bgColor.r+","+i.bgColor.g+","+i.bgColor.b+","+e.o+")",d.fillRect(e.x,e.y,e.w,e.h),d.fillStyle="rgba("+i.textColor.r+","+i.textColor.g+","+i.textColor.b+","+e.o+")",e.n>999?d.fillText((e.n>9999?9:Math.floor(e.n/1e3))+"k+",Math.floor(e.x+e.w/2),Math.floor(e.y+e.h-.2*e.h)):d.fillText(e.n,Math.floor(e.x+e.w/2),Math.floor(e.y+e.h-.15*e.h)),d.closePath()};var I=function(e,t){y=function(){try{if(e>0){if(U.types[""+t]&&(i.animation=t),p.push({type:"badge",options:{n:e}}),p.length>100)throw"Too many badges requests in queue.";b.start()}else b.reset()}catch(o){throw"Error setting badge. Message: "+o.message}},l&&y()},A=function(e){y=function(){try{var t=e.width,o=e.height,n=document.createElement("img"),r=o/s>t/h?t/h:o/s;n.setAttribute("src",e.getAttribute("src")),n.height=o/r,n.width=t/r,d.clearRect(0,0,h,s),d.drawImage(n,0,0,h,s),L.setIcon(c)}catch(i){throw"Error setting image. Message: "+i.message}},l&&y()},E=function(e){y=function(){try{if("stop"===e)return w=!0,b.reset(),w=!1,void 0;e.addEventListener("play",function(){t(this)},!1)}catch(o){throw"Error setting video. Message: "+o.message}},l&&y()},T=function(e){if(window.URL&&window.URL.createObjectURL||(window.URL=window.URL||{},window.URL.createObjectURL=function(e){return e}),m.supported){var o=!1;navigator.getUserMedia=navigator.getUserMedia||navigator.oGetUserMedia||navigator.msGetUserMedia||navigator.mozGetUserMedia||navigator.webkitGetUserMedia,y=function(){try{if("stop"===e)return w=!0,b.reset(),w=!1,void 0;o=document.createElement("video"),o.width=h,o.height=s,navigator.getUserMedia({video:!0,audio:!1},function(e){o.src=URL.createObjectURL(e),o.play(),t(o)},function(){})}catch(n){throw"Error setting webcam. Message: "+n.message}},l&&y()}},L={};L.getIcon=function(){var e=!1,t="",o=function(){for(var e=document.getElementsByTagName("head")[0].getElementsByTagName("link"),t=e.length,o=t-1;o>=0;o--)if(/(^|\s)icon(\s|$)/i.test(e[o].getAttribute("rel")))return e[o];return!1};if(i.elementId?(e=document.getElementById(i.elementId),e.setAttribute("href",e.getAttribute("src"))):(e=o(),e===!1&&(e=document.createElement("link"),e.setAttribute("rel","icon"),document.getElementsByTagName("head")[0].appendChild(e))),t=i.elementId?e.src:e.href,-1===t.indexOf(document.location.hostname))throw new Error("Error setting favicon. Favicon image is on different domain (Icon: "+t+", Domain: "+document.location.hostname+")");return e.setAttribute("type","image/png"),e},L.setIcon=function(e){var t=e.toDataURL("image/png");if(i.elementId)document.getElementById(i.elementId).setAttribute("src",t);else if(m.ff||m.opera){var o=a;a=document.createElement("link"),m.opera&&a.setAttribute("rel","icon"),a.setAttribute("rel","icon"),a.setAttribute("type","image/png"),document.getElementsByTagName("head")[0].appendChild(a),a.setAttribute("href",t),o.parentNode&&o.parentNode.removeChild(o)}else a.setAttribute("href",t)};var U={};return U.duration=40,U.types={},U.types.fade=[{x:.4,y:.4,w:.6,h:.6,o:0},{x:.4,y:.4,w:.6,h:.6,o:.1},{x:.4,y:.4,w:.6,h:.6,o:.2},{x:.4,y:.4,w:.6,h:.6,o:.3},{x:.4,y:.4,w:.6,h:.6,o:.4},{x:.4,y:.4,w:.6,h:.6,o:.5},{x:.4,y:.4,w:.6,h:.6,o:.6},{x:.4,y:.4,w:.6,h:.6,o:.7},{x:.4,y:.4,w:.6,h:.6,o:.8},{x:.4,y:.4,w:.6,h:.6,o:.9},{x:.4,y:.4,w:.6,h:.6,o:1}],U.types.none=[{x:.4,y:.4,w:.6,h:.6,o:1}],U.types.pop=[{x:1,y:1,w:0,h:0,o:1},{x:.9,y:.9,w:.1,h:.1,o:1},{x:.8,y:.8,w:.2,h:.2,o:1},{x:.7,y:.7,w:.3,h:.3,o:1},{x:.6,y:.6,w:.4,h:.4,o:1},{x:.5,y:.5,w:.5,h:.5,o:1},{x:.4,y:.4,w:.6,h:.6,o:1}],U.types.popFade=[{x:.75,y:.75,w:0,h:0,o:0},{x:.65,y:.65,w:.1,h:.1,o:.2},{x:.6,y:.6,w:.2,h:.2,o:.4},{x:.55,y:.55,w:.3,h:.3,o:.6},{x:.5,y:.5,w:.4,h:.4,o:.8},{x:.45,y:.45,w:.5,h:.5,o:.9},{x:.4,y:.4,w:.6,h:.6,o:1}],U.types.slide=[{x:.4,y:1,w:.6,h:.6,o:1},{x:.4,y:.9,w:.6,h:.6,o:1},{x:.4,y:.9,w:.6,h:.6,o:1},{x:.4,y:.8,w:.6,h:.6,o:1},{x:.4,y:.7,w:.6,h:.6,o:1},{x:.4,y:.6,w:.6,h:.6,o:1},{x:.4,y:.5,w:.6,h:.6,o:1},{x:.4,y:.4,w:.6,h:.6,o:1}],U.run=function(e,t,o,a){var s=U.types[r()?"none":i.animation];return a=o===!0?"undefined"!=typeof a?a:s.length-1:"undefined"!=typeof a?a:0,t=t?t:function(){},a<s.length&&a>=0?(C[i.type](n(e,s[a])),setTimeout(function(){o?a-=1:a+=1,U.run(e,t,o,a)},U.duration),L.setIcon(c),void 0):(t(),void 0)},v(),{badge:I,video:E,image:A,webcam:T,reset:b.reset}};"undefined"!=typeof define&&define.amd?define([],function(){return e}):"undefined"!=typeof module&&module.exports?module.exports=e:this.Favico=e}();
<?php } ?>

if(typeof(jqcc) === 'undefined') {
	jqcc = jQuery;
}
// Copyright (c) 2006 Klaus Hartl (stilbuero.de)
// http://www.opensource.org/licenses/mit-license.php

jqcc.cookie=function(a,b,c){if(typeof b!='undefined'){c=c||{};if(b===null){b='';c.expires=-1}var d='';if(c.expires&&(typeof c.expires=='number'||c.expires.toUTCString)){var e;if(typeof c.expires=='number'){e=new Date();e.setTime(e.getTime()+(c.expires*24*60*60*1000))}else{e=c.expires}d='; expires='+e.toUTCString()}var f=c.path?'; path='+(c.path):'';var g=c.domain?'; domain='+(c.domain):'';var h=c.secure?'; secure':'';document.cookie=[a,'=',encodeURIComponent(b),d,f,g,h].join('')}else{var j=null;if(document.cookie&&document.cookie!=''){var k=document.cookie.split(';');for(var i=0;i<k.length;i++){var l=jqcc.trim(k[i]);if(l.substring(0,a.length+1)==(a+'=')){j=decodeURIComponent(l.substring(a.length+1));break}}}return j}};

// SWFObject is (c) 2007 Geoff Stearns and is released under the MIT License
// http://www.opensource.org/licenses/mit-license.php

if(typeof deconcept=="undefined"){var deconcept=new Object();}if(typeof deconcept.util=="undefined"){deconcept.util=new Object();}if(typeof deconcept.SWFObjectCCUtil=="undefined"){deconcept.SWFObjectCCUtil=new Object();}deconcept.SWFObjectCC=function(_1,id,w,h,_5,c,_7,_8,_9,_a){if(!document.getElementById){return;}this.DETECT_KEY=_a?_a:"detectflash";this.skipDetect=deconcept.util.getRequestParameter(this.DETECT_KEY);this.params=new Object();this.variables=new Object();this.attributes=new Array();if(_1){this.setAttribute("swf",_1);}if(id){this.setAttribute("id",id);}if(w){this.setAttribute("width",w);}if(h){this.setAttribute("height",h);}if(_5){this.setAttribute("version",new deconcept.PlayerVersion(_5.toString().split(".")));}this.installedVer=deconcept.SWFObjectCCUtil.getPlayerVersion();if(!window.opera&&document.all&&this.installedVer.major>7){deconcept.SWFObjectCC.doPrepUnload=true;}if(c){this.addParam("bgcolor",c);}var q=_7?_7:"high";this.addParam("quality",q);this.setAttribute("useExpressInstall",false);this.setAttribute("doExpressInstall",false);var _c=(_8)?_8:window.location;this.setAttribute("xiRedirectUrl",_c);this.setAttribute("redirectUrl","");if(_9){this.setAttribute("redirectUrl",_9);}};deconcept.SWFObjectCC.prototype={useExpressInstall:function(_d){this.xiSWFPath=!_d?"expressinstall.swf":_d;this.setAttribute("useExpressInstall",true);},setAttribute:function(_e,_f){this.attributes[_e]=_f;},getAttribute:function(_10){return this.attributes[_10];},addParam:function(_11,_12){this.params[_11]=_12;},getParams:function(){return this.params;},addVariable:function(_13,_14){this.variables[_13]=_14;},getVariable:function(_15){return this.variables[_15];},getVariables:function(){return this.variables;},getVariablePairs:function(){var _16=new Array();var key;var _18=this.getVariables();for(key in _18){_16[_16.length]=key+"="+_18[key];}return _16;},getSWFHTML:function(){var _19="";if(navigator.plugins&&navigator.mimeTypes&&navigator.mimeTypes.length){if(this.getAttribute("doExpressInstall")){this.addVariable("MMplayerType","PlugIn");this.setAttribute("swf",this.xiSWFPath);}_19="<embed type=\"application/x-shockwave-flash\" src=\""+this.getAttribute("swf")+"\" width=\""+this.getAttribute("width")+"\" height=\""+this.getAttribute("height")+"\" style=\""+this.getAttribute("style")+"\"";_19+=" id=\""+this.getAttribute("id")+"\" name=\""+this.getAttribute("id")+"\" ";var _1a=this.getParams();for(var key in _1a){_19+=[key]+"=\""+_1a[key]+"\" ";}var _1c=this.getVariablePairs().join("&");if(_1c.length>0){_19+="flashvars=\""+_1c+"\"";}_19+="/>";}else{if(this.getAttribute("doExpressInstall")){this.addVariable("MMplayerType","ActiveX");this.setAttribute("swf",this.xiSWFPath);}_19="<object id=\""+this.getAttribute("id")+"\" classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" width=\""+this.getAttribute("width")+"\" height=\""+this.getAttribute("height")+"\" style=\""+this.getAttribute("style")+"\">";_19+="<param name=\"movie\" value=\""+this.getAttribute("swf")+"\" />";var _1d=this.getParams();for(var key in _1d){_19+="<param name=\""+key+"\" value=\""+_1d[key]+"\" />";}var _1f=this.getVariablePairs().join("&");if(_1f.length>0){_19+="<param name=\"flashvars\" value=\""+_1f+"\" />";}_19+="</object>";}return _19;},write:function(_20){if(this.getAttribute("useExpressInstall")){var _21=new deconcept.PlayerVersion([6,0,65]);if(this.installedVer.versionIsValid(_21)&&!this.installedVer.versionIsValid(this.getAttribute("version"))){this.setAttribute("doExpressInstall",true);this.addVariable("MMredirectURL",escape(this.getAttribute("xiRedirectUrl")));document.title=document.title.slice(0,47)+" - Flash Player Installation";this.addVariable("MMdoctitle",document.title);}}if(this.skipDetect||this.getAttribute("doExpressInstall")||this.installedVer.versionIsValid(this.getAttribute("version"))){var n=(typeof _20=="string")?document.getElementById(_20):_20;n.innerHTML=this.getSWFHTML();return true;}else{if(this.getAttribute("redirectUrl")!=""){document.location.replace(this.getAttribute("redirectUrl"));}}return false;}};deconcept.SWFObjectCCUtil.getPlayerVersion=function(){var _23=new deconcept.PlayerVersion([0,0,0]);if(navigator.plugins&&navigator.mimeTypes.length){var x=navigator.plugins["Shockwave Flash"];if(x&&x.description){_23=new deconcept.PlayerVersion(x.description.replace(/([a-zA-Z]|\s)+/,"").replace(/(\s+r|\s+b[0-9]+)/,".").split("."));}}else{if(navigator.userAgent&&navigator.userAgent.indexOf("Windows CE")>=0){var axo=1;var _26=3;while(axo){try{_26++;axo=new ActiveXObject("ShockwaveFlash.ShockwaveFlash."+_26);_23=new deconcept.PlayerVersion([_26,0,0]);}catch(e){axo=null;}}}else{try{var axo=new ActiveXObject("ShockwaveFlash.ShockwaveFlash.7");}catch(e){try{var axo=new ActiveXObject("ShockwaveFlash.ShockwaveFlash.6");_23=new deconcept.PlayerVersion([6,0,21]);axo.AllowScriptAccess="always";}catch(e){if(_23.major==6){return _23;}}try{axo=new ActiveXObject("ShockwaveFlash.ShockwaveFlash");}catch(e){}}if(axo!=null){_23=new deconcept.PlayerVersion(axo.GetVariable("$version").split(" ")[1].split(","));}}}return _23;};deconcept.PlayerVersion=function(_29){this.major=_29[0]!=null?parseInt(_29[0]):0;this.minor=_29[1]!=null?parseInt(_29[1]):0;this.rev=_29[2]!=null?parseInt(_29[2]):0;};deconcept.PlayerVersion.prototype.versionIsValid=function(fv){if(this.major<fv.major){return false;}if(this.major>fv.major){return true;}if(this.minor<fv.minor){return false;}if(this.minor>fv.minor){return true;}if(this.rev<fv.rev){return false;}return true;};deconcept.util={getRequestParameter:function(_2b){var q=document.location.search||document.location.hash;if(_2b==null){return q;}if(q){var _2d=q.substring(1).split("&");for(var i=0;i<_2d.length;i++){if(_2d[i].substring(0,_2d[i].indexOf("="))==_2b){return _2d[i].substring((_2d[i].indexOf("=")+1));}}}return "";}};deconcept.SWFObjectCCUtil.cleanupSWFs=function(){var _2f=document.getElementsByTagName("OBJECT");for(var i=_2f.length-1;i>=0;i--){_2f[i].style.display="none";for(var x in _2f[i]){if(typeof _2f[i][x]=="function"){_2f[i][x]=function(){};}}}};if(deconcept.SWFObjectCC.doPrepUnload){if(!deconcept.unloadSet){deconcept.SWFObjectCCUtil.prepUnload=function(){__flash_unloadHandler=function(){};__flash_savedUnloadHandler=function(){};window.attachEvent("onunload",deconcept.SWFObjectCCUtil.cleanupSWFs);};window.attachEvent("onbeforeunload",deconcept.SWFObjectCCUtil.prepUnload);deconcept.unloadSet=true;}}if(!document.getElementById&&document.all){document.getElementById=function(id){return document.all[id];};}var getQueryParamValue=deconcept.util.getRequestParameter;var FlashObject=deconcept.SWFObjectCC;var SWFObjectCC=deconcept.SWFObjectCC;


/**
 * Copyright (c) 2007-2009 Ariel Flesler - aflesler(at)gmail(dot)com
 * http://flesler.blogspot.com/2007/10/jqccscrollto.html
 */

(function($){var h=$.scrollToCC=function(a,b,c){$(window).scrollToCC(a,b,c)};h.defaults={axis:'xy',duration:parseFloat($.fn.jqcc)>=1.3?0:1};h.window=function(a){return $(window)._scrollable()};$.fn._scrollable=function(){return this.map(function(){var a=this,isWin=!a.nodeName||$.inArray(a.nodeName.toLowerCase(),['iframe','#document','html','body'])!=-1;if(!isWin)return a;var b=(a.contentWindow||a).document||a.ownerDocument||a;return $.browser.safari||b.compatMode=='BackCompat'?b.body:b.documentElement})};$.fn.scrollToCC=function(e,f,g){if(typeof f=='object'){g=f;f=0}if(typeof g=='function')g={onAfter:g};if(e=='max')e=9e9;g=$.extend({},h.defaults,g);f=f||g.speed||g.duration;g.queue=g.queue&&g.axis.length>1;if(g.queue)f/=2;g.offset=both(g.offset);g.over=both(g.over);return this._scrollable().each(function(){var d=this,$elem=$(d),targ=e,toff,attr={},win=$elem.is('html,body');switch(typeof targ){case'number':case'string':if((/^([+-]=)?\d+(\.\d+)?(px|%)?$/.test(targ)) || (targ.charAt(0)=='-' && targ.charAt(1)!='=') ){targ=both(targ);break}targ=$(targ,this);case'object':if(targ.is||targ.style)toff=(targ=$(targ)).offset()}$.each(g.axis.split(''),function(i,a){var b=a=='x'?'Left':'Top',pos=b.toLowerCase(),key='scroll'+b,old=d[key],max=h.max(d,a);if(toff){attr[key]=toff[pos]+(win?0:old-$elem.offset()[pos]);if(g.margin){attr[key]-=parseInt(targ.css('margin'+b))||0;attr[key]-=parseInt(targ.css('border'+b+'Width'))||0}attr[key]+=g.offset[pos]||0;if(g.over[pos])attr[key]+=targ[a=='x'?'width':'height']()*g.over[pos]}else{var c=targ[pos];attr[key]=c.slice&&c.slice(-1)=='%'?parseFloat(c)/100*max:c}if(/^\d+$/.test(attr[key]))attr[key]=attr[key]<=0?0:Math.min(attr[key],max);if(!i&&g.queue){if(old!=attr[key])animate(g.onAfterFirst);delete attr[key]}});animate(g.onAfter);function animate(a){$elem.animate(attr,f,g.easing,a&&function(){a.call(this,e,g)})}}).end()};h.max=function(a,b){var c=b=='x'?'Width':'Height',scroll='scroll'+c;if(!$(a).is('html,body'))return a[scroll]-$(a)[c.toLowerCase()]();var d='client'+c,html=a.ownerDocument.documentElement,body=a.ownerDocument.body;return Math.max(html[scroll],body[scroll])-Math.min(html[d],body[d])};function both(a){return typeof a=='object'?a:{top:a,left:a}}})(jqcc);

/*
 jqcc.fullscreen 1.1.4
 https://github.com/kayahr/jqcc-fullscreen-plugin
 Copyright (C) 2012 Klaus Reimer <k@ailis.de>
 Licensed under the MIT license
 (See http://www.opensource.org/licenses/mit-license)
*/

function d(b){var c,a;if(!this.length)return this;c=this[0];c.ownerDocument?a=c.ownerDocument:(a=c,c=a.documentElement);if(null==b){if(!a.cancelFullScreen&&!a.webkitCancelFullScreen&&!a.mozCancelFullScreen)return null;b=!!a.fullScreen||!!a.webkitIsFullScreen||!!a.mozFullScreen;return!b?b:a.fullScreenElement||a.webkitCurrentFullScreenElement||a.mozFullScreenElement||b}b?(b=c.requestFullScreen||c.webkitRequestFullScreen||c.mozRequestFullScreen)&&(Element.ALLOW_KEYBOARD_INPUT?b.call(c,Element.ALLOW_KEYBOARD_INPUT):
b.call(c)):(b=a.cancelFullScreen||a.webkitCancelFullScreen||a.mozCancelFullScreen)&&b.call(a);return this}jqcc.fn.fullScreen=d;jqcc.fn.toggleFullScreen=function(){return d.call(this,!d.call(this))};var e,f,g;e=document;e.webkitCancelFullScreen?(f="webkitfullscreenchange",g="webkitfullscreenerror"):e.mozCancelFullScreen?(f="mozfullscreenchange",g="mozfullscreenerror"):(f="fullscreenchange",g="fullscreenerror");jqcc(document).bind(f,function(){jqcc(document).trigger(new jqcc.Event("fullscreenchange"))});
jqcc(document).bind(g,function(){jqcc(document).trigger(new jqcc.Event("fullscreenerror"))});

/* iContains changes*/
jqcc.expr[':'].icontains = function(a, i, m){
	return (a.textContent||a.innerText||"").toLowerCase().indexOf(m[3].toLowerCase())>=0;
};
/* ----- */

if (!Array.prototype.indexOf){Array.prototype.indexOf = function(elt){var len = this.length >>> 0;var from = Number(arguments[1]) || 0;from = (from < 0)? Math.ceil(from): Math.floor(from);if (from < 0)from += len;for (; from < len; from++){if (from in this && this[from] === elt)return from;}return -1;};}

/*-------GOOGLE ANALYTICS START--------*/
<?php if(!empty($gatrackerid)) {?>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', '<?php echo $gatrackerid; ?>' , 'auto');
  ga('send', 'pageview');
<?php } ?>
 /*-------GOOGLE ANALYTICS END--------*/


 /*-------- Stop Watch Plugin Start ----------*/
 (function($){$.extend({stopwatch:{formatTimer:function(a){if(a<10){a='0'+ a;}
return a;},startTimer:function(dir){var a;$.stopwatch.dir=dir;$.stopwatch.d1=new Date();switch($.stopwatch.state){case'pause':$.stopwatch.t1=$.stopwatch.d1.getTime()- $.stopwatch.td;break;default:$.stopwatch.t1=$.stopwatch.d1.getTime();if($.stopwatch.dir==='cd'){$.stopwatch.t1+=parseInt($('#cd_seconds').val())*1000;}
break;}
$.stopwatch.state='alive';$('#'+ $.stopwatch.dir+'_status').html('Running');$.stopwatch.loopTimer();},pauseTimer:function(){$.stopwatch.dp=new Date();$.stopwatch.tp=$.stopwatch.dp.getTime();$.stopwatch.td=$.stopwatch.tp- $.stopwatch.t1;$('#'+ $.stopwatch.dir+'_start').val('Resume');$.stopwatch.state='pause';$('#'+ $.stopwatch.dir+'_status').html('Paused');},stopTimer:function(){$('#'+ $.stopwatch.dir+'_start').val('Restart');$.stopwatch.state='stop';$('#'+ $.stopwatch.dir+'_status').html('Stopped');},resetTimer:function(){$('#'+ $.stopwatch.dir+'_ms,#'+ $.stopwatch.dir+'_s,#'+ $.stopwatch.dir+'_m,#'+ $.stopwatch.dir+'_h').html('00');$('#'+ $.stopwatch.dir+'_start').val('Start');$.stopwatch.state='reset';$('#'+ $.stopwatch.dir+'_status').html('Reset & Idle again');},endTimer:function(callback){$('#'+ $.stopwatch.dir+'_start').val('Restart');$.stopwatch.state='end';if(typeof callback==='function'){callback();}},loopTimer:function(){var td;var d2,t2;var ms=0;var s=0;var m=0;var h=0;if($.stopwatch.state==='alive'){d2=new Date();t2=d2.getTime();if($.stopwatch.dir==='sw'){td=t2- $.stopwatch.t1;}else{td=$.stopwatch.t1- t2;if(td<=0){$.stopwatch.endTimer(function(){$.stopwatch.resetTimer();$('#'+ $.stopwatch.dir+'_status').html('Ended & Reset');});}}
ms=td%1000;if(ms<1){ms=0;}else{s=(td-ms)/1000;
if(s<1){s=0;}else{var m=(s-(s%60))/60;
if(m<1){m=0;}else{var h=(m-(m%60))/60;
if(h<1){h=0;}}}}
ms=Math.round(ms/100);s=s-(m*60);m=m-(h*60);$('#'+ $.stopwatch.dir+'_ms').html($.stopwatch.formatTimer(ms));$('#'+ $.stopwatch.dir+'_s').html($.stopwatch.formatTimer(s));$('#'+ $.stopwatch.dir+'_m').html($.stopwatch.formatTimer(m));$('#'+ $.stopwatch.dir+'_h').html($.stopwatch.formatTimer(h));$.stopwatch.t=setTimeout($.stopwatch.loopTimer,1);}else{clearTimeout($.stopwatch.t);return true;}}}});})(jqcc);
/*-------- Stop Watch Plugin End ----------*/

var cc_zindex = 0;
var cc_windownames = [];

if(typeof closeCCPopup === "undefined") {
	var type = "<?php echo $type; ?>";
	var $name = "<?php echo $name; ?>";
	var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
	var eventer = window[eventMethod];
	var messageEvent = eventMethod == "attachEvent" ? "onmessage" : "message";

/* Listen to message from child window */
	switch(type){
		case "extension":
			eventer(messageEvent,function(e) {
				if(typeof(e.data) != 'undefined' && typeof(e.data) == 'string') {
					if(e.data.indexOf('CC^CONTROL_')!== -1){
						var controlparameters = e.data.slice(11);
						controlparameters = JSON.parse(controlparameters);
						if(controlparameters.type == 'extensions' && controlparameters.method == 'checkResponse'){
							var controlparameters = {"type":"extensions", "name":"mobilewebapp", "method":"clearTimeout", "params":{"timeOut":controlparameters.params.timeOut}};
							controlparameters = JSON.stringify(controlparameters);
							e.source.postMessage('CC^CONTROL_'+controlparameters,'*');
						}
					} else if(e.data.indexOf('ccmobilewebapp_reinitializeauth')!== -1){
						jqcc.mobilewebapp.reinitialize();
					}
				}
			},false);
		break;
		case "module":
			switch($name){
				case "chatrooms":
					eventer(messageEvent,function(e) {
						if(typeof(e.data) != 'undefined' && typeof(e.data) == 'string'){
							if(e.data.indexOf('CC^CONTROL_')!== -1){
								var controlparameters = e.data.slice(11);
								controlparameters = JSON.parse(controlparameters);
								if(controlparameters.name == 'cometchat' && controlparameters.method == 'processcontrolmessage'){
									/* Chatroom ProcessControlMessage Call */
									var message = jqcc[controlparameters.name][controlparameters.method](controlparameters.item);
									/* Return post Message incase of Chat History plugin */
									var returnparameters = {"message":message, "item":controlparameters.item, "processcontrolmessageResponse":1};
									e.source.postMessage(returnparameters,'*');
								} else if(controlparameters.name == "cometchat" && controlparameters.method == "setInternalVariable"){
									/* This will send setInternalVariable  call from Chatroom to main CometChat to set a variable for A/V Chat calls..  */
									var controlparameters = {"type":"plugins", "name":"cometchat", "method":"setInternalVariable", "params":{"type":controlparameters.params.type, "grp":controlparameters.params.grp, "value":controlparameters.params.value}};
										controlparameters = JSON.stringify(controlparameters);
									if(typeof(parent) != 'undefined'){
										parent.postMessage('CC^CONTROL_'+controlparameters,'*');
									}else{
										window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');
									}
								} else if(controlparameters.name == "cometchat" && controlparameters.method == "chatWith") {
									/* chatWith call of Chatroom loadChatroomPro option to main CometChat. */
									var controlparameters = {"type":"modules", "name":"cometchat", "method":"chatWith", "params":{"uid":controlparameters.params.uid,"chatroommode":"0"}};
									controlparameters = JSON.stringify(controlparameters);
									window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');
								} else if(controlparameters.name == "cometchat" && (controlparameters.method == "kickChatroomUser" || controlparameters.method == "banChatroomUser")){
									/* Chatroom Kick/Ban API calls. */
										jqcc[controlparameters.name][controlparameters.method](controlparameters.params.uid,0);
								} else if(controlparameters.method == "checkChatroomPass") {
									/* Call to checkChatroomPass API of chatroom incase of password protected chatrooms. */
									jqcc[controlparameters.name][controlparameters.method](controlparameters.params.id, controlparameters.params.name, controlparameters.params.silent, controlparameters.params.password, 0, 0, controlparameters.params.encryptPass,1);
								} else if(controlparameters.method == "closeCCPopup"){
									/* Chatroom plugins closeCCPopup call. */
									closeCCPopup(controlparameters.params.name);
								} else if(controlparameters.method == "checkCometChat"){
									/* This will set checkBarEnabled=1 if CometChat bar is present with embedded chatroom. */
									jqcc.cometchat.setChatroomVars('checkBarEnabled',controlparameters.params.enabled);
								} else if(controlparameters.type == "module" && controlparameters.name == "chatrooms" && controlparameters.method == "resizeCCPopup") {
									/* ResizeCCPopup call for all popups in Chatrooms */
									window[controlparameters.method](controlparameters.params.id, controlparameters.params.height, controlparameters.params.width);
								} else if(controlparameters.type == "themes" && controlparameters.method == "loggedout") {
									/* Run chatroom heartbeat after logout from Social Login. This will also Logout user from Chatrooms. */
									jqcc.cometchat.chatroomHeartbeat();
								} else if(controlparameters.name == "core") {
									loadCCPopup(controlparameters.params.url, controlparameters.params.name, controlparameters.params.properties, controlparameters.params.width, controlparameters.params.height, controlparameters.params.title, controlparameters.params.force, controlparameters.params.allowmaximize, controlparameters.params.allowresize, controlparameters.params.allowpopout, controlparameters.params.windowMode);
								} else {
									/* All remaining calls of Chatrooms API's. */
									jqcc[controlparameters.name][controlparameters.method](controlparameters.params);
								}
							}
						}
					},false);
				break;
			}
		break;
		case "plugin":
			switch($name){
				case "chathistory":
					eventer(messageEvent,function(e) {
						if(typeof(e.data)!= 'undefined' && e.data.processcontrolmessageResponse == 1){
							/* This will append Processed messages on Chathistory plugin popup. */
							jqcc("#"+e.data.item.id).find('.chatmessage.chatmessage_short').html(e.data.message);
							jqcc("#"+e.data.item.id).find('.chatmessage.chatnowrap').html(e.data.message);
						}
					},false);
				break;
			}
		break;
		default:
			eventer(messageEvent,function(e) {
				if(typeof(e.data) != 'undefined' && typeof(e.data) == 'string'){
					if(e.data.indexOf('ccmobile_reinitializeauth')!== -1){
						jqcc.ccmobiletab.reinitialize();
					}else if(e.data.indexOf('cc_reinitializeauth')!== -1){
						if(typeof(jqcc.cometchat.ping) != 'undefined') {
							jqcc.cometchat.reinitialize();
							jqcc('#cometchat_userstab').click();
							jqcc('#cometchat_auth_popup').removeClass('cometchat_tabopen');
							jqcc('#cometchat_optionsbutton').removeClass('cometchat_tabclick');
							if(jqcc('#cometchat_trayicon_chatrooms_iframe').length > 0){
								jqcc('#cometchat_trayicon_chatrooms_iframe').attr('src', jqcc('#cometchat_trayicon_chatrooms_iframe').attr('src'));
							}
						}
						if(jqcc('#cometchat_chatrooms_iframe').length > 0){
							jqcc('#cometchat_chatrooms_iframe').attr('src', jqcc('#cometchat_chatrooms_iframe').attr('src'));
						}
					}else if(e.data.indexOf('alert')!== -1 && e.data.indexOf('CC^CONTROL_') === -1){
						if(typeof(e.data.split('^')[1]) != 'undefined'){
							alert(e.data.split('^')[1]);
						}
					}else if(e.data.indexOf('webrtcNoti')!== -1){
						if(typeof(e.data.split('^')[1]) != 'undefined' && e.data.split('^')[1] == 'add'){
								jqcc(document).find('body').prepend('<div id="webrtcArrow" onclick="this.remove();" style="position:fixed;width:100%;height: 100%;margin: 0px;top: 0;left: 0;background: rgba(0,0,0,0.6);z-index: 90000000;text-align: center;"></div>');
						}
						if(typeof(e.data.split('^')[1]) != 'undefined' && e.data.split('^')[1] == 'remove'){
							jqcc(document).find("#webrtcArrow").remove();
						}
					}else if(e.data.indexOf('CC^CONTROL_')!== -1){
						var controlparameters = e.data.slice(11);
						controlparameters = JSON.parse(controlparameters);
						if(controlparameters.type == "extensions" && controlparameters.name == "jabber" && controlparameters.method == "login_gtalk"){
							if(jqcc('#cometchat_synergy_iframe').length > 0){
								jqcc('#cometchat_synergy_iframe').attr('src', jqcc('#cometchat_synergy_iframe').attr('src'));
							}
						} else if(controlparameters.type == "extensions" && controlparameters.name == "desktop" && controlparameters.method == "login"){
							if(typeof(localStorage)!="undefined"){
								localStorage.dm_id=controlparameters.params.dm_id;
							}
						} else if(controlparameters.type == "extensions" && controlparameters.name == "desktop" && controlparameters.method == "guest_login"){
							if(typeof(localStorage)!="undefined"){
								localStorage.guest_id=controlparameters.params.guest_id;
							}
						} else if(controlparameters.type == "core" && controlparameters.name == "cometchat" && controlparameters.method == "customlogout"){
							if(typeof(jqcc.cometchat.customlogout) == 'function') {
								jqcc.cometchat.customlogout();
							}
						}
						else if(controlparameters.type == "extensions" && controlparameters.name == "desktop" && controlparameters.method == "forgot_pass"){
							//Forgot Password link in DM
							gui.Shell.openExternal(controlparameters.params.forgot_url);
						}
						else if(controlparameters.type == "extensions" && controlparameters.name == "desktop" && controlparameters.method == "signup"){
							//Sign Up link in DM
							gui.Shell.openExternal(controlparameters.params.signup_url);
						}else if(controlparameters.type == "extensions" && controlparameters.name == "desktop" && controlparameters.method == "notification"){
							//notifications in DM
							if (Notification.permission !== "granted"){
								Notification.requestPermission();
							}
							else{
								var notification = new Notification(controlparameters.params.title + " " + controlparameters.params.uname, {icon: controlparameters.params.icon, body: controlparameters.params.message });
							}
						}else if(controlparameters.type == "extensions" && controlparameters.name == "desktop" && controlparameters.method == "logout"){  localStorage.dm_id=0;
							localStorage.guest_id=0;
						}else if(controlparameters.type == "extensions" && controlparameters.name == "mobilewebapp" && controlparameters.method == "clearTimeout"){
							clearTimeout(controlparameters.params.timeOut);
							mobiletabwindow.focus();
						} else if(controlparameters.type == "module" && controlparameters.name == "chatrooms" && controlparameters.method == "resizeCCPopup") {
							/* resizeCCPopup call for all CometChat popups */
							window[controlparameters.method](controlparameters.params.id, controlparameters.params.height, controlparameters.params.width);
						} else if(controlparameters.method == "closeCCPopup"){
							/* closeCCPopup call for all CometChat popups */
							closeCCPopup(controlparameters.params.name,controlparameters.params.roomid);
						}else if(controlparameters.method == "closeChatboxCCPopup"){
							/* closeCCPopup call for all CometChat popups */
							closeChatboxCCPopup(controlparameters.params.id,controlparameters.params.chatroommode);
						}
						 else if(controlparameters.type == "plugins" && controlparameters.name == "cometchat" && controlparameters.method == "processcontrolmessage"){
							/* call to Chathistory processControlMessage function. */
							var message = jqcc[controlparameters.name][controlparameters.method](controlparameters.item);
							/* Processed messages will be sent back to ChatHistory plugin window. */
							var returnparameters = {"message":message, "item":controlparameters.item, "processcontrolmessageResponse":1};
							e.source.postMessage(returnparameters,'*');
						} else if(controlparameters.type == "plugins" && controlparameters.name == "cometchat" && controlparameters.method == "setInternalVariable"){
							if(typeof(jqcc.cometchat)!='undefined')
							/* CometChat setInternalVariable call to set A/V chat, Broadcast plugins variables. */
							jqcc[controlparameters.name][controlparameters.method](controlparameters.params.type+'_'+controlparameters.params.grp,controlparameters.params.value);
						} else if(controlparameters.type == "modules" && controlparameters.name == "cometchat" && controlparameters.method == "addMessage") {
							/* Broadcast message module addMessage API call. */
							if(controlparameters.params.caller == "" || typeof(controlparameters.params.caller)=="undefined"){
								jqcc[controlparameters.name][controlparameters.method](controlparameters.params.from, controlparameters.params.message, controlparameters.params.messageId, controlparameters.params.nopopup);
							}else{
								var returnparameters = {"type":"modules", "name":"cometchat", "method":"addMessage", "params":{"from":parseInt(controlparameters.params.from), "message":controlparameters.params.message, "messageId":controlparameters.params.messageId, "nopopup":controlparameters.params.nopopup}};
								returnparameters = JSON.stringify(returnparameters);
								jqcc("#"+controlparameters.params.caller)[0].contentWindow.postMessage('CC^CONTROL_'+returnparameters,'*');
							}
						}  else if(controlparameters.type == "modules" && controlparameters.name == "cometchat" && controlparameters.method == "updateOfflinemessages") {
							var localid;
							var returnparameters = {};
							jqcc.each(controlparameters.params.ids,function(key,value) {
								localid = jqcc[controlparameters.name][controlparameters.method]({
									"id": value,
									"message":controlparameters.params.message
								});
								returnparameters[localid] = {'id':value};
							});
							returnparameters['message'] = controlparameters.params.message;
							returnparameters = JSON.stringify(returnparameters);
							e.source.postMessage('CC^CONTROL_'+returnparameters,'*');

						} else if(controlparameters.type == "modules" && controlparameters.name == "cometchat" && controlparameters.method == "deleteOfflinemessages") {
							var offlinemessages = jqcc.cometchat.getFromStorage('offlinemessagesqueue');
							if(offlinemessages.hasOwnProperty(controlparameters.params.localmessageid)) {
								delete offlinemessages[controlparameters.params.localmessageid];
								jqcc.cometchat.updateToStorage('offlinemessagesqueue',offlinemessages);
							}
							if(jqcc.isEmptyObject(jqcc.cometchat.getFromStorage('offlinemessagesqueue'))) {
								jqcc.cometchat.updateToStorage('offmsgcounter',{'lmid':0});
							}
						} else if(controlparameters.type == "modules" && controlparameters.name == "share") {
							/* setTitle API call of Transliterate plugin and Share Module. */
							if(controlparameters.method == "setTitle") {
								var parenttitle = document.title;
								var parenturl = document.location.href;
								var addthis_share =
								{
									url:parenturl,
									title:parenttitle,
									templates: {
										twitter: '{{title}}: {{url}}'
									}
								}
							} else if(controlparameters.method == "getParentURL") {
								var theUrl = window.location.href;
								var title = document.title;
								var returnparameters = {"theUrl":theUrl, "title": title};
								returnparameters = JSON.stringify(returnparameters);
								e.source.postMessage('CC^CONTROL_'+returnparameters,'*');
							}
						} else if(controlparameters.type == "modules" && controlparameters.method == "closeModule") {
							/* closeModule calls for Theme Changer and Translate Page modules. */
							if(controlparameters.name == "themechanger"){
								location.reload();
							} else if(controlparameters.name == "translate") {
								jqcc('#MSTTExitLink').click();
							}
							jqcc.cometchat.closeModule(controlparameters.name);
						} else if(controlparameters.type == "modules" && controlparameters.name == "translatepage") {
							/* Translate Page module function calls */
							if(typeof(controlparameters.params.lang) == 'undefined'){
								/* Call to addLanguageCode function */
								window[controlparameters.method]();
							} else {
								/* Call to changeLanguage function */
								window[controlparameters.method](controlparameters.params.lang);
							}
						} else if(controlparameters.type == "modules" && controlparameters.name == "realtimetranslate" && controlparameters.method == "setCookie") {
							/* realtimetranslate module calls */
							if(typeof(controlparameters.params.lang) != 'undefined'){
								document.cookie=controlparameters.params.name+'='+controlparameters.params.lang;
							}
						} else if(controlparameters.method == "checkChatroomPass") {
							/* Call to checkChatroomPass API of chatroom incase of password protected chatrooms for Synergy theme. */
							if((typeof(controlparameters.params.noBar) != 'undefined' && controlparameters.params.noBar == 1) || typeof(jqcc[controlparameters.name][controlparameters.method]) == 'undefined'){
								var returnparameters = {"type":controlparameters.type, "name":controlparameters.name, "method":controlparameters.method, "params":controlparameters.params};
					   			returnparameters = JSON.stringify(returnparameters);
								jqcc('#cometchat_trayicon_chatrooms_iframe,.cometchat_chatrooms_iframe,.cometchat_synergy_iframe')[0].contentWindow.postMessage('CC^CONTROL_'+returnparameters,'*');
							} else {
								jqcc[controlparameters.name][controlparameters.method](controlparameters.params.id, controlparameters.params.name, controlparameters.params.silent, controlparameters.params.password,controlparameters.params.clicked,0,controlparameters.params.encryptPass,1);
							}
						} else if(controlparameters.method == "previewCometChatMedia") {
							/* previewCometChatMedia function call for Embedded layout. */
							previewCometChatMedia(controlparameters.params, controlparameters.src);
						} else if(controlparameters.name == "core") {
							/* LoadCCPopup function call */
							/* LoadCCPopup function call for Synergy theme. */
							loadCCPopup(controlparameters.params.url, controlparameters.params.name, controlparameters.params.properties, controlparameters.params.width, controlparameters.params.height, controlparameters.params.title, controlparameters.params.force, controlparameters.params.allowmaximize, controlparameters.params.allowresize, controlparameters.params.allowpopout, controlparameters.params.windowMode);
						} else if(controlparameters.type == "modules" && controlparameters.name == "cometchat" && controlparameters.method == "lightbox") {
							/* jqcc.cometchat.lightbox API call in Embedded Synergy theme for all Modules. */
							if(typeof(controlparameters.params.caller)=="undefined"){
								jqcc[controlparameters.name][controlparameters.method](controlparameters.params.moduleName);
							}else{
								jqcc[controlparameters.name][controlparameters.method](controlparameters.params.moduleName,controlparameters.params.caller);
							}
						} else if(controlparameters.name == "cometchat" && typeof(controlparameters.params.allowed) == 'undefined') {
								/* controlparameters.params.allowed is used for Kick/Ban chatroom calls. */
								if(controlparameters.method == "sendMessage"){
									jqcc[controlparameters.name][controlparameters.method](controlparameters.params.uid,controlparameters.params.message);
								} else if(controlparameters.method == "launchModule" || controlparameters.method == "minimizeAll"){
		  							jqcc[controlparameters.name][controlparameters.method](controlparameters.params.uid);
		  						} else if(controlparameters.method == "startGuestChat"){
									jqcc[controlparameters.name][controlparameters.method](controlparameters.params.name);
								} else if(controlparameters.method == "chatWith" || controlparameters.method == "chatWithUID"){
									/* controlparameters.params.allowed is used for Kick/Ban chatroom calls. */
									/* ChatWith (Private Chat) Call in Chatrooms */
									if(typeof(jqcc.cometchat) == 'undefined'){
										/* Incase of Embedded chatrooms with CometChat disabled return post message will be sent to chatroom window with extra parameter i.e; enabled=0. */
										var returnparameters = {"type":"modules", "name":"cometchat", "method":"checkCometChat", "params":{"enabled":"0"}};
					   					returnparameters = JSON.stringify(returnparameters);
					   					e.source.postMessage('CC^CONTROL_'+returnparameters,'*');
									} else if(typeof(controlparameters.params.caller) != "undefined" && controlparameters.params.caller != '') {
											var returnparameters = {"type":"modules", "name":"cometchat", "method":"chatWith", "params":{"uid":controlparameters.params.uid, "chatroommode":"0", "caller":""}};
											returnparameters = JSON.stringify(returnparameters);
											jqcc('#cometchat_synergy_iframe')[0].contentWindow.postMessage('CC^CONTROL_'+returnparameters,'*');
									} else {
										/* Incase of Embedded chatrooms with CometChat disabled return post message will be sent to chatroom window with extra parameter i.e; enabled=1. */
										if(typeof(controlparameters.params.synergy) == 'undefined' && typeof(controlparameters.params.caller) == 'undefined'){
											var returnparameters = {"type":"modules", "name":"cometchat", "method":"checkCometChat", "params":{"enabled":"1"}};
						   					returnparameters = JSON.stringify(returnparameters);
						   					e.source.postMessage('CC^CONTROL_'+returnparameters,'*');
					   					}
					   					if(typeof(jqcc[controlparameters.name][controlparameters.method])!="undefined")
					   					/* Call to ChatWith Function is CometChat bar is enabled. */
										jqcc[controlparameters.name][controlparameters.method](controlparameters.params.uid);
									}
								} else {
									/* LoadCCPopup calls for Chatrooms. */
									if(typeof(jqcc.cometchat) == 'undefined' || typeof(controlparameters.params.windowMode) != "undefined"){
										if(typeof(controlparameters.params.synergy) != "undefined"){
											/* Incase of Embedded Synergy without CometChat Bar, it will send postmessage to synergy iFrame with windowMode=1 to open loadChatroomPro in windowMode.*/
											/* This is handled in below else block.*/
											var returnparameters = {"type":"modules", "name":"cometchat", "method":"unbanChatroomUser", "params":{"url":controlparameters.params.url, "action":controlparameters.params.action, "lang":controlparameters.params.lang, "windowMode":1}};
											returnparameters = JSON.stringify(returnparameters);
											if(typeof(jqcc('#cometchat_synergy_iframe, #cometchat_chatrooms_iframe')[0]) != "undefined") {
												jqcc('#cometchat_synergy_iframe, #cometchat_chatrooms_iframe')[0].contentWindow.postMessage('CC^CONTROL_'+returnparameters,'*');
											}
										} else {
											/* Above postMessage Call to synergy theme to open loadChatroomPro in window mode. */
											controlparameters.params.url = controlparameters.params.url+'&noBar=1';
											loadCCPopup(controlparameters.params.url, controlparameters.params.action,"status=0,toolbar=0,menubar=0,directories=0,resizable=0,location=0,status=0,scrollbars=1, width=400,height=200",400,200,controlparameters.params.lang,null,null,null,null,1);
										}
									} else {
										/* LoadCCPopup call of Chatrooms with CometChat bar enabled. */
										loadCCPopup(controlparameters.params.url, controlparameters.params.action,"status=0,toolbar=0,menubar=0,directories=0,resizable=0,location=0,status=0,scrollbars=1, width=400,height=200",400,200,controlparameters.params.lang);
									}
								}
						}else if(controlparameters.type == "core" && controlparameters.name == "libraries"){
								if(controlparameters.method == 'incomingCall'){
									incomingCall(controlparameters.params.incoming, controlparameters.params.avchat_data, controlparameters.params.currenttime,controlparameters.params.userdata);
								}
								if(controlparameters.method == 'removeCallContainer'){
									removeCallContainer(controlparameters.params.id);
								}
								if(controlparameters.method == 'outgoingCall'){
									outgoingCall(controlparameters.params.id, controlparameters.params.grp,controlparameters.params.userdata, controlparameters.params.calltype);
								}
								if(controlparameters.method == 'toggleBotsAction'){
									toggleBotsAction(controlparameters.params);
								}
								if(controlparameters.method == 'showBotlist'){
									showBotlist();
								}
						} else if(controlparameters.type == "functions" && controlparameters.name == "socialauth") {
							/* Social Login call for Embedded Chatroom */
							if(jqcc('#cometchat_optionsbutton').length == 1){
								/* If CometChat bar is present, Social auth login popup of the bar will be opened. */
								jqcc('#cometchat_optionsbutton').click();
							} else if (jqcc('.cometchat_optionsimages_ccauth').length == 1) {
								jqcc('.cometchat_optionsimages_ccauth').click();
							} else {
								/* If CometChat bar is not present, Social Login popup will be opened in Window Mode to login to Embedded chatroom. */
								loadCCPopup(controlparameters.params.url, controlparameters.name,"status=0,toolbar=0,menubar=0,directories=0,resizable=0,location=0,status=0,scrollbars=0, width=420,height=250",300,200,jqcc.cometchat.getLanguage('login_options'),null,null,null,null,1);
							}
						} else if(controlparameters.name == "cometchat" && (controlparameters.method == "kickChatroomUser" || controlparameters.method == "banChatroomUser")){
							/* Chatroom Kick/Ban users call. */
							if(typeof(jqcc[controlparameters.name])=="undefined" || typeof(jqcc[controlparameters.name][controlparameters.method])=="undefined"){
								/* In case of embedded chatroom with CometChat bar disabled, a return post message will be sent to Chatroom iFrame which will call the API. */
								var returnparameters = {"type":controlparameters.type, "name":controlparameters.name, "method":controlparameters.method, "params":controlparameters.params};
								returnparameters = JSON.stringify(returnparameters);
								jqcc('#cometchat_chatrooms_iframe, #cometchat_trayicon_chatrooms_iframe, #cometchat_synergy_iframe')[0].contentWindow.postMessage('CC^CONTROL_'+returnparameters,'*');
							}else{
								/* Direct Kick/Ban API call for Synergy theme. */
								jqcc[controlparameters.name][controlparameters.method](controlparameters.params.uid,1);
							}
						} else if(controlparameters.type == "themes" && controlparameters.method == "loggedout") {
							/* Logout from Embedded Chatroom incase of Social Login. */
							if(typeof(jqcc.cometchat)!='undefined'){
								/* If CometChat bar is not enabled, it will only logout from embedded chatroom iFrame. */
								jqcc[jqcc.cometchat.getSettings().theme].loggedOut();
								jqcc.cometchat.setThemeVariable('loggedout', 1);
								clearTimeout(jqcc.cometchat.getCcvariable().heartbeatTimer);
							} else {
								/* If CometChat bar is enabled, it will send postMessage to Chatroom as Chatroom methods are not present in main CometChat. */
								var returnparameters = {"type":controlparameters.type, "name":controlparameters.name, "method":controlparameters.method, "params":controlparameters.params};
					   			returnparameters = JSON.stringify(returnparameters);
								e.source.postMessage('CC^CONTROL_'+returnparameters,'*');
							}
						} else {
							if(controlparameters.params.chatroommode == 1 && controlparameters.method != "init" && typeof(jqcc.cometchat) != 'undefined' && jqcc.cometchat.getSettings().theme != 'synergy' && jqcc.cometchat.getSettings().theme != 'embedded' && jqcc.cometchat.getSettings().theme != 'docked'){
								/* All themes chatroom calls except init calls and CometChat bar is enabled.*/
								if(controlparameters.method == "addtext" && typeof(controlparameters.params.caller) != "undefined" && controlparameters.params.caller != ""){
									/* If Smilies init is opened from Synergy theme chatrooms. Then selected smiley will be added in Synergy chatroom text area. */
					   				var returnparameters = {"type":controlparameters.type, "name":controlparameters.name, "method":controlparameters.method, "params":controlparameters.params};
					   				var caller = returnparameters.params.caller;
					   				delete returnparameters.params.caller;
					   				returnparameters = JSON.stringify(returnparameters);
					   				jqcc('#'+caller+', #cometchat_chatrooms_iframe')[0].contentWindow.postMessage('CC^CONTROL_'+returnparameters,'*');
					   			} else if(typeof(jqcc('#cometchat_trayicon_chatrooms_iframe, #cometchat_chatrooms_iframe, #cometchat_synergy_iframe')[0].contentWindow) != 'undefined'){
									jqcc('#cometchat_trayicon_chatrooms_iframe, #cometchat_chatrooms_iframe, #cometchat_synergy_iframe')[0].contentWindow.postMessage(e.data,'*');
								}
						   	} else {
						   		if(typeof(jqcc[controlparameters.name]) == 'undefined'){
						   			/* If CometChat bar is disabled it will ask Chatroom to open Popups in window mode.*/
						   			if(controlparameters.name != 'mobilewebapp'){
							   			var returnparameters = {"type":"plugins", "name":controlparameters.name, "method":controlparameters.method, "params":controlparameters.params};
							   			returnparameters.params.windowMode = "1";
										returnparameters = JSON.stringify(returnparameters);
										e.source.postMessage('CC^CONTROL_'+returnparameters,'*');
									}
						   		} else {
						   			/* All direct API calls of CometChat.*/
						   			if((controlparameters.method == "addtext" || controlparameters.method == "appendMessage" || controlparameters.method == "appendStickerMessage") && typeof(controlparameters.params.caller) != "undefined" && controlparameters.params.caller != ""){
						   				/* If Smilies/Stickers init is opened from Synergy theme. Then selected smiley will be added in Synergy chatroom text area. */
						   				var returnparameters = {"type":controlparameters.type, "name":controlparameters.name, "method":controlparameters.method, "params":controlparameters.params};
						   				var caller = returnparameters.params.caller;
						   				delete returnparameters.params.caller;
						   				returnparameters = JSON.stringify(returnparameters);
						   				if(typeof(jqcc('#'+caller)[0]) == 'undefined'){
						   					jqcc[controlparameters.name][controlparameters.method](controlparameters.params);
						   				}else{
						   					jqcc('#'+caller)[0].contentWindow.postMessage('CC^CONTROL_'+returnparameters,'*');
						   				}
									} else {
										if(window.top != window.self){
											controlparameters.params.windowMode = "1";
										}
						   				jqcc[controlparameters.name][controlparameters.method](controlparameters.params);
						   			}
						   		}
							}
						}
					}
				}
			},false);
		break;
	}
}

<?php if (($lightboxWindows == 1) || ($cbfn=='desktop')): ?>

var cc_dragobj = new Object();

function loadCCPopup(url,name,properties,width,height,title,force,allowmaximize,allowresize,allowpopout,windowmode) {
	if(typeof url == "undefined" || typeof name == "undefined") return;
	url += url.indexOf('?')<0?'?':'&'+'embed=web';
	if(url.indexOf('basedata') < 0){
		var basedata = '';
		if(typeof(jqcc.cometchat) != 'undefined' && typeof(jqcc.cometchat.getBaseData) != 'undefined'){
			basedata = jqcc.cometchat.getBaseData();
		}
		url += '&basedata='+basedata;
	}
	var mobileDevice = navigator.userAgent.match(/ipad|ipod|iphone|android|windows ce|Windows Phone|blackberry|palm|symbian/i);
	if(typeof(windowmode) != "undefined" && windowmode == 1) {
		url += '&popoutmode=1';
		if(typeof(jqcc.cometchat)!="undefined" && typeof(jqcc.cometchat.getCcvariable) != "undefined" && jqcc.cometchat.getCcvariable().callbackfn=='desktop'){
			var b=properties.split(',');
			var i;
			var nw=0;
			var nh=0;
			for(i=0;i<b.length;i++){
				if(b[i].indexOf('height')!=-1){
				  var h=b[i].split('=');
				  nh=h[1];
				}
				if(b[i].indexOf('width')!=-1){
				  var w=b[i].split('=');
				  nw=w[1];
				}
			}
			nh=parseInt(nh)+30;//For Desktop Messenger, module &plugin height issue
			nw=parseInt(nw)+15;//For Desktop Messenger, module &plugin width issue
			cc_windownames['cc_'+name] = window.open(url,'cc_'+name,properties);
			if(typeof(cc_windownames['cc_'+name]) != null && typeof(cc_windownames['cc_'+name]) != 'undefined'){
				cc_windownames['cc_'+name].document.title='cc_'+name;
				cc_windownames['cc_'+name].resizeTo(nw,nh);
				cc_windownames['cc_'+name].focus();
			}else{
				delete(cc_windownames['cc_'+name]);
				alert("Please allow browser pop ups for "+location.origin);
			}
		}else{
			cc_windownames['cc_'+name] = window.open(url,'cc_'+name,properties);
			if(typeof(cc_windownames['cc_'+name]) != null && typeof(cc_windownames['cc_'+name]) != 'undefined'){
				cc_windownames['cc_'+name].focus();
			}else{
				delete(cc_windownames['cc_'+name]);
				alert("Please allow browser pop ups for "+location.origin);
			}
		}
	} else {
		var dragcss = 'onmousedown="dragStart(event, \'cometchat_container_'+name+'\')"';
		var dividerhtml = '';
		theme = jqcc.cometchat.getSettings().theme;
		url += '&cc_layout='+theme;
		if (jqcc('#cometchat_container_'+name).length > 0) {
			alert(jqcc.cometchat.getLanguage('close_existing_popup'));

			setTimeout(function() {
				cc_zindex += 1;
				jqcc('#cometchat_container_'+name).css('z-index',1000000+cc_zindex);
			}, 100);
			return;
		}
		var top = ((jqcc(window).height() - height) / 2) ;
		var bottom = top;
		var left = ((jqcc(window).width() - width) / 2) + jqcc(window).scrollLeft();

		if (top < 0) { top = 0; }
		if (left < 0) { left = 0; }

		top = 'top:'+top+'px;';
		left = 'left:'+left+'px;';

		if(mobileDevice){
			widthMinBorder = jqcc(window).width()+'px';
		}else{
			widthMinBorder = (width-2)+'px;';
		}

		if (jqcc(document).fullScreen() !== null && allowmaximize == 1 && window.top == window.self) {
			displaymaxicon='style="display:inline-block;"';
		} else {
			displaymaxicon='style="display:none;"';
		}

		if (allowpopout == 1) {
			displaypopicon='style="display:inline-block;"';
		} else {
			displaypopicon='style="display:none;"';
		}
		if(allowmaximize == 1 || allowpopout == 1){
			dividerhtml = '<div class="cometchat_vline"></div>';
		}
		var windowtype = '';
		var cometchat_windows_class = '';
		var borderstyle = '';
		var position = 'position:fixed;';
		var rtl = "<?php echo $rtl; ?>";
		if(theme == 'embedded'){
			position = 'position:absolute;';
			if(rtl == 1){
				borderstyle = ' border-right:1px solid #D1D1D1; ';
				left = 'right:100%;';
			}else{
				borderstyle = ' border-left:1px solid #D1D1D1; ';
				left = 'left:100%;';
			}
			height = jqcc(window).height() - jqcc('#cometchat_header').height() - 40;
			windowtype = 'cometchat_left_container_title';
			if(jqcc('#cometchat_righttab').css('top') == "0px" || jqcc('#cometchat_header').length != 1){
				top = 'top:0px;';
				height = jqcc(window).height() - 40;
			}else{
				top = 'top:73px;';
				height = jqcc(window).height() - jqcc('#cometchat_header').height() - 40;
			}

			if(name == 'blocks' ){
				cometchat_windows_class = 'cometchat_windows';
				width = '300';
				top = 'top: 0px;';
				var leftpos = jqcc(window).width()-302;
				if(rtl == 1){
					left = 'right:100%;';
				}else{
					left = 'left:'+leftpos.toString()+'px;';
				}
				height = jqcc(window).height();
			}else{
				width = '500';
			}
			if(name == 'passwordBox'){
				height = 110;
				width = 322;
				var centerleft = (jqcc(window).width()/2) - (width/2);
				var centertop = (jqcc(window).height()/2) - (height/2);
				left = 'left:'+centerleft+'px;';
				top = 'top:'+centertop+'px;';
			}else{
				dragcss = '';
			}
		}

		jqcc("body").append('<div id="cometchat_container_'+name+'" class="cometchat_container '+cometchat_windows_class+'" style="'+top+left+'width:'+width+'px;'+position+'"><div class="cometchat_container_title '+windowtype+'"  '+dragcss+'><span class="cometchat_container_name">'+title+'</span><div class="cometchat_closebox cometchat_tooltip" title="'+jqcc.cometchat.getLanguage('close_popup')+'" id="cometchat_closebox_'+name+'" style="font-weight: normal;"></div>'+dividerhtml+'<div '+displaymaxicon+' class="cometchat_maxwindow cometchat_tooltip" title="Maximize Popup" id="cometchat_maxwindow_'+name+'"></div><div '+displaypopicon+' class="cometchat_popwindow cometchat_tooltip" title="Popout" id="cometchat_popwindow_'+name+'"></div><div style="clear:both"></div></div><div class="cometchat_container_body" style="'+borderstyle+'height:'+(height)+'px;"><div class="cometchat_loading"></div><iframe class="cometchat_iframe" id="cometchat_trayicon_'+name+'_iframe" width="100%" height="'+(height)+'"  allowtransparency="true" frameborder="0"  scrolling="no" src="'+url+'" allowfullscreen="true" webkitallowfullscreen="true" mozallowfullscreen="true" allow="geolocation; microphone; camera; midi; encrypted-media;"></iframe><div class="cometchat_overlay" style="height:'+(height)+'px;"></div><div style="clear:both"></div></div></div>');
		var cometchat_container = jqcc('#cometchat_container_'+name);
		var left = cometchat_container.offset().left;
		var animatewidth;
		if(theme == 'embedded'){
			jqcc('#cometchat_container_'+name).css('width',width);
			if(jqcc('.cometchat_windows').hasClass('visible')){
				jqcc('.cometchat_container').each(function(){
					if(jqcc('#'+this.id).hasClass('cometchat_windows') && jqcc('#'+this.id).hasClass('visible')){
						jqcc('#'+this.id).remove();
					}
				});
				if(rtl == 1){
					jqcc('.cometchat_windows').animate({'right':'100%'},"fast").removeClass('visible');
				}else{
					jqcc('.cometchat_windows').animate({'left':'100%'},"fast").removeClass('visible');
				}
			}
			if (cometchat_container.hasClass('visible')){
				if(rtl == 1){
					cometchat_container.animate({"left":"-300px"}, "fast").removeClass('visible');
				}else{
					cometchat_container.animate({"left":"100%"}, "fast").removeClass('visible');
				}
			}else{
				if(name == 'blocks'){
					if(rtl == 1){
						cometchat_container.css({right:'100%'}).animate({"right":jqcc(document).width()-300+'px'}, "fast").addClass('visible');
					}else{
						cometchat_container.css({left:left}).animate({"left":jqcc(document).width()-300+'px'}, "fast").addClass('visible');
					}
				}else{
					animatewidth = jqcc(window).width()-cometchat_container.width();
					var reducesize = cometchat_container.width();
					if(name != 'passwordBox'){
						if(!jqcc('.cometchat_windows').hasClass('visible') && !jqcc('.cometchat_container').hasClass('visible')){
							if(jqcc("#cometchat_righttab").width()-cometchat_container.width() <= 400 && jqcc('#cometchat_righttab').width()!=jqcc(window).width()){
								var textareasize = 200;
								if(jqcc(window).width() < 850){
									cometchat_container.width(400);
									reducesize = 400;
									textareasize = 100;
									animatewidth = jqcc(window).width() - cometchat_container.width();
								}
								if(rtl == 1){
									jqcc('#cometchat_righttab').css({'position':'absolute','right':'301px','width':jqcc('#cometchat_righttab').width()});
									jqcc('#cometchat_leftbar').css({'position':'absolute','right':'0'});
									jqcc("#cometchat_righttab").animate({'right':'-=300px','width':(jqcc(window).width()-cometchat_container.width())},500);
									jqcc("#cometchat_righttab").find(".cometchat_textarea").animate({'width':'-='+ textareasize},500);
									jqcc('#cometchat_leftbar').animate({'right':'-=300px'},500);
									cometchat_container.animate({"right":'-='+reducesize}, 500).addClass('visible');
								}else{
									jqcc('#cometchat_righttab').css({'position':'absolute','left':'301px','width':jqcc('#cometchat_righttab').width()});
									jqcc('#cometchat_leftbar').css({'position':'absolute','left':'0'});
									jqcc("#cometchat_righttab").animate({'left':'-=300px','width':(jqcc(window).width()-cometchat_container.width())},500);
									jqcc("#cometchat_righttab").find(".cometchat_textarea").animate({'width':'-='+ textareasize},500);
									jqcc('#cometchat_leftbar').animate({'left':'-=300px'},500);
									cometchat_container.css({left:left}).animate({"left":'-='+reducesize}, 500).addClass('visible');
								}
							}else if(jqcc('#cometchat_righttab').width()==jqcc(window).width()){
								animatewidth = '0';
								if(embeddedchatroomid >= 1 && jqcc(window).width() > 800){
									cometchat_container.width(jqcc(window).width()/2);
									reducesize = jqcc(window).width()/2;
									animatewidth = jqcc(window).width() - cometchat_container.width();
									jqcc("#cometchat_righttab").animate({'width':jqcc("#cometchat_righttab").width()-reducesize+'px'},"fast");
									jqcc("#cometchat_righttab").find(".cometchat_textarea").animate({'width':jqcc("#cometchat_righttab").width() - (reducesize + 140) + 'px'},"fast");
								}else{
									cometchat_container.width(jqcc(window).width());
									reducesize = jqcc(window).width();
								}
								if(rtl == 1){
									if(name == 'singleplayergame'){
										cometchat_container.css({left:"0px"});
									}else{
										cometchat_container.animate({"right":'-='+reducesize}, 500).addClass('visible');
									}
								}else{
									if(name == 'singleplayergame'){
										cometchat_container.css({left:"0px"});
									}else{
										cometchat_container.css({left:left}).animate({"left":'-='+reducesize}, 500).addClass('visible');
									}
								}
							}else{
								jqcc("#cometchat_righttab").animate({'width':'-='+reducesize},500);
								jqcc("#cometchat_righttab").find(".cometchat_textarea").animate({'width':'-='+reducesize},500);
								if(rtl == 1){
									cometchat_container.animate({"right":'-='+reducesize}, 500).addClass('visible');
								} else{
									cometchat_container.css({left:left}).animate({"left":'-='+reducesize}, 500).addClass('visible');
								}

							}
						}else if(jqcc('.cometchat_container').hasClass('visible')){
							width = jqcc('.visible').width();
							cometchat_container.width(width);
							animatewidth = jqcc(window).width() - width;
							jqcc('.cometchat_container').filter('.visible').remove().removeClass('visible');
							if(rtl == 1){
								cometchat_container.animate({"right":animatewidth}, 500).addClass('visible');
							}else{
								cometchat_container.css({left:left}).animate({"left":animatewidth}, 500).addClass('visible');
							}
						}
					}else{
						cometchat_container.find('.cometchat_container_body').css({'border-right':'1px solid #D1D1D1','border-bottom':'1px solid #D1D1D1'});
					}
				}
			}
		} else if(theme == 'docked'){

			if (cometchat_container.hasClass('visible')){
				cometchat_container.animate({"bottom":"100%"}, "fast").removeClass('visible');
			}else{
				bottom = bottom+'px';
				cometchat_container.animate({"bottom":bottom}, "fast").addClass('visible');
			}
		}
		setTimeout(function() {
			cc_zindex += 1;
			jqcc('#cometchat_container_'+name).css('z-index',10000000000+cc_zindex);
		}, 100);

		cometchat_container.find('.cometchat_closebox').click(function() {
			if(theme == 'embedded') {
				var id = this.id;
				id = id.split('_');
				id = id[2];
				setTimeout(function() {
					if(id == 'blocks'){
						if(rtl == 1){
							cometchat_container.animate({"right":"100%"}, "fast").removeClass('visible');
						}else{
							cometchat_container.animate({"left":"100%"}, "fast").removeClass('visible');
						}
					}else{
						if(rtl == 1){
							cometchat_container.animate({"right":"+="+cometchat_container.width()}, 500).removeClass('visible');
						}else{
							cometchat_container.animate({"left":"+="+cometchat_container.width()}, 500).removeClass('visible');
						}

						jqcc("#cometchat_tooltip").css('display', 'none');
						var windowwidth = cometchat_container.width();

						if(name != 'passwordBox'){
							if(jqcc("#cometchat_righttab").width()+cometchat_container.width() >= (jqcc(window).width()-2)){
								var increasesize = (jqcc(window).width() - jqcc("#cometchat_leftbar").width()) - jqcc('#cometchat_righttab').width();
								if(embeddedchatroomid > 0){
									jqcc("#cometchat_righttab").find(".cometchat_textarea").animate({'width':jqcc(document).width() - 140 + 'px'},"fast");
								}else{
									jqcc("#cometchat_righttab").find(".cometchat_textarea").animate({'width':jqcc("#cometchat_righttab").width() + 60 + 'px'},"fast");
								}
								if(rtl == 1){
									jqcc("#cometchat_righttab").animate({'right':'+='+jqcc("#cometchat_leftbar").width(),'width':'+='+increasesize},500);
									jqcc("#cometchat_leftbar").animate({'right':'+='+jqcc("#cometchat_leftbar").width()},500);
								}else{
									jqcc("#cometchat_righttab").animate({'left':'+='+jqcc("#cometchat_leftbar").width(),'width':'+='+increasesize},500);
									jqcc("#cometchat_leftbar").animate({'left':'+='+jqcc("#cometchat_leftbar").width()},500);
								}

								setTimeout(function(){
									jqcc('#cometchat_righttab').removeAttr('style');
									jqcc('#cometchat_leftbar').removeAttr('style');
								},1000);
							}else if(jqcc('#cometchat_righttab').width()==jqcc(window).width()){
								if(rtl == 1){
									cometchat_container.css({right:left}).animate({"right":'+='+jqcc(document).width()}, 500).addClass('visible');
								}else{
									cometchat_container.css({left:left}).animate({"left":'+='+jqcc(document).width()}, 500).addClass('visible');
								}
							}else{
								jqcc("#cometchat_righttab").animate({'width':'+='+windowwidth},500);
								jqcc("#cometchat_righttab").find(".cometchat_textarea").animate({'width':jqcc("#cometchat_righttab").width() +(windowwidth - 140) + 'px'},"fast");
							}
						}
					}
				},400);
			} else {
				cometchat_container.animate({"bottom":"100%"}, "fast").removeClass('visible');
				jqcc("#cometchat_tooltip").css('display', 'none');
			}

			setTimeout(function() {
				cometchat_container.remove();
			},1000);
			window.onbeforeunload = null;
		});

		if (jqcc(document).fullScreen() !== null && allowmaximize ==1) {
			cometchat_container.find('.cometchat_iframe').addClass('cometchat_iframe_'+name);
				cometchat_container.find('.cometchat_maxwindow').click(function() {
					if(window.top == window.self){
						jqcc('.cometchat_iframe_'+name).toggleFullScreen(true);
					}else{
						jqcc('.cometchat_iframe').contents().find('.cometchat_iframe_'+name).toggleFullScreen(true);
					}
				if (name =='whiteboard') {
					jqcc('#cometchat_container_whiteboard').find('.cometchat_iframe').contents().find('#whiteboard').width(screen.width);
					jqcc('#cometchat_container_whiteboard').find('.cometchat_iframe').contents().find('#whiteboard').height(screen.height);
				}
				jqcc("#cometchat_tooltip").css('display', 'none');
			});
		}

		if (allowpopout == 1) {
			cometchat_container.find('.cometchat_popwindow').click(function() {
				var title = cometchat_container.find('.cometchat_container_name').text();
				var calculatedwidth = parseInt(jqcc("#cometchat_righttab").width())+parseInt(width);
				if(jqcc('#cometchat_righttab').width()!=jqcc(window).width()){
					if(jqcc('#cometchat_leftbar').css('left') != 'auto'){
						jqcc('#cometchat_righttab').removeAttr('style');
						jqcc('#cometchat_leftbar').removeAttr('style');
						jqcc("#cometchat_righttab").find(".cometchat_textarea").animate({'width':jqcc("#cometchat_righttab").width() - 140 + 'px'},"fast");
						jqcc('#cometchat_leftbar').animate({'left':'0px'},"fast");
					}else{
						jqcc("#cometchat_righttab").animate({'width':calculatedwidth},"fast");
						jqcc("#cometchat_righttab").find(".cometchat_textarea").animate({'width':jqcc("#cometchat_righttab").width() +(width - 140) + 'px'},"fast");
					}
				}
				jqcc.cometchat.setInternalVariable('avchatpopoutcalled','1');
				cometchat_container.remove();
				setTimeout(function(){
					loadCCPopup(url,name,'width='+width+',height='+height+' scrollbars=yes, resizable=yes',width,height,title,force,0,0,0,1);
				}, 1000);
				jqcc("#cometchat_tooltip").css('display', 'none');
			});
		}

		cometchat_container.click(function() {
			cc_zindex += 1;
			jqcc(this).css('z-index',10000000000+cc_zindex);
		});
	}
}

var closeCCPopup = closeCCPopup || function (name,roomid) {
	var theme = jqcc.cometchat.getSettings().theme;
	if(theme == 'docked') {
		jqcc('#cometchat_group_'+roomid+'_popup').find('.cometchat_backbutton_viewgroupuserspopup').click();
		jqcc('#cometchat_container_'+name).animate({"bottom":"100%"}, "fast").removeClass('visible');
	} else {
		jqcc('#cometchat_container_'+name).animate({"left":"100%"}, "fast").removeClass('visible');
		jqcc('#cometchat_container_'+name).find('.cometchat_closebox').click();
	}
	jqcc("#cometchat_tooltip").css('display', 'none');

	setTimeout(function() {
		if(jqcc('#cometchat_container_'+name).length >0){
			jqcc('#cometchat_container_'+name).remove();
		}
		if(typeof(cc_windownames['cc_'+name]) != null && typeof(cc_windownames['cc_'+name]) != 'undefined'){
			cc_windownames['cc_'+name].close();
		}
	},500);
}

function resizeCCPopup(id,width,height) {
	jqcc('#cometchat_container_'+id).css('width',width+2+'px').find('.cometchat_container_body').css({'height':height, 'width':width});
	jqcc('#cometchat_container_'+id).find('.cometchat_iframe').attr({'height':height, 'width':width});
}

function getID(id) { return document.getElementById(id); }

function dragStart(a,b){
	cc_zindex += 1;jqcc('#'+b).css('z-index',10000000000+cc_zindex);
	jqcc('#'+b).find('.cometchat_overlay').css('display','block');var x,y;cc_dragobj.elNode=getID(b);try{x=window.event.clientX+document.documentElement.scrollLeft+document.body.scrollLeft;y=window.event.clientY+document.documentElement.scrollTop+document.body.scrollTop}catch(e){x=a.clientX+window.scrollX;y=a.clientY+window.scrollY}cc_dragobj.cursorStartX=x;cc_dragobj.cursorStartY=y;cc_dragobj.elStartLeft=parseInt(cc_dragobj.elNode.style.left,10);cc_dragobj.elStartTop=parseInt(cc_dragobj.elNode.style.top,10);if(isNaN(cc_dragobj.elStartLeft))cc_dragobj.elStartLeft=0;if(isNaN(cc_dragobj.elStartTop))cc_dragobj.elStartTop=0;try{document.attachEvent("onmousemove",dragGo);document.attachEvent("onmouseup",dragStop);window.event.cancelBubble=true;window.event.returnValue=false}catch(e){document.addEventListener("mousemove",dragGo,true);document.addEventListener("mouseup",dragStop,true);a.preventDefault()}}

function dragGo(a){var x,y;try{x=window.event.clientX+document.documentElement.scrollLeft+document.body.scrollLeft;y=window.event.clientY+document.documentElement.scrollTop+document.body.scrollTop}catch(e){x=a.clientX+window.scrollX;y=a.clientY+window.scrollY}var b=(cc_dragobj.elStartLeft+x-cc_dragobj.cursorStartX);var c=(cc_dragobj.elStartTop+y-cc_dragobj.cursorStartY);if(b>0){cc_dragobj.elNode.style.left=b+"px"}else{cc_dragobj.elNode.style.left="1px"}if(c>0){cc_dragobj.elNode.style.top=c+"px"}else{cc_dragobj.elNode.style.top="1px"}try{window.event.cancelBubble=true;window.event.returnValue=false}catch(e){a.preventDefault()}}

function dragStop(event){jqcc('.cometchat_overlay').css('display','none');try{document.detachEvent("onmousemove",dragGo);document.detachEvent("onmouseup",dragStop)}catch(e){document.removeEventListener("mousemove",dragGo,true);document.removeEventListener("mouseup",dragStop,true)}}

<?php else:?>

function loadCCPopup(url,name,properties,width,height,title,force,allowmaximize,allowresize,allowpopout) {
	url += url.indexOf('?')<0?'?':'&'+'popoutmode=1';
	theme = jqcc.cometchat.getSettings().theme;
	url += '&cc_layout='+theme;

	if(url.indexOf('basedata') < 0){
		var basedata = '';
		if(typeof(jqcc.cometchat.ping) != 'undefined' && typeof(jqcc.cometchat.getBaseData) != 'undefined'){
			basedata = jqcc.cometchat.getBaseData();
		}
		url += '&basedata='+basedata;
	}

	var w = window.open(url,name,properties);
	if(w != null){
		w.focus();
	}
}

<?php endif;?>

function loadPopupInChatbox(url,name,fromid,toid,chatroommode) {
	if(typeof url != "undefined" && typeof name != "undefined") {
		url += url.indexOf('?')<0?'?':'&'+'basedata='+jqcc.cometchat.getBaseData()+'&embed=web';
		if(chatroommode==1){
			url += '&chatroommode=1';
		}
		theme = jqcc.cometchat.getSettings().theme;

		if(theme == 'embedded'){
			var iOSmobileDevice = navigator.userAgent.match(/ipad|ipod|iphone/i);
			var mobileDevice = navigator.userAgent.match(/ipad|ipod|iphone|android|windows ce|Windows Phone|blackberry|palm|symbian/i);
			if(url.split("?")[1].split("&").indexOf('caller=cometchat_synergy_iframe') > 0){
				theme = 'synergy';
			}
			url += '&cc_layout='+theme;
			if (jqcc('#cometchat_container_'+name).length > 0) {
				alert(jqcc.cometchat.getLanguage('close_existing_popup'));

				setTimeout(function() {
					cc_zindex += 1;
					jqcc('#cometchat_container_'+name).css('z-index',10000000000+cc_zindex);
				}, 100);
				return;
			}
			var width = jqcc('#currentroom_convo').innerWidth();
			var height = jqcc('#cometchat_righttab').find('.cometchat_tabpopup').innerHeight()/2;
			if(height == 0 || height == null || width == 0 || width == null){
				width = jqcc('#cometchat_user_'+toid+'_popup').innerWidth();
				height = jqcc('#cometchat_user_'+toid+'_popup').innerHeight() / 2;
			}
			var top = ((jqcc(window).height() - height) / 2) ;
			var left = ((jqcc(window).width() - width) / 2) + jqcc(window).scrollLeft();

			if (top < 0) { top = 0; }
			if (left < 0) { left = 0; }


			top = 'top:'+top+'px;';
			left = 'left:'+left+'px;';


			if(mobileDevice){
				widthMinBorder = jqcc(window).width()+'px';
			}else{
				widthMinBorder = (width-2)+'px;';
			}

			if(chatroommode == 1){
				var textfieldheight = jqcc('#cometchat_righttab').find('#cometchat_tabinputcontainer').outerHeight(true)+1;
				var messagecontainer = jqcc('#cometchat_righttab').find('#currentroom_convo');
			}else{
				var textfieldheight = jqcc('#cometchat_user_'+toid+'_popup').find('#cometchat_tabinputcontainer').outerHeight(true)+1;
				var messagecontainer = jqcc('#cometchat_righttab').find('#cometchat_tabcontenttext_'+toid);
			}
			var windowtype = '';
			var offset;
			if(name == 'stickers' || name == 'handwrite' || name == 'smilies' || name == 'transliterate' || name == 'voicenote'){
				top = 'top:100%;';
				offset = jqcc('#cometchat_righttab').offset();
				left = 'left:'+offset.left+'px';
				bottom = 'bottom:-200px;';
			}else{
				windowtype = 'cometchat_left_container_title';
				top = 'top:0px;';
				left = 'left:100%';
			}
			width = width - 1;  /* border overlap fix when side plugin is opened */
			jqcc("body").append('<div id="cometchat_container_'+name+'" class="cometchat_container" style="'+bottom+left+'width:'+width+'px;'+'height:200px;"><div class="cometchat_container_body" style="border-top:0.5px solid #D1D1D1;height:200px;"><div class="cometchat_loading"></div><iframe class="cometchat_iframe" id="cometchat_trayicon_'+name+'_iframe" width="100%" height="200px"  allowtransparency="true" frameborder="0"  scrolling="no" src="'+url+'" allowfullscreen="true" webkitallowfullscreen="true" mozallowfullscreen="true" allow="geolocation; microphone; camera; midi; encrypted-media;"></iframe><div class="cometchat_overlay" style="height:'+(height)+'px;"></div><div style="clear:both"></div></div></div>');
			var hidden = jqcc('#cometchat_container_'+name);
			var containerHeight = '200';
			if(jqcc('.cometchat_windows').hasClass('visible')){
				jqcc('.cometchat_windows').animate({'left':'100%'},"fast").removeClass('visible');
			}
			if(name == 'stickers' || name == 'handwrite' || name == 'smilies' || name == 'transliterate' || name == 'voicenote'){
				hidden.css('left',offset.left+'px');
				hidden.css('width',width+'px');
				if (!hidden.hasClass('visible')){
					jqcc('.cometchat_message_container').css('height',jqcc('.cometchat_message_container').height() - jqcc('.cometchat_container').height());
					hidden.css({"bottom":textfieldheight+"px",display:"none"}).addClass('visible').slideDown("slow");
					if(typeof (jqcc[theme].windowResize()) == 'function'){
						jqcc[theme].windowResize();
					}
				}
			}
			setTimeout(function() {
				cc_zindex += 1;
				jqcc('#cometchat_container_'+name).css('z-index',10000000000+cc_zindex);
			}, 100);

			var cometchat_container = jqcc('#cometchat_container_'+name);
			cometchat_container.find('.cometchat_closebox_down').click(function() {
				cometchat_container.animate({"bottom":"-200px"}, "fast").removeClass('visible');
				jqcc("#cometchat_tooltip").css('display', 'none');
				setTimeout(function() {
					cometchat_container.remove();
				},500);
				window.onbeforeunload = null;
			});

		}else{
			if(chatroommode==1){
				var popup = jqcc('#cometchat_chatboxes #cometchat_group_'+toid+'_popup');
			} else {
				var popup = jqcc('#cometchat_chatboxes #cometchat_user_'+toid+'_popup');
			}

			var width = popup.find('.cometchat_tabcontenttext').innerWidth();
			var height = popup.find('.cometchat_tabcontenttext').innerHeight() / 2;

			var bottom = 0;
			var left = 0;
			var currentopenpopup = popup.find('.cometchat_tabcontent .cometchat_chatboxpopup_'+toid).attr('pluginname');

			bottom = 'bottom:'+bottom+'px;';
			left = 'left:'+left+'px;';

			widthMinBorder = (width-2)+'px;';

			if(name=='smilies') {
				closeChatboxCCPopup(toid,chatroommode);
				height = popup.find('.cometchat_tabcontenttext').innerHeight() / 2;
			}

			if(popup.find('.cometchat_tabcontent .cometchat_chatboxpopup_'+toid).length && name!='smilies') {
				closeChatboxCCPopup(toid,chatroommode);
			} else if(currentopenpopup!=name) {
				var height1 = (popup.find('.cometchat_tabcontenttext').innerHeight()-height)+'px';
				popup.find('.cometchat_tabcontenttext').height(height1);
				if(popup.find('.cometchat_tabcontenttext').parent().hasClass('slimScrollDiv')){
					popup.find('.cometchat_tabcontenttext').parent().height(height1);
					jqcc.docked.scrollDown(toid);
				}
				popup.find(".cometchat_tabcontent").append('<div id="cometchat_container_'+name+'_'+toid+'" class="cometchat_container cometchat_chatboxpopup_'+toid+'" style="'+bottom+left+'width:100%" pluginname="'+name+'"><div class="cometchat_container_body" style="height:'+(height)+'px;"><div class="cometchat_loading"></div><iframe class="cometchat_iframe" id="cometchat_trayicon_'+name+'_iframe" width="100%" height="'+(height)+'"  allowtransparency="true" frameborder="0"  scrolling="no" src="'+url+'" allow="geolocation; microphone; camera; midi; encrypted-media;"></iframe></div></div>');
				popup.find('.cometchat_container_body').css('border',0);

				if(name=='stickers'){
					/*var userpopup = window.parent.document.getElementById('cometchat_user_'+toid+'_popup');
					userpopup.find('#cometchat_plugins_openup_icon_13').removeClass('cometchat_pluginsopenup_arrowrotate');*/

					window.parent.jqcc('#cometchat_user_'+toid+'_popup').find('#cometchat_plugins_openup_icon_'+toid).removeClass('cometchat_pluginsopenup_arrowrotate');
				}

				if(chatroommode == 1){
					popup.find('#cometchat_groupplugins_openup_icon_'+toid).addClass('cometchat_pluginsopenup_arrowrotate');
				} else {
					popup.find('#cometchat_plugins_openup_icon_'+toid).addClass('cometchat_pluginsopenup_arrowrotate');
				}
			}

			setTimeout(function() {
				cc_zindex += 1;
				jqcc('#cometchat_container_'+name).css('z-index',10000000000+cc_zindex);
			}, 100);

			var cometchat_container = jqcc('#cometchat_container_'+name);
			cometchat_container.find('.cometchat_closebox').click(function() {
				cometchat_container.remove();
				jqcc("#cometchat_tooltip").css('display', 'none');
				window.onbeforeunload = null;
			});
		}
	}
}

var closeChatboxCCPopup = closeChatboxCCPopup || function (id,chatroommode) {
	if(chatroommode == 1){
		var popup = jqcc('#cometchat_chatboxes #cometchat_group_'+id+'_popup');
		popup.find('#cometchat_groupplugins_openup_icon_'+id).removeClass('cometchat_pluginsopenup_arrowrotate');
	} else {
		var popup = jqcc('#cometchat_chatboxes #cometchat_user_'+id+'_popup');
		popup.find('#cometchat_plugins_openup_icon_'+id).removeClass('cometchat_pluginsopenup_arrowrotate');
	}

	var height = popup.find('.cometchat_tabcontenttext').innerHeight();
	popup.find('.cometchat_tabcontent .cometchat_chatboxpopup_'+id).remove();
	var height_tabcontent = popup.find('.cometchat_tabcontentinput').height();
	var height1 = ("<?php echo $chatboxHeight-75;?>"-(height_tabcontent)+22)+'px';
	popup.find('.cometchat_tabcontent .cometchat_tabcontenttext').height(height1);
	if(popup.find('.cometchat_tabcontenttext').parent().hasClass('slimScrollDiv')){
		popup.find('.cometchat_tabcontenttext').parent().height(height1);
	}
	if(chatroommode){
		jqcc[theme].chatroomScrollDown(1,id);
	} else {
		jqcc[theme].scrollDown(id);
	}
}

function getTimeDisplay(ts) {
	if((ts+"").length == 10){
		ts = ts*1000;
	}
	var language = jqcc.cometchat.getLanguage();
	var time = new Date(ts);
	var ap = "";
	var hour = time.getHours();
	var minute = time.getMinutes();
	var date = time.getDate();
	var month = time.getMonth();
	var day = time.getDay();
	var year = time.getFullYear();
	var armyTime = 0;
	var todaysDate = new Date();
	var todays12am = (todaysDate.getTime() - (todaysDate.getTime()%(24*60*60*1000)));
	var yesterdays12am = todays12am - 86400000;
	var ytt='';
	if(typeof(jqcc.cometchat.getSettings) == 'undefined'){
		armyTime = jqcc.cometchat.getChatroomVars('armyTime');
	} else {
		armyTime = jqcc.cometchat.getSettings()['armyTime'];
	}
	if(armyTime!=1){
		ap = hour>11 ? "PM" : "AM";
		hour = hour==0 ? 12 : hour>12 ? hour-12 : hour;
	}else{
		hour = hour<10 ? "0"+hour : hour;
	}
	minute = minute<10 ? "0"+minute : minute;
	var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
	var days = ['Sun','Mon','Tue','Wed','Thurs','Fri','Sat'];
	var type = 'th';
	if(date==1||date==21||date==31){
		type = 'st';
	}else if(date==2||date==22){
		type = 'nd';
	}else if(date==3||date==23){
		type = 'rd';
	}
	if (time.getTime() > todays12am && time.getTime() > yesterdays12am) {
		ytt = language['today'];
	} else if(time.getTime() < todays12am && time.getTime() < yesterdays12am){
		ytt = '';
	} else {
		ytt = language['yesterday'];
	}
	return {ap:ap,hour:hour,minute:minute,date:date,month:months[month],year:year,type:type,day:days[day],ytt:ytt};
}

function attachPlaceholder(element){
	jqcc(element).find('[placeholder]').focus(function() {
		var input = jqcc(this);
		if (input.val() == input.attr('placeholder')) {
			input.val('');
			input.removeClass('cometchat_placeholder');
		}
		}).blur(function() {
		var input = jqcc(this);
		if (input.val() == '') {
			input.addClass('cometchat_placeholder');
			input.val(input.attr('placeholder'));
		}
	}).blur();

	jqcc(element).find('[placeholder]').parents('form').submit(function() {
		jqcc(this).find('[placeholder]').each(function() {
			var input = jqcc(this);
			if (input.val() == input.attr('placeholder')) {
				input.val('');
			}
		});
	});
}

function isWindowOpen() {
	var webrtcplugins = [];
	if(typeof(jqcc.cometchat.getWebrtcPlugins) != "undefined") {
		webrtcplugins = jqcc.cometchat.getWebrtcPlugins();
	} else if(typeof(parent.jqcc.cometchat.getWebrtcPlugins) != "undefined") {
		webrtcplugins = parent.jqcc.cometchat.getWebrtcPlugins();
	}
	for(var key in cc_windownames) {
	  	for(var i=0;i<webrtcplugins.length;i++) {
			if(key == ("cc_"+webrtcplugins[i]) && !(cc_windownames[key].closed)) {
				return true;
			} else {
				return false;
			}
		}
	}
}

window.onbeforeunload = function(event) {
	jqcc('.cometchat_container').each(function(index,element){
		if(jqcc(this).attr('id').indexOf("audiochat")>-1 || jqcc(this).attr('id').indexOf('audiovideochat')>-1 || jqcc(this).attr('id').indexOf('writeboard')>-1 || jqcc(this).attr('id').indexOf('whiteboard')>-1 || jqcc(this).attr('id').indexOf('broadcast')>-1){
			var language = jqcc.cometchat.getLanguage();
			event.returnValue = language['navigate_page'];
		}
	})
}

/* base64encode */
function b2a(a) {
  var c, d, e, f, g, h, i, j, o, b = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=", k = 0, l = 0, m = "", n = [];
  if (!a) return a;
  do c = a.charCodeAt(k++), d = a.charCodeAt(k++), e = a.charCodeAt(k++), j = c << 16 | d << 8 | e,
  f = 63 & j >> 18, g = 63 & j >> 12, h = 63 & j >> 6, i = 63 & j, n[l++] = b.charAt(f) + b.charAt(g) + b.charAt(h) + b.charAt(i); while (k < a.length);
  return m = n.join(""), o = a.length % 3, (o ? m.slice(0, o - 3) :m) + "===".slice(o || 3);
}

/* base64decode */
function a2b(a) {
  var b, c, d, e = {}, f = 0, g = 0, h = "", i = String.fromCharCode, j = a.length;
  for (b = 0; 64 > b; b++) e["ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/".charAt(b)] = b;
  for (c = 0; j > c; c++) for (b = e[a.charAt(c)], f = (f << 6) + b, g += 6; g >= 8; ) ((d = 255 & f >>> (g -= 8)) || j - 2 > c) && (h += i(d));
  return h;
}

function IsJsonString(str) {
	try {
	   return JSON.parse(str);
   } catch (e) {
	   return false;
   }
}

function IsJsonString(str) {
	 try {
		return JSON.parse(str);
	} catch (e) {
		return false;
	}
}
function delay(time) {
  var d1 = new Date();
  var d2 = new Date();
  while (d2.valueOf() < d1.valueOf() + time) {
	d2 = new Date();
  }
}

function previewCometChatMedia(params,url) {
	var mediaContentData = '';
	var allowDownload = 1;
	if(params.pluginname == 'botresponse_image'){
		mediaContentData = '<img class="cometchat_media_data cometchat_media_image" src= "'+url+'">';
		allowDownload = 0;
	} else {
		var pluginname = params.pluginname;
		if(params.mediatype == 1){
			mediaContentData = '<img class="cometchat_media_data cometchat_media_image" type="'+pluginname+'" md5fileName="'+params.md5file+'" fileName="'+params.file+'" src= "'+url+params.md5file+'">';
			jqcc('.cometchat_media_download').css('display','block');
		} else if(params.mediatype == 2){
			mediaContentData = '<video class="cometchat_media_data" width="360" height="260" type="'+pluginname+'" md5fileName="'+params.md5file+'" fileName="'+params.file+'" controls autoplay><source src="'+url+params.md5file+'" ></video>';
		} else if(params.mediatype == 3){
			mediaContentData = '<audio class="cometchat_media_data" md5fileName="'+params.md5file+'" type="'+pluginname+'" fileName="'+params.file+'" controls><source src="'+url+params.md5file+'"></audio>';
		}
	}
	if(jqcc('.cometchat_media_data').length == 0){
			jqcc('body').find('.cometchat_media_container').append(mediaContentData);
	}
	jqcc('.cometchat_media_modal div').css('visibility','visible');
	jqcc('.cometchat_media_overlay').addClass('cometchat_media_overlay_show');
	if(allowDownload == 0){
		jqcc('.cometchat_media_download').css('display','none');
	}
}

function outgoingCall(id,grp,userdata,calltype){
	var theme = jqcc.cometchat.getSettings().theme;
	var staticCDNUrl = '<?php echo STATIC_CDN_URL; ?>';
	var buddylistName = userdata.name;
	var buddylistAvatar = userdata.avatar;
	var acceptcalltype = rejectcalltype = type = '';
	if(typeof(calltype) != 'undefined' && calltype == "audiocall"){
		cancelcalltype = "audiochat_cancelcall";
	}else{
		cancelcalltype = "avchat_cancelcall";
	}
	if(jqcc("#avchat_container_"+id).length==0){
		jqcc[theme].playSound(4);
		jqcc('body').append('<div id="avchat_container_'+id+'"><div id="cometchat_avchat_container"><div id="cometchat_userself_left"><div id="cometchat_userself"><span class="cometchat_usersavatar"><img class="cometchat_usersavatarimage" src="'+buddylistAvatar+'" /></span><div id="cometchat_userselfDetails"><div class="avchat_userdisplayname">'+buddylistName+'</div><div class="cometchat_callstatus">Ringing...</div></div></div></div><div id="cometchat_cancelcall" class="cometchat_avchat_reject '+cancelcalltype+' avchat_link_'+grp+'" to='+id+' grp='+grp+'><img src="'+staticCDNUrl+'images/call.svg"></div></div></div>');
	}
}
function incomingCall(incoming,avchat_data,currenttime,userdata){
	var theme = jqcc.cometchat.getSettings().theme;
	var staticCDNUrl = '<?php echo STATIC_CDN_URL; ?>';
	var buddylistName = userdata.name;
	var buddylistAvatar = userdata.avatar;
	var acceptcalltype = rejectcalltype = type = '';
	if(jqcc.inArray("audiocall", avchat_data) !== -1){
		acceptcalltype = "acceptAudioChat";
		rejectcalltype = "audiochat_rejectcall";
		type = "ccaudiochat";
	}else{
		acceptcalltype = "acceptAVChat";
		rejectcalltype = "avchat_rejectcall";
		type = "ccavchat";
	}
	if(jqcc("#avchat_container_"+incoming.from).length==0 && (incoming.sent > currenttime - 15)){
			jqcc('body').append("<div id='avchat_container_"+incoming.from+"'><div id='cometchat_avchat_container'><div id='cometchat_userself_left'><div id='cometchat_userself'><span class='cometchat_usersavatar'><img class='cometchat_usersavatarimage' src='"+buddylistAvatar+"'/></span><div id='cometchat_userselfDetails'><div class='avchat_userdisplayname'>"+buddylistName+"</div><div class='cometchat_callstatus'>Incoming Call</div></div></div></div><div id='cometchat_acceptcall' class='cometchat_avchat_accept "+acceptcalltype+" avchat_link_'"+avchat_data[2]+"' to='"+incoming.from+"' token='' grp='"+avchat_data[2]+"' join_url='' start_url='' chatroommode='0' caller='"+avchat_data[3]+"'><img src='"+staticCDNUrl+"images/call.svg'></div><div id='cometchat_rejectcall' class='cometchat_avchat_reject "+rejectcalltype+" avchat_link_"+avchat_data[2]+"' to='"+incoming.from+"' grp='"+avchat_data[2]+"'><img src='"+staticCDNUrl+"images/call.svg'></div></div></div>");
			jqcc[theme].playSound(3);
			var params = {"incoming":incoming.from, "grp":avchat_data[2], "type":type};
			setTimeout(function(params) {
				if(jqcc("#avchat_container_"+incoming.from).length){
					jqcc[type].ignore_call(incoming.from,avchat_data[2]);
					removeCallContainer(incoming.from);
				}
			},30000);
	}
}
function removeCallContainer(id) {
	var windowkey = 'cc_audiovideochat';
	if(jqcc("#avchat_container_"+id).length != 0){
	   jqcc("#avchat_container_"+id).remove();
	} else if(typeof (cc_windownames[windowkey]) != 'undefined' && !(cc_windownames[windowkey].closed)){
		cc_windownames[windowkey].close();
	}
}

function toggleBotsAction(params) {
	var botlist = params.botlist;
	var botid   = params.botid;
	var staticCDNUrl = '<?php echo STATIC_CDN_URL; ?>';
	var bots_language   = params.bots_language;
	var returnparameters = {"type":'core', "name":'libraries', "method":'showBotlist'};
	returnparameters = JSON.stringify(returnparameters);

	jqcc('#bots_window').find("#cometchat_windowtitlebar").prepend('<div id="cometchat_botsback" class="cometchat_backwindow" ><img src="'+staticCDNUrl+'layouts/embedded/images/leftarrow.svg"/></div>');
	jqcc('#bots_window').find("#bots_closewindow").hide();
	jqcc("#bots_window").find("#cometchat_bot_title_text").text(botlist[botid]['n']);
	jqcc("#cometchat_bots_popup").find(".cometchat_closebox").hide();
	jqcc("#cometchat_bots_popup").find(".cometchat_userstabtitle").prepend('<div id="cometchat_botsback" class="cometchat_back" ></div>');
	jqcc("#cometchat_bots_popup").find(".cometchat_userstabtitletext").text(botlist[botid]['n']);
	jqcc("#cometchat_bots_popup").find(".cometchat_userstabtitletext").css('margin-left', '10px');

	jqcc("#bots_window").find("#cometchat_botsback").live('click', function() {
		jqcc('#bots_window').find("#bots_closewindow").show();
		jqcc('#cometchat_bots_iframe')[0].contentWindow.postMessage('CC^CONTROL_'+returnparameters,'*');
		jqcc("#bots_window").find("#cometchat_botsback").remove();
		jqcc("#bots_window").find("#cometchat_bot_title_text").text(bots_language);
	});

	jqcc("#cometchat_bots_popup").find("#cometchat_botsback").live('click', function() {
		jqcc("#cometchat_bots_popup").find(".cometchat_closebox").show();
		jqcc('#cometchat_bots_iframe')[0].contentWindow.postMessage('CC^CONTROL_'+returnparameters,'*');
		jqcc("#cometchat_bots_popup").find("#cometchat_botsback").remove();
		jqcc("#cometchat_bots_popup").find(".cometchat_userstabtitletext").text(bots_language);
		jqcc("#cometchat_bots_popup").find(".cometchat_userstabtitletext").css('margin-left', '20px');
	});
}

jqcc(function(){
	var baseUrl = '<?php echo BASE_URL;?>';
	var staticCDNUrl = '<?php echo STATIC_CDN_URL;?>';
	var intervalCount = 0;
	var mobileDevice  = navigator.userAgent.match(/ipad|ipod|iphone|android|windows ce|Windows Phone|IEMobile|blackberry|palm|symbian/i);
	if(mobileDevice==null){
		var mediaoverlay = fileTransferinterval = setInterval(function () {
			overlay = '<div class="cometchat_media_overlay" style="display:none;"><div class="cometchat_media_modal"><div><div class="cometchat_media_content"><div class="cometchat_media_container"></div></div><img class="cometchat_media_download" src="'+staticCDNUrl+'images/download.png"><img class="cometchat_close_dialog" src="'+staticCDNUrl+'images/close.png" ></div></div></div>';
			if(jqcc('#cometchat').length >= 1 && jqcc('#cometchat').find('.cometchat_media_overlay').length <= 0) {
				jqcc('#cometchat').append(overlay);
			} else if(jqcc('#cometchat').length == 0 && jqcc('body').find('.cometchat_media_overlay').length <= 0) {
				jqcc('body').append(overlay);
			}
			window.clearInterval(mediaoverlay);
		}, 1000);
	}
	jqcc('.cometchat_media_download').live('click',function(){
		var file = jqcc('.cometchat_media_data').attr('fileName');
		var pluginname = jqcc('.cometchat_media_data').attr('type');
		var md5file = jqcc('.cometchat_media_data').attr('md5fileName');
		location.href = baseUrl+"plugins/"+pluginname+"/download.php?file="+md5file+"&unencryptedfilename="+file+"";
	});

	jqcc('.cometchat_close_dialog').live('click',function(){
		jqcc('.cometchat_media_modal div').css('visibility','hidden');
		jqcc('.cometchat_media_overlay').removeClass('cometchat_media_overlay_show');
		jqcc('body').find('.cometchat_media_container').html('');

	});

	jqcc(".cometchat_media_overlay").live('click',function(e){
		if (jqcc(e.target).hasClass('cometchat_media_data') || jqcc(e.target).hasClass('cometchat_media_download')) {
			return false;
		}
		jqcc('.cometchat_media_modal div').css('visibility','hidden');
		jqcc('.cometchat_media_overlay').removeClass('cometchat_media_overlay_show');
		jqcc('body').find('.cometchat_media_container').html('');
	});
	jqcc('.mediamessage').live('click',function(e){
		if(mobileDevice==null){
			e.preventDefault();
			var baseUrl = '<?php echo BASE_URL;?>';
			var file = jqcc(this).attr('filename');
			var md5file = jqcc(this).attr('encfilename');
			var mediatype = jqcc(this).attr('mediatype');
			var pluginname = jqcc(this).attr('pluginname');
			var aws_storage = '<?php echo AWS_STORAGE;?>';
			var aws_bucket_url = '<?php echo !empty($client) ? "s3.amazonaws.com/".$aws_bucket_url : $aws_bucket_url; ?>';
			var bucket_path = '<?php echo $bucket_path;?>';
			if(aws_storage == '1') {
				url = '//'+aws_bucket_url+'/'+bucket_path+pluginname+'/';
			}else {
				url = baseUrl+'writable/'+pluginname+'/uploads/';
			}
			var controlparameters = {"file":file, "md5file":md5file, "mediatype":mediatype, "pluginname":pluginname};
			if(((typeof(parent) != 'undefined' && parent != null && parent != self) || window.top != window.self) && jqcc.cometchat.getCcvariable().callbackfn!='desktop'){
				controlparameters = {"type":"modules", "name":"core", "method":"previewCometChatMedia", "src":url, "params":controlparameters};
				controlparameters = JSON.stringify(controlparameters);
				parent.postMessage('CC^CONTROL_'+controlparameters,'*');
			} else {
				previewCometChatMedia(controlparameters,url);
			}
		} else {
			var downloadLink = jqcc(this).attr('link');
			window.open(downloadLink);
		}
	});

	jqcc('.cometchat_botimagefile').live('click',function(e){
		if(mobileDevice==null){
			e.preventDefault();
			var src = jqcc(this).attr('src');
			var pluginname = 'botresponse_image';
			var controlparameters = {"pluginname":pluginname};
			if(((typeof(parent) != 'undefined' && parent != null && parent != self) || window.top != window.self) && jqcc.cometchat.getCcvariable().callbackfn!='desktop'){
				controlparameters = {"type":"modules", "name":"core", "method":"previewCometChatMedia", "src":src, "params":controlparameters};
				controlparameters = JSON.stringify(controlparameters);
				parent.postMessage('CC^CONTROL_'+controlparameters,'*');
			} else {
				previewCometChatMedia(controlparameters,src);
			}
		} else {
			var downloadLink = jqcc(this).attr('src');
			window.open(downloadLink);
		}
	});
});

function isbase64encoded(){
	try{
		return btoa(atob(str)) == str;
	}catch(err){
		return false;
	}
}
