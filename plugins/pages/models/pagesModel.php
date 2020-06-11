<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of pagesModel
 *
 * @author v0yager
 */
class pagesModel extends ormModel {
    
    public $table = 'pages';
    public $plugin = 'pages';
    public $schema = 'public';
    
    function getAllPages() {
        $sql = "
            select
                path, id
            from
                pages
            where
                path is not null
            group by 1,2
        ";

        return $this->s_fetchAll($sql);
    }

    function getPageById($id) {

        if (strval(intval($id)) != $id)
            $id = $this->getIdByPath($id);

        $sql = "
            select
                *
            from
                pages
            where
                id=" . (int) $id;

        return $this->s_fetchRow($sql);
    }
    
    function getPageByPathLng($path, $lng = 'ru') {
        $path = pg_escape_string($path);

        $sql = "
            select
                *
            from
                pages
            where
                path='$path'
            and
                language='$lng'
        ";

        return $this->s_fetchRow($sql);
        
    }

    function savePageData($params, &$error, &$inserted_id = false) {

        $inserted_id = false;

        $db_data = array();
        
        foreach (explode(',','content,name,description,keywords,content,path,skin') as $param) {
            if (isset($params[$param]) && $params[$param]) {
                $db_data[$param] = $params[$param];
            };
        }
        
        if ($params['content']) {
            if ($params['id'])
                $this->backupContent($params['id']);
            
            $db_data['md5_content'] = md5($params['content']);
        }
        
        $lng = translate::getLanguage();
        if (!$lng) $lng='ru';
        
        $id = (isset($params['id'])) ? intval($params['id']) : $this->getIdByPath($params['path']);

        if ($id) {
            $inserted_id = $id;
            return $this->pq('update', 'pages', $db_data, 'id=' . $id, $error);
        }


        return $this->pq('insert', 'pages', $db_data, false, $error, $inserted_id);
    }
    
    function backupContent($page_id) {
        $sql = "insert into public.pages_backup(page_id,content) values($page_id,(select content from pages where id=$page_id))";
        return $this->pq('query',$sql);
    }
    
    function restore($page_id) {
        $page_id = (int)$page_id;
        
        if (!(int)$this->s_fetchOne('select count(id) from public.pages_backup where id='.$page_id)) throw new Exception('не найдено ни одной резервной копии');
        
        $sql = "update public.pages set content=(select content from public.pages_backup where page_id=$page_id order by backup_date desc limit 1)";
        
        if (!$this->pq('query',$sql)) throw new Exception('Не смогли восстановить последнюю резервную копию');
        
        $this->pq('delete','public.pages_backup','id=(select id from public.pages_backup where page_id=$page_id order by backup_date desc limit 1)');
    }
    
    function getIdByPath($path) {
        return $this->s_fetchOne("select id from pages where path='$path'");
    }

    function getPageByName($name) {

        $id = $this->s_fetchOne("select id from pages where name='$name'");

        if (!$id)
            return false;

        return $this->getPageById($id);
    }
    
    function rollbackPageContent($id) {
        $id = (int) $id;
        
        $sql = "
            update pages set content=(select content from pages_backup where page_id=$id order by backup_date desc limit 1 offset 2) where id=$id
        ";
        
        $this->pq('query',$sql);
        
    }
    
    function install() {
        $sql_file = $this->config->path->root . "plugins/pages/contrib/script.sql";

        if (file_exists($sql_file)) {

            $expressions = explode(';', file_get_contents($sql_file));

            foreach ($expressions as $e)
                $this->pq('query', $e);
        }
    }
    
    
}
