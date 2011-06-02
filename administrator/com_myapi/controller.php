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
		parent::__construct();
    }
	
	
	function savePlugin(){
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$db   	=& JFactory::getDBO();
		$row  	=& JTable::getInstance('plugin');
		$row->load(JRequest::getVar('id','','post'));
		$client = JRequest::getWord( 'filter_client', 'site' );

		if (!$row->bind(JRequest::get('post'))) JError::raiseError(500, $row->getError() );
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
		
		$data['base_domain'] 	= $baseDomain;
		$data['uninstall_url'] 	= JURI::root().'index.php?option=com_myapi&task=deauthorizeCallback';
		$data['connect_url'] 	= $connectURL;
		
		try{
			require_once JPATH_SITE.DS.'plugins'.DS.'system'.DS.'myApiConnectFacebook.php';
			$facebook = new myApiFacebook(array(
				'appId'  => $post['params']['appId'],
				'secret' => $post['params']['secret']
			));
			$app_update = $facebook->api(array('method' => 'admin.setAppProperties','access_token' => $post['params']['appId'].'|'.$post['params']['secret'],'properties'=> json_encode($data)));
			
			$model = $this->getModel('realtime');
			$model->addSubscriptions();
			
			$this->setRedirect( 'index.php?option=com_myapi&view=plugin&plugin=myApiConnect',JText::_('APP_SAVED'));	
		
		}catch (FacebookApiException $e) {
			$this->setRedirect( 'index.php?option=com_myapi&view=plugin&plugin=myApiConnect',JText::_('APP_SAVED_ERROR').$e);		
		}
		
	}
	
	function save_myApiTabs(){
		$post 		= JRequest::get('post');	
		$facebook 	= plgSystemmyApiConnect::getFacebook();
		try{
			$data['tab_default_name'] = $post['params']['tab_name'];
			jimport( 'joomla.application.menu' );
			$data['profile_tab_url'] = JRoute::_(JURI::root()."index.php?Itemid=".$post['params']['tab_url']);
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
}
?>