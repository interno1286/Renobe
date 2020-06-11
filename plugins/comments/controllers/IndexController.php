<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IndexController
 *
 * @author chenzya
 */
class Comments_IndexController extends SiteBaseController {

    function initController() {
        $page = (isset($this->params['page'])) ? $this->params['page'] : 1;

        $this->view->page = $page;

        $this->model->setItemsPerPage($this->config->feedback_items_per_page);

        $total_feedbacks = $this->model->getFeedBacksCount();

        $this->view->total_pages = $this->model->getTotalPagesCount($total_feedbacks);

        $this->model->setCurrentPage($page);
        $this->model->setItemsPerPage($this->config->feedback_items_per_page);
        $this->loadJS();
    }

    function loadJS() {
        $this->view->addScript($this->config->url->base . 'plugins/feedback/public/js/feedback.js');
    }

    function initModel() {
        $this->model = new commentsModel();
    }

    function adminInit() {
        parent::adminInit();

        if ($this->edit_allowed)
            $this->view->addScript($this->config->url->base . 'plugins/feedback/public/js/admin.js');
    }

    function reloadAction() {
        $this->useAjaxView();

        $this->view->comments = $this->model->getComments($this->params['for']);

        $this->renderTplToContent('comments.tpl');
    }

    function indexAction() {
        $this->view->items = $this->model->getAll();
    }

    function newAction() {
        $this->useAjaxView();

        if ($this->isPost()) {

            $error = '';

            try {

                $this->preLeaveCommentCheck();

                $this->model->leave($this->params);

                if (!$this->edit_allowed)
                    $this->sendAdminMessage();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }

            $this->view->content = Zend_Json::encode(array(
                        'error' => $error
            ));
        } else
            $this->renderTplToContent('new_comment.tpl');
    }

    function preLeaveCommentCheck() {
        if (isset($this->user_data->smartCaptcha) && $this->user_data->smartCaptcha) {

            if ($this->user_data->smartCaptcha != $this->params['smCaptcha'])
                throw new Exception('Вы не прошли тест с выбором изображения');
        }
    }

    function sendAdminMessage() {
        $message = "
			Здравствуйте!

			Пользователь {$this->params['name']}, {$this->params['from']} оставил коментарий.
                
            Текст: " . htmlentities(strip_tags($this->params['comment'])) . "
            IP Адрес: {$_SERVER['REMOTE_ADDR']}
            
            Ответить или удалить комментарий вы можете на странице
            
			<a href='{$_SERVER['HTTP_REFERER']}'>{$_SERVER['HTTP_REFERER']}</a>
		";


        Zend_Mail::setDefaultTransport(new Zend_Mail_Transport_Smtp($this->config->smtp_ip));

        $mail = new Zend_Mail('UTF-8');
        $mail->setFrom($this->config->comments->email_from);

        $mail->setSubject('Новый комментарий на сайте ' . $_SERVER['SERVER_NAME']);
        $mail->setBodyHtml(nl2br($message));

        $mail->addTo(settings::getVal('manager_email'));

        try {
            $mail->send();
            return true;
        } catch (Exception $e) {
            errorReport($e, get_defined_vars());
        }
    }

    function removeAction() {
        $this->needAdminRights();

        $this->useAjaxView();
        $error = '';
        $this->model->remove($this->params['id'], $error);

        $this->view->content = Zend_Json::encode(array('error' => $error));
    }

    function segetdataFeedback() {
        $this->needAdminRights();

        $this->useAjaxView();

        $this->view->data = $this->model->getDataById($this->params['objectid']);

        $this->renderTplToContent('new_feedback.tpl');
    }

    function likeAction() {
        $this->ajax();
        
        $this->model->like($this->params['id']);
        
        $this->jsonAnswer([
            'error' => ($this->model->last_error) ? 'По всей видимости вы уже голосовали' : ''
        ]);
    }
    
    function dislikeAction() {
        $this->ajax();
        
        $this->model->dislike($this->params['id']);
        
        $this->jsonAnswer([
            'error' => ($this->model->last_error) ? 'По всей видимости вы уже голосовали' : ''
        ]);
    }
    
}
