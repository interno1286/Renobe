<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * Description of function
 *
 * @author chenz_000
 */
function smarty_function_lastResultReport($params, $template) {
    
    $user_data = Zend_Registry::get('user_data');
    
    $ret = "";
    
    if (@$user_data->result===true || $user_data->result===false) {
        
        $ret = "<div class='alert";
        
        if (!$user_data->result) 
            $ret .= " alert-danger'>
                    Ошибка {$user_data->last_error}
            ";
        else 
            $ret .= " alert-success'>
                Успешно!
            ";
                    
        $ret .= "
                <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                  <span aria-hidden='true'>&times;</span>
                </button>            
            </div>";
        
        $user_data->result = null;
        $user_data->last_error = null;
    }
    
    return $ret;
    
}
