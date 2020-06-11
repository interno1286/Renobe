<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$root = __DIR__ . "/../../";
require_once $root . 'cms/system/console_app_header.php';
require_once $root . 'site/system/wuxiaworld.php';
require_once $root . 'site/system/yandexTranslate.php';

$url = 'https://www.wuxiaworld.co/all/';
$limit = 5;

$doc = new DOMDocument;
@$doc->loadHTMLFile($url);

$whole_novella_list = $doc->getElementById('main');
$novella_model = ormModel::getInstance('public', 'novella');
$provider = new wuxiaworld();
$yandex = yandexTranslate::getInstance();
$count = 0;
foreach ($whole_novella_list->getElementsByTagName('div') as $novella_list_block) {
    foreach ($novella_list_block->getElementsByTagName('ul') as $novella_list_ul) {
        foreach ($novella_list_ul->getElementsByTagName('li') as $novella) {
            $novella_url = 'https://www.wuxiaworld.co' . $novella->getElementsByTagName('a')[0]->getAttribute('href');
            if (empty($novella_model->getRow("url='" . $novella_url . "'")) && ($count < $limit)) {
                $count++;
                $novella_info = $provider->getInfo($novella_url);
                $novella_model->newItem([
                    'url' => $novella_url,
                    'source' => 'wuxia',
                    'author' => $novella_info['author'],
                    'description_original' => $novella_info['description_original'],
                    'description' => $yandex->translate($novella_info['description_original']),
                    'name_original' => $novella_info['name_original'],
                    'name' => $yandex->translate($novella_info['name_original']),
                ]);
                saveImage($novella_model->last_id, $novella_info['image']);
            }
        }
    }
}

function saveImage($novella_id, $image)
{
    if (!is_dir(__DIR__ . '/../../public/novellas')) mkdir(__DIR__ . '/../../public/novellas', 0777, true);

    $extension = pathinfo(parse_url($image, PHP_URL_PATH), PATHINFO_EXTENSION);
    $filename = $novella_id . '.' . $extension;

    $data = file_get_contents($image);

    file_put_contents(__DIR__ . '/../../public/novellas/' . $filename, $data);

    ormModel::getInstance('public', 'novella')->updateItem([
        'image' => $filename
    ], 'id=' . $novella_id);
}
