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
    public function __construct(& $subject, $config) {
 		parent::__construct($subject, $config);
 		$this->loadLanguage();
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
			
			$query = "SELECT #__users.id,#__myapi_users.access_token,#__myapi_users.userId,#__users.block FROM ".$db->nameQuote('#__myapi_users')." LEFT JOIN #__users ON #__myapi_users.userId = #__users.id WHERE ".$db->nameQuote('uid')." = ".$db->quote($uid);
			$db->setQuery( $query );
			$db->query();
			$result = $db->loadAssoc();
			$id = $result['id'];
			
			if($result['block'] == 0){
				if($id != ''){      
					if($result['access_token'] == ''){
						$facebook = plgSystemmyApiConnect::getFacebook();
						$facebookSession = $facebook->getSession();
						$query = "UPDATE ".$db->nameQuote('#__myapi_users')." SET ".$db->nameQuote('access_token')." = ".$db->nameQuote($facebookSession['access_token'])." WHERE ".$db->nameQuote('uid')." = ".$db->quote($uid);
						$db->setQuery($query);
						$db->query();	
					}
					 
					$user = JFactory::getUser($id);
					$response->status			= JAUTHENTICATE_STATUS_SUCCESS;
					$response->error_message	= '';
					$response->username = $user->username;
					return true;
				}else{
					if($result['userId'] != ''){
						JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_myapi'.DS.'tables');
						$row =& JTable::getInstance('myapiusers', 'Table');
						$row->delete($result['userId']);
						$response->error_message = JText::_('ACCOUNT_DELETED');	
					}else{
						$response->error_message = JText::_('NOT_LINKED');		
					}
					$response->status = JAUTHENTICATE_STATUS_FAILURE;
					return false;
				}
			}else{
				$response->error_message = JText::_('ACCOUNT_BLOCKED');
				$response->status = JAUTHENTICATE_STATUS_FAILURE;
				return false;
			}
			
		}
	 }
}
?>