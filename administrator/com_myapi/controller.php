<?php
/**
 * Joomla! 1.5 component myApi
 *
 * @version $Id: controller.php 2010-05-01 08:43:14 svn $
 * @author Thomas Welton
 * @package Joomla
 * @subpackage myApi
 * @license GNU/GPL
 *
 * myApi - Combining the power of the Facebook platform with the ease and simplicity of Joomla.
 *
 * This component file was created using the Joomla Component Creator by Not Web Design
 * http://www.notwebdesign.com/joomla_component_creator/
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );

/**
 * myApi Controller
 *
 * @package Joomla
 * @subpackage myApi
 */
class MyapiController extends JController {
    /**
     * Constructor
     * @access private
     * @subpackage myApi
     */
    function __construct() {
        //Get View
        if(JRequest::getCmd('view') == '') {
            JRequest::setVar('view', 'users');
        }
        $this->item_type = 'Users';
		
		$version = new JVersion;
   		$joomla = $version->getShortVersion();
		$vnum = substr($joomla,0,3);
		if($vnum == '1.6'){
			JSubMenuHelper::addEntry(JText::_('USERS'), 'index.php?option=com_myapi&amp;view=users');
			JSubMenuHelper::addEntry(JText::_('APP_SETTINGS'), 'index.php?option=com_myapi&amp;view=plugin&amp;plugin=myApiConnect');
			JSubMenuHelper::addEntry(JText::_('REAL_TIME'), 'index.php?option=com_myapi&amp;view=realtime');
			JSubMenuHelper::addEntry(JText::_('PAGES'), 'index.php?option=com_myapi&amp;view=pages');
			JSubMenuHelper::addEntry(JText::_('TAB_APP'), 'index.php?option=com_myapi&amp;view=plugin&amp;plugin=myApiTabs');
			JSubMenuHelper::addEntry(JText::_('OPEN_GRAPH'), 'index.php?option=com_myapi&amp;view=plugin&amp;plugin=myApiOpenGraph');
			JSubMenuHelper::addEntry(JText::_('COMMENT'), 'index.php?option=com_myapi&amp;view=plugin&amp;plugin=myApiComment');
			JSubMenuHelper::addEntry(JText::_('SHARE'), 'index.php?option=com_myapi&amp;view=plugin&amp;plugin=myApiShare');
			JSubMenuHelper::addEntry(JText::_('LIKE'), 'index.php?option=com_myapi&amp;view=plugin&amp;plugin=myApiLike');
			JSubMenuHelper::addEntry(JText::_('SEND'), 'index.php?option=com_myapi&amp;view=plugin&amp;plugin=myApiSend');
		}
		parent::__construct();
    }
	
	function cancel(){
		if(JRequest::getVar('view') == 'pages'){
			JRequest::setVar( 'view', 'pages' );
    		JRequest::setVar( 'layout', 'default'  );
    		JRequest::setVar( 'hidemainmenu', 0 );	
			parent::display();
		}	
	}
	
	function composeMessage(){
		JRequest::setVar( 'view', 'pages' );
    	JRequest::setVar( 'layout', 'send'  );
    	JRequest::setVar( 'hidemainmenu', 1 );
 
   	 	parent::display();
	}
	
	function sendMessage(){
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$cid = JRequest::getVar('cid',NULL,'post');
		$message = JRequest::getString('message',NULL,'post');
		
		if(is_null($cid) || !is_array($cid) || sizeof($cid) == 0 || is_null($message) || $message == ''){
			//Invalid
			JError::raiseWarning( 100, JText::_('REQUIRED_FIELDS') );	
			JRequest::setVar( 'view', 'pages' );
    		JRequest::setVar( 'layout', 'send'  );
			parent::display();
		}else{
			$facebook = plgSystemmyApiConnect::getFacebook();
			if(!$facebook){
				JRequest::setVar( 'view', 'plugin' );
    			JRequest::setVar( 'plugin', 'myApiConnect'  );	
				parent::display();
			}else{
				$model = $this->getModel('pages');
				$pages = $model->getPageDetails($cid);
				$user = JFactory::getUser();
				
				$data = array('message' => $message);
				
				$link = JRequest::getVar('menuItem','','post');
				if($link != ''){
					$link_name = JRequest::getVar('link_name','','post');
					$link_caption = JRequest::getVar('link_caption','','post');
					$link_description = JRequest::getVar('link_description','','post');
					
					$data['type'] = 'link';
					$data['link'] = JRoute::_('index.php?Itemid='.$link,true,-1);
					if($link_name 			!= '') $data['name'] 		= $link_name; 
					if($link_caption 		!= '') $data['caption'] 	= $link_caption; 
					if($link_description	!= '') $data['description'] = $link_description; 
				}
				
				$image = JRequest::getVar('fileList','','post');
				if($image != ''){
					$data['picture'] = JURI::root().'images/'.$image;	
				}
				
				foreach($pages as $page){
					try{
						$facebook->api('/'.$page['pageId'].'/'.'feed', 'post',array_merge(array('access_token' => $page['access_token']),$data));		
						JFactory::getApplication()->enqueueMessage(JText::_( 'Success').' - '.$page['name']);
					}catch(FacebookAPIException $e){ JError::raiseWarning( 100, $page['name'].' '.JText::_('Error').' - '.$e ); }	
				}
				$this->setRedirect('index.php?option=com_myapi&view=pages');
			}
		}
	}
	
	
	
	
	function savePlugin(){
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$version = new JVersion;
   		$joomla = $version->getShortVersion();
		$vnum = substr($joomla,0,3);
		$db   	=& JFactory::getDBO();
		
		if($vnum == '1.5'){
			$row  	=& JTable::getInstance('plugin');
		}else{
			$row  	=& JTable::getInstance('extension');
			JPluginHelper::importPlugin('extension');
			
		}
		$post = JRequest::get('post');
	  	$data = ($vnum == '1.5') ? $post : $post['jform'];
		
		$row->load(JRequest::getVar('id','','post'));
		$client = JRequest::getWord( 'filter_client', 'site' );
		
		if (!$row->bind($data)) JError::raiseError(500, $row->getError() );
		if (!$row->check()) JError::raiseError(500, $row->getError() );
		if (!$row->store()) JError::raiseError(500, $row->getError() );
		
		$row->checkin();
		$row->reorder( 'folder = '.$db->Quote($row->folder).' AND ordering > -10000 AND ordering < 10000 AND ( "client_id=0" )' );

		//custom logic for saving spefic plugins
		$funcname = 'save_'.$row->element;
		if(method_exists('MyapiController','save_'.$row->element)){
			$this->$funcname();
		}else{
			$this->setRedirect( 'index.php?option=com_myapi&view=plugin&plugin='.$row->element, JText::_('PLUGIN_SAVED').' '.$row->name );
		}
	}
	
	function save_myApiConnect() {
		$post = JRequest::get('post');
		$u =& JURI::getInstance(JURI::root());
		$port 	= ($u->getPort() == '') ? '' : ":".$u->getPort();
		$host = (substr($u->getHost(),0,4) == 'www.') ? substr($u->getHost(),4) : $u->getHost();
		$connectURL = $u->getScheme().'://'.$host.$port.$u->getPath();
		$baseDomain = $host;
		
		$version = new JVersion;
   		$joomla = $version->getShortVersion();
		$vnum = substr($joomla,0,3);
		
		$data['base_domain'] 	= $baseDomain;
		$data['uninstall_url'] 	= JURI::root().'index.php?option=com_myapi&task=deauthorizeCallback';
		$data['connect_url'] 	= $connectURL;
		$facebook = null;
		global $postFacebook;
		$postFacebook = false;
		try{
			if($vnum == '1.5'){
				require_once JPATH_SITE.DS.'plugins'.DS.'system'.DS.'myApiConnectFacebook.php';
				$appId =  $post['params']['appId']; 
				$secret = $post['params']['secret'];
			}else{
				require_once JPATH_SITE.DS.'plugins'.DS.'system'.DS.'myApiConnect'.DS.'myApiConnectFacebook.php';
				$appId =  $post['jform']['params']['appId']; 
				$secret = $post['jform']['params']['secret'];
			}
			
			$postFacebook = new myApiFacebook(array(
				'appId'  => $appId,
				'secret' => $secret
			));
			$app_update = $postFacebook->api(array('method' => 'admin.setAppProperties','access_token' => $appId.'|'.$secret,'properties'=> json_encode($data)));
		}catch (FacebookApiException $e) {
			$postFacebook = null;
			JError::raiseWarning( 100, JText::_('APP_SAVED_ERROR').$e);
		}
		
		if(!is_null($postFacebook)){
			JFactory::getApplication()->enqueueMessage(JText::_('APP_SAVED'));
			
			$model = $this->getModel('realtime');
			$model->addSubscriptions();
			$this->addPages('index.php?option=com_myapi&view=plugin&plugin=myApiConnect');
		}else{
			$this->setRedirect( 'index.php?option=com_myapi&view=plugin&plugin=myApiConnect');
		}
	}
	
	function save_myApiTabs(){
		$post 		= JRequest::get('post');	
		$facebook 	= plgSystemmyApiConnect::getFacebook();
		try{
			jimport( 'joomla.application.menu' );
			$data['tab_default_name']	= $post['params']['tab_name'];
			$data['profile_tab_url'] 	= JRoute::_(JURI::root()."index.php?Itemid=".$post['params']['tab_url']);
			$data['edit_url'] = $data['profile_tab_url'];
			$app_update = $facebook->api(array('method' => 'admin.setAppProperties','access_token' => $facebook->getAppId().'|'.$facebook->getApiSecret(),'properties'=> json_encode($data)));
			$this->setRedirect( 'index.php?option=com_myapi&view=plugin&plugin=myApiTabs',JText::_('TAB_SAVED'));
		}catch (FacebookApiException $e) {
			$this->setRedirect( 'index.php?option=com_myapi&view=plugin&plugin=myApiTabs',JText::_('TAB_SAVED_ERROR').$e);
		}
	}

	//Deletes the link between a joomla and facebook user
	function unlinkUser(){
		JRequest::checkToken() or die( 'Invalid Token' );
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_myapi'.DS.'tables');
	  	$row =& JTable::getInstance('myapiusers', 'Table');
		foreach($_POST['cid'] as $id){
			$row->delete( $id );
		}
		$this->setRedirect("index.php?option=com_myapi&view=users", JText::_('USER_UNLINKED'));
	}
	
	function addSubscriptions(){
		$model = $this->getModel('realtime');
		$model->addSubscriptions();
		$this->setRedirect('index.php?option=com_myapi&view=realtime');
	}
	
	function deleteSubscriptions(){
		$model = $this->getModel('realtime');
		$model->deleteSubscriptions();
		$this->setRedirect('index.php?option=com_myapi&view=realtime');
	}
	
	function addPages($end = 'index.php?option=com_myapi&view=pages'){
		global $postFacebook;
		$facebook = (is_object($postFacebook)) ? $postFacebook : plgSystemmyApiConnect::getFacebook();
		$endUrl = base64_decode(JRequest::getVar('endUrl',base64_encode($end)));
		if($facebook){
			$login = $facebook->getLoginUrl(array("req_perms" => "manage_pages,read_stream,publish_stream","next" => JURI::base().'index.php?option=com_myapi&task=addPages&endUrl='.base64_encode($end),"cancel" => JURI::base()));
			$user = $facebook->getSession();
			if($user){
				$permissions = null;
				try{
					$permissions = $facebook->api("/me/permissions",'get',array('access_token' => $facebook->getAccessToken()));
				}catch (FacebookApiException $e) {
				}
				
				if(!is_array($permissions) || !array_key_exists('manage_pages', $permissions['data'][0]) || !array_key_exists('read_stream', $permissions['data'][0]) || !array_key_exists('publish_stream', $permissions['data'][0]) ) {
					$this->setRedirect($login);
				}else{
					$pages = $facebook->api('me/accounts');
					$db = JFactory::getDBO();
					$count = 0;
					foreach($pages['data'] as $page){
						if($page['category'] != 'Website'){
							$pageLink = $facebook->api('/'.$page['id']);
							$query = "INSERT INTO ".$db->nameQuote('#__myapi_pages')." (".$db->nameQuote('pageId').",".$db->nameQuote('access_token').",".$db->nameQuote('name').",".$db->nameQuote('link').",".$db->nameQuote('category').",".$db->nameQuote('owner').") VALUES (".$db->quote($page['id']).",".$db->quote($page['access_token']).",".$db->quote($page['name']).",".$db->quote($pageLink['link']).",".$db->quote($page['category']).",".$db->quote($user['uid']).") ".
											"ON DUPLICATE KEY UPDATE ".$db->nameQuote('access_token')." = ".$db->quote($page['access_token'])." , ".$db->nameQuote('name')." = ".$db->quote($page['name'])." , ".$db->nameQuote('owner')." = ".$db->quote($user['uid'])."; ";
							$db->setQuery($query);
							$db->query();
							if($db->getErrorNum()){
								JError::raiseWarning( 100, JText::_('PAGES_ADDED_ERROR').' '.$db->getErrorMsg() );	
							}else{
								$count++;
							}
						}
					}
					
					if($count > 0){
						JFactory::getApplication()->enqueueMessage( sprintf(JText::_('PAGES_ADDED'),$count) );	
					}
					$this->setRedirect($endUrl);
				}
			}else{
				$this->setRedirect($login);
			}	
		}else{
			$this->setRedirect($endUrl);	
		}
	}
	
	function deletePages(){
		JRequest::checkToken() or die( 'Invalid Token' );
		$db = JFactory::getDBO();
		$quoted = array();
		foreach($_POST['cid'] as $id) $quoted[] = $db->quote($id);
		$ids = implode(',',$quoted);
		$query = "DELETE FROM ".$db->nameQuote('#__myapi_pages')." WHERE ".$db->nameQuote('pageId')." IN (".$ids.");";
		$db->setQuery($query);
		$db->query();
		if($db->getErrorNum()){
			JError::raiseWarning( 100, JText::_('PAGES_UNLINKED_ERROR').' '.$db->getErrorMsg() );	
		}else{
			JFactory::getApplication()->enqueueMessage( sprintf(JText::_('PAGES_UNLINKED'),$db->getAffectedRows()) );
		}
		$this->setRedirect("index.php?option=com_myapi&view=pages");
	}
}
?>