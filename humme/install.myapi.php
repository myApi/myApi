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
 **   but WITHOUT ANY WARRANTY without even the implied warranty of	    **
 **   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         **
 **   GNU General Public License for more details.                          **
 **                                                                         **
 **   You should have received a copy of the GNU General Public License	    **
 **   along with myApi.  If not, see <http://www.gnu.org/licenses/>.	    **
 **                                                                         **			
 *****************************************************************************/

jimport('joomla.installer.helper');
$installer = new JInstaller();
$installer->_overwrite = true;

$pkg_path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_myapi'.DS.'extensions'.DS;
$pkgs = array('mod_myapi_fblogin'=> array('name' => 'Facebook connect login module', 'db' => ''),
			  'mod_myapi_fbfan'=> array('name' => 'Facebook fan box module', 'db' => ''),
			  'mod_myapi_fbactivity'=> array('name' => 'Facebook activity box', 'db' => ''),
			  'mod_myapi_fblive'=> array('name' => 'Facebook Live Box', 'db' => ''),
			  'mod_myapi_fbrecommendations'=> array('name' => 'Facebook Recommendations box', 'db' => ''),
			  'plg_myapiauth'=> array('name' => 'Authorisation plugin',  'db' => 'myApiAuth'),
			  'plg_myapicomment'=> array('name' => 'Comment content plugin',  'db' => 'myApiComment'), 
			  'plg_myapiconnect'=> array('name' => 'Facebook Connect parsing plugin', 'db' => 'myApiConnect'),
			  'plg_myapishare'=> array('name' => 'Share content plugin', 'db' => 'myApiShare'),
			  'plg_myapilike'=> array('name' => 'Like content plugin', 'db' => 'myApiLike'),
			  'plg_myapimodal'=> array('name' => 'Facebook Style Modal Boxes', 'db' => 'myApiModal'),
			  'plg_myapiuser'=> array('name' => 'User actions plugin', 'db' => 'myapiuser'));

foreach( $pkgs as $pkg => $pkgarray ){
 $msgcolor = "";
 $msgtext = "";
 try{
  if( $installer->install( dirname(__FILE__).DS.'extensions'.DS.$pkg) )
  {
    $msgcolor = "#E0FFE0";
    $msgtext  = $pkgarray['name']." successfully installed.";
	if($pkgarray['db'] != ''){
		$db = JFactory::getDBO();
		$query = "UPDATE #__plugins SET published=1 WHERE element='".$pkgarray['db']."'";
		$db->setQuery($query);
		$db->query();
		$msgtext = $msgtext. " and automatically published";
	}
  }
  else
  {
    $msgcolor = "#FFD0D0";
    $msgtext  = "ERROR: Could not install the ".$pkgarray['name']." Please install manually.";
  }
  } catch (Exception $e) {
	    $msgcolor = "#FFD0D0";
   	 $msgtext  = "ERROR: Could not install the ".$pkgarray['name']." Please install manually.";
  }
  ?>
  <table bgcolor="<?php echo $msgcolor; ?>" width ="100%">
    <tr style="height:30px;">
      <td width="50"><img src="components/com_myapi/assets/tick.png" height="20px" width="20px"></td>
      <td><font size="2"><b><?php echo $msgtext; ?></b></font></td>
    </tr>
  </table>
<?php } ?>

<?php 

$db = JFactory::getDBO();
$query = array();
$query[] = "ALTER TABLE ".$db->nameQuote('#__myapi_users')."  ADD ( `access_token` varchar(255) NOT NULL,  `avatar` varchar(255) default NULL)";
$query[] = "ALTER TABLE ".$db->nameQuote('#__myapi_users')." MODIFY `uid` bigint(255) unsigned NOT NULL;";
$query[] = "CREATE UNIQUE INDEX ".$db->nameQuote('userId')." ON ".$db->nameQuote('#__myapi_users')." (".$db->nameQuote('userId').");";
$query[] = "CREATE UNIQUE INDEX ".$db->nameQuote('uid')." ON ".$db->nameQuote('#__myapi_users')." (".$db->nameQuote('uid').");";
foreach($query as $sql){
	$db->setQuery($sql);
	$db->query();	
}


?>