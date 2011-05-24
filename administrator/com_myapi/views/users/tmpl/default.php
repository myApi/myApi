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
 *****************************************************************************/
defined('_JEXEC') or die('Restricted access');
$doc =& JFactory::getDocument();
$doc->addStyleSheet( JURI::base().'/components/com_myapi/assets/styles.css' );
JToolBarHelper::title('Facebook Connect Users', 'facebook.png');
JToolBarHelper::deleteList('This will unlink the user(s) Joomla account and Facebook account, it will not delete the user from your site.','unlinkUser', 'Unlink Account(s)');
?>

  <form action="index.php" method="post" name="adminForm">
    <input type="hidden" name="option" value="com_myapi" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="controller" value="myapi" />
    <input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />

	<?php echo JHTML::_( 'form.token' ); ?>	
	<p>Below is a list of joomla users that have linked their facebook profiles to the site, as an admin you are not able to add a new link between a facebook profile and joomla user, this can only be done by the facebook users on the front end.  However you can unlink facebook accounts from joomla account.  Just check the boxes next to the links you want to destroy and click unlink accounts.  This does not delete the joomla user account or facebook profile.</p>
	<table id="userslist" class="adminlist">
 		<tr>
          <th class="top_row" width="20"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->users); ?>);" /></th>
          <th class="top_row"><?php echo JHTML::_('grid.sort','Name','#__users.name', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
          <th class="top_row"><?php echo JHTML::_('grid.sort','Username','#__users.username', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
          <th class="top_row"><?php echo JHTML::_('grid.sort','User Type','#__users.usertype', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
          <th class="top_row"><?php echo JHTML::_('grid.sort','Facebook UID','#__myapi_users.uid', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
       </tr>
        <?php foreach($this->users as $index => $obj): ?>
            <tr>
                <td><?php echo JHTML::_( 'grid.id', $index, $obj->id ); ?></td>
                <td><?php echo $obj->name; ?></td>
                <td><?php echo $obj->username; ?></td>
                <td><?php echo $obj->usertype; ?></td>
                <td><a href="http://www.facebook.com/profile.php?id=<?php echo $obj->uid; ?>" target="_blank">Facebook Profile</a></td>
            </tr>
        <?php endforeach; ?>
        <tfoot>
      		<tr class="footer">
            	<td colspan="5"><?php echo $this->pagination->getResultsCounter(); ?></td>
          	</tr>
          	<tr class="footer">
            	<td colspan="5"><?php echo $this->pagination->getListFooter(); ?></td>
           	</tr>
      </tfoot>
   </table>
</form>
