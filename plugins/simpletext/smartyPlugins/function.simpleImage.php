<?php

function smarty_function_simpleImage($params, $template) {

    $rndid = substr(md5(rand(10000, 99999)), 0, 8);
    
    $id = (isset($params['id'])) ? $params['id'] : 'si'.$rndid;

    $template->smarty->assign('id', $id); //// UNIQ ID OF EDITOR

    $im_data = simpleImageModel::getInstance()->getData($params['src']);

    $data = "\n\n";

    SICheckExistsAndTryToCreate($params);

    $link = false;

    switch (true) {
        case (isset($params['fix_link'])):
            $link = $params['fix_link'];
            break;

        case (isset($im_data['link']) && $im_data['link']):
            $link = $im_data['link'];
            break;
    }

    if ($link)
        $data .= "<a href='{$link}'>";
    
    $edit = (isset($params['edit'])) ? $params['edit'] : $template->smarty->getTemplateVars('edit_allowed');

    $refresh_tag = ($edit) ? "?t=".time() : '';
        
    $data .= "<img id='{$id}' src='{$params['src']}{$refresh_tag}' ";

    if (isset($params['attr']))
        $data .= $params['attr'];

    $data .= ' />';

    if ($link)
        $data .= '</a>';

    $data .= "\n\n";

    

    if ($edit) {

        $config = Zend_Registry::get('cnf');

        $template->smarty->assign('data', $data);

        $template->smarty->assign('si', array(
            $id => array(
                'params' => $params
            )
        ));
        /*
          $image_data = getimagesize($config->path->root.$params['src']);
          $template->smarty->assign('im_width',$image_data[0]);
          $template->smarty->assign('im_height',$image_data[1]);
         */
        $f_name = $config->path->root . 'plugins/simpletext/views/image_editor.tpl';

        $content = site::addDebugTplMarker($config, $f_name, $template->smarty->fetch($f_name));

        if (isset($params['full']) && !$im_data)
            simpleImageModel::getInstance()->saveCustomFullData($params['src'], $params['full']);
    } else
        $content = $data;

    return $content;
}

function SICheckExistsAndTryToCreate($params) {
    $config = Zend_Registry::get('cnf');

    $params['src'] = substr($params['src'], 1);

    $error = '';

    $source = (isset($params['default'])) ? $config->path->root . substr($params['default'], 1) : $config->path->root . 'plugins/simpletext/public/images/default-no-image.png';

    if (!is_dir($config->path->root . dirname($params['src'])))
        mkdir($config->path->root . dirname($params['src']), 0777, true);

    if (!file_exists($config->path->root . $params['src']) && isset($params['width']) && isset($params['height']))
        create_thumb(
                $params['width'], $params['height'], $source, $config->path->root . $params['src'], 90, true, false, false, $error, true
        );
}
