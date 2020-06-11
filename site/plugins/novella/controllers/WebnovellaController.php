<?php

class novella_WebnovellaController extends SiteBaseController {
    
    function initController() {
        parent::initController();
        header("Access-Control-Allow-Origin: *");
        
        $this->yandex = new translaTor();
        $this->yandex->srcLng = 'en';
    }
    
    function saveAction() {
        $this->ajax();
        
        if ($this->params['k']!='xh73g8') {
            $this->view->content = 'ok';
            return false;
        }
        
        $data = json_decode(base64_decode($this->params['data']));
        
        try {
            
            $nm = new novellasModel();
            
            $nm->beginTransaction();
            
            $vm = new volumesModel();
            $cm = new chaptersModel();
            
            if ($nm->get('id',"url='{$data->url}'")) throw new Exception('Такая новелла уже есть');
            
            $db_info = [
                'name' => $this->yandex->translate($data->name),
                'name_original' => $data->name,
                'url'       => $data->url,
                'source'    => 'webnovel',
                'author'    => $data->author,
                'description_original' => $data->description,
                'description' => $this->yandex->translate($data->description)
            ];
            
            $nm->newItem($db_info);
            
            $id = $nm->last_id;
            
            if (!$id) throw new Exception($nm->last_error);
            
            $this->saveImage($id, $data);
            $this->saveTags($data->tags, $id);
            if ($data->volumes) {
                
                foreach ($data->volumes as $v) {
                    
                    $vm->newItem([
                        'title' => $v->name,
                        'title_ru'  => $this->yandex->translate($v->name),
                        'novella_id'    => $id,
                        'number'    => $v->number
                    ]);
                    
                    $volume_id = $vm->last_id;
                    
                    if (!$volume_id) throw new Exception($vm->last_error);
                    
                    
                    if ($v->chapters) {
                        foreach ($v->chapters as $c) {
                            if (!$cm->newItem([
                                'name_original' => $c->name,
                                'volume_id'     => $volume_id,
                                'chapter_url'   => $c->url,
                                'number'        => $c->number,
                                'number_parsed' => $c->number_parsed,
                                'last_sync'     => new Zend_Db_Expr("now() - interval '60 days'")
                            ])) throw new Exception($cm->last_error);
                        }
                    }
                }
            }
            
            $nm->commitTransaction();
            
        }catch (Exception $e) {
            
            $nm->rollbackTransaction();
            
            $err  = $e->getMessage();
        }
        
        $this->jsonAnswer([
            'error' => $err,
            'name'  => $db_info['name']
        ]);
    }
    
    
    function saveImage($novela_id, $d) {
        if ($d->img) {
            
            if (!is_dir('public/novellas')) mkdir('public/novellas',0777,true);
            
            $extension = pathinfo(parse_url($d->img, PHP_URL_PATH), PATHINFO_EXTENSION);
            $filename = $novela_id.'.'.$extension;
            
            $data = file_get_contents($d->img);
            
            file_put_contents('public/novellas/'.$filename, $data);
            
            ormModel::getInstance('public','novella')->updateItem([
                'image' => $filename
            ],'id='.$novela_id);
        }
    }
    
    function saveTags($tags, $novella_id) {
        
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
                'tag_id'        => $id
            ]);
        }
        
    }
    
}
