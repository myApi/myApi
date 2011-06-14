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


class MyapiViewPages extends JView {
    function display($tpl = null) {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');
		
		$model 	= $this->getModel('pages');
		
		if(JRequest::getVar('layout','default') == 'default'){
			JToolBarHelper::title(JText::_('PAGES_HEADER'));
			JToolBarHelper::custom('composeMessage','send.png','send_f2.png','Post to Wall',true);
			JToolBarHelper::addNew('addPages', JText::_('ADD_PAGES'));
			JToolBarHelper::deleteList(JText::_('DELETE_PAGES_DESC'),'deletePages', JText::_('DELETE_PAGES'));
			$pages 				= $model->getData();      
			$pagination			= $model->getPagination();
			$lists['order'] 	= $mainframe->getUserStateFromRequest(  'myapi_pages_filter_order', 'filter_order');
			$lists['order_Dir'] = $mainframe->getUserStateFromRequest( 'myapi_pages_filter_order_Dir', 'filter_order_Dir');
			$search				= $mainframe->getUserStateFromRequest( 'myapi_pages_search',			'search', 			'',			'string' );
			if (strpos($search, '"') !== false) {
				$search = str_replace(array('=', '<'), '', $search);
			}
			$lists['search'] = JString::strtolower($search);
			$lists['categories'] = $model->getCategoriesList();
			
			$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
			$limitstart = $mainframe->getUserStateFromRequest( 'myapi_pages_.limitstart', 'limitstart', 0, 'int' );
			
			$this->assignRef( 'lists', $lists );
			$this->assignRef('pagination', $pagination);
			$this->assignRef('pages',$pages);
		}else{
			JToolBarHelper::title(JText::_('PAGES_COMPOSE_HEADER'));
			JToolBarHelper::custom('sendMessage','send.png','send_f2.png',JText::_('POST_TO_WALL'),false);
			JToolBarHelper::cancel();
			
			$cid = JRequest::getVar('cid');
			$pages = $model->getPageDetails($cid);
			$menulist = $model->getMenuItemList(JRequest::getVar('menuItem'));
			$filelist = $model->getFileList(JRequest::getVar('fileList'));
			
			$this->assignRef('pages',$pages);
			$this->assignRef('cid',$cid);
			$this->assignRef('menulist',$menulist);
			$this->assignRef('filelist',$filelist);
			
		}
		parent::display($tpl);	
	}
	
}
?>