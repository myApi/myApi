<?php defined('_JEXEC') or die('Direct Access to this location is not allowed.');
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
 **   Joomla! 1.5 Module myApi_fblogin                                      **
 **   @Copyright Copyright (C) 2011 - Thomas Welton                         **
 **   @license GNU/GPL http://www.gnu.org/copyleft/gpl.html                 **	
 **                                                                         **	
 **   myApi_fblogin is free software: you can redistribute it and/or modify **
 **   it under the terms of the GNU General Public License as published by  **
 **   the Free Software Foundation, either version 3 of the License, or	    **	
 **   (at your option) any later version.                                   **
 **                                                                         **
 **   myApi_fblogin is distributed in the hope that it will be useful,	    **
 **   but WITHOUT ANY WARRANTY; without even the implied warranty of	    **
 **   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         **
 **   GNU General Public License for more details.                          **
 **                                                                         **
 **   You should have received a copy of the GNU General Public License	    **
 **   along with myApi_fblogin.  If not, see <http://www.gnu.org/licenses/> **
 **                                                                         **			
 *****************************************************************************/

if(!class_exists('plgSystemmyApiConnect') || !$this->_facebook = plgSystemmyApiConnect::getFacebook() || !file_exists(JPATH_SITE.DS.'components'.DS.'com_myapi'.DS.'models'.DS.'myapi.php'))
	return;
	
require_once JPATH_SITE.DS.'components'.DS.'com_myapi'.DS.'models'.DS.'myapi.php';
$myApiModel = new MyapiModelMyapi;

$doc =& JFactory::getDocument();
$doc->addScript('components'.DS.'com_myapi'.DS.'assets'.DS.'js'.DS.'myApi.js');
$doc->addStylesheet('modules'.DS.'mod_myapi_fbLogin'.DS.'mod_myapi_fbLogin.css');

$user 				= JFactory::getUser();
$classSfx 			= $params->get('moduleclass_sfx');
$width				= $params->get('login_width');
$show_faces 		= $params->get('login_facepile');
$max_rows 			= $params->get('login_facepileRows');
$permissions 		= implode(',',$myApiModel->getPerms());

if($params->get('login_userRedirect') != '1'){
	//same page
	$u =& JFactory::getURI(); 
	$redirect_login = JRoute::_($u->toString(),false);
}
else{
	//redirect to different page
	$menuitem = $params->get('login_userRedirectTo');
	$redirect_login = JRoute::_(JFactory::getApplication()->getMenu()->getItem( $menuitem )->link . "&Itemid=".$menuitem,false);	
}

$u 		= JURI::getInstance( $redirect_login );
$host	= JURI::getInstance(JURI::current());
$port 	= ($host->getPort() == '') ? '' : ":".$host->getPort();
$query 	= ($u->getQuery() == '') ? '' : '?'.$u->getQuery();
$redirect_login	= base64_encode($host->getScheme().'://'.$host->getHost().$port.$u->getPath().$query); 

if($user->guest){
	$joomla_login = $params->get('login_joomlaLogin');
	$loginText = $params->get('login_button_text');
	require(JModuleHelper::getLayoutPath('mod_myapi_fbLogin','guest'));	
	
}else{
	$db = JFactory::getDBO();
	$query = "SELECT ".$db->nameQuote('uid')." FROM ".$db->nameQuote('#__myapi_users')." WHERE userId =".$db->quote($user->id);
	$db->setQuery($query);
	$db->query();
	$num_rows = $db->getNumRows();
	if($num_rows == 0){
		$linked = false;
		$facebook = plgSystemmyApiConnect::getFacebook();
		$loginUrl = $facebook->getLoginUrl(array('next' => JURI::base().'index.php?option=com_myapi&task=newLink&return='.$redirect_login ));
		$linkText = $params->get('login_link_text');
	}
	else { 
		$linked = true; 
		//Get User Avatar
		$avatar = $myApiModel->getAvatar();
	}
	require(JModuleHelper::getLayoutPath('mod_myapi_fbLogin','user'));	
}

?>