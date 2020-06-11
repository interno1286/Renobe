<?php
$root = '../../';

include($root.'cms/system/console_app_header.php');

$m = new chaptersModel();

$ids = $m->s_fetchAll("select id,translate_finish from chapters order by id");
$need_translate_percent = (int)settings::getVal('chapters_translate_percent');

foreach ($ids as $chap) {
    $i = $chap['id'];
    
    echo "\nWork with ID $i";
    
    $untranslate_pars = (int)$m->s_fetchOne("select count(id) from paragraph where chapter_id=$i and (text_ru is null or text_ru='')");
    $total_pars = (int)$m->s_fetchOne("select count(id) from paragraph where chapter_id=$i");
    
    if ($total_pars)
        $percent = (int)ceil(($untranslate_pars/$total_pars)*100);
    else 
        $percent = 100;
    
    $percent = 100-$percent;
    
    echo "\n\tTotal / Untranslated {$total_pars} / {$untranslate_pars} ({$percent}%)";
    
    if ($total_pars>0) {
        
        $m->updateItem([
            'translated_percent'    => $percent
        ],'id='.$i);
        
        if ($untranslate_pars==0 || $need_translate_percent<=$percent) {
            
            if (!$chap['translate_finish']) {
                $m->updateItem([
                    'translate_finish'  => new Zend_Db_Expr('now()')
                ], 'id='.$i);

                echo "\n\t Set finished";
            }
            
        }else {
            
            if ($chap['translate_finish']) {
                $m->updateItem([
                    'translate_finish'  => new Zend_Db_Expr('null')
                ], 'id='.$i);

                echo "\n\t Set NOT finished";
            }
        }
    }
}