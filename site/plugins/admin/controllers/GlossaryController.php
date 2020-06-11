<?php

class admin_GlossaryController extends adminController {
    
    
    function editAction() {
        $this->ajax();
        $this->params['id'] = (int)$this->params['id'];
        $this->view->params = $this->params;
        
        $this->renderTplToContent('glossary_edit.tpl');
    }
    
    
    function saveAction() {
        $this->ajax();
        
        $m = ormModel::getInstance('public','glossary');
        $id = (int)$this->params['id'];
        
        if ($id) {
            $m->updateItem([
                'original'  => $this->params['original'],
                'translate' => $this->params['translate']
            ], 'id='.$id);
        }else {
            $m->newItem([
                'original'  => $this->params['original'],
                'translate' => $this->params['translate'],
                'novella_id'    => $this->params['novella']
            ]);
            $id = $m->last_id;
        }
        
        $this->jsonAnswer([
            'error' => $m->last_error,
            'id'    => $id
        ]);
    }
    
    function delAction() {
        $this->ajax();
        
        $m = ormModel::getInstance('public','glossary');
        
        $m->del('id='.(int)$this->params['id']);
        
        $this->jsonAnswer([
            'error' => $m->last_error
        ]);
    }
    
}
