<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GalleryController
 *
 * @author chenz_000
 */
class news_GalleryController extends SiteBaseController {
	
	
	function initModel() {
		$this->model = new newsgalleryModel();
	}
	
	
	function seeditgalleryAction() {
		
	}
	
	
	function uploadAction() {
		$this->needAdminRights();
		
		if ($this->isPost()) {
			
			$dir = $this->config->news_gallery->dir.$this->params['id'];
			
			if (!is_dir($dir))
				mkdir($dir,0777,true);
			
			
			//foreach ($_FILES['file'] as $f) {
				
				$f = $_FILES['file'];
			
				
				
				create_thumb(
						$this->config->news_gallery->big->width, 
						$this->config->news_gallery->big->height, 
						$f['tmp_name'], 
						$dir.'/big_'.$f['name'], 
						90, 
						false
				);
				
				
				create_thumb(
						$this->config->news_gallery->small->width, 
						$this->config->news_gallery->small->height, 
						$f['tmp_name'], 
						$dir.'/small_'.$f['name'], 
						70, 
						true
				);
				
				$this->model->add($f['name'],$this->params['id']);
			//}
			
		}
	}
	
	
	function removeAction() {
		$this->useAjaxView();
		$this->needAdminRights();
		
		$image_info = $this->model->getInfoById($this->params['id']);
		
		unlink($this->config->news_gallery->dir.$image_info['news_id'].'/small_'.$image_info['file']);
		unlink($this->config->news_gallery->dir.$image_info['news_id'].'/big_'.$image_info['file']);
		
		$this->model->pq('delete','news.gallery','id='.(int) $this->params['id']);
		
		$this->goBack();
	}
	
	
}
