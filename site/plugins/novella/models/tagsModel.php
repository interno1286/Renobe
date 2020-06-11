<?php

class tagsModel extends ormModel {
    public $schema = 'public';
    public $table = 'tags';
    
    function getTagsForCloud() {
        
        $key = 'tagsCloud';
        
        $cached = tools_cache::get($key);
        
        if ($cached && $cached!=='!!nocache!!') return $cached;
        
        $sql = "
            select 
                (select count(id) from novella_tags where tag_id=t.id) as cnt,
                t.*, t.name AS name_en,
                CASE WHEN t.name_ru!='' THEN t.name_ru
                ELSE t.name
                END AS name 
            from 
                tags t
            where
                t.name!=''            
            and
                (select count(id) from novella_tags where tag_id=t.id)>0
        ";        
        
        $data = $this->s_fetchAll($sql);
        
        if ($data) tools_cache::save($key, $data, 86400);
        
        return $data;
    }

    function getTagsList() {
        $sql = "SELECT id, CASE 
                    WHEN name_ru<>'' THEN name_ru
                    ELSE name
                    END AS name
                    FROM tags 
                    ORDER BY id";

        return $this->s_fetchAll($sql);
    }
}
