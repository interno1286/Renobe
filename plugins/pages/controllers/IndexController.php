<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IndexController
 *
 * @author v0yager
 */
class Pages_IndexController extends SiteBaseController {

    function initModel() {
        $this->model = new pagesModel();
    }

    function showAction() {
        //$page_data = $this->model->getPageById($this->params['page_id']);
        $lng = translate::getLanguage();
        $page_data = $this->model->getPageByPathLng($this->params['page_path'], $lng);
        
        if (!$page_data) throw new Exception ('page does not exists');
        
        $this->user_data->page_id = $page_data['id'];
        
        if ($page_data['skin'])
            $this->setSkin($page_data['skin']);

        $this->view->setMetaDescription($page_data['description']);
        
        $this->view->addMetaKeywords($page_data['keywords']);
        
        if ($page_data['name'])
            $this->view->addTitle($page_data['name']);

        $content = $page_data['content'];

        $template_name = self::getTemplateNameForPage($page_data);

        $content = $this->view->render($template_name);
/*
        if ($this->user_data->role=='admin') {
            
            $content .= "<script src='/plugins/pages/public/js/appendix.js'></script>";
            $content .= "\n<script>\nvar page_plugin_page_id = {$page_data['id']};\n</script>\n";
            //$content .= $this->renderTpl($tpl)
        }
*/
        $this->view->content = $content;
    }

    function editAction() {
        $this->useAjaxView();
        $this->needAdminRights();

        $page_data = $this->model->getPageById($this->params['id']);

        $this->view->page_data = $page_data;
        $this->view->skins = site::getSkins();

        $this->renderTplToContent('page_editor.tpl');
    }

    function edittplAction() {
        $this->user_data->expert_mode = false;

        $this->useAjaxView();

        $tpl = (isset($this->params['tpl']) && $this->params['tpl']) ? base64_decode($this->params['tpl']) : $this->config->path->views . '/index.tpl';

        if ($this->isPost()) {
            copy($tpl, $tpl . '.backup');
            file_put_contents($tpl, $this->params['content']);
            unlink('cache/pages_cache.ser');
        }

        $this->view->file_content = file_get_contents($tpl);

        $this->renderTplToContent('tpl_editor.tpl');
        
    }

    function editskinAction() {
        $this->useAjaxView();
        $this->needAdminRights();

        $data = file_get_contents($this->config->path->skin . 'views/index.tpl');

        $this->view->page_data = array(
            'content' => $data,
            'path' => '/'
        );
        $this->view->skins = site::getSkins();

        $this->renderTplToContent('page_editor.tpl');
        
    }

    function saveAction() {
        $this->useAjaxView();
        $this->needAdminRights();
        
        if ($this->params['useFile']) {
            $this->params['skin'] = $this->createSkin($this->params['useFile']);
        }
        
        $this->model->savePageData($this->params, $error);
        $this->removeOldTemplates();
        $this->view->content = Zend_Json::encode(array('error' => $error));
        unlink('cache/pages_cache.ser');
    }
    
    
    function createSkin($file) {
        
        $name = substr($file,0, strpos($file, '.'));
        
        $s = $this->config->path->root.'site/skins/';
        
        if (file_exists($s.$name))
            $name.='1';
        
        tools_files::copy_dir($s.$this->config->skin, $s.$name);
        unlink($s.$name.'/views/index.tpl');
        copy($s.$this->config->skin.'/views/'.$file, $s.$name.'/views/index.tpl');
        tools_files::remove_dir($s.$name.'/views/template_c');
        mkdir($s.$name.'/views/template_c',0777,true);
        
        foreach (glob($s.$name.'/views/*.backup*') as $b)
            unlink($b);
        
        return $name;
    }

    static function addPageid(&$content) {
        $user_data = Zend_Registry::get('user_data');

        if ($user_data->role == 'admin') {
            
            $page_id = false;
            
            $page_path = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_path');
            
            if ($page_path) {

                $pm = new pagesModel();

                $page_data = $pm->getPageByPathLng($page_path, translate::getLanguage());

                $page_id = @$page_data['id'];
            }
            
            if (!$page_id) $page_id = 'null';

            $content .= "<script src='/plugins/pages/public/js/appendix.js'></script>";
            $content .= "\n<script>\nvar page_plugin_page_id = {$page_id};\n</script>\n";
        }
        
    }
    
    static function addAdminButtonsToContent(&$content) {
        return false; ///временно отключим управление сайтом через страницы
        $user_data = Zend_Registry::get('user_data');

        if ($user_data->role == 'admin') {

            site::addMainMenuElement('Создать страницу', "editPage(undefined,'" . base64_encode($_SERVER['REQUEST_URI']) . "');");
            $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id');

            if ($page_id) {
                site::addMainMenuElement('Редактировать содержимое страницы', "editPage({$page_id});");
                site::addMainMenuElement('Отменить последнее изменение', "rollbackPage({$page_id});");
            } else {
                site::addMainMenuElement('Редактировать содержимое страницы', "pageTPLEdit();");
            }

            $content .= Zend_Registry::get('view')->render(Zend_Registry::get('cnf')->path->root . 'plugins/pages/views/pages_admin.tpl');
        }
    }

    static function getTemplateNameForPage($page_data) {

        $tpl_folder = Zend_Registry::get('cnf')->path->skin . 'views/pages';

        if (!is_dir($tpl_folder)) {
            if (!mkdir($tpl_folder, 0777, true))
                throw new Exception('Не смогли создать ' . $tpl_folder);
        }

        $tpl_name = $page_data['id'] . '_' . $page_data['md5_content'] . '.tpl';

        if (!file_exists($tpl_folder . '/' . $tpl_name)) {
            $f = fopen($tpl_folder . '/' . $tpl_name, 'a');

            if ($f === false)
                throw new Exception('Не смогли создать ' . $tpl_folder . '/' . $tpl_name);

            fwrite($f, $page_data['content']);

            fclose($f);
        }

        return $tpl_folder . '/' . $tpl_name;
    }

    function getskinAction() {
        $this->ajax();
        
        $data = $this->model->getPageById($this->params['page_id']);
        
        $this->jsonAnswer([
            'error' => $this->model->last_error,
            'skin'  => $data['skin']
        ]);
    }
    
    function removeOldTemplates() {
        $tpl_folder = $this->config->path->root."site/skins/*/views/template_c/*.tpl";

        $files = glob( $tpl_folder );

        foreach ($files as $f)
            unlink($f);
        
        if (file_exists('cache/pages_cache.ser'))
            unlink('cache/pages_cache.ser');
    }

    function appendixAction() {
        $this->needAdminRights();
        $this->useAjaxView();

        $error = '';

        try {
            $content = $this->model->getPageById($this->params['page_path']);

            if (!$content)
                throw new Exception('Не смог получить содержимое страницы');

            $result = phpQuery::newDocumentHTML($content['content']);

            if (!$result)
                throw new Exception('PQ Ошибка 1');

            $clone_elem = pq('#' . $this->params['elem_to_copy'])->_clone();

            $data_to_clone = $clone_elem->html();

            if (!$data_to_clone)
                throw new Exception('не найден исходный элемент');

            $this->fixSimpleEditElementsForAppendix($data_to_clone);
            $this->fixElementsIDs($data_to_clone);

            $clone_elem->html($data_to_clone);
            $clone_elem->removeClass('source');
            $clone_elem->attr('id', md5(microtime(true) . date('z')));

            $result = pq('#' . $this->params['destination_element'])->append($clone_elem);

            if (!$result)
                throw new Exception('PQ Ошибка 3');

            $content['content'] = pq()->html();

            if (!$content['content'])
                throw new Exception('PQ Ошибка 4');

            if (!$this->model->savePageData($content, $error))
                throw new Exception($error);
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        $this->view->content = Zend_Json::encode(array(
                    'error' => $error
        ));
    }

    function remappendixAction() {
        $this->needAdminRights();
        $this->useAjaxView();

        $error = '';

        try {
            $content = $this->model->getPageById($this->params['page_path']);

            if (!$content)
                throw new Exception('Не смог получить содержимое страницы');

            $result = phpQuery::newDocumentHTML($content['content']);

            if (!$result)
                throw new Exception('PQ Ошибка 1');

            pq('#' . $this->params['elem_id'])->remove();

            $content['content'] = pq()->html();

            if (!$content['content'])
                throw new Exception('PQ Ошибка 4');

            if (!$this->model->savePageData($content, $error))
                throw new Exception($error);
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        $this->view->content = Zend_Json::encode(array(
                    'error' => $error
        ));
    }

    function rollbackAction() {
        $this->needAdminRights();
        $this->model->rollbackPageContent($this->params['id']);
        $this->goBack();
    }

    function moveupAction() {
        $this->needAdminRights();
        $this->useAjaxView();

        $error = '';

        try {
            $content = $this->model->getPageById($this->params['page_path']);

            if (!$content)
                throw new Exception('Не смог получить содержимое страницы');

            $result = phpQuery::newDocumentHTML($content['content']);

            if (!$result)
                throw new Exception('PQ Ошибка 1');

            $elem = pq('#' . $this->params['elem_id'])->clone();

            $tag_name = $elem->elements[0]->tagName;

            $prev_elem_id = pq('#' . $this->params['elem_id'])->prev($tag_name)->not(':first-child')->attr('id');

            if (!$prev_elem_id)
                throw new Exception('Объект уже в самом верху.');

            pq('#' . $this->params['elem_id'])->remove();

            pq($elem)->insertBefore('#' . $prev_elem_id);

            $content['content'] = pq()->html();

            if (!$content['content'])
                throw new Exception('PQ Ошибка 4');

            if (!$this->model->savePageData($content, $error))
                throw new Exception($error);
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        $this->view->content = Zend_Json::encode(array(
                    'error' => $error
        ));
    }

    function movedownAction() {
        $this->needAdminRights();
        $this->useAjaxView();

        $error = '';

        try {
            $content = $this->model->getPageById($this->params['page_path']);

            if (!$content)
                throw new Exception('Не смог получить содержимое страницы');

            $result = phpQuery::newDocumentHTML($content['content']);

            if (!$result)
                throw new Exception('PQ Ошибка 1');

            $elem = pq('#' . $this->params['elem_id'])->clone();

            $tag_name = $elem->elements[0]->tagName;

            $next_elem_id = pq('#' . $this->params['elem_id'])->next($tag_name)->attr('id');

            if (!$next_elem_id)
                throw new Exception('Объект уже в самом низу.');

            pq('#' . $this->params['elem_id'])->remove();

            pq($elem)->insertAfter('#' . $next_elem_id);

            //$elem->remove();

            $content['content'] = pq()->html();

            if (!$content['content'])
                throw new Exception('PQ Ошибка 4');

            if (!$this->model->savePageData($content, $error))
                throw new Exception($error);
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        $this->view->content = Zend_Json::encode(array(
                    'error' => $error
        ));
    }

    function archiveAction() {
        $this->needAdminRights();
        $this->useAjaxView();

        $error = '';

        try {
            $content = $this->model->getPageById($this->params['page_path']);

            if (!$content)
                throw new Exception('Не смог получить содержимое страницы');

            $result = phpQuery::newDocumentHTML($content['content']);

            if (!$result)
                throw new Exception('PQ Ошибка 1');

            if (pq('#' . $this->params['elem_id'])->hasClass('archived')) {
                pq('#' . $this->params['elem_id'])->removeClass('archived');
            } else
                pq('#' . $this->params['elem_id'])->addClass('archived');

            $content['content'] = pq()->html();

            if (!$content['content'])
                throw new Exception('PQ Ошибка 4');

            if (!$this->model->savePageData($content, $error))
                throw new Exception($error);
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        $this->view->content = Zend_Json::encode(array(
                    'error' => $error
        ));
    }

    function fixSimpleEditElementsForAppendix(&$data_to_clone) {
        /////// UPDATE SIMPLE EDIT_ELEMENTS NAME FOR NO DUBLICATES /////////
        ///////simpleText
        preg_match_all('#{simpleTextEditor.*name=(?:"|\')(.*)(?:"|\').*}#iuU', $data_to_clone, $simple_edit_element);

        if (sizeof($simple_edit_element[1])) {

            $simple_edit_element[1] = array_unique($simple_edit_element[1]);

            foreach ($simple_edit_element[1] as $entry) {

                $new_name = $entry . '_' . md5(microtime(true) . date('r') . rand(10000, 99999) . $entry);
                $new_name = mb_substr($new_name, 0, 29);

                ///$data_to_clone = str_replace($entry,$new_name,$data_to_clone);
                $data_to_clone = preg_replace("#{simpleTextEditor(.*)name=('|\")$entry('|\")(.*)}#uUi", '{simpleTextEditor$1name=$2' . $new_name . '$3$4}', $data_to_clone);
            }
        }

        ///////simpleImage
        $simple_edit_element = null;
        preg_match_all('#{simpleImage.*src=(?:"|\')(.*)(?:"|\').*}#iuU', $data_to_clone, $simple_edit_element);

        if (sizeof($simple_edit_element[1])) {

            $simple_edit_element[1] = array_unique($simple_edit_element[1]);

            foreach ($simple_edit_element[1] as $source_image) {

                $path_parts = pathinfo($source_image);

                $destination_image = $path_parts['dirname'] . '/' . $path_parts['filename'] . '_' . md5(microtime(true) . date('r') . rand(10000, 99999)) . '.' . $path_parts['extension'];
                copy($_SERVER['DOCUMENT_ROOT'] . $source_image, $_SERVER['DOCUMENT_ROOT'] . $destination_image);

                //$data_to_clone = str_replace($source_image,$destination_image,$data_to_clone);
                $data_to_clone = preg_replace("#{simpleImage(.*)src=('|\")$source_image('|\")(.*)}#uUi", '{simpleImage$1src=$2' . $destination_image . '$3$4}', $data_to_clone);
            }
        }
        //////////////////////////////////////////////////////////////////////
    }

    function fixElementsIDs(&$data_to_clone) {
        preg_match_all("#<.*id=(?:\"|')(.*)(?:\"|')[^>]*>#uUi", $data_to_clone, $ids);

        if (sizeof($ids[1])) {

            $ids[1] = array_unique($ids[1]);

            foreach ($ids[1] as $id) {
                $search = $id;
                $replace = md5(microtime(true) . date('r') . $search . rand(10000, 99999));

                $data_to_clone = str_replace($search, $replace, $data_to_clone);
            };
        }
    }
    
    
    function delAction() {
        $this->ajax();
        $this->needAdminRights();
        
        $m = new pagesModel();
        
        $m->del('id='.(int)$this->params['id']);
        
        unlink('cache/pages_cache.ser');
        
        $this->jsonAnswer([
            'error' => $m->last_error
        ]);
    }

}
