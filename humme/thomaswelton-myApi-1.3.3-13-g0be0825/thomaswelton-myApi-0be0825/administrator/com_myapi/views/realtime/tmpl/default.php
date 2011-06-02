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
    	
    <h2><?php echo JText::_('SUBSCRIPTIONS_H2') ?></h2>
    <p><?php echo JText::_('SUBSCRIPTIONS_DESC') ?></p>
    
    <?php if(sizeof($this->subscriptions['data']) == 0): ?>
    	<h3><?php echo JText::_('NOTICE') ?></h3>
        <p><?php echo JText::_('SUBSCRIPTIONS_NONE') ?></p>	
    <?php endif; ?>
    
	<table class="adminlist">
		<tr>
			<th class="top_row" width="20"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->subscriptions['data']); ?>);" /></th>
            <th class="top_row"><?php echo JText::_('OBJECT'); ?></th>
            <th class="top_row"><?php echo JText::_('CALLBACK'); ?></th>
            <th class="top_row"><?php echo JText::_('FIELDS'); ?></th>
            <th class="top_row"><?php echo JText::_('ACTIVE'); ?></th>
		</tr>
        <?php foreach ($this->subscriptions['data'] as $index => $result): ?>
        <tr>
            <td><?php echo JHTML::_( 'grid.id', $index, $result['object']); ?></td>
            <td><?php echo $result['object']; ?></td>
            <td><?php echo $result['callback_url']; ?></td>
            <td><?php echo implode(',',$result['fields']); ?></td>
            <td><?php echo $result['active']; ?></td>
        </tr>
		<?php endforeach; ?>
    </table>
</form>
