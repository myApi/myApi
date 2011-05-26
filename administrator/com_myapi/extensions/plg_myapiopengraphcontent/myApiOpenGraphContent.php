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
 **   Joomla! 1.5 Plugin myApimyApiOpenGraphContent                                **
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
jimport( 'joomla.plugin.plugin' );

class plgContentmyApiOpenGraphContent extends JPlugin
{
	function getContentImage($text){
		require_once(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'myApiDom.php');
		$dom = new simple_html_dom();
		$dom->load($text);
		$dom_image = $dom->find('img',0);
		if($dom_image){
			$src = $dom_image->src;
			if ( (substr($src, 0, 7) != 'http://') && (substr($src, 0, 8) != 'https://') )
				$src = (substr($src, 0, 1) == '/') ? JURI::base().substr($src, 1) : JURI::base().$src;
				
			return $src; 
		}else{
			return 0;	
		}	
	}
	
	function onBeforeContentSave( &$article, $isNew ){
		$image_src = plgContentmyApiOpenGraphContent::getContentImage($article->introtext);
		$attribs = new JParameter($article->attribs);
		$attribs->set('ogimage',$image_src);
		$article->attribs = $attribs->toString();
		return true;
	}
	
	function onPrepareContent( &$article, &$params, $limitstart ){
		//this may fire fron a component other than com_content
		if((@$article->id != '') && (@$_POST['fb_sig_api_key'] == '') && class_exists('plgSystemmyApiOpenGraph')){
			$attribs = new JParameter($article->attribs);
			if($attribs->get('ogimage','') == ''){
				$row = & JTable::getInstance('content');
				$row->load($article->id);
				$attribs->set('ogimage',plgContentmyApiOpenGraphContent::getContentImage($article->text));
				$row->attribs = $attribs->toString();
				$row->bind($row);
				$row->store();
			}
			//Set open graph tags
			if(JRequest::getVar('view','','get') == 'article'){
				require_once(JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
				$link = ContentHelperRoute::getArticleRoute($article->slug, $article->catslug, $article->sectionid);
				$u =& JURI::getInstance( JURI::base().$link );
				$articleURL = 'http://'.$u->getHost().$u->getPath().$u->getQuery();
				$rawText = strip_tags($article->introtext);
				$newTags = array();
				$newTags['og:title'] 		= $article->title;
				$newTags['og:description'] 	= (strlen($rawText) > 247) ? substr($rawText,0,247).'...' : $rawText;
				$newTags['og:type']	= 'article';
				$newTags['og:url'] 	= $articleURL;
				if($attribs->get('ogimage','0') != '0') $newTags['og:image'] = $attribs->get('ogimage');
				
				plgSystemmyApiOpenGraph::setTags($newTags);
			}
		}
	}
}
