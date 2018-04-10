/*
 * CometChat
 * Copyright (c) 2016 Inscripts - support@cometchat.com | http://www.cometchat.com | http://www.inscripts.com
 */
 <?php $callbackfn = ''; if (!empty($_GET['callbackfn'])) { $callbackfn = $_GET['callbackfn']; }?>

; if (!Object.keys) Object.keys = function(o) {if (o !== Object(o))throw new TypeError('Object.keys called on a non-object');var k=[],p;for (p in o) if(Object.prototype.hasOwnProperty.call(o,p)) k.push(p);return k;};

(function($){
	$.cometchat = $.cometchat||function(){
		var baseUrl = '<?php echo BASE_URL;?>',
			role = '',
			staticCDNUrl = '<?php echo STATIC_CDN_URL;?>',
			sendajax = true,
			broadcastData = {},
			sendbroadcastinterval = 0,
			transport = '<?php echo TRANSPORT;?>',
			webrtcplugins = ['audiovideochat', 'audiochat', 'broadcast', 'screenshare'];
		<?php echo $jssettings; ?>
		<?php echo $mobileappdetails; ?>
		function getURLParameter (name,url) {
			if(typeof(url)=="undefined"){
				url = location.search
			}
			return decodeURI((RegExp(name + '=' + '(.+?)(&|$)').exec(url)||[,null])[1]);
		}
		var basedataFromCookieOrQueryString = (getURLParameter('basedata') !== "null" && getURLParameter('basedata') !== null) ?getURLParameter('basedata') : $.cookie(settings.cookiePrefix+'data');
		var calleeAPI = settings.theme,
			ccvariable = {
				documentTitle: document.title,
				callbackfn: '<?php echo $callbackfn;?>',
				crossDomain: '<?php echo CROSS_DOMAIN;?>',
				baseData: settings['ccauth']['enabled']==0?decodeURIComponent(basedataFromCookieOrQueryString):0,
				mobileDevice: navigator.userAgent.match(/ipad|ipod|iphone|android|windows ce|Windows Phone|IEMobile|blackberry|palm|symbian/i),
				prependLimit: (typeof(settings['prependLimit'])!=="undefined")?settings['prependLimit']:0,
				specialChars: /([^\x00-\x80]+)|([&][#])+/,
				windowFocus: true,
				lastOnlineNumber: 0,
				localmessageid: 0,
				newMessages: 0,
				idleFlag: 0,
				hasSearchbox: 0,
				ccmobileauth: 0 ,
				isMini: 0,
				userid: 0,
				cometid: '',
				showAvatar: 1,
				displayname: '',
				currentStatus:null,
				statusmessage: '',
				userActive: 0,
				loggedout: 0,
				offline: 0,
				todaysDate: new Date(),
				timedifference: 0,
				externalVars: {
					lastseensetting: 0,
					messagereceiptsetting: 0,
					activeChatboxIds: ''
				},
				sendVars: {},
				sessionVars: {},
				internalVars: {},
				updateSessionVars: 0,
				dataMethod: 'post',
				dataTimeout: '10000',
				initialized: 0,
				timestamp: 0,
				heartbeatTimer:null,
				heartbeatTime: settings.minHeartbeat,
				heartbeatCount: 1,
				runHeartbeat: 1,
				buddyListHash:null,
				buddylistName: {},
				buddylistMessage: {},
				buddylistStatus: {},
				buddylistAvatar: {},
				buddylistLink: {},
				buddylistIsDevice: {},
				buddylistChannelHash: {},
				buddylistLastseen: {},
				buddylistLastseensetting: {},
				buddylistReadReceiptSetting: {},
				buddylistUnreadMessageCount: {},
				botListHash:null,
				botlistName: {},
				botlistAvatar: {},
				botlistDescription: {},
				botlistApikey: {},
				openChatboxId: [],
				openedChatbox: '',
				chatroomOpen: '',
				trayOpen: '',
				chatBoxesOrder: {},
				chatBoxOrder: [],
				trying: {},
				desktopNotification: {},
				lastmessagereadstatus: {},
				loggedinusertype : 'loginuser',
				registeredCallbacks : {},
				lastmessageid : {},
        		dockedAlignment : 'right',
			};
		ccvariable.currentTime = ccvariable.idleTime = Math.floor(ccvariable.todaysDate.getTime()/1000);
		ccvariable.todays12am = ccvariable.currentTime -(ccvariable.currentTime%(24*60*60*1000));

		if(typeof (ccvariable.callbackfn)!='undefined'&&ccvariable.callbackfn!=''&&ccvariable.callbackfn!='desktop'){
			calleeAPI = ccvariable.callbackfn;
		}else if( ccvariable.mobileDevice&&settings.disableForMobileDevices&&calleeAPI!='embedded' || ccvariable.mobileDevice&&settings.disableForMobileDevices&&calleeAPI!='embedded'){
			calleeAPI = ccvariable.callbackfn = 'ccmobiletab';
		}
		ccvariable.externalVars["callbackfn"] = ccvariable.callbackfn;

		$.ajaxSetup({scriptCharset: "utf-8", cache: "false"});
		function setWindowFocus(){
			if(!ccvariable.windowFocus){
				jqcc.each( ccvariable.openChatboxId, function( key, value ) {
					if(typeof ccvariable.lastmessagereadstatus[value] != 'undefined' && ccvariable.lastmessagereadstatus[value] == 0){
						var messageid = ccvariable.lastmessageid[value];
						var message = {"id": messageid, "from": value, "self": 0, "old": 0};
						if(ccvariable.currentStatus != 'invisible'){
							jqcc.cometchat.sendReceipt(message, 'readMessageNotify');
						}
						ccvariable.lastmessagereadstatus[value] = 1;
					}
				});
			}
			ccvariable.windowFocus = true;
		}
		$(window).focus(function(){
			setWindowFocus();
			ccvariable.isMini = 0;
			if(settings.desktopNotifications==1){
				for(x in  ccvariable.desktopNotification){
					for(y in  ccvariable.desktopNotification[x]){
						ccvariable.desktopNotification[x][y].close();
					}
				}
			}
			ccvariable.desktopNotification = {};
		});
		$(window).blur(function(){
			ccvariable.windowFocus = false;
			ccvariable.isMini = 1;
		});
		$(window).on('mouseenter',setWindowFocus);
		$(window).on('mouseleave', function() {
			ccvariable.windowFocus = false;
		});
		function userClickId(id){
			if(typeof (jqcc[calleeAPI].createChatbox)!=='undefined'){
				jqcc[calleeAPI].createChatbox(id, ccvariable.buddylistName[id], ccvariable.buddylistStatus[id], ccvariable.buddylistMessage[id], ccvariable.buddylistAvatar[id], ccvariable.buddylistLink[id], ccvariable.buddylistIsDevice[id],1,null);
			}
		};
		function branded(){
			$("body").append('<div id="cc_power" style="display:none">Powered By <a href="https://www.cometchat.com">CometChat</a></div>');
			language[1] = 'Powered By <a href="https://www.cometchat.com">CometChat</a>';
			setTimeout(function(){
				if (settings.theme == "embedded" || settings.theme == "synergy") {
					$('#cometchat_leftbar').append('<div class="right_footer"><a target="_blank" title="Powered by CometChat" href="https://www.cometchat.com/?r=LB1">Powered by CometChat</a></div>');
				} else {
					var userstabpopup = jqcc('#cometchat_userstab_popup');
					$('#cometchat_userstab_popup').find('div.cometchat_tabcontent').append('<div class="right_footer"><a target="_blank" title="Powered by CometChat" href="https://www.cometchat.com/?r=LB1">Powered by CometChat</a></div>');
					$('#cometchat_userstab_popup').find('div#cometchat_userscontent').css('height',"270px");
					if(jqcc.cometchat.getThemeVariable('hasSearchbox')){
						var chatlistheight = '240px';
						$('#cometchat_userstab_popup').find('div#cometchat_userscontent').css('height',chatlistheight);
					 } else {
						var chatlistheight = '270px';
					 }
					userstabpopup.find('#cometchat_userscontent #cometchat_userslist > div').css({'height': chatlistheight});
					userstabpopup.find('#cometchat_userscontent #cometchat_groupslist_content > div').css({'height': chatlistheight});
					userstabpopup.find('#cometchat_userscontent .slimScrollDiv > div').css({'height': chatlistheight}).next('div').css({'height': chatlistheight});
				}
			},100)
		};
		function preinitialize(){
			if((typeof(cc_synergy_enabled)!="undefined" && cc_synergy_enabled == 1) || (typeof(cc_embedded_enabled)!="undefined" && cc_embedded_enabled == 1) ){
				return;
			}
			if(jqcc.cometchat.getUserAgent()[0]=="MSIE" && parseInt(jqcc.cometchat.getUserAgent()[1])<9){
				settings.windowFavicon=0;
			};
			if(ccvariable.callbackfn==''&&settings.hideBarCheck==1&&settings.theme=='docked'&&$.cookie(settings.cookiePrefix+"loggedin")!=1){
				$.ajax({
					url: baseUrl+"cometchat_check.php",
					data: {'init': '1', basedata: ccvariable.baseData},
					dataType: 'jsonp',
					type: ccvariable.dataMethod,
					timeout: ccvariable.dataTimeout,
					success: setPreInitVars
				});
			}else{
				setPreInitVars(1);
			}
			function setPreInitVars(data){		  /*child function of preinitialize*/
				if(data!='0'){
					if(typeof(jqcc[calleeAPI]) == 'undefined'){
						return;
					}
					$.cookie(settings.cookiePrefix+"loggedin", '1', {path: '/'});
					if(typeof (jqcc[calleeAPI].initialize)!=='undefined'){
						jqcc[calleeAPI].initialize();
					}else if(ccvariable.callbackfn!=''&&typeof (jqcc[calleeAPI].init())=='function'){
						jqcc[calleeAPI].init();
					}
					ccvariable.externalVars["buddylist"] = '1';
					ccvariable.externalVars["initialize"] = '1';
					jqcc.cometchat.restoreFromCCState();
					ccvariable.externalVars["currenttime"] = ccvariable.currentTime;
					if (ccvariable.runHeartbeat == 1) {
					  jqcc.cometchat.chatHeartbeat();
					}
				}
			}
		};
		function cleanExternalVars(externalVars){
			var cleanedExternalVars ={};
			$.each(externalVars,function(property,value){
				if($.isNumeric(value)){
					value+='';
				}
				if(value=='0'||value==''||value==null||value=='null'||value=='undefined'||$.isEmptyObject(value)||value.length==0){
					return;
				}
				if(typeof value == 'object'){
					cleanedExternalVars[property] = cleanExternalVars(value);
				}else{
					cleanedExternalVars[property] = value;
				}
			});
			return cleanedExternalVars;
		}
		arguments.callee.checkInternetConnection = function(){
			return navigator.onLine;
		};
		arguments.callee.stimulateHeartbeat = function(options){
			var defaults = {};
			var params = $.extend(defaults,options);
			clearTimeout(ccvariable.heartbeatTimer);
			if(ccvariable.loggedout!=1&&ccvariable.offline!=1){
				if(params.hasOwnProperty('heartbeatTime')){
					ccvariable.heartbeatTime = params.heartbeatTime;
					ccvariable.heartbeatCount = 1;
				}else{
					ccvariable.heartbeatCount++;
					if(ccvariable.heartbeatCount>4){
						ccvariable.heartbeatTime *= 2;
						ccvariable.heartbeatCount = 1;
					}
					if(ccvariable.heartbeatTime>settings.maxHeartbeat){
						ccvariable.heartbeatTime = settings.maxHeartbeat;
					}
				}
				ccvariable.heartbeatTimer = setTimeout(function(){
					jqcc.cometchat.chatHeartbeat();
				}, ccvariable.heartbeatTime);
			}
		};
		arguments.callee.getUserAgent = function(){
			var ua= navigator.userAgent, tem,
			M= ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
			if(/trident/i.test(M[1])){
				tem=  /\brv[ :]+(\d+)/g.exec(ua) || [];
				return 'IE '+(tem[1] || '');
			}
			if(M[1]=== 'Chrome'){
				tem= ua.match(/\bOPR\/(\d+)/);
				if(tem!= null) return 'Opera '+tem[1];
			}
			M= M[2]? [M[1], M[2]]: [navigator.appName, navigator.appVersion, '-?'];
			if((tem= ua.match(/version\/(\d+)/i))!= null) M.splice(1, 1, tem[1]);
			return M;
		};
		arguments.callee.startGuestChat = function(name){
			if((typeof(cc_synergy_enabled)!="undefined" && cc_synergy_enabled == 1) || (typeof(cc_embedded_enabled)!="undefined" && cc_embedded_enabled == 1) ){
				var controlparameters = {"type":"modules", "name":"cometchat", "method":"startGuestChat", "params":{'name':name}};
				controlparameters = JSON.stringify(controlparameters);
				if(typeof(jqcc('#cometchat_synergy_iframe')[0]) != 'undefined'){
					jqcc('#cometchat_synergy_iframe')[0].contentWindow.postMessage('CC^CONTROL_'+controlparameters,'*');
				}
			}else{
				ccvariable.externalVars["guest_login"] = 1;
				ccvariable.externalVars["username"] = name;
				jqcc.cometchat.reinitialize();
			}
		};
		arguments.callee.chatHeartbeat = function(force){
			if (settings.disableDockedLayout == "1" && settings.theme == "docked" && settings.forceDockedEnable == "0" ) {
				return false;
			}
			var newMessage = 0;
			var internetconnection = true;

			if(force==1){
				if(typeof window.cometcall_function=='function' && ccvariable.cometid != ''){
					cometcall_function(ccvariable.cometid, 0, calleeAPI);
				}
			}
			ccvariable.externalVars["blh"] = ccvariable.buddyListHash;
			ccvariable.externalVars["botlh"] = ccvariable.botListHash;
			ccvariable.externalVars["status"] = "";
			if((ccvariable.callbackfn!=''&&ccvariable.callbackfn!='desktop')||calleeAPI=='ccmobiletab'){
				ccvariable.externalVars["status"] = 'available';
			}
			if(force==1){
				ccvariable.externalVars["f"] = 1;
			}else{
				delete ccvariable.externalVars["f"];
			}
			var atleastOneNewMessage = 0;
			var nowTime = new Date();
			var n = {};
			var idleDifference = Math.floor(nowTime.getTime()/1000)-ccvariable.idleTime;
			if(idleDifference>=settings.idleTimeout&&ccvariable.idleFlag==0){
				if(ccvariable.currentStatus=='available'){
					ccvariable.idleFlag = 1;
					ccvariable.externalVars["status"] = 'away';
				}
			}
			if(idleDifference<settings.idleTimeout&&ccvariable.idleFlag==1){
				if(ccvariable.currentStatus=='away'){
					ccvariable.idleFlag = 0;
					ccvariable.externalVars["status"] = 'available';
				}
			}
			if(ccvariable.crossDomain==1){
				ccvariable.externalVars["cookie_"+settings.cookiePrefix+"state"] = $.cookie(settings.cookiePrefix+'state');
				ccvariable.externalVars["cookie_"+settings.cookiePrefix+"hidebar"] = $.cookie(settings.cookiePrefix+'hidebar');
				ccvariable.externalVars["cookie_"+settings.cookiePrefix+"an"] = $.cookie(settings.cookiePrefix+'an');
				ccvariable.externalVars["cookie_"+settings.cookiePrefix+"loggedin"] = $.cookie(settings.cookiePrefix+'loggedin');
				ccvariable.externalVars["cookie_"+settings.cookiePrefix+"lang"] = $.cookie(settings.cookiePrefix+'lang');
			}
			ccvariable.externalVars['currenttime'] = Math.floor(new Date().getTime()/1000);
			ccvariable.externalVars["basedata"] = ccvariable.baseData;
			ccvariable.externalVars["readmessages"] = jqcc.cometchat.getFromStorage('readmessages');
			/*ccvariable.externalVars["receivedunreadmessages"] = jqcc.cometchat.getFromStorage('receivedunreadmessages');*/

			if((settings.theme == "synergy" || settings.theme == "embedded") && settings.enableType == 1 && embeddedchatroomid > 0 && ccvariable.externalVars["initialize"] == 1){
				ccvariable.externalVars["buddylist"] = 0;
			}
			if (((settings.theme == "synergy" || settings.theme == "embedded") && settings.enableType == 1 && ccvariable.externalVars["initialize"] == 1) || ((settings.theme == "synergy" || settings.theme == "embedded") && settings.enableType != 1 && embeddedchatroomid == 0) || (settings.theme != "synergy" || settings.theme != "embedded")) {
				$.ajax({
					url: baseUrl+"cometchat_receive.php",
					data: cleanExternalVars(ccvariable.externalVars),
					dataType: 'jsonp',
					type: ccvariable.dataMethod,
					timeout: ccvariable.dataTimeout,
					error: function(xhr){
						if(!xhr.status && typeof jqcc[settings.theme].nointernetconnection != "undefined") {
							jqcc[settings.theme].nointernetconnection();
						}
						if(!(jqcc(document).find("#cometchat").hasClass('CCReceiveError')) && ccvariable.externalVars["initialize"] == '1'){
							jqcc(document).find("#cometchat").addClass('CCReceiveError');
						}
						jqcc.cometchat.stimulateHeartbeat({heartbeatTime:settings.minHeartbeat});
					},
					success: function(data){
						if(jqcc.cookie(settings.cookiePrefix+'guest') == null && data.hasOwnProperty(settings.cookiePrefix+'guest')){
							jqcc.cookie(settings.cookiePrefix+'guest', data[settings.cookiePrefix+'guest']);
						}
						if(jqcc.cookie(settings.cookiePrefix+'guest') != null && jqcc.cookie(settings.cookiePrefix+'guest') != data[settings.cookiePrefix+'guest']){
							jqcc.cookie(settings.cookiePrefix+'guest', data[settings.cookiePrefix+'guest']);
						}
						if(jqcc(document).find("#cometchat").hasClass('CCReceiveError')){
							jqcc(document).find("#cometchat").removeClass('CCReceiveError');
						}
						if(data){
							jqcc.cometchat.setInternalVariable('allowchatboxpopup', '1');
							jqcc.cometchat.updateToStorage('readmessages',{});
							if(ccvariable.externalVars['initialize'] == 1 && typeof initializeCometService == 'function' && (data.hasOwnProperty('userstatus')||data.hasOwnProperty('userid'))){
								initializeCometService();
							}
							$.each(data, function(type, item){
								if(type=='blh'){
									ccvariable.buddyListHash = item;
								}
								if(type=='botlh'){
									ccvariable.botListHash = item;
								}
								if(type=='buc'){
									$("#cometchat_blockeduserscount").html(item);
								}
								if(type=='an'){
									if(typeof (jqcc[calleeAPI].newAnnouncement)!=='undefined'){
										jqcc[calleeAPI].newAnnouncement(item);
									}
									/*Callback for Announcement*/
							        jqcc.cometchat.processSubscribeCallback('gotAnnouncement',item);
								}
								if(type=='buddylist'){
									if(typeof (jqcc[calleeAPI].buddyList)=='function'){
										jqcc[calleeAPI].buddyList(item);
									}
							        /*Callback for Buddy List*/
							        jqcc.cometchat.processSubscribeCallback('gotOnlineList',item);
								}
								if(type=='botlist'){
									if(typeof (jqcc[calleeAPI].botList)=='function'){
										jqcc[calleeAPI].botList(item);
									}
								}
								if(type=='recent'){
									if(typeof (jqcc.cometchat.updateRecentChats)=='function'){
										var params = {'force':1,'list':item};
										jqcc.cometchat.updateRecentChats(params);
									}
									/*Callback for Recent Chats*/
                            		jqcc.cometchat.processSubscribeCallback('gotRecentChatsList',item);
								}
								if(type=='loggedintype'){
									ccvariable.loggedinusertype = item;
								}
								if(type=='role'){
									role = item;
								}
								if(type=='loggedout'){
									if(ccvariable.cometid!='' && typeof(cometuncall_function)==="function"){
										cometuncall_function(ccvariable.cometid);
										jqcc.cometchat.setThemeVariable('cometid','');
									}
									if(typeof(cometstop_function)==="function"){
										cometstop_function();
									}
									$.cookie(settings.cookiePrefix+"loggedin", null, {path: '/'});
									$.cookie(settings.cookiePrefix+"state", null, {path: '/'});
									$.cookie(settings.cookiePrefix+"jabber", null, {path: '/'});
									$.cookie(settings.cookiePrefix+"jabber_type", null, {path: '/'});
									$.cookie(settings.cookiePrefix+"hidebar", null, {path: '/'});
									$.cookie(settings.cookiePrefix+"lang", null, {path: '/'});
									$.cookie(settings.cookiePrefix+"theme", null, {path: '/'});
									$.cookie(settings.cookiePrefix+"color", null, {path: '/'});
									if(typeof (jqcc[calleeAPI].loggedOut)!=='undefined'){
										jqcc[calleeAPI].loggedOut();
									}
									jqcc.cometchat.setThemeVariable('loggedout', 1);
									clearTimeout(ccvariable.heartbeatTimer);
									if (typeof(jqcc[jqcc.cometchat.getChatroomVars('calleeAPI')].chatroomLogout) == "function") {
										jqcc[jqcc.cometchat.getChatroomVars('calleeAPI')].chatroomLogout();
									}
								}
								if(type=='userstatus'){
									if(settings.ccauth.enabled){
										postMessage('cc_reinitializeauth','*');
									}
									ccvariable.userid = item.id;
									ccvariable.buddylistStatus[item.id] = item.s;
									ccvariable.buddylistMessage[item.id] = item.m;
									ccvariable.buddylistName[item.id] = item.n;
									ccvariable.buddylistAvatar[item.id] = item.a;
									ccvariable.buddylistLink[item.id] = item.l;
									ccvariable.buddylistChannelHash[item.id] = item.ch||'';
									ccvariable.buddylistLastseen[item.id] = item.ls||0;
									ccvariable.ccmobileauth = item.ccmobileauth;
									if(typeof (jqcc[calleeAPI].userStatus)!=='undefined'){
										jqcc[calleeAPI].userStatus(item);
									}
									if(settings.messageBeep==1&&(ccvariable.callbackfn==""||ccvariable.callbackfn=="desktop")){
										if(typeof (jqcc[calleeAPI].messageBeep)!='undefined'){
											jqcc[calleeAPI].messageBeep(staticCDNUrl);
										}
									}
									if(ccvariable.callbackfn!=""&&ccvariable.callbackfn=="desktop" && (settings.plugins).indexOf('screenshare') > -1){
										var ccpluginindex=(settings.plugins).indexOf('screenshare');
										(settings.plugins).splice(ccpluginindex,1);
									}
									if(parseInt(ccvariable.userid) && typeof jqcc.cometchat.subscribeToStorage !== 'undefined'){
										jqcc.cometchat.subscribeToStorage('cometchat_user_'+ccvariable.userid);
									}
							        /*Callback for User Status*/
							        jqcc.cometchat.processSubscribeCallback('gotProfileInfo',item);
								}
								if(type=='cometid'){
									ccvariable.cometid = item.id;
									cometcall_function(ccvariable.cometid, 0, calleeAPI);
								}
								if(type=='init'){
									jqcc.cometchat.setInternalVariable('updatingsession', '1');
								}
								if(type=='initialize'){
									ccvariable.timestamp = item;
									ccvariable.externalVars["timestamp"] = item;
									if(typeof (jqcc.cometchat.restoreFromCCState)!=='undefined'){
										jqcc.cometchat.restoreFromCCState();
										if(typeof (jqcc[calleeAPI].resynch)!=='undefined'){
											jqcc[calleeAPI].resynch();
										}
										if(typeof jqcc.cometchat.subscribeToStorage !== 'undefined'){
											jqcc.cometchat.subscribeToStorage('cometchat_chattab_state'+ccvariable.userid);
										}
									}
									if(typeof (jqcc[calleeAPI].windowResize)!=='undefined'){
										jqcc[calleeAPI].windowResize();
									}
								}
								if(type=='st'){
									ccvariable.timedifference = (item*1000) - parseInt(new Date().getTime());
								}
								if(type=='messages'){
									if(ccvariable.externalVars['initialize'] != 1){
										ccvariable.externalVars["timestamp"] = item[Object.keys(item).sort().reverse()[0]].id;
									}
									if(typeof (jqcc.cometchat.publishToStorage) !== 'undefined'){
										jqcc.cometchat.publishToStorage('cometchat_user_'+ccvariable.userid,item);
									}
									jqcc.cometchat.stimulateHeartbeat({heartbeatTime:settings.minHeartbeat});
								}

							/*chatroom responses start*/
								if (type == 'logout') {
									if (typeof(jqcc[jqcc.cometchat.getChatroomVars('calleeAPI')].chatroomLogout) == "function") {
										jqcc[jqcc.cometchat.getChatroomVars('calleeAPI')].chatroomLogout();
									}

									if(typeof(cometstop_function)==="function"){
										cometstop_function();
									}
								}
								if (type == 'userid') {
									jqcc.cometchat.setChatroomVars('myid',item);
									jqcc.cometchat.setChatroomVars('initialize',0);
									if (typeof(jqcc[jqcc.cometchat.getChatroomVars('calleeAPI')]) != 'undefined' && typeof(jqcc[jqcc.cometchat.getChatroomVars('calleeAPI')].createChatroomTab) == "function"){

										jqcc[jqcc.cometchat.getChatroomVars('calleeAPI')].createChatroomTab(item);
									}
								}
								if (type == 'chatrooms') {
									if (jqcc.cometchat.getChatroomVars('initializeAutoLogin') == 1 && jqcc.cometchat.getChatroomVars('themename') == 'embedded') {
										var autoLoginCr = jqcc.cometchat.getChatroomVars('autoLogin');
										jqcc.cometchat.setChatroomVars('chatroomdetails',item);
										$.each(item, function(i,room) {
											if (('_'+autoLoginCr) == i) {
												if(typeof(btoa) != 'undefined'){
													var encodedroomname = btoa(room.name);
												}else{
													var encodedroomname = base64_encode(room.name);
												}
												jqcc.cometchat.silentroom(autoLoginCr,'',encodedroomname);
												if($('#cometchat_chatroomstab').length > 0) {
													$('#cometchat_chatroomstab').click();
												}
												if($('#cometchat_chatroomstab_popup').length > 0) {
													$('#cometchat_chatroomstab_popup').addClass("cometchat_tabopen");
												}
											}
										});
										jqcc.cometchat.setChatroomVars('initializeAutoLogin',0);
									}
									if (typeof(jqcc[jqcc.cometchat.getChatroomVars('calleeAPI')].loadChatroomList) == "function"){
										jqcc[jqcc.cometchat.getChatroomVars('calleeAPI')].loadChatroomList(item);
									}
									/*Callback for Groups*/
							        jqcc.cometchat.processSubscribeCallback('gotGroupList',item);
								}
								if (type == 'clh') {
									jqcc.cometchat.setChatroomVars('clh',item);
								}
								if (type == 'prepend') {
									jqcc.cometchat.setChatroomVars('prepend',item);
								}
								if (type == 'ulh') {
									jqcc.cometchat.setChatroomVars('ulh',item);
								}
								if(type == 'chatroomList') {
									if (typeof(jqcc[jqcc.cometchat.getChatroomVars('calleeAPI')].updateCRReadMessages) == "function")
										jqcc[jqcc.cometchat.getChatroomVars('calleeAPI')].updateCRReadMessages(item);
								}
								if (type == 'crmessages') {
									if (typeof(jqcc[jqcc.cometchat.getChatroomVars('calleeAPI')].displayChatroomMessage) == "function"){
										jqcc[jqcc.cometchat.getChatroomVars('calleeAPI')].displayChatroomMessage(item,0);
									}
									if(jqcc.cometchat.getChatroomVars('calleeAPI') == 'embedded'){
										if(($("#currentroom_convo")[0].scrollHeight) - ($("#currentroom_convo").scrollTop() + $("#currentroom_convo").innerHeight()) < 70) {
											if (typeof(jqcc[jqcc.cometchat.getChatroomVars('calleeAPI')].chatroomScrollDown) == "function")
											jqcc[jqcc.cometchat.getChatroomVars('calleeAPI')].chatroomScrollDown(1);
										}
									}
									jqcc.cometchat.stimulateHeartbeat({heartbeatTime:settings.minHeartbeat});
									if(ccvariable.externalVars['initialize'] != 1){
										ccvariable.externalVars["lastgroupmessageid"] = item[Object.keys(item).sort().reverse()[0]].id;
									}
									/*Callback for Groups Messages*/
							        jqcc.cometchat.processSubscribeCallback('onGroupMessageReceived',item);
								}
								if (type == 'users') {
									if (typeof(jqcc[jqcc.cometchat.getChatroomVars('calleeAPI')].updateChatroomUsers) == "function")
										jqcc[jqcc.cometchat.getChatroomVars('calleeAPI')].updateChatroomUsers(item,0);
								}
								if (type == 'error') {
									jqcc.cometchat.leaveChatroom();
								}
								if (type == 'subscribeChatrooms') {
									jqcc.cometchat.subscribeCometChatrooms(item);
								}
								if(type == 'lastgroupmessageid'){
									ccvariable.externalVars["lastgroupmessageid"] = item;
								}
								/*chatroom responses end*/
							});
							if(ccvariable.externalVars["status"]!=""){
								if(typeof (jqcc[calleeAPI].removeUnderline2)!=='undefined'){
									jqcc[calleeAPI].removeUnderline2();
								}
								if(typeof (jqcc[calleeAPI].updateStatus)!=='undefined'){
									jqcc[calleeAPI].updateStatus(ccvariable.externalVars["status"]);
								}
							}
							jqcc.cometchat.setExternalVariable('initialize', '0');
							jqcc.cometchat.setExternalVariable('currenttime', '0');
							jqcc.cometchat.stimulateHeartbeat();
						}
						var offlinemessagesqueue = jqcc.cometchat.getFromStorage('offlinemessagesqueue');
						if(typeof(offlinemessagesqueue) != 'undefined' && !jqcc.isEmptyObject(offlinemessagesqueue)) {
							jqcc.each(offlinemessagesqueue,function(key,value){
								if(!jqcc.isEmptyObject(value) && value.msgStatus == 0) {
									if(value.hasOwnProperty('type') && value.type != '') {
										jqcc["cc"+value.name][value.method](value.message);
									}
									else if(value.hasOwnProperty('chatroommode') && value.chatroommode == 1) {
										jqcc.cometchat.sendmessageProcess(value.message,value.id,'','',key);
									}
									else {
										jqcc.cometchat.chatboxKeydownSet(value.id,value.message,'',key);
									}
								}
							});
						}
					}
				});
			}
		};
		arguments.callee.memberPluginRestrictions = function(memberfeature){

			var memberAvailablePlugin = settings[role+'_plugins'];
			if (memberAvailablePlugin.indexOf(memberfeature) > -1) {
				return true;
			}else {
				return false;
			}
		}
		arguments.callee.memberExtensionRestrictions = function(memberfeature){
			var memberAvailableExt = settings[role+'_extensions'];
			if (memberAvailableExt.indexOf(memberfeature) > -1) {
				return true;
			}else {
				return false;
			}
		}
		arguments.callee.memberModuleRestrictions = function(memberfeature){
			var memberAvailableModule = settings[role+'_modules'];
			if (memberAvailableModule.indexOf(memberfeature) > -1) {
				return true;
			}else {
				return false;
			}
		}
		arguments.callee.membershipAccess = function(feature,type){
			if ( typeof (settings.memberShipLevel) != 'undefined' && settings.memberShipLevel == 1 && role != '') {
				var memberAvailableFeature = settings[role+'_'+type];
				if (memberAvailableFeature.indexOf(feature) > -1) {
					return true;
				}else {
					var message = jqcc.cometchat.getLanguage('membership_msg');
					alert(message);
					return false;
				}
			} else {
				return true;
			}
		}
		/** Local Storage Start **/
		arguments.callee.updateToStorage = function(key,value){
			if(Object.keys(value).length === 0){
				jqcc.jStorage.set(key,{});
			}else{
				jqcc.jStorage.set(key,
					jqcc.extend(true,
						{},
						jqcc.jStorage.get(key,{}),
						value
					)
				);
			}
		}
		arguments.callee.getFromStorage = function(key){
			return jqcc.jStorage.get(key,{});
		}
		arguments.callee.publishToStorage = function(channel,payload){
			if(typeof payload == 'object' && Object.keys(payload).length === 0){
				jqcc.jStorage.publish(channel,{});
			}else{
				jqcc.jStorage.publish(channel, payload);
			}
		}
		arguments.callee.subscribeToStorage = function(channel){
			jqcc.jStorage.subscribe(channel, function(channel, payload){
				if(payload ==='restoreState'){
					jqcc.cometchat.restoreFromCCState();
				}else if(typeof (jqcc[calleeAPI].addMessages)!=='undefined'){
					jqcc[calleeAPI].addMessages(payload,1);
				}
			});
		}
		/** Local Storage End **/

		/** Recent Chats Start **/
		arguments.callee.updateRecentChats = function(params){
			var userid = jqcc.cometchat.getUserID();
			var recentkey = 'recentchats_'+userid;
			if(typeof(userid) != "undefined" && userid > 0) {
				var recentlist = jqcc.cometchat.getFromStorage(recentkey);
				if(params.force == 1){
					var recentlist = {};
					if(typeof(params.list) == "object" && params.list != 'null' && params.list != null){
						$.each(params.list, function(i, details){
							details.m = jqcc.cometchat.processRecentmessages(details.m);
							if(recentlist.hasOwnProperty(i) && (jqcc.cometchat.processTimestamp(details.t) > jqcc.cometchat.processTimestamp(recentlist[i].t)) && details.m != '') {
								recentlist[i].t = jqcc.cometchat.processTimestamp(details.t);
								recentlist[i].m = details.m;
								recentlist[i].n = details.n || jqcc.cometchat.getThemeArray('buddylistName', i);
								if(i.charAt(0)!='_'){
									recentlist[i].a = details.a || jqcc.cometchat.getThemeArray('buddylistAvatar', i);
								}
							} else if(details.m != '') {
								if(i.charAt(0)=='_') {
									var grpid = i.replace("_", "");
									var groupdetails = jqcc.cometchat.getChatroomVars('chatroomdetails');
									if(groupdetails.hasOwnProperty(i) && details.hasOwnProperty('m') && details.hasOwnProperty('t')){
										recentlist[i] = {'n':details.n || groupdetails[i].name,
														'id':grpid,
														'createdby':groupdetails[i].createdby,
														'pass':groupdetails[i].i,
														'j':groupdetails[i].j,
														'o':groupdetails[i].online,
														's':groupdetails[i].s,
														'type':groupdetails[i].type,
														'grp':1,
														'm':details.m,
														't':jqcc.cometchat.processTimestamp(details.t)
													};
									}
								} else if(i != userid) {
									recentlist[i] = {'n':details.n || jqcc.cometchat.getThemeArray('buddylistName', i),
												'id':i,
												'a': details.a || jqcc.cometchat.getThemeArray('buddylistAvatar', i),
												'grp':0,
												'm':details.m,
												't':jqcc.cometchat.processTimestamp(details.t)
											};
								}
							}
						});
					}
				} else {
				   if(params.isgroup) {
						var grpid = '_'+params.chatid;
						var groupdetails = jqcc.cometchat.getChatroomVars('chatroomdetails');
						if(groupdetails.hasOwnProperty(grpid)) {
							recentlist[grpid] = {'n':groupdetails[grpid].name,
											'id':params.chatid,
											'createdby':groupdetails[grpid].createdby,
											'pass':groupdetails[grpid].i,
											'j':groupdetails[grpid].j,
											'o':groupdetails[grpid].online,
											's':groupdetails[grpid].s,
											'type':groupdetails[grpid].type,
											'grp':1,
											'm':params.m,
											't':jqcc.cometchat.processTimestamp(params.timestamp)
										};
						}
					} else if(params.chatid != userid) {
						recentlist[params.chatid] = {'n':jqcc.cometchat.getThemeArray('buddylistName', params.chatid),
											'id':params.chatid,
											'a':jqcc.cometchat.getThemeArray('buddylistAvatar', params.chatid),
											'd':jqcc.cometchat.getThemeArray('buddylistIsDevice', params.chatid),
											'grp':0,
											'm':params.m,
											't':jqcc.cometchat.processTimestamp(params.timestamp)
										};
					}
				}
				jqcc.cometchat.updateToStorage(recentkey,recentlist);
				jqcc.cometchat.refreshRecentChats(recentlist);
			}
		}

		arguments.callee.refreshRecentChats = function(recentlist){
			var userid = jqcc.cometchat.getUserID();
			var settings = jqcc.cometchat.getSettings();
			var recentkey = 'recentchats_'+userid;
			if(typeof(userid) != "undefined" && userid > 0) {
				if(typeof(recentlist)=="undefined") {
					recentlist = jqcc.cometchat.getFromStorage(recentkey);
				}

				recentlist = jqcc.cometchat.processRecentmessages(recentlist);
				var sortedlist = Object.keys(recentlist).map(function (key) { return recentlist[key]; });
				sortedlist.sort(function(a, b){
					a.t = jqcc.cometchat.processTimestamp(a.t);
					b.t = jqcc.cometchat.processTimestamp(b.t);
					return b.t - a.t;
				});
				sortedlist = sortedlist.slice(0, settings.recentListLimit);
				if(typeof(jqcc[calleeAPI].recentList) == 'function') {
					jqcc[calleeAPI].recentList(sortedlist);
				}
			}
		}

		arguments.callee.processRecentmessages = function(recentlist){
			if(typeof(recentlist) == "object") {
				var staticCDNUrl = jqcc.cometchat.getStaticCDNUrl();
				$.each(recentlist, function(id, details){
					if(typeof(details.m) != "undefined" && details.m.indexOf("CC^CONTROL_") != -1) {
						var data = (details.m).replace('CC^CONTROL_','');
						data = JSON.parse(data);
						switch(data.type){
							case 'core':
								break;
							case 'smiley':
								if(data.m.length > 20){
									data.m = data.m.substring(0, 19)+' ';
								}
								var arrStr = data.m.split(/[::]/);
								for(var i=0;i<arrStr.length;i++) {
									if(arrStr[i] != '' && arrStr[i].indexOf(' ') == -1) {
										var smiley = '<img class="cometchat_smiley" height="15" width="15" src="'+staticCDNUrl+'writable/images/smileys/'+arrStr[i]+'.png" title="'+arrStr[i]+'"> ';
										data.m = (data.m).replace(':'+arrStr[i]+':',smiley);
									}
								}
								details.m = data.m;
								break;
							default:
								break;
						}
					}
				});
			} else if(typeof(recentlist) == "string") {
				var smileycount = (recentlist.match(/cometchat_smiley/g) || []).length;
				var stickercount = (recentlist.match(/cometchat_stickerImage/g) || []).length;
				var handwritecount = (recentlist.match(/cc_handwrite_image/g) || []).length;
				var audiofilecount = (recentlist.match(/file_audio/g) || []).length;
				var filemsgcount = (recentlist.match(/imagemessage/g) || []).length;
				var imagemsgcount = (recentlist.match(/cometchat_botimagefile/g) || []).length;
				var videobroadcastcount = (recentlist.match(/jqcc.ccbroadcast/g) || []).length;
				var screensharecount = (recentlist.match(/jqcc.ccscreenshare/g) || []).length;
				var whiteboardcount = (recentlist.match(/jqcc.ccwhiteboard/g) || []).length;
				var writeboardcount = (recentlist.match(/jqcc.ccwriteboard/g) || []).length;
				var avchatcount = (recentlist.match(/jqcc.ccavchat/g) || []).length;
				var audiochatcount = (recentlist.match(/jqcc.ccaudiochat/g) || []).length;
				var colortextcount = (recentlist.match(/style="color:/g) || []).length;

				if(smileycount > 0) {
					if(colortextcount > 0) {
						recentlist = recentlist.replace(/<\/?span[^>]*>/g,"");
					}
					var regex = /<img.*?title=['"](.*?)['"]/;
					var smileyarray = [];
					for(var i=0;i<smileycount;i++){
						smileyarray[i] = (':'+regex.exec(recentlist)[1]+':').toLowerCase();
						recentlist = recentlist.replace(/<img[^>]*>/,smileyarray[i]);
					}
					recentlist = 'CC^CONTROL_'+JSON.stringify({'m':recentlist,'type':'smiley'});
				} else if(stickercount > 0) {
					recentlist = language['sticker'];
				} else if(handwritecount > 0) {
					recentlist = language['handwrite'];
				} else if(audiofilecount > 0) {
					recentlist = language['audiofile'];
				} else if(filemsgcount > 0) {
					recentlist = language['file'];
				} else if(imagemsgcount > 0) {
					recentlist = language['image'];
				} else if(videobroadcastcount > 0) {
					recentlist = language['videobroadcast'];
				} else if(screensharecount > 0) {
					recentlist = language['screenshare'];
				} else if(whiteboardcount > 0) {
					recentlist = language['whiteboard'];
				} else if(writeboardcount > 0) {
					recentlist = language['writeboard'];
				} else if(avchatcount > 0) {
					recentlist = language['avchat'];
				} else if(audiochatcount > 0) {
					recentlist = language['audiochat'];
				} else if(colortextcount > 0) {
					recentlist = recentlist.replace(/<\/?span[^>]*>/g,"");
				} else if(recentlist.indexOf("<a ") != -1) {
					recentlist = $(recentlist).text();
				}

				if(recentlist.length > 20 && recentlist.indexOf("CC^CONTROL_") == -1){
					recentlist = recentlist.substring(0, 19);
				}
				if(recentlist.indexOf("CC^CONTROL_") !== -1 && smileycount == 0){
					recentlist = '';
				}
			}
			return recentlist;
		}

		arguments.callee.processTimestamp = function(ts){
			if((ts+'').length == 10) {
				ts *= 1000;
			}
			return ts;
		}
		/** Recent Chats End **/

		/** External Variable Start **/
		arguments.callee.setExternalVariable = function(name, value){
			ccvariable.externalVars[name] = value;
		};
		arguments.callee.getExternalVariable = function(name){
			if(ccvariable.externalVars[name]){
				return ccvariable.externalVars[name];
			}else{
				return '';
			}
		};
		/** External Variable End **/

		/** Internal Variable Start **/
		arguments.callee.setInternalVariable = function(name, value){
			ccvariable.internalVars[name] = value;
		};
		arguments.callee.getInternalVariable = function(name){
			if(ccvariable.internalVars[name]){
				return ccvariable.internalVars[name];
			}else{
				return '';
			}
		};
		/** Internal Variable Start **/

		/** Session Variable Start **/
		arguments.callee.getSessionVariable = function(name){
			if(ccvariable.sessionVars[name]){
				return ccvariable.sessionVars[name];
			}else{
				return '';
			}
		};

		arguments.callee.setSessionVariable = function(name, value){
			/**
				Session variables:
				The variables are separated by colon(':')
				chats:
					The variable indicates whether the "Chats" panel is open or close.
					The possible values are: (0 or '' ) => close and 1 => Open
				openedtab:
					The variable stores the tab opened under chats tab.
					The possible values are: (0 or '' ) => Recent tab, 1 => Contacts tab and 2 => Groups tab
				chatboxes:
					The variable stores the chatboxes along with their properties.
					The chatboxes are separated by comma (',')
					The properties of a chatbox are separated by pipe ('|')
					The first chatbox property stores the userid or groupid. The groupids are prefixed by '_'
					The second chatbox property stores the state of the chatbox. 0=> closed, 1=>opened and 2=>minimized
					The third chatbox property stores unread message count.
			**/
			ccvariable.sessionVars[name] = value;
			var cc_state = '';
			if(ccvariable.sessionVars['chats']){
				cc_state += ccvariable.sessionVars['chats'];
			}
			cc_state += ':';
			if(typeof(ccvariable.sessionVars['openedtab']) != "undefined"){
				cc_state += ccvariable.sessionVars['openedtab'];
			}
			cc_state += ':';
			if(ccvariable.sessionVars['chatboxstates']){
				cc_state += ccvariable.sessionVars['chatboxstates'];
			}
			var oldValue = $.cookie(settings.cookiePrefix+'state');
			if(oldValue != cc_state){
				$.cookie(settings.cookiePrefix+'state', cc_state, {path: '/'});
				jqcc.cometchat.publishToStorage('cometchat_chattab_state'+ccvariable.userid,'restoreState');
			}
		};
		arguments.callee.restoreFromCCState = function(){
			var cc_state = $.cookie(settings.cookiePrefix+'state');
			if(cc_state!=null){
				var cc_states = cc_state.split(/:/);
				if(cc_states[0]){
					if(ccvariable.sessionVars['chats'] != cc_states[0]){
						ccvariable.sessionVars['chats'] = cc_states[0];
						if(typeof(jqcc[calleeAPI]) == "function" && typeof(jqcc[calleeAPI].openMainContainer)=='function'){
							jqcc[calleeAPI].openMainContainer();
						}
					}
				}
				if(cc_states[1]!=undefined){
					if(ccvariable.sessionVars['openedtab'] != cc_states[1]){
						ccvariable.sessionVars['openedtab'] = cc_states[1];
						if(typeof(jqcc[calleeAPI]) == "function" && typeof(jqcc[calleeAPI].openChatTab)=='function'){
							jqcc[calleeAPI].openChatTab(parseInt(cc_states[1]), 1);
						}
					}
				}
				if(cc_states[2]!=undefined){
					if(ccvariable.sessionVars['chatboxstates'] != cc_states[2]){
						ccvariable.sessionVars['chatboxstates'] = cc_states[2];
						var chatboxstates = {};
						var statestoapply = [];
						var chatboxstatesarray = cc_states[2].split(/,/);
						var next = 0;
						$.each(ccvariable.chatBoxOrder, function(i, e) {
							if((chatboxstatesarray[next] == '' || typeof(chatboxstatesarray[next])==="undefined") || chatboxstatesarray[next].split(/\|/)[0] != ccvariable.chatBoxOrder[next]){
								if(typeof(ccvariable.chatBoxOrder[next]) != 'undefined'){
									if(ccvariable.chatBoxOrder[next].charAt(0)=='_'){
										key = parseInt(ccvariable.chatBoxOrder[next].replace('_',''));
										jqcc[jqcc.cometchat.getChatroomVars('calleeAPI')].closeChatroom(key,1);
									}else{
										if (typeof(jqcc[calleeAPI].closeChatbox) == 'function'){
											jqcc[calleeAPI].closeChatbox(ccvariable.chatBoxOrder[next],1);
										}
									}
									if(i != 0)
										next = next;
									else if(i == 0)
										next = 0;
								}
							}else{
								next = next+1;
							}
						});
						for(var i=0,len=chatboxstatesarray.length;i<len;i++){
							var state = chatboxstatesarray[i].split(/\|/);
							if(!state[0]){
								continue;
							}
							chatboxstates[state[0]]=chatboxstatesarray[i]; // stores states of chatboxes
							ccvariable.chatBoxOrder.push(state[0]);// preserves order of chatboxes
							var params = {};
							if(state[0].charAt(0)=='_'){
								params.g=1;
								state[0] = state[0].replace('_','');
							}
							params.id=parseInt(state[0]);
							if(state[1]){
								params.s = parseInt(state[1]);
							}
							if(state[2]){
								params.c = parseInt(state[2]);
							}
							statestoapply.push(params);
						}
						ccvariable.internalVars['chatboxstates'] = chatboxstates;
						if(typeof(jqcc[calleeAPI]) == "function" && typeof(jqcc[calleeAPI].applyChatBoxStates)=='function' && statestoapply.length > 0){
							jqcc[calleeAPI].applyChatBoxStates(statestoapply);
						}
					}
				}
			}
		}
		arguments.callee.updateChatBoxState = function(params){
			/**
				params: JSON Object with the properties:
				id: userid or group id
				g: optional indicating id is user or group
				s: state of the chatbox
				c: count of unread messages
			*/
			if (getURLParameter('crid') != 'null'){
				return;
			}
			if(!params.hasOwnProperty('id') || !$.isNumeric(params.id)){
				return;
			}
			if(!ccvariable.internalVars.hasOwnProperty('chatboxstates')){
				ccvariable.internalVars['chatboxstates'] = {};
			}
			var chatboxstates = ccvariable.internalVars['chatboxstates'];
			var key = ''+parseInt(params.id);
			var unreadcount = 0;
			if(params.hasOwnProperty('g') && params.g==1){
				key = '_'+key;
			}
			if(!params.hasOwnProperty('c')){
				params.c = 0;
			}
			unreadcount += params.c;
			if(chatboxstates.hasOwnProperty(key)){
				var states = chatboxstates[key].split('|');
				var oldstate = states[1];
				var oldunreadcount = states[2];
				if(!params.hasOwnProperty('s')){
					params.s = states[1];
				}
				switch(params.s){
					case 0:
					case '':
					case '0':
						if(oldunreadcount){
							unreadcount += parseInt(oldunreadcount);
						}
						if(ccvariable.chatBoxOrder.indexOf(key)>-1 && unreadcount == 0){
							ccvariable.chatBoxOrder.splice(ccvariable.chatBoxOrder.indexOf(key),1);
						}
						if(unreadcount > 0){
							ccvariable.chatBoxOrder.push(key);
						}
						break;
					case 1:
					case '1':
						if(calleeAPI == 'embedded') {
							if(ccvariable.chatBoxOrder.indexOf(key)>-1){
								ccvariable.chatBoxOrder.splice(ccvariable.chatBoxOrder.indexOf(key),1);
							}
							ccvariable.chatBoxOrder.push(key);
						}else {
							if(oldstate == 0 || oldstate == ''){
								if(ccvariable.chatBoxOrder.indexOf(key)==-1){
									ccvariable.chatBoxOrder.push(key);
								}
							}
						}
						unreadcount = 0;
						break;
					case 2:
					case '2':
						if(oldstate==2){
							if(oldunreadcount){
								unreadcount += parseInt(oldunreadcount);
							}
						}
						break;
					default:
						unreadcount = 0;
						break;
				}
			}else{
				if(ccvariable.chatBoxOrder.indexOf(key)==-1){
					ccvariable.chatBoxOrder.push(key);
				}
			}
			chatboxstates[key] = key;
			chatboxstates[key] += '|';
			if(params.hasOwnProperty('s')&& params.s>0){
				chatboxstates[key] += params.s;
			}
			chatboxstates[key] += '|';
			if(unreadcount){
				chatboxstates[key] += unreadcount;
			}
			chatboxstatesarray = [];
			var result = [];
			for(var i = 0, len = ccvariable.chatBoxOrder.length; i<len; i++){
				if(result.indexOf(ccvariable.chatBoxOrder[i])==-1){
					result.push(ccvariable.chatBoxOrder[i]);
					chatboxstatesarray.push(chatboxstates[ccvariable.chatBoxOrder[i]]);
				}
			}
			ccvariable.chatBoxOrder = result;
			ccvariable.internalVars['chatboxstates'] = chatboxstates;
			if(typeof(params.r) == "undefined"){
				jqcc.cometchat.setSessionVariable('chatboxstates',chatboxstatesarray.join());
			}
			return unreadcount;
		}
		/** Session Variable End **/

		/** Theme Variable Start **/
		arguments.callee.incrementThemeVariable = function(name){
			ccvariable[name]++;
		};
		arguments.callee.setThemeVariable = function(name, value){
			ccvariable[name] = value;
		};
		arguments.callee.setThemeArray = function(name, id, value){
			if(typeof(ccvariable[name])==="undefined"){
				ccvariable[name]={};
			}
			ccvariable[name][id] = value;
		};
		arguments.callee.unsetThemeArray = function(name, id){
			if(typeof(ccvariable[name])!=="undefined"){
				delete ccvariable[name][id];
			}else{
				return false;
			}
		};
		arguments.callee.getThemeArray = function(name, id){
			if(typeof(ccvariable[name])!=="undefined"){
				return ccvariable[name][id];
			}else{
				return false;
			}
		};
		arguments.callee.getThemeVariable = function(name){
			return ccvariable[name];
		};
		/** Theme Variable End **/

		arguments.callee.userClick = function(listing,isrecent){
			if(typeof (jqcc[calleeAPI].userClick)!=='undefined'){
				jqcc[calleeAPI].userClick(listing,isrecent);
			}
		};
		arguments.callee.orderChatboxes = function(){
			return;
			/*var activeids = '';
			var activeChatboxId = '';
			var selfNewMessages = 0;
			for(chatbox in ccvariable.chatBoxesOrder){
				if(ccvariable.chatBoxesOrder.hasOwnProperty(chatbox)){
					if(ccvariable.chatBoxesOrder[chatbox]!=null){
						if(!Number(ccvariable.chatBoxesOrder[chatbox])){
							ccvariable.chatBoxesOrder[chatbox] = 0;
						}
						activeids += chatbox.replace('_','')+'|'+ccvariable.chatBoxesOrder[chatbox]+',';
						activeChatboxId += chatbox.replace('_','')+',';
						if(ccvariable.chatBoxesOrder[chatbox]>0){
							selfNewMessages = 1;
						}
					}
				}
			}
			ccvariable.newMessages = selfNewMessages;
			activeids = activeids.slice(0, -1);
			activeChatboxId = activeChatboxId.slice(0, -1);
			ccvariable.externalVars["activeChatboxIds"] = activeChatboxId;
			jqcc.cometchat.setSessionVariable('activeChatboxes', activeids);*/
		};
		arguments.callee.c5 = function(){
			branded();
			preinitialize();
			return;
		};
		arguments.callee.c6 = function(){
			preinitialize();
			return;
		};
		arguments.callee.getBaseData = function(){
			return ccvariable.baseData;
		};
		arguments.callee.getActiveId = function(){
			return ccvariable.openChatboxId;
		};
		arguments.callee.getUserID = function(){
		   return ccvariable.userid;
		};
		arguments.callee.getUser = function(id, callbackfn){
		  if (typeof (jqcc.cometchat.getUserFromUID) == 'function') {
			  jqcc.cometchat.getUserFromUID(id, callbackfn);
		  } else {
			  $.ajax({
					url: baseUrl+"cometchat_getid.php",
					data: {userid: id, basedata: ccvariable.baseData},
					cache: false,
					dataType: 'jsonp',
					type: ccvariable.dataMethod,
					timeout: ccvariable.dataTimeout,
					success: function(response){
						jqcc.cometchat.addBuddy(response);
						data = response[0] || response;
						if(data.hasOwnProperty('id')&&data.id!=null&&data.id!='null'&&data.id!=0){
							window[callbackfn](data);
						}else{
							window[callbackfn](0);
						}
					}
				});
			}
		};
		arguments.callee.getUserAuth = function(platform) {
		   var userAuthJson = {};
		   userAuthJson['basedata'] = jqcc.cometchat.getBaseData();
		   userAuthJson['baseurl'] = jqcc.cometchat.getBaseUrl();
		   userAuth = JSON.stringify(userAuthJson);

		   if(platform == "1") {
			   androidCometchat.sendToMobile(userAuth);
		   } else if (platform == "2") {
			   return userAuth;
		   }
		};
		arguments.callee.ping = function(){
			return 1;
		};
		arguments.callee.checkReadReceiptSetting = function(fromid){
			var showReadReceipt = 0;
			if(typeof $.cookie(settings.cookiePrefix+"read") == 'undefined' || $.cookie(settings.cookiePrefix+"read") == null){
				if(jqcc.cometchat.getThemeArray('buddylistReadReceiptSetting',jqcc.cometchat.getUserID()) == 1 && jqcc.cometchat.getThemeArray('buddylistReadReceiptSetting',fromid) == 1){
					showReadReceipt = 1;
				}
			}else if($.cookie(settings.cookiePrefix+"read")=='true' && jqcc.cometchat.getThemeArray('buddylistReadReceiptSetting',fromid) == 1){
				showReadReceipt = 1;
			}
			return showReadReceipt;
		};
		arguments.callee.getLanguage = function(id){
			if(typeof(id) != 'undefined' && id != null && id != ''){
				if(typeof(language[id]) != 'undefined' ){
					return language[id];
				}else{
					return '';
				}
			}
			return language;
		};
		/**
		 * chatWith
		 * @params userid or {'groupid':''} for groups
		 * return open respective chatbox
		 */
		arguments.callee.chatWith = function(id, cccloud){
			if (typeof(id) != 'object') {
				id = parseInt(id);
			}
		    if(typeof(jqcc.cometchat.chatWithUID) == 'function' && typeof(cccloud) != 'undefined'){
			   jqcc.cometchat.chatWithUID(id);
		    }else {
		   		if(typeof(id) == 'number'){
		   			if(typeof (jqcc[calleeAPI]) !== 'undefined' && typeof (jqcc[calleeAPI].chatWith)!=='undefined' && jqcc('#cometchat_synergy_iframe').length==0){
		   				jqcc[calleeAPI].chatWith(id);
		   			} else {
		   				var controlparameters = {"type":"modules", "name":"cometchat", "method":"chatWith", "params":{"uid":id, "synergy":"1"}};
		   				controlparameters = JSON.stringify(controlparameters);
		   				if(typeof(jqcc('#cometchat_synergy_iframe')[0]) != 'undefined'){
		   					jqcc('#cometchat_synergy_iframe')[0].contentWindow.postMessage('CC^CONTROL_'+controlparameters,'*');
		   				}
		   			}
		   		}else if(typeof(id) == 'object' && id.hasOwnProperty('groupid')){
		   			jqcc.cometchat.loadGroup({'id':id.groupid});
		   		}
			}
		};
		/**
		 * getUnreadMessageCount
		 * @params undefined
		 *			returns total count of unread messages including all the contacts and groups.
		 * @params string 'contacts' or 'groups'
		 *			returns total count of unread messages only for one-on-one chat or group chat.
		 * @params {contact: [userid1, userid2, ...], group: [groupid1, groupid2, ...]}
		 * 			returns the sum of unread message counts for the contacts and groups provided in params
		 */
		arguments.callee.getUnreadMessageCount = function(params){
			var cc_state = jqcc.cookie('cc_state');
			if(cc_state==null){
				return 0;
			}
			if(typeof params == 'undefined'){
				params = {contacts: [], groups: []};
			}
			if(typeof params == 'string'){
				if(params == 'contact' || params == 'contacts'){
					params = {contacts: []};
				}else if(params == 'group' || params == 'groups'){
					params = {groups: []};
				}
			}
			if(typeof params != 'object'){
				console.warn('Please check the document to use the JS API getUnreadMessageCountTest');
				return 0;
			}

			var chatboxstates = cc_state.split(':')[2];
			if(chatboxstates == '' || chatboxstates == 'undefined'){
				return 0;
			}

			chatboxstates = chatboxstates.split(',')
			var groupsdata = {};
			var contactsdata = {};
			chatboxstates.forEach(function(chatboxstate){
				states = chatboxstate.split('|');
				if(states[2]=='' || states[2] == 'undefined'){
					states[2] = 0;
				}
				states[2] = parseInt(states[2]);
				if(states[0].charAt(0)=='_'){
					groupsdata[states[0].slice(1)] = states[2];
				}else{
					contactsdata[states[0]] = states[2];
				}
			});
			var unreadmessagecount = 0;
			for(param in params){
				if(param == 'contact' || param == 'contacts'){
					for(contact in contactsdata){
						if($.isEmptyObject(params[param]) || params[param].indexOf(parseInt(contact))!=-1){
							unreadmessagecount += contactsdata[contact];
						}
					}
				}
				if(param == 'group' || param == 'groups'){
					for(group in groupsdata){
						if($.isEmptyObject(params[param]) || params[param].indexOf(parseInt(group))!=-1){
							unreadmessagecount += groupsdata[group];
						}
					}
				}
			}
			return unreadmessagecount;
		}
		arguments.callee.getRecentData = function(id){
			$.ajax({
				cache: false,
				url: baseUrl+"cometchat_receive.php",
				data: {chatbox: id, basedata: ccvariable.baseData},
				dataType: 'jsonp',
				type: ccvariable.dataMethod,
				timeout: ccvariable.dataTimeout,
				success: function(data){
					if(typeof (jqcc[calleeAPI].loadData)!=='undefined'){
						jqcc[calleeAPI].loadData(id, data);
					}
				}
			});
		};
		arguments.callee.getUserDetails = function(ids,callback){
			var isarray = Object.prototype.toString.call(ids) === '[object Array]';
			if(!ids || (typeof ids !== 'number' &&  !isarray)||(typeof ids === 'number' && ids!==parseInt(ids)) || (isarray && ids.length==0)){
				return;
			}
			var id = (isarray && ids.length>0)?ids.join():ids;

			$.ajax({
				url: baseUrl+"cometchat_getid.php",
				data: {userid: id, basedata: ccvariable.baseData},
				type: ccvariable.dataMethod,
				timeout: ccvariable.dataTimeout,
				cache: false,
				dataType: 'jsonp',
				success: function(data){
					jqcc.cometchat.addBuddy(data);
					if(ccvariable.callbackfn=='mobilewebapp'){
						jqcc[ccvariable.callbackfn].loadUserData(id, data);
					}
					if(callback){
						if(typeof jqcc[calleeAPI][callback]){
							jqcc[calleeAPI][callback](ids);
						}
					}
				}
			});
		};
		arguments.callee.launchModule = function(id){
			if(typeof (jqcc[calleeAPI].launchModule)!=='undefined' && jqcc("#cometchat").length > 0){
				jqcc[calleeAPI].launchModule(id);
			} else {
				var controlparameters = {"type":"modules", "name":"cometchat", "method":"launchModule", "params":{"uid":id, "synergy":"1","embedded":"1"}};
				controlparameters = JSON.stringify(controlparameters);
				if(typeof(jqcc('#cometchat_synergy_iframe')[0]) != 'undefined'){
					jqcc('#cometchat_synergy_iframe')[0].contentWindow.postMessage('CC^CONTROL_'+controlparameters,'*');
				}
			}
		};
		arguments.callee.toggleModule = function(id){
			if(typeof (jqcc[calleeAPI].toggleModule)!=='undefined'){
				jqcc[calleeAPI].toggleModule(id);
			}
		};
		arguments.callee.closeModule = function(id){
			if(typeof (jqcc[calleeAPI].closeModule)!=='undefined'){
				jqcc[calleeAPI].closeModule(id);
			}
		};
		arguments.callee.closeAllModule = function(){
			if(typeof (jqcc[calleeAPI].closeAllModule)!=='undefined'){
				jqcc[calleeAPI].closeAllModule();
			}
		};
		arguments.callee.closeChatbox = function(id){
			if(typeof (jqcc[calleeAPI].closeChatbox)!=='undefined'){
				jqcc[calleeAPI].closeChatbox(id);
			}
		};
		arguments.callee.joinChatroom = function(roomid, inviteid, roomname){
			if(typeof (jqcc[calleeAPI].joinChatroom)!=='undefined'){
				jqcc.cometchat.chatroom(roomid, roomname, 2, inviteid);
			}
		};
		arguments.callee.createChatboxSet = function(id, name, status, message, avatar, link, isdevice, chatboxstate, unreadmessagecount, restored){
			if(id != 0 || !isNaN(id) || typeof(id) != "undefined"){
				$.ajax({
					url: baseUrl+"cometchat_getid.php",
					data: {userid: id, basedata: ccvariable.baseData},
					dataType: 'jsonp',
					type: ccvariable.dataMethod,
					timeout: ccvariable.dataTimeout,
					cache: false,
					success: function(response){
						if(response){
							jqcc.cometchat.addBuddy(response);
							var data = response[0] || response;
							if(data.hasOwnProperty('id')&&data.id!=null&&data.id!='null'&&data.id!=0){
							jqcc[settings.theme].createChatbox(id, data.n, data.s, data.m, data.a, data.l, data.d, chatboxstate, unreadmessagecount, restored);
							}
						}
					},
					error: function(data){
						jqcc.cometchat.setThemeVariable('trying', id, 5);
					}
				});
			}
		};
		arguments.callee.updateChatboxSet = function(id,prepend){
			var postVars={chatbox: id, basedata: ccvariable.baseData};
			if(typeof(prepend)!=="undefined"){
				postVars["prepend"]=prepend;
			}
			$.ajax({
				cache: false,
				url: baseUrl+"cometchat_receive.php",
				data: postVars,
				type: ccvariable.dataMethod,
				timeout: ccvariable.dataTimeout,
				dataType: 'jsonp',
				success: function(data){
					if(data){
						if(typeof(prepend)!=="undefined"){
							jqcc[settings.theme].prependMessages(id, data);
						}else{
							jqcc[settings.theme].updateChatboxSuccess(id, data);
						}
					}
				}
			});
		};
		arguments.callee.chatboxKeydownSet = function(id, message, callbackfn,localmessagekey){
			var localmessageid = jqcc.cometchat.updateOfflinemessages({
				"id": id,
				"message":message,
				"localmsgid":localmessagekey,
				'msgStatus':1
			});
			if(localmessageid != '' && localmessageid != 'undefined') {
				jqcc[calleeAPI].addMessages([{"from": id, "message": message, "broadcast": 0, "direction": 2, "calledfromsend": 0, "localmessageid": localmessageid}]);
			}
			if(typeof(callbackfn) === "undefined" || callbackfn !="") {
				callbackfn = ccvariable.callbackfn;
			}
			ccvariable.sendVars["callbackfn"] = callbackfn;
			if(message.length>1000){
				if(message.indexOf(" ") == -1 || message.indexOf(" ") >= 1000) {
					message = message.substr(0,999)+" "+message.substr(999,message.length);
				}
				if(message.charAt(999)==' '){
					messagecurrent = message.substring(0, 1000);
				}else{
					messagecurrent = message.substring(0, 1000);
					var spacePos = messagecurrent.length;
					while(messagecurrent.charAt(spacePos)!=' '){
						spacePos--;
					}
					messagecurrent = message.substring(0, spacePos);
				}
				messagenext = message.substring(messagecurrent.length);
				if(messagenext.length>0){
					messagecurrent = messagecurrent+"...";
				}
			}else{
				messagecurrent = message;
				messagenext = '';
			}
			message = messagecurrent;
			sendAjax = function (broadcastflag) {

				sendajax = false;
				$.ajax({
					url: baseUrl+"cometchat_send.php",
					data: ccvariable.sendVars,
					dataType: 'jsonp',
					type: ccvariable.dataMethod,
					timeout: ccvariable.dataTimeout,
					success: function(data){
						ccvariable.sendVars = {};
						if(data != null && typeof(data) != 'undefined'){
							var offlinemessages = jqcc.cometchat.getFromStorage('offlinemessagesqueue');
							if(data.hasOwnProperty("localmessageid") && typeof(data.localmessageid) != 'undefined') {
								if(offlinemessages.hasOwnProperty(data.localmessageid)) {
									delete offlinemessages[data.localmessageid];
									jqcc.cometchat.updateToStorage('offlinemessagesqueue',offlinemessages);
								}
							}else {
								jqcc.each(data,function(key,value) {
									if(offlinemessages.hasOwnProperty(value.localmessageid)) {
										delete offlinemessages[value.localmessageid];
										jqcc.cometchat.updateToStorage('offlinemessagesqueue',offlinemessages);
									}
								});
							}
							ccvariable.localmessageid = 0;
							if(jqcc.isEmptyObject(jqcc.cometchat.getFromStorage('offlinemessagesqueue'))) {
								jqcc.cometchat.updateToStorage('offmsgcounter',{'lmid':0});
							}
							if(typeof (jqcc[calleeAPI].addMessages)!=='undefined'){
								if(broadcastflag){
									jqcc[calleeAPI].addMessages(data);
								}else{
									jqcc[calleeAPI].addMessages([{"from": id, "message": data.m, "id": data.id, "broadcast": 0, "direction": data.direction, "calledfromsend": 1, "localmessageid":data.localmessageid}]);
									var alreadyreceivedunreadmessages = jqcc.cometchat.getFromStorage('receivedunreadmessages');
									var arr = Object.keys(alreadyreceivedunreadmessages).map(function(k) { return alreadyreceivedunreadmessages[k] });
									var maxmsgid = Math.max.apply(null,arr);
									if( data.id < maxmsgid){
										jqcc.cometchat.updateToStorage('receivedunreadmessages',{});
									}
								}
							}
						}
						jqcc.cometchat.stimulateHeartbeat({heartbeatTime:settings.minHeartbeat});
						ccvariable.sendVars = {};
						sendajax = true;
					},
				   error: function(data){
	   					jqcc.cometchat.updateOfflinemessages({
							"id": id,
							"message":message,
							"localmsgid":localmessageid,
							'msgStatus':0
						});
						sendajax = true;
						if(jqcc.isEmptyObject(broadcastData)){
							sendbroadcastinterval = 0;
							clearInterval(sendbroadcastinterval);
						}
				   }
			   });
			}
			$( document ).ajaxStop(function() {
				sendajax = true;
				if(jqcc.isEmptyObject(broadcastData)){
					sendbroadcastinterval = 0;
					clearInterval(sendbroadcastinterval);
				}
			});
			if(sendajax == true){
				ccvariable.sendVars["basedata"] = ccvariable.baseData;
				if(jqcc.isEmptyObject(broadcastData)){
					ccvariable.sendVars["to"] = id;
					ccvariable.sendVars["message"] = message;
					ccvariable.sendVars['localmessageid'] = localmessageid;
					var broadcastflag = 0;
				}else{
					broadcastData[localmessageid] = {"to":id,"message":message};
					ccvariable.sendVars["broadcast"] = broadcastData;
					var broadcastflag = 1;
				}
				sendAjax(broadcastflag);
				ccvariable.sendVars = {};
			}else{
				broadcastData[localmessageid] = {"to":id,"message":message};
				if(sendbroadcastinterval == 0){
					sendbroadcastinterval = setInterval(function(){
						sendbroadcastinterval = 0;
						clearInterval(sendbroadcastinterval);
						if(jqcc.isEmptyObject(broadcastData)){
							clearInterval(sendbroadcastinterval);
						}
						if(sendajax == true && !jqcc.isEmptyObject(broadcastData)){
							sendbroadcastinterval = 0;
							clearInterval(sendbroadcastinterval);
							ccvariable.sendVars["basedata"] = ccvariable.baseData;
							ccvariable.sendVars["broadcast"] = broadcastData;
							sendAjax(1);
							broadcastData = {};
							ccvariable.sendVars = {};
						}
					}, 50);
				}
			}
			if(messagenext.length>0){
				jqcc.cometchat.chatboxKeydownSet(id, '...'+messagenext);
			}
		};

		arguments.callee.sendMessage = function(id, message){
			if(jqcc("#cometchat").length > 0 || jqcc(".cometchat_ccmobiletab_redirect").length > 0) {
				jqcc.cometchat.chatboxKeydownSet(id,message);
			} else {
				var controlparameters = {"type":"modules", "name":"cometchat", "method":"sendMessage", "params":{"uid":id, "message":message, "synergy":"1","embedded":"1"}};
				controlparameters = JSON.stringify(controlparameters);
				if(typeof(jqcc('#cometchat_synergy_iframe')[0]) != 'undefined'){
					jqcc('#cometchat_synergy_iframe')[0].contentWindow.postMessage('CC^CONTROL_'+controlparameters,'*');
				}
			}
		};

		arguments.callee.addMessage = function(boxid,message,msgid,nopopup){
			if(typeof(nopopup) === "undefined" || nopopup =="") {
				nopopup = 0;
			}
			if(typeof (jqcc[calleeAPI].addMessages)!=='undefined'){
				jqcc[calleeAPI].addMessages([{"from": boxid, "message": message, "self": 1, "old": 1, "id": msgid, "sent": Math.floor(new Date().getTime()), "nopopup": nopopup}]);
			}
			if(typeof (jqcc[calleeAPI].scrollDown)!=='undefined'){
				jqcc[calleeAPI].scrollDown(boxid);
			}
		};
		arguments.callee.updateOfflinemessages = function(obj){
			var options = {"msgStatus" : 1};
			var localmessageid = '';
			var offlinemessagequeue = jqcc.cometchat.getFromStorage('offlinemessagequeue');
			$.extend( true, options, obj );
			if(options.id != '' && options.id != 'undefined' && options.message != '' && options.message != 'undefined') {
				if(typeof(options.localmsgid) != 'undefined' && options.localmsgid != "") {
					localmessageid = options.localmsgid;
				}else {
					var currentdate = new Date();
					ccvariable.localmessageid = currentdate.getTime();
					localmessageid = '_'+ccvariable.localmessageid;
					jqcc.cometchat.updateToStorage('offmsgcounter',{'lmid':localmessageid});
				}
				if(typeof(options.chatroommode) != 'undefined' && options.chatroommode != '') {
					options['chatroommode'] = options.chatroommode;
				}
				if(typeof(options.type) != 'undefined' && options.type != '') {
					options.message['localmsgid'] = localmessageid;
				}
				offlinemessagequeue[localmessageid] = options;
				jqcc.cometchat.updateToStorage('offlinemessagesqueue',offlinemessagequeue);
				return localmessageid;
			}
		};
		arguments.callee.statusSendMessageSet = function(message){
			$.ajax({
				url: baseUrl+"cometchat_send.php",
				data: {statusmessage: message, basedata: ccvariable.baseData},
				dataType: 'jsonp',
				type: ccvariable.dataMethod,
				timeout: ccvariable.dataTimeout,
				success: function(data){
					ccvariable.statusmessage = message;
					if(typeof jqcc[settings.theme].statusSendMessageSuccess != "undefined") {
						jqcc[settings.theme].statusSendMessageSuccess();
					}
				},
				error: function(data){
					if (typeof jqcc[settings.theme].statusSendMessageError != "undefined") {
					   jqcc[settings.theme].statusSendMessageError();
				   }
				}
			});
		};
		arguments.callee.updateSettings = function(guestname, statusmessage, status, lastseensetting, readreceiptsetting){
			$.ajax({
				url: baseUrl+"cometchat_send.php",
				data: {statusmessage: statusmessage, basedata: ccvariable.baseData, status: status, lastseenSettingsFlag: lastseensetting, readreceiptSettingsFlag: readreceiptsetting, guestname: guestname},
				dataType: 'jsonp',
				type: ccvariable.dataMethod,
				timeout: ccvariable.dataTimeout,
				success: function(data){
					//jqcc[settings.theme].updateSettingsSuccess();
				},
				error: function(data){
					//jqcc[settings.theme].updateSettingsError();
				}
			});
		};
		arguments.callee.updateReadReceipt = function(readreceiptsetting){
			$.ajax({
				url: baseUrl+"cometchat_send.php",
				data: {readreceiptsetting: readreceiptsetting},
				dataType: 'jsonp',
				type: ccvariable.dataMethod,
				timeout: ccvariable.dataTimeout,
				error: function(data){
				}
			});
		};
		arguments.callee.setGuestNameSet = function(guestname){
			$.ajax({
				url: baseUrl+"cometchat_send.php",
				data: {guestname: guestname, basedata: ccvariable.baseData},
				dataType: 'jsonp',
				type: ccvariable.dataMethod,
				timeout: ccvariable.dataTimeout,
				success: function(data){
					settings = jqcc.cometchat.getSettings();
					if(settings.uniqueguestname == 1){
						if (data.hasOwnProperty('error') && data.error == 0){
							ccvariable.displayname = guestname;
							if(typeof jqcc[settings.theme].setGuestNameSuccess != "undefined") {
								jqcc[settings.theme].setGuestNameSuccess();
							}
						}else{
							if(typeof jqcc[settings.theme].resetGuestName != "undefined") {
								jqcc[settings.theme].resetGuestName(function(){
									alert(data.message);
								});
							}
						}
					}else{
						ccvariable.displayname = guestname;
						if(typeof jqcc[settings.theme].setGuestNameSuccess != "undefined") {
							jqcc[settings.theme].setGuestNameSuccess();
						}
					}
				},
				error: function(data){
					if(typeof jqcc[settings.theme].setGuestNameError != "undefined") {
						jqcc[settings.theme].setGuestNameError();
					}
				}
			});
		};
		arguments.callee.hideBar = function(){
			if(typeof (jqcc[calleeAPI].hideBar)!=='undefined'){
				jqcc[calleeAPI].hideBar();
			}
		};
		arguments.callee.getBaseUrl = function(){
			return baseUrl;
		};
		arguments.callee.getStaticCDNUrl = function(){
			return staticCDNUrl;
		};
		arguments.callee.setAlert = function(id, number){
			if(typeof (jqcc[calleeAPI].setModuleAlert)!=='undefined'){
				jqcc[calleeAPI].setModuleAlert(id, number);
			}
		};
		arguments.callee.closeTooltip = function(){
			if(typeof (jqcc[calleeAPI].closeTooltip)!=='undefined'){
				jqcc[calleeAPI].closeTooltip();
			}
		};
		arguments.callee.scrollToTop = function(){
			if(typeof (jqcc[calleeAPI].scrollToTop)!=='undefined'){
				if ((jqcc.cometchat.membershipAccess('scrolltotop','modules'))){
					jqcc[calleeAPI].scrollToTop();
				}
			}
		};
		arguments.callee.goToHomePage = function(){
			if ((jqcc.cometchat.membershipAccess('home','modules'))){
				location.href = "/";
			}
		};
		arguments.callee.reinitialize = function(){
			ccvariable.baseData = $.cookie(settings.cookiePrefix+'data');
			if(typeof (jqcc[calleeAPI].reinitialize)!=='undefined'){
				jqcc[calleeAPI].reinitialize();
			}
		};
		arguments.callee.updateHtml = function(id, temp){
			if(typeof (jqcc[calleeAPI].updateHtml)!=='undefined'){
				jqcc[calleeAPI].updateHtml(id, temp);
			}
		};
		arguments.callee.processMessage = function(id, value){
			if(typeof (jqcc[calleeAPI].processMessage)!=='undefined'){
				return jqcc[calleeAPI].processMessage(id, value);
			}
		};
		arguments.callee.replaceHtml = function(id, value){
			replaceHtml(id, value);
		};
		arguments.callee.getSettings = function(e){
			return settings;
		};
		arguments.callee.getMobileappdetails = function(e){
			return mobileappdetails;
		};

		arguments.callee.getTrayicon = function(e){
			return trayicon;
		};
		arguments.callee.getCcvariable = function(e){
			return ccvariable;
		};
		arguments.callee.echo = function(e){
			return "ECHO";
		};
		arguments.callee.getWebrtcPlugins = function(e){
			return webrtcplugins;
		};
		arguments.callee.subscribe = function(callbackData){
			$.each(callbackData,function(callbackKey,callbacks){
				if(typeof callbackKey == 'string' && typeof callbacks == 'object'){
					ccvariable.registeredCallbacks[callbackKey] = callbacks;
					window[callbackKey] = callbacks;
				}
			})
		};
		arguments.callee.processSubscribeCallback = function(callbackKey,data){
			if(window[callbackKey] !== undefined && ccvariable.registeredCallbacks.hasOwnProperty(callbackKey)){
				$.each(window[callbackKey],function(index,callback){
					callback(data);
				})
			}
		};
		arguments.callee.disableLayout = function(){
			if(typeof(jqcc[calleeAPI].disableLayout) !== 'undefined'){
				jqcc[calleeAPI].disableLayout();
			}
		};
		arguments.callee.addBuddy = function(params){
			if(params.hasOwnProperty('id')){
				params = [params];
			}
			$.each(params,function(i,user){
			   if(user.hasOwnProperty('id')&&user.id!=null&&user.id!='null'&&user.id!=0){
					var id = user.id;
					ccvariable.buddylistName[id] = user.n;
					ccvariable.buddylistMessage[id] = user.m;
					ccvariable.buddylistStatus[id] = user.s;
					ccvariable.buddylistAvatar[id] = user.a;
					ccvariable.buddylistLink[id] = user.l||'';
					ccvariable.buddylistIsDevice[id] = user.d||0;
					ccvariable.buddylistChannelHash[id] = user.ch||'';
					ccvariable.buddylistLastseen[id] = user.ls||'';
					ccvariable.buddylistLastseensetting[id] = user.lstn||0;
					ccvariable.buddylistReadReceiptSetting[id] = user.rdrs||0;
				}
			});
		};
		arguments.callee.updateJabberOnlineNumber = function(number){
			if(typeof (jqcc[calleeAPI].updateJabberOnlineNumber)!=='undefined'){
				jqcc[calleeAPI].updateJabberOnlineNumber(number);
			}
		};
		arguments.callee.getName = function(id){
			if(typeof (ccvariable.buddylistName[id])!=='undefined'){
				return ccvariable.buddylistName[id];
			}
		};
		arguments.callee.lightbox = function(name,caller,windowMode){
			if (jqcc.cometchat.membershipAccess(name,'modules')){
				var allowpopout = 0;
				var callbackfn='';
				var cc_layout = jqcc.cometchat.getChatroomVars('calleeAPI');
				if(ccvariable.callbackfn=='desktop'){
					callbackfn='desktop';
				}
				if(ccvariable.mobileDevice){
					callbackfn='mobilewebapp';
				}
				if(typeof(windowMode)=="undefined"){
					windowMode = 0;
				}
				var callerUrl = "";
				if(typeof(caller) != "undefined"){
					callerUrl = "caller="+caller;
				}
				if(trayicon[name]){
					if(cc_layout == 'docked' && ccvariable.mobileDevice){
						windowMode = 1;
					}

					if(name=='chatrooms'||name=='games'||name=='broadcastmessage'){
						allowpopout = 1;
						if(settings.theme == 'lite' && name=='chatrooms'){
							jqcc[calleeAPI].minimizeOpenChatbox();
						}
					}
					loadCCPopup(trayicon[name][2]+'?'+callerUrl+'&callbackfn='+callbackfn, trayicon[name][0], "status=0,toolbar=0,menubar=0,directories=0,resizable=0,location=0,status=0,scrollbars=0, width="+(Number(trayicon[name][4])+2)+",height="+trayicon[name][5]+"", Number(trayicon[name][4])+2, trayicon[name][5], trayicon[name][1], 0, 0, 0, allowpopout,windowMode);
				}
			}
		};
		arguments.callee.sendStatus = function(message){
			$.ajax({
				url: baseUrl+"cometchat_send.php",
				data: {status: message, basedata: ccvariable.baseData},
				dataType: 'jsonp',
				type: ccvariable.dataMethod,
				timeout: ccvariable.dataTimeout,
				success: function(data){
					ccvariable.currentStatus = message;
					if(typeof(jqcc[calleeAPI].updateStatus)=='function'){
						jqcc[settings.theme].removeUnderline();
						jqcc[calleeAPI].updateStatus(message);
					}
				}
			});
		};
		arguments.callee.tryClickSync = function(id){
			if(ccvariable.buddylistName[id]==null||ccvariable.buddylistName[id]==''){
				if(ccvariable.trying[id]<5){
					setTimeout(function(){
						jqcc.cometchat.tryClickSync(id);
					}, 500);
				}
			}else{
				jqcc.cometchat.chatWith(id);
			}
		};
		arguments.callee.tryClick = function(id){
			if(ccvariable.buddylistName[id]==null||ccvariable.buddylistName[id]==''){
				if(ccvariable.trying[id]<5){
					setTimeout(function(){
						jqcc.cometchat.tryClick(id);
					}, 500);
				}
			}else{
				if(ccvariable['openChatboxId'].indexOf(id)==-1){
					jqcc.cometchat.chatWith(id);
				}
			}
		};
		arguments.callee.notify = function(title, image, message, clickEvent, id, msgid){

			if(typeof jqcc.cometchat.getFromStorage('loggedin_'+jqcc.cometchat.getUserID(),{'lastnotifiedmessageid':0}).lastnotifiedmessageid == "undefined"){
				jqcc.cometchat.updateToStorage('loggedin_'+jqcc.cometchat.getUserID(),{'lastnotifiedmessageid':0});
			}

			if(jqcc.cometchat.getFromStorage('loggedin_'+jqcc.cometchat.getUserID(),{'lastnotifiedmessageid':0}).lastnotifiedmessageid != msgid && jqcc.cometchat.getFromStorage('loggedin_'+jqcc.cometchat.getUserID(),{'lastnotifiedmessageid':0}).lastnotifiedmessageid < msgid){
				jqcc.cometchat.updateToStorage('loggedin_'+jqcc.cometchat.getUserID(),{'lastnotifiedmessageid':msgid});
				if(navigator.userAgent.match(/chrome|firefox/i)&&settings.desktopNotifications==1){
					if(ccvariable.callbackfn=="desktop"&&typeof title!='undefined'&&typeof image!='undefined'&&typeof message!='undefined'){
						jqcc.ccdesktop.desktopNotify(image, message, ccvariable.buddylistName[id], msgid);
					}else if(ccvariable.idleFlag){
						if (Notification.permission !== 'denied') {
							Notification.requestPermission(function (permission) {
								if(!('permission' in Notification)) {
									Notification.permission = permission;
								}
							});
						}
						if(Notification.permission === "granted"&&typeof title!='undefined'&&typeof image!='undefined'&&typeof message!='undefined'){
							tempMsg = jqcc('<div>'+message+'</div>');
							jqcc.each(tempMsg.find('img.cometchat_smiley'),function(){
								jqcc(this).replaceWith('*'+jqcc(this).attr('title')+'*');
							});
							message = tempMsg.text();
							if(typeof id!='undefined'){
								if(typeof ccvariable.desktopNotification[id]=="undefined"){
									ccvariable.desktopNotification[id] = {};
								}
								ccvariable.desktopNotification[id][msgid] = new Notification(title, {icon: image, body: message});
								ccvariable.desktopNotification[id][msgid].onclick = function(){
									if(typeof clickEvent=='function'){
										clickEvent();
									}
								};
							}else{
								ccvariable.desktopNotification[id][msgid] = new Notification(title, {icon: image, body: message});
								ccvariable.desktopNotification[id][msgid].onclick = function(){
									if(typeof clickEvent=='function'){
										clickEvent();
									}
								};
							}
						}
					}
				}
			}
		};
		arguments.callee.statusKeydown = function(event, statustextarea){
			if(event.keyCode==13&&event.shiftKey==0){
				if(typeof (jqcc[calleeAPI].statusSendMessage)!=='undefined'){
					jqcc[calleeAPI].statusSendMessage();
				}
				return false;
			}
		};
		arguments.callee.guestnameKeydown = function(event, statustextarea){
			if(event.keyCode==13&&event.shiftKey==0){
				if(typeof (jqcc[calleeAPI].setGuestName)!=='undefined'){
					jqcc[calleeAPI].setGuestName(statustextarea);
				}
				return false;
			}
		};
		arguments.callee.minimizeAll = function(){
			if(jqcc("#cometchat").length > 0){
				jqcc[settings.theme].minimizeAll();
			} else {
				var controlparameters = {"type":"modules", "name":"cometchat", "method":"minimizeAll", "params":{"uid":"", "synergy":"1" ,"embedded":"1"}};
				controlparameters = JSON.stringify(controlparameters);
				if(typeof(jqcc('#cometchat_synergy_iframe')[0]) != 'undefined'){
					jqcc('#cometchat_synergy_iframe')[0].contentWindow.postMessage('CC^CONTROL_'+controlparameters,'*');
				}
			}
		};
		arguments.callee.processcontrolmessage = function(incoming){
			var callbackParameter = incoming;
			var processedMessage = '';
			if(typeof incoming != 'undefined' && incoming.hasOwnProperty('groupid')){
				jqcc.cometchat.processgroupcontrolmessage(incoming);
			}
			if(typeof incoming.message != "undefined" && (incoming.message).indexOf('CC^CONTROL_')!=-1){
				var message = (incoming.message).replace('CC^CONTROL_','');
				var data = incoming.message.split('_');
				var chatroommode = 0;
				var hasChatroom = 0;
				settings = jqcc.cometchat.getSettings();
				if(settings.disableGroupTab == 0) {
                     hasChatroom = 1;
                }
				if(typeof(data[5])!='undefined' && data[5]==1){
					chatroommode = 1;
				}
				if(cp = IsJsonString(message)){
					var type = cp["type"] || "",
						name = cp["name"] || "",
						method = cp["method"] || "",
						params = cp["params"] || {};
					switch(type){
						case 'core':
							switch(name){
								case 'bots':
									var botid = parseInt(params.botid);
									incoming.botid = botid;
									processedMessage = params.message;
									break;
								case 'textchat':
									if(typeof jqcc[calleeAPI][method] == "function"){
										jqcc[calleeAPI][method](params);
									}
									processedMessage = null;
									break;
								default:
									if(typeof jqcc[calleeAPI][method] == "function" && ccvariable.callbackfn != "mobilewebapp"){
										jqcc[calleeAPI][method](params);
									}
									break;
							}
							break;
						case 'plugins':
							message = JSON.parse(message);
							processedMessage = jqcc['cc'+name.toLowerCase()].processControlMessage(params);
							break;
						default:
							break;
					}
				} else if(data[1]=='PLUGIN'){
					switch(data[2]){
						case 'AVCHAT':
						switch(data[3]){
							case 'ENDCALL':
							var controlparameters = {"type":"plugins", "name":"avchat", "method":"endcall", "params":{"grp":data[4], "chatroommode":chatroommode}};
							break;
							case 'REJECTCALL':
							var controlparameters = {"type":"plugins", "name":"avchat", "method":"rejectcall", "params":{"grp":data[4], "chatroommode":chatroommode}};
							jqcc[calleeAPI].removeAVchatContainer(incoming.from);
							break;
							case 'NOANSWER':
							var controlparameters = {"type":"plugins", "name":"avchat", "method":"noanswer", "params":{"grp":data[4], "chatroommode":chatroommode}};
							jqcc[calleeAPI].removeAVchatContainer(incoming.from);
							break;
							case 'CANCELCALL':
							var controlparameters = {"type":"plugins", "name":"avchat", "method":"canceloutgoingcall", "params":{"grp":data[4], "chatroommode":chatroommode}};
							jqcc[calleeAPI].removeAVchatContainer(incoming.from);
							break;
							case 'BUSYCALL':
							var controlparameters = {"type":"plugins", "name":"avchat", "method":"busycall", "params":{"grp":data[4], "chatroommode":chatroommode}};
							break;
							case 'INITIATECALL':
								var controlparameters = {"type":"plugins", "name":"avchat", "method":"initiatecall", "params":{"grp":data[4], "chatroommode":chatroommode, "caller": data[6], "direction": data[7]}};
							break;
							default :
							message = '';
							break;
						}
						break;
						case 'AUDIOCHAT':
						switch(data[3]){
							case 'ENDCALL':
							var controlparameters = {"type":"plugins", "name":"audiochat", "method":"endcall", "params":{"grp":data[4], "chatroommode":chatroommode}};
							break;
							case 'REJECTCALL':
							var controlparameters = {"type":"plugins", "name":"audiochat", "method":"rejectcall", "params":{"grp":data[4], "chatroommode":chatroommode, "fromid":incoming.from}};
							break;
							case 'NOANSWER':
							var controlparameters = {"type":"plugins", "name":"audiochat", "method":"noanswer", "params":{"grp":data[4], "chatroommode":chatroommode, "fromid":incoming.from}};
							break;
							case 'CANCELCALL':
							var controlparameters = {"type":"plugins", "name":"audiochat", "method":"canceloutgoingcall", "params":{"grp":data[4], "chatroommode":chatroommode, "fromid":incoming.from}};
							break;
							case 'BUSYCALL':
							var controlparameters = {"type":"plugins", "name":"audiochat", "method":"busycall", "params":{"grp":data[4], "chatroommode":chatroommode, "fromid":incoming.from}};
							break;
							default :
							message = '';
							break;
						}
						break;
						case 'BROADCAST':
						switch(data[3]){
							case 'ENDCALL':
							var controlparameters = {"type":"plugins", "name":"broadcast", "method":"endcall", "params":{"grp":data[4], "chatroommode":chatroommode}};
							break;
							default :
							message = '';
							break;
						}
						break;
						default :
						break;
					}
					if(typeof(data[2]) == 'undefined'){return;}
					processedMessage = jqcc['cc'+data[2].toLowerCase()].processControlMessage(controlparameters);
				} else {
						if (hasChatroom) {
							switch(data[1]){
								case 'kicked':
									if (jqcc.cometchat.getChatroomVars('myid') == data[2]) {
										alert ("<?php echo $chatrooms_language['kicked'];?>");
										jqcc.cometchat.leaveChatroom(incoming.chatroomid,'kick');
									}
									processedMessage = '';
									break;
								case 'banned':
									var roomindex = jqcc.cometchat.getChatroomVars('joinedrooms').indexOf(incoming.chatroomid);
									if (jqcc.cometchat.getChatroomVars('myid') == data[2] && roomindex > -1) {
										alert ("<?php echo $chatrooms_language['banned'];?>");
										jqcc.cometchat.leaveChatroom(incoming.chatroomid, 'ban');
									}
									processedMessage = '';
									break;
								case 'deletemessage':
									if(jqcc.cometchat.getChatroomVars('calleeAPI')=='docked') {
									   $("#cometchat_groupmessage_"+data[2]).remove();
									}else{
									   $("#cometchat_groupmessage_"+data[2]).parent().remove();
									}
									processedMessage = '';
									break;
								case 'deletedchatroom':
									var roomindex = jqcc.cometchat.getChatroomVars('joinedrooms').indexOf(incoming.id);
									if(roomindex > -1){
										jqcc.cometchat.leaveChatroom(incoming.chatroomid);
										var params = {'chatid':data[2],'isgroup':1,'timestamp':incoming.sent,'m':'','msgid':incoming.id,'force':0,'del':1};
										jqcc.cometchat.updateRecentChats(params);
										alert ("<?php echo $chatrooms_language['room_deleted'];?>");
									}
									processedMessage = '';
									break;
								default :
									break;
							}
						}
				}
			}else if(typeof incoming.message != "undefined" && ((incoming.message).indexOf('has successfully sent a file')!=-1 || (incoming.message).indexOf('has sent you a file')!=-1)){
				if(ccvariable.callbackfn=="desktop"){
					if(incoming.message.indexOf('target')>=-1){
						incoming.message=incoming.message.replace(/target="_blank"/g,'');
					}
				}
				 processedMessage = incoming.message;
			}else if(typeof incoming.message != "undefined" && ((incoming.message).indexOf('has successfully sent a handwritten message')!=-1 || (incoming.message).indexOf('has sent you a handwritten message')!=-1)){
				/*if(jqcc.cometchat.getCcvariable().callbackfn=="desktop"){
					if(incoming.message.indexOf('href')>=-1){
						var start = (incoming.message).indexOf('href');
						var end = (incoming.message).indexOf('target');
						var HtmlString=(incoming.message).slice(start,end);
						incoming.message=(incoming.message).replace(HtmlString,'');
					}
				}*/
				 processedMessage = incoming.message;
			} else if(typeof incoming.message != "undefined") {
				if(ccvariable.callbackfn=="desktop"){
					if((incoming.message).indexOf('has shared a file')!=-1){
						if(incoming.message.indexOf('target')>=-1){
							incoming.message=incoming.message.replace(/target="_blank"/g,'');
						}
						processedMessage = incoming.message;
					}else if((incoming.message).indexOf('has shared a handwritten message')!=-1){
						/*if(incoming.message.indexOf('href')>=-1){
							var start = (incoming.message).indexOf('href');
							var end = (incoming.message).indexOf('target');
							var HtmlString=(incoming.message).slice(start,end);
							incoming.message=(incoming.message).replace(HtmlString,'');
						}*/
						processedMessage = incoming.message;
					}else{
						processedMessage = incoming.message;
					}
				}else{
					processedMessage = incoming.message;
				}
			}
			callbackParameter.message = processedMessage;
			if(callbackParameter.hasOwnProperty('chatroomid') || callbackParameter.hasOwnProperty('roomid'))
				jqcc.cometchat.processSubscribeCallback('onGroupMessageReceived',callbackParameter);
			else
				jqcc.cometchat.processSubscribeCallback('onMessageReceived',callbackParameter);
			return processedMessage;
		}
		arguments.callee.closeCRPopout = function(params){

		}
		arguments.callee.typingTo = function(params){
			if(settings.cometserviceEnabled == 1 && settings.istypingEnabled == 1){
				var senttime = (new Date()).getTime()+jqcc.cometchat.getThemeVariable('timedifference');
				var channel = jqcc.cometchat.getThemeArray('buddylistChannelHash', params.id);
				if(typeof channel != 'undefined' && channel != ''){
					var controlparameters = {
						type:'core',
						name:'textchat',
						method:params.method,
						params:{
							fromid:ccvariable.userid,
							typingtime:senttime
						}
					};
					var jsondata = {
						channel: (transport == 'cometserviceselfhosted' ? '/' : '')+channel,
						message:{
							from: ccvariable.userid,
							message: 'CC^CONTROL_'+JSON.stringify(controlparameters),
							sent: senttime,
							self:0
						},
						callback:''
					};
					COMET.publish(jsondata);
				}
			}
		}
		arguments.callee.sendReceipt = function(incoming, receipt){
			if(!incoming.hasOwnProperty('id') || incoming.id==='' || incoming.id ==  undefined || typeof incoming.old == 'undefined' || incoming.old == 1 || typeof incoming.self == 'undefined' || incoming.self == 1){
				return;
			}
			var fromid = incoming.from;
			var messageid = incoming.id;
			ccvariable.lastmessagereadstatus[fromid] = 1;

			if(typeof receipt == 'undefined' && incoming.self == 0 && ccvariable.currentStatus != 'invisible'){
				receipt = 'deliveredMessageNotify';
				ccvariable.lastmessagereadstatus[fromid] = 0;
				if(ccvariable['openChatboxId'].indexOf(fromid) > -1 && ccvariable.windowFocus == true){
					receipt = 'deliveredReadMessageNotify';
					ccvariable.lastmessagereadstatus[fromid] = 1;
				}
			}
			if(settings.cometserviceEnabled == 1 && settings.messagereceiptEnabled == 1 && typeof receipt != 'undefined' && incoming.id != undefined){
				var channel = jqcc.cometchat.getThemeArray('buddylistChannelHash',fromid);
				if(typeof channel != 'undefined' && channel != ''){
					var controlparameters = {
						type:'core',
						name:'textchat',
						method:receipt,
						params:{
							fromid:ccvariable.userid,
							message:messageid
						}
					};
					var jsondata = {
						channel: (transport == 'cometserviceselfhosted' ? '/' : '')+channel,
						message:{
							from: ccvariable.userid,
							message: 'CC^CONTROL_'+JSON.stringify(controlparameters),
							sent: (new Date()).getTime() + jqcc.cometchat.getThemeVariable('timedifference'),
							self:0
						},
						callback:''
					};
					COMET.publish(jsondata);
				}
			}
		},
		arguments.callee.sociallogin = function(social_details){
			jqcc.ajax({
				url: baseUrl+"cometchat_login.php?socialLogin=1&callbackfn="+ccvariable.callbackfn,
				data: {social_details:social_details},
				dataType: 'jsonp',
				type: ccvariable.dataMethod,
				timeout: ccvariable.dataTimeout,
				success: function(data){
					postMessage('cc_reinitializeauth','*');
					jqcc.cometchat.reinitialize();
				},
				error: function(data){
					console.log('Error',data);
				}
			});
		},
		arguments.callee.sociallogout = function(social_details){
			jqcc.ajax({
                url: baseUrl+'cometchat_logout.php',
                dataType: 'jsonp',
                success: function(data){
                    jqcc.cometchat.chatHeartbeat(1);
                },
                error: function(){
                }
            });
		},
		arguments.callee.htmlEntities = function(str){
			if(typeof str != 'undefined' && str != '' && str != null){
				str = str.trim();
				if (str.indexOf('<script') != -1){
					return str.replace(/</g,'&lt;').replace(/>/g,'&gt;');
				} else {
					return str;
				}
			}
		},
		arguments.callee.audiovideocall = function(toid){
			if(toid == 0 || toid == ''){
				return;
			}
			var getActivePlugins = jqcc.cometchat.getSettings().plugins;
			if(!getActivePlugins.includes('audiochat')){
				var avcallDisable = jqcc.ccavchat.getLanguage('avcall_disabled');
				alert(avcallDisable);
				return;
			}
			var caller = jqcc.cometchat.getUserID();
			jqcc['ccavchat'].init({'to':toid, 'caller': caller, 'chatroommode': 0});
		},
		arguments.callee.audiocall = function(toid){
			if(toid == 0 || toid == ''){
				return;
			}
			var getActivePlugins = jqcc.cometchat.getSettings().plugins;
			if(!getActivePlugins.includes('audiochat')){
				var audiocallDisable = jqcc.ccaudiochat.getLanguage('audiocall_disabled');
				alert(audiocallDisable);
				return;
			}
			var caller = jqcc.cometchat.getUserID();
			jqcc['ccaudiochat'].init({'to':toid, 'caller': caller, 'chatroommode': 0});
		}
	};
	function replaceHtml(el, html){
		var oldEl = typeof el==="string" ? document.getElementById(el) : el;
		/*@cc_on // Pure innerHTML is slightly faster in IE
		 oldEl.innerHTML = html;
		 return oldEl;
		 @*/
		var newEl = oldEl.cloneNode(false);
		newEl.innerHTML = html;
		oldEl.parentNode.replaceChild(newEl, oldEl);
		/* Since we just removed the old element from the DOM, return a reference
		 to the new element, which can be used to restore variable references. */
		return newEl;
	};
})(jqcc);
jqcc(document).bind('keyup', function(e){
	if(e.keyCode==27){
		jqcc('.cometchat_closebox').click();
		$('.cometchat_container').remove();
		jqcc.cometchat.minimizeAll();
	}
});

function cometready(){
	jqcc(document).ready(function() {
		if(typeof CometChathasBeenRun==='undefined'){
			CometChathasBeenRun = true;
		}else{
			return;
		}
		jqcc.cometchat();
		jqcc.cometchat.<?php echo $jsfn; ?>();
		if(typeof initializeEmbeddedLayout == "function"){
			initializeEmbeddedLayout();
		}
		if('<?php echo $layout; ?>'=='embedded'){
			var loaderHtml = '<div id="cometchat_loader"><div class="cometchat_spinner"></div></div>';
			jqcc("body").append(loaderHtml);
		}
	});
};

<?php if(!defined('USE_COMET') || USE_COMET == 0 || (USE_COMET==1 && (getCometServiceVersion()==1||TRANSPORT=='cometserviceselfhosted'))) { ?>
	jqcc(document).ready(function(){
		if(window.top==window.self||'<?php echo $layout; ?>'!='docked'){
			cometready();
		}
	});
<?php } ?>
