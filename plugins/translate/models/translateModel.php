<?php

/**
 * Created by JetBrains PhpStorm.
 * User: Евгений
 * Date: 24.04.14
 * Time: 19:05
 * To change this template use File | Settings | File Templates.
 */
class translateModel extends DbModel {

    function getItem($code, $lng = 'ru') {
        
        $lng = $this->filterString($lng);
        $code = $this->filterString($code);
        
        $sql = "
            SELECT 
                *
            FROM    
                translate.items
            WHERE 
                lng='$lng'
            AND 
                code='$code'";
        $data = $this->s_fetchRow($sql);
        return $data;
    }

    function getItems($lng = 'ru') {
        $sql = "
            select 
                q.code,
                coalesce(tr.txt,'') as txt,
                '$lng' as l_code
            from 	    
            (
            SELECT 
                DISTINCT c.code
            FROM 
                translate_items c
            ) q
            left join
            translate_items tr on tr.l_code='$lng' and q.code=tr.code

        ";
        $data = $this->s_fetchAll($sql);
        return $data;
    }

    function getLanguages() {
        $sql = "SELECT * FROM translate_languages";
        $data = $this->s_fetchAll($sql);
        return $data;
    }

    function addLanguage($code, $name) {
        $code = $this->filterString($code);

        $values = array(
            'code' => $code,
            'name' => $this->filterString($name)
        );

        $this->pq('insert', 'translate_languages', $values);
    }

    function removeLanguage($code) {
        $code = $this->filterString($code);

        $this->beginTransaction();
        try {
            $this->pq('delete', 'translate_languages', "code='{$code}'");
            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }
    }

    function addItem($lng, $code, $txt) {
        $values = array(
            'code' => $this->filterString($code),
            'txt' => $this->filterString($txt),
            'l_code' => $this->filterString($lng)
        );

        $this->pq('insert', 'translate_items', $values);
    }

    function editItem($lng, $code, $txt) {
        $values = array(
            'txt' => $this->filterString($txt)
        );

        if ($this->s_fetchOne("select count(txt) from translate_items where l_code='$lng' and code='$code'")) {

            $this->pq('update', 'translate_items', $values, "code='" . $this->filterString($code) . "' AND l_code='" . $this->filterString($lng) . "'");
        } else {
            $values['l_code'] = $lng;
            $values['code'] = $code;
            $this->pq('insert', 'translate_items', $values);
        }
    }

    function editItemCode($lng, $code, $new_code) {
        $values = array(
            'code' => $this->filterString($new_code)
        );

        $this->pq('update', 'translate_items', $values, "code='" . $this->filterString($code) . "' AND l_code='" . $this->filterString($lng) . "'");
    }
    
    function deleteItem($l_code , $code){
        $this->pq('delete', 'translate_items', "code='{$code}'");
    }
}
