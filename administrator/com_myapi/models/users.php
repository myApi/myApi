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
		$mainframe =& JFactory::getApplication();
		 
        $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');		
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		
		$this->setState('limitstart', $limitstart);
 		$this->setState('limit', $limit);
		
		//for sortable columns
		$filter_order     = $mainframe->getUserStateFromRequest(  'myapi_users_filter_order', 'filter_order', '#__jos_myapi_users.userId', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( 'myapi_users_filter_order_Dir', 'filter_order_Dir', 'asc', 'word' );
 
        $this->setState( 'myapi_users_filter_order', $filter_order);
        $this->setState( 'myapi_users_filter_order_Dir', $filter_order_Dir);
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
	  $query = "SELECT * FROM ".$db->nameQuote('#__myapi_users')." JOIN ".$db->nameQuote('#__users')." ON ".$db->nameQuote('#__myapi_users.userId')." = ".$db->nameQuote('#__users.id')." ".$this->_buildWhereClause()." ".$this->_buildContentOrderBy();
	  return $query;
	}
	
	function _buildWhereClause(){
		$mainframe =& JFactory::getApplication();
		$db = JFactory::getDBO();
		
		$type	= $mainframe->getUserStateFromRequest( 'myapi_users_filter_type', 'filter_type', '0', 'string');
		$search	= $mainframe->getUserStateFromRequest( 'myapi_users_search', 'search', '', 'string' );
		if (strpos($search, '"') !== false) {
			$search = str_replace(array('=', '<'), '', $search);
		}
		$search = JString::strtolower($search);
		
		if($type != '0'){
			if ( $type == 'Public Frontend' )
			{
				$where[] = ' #__users.usertype = \'Registered\' OR #__users.usertype = \'Author\' OR #__users.usertype = \'Editor\' OR #__users.usertype = \'Publisher\' ';
			}
			else if ( $type == 'Public Backend' )
			{
				$where[] = '#__users.usertype = \'Manager\' OR #__users.usertype = \'Administrator\' OR #__users.usertype = \'Super Administrator\' ';
			}
			else
			{
				$where[] = '#__users.usertype = LOWER( '.$db->quote($type).' ) ';
			}	
		}
		
		if (isset( $search ) && $search!= ''){
			$searchEscaped = $db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where[] = '#__users.username LIKE '.$searchEscaped.' OR #__users.email LIKE '.$searchEscaped.' OR #__users.name LIKE '.$searchEscaped;
		}
		
		if(is_array(@$where)){
			return "WHERE ".implode(" AND ",$where);
		}else{
			return;
		}
	}
  
  	function _buildContentOrderBy(){
		$mainframe =& JFactory::getApplication();
		$orderby = '';
		$filter_order     = $mainframe->getUserStateFromRequest( 'myapi_users_filter_order', 'filter_order');
		$filter_order_Dir = $mainframe->getUserStateFromRequest( 'myapi_users_filter_order_Dir', 'filter_order_Dir');
		if(!empty($filter_order) && !empty($filter_order_Dir) ){
			$orderby = ' ORDER BY '.$filter_order.' '.$filter_order_Dir;
		}
		return $orderby;
	}
	
	function getUserTypesList(){
		$mainframe =& JFactory::getApplication();
		$db = JFactory::getDBO();
		
		$query = 'SELECT name AS value, name AS text'
			. ' FROM #__core_acl_aro_groups'
			. ' WHERE name != "ROOT"'
			. ' AND name != "USERS"';
		
		$db->setQuery( $query );
		$types[]	= JHTML::_('select.option',  '0', '- '. JText::_( 'Select Group' ) .' -' );
		foreach( $db->loadObjectList() as $obj ) $types[] = JHTML::_('select.option',  $obj->value, JText::_( $obj->text ) );
		
		return JHTML::_('select.genericlist',   $types, 'filter_type', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text',  $mainframe->getUserStateFromRequest( 'myapi_users_filter_type', 'filter_type', 0, 'string' ));	
	}
	
}
?>