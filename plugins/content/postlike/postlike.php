<?php

// author    Sergey Pronin
// copyright Copyright (C) 2013 seregin-pro.ru. All Rights Reserved.
// @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
// Websites: http://seregin-pro.ru

// No direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');


class plgContentPostLike extends JPlugin
{
	protected $article_id;
	
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
		
		$this->view = JRequest::getCmd('view');
	}
public function onContentBeforeDisplay($context, &$article, &$params, $limitstart = 1){
	if (strpos($context, 'com_content') !== false) {
			$this->article_id = $article->id;
			$hide  = $this->params->get('hide', 1);
	if($this->params->get('display') == 0){ // После заголовка
		if ( $hide == 0 || $this->view == 'article'){
				return $this->ContentPostLike($article, $params);
		}
	}
	else{		
				if ($this->view == 'article') {
			        $article->text .= $this->ContentPostLike($article, $params);
				}
				elseif ( $this->params->get('hide') == 0 ) { // Отображать после текста в блоге
			        $article->introtext .= $this->ContentPostLike($article, $params);
				}
		}
	}
}
	protected function ContentPostLike(&$article, &$params)
	{ 		
		$rating_dislike=0;
		$rating_like = 0;
		$html='';

		if ($params->get('show_vote'))
		{
			$db	= JFactory::getDBO();
			$query='SELECT * FROM #__content_postlike WHERE content_id='.$this->article_id;
			$db->setQuery($query);
			$vote=$db->loadObject();
		
			if($vote) {
				$rating_dislike = intval($vote->rating_dislike);
				$rating_like = intval($vote->rating_like);
			}
		
			$html .= $this->plgContentPostLikeStars( $this->article_id, $rating_dislike, $rating_like);
		}
		return $html;
 	}
	
  
protected function plgContentPostLikeStars( $id, $rating_dislike, $rating_like){
		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::root(true).'/plugins/content/postlike/assets/postlike.css');
		$rating  = 0;	
		global $plgContentPostLikeAddScript;
	 	if(!$plgContentPostLikeAddScript){ 
         	$document->addScriptDeclaration("
	function PLVote(select,id,i,total_count){
	var currentURL = window.location;
	var live_site = currentURL.protocol+'//'+currentURL.host;
	var msg = document.getElementById('msg_'+id);
	if(select == 0){			
		var info = document.getElementById('postlike_dis_'+id);
	}	
	if(select == 1){
		var info = document.getElementById('postlike_'+id);
	}
	var text = info.innerHTML;
	if (info.className != 'postlike-info voted') {
		var ajax=new XMLHttpRequest();
		ajax.onreadystatechange=function() {
		var response;
			if(ajax.readyState==4){		
					response = ajax.responseText;
					if(response=='thanks') msg.innerHTML = '".JTEXT::_('PLG_CONTENT_POSTLIKE_THANKS')."';
					if(response=='login') msg.innerHTML = '".JTEXT::_('PLG_CONTENT_POSTLIKE_LOGIN')."';
					if(response=='voted') msg.innerHTML = '".JTEXT::_('PLG_CONTENT_POSTLIKE_VOTED')."';
					
					if(response=='thanks'){
						text = '';
						var newtotal  = total_count+1;
						text += newtotal;
						info.innerHTML = text;
					}
			}
		}
ajax.open(\"GET\",
live_site+\"/plugins/content/postlike/assets/ajax.php?select=\"+select+\"&task=vote&user_rating=\"+i+\"&cid=\"+id,
true);
		ajax.send(0);
	}
	info.className = 'postlike-info voted';
}
			");
     		$plgContentPostLikeAddScript = 1;
		}
		
		$container = 'div';
		$class     = 'postlike';

		$spans = '';
		
		$j=1;
		
		
		
			$spans .= "
        <span class=\"like-button\" onclick=\"PLVote(0,".$id.",".$j.",".$rating_like.");\">".
					JTEXT::_('PLG_CONTENT_POSTLIKE_LIKE')."</span>";
		 $spans .= "<span class=\"postlike-info\" id=\"postlike_dis_".$id."\">".$rating_like."</span>";
		 
	  
	  $spans .= "
      <span class=\"dislike-button\" onclick=\"PLVote(1,".$id.",".$j.",".$rating_dislike.");\">".
					JTEXT::_('PLG_CONTENT_POSTLIKE_DISLIKE')."</span>";
		
	 	$html = "
<".$container." class=\"".$class."\">
    <span id=\"rating_".$id."\" class=\"current-rating\"></span>"
	.$spans."<span class=\"postlike-info\" id=\"postlike_".$id."\">";
				$html .= $rating_dislike;
 	 	$html .="</span><span class=\"msg-info\" id=\"msg_".$id."\"></span>";
 	 	$html .="</".$container.">";
	 	return $html;
 	}

	
}
