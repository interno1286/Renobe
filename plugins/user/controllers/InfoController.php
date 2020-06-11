<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of InfoController
 *
 * @author chenz_000
 */
class user_InfoController extends SiteBaseController {
    
    function getEditFields() {
        return explode(',','email');
    }
    
    function saveallAction() {
        
        $this->useAjaxView();
        
        try {
            $error = '';
                    
            $fields = $this->getEditFields();

            $db_data = array();

            foreach ($fields as $f) {
                if ($this->params[$f]) {

                    if ($f === 'email' && $this->params[$f]!==$this->user_data->email)
                        if ($this->model->getUserIdByEmail($this->params[$f])) throw new Exception('Такой email уже принадлежит другому пользователю, укажите другой.');


                    $db_data[$f] = $this->params[$f];
                }
            }

            if (!$this->model->updateItem($db_data,'id='.$this->user_data->id))
                throw new Exception ('При сохранении произошла ошибка');
            
        }catch (Exception $e) {
            $error = $e->getMessage();
        }
        
        $this->view->content = Zend_Json::encode(array(
            'error' => $error
        ));
        
    }
    
    
}
