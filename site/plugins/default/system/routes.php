<?php
$router = Zend_Controller_Front::getInstance()->getRouter();

$route = new Zend_Controller_Router_Route(
										'/404',
										array(
											'module'	=> 'default',
											'controller'	=> 'index',
											'action'	=> 'notfound'
										)
);

$router->addRoute('404',$route);
