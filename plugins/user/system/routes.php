<?php

$route = new Zend_Controller_Router_Route(
										'/register',
										array(
											'module'		=> 'user',
											'controller'    => 'register',
											'action'		=> 'index'
										)
);

$router->addRoute('register',$route);

$route = new Zend_Controller_Router_Route(
										'/registerok',
										array(
											'module'		=> 'user',
											'controller'    => 'register',
											'action'		=> 'ok'
										)
);

$router->addRoute('registerok',$route);

