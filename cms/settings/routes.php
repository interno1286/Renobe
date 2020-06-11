<?php
$router = Zend_Controller_Front::getInstance()->getRouter();

$route = new Zend_Controller_Router_Route(
										'/auth/*',
										array(
											'controller'	=> 'index',
											'action'		=> 'auth'
										)
);

$router->addRoute('simpleauth',$route);


$route = new Zend_Controller_Router_Route(
										'/installplugin/:name',
										array(
											'controller'	=> 'index',
											'action'	=> 'install'
										)
);

$router->addRoute('instrallPlugin',$route);


$route = new Zend_Controller_Router_Route(
										'/cms/manage',
										array(
											'controller'	=> 'index',
											'action'	=> 'manage'
										)
);

$router->addRoute('managecms',$route);
