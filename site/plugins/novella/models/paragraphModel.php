<?php

class paragraphModel extends ormModel{
    public $schema = 'public';
    public $table = 'paragraph';
    function search($t) {
        $sql = "
            select 
                p.*,
                n.name as novella_name,
                n.id as novella_id,
                n.description as novella_description,
                n.image as novella_image,
                'par' as type,
                c.name_ru as chapter_name,
                c.id as chapter_id,
                c.number as chapter_number,
                v.number as volume_number,
                v.id as volume_id
            from
                paragraph p
            left join
                chapters c on c.id=p.chapter_id
            left join
                volumes v on v.id=c.volume_id
            left join
                novella n on n.id=v.novella_id
            where
                p.text_ru ilike '%$t%'
            or
                ru_search_index @@ to_tsquery('". str_replace(' ', ' & ', $t)."')
        ";
        
        return $this->s_fetchAll($sql);
    }
    
    function getUntranslatedInNovella($n_id) {
        
        $sql = "            
            select 
                c.id,
                c.number,
                v.number
            from 
                chapters c 
            inner join
                volumes v on v.id=c.volume_id
            where
                v.novella_id=$n_id
            and
            	c.translate_finish is null
            and
                (
                    select 
                        count(id) 
                    from 
                        paragraph 
                    where 
                        chapter_id=c.id 
                    and 
                        coalesce(translated,false)=false 
                    and 
                        coalesce(translate_now,false)=false
                    and 
                        coalesce(translate_failed,false)=false 
                        
                )>0
            order by 3,2,1
            limit ".settings::getVal('chapters_translate_count');
        
        $chapters = $this->s_fetchAll($sql);
        
        if (!$chapters) return false;
        
        $out   = [];
        $c_ids = [];
        
        foreach ($chapters as $c) {
            $c_ids[] = $c['id'];
        }
        
        $sql = "
            select
                p.id,
                p.text_original,
                p.text_ru,
                p.text_en,
                v.novella_id,
                c.id as chapter_id,
                v.id as volume_id
            from
                paragraph p
            left join
                chapters c on c.id=p.chapter_id
            left join
                volumes v on v.id=c.volume_id
            where
                chapter_id in (".implode(',', $c_ids).")
            and
                coalesce(p.translated,false)=false
            and
                coalesce(p.translate_now,false)=false
            and
                coalesce(p.translate_failed,false)=false                 
            and
                p.id not in (select object_id from translate_failed where type='paragraph')
             
        ";
        
        $paras = $this->s_fetchAll($sql);
        
        
        return $paras;
    }
    
    function checkChapterTranslateFinishedByPar($p_id) {
        $chapter_id = $this->s_fetchOne("select chapter_id from paragraph where id=".(int)$p_id);
        if (!$chapter_id) return false;
        
        //$total = $this->s_fetchOne("select count(id) from paragraph where chapter_id=$chapter_id");
        //$untranslated = $this->s_fetchOne("select count(id) from paragraph where coalesce(translated,false)=false and chapter_id=$chapter_id");
        
        $untranslate_pars = (int)$this->s_fetchOne("select count(id) from paragraph where chapter_id=$chapter_id and (text_ru is null or text_ru='')");
        $total_pars = (int)$this->s_fetchOne("select count(id) from paragraph where chapter_id=$chapter_id");

        if ($total_pars)
            $percent = (int)ceil(($untranslate_pars/$total_pars)*100);
        else 
            $percent = 100;

        $percent = 100-$percent;

//        echo "\n\tTotal / Untranslated {$total_pars} / {$untranslate_pars} ({$percent}%)";
        
        $this->pq('query',"update chapters set translated_percent=$percent where id=$chapter_id");
        
        if ($percent==100) {
            $this->pq('query',"update chapters set translate_finish=now() where id=$chapter_id");
        }
        
        return true;
        
    }
    
    
    function getErrorParagraph() {
        $sql = "
            select
                t.date,
                p.*,
                c.name_ru as chapter_name,
                c.name_original as chapter_name_original,
                c.id as chapter_id
            from
                translate_failed t
            inner join
                paragraph p on p.id=t.object_id
            left join
                chapters c on c.id=p.chapter_id
            where
                t.type='paragraph'
        ";
        
        return $this->s_fetchAll($sql);
    }
    
    function getFailedParInfo($id) {
        $id = (int)$id;
        
        $sql = "
            select
                p.*,
                n.name as novella_name,
                n.name_original as novella_original_name,
                v.title_ru as volume_name,
                v.title as volume_original_name,
                c.name_ru as chapter_name,
                c.name_original as chapter_original_name,
                c.id as chapter_id
            from
                paragraph p 
            left join
                chapters c on c.id=p.chapter_id
            left join
                volumes v on v.id=c.volume_id
            left join
                novella n on n.id=v.novella_id
            where
                p.id=$id
        ";
        
        return $this->s_fetchRow($sql);
    }
}
