<?php
/**
 * @package library\Star\Model\Mysqli
 */

/**
 * 导入文件
 */
require_once 'Star/Model/Interface.php';
require_once 'Star/Model/Mysqli/Select.php';

/**
 * 数据模型 基类
 * 
 * @package library\Star\Model\Mysqli
 * @author zhangqinyang
 *
 */
class Star_Model_Mysqli_Abstract implements Star_Model_Interface
{
	protected $db;
	
	protected $default_charset = 'utf8';
	
    protected $slow_query_log = false; //默认关闭慢查询日志

    protected $slow_query_time = 2; //慢查询默认时间

    protected $statement = null;
	
    /**
     * 构造方法
     * 
     * @param unknown $config
     */
	public function __construct($config)
	{
		$this->init($config);
	}
    
	/**
	 * 初始化
     * 
	 * @param array $config
	 */
    protected function init($config)
    {
        if (isset($config['slow_query_log']))
        {
            $this->slow_query_log = $config['slow_query_log'];
        }
        
        if (isset($config['slow_query_time']) && !empty($config['slow_query_time']))
        {
            $this->slow_query_time = $config['slow_query_time'];
        }
        
        $this->connect($config);
    }

    /**
     * 连接mysql数据库
     * 
     * @param type $db
     * @throws Star_Exception
     */
    public function connect($db)
	{
        if (!extension_loaded('mysqli'))
        {
            throw new Star_Exception('The Mysqli extension is required for this adapter but the extension is not loaded');
        }
        
		extract($db);
        !isset($port) && $port = 3306; 
		$this->db = new mysqli($host, $username, $password, $dbname, $port);
		
		if ($this->db->connect_error)
		{
			throw new Star_Exception('Mysqli connet error:(' . $this->db->connect_errno . ')' . $this->db->connect_error, 500);
		}
        
        $this->db->set_charset($this->default_charset);
	}
	
	/**
	 * 开始事务执行
	 */
	public function beginTransaction()
	{
		$this->db->autocommit(false);
	}
	
	/**
     * 插入数据
     * 
     * @param type $table
     * @param array $data
     * @return int 插入数据的自增ID
     */
	public function insert($table, Array $data)
	{
		$columns = array_keys($data); //表字段
		$columns = $this->quoteIdentifier($columns);
		$data = $this->quoteIdentifier($data, true);
		$sql = 'INSERT INTO ' . $this->quoteIdentifier($table) . '(' . implode(',', $columns) . ') VALUES (' . implode(',', $data) . ')';
		$this->_query($sql);
		return $this->db->insert_id;
	}
	
    /**
     * 更新数据
     * 
     * @param type $table
     * @param type $where
     * @param array $data
     * @param type $quote_indentifier
     * @return int 影响行数
     */
	public function update($table, $where, Array $data, $quote_indentifier = true)
	{
		
		foreach ($data as $column => &$value)
		{
            $value = $this->quoteIdentifier($column) . ' = ' .  ($quote_indentifier == true ? $this->quoteIdentifier($value, true) : $value);
		}
		
		$sql = 'UPDATE ' . $this->quoteIdentifier($table) . ' SET ' . implode(',', $data) . ' WHERE ' . $this->setWhere($where);
		$this->_query($sql);
		return $this->rowCount();
		
	}

    protected function setWhere($where)
    {
        if (is_array($where))
        {
            $data = $where;
            $where = array();
            foreach ($data as $key => $value)
            {
                if (is_array($value))
                {
                    $where[] = $key . ' in( ' . implode(',', $this->quoteIdentifier($value, true)) . ' )';
                } else {
                    $where[] = $key . ' = ' . $this->quoteIdentifier($value, true);
                }
            }
            $where = implode(' AND ', $where);
        } else {

        }
        return $where;
    }


    /**
	 * 删除数据
     * 
     * @param $table
	 * @param $where
     * @return int 影响行数
	 */
	public function delete($table, $where)
	{
		$sql = 'DELETE FROM ' . $table . ' WHERE ' . $this->setWhere($where);
		$this->_query($sql);
		return $this->rowCount();
	}
    
    /**
     * 根据条件生成SQL
     * 
     * @param type $where
     * @param type $conditions
     * @param type $table
     * @param type $order
     * @param type $page
     * @param type $page_size
     * @return type 
     */
    protected function getSql($where, $conditions, $table, $order = null, $page = 1, $page_size = 1)
    {
        $select = $this->select();
        $select->from($table, $conditions)->where($where);
        
        if ($order != null)
        {
            $select->order($order);
        }
        
        if ($page != null && $page_size != null)
        {
            $select->limitPage($page, $page_size);
        }
        
        return $select->assemble();
    }


    /**
     * 返回结果集
     * @param Star_Model_Mysqli_Select $where
     * @param type $conditions
     * @param type $table
     * @param type $order
     * @param type $page
     * @param type $page_size
     * @return array $data
     */
	public function fetchAll($where, $conditions = null, $table = null, $order = null, $page = null, $page_size = null)
	{
        $sql = '';
        
		if ($where instanceof Star_Model_Mysqli_Select)
		{
			$sql = $where->assemble();
		} else
        {
            $sql = $this->getSql($where, $conditions, $table, $order, $page, $page_size);
        }
		
		$result = $this->_query($sql);
        $data = array();
		
		while ($rs = $result->fetch_assoc())
		{
			$data[] = $rs;
		}
		
		$result->free();
		return $data;
	}
	
	/**
     * 返回结果集第一个字段
     * 
     * @param Star_Model_Mysqli_Select $where
     * @param type $conditions
     * @param type $table
     * @param type $order
     * @return type
     */
	public function fetchOne($where, $conditions = null, $table = null, $order = null)
	{
        $sql = '';
        
		if ($where instanceof Star_Model_Mysqli_Select)
		{
			$where->limit(1);
			$sql = $where->assemble();
		} else {
            $sql = $this->getSql($where, $conditions, $table, $order);
        }

		$result = $this->_query($sql);
		$data = $result->fetch_assoc();
        $result->free();
		return is_array($data) ? current($data) : '';
	}
	
	/**
     * 返回一行结果集
     * 
     * @param Star_Model_Mysqli_Select $where
     * @param type $conditions
     * @param type $table
     * @param type $order
     * @return type
     */
	public function fetchRow($where, $conditions = null , $table = null, $order = null)
	{
		if ($where instanceof Star_Model_Mysqli_Select)
		{
			$where->limit(1);
			
			$sql = $where->assemble();
		} else {
            $sql = $this->getSql($where, $conditions, $table, $order);
        }
		
		$result = $this->_query($sql);
		$data = $result->fetch_assoc();
        $result->free();
		return $data;
	}
	
    /**
     * 返回第一个查询字段集合
     * 
     * @param Star_Model_Mysqli_Select $where
     * @param type $conditions
     * @param type $table
     * @param type $order
     * @param type $page
     * @param type $page_size
     * @return type
     */
	public function fetchCol($where, $conditions = null , $table = null, $order = null, $page = null, $page_size = null)
	{
		$sql = '';
        
		if ($where instanceof Star_Model_Mysqli_Select)
		{
			$sql = $where->assemble();
		} else
        {
            $sql = $this->getSql($where, $conditions, $table, $order, $page, $page_size);
        }
		
		$result = $this->_query($sql);
        $data = array();
		
		while ($rs = $result->fetch_row())
		{
			$data[] = $rs[0];
		}
		return $data;
	}
    
    /**
     * 执行sql
     * 
     * @param type $sql
     * @return result
     */
    public function query($sql)
    {
        $result = $this->_query($sql);
        if ($this->isSelect($sql) == true)
        {
            $data = array();
            while ($rs = $result->fetch_assoc())
            {
                $data[] = $rs;
            }
            $result->free();
            return $data;
        } else {
            return $this->rowCount();
        }
    }
    
    /**
     * 判断是否是查询sql
     * 
     * @param type $sql 
     * return bool是否是查询语句
     */
    public function isSelect($sql)
    {
        $sql = trim($sql);
        if (strtoupper(substr($sql, 0, 6)) == 'SELECT')
        {
            return true;
        } else
        {
            return false;
        }
    }


    /**
	 * 返回影响行数
     * 
     * @return int affected_rows
	 */
	public function rowCount()
	{
		return $this->db->affected_rows;
	}
	
	/**
	 * sql query
     * 
	 * @param $sql
     * @return $resource
	 */
	public function _query($sql)
	{
        if ($this->slow_query_log == true)
        {
            $start_time = time();
        }
        
		$resource = $this->db->query($sql);
        if ($resource === false)
        {
            throw new Star_Exception("SQL: ". $sql . " \nError Message:" . $this->db->error, 500);
        }

        if($this->slow_query_log == true)
        {
            $end_time = time();

            $time = $end_time - $start_time;

            if ($time >= $this->slow_query_time) //慢查询日志
            {
                $stact_trace = Star_Debug::Trace(); //返回堆栈详细信息
                $stact_trace = implode("\n", $stact_trace);
                Star_Log::log("Query_time: {$time}s     Slow query: {$sql} \nStack trace:\n{$stact_trace}", 'slow_query'); //记录慢查询日志
            }
        }
        
        return $resource;
	}
    
    /**
     * 返回select对象
     * 
     * @return \Star_Model_Mysqli_Select
     */
    public function select()
	{
		return new Star_Model_Mysqli_Select();
	}
	
    /**
     * 关闭数据连接
     */
	public function close()
	{
		return $this->db->close();
	}
	
	/**
	 * 字符串引号
	 *
	 * @param $identifier
	 * @param $auto_quote
     * return $identifier
	 */
	public function quoteIdentifier($identifier, $auto_quote = false)
	{
		if (is_array($identifier))
		{
			foreach ($identifier as &$value)
			{
				if ($auto_quote == true)
				{
					$value = "'" . addslashes($value) . "'";
				} else
				{
					$value = '`' . $value . '`';
				}
			}
		} else
		{
			if ($auto_quote == true)
			{
				$identifier = "'" . addslashes($identifier) . "'";
			} else
			{
				$identifier = '`' . $identifier . '`';
			}
		}
		
		return $identifier;
	}
	
	protected function deepStripslashes($data)
	{
		if (is_array($data))
		{
			foreach ($data as $key => $value)
			{
				$data[$key] = $this->deepStripslashes($value);
			}
		} else if (is_string($data))
		{
			$data = stripcslashes($data);
		}
		
		return $data;
	}
	
	/**
	 * 提交事务
     * 
	 * @return boolean
	 */
	public function commit()
	{
		return $this->db->commit();
	}
	
	/**
	 * mysql操作回滚
     * 
	 * @return boolean
	 */
	public function rollback()
	{
		return $this->db->rollback();
	}

}
?>