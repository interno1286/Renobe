<?php

/**
 * @DESC Класс новостей. Списки новостей принадлежат определённому OWNERу
 * @DESC таким образом новости могут быть у любой сущности
 */


class news {

	public $id;
	protected $type=false;
	protected $owner;

	protected $caller;
	protected $act;

	public $y=false,$m=false,$d=false;

	public $params;

	protected $model;
	protected $view;
	protected $user_data;

	protected $items_per_page = 5;
	protected $current_page = 1;

	protected $newsTypes;

	function __construct(&$caller, $owner='default', $params=false) {

		$this->initVars($caller,$params,$owner);

		$this->view->assign('owner',$this->owner);
		$this->view->assign('act',$this->caller->action);

		$this->initModel();

		$this->initDate();


		$this->getSettings();
		$this->setDate();
		$this->linkAdminScripts();

		$this->initNewsTypes();

	}


	function setOwner($owner) {
		$this->owner = $owner;
		$this->model->setOwner($owner);
	}


	function initModel(&$model=false) {
		$this->model = ($model) ? $model : new newsModel($this->owner, $this->params);
	}


	function initNewsTypes() {

		$this->newsTypes = array(
			'news'=>'Новость',
			'exhibition'=>'Выставка'
		);

		$this->view->newsTypes = $this->newsTypes;
	}


	function initVars(&$caller,$params,$owner='default') {

		$this->setParams($params);

		if ($caller instanceof CmsBaseController) {
			$this->caller = &$caller;
			$this->owner = $owner;
			$this->user_data = $this->caller->user_data;
			$this->config = $this->caller->config;
			$this->view = &$this->caller->view;
		}else {
			$this->caller = false;
			$this->owner = $owner;
			$this->view = Zend_Registry::get('view');
			$this->user_data = Zend_Registry::get('user_data');
			$this->config = Zend_Registry::get('cnf');
		}
	}


	function setParams($params) {
		$this->params = $params;
	}


	function initDate() {
		if ($this->params!==false) {
			if (isset($this->params["ondate"])) $this->setDate($this->params["ondate"]);
			$this->setPage((isset($this->params["page"])) ? $this->params["page"] : 1);
		}
	}

	function start() {
		if (!isset($this->params['act'])) $this->params['act']='showNews';
		switch ($this->params['act']) {
			case 'add':
			case 'edit':
				$this->addNewsItem();
				break;

			case 'del':
				$this->delete($this->params['id']);
				break;

			case 'show':
				$this->showItem($this->params['id']);
				break;


			case 'addcomment':
				$this->saveComment($this->params);
				$this->caller->doRedirect($_SERVER['HTTP_REFERER']);
				break;

			default:
				$this->showNews();
				break;

		}
	}



	function delete($id) {
		$e_data = $this->model->getEvent($id);

		if ($e_data['video']) {
			unlink($this->config->news_upload_video_folder."/{$e_data['video']}");
		}

		if ($e_data['audio']) {
			unlink($this->config->news_upload_video_folder."/{$e_data['video']}");
		}

		$this->delEvent($this->params['id']);
		$this->caller->doRedirect($_SERVER['HTTP_REFERER']);
	}

	function getLastNews($count=3) {
		return $this->model->getLastNews($count);
	}


	function addNewsItem() {
		if ($this->caller->getRequest()->isPost()) {
			if (!$this->model->savenews($this->params,$error)) throw new Exception($error);
		}else {
			$this->caller->getAjaxView();
			if (isset($this->params['id'])) {
				$this->view->assign('id',$this->params['id']);
				$this->view->assign('current_data',$this->model->getEvent ($this->params['id']));
			}

			$this->caller->renderTplToContent('seedit/news.tpl');
		}
	}



	function linkAdminScripts() {
		if ($this->user_data->role=='admin') {
			$this->view->addScript(array(
				'/plugins/news/public/js/admin.js',
				'/cms/public/js/ckeditor/ckeditor.js',
				'/cms/public/js/jquery/jquery.ui.datepicker-ru.js'
			));
			// Общие функции (календарь)
			//$this->view->addStyle('comments_editor.css');
			$is_editable = true;
		}else $is_editable = false;

		$this->view->assign("is_editable",$is_editable);
	}


	function getSettings() {
		/******** Количество новостей на страницу из базы */
		if (isset($this->caller->config->settings->news->items_per_page)) {
			$this->items_per_page = $this->caller->config->settings->news->items_per_page;
			$this->model->items_per_page = $this->caller->config->settings->news->items_per_page;
		}
		/**********************************************************/
	}

	public function setDate($date="") {
		if ($date=='') {
			$this->y=date('Y');
			$this->m=date('n');
			$this->d=date('d');
		}else {
			list($y,$m,$d) = explode('-',$date);
			$this->y = intval($y); $this->m = intval($m);	$this->d = intval($d);
			$this->model->date = "$y-".str_pad($m,2,"0",STR_PAD_LEFT)."-".str_pad($d,2,"0",STR_PAD_LEFT);
		}
	}


	public function setPage($page) {
		$this->current_page = intval($page);
		$this->model->current_page = $this->current_page;
		$this->view->current_page=$this->current_page;
	}


	public function showNews() {
		$this->view->assign('pages_count',$this->model->getPagesCount());
		$this->view->assign('news', $this->model->getNews());
		$this->view->assign('news_content',site::renderTpl('news/news_list.tpl'));
	}

	public function showItem($id) {
		$event =  $this->model->getEvent($id);

		if (sizeof($event)<3) {
			$this->caller->doRedirect('/');
		}

		$this->view->assign('debug',Zend_Debug::dump($event));
		$this->view->assign('event', $event);
		//$this->view->assign('comments', $this->model->getComments($id));
		$this->view->assign("edit",$id);

		$this->view->assign('news_content',site::renderTpl('news_details.tpl'));

	}

	public function show() {
		if ($this->caller->getRequest()->isPost()) {
			if ($this->caller->hasParam('add_comment'))
				$this->saveComment($this->caller->getAllParams());
		}

		if ($this->caller->hasParam('edit'))
			$this->showEvent($this->caller->getParam('edit'), true);
		elseif ($this->caller->hasParam('del')) {
			$this->delEvent(intval($this->caller->getParam('del')));
			$this->caller->redirect("/{$this->caller->controller}/eventlist/id/{$this->caller->id}");
		}else
			$this->showEvent($this->caller->getParam('view'), false);
	}

	public function showList() {
		if ($this->caller->getRequest()->isPost()) {
			if ($this->caller->hasParam('save_event'))
				$this->saveEvent($this->caller->getAllParams());
		}

		$this->showEvents();
	}

	public function saveComment($params) {
		if (!$this->user_data->id) return;
		$this->model->saveComment($params);
	}

	public function saveEvent($params) {
		
		$admin_role = ($this->caller->config->admin_role) ? $this->caller->config->admin_role : 'admin';
		
		if ($this->user_data->role!=$admin_role) return;
		$this->model->saveEvent($params);
	}

	public function delEvent($id) {
		
		$this->model->delEvent($id);
		delPhoto("news",$id);
	}


	public function getCalendarEvents() {
		return $this->model->getCalendarEvents();
	}


	function getHumanDate($date) {
		$date_timestamp = strtotime($date);

		$difference = time() - $date_timestamp;

		switch (true) {
			case ($difference<86400):

				$sufix = array(
					'1'=>'',
					'2'=>'а',
					'3'=>'а',
					'4'=>'а',
					'5'=>'ов',
					'6'=>'ов',
					'7'=>'ов',
					'8'=>'ов',
					'9'=>'ов',
					'0'=>'ов'
				);

				$period = floor($difference / 3600);

				if ($period>0) {
					$index = (strlen($period)>1) ? mb_substr($period, strlen($period)-1) : $period;
					$ret = $period.' час'.$sufix[$index];
				}else $ret = "меньше часа";

				return $ret;
				break;

			case ($difference<604800):

				$sufix = array(
					'1'=>'день',
					'2'=>'дня',
					'3'=>'дня',
					'4'=>'дня',
					'5'=>'дней',
					'6'=>'дней',
					'7'=>'дней',
					'8'=>'дней'
				);

				$period = floor($difference / 86400);

				return $period.' '.$sufix[$period];
				break;

			case ($difference<2419200):

				$period = floor($difference / 604800);

				$sufix = ($period>1) ? 'и' : 'ю';

				return $period.' недел'.$sufix;
				break;


			case ($difference<31536000):
				$period = floor($difference / 2419200);

				$sufix = array(
					1=>'',
					2=>'а',
					3=>'а',
					4=>'а',
					5=>'ев',
					6=>'ев',
					7=>'ев',
					8=>'ев',
					9=>'ев',
					10=>'ев',
					11=>'ев',
					12=>'ев',
				);

				return $period.' месяц'.$sufix[$period];
				break;

			default:
				$period = floor($difference / 31536000);

				$sufix = array(
					'1'=>'год',
					'2'=>'года',
					'3'=>'года',
					'4'=>'года',
					'5'=>'лет',
					'6'=>'лет',
					'7'=>'лет',
					'8'=>'лет',
					'9'=>'лет',
					'0'=>'лет'
				);


				return $period.' '.$sufix[$period{strlen($period)-1}];
				break;
		}
	}



	static function attachImagesToNewsArray(&$data) {
		foreach($data as &$one_data){
			$one_data["image"] = tools_photo::getPhoto("medium","news",$one_data['id']);
			$one_data["small_image"] = tools_photo::getPhoto("small","news",$one_data['id']);
            $one_data["medium_image"] = tools_photo::getPhoto("medium","news",$one_data['id']);
			$one_data["micro_image"] = tools_photo::getPhoto("micro","news",$one_data['id']);
            $one_data["big_image"] = tools_photo::getPhoto("big","news",$one_data['id']);
		}
	}
}

