<?php

class users_IndexController extends User_IndexController{
    
    function loginAction() {
        $this->ajax();
        $this->renderTplToContent('login.tpl');
    }
    
    function changepassbytokenAction() {
        $this->setSkin('pages');
        parent::changepassbytokenAction();
        
    }
    
    function registerAction() {
        $this->ajax();
        $this->renderTplToContent('register.tpl');
    }
    
    function getAdditionalUserInfo($user_data) {
        $this->user_data->created = $user_data['created'];
        $this->user_data->avatar  = $user_data['avatar'];
    }
    
    
    function avatarAction() {
        $this->ajax();
        
        try {
            if (!is_uploaded_file($_FILES['image']['tmp_name']))
                throw new Exception('нет загруженного файла');

            $img_ext = ['jpg','jpeg','gif','png'];

            if (!is_dir('public/avatar'))
                mkdir('public/avatar',0777,true);
            
            $ext = mb_strtolower(pathinfo($_FILES['image']['name'],PATHINFO_EXTENSION));
            
            if (!in_array($ext, $img_ext))
                throw new Exception('загруженный файл не является изображением');    
            
            $new_name = $this->user_data->id.'_'.tools_string::randString(4).'.'.$ext;
            
            
            if (!tools_images::thumb([
                'src'   => $_FILES['image']['tmp_name'],
                'dst'   => 'public/avatar/'.$new_name,
                'width' => 200,
                'height' => 200
            ])) throw new Exception('Ошибка создания аватара');
            
            $this->user_data->avatar = $new_name;
            
            ormModel::getInstance('userModel')->updateItem([
                'avatar'    => $new_name
            ],'id='.$this->user_data->id);
            
        }catch (Exception $e) {
            $this->user_data->error = $e->getMessage();
        }
        
        $this->_redirect('/users/profile');
    }
}
