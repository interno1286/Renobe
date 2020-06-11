<?php

class admin_TagsController extends adminController {

    function indexAction()
    {
    }

    function saveAction()
    {
        $this->ajax();
        $tag_model = ormModel::getInstance('public','tags');

        foreach ($_POST['tag'] as $key => $value) {
            if ($value) {
                $tag_model->updateItem([
                    'name_ru' => $value
                ], 'id=' . $key);
            }
        }

        $this->_redirect('/admin/tags');
    }

}
