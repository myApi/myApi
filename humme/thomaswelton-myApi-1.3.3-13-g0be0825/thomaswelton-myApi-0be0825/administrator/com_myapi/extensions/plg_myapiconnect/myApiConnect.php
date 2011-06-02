<?php defined( '_JEXEC' ) or die( 'Restricted access' );
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
jimport( 'joomla.plugin.plugin');

class plgSystemmyApiConnect extends JPlugin
{
	function plgSystemmyApiConnect(&$subject, $config){
		parent::__construct($subject, $config);
	}
	
	function getFacebook(){
		$plugin =& JPluginHelper::getPlugin('system', 'myApiConnect');
		$params = new JParameter( $plugin->params );
		 
		require_once JPATH_SITE.DS.'plugins'.DS.'system'.DS.'myApiConnectFacebook.php';
		$facebook =  new myApiFacebook(array(
			'appId'  => $params->get('appId'),
			'secret' => $params->get('secret'),
			'cookie' => true, // enable optional cookie support
		));	
		return $facebook;
	}

	function onAfterDispatch(){
		JHTML::_('behavior.mootools');
		$doc = & JFactory::getDocument();
		$doc->addStylesheet('plugins'.DS.'system'.DS.'myApiConnect'.DS.'myApi.css');	
	}

	function onAfterRender(){
		global $mainframe, $fbAsyncInitJs;
		$document=& JFactory::getDocument();   
		
		if($document->getType() != 'html' || $mainframe->isAdmin()) 
			return;
			

		$plugin	=& JPluginHelper::getPlugin('system', 'myApiConnect');
		$params = new JParameter( $plugin->params );  
		
		$u =& JURI::getInstance( JURI::base() );
		$port 	= ($u->getPort() == '') ? '' : ":".$u->getPort();
		$xdPath	= $u->getScheme().'://'.$u->getHost().$port.$u->getPath().'plugins/system/facebookXD.html';
		
		$locale = ($params->get("locale") == '') ? 'en_US' : $params->get("locale");	
		
		$js 	= <<<EOD
/* <![CDATA[ */		
window.addEvent('domready',function(){
	(function() {
		var e = document.createElement('script'); e.async = true;
		e.src = document.location.protocol +
		  '//connect.facebook.net/{$locale}/all.js';
		document.getElementById('fb-root').appendChild(e);
	}());
});
window.fbAsyncInit = function() {
     FB.init({appId: "{$params->get('appId')}", status: true, cookie: true, xfbml: true, channelUrl: "{$xdPath}"});
	 {$fbAsyncInitJs};
};
/* ]]> */
EOD;
		unset($fbAsyncInitJs);
		
		$buffer = JResponse::getBody();
		require_once(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'myApiDom.php');
		$dom = new simple_html_dom();
		$dom->load($buffer);
		
		$htmlEl = $dom->find('html',0);
		$xmlns = 'xmlns:fb';
		$htmlEl->$xmlns = "http://www.facebook.com/2008/fbml";
		
		//This image points to myapi.co.uk but it is not a backlink and doesn't harm your SEO rankings in anyway. If you want to delete it you can but the rest of the code is vital.
		$host = JURI::getInstance(JURI::current());
		$pixelTag = ($host->getScheme() == 'http') ? '<img src="http://myapi.co.uk/index.php?option=com_pixeltag&appid='.$params->get('appId').'&domain='.JURI::base().'" style="border:0px;" alt="fbPixel"/>' : '';
		$FeatureLoader_javascript = '<div id="fb-root">'.$pixelTag.'</div><script type="text/javascript">document.getElementsByTagName("html")[0].style.display="block"; '.$js.'</script>';
		
		$bodyEl = $dom->find('body',0);
		$bodyEl->innertext .= $FeatureLoader_javascript;
		
		JResponse::setBody( $dom );		
		$dom->clear(); 
		unset($dom);	
	}
	
}
?>