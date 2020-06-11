<?php


class users_SettingsController extends SiteBaseController {
    
    function dialogAction() {
        $this->ajax();
        
        $this->renderTplToContent('settings.tpl');
    }
 
    function saveAction() {
        $this->ajax();
        
        try {
            $error = '';
            
            $available = ['font','colorSchema', 'fontSize', 'chapterFont'];

            if (!in_array($this->params['name'], $available)) throw new Exception('Некорректный параметр');
            
            $_SESSION[$this->params['name']] = $this->params['value'];

        }catch (Exception $e) {
            $error = $e->getMessage();
        }
        $this->jsonAnswer([
            'error' => $error
        ]);
    }
}
