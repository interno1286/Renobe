<?php

/**
 * Description of IndexController
 *
 * @author v0yager
 */
class meta_IndexController extends SiteBaseController {
    
    function initModel() {
        $this->model = new metaModel();
    }
    
    function checkRights() {
        $this->needAdminRights();
    }
    
    static function addNotch(&$content) {
        $config = Zend_Registry::get('cnf');
        @list($before, $after) = @explode('</body>', $content);
        
        $ewf = Zend_Registry::get('view')->_fetch($config->path->root . 'plugins/meta/views/editMetaTags.tpl');
        
        $content = $before . $ewf . "\r\n</body>" . $after; 
        
    }
    
    function getUrl() {
        preg_match('#.*//.*/(.*)$#uUi', $this->params['url'], $m);
        
        return md5('/'.$m[1]);
    }
    
    
    function tagsAction() {
        $this->ajax();

        $data = $this->model->getRow("url='{$this->getUrl()}'");
        
        $data['error'] = '';
        
        $this->jsonAnswer($data);
    }
    
    function saveAction() {
        $this->ajax();
        
        try {
            $error = '';
            
            $db = [
                'title'         => $this->params['title'],
                'description'   => $this->params['description'],
                'keywords'      => $this->params['keywords']
            ];
            
            if ($this->model->get('id',"url='{$this->getUrl()}'")) {
                $this->model->updateItem($db, "url='".$this->getUrl()."'");
            }else  {
                $db['url'] = $this->getUrl();
                $this->model->newItem($db);
            }
            
            tools_cache::delete('meta_'.md5($this->getUrl()));
            
        }catch (Exception $e) {
            $error = $e->getMessage();
        }
        
        $this->jsonAnswer([
            'error' => $error 
        ]);
        
    }

}
