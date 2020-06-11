<?php

function smarty_function_showAnsFor($params, $template) {

	$edit = (isset($params['edit']) && $params['edit']==true);
	$template->smarty->assign('edit',$edit);

	$config = Zend_Registry::get('cnf');

	$model = new commentsModel();

    $data = $model->getDataById($params['comment']['in_ans_for']);
    
    if ($data['id']) {
        $template->smarty->assign('data', $data);

        $d = $template->smarty->fetch($config->path->skin.'views/ansFor.tpl');
        return $d;
    }
    
    return '';
}