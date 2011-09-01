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
 
jimport( 'joomla.application.component.view');
class MyapiViewUsers extends JView {
    function display($tpl = null) {
		$mainframe =& JFactory::getApplication();
		$model 		= $this->getModel('users');
		$items 				= $model->getData();      
		$pagination			= $model->getPagination();
		$lists['order'] 	= $mainframe->getUserStateFromRequest(  'myapi_users_filter_order', 'filter_order');
		$lists['order_Dir'] = $mainframe->getUserStateFromRequest( 'myapi_users_filter_order_Dir', 'filter_order_Dir');
		
		$search				= $mainframe->getUserStateFromRequest( 'myapi_users_search', 'search', '', 'string' );
		if (strpos($search, '"') !== false) {
			$search = str_replace(array('=', '<'), '', $search);
		}
		$lists['search'] = JString::strtolower($search);
		$lists['type'] = $model->getUserTypesList();
		
		$this->assignRef( 'lists', $lists );
		$this->assignRef('pagination', $pagination);
		$this->assignRef("users", $items);
		
		$doc =& JFactory::getDocument();
		$version = new JVersion;
   		$joomla = $version->getShortVersion();
		$vnum = substr($joomla,0,3);
		
		if($vnum == '1.5'){
			$doc->addStylesheet(JURI::root().'plugins'.DS.'system'.DS.'myApiConnect'.DS.'myApi.css');
			$doc->addScript(JURI::root().'plugins'.DS.'system'.DS.'myApiConnect'.DS.'myApiModal'.'-J'.$vnum.'.js');
			$doc->addScript(JURI::root().'components/com_myapi/assets/js/myApi'.'-J'.$vnum.'.js');
		}else{
			$doc->addStylesheet(JURI::root().'plugins'.DS.'system'.DS.'myApiConnect'.DS.'myApiConnect'.DS.'myApi.css');
			$doc->addScript(JURI::root().'plugins'.DS.'system'.DS.'myApiConnect'.DS.'myApiConnect'.DS.'myApiModal.js');
			$doc->addScript(JURI::root().'components/com_myapi/assets/js/myApi.js');
		}
		
		
		JToolBarHelper::title(JText::_('USERS_HEADER'), 'facebook.png');
		JToolBarHelper::deleteList(JText::_('UNLINK_USER_DESC'),'unlinkUser', JText::_('UNLINK_USER'));

		parent::display($tpl);
    }
}
?>