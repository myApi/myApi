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
 *****************************************************************************/
 
//don't allow other scripts to grab and execute our file
defined('_JEXEC') or die('Direct Access to this location is not allowed.');
if(!file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_myapi'.DS.'myapi.php')){ return; }

$doc =& JFactory::getDocument();
$doc->addScript('components'.DS.'com_myapi'.DS.'assets'.DS.'js'.DS.'myApi.js');
$redirect_logout = JURI::base();

$userRedirect = $params->get('login_userRedirect');
$button_size = $params->get('login_size');
if($userRedirect != '1'){
	//same page
	$u =& JFactory::getURI(); 
	$redirect_login = JRoute::_($u->toString(),false);
}
else{
	//redirect to different page
	$menuitem = $params->get('login_userRedirectTo');
	$redirect_login = JRoute::_(JFactory::getApplication()->getMenu()->getItem( $menuitem )->link . "&Itemid=".$menuitem,false);
		
}

$u =& JURI::getInstance( $redirect_login );
$root = JURI::root();
$root = (substr($root,0,7) == 'http://') ? substr($root,7) : $root;
$root = (substr($root,0,4) == 'www.') ? substr($root,4) : $root;
$root = (substr($root,-1,1) == '/') ? substr($root,0,-1) : $root;
$redirect_login = 'http://'.$root.$u->getPath();
$redirect_login = base64_encode($redirect_login);
$user = JFactory::getUser();
global $facebook;

require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_myapi'.DS.'models'.DS.'facebook.php';
$facebookmodel = new myapiModelfacebook;  //Bring the myAPI facebook model

$linked = false;
$user = JFactory::getUser();
if(!$user->guest){
	$db = JFactory::getDBO();
	$query = "SELECT * FROM ".$db->nameQuote('#__myapi_users')." WHERE userId =".$db->quote($user->id);
	$db->setQuery($query);
	$db->query();
	$num_rows = $db->getNumRows();
	if($num_rows == 0){ $linked = false; }
		else { $linked = true; }  
}

require_once JPATH_SITE.DS.'components'.DS.'com_myapi'.DS.'models'.DS.'myapi.php';
$myApiModel = new MyapiModelMyapi;  //Bring the myAPI facebook model

$avatar = $myApiModel->getAvatar();

?>



<div id="myApiLoginWrapper" class="<?php echo $params->get('moduleclass_sfx'); ?>">
<?php if($user->guest): ?>
<div style="text-align:center; margin-bottom:10px; padding:10px 0px;">
		<fb:login-button id="fbLoginButton" autologoutlink="false" v="2" size="<?php echo $button_size; ?>" onlogin="myapi.auth.checkAndLogin('<?php echo JUtility::getToken(); ?>','<?php echo $redirect_login; ?>');" perms="email,user_likes,user_photos,user_status,read_stream,publish_stream,offline_access">Connect with facebook</fb:login-button>
		  <div style="margin-top:15px;"></div>
		<?php if($params->get('login_facepile') == '1') : ?>
		<fb:facepile width="<?php echo $params->get('login_facepileWidth'); ?>" max-rows="<?php echo $params->get('login_facepileRows'); ?>"></fb:facepile>
		<?php endif; ?>
		
		
</div>
		
		<?php if($params->get('login_joomlaLogin') == '1') : ?>
			<form action="<?php echo JRoute::_( 'index.php', true, $params->get('usesecure')); ?>" method="post" name="login" id="form-login" >
				<?php echo $params->get('pretext'); ?>
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
				<?php echo $params->get('posttext'); ?>
			
				<input type="hidden" name="option" value="com_user" />
				<input type="hidden" name="task" value="login" />
				<input type="hidden" name="return" value="<?php echo $redirect_login; ?>" />
				<?php echo JHTML::_( 'form.token' ); ?>
			</form>
		<?php endif; ?>
		
		
<?php else: ?>
     <div style="text-align:center;">
      <?php if(!$linked): ?> 
     <?php
	 
	 $loginUrl = $facebook->getLoginUrl(array('next' => JURI::base().'index.php?option=com_myapi&task=newLink&return='.$redirect_login ));	
	 
	 ?>
     
	  <fb:login-button id="fbLinkButton" autologoutlink="false" v="2" size="small" onlogin="myapi.auth.newLink('<?php echo JUtility::getToken(); ?>','<?php echo $loginUrl; ?>');" perms="email,user_likes,user_photos,user_status,read_stream,publish_stream,offline_access"><?php echo $params->get('login_link_text'); ?></fb:login-button>
      <div style="margin-top:15px;"></div>
	  <fb:facepile width="198" max-rows="2"></fb:facepile>
		
	<?php else: ?>
	
	<?php if($avatar != ''): ?>
	<img src="images/comprofiler/<?php echo $avatar; ?> " style="margin: 0px 1px 3px 1px; border-width:0px;">
	<?php endif; ?>
	<br />
	<?php echo $user->name; ?>
	
	
	<?php endif; ?>
        
        <form action="index.php" onsubmit="myapi.auth.logout(); return false;" method="post">
        <input type="hidden" name="option" value="com_myapi" />
         <input type="hidden" name="task" value="logout" />
	
		<input type="submit" name="Submit" class="button" value="Log out">
	

</form>
	</div>
<?php endif; ?> 
</div>
