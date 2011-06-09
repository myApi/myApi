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
	public function __construct(& $subject, $config) {
 		parent::__construct($subject, $config);
 		$this->loadLanguage();
  	}
	
	public function onContentBeforeDisplay($context, &$article, &$params, $limitstart){
		$result	= $this->onBeforeDisplayContent( &$article, &$params, $limitstart );
		return $result;
	}
	
	function onBeforeDisplayContent( &$article, &$params, $limitstart ){
		$version = new JVersion;
   		$joomla = $version->getShortVersion();
		$vnum = substr($joomla,0,3);
		if(!class_exists('plgSystemmyApiConnect') || ( (!array_key_exists('category',$article) && !isset($params->showK2Plugins) && ($vnum == '1.5')))){ return; }

		//this may fire fron a component other than com_content
		if((@$article->id != '') && (@$_POST['fb_sig_api_key'] == '')){
			$doc = & JFactory::getDocument();
			
			$plugin = & JPluginHelper::getPlugin('content', 'myApiLike');

			$like_sections 		= $this->params->get('like_sections');
			$like_categories 	= $this->params->get('like_categories');
			$like_show_on 		= $this->params->get('like_show_on');
			$layout_style 		= $this->params->get('layout_style');
			$color_scheme 		= $this->params->get('color_scheme');
			$verb 				= $this->params->get('verb');
			$width 				= (JRequest::getVar('tmpl') == 'component') ? '40' : $this->params->get('width');
			$font 				= $this->params->get('like_font');
			$ref 				= $this->params->get('like_ref');
			$show_send 			= ($this->params->get('show_send') == 1) ? 'true' : 'false';
			$show_faces 		= ($this->params->get('show_faces') == 1) ? 'true' : 'false';
			$position			= $this->params->get('position','myApiShareTop');
			$like_show 			= false;
			$viewAccess			= false;
		
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
			
			if(JRequest::getVar('view','','get') == 'article'){	
				$viewAccess = $this->params->get("like_view_article","1");
			}elseif((JRequest::getVar('layout','','get') == 'blog') || (JRequest::getVar('view','','get') == 'frontpage')){
				$viewAccess = $this->params->get("like_view_blog","1");
			}else{
				$viewAccess = $this->params->get("like_view_list","1");
			}
			
			if( (($like_show) || ($like_show_on == 'all'))  && $viewAccess ){
				if(isset($article->slug)){
					require_once(JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
					$link = ContentHelperRoute::getArticleRoute($article->slug, $article->catslug, $article->sectionid);
				}elseif(method_exists('K2HelperRoute','getItemRoute')){
					$link = K2HelperRoute::getItemRoute($article->id.':'.urlencode($article->alias),$article->catid.':'.urlencode($article->category->alias));
				}else{
					error_log('myApi unable to calculate link for the article id '.$article->id);
					return;
				}
				$link = JRoute::_($link,true,-1);
				
				$button = '<fb:like href="'.$link.'" layout="'.$layout_style.'" show_faces="'.$show_faces.'" width="'.$width.'" action="'.$verb.'" colorscheme="'.$color_scheme.'" font="'.$font.'" send="'.$show_send.'" ref="'.$ref.'"></fb:like>';
				if($vnum == '1.5'){
					require_once(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'myApiDom.php');
				}else{
					require_once(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'myApiConnect'.DS.'myApiDom.php');
				}
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
