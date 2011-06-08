<?php
/**
 * Joomla! 1.5 component myApi
 *
 * @version $Id: controller.php 2010-05-01 08:43:14 svn $
 * @author Thomas Welton
 * @package Joomla
 * @subpackage myApi
 * @license GNU/GPL
 *
 * myApi - Combining the power of the Facebook platform with the ease and simplicity of Joomla.
 *
 * This component file was created using the Joomla Component Creator by Not Web Design
 * http://www.notwebdesign.com/joomla_component_creator/
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * myApi Component Controller
 */
class MyapiController extends JController {
	
	function display() {
        // Make sure we have a default view
        if( !JRequest::getVar( 'view' )) {
		    JRequest::setVar('view', 'myapi' );
        }
		parent::display();
	}
	
	function facebookRealTime(){
		$integrations 	= array('users' => 'id','k2_users' => 'userID','community_users' => 'userid');
		$method 		= $_SERVER['REQUEST_METHOD'];  
		global $mainframe;    
		
		//Callback verification                                                 
		if ($method == 'GET' && JRequest::getVar('hub_mode','','get') == 'subscribe' && JRequest::getVar('hub_verify_token','','get') == $mainframe->getCfg( 'secret' )) {
		  	echo JRequest::getVar('hub_challenge','','get');
			$mainframe->close();
		} else if ($method == 'POST') {                                   
			$updates = json_decode(file_get_contents("php://input"), true); 
		  	
			ignore_user_abort(true); 
			set_time_limit(0);
			$content = '';
			if (strlen($content) < 256) {
			   $content = str_pad($content, 256); // IE hack
			}
			header("HTTP/1.1 200 OK");
			header("Content-Length: ".strlen($content));
			echo $content;
			flush();
		  	switch($updates['object']){
				case 'user':
					foreach($updates['entry'] as $entry){
						$uid	= $entry['uid'];
						$db 	= JFactory::getDBO();
						$query 	= "SELECT ".$db->nameQuote('access_token')." FROM ".$db->nameQuote('#__myapi_users')." WHERE ".$db->nameQuote('uid')." = ".$db->quote($uid);
						$db->setQuery($query);
						$access_token = $db->loadResult();
						try {
							$facebook = plgSystemmyApiConnect::getFacebook();
							$me = $facebook->api('/'.$uid.'?metadata=1','get',array('access_token' => $access_token));
						} catch (FacebookApiException $e) {
							return;
						}
						
						$changes = array();
						if(in_array('name',$entry['changed_fields']) ){
							$changes['users']['name'] = $changes['k2_users']['userName'] = $me['name'];
						}
						if(in_array('email',$entry['changed_fields']) ){
							$changes['users']['email'] = $me['email'];
						}
						if(in_array('status',$entry['changed_fields']) ){
							try {
								$facebook = plgSystemmyApiConnect::getFacebook();
								$statusCall = $facebook->api('/'.$uid.'/statuses','get',array('access_token' => $access_token,'limit' => 1));
								$changes['community_users']['status'] = $statusCall['data'][0]['message'];
							} catch (FacebookApiException $e) {
								return;
							}
						}
						if(in_array('about_me',$entry['changed_fields']) ){
							$changes['k2_users']['description'] = $me['bio'];
						}
						if(in_array('username',$entry['changed_fields']) ){
							$changes['k2_users']['url'] = $me['link'];
						}
						if(in_array('pic',$entry['changed_fields']) ){
							$dest		= JPATH_SITE.DS.'images'.DS.'comprofiler'.DS.'tn'.'facebookUID'.$uid.'.jpg';
							$avatar		= 'facebookUID'.$uid.'.jpg';
							$avatarData = $facebook->api(array('method' => 'fql.query','query' => 'SELECT pic FROM user WHERE uid = "'.$uid.'";', 'access_token' => $access_token));
							$buffer		= file_get_contents($avatarData[0]['pic']);
							jimport( 'joomla.filesystem.file' );
							JFile::write($dest,$buffer);
						} 
						
						if(sizeof($changes) > 0){
							foreach($changes as $key => $array){
								$db			= JFactory::getDBO();
								$setArray 	= array();
								foreach($array as $col => $val){
									$setArray[] = $db->nameQuote($col)." = ".$db->quote($val);	
								}
								$query = "UPDATE ".$db->nameQuote('#__'.$key)." JOIN ".$db->nameQuote('#__myapi_users')." ON ".$db->nameQuote('#__'.$key.'.'.$integrations[$key])." = ".$db->nameQuote('#__myapi_users.userId')."   SET ".implode(',',$setArray)." WHERE ".$db->nameQuote('#__myapi_users.uid')." = ".$db->quote($uid);
								$db->setQuery($query);
								$db->query();
							}
						}
					}
				break;
					
				case 'permissions':
					foreach($updates['entry'] as $entry){
						foreach($updates['entry'] as $entry){
							$uid 	= $entry['uid'];
							$db 	= JFactory::getDBO();
							$query 	= "UPDATE ".$db->nameQuote('#__myapi_users')." SET ".$db->nameQuote('access_token')." = ".$db->quote('')." WHERE ".$db->nameQuote('uid')." = ".$db->quote($uid);
							$db->setQuery($query);	
							$db->query();
							
							$query = "SELECT ".$db->nameQuote('userId')." FROM ".$db->nameQuote('#__myapi_users')." WHERE ".$db->nameQuote('uid')." = ".$db->quote($uid);
							$db->setQuery($query);
							if($userId = $db->loadResult()){ 	
								$mainframe->logout($userId);
							}
							
							if( in_array('manage_pages',$entry['changed_fields']) || in_array('publish_stream',$entry['changed_fields']) ){
								$query 	= "DELETE FROM ".$db->nameQuote('#__myapi_pages')." WHERE ".$db->nameQuote('owner')." = ".$db->quote($uid);
								$db->setQuery($query);	
								$db->query();	
							}
						}
					}
				break;
				
				case 'page':
					foreach($updates['entry'] as $entry){
						foreach($updates['entry'] as $entry){
							$id 	= $entry['uid'];
							$cache = & JFactory::getCache('com_myapi - Feed '.$id);
							$cache->clean();
							$cache = & JFactory::getCache('com_myapi - Page '.$id);
							$cache->clean();
							
							require_once JPATH_SITE.DS.'components'.DS.'com_myapi'.DS.'models'.DS.'myapi.php';
							$myApiModel 	= new MyapiModelMyapi;  //Bring the myAPI facebook model
							$page = $myApiModel->getPage($id);
							$feed = $myApiModel->getFeed($id);	
						}
					}
				
				break;
			}
		
		  $mainframe->close();           
		}	
	}
	
	function deauthorizeCallback(){
		$facebook = plgSystemmyApiConnect::getFacebook();
		$signedRequest = $facebook->getSignedRequest();
		$db = JFactory::getDBO();
		$query = "DELETE FROM ".$db->nameQuote('#__myapi_users')." WHERE ".$db->nameQuote('uid')." = ".$db->quote($signedRequest['user_id']);
		$db->setQuery($query);
		$db->query();
		
		global $mainframe;
		$mainframe->close();
	}
	
	function showRegisterWindow(){
		$facebook = plgSystemmyApiConnect::getFacebook();
		require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_myapi'.DS.'models'.DS.'facebook.php';
		
		$db 			= JFactory::getDBO();
		$facebookmodel 	= new myapiModelfacebook;  //Bring the myAPI facebook model
		$fbUser 		= $facebookmodel->getLoggedInUserLiked();
		$query 			= "SELECT COUNT(".$db->nameQuote('id').") FROM ".$db->nameQuote('#__users')." WHERE ".$db->nameQuote('email')." = ".$db->quote($fbUser['email']);
		
		$db->setQuery($query);
		$registeredEmail = $db->loadResult();
		
		$forgotPass	= JRoute::_( 'index.php?option=com_user&view=reset' );
		$forgotUser	= JRoute::_( 'index.php?option=com_user&view=remind' );
		$formToken	= JHTML::_( 'form.token' );

		ob_start();
	 		include(JPATH_SITE.DS.'components'.DS.'com_myapi'.DS.'views'.DS.'link'.DS.'tmpl'.DS.'default.php');
			$html = ob_get_contents();
		ob_end_clean();	
		
		$data[] = "myApiModal.open('".JText::_('FACEBOOK_CONNECT',true)."','".JText::_('REGISTRATION_PROMPT',true)."','".addslashes($html)."');";
		if(!$fbUser['liked']){
			$data[] = "FB.Event.subscribe('edge.create', function(response) { $('myApiNewUserRegForm').submit(); });";
		}
		echo json_encode($data);
		global $mainframe;
		$mainframe->close();
	}
	
	function syncPhoto($uid){
		jimport( 'joomla.filesystem.folder' );
		if(!JFolder::exists(JPATH_SITE.DS.'images'.DS.'comprofiler'))
			JFolder::create(JPATH_SITE.DS.'images'.DS.'comprofiler');
			
		$dest	= JPATH_SITE.DS.'images'.DS.'comprofiler'.DS.'tn'.'facebookUID'.$uid.'.jpg';
		$avatar	= 'facebookUID'.$uid.'.jpg';
		$buffer	= file_get_contents('https://graph.facebook.com/'.$uid.'/picture',$dest);
		jimport( 'joomla.filesystem.file' );
		JFile::write($dest,$buffer);
		
		$db 	=& JFactory::getDBO();
		$query 	= "UPDATE #__myapi_users SET avatar ='".$avatar."' WHERE uid ='".$uid."'";
		$db->setQuery($query);
		$db->query();
		
		try{
		  $query = "UPDATE #__comprofiler JOIN #__myapi_users ON #__comprofiler.user_id = #__myapi_users.userId  SET #__comprofiler.avatar =".$db->quote($avatar)." WHERE #__myapi_users.uid =".$db->quote($uid)."";
		  $db->setQuery($query);
		  $db->query();
		}catch(Exception $e){}
		try{
		  $query = "UPDATE #__k2_users JOIN #__myapi_users ON #__k2_users.userID = #__myapi_users.userId SET #__k2_users.image =".$db->quote('../../../images/comprofiler/tn'.$avatar)." WHERE #__myapi_users.uid =".$db->quote($uid)."";
		  $db->setQuery($query);
		  $db->query();
		}catch(Exception $e){}
		try{
		  $query = "UPDATE #__community_users JOIN #__myapi_users ON #__community_users.userid = #__myapi_users.userId SET #__community_users.avatar =".$db->quote('images/comprofiler/tn'.$avatar).", #__community_users.thumb =".$db->quote('images/comprofiler/tn'.$avatar)." WHERE #__myapi_users.uid =".$db->quote($uid)."";
		  $db->setQuery($query);
		  $db->query();
		}catch(Exception $e){}
		
		return;
	}
	
	//This task logs in a user
	function facebookLogin() {
		$user 		= JFactory::getUser();
		$return 	= base64_decode(JRequest::getVar('return',''));
		
		$facebook = plgSystemmyApiConnect::getFacebook();
		$session	= $facebook->getSession();
		$uid 		= $session['uid'];
		
		if($uid){
			global $mainframe;
			$options['return'] = $return;
			$options['uid'] = $uid;
			$error = $mainframe->login($uid,$options);
			if(!is_object($error)){
				$return = ($return == '') ? JURI::base() : $return;
				$this->setRedirect($return,JText::_( 'LOGGED_IN_FACEBOOK' ));
			}else{ 
				$this->setRedirect(JURI::base(),JText::_( 'LOGIN_ERROR' )." - ".$error->message); 
			}
		}else{
			$this->setRedirect($return,JText::_('NO_SESSION'));
		}
		
	}
	
	function logout(){
		global $mainframe;
		$facebook = plgSystemmyApiConnect::getFacebook();
		$mainframe->logout();
		$facebook->setSession(null);
		$msg = (JRequest::getVar('auto','0','get') == '0') ? JText::_('FACEBOOK_LOGOUT') : JText::_('FACEBOOK_EXPIRED');
		$this->setRedirect(JURI::base(),$msg);
	}
	//Joomla user login task
	function login(){
		global $mainframe;
		$facebook = plgSystemmyApiConnect::getFacebook();

		$options 				= array();
		$credentials 			= array();
		$return 				= base64_decode(JRequest::getVar('return','','post'));
		$session 				= $facebook->getSession();
		$options['remember']	= JRequest::getBool('remember', false);
		$options['return'] 		= $return;
		$options['uid'] 		= $session['uid'];
		$credentials['username'] = JRequest::getVar('username', '', 'method', 'username');
		$credentials['password'] = JRequest::getString('passwd', '', 'post', JREQUEST_ALLOWRAW);
 
		$error = $mainframe->login($credentials, $options);
		
		if(JError::isError($error)){
			JError::raiseWarning( 100, JText::_('PAGES_ADDED_ERROR').' '.$error->message );
		}else{
			$facebook = plgSystemmyApiConnect::getFacebook();
			$facebookSession = $facebook->getSession();
			$avatar = 'facebookUID'.$facebookSession['uid'].'.jpg';
			$user = JFactory::getUser();
			$db		= JFactory::getDBO();
			$query	= "INSERT INTO ".$db->nameQuote('#__myapi_users')." (userId,uid,access_token,avatar) VALUES(".$db->quote($user->id).",".$db->quote($facebookSession['uid']).",".$db->quote($facebookSession['access_token']).",".$db->quote($avatar).")";
			$db->setQuery($query);
			$db->query();
			if($db->getErrorNum()){
				JError::raiseWarning( 100, JText::_('PAGES_ADDED_ERROR').' '.$db->getErrorMsg() );	
			}else{
				JFactory::getApplication()->enqueueMessage(JText::_( 'LOGGED_IN_FACEBOOK'));
			}
			
		}
		$this->setRedirect($return);
	}
	
	function deleteLink(){
		JRequest::checkToken( 'get' ) or die( 'Invalid Token' );
		$user = JFactory::getUser();
		if(!$user->guest){
			$db = JFactory::getDBO();
			$query = "DELETE FROM ".$db->nameQuote('#__myapi_users')." WHERE ".$db->nameQuote('userId')." = ".$db->quote($user->id);
			$db->setQuery($query);
			$db->query();
			
			 if(!$db->getErrorNum()){
				JFactory::getApplication()->enqueueMessage( JText::_('FACEBOOK_UNLINKED') );	
			}else{
				JError::raiseWarning( 100, JText::_('FACEBOOK_UNLINKED_ERROR') );	
			}
			$this->setRedirect(base64_decode(JRequest::getVar('return')));	
		}
	}
	
	//A function called via ajax to see is a Facebook user is linked to a Joomla user
	function isLinked(){
		global $mainframe;
		$facebook = plgSystemmyApiConnect::getFacebook();
		JRequest::checkToken( 'get' ) or die( 'Invalid Token' );
		$db 	= JFactory::getDBO();
		$uid 	= JRequest::getVar('fbId','','get');
		$query 	= "SELECT userId FROM ".$db->nameQuote('#__myapi_users')." WHERE uid =".$db->quote($uid);
		
		$query = "SELECT #__users.id,#__myapi_users.access_token,#__myapi_users.userId,#__users.block FROM ".$db->nameQuote('#__myapi_users')." LEFT JOIN #__users ON #__myapi_users.userId = #__users.id WHERE ".$db->nameQuote('uid')." = ".$db->quote($uid);
		$db->setQuery($query);
		$db->query();
		$num_rows = $db->getNumRows();
		$result = $db->loadAssoc();
		
		$facebook->setSession(array('access_token' => JRequest::getVar('access_token'), 'sig' => JRequest::getVar('sig'), 'uid' => JRequest::getVar('uid'),'expires' => JRequest::getVar('expires'),'secret' => JRequest::getVar('secret'),'session_key' => JRequest::getVar('session_key'),'base_domain' => JRequest::getVar('base_domain')));
		$session = $facebook->getSession();
		
		if($num_rows > 0 && $result['id'] != $result['userId']){
			JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_myapi'.DS.'tables');
			$row =& JTable::getInstance('myapiusers', 'Table');
			$row->delete($result['userId']);	
		}
		
		if($num_rows == 0 || ($result['id'] != $result['userId'])){
			require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_myapi'.DS.'models'.DS.'facebook.php';
			$facebookmodel = new myapiModelfacebook;  //Bring the myAPI facebook model
			global $fbUser;
			$fbUser = $facebookmodel->getLoggedInUser(JRequest::getVar('access_token',NULL));
			
			$db 			= JFactory::getDBO();
			$query 			= "SELECT COUNT(".$db->nameQuote('id').") FROM ".$db->nameQuote('#__users')." WHERE ".$db->nameQuote('email')." = ".$db->quote($fbUser['email']);
			$db->setQuery($query);
			$registeredEmail = $db->loadResult();
			if($registeredEmail){
				MyapiController::showRegisterWindow(); 
			}else{
				MyapiController::newUser();
				
				$data = array();
				$data[] =  "document.myApiLoginForm.submit();";
				echo json_encode($data);
				$mainframe->close(); 
			}
		}else{
			$data = array();
			$data[] =  "document.myApiLoginForm.submit();";
			echo json_encode($data);
			$mainframe->close(); 
		}
	}
	
	function newLink(){
		// Check for request forgeries
		$facebook = plgSystemmyApiConnect::getFacebook();
		$facebookSession = $facebook->getSession();
		if($facebookSession['uid'] != ''){
			$user	= JFactory::getUser();
			$db 	= JFactory::getDBO();
			
			jimport( 'joomla.filesystem.file' );
			jimport( 'joomla.filesystem.folder' );
			if(!JFolder::exists(JPATH_SITE.DS.'images'.DS.'comprofiler'))
				JFolder::create(JPATH_SITE.DS.'images'.DS.'comprofiler');
				
			$dest 	= JPATH_SITE.DS.'images'.DS.'comprofiler'.DS.'tn'.'facebookUID'.$facebookSession['uid'].'.jpg';
			$avatar = 'facebookUID'.$facebookSession['uid'].'.jpg';
			$buffer = file_get_contents('https://graph.facebook.com/'.$facebookSession['uid'].'/picture',$dest);
			JFile::write($dest,$buffer);
			
			$db		= JFactory::getDBO();
			$query	= "INSERT INTO ".$db->nameQuote('#__myapi_users')." (userId,uid,access_token,avatar) VALUES(".$db->quote($user->id).",".$db->quote($facebookSession['uid']).",".$db->quote($facebookSession['access_token']).",".$db->quote($avatar).")";
			$db->setQuery($query);
			$db->query();
			
			try{
			  $query = "UPDATE #__comprofiler SET #__comprofiler.avatar ='".$avatar."' user_id ='".$user->id."'";
			  $db->setQuery($query);
			  $db->query();
			}catch(Exception $e){}
			
			$this->setRedirect(JURI::base(),JText::_('LINK_COMPLETE'));
		}else{
			$this->setRedirect(JURI::base(),JText::_('NO_UID_FOUND'));
		}
	}
	
	//New user
	function newUser()
	{
		global $mainframe;

		$return = base64_decode(JRequest::getVar('return',''));
		// Get required system objects
		$user 		= clone(JFactory::getUser());
		$pathway 	=& $mainframe->getPathway();
		$config		=& JFactory::getConfig();
		$authorize	=& JFactory::getACL();
		$document   =& JFactory::getDocument();

		$usersConfig = &JComponentHelper::getParams( 'com_users' );
		// Initialize new usertype setting
		$newUsertype = $usersConfig->get( 'new_usertype' );
		if (!$newUsertype) {
			$newUsertype = 'Registered';
		}
		
		global $fbUser;
		if(!is_object($fbUser)){
			require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_myapi'.DS.'models'.DS.'facebook.php';
			$facebookmodel = new myapiModelfacebook;  //Bring the myAPI facebook model
			$fbUser = $facebookmodel->getLoggedInUser();
		}
		
		if(array_key_exists('username',$fbUser) && $fbUser['username'] != ''){
			$newuserName = $fbUser['username'];
		}else{
			$newuserName = str_replace(' ', '',$fbUser['name']);
		}
		
		$db = JFactory::getDBO();
		$uniqueUsername = false;
		$i = 0;
		
		while(!$uniqueUsername){
			$tryUsername = $newuserName;
			if($i >= 1){
				$tryUsername = $tryUsername.$i;	
			}
			
			$query = "SELECT COUNT(".$db->nameQuote('id').") FROM ".$db->nameQuote('#__users')." WHERE ".$db->nameQuote('username')." = ".$db->quote($tryUsername);
			$db->setQuery($query);
			$count = $db->loadResult();
			if($count == 0){
				$uniqueUsername = true;
				$newuserName = $tryUsername;
			}
				$i++;
			
		}
		
    	$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    	$randomPassword = "";    
    	for ($p = 0; $p < 8; $p++) {
    	    $randomPassword .= $characters[mt_rand(0, strlen($characters))];
    	}
		
		
		$newUser['name'] = $fbUser['name'];
		$newUser['username'] = $newuserName;
		$newUser['password'] = $newUser['password2'] = $randomPassword;
		$newUser['email'] = $fbUser['email'];
		
		// Bind the post array to the user object
		if (!$user->bind( $newUser, 'usertype' )) {
			$message = $user->getError();
			$this->setRedirect($return,$message);
		}
		
		// Set some initial user values
		$user->set('id', 0);
		$user->set('usertype', $newUsertype);
		$user->set('gid', $authorize->get_group_id( '', $newUsertype, 'ARO' ));

		$date =& JFactory::getDate();
		$user->set('registerDate', $date->toMySQL());

		// If user activation is turned on, we need to set the activation information
		$useractivation = $usersConfig->get( 'useractivation' );
		
		// If there was an error with registration, set the message and display form
		if ( $result = !$user->save() ){
			$message = $user->getError();
			$this->setRedirect($return,$message);
		}elseif($fbUser['id'] != ''){
			$db = JFactory::getDBO();
			$facebook = plgSystemmyApiConnect::getFacebook();
			$facebookSession = $facebook->getSession();
			$query = "INSERT INTO ".$db->nameQuote('#__myapi_users')." (userId,uid,access_token) VALUES(".$db->quote($user->id).",".$db->quote($fbUser['id']).",".$db->quote($facebookSession['access_token']).")";
			$db->setQuery($query);
			$db->query();
			
			//Sync Community Builder
			$sql_sync = "INSERT IGNORE INTO #__comprofiler(id,user_id) SELECT id,id FROM #__users WHERE #__users.id =".$db->Quote($user->id);
			$db->setQuery($sql_sync);
			$db->query();
			
			// Send registration confirmation mail
			$cleanPassword = preg_replace('/[\x00-\x1F\x7F]/', '', $randomPassword); //Disallow control chars in the email
			myapiController::_sendMail($user, $cleanPassword,$fbUser);
	
			$message = JText::_( 'LOGGED_IN_FACEBOOK' );
			$options['return'] = $return;
			$options['uid'] = $fbUser['id'];
			$options['silent'] = true;
			$error = $mainframe->login($fbUser['id'],$options);
			$user = JFactory::getUser();
			
			JRequest::setVar('K2UserForm',$a = 1);
			JRequest::setVar('gender',$a = substr(@$fbUser['gender'],0,1)); 
			JRequest::setVar('url',$a = @$fbUser['link']);
			JRequest::setVar('description',$a = @$fbUser['bio']);
			
			$dispatcher =& JDispatcher::getInstance();
			$dispatcher->trigger('onAfterStoreUser', array($user->getProperties(), true, $result,''));
			
			MyapiController::syncPhoto($fbUser['id']);
			$this->setRedirect($return,$message);
		}else{
			$this->setRedirect($return,JText::_('NO_UID_FOUND'));
		}
	}
	
	function _sendMail(&$user, $password,$fbUser){
		global $mainframe;
		$db		=& JFactory::getDBO();
		
		
		require_once JPATH_SITE.DS.'components'.DS.'com_myapi'.DS.'models'.DS.'myapi.php';
		$myApiModel 	= new MyapiModelMyapi;  //Bring the myAPI facebook model
		
		$name 		= $user->get('name');
		$email 		= $user->get('email');
		$username 	= $user->get('username');

		$usersConfig 	= &JComponentHelper::getParams( 'com_users' );
		$sitename 		= $mainframe->getCfg( 'sitename' );
		$useractivation = $usersConfig->get( 'useractivation' );
		$mailfrom 		= $mainframe->getCfg( 'mailfrom' );
		$fromname 		= $mainframe->getCfg( 'fromname' );
		$siteURL		= JURI::base();

		$subject 	= sprintf ( JText::_( 'Account details for' ), $name, $sitename);
		$subject 	= html_entity_decode($subject, ENT_QUOTES);
		
		$facebook = plgSystemmyApiConnect::getFacebook();
		$page = $myApiModel->getPage($facebook->getAppId());
		$feed = $myApiModel->getFeed($facebook->getAppId());	
		
		ob_start();
	 		include(JPATH_SITE.DS.'components'.DS.'com_myapi'.DS.'views'.DS.'link'.DS.'tmpl'.DS.'email.php');
			$html = ob_get_contents();
		ob_end_clean();	
		
		//get all super administrator
		$query = 'SELECT name, email, sendEmail' .
				' FROM #__users' .
				' WHERE LOWER( usertype ) = "super administrator"';
		$db->setQuery( $query );
		$rows = $db->loadObjectList();

		// Send email to user
		if ( ! $mailfrom  || ! $fromname ) {
			$fromname = $rows[0]->name;
			$mailfrom = $rows[0]->email;
		}

		$mailer =& JFactory::getMailer();
		$mailer->addReplyTo(array($mailfrom,$fromname));
		$mailer->addRecipient($email);
		$mailer->setSubject($subject);
		$mailer->setBody($html);
		$mailer->isHTML(true);
		$mailer->AltBody = strip_tags($html);
		$send = $mailer->Send();
		

		// Send notification to all administrators
		$subject2 = sprintf ( JText::_( 'Account details for' ), $name, $sitename);
		$subject2 = html_entity_decode($subject2, ENT_QUOTES);

		// get superadministrators id
		foreach ( $rows as $row )
		{
			if ($row->sendEmail)
			{
				$message2 = sprintf ( JText::_( 'SEND_MSG_ADMIN' ), $row->name, $sitename, $name, $email, $username);
				$message2 = html_entity_decode($message2, ENT_QUOTES);
				JUtility::sendMail($mailfrom, $fromname, $row->email, $subject2, $message2);
			}
		}
	}
	
	function commentCreate(){
		global $mainframe;
		$href 	= JRequest::getVar('commentlink',NULL,'get');
		$db		=& JFactory::getDBO();
		$query 	= "INSERT IGNORE INTO ".$db->nameQuote('#__myapi_comment_mail')." (".$db->nameQuote('href').") VALUES (".$db->quote($href).");"; 
		$db->setQuery($query);
		$db->query();
		
		$plugin = & JPluginHelper::getPlugin('content', 'myApiComment');
		$commentParams = new JParameter($plugin->params);
		$commentSend = $commentParams->get('comments_email',1);
		
		if(intval($commentSend) == 1)
			$this->commentQueueSend($commentSend);
			
		$mainframe->close();	
	}
	
	
	function commentQueueSend(){
		global $mainframe;
		//get the comment queue
		$db = JFactory::getDBO();
		$query = "SELECT ".$db->nameQuote('href')." FROM ".$db->nameQuote('#__myapi_comment_mail');
		$db->setQuery($query);
		$commentQueue = $db->loadResultArray();
		$plugin = & JPluginHelper::getPlugin('content', 'myApiComment');
		$commentParams = new JParameter($plugin->params);
		$commentSend = $commentParams->get('comments_email',1);
		if($db->getAffectedRows() > 0 && intval($commentSend) > 0){
			$facebook 	= plgSystemmyApiConnect::getFacebook();
			if($facebook){
				
				$component = JComponentHelper::getComponent( 'com_myapi' );
				$params = new JParameter( $component->params );
				$lastEmailTime = $params->get('commentEmailTime',time()-(60*60*24*7*4));
				$newEmailTime = time();
				
				//Get the admins
				$db		=& JFactory::getDBO();
				$query = 'SELECT email' . ' FROM #__users' . ' WHERE LOWER( usertype ) = "super administrator"';
				$db->setQuery( $query );
				$recipients = $db->loadResultArray();
				
				$emailBody = array();
				$totalComments = 0;
				foreach($commentQueue as $index => $eachHref){	
					$href = parse_url($eachHref);
					parse_str($href['query']);
					//Get comments from facebook
					if(isset($xid)){
						try{
							$fbQuery = "SELECT fromid, username, text, xid FROM comment WHERE (xid = '".$xid."' OR object_id in (SELECT post_fbid FROM comment WHERE xid = '".$xid."')) AND  time > ".($lastEmailTime)." AND time < ".$newEmailTime." ";
							error_log($fbQuery);
							$comments = $facebook->api(array('method' => 'fql.query','query' => $fbQuery,'access_token' => $facebook->getAppId().'|'.$facebook->getApiSecret()));
						} catch (FacebookApiException $e) { return; }
						
						if(is_array($comments)){
							//build email strings
							$totalComments += sizeof($comments);
							$emailBody[$xid]['link'] = $eachHref;
							$emailBody[$xid]['xid'] = $xid;
							
							if(substr($xid,0,7) == 'article'){
								
							}
							
							$articlequery = null;
							switch(substr($xid,0,2)){
								case 'k2':
									$articlequery = "SELECT ".$db->nameQuote('title')." FROM ".$db->nameQuote('#__content')." WHERE ".$db->nameQuote('id')." = ".$db->quote(substr($xid,9));
								break;
								
								case 'ar':
									$articlequery = "SELECT ".$db->nameQuote('title')." FROM ".$db->nameQuote('#__content')." WHERE ".$db->nameQuote('id')." = ".$db->quote(substr($xid,14));
								break;		
							}
							$articleTitle = '';
							if(!is_null($articlequery)){
								$db->setQuery($articlequery);
								$articleTitle = $db->loadResult();
							}	
						 	
							$intro = sprintf ( JText::_( 'NEW_COMMENT_ARTICLE' ), sizeof($comments),$url,$articleTitle);
							$emailBody[$xid]['comments']['plain'] = $intro."\n";
							$emailBody[$xid]['comments']['html'] = '<tr><td colspan="3"><h5 style="margin-bottom:5px; color: #1C2A47; margin-top:10px;">'.$intro.'</h5></td></tr>';
							foreach($comments as $comment){
								$username = $facebook->api('/'.$comment['fromid']);
								$emailBody[$xid]['comments']['plain'] .= "\t".sprintf(JText::_('COMMENT_ITEM'),$username['name'],$comment['text'])."\n";
								$emailBody[$xid]['comments']['html'] .=	'<tr>'.
																			'<td width="50"><a href="http://www.facebook.com/profile.php?id='.$username['id'].'" title="'.$username['name'].'"><img border="0" src="https://graph.facebook.com/'.$username['id'].'/picture" width="50" height="50" alt="'.$username['name'].'" /></td>'.
																			'<td width="10"></td>'.
																			'<td><a style="color:#3B5998;" href="http://www.facebook.com/profile.php?id='.$username['id'].'" title="'.$username['name'].'"><b style="color:#3B5998;">'.$username['name'].'</b></a><br /><em style="color:#333;">'.$comment['text'].'</em></td>'.
																		'</tr>';
							}
							$emailBody[$xid]['comments']['html'] .=	'<tr><td colspan="3"><hr /></td></tr>';
							$emailBody[$xid]['comments']['plain'] .= "\n";
						}
					}
				}
				
				if($totalComments == 0){ return; }
				
				$subject 	= sprintf ( JText::_( 'NEW_COMMENT_SUBJECT' ), $totalComments, sizeof($emailBody));
				$subject 	= html_entity_decode($subject, ENT_QUOTES);
				
				$message_intro = sprintf ( JText::_( 'NEW_COMMENT_INTRO' ), $totalComments,$mainframe->getCfg('sitename'));
				$message_intro = html_entity_decode($message_intro, ENT_QUOTES);
				
				$message_body_plain = $message_intro."\n\n";
				$message_body_html = '<table align="center" width="500"><tr><td colspan="3"><h2 style="color: #1C2A47; font-size: 16px;">'.$message_intro.'</h2></td></tr>';
				foreach($emailBody as $array){
					$message_body_plain .= $array['comments']['plain'];	
					$message_body_html  .= $array['comments']['html'];		
				}
				
				if(intval($commentSend) == 1){
					$cronMessage 		 = sprintf(JText::_('CRON_SETUP'),JURI::root().'index.php?option=com_myapi&task=commentQueueSend');	
					$message_body_plain .= "\n\n\n".$cronMessage;
					$message_body_html	.=	'<tr><td colspan="3"><sub style="color:#808080; font-size:10px;">'.$cronMessage.'</sub></td></tr>';
				}
				
				$message_body_html .= "</table>";
				
				$mailfrom = $mainframe->getCfg( 'mailfrom' );
				$fromname = $mainframe->getCfg( 'fromname' );
				
				$mailer =& JFactory::getMailer();
				$mailer->addReplyTo(array($mailfrom,$fromname));
				$mailer->addRecipient($recipients);
				$mailer->setSubject($subject);
				$mailer->setBody($message_body_html);
				$mailer->isHTML(true);
				$mailer->AltBody = $message_body_plain;
				
				$send = $mailer->Send();
				if ( $send !== true ) {
					error_log($send->message);
				} else {
					$quoted = array();
					foreach($commentQueue as $value) $quoted[] = $db->quote($value);
					$query = "DELETE FROM ".$db->nameQuote('#__myapi_comment_mail')." WHERE ".$db->nameQuote('href')." IN (".implode(',',$quoted ).")";
					$db->setQuery($query);
					$db->query();
					
					$table =& JTable::getInstance('component');
					$table->loadByOption('com_myapi');
					$paramData = array('params' => array('commentEmailTime' => $newEmailTime )); 
					$table->bind( $paramData );
					$table->check();
					$table->store();
				}
			}
		}
		$mainframe->close();			
	}
}
?>