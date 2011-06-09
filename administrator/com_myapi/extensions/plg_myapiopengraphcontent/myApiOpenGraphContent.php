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
 **   Joomla! 1.5 Plugin myApimyApiOpenGraphContent                        	**
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
	public function __construct(& $subject, $config) {
 		parent::__construct($subject, $config);
 		$this->loadLanguage();
  	}
	
	public function onContentBeforeSave($context, &$article, $isNew){
		$result	= $this->onBeforeContentSave( &$article, $isNew );
		return $result;
	}
	public function onContentBeforeDisplay($context, &$article, &$params, $limitstart){
		$result	= $this->onBeforeDisplayContent( &$article, &$params, $limitstart );
		return $result;
	}
	
	function getContentImage($text){
		$version = new JVersion;
   	 	$joomla = $version->getShortVersion();
    	if(substr($joomla,0,3) == '1.6'){
			require_once(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'myApiConnect'.DS.'myApiDom.php');
		}else{
			require_once(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'myApiDom.php');
		}
		
		$dom = new simple_html_dom();
		$dom->load($text);
		$dom_image = $dom->find('img',0);
		if($dom_image){
			$src = $dom_image->src;
			if ( (substr($src, 0, 7) != 'http://') && (substr($src, 0, 8) != 'https://') )
				$src = (substr($src, 0, 1) == '/') ? JURI::root().substr($src, 1) : JURI::root().$src;
				
			return $src; 
		}else{
			return 0;	
		}	
	}
	
	function onBeforeContentSave( &$article, $isNew ){
		$image_src = plgContentmyApiOpenGraphContent::getContentImage($article->introtext);
		$attribs = new JParameter($article->attribs);
		$attribs->set('myapiimage',$image_src);
		$article->attribs = $attribs->toString();
		return true;
	}
	
	function onBeforeDisplayContent( &$article, &$params, $limitstart ){
		$version = new JVersion;
   		$joomla = $version->getShortVersion();
		$vnum = substr($joomla,0,3);
		if(!class_exists('plgSystemmyApiConnect') || ( (!array_key_exists('category',$article) && !isset($params->showK2Plugins) && ($vnum == '1.5')))){ return; }
		
		if((@$article->id != '') && (@$_POST['fb_sig_api_key'] == '') && class_exists('plgSystemmyApiOpenGraph')){
			$row = & JTable::getInstance('content');
			$row->load($article->id);
			
			$version = new JVersion;
   	 		$joomla = $version->getShortVersion();
    		if(substr($joomla,0,3) == '1.6'){
				$attribs = new JRegistry();
				$attribs->loadJSON($row->attribs);
				if($attribs->get('myapiimage','') == ''){
					$attribs->set('myapiimage',plgContentmyApiOpenGraphContent::getContentImage($article->text));
					$row->attribs = $attribs->toString();
					$row->store();	
				}
			}else{
				$attribs = new JParameter($row->attribs);
				if($attribs->get('myapiimage','') == ''){
					$attribs->set('myapiimage',plgContentmyApiOpenGraphContent::getContentImage($article->text));
					$row->attribs = $attribs->toString();
					$row->bind($row);
					$row->store();
				}
			}
			
			//Set open graph tags
			if(JRequest::getVar('view','','get') == 'article' || (JRequest::getVar('option','','get') == 'com_k2' && JRequest::getVar('view','','get') == 'item')){
				if(isset($article->slug)){
					require_once(JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
					$link = ContentHelperRoute::getArticleRoute($article->slug, $article->catslug, $article->sectionid);
				}elseif(method_exists('K2HelperRoute','getItemRoute')){
					$link = K2HelperRoute::getItemRoute($article->id.':'.urlencode($article->alias),$article->catid.':'.urlencode($article->category->alias));
				}else{
					error_log('myApi unable to calculate link for the article id '.$article->id);
					return;
				}
				$articleURL = JRoute::_($link,true,-1);
				$rawText = strip_tags($article->introtext);
				$newTags = array();
				$newTags['og:title'] 		= $article->title;
				$newTags['og:description'] 	= (strlen($rawText) > 247) ? substr($rawText,0,247).'...' : $rawText;
				$newTags['og:type']	= 'article';
				$newTags['og:author']	= (is_object($article->author)) ? $article->author->name : $article->author;
				$newTags['og:url'] 	= $articleURL;
				if($attribs->get('ogimage','0') != '0') $newTags['og:image'] = $attribs->get('myapiimage');
				
				plgSystemmyApiOpenGraph::setTags($newTags);
			}
		}
	}
}
