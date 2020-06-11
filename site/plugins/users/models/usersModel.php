<?php

class usersModel extends userModel {

    public $schema = 'public';
    public $table = 'users';

    function getTopUsers()
    {
        $sql = "SELECT * FROM users 
                WHERE rating > 0
                ORDER BY rating DESC 
                LIMIT 10";

        return $this->s_fetchAll($sql);
    }

}
