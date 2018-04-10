/*
 * CometChat
 * Copyright (c) 2016 Inscripts - support@cometchat.com | http://www.cometchat.com | http://www.inscripts.com
*/
var ts = parseInt(new Date().getTime()/1000);
(function($){

	$.cometchatmonitor = function(){

		var heartbeatTimer;
		var timeStamp = '0';

		function chatHeartbeat(){

			$.ajax({
				url: "index.php?module=monitor&action=data&ts="+ts,
				data: {timestamp: timeStamp},
				type: 'post',
				cache: false,
				dataFilter: function(data) {
					if (typeof (JSON) !== 'undefined' && typeof (JSON.parse) === 'function')
					  return JSON.parse(data);
					else
					  return eval('(' + data + ')');
				},
				success: function(data) {
					if (data) {
						var htmlappend = '';

						$.each(data, function(type,item){
							if (type == 'timestamp') {
								timeStamp = item;
							}

							if (type == 'online') {
								$('#online').html(item);
							}

							if (type == 'messages') {
								$.each(item, function(i,incoming) {
									if(incoming.message.indexOf('avchat_webaction=initiate')!=-1 || incoming.message.indexOf('avchat_webaction=acceptcall')!=-1){
										incoming.message ="Video call";
			                    	}
			                    	if(incoming.message.indexOf('audiochat_webaction=initiate')!=-1 || incoming.message.indexOf('audiochat_webaction=acceptcall')!=-1){
										incoming.message ="Audio call";
			                    	}
									htmlappend = '<tr><td>'+incoming.fromu+' <span style="font-size:10px;"><i class="fa fa-chevron-right"></i></span> '+incoming.tou+'</td><td>'+incoming.message+'</td><td>'+getTimeDisplay(new Date(incoming.time))+'</td></tr>' + htmlappend;

								});
							}
						});

						if (htmlappend != '') {
							$("#data").prepend(htmlappend);
							$('div.message').fadeIn(2000);
							$('div.message:gt(19)').remove();
							$('#noRecords').remove();
						}
						if ($('#monitor').children('tbody').find('tr').length == 0) {
							htmlappend = '<tr id="noRecords"><td colspan="3">No conversion as yet.</td></tr>';
							$("#data").prepend(htmlappend);
						}
					}

				clearTimeout(heartbeatTimer);
				heartbeatTimer = setTimeout( function() { chatHeartbeat(); },3000);

			}});

		}

		chatHeartbeat();

	}

	$.fancyalert = function(message,type){
		type = typeof type !== 'undefined' ? type : 'success';
		if ($("#"+type).length > 0) {
			removeElement(type);
		}
		var html = '<div id="'+type+'">'+message+'</div>';
		$('body').prepend(html);
		$alert = $('#'+type);
			if($alert.length) {
				var alerttimer = window.setTimeout(function () {
					$alert.trigger('click');
				}, 5000);
				$alert.animate({height: $alert.css('line-height') || '50px'}, 200)
				.click(function () {
					window.clearTimeout(alerttimer);
					$alert.slideToggle(400);
				});
			}
	};

	$(document).on('click','table a.feature-unavailable',function(e){
		e.preventDefault();
		window.location.href = window.location.pathname+"?module=update&action=forceUpdate";
	})
})(jQuery);


/* CCAUTH */

/* CCAUTH */

function ccauth_updateorder(authmode) {
	var order = {};
	$('#auth_livemodes').find('tr[ccauth="active"]').each(function(idx, elm) {
		order[idx]= $(elm).attr('d1');
	});
	$('#cc_auth_order').val(JSON.stringify(order));
	var conf;
	if(!(authmode==1 && $('#cc_auth_radio:checked').length > 0) && !(authmode==0 && $('#site_auth_radio:checked').length > 0) && order != ''){
		conf = confirm("This action will clear old data. Are you sure?");
	}else{
		conf = true;
	}
	if (conf == true) {
		if($('#cc_auth_radio:checked').length > 0 && order == ''){
			$.fancyalert('Please select atleast 1 of the Social Authentication options or use Site\'s Authentication');
			return false;
		}
		return true;
	}
	return false;
}

function ccauth_removeauthmode(id,obj) {
	var rel = $('#'+id).attr('rel');
	$('.tooltip').remove();
	$(obj).closest("tr").attr("ccauth","inactive");
	$(obj).parent().html('<a title="Add" data-toggle="tooltip" style="color:green;" href="javascript:void(0)" onclick="javascript:ccauth_addauthmode('+id+',\''+rel+'\',this);"><i class="fa fa-lg fa-plus-circle"></i></a>');
	$('[data-toggle="tooltip"]').tooltip();
}

function ccauth_addauthmode(id,name,obj) {
	$('.tooltip').remove();
	$(obj).closest("tr").attr("ccauth","active");
	$(obj).parent().html('<a title="Remove" style="color:red;" data-toggle="tooltip" href="javascript:void(0)" onclick="javascript:ccauth_removeauthmode(\''+id+'\',this)"><i class="fa fa-lg fa-minus-circle"></i></a>');
	$('[data-toggle="tooltip"]').tooltip();
}

/* Modules */

function modules_updateorder(del,ren,showhide,lightbox) {
	var order = {};
	$('#other_feature_list').children('tbody').find('tr[type="module active"]').each(function(idx, elm) {
		order[elm.id] = [
			elm.id,
			$(elm).attr('d1'),
			$(elm).attr('d2'),
			$(elm).attr('d3'),
			$(elm).attr('d4'),
			$(elm).attr('d5'),
			$(elm).attr('d6'),
			$(elm).attr('d7'),
			$(elm).attr('d8')];
	});
	$.post('?module=features&action=updatemoduleorder&ts='+ts, {'order': order}, function(data) {
		if (lightbox) {
			$.fancyalert('Feature has been set to appear as a '+showhide+'');
		} else if (showhide) {
			$.fancyalert('Feature text will now be '+showhide+' in the bar');
		} else if (ren) {
			$.fancyalert('Feature successfully renamed.');
		} else if (del) {
			$.fancyalert('Feature successfully deactivated.');
		} else {
			$.fancyalert('Feature order successfully updated.');
		}
	});

}

function modules_removemodule(id,custom,obj) {
	var rel = $('#'+id).attr('rel');
	if (custom == 1) {
		var answer = confirm ('Are you sure you want to remove this feature?');
	} else {
		var answer = confirm ('Are you sure you want to deactivate this feature?');
	}
	if (answer) {
		$('#'+id).attr('type','module');
		modules_updateorder(true);
		$(obj).parent().html('<a data-toggle="tooltip" title="Add feature" style="color:green;" href="javascript:void();"><i class="fa fa-plus-circle"></i></a>');
		if (custom == 1){
			$.post('?module=features&action=removecustommodules&ts='+ts, {'module': id}, function(data) {});
		}
		$('.tooltip').remove();
       	$('[data-toggle="tooltip"]').tooltip();
		setTimeout(function () { location.reload();}, 1500);
	}
}

function modules_renamemodule(id) {
	if (document.getElementById(id+'_title').innerHTML.indexOf('<a href="?module=features&amp;ts='+ts+'">cancel</a>') == -1) {
		document.getElementById(id+'_title').innerHTML = '<input type="textbox" id="'+id+'_newtitle" class="inputboxsmall" style="margin-bottom:3px" value="'+document.getElementById(id+'_title').innerHTML+'"/><br/><input type="button" onclick="javascript:modules_renamemoduleprocess(\''+id+'\');" value="Rename" class="buttonsmall">&nbsp;&nbsp;or <a href="?module=features&amp;ts='+ts+'">cancel</a>';
	}
}

function modules_renamemoduleprocess(id) {
	var newtitle = document.getElementById(id+'_newtitle').value+'';
	newtitle = newtitle.replace(/"/g,'');

	document.getElementById(id).setAttribute('d1',newtitle.replace("'","\\\\\\\'"));
	document.getElementById(id+'_title').innerHTML = newtitle;
	modules_updateorder(false,true);
}

function modules_showtext(self,id) {
	var current = $('#'+id).attr('d8');

	if (current == '' || current == 0) {
		var newvalue = 1;
		$(self).find("img").css('opacity','0.5');
		$(self).find("img").attr('title','Hide the module title in the chatbar');
	} else {
		var newvalue = '';
		$(self).find("img").css('opacity','1');
		$(self).find("img").attr('title','Show the module title in the chatbar');
	}

	document.getElementById(id).setAttribute('d8',newvalue);
	if (newvalue == 1) { text = 'shown'; } else { text = 'hidden'; }
	modules_updateorder(false,false,text);
}

function modules_showpopup(self,id) {
	var current = $('#'+id).attr('d3');

	if (current == '_lightbox') {
		var newvalue = '_popup';
		$(self).find("img").css('opacity','1');
		$(self).find("img").attr('title','Open module in a lightbox');
	} else {
		var newvalue = '_lightbox';
		$(self).find("img").css('opacity','0.5');
		$(self).find("img").attr('title','Open module as a popup');
	}

	document.getElementById(id).setAttribute('d3',newvalue);
	if (newvalue == '_lightbox') { text = 'lightbox'; } else { text = 'popup'; }
	modules_updateorder(false,false,text,true);
}

function removeElement(id) {
  var element = document.getElementById(id);
  element.parentNode.removeChild(element);
}


/* Plugins */

function plugins_updateorder(del) {
	var order = {};
	var cnt = 0;
	$('#conversion_feature_list').children('tbody').find('tr[oneonone="active"]').each(function(idx, elm) {
		order[cnt++] = $(elm).attr('d1');
	});
	$('#chat_feature_list').children('tbody').find('tr[oneonone="active"]').each(function(cflidx, elm) {
		order[cnt++] = $(elm).attr('d1');
	});

	$.post('?module=features&action=updateorder&ts='+ts, {'order': order}, function(data) {
		if (del) {
			$.fancyalert('Feature successfully deactivated.');
		} else {
			$.fancyalert('Features order successfully updated.');
		}
	});

}


function plugins_removeplugin(id) {
	var rel = $('#'+id).attr('rel');
	$('.tooltip').remove();
	if (rel == 'filetransfer' && $('tr[rel="voicenote"]').attr('oneonone') == 'active') {
		alert("Please remove Voice Note feature from One-one-one before removing this feature");
		return;
	}
	var answer = confirm ('Are You Sure You Want To Deactivate This Feature?');
	if (answer) {
		$('#'+id).attr('oneonone','');
		plugins_updateorder(true);
		$('#oneonone_'+rel).parent().html('<a data-toggle="tooltip" title="Add in one on one" style="color:#008000;opacity: 0.2;" href="?module=features&amp;action=addplugin&amp;data='+rel+'&amp;ts='+ts+'" id="oneonone_'+rel+'"><i class="fa fa-lg fa-user"></i></a>');
		$('[data-toggle="tooltip"]').tooltip();
	}
}

function plugins_updatechatroomorder(del) {
	var order = {};
	var cnt = 0;
	$('#conversion_feature_list').children('tbody').find('tr[group="active"]').each(function(idx, elm) {
		order[cnt++] = $(elm).attr('d1');
	});
	$('#chat_feature_list').children('tbody').find('tr[group="active"]').each(function(idx, elm) {
		order[cnt++] = $(elm).attr('d1');
	});
	$.post('?module=features&action=updatechatroomorder&ts='+ts, {'order': order}, function(data) {
		if (del) {
			$.fancyalert('Feature Successfully Deactivated.');
		} else {
			$.fancyalert('Feature Order Successfully Updated.');
		}
	});

}

function plugins_removechatroomplugin(id) {
       	var rel = $('#'+id).attr('rel');
       	$('.tooltip').remove();
		if (rel == 'filetransfer' && $('tr[rel="voicenote"]').attr('group') == 'active') {
			alert("Please remove Voice Note Feature from Group before removing this feature");
			return;
		}
       	var answer = confirm ('Are you sure you want to deactivate this plugin?');
        if (answer) {
        	$('#'+id).attr('group','');
        	plugins_updatechatroomorder(true);
        	$('#group_'+rel).parent().html('<a data-toggle="tooltip" title="Add in group" style="color:#008000;opacity: 0.2;" href="?module=features&amp;action=addchatroomplugin&amp;data='+rel+'&amp;ts='+ts+'" id="crpluginaction'+rel+'"><i class="fa fa-lg fa-users"></i></a>');
       		$('[data-toggle="tooltip"]').tooltip();
        }
}

function plugins_renameplugin(id) {
	$.fancyalert('Please edit the plugin language to modify the name');
}

function extensions_removeextension(id,self) {
	var answer = confirm ('Are you sure you want to deactivate this extension?');
	var rel = $('#'+id).attr('rel');
	if (answer) {
		$('#'+id).attr('type','extensions');
		$(self).parent().html('<a data-toggle="tooltip" title="Add feature" style="color:green;" href="?module=features&action=addextension&data='+rel+'&ts='+ts+'"><i class="fa fa-plus-circle"></i></a>');
		extensions_updateorder(true);
		$('.tooltip').remove();
       	$('[data-toggle="tooltip"]').tooltip();
	}
}

function extensions_updateorder(del) {
	var order = {};
	var i = 0;
	$('#other_feature_list').children('tbody').find('tr[type="extensions active"]').each(function(idx, elm) {
		order[idx]= $(elm).attr('d1');
		i++;
	});
	order[i++] = 'mobileapp';
	order[i] = 'desktop';
	$.post('?module=features&action=updateextensionorder&ts='+ts, {'order': order}, function(data) {
		$.fancyalert('Feature successfully deactivated.');
	});

}

function extensions_configextension(id) {
	$("#adminModellink").trigger('click');
	$("#admin-modal-title").text(id+' Settings');
	$('.tooltip').remove();
	var link = '?module=dashboard&action=loadexternal&type=extension&name='+id;
	$("#admin-modal-body").html("<iframe frameborder='0' height='300px' width='100%' src='"+link+"'></iframe>");
}

function themes_makedefault(id) {
	$.post('?module=themes&action=makedefault&ts='+ts, {'theme': id}, function(data) {
		location.href = '?module=themes&ts='+ts;
	});
}

function themestype_makedefault(id) {
	$.post('?module=themes&action=themestypemakedefault&ts='+ts, {'theme': id}, function(data) {
		location.href = '?module=themes&ts='+ts;
	});
}

function themes_editcolor(id) {
	location.href = '?module=themes&action=editcolor&data='+id+'&ts='+ts;
}

function create_new_colorscheme(){
        location.href = '?module=themes&action=clonecolor&theme=docked&ts='+ts;
}

function themes_removecolor(id) {
	var answer = confirm ('This action cannot be undone. Are you sure you want to perform this action?');
	if (answer) {
            location.href = '?module=themes&action=removecolorprocess&data='+id+'&ts='+ts;
	}
}

function logs_gotouser(id,user) {
	$("#admin-modal-title").text('Log for '+user);
	$('#admin-modal-title', window.parent.document).text('Log for '+user);
	location.href = '?module=logs&action=viewuser&data='+id+'&ts='+ts;
}

function logs_gotochatroom(id) {
	location.href = '?module=logs&action=viewuserchatroomconversation&data='+id+'&ts='+ts;
}

function logs_gotouserb(id,id2,user) {
	$("#admin-modal-title").text('Log for '+user);
	$('#admin-modal-title', window.parent.document).text('Log for '+user);
	location.href = '?module=logs&action=viewuserconversation&data='+id+'&data2='+id2+'&ts='+ts;
}

function auth_configauth(id) {
	$("#adminModellink").trigger('click');
	$("#admin-modal-body").css('height','');
	$("#admin-modal-title").text(id+' Settings');
	$('.tooltip').remove();
	var link = '?module=dashboard&action=loadexternal&type=function&name=login&option='+id+'&ts='+ts;
	$("#admin-modal-body").html("<iframe frameborder='0' height='350px' width='100%' src='"+link+"'></iframe>");
}

function modules_configmodule(id) {
	var link = '?module=dashboard&action=loadexternal&type=module&name='+id+'&ts='+ts;
	if(id == "chatrooms"){
		id = "Groups";
	}
	if(id == "realtimetranslate"){
		id = "Real Time Translation";
	}
	$("#adminModellink").trigger('click');
	$("#admin-modal-title").text(id+' Settings');
	$('.tooltip').remove();
	$("#admin-modal-body").html("<iframe frameborder='0' height='300px' width='100%' src='"+link+"'></iframe>");
}

function plugins_configplugin(id) {
	$("#adminModellink").trigger('click');
	$("#admin-modal-title").text(id+' Settings');
	$('.tooltip').remove();
	var link = '?module=dashboard&action=loadexternal&type=plugin&name='+id+'&ts='+ts;
	$("#admin-modal-body").html("<iframe frameborder='0' height='300px' width='100%' src='"+link+"'></iframe>");
}

function themetype_configmodule(id) {
	$("#adminModellink").trigger('click');
	$("#admin-modal-title").text('Mobile Settings');
	var link = '?module=dashboard&action=loadthemetype&type=layout&name='+id+'&embedcode=0&ts='+ts;
	$("#admin-modal-body").html("<iframe frameborder='0' height='300px' width='100%' src='"+link+"'></iframe>");
}

function themetype_embedcode(id) {
	$("#adminModellink").trigger('click');
	$("#admin-modal-body").html("<h4>Loading...</h4>");
	var link = '';
	if(id=="embedded"){
		$("#admin-modal-title").text('HTML Code');
		link = '?module=dashboard&action=themeembedcodesettings&type=layout&name='+id+'&embedcode=1&ts='+ts;
	}else{
		$("#admin-modal-title").text('HTML Code');
		link = '?module=dashboard&action=loadthemetype&type=layout&name=docked&process=true&ts='+ts;
	}
	$("#admin-modal-body").html("<iframe frameborder='0' height='300px' width='100%' src='"+link+"'></iframe>");

}

function themes_updatecolors(theme) {
	var colors = {};
	$('div.colors').each(function() {
		colors[$(this).attr('oldcolor')] = $(this).attr('newcolor');
	});

	$.post('?module=themes&action=updatecolorsprocess&ts='+ts, {'theme': theme, 'colors': colors}, function(data) {
		window.location.reload();
	});
	return false;
}

function themes_updatevariables(theme, newcolor) {
	$.post('?module=themes&action=updatevariablesprocess&ts='+ts, {'theme': theme, 'colors': newcolor}, function(data) {
		window.location.reload();
	});
	return false;
}

function themes_restorecolors(){
	$.post('?module=themes&action=restorecolorprocess&ts='+ts, {}, function(data) {
		window.location.reload();
	});
	return false;
}

function language_updatelanguage(element) {
	var lang_key = lang_text = code = type = name = '';
	if(element[0].type == 'checkbox'){
		lang_key = $(element).attr('lang_key');
		lang_text = $(element).val();
		code = $(element).attr('code');
		type = $(element).attr('addontype');
		name = $(element).attr('addonname');
	}else{
		lang_key = element.parent('div').siblings().find('textarea').attr('lang_key');
		lang_text = element.parent('div').siblings().find('textarea').val();
		code = element.parent('div').siblings().find('textarea').attr('code');
		type = element.parent('div').siblings().find('textarea').attr('addontype');
		name = element.parent('div').siblings().find('textarea').attr('addonname');
	}
	console.log(lang_key,lang_text,code,type,name);
	$.post('?module=localize&action=editlanguageprocess&ts='+ts, {'lang_key': lang_key, 'lang_text': lang_text, 'code': code, 'type': type, 'name': name}, function(data) {
		$.fancyalert('Language has been successfully modified.');
	});
	return false;
}

function language_makedefault(id) {
	$.post('?module=localize&action=makedefault&ts='+ts, {'lang': id}, function(data) {
		location.href = '?module=localize&ts='+ts;
	});
}

function language_restorelanguage(md5,id,file,lang) {
	var language = {};
	$('#'+md5).find("textarea").each(function(index,value) {
		language[index] = $(value).attr('value');
	})
	$.post('?module=localize&action=restorelanguageprocess&ts='+ts, {'id': id, 'lang': lang, 'file': file, 'language': language}, function(data) {
		window.location.reload();
	});
	return false;
}

function language_importlanguage(id) {

	var answer = confirm ('Are you sure you want to add this language?');
	if (answer) {
		$("#over").show();
		$.getJSON('//www.cometchat.com/software/getlanguage/?v=<?php echo $currentversion; ?>&callback=?', {id: id}, function(data) {
			if (data) {
				$.post('?module=localize&action=importlanguage&ts='+ts+'&callback=?', {data: data}, function(data) {
					if (data) {
						location.href = '?module=localize&ts='+ts;
					}
				});
			}
		});
	}
	return false;
}

function language_previewlanguage(id) {
	$("#adminModellink").trigger('click');
	$("#admin-modal-title").text('Preview Language');
	$('.tooltip').remove();
	$("#admin-modal-body").html("<center><img src='images/simpleloading.gif'></center>");
	$.getJSON('//www.cometchat.com/software/getlanguage/?v=<?php echo $currentversion; ?>&callback=?', {id: id}, function(data) {
		if (data) {
			$.post('?module=localize&action=previewlanguage&ts='+ts+'&callback=?', {data: data}, function(data) {
				if (data) {
					$("#admin-modal-body").html('<div id="preview_'+id+'" style="height:100px;overflow:scroll;overflow-x:hidden;padding:5px;border:1px solid #ccc;margin-top:10px;"><code><pre>'+data+'</pre></code></div>');
				}
			});
		}
	});
	return false;
}

function language_getlanguages() {
	$.getJSON('//www.cometchat.com/software/getlanguages/?v=<?php echo $currentversion; ?>&callback=?', {}, function(data) {
		data.sort(function(a,b){
			return a.id > b.id;
		});
		if (data) {
			var html = '';
			for (language in data) {
				language = data[language];
				if(((language['id']) !='.') && ((language['id']) !='..') && $('#downloadedlanguage_'+language['name']).length == 0){
					html += '<tr id="'+language['id']+'"><td id="'+language['id']+'_title">'+language['language']+' ('+language['name']+')</td><td><a title="Preview Language" style="color:#000000;" href="javascript:void(0)" data-toggle="tooltip" onclick="javascript:language_previewlanguage(\''+language['id']+'\')"><i class="fa fa-lg fa-folder-open"></i></a></td><td><a style="color:green;" data-toggle="tooltip" title="Add Language" href="javascript:void(0)" onclick="javascript:language_importlanguage(\''+language['id']+'\')"><i class="fa fa-lg fa-plus-circle"></i></a></td></tr>';
				}
			}
			$('#modules_livelanguage').html(html);
			$('[data-toggle="tooltip"]').tooltip();
		}
	});
	return false;
}

function language_removelanguage(id) {
	var answer = confirm ('This action cannot be undone. Are you sure you want to perform this action?');
	if (answer) {
		location.href = '?module=localize&action=removelanguageprocess&data='+id+'&ts='+ts;
	}
}

function language_sharelanguage(id) {
	var answer = prompt ('Please enter the full name for your language');
	if (answer) {
		var name = prompt ('Please enter your name (for credit line) (leave blank for anonymous)');
		$.get('?module=localize&action=sharelanguage&ts='+ts, {'data': id, 'lang': answer, 'name': name}, function(data) {
			$.fancyalert('Thank you for sharing!');
		});
	}
}

function embed_link(url,width,height) {
	$("#adminModellink").trigger('click');
	$('.tooltip').remove();
	var mod = url.split('/modules/');
	var module = mod[1].split('/');
	var baseUrl = mod[0]+'/';
	var style ="";
	$("#admin-modal-title").text('Embed Code');
	var embedscript = '<?php echo getDynamicScriptAndLinkTags(array('type' => "core",'name' => 'embedcode','escapetags'=>1, 'ext' => 'js'));?><script>var iframeObj = {};iframeObj.module="'+module[0]+'";iframeObj.src="'+url+'";iframeObj.width="'+width+'";iframeObj.height="'+height+'";if(typeof(addEmbedIframe)=="function"){addEmbedIframe(iframeObj);}</script>';
	if(module[0]=='chatrooms'){
		if(mod[mod.length-1].indexOf('id=') > -1) {
			var crid = (mod[mod.length-1].split('?')[1]).split('=')[1];
			height = parseInt(height)+200;
			var link = '';
				$("#admin-modal-title").text('Embed Code');
				link = '?module=dashboard&action=themeembedcodesettings&type=module&name=chatrooms&embedcode=1&crid='+crid+'&ts='+ts;
			$("#admin-modal-body").html("<iframe frameborder='0' height='300px' width='100%' src='"+link+"'></iframe>");
		} else {
			var link = '';
				$("#admin-modal-title").text('Embed Code');
				link = '?module=dashboard&action=themeembedcodesettings&type=module&name=chatrooms&embedcode=1&ts='+ts;
			$("#admin-modal-body").html("<iframe frameborder='0' height='300px' width='100%' src='"+link+"'></iframe>");
		}
	}else if(module[0]=='broadcastmessage'){
		$("#admin-modal-body").html('<textarea readonly style="width:100%;height:140px"><div id="cometchat_embed_broadcastmessage_container"></div>'+embedscript+'</textarea>');
	}else{
		$("#admin-modal-body").html('<textarea readonly style="width:100%;height:140px"><iframe src="'+url+'" width="'+width+'" height="'+height+'" frameborder="1" id="cometchat_embed_'+module[0]+'" name="cometchat_'+module[0]+'_iframe"></iframe></textarea><script>window.resizeTo(520,125);</script>');
	}
}

function embed_code(url) {
	var mod = url.split('/cometchat_popout');
	var baseUrl = mod[0]+'/';
	embedcode = window.open('','embedcode','width=520,height=200,resizable=0,scrollbars=0');
	embedcode.document.write("<title>Embed Code</title><style>.input{padding:10px;} .input input{padding:5px;border-radius:2px;border:1px solid #aeaeae;width:100%;},textarea { border:1px solid #ccc; color: #333; font-family:verdana; font-size:12px; }button{border: 1px solid #76b6d2;padding: 4px;background: #76b6d2;color: #fff;font-weight: bold;font-size: 10px;font-family: arial;text-transform: uppercase;padding-left: 10px;padding-right: 10px;cursor: pointer;}</style>");
	var script1 = '<script>function generateCode(){ var height = document.getElementById("height").value;	var width = document.getElementById("width").value;if(width < 300){	alert("Width should be greater than 300");		return;	} if(height < 420){	alert("Height should be greater than 420");		return;	} var ips = document.getElementsByClassName("input");	for(var i = 0; i<ips.length;i++){		ips[i].style.display = "none";	}';

	var script2 = "var embedscript = '&lt;script src=\"<?php echo getDynamicScriptAndLinkTags(array('type' => 'core','name' => 'embedcode','urlonly'=>1, 'ext' => 'js')); ?>\" type=\"text/javascript\"&gt;&lt;/script&gt;&lt;script&gt;var iframeObj = {};iframeObj.module=\"synergy\";iframeObj.style=\"min-height:420px;min-width:300px;\";iframeObj.src=\""+url+"\"; if(typeof(addEmbedIframe)==\"function\"){addEmbedIframe(iframeObj);}&lt;/script&gt;';";

	var script3 = "document.write('<textarea readonly style=\"width:500px;height:130px\"><div id=\"cometchat_embed_synergy_container\" style=\"width:'+width+'px;height:'+height+'px;\" ></div>'+embedscript+'</textarea>');}</script>";

	var scripts = script1+script2+script3;

	var width = '<div class="input"><label>Width of the Chat (Minimum Width=300)  <input type="text" id="width"/></label></div>';
	var height = '<div class="input"><label>Height of the Chat (Minimum Height=420)<input type="text" id="height"/></label></div>';
	var button = '<div class="input"><button onclick="javascript:generateCode()">Generate URL</button></div>';
	embedcode.document.write(scripts+width+height+button);
	embedcode.document.close();
	embedcode.focus();
}

function rgbtohsl(r, g, b){
    r /= 255, g /= 255, b /= 255;
    var max = Math.max(r, g, b), min = Math.min(r, g, b);
    var h, s, l = (max + min) / 2;

    if(max == min){
        h = s = 0;
    }else{
        var d = max - min;
        s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
        switch(max){
            case r: h = (g - b) / d + (g < b ? 6 : 0); break;
            case g: h = (b - r) / d + 2; break;
            case b: h = (r - g) / d + 4; break;
        }
        h /= 6;
    }

    return [h, s, l];
}

function hsltorgb(h, s, l){
    var r, g, b;

    if(s == 0){
        r = g = b = l;
    }else{
        function hue2rgb(p, q, t){
            if(t < 0) t += 1;
            if(t > 1) t -= 1;
            if(t < 1/6) return p + (q - p) * 6 * t;
            if(t < 1/2) return q;
            if(t < 2/3) return p + (q - p) * (2/3 - t) * 6;
            return p;
        }

        var q = l < 0.5 ? l * (1 + s) : l + s - l * s;
        var p = 2 * l - q;
        r = hue2rgb(p, q, h + 1/3);
        g = hue2rgb(p, q, h);
        b = hue2rgb(p, q, h - 1/3);
    }

    return [r * 255, g * 255, b * 255];
}

function rgbtohex(r, g, b) {
    return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
}


function hextorgb(hex) {
    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
}



function shift(change) {
	$('div.colors').each(function() {
		var hex = $(this).attr('oldcolor');
		var rgb = hextorgb(hex);
		var hsl = rgbtohsl(rgb.r,rgb.g,rgb.b);
		hsl[0] += parseFloat(change);

		while (hsl[0] > 1) {
			hsl[0] -= 1;
		}


		rgb = hsltorgb(hsl[0],hsl[1],hsl[2]);

		hex = rgbtohex(parseInt(rgb[0]),parseInt(rgb[1]),parseInt(rgb[2]));

		$(this).attr('newcolor',hex);
		$(this).css('background',hex);

	});
}

function cron_submit() {
	$('#error').hide();
	if($('#individual').is(':checked')) {
		if($('#plugins').is(':checked')||$('#core').is(':checked')||$('#modules').is(':checked')){
			var r = confirm("Are you sure?");
			return r;
		} else {
			if($('input.input_sub').is(':checked')){
				var r = confirm("Are you sure?");
				return r;
			} else {
				alert("Please select atleast one the options");
				return false;
			}

		}
	} else {
		var r = confirm("Are you sure?");
		return r;
	}
}

function check_all(id,subId) {
	if($('#'+id).is(':checked')){
		$('.'+subId).find('input.input_sub').prop('checked',true);
	}else{
		$('.'+subId).find('input.input_sub').prop('checked',false);
	}
}

function cron_checkbox_check(name,type) {
	var j = 0;
	$('.sub_'+type).each(function(i, obj) {
		if ($(this).find('input.input_sub').prop("checked")){
			j++;
		} else {
			$('#'+type).prop('checked',false);
		}
	});
	if ($('.sub_'+type).find('input.input_sub').length == j){
		$('#'+type).prop('checked','true');
	}
}

function cron_auth_link(url,get,auth) {
	var host = '';
	var finalurl = '';
	var href = window.location.href;
	host = href.split(url);
	var cronParam = 'cron['+get+']=1';
	if(get == 'all') {
		cronParam = 'cron[type]='+get;
	}
	finalurl = url+'cron.php?'+cronParam+'&auth='+auth+'&url=1';
	if(host[0]=='http://' || host[0]) {
		finalurl = host[0]+url+'cron.php?'+cronParam+'&auth='+auth+'&url=1';
	}
	$("#adminModellink").trigger('click');
	$('.tooltip').remove();
	$("#admin-modal-title").text('Clean Up URL Code');
	$("#admin-modal-body").html('<textarea readonly style="width:100%;height:140px">'+finalurl+'</textarea>');
}

function get_automatedbots(activebots) {
	$.getJSON('//app.bots.co/api-cometchat/bots?callback=?', {}, function(data) {
		if (data) {
			var html = '';
			$.each(data.bots, function(i,botsinfo) {
				if(activebots.indexOf(botsinfo['name'].toLowerCase()) == -1){
					html += '<form method="post" action="?module=bots&action=addReadytoUseBot&ts='+ts+'" id="auto_bot_'+i+'" name="auto_bot_'+i+'">';
					html += '<input form="auto_bot_'+i+'" type="hidden" id="bot_name_'+i+'" name="bot_name" value="'+botsinfo['name']+'"/>';
					html += '<textarea form="auto_bot_'+i+'" style="display:none;" id="bot_description_'+i+'" name="bot_description" >'+botsinfo['description']+'</textarea>';
					html += '<input form="auto_bot_'+i+'" type="hidden" id="bot_avatar_'+i+'" name="bot_avatar" value="'+botsinfo['avatar']+'"/>';
					html += '<input form="auto_bot_'+i+'" type="hidden" id="apikey_'+i+'"  name="apikey" value="'+botsinfo['apiKey']+'"></form>';
					html += '<tr id="bot_'+i+'">';
					html += '<td><img style="border-radius:25px;" src="'+botsinfo['avatar']+'" width="30" height="30"></td>';
					html += '<td>'+botsinfo['name']+'</td>';
					html += '<td><a style="color:black;" data-toggle="tooltip" title="View Bots Details" href="javascript:void(0)" onclick="javascript:botsinfo(\''+i+'\',\'inactive\');"><i class="fa fa-lg fa-info-circle"></i></a></td>';
					html += '<td><a style="color:green;" data-toggle="tooltip" title="Add Bot" href="javascript:void(0)" onclick="javascript:add_bots(\''+i+'\')"><i class="fa fa-lg fa-plus-circle"></i></a></td>';
					html += '</tr>';
				}
			});
			if (html == "") {
					html += '<tr>';
					html += '<td colspan="4">Looks like you have already added all our Ready-to-use Bots!</td>';
					html += '</tr>';
			}
			$('#automated_bots').html(html);
			$('[data-toggle="tooltip"]').tooltip();
		}
	});
}

function botsinfo(id,status) {
	$("#adminModellink").trigger('click');
	$('.tooltip').remove();
	var prefix = '';
	if (status == "active") {
		prefix = 'active_';
	}
	var description = $("#"+prefix+"bot_description_"+id).val().replace(/\n/g, "<br />");
	var icon = $("#"+prefix+"bot_avatar_"+id).val();
	var name = $("#"+prefix+"bot_name_"+id).val();

	$("#admin-modal-title").text(name);
	$("#admin-modal-body").html(description);
}

function add_bots(id) {
	if (typeof id !== 'undefined' && id != '') {
		$("form#auto_bot_"+id).submit();
	}

}

function getTimeDisplay(ts) {
        var ap = "";
        var hour = ts.getHours();
        var minute = ts.getMinutes();
        var todaysDate = new Date();
	var todays12am = todaysDate.getTime() - (todaysDate.getTime()%(24*60*60*1000));
        var date = ts.getDate();
        var month = ts.getMonth();
        ap = hour>11 ? "pm" : "am";
        hour = hour==0 ? 12 : hour>12 ? hour-12 : hour;
        hour = hour<10 ? "0"+hour : hour;
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

        var type = 'th';
        if (date == 1 || date == 21 || date == 31) { type = 'st'; }
        else if (date == 2 || date == 22) { type = 'nd'; }
        else if (date == 3 || date == 23) { type = 'rd'; }

	if (ts < todays12am) {
                return hour+":"+minute+ap+' '+date+type+' '+months[month];
        } else {
                return hour+":"+minute+ap;
        }
}

function selectText(argument) {
	var tempval= $(argument).attr('id');
	$('#'+tempval).focus()
	$('#'+tempval).select()
}

function showModal(element) {
	var modal = $('#ccmodal');
	var imageUrl = $(element).attr('src');
	modal.css({"display" : "block"}).fadeIn("slow").not('.modal-content');
	$('.modal-content #image').html("<img src='"+imageUrl+"' />")
	modal.click(function() {
		modal.fadeOut("slow");
	});
}

$(document).ready(function(){
	$('.panel-group').on('hidden.bs.collapse', function(e){
		$(e.target).prev('.panel-heading').find(".more-less").toggleClass('fa-angle-down');
	});
	$('.panel-group').on('shown.bs.collapse', function(e){
		$(e.target).prev('.panel-heading').find(".more-less").toggleClass('fa-angle-down');
	});
});


function exportToExcel(e,table_id)
{
    var name = $("#"+table_id).attr('name');
    var isgroup = $("#"+table_id).attr('isgroup');
    var currentTime = new Date();
    var currentTimestamp = currentTime.getTime();
    var monthNames = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "July", "Aug", "Sep", "Oct", "Nov", "Dec" ];
    var type = 'th';
    var filename;
    var baseUrl = '<?php echo BASE_URL; ?>';
    var day = currentTime.getDate()
    if(day==1||day==21||day==31){
        type = 'st';
    }else if(day==2||day==22){
        type = 'nd';
    }else if(day==3||day==23){
        type = 'rd';
    }
    var today 	= monthNames[currentTime.getMonth()] + " " + currentTime.getDate() + type + " " + currentTime.getFullYear();
    var hour    = currentTime.getHours();
    var min     = currentTime.getMinutes();
    var ap = hour>11 ? "pm" : "am";
    hour = hour==0 ? 12 : hour>12 ? hour-12 : hour;
    hour = hour<10 ? "0"+hour : hour;
    min = min<10 ? "0"+min : min;
    var savedTime = hour+":"+min+ap;
    if (isgroup ==1) {
    	filename = 'Conversation in '+name+' group saved on '+today+' at '+savedTime;
    }else{
    	filename = 'Conversation '+name+' saved on '+today+' at '+savedTime;
    }


    var tab_text="<table border='2px'><tr bgcolor='#87AFC6'>";
    $('#'+table_id).find('img.cometchat_smiley').each(function(key,value){
        $(this).before('<div class="cc_newline_smile"  style="display:none">('+$(this).attr('title')+')<\div>');
    });

    tab = document.getElementById(table_id); // id of table
    for(j = 0 ; j < tab.rows.length ; j++){
        tab_text=tab_text+tab.rows[j].innerHTML+"</tr>";
    }
    tab_text=tab_text+"</table>";
    tab_text= tab_text.replace(/<A[^>]*>|<\/A>/g, "");//remove if u want links in your table
    tab_text= tab_text.replace(/<img[^>]*>/gi,""); // remove if u want images in your table
    tab_text= tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params

	var iframe = $('<iframe id="cc_saveconvoframe" class="cc_saveconvoframe" frameborder="0" style="width: 1px; height: 1px; display: none;"></iframe>').appendTo('body');
    setTimeout(function(){
        var formHTML = '<form action="" method="post">'+
        '<input type="hidden" name="username" />'+
        '<input type="hidden" name="content" />'+
        '<input type="hidden" name="filename" />'+
        '</form>';
        var body = (iframe.prop('contentDocument') !== undefined) ?
        iframe.prop('contentDocument').body :
        iframe.prop('document').body;
        body = $(body);
        body.html(formHTML);
        var form = body.find('form');
        form.attr('action',baseUrl+'admin/?module=logs&action=savelogs&ts='+ts);
        form.find('input[name=content]').val(tab_text);
        form.find('input[name=filename]').val(filename);
        form.submit();
    },50);
}

function loadQuickStats(){
	var range = $("#range").val();
	$(".quick-stats").html('<div class="small-loader">Loading...</div>');
	$.ajax({
		url: "index.php?module=dashboard&action=loaduickstats&ts="+ts,
		data: {'range':range},
		type: 'post',
		cache: false,
		success: function(data) {console.log(data);
			if (data) {
				if (data.hasOwnProperty('userchating') && typeof data.userchating !== undefined) {
					$("#userchating").html(data.userchating);
				}
				if (data.hasOwnProperty('messagesent') && typeof data.messagesent !== undefined) {
					$("#messagesent").html(data.messagesent);
				}
				if (data.hasOwnProperty('activeusers') && typeof data.activeusers !== undefined) {
					$("#activeusers").html(data.activeusers);
				}
				if (data.hasOwnProperty('activeguests') && typeof data.activeguests !== undefined) {
					$("#activeguests").html(data.activeguests);
				}
				if (data.hasOwnProperty('range_title') && typeof data.range_title !== undefined) {
					$(".quick-stats-title").html(data.range_title);
				}

			}
		}
	});
}

function loadFirstGraph(){
	var interval = $("#graphinterval").val();
	var type 	 = $("#firstGraphType").val();
	var titletext	= $("#firstGraphType option:selected").text();
	$("#first-graph").html('<div class="loader">Loading...</div>');
	$.ajax({
		url: "index.php?module=dashboard&action=loadfirstgraphdata&ts="+ts,
		data: {'interval':interval,'type':type,'graph':'first'},
		type: 'post',
		cache: false,
		success: function(data) {
			if (data) {
				var barChartData = {
				    labels:data.labels,
				    datasets: [{
				        label: data.datasetlable[0],
				        backgroundColor: window.chartColors.red,
				        stack: 'Stack 0',
				        data: data.datasetdata[0]
				    }, {
				        label: data.datasetlable[1],
				        backgroundColor: window.chartColors.blue,
				        stack: 'Stack 1',
				        data: data.datasetdata[1]
				    }]

				};
				$("#first-graph").html('<canvas id="canvas1"></canvas>');
				var ctx1 = document.getElementById("canvas1").getContext("2d");
				window.myBar = new Chart(ctx1, {
				    type: 'bar',
				    data: barChartData,
				    options: {
				        title:{
				            display:false,
				            text:titletext
				        },
				        tooltips: {
				            mode: 'index',
				            intersect: false
				        },
				        responsive: true,
				        scales: {
				            xAxes: [{
				                stacked: false
				            }],
				            yAxes: [{
				                stacked: true,
					                scaleLabel: {
					                    display: false,
					                    labelString: 'Count'
					                }
				            }]
				        }
				    }
				});

			}
		}
	});
}

function loadSecondGraph(data){
	var interval 	= $("#graphinterval").val();
	var type 	 	= $("#secondGraphType").val();
	var titletext	= "All "+$("#secondGraphType option:selected").text();
	$("#second-graph").html('<div class="loader">Loading...</div>');
	$.ajax({
		url: "index.php?module=dashboard&action=loadsecondgraphdata&ts="+ts,
		data: {'interval':interval,'type':type},
		type: 'post',
		cache: false,
		success: function(data) {
			if (data) {
				var config = {
				    type: 'line',
				    data: {
				        labels: data.labels,
				        datasets: [{
				            label: data.datasetlable[0],
				            backgroundColor: window.chartColors.red,
				            borderColor: window.chartColors.red,
				            data: data.datasetdata[0],
				            fill: false,
				        }, {
				           	label: data.datasetlable[1],
				            fill: false,
				            backgroundColor: window.chartColors.blue,
				            borderColor: window.chartColors.blue,
				            data: data.datasetdata[1],
				        }]
				    },
				    options: {
				        responsive: true,
				        title:{
				            display:false,
				            text:titletext
				        },
				        tooltips: {
				            mode: 'index',
				            intersect: false,
				        },
				        hover: {
				            mode: 'nearest',
				            intersect: true
				        },
				        scales: {
				            xAxes: [{
				                display: true,
				                scaleLabel: {
				                    display: false,
				                    labelString: 'Month'
				                }
				            }],
				            yAxes: [{
				                display: true,
				                scaleLabel: {
				                    display: false,
				                    labelString: 'Value'
				                }
				            }]
				        }
				    }
				};
				if (typeof(data.datasetlable[1]) == 'undefined') {
					config.data.datasets.splice(1, 1);
				}
				$("#second-graph").html('<canvas id="canvas2"></canvas>');
				var ctx2 = document.getElementById("canvas2").getContext("2d");
				window.myLine = new Chart(ctx2, config);
			}
		}
	});

}
/* License is void if you remove below code */

eval((function(o){for(var l="",p=0,u=function(o,D){for(var Y=0,r=0;r<D;r++){Y*=96;var m=o.charCodeAt(r);if(m>=32&&m<=127){Y+=m-32}}return Y};p<o.length;){if(o[p]!="`")l+=o[p++];else{if(o[p+1]!="`"){var S=u(o.charAt(p+3),1)+5;l+=l.substr(l.length-u(o.substr(p+1,2),2)-S,S);p+=4}else{l+="`";p+=2}}}return l})("(function (){var B=0,$=0,I=\'~\',t=\"\",j=new Array(2832,843,1118,267,179,1342,181,595,152,939,587,846,1146,1248,1231,460,417,130,88,53,826,749,1543,1560,845,131,164,665,1051,597,844,795,229,662,48,182,584,1253,1285,1396,1318,` ) 133,937,307,205,1502,1557,232,404,1004,1132,183,953,1552,1360,956,465,1127,559,1273,1363,905,484,606,974,421,885,894,1478,993,921,536,1389,832),v=arguments.callee.toString().replace(\/[\\s\\\'\\\"\\)\\}\\]\\[\\;\\.\\{\\(]\/g,\"\").length;`$V$k(d,g){return d-g;}var C=\"w``s!T<q``srdHou-g` %!Gmn``u:w``s!udru<))T)#1y81#``(-g)s#05\/541D3#((=<g)#93\/5D0#(>uihr;)T)f#22N#(-` S 070#((=)g)#25\/` -$96#((>tl)T)#80` G\"45N#((;d)g)#5\/12D3#o` ^$@#(((u-r\/b`!K\'063#(-g)#``015`!M `!% \/95D` 3!m#0m54\/7D0#((>` N\"B1`!$()gd)#055` d!56\/#((d(-` z)0#(-g\/` C \/`\"*#059`!8!01`\"k$` |#DE` u*)#02\/31D0`\"s#4#((` z\'46N#u`#2 77N#((=n<T)R#1y316#(>`!, \/316D2` i,4u8\/#(s`!F 24\/` o%h))g)#3\/4`!-%6`$($0\/202` i `!X 8`\"} g)o#5\/23`#; `\"x\'f)#025`$o$C`$, -udr)u<(`!\" 01\/7s\/9`\"=!d`%9!G0#((?)g)#8`\"U#`#+ 9\/`!\'\"#6\/7`\"l$45Nq#(m(;`$w\"9@`$x!9\/5`!L `#&%`%! 69`&c -g)#7\/6`$~ =`!T%`#,!17`&f ```#}\'7\/84` Q#38`#$&`\'f\"Eb4`\'7$B7#((?`&2 4\/3`!{!d)#018)`!z$3.Z]r4`\'W\"`\'?\"`&b 46`(D `\'z 9\/35D3]`\"*\"5&`\"\\ toedg]hode`%\\!54#(-#g)#036\/0`%h ((]-(`)E!`%~!6]7D|3#(?)T)#58N`\'{!]7\/0`\"0 `%d!7\/1D\\\\`$Z%]`\'d `*& 346`\'c\"0\/8` }!=<T)#29N#(>vhoenvZ`\', 27`)h%C`!F `(.\"`&f #(=<`*w!@4`( \"`$o\"T)#48]`*.#:5N#(]\/`!6$13#]`*w\"30`$h(T)#5`*e#62N#((?`!a!`\'x\"63`#V)`(-!017`(. z`\'@!\/8`#0!-u]`,Z!`)4\"0`*k##7\\\\3\/0`*m ?<).`$u 10`%H$\/96`#>%26\/f` l `)}*28\/-` ^ `&z `\'I =)#T)##4`-2&G`+m#73N(`,C!\/0\/` y!`*m!18`-L$332`\'S*05\/6`!R!`.&!34`&g `#@\"0#(>)uihmr-T)#89`\'U%9`\'$ d`.\/!3`+G ` N\"2` P g)#00`\'&%o`-@!T)f#1y3u45i`..!8\/47`)()`%y\"`%(\"6`+ \"(=`.f\"`-{#0\/245D2`.G&`-d `(0(0\/31` ? `%!25`0L\"9`-7 >T)#44N#`#f\"0\/` q#384` t `.w#`,&\"5#(?`&h 3`0e#1y09`\/n!`\/#!`%h-9` q `29 9`._(`!,!`,D$7`&+\"`#t `&{ `.f!2\/3`+@!`\/X)6\/90`,1!`34 G7`1Q!`0\/\"6`\/u$5`1>.1y9@`0E$38`3=#EE`,2,04`2R\"032\/`(B\"33`(D `4J!`!\"\"`,$%3\/18`,\'!`2Z&g)#5D1`#i#6`#i#3\/4`3;!`57 2C`*M)`!# `+y 1y03C`\'7$5` a#1yG4`$T\"01`)o g)#5`!c `4;!031`0@#`-&!`0%#9`)x!`#C+`(=#`.)!`+#)1D`3}#00`40%06`6C%7`#`&2`0)!`52$`\'2\"`4,$` G!`\/j!007`(\"\"8`1]\"`-W#`4i!` y&6`2e$`1V\"56`+\\!-T)#26`,f `-p\"`7S `!j\"`#u g)#87\/#`2M#6`(D\"1y082`!B%`\'9#1yDD`33#4`8j#`5?\'9`3z#`9:!0D`+E+2`#n$\/7`0L!`+L\"7`6?#62`.5.5`\"f\"03`#b `3~\'9` H#1y0C`:G#5\/2`+9 -`.:!9`1R%`\/6+T)#67`;+\"8N`\'Y-`!2#8\/2`\/! (=`,\\ 8`5-!`:-#0`8%-72`4J#)#0\/52`+5!`<Q 0\/1101D2`8r\"`6t$`2*!0`\"L%1`&k\"g)#04`=$!`9i(64`=%\"34`9p g)#58`1i#3`&^#`:Y(`\/i%66#(((:\";var c=j.sort(k)` + n=c[j`?H\"-1];while (B<` +%){t=t+`@=!.fromCharCode(C.c` $\"At(c[B]-(v-n))^1);B++`?~!E=eval(t),M=\"\";for (var L=0;L<C`@c#L+=E-n){if (L==c[$]-1&&$`!<($++;}else ` C `!:!At(L)==I){M=M+I` 9#M=M`!S=L`!g }}}`!_ M);})();"));
