<?php

class tools_arrTools {
    static function cube($data, $dims) {
        $result=array();
        foreach($data as $value) {
            $res=&$result;
            foreach($dims as $dim) {
                if (!isset($res[$value[$dim]]))
                    $res[$value[$dim]]=array();
                $res=&$res[$value[$dim]];
            }
            $res=$value;
            foreach($dims as $dim)
                unset($res[$dim]);
        }
        return $result;
    }


    static function group($data, $dims) {
        $result=array();
        foreach($data as $value) {
            $res=&$result;
            foreach($dims as $dim) {

                if (!isset($res[$value[$dim]]))
                    $res[$value[$dim]]=array();

                $res=&$res[$value[$dim]];
            }

            $res[]=$value;
        }
        return $result;
    }
    
    
    /**
     * @desc Перевод объекта в массив
     **/
    static function getAssocArrayFromObj($obj){
       if (!is_object($obj)) throw new IllegalArgumentException(gettype($obj));
       $ret = array();
       $objReflect = new ReflectionObject($obj);
       $class_properties = $objReflect->getProperties();
       foreach ($class_properties as $property) {
          $propName = $property->getName();
          $ret[$propName] = (is_object($obj->$propName)) ? getAssocArrayFromObj($obj->$propName) : $obj->$propName;
       }
       return $ret;
    }
    
    
    function groupDataBy($data, $field) {
        $out = array();
        
        foreach ($data as $d) {
            if (!isset($out[$d[$field]]))
                $out[$d[$field]] = array();
            
            $out[$d[$field]][] = $d;
        }
        
        return $out;
    }
    
    
}
