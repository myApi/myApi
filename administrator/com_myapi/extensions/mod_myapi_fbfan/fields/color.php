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
 **   Joomla! 1.5 Module mod_myapi_fbFan                                    **
 **   @Copyright Copyright (C) 2011 - Thomas Welton                         **
 **   @license GNU/GPL http://www.gnu.org/copyleft/gpl.html                 **	
 **                                                                         **	
 **   mod_myapi_fbFan is free software: you can redistribute it and/or      **
 **   modify it under the terms of the GNU General Public License as        **
 **   published by the Free Software Foundation, either version 3 of the    **	
 **   License, or (at your option) any later version.                       **
 **                                                                         **
 **   mod_myapi_fbFan is distributed in the hope that it will be useful,    **
 **   but WITHOUT ANY WARRANTY; without even the implied warranty of	    **
 **   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         **
 **   GNU General Public License for more details.                          **
 **                                                                         **
 **   You should have received a copy of the GNU General Public License	    **
 **   along with mod_myapi_fbFan. If not, see <http://www.gnu.org/licenses/>**
 **                                                                         **			
 *****************************************************************************/
 
class JFormFieldColor extends JFormField{
	protected function getInput(){
		$startColor = ($this->value == '') ? '#CCC' :$this->value;
		$js = "window.addEvent('domready',function(){
				var r = new MooRainbow('".$this->name."', { 
					imgPath: '".JURI::root()."modules/mod_myapi_fbFan/fields/moorainbow/images/',
					startColor: startValue,
					onChange: function(color) { 
						this.element.value = color.hex; 
					} 
				});
			});";
			
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($js);
		$document->addScript(JURI::root()."modules/mod_myapi_fbFan/fields/moorainbow/mooRainbow.js");
		$document->addStyleSheet(JURI::root()."modules/mod_myapi_fbFan/fields/moorainbow/mooRainbow.css");
		
		return '<input name="'.$this->name.'" type="text" class="inputbox" id="'.$this->name.'" value="'.$this->value.'" size="10" />';	
	}
}