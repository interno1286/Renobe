<?php

class admin_IndexController extends adminController {
    
    function indexAction() {
        
    }
    
    function logAction() {
        $this->view->data = $this->getLog();
    }
    
    function getLog() {
        $file = new SplFileObject("site/system/sync_log.html");
        $file->seek(PHP_INT_MAX); // cheap trick to seek to EoF
        $total_lines = $file->key(); // last line number
        
        if ($total_lines>27) {
                $out = '';
            // output the last twenty lines
            $reader = new LimitIterator($file, $total_lines - 27);
            foreach ($reader as $line) {
                $out .= $line; // includes newlines
            }        
            return $out;
        }else {
            return file_get_contents("site/system/sync_log.txt");
        }
    }
    
    
}
