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

class MyapiModelPages extends JModel {
  	
	function getPages(){
		$db = JFactory::getDBO();	
		$query = "SELECT * FROM ".$db->nameQuote('#__myapi_pages');
		$db->setQuery($query);
		return $db->loadAssocList();
	}
	
	var $_total = null;
  	var $_pagination = null;
	
	function __construct() {
		parent::__construct();
		global $mainframe;
		 
        $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');		
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		
		$this->setState('limitstart', $limitstart);
 		$this->setState('limit', $limit);
		
		//for sortable columns
		$filter_order     = $mainframe->getUserStateFromRequest(  'myapi_pages_filter_order', 'filter_order', '#__jos_myapi_pages.name', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( 'myapi_pages_filter_order_Dir', 'filter_order_Dir', 'asc', 'word' );
		$filter_category		= $mainframe->getUserStateFromRequest( 'myapi_pages_filter_category',		'filter_category', 		0,			'string' );
		$search				= $mainframe->getUserStateFromRequest( 'myapi_pages_search',			'search', 			'',			'string' );
 
        $this->setState('myapi_pages_filter_order', $filter_order);
        $this->setState('myapi_pages_filter_order_Dir', $filter_order_Dir);
		$this->setState('myapi_pages_filter_category', $filter_category);
		$this->setState('myapi_pages_search', $search);
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
	  $query = "SELECT * FROM ".$db->nameQuote('#__myapi_pages')." ".$this->_buildWhereClause()." ".$this->_buildContentOrderBy();
	  return $query;
	}
	
	function _buildWhereClause(){
		global $mainframe;
		$db = JFactory::getDBO();
		
		$category 	= $mainframe->getUserStateFromRequest( 'myapi_pages_filter_category', 'filter_category', '0', 'string');
		$search		= $mainframe->getUserStateFromRequest( 'myapi_pages_search', 'search', '', 'string' );
		if (strpos($search, '"') !== false) {
			$search = str_replace(array('=', '<'), '', $search);
		}
		$search = JString::strtolower($search);
		
		if($category != '0')
			$where[] = " ".$db->nameQuote('#__myapi_pages.category')." = ".$db->quote($category)." ";
		
		if (isset( $search ) && $search!= ''){
			$searchEscaped = $db->quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where[] = ' ( #__myapi_pages.name LIKE '.$searchEscaped.' OR #__myapi_pages.pageId = '.$searchEscaped.' ) ';
		}
		
		if(is_array(@$where)){
			return "WHERE ".implode(" AND ",$where);
		}else{
			return;
		}
	}
  
  	function _buildContentOrderBy(){
		global $mainframe;
		$orderby = '';
		$filter_order     = $mainframe->getUserStateFromRequest( 'myapi_pages_filter_order', 'filter_order');
		$filter_order_Dir = $mainframe->getUserStateFromRequest( 'myapi_pages_filter_order_Dir', 'filter_order_Dir');
		if(!empty($filter_order) && !empty($filter_order_Dir) ){
			$orderby = ' ORDER BY '.$filter_order.' '.$filter_order_Dir;
		}
		return $orderby;
	}
	
	function getCategories(){
		$db = JFactory::getDBO();
		$query = "SELECT ".$db->nameQuote('category')." FROM ".$db->nameQuote('#__myapi_pages')." GROUP BY ".$db->nameQuote('category');
		$db->setQuery($query);
		$db->query();
		return $db->loadObjectList();	
	}
	
	function getCategoriesList(){
		global $mainframe;
		$cats = $this->getCategories();
		$catOptions = array(JHTML::_('select.option', 0, '- '.JText::_('CATEGORY_SELECT').' -' ));
		foreach($cats as $cat){
			$catOptions[] = JHTML::_('select.option',  $cat->category, $cat->category );	
		}
		return JHTML::_('select.genericlist',   $catOptions, 'filter_category', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text',  $mainframe->getUserStateFromRequest( 'myapi_pages_filter_category', 'filter_category', 0, 'string' ));
	}
}
?>