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
 **   Joomla! 1.5 Plugin myApiLike                                          **
 **   @Copyright Copyright (C) 2011 - Thomas Welton                         **
 **   @license GNU/GPL http://www.gnu.org/copyleft/gpl.html                 **	
 **                                                                         **	
 **   myApiLike is free software: you can redistribute it and/or modify     **
 **   it under the terms of the GNU General Public License as published by  **
 **   the Free Software Foundation, either version 3 of the License, or	    **	
 **   (at your option) any later version.                                   **
 **                                                                         **
 **   myApiLike is distributed in the hope that it will be useful,   	    **
 **   but WITHOUT ANY WARRANTY; without even the implied warranty of	    **
 **   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         **
 **   GNU General Public License for more details.                          **
 **                                                                         **
 **   You should have received a copy of the GNU General Public License	    **
 **   along with myApiLike.  If not, see <http://www.gnu.org/licenses/>     **
 **                                                                         **			
 *****************************************************************************/
jimport( 'joomla.plugin.plugin' );

class plgContentmyApiLike extends JPlugin
{
	function onBeforeDisplayContent( &$article, &$params, $limitstart ){
		
		if(!file_exists(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'myApiConnectFacebook.php') || (!array_key_exists('category',$article) && !isset($params->showK2Plugins)  )){ return; }
		
		//this may fire fron a component other than com_content
		if((@$article->id != '') && (@$_POST['fb_sig_api_key'] == '')){
			$doc = & JFactory::getDocument();
			
			$plugin = & JPluginHelper::getPlugin('content', 'myApiLike');

			// Load plugin params info
			$myapiparama = new JParameter($plugin->params);
			
			$like_sections 		= $myapiparama->get('like_sections');
			$like_categories 	= $myapiparama->get('like_categories');
			$like_show_on 		= $myapiparama->get('like_show_on');
			$layout_style 		= $myapiparama->get('layout_style');
			$color_scheme 		= $myapiparama->get('color_scheme');
			$verb 				= $myapiparama->get('verb');
			$width 				= $myapiparama->get('width');
			$font 				= $myapiparama->get('like_font');
			$ref 				= $myapiparama->get('like_ref');
			$show_send 			= ($myapiparama->get('show_send') == 1) ? 'true' : 'false';
			$show_faces 		= ($myapiparama->get('show_faces') == 1) ? 'true' : 'false';
			$position			= $myapiparama->get('position','myApiShareTop');
			$like_show 			= false;
		
			$facebook = plgSystemmyApiConnect::getFacebook();
			
			if(isset($article->sectionid) && $article->sectionid != ''){
				if( is_array($like_sections) ){	
					foreach($like_sections as $id){ if($id == $article->sectionid) $like_show = true;  }
				}
				elseif($like_sections == $article->sectionid) $like_show = true;
			}
			 
			if(isset($article->category) && $article->category != ''){
				if( is_array($like_categories) ){	
					foreach($like_categories as $id){ if($id == $article->category) $like_show = true; }
				}
				elseif($like_categories == $article->category) $like_show = true;
			}
			
			if(($like_show) || ($like_show_on == 'all')){
				if(isset($article->slug)){
					require_once(JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
					$link = ContentHelperRoute::getArticleRoute($article->slug, $article->catslug, $article->sectionid);
				}elseif(method_exists('K2HelperRoute','getItemRoute')){
					$link = K2HelperRoute::getItemRoute($article->id.':'.urlencode($article->alias),$article->catid.':'.urlencode($article->category->alias));
				}else{
					error_log('myApi unable to calculate link for the article id '.$article->id);
					return;
				}
				$u =& JURI::getInstance( JURI::base().$link );
				$port 	= ($u->getPort() == '') ? '' : ":".$u->getPort();
				$link = 'http://'.$u->getHost().$port.$u->getPath().'?'.$u->getQuery();
				
				$button = '<fb:like href="'.$link.'" layout="'.$layout_style.'" show_faces="'.$show_faces.'" width="'.$width.'" action="'.$verb.'" colorscheme="'.$color_scheme.'" font="'.$font.'" send="'.$show_send.'" ref="'.$ref.'"></fb:like>';
				
				require_once(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'myApiDom.php');
				$article->text = myApiButtons::addToTable($article->text,$position,$button);
			}
		}
	}

    function bind( $array, $ignore = '' ){
        if (key_exists( 'like_sections', $array ) && is_array( $array['like_sections'] )) {
                $array['like_sections'] = implode( ',', $array['like_sections'] );
        }
		if (key_exists( 'like_categories', $array ) && is_array( $array['like_categories'] )) {
                $array['like_categories'] = implode( ',', $array['like_categories'] );
        }
 
        return parent::bind( $array, $ignore );
    }

}
