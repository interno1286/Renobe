<?php

function smarty_function_simpleTextEditor($params, $template) {

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

        if (isset($params['save']))
            $template->smarty->assign('save', $params['save']);         ////// assign save element function

        $template->smarty->assign('editor', (isset($params['editor']) && $params['editor'] == true));  //// check that we need an ckeditor
        $template->smarty->assign('callback_script', (isset($params['callback_script'])) ? $params['callback_script'] : false);  ////
        $template->smarty->assign('in_dialog', (isset($params['in_dialog'])) ? $params['in_dialog'] : true);
        $template->smarty->assign('display', (isset($params['display'])) ? $params['display'] : 'block');
        $template->smarty->assign('editEvent', (isset($params['editEvent'])) ? $params['editEvent'] : 'onclick');
        $template->smarty->assign('is_draft', $is_draft);
        $template->smarty->assign('notooltip', (isset($params['notooltip'])) ? $params['notooltip'] : false);
        

        $template->smarty->assign('st_params', $params);

        $toolbar = (isset($params['toolbar'])) ? $params['toolbar'] : 'Simple';
        $template->smarty->assign('toolbar', $toolbar);

        $template->smarty->assign('editor_config', (isset($params['editor_config'])) ? $params['editor_config'] : false);

        $c = 1;

        $save_params = array();

        while (isset($params["saveparam$c"])) {
            $save_params[] = $params["saveparam$c"];
            $c++;
        }

        if (sizeof($save_params) == 0)
            $save_params[] = 1;

        $template->smarty->assign('save_params', implode("','", $save_params));  ///// ADD SAVE PARAMS

        $template->smarty->assign('id', substr(md5(rand(10000, 99999)), 0, 8)); //// UNIQ ID OF EDITOR

        $f_name = $config->simpletext_view_folder . 'editor.tpl';

        $content = site::addDebugTplMarker($config, $f_name, $template->smarty->fetch($f_name));
    } else {
        
        if (isset($params['href'])) {
            
            $a_attr = (isset($params['a_attr'])) ? $params['a_attr'] : '';
            
            $content = "<a href='{$params["href"]}' $a_attr>{$data}</a>";
        }else
            $content = $data;
    }

    return $content;
}
