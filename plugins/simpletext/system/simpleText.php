<?php
/**
 * Description of simpleText
 *
 * @author glenn.ru
 */
class simpleText {
    
    
    function getVal($name) {
        
        $sm = new simpleTextModel();
        
        return $sm->getSimpleTextContentByName($name);
        
    }
    
}
