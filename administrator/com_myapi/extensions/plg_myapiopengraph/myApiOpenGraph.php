<?php defined( '_JEXEC' ) or die( 'Restricted access' );
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
 **   Joomla! 1.5 Plugin myApiOpenGraph                                     **
 **   @Copyright Copyright (C) 2011 - Thomas Welton                         **
 **   @license GNU/GPL http://www.gnu.org/copyleft/gpl.html                 **	
 **                                                                         **	
 **   myApiConnect is free software: you can redistribute it and/or modify  **
 **   it under the terms of the GNU General Public License as published by  **
 **   the Free Software Foundation, either version 3 of the License, or	    **	
 **   (at your option) any later version.                                   **
 **                                                                         **
 **   myApiConnect is distributed in the hope that it will be useful,	    **
 **   but WITHOUT ANY WARRANTY; without even the implied warranty of	    **
 **   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         **
 **   GNU General Public License for more details.                          **
 **                                                                         **
 **   You should have received a copy of the GNU General Public License	    **
 **   along with myApiConnect.  If not, see <http://www.gnu.org/licenses/>  **
 **                                                                         **			
 *****************************************************************************/
jimport( 'joomla.plugin.plugin');
class plgSystemmyApiOpenGraph extends JPlugin{
	
	static $ogptags = array();

	public function __construct(& $subject, $config) {
 		parent::__construct($subject, $config);
 		$this->loadLanguage();
		
		if(!class_exists('plgSystemmyApiConnect') || !$facebook = plgSystemmyApiConnect::getFacebook()) return;
		
		$cache = & JFactory::getCache('plgSystemmyApiOpenGraph - FB Admins query');
		$cache->setCaching( 1 );
		$config 	=& JFactory::getConfig();
		
		$db_admins = $cache->call( array( 'plgSystemmyApiOpenGraph', 'getFbAdmins'));
		$param_admins = ($this->params->get('fbadmins') != '') ? explode(',',$this->params->get('fbadmins')) : array();
		$admins = array_merge($db_admins,$param_admins);
		
		$ogptags_default					= array();
		$ogptags_default['og:title']		= $config->getValue( 'config.sitename' );
		$ogptags_default['og:type'] 		= 'website';
		$ogptags_default['og:url'] 			= JURI::getInstance()->toString();
		$ogptags_default['og:site_name']	= $config->getValue( 'config.sitename' );
		$ogptags_default['fb:app_id'] 		= $facebook->getAppId();
		$ogptags_default['fb:admins']		= implode(',',$admins);
		if($this->params->get('ogimage') != '' && $this->params->get('ogimage') != -1) $ogptags_default['og:image'] = JURI::base().'images/'.$this->params->get('ogimage');
		if($this->params->get('fbpageid') != '') $ogptags_default['fb:page_id'] = $this->params->get('fbpageid');
		
		plgSystemmyApiOpenGraph::setTags($ogptags_default);
	}
	
	function getFbAdmins(){
		$db = JFactory::getDBO();
		$version = new JVersion;
   	 	$joomla = $version->getShortVersion();
    	if(substr($joomla,0,3) == '1.6'){
			$query = "SELECT ".$db->nameQuote('uid')." FROM ".$db->nameQuote('#__myapi_users')." JOIN ".$db->nameQuote('#__user_usergroup_map')." ON ".$db->nameQuote('#__myapi_users').".".$db->nameQuote('userId')." = ".$db->nameQuote('#__user_usergroup_map').".".$db->nameQuote('user_id')." WHERE ".$db->nameQuote('#__user_usergroup_map').".".$db->nameQuote('group_id')." = ".$db->quote('8');
		}else{
			$query = "SELECT ".$db->nameQuote('uid')." FROM ".$db->nameQuote('#__myapi_users')." JOIN ".$db->nameQuote('#__users')." ON ".$db->nameQuote('#__myapi_users.userId')." = ".$db->nameQuote('#__users.id')." WHERE ".$db->nameQuote('#__users.gid')." = ".$db->quote('25');
		}
		$db->setQuery($query);
		return $db->loadResultArray();
	}
	
	function setTags($array){
		plgSystemmyApiOpenGraph::$ogptags = array_merge(plgSystemmyApiOpenGraph::$ogptags, $array);
	}
	
	function onAfterDispatch(){
		$mainframe =& JFactory::getApplication();
		$document = JFactory::getDocument(); 
		if($document->getType() != 'html' || $mainframe->isAdmin()) return;
		foreach(plgSystemmyApiOpenGraph::$ogptags as $key => $value) $document->addCustomTag('<meta property="'.$key.'" content="'.htmlspecialchars($value).'" />');
	}
	
	function onAfterRender(){
		$mainframe =& JFactory::getApplication();
		$document = JFactory::getDocument(); 
		if(!class_exists('plgSystemmyApiConnect') || $document->getType() != 'html' || $mainframe->isAdmin()) return;
		
		$buffer = JResponse::getBody();
		
		$version = new JVersion;
   		$joomla = $version->getShortVersion();
		$vnum = substr($joomla,0,3);
		if($vnum == '1.5'){
			require_once(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'myApiDom.php');
		}else{
			require_once(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'myApiConnect'.DS.'myApiDom.php');
		}
		$dom = new simple_html_dom();
		$dom->load($buffer);
		$htmlEl = $dom->find('html',0);
		$xmlns = 'xmlns:og';
		$htmlEl->$xmlns = "http://ogp.me/ns#";
		
		JResponse::setBody( $dom );		
		$dom->clear(); 
		unset($dom);
	}
}
?>