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
 **   Joomla! 1.5 Plugin myApiTabs                                     		**
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
class plgSystemmyApiTabs extends JPlugin{
	
	static $ogptags = array();

	public function __construct(& $subject, $config) {
 		parent::__construct($subject, $config);
 		$this->loadLanguage();
  	}
	
	function onAfterInitialise(){
		global $mainframe;
		if(!class_exists('plgSystemmyApiConnect') || $mainframe->isAdmin()) 
			return;
		
		$facebook = plgSystemmyApiConnect::getFacebook();
		if($facebook){
			$signedRequest = $facebook->getSignedRequest();
			$session =& JFactory::getSession();
			if(is_array($signedRequest)){
				JRequest::setVar('tmpl','component');
				if(isset($signedRequest['user_id'])){
					$user = JFactory::getUser();
					if($user->guest){
						global $mainframe;
						$options['uid'] = $signedRequest['user_id'];
						$mainframe->login($signedRequest['user_id'],$options);
					}
				}
			}
			$method = $_SERVER['REQUEST_METHOD'];  
			if($session->get( 'fbtmpl' ) == '1'){
				JRequest::setVar('tmpl','component');
			}
			
			if(JRequest::getVar('tmpl') == 'component')
				$session->set( 'fbtmpl','1');
		}
	}
	
	function onAfterDispatch(){
		if(JRequest::getVar('tmpl') == 'component' && class_exists('plgSystemmyApiConnect')){
			global $fbAsyncInitJs;
			$fbAsyncInitJs .= ' FB.Canvas.setAutoResize(); ';
			$document = JFactory::getDocument();
			//This was added to stop Kunea doing an automatic JS redirect, is has code that removes the redirect if the user clicks the page
			$document->addScriptDeclaration('window.addEvent("domready",function(){ $(document.body).fireEvent("click"); try{ jQuery("body").trigger("click"); }catch(e){} });');	
			$document->addStyleDeclaration('html{margin:0px; padding:0px;}body{margin:0px; padding:0px; width:520px; overflow-x:hidden;}');
		}
	}
	
	function onAfterRender(){
		global $mainframe;
		$document=& JFactory::getDocument();   
		
		$session =& JFactory::getSession();
		$session->set( 'fbtmpl','0');
		
		if( !class_exists('plgSystemmyApiConnect') || $document->getType() != 'html' || $mainframe->isAdmin() || JRequest::getVar('tmpl') != 'component') return;
	
		$buffer = JResponse::getBody();
		require_once(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'myApiDom.php');
		$dom = new simple_html_dom();
		$dom->load($buffer);
		
		$links = $dom->find('a');
		foreach($links as $index => $object){
			if(JURI::isInternal($object->href) && $object->href != '#'){
				$u =& JURI::getInstance($object->href);
				$port 	= ($u->getPort() == '') ? '' : ":".$u->getPort();
				$scheme = ($u->getScheme() == '') ? '' : $u->getScheme().'://';
				$fragment = ($u->getFragment() == '') ? '' : '#'.$u->getFragment();
				$href	= $scheme.$u->getHost().$port.$u->getPath().'?'.$u->getQuery().'&tmpl=component'.$fragment;
				$object->href = $href;
			}else{
				$object->taget = "_blank";
			}
		}
		
		$forms = $dom->find('form');
		foreach($forms as $index => $object){
			$object->action .= '?&tmpl=component';
		}
		
		JResponse::setBody( $dom );		
		$dom->clear(); 
		unset($dom);	
		
	}
	
}
?>