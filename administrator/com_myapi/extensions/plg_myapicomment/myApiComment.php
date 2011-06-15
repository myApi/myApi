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
 **   Joomla! 1.5 Plugin myApiContent                                       **
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

class plgContentmyApiComment extends JPlugin
{	
	public function __construct(& $subject, $config) {
 		parent::__construct($subject, $config);
 		$this->loadLanguage();
  		
		global $myApiCommentJsAdded;
		if(! $myApiCommentJsAdded == true){
			global $fbAsyncInitJs;
			
			$version 	= new JVersion;
   			$joomla		= $version->getShortVersion();
			$vnum 		= substr($joomla,0,3);
    		if($vnum == '1.6'){
				$fbAsyncInitJs .= 'FB.Event.subscribe("comment.create", function(response) { var ajax = new Request({url: "index.php?option=com_myapi&task=commentCreate&commentlink=" + escape(response.href), method: "get"}).send(); });';	
			}else{
				$fbAsyncInitJs .= 'FB.Event.subscribe("comment.create", function(response) { var ajax = new Ajax("index.php?option=com_myapi&task=commentCreate&commentlink=" + escape(response.href),{method: "get"}).request(); });';	
			}
			$myApiCommentJsAdded = true;
		}
	}
	
	public function onContentBeforeDisplay($context, &$article, &$params, $limitstart){
		$result	= $this->onBeforeDisplayContent( &$article, &$params, $limitstart );
		return $result;
	}
	
	function getComments($xid){
		$params  =   array(
		 'method'    => 'fql.query',
		 'query'     => "SELECT username,fromid,text,time FROM comment WHERE xid='".$xid."';"
		);
		$facebook = plgSystemmyApiConnect::getFacebook();
		$fqlResult   =   $facebook->api($params);
		
		$comments_seo = '';
		foreach($fqlResult as $comment){
			$comments_seo .= "<br />".$comment['username']." said:<br />".$comment['text'];
		}
		return $comments_seo;
	}
	
	function onBeforeDisplayContent( &$article, &$params, $limitstart )
	{
		//this may fire fron a component other than com_content
		if(is_object($article) && (@$article->id != '') && (@$_POST['fb_sig_api_key'] == '') && class_exists('plgSystemmyApiConnect'))
		{
			JPlugin::loadLanguage( 'plg_content_myapicomment' , JPATH_ADMINISTRATOR );
			$facebook = plgSystemmyApiConnect::getFacebook();
			if(!$facebook){ return; }
			
			require_once(JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
			
			$version = new JVersion;
   	 		$joomla = $version->getShortVersion();
    		$vnum = substr($joomla,0,3);
				
			if(isset($article->slug)){
				require_once(JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
				$link = ($vnum == '1.5') ? ContentHelperRoute::getArticleRoute($article->slug, $article->catslug, $article->sectionid) : ContentHelperRoute::getArticleRoute($article->slug, $article->catslug);
				$xid = urlencode('articlecomment'.$article->id);
			}elseif(method_exists('K2HelperRoute','getItemRoute')){
				$link = K2HelperRoute::getItemRoute($article->id.':'.urlencode($article->alias),$article->catid.':'.urlencode($article->category->alias));
				$xid = urlencode('k2comment'.$article->id);
			}else{
				error_log('myApi unable to calculate link for the article id '.$article->id);
				return;
			}
			$commentURL = JRoute::_($link,true,-1);
			
			$base = JURI::base();
			$doc = & JFactory::getDocument();
			JHTML::_('behavior.mootools');
					
			$comment_sections = $this->params->get('comment_sections');
			$comment_categories = $this->params->get('comment_categories');
			$comments_show_on = $this->params->get('comments_show_on');
			$comments_access = $this->params->get('comments_access');
			$comments_width = (JRequest::getVar('tmpl') == 'component') ? '520' : $this->params->get('comments_width');
			$comments_numposts = $this->params->get('comments_numposts');
			$comments_scheme = $this->params->get('comments_scheme');
			
			$comments_view_article = $this->params->get('comments_view_article');
			$comments_view_list = $this->params->get('comments_view_list');
			$comments_view_blog = $this->params->get('comments_view_blog');
			
			$comment_show = false;
			if(isset($article->sectionid))
			{
				if( is_array($comment_sections) )
				{	foreach($comment_sections as $id)
					{ if($id == $article->sectionid) { $comment_show = true; } }
				}
				else{
					if($comment_sections == $article->sectionid) { $comment_show = true; }	
				}
			}
			if(isset($article->category))
			{
				if( is_array($comment_categories) )
				{	foreach($comment_categories as $id)
					{ if($id == $article->category) { $comment_show = true; } }
				}
				else{
					if($comment_categories == $article->category) { $comment_show = true; }	
				}
			}
			
			//After checking categories and sections reset to fasle is not in articel view
			
			$version = new JVersion;
        	$joomla = $version->getShortVersion();
        	if(substr($joomla,0,3) == '1.6'){
				//will be adding ACL to do this,untill then remove it.
				$hasAccess = true;	
			}else{
				$user = JFactory::getUser();
				if($comments_access == '29'){
					$hasAccess = true;
				}elseif($comments_access == '30'){
					if(($user->gid == '23') || ($user->gid == '24') || ($user->gid == '25'))
						$hasAccess = true;
				}
				else{
					if($user->gid >= $comments_access)
						$hasAccess = true;
				}
					
				if(($comments_access == $user->gid) || ($comments_access == '29') )
					$hasAccess = true;
			}
			
			if($comments_show_on == 'all')
				$comment_show = true;
			
		
			if($comment_show && $hasAccess ){
				$lang =& JFactory::getLanguage();
				$lang->load( 'plg_content_myApiComment', JPATH_ADMINISTRATOR );
				
				$comment_box = '<fb:comments app_id="'.$facebook->getAppId().'" migrated="1" xid="'.$xid.'" url="'.$commentURL.'" numposts="'.$comments_numposts.'" width="'.$comments_width.'" colorscheme="'.$comments_scheme.'"></fb:comments>';
				
				$comment_link = "<a id='".$xid."commentLink' class='show' href='#'>".JText::_('ADD_COMMENT')."</a>";
				
				$jsCommentBox = "<fb:comments app_id=\'".$facebook->getAppId()."\' migrated=\'1\' xid=\'".$xid."\' url=\'".$commentURL."\' numposts=\'5\' width=\'693\'></fb:comments>";
				$modalJs = "window.addEvent('domready',function(){ $('".$xid."commentLink').addEvent('click',function(){ myApiModal.open(\"".JText::_('COMMENT_PROMPT')."\",null,'".$jsCommentBox."'); }); });";
				$injectJs = "window.addEvent('domready',function(){ $('".$xid."commentLink').addEvent('click',function(){ if(this.hasClass('show')){ this.removeClass('show'); this.setText('".JText::_('HIDE_COMMENTS')."'); var delBox = new Element('div', {'id': '".$xid."delBox'}); delBox.setHTML('".$jsCommentBox."'); delBox.injectInside(this.getParent()); FB.XFBML.parse($('".$xid."delBox'));  } else {  this.setText('".JText::_('ADD_COMMENT')."'); this.addClass('show'); $('".$xid."delBox').remove(); } });  });";
				
				if(JRequest::getVar('view','','get') == 'article' || JRequest::getVar('view','','get') == 'item'){	
					$viewType = 'article';
				
					//Only add noscript comments for article view
					$cache = & JFactory::getCache('plgContentmyApiComment - Comments for SEO');
					$cache->setCaching( 1 );
					$cache->setLifeTime(60*60*24*2);
					$comments = $cache->call( array( 'plgContentmyApiComment', 'getComments'),$xid);
					$article->text .= "<noscript><h3>Comments for ".$article->title."</h3>".$comments."</noscript>";
				}elseif((JRequest::getVar('layout','','get') == 'blog') || (JRequest::getVar('view','','get') == 'frontpage') || (JRequest::getVar('view','','get') == 'latest')){
					$viewType = 'blog';
				}else{
					$viewType = 'list';
				}
				
				$version = new JVersion;
        		$joomla = $version->getShortVersion();
        		if(substr($joomla,0,3) == '1.6'){
					require_once(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'myApiConnect'.DS.'myApiDom.php');
				}else{
					require_once(JPATH_SITE.DS.'plugins'.DS.'system'.DS.'myApiDom.php');
				}
				
				$dom = new simple_html_dom();
				$content = (isset($article->text) && $article->text != '') ? $article->text : $article->introtext;
				$dom->load($content);
				$tableEl = $dom->find('.myApiShareBottom',0);
				if(!$tableEl){
					$table = '<table class="myApiShareBottom myApiShareTable"></table>';
					$content = $content.$table;
					$dom->load($content);
				}
				
				switch($this->params->get('comments_view_'.$viewType)){
					case 1:
						$commentEl = $comment_box;	
					break;
					
					case 2:
						$commentEl = $comment_link;
						$doc->addScriptDeclaration($modalJs);	
						plgContentmyApiComment::addFbJs($xid);
					break;
					
					case 3:
						$commentEl = $comment_link;
						$doc->addScriptDeclaration($injectJs);
						plgContentmyApiComment::addFbJs($xid);
					break;
					
					default:
						$commentEl = NULL;
					break;
						
				}
				
				if(!is_null($commentEl)){
					$tr = '<tr class="myApiComments"><td class="myApiCommentsCell">'.$commentEl.'</td></tr>';
					$row = $dom->find('.myApiShareBottom',0);
					$row->innertext .= $tr;
					
					if(isset($article->text) && $article->text != ''){
						 $article->text = $dom->save();
					}else{
						$article->introtext = $dom->save();
					}
				}
				$dom->clear(); unset($dom);
			}
		}

	}
	
	function addFbJs($xid){
		$version = new JVersion;
   	 	$joomla = $version->getShortVersion();
    	$vnum = substr($joomla,0,3);
		
		global $fbAsyncInitJs;
		$facebook = plgSystemmyApiConnect::getFacebook();
		$app_id = $facebook->getAppId();
		$fbAsyncInitJs .= ($vnum == '1.5') ? "var query".$xid." = FB.Data.query('select id from comment where xid=\"$xid\" ; '); query".$xid.".wait(function(result) { if(result.length > 0){ $('".$xid."commentLink').setHTML('".JText::_('ADD_COMMENT')." ('+result.length+')'); } });" : "var query".$xid." = FB.Data.query('select id from comment where xid=\"$xid\" ; '); query".$xid.".wait(function(result) { if(result.length > 0){ $('".$xid."commentLink').set('html','".JText::_('ADD_COMMENT')." ('+result.length+')'); } });";	
	}

    function bind( $array, $ignore = '' )
    {
        if (key_exists( 'comment_sections', $array ) && is_array( $array['comment_sections'] )) {
                $array['comment_sections'] = implode( ',', $array['comment_sections'] );
        }
		 if (key_exists( 'comment_categories', $array ) && is_array( $array['comment_categories'] )) {
                $array['comment_categories'] = implode( ',', $array['comment_categories'] );
        }
	
        return parent::bind( $array, $ignore );
    }

}
