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
	function onPrepareContent( &$article, &$params, $limitstart )
	{
		//this may fire fron a component other than com_content
		if((@$article->id != '') && (@$_POST['fb_sig_api_key'] == ''))
		{
			$xid = urlencode('articlecomment'.$article->id);
			require_once(JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
				
			$link = JRoute::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catslug, $article->sectionid));
			$u =& JURI::getInstance( JURI::base() );
			$commentURL = 'http://'.$u->getHost().$link;
			
			$base = JURI::base();
			$doc = & JFactory::getDocument();
			JHTML::_('behavior.mootools');
			
			$plugin = & JPluginHelper::getPlugin('content', 'myApiComment');

			// Load plugin params info
			$myapiparama = new JParameter($plugin->params);
					
			$comment_sections = $myapiparama->get('comment_sections');
			$comment_categories = $myapiparama->get('comment_categories');
			$comments_show_on = $myapiparama->get('comments_show_on');
			$comments_access = $myapiparama->get('comments_access');
			$comments_width = $myapiparama->get('comments_width');
			$comments_numposts = $myapiparama->get('comments_numposts');
			$comments_publish_feed = $myapiparama->get('comments_publish_feed');
			$comments_color_scheme = $myapiparama->get('comments_color_scheme');
			
			$comments_view_article = $myapiparama->get('comments_view_article');
			$comments_view_list = $myapiparama->get('comments_view_list');
			$comments_view_blog = $myapiparama->get('comments_view_blog');
			
			$comment_show = false;
			global $facebook;
			
			if($article->sectionid != '')
			{
				if( is_array($comment_sections) )
				{	foreach($comment_sections as $id)
					{ if($id == $article->sectionid) { $comment_show = true; } }
				}
				else{
					if($comment_sections == $article->sectionid) { $comment_show = true; }	
				}
			}
			if($article->category != '')
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
			//if(JRequest::getVar('view') != 'article') { $comment_show = false; }
			
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
			
			if($comments_show_on == 'all')
				$comment_show = true;
			
			
			if($comment_show && $hasAccess )
			{
				
				$comment_box = '<fb:comments app_id="'.$facebook->getAppId().'" migrated="1" xid="'.$xid.'" href="'.$commentURL.'" numposts="'.$comments_numposts.'" width="'.$comments_width.'" publish_feed="'.$comments_publish_feed.'" colorscheme="'.$comments_color_scheme.'"></fb:comments>';
				
				$comment_link = "<br /><a id='".$xid."commentLink' class='' href='#'>Add a comment</a><br />";
				
				$js = "window.addEvent('domready',function(){ $('".$xid."commentLink').addEvent('click',function(){ myApiModal.open(\"Leave a comment.\",null,\"<fb:comments app_id=\'".$facebook->getAppId()."\' migrated=\'1\' xid=\'".$xid."\' href=\'".$commentURL."\' numposts=\'5\' width=\'700\' publish_feed=\'".$comments_publish_feed."\' colorscheme=\'".$comments_color_scheme."\'></fb:comments>\"); }); });";
				
				if(JRequest::getVar('view','','get') == 'article'){
					//article	
					if($comments_view_article == 0){
						//box
						$article->text = $article->text.$comment_box;
					}else{
						//link
						$article->text = $article->text.$comment_link;
						$doc->addScriptDeclaration($js);
					}
				}elseif((JRequest::getVar('layout','','get') == 'blog') || (JRequest::getVar('view','','get') == 'frontpage')){
					//blog		
					if($comments_view_blog == 0){
						//box
						$article->text = $article->text.$comment_box;
					}else{
						//link
						$article->text = $article->text.$comment_link;
						$doc->addScriptDeclaration($js);
					}
				}else{
					//must be list
					if($comments_view_list == 0){
						//box
						$article->text = $article->text.$comment_box;
					}else{
						//link
						$article->text = $article->text.$comment_link;
						$doc->addScriptDeclaration($js);
					}
				}
			}
		}

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
