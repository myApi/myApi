myapi = {
	ajax: {
		evaluate: function(changes){
			try{ 
				for( index in changes ){
					var str = changes[index];
					str = str.clean(); 
					str = str.replace(/[\n\r\t]/g,''); 
					eval( str ); 
				}
			 }catch(e){  }	
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
					
					var isLinkedAjax = new Ajax('index.php?option=com_myapi&task=isLinked&'+token+'=1&fbId='+uid+'&return='+redirect,{
						method: 'get',
						onRequest: function() { 
							
						},
						onComplete: function( response ) {
							  //redirect to link 
							  var data = Json.evaluate(response);
						  	  myapi.ajax.evaluate(data);
							  
							  try{ 
							  $('fbLoginButton').disabled = false;
							  $ES('.fb_button_text','myApiLoginWrapper')[0].innerHTML = "Connect with facebook";
							  }catch(e){}
							
						}
					}).request();	
				}
			});   
		},
		newLink: function(token,redirect){
			$ES('.fb_button_text','fbLinkButton')[0].innerHTML = "Linking...";
			FB.getLoginStatus(function(response) {
				if (response.session) {
					window.location = 'index.php?option=com_myapi&task=newLink&'+token+'=1&return='+redirect;
				}
			});
		},		
	},
	register: {
		showRegisterWindow: function(){
			FB.api({method: 'users.hasAppPermission', ext_perm: 'email,user_likes,user_photos,user_status,publish_stream,offline_access'},function(response) {
				if(response){ 
				   var ajax = new Ajax('index.php?option=com_myapi&task=showRegisterWindow',{
					  method: 'get',
					  onComplete: function(response){
						  var data = Json.evaluate(response);
						  myapi.ajax.evaluate(data);	
						  
					  }	
				  }).request();
				}
			}
		  );
		}
	}
};