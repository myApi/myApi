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
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'helper.php' );

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
	
	
	function savePlugin()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$db   =& JFactory::getDBO();
		$row  =& JTable::getInstance('plugin');

		$client = JRequest::getWord( 'filter_client', 'site' );

		if (!$row->bind(JRequest::get('post'))) {
			JError::raiseError(500, $row->getError() );
		}
		if (!$row->check()) {
			JError::raiseError(500, $row->getError() );
		}
		if (!$row->store()) {
			JError::raiseError(500, $row->getError() );
		}
		$row->checkin();
		$where = "client_id=0";
		
		$row->reorder( 'folder = '.$db->Quote($row->folder).' AND ordering > -10000 AND ordering < 10000 AND ( '.$where.' )' );

		$msg = 'Successfully Saved Plugin'.$row->name;
		$this->setRedirect( 'index.php?option=com_myapi&view='. JRequest::getVar('view','','post'), $msg );
			
	}
	
	function saveAPI() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$post = JRequest::get( 'post' );
		$db   =& JFactory::getDBO();
		$row  =& JTable::getInstance('plugin');

		$client = JRequest::getWord( 'filter_client', 'site' );

		if (!$row->bind(JRequest::get('post'))) {
			JError::raiseError(500, $row->getError() );
		}
		if (!$row->check()) {
			JError::raiseError(500, $row->getError() );
		}
		if (!$row->store()) {
			JError::raiseError(500, $row->getError() );
		}
		$row->checkin();
		$where = "client_id=0";
		
		$row->reorder( 'folder = '.$db->Quote($row->folder).' AND ordering > -10000 AND ordering < 10000 AND ( '.$where.' )' );
		
			require_once JPATH_SITE.DS.'plugins'.DS.'system'.DS.'myApiConnectFacebook.php';
			$facebook =& new myApiFacebook(array(
				'appId'  => $post['params']['appId'],
				'secret' => $post['params']['secret']
			 ));
			
			$root = JURI::root();
			$root = str_replace('http://','',$root);
			$root = str_replace('www.','',$root);
			
			if(substr($root,-1,1) == '/'){
				$root = substr($root,0,-1);	
			}
			$rootArray = explode(':',$root);
			$root = $rootArray[0];
			$connectURL = 'http://'.$root.'/';
			$baseDomain = $root;
			
			$data['base_domain'] = $baseDomain;
			$data['uninstall_url'] = JURI::base().'index.php?option=com_myapi&task=deauthorizeCallback';
			$data['connect_url'] = $connectURL;
			$app_update = $facebook->api(array('method' => 'admin.setAppProperties','properties'=> json_encode($data)));
			
			$this->setRedirect( 'index.php?option=com_myapi','Details saved, please ensure your Facebook application details are correct at facebook.com');	
		
	}

	
	//Deletes the link between a joomla and facebook user
	function unlinkUser(){
		JRequest::checkToken() or die( 'Invalid Token' );
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_myapi'.DS.'tables');
	  	$row =& JTable::getInstance('myapiusers', 'Table');
		foreach($_POST['cid'] as $id){
			$row->delete( $id );
		}
		$this->setRedirect("index.php?option=com_myapi&view=users", "User(s) Facebook accounts unlinked");
	}
}
?>