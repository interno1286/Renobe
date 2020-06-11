<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of registerModel
 *
 * @author chenzya
 */
class registerModel extends ormModel {

    public $table = 'users';
    public $schema = 'public';

    function doRegister(&$params, &$user_id = false) {

        $db_data = array(
            'email' => mb_strtolower($params['email'])
        );
        
        if (isset($params['password1']))
            $db_data['password'] = sha1($params['password1']);
        
        if (isset($params['pass_hash']))
            $db_data['password'] = $params['pass_hash'];

        $this->fillCustomFields($params, $db_data);

        return $this->pq('insert', 'users', $db_data, false, $error, $user_id);
    }

    function checkLoginExists($params) {

        $params['email'] = mb_strtolower(preg_replace('#([^0-9a-zа-я.@_+-])#Usi', '', $params['email']));

        $sql = "
			select
				count(id)
			from
				users
			where
				email='{$params['email']}'
		";

        return (bool) $this->s_fetchOne($sql);
    }

    function fillCustomFields(&$params, &$db_data) {}

}
