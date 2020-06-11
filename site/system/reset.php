<?php

$root = '../../';

include $root.'cms/system/console_app_header.php';

$pm = new paragraphModel();
$cm = new chaptersModel();
$vm = new volumesModel();
$nm = new novellasModel();

$tm = ormModel::init('public', 'translators');

$pm->updateItem([
    'translate_now' => new Zend_Db_Expr('false')
],"translate_now=true and translate_start<now() - interval '30 minute'");


$cm->updateItem([
    'now_translated' => new Zend_Db_Expr('false')
],"now_translated=true and translate_start<now() - interval '30 minute'");

$vm->updateItem([
    'translate_now' => new Zend_Db_Expr('false')
],"translate_now=true and translate_start<now() - interval '30 minute'");

$nm->updateItem([
    'translate_now' => new Zend_Db_Expr('false')
],"translate_now=true and translate_start<now() - interval '30 minute'");


if (strftime('%H')=='00')  {
    $tm->updateItem([
        'day_used'  => 0
    ]);
    
    if (strftime('%d')=='01') {
        $tm->updateItem([
            'month_used'  => 0
        ]);
    }
}

?>