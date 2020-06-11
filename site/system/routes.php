<?php

$router = Zend_Controller_Front::getInstance()->getRouter();

$route = new Zend_Controller_Router_Route(
    '/about',
    array(
            'module'	=> 'default',
            'controller'	=> 'index',
            'action'	=> 'about',
    )
);
$router->addRoute('about',$route);

$route = new Zend_Controller_Router_Route(
    '/faq',
    array(
            'module'	=> 'default',
            'controller'	=> 'index',
            'action'	=> 'faq',
    )
);

$router->addRoute('faq',$route);

$route = new Zend_Controller_Router_Route(
    '/chapter/:name/:id',
    array(
            'module'	=> 'novella',
            'controller'	=> 'chapter',
            'action'	=> 'show'
    )
);

$router->addRoute('chapter',$route);

$route = new Zend_Controller_Router_Route(
    '/novellas/:name/:id',
    array(
            'module'	=> 'novella',
            'controller'	=> 'index',
            'action'	=> 'index'
    )
);

$router->addRoute('novellas',$route);


$route = new Zend_Controller_Router_Route(
    '/novellas/:name/:id/*',
    array(
            'module'	=> 'novella',
            'controller'	=> 'index',
            'action'	=> 'index'
    )
);

$router->addRoute('novellasParams',$route);


$route = new Zend_Controller_Router_Route(
    '/novellas',
    array(
            'module'	=> 'novella',
            'controller'	=> 'index',
            'action'	=> 'list'
    )
);

$router->addRoute('novellasAll',$route);



$route = new Zend_Controller_Router_Route(
    '/news',
    array(
            'module'	=> 'default',
            'controller'	=> 'index',
            'action'	=> 'news'
    )
);

$router->addRoute('news',$route);



$route = new Zend_Controller_Router_Route(
    '/news/:name/:id',
    array(
            'module'	=> 'default',
            'controller'	=> 'index',
            'action'	=> 'shownews'
    )
);

$router->addRoute('shownews',$route);


$route = new Zend_Controller_Router_Route(
    '/search',
    array(
            'module'            => 'default',
            'controller'	=> 'index',
            'action'            => 'search'
    )
);

$router->addRoute('search',$route);
