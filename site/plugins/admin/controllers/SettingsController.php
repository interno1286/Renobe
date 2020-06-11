<?php

class admin_SettingsController extends adminController {
    
    function indexAction() {
        
    }
    
    function saveAction() {
        $this->ajax();
        
        foreach ($this->params['settings'] as $key=>$value)
            settings::setVal ($key, $value);
        
        $this->jsonAnswer([
            'error' => ''
        ]);
    }
}
