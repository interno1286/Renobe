<?php

class webnovel extends provider {
    
    public $translate_direction = "en-ru";
    public $source_language = "en";
    
    public $volume_id_field = 'lnmtl_volume_id';
    
    public $yandex = null;

    public $novella_id = false;
    
    function getVolumes($url) {
        return [];
    }
    
    function saveVolume($remote_volumes, $novella_id) {
        
    }
    
    function sync($novella_id) {
        
        $this->novella_id = $novella_id;
        
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
            
            //////// TEXT RECEIVER //////////////
            clog("Work with volume #".$v['number'].' '.$v['title']);
            //$chapters = $cmodel->getAll("volume_id={$v['id']} and last_sync<now()-interval '1 day' and translate_finish is null", 'number');
            $chapters = $cmodel->getAll("volume_id={$v['id']} and last_sync<now()-interval '30 day'", 'number');
            
            if (!$chapters) clog("Все главы этого тома переведены переходим к следующему");
            clog("Sync chapters text Num of chapters ". sizeof($chapters));
            
            $chapters_translate_count = (int)settings::getVal('chapters_translate_count');
            
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
        
        clog("WEBNOVEL Sync chapter #".$c['number']);

        $paragraphs = $this->getChapterParagraphs($c['chapter_url']);

        $paragraph_last_index = (int)$pmodel->get('max(index) as m','chapter_id='.$c['id']);

        clog("Paragraph last index $paragraph_last_index");

        foreach ($paragraphs as $p) {
            
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
        }

        $cmodel->updateItem([
            'last_sync' => new Zend_Db_Expr('now()')
        ],'id='.$c['id']);
        
        clog("Chapter #{$c['number']} parse finish! Last sync updated ". strftime('%r'));
    }

    function getChapterParagraphs($chapter_url) {
        require_once('phpQuery.php');
        
        $data = $this->getPage($chapter_url);

        phpQuery::newDocument($data);
        
        $paragraphs = pq('.cha-words p');
        
        $out = [];
        $index = 1;
        foreach ($paragraphs as $p) {
            $t = trim(pq($p)->text());
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



    function getImage($url) {
        throw new Exception('Вы должны использовать другой метод добавления новелл с Webnovell');
    }
    
    function getInfo($url) {
        throw new Exception('Вы должны использовать другой метод добавления новелл с Webnovell');

    }

}
