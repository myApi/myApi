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
 **   Joomla! 1.5 Module myApi_fbLive                                       **
 **   @Copyright Copyright (C) 2011 - Thomas Welton                         **
 **   @license GNU/GPL http://www.gnu.org/copyleft/gpl.html                 **	
 **                                                                         **	
 **   myApi_fbLive is free software: you can redistribute it and/or modify  **
 **   it under the terms of the GNU General Public License as published by  **
 **   the Free Software Foundation, either version 3 of the License, or	    **	
 **   (at your option) any later version.                                   **
 **                                                                         **
 **   myApi_fbLive is distributed in the hope that it will be useful,	    **
 **   but WITHOUT ANY WARRANTY; without even the implied warranty of	    **
 **   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         **
 **   GNU General Public License for more details.                          **
 **                                                                         **
 **   You should have received a copy of the GNU General Public License	    **
 **   along with myApi_fbLive.  If not, see <http://www.gnu.org/licenses/>.	**
 **                                                                         **			
 *****************************************************************************/

if(!class_exists('plgSystemmyApiConnect'))
	return;
	
$plugin 	=& JPluginHelper::getPlugin('system', 'myApiConnect');
$com_params = new JParameter( $plugin->params );

$app_id 		= $com_params->get('appId');	
$classSfx 		= $params->get('moduleclass_sfx');
$width 			= $params->get('live_width');
$height 		= $params->get('live_height');
$xid			= $params->get('live_xid');
$always_post	= $params->get('live_always_post');

$via_url = JURI::getInstance()->toString();

require(JModuleHelper::getLayoutPath('mod_myapi_fbLive','default'));

?>