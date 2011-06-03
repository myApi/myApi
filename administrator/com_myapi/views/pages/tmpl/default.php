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
 *************************************************************************/ ?>

<form action="index.php" method="post" name="adminForm">
	<input type="hidden" name="option" value="com_myapi" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="controller" value="myapi" />
	<?php echo JHTML::_( 'form.token' ); ?>
    	
    <h2><?php echo JText::_('PAGES_H2') ?></h2>
    <p><?php echo JText::_('PAGES_DESC') ?></p>
      
	<table class="adminlist">
		<tr>
			<th class="top_row" width="10"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->pages ); ?>);" /></th>
            <th class="top_row" width="50"><?php echo JText::_('AVATAR'); ?></th>
            <th class="top_row"><?php echo JText::_('NAME'); ?></th>
            <th class="top_row"><?php echo JText::_('TOKEN'); ?></th>
            <th class="top_row"><?php echo JText::_('CATEGORY'); ?></th>
		</tr>
        <?php foreach ($this->pages as $index => $result): ?>
        <tr>
            <td><?php echo JHTML::_( 'grid.id', $index, $result['pageId']); ?></td>
            <td><?php echo JHTML::image('https://graph.facebook.com/'.$result['pageId'].'/picture',$result['name']); ?></td>
            <td><a href="<?php echo $result['link']; ?>" title="Facebook Page - <?php echo $result['name']; ?>" target="_blank"><?php echo $result['name']; ?></a></td>
            <td><?php echo $result['access_token']; ?></td>
            <td><?php echo $result['category']; ?></td>
        </tr>
		<?php endforeach; ?>
    </table>
</form>

