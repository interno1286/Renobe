<?php

class novella_ChapterController extends SiteBaseController {
    
    function showAction() {
        $this->setSkin('chapter');
        $this->params['id'] = (int)$this->params['id'];
        $m = new chaptersModel();
        
        $data = $m->getData($this->params['id']);
        
        if (!$data) {
            $this->_redirect('/404');
            return true;
        }
        
        $translit = tools_string::translit($data['name_ru']);
        
        if ($translit!==$this->params['name']) {
            header("Location: /chapter/{$translit}/{$data['id']}",true,301);
        }
        
        $this->view->meta_title = 'Ранобэ '.$data['novella_name'].' :: '.$data['name_ru'];
        $this->view->meta_description = 'Новэлла '.$data['novella_name'].' читать бесплатно онлайн';
        $this->view->meta_keywords = 'читать, ранобэ, '.$data['name_ru'].', '.$data['novella_name'].', бесплатно, скачать, новеллы';
        
    }
    
    
    function loadlistAction() {
        $this->ajax();
        $cm = new chaptersModel();
        $this->view->data = $cm->getChapters($this->params['v'], $this->params['page']);
        $this->view->volume_id = $this->params['v'];
        
        $this->renderTplToContent('chapters_table.tpl');
    }
    
    function lostAction() {
        $this->ajax();
        
        $m = ormModel::getInstance('public','volumes');
        $nm = new novellasModel();
        
        $err='';
        
        $d = $m->getRow('id='.(int)$this->params['v']);
        $nd = $nm->getRow('id='.$d['novella_id']);
        
        $message = nl2br("
            Пользователь сообщил о том что утеряна глава 
            в томе <a href='http://{$_SERVER['SERVER_NAME']}/novellas/".tools_string::translit($nd['name'])."/".$nd['id']."/v/".$d['id']."'>№{$d['number']} новеллы {$nd['name']}</a>.
                
            Сообщение: {$this->params['message']}
                
            --
            Система автоматических уведомлений
            {$_SERVER['SERVER_NAME']}
        ");
            
        try {
            tools_email::send(settings::getVal('manager_email'), 'Сообщение о потерянной главе', $message);
        }catch (Exception $e) {
            $err = $e->getMessage();
            $err = 'Ошибка отправки сообщения. Попробуйте повторить позже.';
        }
        
        
        $this->jsonAnswer([
            'error' => $err
        ]);
    }
    
    
    function commentsAction() {
        $this->ajax();
        $this->params['id'] = (int)$this->params['id'];
        
        $this->view->params = $this->params;
        $this->renderTplToContent("chapter_comments.tpl");
    }
}
