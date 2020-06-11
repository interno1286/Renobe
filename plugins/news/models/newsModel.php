<?php

/***
 * Useful functions for extend this class:
 * 
 * fillCustomFieldsBeforeSave($db_data,$params); /// save your news params to db
 * 
 */
class newsModel extends ormModel {

	public $type=false;
	public $owner='default';

	public $date = false;
        
        public $schema = 'news';
        public $table = 'items';

	protected $v;
	protected $params;

	public $items_per_page = 6;
	public $current_page = 1;
    
        protected $exclude = false;

	function setParams($params) {
		$this->params = $params;
	}

	function getDateFilter() {

		$expr = '';

		if (isset($this->params['year'])) {
			$expr .= " and date_part('year', ".$this->getFilterField().")::smallint=".intval($this->params['year']);
		}


		if (isset($this->params['month'])) {
			$expr .= " and date_part('month', ".$this->getFilterField().")::smallint=".intval($this->params['month']);
		}

		if (isset($this->params['date'])) {
			$date = preg_replace('#([^\d.])#Usi','',$this->params['date']);
			$expr .= " and {$this->getFilterField()}='$date'";
		}

		return $expr;
	}

	function getFilterField() {
		return (isset($this->date_filter_field)) ? $this->date_filter_field : 'create_date';
	}

	function setFilterField($field) {
		$this->date_filter_field=$field;
	}


	function setOwner($owner) {
		$this->owner=$owner;
	}

	function getEventTypes() {
		$sql="select * from event_type order by name";
		return $this->db->fetchAll($sql);
	}

	function getCalendarEventsYears() {
		$sql="
			select
				distinct to_char(create_date,'YYYY')::integer as n
			from
				news.items
			where
				owner_type='{$this->owner}' and owner_id='{$this->id}'
			order by 1
		";

		return $this->db->fetchCol($sql);
	}

	function getPagesCount() {
		$total = $this->getNews(true);
		return ceil($total / $this->items_per_page);
	}

	function getNews($onlycount=false,$order=null) {

		if ($this->getDateFilter()) {
			$date_expr = $this->getDateFilter();
		}else 
			$date_expr = ($this->date!==false) ? " and create_date='{$this->date}'" : "";

		$add_expr = "";

		$s_items = ($onlycount) ? "count(1)" : "t.*,e.*, to_char(e.create_date, 'DD.MM.YYYY') as datef";

		$limit = (!$onlycount) ? "offset ".($this->items_per_page*$this->current_page-$this->items_per_page)." limit ".$this->items_per_page : "";
		
		$order_expr = (!$order) ? "order by create_date desc,e.id desc" : " order by ".$order;
		
		
		$sql="
			select
				$s_items
			from (
				select
					e.id as id,
					count(ec.id) as cnt,
					e.owner
				from
					news.items e
				LEFT JOIN
					news.comments ec on (e.id=ec.news_id)
				where
				(
					({$this->getOwnerExpr()})
					$date_expr
                    {$this->getExcludeExpr()}
				)
				group by e.id, e.owner
			) t
			inner join
				news.items e using (id)
			".((!$onlycount) ? $order_expr : "")."
			$limit
		";

		if ($onlycount)
			$out = $this->s_fetchOne($sql);
		else {
			$out = $this->s_fetchAll($sql);

			news::attachImagesToNewsArray($out);
		}
		return $out;
	}

	function getOwnerExpr() {
		if (is_array($this->owner)) {
			return "owner in ('".implode("','",$this->owner)."')";
		}else return "owner='{$this->owner}'";
	}
	
    function getExcludeExpr() {
		if ($this->exclude) {
			return " and e.id!=".(int) $this->exclude;
		}
        
        return "";
    }
	
    function exclude($id) {
        $this->exclude = $id;
    }
    
	function getOwnerById($id) {
		$sql = "
			
			select
				owner
			from
				news.items
			where
				id=".(int) $id;
		
		return $this->s_fetchOne($sql);
	}
	
	
	function getEvent($id) {
		$sql="
				select
					*,
					to_char(create_date, 'DD.MM.YYYY') as datef
				from
					news.items
				where
					id=".intval($id)."
				and
					owner='$this->owner'
				order by create_date desc
		";

		$data = $this->s_fetchRow($sql);
        
        $a = array($data);
        
        news::attachImagesToNewsArray($a);

		return $a[0];
	}


	function getLastNews($count=3) {
		$count = intval($count);
		$sql = "
			select
				n.*,
				(select count(1) from news.comments where news_id=n.id) as cnt
			from
				news.items n
			where
				owner='{$this->owner}'

			order by 
                ".$this->getLastNewsCustomOrder()."
			limit $count offset 0
		";


		$data = $this->s_fetchAll($sql);

		news::attachImagesToNewsArray($data);

		return $data;
	}


    function getLastNewsCustomOrder() {
        return ' create_date desc ';
    }

	function getEventData($id) {
		$id = intval($id);
		$sql="select * from news.items where id=$id";
		return $this->db->fetchRow($sql);
	}



	function getComments($id) {
		$id = intval($id);
		$sql = "
			select
				nc.id,
				nc.user_id as author,
				nc.comment,
				nc.datetime,
				full_user_name(u) as user_fio,
				u.first_name,
				u.last_name
			from
				news.comments nc
			inner join
				users u on u.id=nc.user_id
			where
				news_id=$id
			order by datetime desc
		";

		return $this->s_fetchAll($sql);
	}



	function saveComment($params) {

		$db_data = array();
		$db_data['news_id'] = $params['id'];
		$db_data['user_id'] = $this->user_data->id;
		$db_data['comment'] = strip_tags($params['comment']);

		$this->pq('insert','news.comments',$db_data);

	}

	function savenews($params,&$error="") {
		$error = "";

		$user_id = (isset(Zend_Registry::get('user_data')->id) && Zend_Registry::get('user_data')->id!='') ? Zend_Registry::get('user_data')->id : new Zend_Db_Expr('null');

		$db_data = array();
		$db_data["owner"] = $this->owner;
		$db_data["user_id"] = $user_id;
		$db_data["description"] = html_entity_decode($params["description"],ENT_QUOTES,'UTF-8');
		$db_data["text"] = html_entity_decode($params["text"],ENT_QUOTES,'UTF-8');
		$db_data["header"] = html_entity_decode($params["header"],ENT_QUOTES,'UTF-8');
		$db_data["create_date"] = ($params['create_date']) ? strftime('%Y-%m-%d', strtotime($params['create_date'])).strftime(' %H:%M:%S') : new Zend_Db_Expr('now()');

		$this->fillCustomFieldsBeforeSave($db_data,$params);

		try {
                    $this->db->beginTransaction();

                    if ($params['id']) {
                            $this->db->update("news.items",$db_data,"id={$params['id']}");
                    }else $this->db->insert("news.items",$db_data);

                    $id = ($params['id']) ? intval($params['id']) : $this->db->lastInsertId('news.items_id');

                    if (!$id) throw new Exception('Не удалось отредактировать новость');

                    $this->uploadVideo($id);
                    $this->uploadAudio($id);

                    PhotoUpload("news",$id);

                    $this->addNewsPostAction($id);

                    $this->db->commit();
                    return true;
		}catch (Exception $e) {

			$this->db->rollBack();

			$config = Zend_Registry::get('cnf');
			errorReport($e,get_defined_vars());
			$error= ($config->debug->on) ? $e->getMessage() : $config->debug->message;
			return false;
		}
	}

	
	function addNewsPostAction($id) {
		
	}
	
	

	function uploadVideo($id) {
		$filename = News_IndexController::uploadMedia($id,'video');

		if ($filename) {
			$db_data = array(
				'video'	=> $filename
			);

			$this->pq('update','news.items',$db_data,'id='.intval($id));
		}
	}


	function uploadAudio($id) {
		$filename = News_IndexController::uploadMedia($id, 'audio');

		if ($filename) {
			$db_data = array(
				'audio'	=> $filename
			);

			$this->pq('update','news.items',$db_data,'id='.intval($id));
		};
	}





	function fillCustomFieldsBeforeSave(&$db_data,$params) {}



	public function delEvent($id) {
		$this->pq('delete',"news.items","id = ".intval($id));
	}

	
	public function delEventsByOwner($owner) {
		$this->pq('delete',"news.items","owner = '$owner'");
	}

	
	function getItemOwnerById($id) {
		$sql = "
			select
				owner
			from
				news.items
			where
				id=".intval($id);
		
		return $this->s_fetchOne($sql);
	}
	
	
	function getEventsYears() {
		$sql = "
			select
				to_char(n.create_date,'YYYY') as year
			from
				news.items n
			where
				n.owner='{$this->owner}'
			group by 1
			order by 1 desc
		";
				
				
		return $this->s_fetchCol($sql);
	}
	
	
	function moveItemUp($item_id) {
		
		$current_item_data = $this->getEvent($item_id);
		
		$sql = "
			select
				i.*
			from
				news.items i
			where
				i.create_date>'{$current_item_data['create_date']}'::timestamp
			and
				i.id!={$current_item_data['id']}
			and
				{$this->getOwnerExpr()}
			order by create_date
			
			limit 1
		";
				
		$item = $this->s_fetchRow($sql);
		
		if ($item) {
			/////////////// апдейтим исходную новость
			$db_data['create_date'] = strftime('%Y-%m-%d %H:%M:%S',strtotime($item['create_date']));
			
			$this->pq('update','news.items',$db_data,'id='.$current_item_data['id']);
			
			////////////// апдейтим новость с которой списздили дату
			
			$db_data['create_date'] = strftime('%Y-%m-%d %H:%M:%S',strtotime($current_item_data['create_date']));
			
			$this->pq('update','news.items',$db_data,'id='.$item['id']);
		}
		
		return true;
	}
	
	
	function moveItemDown($item_id) {
		
		$current_item_data = $this->getEvent($item_id);
		
		$sql = "
			select
				i.*
			from
				news.items i
			where
				i.create_date<'{$current_item_data['create_date']}'::timestamp
			and
				i.id!={$current_item_data['id']}
			and
				{$this->getOwnerExpr()}
			order by create_date desc
			
			limit 1
		";
				
		$item = $this->s_fetchRow($sql);
		
		if ($item) {
			/////////////// апдейтим исходную новость
			$db_data['create_date'] = strftime('%Y-%m-%d %H:%M:%S',strtotime($item['create_date']));
			
			$this->pq('update','news.items',$db_data,'id='.$current_item_data['id']);
			
			////////////// апдейтим новость с которой списздили дату
			
			$db_data['create_date'] = strftime('%Y-%m-%d %H:%M:%S',strtotime($current_item_data['create_date']));
			
			$this->pq('update','news.items',$db_data,'id='.$item['id']);
		}
		
		return true;
	}
	
    function getNextNewsAfter($id) {
        
        $id = (int) $id;
        
        $sql = "
            select * from news.items where id=(
                select
                    i.id
                from
                    news.items i
                where 
                    i.create_date>(select create_date from news.items where id=$id)
                order by 
                    create_date
                limit 1
            )
        ";
        
        return $this->s_fetchRow($sql);
    }
    
    function getPrevNewsBefore($id) {
        
        $id = (int) $id;
        
        $sql = "
            select * from news.items where id=(
                select
                    i.id
                from
                    news.items i
                where 
                    i.create_date<(select create_date from news.items where id=$id)
                order by  
                    create_date desc
                limit 1
            )
        ";
        
        return $this->s_fetchRow($sql);
    }
}



