<?php


class users_RegisterController extends user_RegisterController {
    
    function sendFinishRegisterMessage($params) {
        return true;
    }
    
    function getEmailBusyMessage() {
        return 'Этот аккаунт уже принадлежит другому пользователю';
    }
    
    function finishRegister($user_id) {
        return true;
    }
    
}
