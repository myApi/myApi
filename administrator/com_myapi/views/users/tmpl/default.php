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

<form action="<?php echo JRoute::_('index.php?option=com_myapi&view=users'); ?>" method="post" name="adminForm">
    <input type="hidden" name="option" value="com_myapi" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="controller" value="myapi" />
    <input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />

	<?php echo JHTML::_( 'form.token' ); ?>	
	<p><?php echo JText::_('USERS_DESC'); ?></p>
     
    <table>
        <tr>
            <td width="100%">
                <?php echo JText::_( 'Filter' ); ?>:
                <input type="text" name="search" id="search" value="<?php echo htmlspecialchars($this->lists['search']);?>" class="text_area" onchange="document.adminForm.submit();" />
                <button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
                <button onclick="document.getElementById('search').value='';this.form.getElementById('filter_type').value=0;this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
            </td>
            <td nowrap="nowrap">
                <?php echo $this->lists['type'];?>
            </td>
        </tr>
    </table>
    
	<table id="userslist" class="adminlist">
 		<tr>
          <th class="top_row" width="10"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->users); ?>);" /></th>
          <th class="top_row" width="50"><?php echo JText::_('AVATAR'); ?></th>
          <th class="top_row"><?php echo JHTML::_('grid.sort',JText::_('NAME'),'#__users.name', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
          <th class="top_row"><?php echo JHTML::_('grid.sort',JText::_('USERNAME'),'#__users.username', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
          <th class="top_row"><?php echo JHTML::_('grid.sort',JText::_('USER_TYPE'),'#__users.usertype', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
          <th class="top_row"><?php echo JHTML::_('grid.sort',JText::_('FACEBOOK_UID'),'#__myapi_users.uid', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
          <th class="top_row"><?php echo JHTML::_('grid.sort',JText::_('TOKEN'),'#__myapi_users.access_token', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
          <th class="top_row">Add as Friend</th>
       </tr>
        <?php foreach($this->users as $index => $obj): ?>
            <tr>
                <td><?php echo JHTML::_( 'grid.id', $index, $obj->id ); ?></td>
                <td><?php echo JHTML::image('https://graph.facebook.com/'.$obj->uid.'/picture',$obj->name); ?></td>
                <td><a href="http://www.facebook.com/profile.php?id=<?php echo $obj->uid; ?>" title="<?php echo JText::_('FACEBOOK_PROFILE'); ?> - <?php echo $obj->name; ?>" target="_blank"><?php echo $obj->name; ?></a></td>
                <td><?php echo $obj->username; ?></td>
                <td><?php echo $obj->usertype; ?></td>
                <td><?php echo $obj->uid; ?></td>
                <td><a href="https://developers.facebook.com/tools/access_token/lint?access_token=<?php echo $obj->access_token; ?>" target="_blank" title="<?php echo JText::_('LINT'); ?>"><?php echo $obj->access_token; ?></a></td>
                <td><a class="myApiAddFriend" role="button" id="profile_connect" rel="dialog-post" href="#" onclick="myapi.addFriend(<?php echo $obj->uid; ?>);"><i></i><span><?php echo JText::_('ADD_FRIEND'); ?></span></a></td>
            </tr>
        <?php endforeach; ?>
        <tfoot>
      		<tr class="footer">
            	<td colspan="8"><?php echo $this->pagination->getResultsCounter(); ?></td>
          	</tr>
          	<tr class="footer">
            	<td colspan="8"><?php echo $this->pagination->getListFooter(); ?></td>
           	</tr>
      </tfoot>
   </table>
</form>
