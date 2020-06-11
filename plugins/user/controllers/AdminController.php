<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AdminController
 *
 * @author chenz_000
 */
class user_AdminController extends SiteBaseController {
    
    function initController() {
        $this->needAdminRights();
        site::enabledJsVarTransport();
    }
    
    function initModel() {
        $this->model = new userModel();
    }
    
    function listAction() {

        $this->needAdminRights();
        

        $this->view->addScript($this->config->url->base.'plugins/user/public/js/script.js');
        
        $page = (isset($this->params['page'])) ? (int) $this->params['page'] : 1;

        $s = $this->_getParam('s','');
        
        $this->model->setCurrentPage($page);

        $this->model->setItemsPerPage(20);

        $this->view->total_pages = $this->model->getTotalPagesCount($this->model->getAllUsersCount($s));

        $this->view->users = $this->model->getUsers($s);
        
        if ($this->isAjaxRequest()) {
            $this->ajax ();
            $this->renderTplToContent('admin_users_table.tpl');
        }
        
    }
    
    
    
    function blockAction() {
        $this->useAjaxView();
        
        $m = new userModel();
        
        $state = $m->get('blocked','id='.(int)$this->params['user_id']);
        
        $data = array(
            'blocked'   => new Zend_Db_Expr(($state) ? 'false' : 'true')
        );
        
        $m->updateItem($data, 'id='.(int)$this->params['user_id']);
        
        $this->view->content = Zend_Json::encode(array(
            'error' => '',
            'id'    => $this->params['user_id'],
            'blocked'   => !$state
        ));
    }
    
    function loginAction() {
        $um = new userModel();
        
        $user_info = $um->getRow('id='.(int)$this->params['id']);
        
        $this->user_data->prev_admin_user_id = $this->user_data->id;
        $this->user_data->prev_admin_user_role = 'admin';
        
        $this->user_data->id = $user_info['id'];
        $this->user_data->role = $user_info['user_type'];
        $this->user_data->user_type = $user_info['user_type'];
        $this->user_data->fio = $user_info['fio'];
        $this->user_data->email = $user_info['email'];
        $this->user_data->logged = true;
        
        $this->user_data->nickname = $user_data['nickname'];
        $this->user_data->law_type = $user_data['who'];
        $this->user_data->balance = $user_data['balance'];
        
        
        if (isset($this->params['back_url']))
            $this->user_data->back_url = $this->params['back_url'];
        
        $url = (isset($this->params['url'])) ? $this->params['url'] : '/office';
        
        $this->_redirect($url);
    }
    
    function rolesetAction() {
        $this->useAjaxView();
        try {
            $error = '';
            
            $um = new userModel();
            
            //if (!in_array($this->params['role'],array('admin','user'))) throw new Exception('Странный тип '.$this->params['role']);
            
            $um->updateItem(array('user_type'=>$this->params['role']), 'id='.(int)$this->params['user_id']);
                    
        }catch (Exception $e) {
            $error = $e->getMessage();
        }
        
        $this->view->content = Zend_Json::encode(array(
            'error' => $error
        ));
    }
    
    function delAction() {
        $this->useAjaxView();
        try {
            $error = '';
            
            $um = new userModel();
            
            if (!$um->del('id='.(int)$this->params['id'])) throw new Exception($um->last_error);
                    
        }catch (Exception $e) {
            $error = $e->getMessage();
        }
        
        $this->view->content = Zend_Json::encode(array(
            'error' => $error
        ));
        
        
    }
}
