<?php
/*** FOR EXAMPLE
$router = Zend_Controller_Front::getInstance()->getRouter();

$route = new Zend_Controller_Router_Route(
										'/balance/ac/:ac/pin/:pin',
										array(
											'module'	=> 'payments',
											'controller'	=> 'index',
											'action'	=> 'getbalance'
										)
);

$router->addRoute('register',$route);
*/
