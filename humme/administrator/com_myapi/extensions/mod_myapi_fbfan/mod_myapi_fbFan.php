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
$plugin =& JPluginHelper::getPlugin('system', 'myApiConnect');
$com_params = new JParameter( $plugin->params );

$user = JFactory::getUser();

$profile_id = $params->get('fan_profile_id');
	if($profile_id == '') $profile_id = $com_params->get('appId');

$width = $params->get('fan_width');
$height = $params->get('fan_height');
$connections = $params->get('fan_connections');
$logobar = $params->get('fan_logobar');
$stream = $params->get('fan_stream');
?>

<?php if($profile_id != ""): ?>

  <div class="<?php echo $params->get('moduleclass_sfx'); ?>">
    	<fb:like-box profile_id="<?php echo $profile_id; ?>" stream="<?php echo $stream; ?>" connections="<?php echo $connections; ?>" height="<?php echo $height; ?>" logobar="<?php echo $logobar; ?>" width="<?php echo $width; ?>"></fb:like-box>
        <div style="clear:both;"></div>
  </div>
  
<?php elseif($user->usertype == 'Administrator' || $user->usertype == 'Super Administrator'): ?>
	<div class="<?php echo $params->get('moduleclass_sfx'); ?>">
    	<p>No profile ID for fan box</p>
   	</div>
<?php endif; ?>

