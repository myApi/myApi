//myApi Javascript

var myapi = {
	ajax: {
		evaluate: function(changes){
			try{ 
				for(var i=0;i<changes.length;i++){
					var str = changes[i].clean(); 
					str = str.replace(/[\n\r\t]/g,''); 
					eval( str ); 
				}
			}catch(e){ console.log(e); }	
		}
	},
	auth: {
		logoutAction: function(auto){
			window.location = 'index.php?option=com_myapi&task=logout&auto='+auto;
		},
		logout: function(){
			//This logout function allows Joomla users to logout using the fb logout button without throwing a "no session" error is they are not 
			//logged in with facebook
			FB.getLoginStatus(function(response) {
				if (response.session) {
					FB.logout();
					myapi.auth.logoutAction(0);
				}else{
					myapi.auth.logoutAction(0);
				}
			});
		},
		checkAndLogin: function(token,redirect){
			FB.getLoginStatus(function(response) {
				if (response.session) {
					var uid = response.session.uid;
					try{
					$ES('.fb_button_text','myApiLoginWrapper')[0].innerHTML = "Connecting";
					}catch(e){}
					
					var isLinkedAjax = new Request.JSON({
						url: 'index.php?option=com_myapi&task=isLinked&'+token+'=1&fbId='+uid+'&return='+redirect+'&'+Object.toQueryString(response.session),
						method: 'get',
						onRequest: function() { 
							
						},
						onComplete: function( response ) {
							  //redirect to link 
						  	  myapi.ajax.evaluate(response);
							  
							  try{ 
							  $('fbLoginButton').disabled = false;
							  $ES('.fb_button_text','myApiLoginWrapper')[0].innerHTML = "Connect with facebook";
							  }catch(e){}
							
						}
					}).send();	
				}
			});   
		},
		newLink: function(token,redirect){
			$ES('.fb_button_text','fbLinkButton')[0].innerHTML = "Linking...";
			FB.getLoginStatus(function(response) {
				if (response.session) {
					window.location = redirect;
				}
			});
		}		
	},
	register: {
		showRegisterWindow: function(){
			FB.api({method: 'users.hasAppPermission', ext_perm: 'email,user_likes,user_photos,user_status,publish_stream,offline_access'},function(response) {
				if(response){ 
					var ajax = new Request({
						url: 'index.php?option=com_myapi&task=showRegisterWindow',
					  	method: 'get',
					  	onComplete: function(response){
							var data = Json.evaluate(response);
						  	myapi.ajax.evaluate(data);	
						}	
				  }).send();
				}
			}
		  );
		}
	},
	addFriend: function(uid){
		FB.ui({method: 'friends', display: 'popup', id: uid });	
	}
};