<?php defined('_JEXEC') or die('Restricted access');
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
 **   Joomla! 1.5 Component myApi                                           **
 **   @Copyright Copyright (C) 2010 - Thomas Welton                         **
 **   @license GNU/GPL http://www.gnu.org/copyleft/gpl.html                 **	
 **                                                                         **	
 **   myApi is free software: you can redistribute it and/or modify         **
 **   it under the terms of the GNU General Public License as published by  **
 **   the Free Software Foundation, either version 3 of the License, or	    **	
 **   (at your option) any later version.                                   **
 **                                                                         **
 **   myApi is distributed in the hope that it will be useful,	            **
 **   but WITHOUT ANY WARRANTY; without even the implied warranty of	    **
 **   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         **
 **   GNU General Public License for more details.                          **
 **                                                                         **
 **   You should have received a copy of the GNU General Public License	    **
 **   along with myApi.  If not, see <http://www.gnu.org/licenses/>.	    **
 **                                                                         **			
 *****************************************************************************/

jimport('joomla.application.component.model');

class MyapiModelRealtime extends JModel {
  	
	var $access_token = null;
	
	function getRealTimeAccess(){
		if(is_null($this->access_token)){
			global $postFacebook;
			$facebook = (is_object($postFacebook)) ? $postFacebook : plgSystemmyApiConnect::getFacebook();
			$config 	=& JFactory::getConfig();
			$token = file_get_contents('https://graph.facebook.com/oauth/access_token?client_id='.$facebook->getAppId().'&client_secret='.$facebook->getApiSecret().'&grant_type=client_credentials');
			$params = null;
			parse_str($token, $params);
			$this->access_token = $params['access_token'];
		}
		return $this->access_token;
	}
	
	function getSubscriptions(){
		$facebook = plgSystemmyApiConnect::getFacebook();
		if($facebook)
			return $facebook->api('/'.$facebook->getAppId().'/subscriptions','get',array('access_token' => MyapiModelRealtime::getRealTimeAccess() ));
		else
			return false;
	}
	
	function addSubscriptions(){
		global $postFacebook;
		$facebook = (is_object($postFacebook)) ? $postFacebook : plgSystemmyApiConnect::getFacebook();
		if($facebook){
			require_once(JPATH_SITE.DS.'components'.DS.'com_myapi'.DS.'models'.DS.'myapi.php');
			$myApiModel = new MyapiModelMyapi;
			$config 	=& JFactory::getConfig();
			
			$u =& JURI::getInstance( JURI::root() );
			$port 	= ($u->getPort() == '') ? '' : ":".$u->getPort();
			$callback = $u->getScheme().'://'.$u->getHost().$port.$u->getPath().'index.php?option=com_myapi&task=facebookRealTime';
			try{
				$subscription = $facebook->api('/'.$facebook->getAppId().'/subscriptions','post',array('object' => 'user', 'fields' => 'email,name,pic,status,about_me,username','callback_url' => $callback, 'verify_token' => $config->getValue( 'config.secret' ), 'access_token' => $this->getRealTimeAccess() ));
				
				// Waiting on FB bug fix http://bugs.developers.facebook.net/show_bug.cgi?id=18048
				//$subscription = $facebook->api('/'.$facebook->getAppId().'/subscriptions','post',array('object' => 'page', 'fields' => 'feed','callback_url' => $callback, 'verify_token' => $config->getValue( 'config.secret' ), 'access_token' => $this->getRealTimeAccess() ));
				//
				$subscription = $facebook->api('/'.$facebook->getAppId().'/subscriptions','post',array('object' => 'permissions', 'fields' => implode(',',array_merge(array('manage_pages','read_stream','publish_stream'),$myApiModel->getPerms())),'callback_url' => $callback, 'verify_token' => $config->getValue( 'config.secret' ), 'access_token' => $this->getRealTimeAccess() ));
				JFactory::getApplication()->enqueueMessage( JText::_('SUBSCRIPTIONS_ADDED') );
			} catch (FacebookApiException $e) {
				JError::raiseNotice( 100, $e->__toString());		
			}
		}
		return;
	}
	
	function deleteSubscriptions(){
		$mainframe =& JFactory::getApplication();
		$facebook = plgSystemmyApiConnect::getFacebook();
		try{
			foreach($_POST['cid'] as $object){
				$facebook->api('/'.$facebook->getAppId().'/subscriptions','delete',array('access_token' => $this->getRealTimeAccess(),'object' => $object));
			}
			JFactory::getApplication()->enqueueMessage( JText::_('SUBSCRIPTIONS_DELETED') );
		} catch (FacebookApiException $e) {
			JError::raiseNotice( 100, $e->__toString() );
		}
		return;
	}
}
?>