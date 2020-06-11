<?php

class aixdz extends provider {
    
    function __construct() {
        //$this->yandex = new yandexCloudTranslate($this->source_language);
        $this->yandex = new yandexTranslate("zh");
    }
    
    public $baseUrl = "";
    
    function getVolumes($url) {
        clog("aixdz Get VOLUMES ");
        $data = $this->getPage($url);
        $this->baseUrl = $url;
        $this->host = parse_url($url, PHP_URL_SCHEME).'://'.parse_url($url, PHP_URL_HOST);
        
        phpQuery::newDocument($data);
        $readUrl = pq(".d_info .d_ot:eq(1)")->attr('href');
        
        
        $data = $this->getPage($this->host.$readUrl);
        file_put_contents("read.txt", $data);
        phpQuery::newDocument($data);
        
        $items = pq(".catalog .chapter");
        
        $out = [];
        
        
        $chapter_num = 1;
        $volume_num = 1;
        
        $chapters = [];
        
        foreach ($items as $i) {
            $chapter_name = pq("a",$i)->text();

            $chapter_href = pq("a",$i)->attr('href');
            
            $chapter_href = $this->host.$readUrl.$chapter_href;

            $chapters[] = [
                'name'  => $chapter_name,
                'href'  => $chapter_href,
                'number' => $chapter_num
            ];

            $chapter_num++;

            clog("Found chapter {$chapter_name} ({$chapter_num})");
        }
        
        
        $volume = [
            'name'  => 'Том 1',
            'number'    => 1,
            'items' => $chapters
        ];
        
        
        return [$volume];
    }
    
    function getImage($url) {
        require_once('phpQuery.php');

        $data = $this->getPage($url);

        phpQuery::newDocument($data);
        
        $out['image'] = pq('.d_info .fdl img')->attr('src');
        
        return $out['image'];
    }
    
    function getInfo($url) {
        require_once('phpQuery.php');

        $data = $this->getPage($url);
        
        file_put_contents('info.txt', $url.'  '.$data);

        phpQuery::newDocument($data);
        $out = [];
        
        $out['image'] = pq('.d_info .fdl img')->attr('src');
        
        $out['name'] = str_replace("下","", pq('.crumbs li:last')->text());
        $out['name_original'] = $out['name'];
        
        $desc = trim(strip_tags(pq('.d_intro .d_co')->html()));
        $pos = mb_strpos($desc, 'Tags：');
        
        if ($pos!==false) {
            $out['description_original'] = mb_substr($desc, 0, $pos);
            $out['tags'] = mb_substr($desc, $pos+6);
        }
        
        
        
        $out['author'] = str_replace('作者：','', pq('.fdl li:eq(0)')->text());
        
        return $out;
    }
    
    function saveVolume($data, $novella_id) {
        $this->novella_id = $novella_id;
        
        $vm = ormModel::getInstance('public','volumes');
        $cm = ormModel::getInstance('public','chapters');
        
        //$yandex = yandexTranslate::getInstance($this->translate_direction);
        clog("AiXDz sync volumes ");
        
        foreach ($data as $volume) {
            if (!trim($volume['name'])) continue;
            
            $volume_data = $vm->getRow("number={$volume['number']} and novella_id=$novella_id");
            
            
            
            if (!$volume_data) {
                $d = [
                    'title'         => $volume['name'],
                    'novella_id'    => $novella_id,
                    
                    //'title_ru'      => $this->yandex->translate($volume['name'], $this->novella_id),
                    'number'        => $volume['number']
                ];
                $vm->newItem($d);
                
                $volume_id = $vm->last_id;
            }else {
                $volume_id = $volume_data['id'];
                if ($volume_data['title']!==$volume['name']) {
                    $vm->updateItem([
                        'title'         => $volume['name'],
                        'title_ru'      => ''
                        //'title_ru'      => $this->yandex->translate($volume['name'], $this->novella_id)
                    ],'id='.$volume_id);
                    
                    clog("Volume title updated");
                }
            }
            
            if (!$volume_id) throw new Exception('cannot save volume '.$volume['name']);
                
            $last_chapter_num = (int)$cm->get('max(number)','volume_id='.$volume_id);
                
            foreach ($volume['items'] as $c) {
                $chapter_data = $cm->getRow("volume_id=$volume_id and number={$c['number']}");
                clog("Sync chapter #".$c['number'].' '.$c['name']);
                /////////////////// chapter name sync ///////////
                if ($chapter_data && $chapter_data['name_original']!=$c['name']) {
                    $cm->updateItem([
                        'name_original' => $c['name'],
                        //'name_ru'       => $this->yandex->translate($c['name'], $this->novella_id)
                        'name_ru'       => ''
                    ],'id='.$chapter_data['id']);
                    clog("Save NEW chapter name {$c['number_parsed']}");
                }
                /////////////////// chapter num sync ///////////
                if ($chapter_data && $chapter_data['number_parsed']!=$c['number_parsed']) {
                    $cm->updateItem([
                        'number_parsed' => $c['number_parsed']
                    ],'id='.$chapter_data['id']);
                    
                    clog("Save NEW chapter number {$c['number_parsed']}");
                }
                
                
                if ((int)$c['number']<=$last_chapter_num) {
                    clog("Chapter exists skip it");
                    continue;
                }

                clog("Save NEW chapter #".$c['number'].' '.$c['name']);
                
                $d = [
                    'volume_id'     => $volume_id,
                    'name_original' => $c['name'],
                    //'name_ru'       => $this->yandex->translate($c['name'], $this->novella_id),
                    'chapter_url'   => $c['href'],
                    'number'        => $c['number'],
                    'last_sync'     => new Zend_Db_Expr("now()-interval '2 days'")
                ];
                
                if ($c['number_parsed'])
                    $d['number_parsed'] = $c['number_parsed'];
                
                $cm->newItem($d);
                
            }
                
        }
        
    }
    
    function sync($novella_id) {
        
        $this->novella_id = $novella_id;
        //$yandex = yandexTranslate::getInstance($this->translate_direction);
        
        $nmodel = ormModel::getInstance('public','novella');
        $cmodel = ormModel::getInstance('public','chapters');
        $vmodel = new volumesModel();
        
        $novella = $nmodel->getRow('id='.$novella_id);
        
        clog("SYNC volumes");
        //$remote_volumes = $this->getVolumes($novella['url']);
        //$this->saveVolume($remote_volumes, $novella_id);
        
        $volumes = $vmodel->getVolumesForTranslate( $novella_id );

        if (!$volumes) clog("Все тома этой новэллы переведены. Переходим к следующей.");
        $upsync = true; /// флаг обновления даты синхронизации
        
        
        foreach ($volumes as $v) {
            
            //////// REMOTE GET    //////////////////
            
            //////// TEXT RECEIVER //////////////
            clog("Work with volume #".$v['number'].' '.$v['title']);
            $chapters = $cmodel->getAll("volume_id={$v['id']} and last_sync<now()-interval '1 day' and translate_finish is null", 'number');
            
            if (!$chapters) clog("Все главы этого тома переведены переходим к следующему");
            clog("Sync chapters text Num of chapters ". sizeof($chapters));
            
            foreach ($chapters as $c) {
                $this->syncChapter($c);
            }
        }
        
        
        if ($upsync)
            $nmodel->updateItem([
                'last_sync' => new Zend_Db_Expr('now()')
            ],'id='.$novella_id);

    }
    
    function syncChapter($c) {
        $cmodel = ormModel::getInstance('public','chapters');
        $pmodel = ormModel::getInstance('public','paragraph');
        
        clog("AIXDZ Sync chapter #".$c['number']);

        $paragraphs = $this->getChapterParagraphs($c['chapter_url']);

        $paragraph_last_index = (int)$pmodel->get('max(index) as m','chapter_id='.$c['id']);

        clog("Paragraph last index $paragraph_last_index");

        foreach ($paragraphs as $p) {

            if ($p['index']<=$paragraph_last_index) continue;

            clog("Save new paragraph ".$p['index']);

            if (!$pmodel->newItem([
                'text_original'         => $p['text'],
                'text_original_sha1'    => $p['sha1'],
                //'text_ru'               => $this->yandex->translate($p['text_en'], $this->novella_id),
                'index'                 => $p['index'],
                'chapter_id'            => $c['id']
            ])) throw new Exception($pmodel->last_error);
/*
            $pmodel->updateItem([
                'ru_search_index'   => new Zend_Db_Expr("to_tsvector('russian', text_ru)")
            ],'id='.$pmodel->last_id);
 */
        }

        $cmodel->updateItem([
            'last_sync' => new Zend_Db_Expr('now()')
            //'translate_finish' => new Zend_Db_Expr('now()')
        ],'id='.$c['id']);
        
        clog("Chapter #{$c['number']} parse finish! Last sync updated ". strftime('%r'));
    }
    
    function getChapterParagraphs($chapter_url) {
        require_once('phpQuery.php');
        
        $data = $this->getPage($chapter_url);

        phpQuery::newDocument($data);
        
        $paragraphs = pq('.content p');
        
//        file_put_contents('paras', pq('#content')->text());
        
        $out = [];
        $index = 1;
        foreach ($paragraphs as $p) {
            $t = pq($p)->text();
            if (!$t) continue;
            
            $out[] = [
                'text'  => $t,
                'index' => $index,
                'sha1'  => sha1($t)
            ];
            
            $index++;
        }
        
        return $out;
    }
    
    function getChapters($volume) {
        clog("AIXZSD Get chapters for volume #".$volume['number']);
        $cm = ormModel::getInstance('public', 'chapters');
        
        return $cm->getAll("1=1",'number');
    }
    
    
    function getGenres($url) {
        $out = [];
        
        return $out;
    }
    
    function getTags($url) {
        require_once('phpQuery.php');
        
        $data = $this->getPage($url);
        
//        file_put_contents('info.txt', $url.'  '.$data);

        phpQuery::newDocument($data);
        $out = [];
        
        $desc = trim(strip_tags(pq('.d_intro .d_co')->html()));
        $pos = mb_strpos($desc, 'Tags：');
        
        if ($pos!==false) {
            $tags = mb_substr($desc, $pos+6);
            
            $tags = explode("，", $tags);
        }
        
        foreach ($tags as $t)
            $out[] = $t;
        
        return $out;
        
            
    }
    
}
