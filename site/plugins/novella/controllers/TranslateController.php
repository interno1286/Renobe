<?php

class novella_TranslateController extends SiteBaseController {
    
    
    function initController() {
        parent::initController();
        header("Access-Control-Allow-Origin: *");
    }
    
    public $key = '9q298cb982bcy3988vuxfddvwnbevgh';
    
    function suggestAction() {
        $this->ajax();
        $this->params['id'] = (int)$this->params['id'];
        $this->view->params = $this->params;
        if (!settings::getVal("user_translate")) {
            $this->view->content = 'Возможность перевода временна отключена';
        }else $this->renderTplToContent('translate_suggest.tpl');
    }
    
    function sendAction() {
        $this->ajax();
        
        try {
            
            $error = '';
            $approved = false;
            
            if (!settings::getVal("user_translate")) throw new Exception('Возможность перевода временна отключена');
            
            $type_fields = [
                'paragraph'     => 'paragraph_id',
                'description'   => 'description_id',
                'novella' => 'novella_id',
                'chapter' => 'chapter_id'
            ];
            
            if (!$this->user_data->id) throw new Exception('Вы должны быть зарегистрированы на сайте');
            $id = (int)$this->params['id'];
            
            $m = ormModel::getInstance('public','user_translate');
            
            $data = [
                'user_id'  => $this->user_data->id,
                'translate' => $this->params['translate']
            ];
            
            if ($this->user_data->role=='admin')
                $data['approved'] = new Zend_Db_Expr('true');
            
            
            $data[$type_fields[$this->params['type']]] = $id;
            
            
            $m->newItem($data);
            
            
            if ($this->user_data->role=='admin') {
                if ($this->params['type']=='paragraph') {
                    ormModel::getInstance('public','paragraph')->updateItem([
                        'text_ru'   => $this->params['translate']
                    ],'id='.$id);
                }
                
                if ($this->params['type']=='description') {
                    ormModel::getInstance('public','novella')->updateItem([
                        'description'   => $this->params['translate']
                    ],'id='.$id);
                    
                }
                
                
                if ($this->params['type']=='novella') {
                    ormModel::getInstance('public','novella')->updateItem([
                        'name'   => $this->params['translate']
                    ],'id='.$id);
                }
                
                if ($this->params['type']=='chapter') {
                    ormModel::getInstance('public','chapters')->updateItem([
                        'name_ru'   => $this->params['translate']
                    ],'id='.$id);
                }
                
                
                
                $approved = true;
            }
        
        }catch (Exception $e) {
            $error = $e->getMessage();
        }
        
        $this->jsonAnswer([
            'error' => $error,
            'approved'  => $approved
        ]);
    }
    
    
    function getparAction() {
        $this->ajax();
        
        $nm = new novellasModel();
        $pm = new paragraphModel();
        $cm = new chaptersModel();
        $vm = new volumesModel();
        
        $tm = ormModel::getInstance('public','translators');
        
        try {
            $state = 'ok';
            $err = '';
            $list = [];
            
            
            ///////////////////////////////////////////////////
            $t_id = 5;
            
            //$translator = $this->_getParam('t','deepl');
            
            $translator = $this->params['t'];
            if (!$translator) $translator = 'deepl';
            
            $source_expr = "and (source='wuxia' or source='webnovel')";
            
            if ($translator=='deepl' || $translator=='deepl1') {
                $t_id = 5;
            }
            
            if ($translator=='promt') {
                $t_id = 6;
                $source_expr = "and source='aixdzs'";
            }
            
            if ($translator=='yapp') {
                $t_id = 7;
            }
            /////////////////////////////////////////////////////
            
            
            
            $tdata = $tm->getRow('id='.$t_id);
            
            if (!$tdata) throw new Exception('Error 800');
            
            if (($tdata['day_limit']<$tdata['day_used']) || ($tdata['day_limit']<$tdata['day_used']))
                throw new Exception('OverLimit');
            
            $novellas = $nm->getAll("status='inprogress'".$source_expr,"last_paragraph_translate nulls first, priority_parsing limit 1");

            $sym_len = 0;
            $p_ids = [];
            $c_ids = [];
            $novellas_ids = [];
            $chapters = [];
            $volumes = [];
            
            
            foreach ($novellas as $n) {
                $paras_totranslate = $pm->getUntranslatedInNovella($n['id']);

                $nm->updateItem([
                    'last_paragraph_translate'  => new Zend_Db_Expr('now()')
                ],'id='.$n['id']);
                
                if (!$paras_totranslate) continue;
                
                foreach ($paras_totranslate as $p) {
                    $p['novella_id'] = $n['id'];
                    $p['h'] = sha1($p['text_original'].$p['text_en'].$this->key);
                    $p['type'] = 'paragraph';
                    
                    $list[] = $p;
                    $p_ids[] = $p['id'];

                    $novellas_ids[$p['novella_id']] = $p['novella_id'];
                    $chapters[$p['chapter_id']] = $p['chapter_id'];
                    $volumes[$p['volume_id']] = $p['volume_id'];
                    
                    $text_len = mb_strlen($p['text_en']);
                    
                    if (!$text_len)
                        $text_len = mb_strlen($p['text_original']);

                    $sym_len += $text_len;
                    
                    //if ($sym_len>4300) break 2;
                }
                

            }
            
            $chapters_to_translate = $cm->getChaptersToTranslate($chapters);
            
            if ($translator!=='deepl1') {
                foreach ($chapters_to_translate as $c) {
                    $c['type']  = 'chapter';
                    $text_len = mb_strlen($c['name_original']);
                    $sym_len += $text_len;
                    $list[] = $c;
                    $c_ids[] = $c['id'];
                }

                $volumes_to_translate = $vm->getVolumesToTranslate($volumes);

                $v_ids = [];
                foreach ($volumes_to_translate as $v) {
                    $v['type']  = 'volume';
                    $text_len = mb_strlen($v['title']);
                    $sym_len += $text_len;
                    $list[] = $v;
                    $v_ids[] = $v['id'];
                }

                $novellas_to_translate = $nm->getNovellasToTranslate();
                $n_ids = [];

                foreach ($novellas_to_translate as $n) {
                    $n['type']  = 'novella';
                    $text_len = mb_strlen($n['name_original']);
                    $sym_len += $text_len;
                    $list[] = $n;
                    $n_ids[] = $n['id'];
                }
            }

            if ($sym_len>0) {
                $tm->updateItem([
                    'day_used'  => new Zend_Db_Expr('day_used+'.$sym_len),
                    'month_used'  => new Zend_Db_Expr('month_used+'.$sym_len)
                ],'id='.$tdata['id']);
            }

            if ($p_ids)
                $pm->updateItem([
                    'translate_now'   => new Zend_Db_Expr('true'),
                    'translate_start' => new Zend_Db_Expr('now()')
                ], 'id in ('.implode(',',$p_ids).')');
            
            if ($c_ids)
                $cm->updateItem([
                    'now_translated' => new Zend_Db_Expr('true'),
                    'translate_start' => new Zend_Db_Expr('now()')
                ], 'id in ('.implode(',',$c_ids).')');

            if ($v_ids)
                $vm->updateItem([
                    'translate_now' => new Zend_Db_Expr('true'),
                    'translate_start' => new Zend_Db_Expr('now()')
                ], 'id in ('.implode(',',$v_ids).')');
            
            if ($n_ids)
                $nm->updateItem([
                    'translate_now' => new Zend_Db_Expr('true'),
                    'translate_start' => new Zend_Db_Expr('now()')
                ], 'id in ('.implode(',',$n_ids).')');            

        }catch (Exception $e) {
            $state = 'err';
            $err = $e->getMessage();
        }
        
        $this->jsonAnswer([
            'state' => $state,
            'list'  => $list,
            'settings'  => [
                'deeplSymCount' => settings::getVal('deepl_max_sym'),
                'pauseSec'      => settings::getVal('next_pause_sec'),
                'workTime'      => settings::getVal('work_time'),
                'pauseTime'      => settings::getVal('pause_time')
            ],
            'error' => $err
        ]);
        
        
        return $list;
    }
    
    function saveblockAction() {
        $this->ajax();
        
        try {
            $err = '';
            $this->params['k'] = $this->key;
            if (!$this->params['u']) throw new Exception('error 7');
            
            $check = sha1($this->params['u'].$this->secret_key);

            if ($check!==$this->params['f']) throw new Exception('error 4');
            
            $data = json_decode(base64_decode($this->params['u']));
            
            if (!$data) throw new Exception('error 5');
            
            if (!$this->params['translate']) throw new Exception('error 6');
            
            $this->params['j'] = json_encode([
                'list'  => [
                    [
                        'type'  => $data->type,
                        'id'    => $data->id,
                        'translate' => $this->params['translate']
                    ]
                ]
            ]);
            
            $this->setparAction();
            
        }catch (Exception $e) {
            $err = $e->getMessage();
            
            $this->jsonAnswer([
                'error' => $err
            ]);
            
        }
        
    }    
    
    
    function setparAction() {
        $this->ajax();
        
        try {
            $pm = new paragraphModel();
            $cm = new chaptersModel();
            $vm = new volumesModel();
            $nm = new novellasModel();
            
            if ($this->params['k']!==$this->key) throw new Exception('not ok');
            
            $data = json_decode($this->params['j']);
            
            if (!$data->list) throw new Exception('no list');
            
            foreach ($data->list as $p) {

                if (!$p->id || !$p->translate) continue;
                
                if ($p->type == 'paragraph') {
                    
                    if (!$pm->updateItem([
                        'text_ru'           => $p->translate,
                        'translate_now'     => new Zend_Db_Expr('false'),
                        'translated'        => new Zend_Db_Expr('true'),
                        'translate_failed'  => new Zend_Db_Expr('false'),
                        'translate_date'    => new Zend_Db_Expr('now()')
                        
                    ],'id='.$p->id)) throw new Exception($pm->last_error);
                    
                    $pm->updateItem([
                        'ru_search_index'   => new Zend_Db_Expr("to_tsvector('russian', text_ru)")
                    ],'id='.$p->id);
                    
                    $pm->checkChapterTranslateFinishedByPar($p->id);
                    
                }else if ($p->type=='chapter') {
                    
                    if (!$cm->updateItem([
                        'name_ru'   => $p->translate,
                        'now_translated' => new Zend_Db_Expr('false')
                    ],'id='.$p->id)) throw new Exception($cm->last_error);;
                    
                }else if ($p->type=='volume') {
                    
                    if (!$vm->updateItem([
                        'title_ru'   => $p->translate,
                        'translate_now' => new Zend_Db_Expr('false')
                    ],'id='.$p->id)) throw new Exception($cm->last_error);;
                    
                }else if ($p->type=='novella') {
                    
                    if (!$nm->updateItem([
                        'name'   => $p->translate,
                        'translate_now' => new Zend_Db_Expr('false')
                    ],'id='.$p->id)) throw new Exception($cm->last_error);;
                 
                }
                
                
                
            }
            
        }catch (Exception $e) {
            $pm->last_error = $e->getMessage();
        }
        $this->jsonAnswer([
            'error' => $pm->last_error
        ]);
    }
    
    
    function failedAction() {
        $this->ajax();
        
        $m = ormModel::init('public','translate_failed');
        $pm = new paragraphModel();
        
        if (strpos($this->params['id'], ',')!==false) {
            $ids = explode(',',$this->params['id']);
            foreach ($ids as $i) {
                $m->newItem([
                    'object_id'=> (int)$i,
                    'type'      => $this->params['type']
                ]);
                
                if ($this->params['type']=='paragraph')
                    $pm->updateItem([
                        'translate_failed'  => new Zend_Db_Expr('true')
                    ],'id='.(int)$i);
            }
        }else {

            $m->newItem([
                'object_id'=> (int)$this->params['id'],
                'type'      => $this->params['type']
            ]);
        }
        
        if ($this->params['type']=='paragraph')
            $pm->updateItem([
                'translate_failed'  => new Zend_Db_Expr('true')
            ],'id='.(int)$this->params['id']);
        
        $this->jsonAnswer([
            'error' => $m->last_error
        ]);
    }
    
    
    public $secret_key = '87g2v382tvc9823tv42';
    public $blockIter  = 0;
    
    function gettranslateblockAction() {
        $this->ajax();
        $err = '';
        $key = 'yapp_stack';
        
        
        try {
            
            $stack = tools_cache::get($key);

            if ($stack==='!!nocache!!') throw new Exception('error 1');
            
            if (!$stack) {
                $stack = $this->getparAction();
                $stack = array_reverse($stack);
            }
            
            $item = '';
            $url = '';
            
            if ($stack) {
                $item = array_pop($stack);
                
                tools_cache::save($key, $stack, 1798);
                
                $data = base64_encode(json_encode([
                    'type'  => $item['type'],
                    'id'    => $item['id']
                ]));
                
                $k = sha1($data.$this->secret_key);
                
                $url = 'https://mlate.ru/novella/translate/saveblock/u/'.$data.'/f/'.$k;
                
                $text = '';
                
                if ($item['type']==='paragraph') {
                    $text = $item['text_en'];
                    if (!$text) $text = $item['text_original'];
                }else if ($item['type']==='chapter') {
                    $text = $item['name_original'];
                }else if ($item['type']==='volume') {
                    $text = $item['title'];
                }else if ($item['type']==='novella') {
                    $text = $item['name_original'];
                }
                
                if (!$text || mb_strlen($text)>1750) {
                    $this->blockIter++;
                    if ($this->blockIter>10) throw new Exception('no available data');
                    return $this->gettranslateblock();
                }
            }
            
        }catch (Exception $e) {
            $err = $e->getMessage();
            $text = '';
            $url = '';
        }
        
        $this->jsonAnswer([
            'error' => $err,
            'text'  => $text,
            'url'   => $url
        ]);
    }
    
    
}
