<?php

$pages_cache = false;

if (file_exists('cache/pages_cache.ser'))
    $pages_cache = unserialize (file_get_contents ('cache/pages_cache.ser'));

if (!$pages_cache) {

    $pm = new pagesModel();

    $current_pages = $pm->getAllPages();
    
    file_put_contents('cache/pages_cache.ser', serialize($current_pages));    
}else $current_pages = $pages_cache;

if ($current_pages) {

    $router = Zend_Controller_Front::getInstance()->getRouter();

    foreach ($current_pages as $p) {

        if ($p['path']{0} != '/')
            continue;

        $route = new Zend_Controller_Router_Route(
                $p['path'], array(
                    'module' => 'pages',
                    'controller' => 'index',
                    'action' => 'show',
                    'page_path' => $p['path']
                )
        );

        $router->addRoute('pages_route_' . $p['path'], $route);
    }
}
