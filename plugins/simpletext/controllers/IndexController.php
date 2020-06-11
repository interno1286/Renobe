<?php

/**
 * Description of IndexController
 *
 * @author chenzya
 */
class Simpletext_IndexController extends SiteBaseController {

    
    function checkRights() {
        parent::checkRights();
        $this->needAdminRights();
    }
    
    function initModel() {
        $this->model = new simpleTextModel();
    }
    
    static function addScripts(&$content) {
        
        $config = Zend_Registry::get('cnf');
        @list($before, $after) = @explode('</body>', $content);
        
        $ewf = '<link rel="stylesheet" href="/plugins/simpletext/public/css/style.css" />
        <script src="/plugins/simpletext/public/js/script.js"></script>';
        
        $content = $before . $ewf . "</body>" . $after; 
    }

    function savedataAction() {
        $this->useAjaxView();
        $this->needAdminRights();

        $error = '';

        if ($this->getRequest()->isPost()) {
            $this->model->saveDataByName(
                    $this->params['name'], $this->params['data'], $error, $this->params['editor'], (isset($this->params['draft']) && $this->params['draft'] == '1')
            );
        }

        $this->view->content = Zend_Json::encode(array('error' => $error));
    }
    
    function editAction() {
        $this->ajax();
        
        $p = json_decode(base64_decode($this->params['config']),1);
        
        $this->view->data = $this->model->getSimpleTextContentByName($this->params['name']);
        
        if ($p['editor']){
            $this->renderTplToContent('dialog_editor.tpl');
        }else $this->renderTplToContent('input_editor.tpl');
    }
    

    function editdataAction() {
        $this->useAjaxView();
        

        $this->view->data = $this->model->getSimpleTextContentByName($this->params['name']);

        $this->renderTplToContent('dialog_editor.tpl');
    }
    
    
    function editelementAction() {
        $this->ajax();
        
        $this->renderTplToContent('edit_element.tpl');
    }

    
    
}
