<?php
/*
 * Developed by GLENN.ru
 */
class chaptersModel extends ormModel {
    public $schema = 'public';
    public $table = 'chapters';
    
    function getChaptersForAdmin($volume_id) {
        
        $sql = " 
            select 
                c.id,
                c.number as chap_num,
                v.number as vol_num,
                (
                    select 
                        count(id) 
                    from 
                        paragraph 
                    where 
                        chapter_id=c.id 
                        
                ) as total_pars,    
                (
                    select 
                        count(id) 
                    from 
                        paragraph 
                    where 
                        chapter_id=c.id 
                    and 
                        coalesce(translated,false)=true
                        
                ) as trans_pars,
                
                c.*
                
            from 
                chapters c 
            inner join
                volumes v on v.id=c.volume_id
            where
                v.id=".(int)$volume_id."
                
            order by 3,2,1
            ";
        
        return $this->s_fetchAll($sql);
    }
    function getLatest($novella_id) {
        $novella_id = (int)$novella_id;
        
        $sql = "
            select
                c.*,
                n.id as novella_id,
                v.number as volume_number,
                n.name as novella_name
            from
                chapters c
            left join
                volumes v on v.id=c.volume_id
            
            left join
                novella n on n.id=v.novella_id
            where
                n.id=$novella_id
            and
                c.translate_finish is not null
            
            order by c.translate_finish desc
            limit 3
        ";
        
        return $this->s_fetchAll($sql);
    }
    
    function getLastChapters($limit=20, $page=1) {
        
        $this->setItemsPerPage($limit);
        $this->setCurrentPage($page);
        
        $key = "lastMain";

        $sql = "
            select
                c.*,
                n.id as novella_id,
                v.number as volume_number,
                v.title_ru as volume_name,
                n.name as novella_name,
                n.image as novella_image
            from
                chapters c
                
            left join
                volumes v on v.id=c.volume_id
                
            left join
                novella n on n.id=v.novella_id
            where
            	c.name_ru is not null 
            and
            	c.name_ru!=''                
            and
                c.translate_finish is not null
                
            order by c.translate_finish desc
        ".$this->getPageExpr();
        
        $latest_chapters = $this->s_fetchAll($sql);
        /*
        if ($latest_chapters)
            tools_cache::save($key, $latest_chapters, 600);
        */
        return $latest_chapters;
    }    
    
    function getLatestChapters($limit=20) {
        
        $key = "latestMain";
        
        $cached = tools_cache::get($key);
        
        if ($cached && $cached!=='!!nocache!!') return $cached;
        
        $sql = "
            select
                c.*,
                n.id as novella_id,
                v.number as volume_number,
                n.name as novella_name
            from
                chapters c
                
            left join
                volumes v on v.id=c.volume_id
                
            left join
                novella n on n.id=v.novella_id
            where
            	c.name_ru is not null 
            and
            	c.name_ru!=''                
            and
                c.translate_finish is not null
                
            order by c.translate_finish desc
            
            limit $limit
        ";
        
        $latest_chapters = $this->s_fetchAll($sql);
        $sorted = [];
        foreach ($latest_chapters as $c) {
            $sorted[$c['novella_id']]['name'] = $c['novella_name'];
            $sorted[$c['novella_id']]['id'] = $c['novella_id'];
            
            $chapters = &$sorted[$c['novella_id']]['chapters'];
            if (!$chapters) $chapters = [];
            
            $chapters[] = $c;
        }
        
        tools_cache::save($key, $sorted, 600);
        
        return $sorted;
    }

    function getChaptersCountByNovellaId(int $novella_id)
    {
        $sql = "
            SELECT COUNT(*) AS count 
            FROM chapters 
            WHERE volume_id IN (
                SELECT id FROM volumes WHERE novella_id = $novella_id
            )";

        return $this->s_fetchAll($sql)[0]['count'];
    }

    function getChapters($volume_id, $page=1) {
        $volume_id = (int)$volume_id;
        $page = (int)$page;
        
        $this->setCurrentPage($page);
        $this->setItemsPerPage(20);
        $sql = "
            select
                *
            from
                chapters c
            where
               volume_id=$volume_id
            and
                translate_finish is not null
            order by number
        ".$this->getPageExpr();
        
        return $this->s_fetchAll($sql);
    }
    
    function getLastChaptersPagesCount($perPage=10) {

        $key = "latestMainPC";
        
        $cached = tools_cache::get($key);
        
        if ($cached && $cached!=='!!nocache!!') return $cached;
        
        $sql = "
            select
                count(c.id)
            from
                chapters c
                
            left join
                volumes v on v.id=c.volume_id
                
            left join
                novella n on n.id=v.novella_id
            where
            	c.name_ru is not null 
            and
            	c.name_ru!=''                
            and
                c.translate_finish is not null
        ";
        
        $total_chapters = $this->s_fetchOne($sql);
        
        $perPage = ceil($total_chapters / $perPage);
        
        
        tools_cache::save($key, $perPage, 600);
        
        return $perPage;        
    }
    
    function getTotalPages($volume_id) {
        $volume_id = (int)$volume_id;
        $this->setItemsPerPage(20);
        return $this->getTotalPagesCount($this->get('count(id)','volume_id='.$volume_id.' and translate_finish is not null '));
    }
    
    function getNovellaByChapter($chapter_id) {
        $sql = "
            select
                n.id
            from
                novella n
            inner join
                volumes v on v.novella_id=n.id
            inner join
                chapters c on c.volume_id=v.id
            where
                c.id=$chapter_id
        ";
        
        return $this->s_fetchOne($sql);
    }
    
    function getData($chapter_id) {
        $chapter_id = (int)$chapter_id;
        
        $key = 'ch'.$chapter_id;
        
        $cached = tools_cache::get($key);
        if ($cached && $cached!=='!!nocache!!') return $cached;
        
        $novella = $this->getNovellaByChapter($chapter_id);
        
        if (!$novella) return false;
        
        $sql = "
            select
                c.*,
                n.name as novella_name,
                n.id as novella_id,
                v.number as volume_number,
                q.prev_chapter,
                q.next_chapter
            from
                (
                    with chaps as (
                        select
                            c.id,
                            c.number as chap_num,
                            v.number as volume_number
                        from
                            chapters c
                        inner join
                            volumes v on v.id=c.volume_id
                        where
                            v.novella_id=$novella
                    )
                    select
                        chaps.id,
                        lead(chaps.id) over(order by chaps.volume_number, chaps.chap_num) as next_chapter,
                        lag(chaps.id) over(order by chaps.volume_number, chaps.chap_num) as prev_chapter
                    from
                        chaps
                ) q
            left join
                    chapters c on c.id=q.id    
            left join
                volumes v on c.volume_id=v.id
            left join
                novella n on n.id=v.novella_id
            where
                    q.id=$chapter_id          

        ";
        $data = $this->s_fetchRow($sql);
        
        tools_cache::save($key, $data, 86400*7);
        
        return $data;
        
    }
    
    function search($t) {
        $sql = "
            select
                c.*,
                c.id as chapter_id,
                n.image as novella_image,
                n.name as novella_name,
                c.name_ru as chapter_name,
                'chapter' as type
            from
                chapters c
            left join
                volumes v on v.id=c.volume_id
            left join
                novella n on n.id=v.novella_id
            where
                c.name_ru ilike '%$t%'
            or
                c.name_original ilike '%{$t}%'
        ";
        
        return $this->s_fetchAll($sql);
    }
    
    function getChaptersToTranslate($chapters_ids=false) {
        
        $data = [];
        
        if ($chapters_ids) {
            $sql = "
                select
                    id,
                    name_original,
                    name_ru
                from	
                        chapters
                where
                    (name_ru='' or name_ru is null)
                and
                    coalesce(now_translated,false) = false

                and id in (".implode(',', $chapters_ids).")
            ";
        
            $data = $this->s_fetchAll($sql);
        }

        $sql2 = "
            select
                c.id,
                c.name_original,
                c.name_ru,
                v.number,
                c.number
            from	
                chapters c
            inner join
                volumes v on v.id=c.volume_id
            inner join 
                novella n on n.id=v.novella_id
            where
                (c.name_ru='' or c.name_ru is null)
            and
                coalesce(c.now_translated,false) = false
            and
                n.source='wuxia'
            ".(($chapters_ids) ? ' and c.id not in ('.implode(',', $chapters_ids).')' : '')."
            order by v.number, c.number, c.translated_percent desc nulls last
            
            limit ".settings::getVal('chap_tr_count');

        
        
        $data2 = $this->s_fetchAll($sql2);
        
        return array_merge($data,$data2);
    }
    
    function getTranslatedChaptersCount() {
        $sql = "
            select
                count(id)
            from
                chapters
            where
                translate_finish is not null
        ";
        
        return $this->s_fetchOne($sql);
    }
    
    function get2dayTranslatedChaptersCount() {
        $sql = "
            select
                count(id)
            from
                chapters
            where
                translate_finish is not null
            and
                translate_finish>now() - interval '24 hours'
        ";
        
        return $this->s_fetchOne($sql);
    }
    
}
