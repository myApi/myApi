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
 **   Joomla! 1.5 Module mod_myapi_fbActivity                               **
 **   @Copyright Copyright (C) 2011 - Thomas Welton                         **
 **   @license GNU/GPL http://www.gnu.org/copyleft/gpl.html                 **	
 **                                                                         **	
 **   mod_myapi_fbActivity is free software: you can redistribute it and/or **
 **   modify it under the terms of the GNU General Public License as        **
 **   published by the Free Software Foundation, either version 3 of the    **	
 **   License, or (at your option) any later version.                       **
 **                                                                         **
 **   mod_myapi_fbActivity is distributed in the hope that it will be       **
 **   useful but WITHOUT ANY WARRANTY; without even the implied warranty of **
 **   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         **
 **   GNU General Public License for more details.                          **
 **                                                                         **
 **   You should have received a copy of the GNU General Public License	    **
 **   along with mod_myapi_fbActivity.										**
 **                                                                         **			
 *****************************************************************************/
 
class JElementColor extends JElement{
	function fetchElement($name, $value, &$node, $control_name){
		static $embedded;
		if(!$embedded){
			$js = "window.addEvent('domready',function(){
					Element.extend({
						getSiblings: function() {
							return this.getParent().getChildren().remove(this);
						}
					});
					
					$$('.rainbowbtn').each(function(item){
						item.color=new MooRainbow(item.id, {
							startColor: [58, 142, 246],
							wheel: true,
							id:item.id+'x',
							onChange: function(color) {
							item.getSiblings()[0].value = color.hex;
							},
							onComplete: function(color) {
							item.getSiblings()[0].value = color.hex;
							},
							imgPath: '".JURI::root()."modules/mod_myapi_fbActivity/elements/moorainbow/images/'
						});
					});
				});";
				
			$document = JFactory::getDocument();
			$document->addScriptDeclaration($js);
			$document->addScript(JURI::root()."modules/mod_myapi_fbActivity/elements/moorainbow/mooRainbow.js");
			$document->addStyleSheet(JURI::root()."modules/mod_myapi_fbActivity/elements/moorainbow/mooRainbow.css");
			$embedded=true;
		} 
		$html = '<input name="'.$control_name.'['.$name.']" type="text" class="inputbox" id="'.$control_name.$name.'" value="'.$value.'" size="10" />'.
            	'<button type="button" id="img'.$name.'" class="rainbowbtn">'.JText::_('COLOR_PICKER').'</button>';	
		
		return $html;
	}
}