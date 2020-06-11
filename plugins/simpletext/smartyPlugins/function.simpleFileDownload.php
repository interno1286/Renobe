<?php
/**
 * 
 * Simple File Uploader
 * 
 * Usage example:   {simpleFileDownload container='<a href="%file%" target="_blank">Файл для загрузки</a>' name="BKZcatalog2008_AT"}
 * 
 * @desc 
 * @param type $params
 * @param type $template
 * @return type
 */

function smarty_function_simpleFileDownload($params, $template) {

	$id = substr(md5(rand(10000,99999)),0,8);

	$template->smarty->assign('id',$id); //// UNIQ ID OF EDITOR

    $edit = (isset($params['edit'])) ? $params['edit'] : $template->smarty->getTemplateVars('edit_allowed');
        
	$data = $params['container'];
	$name = $params['name'];

	$config = Zend_Registry::get('cnf');

	$dir = $config->path->root.'public/simpleFiles';

	if (!is_dir($dir))
		mkdir($dir,0777,true);

	$file = glob($dir.'/'.$params['name'].'.*');

	$file = (isset($file[0])) ? $file[0] : '';

	$data = str_replace('%file%',str_replace($config->path->root,'/',$file),$data);
        
	if ($edit) {

		$template->smarty->assign('data',$data);

		$template->smarty->assign('sf',array(
			$id => array(
				'params'	=> $params
			)
		));

		 $f_name = $config->path->root.'plugins/simpletext/views/file_editor.tpl';

		$content = site::addDebugTplMarker($config, $f_name, $template->smarty->fetch($f_name));

	}else $content = $data;

	return $content;
}