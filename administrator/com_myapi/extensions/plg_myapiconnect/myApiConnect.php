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
 **   Joomla! 1.5 Plugin myApiConnect                                       **
 **   @Copyright Copyright (C) 2011 - Thomas Welton                         **
 **   @license GNU/GPL http://www.gnu.org/copyleft/gpl.html                 **	
 **                                                                         **	
 **   myApiConnect is free software: you can redistribute it and/or modify  **
 **   it under the terms of the GNU General Public License as published by  **
 **   the Free Software Foundation, either version 3 of the License, or	    **	
 **   (at your option) any later version.                                   **
 **                                                                         **
 **   myApiConnect is distributed in the hope that it will be useful,	    **
 **   but WITHOUT ANY WARRANTY; without even the implied warranty of	    **
 **   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         **
 **   GNU General Public License for more details.                          **
 **                                                                         **
 **   You should have received a copy of the GNU General Public License	    **
 **   along with myApiConnect.  If not, see <http://www.gnu.org/licenses/>  **
 **                                                                         **			
 *****************************************************************************/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.plugin.plugin');


class plgSystemmyApiConnect extends JPlugin
{
	var $_facebook = null;
	
	function plgSystemmyApiConnect(&$subject, $config){
		parent::__construct($subject, $config);
	}
	
	function getFacebook(){
		if(empty($this->_facebook)){
			$plugin =& JPluginHelper::getPlugin('system', 'myApiConnect');
			$com_params = new JParameter( $plugin->params );
			 
			require_once JPATH_SITE.DS.'plugins'.DS.'system'.DS.'myApiConnectFacebook.php';
			$this->_facebook = new myApiFacebook(array(
				'appId'  => $com_params->get('appId'),
				'secret' => $com_params->get('secret'),
				'cookie' => true, // enable optional cookie support
			 ));
		}
		return $this->_facebook;
	}
	
	function onAfterRender(){
		//For the async facebook injection
		global $mainframe;
		global $fbAsyncInitJs;
		JHTML::_('behavior.mootools');
		
		$plugin 	=& JPluginHelper::getPlugin('system', 'myApiConnect');
		$com_params = new JParameter( $plugin->params );  
		$xdPath		= JURI::base().'plugins/system/facebookXD.html';
			
		$js = <<<EOD
  
  window.addEvent('domready',function(){
  (function() {
    var e = document.createElement('script'); e.async = true;
    e.src = document.location.protocol +
      '//connect.facebook.net/{$com_params->get("locale")}/all.js';
    document.getElementById('fb-root').appendChild(e);
  }());
  });
	
/* ]]> */
EOD;
		if(!$mainframe->isAdmin()) {
		$js .= <<<EOD
/* <![CDATA[ */
window.fbAsyncInit = function() {
     FB.init({appId: "{$com_params->get('appId')}", status: true, cookie: true, xfbml: true, channelUrl: "{$xdPath}"});
	 {$fbAsyncInitJs};
};
/* ]]> */
EOD;
		}
		unset($fbAsyncInitJs);
		$doc = & JFactory::getDocument();
		$buffer = JResponse::getBody();
		
		$fbml	= '<html xmlns:fb="http://www.facebook.com/2008/fbml" ';
		  
		$FeatureLoader_javascript = '<div id="fb-root"></div><script type="text/javascript">document.getElementsByTagName("html")[0].style.display="block"; '.$js.'</script>';
		$buffer = str_replace ("</body>", $FeatureLoader_javascript."</body>", $buffer); 
		$html	= str_replace( '<html' , $fbml , $buffer );
		JResponse::setBody( $html );
	}
	
}
?>