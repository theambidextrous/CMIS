var CCCREDITS = (function($){
	var apiEndPoint = '<?php echo BASE_URL;?>api/',
		deductionInterval = 0,
		creditsToDeduct = 0,
		balance = 0,
		stopCallback,
		defaultParams = {
			action: '',
			'api-key' : '',
			userid: 0,
			type: '',
			name: '',
			isGroup: 0,
			to: 0,
		},
		isObject = function(a) {
	    	return (!!a) && (a.constructor === Object);
		},
		isArray = function(a) {
    		return (!!a) && (a.constructor === Array);
		},
		jsonp = function(url,data, callback) {
			var callbackName = 'jqcc_callback_' + Math.round(100000 * Math.random());
			var script = document.createElement('script');
			script.src = url + (url.indexOf('?') >= 0 ? '&' : '?') + 'callback=' + callbackName;
			for(var key in data){
				if(data.hasOwnProperty(key)){
					script.src+='&'+key+'='+data[key];
				}
			}
			document.body.appendChild(script);
			window[callbackName] = function(data) {
				delete window[callbackName];
				document.body.removeChild(script);
				if(callback){
					callback(data);
				}
			}
		},
		setCreditInformation = function(params){
			Object.assign(params,defaultParams);
			deductionInterval = params['creditsinfo']['deductionInterval'];
			creditsToDeduct = params['creditsinfo']['creditsToDeduct'];
			startDeductionInterval(params);
		},
		startDeductionInterval = function(params){
			if(creditsToDeduct!=0&&deductionInterval!=0){
				params['action'] = 'deductCredits';
				var deductionIntervalTimer = setInterval(function(){
					jsonp(apiEndPoint,params, function(data){
						if(data.hasOwnProperty('errorcode')&&data['errorcode']==3){
							alert(data['message']);
							if(stopCallback){
								(stopCallback)();
							}
							clearInterval(deductionIntervalTimer);
						}
					})
				}, deductionInterval*60000);
			}
		};

	return{
		init: function(params){
			/* @params:
			 * type: type of the feature: core, plugin, extension and module
			 * name: name of the feature: avchat, audiochat etc.
			 * stopCallback: The callback to trigger when user runs out of the credits.
			 */
			if(!params.hasOwnProperty('type') || !params.hasOwnProperty('name') || !params.hasOwnProperty('stopCallback')||!params.hasOwnProperty('userid') || !params.hasOwnProperty('to') || !params.hasOwnProperty('isGroup')){
			 	console.error('Unable to Initialize. Please check the params');
			 	return;
			}
			defaultParams['type'] = params['type'];
			defaultParams['name'] = params['name'];
			defaultParams['to'] = params['to'];
			defaultParams['userid'] = params['userid'];
			defaultParams['isGroup'] = params['isGroup'];
			stopCallback = params['stopCallback'];
			delete params['stopCallback'];
			if(params.hasOwnProperty('deductionInterval')){
				deductionInterval = params['deductionInterval'];
			}else{
				params['action']='getCreditsToDeduct'
				jsonp(apiEndPoint,params,setCreditInformation);
			}
		}
	}
})();
