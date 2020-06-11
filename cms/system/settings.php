<?php

/*
 * @desc настройки хранящиеся в таблице settings
 * 
 * @desc получить значение settings::getVal('myparam')
 * @desc установить значение settings::setVal('myparam')
 * 
 */
class settings extends ormModel {

    public $table='settings';          ///// Основная таблица модели указывается для проверки и автоматическорй установки в случае отсутствия
    public $schema='public';           ///// Рабочая схема модели - указывается для проверки и автоматическорй установки в случае отсутствия

    static function getVal($name, $nocache=false) {
        $key = 'settings_'.$name;
        
        $cached = tools_cache::get($key);
        
        if ($cached && $cached!=='!!nocache!!' && $nocache===false) return $cached;
        
        $s = self::getInstance('public','settings');
        $val = $s->get('value',"name='$name'");
        
        tools_cache::save($key, $val);
        
        return $val;
    }
    
    static function setVal($name, $value) {
        $s = self::getInstance('public','settings');
        
        $exists = $s->get('id',"name='$name'");
        
        if ($exists) {
            $retval = $s->updateItem(array(
                'value' => $value
            ),'id='.$exists);
        }else {
            $retval = $s->newItem(array(
                'name'  => $name,
                'value' => $value
            ));
        }
        
        $key = 'settings_'.$name;
        tools_cache::save($key, $value);        
        
        return $retval;
    }
}
