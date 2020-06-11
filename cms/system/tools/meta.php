<?php

class tools_meta {
    
    static $page_titles = array();
    static $current_title = '';
    
    static function getTitle($url=false) {
        $title = ($url && isset(self::$page_titles[$url])) ? self::$page_titles[$url] : self::$current_title;
        return ($title) ? ' :: '.$title : '';
    }
    
    static function setTitle($title) {
        self::$current_title = $title;
    }
    
}
