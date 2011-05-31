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
 **   Joomla! 1.5 Plugin myApiShare                                         **
 **   @Copyright Copyright (C) 2011 - Thomas Welton                         **
 **   @license GNU/GPL http://www.gnu.org/copyleft/gpl.html                 **	
 **                                                                         **	
 **   myApiContent is free software: you can redistribute it and/or modify  **
 **   it under the terms of the GNU General Public License as published by  **
 **   the Free Software Foundation, either version 3 of the License, or	    **	
 **   (at your option) any later version.                                   **
 **                                                                         **
 **   myApiContent is distributed in the hope that it will be useful,	    **
 **   but WITHOUT ANY WARRANTY; without even the implied warranty of	    **
 **   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         **
 **   GNU General Public License for more details.                          **
 **                                                                         **
 **   You should have received a copy of the GNU General Public License	    **
 **   along with myApiContent.  If not, see <http://www.gnu.org/licenses/>  **
 **                                                                         **			
 *****************************************************************************/
 
// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class plgContentmyApiShare extends JPlugin
{

	function onBeforeDisplayContent( &$article, &$params, $limitstart )
	{
		if(!file_exists(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'myApiConnectFacebook.php') || (!array_key_exists('category',$article) && !isset($params->showK2Plugins)  )){ return; }
		
		//this may fire fron a component other than com_content
		if((@$article->id != '') && (@$_POST['fb_sig_api_key'] == ''))
		{
			$doc = & JFactory::getDocument();
			
			$plugin = & JPluginHelper::getPlugin('content', 'myApiShare');

			// Load plugin params info
			$myapiparama = new JParameter($plugin->params);
			
			$share_sections = $myapiparama->get('share_sections');
			$share_categories = $myapiparama->get('share_categories');
			$share_show_on = $myapiparama->get('share_show_on');
			$share_type = $myapiparama->get('share_type');
			$share_style = $myapiparama->get('share_style');
			$share_show = false;
		
			$facebook = plgSystemmyApiConnect::getFacebook();
			
			if(isset($article->sectionid))
			{
				if( is_array($share_sections) )
				{	foreach($share_sections as $id)
					{ if($id == $article->sectionid) { $share_show = true; } }
				}
				else{ if($share_sections == $article->sectionid) { $share_show = true; } }
			}
			
			if(isset($article->category))
			{
				if( is_array($share_categories) )
				{	foreach($share_categories as $id)
					{ if($id == $article->category) { $share_show = true; } }
				}
				else
				{ if($share_categories == $article->category) { $share_show = true; }	}
			}
			
			if(($share_show) || ($share_show_on == 'all'))
			{
				require_once(JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
				
				if(isset($article->slug)){
					require_once(JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
					$link = ContentHelperRoute::getArticleRoute($article->slug, $article->catslug, $article->sectionid);
				}elseif(method_exists('K2HelperRoute','getItemRoute')){
					$link = K2HelperRoute::getItemRoute($article->id.':'.urlencode($article->alias),$article->catid.':'.urlencode($article->category->alias));
				}else{
					error_log('myApi unable to calculate link for the article id '.$article->id);
					return;
				}
				$u =& JURI::getInstance( JURI::base() );
				$link = 'http://'.$u->getHost().$link;
				$newtext = '<fb:share-button class="url" href="'.$link.'" style="'.$share_style.'" type="'.$share_type.'"></fb:share-button>';
				
				$com_params = &JComponentHelper::getParams( 'com_myapi' );
				
				$newtext = $newtext.$article->text;
				$article->text = $newtext;
			}
		}

	}

    function bind( $array, $ignore = '' )
    {
        if (key_exists( 'share_sections', $array ) && is_array( $array['share_sections'] )) {
                $array['share_sections'] = implode( ',', $array['share_sections'] );
        }
		 if (key_exists( 'share_categories', $array ) && is_array( $array['share_categories'] )) {
                $array['share_categories'] = implode( ',', $array['share_categories'] );
        }
 
        return parent::bind( $array, $ignore );
    }

}
