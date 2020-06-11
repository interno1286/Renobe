<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$root = __DIR__ . "/../../";
require_once $root . 'cms/system/console_app_header.php';
require_once $root . 'site/system/wuxiaworld.php';
require_once $root . 'site/system/yandexTranslate.php';
$yandex = yandexTranslate::getInstance();
$ch = curl_init();
//
$novella_list = ormModel::getInstance('novellasModel')->getAll("name is not null and name!=''");
//
$tag_model = ormModel::getInstance('public', 'tags');
$novella_tag_model = ormModel::getInstance('public', 'novella_tags');
//
$genre_model = ormModel::getInstance('public', 'genres');
$novella_genre_model = ormModel::getInstance('public', 'novella_genres');
//
foreach ($novella_list as $novella) {
    $novella_page_dom = phpQuery::newDocument(getNovellaPageUrl($novella['name_original'], $ch));
    foreach ($novella_page_dom->find('#etagme') as $tag) {
        $tag_id = getTagId($tag_model, pq($tag)->text(), $yandex);
        if (!$tag_id) {
            break;
        }
        $novella_tag_data = $novella_tag_model->getRow("novella_id=" . $novella['id'] . " and tag_id=" . $tag_id);
        if (!$novella_tag_data) {
            $novella_tag_model->newItem([
                'novella_id' => $novella['id'],
                'tag_id' => $tag_id,
            ]);
        }
    }

    foreach ($novella_page_dom->find('.genre[gid]') as $genre) {
        $genre_id = getGenreId($genre_model, pq($genre)->text(), $yandex);
        if (!$genre_id) {
            break;
        }
        $novella_genre_data = $novella_genre_model->getRow("novella_id=" . $novella['id'] . " and genre_id=" . $genre_id);
        if (!$novella_genre_data) {
            $novella_genre_model->newItem([
                'novella_id' => $novella['id'],
                'genre_id' => $genre_id,
            ]);
        }
    }
}

function getTagId($tag_model, $tag_title, $yandex)
{
    $tag_data = $tag_model->getRow("name='" . $tag_title . "'");
    if (!$tag_data) {
        $tag_model->newItem([
            'name' => $tag_title,
            'name_ru' => $yandex->translate($tag_title),
        ]);
        $tag_data = $tag_model->getRow("name=" . $tag_title);
    }
    return $tag_data['id'];
}

function getGenreId($genre_model, $genre_title, $yandex)
{
    $genre_data = $genre_model->getRow("name='" . $genre_title . "'");
    if (!$genre_data) {
        $genre_model->newItem([
            'name' => $genre_title,
            'name_ru' => $yandex->translate($genre_title),
        ]);
        $genre_data = $genre_model->getRow("name=" . $genre_title);
    }
    return $genre_data['id'];
}

function getNovellaPageUrl($novella_title, $ch) {
    $params = http_build_query(['s' => $novella_title, 'post_type' => 'seriesplans']);
    $site_url = 'https://www.novelupdates.com/' . '?' . $params;
    phpQuery::newDocument(searchRequest($ch, $site_url));
    return searchRequest($ch, pq('.search_title a')->attr('href'));
}

function searchRequest($ch, $site_url)
{
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $site_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent:Mozilla/5.0 (compatible; Rigor/1.0.0; http://rigor.com)',
        'cookie:__cfduid=dc6aca00ad1547271839212f5c1dab6a41578915025; _ga=GA1.2.1674769621.1578915028; _gid=GA1.2.1967211438.1578915028; _cmpQcif3pcsupported=1; cookieconsent_dismissed=yes; __lfcc=1; _gat=1'
    ]);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    return curl_exec($ch);
}

curl_close($ch);
