<?php

class admin_NovellaController extends adminController {
    
    function initController() {
        parent::initController();
        
        //$this->yandex = new yandexCloudTranslate("en");
        $this->yandex = new translaTor("en");
    }
    
    function indexAction() {
        
    }
    
    
    function editAction() {
        $this->ajax();
        $this->renderTplToContent('edit_novella.tpl');
    }
    
    
    function saveAction() {
        $this->ajax();
        $m = new novellasModel();
        
        try {
            if (isset($this->params['id'])) {
                $m->updateItem($this->params['db'],'id='.(int)$this->params['id']);
                
                $this->saveGenres($this->params['genres'], $this->params['id']);
                $this->saveTags($this->params['tags'], $this->params['id']);
                $this->saveVolumeTitles($this->params['volume_title'], $this->params['id']);
            }else {
                
                $provider = $m->getProvider($this->params['db']['url']);
                
                if ($provider===false) throw new Exception('неизвестный источник');

                $m->newItem($this->params['db']);
                $id = $m->last_id;
                if (!$id) throw new Exception("Ошибка добавления. Такая новелла уже скорее всего есть. ".$m->last_error);
                
                $this->saveImage($id);
                $this->saveVolumes($id, $this->params['db']['url'], $provider);

                $this->saveGenresFromSource($id, $this->params['db']['url'], $provider);
                $this->saveTagsFromSource($id, $this->params['db']['url'], $provider);
            }
        }catch (Exception $e) {
            $m->last_error = $e->getMessage();
        }
        $this->jsonAnswer([
            'error' => $m->last_error
        ]);
    }
    
    function saveImage($novela_id) {
        if (isset($this->params['image']) && $this->params['image']) {
            
            if (!is_dir('public/novellas')) mkdir('public/novellas',0777,true);
            
            $extension = pathinfo(parse_url($this->params['image'], PHP_URL_PATH), PATHINFO_EXTENSION);
            $filename = $novela_id.'.'.$extension;
            
            $data = file_get_contents($this->params['image']);
            
            file_put_contents('public/novellas/'.$filename, $data);
            
            ormModel::getInstance('public','novella')->updateItem([
                'image' => $filename
            ],'id='.$novela_id);
        }
    }
    
    function voldelnamesAction() {
        $this->ajax();
        $cm = new chaptersModel();
        
        $cm->updateItem([
            'name_ru'   => new Zend_Db_Expr('null')
        ],'volume_id='.(int)$this->params['id']);
        
        $this->jsonAnswer([
            'error' => $cm->last_error
        ]);
        
    }

    function saveVolumeTitles($titles, $novella_id) {
        $model = ormModel::getInstance('public','volumes');

        foreach ($titles as $id => $title) {
            if ($title && $model->get('id', 'id=' . $id)) {
                $model->updateItem([
                    'title_ru' => $title
                ], 'id=' . $id);
            }
        }
    }

    function saveGenres($genres, $nov_id) {
        //$yandex = new yandexTranslate("en-ru");
        
        $tm = ormModel::getInstance('public','genres');
        $linkModel = ormModel::getInstance('public','novella_genres');
        
        $arr = explode(',',$genres);
        
        foreach ($arr as $g) {
            
            $n = preg_replace("#([\"'+%$])#ui",'',$g);
            
            $id = $tm->get("id","name='$n'");
            
            if (!$id) {
                
                $tr = '';
                
                try {
                    $tr = $this->yandex->translate($n);
                }catch (Exception $e) {}
                
                $tm->newItem([
                    'name'  => $n,
                    'name_ru'   => $tr
                ]);
                
                $id = $tm->last_id;
            }
            
            if (!$linkModel->get('id',"novella_id=$nov_id and genre_id=$id")) {
                $linkModel->newItem([
                    'genre_id'    => $id,
                    'novella_id' => $nov_id
                ]);
            }
            
        }        
    }
    
    function saveTags($tags, $nov_id) {
        //$yandex = new yandexTranslate("en-ru");
        
        $tm = ormModel::getInstance('public','tags');
        $linkModel = ormModel::getInstance('public','novella_tags');
        
        $arr = explode(',',$tags);
        
        foreach ($arr as $t) {
            
            $n = preg_replace("#([\"'+%$])#ui",'',$t);
            
            $id = $tm->get("id", "name='$n'");
            
            if (!$id) {
                
                $tr = '';
                
                try {
                    $tr = $this->yandex->translate($n);
                }catch (Exception $e) {}
                
                $tm->newItem([
                    'name'  => $n,
                    'name_ru'   => $tr
                ]);
                
                $id = $tm->last_id;
            }
            
            if (!$linkModel->get('id',"novella_id=$nov_id and tag_id=$id")) {
                $linkModel->newItem([
                    'tag_id'    => $id,
                    'novella_id' => $nov_id
                ]);
            }
            
        }
    }
    
    function saveGenresFromSource($novella_id, $url, $provider) {
        $genres = $provider->getGenres($url);
        $gm = ormModel::getInstance('public','genres');
        $linkModel = ormModel::getInstance('public','novella_genres');
        //$yandex = yandexTranslate::getInstance();
        
        foreach ($genres as $g) {
            $id = $gm->get('id',"name='$g'");
            if (!$id) {
                $gm->newItem([
                    'name'      => $g,
                    'name_ru'   => $this->yandex->translate($g)
                ]);
                
                $id = $gm->last_id;
            }
            
            $linkModel->newItem([
                'novella_id'    => $novella_id,
                'genre_id'      => $id
            ]);
        }
    }
    
    function saveTagsFromSource($novella_id, $url, $provider) {
        $tags = $provider->getTags($url);
        $tm = ormModel::getInstance('public','tags');
        $linkModel = ormModel::getInstance('public','novella_tags');
        //$yandex = yandexTranslate::getInstance();
        
        foreach ($tags as $t) {
            $id = $tm->get('id',"name='$t'");
            if (!$id) {
                $tm->newItem([
                    'name'      => $t,
                    'name_ru'   => $this->yandex->translate($t)
                ]);
                
                $id = $tm->last_id;
            }
            
            $linkModel->newItem([
                'novella_id'    => $novella_id,
                'tag_id'      => $id
            ]);
        }
    }

    
    function saveVolumes($novella_id, $url, $provider) {
        
        $volumes = $provider->getVolumes($url);
        
        $provider->saveVolume($volumes, $novella_id);
        //foreach ($volumes as $v) {
            ///$provider->saveVolume([$v],$novella_id);
        //}
        
        
    }
    
    function delAction() {
        $this->ajax();
        $m = ormModel::getInstance('public','novella');
        $m->del('id='.(int)$this->params['id']);
        $this->jsonAnswer([
            'error' => $m->last_error
        ]);
    }
    
    function loadchaptersAction() {
        $this->ajax();
        $this->renderTplToContent('chapters.tpl');
    }
    
    function volumesAction() {
        $this->ajax();
        $this->renderTplToContent('volumes_data.tpl');
    }
    
    
    function getinfoAction() {
        $this->ajax();
        
        try {
            $error = '';
            
            $p = &$this->params;
            $info = [];
            $m = new novellasModel();
            
            $prov = $m->getProvider($p['url']);
            
            if ($prov instanceof aixdz)
                $this->yandex = new translaTor("zh");
            
            if (!$prov) throw new Exception('Неизвестный поставщик');

            $info = $prov->getInfo($p['url']);
            
            try {
                
                if ($info['name_original']) {
                    $info['name_ru'] = $this->yandex->translate($info['name_original']);
                }

                if ($info['description_original'])
                    $info['description_ru'] = $this->yandex->translate($info['description_original']);
                
            }catch (Exception $e) {
                //$info['description_ru'] = $info['name_ru'] = 'ошибка перевода';
            }
        }catch (Exception $e) {
            $error = $e->getMessage();
        }
        
        $this->jsonAnswer([
            'info'  => $info,
            'error' => $error
        ]);
        
    }
    
    
    function syncvolumeAction() {
        $vm = new volumesModel();
        $nm = new novellasModel();
        
        $vdata = $vm->getRow("id=".(int)$this->params['id']);
        $ndata = $nm->getRow("id=".$vdata['novella_id']);
        
        $provider = $nm->getProvider($ndata['url']);
        
        $volumes = $provider->getVolumes($ndata['url']);
        
        if ($volumes) {
            foreach ($volumes as $v) {
                if ($v['number']==$vdata['number']) {
                    //$vm->del("id=".$vdata['id']);
                    $provider->saveVolume([$v],$ndata['id']);
                    break;
                }
            }
        }
        
        $this->ajax();
        
        $this->jsonAnswer([
            'error' => ''
        ]);
        
    }
    
    
    function regetvolumesAction() {
        try {
            $err = '';
            $vm = new volumesModel();
            
            $id = (int)$this->params['id'];

            $vm->del('novella_id='.$id);

            $nm = new novellasModel();

            $ndata = $nm->getRow("id=".$id);

            $provider = $nm->getProvider($ndata['url']);
            
            if (!$provider) throw new Exception('unknown provider '.$ndata['url']);

            
            $remote_volumes = $provider->getVolumes($ndata['url']);
            $provider->saveVolume($remote_volumes, $id);
            
            $nm->updateItem([
                'last_sync' => new Zend_Db_Expr('null')
            ], 'id='.$id);

        }catch (Exception $e) {
            $err = $e->getMessage();
        };
        
        $this->ajax();
        
        $this->jsonAnswer([
            'error' => $err
        ]);
    }
    
    
    function reloadchapterAction() {
        $this->ajax();
        try {
            $p = &$this->params;
            $err = '';
            
            $pm = new paragraphModel();
            $cm = new chaptersModel();
            $nm = new novellasModel();

            $pm->del("chapter_id=".$p['id']);
            $novella_id = $cm->getNovellaByChapter($p['id']);

            $provider = $nm->getNovellaProvider($novella_id);
            $chapter_data = $cm->getRow("id=".$p['id']);

            $provider->syncChapter($chapter_data);
            
        }catch (Exception $e) {
            $err = $e->getMessage();
        }
        
        $this->jsonAnswer([
            'error' => $err,
            'pars'  => $pm->get('count(id)',"chapter_id=".$p['id'])
        ]);
    }
    
    function tAction() {
        $this->ajax();
        $n = new wuxiaworld();
        
        $n->getVolumes('https://www.wuxiaworld.co/Reincarnation-Of-The-Strongest-Sword-God/');
        
    }
}
