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
jimport( 'joomla.application.component.view');
class MyapiViewLike extends JView {
    function display($tpl = null) {
       	
		
		
		$db = JFactory::getDBO();
		$query = "SELECT id FROM #__plugins WHERE element =".$db->quote('myApiLike');
		$db->setQuery($query);
		$id = $db->loadResult();
		
		$row 	=& JTable::getInstance('plugin');
		$row->load($id);
		
		$plugin = & JPluginHelper::getPlugin('content', 'myApiLike');
		if(is_object($plugin)){
			$paramsdata = $plugin->params;
			$paramsdefs = JPATH_SITE.DS.'plugins'.DS.'content'.DS.'myApiLike.xml';
			$params = new JParameter( $paramsdata, $paramsdefs );
			$this->assignRef('params',$params);
			$this->assignRef('plugin',$row);
			JToolbarHelper::save('savePlugin','Save Settings');
		}else{
			global $mainframe;
			$mainframe->redirect('index.php?option=com_plugins&view=plugin&task=edit&cid='.$id,'The myApiLike plugin needs to be ENABLED before you can configure it.');	
		}
		
	
		
	 parent::display($tpl);
    }
}
?>