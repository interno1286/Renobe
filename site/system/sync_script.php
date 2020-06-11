<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
$root = "../../";
require_once $root.'cms/system/console_app_header.php';
$nmodel = new novellasModel();
$retranslate_model = ormModel::getInstance('public', 'retranslate');
$cm = new chaptersModel();
while (true) {
    try {
        //////////// first retranslate chapters //////////
        /*
         * TEMP disabled it here
        $retranslate = $retranslate_model->getAll('finished is null','created');
        
        foreach ($retranslate as $r) {
            if ($r['item']=='chapter') {
                
                $c = $cm->getData($r['item_id']);
                
                clog("Retranslate CHAPTER #".$c['number']." id ".$c['id']);
                
                $provider = new $r['provider']();
                $provider->novella_id = $c['novella_id'];
                $provider->syncChapter($c);
                
                $retranslate_model->updateItem([
                    'finished'  => new Zend_Db_Expr('now()')
                ],'id='.$r['id']);
                
                clog("Retranslate CHAPTER finished");
            }
        }
        */
        ///////////////// novellas sync  /////////////////
        
        $novellas = $nmodel->getAll("last_sync<now()-interval '3 hours' or last_sync is null", "priority_parsing desc");
        
        
        if (!$novellas && $nmodel->last_error) {
            $err = $nmodel->last_error;
            $nmodel->last_error = '';
            throw new Exception($err);
        }
        
        foreach ($novellas  as $n) {
            clog("Sync novella ".$n['id'].' '.$n['name'].' ('.$n['name_original'].')');
            
            $provider = $nmodel->getProvider($n['url']);
            
            if (!$provider) throw new Exception('UNKNOWN PROVIDER IN ID '.$n['id']);
            
            $provider->sync($n['id']);
        }
        
    }catch (Exception $e) {
        if ($e->getCode()===666) {
            $sleep = (int)settings::getVal('parser_sleep');
            clog("===============================\n\nTranslate limit sleep for 1 hour\n\n=======================================");
            sleep($sleep*60);
        }else clog("\n\n========================= ERROR ========================\n".$e->getMessage()."\nLine: ".$e->getLine()."\n\n".$e->getTraceAsString()."\n\n==========================================\n\n");
    }   
    sleep(5);
}

