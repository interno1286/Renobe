<?php
/**
 * usage example {newsBlockLast edit=$edit_allowed tpl="events_block.tpl" owner=events count=20 items_per_page=2 page_link='/events/page/#.html'}
 * @param type $params
 * @param type $template
 * @return type
 */
function smarty_function_newsBlockLast($params, $template) {

	$edit = (isset($params['edit']) && $params['edit']==true);

	$template->smarty->assign('edit',$edit);

	$config = Zend_Registry::get('cnf');

	$owner = (isset($params['owner'])) ? $params['owner'] : 'default';

	$vars = $template->smarty->getTemplateVars();

	$prms = $vars['params'];

	$model = new newsModel($owner);
	$model->setParams($prms);
    if (isset($params['exclude']) && $params['exclude'])
        $model->exclude($params['exclude']);

	if (isset($params['date_filter_field']))
		$model->setFilterField($params['date_filter_field']);

	$template->smarty->assign('plugin_params', $params);
	$template->smarty->assign('params', $prms);
    $template->smarty->assign('months',  tools_dateTime::getCyrMonths());

	if (isset($params['items_per_page'])) {

		$model->setItemsPerPage($params['items_per_page']);

		$total_pages = $model->getTotalPagesCount($model->getNews(true));

		$template->smarty->assign('events_page_count', $total_pages);

		$page = (isset($prms['page'])) ? $prms['page'] : 1;

		$model->setCurrentPage($page);

		$template->smarty->assign('current_page', $page);
		$template->smarty->assign('total_pages', $total_pages);

		$news = $model->getNews(false,(isset($params['order'])? $params['order'] : null));

		$page_addon = site::renderTpl('paging.tpl');

	}else {
		$count = (isset($params['count'])) ? intval($params['count']) : 5;
		$news = $model->getLastNews($count);
		$page_addon = '';
	}

	$template->smarty->assign('pages', $page_addon);

	$template->smarty->assign('news', $news);

	$template->smarty->assign('owner', $owner);

	$template->smarty->assign('add_button', (($edit) ? site::renderTpl('add_button.tpl') : ''));

	$data = (isset($params['tpl'])) ? site::renderTpl($params['tpl']) : site::renderTpl('news_block.tpl');

	if ($edit) {
		$data .= site::renderTpl($config->path->root.'plugins/news/views/ckeditor_connect.tpl');
	}

	return $data;
}