<?php

function smarty_function_comments($params, $template) {

    $edit = (isset($params['edit']) && $params['edit']==true);
    $template->smarty->assign('edit',$edit);
    
    $_SESSION['comments_shift'] = 0;
    
    $model = new commentsModel();
    
    $template->smarty->assign('params',$params);
    
    $template->smarty->assign('for_what',@$params['for']);

    if (isset($params['moder']) && $params['moder']) {
        $template->smarty->assign('comments', $model->getCommentsForModer());
        $template->smarty->assign('total_comments', 0);
    }else {
        $template->smarty->assign('comments', $model->getComments($params['for']));
        $template->smarty->assign('total_comments', $model->getCommentsTotal($params['for']));
    }
    

    return site::renderTpl('comments.tpl');
}