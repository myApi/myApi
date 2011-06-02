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

class MyapiModelUsers extends JModel {
  	
	var $_total = null;
  	var $_pagination = null;
	
	function __construct() {
		parent::__construct();
		global $mainframe, $option;
		 
        $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');		
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		
		$this->setState('limitstart', $limitstart);
 		$this->setState('limit', $limit);
		
		//for sortable columns
		$filter_order     = $mainframe->getUserStateFromRequest(  $option.'filter_order', 'filter_order', '#__jos_myapi_users.userId', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $option.'filter_order_Dir', 'filter_order_Dir', 'asc', 'word' );
 
        $this->setState('filter_order', $filter_order);
        $this->setState('filter_order_Dir', $filter_order_Dir);
    }
	
	function getUsers(){
		$db =& JFactory::getDBO();
		$query = "SELECT * FROM #__myapi_users join #__users on #__myapi_users.userId = #__users.id;";
		$db->setQuery($query);
		$users = $db->loadAssocList();
		return $users;
	}
	
	function getData(){
		if(empty($this->_data)){
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit')); 
		}
		return $this->_data;
	}
	function getTotal(){
		if (empty($this->_total)) {
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);    
		}
		return $this->_total;
	}
	function getPagination(){
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}
	
	function _buildQuery(){  
	  $db = JFactory::getDBO();
	  $query = "SELECT * FROM ".$db->nameQuote('#__myapi_users')." JOIN ".$db->nameQuote('#__users')." ON ".$db->nameQuote('#__myapi_users.userId')." = ".$db->nameQuote('#__users.id')." ".$this->_buildContentOrderBy();
	  return $query;
	}
  
  	function _buildContentOrderBy(){
		global $mainframe, $option;
		$orderby = '';
		$filter_order     = $mainframe->getUserStateFromRequest( $option.'filter_order', 'filter_order');
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $option.'filter_order_Dir', 'filter_order_Dir');
		if(!empty($filter_order) && !empty($filter_order_Dir) ){
			$orderby = ' ORDER BY '.$filter_order.' '.$filter_order_Dir;
		}
		return $orderby;
	}
	
}
?>