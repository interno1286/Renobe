<?php

/**
 * Smarty plugin
 *
 */

/**
 * Smarty {use} plugin
 *
 * Type:     function
 * Name:     use
 * Purpose:  use template
 *
 */
function smarty_function_use($params, $template) {

	//$template->smarty

	if (empty($params['file'])) {
		trigger_error("[plugin] use parameter 'file' cannot be empty", E_USER_NOTICE);
		return;
	}

	foreach ($params as $key=>$value)
		$template->smarty->assign($key,$value);

	$template->smarty->assign('user_data',Zend_Registry::get('user_data'));

	$template->smarty->assign('use_uniq', tools_string::randString(6));

	return site::renderTpl($params['file']);
}

