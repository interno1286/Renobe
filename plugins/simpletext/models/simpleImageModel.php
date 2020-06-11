<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of simpleImageModel
 *
 * @author v0yager
 */
class simpleImageModel extends DbModel {
	
    
    public $controll_table = 'simple_image_data';
    
	static $instance = false;
	
	static function getInstance() {
		if (!self::$instance)
			self::$instance = new simpleImageModel();
		
		return self::$instance;
	}
	
	
	function updateLink($image_path,$link) {
		
		$path_hash = md5($image_path);
		
		$db_data = array(
			'link'	=> $link
		);
		
		if ($this->s_fetchOne("select src_hash from simple_image_data where src_hash='$path_hash'")) {
			$this->pq('update','simple_image_data',$db_data,"src_hash='$path_hash'");
		}else {
			$db_data['src_hash'] = $path_hash;
			$this->pq('insert','simple_image_data',$db_data);
		}
		
	}
	
	
	function getData($image_path) {
		$path_hash = md5($image_path);
		
		return $this->s_fetchRow("select * from simple_image_data where src_hash='$path_hash'");
	}
	
    function saveCustomFullData($image_path,$full_im) {
        $path_hash = md5($image_path);
        
        $db_data = array(
            'custom_full_version'   => $full_im,
            'src_hash'              => $path_hash
        );
        
        $this->pq('insert','simple_image_data',$db_data);
    }
}
