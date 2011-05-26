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

define( 'COM_MYAPI_DIR', 'images'.DS.'myapi'.DS );
define( 'COM_MYAPI_BASE', JPATH_ROOT.DS.COM_MYAPI_DIR );
define( 'COM_MYAPI_BASEURL', JURI::root().str_replace( DS, '/', COM_MYAPI_DIR ));


require_once JPATH_COMPONENT.DS.'controller.php';
require_once JPATH_COMPONENT.DS.'helpers'.DS.'helper.php';
$controller = new MyapiController( );
$controller->execute( JRequest::getCmd('task'));


if((JRequest::getVar('view') != "settings") && (JRequest::getVar('task','','post') != 'saveAPI')){
	$plugin =& JPluginHelper::getPlugin('system', 'myApiConnect');
	$params = new JParameter( $plugin->params );
	$appId 	= $params->get('appId');
	$secret = $params->get('secret');
	if(($appId == '') || ($secret == '')) header('Location: index.php?option=com_myapi&view=settings');
}

$controller->redirect();
?>