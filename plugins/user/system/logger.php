<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of logger
 *
 * @author chenz_000
 */
class logger {
    
    static function log($type, $message, $data, $user_id) {
        $m = new authLoggerModel();
        
        $m->newItem(array(
            'type'  => $type,
            'message'   => $message,
            'data'      => json_encode($data),
            'user_id'   => $user_id
        ));
    }
    
}
