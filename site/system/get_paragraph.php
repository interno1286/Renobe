<?php
$root = "../../";
require_once $root.'cms/system/console_app_header.php';

$p = ormModel::getInstance('public','paragraph');
$text = $p->get('text_en', 'text_en is not null and text_ru is null', 'random()');

echo json_encode(['id' => 0, 'text' => $text]);