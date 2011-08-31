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
		<fb:login-button id="fbLoginButton" onlogin="myapi.auth.checkAndLogin('<?php echo JUtility::getToken(); ?>','<?php echo $redirect_login; ?>');" scope="<?php echo $permissions; ?>"><?php echo $loginText; ?></fb:login-button>
		
		<?php if($show_faces == '1') : ?>
			<div class="myApiFacepile" style="overflow:hidden;"><fb:facepile width="<?php echo $width; ?>" max-rows="<?php echo $max_rows; ?>"></fb:facepile></div>
		<?php endif; ?>
	
		<?php if($joomla_login == '1') : ?>
			<form action="<?php echo JRoute::_( 'index.php', true); ?>" method="post" name="login" id="form-login" >
				<fieldset class="input">
                    <p id="form-login-username">
                        <label for="modlgn_username"><?php echo JText::_('Username') ?></label><br />
                        <input id="modlgn_username" type="text" name="username" class="inputbox" alt="username" size="18" />
                    </p>
                    <p id="form-login-password">
                        <label for="modlgn_passwd"><?php echo JText::_('Password') ?></label><br />
                        <input id="modlgn_passwd" type="password" name="passwd" class="inputbox" size="18" alt="password" />
                    </p>
                    <?php if(JPluginHelper::isEnabled('system', 'remember')) : ?>
                        <p id="form-login-remember">
                            <label for="modlgn_remember"><?php echo JText::_('Remember me') ?></label>
                            <input id="modlgn_remember" type="checkbox" name="remember" class="inputbox" value="yes" alt="Remember Me" />
                        </p>
                    <?php endif; ?>
                    <input type="submit" name="Submit" class="button" value="<?php echo JText::_('LOGIN') ?>" />
				</fieldset>
				<ul>
					<li>
						<a href="<?php echo JRoute::_( 'index.php?option=com_user&view=reset' ); ?>">
						<?php echo JText::_('FORGOT_YOUR_PASSWORD'); ?></a>
					</li>
					<li>
						<a href="<?php echo JRoute::_( 'index.php?option=com_user&view=remind' ); ?>">
						<?php echo JText::_('FORGOT_YOUR_USERNAME'); ?></a>
					</li>
					<?php
					$usersConfig = &JComponentHelper::getParams( 'com_users' );
					if ($usersConfig->get('allowUserRegistration')) : ?>
					<li>
						<a href="<?php echo JRoute::_( 'index.php?option=com_user&view=register' ); ?>">
							<?php echo JText::_('REGISTER'); ?></a>
					</li>
					<?php endif; ?>
				</ul>
				<input type="hidden" name="option" value="com_user" />
				<input type="hidden" name="task" value="login" />
				<input type="hidden" name="return" value="<?php echo $redirect_login; ?>" />
				<?php echo JHTML::_( 'form.token' ); ?>
			</form>
		<?php endif; ?>
        
   	<div style="display:none;">
    	<form name="myApiLoginForm" action="<?php echo JRoute::_( 'index.php?option=com_myapi&task=facebookLogin', true); ?>" method="post">
            <input type="hidden" name="return" value="<?php echo $redirect_login; ?>" />
        </form>
    </div>
</div>	