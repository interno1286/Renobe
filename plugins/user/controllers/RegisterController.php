<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IndexController
 *
 * @author chenzya
 */
class user_RegisterController extends SiteBaseController {

    function initController() {
        $this->view->addScript('/plugins/user/public/js/script.js');

        if ($this->user_data->id && $this->action != 'ok') {
            $this->_redirect('/');
        }
    }
    
    
    function getRegisterErrorMessage() {
        return 'При регистрации произошла ошибка';
    }
    
    function getEmailNotSetError() {
        return 'Вы не указали адрес электронной почты';
    }
    
    function getPassMmMessage() {
        return 'Пароли не совпадают';
    }
    
    
    function getEmailBusyMessage() {
        return 'Этот email уже принадлежит другому пользователю';
    }
    
    function printRegisterFinishText() {
        $this->view->register_content = '<h2>Регистрация успешно завершена!</h2><br /><a href="/">Перейти в личный кабинет</a>';
    }
    
    function getRegisterSubj() {
        return 'Регистрация на сайте ' . $_SERVER['SERVER_NAME'] . ' успешно завершена!';
    }
    
    
    function getEmailError() {
        return 'Не удалось отправить письмо. Попробуйте повторить попытку регистрации позже';
    }
    

    function getRegisterMessage($params) {
        return "
                Здравствуйте!

                Вы успешно зарегистрированы на сайте {$_SERVER['SERVER_NAME']}

                Ваш логин: {$params['email']}
                Пароль: {$params['password1']}

                Вход на сайт <a href='http://{$_SERVER['SERVER_NAME']}/'>http://{$_SERVER['SERVER_NAME']}</a>
        ";        
    }
    

    function initModel() {
        $this->model = new registerModel();
    }

    function indexAction() {
        
        if ($this->getRequest()->isPost()) {
            $this->doRegister();
        } else $this->renderRegisterForm();
    }

    function ajaxformAction() {
        $this->useAjaxView();
        $this->renderRegisterForm();
    }
    
    function ajaxregisterAction() {
        $this->useAjaxView();
        $error = '';
        
        try {
            //$this->model->beginTransaction();
            $this->preCheck();
            
            $result = $this->model->doRegister($this->params, $user_id);

            if (!$result)
                throw new Exception($this->getRegisterErrorMessage());

            $this->sendFinishRegisterMessage($this->params);
            //$this->model->commitTransaction();

            $this->initUserData($user_id);

            $this->finishRegister($user_id);
            
        }catch (Exception $e) {
            $this->model->rollbackTransaction();
            $this->registerFail($e->getMessage());
            $error = $e->getMessage();
        }
        
        $this->view->content = Zend_Json::encode(array(
            'error' => $error
        ));
    }
    
    
    function renderRegisterForm() {
        $this->renderTplToContent('register_form.tpl');
    }

    function doRegister() {
        try {
            $this->model->beginTransaction();

            $this->preCheck();

            $result = $this->model->doRegister($this->params, $user_id);

            if (!$result)
                throw new Exception($this->getRegisterErrorMessage());

            //$this->sendFinishRegisterMessage($this->params);
            $this->model->commitTransaction();

            $this->initUserData($user_id);

            $this->finishRegister($user_id);
        } catch (Exception $e) {
            $this->model->rollbackTransaction();
            $this->view->error = $e->getMessage();
            $this->registerFail($e->getMessage());
            errorReport($e, get_defined_vars());
        }
    }
    
    
    function preCheck() {

        if (!$this->params['email'])
            throw new Exception($this->getEmailNotSetError());

        if ($this->params['password1'] != $this->params['password2'])
            throw new Exception($this->getPassMmMessage());

        if ($this->model->checkLoginExists($this->params))
            throw new Exception($this->getEmailBusyMessage());
    }

    function finishRegister($user_id) {
        $this->printRegisterFinishText();
        $this->_redirect('/registerok');
    }

    function registerFail($error) {
        $this->view->error = $error;
        $this->renderRegisterForm();
    }
    
    function sendFinishRegisterMessage($params) {
        $message = $this->getRegisterMessage($params);

        Zend_Mail::setDefaultTransport(new Zend_Mail_Transport_Smtp($this->config->smtp_ip));

        $mail = new Zend_Mail('UTF-8');
        $mail->setFrom($this->config->user->email_from);

        $mail->setSubject($this->getRegisterSubj());
        $mail->setBodyHtml(nl2br($message));

        $mail->addTo($params['email']);

        try {
            $mail->send();
            return true;
        } catch (Exception $e) {
            errorReport($e, get_defined_vars());
            throw new Exception($this->getEmailError());
        }
    }

    function checkloginexistsAction() {
        $this->getAjaxView();

        $exist = $this->model->checkLoginExists($this->params);

        $out = array(
            'exist' => $exist
        );

        $this->view->content = Zend_Json::encode($out);
    }

    /**
     * @desc Validates captcha response
     * @param $captcha
     * @return unknown_type
     */
    function validateCaptcha($captcha) {
        $captchaId = $captcha['id'];
        $captchaInput = $captcha['input'];
        $captchaSession = new Zend_Session_Namespace('Zend_Form_Captcha_' . $captchaId);
        $captchaIterator = $captchaSession->getIterator();

        if (isset($captchaIterator['word']) && $captchaIterator['word']) {
            $captchaWord = $captchaIterator['word'];

            Zend_Session::namespaceUnset('Zend_Form_Captcha_' . $captchaId);

            if ($captchaInput != $captchaWord) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    function okAction() {}

    function initUserData($user_id) {
        $um = new userModel();

        $user_data = $um->getUserDataById($user_id);

        $this->user_data->id = $user_data['id'];
        $this->user_data->role = $user_data['user_type'];
        $this->user_data->user_type = $user_data['user_type'];
        $this->user_data->fio = $user_data['fio'];
        $this->user_data->token = $user_data['token'];
        $this->user_data->balance = $user_data['balance'];
        $this->user_data->email = $user_data['email'];
        $this->user_data->logged = true;
    }

}
