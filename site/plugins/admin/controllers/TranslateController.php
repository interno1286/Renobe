<?php

class admin_TranslateController extends adminController {
    
    function indexAction() {
    }
    
    function showAction() {
        $this->ajax();
        
        $this->renderTplToContent('translate.tpl');
    }
    
    function denyAction() {
        $this->ajax();
        
        $m = new userTranslateModel();
        
        $data = $m->getRow('id='.(int)$this->params['id']);
        
        $m->updateItem([
            'approved'  => new Zend_Db_Expr('false'),
            'user_rating'   => '-5'
        ],'id='.(int)$this->params['id']);
        
        $um = new userModel();
        
        $um->updateItem([
            'rating'    => new Zend_Db_Expr('rating-5')
        ],'id='.$data['user_id']);
        
        $this->jsonAnswer([
            'error' => ''
        ]);
    }
    
    
    function acceptAction() {
        $this->ajax();
        
        $m = new userTranslateModel();
        
        
        $data = $m->getRow('id='.(int)$this->params['id']);
        
        switch (true) {
            case ($data['paragraph_id']):
                $pm = ormModel::getInstance('public','paragraph');
                $pm->updateItem([
                    'text_ru'   => $data['translate'],
                    'translated'    => new Zend_Db_Expr('true')
                ],'id='.$data['paragraph_id']);
                
                break;
            
            case ($data['chapter_id']):
                $pm = ormModel::getInstance('public','chapters');
                $pm->updateItem([
                    'name_ru'   => $data['translate']
                ],'id='.$data['chapter_id']);
                
                break;
            
            case ($data['novella_id']):
                $pm = ormModel::getInstance('public','novella');
                $pm->updateItem([
                    'name'   => $data['translate']
                ],'id='.$data['novella_id']);
                
                break;
            
            case ($data['description_id']):
                $pm = ormModel::getInstance('public','novella');
                $pm->updateItem([
                    'description'   => $data['translate']
                ],'id='.$data['description_id']);
                break;
            
        }
        
        
        $m->updateItem([
            'approved'  => new Zend_Db_Expr('true'),
            'user_rating'   => '5'
        ],'id='.(int)$this->params['id']);
        
        $um = new userModel();
        
        $um->updateItem([
            'rating'    => new Zend_Db_Expr('rating+5')
        ],'id='.$data['user_id']);
        
        
        $this->jsonAnswer([
            'error' => ''
        ]);
    }
    
    
    function retranslateAction() {
        $this->ajax();
        
        if ($this->params['item']=='chapter') {
            $m = new chaptersModel();
            $pm = new paragraphModel();
            
            $chapter_data = $m->getData($this->params['id']);
            
            $nm = new novellasModel();
            
            $novella_data = $nm->getRow("id=".$chapter_data['novella_id']);
            
            $provider = $nm->getProvider($novella_data['url']);
            
            $rtm = ormModel::getInstance('public','retranslate');
            
            $pm->updateItem([
                'text_ru'   => new Zend_Db_Expr('null')
            ],'chapter_id='.$chapter_data['id']);
            
            $m->updateItem([
                'translate_finish'  => new Zend_Db_Expr('null'),
                'translated_percent'    => 0
            ],'id='.$chapter_data['id']);
            
        }
        
        $this->jsonAnswer([
            'error' => ''
        ]);
    }
    
    
    function viewlogAction() {
        $this->ajax();
        $this->renderTplToContent('view_log.tpl');
    }
 
    
    function errorsAction() {
        
    }
    
    function parafailedAction() {
        $this->ajax();
        $this->renderTplToContent('parafailed.tpl');
    }
    
    function parasetAction() {
        $this->ajax();
        
        $m = new paragraphModel();
        $id = (int)$this->params['id'];
        
        $m->updateItem([
            'text_ru'   => $this->params['translate'],
            'translate_failed'  => new Zend_Db_Expr('false')
        ],'id='.$id);
        
        $fm = ormModel::init('public','translate_failed');
        $fm->del("type='paragraph' and object_id=".$id);
        
        $m->checkChapterTranslateFinishedByPar($id);
        
        $this->jsonAnswer([
            'error' => $m->last_error
        ]);
    }
    
    function paradelAction() {
        $this->ajax();
        
        $id = (int)$this->params['id'];
        
        $fm = ormModel::init('public','translate_failed');
        
        $fm->del("type='paragraph' and object_id=".$id);
        
        $this->jsonAnswer([
            'error' => $fm->last_error
        ]);
        
    }
    
    
    function delallparaerrorsAction() {
        $this->ajax();
        
        $ids = preg_replace("#([^0-9,])#ui",'',$this->params['ids']);
        
        $fm = ormModel::init('public','translate_failed');
        
        $fm->del("type='paragraph' and object_id in ($ids)");
        
        $this->jsonAnswer([
            'error' => $fm->last_error
        ]);
        
    }
}
