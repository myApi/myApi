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
JToolBarHelper::title('myApi Authentication', 'facebook.png');
JToolbarHelper::preferences('com_myapi');

$properties = array();
$properties['application_name'] = 'myApi';
$properties = json_encode($properties);


$root = JURI::root();
$root = str_replace('http://','',$root);
$root = str_replace('www.','',$root);

if(substr($root,-1,1) == '/'){
	$root = substr($root,0,-1);	
}

$connectURL = 'http://'.$root.'/';
$baseDomain = $root;

$adminSettings['connectURL'] = $connectURL;
$adminSettings['base_domain'] = $baseDomain;

$adminSettings = json_encode($adminSettings);

$js = <<<EOD
/* <![CDATA[ */
window.addEvent('domready',function(){
var data;
$('toolbar-save').addEvent('click',function(e){
	var newEvent = new Event(e); newEvent.stop();
	if($('paramsappId').value == ''){
		alert('Please enter your Facebook Application ID');	
		return false;
	}else{
	  FB.init({appId: $('paramsappId').value, status: true, cookie: true, xfbml: true,session: FB.getSession()});
		  FB.login(function(loginResponse){
			  if(loginResponse.session){
				  FB.api({method: 'application.getPublicInfo', application_id: $('paramsappId').value},function(response){
					  data = response;
					  if(response.api_key){
						  $('adminForm').submit();
						  
					  }else{
						  alert(data.error_msg);
					 }
				  });
			  }else{
				  //alert('Unable to login, please check your Connect URL and Base Domain');	
			  }
	  });	
	}

});
});
/* ]]> */
EOD;


		 JHTML::_('behavior.mootools');
		 $doc = & JFactory::getDocument();
		 $doc->addScriptDeclaration($js);

?>
  <form action="index.php" id="adminForm" method="post">
  <input type="hidden" name="option" value="com_myapi" />
   <input type="hidden" name="task" value="saveApi" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="id" value="<?php echo $this->plugin->id; ?>" />
     <input type="hidden" name="cid[]" value="<?php echo $this->plugin->id; ?>" />
    <input type="hidden" name="controller" value="myapi" />	
    <input type="hidden" name="component" value="com_myapi" />		
     <?php echo JHTML::_( 'form.token' ); ?>	
 	
    <h1>Settings</h1>
	
	<p>In order to connect your website to Facebook you'll need to set up a Facebook application, this can be done from your <a href="http://www.facebook.com/developers/apps.php" target="_new">Developer dashboard</a> from within Facebook. Either set up a new application, or if you have previously used myApi click "edit settings" for your application.</p>
	
	<p>If you are creating a new app you'll need to give it a name and agree to the facebook terms and conditions.</p>
	

	<p>Once you have confirmed your settings find your application's ID, and Secret key and enter them below and hit save</p>

<?php echo $this->params->render('params'); ?>


</form>