<?php

class users_ProfileController extends insideController {
    
    function indexAction() {
        $this->setSkin('pages');
        
        if (isset($this->user_data->error) && $this->user_data->error) {
            $this->view->error = $this->user_data->error;
            $this->user_data->error = null;
        }
        
    }
    
    
    function editAction() {
        $this->ajax();
        
        $this->renderTplToContent('edit_profile.tpl');
    }
    
    function saveAction() {
        $this->ajax();
        
        try {
            $error = '';
            $m = new userModel();
            
            $email = preg_replace('#([^a-zа-я0-9.@_-])#', '', $this->params['email']);
            
            if ($email !== $this->user_data->email) {
                if ($m->get('id',"email='$email'")) throw new Exception('этот email принадлежит другому пользователю');
            }

            $update_array = [
                'email' => $email,
                'fio'   => $this->params['fio']
            ];

            $password = $this->params['password'];
            if ($password) {
                $update_array['password'] = sha1($password);
            }

            $m->updateItem($update_array, 'id='.$this->user_data->id);
            
            $this->user_data->email = $email;
            $this->user_data->fio = $this->params['fio'];
            $this->user_data->password = $password;

        } catch (Exception $e) {
            $error = $e->getMessage();
        }
        
        
        $this->jsonAnswer([
            'error' => $error
        ]);
        
        
    }
    
}
