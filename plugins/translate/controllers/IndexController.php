<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IndexController
 *
 * @author belov
 */
class Translate_IndexController extends SiteBaseController {
	
    function initController() {
		$this->setSkin('manager');
    }
		
    function initModel() {
		$this->model = new translateModel();
    }
	
    function listAction(){
        $lng = $this->params['lng'];
        $lng = empty($lng)?'ru':$lng;
        $this->view->lng = $lng;
        $this->view->items = $this->model->getItems($lng);

        $lngs = $this->model->getLanguages();
        $res = array();
        foreach($lngs as $item){
            $res[$item['code']] = $item['name'];
        }
        $this->view->languages = $res;
        $this->view->mess = '';//print_r($this->view->languages, true);
	}

    function editAction(){
        $this->getAjaxView();
        $code = $this->params['code'];
        $new_code = $this->params['new_code'];
        $txt = $this->params['txt'];
        $lng = $this->params['lng'];
        if (!empty($new_code)) {
            $this->model->editItemCode($lng, $code, $new_code);
        }else{
            $this->model->editItem($lng, $code, $txt);
        }
        
        self::flushCache();
        
        $this->view->content = "OK";
    }

    function seeditLanguage(){
        $code = $this->params['code'];
        $name = $this->params['name'];
        $this->model->addLanguage($code, $name);
    }

    function segetdataItem(){
        $this->view->lng = $this->params['objectid'];
    }

    function seeditItem(){
        $code = $this->params['code'];
        $txt = $this->params['txt'];
        $lng = $this->params['lng'];
        $this->model->addItem($lng, $code, $txt);
    }
    
    
    function setAction() {
        $this->useAjaxView();
        
        if (isset($this->params['lng'])) {
            $lng = preg_replace('#([^a-z])#', '', $this->params['lng']);
            
            $available_lng = $this->model->getLanguages();
            
            foreach ($available_lng as $av) {
                if ($av['code']==$lng) {
                    translate::setLanguage($lng);
                    $this->user_data->language = $lng;
                    break;
                }
            }
        }
        
        $this->_redirect($_SERVER['HTTP_REFERER']);
    }
    function deleteAction(){
        $this->useAjaxView();
        if (isset($this->params['item']) && isset($this->params['lng'])){
            $item = $this->params['item'];
            $lng = $this->params['lng'];
            $this->model->deleteItem($lng , $item);
        }
    }
}