<?php

function smarty_function_simpleText($params, $template) {

    $content = '';
    
    $edit = (isset($params['edit'])) ? $params['edit'] : $template->smarty->getTemplateVars('edit_allowed');
    
    $st_model = new simpleTextModel();
    
    if (isset($params['name']) && !$edit) {

        $key = 'st'.$params['name'];

        $data = tools_cache::get($key);
        
        if (!$data || $data==='!!nocache!!') {
            $data = $st_model->getSimpleTextContentByName($params['name']);
            tools_cache::save($key, $data, 86400*7);
        }
        
    }else $data = (isset($params['name'])) ? $st_model->getSimpleTextContentByName($params['name']) : $params['data'];

    if ($edit) {

        $config = Zend_Registry::get('cnf');

        $template->smarty->assign('name', (isset($params['name']) ? $params['name'] : ''));

        $data = $st_model->getSimpleTextContentForEditByName($params['name'], (isset($params['editor']) && $params['editor'] == true), $is_draft);

        $template->smarty->assign('data', $data);                       //////  content of edited text
        $template->smarty->assign('params', $params);

        $f_name = $config->simpletext_view_folder . 'editor2.tpl';

        $content = site::addDebugTplMarker($config, $f_name, $template->smarty->fetch($f_name));
        
    } else {
        
        $content = $data;
    }

    return $content;
}
