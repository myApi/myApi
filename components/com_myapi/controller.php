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
		  $query = "UPDATE #__comprofiler JOIN #__myapi_users ON #__comprofiler.user_id = #__myapi_users.userId  SET #__comprofiler.avatar ='".$avatar."' WHERE #__myapi_users.uid ='".$uid."'";
		  $db->setQuery($query);
		  $db->query();
		}catch(Exception $e){}
		return;
	}
	
	//This task logs in a user
	function facebookLogin() {
		global $facebook, $mainframe;
		
		$session	= $facebook->getSession();
		$uid 		= $session['uid'];
		$return 	= base64_decode(JRequest::getVar('return',''));
		$user 		= JFactory::getUser();
		
		if($uid && $user->guest){
			$options['return'] = $return;
			$options['uid'] = $uid;
			$error = $mainframe->login($uid,$options);
			if(!is_object($error)){
				MyapiController::syncPhoto($uid);
				$return = ($return == '') ? JURI::base() : $return;
				$this->setRedirect($return,JText::_( 'LOGGED_IN_FACEBOOK' ));
			}else{ 
				$this->setRedirect(JURI::base(),JText::_( 'LOGIN_ERROR' )." - ".$uid); 
			}
		}else{
			$this->setRedirect($return,JText::_('NO_SESSION'));
		}
	}
	
	function logout(){
		global $mainframe, $facebook;
		$mainframe->logout();
		$facebook->setSession(null);
		$msg = (JRequest::getVar('auto','0','get') == '0') ? JText::_('FACEBOOK_LOGOUT') : JText::_('FACEBOOK_EXPIRED');
		$this->setRedirect(JURI::base(),$msg);
	}
	//Joomla user login task
	function login(){
		global $mainframe,$facebook;

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
		$message = (JError::isError($error)) ? $error->message : JText::_( 'LOGGED_IN_FACEBOOK' );
		$this->setRedirect($return,$message);
	}
	
	//A function called via ajax to see is a Facebook user is linked to a Joomla user
	function isLinked(){
		global $facebook, $mainframe;
		JRequest::checkToken( 'get' ) or die( 'Invalid Token' );
		$db 	= JFactory::getDBO();
		$uid 	= JRequest::getVar('fbId','','get');
		$query 	= "SELECT userId FROM ".$db->nameQuote('#__myapi_users')." WHERE uid =".$db->quote($uid);
		$db->setQuery($query);
		$db->query();
		$num_rows = $db->getNumRows();
		$query_id = $db->loadResult();
		
		$facebook->setSession(array('access_token' => JRequest::getVar('access_token'), 'sig' => JRequest::getVar('sig'), 'uid' => JRequest::getVar('uid'),'expires' => JRequest::getVar('expires'),'secret' => JRequest::getVar('secret'),'session_key' => JRequest::getVar('session_key'),'base_domain' => JRequest::getVar('base_domain')));
		$session = $facebook->getSession();
		
		if($num_rows == 0){ 
			MyapiController::showRegisterWindow(); 
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

		$return = base64_decode(JRequest::getVar('return','','post'));
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
		
		require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_myapi'.DS.'models'.DS.'facebook.php';
		$facebookmodel = new myapiModelfacebook;  //Bring the myAPI facebook model
		$fbUser = $facebookmodel->getLoggedInUserDetails();
		
		if($fbUser['username'] != ''){
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
		
		jimport('joomla.user.helper');
		$newUser['name'] = $fbUser['name'];
		$newUser['username'] = $newuserName;
		$newUser['password'] = $newUser['password2'] = JUserHelper::genRandomPassword();
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
		if ( !$user->save() ){
			$message = $user->getError();
			$this->setRedirect($return,$message);
		}elseif($fbUser['uid'] != ''){
			$db = JFactory::getDBO();
			$facebook = plgSystemmyApiConnect::getFacebook();
			$facebookSession = $facebook->getSession();
			$query = "INSERT INTO ".$db->nameQuote('#__myapi_users')." (userId,uid,access_token) VALUES(".$db->quote($user->id).",".$db->quote($fbUser['uid']).",".$db->quote($facebookSession['access_token']).")";
			$db->setQuery($query);
			$db->query();
			
			//Sync Community Builder
			$sql_sync = "INSERT IGNORE INTO #__comprofiler(id,user_id) SELECT id,id FROM #__users WHERE #__users.id =".$db->Quote($user->id);
			$db->setQuery($sql_sync);
			$db->query();
			
			// Send registration confirmation mail
			$password = $newUser['password'];
			$password = preg_replace('/[\x00-\x1F\x7F]/', '', $password); //Disallow control chars in the email
			myapiController::_sendMail($user, $password);
	
			$message = JText::_( 'LOGGED_IN_FACEBOOK' );
			
			$options['fake_array'] = "This mainframe->login needs and array passed to it";
			$error = $mainframe->login($fbUser['uid'],$options);
			$user = JFactory::getUser();
			MyapiController::syncPhoto($fbUser['uid']);
			$this->setRedirect($return,$message);
		}else{
			$this->setRedirect($return,JText::_('NO_UID_FOUND'));
		}
	}
	
	function _sendMail(&$user, $password){
		global $mainframe;
		$db		=& JFactory::getDBO();

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

		$message = sprintf ( JText::_( 'SEND_MSG' ), $name, $sitename, $siteURL);
		$message = html_entity_decode($message, ENT_QUOTES);

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

		JUtility::sendMail($mailfrom, $fromname, $email, $subject, $message);

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
}
?>