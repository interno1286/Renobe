<?php

/**
 *
 *
 * @author chenzya
 */
class News_IndexController extends SiteBaseController {

	//// Редактирование новостей выполнено по схеме StandartEdit http://cms.glenn.ru/docs/StandartEdit
	//// Контроллер служит исключительно для функционала редактирования
	//// Отображение новостей через смарти плагин newsBlockLast http://cms.glenn.ru/docs/NewsBlockLast

	protected $owner;

	function initController() {
		$this->setWorker();
		$this->setOwner();
	}
	
	function initModel() {
		$this->model = new newsModel();
        
        ////// DONT FORGET CHANGE THE WORKER MODEL!
        //if ($this->worker) {
        //    $this->worker->initModel($model);
        //}
	}
	
	function setWorker() {
		$this->worker = new news($this,$this->owner, $this->params);
	}
	
	protected function setOwner($owner='default') {
		$this->owner = (isset($this->params['news_owner'])) ? $this->params['news_owner'] : $owner;
		$this->worker->setOwner($this->owner);
		$this->model->setOwner($this->owner);
	}

	function doAction() {
		$this->needAdminRights();
		$this->worker->start();
	}

	function segetdataNews() {
		$this->params['id'] = $this->params['objectid'];

		$this->worker->setParams($this->params);
		$this->worker->addNewsItem();
	}


	function seeditNews() {
		$this->needAdminRights();

		$this->params['id'] = $this->params['objectid'];
		$this->worker->setParams($this->params);

		$this->worker->addNewsItem();
	}


	static function uploadMedia($id,$type='video') {

		if (is_uploaded_file($_FILES[$type]['tmp_name'])) {
			list($name, $ext) = explode_filename($_FILES[$type]['name']);

			$config = Zend_Registry::get('cnf');

			$config_elem = "news_upload_{$type}_folder";

			$name = gen_filename();

			$filename = "{$id}_{$name}.{$ext}";

			if (!is_dir($config->$config_elem))
				if (!mkdir($config->$config_elem,0777,true)) throw new Exception('Не могу создать папку для загруженного медиа файла');

			if (!move_uploaded_file($_FILES[$type]['tmp_name'], $config->$config_elem."/$filename")) throw new Exception('Не удается загрузить медиа файл');

			return $filename;
		}

		return false;
	}
	
	
	function showAction() {
		$event =  $this->model->getEvent($this->params['id']);
		
		if (!isset($event['id']))
			$this->_redirect('/'.$this->module);
		
		if ($event['video'])
			$this->view->addScript('/plugins/news/public/flowplayer/flowplayer-3.2.12.min.js');
		
		
		if ($this->edit_allowed) {
			$this->view->addScript('/plugins/news/public/dropzone/dropzone.js');
			$this->view->addStyle('/plugins/news/public/dropzone/css/basic.css');
		}
		
		$galleryModel = new newsgalleryModel();
		
		
		$images = $galleryModel->getImagesForNews($event['id']);
		
		$this->view->gallery_images = $images;
		
		if ($images) {
			$this->view->addStyle('/plugins/news/public/slick-master/slick/slick.css');
			$this->view->addScript('/plugins/news/public/slick-master/slick/slick.min.js');
			
			
			$this->view->addScript('/plugins/news/public/fancybox/source/jquery.fancybox.pack.js');
			$this->view->addStyle('/plugins/news/public/fancybox/source/jquery.fancybox.css');
		}
		
		
		$this->view->event = $event;
	}
	
	
	function listAction() {
		$this->view->assign('pages_count',$this->model->getPagesCount());
		$this->view->assign('news', $this->model->getNews());
	}
	
	
	function moveupAction() {
		$this->needAdminRights();
		
		$this->useAjaxView();
		
		$this->model->moveItemUp($this->params['item']);
		
		$this->view->content = Zend_Json::encode(array('error'=>''));
	}
	
	function movedownAction() {
		$this->needAdminRights();
		
		$this->useAjaxView();
		
		$this->model->moveItemDown($this->params['item']);
		
		$this->view->content = Zend_Json::encode(array('error'=>''));
	}
	
}
