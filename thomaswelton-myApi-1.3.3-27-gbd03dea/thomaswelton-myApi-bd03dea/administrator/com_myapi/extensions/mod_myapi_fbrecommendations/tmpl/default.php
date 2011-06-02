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
 **   Joomla! 1.5 Module myApi_fbRecommendations                            **
 **   @Copyright Copyright (C) 2011 - Thomas Welton                         **
 **   @license GNU/GPL http://www.gnu.org/copyleft/gpl.html                 **	
 **                                                                         **	
 **   myApi_fbRecommendations is free software: you can redistribute it		**
 **   it under the terms of the GNU General Public License as published by  **
 **   and/or modify the Free Software Foundation, either version 3 of the   **	
 **   License, or (at your option) any later version.                       **
 **                                                                         **
 **   myApi_fbRecommendations is distributed in the hope that it will be    **
 **   useful, but WITHOUT ANY WARRANTY; without even the implied warranty   **
 **   of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the      **
 **   GNU General Public License for more details.                          **
 **                                                                         **
 **   You should have received a copy of the GNU General Public License	    **
 **   along with myApi_fbRecommendations.  									**
 **   If not, see <http://www.gnu.org/licenses/>.							**
 **                                                                         **			
 *************************************************************************/ ?>
 
<div class="<?php echo $classSfx; ?>">
	<fb:recommendations width="<?php echo $width; ?>" height="<?php echo $height; ?>" header="<?php echo $header; ?>" colorscheme="<?php echo $scheme; ?>" font="<?php echo $font; ?>" border_color="<?php echo $border; ?>" ref="<?php echo $ref; ?>"></fb:recommendations>
</div>