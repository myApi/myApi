<?php
/*****************************************************************************
 **                                                                         ** 
 **                                         .o.                   o8o  	    **
 **                                        .888.                  `"'  	    **
 **     ooo. .oo.  .oo.   oooo    ooo     .8"888.     oo.ooooo.  oooo  	    **
 **     `888P"Y88bP"Y88b   `88.  .8'     .8' `888.     888' `88b `888  	    **
 **      888   888   888    `88..8'     .88ooo8888.    888   888  888  	    **
 **      888   888   888     `888'     .8'     `888.   888   888  888  	    **
 **     o888o o888o o888o     .8'     o88o     o8888o  888bod8P' o888o      **
 **                       .o..P'                       888             	    **
 **                       `Y8P'                       o888o            	    **
 **                                                                         **
 **                                                                         **
 **   Joomla! 1.5 Plugin myApiUser                                          **
 **   @Copyright Copyright (C) 2011 - Thomas Welton                         **
 **   @license GNU/GPL http://www.gnu.org/copyleft/gpl.html                 **	
 **                                                                         **	
 **   myApiUser is free software: you can redistribute it and/or modify     **
 **   it under the terms of the GNU General Public License as published by  **
 **   the Free Software Foundation, either version 3 of the License, or	    **	
 **   (at your option) any later version.                                   **
 **                                                                         **
 **   myApiUser is distributed in the hope that it will be useful,	        **
 **   but WITHOUT ANY WARRANTY; without even the implied warranty of	    **
 **   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         **
 **   GNU General Public License for more details.                          **
 **                                                                         **
 **   You should have received a copy of the GNU General Public License	    **
 **   along with myApiUser.  If not, see <http://www.gnu.org/licenses/>.    **
 **                                                                         **			
 *****************************************************************************/
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.plugin.plugin');

class plgUsermyApiUser extends JPlugin {


	function plgUserJoomla(& $subject, $config) {
		parent::__construct($subject, $config);
	}


	
	function onAfterDeleteUser($user, $succes, $msg)
	{
		$db =& JFactory::getDBO();
		$db->setQuery('DELETE FROM #__myapi_users WHERE userId ='.$db->Quote($user['id']));
		$db->Query();	
	}
	
	
	
	
	function onLoginUser($user, $options = array())
	{
		if(!file_exists(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'myApiConnectFacebook.php')){ return; }
		if(@$options['uid'] == ''){  return; }
		global $mainframe;
		if($mainframe->isAdmin() || strpos($_SERVER["PHP_SELF"], "index.php") === false) { return; }
		
		
		$db =& JFactory::getDBO();
		
		jimport('joomla.user.helper');
		$instance =& $this->_getUser($user, $options);
		
		$uid = $options['uid'];
		if($uid && $instance->id != ''):
		  $query = "SELECT ".$db->nameQuote('userId').",".$db->nameQuote('access_token')." FROM ".$db->nameQuote('#__myapi_users')." WHERE uid =".$db->quote($uid);
		  $db->setQuery( $query );
		  $result = $db->loadAssoc();
		  $count = $db->getAffectedRows();
		  
		  $facebook = plgSystemmyApiConnect::getFacebook();
			  $facebookSession = $facebook->getSession();
		  
		  if($count == 0)
		  {
			  jimport( 'joomla.filesystem.folder' );
			  if(!JFolder::exists(JPATH_SITE.DS.'images'.DS.'comprofiler'))
				  JFolder::create(JPATH_SITE.DS.'images'.DS.'comprofiler');
				  
			  $dest = JPATH_SITE.DS.'images'.DS.'comprofiler'.DS.'tn'.'facebookUID'.$facebookSession['uid'].'.jpg';
			  $avatar = 'facebookUID'.$facebookSession['uid'].'.jpg';
			  $buffer = file_get_contents('https://graph.facebook.com/'.$facebookSession['uid'].'/picture',$dest);
			  jimport( 'joomla.filesystem.file' );
			  JFile::write($dest,$buffer);
			
			  $query = "INSERT INTO ".$db->nameQuote('#__myapi_users')."  (userId,uid,access_token,avatar) VALUES (".$db->quote($instance->id).",".$db->quote($uid).",".$db->quote($facebookSession['access_token']).",".$db->quote($avatar).")";
			  $db->setQuery( $query );
			  $db->query();
		  }
		  else{
			  //A connect user has logged in
			  if($result['access_token'] == ''){
				   $query = "UPDATE ".$db->nameQuote('#__myapi_users')." SET  ".$db->nameQuote('access_token')." = ".$db->quote($facebookSession['access_token'])." WHERE ".$db->nameQuote('userId')." = ".$db->quote($result['userId']);
			  $db->setQuery( $query );
			  $db->query();
			  }
		  }
		endif;
	}
	
	
	function &_getUser($user, $options = array())
	{
		$instance = new JUser();
		if($id = intval(JUserHelper::getUserId($user['username'])))  {
			$instance->load($id);
			return $instance;
		}

		//TODO : move this out of the plugin
		jimport('joomla.application.component.helper');
		$config   = &JComponentHelper::getParams( 'com_users' );
		$usertype = $config->get( 'new_usertype', 'Registered' );

		$acl =& JFactory::getACL();

		$instance->set( 'id'			, 0 );
		$instance->set( 'name'			, $user['fullname'] );
		$instance->set( 'username'		, $user['username'] );
		$instance->set( 'password_clear'	, $user['password_clear'] );
		$instance->set( 'email'			, $user['email'] );	// Result should contain an email (check)
		$instance->set( 'gid'			, $acl->get_group_id( '', $usertype));
		$instance->set( 'usertype'		, $usertype );

		//If autoregister is set let's register the user
		$autoregister = isset($options['autoregister']) ? $options['autoregister'] :  $this->params->get('autoregister', 1);

		if($autoregister)
		{
			if(!$instance->save()) {
				return JError::raiseWarning('', $instance->getError());
			}
		} else {
			// No existing user and autoregister off, this is a temporary user
			$instance->set( 'tmp_user', true );
		}

		return $instance;
	}
	
	
}
