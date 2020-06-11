<?php

class wuxianovel extends provider {
    
    public $translate_direction = "en-ru";
    public $source_language = "en";
    
    public $volume_id_field = 'lnmtl_volume_id';
    
    public $yandex = null;
    
    public $novella_id = false;
    
    function __construct() {
        //$this->yandex = new yandexCloudTranslate($this->source_language);
        $this->yandex = new translaTor("en");
    }
    
    function getVolumes($url) {
        
        clog("WuxiaNovel Get chapters ");
        
        $data = $this->getPage($url);
        
        phpQuery::newDocument($data);
        
        $items = pq(".chap p");
        $out = [];
        $volume = false;
        
        $chapter_num = 1;
        $volume_num = 1;
        $volume = [
            'name'  => 'Том 1',
            'items' => []
        ];
        
        $volume['number'] = $volume_num;

        $chapter_num = 1;
        
        foreach ($items as $i) {
                
                $chapter_name = pq("a",$i)->text();
                
                $chapter_href = $url.pq("a",$i)->attr('href');
                
                preg_match('#Chapter (\d+) (.*)#', $chapter_name, $m);
                
                $chap_data = [
                    'name'  => $chapter_name,
                    'href'  => $chapter_href,
                    'number' => $chapter_num,
                    'number_parsed' => $m[1]
                ];
                
                $volume['items'][] = $chap_data;
                
                $chapter_num++;
                
                clog("Found chapter {$chapter_name} ({$chapter_num}) | {$chap_data['number_parsed']}");
            
        }
        
        if ($volume)
            $out[] = $volume;

        
        return $out;
    }
    
    function getImage($url) {
        require_once('phpQuery.php');

        $data = $this->getPage($url);

        phpQuery::newDocument($data);
        
        $out['image'] = pq('p.desc-thumb')->attr('src');
        
        return $out['image'];
    }
    
    function getInfo($url) {
        require_once('phpQuery.php');

        $data = $this->getPage($url);

        phpQuery::newDocument($data);
        $out = [];
        
        $out['image'] = pq('p.desc-thumb')->attr('src');
        
        $out['name'] = pq('h1')->html();
        $out['name_original'] = $out['name'];
        
        $out['description_original'] = trim(strip_tags(pq('.desc-text')->html()));
        
        $out['author'] = pq('.desc-story p a b')->text();
        
        return $out;
    }
    
    function saveVolume($data, $novella_id) {
        $this->novella_id = $novella_id;
        
        $vm = ormModel::getInstance('public','volumes');
        $cm = ormModel::getInstance('public','chapters');
        
        //$yandex = yandexTranslate::getInstance($this->translate_direction);
        clog("WuxiaNovel save volumes ");
        
        foreach ($data as $volume) {
            if (!trim($volume['name'])) continue;
            //$volume_id = $vm->get('id',"title='".pg_escape_string($volume['name'])."' and novella_id=$novella_id and number=");
            $volume_data = $vm->getRow("novella_id=$novella_id and number={$volume['number']}");
            
            if (!$volume_data) {
                $d = [
                    'title'         => $volume['name'],
                    'novella_id'    => $novella_id,
                    //'title_ru'      => $this->yandex->translate($volume['name'], $this->novella_id),
                    'title_ru'      => $volume['name'],
                    'number'        => $volume['number']
                ];
                $vm->newItem($d);
                
                $volume_id = $vm->last_id;
            }else {
                $volume_id = $volume_data['id'];
                if ($volume_data['title']!==$volume['name']) {
                    
                    $vm->updateItem([
                        'title'         => $volume['name'],
                        'title_ru'      => $volume['name']
                        //title_ru'      => $this->yandex->translate($volume['name'], $this->novella_id)
                    ],'id='.$volume_id);
                    
                    clog("Volume title updated");
                }
            }
            
            if (!$volume_id) throw new Exception('cannot save volume '.$volume['name']);
            
            $last_chapter_num = (int)$cm->get('max(number)','volume_id='.$volume_id);
                
            foreach ($volume['items'] as $c) {
                
                $chapter_data = $cm->getRow("volume_id=$volume_id and number={$c['number']}");
                
                /////////////////// chapter name sync ///////////
                if ($chapter_data && $chapter_data['name_original']!=$c['name']) {
                    $cm->updateItem([
                        'name_original' => $c['name'],
                        'name_ru'       => $this->yandex->translate($c['name'], $this->novella_id),
                        //'name_ru'       => ''
                    ],'id='.$chapter_data['id']);
                    clog("Update chapter name {$c['number_parsed']}");
                }
                
                /////////////////// chapter num sync ///////////
                if ($chapter_data && $chapter_data['number_parsed']!=$c['number_parsed']) {
                    $cm->updateItem([
                        'number_parsed' => $c['number_parsed']
                    ],'id='.$chapter_data['id']);
                    
                    clog("Update chapter number {$c['number_parsed']}");
                }
                
                if ((int)$c['number']<=$last_chapter_num) continue;

                clog("Save NEW chapter #".$c['number'].' '.$c['name']);
                
                $d = [
                    'volume_id'     => $volume_id,
                    'name_original' => $c['name'],
                    'name_ru'       => $this->yandex->translate($c['name'], $this->novella_id),
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
        $remote_volumes = $this->getVolumes($novella['url']);
        $this->saveVolume($remote_volumes, $novella_id);
        
        //$volumes = $vmodel->getVolumesForTranslate( $novella_id );
        $volumes = $vmodel->getVolumesForSync( $novella_id );
        

        if (!$volumes) clog("Все тома этой новэллы переведены. Переходим к следующей.");
        $upsync = true; /// флаг обновления даты синхронизации
        
        
        foreach ($volumes as $v) {
            
            //////// REMOTE GET    //////////////////
            
            //////// TEXT RECEIVER //////////////
            clog("Work with volume #".$v['number'].' '.$v['title']);
            //$chapters = $cmodel->getAll("volume_id={$v['id']} and last_sync<now()-interval '1 day' and translate_finish is null", 'number');
            $chapters = $cmodel->getAll("volume_id={$v['id']} and last_sync<now()-interval '30 day'", 'number');
            
            if (!$chapters) clog("Все главы этого тома переведены переходим к следующему");
            clog("Sync chapters text Num of chapters ". sizeof($chapters));
            
            $chapters_translate_count = (int)settings::getVal('chapters_translate_count');
            //$chapter_counter = 1;
            
            foreach ($chapters as $c) {
/*                
                if ($chapter_counter>$chapters_translate_count) {
                    clog("Достигнут предельный размер переводимых глав в одной новэлле ".$chapters_translate_count." переходим к следуюей");
                    $upsync = false;
                    break 2;
                }
                
                //// check for retranslate request 
                if ((int)ormModel::getInstance('public', 'retranslate')->get('count(id)','finished is null')) {
                    clog("Обнаружен запрос повторного перевода! Делаем это в первую очередь");
                    $upsync = false;
                    break 2;
                }
                $chapter_counter++;
                
*/                
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
        
        clog("WUXIANOVEL Sync chapter #".$c['number']);

        $paragraphs = $this->getChapterParagraphs($c['chapter_url']);

        $paragraph_last_index = (int)$pmodel->get('max(index) as m','chapter_id='.$c['id']);

        clog("Paragraph last index $paragraph_last_index");

        foreach ($paragraphs as $p) {
            
            if (mb_strpos($p['text_en'], ' you need to refresh the page to get')!==false) {
                clog("Found unfinished chapter TEXT skip all paragraph");
                $pmodel->del("chapter_id={$c['id']}");
                return false;
                break;
            }
            
            
            if ($p['index']<=$paragraph_last_index) continue;

            clog("Save new paragraph ".$p['index']);

            if (!$pmodel->newItem([
                //'text_original'         => $p['text'],
                'text_original_sha1'    => $p['sha1'],
                //'text_ru'               => $this->yandex->translate($p['text_en'], $this->novella_id),
                'text_en'               => $p['text_en'],
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
        
        //$para_one = explode("<br><br>", pq('#content')->html());
        //file_put_contents("paras.txt", pq('#content')->html());
        
        $paragraphs = explode("<br>", pq('#content')->html());
        
        $out = [];
        $index = 1;
        foreach ($paragraphs as $p) {
            $t = trim(preg_replace("#<script>.*</script>#ui",'',$p));
            if (!$t) continue;
            
            $out[] = [
                //'text'  => $t,
                'text_en'  => $t,
                'index' => $index,
                'sha1'  => sha1($t)
            ];
            
            $index++;
        }
        
        return $out;
    }
    
    function getChapters($volume) {
        clog("WUXIA Get chapters for volume #".$volume['number']);
        $cm = ormModel::getInstance('public', 'chapters');
        
        return $cm->getAll("1=1",'number');
    }
    
    
    function getGenres($url) {
        $out = [];
        
        return $out;
    }
    
    function getTags($url) {
        $out = [];
        
        return $out;
        
    }
    
    function grabAllNovellas() {
        $main_page = $this->getPage('https://www.wuxianovel.co/');
        
        phpQuery::newDocument($main_page);
        
        $cats = pq(".listcat a");
        $categorys = [];
        
        foreach ($cats as $c) {
            $categorys[] = [
                'name'  => pq($c)->text(),
                'url'   => pq($c)->attr('href')
            ];
        };
        
        echo "\nFound ".sizeof($categorys)." categorys";
        
        $novellas = [];
        
        foreach ($categorys as $c) {
            echo "\nWork with {$c['name']} {$c['url']}";
            $nextUrl = $c['url'];
            
            while ($nextUrl) {
                
                echo "\nGet novellas from page $nextUrl";
                
                $pageData = file_get_contents($nextUrl);

                phpQuery::newDocument($pageData);

                $this->parseNovellas($novellas);

                if (pq('.page a i.fa-arrow-right')->length>0) {
                    $nextUrl = pq('.page a i.fa-arrow-right')->parents('a')[0]->attr('href');
                }else $nextUrl = false;
            }
        }
        
        
    }
    
    function parseNovellas(&$novellas) {
        $n_items = pq(".liststory");

        foreach ($n_items as $n) {

            $url = pq("h4 a",$n)->attr('href');
            $name = pq("h4 a",$n)->text();

            echo "\nNovella found {$name} {$url}";

            $novellas[] = [
                'name'  => $name,
                'url'   => $url
            ];
        }
        
    }
}
