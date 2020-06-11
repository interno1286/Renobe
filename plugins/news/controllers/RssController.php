<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RssController
 *
 * @author chenz_000
 */
class News_RssController extends SiteBaseController {
	
	
	function initModel() {
		
		$owner = (isset($this->params['owner'])) ? $this->params['owner'] : 'default';
		
		$owner = preg_replace('#([^a-z0-9_-])#','',$owner);
		
		$this->model = new newsModel($owner);
	}
	
		
	
	function indexAction() {
		$this->disableLayout();
		
		//header("Content-Type: application/rss+xml; charset=UTF-8");
		header("Content-Type: text/xml; charset=utf-8");
		
		$this->model->setItemsPerPage(20);
		
		$news = $this->model->getNews();
		
		$rssfeed = '<?xml version="1.0" encoding="UTF-8"?>'."\r\n";
		
		$rssfeed .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">'."\r\n";
		$rssfeed .= '<channel>'."\r\n";
		//$rssfeed .= '<atom:link href="http://'.$_SERVER['SERVER_NAME'].'/news/last.xml" rel="self" type="application/rss+xml" />'."\r\n";
		$rssfeed .= '<managingEditor>support@permprofi.ru (Admin)</managingEditor>'."\r\n";
		$rssfeed .= '<title>Лента новостей. Профессионал.</title>'."\r\n";
		$rssfeed .= '<link>http://'.$_SERVER['SERVER_NAME'].'</link>'."\r\n";
		$rssfeed .= '<description><![CDATA[ Новости. Профессионал (г. Пермь)]]></description>'."\r\n";
		$rssfeed .= '<language>ru</language>'."\r\n";
		$rssfeed .= '<copyright>Copyright (C) '.date('Y').' '.$_SERVER['SERVER_NAME'].'</copyright>'."\r\n";

		foreach ($news as $item) {
			$rssfeed .= '<item>'."\r\n";
			
			$header = ($item['header']) ? $item['header'] : $item['description'];
			
			$rssfeed .= '<title><![CDATA[' . $header . ']]></title>'."\r\n";
			$rssfeed .= '<guid isPermaLink="true">http://'.$_SERVER['SERVER_NAME'].'/news/show/'. $item['id'] . '</guid>'."\r\n";
			$rssfeed .= '<link>http://'.$_SERVER['SERVER_NAME'].'/news/show/'. $item['id'] . '</link>'."\r\n";
			$rssfeed .= '<description><![CDATA[' . $item['text'] . ']]></description>'."\r\n";
			$rssfeed .= '<pubDate>' . date("D, d M Y H:i:s O", strtotime($item['create_date'])) . '</pubDate>'."\r\n";
			$rssfeed .= '<author>info@permprofi.ru (Admin)</author>'."\r\n";
			$rssfeed .= '</item>'."\r\n";
			
		}

		$rssfeed .= '</channel>'."\r\n";
		$rssfeed .= '</rss>'."\r\n";

		echo $rssfeed;
	}
	
	
}
