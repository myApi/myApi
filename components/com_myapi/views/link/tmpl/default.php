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
global $mainframe;
JHTML::_('behavior.formvalidation');
$com_params = &JComponentHelper::getParams( 'com_myapi' );

$button_size = $com_params->get('size');
$button_text = $com_params->get('button_text');

//redirect to different page
	$menuitem = $com_params->get('userRedirectTo');
	if($menuitem == '') { 
		$menu =& JSite::getMenu();
		$menuitem = $menu->getDefault()->id;
	}
	$redirect = JRoute::_(JFactory::getApplication()->getMenu()->getItem( $menuitem )->link . "&Itemid=$menuitem",false);
		

$redirect = base64_encode($redirect);
?>

<script>

window.addEvent('domready',function(){

  $$('.myapiAjaxForm').each(function(el,index){
	  $$('.myapiAjaxForm')[index].addEvent('submit', function(e) {
		/**
		 * Prevent the submit event
		 */
		 $ES('.button',el).each(function(el,index){
			el.disabled = true;
			el.innerHTML = 'Loading...';	 
		 });
		 
		new Event(e).stop();
	 
		/**
		 * send takes care of encoding and returns the Ajax instance.
		 * onComplete removes the spinner from the log.
		 */
		this.send({
			onComplete: function(response) {
				var json = Json.evaluate(response);
				var message = json.message;
				var redirect = json.redirect;
				parent.SqueezeBox.close();
				parent.window.location = '<?php echo "index.php?option=com_myapi&task=frameTask"; ?>&redirect='+redirect+'&message='+message;
			
			}
		});
	});
 });
});
</script>
<style type="text/css">

html,body{
	width:600px;
	height:530px;
	margin:0px;
	padding:0px;
	color:#333333;
	font-family:Verdana, Geneva, sans-serif;
	overflow:hidden;
}
body{
	background:url(/components/com_myapi/assets/images/bg.gif) repeat-x bottom #f7f7f7;
	position:absolute;
}
#poweredBy{
	background:url(/components/com_myapi/assets/images/men.gif) no-repeat right;
	float:left;
	min-height:130px;
	width:560px;
	margin:20px;
}
input{
	float:right;
	width:100px;
}
a{
	float:right;
	color:#333333;
}
label{
	float:left;
	clear:both;
	padding:3px;
	width:120px;
}
label span{
	vertical-align:middle;
	padding:3px;
	width:120px;
}
button{
	float:left;
	clear:both;
}
p{
	padding-bottom:10px;
	font-size:12px;
	color:#333333;	
}
form{
color: #333;
float: left;
font-family: Helvetica, Arial, sans-serif;
font-size: 12px;
height: 252px;
line-height: 15px;
margin-bottom: 0px;
margin-left: 10px;
margin-right: 10px;
margin-top: 0px;
padding:0px;
width: 280px;
}

h1{
	background-image: none;
	color: #666;
	display: block;
	font-family: Helvetica, Arial, sans-serif;
	font-size: 16px;
	font-weight: bold;
	height: 15px;
	line-height: 15px;
	margin-bottom: 10px;
	margin-left: 0px;
	margin-right: 0px;
	margin-top: 10px;
	padding: 0px;
	text-align: left;
	vertical-align: bottom;
	width: 600px;
}
h2{
	font-size:16px;
	font:Verdana, Geneva, sans-serif;
}
.link{
	text-decoration:underline;
}
fieldset{
	
border-bottom-color: #CCC;
border-bottom-style: solid;
border-bottom-width: 1px;
border-left-color: #CCC;
border-left-style: solid;
border-left-width: 1px;
border-right-color: #CCC;
border-right-style: solid;
border-right-width: 1px;
border-top-color: #CCC;
border-top-style: solid;
border-top-width: 1px;
color: #333;
display: block;
font-family: Helvetica, Arial, sans-serif;
font-size: 12px;
height: 205px;
line-height: 15px;
margin-bottom: 0px;
margin-left: 0px;
margin-right: 0px;
margin-top: 15px;
overflow-x: visible;
overflow-y: visible;
padding-bottom: 15px;
padding-left: 15px;
padding-right: 15px;
padding-top: 15px;
width: 248px;
	
}
legend{
	
background-color: transparent;
background-image: none;
background-origin: padding-box;
border-bottom-style: none;
border-left-style: none;
border-right-style: none;
border-top-style: none;
color: #333;
display: block;
font-family: Helvetica, Arial, sans-serif;
font-size: 18px;
font-weight: bold;
height: 15px;
line-height: 15px;
margin-bottom: 0px;
margin-left: 0px;
margin-right: 179px;
margin-top: 0px;
overflow-x: visible;
overflow-y: visible;
padding-bottom: 0px;
padding-left: 10px;
padding-right: 10px;
padding-top: 0px;
text-align: left;
width: 49px;
}
#h2{
	
color: #333;
display: block;
font-family: Arial, Helvetica, sans-serif;
font-size: 16px;
font-weight: normal;
height: 15px;
line-height: 15px;
margin-bottom: 13px;
margin-left: 0px;
margin-right: -20px;
margin-top: 13px;
overflow-x: visible;
overflow-y: visible;
padding-bottom: 10px;
padding-left: 10px;
padding-right: 10px;
padding-top: 10px;
text-align: left;
vertical-align: bottom;
width: 560px;
}
#poweredBy p{
	width:245px;
	padding:10px;
}
#poweredBy a{
	text-decoration:none;
	color:#333333;
}
.myapiAjaxForm{
	width:280px;
	margin:0px 10px 0px 10px;
	float:left;
}

</style>

<div id="formsWrapper">
<h1>Hi <?php echo JRequest::getVar('name','','get'); ?>, login or create a new account below</h1>
<form action="index.php?option=com_myapi&task=login" method="post" id="myapiLogin" class="myapiAjaxForm">
<fieldset class="adminform">

	<legend><?php echo JText::_( 'Login' ); ?></legend>
    <p>If you already have an account with this website login with your existing user name and password to enable Facebook Connect</p>
	
		<label for="username"><span><?php echo JText::_('Username') ?>: </span></label>
		<input name="username" id="username" type="text" class="inputbox" alt="username" size="10" />
	
		<label for="passwd"><span><?php echo JText::_('Password') ?>: </span></label>
		<input type="password" id="passwd" name="passwd" class="inputbox" size="10" alt="password" />

	<?php if(JPluginHelper::isEnabled('system', 'remember')) : ?>
	
		<label for="remember"><span><?php echo JText::_('Remember me') ?>: </span></label>
		<input type="checkbox" id="remember" name="remember" class="inputbox" size="10" value="yes" alt="Remember Me" />
	
	<?php endif; ?>
	
	<button class="button" type="submit"><?php echo JText::_('LOGIN') ?></button>

		<a href="<?php echo JRoute::_( 'index.php?option=com_user&view=reset' ); ?>">Forgot your password?</a>

		<a href="<?php echo JRoute::_( 'index.php?option=com_user&view=remind' ); ?>">Forgot you username?</a>


	<input type="hidden" name="option" value="com_myapi" />
	<input type="hidden" name="myapiFbLink" value="1" />
	<input type="hidden" name="task" value="login" />
	<input type="hidden" name="return" value="<?php echo $redirect; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
	</fieldset>
</form>

<form action="index.php?option=com_myapi&task=newUser" method="post" class="myapiAjaxForm">
<fieldset class="adminform">

	<legend><?php echo JText::_( 'Register' ); ?></legend>
    <p>Alternativley to create a new account using details from your Facebook profile enter your desired user name and password below</p>
	<input type="hidden" name="option" value="com_myapi" />
  	<input type="hidden" name="name" id="name" value="<?php echo JRequest::getVar('name','','get'); ?>"/>
  
    <label for="username"><span><?php echo JText::_('Username') ?>: </span></label>
	<input type="text" id="username" name="username" size="10" />

    
		<label for="passwd"><span><?php echo JText::_('Password') ?>: </span></label>
        <input type="password" id="password" name="password" size="10" />
      
        <label for="passwd"><span><?php echo JText::_('Confirm Password') ?>: </span></label>
  		<input type="password"id="password2" name="password2" size="10" />
	
    <input type="hidden" id="pic" name="pic" value="<?php echo JRequest::getVar('pic_square','','get'); ?>" />
    <input type="hidden" id="uid" name="uid" value="<?php echo JRequest::getVar('uid','','get'); ?>" />
	<input type="hidden" id="email" name="email" value="<?php echo JRequest::getVar('email','','get'); ?>" />
	<button class="button validate" type="submit"><?php echo JText::_('Register'); ?></button>
	<input type="hidden" name="task" value="newUser" />
	<input type="hidden" name="id" value="0" />
	<input type="hidden" name="gid" value="0" />
	<input type="hidden" name="return" value="<?php echo $redirect; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
    </fieldset>
</form>

<div id="poweredBy">

<h2>Powered by myApi</h2>
<p>Registration and login on this web site has been made faster and easier by <a href="http://www.myapi.co.uk/"><span class="link">myApi</span>, the Facebook Connect Joomla bridge</a></p>
<p>There is no need to worry, this website will never be able gain access to your account, or personal data you do not explicitly give it permission to use</p>

</div>

</div>