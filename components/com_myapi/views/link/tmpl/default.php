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
?>

<div class="ubersearch search_profile">
    <div class="result clearfix">
        <?php if(!$registeredEmail): ?>
        <table class="myapi" cellspacing="5" cellpadding="5">
            <tr >
                <td width="150" class="loginHeaders headers"><?php echo JText::_('LOGIN'); ?></td>
                <td width="350" class="registerHeaders headers"><?php echo JText::_('REGISTER'); ?></td>
            </tr>
            <tr>
                <td><p><?php echo JText::_('REGISTRATION_LINK_HEADER'); ?></p></td>
                <td><p><?php echo JText::_('REGISTRATION_CREATE_HEADER'); ?></p></td>
            </tr>
            <tr>
                <td>
                	<form action="index.php?option=com_myapi&task=login" method="post" id="myapiLogin" class="myapiAjaxForm">
                        <table>
                            <tr>
                                <td><label for="username"><span><?php echo JText::_('USERNAME'); ?></span></label></td>
                                <td><input name="username" id="username" type="text" class="inputbox" alt="username" size="10" /></td>
                            </tr>
                            <tr>
                                <td><label for="passwd"><span><?php echo JText::_('PASSWORD'); ?></span></label></td>
                                <td><input type="password" id="passwd" name="passwd" class="inputbox" size="10" alt="password" /></td>
                            </tr>
                            <tr>
                                <td colspan="2"><button class="button" type="submit"><?php echo JText::_('LOGIN'); ?></button></td>
                            </tr>
                            <tr>
                                <td colspan="2"><a href="<?php echo $forgotPass; ?>"><?php echo JText::_('FORGOT_PASS'); ?></a><br  />
                                    <a href="<?php echo $forgotUser; ?>"><?php echo JText::_('FORGOT_USERNAME'); ?></a></td>
                            </tr>
                        </table>
                        <input type="hidden" name="option" value="com_myapi" />
                        <input type="hidden" name="myapiFbLink" value="1" />
                        <input type="hidden" name="task" value="login" />
                        <input type="hidden" name="return" value="<?php echo JRequest::getVar('return',JURI::base(),'get'); ?>" />
                        <?php echo $formToken; ?>
                    </form>
              	</td>
                <td>
                	<form action="index.php?option=com_myapi&task=newUser" method="post" id="myApiNewUserRegForm" class="myapiAjaxForm">
                        <table class="myapi">
                            <input type="hidden" name="option" value="com_myapi" />
                            <tr>
                                <td width="120" rowspan="2"><fb:profile-pic uid="loggedinuser" facebook-logo="false" linked="false" size="s"></fb:profile-pic></td>
                                <td><?php echo JText::_('NEW_ACCOUNT_CLICK'); ?> <?php echo JText::_('NEW_ACCOUNT_CLICK_REGISTER');  ?></td>
                            <tr>
                                <td>
                                    	<button class="button" type="submit"><?php echo JText::_('REGISTER'); ?></button>
                                  
                               	</td>
                            </tr>
                        </table>
                        <input type="hidden" name="task" value="newUser" />
                        <input type="hidden" name="id" value="0" />
                        <input type="hidden" name="gid" value="0" />
                        <input type="hidden" name="return" value="<?php echo JRequest::getVar('return',JURI::base(),'get'); ?>" />
                        <?php echo $formToken ?>
                    </form>
               	</td>
            </tr>
        </table>
        <?php else: ?>
        <table class="myapi" cellspacing="5" cellpadding="5" width="100%">
            <tr >
                <td colspan="3" class="headers"><?php echo JText::_('LOGIN'); ?></td>
            </tr>
            <tr>
                <td width="120"><fb:profile-pic uid="loggedinuser" facebook-logo="false" linked="false" size="s"></fb:profile-pic></td>
                <td width="300"><p><?php echo JText::_('EMAIL_REGISTERED'); ?></p>
                    <p><?php echo JText::_('EMAIL_REGISTERED_LOGIN'); ?></p></td>
                <td><form action="index.php?option=com_myapi&task=login" method="post" id="myapiLogin" class="myapiAjaxForm">
                        <table align="right">
                            <tr>
                                <td><label for="username"><span><?php echo JText::_('USERNAME'); ?></span></label></td>
                                <td><input name="username" id="username" type="text" class="inputbox" alt="username" size="10" /></td>
                            </tr>
                            <tr>
                                <td><label for="passwd"><span><?php echo JText::_('PASSWORD'); ?></span></label></td>
                                <td><input type="password" id="passwd" name="passwd" class="inputbox" size="10" alt="password" /></td>
                            </tr>
                            <tr>
                                <td colspan="2"><button class="button" type="submit"><?php echo JText::_('LOGIN'); ?></button></td>
                            </tr>
                            <tr>
                                <td colspan="2"><a href="<?php echo $forgotPass; ?>"><?php echo JText::_('FORGOT_PASS'); ?></a><br  />
                                    <a href="<?php echo $forgotUser; ?>"><?php echo JText::_('FORGOT_USERNAME'); ?></a></td>
                            </tr>
                        </table>
                        <input type="hidden" name="option" value="com_myapi" />
                        <input type="hidden" name="myapiFbLink" value="1" />
                        <input type="hidden" name="task" value="login" />
                        <input type="hidden" name="return" value="<?php echo JRequest::getVar('return',JURI::base(),'get'); ?>" />
                        <?php echo $formToken; ?>
                    </form></td>
            </tr>
        </table>
        <?php endif; ?>
        <div class="clear" style="clear:both;"></div>
    </div>
</div>
