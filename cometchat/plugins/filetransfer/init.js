<?php
include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."config.php");
if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php")) {
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php");
}
?>

/*
 * CometChat
 * Copyright (c) 2016 Inscripts - support@cometchat.com | http://www.cometchat.com | http://www.inscripts.com
*/

(function($){
	$.ccfiletransfer = (function() {
		var request = {};
		var count = 0;
		var calleeAPI = "<?php echo 'cc'.$layout; ?>";

		return {
			getTitle: function() {
				return jqcc.ccfiletransfer.getLanguage('title');
			},
			init: function (params) {
				var id = params.to;
				var chatroommode = params.chatroommode;
				var roomname = params.roomname;
				var caller = '';
				var mobileDevice = navigator.userAgent.match(/ipad|ipod|iphone|android|windows ce|Windows Phone|blackberry|palm|symbian/i);
				if (!(jqcc.cometchat.membershipAccess('filetransfer','plugins'))){
					return;
				}
				if(typeof(params.caller) != "undefined") {
					caller = params.caller;
				}
				var windowMode = 0;
				if(typeof(params.windowMode) == "undefined") {
					windowMode = 0;
				} else {
					windowMode = 1;
				}
				if(chatroommode == 1 && mobileDevice == null) {
					var baseUrl = $.cometchat.getBaseUrl();
					var basedata = $.cometchat.getBaseData();

					if($("#currentroom").length){
						$("#currentroom").find('#cometchat_uploadfile_'+id).click();
					} else {
						var cometchat_group_popup = $("#cometchat_group_"+id+"_popup");
						cometchat_group_popup.find('#cometchat_uploadfile_'+id).click();
					}
				} else if(chatroommode == 0 && mobileDevice == null){
					var baseUrl = $.cometchat.getBaseUrl();
					var basedata = $.cometchat.getBaseData();
					var cometchat_user_popup = $("#cometchat_user_"+id+"_popup");
					cometchat_user_popup.find('#cometchat_uploadfile_'+id).click();
					/*
					cometchat_user_popup.find(".cometchat_tabcontent").append("<input id='cometchat_uploadfile_"+id+"' type='file' class='file' name='Filedata' style='display:none;' />");

					if(!cometchat_user_popup.find('cometchat_uploadfile_'+id).length) {
						var x = document.createElement("INPUT");
					    x.setAttribute("type", "file");
					    x.setAttribute("class", "cometchat_fileupload");
					    x.setAttribute("id", 'cometchat_uploadfile_'+id);
					    x.setAttribute("name", "Filedata");
					    x.setAttribute("multiple", "true");
					    cometchat_user_popup.find(".cometchat_tabcontent").append(x);
					    x.addEventListener("change", jqcc.ccfiletransfer.FileSelectHandler(cometchat_user_popup.find('.cometchat_tabcontent'),id,chatroommode), false);
					}*/
				} else if(chatroommode == 0 && mobileDevice){
					var baseUrl = $.cometchat.getBaseUrl();
					var baseData = $.cometchat.getBaseData();
					if(caller != ''){
						var cometchat_user_popup = $('#cometchat_synergy_iframe').contents().find("#cometchat_user_"+id+"_popup");
					}else{
						var cometchat_user_popup = $("#cometchat_user_"+id+"_popup");
					}
					cometchat_user_popup.find('#cometchat_uploadfile_'+id).click();
				} else if(chatroommode == 1 && mobileDevice){
					var baseUrl = $.cometchat.getBaseUrl();
					var basedata = $.cometchat.getBaseData();
					if($("#currentroom").length || $('#cometchat_synergy_iframe').contents().find('#currentroom').length){
						if(caller != ''){
							$('#cometchat_synergy_iframe').contents().find("#currentroom").find('#cometchat_uploadfile_'+id).click();
							$("#currentroom").find('#cometchat_uploadfile_'+id).click();
						}else{
							$("#currentroom").find('#cometchat_uploadfile_'+id).click();
						}
					} else {
						var cometchat_group_popup = $("#cometchat_group_"+id+"_popup");
						cometchat_group_popup.find('#cometchat_uploadfile_'+id).click();
					}
				}
			},
			FileSelectHandler: function (div,id,chatroommode) {
				return function (e) {
			    jqcc.ccfiletransfer.FileDragHover(e,div,id);

			    /*fetch FileList object*/
			    var files = e.target.files || e.dataTransfer.files;
			    /*process all File objects*/
			    for (var i = 0, f; f = files[i]; i++) {
			    	jqcc.ccfiletransfer.fileAjax(div,f,id,chatroommode);
			    }
			  }
			},
			FileDragHover: function (div,id,enter) {
				return function (e) {
					var baseUrl = $.cometchat.getBaseUrl();
					if(enter == 1){
						if(!div.find("#cometchat_file_drag_"+id).length){
							div.append("<div class='cometchat_file_drag' id='cometchat_file_drag_"+id+"'>"+jqcc.ccfiletransfer.getLanguage('dropfiles')+"</div>");
							div.find('.cometchat_tabcontenttext').css('box-shadow','rgba(67, 159, 224, 0.3) 18px 0px 100px inset');
						}
					}else{
						$("#cometchat_file_drag_"+id).remove();
						div.find('.cometchat_tabcontenttext').css('box-shadow','none');
					}
					/*e.stopPropagation();
					e.preventDefault();*/
				}
			},
			createProgressHandler: function (i) {
				return function (e) {
				    var _progress = document.getElementById('progress_'+i);
				    if(_progress)
				    _progress.style.width = Math.ceil(e.loaded/e.total * 100) + '%';
				}
			},
			handleResponse: function (request,id) {
				var response;
				if(request.readyState == 4){
				    try {
				        $("#progress_bar_"+request.count).html("<div class='progress_result'>"+jqcc.ccfiletransfer.getLanguage('uploaded')+"<div>");

				        setTimeout(function(){
				        	$("#cometchat_progresscontainer_"+id).remove();
				        	$("#cometchat_file_drag_"+id).remove();
				        	$('.cometchat_tabcontenttext').css('box-shadow','none');
						},800);
				    } catch (e){
				        var resp = {
				            status: 'error',
				            data: 'Unknown error occurred: [' + request.responseText + ']'
				        };
				    }
				}
			},
			abortUploading: function (id) {
				var baseUrl = $.cometchat.getBaseUrl();
			  request['request_'+id].abort();/*ajax abort code*/
			  $("#progress_bar_"+id).html('<div class="progress_result">'+jqcc.ccfiletransfer.getLanguage('upload_stopped')+'<div>');
			},
			progessResponse: function (e,_progress) {
				_progress.style.width = Math.ceil(e.loaded/e.total * 100) + '%';
			},
			fileAjax: function (div,data,id,chatroommode) {
				var baseUrl = $.cometchat.getBaseUrl();
				var basedata = $.cometchat.getBaseData();

			    count += 1;
			    var form_data = new FormData();
			    form_data.append('Filedata', data);
			    form_data.append('to', id);
			    form_data.append('basedata', basedata);
			    form_data.append('chatroommode', chatroommode);
			    /*$(div).append("<div class='cometchat_progresscontainer' id='cometchat_progresscontainer_"+id+"_"+count+"'><div class='progress_text' id ='progress_text"+count+"'>"+data['name']+"</div><div class='progress_bar' id='progress_bar_"+count+"'><div class='close' id='close_"+count+"' onclick='jqcc.ccfiletransfer.abortUploading("+count+");'>×</div><div class='progress_outer' id='progress_outer_"+count+"'><div id='progress_"+count+"' class='progress'></div></div></div></div>");*/

			    if(calleeAPI == 'ccembedded'){
			    	/*UNCOMMENT FOR PROGRESS BAR*/
			    	/*if(!div.find('#cometchat_progresscontainer_'+id).length){
			    		div.append("<div class='cometchat_progresscontainer' id='cometchat_progresscontainer_"+id+"' ></div>");
			    	}*/
			    	/*div.find("#cometchat_progresscontainer_"+id).append("<div class='progressbar_container'><div class='progress_text' id ='progress_text"+count+"'>"+data['name']+"</div><div class='progress_bar' id='progress_bar_"+count+"'><div class='cometchat_progressbar_close' id='close_"+count+"' onclick='jqcc.ccfiletransfer.abortUploading("+count+");'>×</div><div class='progress_outer' id='progress_outer_"+count+"'><div id='progress_"+count+"' class='progress'></div></div></div></div>");*/
			    }else{
			    	/*$(div).find('div.cometchat_tabcontentinput').before("<div class='cometchat_progresscontainer' id='cometchat_progresscontainer_"+id+"'><div class='progress_text' id ='progress_text"+count+"'>"+data['name']+"</div><div class='progress_bar' id='progress_bar_"+count+"'><div class='cometchat_progressbar_close' id='close_"+count+"' onclick='jqcc.ccfiletransfer.abortUploading("+count+");'>×</div><div class='progress_outer' id='progress_outer_"+count+"'><div id='progress_"+count+"' class='progress'></div></div></div></div>");*/
			    }
			    /*$(div).find('div.cometchat_tabcontentinput').before("<div class='cometchat_progresscontainer' id='cometchat_progresscontainer_"+id+"'><div class='progress_text' id ='progress_text"+count+"'>"+data['name']+"</div><div class='progress_bar' id='progress_bar_"+count+"'><div class='close' id='close_"+count+"' onclick='jqcc.ccfiletransfer.abortUploading("+count+");'>×</div><div class='progress_outer' id='progress_outer_"+count+"'><div id='progress_"+count+"' class='progress'></div></div></div></div>");*/
			    var  settings = jqcc.cometchat.getSettings();

			    request['request_'+count] = {};
			    request['request_'+count]['count'] = count;
			    request['request_'+count] = new XMLHttpRequest();
			    request['request_'+count].count=count;
			    request['request_'+count].onreadystatechange = function () {jqcc.ccfiletransfer.handleResponse(this,id); };
			    request['request_'+count].upload.addEventListener("progress", jqcc.ccfiletransfer.createProgressHandler(count), false);
			    request['request_'+count].open('POST', baseUrl+'plugins/filetransfer/upload.php?cookie_'+settings.cookiePrefix+'guest'+'='+$.cookie(settings.cookiePrefix+'guest'));
			    request['request_'+count].send(form_data);
			},
			getLanguage: function(id) {
				filetransfer_language =  <?php echo json_encode($filetransfer_language); ?>;
				if(typeof id==undefined){
					return filetransfer_language;
				}else{
					return filetransfer_language[id];
				}
			}
		};
	})();
})(jqcc);
