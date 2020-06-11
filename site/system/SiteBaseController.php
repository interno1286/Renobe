<?php

/**
 *
 * @author glenn.ru
 */
class SiteBaseController extends CmsBaseController {
    
    function initController() {
        parent::initController();
        
        if ($this->user_data->id) {
            $blocked = ormModel::getInstance('userModel')->get('blocked','id='.$this->user_data->id);
            
            if ($blocked) {
                $this->user_data->id = null;
                $this->user_data->role = null;
                $this->user_data->user_type = null;
                
                header('Location: /');
            }
        }
    }
    
    function authAction() {
        $this->_redirect('/');
    }
    
}
