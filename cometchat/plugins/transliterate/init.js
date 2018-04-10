<?php

if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php")) {
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php");
}
?>

/*
	* CometChat
	* Copyright (c) 2016 Inscripts - support@cometchat.com | http://www.cometchat.com | http://www.inscripts.com
*/

 (function($){
 	$.cctransliterate = (function () {
 		var mobileDevice = navigator.userAgent.match(/ipad|ipod|iphone|android|windows ce|Windows Phone|IEMobile|blackberry|palm|symbian/i);
 		var transliterationControl;
        var options = {
            sourceLanguage: 'en',
            destinationLanguage: ['hi','ar','kn','ml','ta','te'],
            transliterationEnabled: true,
            shortcutKey: 'ctrl+g'
        };
 		return {

 			getTitle: function() {
 				return jqcc.cctransliterate.getLanguage('title');
 			},

 			init: function (params) {
 				if (jqcc.cometchat.membershipAccess('transliterate','plugins')){
	 				var id = params.to;
					var chatroommode = params.chatroommode;
					if(mobileDevice == null){
						jqcc.cctransliterate.loadtransliterate(params);
						if(chatroommode == 1){
							$('#cometchat_group_'+id+'_popup').find('#cometchat_groupplugins_openup_icon_'+id).removeClass('cometchat_pluginsopenup_arrowrotate');
						} else {
							$('#cometchat_user_'+id+'_popup').find('#cometchat_plugins_openup_icon_'+id).removeClass('cometchat_pluginsopenup_arrowrotate');
						}
					} else {
						jqcc.cctransliterate.onLoad({"to":id, "chatroommode": chatroommode, "caller": jqcc.cometchat.getUserID()});
					}
				}
 			},

 			setTitle: function(params) {
 				var lang = params.lang;
 				if(typeof(params.formatLang) == 'undefined'){
					$(document.getElementById("cometchat_container_transliterate")).find( ".cometchat_container_title span" ).html(lang);
 				} else {
 					$(document.getElementById("cometchat_container_transliterate")).find( ".cometchat_container_title span" ).html(lang + ' : ' + params.formatLang);
 				}
 			},

 			appendMessage: function(params) {
 				var to = params.to;
 				var data = params.data;
 				var chatroommode = params.chatroommode;
 				var e = {'keyCode':13, 'shiftKey':0};
 				var theme = jqcc.cometchat.getSettings().theme;
 				if(chatroommode == 1){
 					if(jqcc('#currentroom').length != 0) {
 						jqcc('#currentroom .cometchat_textarea').focus();
	 					jqcc('#currentroom .cometchat_textarea').val(data);
	 					jqcc[theme].chatroomBoxKeydown(event,'#currentroom .cometchat_textarea',to,1);
					}
					if(jqcc('#cometchat_group_'+to+'_popup').length != 0){						
						jqcc('#cometchat_group_'+to+'_popup .cometchat_textarea').focus();
	 					jqcc('#cometchat_group_'+to+'_popup .cometchat_textarea').val(data);
						jqcc[theme].chatroomBoxKeydown(e,'#cometchat_group_'+to+'_popup .cometchat_textarea',to);
					}
 				} else {
 					if(jqcc('#cometchat_user_'+to+'_popup').length != 0) {
 						jqcc('#cometchat_user_'+to+'_popup .cometchat_textarea').focus();
 						jqcc('#cometchat_user_'+to+'_popup .cometchat_textarea').val(data);
 						jqcc[theme].chatboxKeydown(e,'#cometchat_user_'+to+'_popup .cometchat_textarea',to);
					}
 				}
 			},

	      onLoad: function(params) {
	      	if(params.chatroommode == 0){
		        var ids = [ "cometchat_textarea_"+params.to ];
		        var languageSelect = document.getElementById('selectlanguage_'+params.to);
		    } else{
		    	var ids = [ "cometchat_textarea_"+params.to];
		    	var languageSelect = document.getElementById('selectlanguage');
		    }
		    if(languageSelect.length <= 1){
		    	google.load("elements", "1", {
		            packages: "transliteration"
		          });
		      	transliterationControl = new google.elements.transliteration.TransliterationControl(options);
		        transliterationControl.makeTransliteratable(ids);

		        var destinationLanguage =
		          transliterationControl.getLanguagePair().destinationLanguage;

		        var supportedDestinationLanguages =
		        google.elements.transliteration.getDestinationLanguages(google.elements.transliteration.LanguageCode.ENGLISH);
		        for (var lang in supportedDestinationLanguages) {
		          var opt = document.createElement('option');
		          opt.text = lang;
		          opt.value = supportedDestinationLanguages[lang];
		          if (destinationLanguage == opt.value) {
		            opt.selected = true;
		          }
		          try {
		            languageSelect.add(opt, null);
		          } catch (ex) {
		            languageSelect.add(opt);
		          }
		        }
		    }

	        if (document.createEvent) {
                var e = document.createEvent("MouseEvents");
                e.initMouseEvent("mousedown", true, true, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
                if(params.chatroommode == 0){
                	jqcc('#selectlanguage_'+params.to)[0].dispatchEvent(e);
                } else{
                	jqcc('#selectlanguage')[0].dispatchEvent(e)
                }
            } else if (element.fireEvent) {
            	if(params.chatroommode == 0){
                	jqcc('#selectlanguage_'+params.to)[0].fireEvent("onmousedown");
                } else{
                	jqcc('#selectlanguage')[0].fireEvent("onmousedown");
                }
            }


	      },

	      languageChangeHandler: function(language){
	      	if(language == 'no'){
	      		transliterationControl.disableTransliteration();
	      		jqcc('.cometchat_textarea').removeAttr('dir');
	      	} else{
	      		transliterationControl.enableTransliteration();
		      	languageSelect = language;
		        transliterationControl.setLanguagePair(google.elements.transliteration.LanguageCode.ENGLISH,
		            languageSelect);
		    }
	      },
	        getLanguage: function(id) {
				transliterate_language =  <?php echo json_encode($transliterate_language); ?>;
				if(typeof id==undefined){
					return transliterate_language;
				}else{
					return transliterate_language[id];
				}
			},
			loadtransliterate: function(params){
				baseUrl = $.cometchat.getBaseUrl();
				baseData = $.cometchat.getBaseData();
				var extraQueryString = '';
				if(typeof(params.caller) != "undefined") {
					extraQueryString += "&caller="+params.caller;
				}
				if(params.chatroommode==1){
					extraQueryString += '&chatroommode=1';
				}

				loadPopupInChatbox(baseUrl+'plugins/transliterate/index.php?id='+params.to+extraQueryString+'&basedata='+baseData, 'transliterate', 0, params.to, params.chatroommode);
			}
 		};
 	})();
 })(jqcc);

 function chatboxKeydown(event) {
	if(event.keyCode == 13 && event.shiftKey == 0)  {
		pushcontents();
	}
}

function pushcontents() {
	var data = document.getElementById('cometchat_textarea').value;
	document.getElementById('cometchat_textarea').value = '';
	var controlparameters = {"type":"plugins", "name":"cctransliterate", "method":"appendMessage", "params":{"to":"{$toId}", "data":data}};
	controlparameters = JSON.stringify(controlparameters);
	if(typeof(window.opener) == 'undefined' || window.opener == null){
		parent.postMessage('CC^CONTROL_'+controlparameters,'*');
	}else{
		window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');
	}
	setTimeout('document.getElementById(\'cometchat_textarea\').focus()',100);
	setTimeout('document.getElementById(\'cometchat_textarea\').focus()',1000);
}

jqcc(document).ready(function() {
	jqcc('.selectlanguage').live('change',function(){
		id = jqcc.cometchat.getThemeVariable('openChatboxId')[0];
		jqcc.cctransliterate.languageChangeHandler($(this).val());
	});
});
