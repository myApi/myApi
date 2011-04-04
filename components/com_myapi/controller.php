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
		global $facebook;
		$signedRequest = $facebook->getSignedRequest();
		$db = JFactory::getDBO();
		$query = "DELETE FROM ".$db->nameQuote('#__myapi_users')." WHERE ".$db->nameQuote('uid')." = ".$db->quote($signedRequest['user_id']);
		$db->setQuery($query);
		$db->query();
		
		global $mainframe;
		$mainframe->close();
	}
	
	function showRegisterWindow(){
		global $facebook;
		require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_myapi'.DS.'models'.DS.'facebook.php';
		$facebookmodel = new myapiModelfacebook;  //Bring the myAPI facebook model
		$fbUser = $facebookmodel->getLoggedInUserLiked();
		
		
		$db = JFactory::getDBO();
		$query = "SELECT COUNT(".$db->nameQuote('id').") FROM ".$db->nameQuote('#__users')." WHERE ".$db->nameQuote('email')." = ".$db->quote($fbUser['email']);
		$db->setQuery($query);
		$registeredEmail = $db->loadResult();
		
		
		require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_myapi'.DS.'models'.DS.'facebook.php';
		$facebookmodel = new myapiModelfacebook;  //Bring the myAPI facebook model
		
		$com_params = &JComponentHelper::getParams( 'com_myapi' );
		$appId = $com_params->get('appId');
		
global $mainframe;

//redirect to different page
	$menuitem = $com_params->get('userRedirectTo');
	if($menuitem == '') { 
		$menu =& JSite::getMenu();
		$menuitem = $menu->getDefault()->id;
	}
	$redirect = base64_encode(JRoute::_(JFactory::getApplication()->getMenu()->getItem( $menuitem )->link . "&Itemid=$menuitem",false));
		
$forgotPass = JRoute::_( 'index.php?option=com_user&view=reset' );
$forgotUser = JRoute::_( 'index.php?option=com_user&view=remind' );
$formToken = JHTML::_( 'form.token' );

		
		ob_start();
	 include(JPATH_SITE.DS.'components'.DS.'com_myapi'.DS.'views'.DS.'link'.DS.'tmpl'.DS.'default.php');
	
 		$html = ob_get_contents();
		ob_end_clean();	
		
		$header = "Hi, login or register below. You can create a new user accout, or link your facebook account to an exisiting user.";
		
		$data[] = "myApiModal.open('Facebook Connect','".addslashes($header)."','".addslashes($html)."');";
		if(!$fbUser['liked']){
		$data[] = "FB.Event.subscribe('edge.create', function(response) { $('myApiNewUserRegForm').submit(); });";
		}
		echo json_encode($data);
		global $mainframe;
		$mainframe->close();
	}
	
	function syncPhoto($id,$uid){
		jimport( 'joomla.filesystem.folder' );
			if(!JFolder::exists(JPATH_SITE.DS.'images'.DS.'comprofiler'))
				JFolder::create(JPATH_SITE.DS.'images'.DS.'comprofiler');
				
			$dest = JPATH_SITE.DS.'images'.DS.'comprofiler'.DS.'tn'.'facebookUID'.$uid.'.jpg';
			
			$avatar = 'facebookUID'.$uid.'.jpg';
			$buffer = file_get_contents('https://graph.facebook.com/'.$uid.'/picture',$dest);
			jimport( 'joomla.filesystem.file' );
			JFile::write($dest,$buffer);
			$db =& JFactory::getDBO();
			
			$query = "UPDATE #__myapi_users SET avatar ='".$avatar."' WHERE userId ='".$id."'";
			$db->setQuery($query);
			$db->query();
			
			try{
			  $query = "UPDATE #__comprofiler SET avatar ='".$avatar."' WHERE user_id ='".$id."'";
			  $db->setQuery($query);
			  $db->query();
			}catch(Exception $e){}
		return;
		// end new code
	
	}
	
	//This task logs in a user
	function facebookLogin() {
		// Check for request forgeries
		JRequest::checkToken('get') or jexit( 'Invalid Token' );
		
		
		require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_myapi'.DS.'models'.DS.'facebook.php';
		$facebookmodel = new myapiModelfacebook;  //Bring the myAPI facebook model
		$fbUser = $facebookmodel->getLoggedInUser();
		$uid = $fbUser['id'];
		$return = base64_decode(JRequest::getVar('return','','post'));
		$user = JFactory::getUser();
		if($uid):
			global $mainframe;
			$db =& JFactory::getDBO();
			$query = "SELECT userId FROM ".$db->nameQuote('#__myapi_users')." WHERE ".$db->nameQuote('uid')." = ".$db->quote($uid);
			$db->setQuery( $query );
			$db->query();
			$num_rows = $db->getNumRows();
			$query_id = $db->loadResult();
			if($num_rows!=0)
			{
				if($user->guest){
					$options['fake_array'] = "This mainframe->login needs and array passed to it";
					$options['uid'] = $uid;
					$error = $mainframe->login($uid,$options);
					if(!is_object($error))
					{
						$this->syncPhoto($query_id,$uid);
						if($return == '') { $this->setRedirect(JURI::base(),JText::_( 'LOGGED_IN_FACEBOOK' )); }
						else { $this->setRedirect($return,JText::_( 'LOGGED_IN_FACEBOOK' )); }
					}
					else{ $this->setRedirect(JURI::base(),JText::_( 'LOGIN_ERROR' )." - ".$uid); }
				}elseif($query_id != $user->id){
					//If the user is logged then the facebook link must be for another joomla account
					$this->setRedirect(JURI::base(),JText::_('DOUBLE_LINK'));
				}
				else{
					$this->syncPhoto($query_id,$uid);
					 if(JRequest::getVar('return','') == '') { $this->setRedirect(JURI::base(),JText::_( 'LOGGED_IN_FACEBOOK' )); }
					 else { $this->setRedirect($return,JText::_( 'LOGGED_IN_FACEBOOK' )); }
				}
				
			}
			else
			{
				return false; //No link found
				
					
			}
		else:
			$this->setRedirect($return,JText::_('NO_SESSION'));
		endif;
	}
	
	
	function logout(){
	
		global $mainframe;
		$mainframe->logout();
		global $facebook;
		$facebook->setSession(null);
		
		if(JRequest::getVar('auto','0','get') == '0'){
			$this->setRedirect(JURI::base(),JText::_('FACEBOOK_LOGOUT'));
		}else{
			$this->setRedirect(JURI::base(),JText::_('FACEBOOK_EXPIRED'));
		}
	
	}
	//Joomla user login task
	function login()
	{
		// Check for request forgeries
		JRequest::checkToken('request') or jexit( 'Invalid Token' );

		global $mainframe;

		$return = base64_decode(JRequest::getVar('return','','post'));
			
		require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_myapi'.DS.'models'.DS.'facebook.php';
		$facebookmodel = new myapiModelfacebook;  //Bring the myAPI facebook model
		$fbUser = $facebookmodel->getLoggedInUser();
		
		$options = array();
		$options['remember'] = JRequest::getBool('remember', false);
		$options['return'] = $return;
		$options['uid'] = $fbUser['id'];

		$credentials = array();
		$credentials['username'] = JRequest::getVar('username', '', 'method', 'username');
		$credentials['password'] = JRequest::getString('passwd', '', 'post', JREQUEST_ALLOWRAW);

		//preform the login action
		$error = $mainframe->login($credentials, $options);
		$message = JText::_( 'LOGGED_IN_FACEBOOK' );
		if(JError::isError($error))
		{
			$message = $error->message;
		}
		
		
		$this->setRedirect($return,$message);
	}
	
	//A function called via ajax to see is a Facebook user is linked to a Joomla user
	function isLinked(){
		JRequest::checkToken( 'get' ) or die( 'Invalid Token' );
		$db = JFactory::getDBO();
		$uid = JRequest::getVar('fbId','','get');
		$query = "SELECT userId FROM ".$db->nameQuote('#__myapi_users')." WHERE uid =".$db->quote($uid);
		$db->setQuery($query);
		$db->query();
		$num_rows = $db->getNumRows();
		$query_id = $db->loadResult();
		if($num_rows == 0){ 
			MyapiController::showRegisterWindow(); 
		}else{
			global $mainframe;
			$options['fake_array'] = "This mainframe->login needs and array passed to it";
			$options['uid'] = $uid;
			$error = $mainframe->login($uid,$options);
			if(!is_object($error)){
				$this->syncPhoto($query_id,$uid);
			}
						
			$data = array();
			$data[] = "window.location = '".base64_decode(JRequest::getVar('return','','get'))."';";
			echo json_encode($data);
			
			$mainframe->close(); 
		}
	}
	
	

	function newLink(){
		// Check for request forgeries
		JRequest::checkToken('get') or jexit( 'Invalid Token' );
		require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_myapi'.DS.'models'.DS.'facebook.php';
		$facebookmodel = new myapiModelfacebook;  //Bring the myAPI facebook model
		$fbUser = $facebookmodel->getLoggedInUser();
		if($fbUser['id'] != ''){
			$user = JFactory::getUser();
			$db = JFactory::getDBO();
			global $facebook;
			$facebookmodel = new myapiModelfacebook;  //Bring the myAPI facebook model
			$facebookSession = $facebook->getSession();
			$query = "INSERT INTO ".$db->nameQuote('#__myapi_users')." (userId,uid,access_token) VALUES(".$db->quote($user->id).",".$db->quote($fbUser['id']).",".$db->quote($facebookSession['access_token']).")";
			$db->setQuery($query);
			$db->query();
			$this->syncPhoto($user->id,$fbUser['id']);
			
			$this->setRedirect(JURI::base(),JText::_('LINK_COMPLETE'));
		}else{
		$this->setRedirect(JURI::base(),JText::_('No Facebook User ID found'));
		}
	}
	
	//New user
	function newUser()
	{
		global $mainframe;

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
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
		if ( !$user->save() )
		{
			$message = $user->getError();
			$this->setRedirect($return,$message);
		}elseif($fbUser['uid'] != ''){
			$db = JFactory::getDBO();
			global $facebook;
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
			$this->syncPhoto($user->id,$fbUser['uid']);
			$this->setRedirect($return,$message);
		}else{
			
			$this->setRedirect($return,'No Facebook User ID found');
			
		}
	}
	
	
	
	function _sendMail(&$user, $password)
	{
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