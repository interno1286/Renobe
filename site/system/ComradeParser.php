<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$root = __DIR__ . "/../../";
require_once $root . 'cms/system/console_app_header.php';

class ComradeParser {

    private $ch;
    private $novella_model;
    private $volumes_model;
    private $chapters_model;
    private $paragraphs_model;
    private $yandex;

    private $limit = 15;

    function __construct()
    {
        $this->ch = curl_init();
        $this->novella_model = ormModel::getInstance('public', 'novella');
        $this->volumes_model = ormModel::getInstance('public', 'volumes');
        $this->chapters_model = ormModel::getInstance('public', 'chapters');
        $this->paragraphs_model = ormModel::getInstance('public', 'paragraph');

        $this->yandex = yandexTranslate::getInstance();

        $this->parseNovelList('https://comrademao.com/mtype/chinese/');
        $this->parseNovelList('https://comrademao.com/mtype/japanese/');
        $this->parseNovelList('https://comrademao.com/mtype/korean/');
    }

    private function parseNovelList($list_url)
    {
        $novel_list = phpQuery::newDocument($this->getPage($list_url));
        $this->saveNovel($novel_list);

        $next_page_href = $novel_list->find('.next')->attr('href');
        if ($next_page_href) {
            $novel_list = phpQuery::newDocument($this->getPage($next_page_href));
            $this->saveNovel($novel_list);
        }
    }

    private function saveNovel($novel_list)
    {
        $n = 0;
        foreach ($novel_list->find('.status-publish') as $novel) {
            if ($n <= $this->limit) {
                $content = pq($novel)->find('.novel-content > .title-novel');

                $novel_page = phpQuery::newDocument($this->getPage($content->attr('href')));
                $novel_page->find('#Description > h5')->remove();

                $this->novella_model->newItem([
                    'source' => 'comrade',
                    'url' => $content->attr('href'),
                    'name' => $this->yandex->translate($content->text()),
                    'name_original' => $content->text(),
                    'description' => $this->yandex->translate($novel_page->find('#Description')->text()),
                    'description_original' => $novel_page->find('#Description')->text()
                ]);
                $novella_id = $this->novella_model->last_id;
                $this->saveImage($novella_id, $novel_page->find('#thumbnail > img')->attr('src'));

                $this->volumes_model->newItem([
                    'title' => 'Volume 1',
                    'title_ru' => 'Том 1',
                    'novella_id' => $novella_id,
                ]);

                $this->saveChapters($novel_page);
                $n++;
            }
        }
    }

    private function saveChapters($novel_page)
    {
        foreach ($novel_page->find('td > a') as $chapter) {
            $this->chapters_model->newItem([
                'name_original' => pq($chapter)->text(),
                'name_ru' => $this->yandex->translate(pq($chapter)->text()),
                'volume_id' => $this->volumes_model->last_id,
                'chapter_url' => pq($chapter)->attr('href')
            ]);
            $this->saveChapterParagraphs(phpQuery::newDocument($this->getPage(pq($chapter)->attr('href'))), $this->chapters_model->last_id);
        }

        $next_page_href = $novel_page->find('.next')->attr('href');
        if ($next_page_href) {
            $this->saveChapters(phpQuery::newDocument($this->getPage($next_page_href)));
        }
    }

    function saveChapterParagraphs($chapter_page, $chapter_id)
    {
        $paragraphs = explode("<p>", $chapter_page->find('div[readability]')->html());
        $n = 0;
        foreach ($paragraphs as $paragraph) {
            $paragraph = trim(preg_replace("</p>",'', $paragraph));
            if ($paragraph) {
                $this->paragraphs_model->newItem([
                    'text_original_sha1' => sha1($paragraph),
                    'text_en' => $paragraph,
                    'index' => $n,
                    'chapter_id' => $chapter_id
                ]);
                $n++;
            }
        }
    }


    private function getPage($url)
    {
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, ['User-Agent:Mozilla/5.0 (compatible; Rigor/1.0.0; http://rigor.com)']);
        return curl_exec($this->ch);
    }

    private function saveImage($novella_id, $image)
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

    function __destruct()
    {
        curl_close($this->ch);
    }
}

new ComradeParser();