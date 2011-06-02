<?php
/**
 * Joomla! 1.5 component myApi
 *
 * @version $Id: myapi.php 2010-05-01 08:43:14 svn $
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

jimport('joomla.application.component.model');

/**
 * myApi Component myApi Model
 *
 * @author      notwebdesign
 * @package		Joomla
 * @subpackage	myApi
 * @since 1.5
 */
class MyapiModelMyapi extends JModel {
    /**
	 * Constructor
	 */
	function __construct() {
		parent::__construct();
    }
	
	function getAvatar($id = NULL){
		$db = JFactory::getDBO();
		$user = JFactory::getUser($id);
		
		  $query = "SELECT avatar FROM ".$db->nameQuote('#__myapi_users')." WHERE userId =".$db->quote($user->id);
		  $db->setQuery($query);
		  $db->query();
		  return 'tn'.$db->loadResult();
	}
	
	//Get the permissions required
	// for now this just returns an array but in future it will only requests permsissions needed
	// for example without jomsocial integration we don't need user_status.
	function getPerms(){
		return array('email','user_likes','publish_stream','offline_access','user_about_me','user_status');
	}
}
?>