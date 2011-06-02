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

class MyapiViewPlugin extends JView {
    function display($tpl = null) {
		$db = JFactory::getDBO();
		$query = "SELECT id FROM #__plugins WHERE element =".$db->quote(JRequest::getVar('plugin'));
		$db->setQuery($query);
		$id = $db->loadResult();
		
		$row 	=& JTable::getInstance('plugin');
		$row->load($id);
		
		$plugin = & JPluginHelper::getPlugin($row->folder, $row->element);
		if(is_object($plugin)){
			$lang =& JFactory::getLanguage();
			$lang->load( 'plg_' . trim( $row->folder ) . '_' . trim( $row->element ), JPATH_ADMINISTRATOR );
			
			$doc =& JFactory::getDocument();
			$doc->addStyleSheet( JURI::base().'/components/com_myapi/assets/styles.css' );
			JToolBarHelper::title(JText::_(strtoupper($row->element).'_HEADER'), 'facebook.png');
			
			$paramsdata = $plugin->params;
			$paramsdefs = JPATH_SITE.DS.'plugins'.DS.$row->folder.DS.$row->element.'.xml';
			$params = new JParameter( $paramsdata, $paramsdefs );
			$this->assignRef('params',$params);
			$this->assignRef('plugin',$row);
			$this->assignRef('description',JText::_(strtoupper($row->element).'_DESC'));
			JToolbarHelper::save('savePlugin',JText::_('SAVE'));
			
			$funcname = '_'.$row->element;
			if(method_exists('MyapiViewPlugin','_'.$row->element)) $this->$funcname();
		}else{
			global $mainframe;
			$mainframe->redirect('index.php?option=com_plugins&view=plugin&task=edit&cid='.$id,JText::_('ENABLE_PLUGIN'));		
		}
		parent::display($tpl);
    }
	
	function _myApiConnect(){
		$this->assignRef('aside',JHTML::_('image', 'plugins/system/fbapp.png', null));
	}
	
	function _myApiTabs(){
		$facebook = plgSystemmyApiConnect::getFacebook();
		$pageLink = 'http://www.facebook.com/apps/application.php?id='.$facebook->getAppId();
		$sideContent = JText::sprintf('MYAPITABS_SIDE',$pageLink).'<br />'.JHTML::_('image', 'plugins/system/addtab.png', null);
		$this->assignRef('aside',$sideContent);
	}
}
?>