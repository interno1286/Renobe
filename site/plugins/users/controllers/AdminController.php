<?php

class users_AdminController extends user_AdminController {
    
    function listAction() {
        $this->setSkin('admin');
        
        parent::listAction();
    }
    
}
