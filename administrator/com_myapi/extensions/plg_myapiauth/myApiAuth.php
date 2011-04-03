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
 **   Joomla! 1.5 Plugin myApiAuth                                          **
 **   @Copyright Copyright (C) 2011 - Thomas Welton                         **
 **   @license GNU/GPL http://www.gnu.org/copyleft/gpl.html                 **	
 **                                                                         **	
 **   myApiAuth is free software: you can redistribute it and/or modify     **
 **   it under the terms of the GNU General Public License as published by  **
 **   the Free Software Foundation, either version 3 of the License, or	    **	
 **   (at your option) any later version.                                   **
 **                                                                         **
 **   myApiAuth is distributed in the hope that it will be useful,          **
 **   but WITHOUT ANY WARRANTY; without even the implied warranty of	    **
 **   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         **
 **   GNU General Public License for more details.                          **
 **                                                                         **
 **   You should have received a copy of the GNU General Public License	    **
 **   along with myApiAuth.  If not, see <http://www.gnu.org/licenses/>.    **
 **                                                                         **			
 *****************************************************************************/
  
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
 
jimport('joomla.event.plugin');

class plgAuthenticationmyApiAuth extends JPlugin
{
    function plgAuthenticationmyApiAuth(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}
 
  
    function onAuthenticate($uid, $options, &$response )
    {
		if(!file_exists(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'myApiConnectFacebook.php')){ return; }
		if(isset($options['group'])){
			if($options['group']!="Public Backend"){
				return;
			}
			
		}
      	if(!is_array($uid)){
		$db =& JFactory::getDBO();
		
		$query = "SELECT #__users.id FROM ".$db->nameQuote('#__myapi_users')." JOIN #__users ON #__myapi_users.userId = #__users.id WHERE ".$db->nameQuote('uid')." = ".$db->quote($uid)." AND #__users.block = '0'";
		$db->setQuery( $query );
		$db->query();
		$id = $db->loadResult();
		
		if($id != '')
		{       
				//If facebook user had a linked account
				$query = "SELECT ".$db->nameQuote('username')." FROM ".$db->nameQuote('#__users')." WHERE ".$db->nameQuote('id')." = ".$db->quote($id);
				$db->setQuery( $query );
				$db->query();
				$username = $db->loadResult();
				
				if($username != '')
				{
					$user = JFactory::getUser($id);
					$response->status			= JAUTHENTICATE_STATUS_SUCCESS;
					$response->error_message	= '';
					$response->username = $user->username;
					return true;
				}
				else
				{
					
					$query = "DELETE FROM ".$db->nameQuote('#__myapi_users')." WHERE ".$db->nameQuote('uid')." = ".$db->quote($uid);
					$db->setQuery( $query );
					$db->query();
					
					$response->status = JAUTHENTICATE_STATUS_FAILURE;
					$response->error_message = 'Woops - Looks like the account you had linked to your facebook account has been deleted.  Please create a new user account or log in with another username';
					return false;
					
				}
		
		}
		else
		{
			
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = 'This facebook profile is not linked to any existing account';
			return false;
		}
	}
	 }
}
?>