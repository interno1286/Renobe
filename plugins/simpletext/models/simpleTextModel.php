<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of simpleTextModel
 *
 * @author chenzya
 */
class simpleTextModel extends ormModel {
    
    public $schema = "public";
    public $table = "simple_text_data";
    public $controll_table = 'simple_text_data';

    function getSimpleTextContentByName($code) {

        $code = preg_replace('#([^a-zA-Z0-9_-])#', '', $code);

        return $this->s_fetchOne("select content from simple_text_data where code='$code'");
    }

    function getSimpleTextContentForEditByName($code, $editor, &$is_draft = false) {
        $code = preg_replace('#([^a-zA-Z0-9_-])#', '', $code);
        $is_draft = false;

        $data = $this->s_fetchRow("select content,draft from simple_text_data where code='$code'");

        if (!$data)
            return '';

        $ret = $data['content'];

        if ($editor && $data['draft']) {
            $is_draft = true;
            $ret = $data['draft'];
        };

        return $ret;
    }

    function saveDataByName($code, $data, &$error, $editor = true, $draft = false) {

        $code = preg_replace('#([^a-zA-Z0-9_-])#', '', $code);

        if ($draft) {
            $db_data = array(
                'draft' => preg_replace('#^(\r|\n|\s)*(<p>&nbsp;</p>|\s|\r|\n)*#umi', '', $data)
            );
        } else
            $db_data = array(
                'content' => preg_replace('#^(\r|\n|\s)*(<p>&nbsp;</p>|\s|\r|\n)*#umi', '', $data),
                'draft' => new Zend_Db_Expr('null')
            );

        if ($editor == 'false') {
            $db_data['content'] = htmlentities($db_data['content'], ENT_QUOTES, 'UTF-8');
        }

        $exists_id = $this->s_fetchOne("select id from simple_text_data where code='$code'");

        if ($exists_id) {
            $this->pq('update', 'simple_text_data', $db_data, 'id=' . $exists_id, $error);
        } else {
            $db_data['code'] = $code;

            $this->pq('insert', 'simple_text_data', $db_data, false, $error);
        }
        
        
        tools_cache::flush();
    }
    
    function install() {
        $sql_file = $this->config->path->root . "plugins/simpletext/contrib/script.sql";

        if (file_exists($sql_file)) {

            $expressions = explode(';', file_get_contents($sql_file));

            foreach ($expressions as $e)
                $this->pq('query', $e);
        }
    }
    
}
