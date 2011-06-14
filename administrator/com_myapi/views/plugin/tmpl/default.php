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

<form action="index.php" method="post" name="adminForm" id="adminForm">
    <input type="hidden" name="option" value="com_myapi" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="id" value="<?php echo $this->pluginID ?>" />
    <input type="hidden" name="cid[]" value="<?php echo $this->pluginID; ?>" />
    <?php echo JHTML::_( 'form.token' ); ?>
    <input type="hidden" name="controller" value="myapi" />	
	<table width="100%">
		<tr>
			<td colspan="2"><p><?php echo $this->description; ?></p></td>
		</tr>
    	<tr>
			<td align="center" valign="top" style="padding-right:20px;" width="50%">
				<?php if($this->vnum == '1.5'): echo $this->params->render( 'params'); ?>
                <?php else: ?>
                	<fieldset class="panelform">
						<?php $hidden_fields = ''; ?>
                        <ul class="adminformlist">
                            <?php foreach ($this->fields as $field) : ?>
                            <?php if (!$field->hidden) : ?>
                            <li>
                                <?php echo $field->label; ?>
                                <?php echo $field->input; ?>
                            </li>
                            <?php else : $hidden_fields.= $field->input; ?>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                        <?php echo $hidden_fields; ?>
                    </fieldset>
                <?php endif; ?>
           	</td>
            <td width="50%"><?php echo @$this->aside; ?></td>
    	</tr>
    </table>
</form>


