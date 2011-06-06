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
 *****************************************************************************/
error_reporting(0);
?>
<table align="center" width="600" style="margin-top:20px; margin-bottom:50px; border-top:1px solid #333; padding:5px; border-left:1px solid #333; border-bottom:1px solid #666; border-right:1px solid #666; font-size:13px; line-height:18px; font-family: 'Lucida Grande', Tahoma, Verdana, Arial, sans-serif; color: #333;">
	<tr>	
    	<td>
        	<table>
            	<tr>
                	<td width="180" valign="top">
                        <img src="https://graph.facebook.com/<?php echo $fbUser['id']; ?>/picture?type=large" width="180" alt="<?php echo $fbUser['name']; ?>" border="0" />
                    </td>
                    <td width="410" align="right" valign="top">
                        <h1 style="text-align:right; margin:0px 0px 5px 10px; font-size:16px; color: #333; font-size:24px; line-height:28px;"><?php echo JText::_('WELCOME'); ?></h1>
                        <em style="text-align:right; display:block; margin:0px 0px 10px 10px; font-size:14px; color:#3B5998;"><?php echo JURI::base(); ?></em>
                        <h2 style="text-align:right; margin:0px 0px 5px 10px; font-size:16px; color: #333; font-size:20px; line-height:24px;"><?php echo $fbUser['name']; ?></h2>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
    	<td>
        	<table width="600">
            	<tr>
                	<td width="290" align="left" valign="top">
                    	<h3 style="color: #333;"><?php echo JText::_('REG_THANKYOU'); ?></h3>
						<p><?php echo JText::_('REG_EMAIL_BODY'); ?></p>
                        <b><?php echo JText::_('Username') ?>:</b> <em><?php echo $username; ?></em><br />
                       	<b><?php echo JText::_('Password'); ?>:</b> <em><?php echo $password; ?></em>
                        <br />
                    </td>
                    <td width="290" align="right" valign="top">
                        <table  style="background-color:#E5E5E5; border-bottom:1px solid #A6A6A6; border-right:1px solid #A6A6A6;" width="290">
                            <tr>
                                <td width="50"><img src="https://graph.facebook.com/<?php echo $page['id']; ?>/picture" width="50" height="50" alt="<?php echo $page['name']; ?>" border="0" /></td>
                                <td>
                                    <h4 style="margin:0px "><?php echo JText::_('RECENT_FEED'); ?></h4>
                                    <a href="<?php echo $page['link']; ?>" style="color:#3B5998; font-size:14px; text-align:right;" title="<?php echo $page['name']; ?>"><?php echo $page['name']; ?></a>
                                </td>
                            </tr>
                            <?php if(is_array($feed)): ?>
								<tr><td colspan="2"><hr /></td></tr>
								<?php foreach($feed as $index => $post): if(array_key_exists('message',$post) || array_key_exists('link',$post)): ?>
                                <tr>
                                    <td colspan="2" style="padding:2px 5px;">
                                        <?php echo $post['message']; ?><br />
                                        <?php if(isset($post['link']) && $post['link'] != ''): ?>
                                            <a style="color:#3B5998;" href="<?php echo $post['link']; ?>" title="<?php echo $post['caption']; ?>"><?php echo $post['caption']; ?></a>
                                           <?php if(isset($post['description']) && $post['description'] != ''): ?>
                                                <?php echo ' - '.$post['description']; ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <br />
                                        <sub style="color:#808080; font-size:10px; text-align:right;"><?php echo date('D jS M h:i a', strtotime($post['created_time'])); ?></sub>
                                    </td>
                                </tr>
                                <tr>
                                	<td colspan="2">
                                		<?php echo ($index != (sizeof($feed) -1)) ? '<hr />' : '<br />'; ?>
                              		</td>
                              	</tr>
                                <?php endif; endforeach; ?>
                         	<?php endif; ?>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>