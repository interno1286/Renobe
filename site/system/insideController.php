<?php

class insideController extends SiteBaseController {
    
    function checkRights() {
        parent::checkRights();
        
        if (!$this->user_data->id)
            $this->_forward ('accessdenied');
    }
    
}
