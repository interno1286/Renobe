<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of feedbackModel
 *
 * @author chenzya
 */
class commentsModel extends ormModel {
    
    public $schema = "comments";
    public $table = "items";


    function getComments($for) {

        $for = preg_replace('#([^a-z0-9_-])#ui', '', $for);

        
        $sql = "
            select
                i.*,
                q.text as ansfor,
                q.author as ansfor_author
            from
                comments.items i
            left join
                comments.items q on q.id=i.in_ans_for
            where
                i.for='$for'
            order by i.likes desc, i.dislikes, i.date desc
            
            offset {$_SESSION['comments_shift']}
                
            limit 3
        ";

        return $this->s_fetchAll($sql);
    }
    
    function getCommentsForModer() {

        
        $sql = "
            select
                i.*,
                q.text as ansfor,
                q.author as ansfor_author
            from
                comments.items i
            left join
                comments.items q on q.id=i.in_ans_for
            where
                i.moder=false
            order by i.date desc
            
            offset {$_SESSION['comments_moder_offset']}
                
            limit 30
        ";

        return $this->s_fetchAll($sql);
    }
    
    
    
    function getCommentsTotal($for) {
        $for = preg_replace('#([^a-z0-9_-])#ui', '', $for);

        $sql = "
            select
                count(id)
            from
                comments.items i
            where
                i.for='$for'
        ";

        return $this->s_fetchOne($sql);
        
    }

    function getAnsFor($id) {
        $sql = "
            select
                *
            from
                comments.items i
            where
                id=$id
        ";
    }

    function leave($params, &$error = '') {

        $db_data = array(
            'author' => strip_tags($params['name']),
            'from' => strip_tags($params['from']),
            'text' => htmlentities(strip_tags($params['comment'])),
            'ip' => $_SERVER['REMOTE_ADDR'],
            'for' => preg_replace('#([^a-z0-9_-])#ui', '', $params['for']),
            'in_ans_for' => ($params['inansfor']) ? (int) $params['inansfor'] : new Zend_Db_Expr('null')
        );

        return $this->pq('insert', 'comments.items', $db_data, $error);
    }

    function remove($id) {
        return $this->pq('delete', 'comments.items', 'id=' . intval($id));
    }

    function getDataById($id) {
        $sql = "
            select
                *
            from
                comments.items
            where
                id=" . intval($id);

        return $this->s_fetchRow($sql);
    }

    function seeditFeedback($params) {
        $db_data = array(
            'author' => strip_tags(htmlentities($params['name'])),
            'text' => strip_tags(htmlentities($params['text']))
        );

        if ($params['date'])
            $db_data['datetime'] = strftime('%Y-%m-%d', strtotime($params['date']));

        $this->db->update('comments.items', $db_data, 'id=' . intval($params['objectid']));
    }

    function getFeedBacksCount() {
        return $this->s_fetchOne('select count(id) from comments.items');
    }

    function getRandom() {

        $feed_back_ids = $this->s_fetchCol('select id from comments.items');

        $sql = "
			select
				*
			from
				comments.items
			where
				id=" . $feed_back_ids[rand(0, (sizeof($feed_back_ids) - 1))];

        return $this->s_fetchRow($sql);
    }

    function like($comment_id) {
        $lm = ormModel::getInstance('comments','likes');
        
        $res = $lm->newItem([
            'like'          => new Zend_Db_Expr('true'),
            'comment_id'    => (int)$comment_id,
            'ip'            => $_SERVER['REMOTE_ADDR']
        ]);
        
        if ($res) {
            $this->updateItem([
                'likes'  => new Zend_Db_Expr('likes+1')
            ], 'id='.$comment_id);
        }
        
    }
    
    function dislike($comment_id) {
        $lm = ormModel::getInstance('comments','likes');
        
        $res = $lm->newItem([
            'like'          => new Zend_Db_Expr('true'),
            'comment_id'    => (int)$comment_id,
            'ip'            => $_SERVER['REMOTE_ADDR']
        ]);
        
        if ($res) {
            $this->updateItem([
                'dislikes'  => new Zend_Db_Expr('dislikes+1')
            ], 'id='.$comment_id);
        }
    }
    

}
