<?php

class userTranslateModel extends ormModel{
    
    public $schema = 'public';
    public $table  = 'user_translate';
    
    function getTranslateForAdmin() {
        $sql = "
            select
                t.*,
                n.name as novella_name,
                n.id as novella_id,
                v.number as volume_number,
                c.number as chapter_number,
                c.id as chapter_id,
                u.email as user_name,
                'paragraph' as type
            from
                user_translate t
            left join
                paragraph p on p.id=t.paragraph_id
            left join
                chapters c on c.id=p.chapter_id
            left join
                volumes v on v.id=c.volume_id
            left join
                novella n on n.id=v.novella_id
            left join
                users u on u.id=t.user_id
            
            where
                t.approved is null
            and
                t.paragraph_id is not null
            order by t.created 
            
        ";
        
        $paragraph = $this->s_fetchAll($sql);
        if (!$paragraph) $paragraph=[];
        
        $sql = "
            select
                t.*,
                n.name as novella_name,
                n.id as novella_id,
                v.number as volume_number,
                c.number as chapter_number,
                c.id as chapter_id,
                u.email as user_name,
                'chapter' as type
            from
                user_translate t
            left join
                chapters c on c.id=t.chapter_id
            left join
                volumes v on v.id=c.volume_id
            left join
                novella n on n.id=v.novella_id
            left join
                users u on u.id=t.user_id
            
            where
                t.approved is null
            and
                t.chapter_id is not null
            order by t.created 
        ";
        
        $chapters = $this->s_fetchAll($sql);
        if (!$chapters) $chapters=[];
        
        $sql = "
            select
                t.*,
                n.name as novella_name,
                n.id as novella_id,
                u.email as user_name,
                'novella' as type
            from
                user_translate t
            left join
                novella n on n.id=t.novella_id
            left join
                users u on u.id=t.user_id
            
            where
                t.approved is null
            and
                t.novella_id is not null
                
            order by t.created 
        ";
        
        $novellas = $this->s_fetchAll($sql);
        if (!$novellas) $novellas=[];
        
        $sql = "
            select
                t.*,
                n.name as novella_name,
                n.id as novella_id,
                u.email as user_name,
                'description' as type
            from
                user_translate t
            left join
                novella n on n.id=t.description_id
            left join
                users u on u.id=t.user_id
            
            where
                t.approved is null
            and
                t.description_id is not null
                
            order by t.created 
        ";
        
        $descriptions = $this->s_fetchAll($sql);
        if (!$descriptions) $descriptions=[];
        
        return array_merge($paragraph, $chapters, $novellas, $descriptions);
        
    }
    
    function getUserTranslate($id) {
        $id = (int)$id;
        
        $sql = "
            select
                t.*,
                u.email as user_name
            from
                user_translate t
            left join
                users u on u.id=t.user_id
            
            where
                t.id=$id
        ";
        
        return $this->s_fetchRow($sql);
    }
    
    function getMyTranslates() {
        $sql = "
            select
                *
            from
                user_translate t
            left join
                paragraph p
        ";
    }
    
}
