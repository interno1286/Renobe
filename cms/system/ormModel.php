<?php
/**
 *
 */
class ormModel extends DbModel {
    
    public $table=null;          ///// Основная таблица модели указывается для проверки и автоматическорй установки в случае отсутствия
    public $schema='public';     ///// Рабочая схема модели - указывается для проверки и автоматическорй установки в случае отсутствия
    
    public $last_id; // last insert id
    public $last_error=''; // last error
    
    public $table_struct = false;
    
    static $instance = array();
    
    function __construct($schema='',$table='') {
        
        if ($schema)
            $this->schema = $schema;
        
        if ($table)
            $this->table = $table;
        
        else if ($schema) {
            $this->table = $schema;
            $this->schema = 'public';
        }
        
        parent::__construct();
    }
    
    static function init($schema, $table='') {
        return self::getInstance($schema, $table);
    }
    
    static function getInstance($schema, $table='') {
        $key = substr(md5($schema.$table),0,6);
        
        if (isset(self::$instance[$key]))
            return self::$instance[$key];
        
        if (strpos($schema, 'Model')!==false) {
            self::$instance[$key] = new $schema();
        }else self::$instance[$key] = new ormModel($schema, $table);
        
        return self::$instance[$key];
    }
    
    function initModel() {
        if (!isset($this->table))
            throw new Exception('cannot use ormModel without variable public $table="some_db_table"');
    }
    
    function newItem($data) {
        $ret = $this->pq('insert', $this->getScheme().$this->table, $data, false, $this->last_error, $this->last_id);
        
        $this->user_data->result = $ret;
        $this->user_data->last_error = $this->last_error;
        
        return $ret;
    }
    
    function updateItem($data,$expr) {
        return $this->pq('update',$this->getScheme().$this->table,$data,$expr);
    }
    
    function insertIfNotExists($data, $updateIfExists = false) {
        
        $sql = "select id from {$this->getScheme()}{$this->table} where ";
        
        foreach ($data as $field_name=>$value)
            $expr[] = ($value instanceof Zend_Db_Expr) ? "{$field_name}={$value}" : "{$field_name}='{$value}'";
        
        $sql .= implode(" and ",$expr);
        
        $exists_id = $this->s_fetchOne($sql);
        
        if ($exists_id) {
            if ($updateIfExists)
                $this->updateItem($data, 'id='.$exists_id);
            
            return $exists_id;
        }else {
            $this->newItem($data);
            return $this->last_id;
        }
        
    }
    
    
    function sum($field) {
        
        $sql = "
            select
                sum($field)
            from
                {$this->table}
        ";
        
        return $this->s_fetchOne($sql);
    }
    
    function getAll($where=null, $order_by=null) {
        return $this->selectAll($where, $order_by);
    }
    
    function getAllWithPages($where=null, $order_by=null) {
        if (!$order_by) $order_by = '';
        return $this->selectAll($where, $order_by.' '.$this->getPageExpr());
    }
    
    function selectAll($where=null, $order_by=null) {
        $sql = "
            select
                *
            from
                {$this->getScheme()}{$this->table}
        ";
                
        if ($where)
            $sql .= "
            where
                $where
        ";
        
        if ($order_by)
            $sql .= "
                order by $order_by
            ";
        
        $data =  $this->s_fetchAll($sql, $this->last_error);
        
        return $data;
    }
    
    static function fastGetAll($schema,$table,$where='',$order_by='') {
        $model = new ormModel($schema, $table);
        
        return $model->getAll($where, $order_by);
    }
    
    function delAll() {
        $res = $this->pq('delete',$this->getScheme().$this->table,false,false,$error);
        $this->setLastError($res, $error);
        return $res;
    }
    
    function del($expr) {
        return $this->pq('delete',$this->getScheme().$this->table, $expr);
    }
    
    function ins($data) {
        $res = $this->pq('insert',$this->getScheme().$this->table,$data,false, $error, $this->last_id);
        $this->setLastError($res, $error);
        return $res;
    }
    
    ////// Security is not my problem!!!!!!!
    ////// Вы обязаны фильтровать переменные перед их использованием 
    ////// Можно это делать прямо в SiteBaseController->initController()
    function get($field, $expression='1=1', $order_by='') {
        
        if ($order_by)
            $order_by = ' order by '.$order_by;
        
        $sql = "
            select
                $field
            from
                {$this->getScheme()}{$this->table}
            where
                $expression
            $order_by
        ";
        
        return $this->s_fetchOne($sql);
    }
    
    function getRow($expression) {
        
        $sql = "
            select
                *
            from
                {$this->getScheme()}{$this->table}
            where
                $expression
        ";
        
        return $this->s_fetchRow($sql);
    }
    
    function selectCol($field='', $expr='', $order='') {
        $sql = "
            select
                $field
            from
                {$this->getScheme()}{$this->table}
        ".(($expr) ? ' where '.$expr : '');
                
        if ($order)
            $sql .= " order by $order ";
        
        return $this->s_fetchCol($sql);
    }
    
    function getTableStruct() {
        if ($this->table_struct) return $this->table_struct;
        
        
        $sql = "
            SELECT 
                c.*,
                coalesce(pgd.description,'') as description
            FROM 
	            information_schema.columns c 
            left join
            	pg_catalog.pg_statio_all_tables st on (c.table_schema=st.schemaname and c.table_name=st.relname)
            
            left join
            
            	pg_catalog.pg_description pgd on (pgd.objoid=st.relid and pgd.objsubid=c.ordinal_position)

            WHERE 
                c.table_schema = '{$this->schema}'
            AND 
                c.table_name   = '{$this->table}'
            order by 
                c.ordinal_position

        ";
                
        $data = $this->table_struct = $this->s_fetchAll($sql);
        
        return $data;
    }
    
    function getForeignKeys() {
        $sql = "
            SELECT
                tc.constraint_name, 
                tc.table_name, 
                kcu.column_name, 

                ccu.table_name AS foreign_table_name,
                ccu.column_name AS foreign_column_name,
                ccu.table_schema AS foreign_schema_name 
            FROM 
                information_schema.table_constraints AS tc 
                JOIN information_schema.key_column_usage AS kcu
                  ON tc.constraint_name = kcu.constraint_name
                JOIN information_schema.constraint_column_usage AS ccu
                  ON ccu.constraint_name = tc.constraint_name
            WHERE 
                constraint_type = 'FOREIGN KEY' 
            AND 
                tc.table_name='{$this->table}'
            and
                tc.table_schema = '{$this->schema}'
        ";
                
        return $this->s_fetchAll($sql);
    }
    
    
}
