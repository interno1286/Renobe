<?php

class novella_IndexController extends SiteBaseController {
    
    function indexAction() {
        $this->setSkin('novella');
        $this->params['id'] = (int)$this->params['id'];
        
        $this->view->params = $this->params;
        $m = new novellasModel();
        $m->setViews($this->params['id']);
        $data=$m->getRow('id='.$this->params['id']);
        
        $translit = tools_string::translit($data['name']);
        
        if ($translit!==$this->params['name']) {
            header("Location: /novellas/{$translit}/{$data['id']}",true,301);
        }
        
        $this->view->meta_title = 'Ранобэ '.$data['name'].' / '.$data['name_original'].' читать онлайн';
        $this->view->meta_description = 'Новэлла '.$data['name'].' читать бесплатно онлайн';
        $this->view->meta_keywords = 'читать, ранобэ, '.$data['name_original'].' бесплатно, скачать, новеллы';
    }
    
    
    function listAction() {
        $this->setSkin('allnovellas');
        $page = $this->_getParam('page',1);
        
        $m = new novellasModel();
        $m->setCurrentPage($page);
        $m->setItemsPerPage(5);
        
        $novellas = $m->getList($this->params);
        
        $this->view->data = $novellas;
        $this->view->total_pages = $m->getTotalPagesCount($m->getAllListCount($this->params));
    }
    
    
    function ratingAction() {
        $this->ajax();
        $p = &$this->params;
        $n = (int)$p['n'];
        $r = (int)$p['r'];
        
        $lm = ormModel::getInstance('public','likes');
        
        if ($lm->get('id',"novella_id=$n and user_ip='{$_SERVER['REMOTE_ADDR']}' and created>now()-interval '24 hours'")) {
            
            $this->view->refer = $_SERVER['HTTP_REFERER'];
            $this->renderTplToContent('already_like.tpl');
        }else {

            $m=new novellasModel();

            $fields = [
                -1  => 'likes_minus',
                0   => 'likes_neutral',
                1   => 'likes_plus'
            ];

            $m->updateItem([
                $fields[$r] => new Zend_Db_Expr("coalesce(".$fields[$r].",0)"."+1")
            ],'id='.$n);
            
            $lm->newItem([
                'novella_id'    => $n,
                'user_ip'       => $_SERVER['REMOTE_ADDR'],
                'rate'          => $r
            ]);

            $this->view->content = 'ok';

            $this->_redirect($_SERVER['HTTP_REFERER']);
        }
    }
    
    function favoriteAction() {
        $this->ajax();
        
        try {
            $error = '';
            $message = '';
            
            if (!$this->user_data->id) throw new Exception('Необходимо авторизоваться');
            
            $m = ormModel::getInstance('public','favorites');
            $id = (int)$this->params['id'];
            
            $exists = $m->get('id',"user_id={$this->user_data->id} and novella_id=$id");
            
            if ($exists) {
                $m->del('id='.$exists);
                $message = 'Удалено из избранного';
            }else {
                $m->newItem([
                    'user_id'       => $this->user_data->id,
                    'novella_id'    => $id
                ]);
                
                $message = 'Добавлено в избранное';
            }
                    
        }catch (Exception $e) {
            
        }
        $this->jsonAnswer([
            'error' => $error,
            'message'   => $message
        ]);
    }
}
