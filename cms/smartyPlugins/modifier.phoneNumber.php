<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function smarty_modifier_phoneNumber($string) {
    
    if(  preg_match( '/(\d{1})(\d{3})(\d{3})(\d{4})$/ui', $string,  $matches ) ) {
        $result = '+'.$matches[1] . '(' .$matches[2] . ') ' . $matches[3].'-'.$matches[4];
        return $result;
    }
    
    return $string;
}