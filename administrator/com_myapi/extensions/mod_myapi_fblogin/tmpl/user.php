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
 **   Joomla! 1.5 Module myApi_fblogin                                      **
 **   @Copyright Copyright (C) 2011 - Thomas Welton                         **
 **   @license GNU/GPL http://www.gnu.org/copyleft/gpl.html                 **	
 **                                                                         **	
 **   myApi_fblogin is free software: you can redistribute it and/or modify **
 **   it under the terms of the GNU General Public License as published by  **
 **   the Free Software Foundation, either version 3 of the License, or	    **	
 **   (at your option) any later version.                                   **
 **                                                                         **
 **   myApi_fblogin is distributed in the hope that it will be useful,	    **
 **   but WITHOUT ANY WARRANTY; without even the implied warranty of	    **
 **   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         **
 **   GNU General Public License for more details.                          **
 **                                                                         **
 **   You should have received a copy of the GNU General Public License	    **
 **   along with myApi_fblogin.  If not, see <http://www.gnu.org/licenses/> **
 **                                                                         **			
 *************************************************************************/ ?>

<div id="myApiLoginWrapper" class="<?php echo $classSfx; ?>">
		<?php if(!$linked): ?> 
        	<fb:login-button id="fbLoginButton" show-faces="<?php echo $show_faces; ?>" width="<?php echo $width; ?>" max-rows="<?php echo $max_rows; ?>" onlogin="myapi.auth.newLink('<?php echo JUtility::getToken(); ?>','<?php echo $loginUrl; ?>');" perms="<?php echo $permissions; ?>"><?php echo $linkText; ?></fb:login-button>
        <?php else: ?>
        	<?php if($avatar != ''): ?>
        		<img src="images/comprofiler/<?php echo $avatar; ?> " style="margin: 0px 1px 3px 1px; border-width:0px;">
        	<?php endif; ?>
        	<br />
        	<?php echo $user->name; ?>
        <?php endif; ?>
        
        <form action="index.php" onsubmit="myapi.auth.logout(); return false;" method="post">
            <input type="hidden" name="option" value="com_myapi" />
            <input type="hidden" name="task" value="logout" />
            <input type="submit" name="Submit" class="button" value="Log out">
        </form>
</div>

