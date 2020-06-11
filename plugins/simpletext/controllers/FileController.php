<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FileController
 *
 * @author chenz_000
 */
class simpletext_FileController extends SiteBaseController {
    
    
    function editAction() {
        $this->needAdminRights();
        $this->useAjaxView();
        
        if (is_uploaded_file($_FILES['file']['tmp_name'])) {
            
            $dir = $this->config->path->root.'public/simpleFiles';
        
            if (!is_dir($dir))
                mkdir($dir,0777,true);
            
            $name = $this->params['name'];
            
            $up_fp = pathinfo($_FILES['file']['name']);
            
            $this->cleanOldFiles();
            
            if (!move_uploaded_file($_FILES['file']['tmp_name'], $dir.'/'.$name.'.'.$up_fp['extension'])) throw new Exception('Не могу загрузить переместить загруженный файл');
            
            $this->_redirect($_SERVER['HTTP_REFERER']);
        }
        
        $this->renderTplToContent('file_edit_form.tpl');
    }
    
    
    function cleanOldFiles() {
        
        $dir = $this->config->path->root.'public/simpleFiles';
        
        $files = glob($dir.'/'.$this->params['name'].'.*');
        
        foreach ($files as $f)
            unlink ($f);
    }
    
}
