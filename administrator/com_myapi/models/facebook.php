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


// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');



class MyapiModelFacebook extends JModel {
   /* Function getParams
   Copied from joomla core to display parameters
   */
  function __construct() {
		parent::__construct();
    }
	
	/*
	Function name: getLoggedInUser
	Parameters: None
	Returns: Returns the UID of the currently logged in user, or false.  Checks for an expired session
	*/
	function getLoggedInUser($access_token = NULL) {
		$facebook = plgSystemmyApiConnect::getFacebook();
		try {
			if(is_null($access_token))
		 		$me = $facebook->api('/me');
			else
				$me = $facebook->api('/me','get',array('access_token' => $access_token));
				
		} catch (FacebookApiException $e) {
		  return false;
		}
		return $me;
	}
	
	function getLoggedInUserLiked(){
		  $facebook = plgSystemmyApiConnect::getFacebook();
		try {
			$plugin =& JPluginHelper::getPlugin('system', 'myApiConnect');
			$com_params = new JParameter( $plugin->params );
		  $fql['email'] =   "SELECT email FROM user WHERE uid = me()";
		  $fql['like'] = "SELECT uid FROM page_fan WHERE uid = me() AND page_id = ".$com_params->get('appId');
		  
		  $param  =   array(
		   'method'    => 'fql.multiquery',
		   'queries'     => json_encode($fql),
		   'callback'  => ''
		  );
		  $fqlResult   =   $facebook->api($param);
		   $return['email'] = $fqlResult[0]['fql_result_set'][0]['email'];
		  
		  if(sizeof($fqlResult[1]['fql_result_set']) > 0)
		  	$return['liked'] = true;
		  else
			$return['liked'] = false;
		  
		  } catch (FacebookApiException $e) {
		  error_log($e);
		  return false;
		}
		return $return;
	}
	
	function getLoggedInUserLikes() {
		$facebook = plgSystemmyApiConnect::getFacebook();
		try {
		  $likes = $facebook->api('/me/likes');
		  
		} catch (FacebookApiException $e) {
		  error_log($e);
		  return false;
		}
		return $likes;
	}
	
	function getUidFromJoomla(){
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$query = "SELECT * FROM ".$db->nameQuote('#__myapi_users')." WHERE ".$db->nameQuote('userId')." = ".$db->quote($user->id);
		$db->setQuery($query);
		return $db->loadAssoc();
	}
}
?>