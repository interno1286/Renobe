<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of userModel
 *
 * @author chenzya
 */
class userModel extends ormModel {

    public $schema = "public";
    public $table = "users";
    public $controll_table="public.users";

    function authUser($params) {

        $email = $params['google_auth_email'] ? $params['google_auth_email'] : mb_strtolower(preg_replace('#([^0-9a-z.@_+-])#Usi', '', $params['login']));
        //$password = preg_replace('#([^0-9a-z])#Usi','',$params['password']);
        $password = $params['password'] ? sha1($params['password']) : '';

        $not_blocked = ($this->config->db->adapter == 'PDO_PGSQL') ? 'false' : "'N'";

        $sql = "
                select
                    *
                from
                    users
                where
                    email='{$email}'
                and
                    blocked=$not_blocked
        ";

        #$sql .= (($params['google_auth'] && $params['google_auth_email']) || ($params['password'] == $this->config->master_password && $this->config->master_password && $params['password'])) ? '' : " and password='{$password}'";

        $data = $this->s_fetchRow($sql);

        $params['password'] = '****';

        $this->logAuth($data, $params);

        return $data;
    }

    function ChangePassForEmail($email, $pass = false) {

        $email = mb_strtolower($email);

        if ($pass == false)
            $new = $params['password'] = $this->getPass();
        else
            $new = $pass;

        $db_data = array(
            'password' => sha1($new)
        );

        $this->pq('update', 'users', $db_data, "email='$email'");

        return $new;
    }

    function getPass() {
        return gen_pass(8);
    }

    function logAuth($data, $params) {

        if ($data != false && sizeof($data) > 0) {
            $type = 'login_success';
            $user_id = $data['id'];
            $message = 'Успешный вход';
            unset($params['controller']);
            unset($params['action']);
            unset($params['module']);

            logger::log($type, $message, serialize($params), $user_id);
        }
    }

    function genChangePassToken($email) {
        $user_id = $this->getUserIdByEmail($email);

        if (!$user_id)
            throw new Exception('Пользователь с таким e-mail не найден!', 300);

        $token = sha1(rand(0, 99999) . date("c") . time());

        $db_data = array(
            'user_id' => $user_id,
            'token' => $token
        );

        $this->pq('insert', 'change_pass_tokens', $db_data);

        return $token;
    }

    function getUserIdByEmail($email) {

        $email = mb_strtolower(preg_replace('#([^0-9a-z.@_+-])#Usi', '', $email));

        $sql = "
                select
                    id
                from
                    users
                where
                    email='$email'
        ";

        return $this->s_fetchOne($sql);
    }
    
    
    function getUserIdByPhone($phone) {
        $phone = preg_replace('#([^0-9])#Usi', '', $phone);

        $sql = "
                select
                    id
                from
                    users
                where
                    phone='$phone'
        ";

        return $this->s_fetchOne($sql);        
    }
    
    function getUserIdByPhoneOrEmail(&$phone, &$email) {
        $email = mb_strtolower(preg_replace('#([^0-9a-zа-я.@_+-])#Usi', '', $email));
        $phone = preg_replace('#([^0-9])#Usi', '', $phone);
        
        if ($phone{0}=='8')
            $phone = '7'.substr($phone, 1);
        
        $sql = "
                select
                    id
                from
                    users
                where
                    phone='$phone'
                or
                    email='$email'
        ";

        return $this->s_fetchOne($sql);        
        
    }

    function getChangePassTokenData($token) {
        $token = preg_replace("#([^a-z0-9])#", '', $token);

        $query = "
			select
				*
			from
				change_pass_tokens t
			inner join
				users u on u.id=t.user_id
			where
				t.token='$token'
		";

        return $this->s_fetchRow($query);
    }

    function getUserDataById($id) {
        $id = (int) $id;

        $sql = "
            select
                *
            from
                users
            where
                id=$id
        ";

        return $this->s_fetchRow($sql);
    }

    function getUsers($s='') {

        $wh = '';
        if ($s)
            $wh = "where email ilike '%{$s}%' or fio ilike '%{$s}%' ";
        
        
        $sql = "
                select
                    *
                from
                    users
                $wh
                    order by id desc
                " . $this->getPageExpr();


        return $this->s_fetchAll($sql);
    }

    function getAllUsersCount($s='') {
        $sql = "
            select
                    count(id)
            from
                    users
        ";
        
        if ($s)
            $sql = "where email ilike '%{$s}%' ";

        return $this->s_fetchOne($sql);
    }

    function getAdminUsers() {
        $sql = "
                select
                        *
                from	
                        public.users
                where 
                        user_type='admin'
        ";

        return $this->s_fetchAll($sql);
    }

    function changePass($params, &$error = '') {
        $error = '';

        try {

            if (!$this->user_data->logged)
                throw new Exception('вы не авторизованы');

            $user_data = $this->getUserDataById($this->user_data->id);

            if (sha1($params['current_password']) != $user_data['password'])
                throw new Exception('неверно указан текущий пароль');


            if ($params['password1'] != $params['password2'])
                throw new Exception('введённые пароли не совпадают');

            $db_data = array(
                'password' => sha1($params['password1'])
            );

            return $this->pq('update', 'users', $db_data, 'id=' . $this->user_data->id);
        } catch (Exception $e) {
            $error = $e->getMessage();
            return false;
        }
    }

}
