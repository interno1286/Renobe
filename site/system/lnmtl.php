<?php
/*
 * Developed by GLENN.ru
 */
class lnmtl extends provider {
    
    public $volume_id_field = 'lnmtl_volume_id';
    public $translate_direction = "zn-ru";
    public $source_language = "zn";
    
    public $yandex = null;
    
    public $novella_id = false;
    
    function __construct() {
        //$this->yandex = new yandexCloudTranslate($this->source_language);
        $this->yandex = new translaTor("en");
    }
    
    function getVolumes($url) {
        
        $data = $this->getPage($url);
        
        $pos = mb_strpos($data, 'lnmtl.volumes = [')+ mb_strlen('lnmtl.volumes = [');
        
        $len = (mb_strpos($data, ']', $pos)-$pos);
        
        $json = mb_substr($data, $pos, $len );
        //file_put_contents('json.txt', $json);
        return json_decode("[{$json}]");
    }
    
    
    function getImage($url) {
        require_once('phpQuery.php');

        $data = $this->getPage($url);

        phpQuery::newDocument($data);
        
        return pq('.media-left img')->attr('src');
    }
    
    function getInfo($url) {
        require_once('phpQuery.php');

        $data = $this->getPage($url);

        phpQuery::newDocument($data);
        $out = [];
        
        $out['image'] = pq('.media-left img')->attr('src');
        
        $out['name'] = pq('.novel-name')->html();
        $out['name_original'] = pq('.novel-name small')->text();
        $out['name'] = str_replace($out['name_original'], '', trim(strip_tags($out['name'])));
        
        $out['description_original'] = pq('.description p')->text();
        
        $out['author'] = pq('.container:eq(4) .panel-body span.label-default:eq(0)')->text();
        
        return $out;
    }
    
    function saveVolume($volumes, $novella_id) {
        $this->novella_id = $novella_id;
        
        
        foreach  ($volumes as $data) {

            clog("Save volume ".$data->title.' #'.$data->number);

            //$yandex = yandexTranslate::getInstance($this->translate_direction);

            $db = ormModel::getInstance('public', 'volumes');
            $db->newItem([
                'title'          => $data->title,
                //'title_ru'          => $this->yandex->translate($data->title, $this->novella_id),
                'lnmtl_novel_id' => $data->novel_id,
                'lnmtl_volume_id' => $data->id,
                'number'         => $data->number,
                'url'            => $data->slug,
                'novella_id'     => $novella_id
            ]);
        }
        
        
    }
    
    function sync($novella_id) {
        
        $this->novella_id = $novella_id;
        
        //$yandex = yandexTranslate::getInstance($this->translate_direction);
        
        $nmodel = ormModel::getInstance('public','novella');
        $cmodel = ormModel::getInstance('public','chapters');
        $vmodel = new volumesModel();
        
        $novella = $nmodel->getRow('id='.$novella_id);
        
        clog("Sync volumes");
        $orig_volumes = $this->getVolumes($novella['url']);

        $this->saveVolume($orig_volumes, $novella_id);
        /*
        foreach ($orig_volumes as $v)
            $this->saveVolume($v, $novella_id);
        */
        $volumes = $vmodel->getVolumesForTranslate($novella_id);
        
        if (!$volumes) clog("Все тома этой новэллы переведены. Переходим к следующей.");
        
        $upsync = true;
        
        foreach ($volumes as $v) {
            clog("Work with volume #".$v['number'].' '.$v['title']);
            ////// REMOTE GET  //////////////////
            
            $chapters = $this->getChapters($v);

            /////////// FILTER /////////////////
            clog("Filter chapters");
            $db_chapters = $cmodel->selectCol('lnmtl_chapter_id');
            
            $need_to_sync = [];
            
            foreach ($chapters as $sci) {
                if (!in_array($sci->id, $db_chapters)) {
                    $need_to_sync[] = $sci;
                }
            }
            
            ///////// DB INSERT NEW CHAPTERS ////////////////
            
            foreach ($need_to_sync as $chapter) {
                clog("Save chapter ".$chapter->id);
                $cmodel->newItem([
                    'name_original' => $chapter->title_raw,
                    'number'        => $chapter->number,
                    //'name_ru'       => $this->yandex->translate($chapter->title_raw, $this->novella_id),
                    'volume_id'     => $v['id'],
                    'chapter_url'   => $chapter->site_url,
                    'lnmtl_chapter_id' => $chapter->id
                ]);
            }
            
            
            //////// TEXT RECEIVER //////////////
            ///// TEMP DISABLED CHAPTERS TRANSLATE
            
            $chapters = $cmodel->getAll("volume_id={$v['id']} and last_sync<now()-interval '10 day' and translate_finish is null", 'number');
            
            clog("Sync chapters text Num of chapters ". sizeof($chapters));
            
            $chapters_translate_count = settings::getVal('chapters_translate_count');
            
            $chapter_counter = 0;
            
            foreach ($chapters as $c) {
                /*
                if ($chapter_counter>$chapters_translate_count) {
                    clog("Достигнут предельный размер переводимых глав в одной новэлле ".$chapters_translate_count." переходим к следуюей");
                    $upsync = false;
                    break 2;
                }
                */
                $this->syncChapter($c);
                /*
                //// check for retranslate request 
                if ((int)ormModel::getInstance('public', 'retranslate')->get('count(id)','finished is null')) {
                    clog("Обнаружен запрос повторного перевода! Делаем это в первую очередь");
                    $upsync = false;
                    break 2;
                }
                */
                $chapter_counter++;
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

        clog("Sync chapter #".$c['number']);

        $paragraphs = $this->getChapterParagraphs($c['chapter_url']);

        $paragraph_last_index = (int)$pmodel->get('max(index) as m','chapter_id='.$c['id']);

        clog("Paragraph last index $paragraph_last_index");

        foreach ($paragraphs as $p) {

            if ($p['index']<=$paragraph_last_index) continue;

            clog("Save new paragraph ".$p['index']);

            if (!$pmodel->newItem([
                'text_original'         => $p['text'],
                'text_original_sha1'    => $p['sha1'],
                //'text_ru'               => $this->yandex->translate($p['text'], $this->novella_id),
                'text_en'               => $p['text_en'],
                'index'                 => $p['index'],
                'chapter_id'            => $c['id']
            ])) throw new Exception($pmodel->last_error);

            $pmodel->updateItem([
                'ru_search_index'   => new Zend_Db_Expr("to_tsvector('russian', text_ru)")
            ],'id='.$pmodel->last_id);
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
        
        $paragraphs = pq('.chapter-body .original');
        $en_paragraphs = pq('.chapter-body .translated');
        
        $index = 1;
        $tranlated = [];
        foreach ($en_paragraphs as $p) {
            $translated[$index++] = pq($p)->text();
        }
        
        $out = [];
        $index = 1;
        foreach ($paragraphs as $p) {
            $text = pq($p)->text();
            $out[] = [
                'text'  => $text,
                'text_en'  => $translated[$index],
                'index' => $index,
                'sha1'  => sha1($text)
            ];
            
            $index++;
        }
        
        return $out;
    }
    
    function getChapters($volume) {
        clog("Get chapters for volume #".$volume['number']);
        $page = 1;
        $last_page = 10;
        
        $out = [];
        
        while ($last_page+1>$page) {
            if (!$volume['lnmtl_volume_id']) throw new Exception('NO lnmtl_volume_id on volume_id '.$volume['id']);
            
            $url = "https://lnmtl.com/chapter?page={$page}&volumeId=".$volume['lnmtl_volume_id'];

            $data = $this->getPage($url);

            if (!$data) break;
            
            $encoded = json_decode($data);

            if (!$encoded) break;
            
            clog("Get data for chapter - ok");
   
            $last_page = $encoded->last_page;
            
            if ($encoded->data)
                $out = array_merge ($out, $encoded->data);
            
            $page++;
        }
        
        return $out;
    }
    
    
    function getGenres($url) {
        require_once('phpQuery.php');
        
        $data = $this->getPage($url);

        phpQuery::newDocument($data);
        $out = [];
        $genres = pq('.panel-default:eq(3) ul.list-inline li');
        foreach ($genres as $g)
            $out[] = pq($g)->text();
        
        return $out;
    }
    
    function getTags($url) {
        require_once('phpQuery.php');
        
        $data = $this->getPage($url);

        phpQuery::newDocument($data);
        $out = [];
        $genres = pq('.panel-default:eq(4) ul.list-inline li');
        
        foreach ($genres as $g)
            $out[] = pq($g)->text();
        
        return $out;
        
    }
}
 