<?php

class volumesModel extends ormModel {
    
    public $schema = 'public';
    public $table  = 'volumes';
    
    function getVolumesForSync($novella_id) {
        
        $sql = "
            select 
               distinct v.*
            from
                volumes v
            inner join
                chapters c on c.volume_id=v.id
            where
                v.novella_id=$novella_id
            and
                c.translate_finish is null
            order by v.number, v.created            
        ";
        
        return $this->s_fetchAll($sql);
    }
    
    
    
    function getVolumesForTranslate($novella_id) {
        
        $sql = "
            select 
               distinct v.*
            from
                volumes v
            inner join
                chapters c on c.volume_id=v.id
            where
                v.novella_id=$novella_id
            and
                c.translate_finish is null
            order by v.number, v.created            
        ";
        
        return $this->s_fetchAll($sql);
    }
    
    function getVolumesToTranslate($volumes=false) {
        
        $data  = false;
        
        if ($volumes) {

            $sql = "
                select 
                   v.*
                from
                    volumes v
                where
                    coalesce(v.translate_now,false)=false
                and
                    (title_ru is null or title_ru='')
                and
                    id in (".implode(',', $volumes).")
                
            ";

            $data = $this->s_fetchAll($sql);
        }
        
        if (!$data) {
            $sql = "
                select 
                   v.*
                from
                    volumes v
                inner join
                    novella n on n.id=v.novella_id
                where
                    coalesce(v.translate_now,false)=false
                and
                    (v.title_ru is null or v.title_ru='')
                and
                    n.source='wuxia'
                order by number, id
                limit 10
            ";

            $data = $this->s_fetchAll($sql);
            
        }
        
        return $data;
    }


    
    
}
