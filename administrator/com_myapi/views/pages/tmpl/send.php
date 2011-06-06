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

<form action="index.php?option=com_myapi&view=pages&layout=send" method="post" name="adminForm">
	<input type="hidden" name="option" value="com_myapi" />
    <input type="hidden" name="view" value="pages" />
    <input type="hidden" name="layout" value="send" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="controller" value="myapi" />
	<?php echo JHTML::_( 'form.token' ); ?>
    
    <div class="col width-60">
    	<fieldset class="adminform">
			<legend><?php echo JText::_('MESSAGE') ?></legend>
            <table class="admintable">
				<tr>
					<td valign="top" class="key">
                    	<label for="mm_message">
                    		<?php echo JText::_( 'MESSAGE' ); ?>
                        </label>
                    </td>
                   <td id="mm_pane" >
    					<textarea rows="5" cols="150"  id="mm_message" class="inputbox" name="message"><?php echo JRequest::getVar('message','','post'); ?></textarea>
                  	</td>
               </tr>
            </table>
       	</fieldset>
        
          <fieldset class="adminform">
			<legend><?php echo JText::_('PAGES_SEND_LINK') ?></legend>
    		<table class="admintable">
				<tr>
                	<td valign="top" colspan="2">
                    	<?php echo JText::_('PAGES_SEND_LINK_DESC') ?>
                   	</td>
              	</tr>
                <tr>
                	<td valign="top" class="key">
            			<label for="menuItem"><?php echo JText::_('MENU_ITEM'); ?></label>
                   	</td>
                	<td>
            			<?php echo $this->menulist; ?>
            		</td>
              	</tr>
                <tr>
                	<td valign="top" class="key">
            			<label for="link_name"><?php echo JText::_('LINK_NAME'); ?></label>
            		</td>
                	<td>
            			<input class="inputbox" type="text" name="link_name" value="<?php echo JRequest::getVar('link_name','','post'); ?>" size="50" />
                 	</td>
              	</tr>
                <tr>
                	<td valign="top" class="key">
            			<label for="link_caption"><?php echo JText::_('LINK_CAPTION'); ?></label>
            		</td>
                	<td>
            			<input class="inputbox" type="text" name="link_caption" value="<?php echo JRequest::getVar('link_caption','','post'); ?>" size="50" />
                 	</td>
              	</tr>
                <tr>
                	<td valign="top" class="key">
                		<label for="link_description"><?php echo JText::_('LINK_DESC'); ?></label>
                  	</td>
                    <td>
                    	<textarea rows="5" cols="150"  id="mm_message" class="inputbox" name="link_description" ><?php echo JRequest::getVar('link_description','','post'); ?></textarea>
                  	</td>
              	</tr>
            </table>    
        </fieldset>
    </div>
     
	<div class="col width-40">	
    	<fieldset class="adminform">
			<legend><?php echo JText::_('PAGES_SEND') ?></legend>
    		<p><?php echo JText::_('PAGES_SEND_DESC') ?></p>
            <?php foreach($this->pages as $array): ?>
            	<a href="<?php echo $array['link']; ?>" title="<?php echo $array['name']; ?>" target="_blank" style="float:left; margin-right:8px; margin-bottom:8px;">
                	<img src="https://graph.facebook.com/<?php echo $array['pageId']; ?>/picture" width="50" style="border:1px solid #ccc;"  height="50" />
               	</a>
                <input type="hidden" name="cid[]" value="<?php echo $array['pageId']; ?>"  />
            <?php endforeach; ?>
       </fieldset>
       <fieldset class="adminform">
			<legend><?php echo JText::_('PAGES_SEND_IMAGE') ?></legend>
    		<table class="admintable">
				<tr>
                	<td valign="top" colspan="2">
                    	<?php echo JText::_('PAGES_SEND_IMAGE_DESC') ?>
                   	</td>
              	</tr>
                <tr>
                	<td valign="top" class="key">
            			<label for="fileList"><?php echo JText::_('PAGES_SEND_IMAGE_LABEL'); ?></label>
                   	</td>
                	<td>
            			<?php echo $this->filelist; ?>
            		</td>
              	</tr>
         	</table>
    	</fieldset>
    </div>
    
</form>

