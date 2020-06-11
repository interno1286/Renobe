<?php

function smarty_function_pageData($params, $template) {

	$id = substr(md5(rand(10000,99999)),0,8);

	$template->smarty->assign('id',$id); //// UNIQ ID OF EDITOR

        $edit = (isset($params['edit'])) ? $params['edit'] : $template->smarty->getTemplateVars('edit_allowed');

        $model = new pagesModel();
        
	$data = $model->getPageByName($params['name']);
        
        if (!$data) {
            $params = array(
                'name'      => $params['name'],
                'path'      => $params['name'],
                'content'   => ''
            );
            
            $model->savePageData($params,$error,$params['id']);
            
            $data = $params;
        };
        
        $template_name = Pages_IndexController::getTemplateNameForPage($data);
        
        $config = Zend_Registry::get('cnf');
        
        $content = $template->smarty->fetch($template_name);
        
	if ($edit && $config->debug->on) {

		$template->smarty->assign('data',$data);

		$template->smarty->assign('pages',array(
			$id => array(
                            'params' => $params
			)
		));
                
		$template->smarty->assign('page_id',$data['id']);

		$f_name = $config->path->root.'plugins/pages/views/pageDataEdit.tpl';

		$content .= site::addDebugTplMarker($config, $f_name, $template->smarty->fetch($f_name));

	};

	return $content;
}