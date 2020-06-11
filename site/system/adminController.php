<?php

class adminController extends SiteBaseController {
    
    function initController() {
        parent::initController();
        if (isset($this->params['id'])) {
            $this->params['id'] = (int)$this->params['id'];
            $this->view->params = $this->params;
        }
            
        $this->setSkin('admin');
        
        if ($this->user_data->role!=='admin' && $this->action!=='accessdenied')
            $this->_forward ('accessdenied');
    }
    
}
