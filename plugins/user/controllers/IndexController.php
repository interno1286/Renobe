<?php

class User_IndexController extends SiteBaseController {

    function initModel() {
        $this->model = new userModel();
    }
    
    function getWrongPassMessage() {
        return 'Неверный логин или пароль';
    }
    
    function getAccBlockedMessage() {
        return 'Ваш аккаунт заблокирован';
    }
    
    function getEmailMessage() {
        return 'Вы не указали email';
    }

    function wrongEmailMessage() {
        return 'Вы указали некорректный email!';
    }
    
    
    function getRestoreEmailSubj() {
        return 'Смена пароля на сайте ' . $_SERVER['SERVER_NAME'] . '!';
    }
    
    function getEmailErrorMessage() {
        return 'Не удалось отправить письмо. Попробуйте повторить попытку позже';
    }
    
    
    function getRestoreEmail($token) {
        return "
                Здравствуйте!

                Кто-то или вы сами запросили смену пароля на сайте {$_SERVER['SERVER_NAME']}

                Для смены пароля перейдите по следующей ссылке

                <a href='http://{$_SERVER['SERVER_NAME']}/{$this->module}/{$this->controller}/changepassbytoken/token/{$token}'>
                        http://{$_SERVER['SERVER_NAME']}/{$this->module}/{$this->controller}/changepassbytoken/token/{$token}
                </a>
        ";
        
    }
    
    function getNewPassMessage($email, $password) {
        return "
                Здравствуйте!

                Ваш новый пароль: $password
                В качестве логина используйте этот адрес электронной почты ($email)

                Вход на сайт <a href='http://{$_SERVER['SERVER_NAME']}/'>http://{$_SERVER['SERVER_NAME']}</a>
        ";        
    }
    
    function ajaxloginAction() {
        $this->useAjaxView();
        $this->grantAccess();

        $error = '';
        
        try {

            if (!$this->preCheck($error))
                throw new Exception($error);

            $user_data = $this->model->authUser($this->params);
            
            if (!$user_data)
                throw new Exception($this->getWrongPassMessage());

            if ($user_data['blocked'])
                throw new Exception($this->getAccBlockedMessage());
            
            $this->user_data->id        = $user_data['id'];
            $this->user_data->email     = $user_data['email'];
            $this->user_data->name      = $user_data['name'];
            $this->user_data->surname      = $user_data['surname'];
            $this->user_data->user_type = $user_data['user_type'];
            $this->user_data->role      = $user_data['user_type'];
            $this->user_data->logged    = true;
            $this->user_data->last_login_box_show = true;

            $this->getAdditionalUserInfo($user_data);
            $this->postAuthAction($user_data['id']);

            $_SESSION['logout_message'] = '';
            
        }catch (Exception $e) {
            $error = $e->getMessage();
            $this->faildAuthPostActions($error);
        }
        
        $this->view->content = Zend_Json::encode(array(
            'error' => $error
        ));
                
    }    
    
    function indexAction() {
        $this->grantAccess();

        if ($this->user_data->logged) {
            $this->_redirect($this->getUrlForRedirectAfterAuth());
            return true;
        }

        if ($this->isPost() || $this->_hasParam('auto')) {
            $this->doAuth();
        }
    }

    function cabinetAction() {
        
    }

    function doAuth() {

        try {
            if (!$this->preCheck($error))
                throw new Exception($error);

            $user_data = $this->model->authUser($this->params);
            
            if (!$user_data)
                throw new Exception($this->getWrongPassMessage());

            $this->user_data->id = $user_data['id'];
            $this->user_data->email = $user_data['email'];
            $this->user_data->name = $user_data['name'];
            $this->user_data->user_type = $user_data['user_type'];
            $this->user_data->role = $user_data['user_type'];
            $this->user_data->logged = true;
            $this->user_data->last_login_box_show = true;

            $this->getAdditionalUserInfo($user_data);
            $this->postAuthAction($user_data['id']);

            $_SESSION['logout_message'] = '';

            $this->afterAuthAction();
        } catch (Exception $e) {
            $error = $e->getMessage();
            $this->view->error = $error;
            $this->faildAuthPostActions($error);
        }
    }

    function afterAuthAction() {
        $this->_redirect($this->getUrlForRedirectAfterAuth());
    }

    function postAuthAction($user_id) {
        
    }

    function getUrlForRedirectAfterAuth() {
        return "/{$this->module}/{$this->controller}/cabinet";
    }

    function preCheck(&$error) {
        return true;
    }

    function faildAuthPostActions() {
        
    }

    function getAdditionalUserInfo($user_data) {
        
    }
    
    
    function lostpassAction() {
        $this->getAjaxView();

        if ($this->getRequest()->isPost()) {

            try {

                if (!$this->params['email'])
                    throw new Exception($this->getEmailMessage(), 300);

                if (!preg_match('/^[_a-zа-я0-9-]+(\.[_a-zа-я0-9-]+)*@[a-zа-я0-9-]+(\.[a-zа-я0-9-]+)*(\.[a-zа-я]{2,15})$/ui', $this->params['email']))
                    throw new Exception($this->wrongEmailMessage(), 300);

                $this->preLostPassTrigger();

                $error = '';
                $this->initChangePassProcedure($this->params['email']);
            } catch (Exception $e) {
                errorReport($e, get_defined_vars());
                $error = $e->getMessage();
            }

            $this->view->content = Zend_Json::encode(array('error' => $error));
        } else
            $this->renderTplToContent("lost_pass.tpl");
    }

    function preLostPassTrigger() {
        
    }

    function initChangePassProcedure($email) {
        $token = $this->model->genChangePassToken($email);

        $this->sendChangePassTokenMessage($email, $token);
    }
    
    
    function sendChangePassTokenMessage($email, $token) {
        $message = $this->getRestoreEmail($token);

        Zend_Mail::setDefaultTransport(new Zend_Mail_Transport_Smtp($this->config->smtp_ip));

        $mail = new Zend_Mail('UTF-8');
        $mail->setFrom('noreply@'.$_SERVER['SERVER_NAME']);

        $mail->setSubject($this->getRestoreEmailSubj());
        $mail->setBodyHtml(nl2br($message));

        $mail->addTo($email);

        try {
            $mail->send();
            return true;
        } catch (Exception $e) {
            errorReport($e, get_defined_vars());
            throw new Exception($this->getEmailErrorMessage(), 300);
        }
    }

    function changePassForUser() {
        $error = '';

        $new_pass = $this->model->getPass();

        $this->model->ChangePassForEmail($this->params['email'], $new_pass);

        $this->sendRememberPassMessage($this->params['email'], $new_pass);

        return $new_pass;
    }

    
    function sendRememberPassMessage($email, $password) {
        $message = $this->getNewPassMessage($email, $password);

        Zend_Mail::setDefaultTransport(new Zend_Mail_Transport_Smtp($this->config->smtp_ip));

        $mail = new Zend_Mail('UTF-8');
        $mail->setFrom('noreply@'.$_SERVER['SERVER_NAME']);

        $mail->setSubject($this->getRestoreEmailSubj());
        $mail->setBodyHtml(nl2br($message));

        $mail->addTo($email);

        try {
            $mail->send();
            return true;
        } catch (Exception $e) {
            errorReport($e, get_defined_vars());
            throw new Exception($this->getEmailErrorMessage(), 300);
        }
    }

    /**
     * @desc Validates captcha response
     * @param $captcha
     * @return unknown_type
     */
    function validateCaptcha($captcha) {
        $captchaId = $captcha['id'];
        $captchaInput = preg_replace('#([^a-z0-9])#uUsi', '', $captcha['input']);
        $captchaSession = new Zend_Session_Namespace('Zend_Form_Captcha_' . $captchaId);
        $captchaIterator = $captchaSession->getIterator();

        if (isset($captchaIterator['word']) && $captchaIterator['word']) {
            $captchaWord = $captchaIterator['word'];
            if ($captchaInput != $captchaWord) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * @desc Отображение каптчи
     */
    public function captchaAction() {
        $this->grantAccess();
        $this->getAjaxView();

        $dir = "{$this->config->path->root}/public/captcha/";

        // Проверка папки на запись
        if (is_dir($dir)) {
            if (!is_writable($dir))
                chmod($dir, 0777);
        } else
            mkdir($dir, 0777, true);

        $captcha = new Zend_Captcha_Image();
        
        $width = (isset($this->config->captcha->width)) ? $this->config->captcha->width : 120;
        $height = (isset($this->config->captcha->height)) ? $this->config->captcha->height : 50;
        
        
        $captcha->setTimeout('90')
                ->setWordLen(rand(4, 5))
                ->setHeight($height)
                ->setWidth($width)
                ->setFont("{$this->plugin_root_path}public/fonts/ariblk.ttf")
                ->setImgDir($dir)
                //->setStartImage("{$this->plugin_root_path}public/images/captcha_back.png")
                ->setDotNoiseLevel(rand(80, 100))
                ->setLineNoiseLevel(rand(4, 5))
        ;

        if ($this->config->use_only_numbers_in_captcha) {
            Zend_Captcha_Image::$V = array("1", "2", "3", "4", "5", "6");
            Zend_Captcha_Image::$VN = array("1", "2", "3", "4", "5", "6", "2", "3", "4", "5", "6", "7", "8", "9");
            Zend_Captcha_Image::$C = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "1", "2", "3", "4", "5", "6", "7", "8", "9", "1", "2");
            Zend_Captcha_Image::$CN = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "1", "2", "3", "4", "5", "6", "7", "8", "9", "1", "2", "2", "3", "4", "5", "6", "7", "8", "9");
        }

        $captcha->generate(); //command to generate session + create image

        $captchaId = $captcha->getId();

        $this->view->captcha = $captchaId;
        $this->view->content = $this->view->render($this->plugin_root_path . "views/captcha.tpl");
    }

    function changepassbytokenAction() {
        $token_data = $this->model->getChangePassTokenData($this->params['token']);

        if ($this->getRequest()->isPost()) {
            if ($this->validateCaptcha($this->params['captcha'])) {
                $this->params['email'] = $token_data['email'];

                $new_pass = $this->changePassForUser($token_data['user_id']);

                $this->view->new_pass = $new_pass;
            }
        }

        $this->view->token_data = $token_data;
    }
    
    
    function logoutAction() {
        
        if  ($this->user_data->prev_admin_user_id) {
            $um = new userModel();
            $admin_info = $um->getRow('id='.$this->user_data->prev_admin_user_id);
            
            $this->user_data->id = $admin_info['id'];
            $this->user_data->role = $admin_info['user_type'];
            $this->user_data->user_type = $admin_info['user_type'];
            $this->user_data->name = $admin_info['name'];
            $this->user_data->email = $admin_info['email'];
            $this->user_data->logged = true;
            
            $this->user_data->prev_admin_user_id = null;
            $this->user_data->prev_admin_user_role = null;
            
            $this->getAdditionalUserInfo($admin_info);
            
            $url = (isset($this->params['url'])) ? $this->params['url'] : '/';
            
            $this->_redirect($url);
            
        }else {
            Zend_Session::namespaceUnset('user_data');
            unset($_SESSION["user_data"]);
            unset($this->user_data);
            $this->_redirect('/');
        }
    }


    function changepassAction() {
        
        $this->useAjaxView();
        
        $result = $this->model->changePass($this->params, $error);
        
        $this->view->content = Zend_Json::encode(array(
            'error' => $error
        ));
        
    }
    
    function editAction() {
        $this->useAjaxView();
        if ($this->user_data->user_type!=='admin') {
            $this->params['id'] = $this->user_data->id;
        }
        
        if ($this->isPost()) {
            
            if (isset($this->params['db']['password']) && $this->params['db']['password']!='') {
                $this->params['db']['password'] = sha1($this->params['db']['password']);
            }else unset($this->params['db']['password']);
            
            if (isset($this->params['id'])) {
                $this->model->updateItem($this->params['db'],'id='.(int)$this->params['id']);
            }else $this->model->newItem($this->params['db']);
            
            $this->jsonAnswer(array(
                'error' => $this->model->last_error
            ));

            
        }else {
            $this->view->data = $this->model->getRow('id='.(int)$this->params['id']);
            $this->renderTplToContent ('edit_user.tpl');
        }
    }
}
