	var baseUrl = '<?php echo BASE_URL; ?>';
	var cometchatsocialauth = firebase;
	var config = {
		apiKey: "<?php echo $firebaseAPIKey; ?>",
		authDomain: "<?php echo $firebaseAuthDomain; ?>",
		projectId: "<?php echo $firebaseProjectID; ?>",
	};
	if (!cometchatsocialauth.apps.length) {
		cometchatsocialauth.initializeApp(config);
	}else{
		cometchatsocialauth.initializeApp(config,'CometChatAuth');
	}

	function cometchat_socialauth_logout(){
		cometchatsocialauth.auth().signOut().then(function() {
			console.log('Sign out Successful');
		}).catch(function(error) {
			console.log('Error: ', error);
		});
	}
	function cometchat_socialauth_login(params){
		if(typeof(params)== undefined || !params.hasOwnProperty('AuthProvider')){
			return;
		}
		
		if(['Facebook','Google','Twitter'].indexOf(params.AuthProvider)==-1){
			return;
		}
		var provider = new cometchatsocialauth.auth[params.AuthProvider+'AuthProvider']();

		if(params.AuthProvider == 'Facebook') {
			provider.addScope('email');
			provider.addScope('public_profile');
		}

		cometchatsocialauth.auth().signInWithPopup(provider).then(function(result) {
			// The signed-in user info.
			if(params.AuthProvider=='Twitter'){
				result.additionalUserInfo.profile.link =  'https://twitter.com/'+result.additionalUserInfo.username;
			}
			var identifier = result.additionalUserInfo.profile.id;
			if(params.AuthProvider=='Facebook' && result.additionalUserInfo.hasOwnProperty('profile') && result.additionalUserInfo.profile.hasOwnProperty('email') && result.additionalUserInfo.profile.email!=null){
				identifier =  result.additionalUserInfo.profile.email;
			}
			var social_details = {
				network_name: result.additionalUserInfo.providerId,
				identifier: identifier,
				firstName: result.user.displayName,
				profileURL: result.additionalUserInfo.profile.link||'',
				photoURL: result.user.photoURL||'',
				allowed: 1
			}
			var controlparameters = {"type":"core", "name":"cometchat", "method":"sociallogin", "params":social_details};
			parent.postMessage('CC^CONTROL_'+JSON.stringify(controlparameters),'*');
			cometchat_socialauth_logout();
		}).catch(function(error) {
			console.log('Error: ', error);
		});
	}
