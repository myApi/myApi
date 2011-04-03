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
 **   Joomla! 1.5 Module myApi_fbfan                                        **
 **   @Copyright Copyright (C) 2011 - Thomas Welton                         **
 **   @license GNU/GPL http://www.gnu.org/copyleft/gpl.html                 **	
 **                                                                         **	
 **   myApi_fbfan is free software: you can redistribute it and/or modify   **
 **   it under the terms of the GNU General Public License as published by  **
 **   the Free Software Foundation, either version 3 of the License, or	    **	
 **   (at your option) any later version.                                   **
 **                                                                         **
 **   myApi_fbfan is distributed in the hope that it will be useful,	    **
 **   but WITHOUT ANY WARRANTY; without even the implied warranty of	    **
 **   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         **
 **   GNU General Public License for more details.                          **
 **                                                                         **
 **   You should have received a copy of the GNU General Public License	    **
 **   along with myApi_fbfan.  If not, see <http://www.gnu.org/licenses/>.	**
 **                                                                         **			
 *****************************************************************************/
 
//don't allow other scripts to grab and execute our file
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

$width = $params->get('recommendations_width');
$height = $params->get('recommendations_height');

$header = $params->get('recommendations_header');
$font = $params->get('recommendations_font');
$scheme = $params->get('recommendations_scheme');
$border = $params->get('recommendations_border');


?>
<div class="<?php echo $params->get('moduleclass_sfx'); ?>">
	<fb:recommendations border_color="<?php echo $border; ?>" colorscheme="<?php echo $scheme; ?>" header="<?php echo $header; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" font="<?php echo $font; ?>"></fb:recommendations>
</div>