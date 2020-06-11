<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of galleryModel
 *
 * @author chenz_000
 */
class newsgalleryModel extends DbModel {
	
	
	function add($file, $news_id) {
		
		$db_data = array(
			'news_id'	=> (int) $news_id,
			'file'		=> $file
		);
		
		$this->pq('insert','news.gallery',$db_data);
		
	}
	
	function getImagesForNews($id) {
		$sql = "
			
			select
				*
			from
				news.gallery
			where
				news_id=".(int) $id;
		
		return $this->s_fetchAll($sql);
	}
	
	
	function getInfoById($id) {
		$sql = "
			select
				*
			from
				news.gallery
			where
				id=".(int) $id;
		
		return $this->s_fetchRow($sql);
	}
}
