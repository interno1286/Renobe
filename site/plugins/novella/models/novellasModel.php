<?php
/*
 * @author glenn.ru
 */
class novellasModel extends ormModel {
    
    public $schema = 'public';
    public $table = 'novella';
    
    
    function getList($params) {
        
        $key = 'nlist'.md5(print_r($params,1));
        
        $cached = tools_cache::get($key);
        
        if ($cached && $cached!=='!!nocache!!') return $cached;
        
        $sql = "
            select
                n.*
            from
                novella n
            where
                (
                    select 
                            count(id) 
                    from 
                            paragraph p 
                    where 
                            p.chapter_id in (
                                            select 
                                                id 
                                            from 
                                                chapters 
                                            where 
                                                volume_id in (select id from volumes where novella_id=n.id)
                                        )
                    and
                            p.text_ru is not null
                    and
                            p.text_ru!=''
                    )>0
        ";
        
        if (isset($params['filter'])) {
            $filter = preg_replace("#([^a-z_-])#ui",'',$params['filter']);
            
            if ($filter && $filter!=='all')
                $sql .= " and n.status='{$filter}'";
        }
        
        if ($params['tag']) {
            $t = (int)$params['tag'];
            $sql .= " and n.id in (select novella_id from novella_tags where tag_id=$t)";
        }

        if (isset($params['usedTags'])) {
            $tags = implode(',', $params['usedTags']);
            $sql .= " AND n.id IN (SELECT novella_id FROM novella_tags WHERE tag_id IN (" . $tags . ") )";
        }
        if (isset($params['unUsedTags'])) {
            $tags = implode(',', $params['unUsedTags']);
            $sql .= " AND n.id IN (SELECT novella_id FROM novella_tags WHERE tag_id NOT IN (" . $tags . ") )";
        }
        
        if ($params['genre']) {
            $t = (int)$params['genre'];
            $sql .= " and n.id in (select novella_id from novella_genres where genre_id=$t)";
        }
        
        if (isset($params['orderBy'])) {
            $o = preg_replace("#([^a-z_-])#ui",'',$params['orderBy']);
            if (!$params['order']) $params['order'] = 'desc';
            
            $sql .=' order by '.$o.' '.preg_replace("#([^a-z_-])#ui",'',$params['order']);
        }



        $data = $this->s_fetchAll($sql.' '.$this->getPageExpr());
        
        foreach ($data as &$d) {
            $d['tags'] = $this->getTags($d['id']);
            $d['genres'] = $this->getGenres($d['id']);
        }
        
        tools_cache::save($key, $data, 86400);
        
        return $data;
        
    }
    
    function getAllListCount($params) {
        
        $sql = "
            select
                count(n.id)
            from
                novella n
            where
                (
                    select 
                            count(id) 
                    from 
                            paragraph p 
                    where 
                            p.chapter_id in (
                                            select 
                                                id 
                                            from 
                                                chapters 
                                            where 
                                                volume_id in (select id from volumes where novella_id=n.id)
                                        )
                    and
                            p.text_ru is not null
                    and
                            p.text_ru!=''
                    )>0
                
        ";
        
        if ($params['filter']) {
            $f = preg_replace("#([^a-z0-9_-])#ui",'',$params['filter']);
            
            $sql .= " and n.status='$f'";
        }
        
        
        
        $data = $this->s_fetchOne($sql);
        
        return $data;
    }
    
    function getGenres($novella_id) {
        $novella_id = (int)$novella_id;
        
        $sql = "
            select
                CASE WHEN g.name_ru IS NOT NULL THEN g.name_ru
                ELSE g.name
                END AS name,
                g.id
            from
                novella_genres l
            inner join
                genres g on g.id=l.genre_id
            where
                l.novella_id=$novella_id
            order by 1
        ";
        
        return $this->s_fetchAll($sql);
    }
    
    function getTags($novella_id) {
        $novella_id = (int)$novella_id;
        
        $sql = "
            select
                CASE WHEN t.name_ru IS NOT NULL THEN t.name_ru
                ELSE t.name
                END AS name,
                t.id
            from
                novella_tags l
            inner join
                tags t on t.id=l.tag_id
            where
                l.novella_id=$novella_id
            order by 1
        ";
        
        return $this->s_fetchAll($sql);
        
    }
    
    function search($t) {
        
        $t = preg_replace('#(["\'%])#ui', '', $t);
        
        $sql = "
            select 
                n.*,
                'nov' as type
            from 
                novella n
            where
                (
                    select 
                            count(id) 
                    from 
                            paragraph p 
                    where 
                            p.chapter_id in (
                                            select 
                                                    id 
                                            from 
                                                    chapters 
                                            where 
                                                    volume_id in (select id from volumes where novella_id=n.id)
                                            )
                    and
                            p.text_ru is not null
                    and
                            p.text_ru!=''
                    )>0
            and
               n.name ilike '%{$t}%'
        ";
        
               
        return $this->s_fetchAll($sql);
    }
    
    function getMyFavs() {
        $sql = "
            select
                n.*,
                f.created
            from
                novella n
            inner join
                favorites f on f.novella_id=n.id
            where
                f.user_id={$this->user_data->id}
            order by 2 desc
        ";
                
        return $this->s_fetchAll($sql);
    }
    
    function getNovellasToTranslate() {
        $sql = "
            select 
                *
            from    
                novella
            where
                coalesce(translate_now,false)=false
            and
                (name is null or name='')
            and
                source='wuxia'
        ";
        
        return $this->s_fetchAll($sql);
    }
    
    function getFirstChapter($novella_id) {
        $sql = "
            select
                v.number,
                v.id,
                c.*
            from    
                chapters c
            left join
                volumes v on v.id=c.volume_id
            left join
                novella n on n.id=v.novella_id
            where
                n.id=$novella_id
            order by 1,2,3
            limit 1
        ";
        
        return $this->s_fetchRow($sql);
    }
    
    
    function getNovellaProvider($novella_id) {
        $novella_data = $this->getRow('id='.$novella_id);
        return $this->getProvider($novella_data['url']);
    }
    
    function getProvider($url) {
        switch (true) {
            case (mb_strpos($url, 'wuxiaworld.co')):
                $provider = new wuxiaworld();
                break;
            
            case (mb_strpos($url, 'aixdzs.com')):
                $provider = new aixdz();
                break;
            
            case (mb_strpos($url, 'lnmtl')):
                $provider = new lnmtl();
                break;
            
            case (mb_strpos($url, 'wuxianovel')):
                $provider = new wuxianovel();
                break;
            
            case (mb_strpos($url, 'webnovel')):
                $provider = new webnovel();
                break;
            
            default:
                return false;
        }
        
        return $provider;
    }

    function setViews($novella_id)
    {
        $sql = "INSERT INTO novella_views(novella_id, count)
                VALUES (" . $novella_id . ", 1) 
                ON CONFLICT (novella_id) DO UPDATE 
                SET count = novella_views.count + 1 
                WHERE novella_views.novella_id = " . $novella_id . " 
        ";
        $this->pq('query', $sql);
    }

}
