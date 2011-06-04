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
 *************************************************************************/ ?>

<?php if($pageLink != ""): ?>
	<div class="<?php echo $classSfx; ?>">
   		<fb:like-box href="<?php echo $pageLink; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" colorscheme="<?php echo $scheme; ?>" show_faces="<?php echo $faces; ?>" stream="<?php echo $stream; ?>" header="<?php echo $header; ?>" border_color="<?php echo $border; ?>"></fb:like-box>
        <div style="clear:both;"></div>
  	</div>
<?php elseif($user->usertype == 'Administrator' || $user->usertype == 'Super Administrator'): ?>
	<div class="<?php echo $classSfx; ?>">
    	<p>Unable to find a valid URL for the specified page ID.</p>
   	</div>
<?php endif; ?>

