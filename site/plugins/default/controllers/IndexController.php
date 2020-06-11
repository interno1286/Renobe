<?php

/**
 * Description of SiteBaseController
 *
 * @author chenzya
 */
class IndexController extends SiteBaseController {

	function initModel() {
	}

	function initController() {
	}


	function indexAction(){
            
            $this->view->total_pages = $total_pages = ormModel::getInstance('chaptersModel')->getLastChaptersPagesCount(10);
            
            if ($this->_getParam('p',1)>$total_pages)
                    $this->_redirect('/404');
            
	}
        
	function aboutAction(){
                $this->setSkin('pages');
                $this->renderTplToContent('about.tpl');
	}
        
	function faqAction(){
		$this->setSkin('pages');
                $this->renderTplToContent('faq.tpl');
	}
        
        function newsAction() {
            $this->setSkin('pages');
            
            
        }

        function shownewsAction() {
            $this->setSkin('pages');
            $this->params['id'] = (int)$this->params['id'];
            $this->view->params = $this->params;
        }
        
        
        function searchAction() {
            $this->setSkin('pages');
            
            $nm=new novellasModel();
            $pm=new paragraphModel();
            $cm=new chaptersModel();
            
            $this->params['t'] = $t = preg_replace('#([\\=."\'%])#ui', '', $this->params['t']);
            
            $elems = [];
            $types = explode('|',$this->params['types']);
            
            if (in_array('n', $types)) 
                $elems = array_merge($elems, $nm->search($t));
            
            if (in_array('c', $types))
                $elems = array_merge($elems, $cm->search($t));
            
            if (in_array('p', $types))
                $elems = array_merge($elems, $pm->search($t));
            
            if (sizeof($types)>0)
                $this->view->stypes = $types;
            
            $this->view->results = $elems;
            
        }
        
        function loadlatestAction() {
            $this->setSkin('main');
            $this->ajax();
            
            $this->renderTplToContent('latest_chaps.tpl');
            
        }
        
        function notfoundAction() {
            $this->setSkin('pages');
            
            header("HTTP/1.0 404 Not Found");
            
            $this->view->content = '<h1>Страница не найдена</h1>';
        }
}
