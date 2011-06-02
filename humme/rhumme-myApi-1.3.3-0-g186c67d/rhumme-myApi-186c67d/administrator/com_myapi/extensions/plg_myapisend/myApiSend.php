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
 **   Joomla! 1.5 Plugin myApiSend                                          **
 **   @Copyright Copyright (C) 2011 - Thomas Welton                         **
 **   @license GNU/GPL http://www.gnu.org/copyleft/gpl.html                 **	
 **                                                                         **	
 **   myApiSend is free software: you can redistribute it and/or modify     **
 **   it under the terms of the GNU General Public License as published by  **
 **   the Free Software Foundation, either version 3 of the License, or	    **	
 **   (at your option) any later version.                                   **
 **                                                                         **
 **   myApiSend is distributed in the hope that it will be useful,   	    **
 **   but WITHOUT ANY WARRANTY; without even the implied warranty of	    **
 **   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         **
 **   GNU General Public License for more details.                          **
 **                                                                         **
 **   You should have received a copy of the GNU General Public License	    **
 **   along with myApiSend.  If not, see <http://www.gnu.org/licenses/>     **
 **                                                                         **			
 *****************************************************************************/
jimport( 'joomla.plugin.plugin' );

class plgContentmyApiSend extends JPlugin
{
	function onPrepareContent( &$article, &$params, $limitstart ){
		if(!file_exists(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'myApiConnectFacebook.php')){ return; }
		
		//this may fire fron a component other than com_content
		if((@$article->id != '') && (@$_POST['fb_sig_api_key'] == '')){
			$doc = & JFactory::getDocument();
			
			$plugin = & JPluginHelper::getPlugin('content', 'myApiSend');

			// Load plugin params info
			$myapiparama = new JParameter($plugin->params);
			
			$send_sections 		= $myapiparama->get('send_sections');
			$send_categories 	= $myapiparama->get('send_categories');
			$send_show_on 		= $myapiparama->get('send_show_on');
			$layout_style 		= $myapiparama->get('layout_style');
			$show_faces 		= $myapiparama->get('show_faces');
			$color_scheme 		= $myapiparama->get('color_scheme');
			$verb 				= $myapiparama->get('verb');
			$width 				= $myapiparama->get('width');
			$send_style 		= $myapiparama->get('send_style');
			$font 				= $myapiparama->get('send_font');
			$ref 				= $myapiparama->get('send_ref');
			$show_send 			= $myapiparama->get('send_send');
			$send_show 			= false;
		
			$facebook = plgSystemmyApiConnect::getFacebook();
			
			if($article->sectionid != ''){
				if( is_array($send_sections) ){	
					foreach($send_sections as $id){ if($id == $article->sectionid) $send_show = true;  }
				}
				elseif($send_sections == $article->sectionid) $send_show = true;
			}
			
			if($article->category != ''){
				if( is_array($send_categories) ){	
					foreach($send_categories as $id){ if($id == $article->category) $send_show = true; }
				}
				elseif($send_categories == $article->category) $send_show = true;
			}
			
			if(($send_show) || ($send_show_on == 'all')){
				require_once(JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
				
				$link 		= JRoute::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catslug, $article->sectionid));
				$u			=& JURI::getInstance( JURI::base() );
				$link 		= 'http://'.$u->getHost().$link;
				$newtext	= '<fb:send href="'.$link.'" colorscheme="'.$color_scheme.'" font="'.$font.'" ref="'.$ref.'"></fb:send>';
		
				$newtext 		= $newtext.$article->text;
				$article->text 	= $newtext;
			}
		}
	}

    function bind( $array, $ignore = '' ){
        if (key_exists( 'send_sections', $array ) && is_array( $array['send_sections'] )) {
                $array['send_sections'] = implode( ',', $array['send_sections'] );
        }
		if (key_exists( 'send_categories', $array ) && is_array( $array['send_categories'] )) {
                $array['send_categories'] = implode( ',', $array['send_categories'] );
        }
 
        return parent::bind( $array, $ignore );
    }

}
