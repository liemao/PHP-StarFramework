<?php 
/**
 * @package library\Star\Model\Mysqli
 */

/**
 * 导入文件
 */
require_once 'Star/Model/Select/Interface.php';

/**
 * 数据库操作类
 * 
 * @package library\Star\Model\Pdo\Mysql
 * @author zhangqinyang
 *
 */
class Star_Model_Pdo_Mysql_Select implements Star_Model_Select_Interface
{
	protected $_table;
	
	protected $_alias;
	
	protected $_where = array();
    
    protected $_params = array();

    protected $_column = array();
	
	protected $_group_by;
	
	protected $_limit;
	
	protected $_limit_page;
	
	protected $_order_by = array();
	
	protected $_having;
	
	protected $_join = array();
	
	const SQL_FROM = 'FROM';
	
	const SQL_WHERE = 'WHERE';
	
	const SQL_SELECT = 'SELECT';
	
	const SQL_AND = 'AND';
	
	const SQL_OR = 'OR';
	
	const SQL_ON = 'ON';
	
	const SQL_GROUP = 'GROUP BY';
	
	const SQL_ORDER = 'ORDER BY';
	
	const SQL_HAVING = 'HAVING';
	
	const SQL_DESC = 'DESC';
	
	const SQL_ASC = 'ASC';
	
	const SQL_AS = 'AS';
	
	const SQL_LEFT_JOIN = 'LEFT JOIN';
	
	const SQL_RIGHT_JOIN = 'RIGHT JOIN';
	
	const SQL_INNER_JOIN = 'INNER JOIN';
	
	const SQL_LIMIT = 'LIMIT';
	
	const SQL_OFFSET = 'OFFSET';
	
	protected $_parts = array(
		'select',
		'columns',
		'from',
		'join',
		'where',
		'group',
		'having',
		'order',
		'limit'
	);
	
	/**
	 * 添加一个where条件
	 * 
	 * @param string $conditions
	 * @param array or string $value
	 * @return Mysql_Select object
	 */
	public function where($conditions, $value = null)
	{
		$this->setWhere($conditions, $value);
		return $this;
	}
	
    /**
     * 设置where
     * 
     * @param string $conditions
     * @param type $value
     * @param type $where_type
     */
	protected function setWhere($conditions, $value = null, $where_type = self::SQL_AND)
	{
		$conditions = '(' . $conditions . ')';
		if($value !== null)
		{
			if (is_array($value))
			{
				foreach ($value as $k => $v)
				{
					if (!is_numeric($v))
					{
						unset($value[$k]);
					}
				}
				$count = count($value);
				$conditions = str_replace('?', implode(',', array_fill(0, $count, '?')),$conditions);
				foreach ($value as $v)
				{
					$this->_params[] = $v;
				}
			} else {
				$this->_params[] = $value;
			}
		}
		$this->_where[] = !count($this->_where) ? $conditions :  $where_type . ' ' . $conditions;
	}
	
    /**
     * 引号添加反斜杠 防止注入
     * 
     * @param type $value
     * @return type
     */
	protected function disposeQuote($value)
	{
		return addslashes($value);
	}
	
    /**
     * 添加一个where 中的or语句
     * 
     * @param type $conditions
     * @param type $value
     * @return \Star_Model_Pdo_Mysql_Select
     */
	public function orWhere($conditions, $value = null)
	{
		$this->setWhere($conditions, $value, self::SQL_OR);
		return $this;
	}
	

	/**
	 * 设置所要查询的表与字段
     * 
	 * @param $table
	 * @param $columns
	 */
	public function from($table, $columns = '*')
	{
		$this->setTable($table, false);
		$this->setColumn($this->_table, $columns, $this->_alias);
		return $this;
	}
	
    /**
     * 设置表名
     * 
     * @param string $table
     * @param bool $is_join
     * @return type
     */
	protected function setTable($table, $is_join=true)
	{
        $alias = '';
		if (preg_match('/^(.+)\s+' . self::SQL_AS . '\s+(.+)$/i', $table, $buffer))
		{
			$table_name = $buffer[1];
			$alias      = $buffer[2];
		} else
		{
			$table_name = $table;
		}
		if ($is_join == false)
		{
			$this->_table = $table_name;
			$this->_alias = $alias;
		}else
		{
			return array('table_name'=>$table_name, 'alias'=>$alias);
		}
	}
	
    /**
     * 设置查询字段
     * 
     * @param type $table_name
     * @param type $columns
     * @param type $alias
     */
	protected function setColumn($table_name, $columns, $alias)
	{
        if ($columns)
        {
            if (is_array($columns))
            {
                $talbe = $this->setAlias($table_name, $alias);
                foreach ($columns as $key => $value)
                {
                    $this->_column[] = !is_numeric($key) ? $talbe . '.`' . $value . '` `' . $key . '`': $talbe . '.`' . $value . '`';
                }
            } else
            {
                $talbe = $this->setAlias($table_name, $alias);
                $this->_column[] = $columns == '*' ? $talbe . '.' . $columns : $columns;
            }
        }
	}
	
	protected function setAlias($table_name, $alias)
	{
		return empty($alias) ? $table_name : $alias;
	}
	
	/**
	 * 左外连接表
     * 
	 * @param $table 所要外连的表
	 * @param $conditions 查询条件
	 * @param $columns 查询字段
	 */
	public function joinLeft($table, $conditions, $columns = '')
	{
		$this->setJoin(self::SQL_LEFT_JOIN, $table, $conditions, $columns);
		return $this;
	}
	
	/**
	 * 右外连接
     * 
	 * @param $table 所要外连的表
	 * @param $conditions 查询条件
	 * @param $columns 查询字段
	 */
	public function joinRight($table, $conditions, $columns = '')
	{
		$this->setJoin(self::SQL_RIGHT_JOIN, $table, $conditions, $columns);
		return $this;
	}
	
	/**
	 * 内连接
     * 
	 * @param $table 所要外连的表
	 * @param $conditions 查询条件
	 * @param $columns 查询字段
	 */
	public function joinInner($table, $conditions, $columns = '')
	{
		$this->setJoin(self::SQL_INNER_JOIN, $table, $conditions, $columns);
		return $this;
	}
	
    /**
     * 设置连接条件
     * 
     * @param string $join_type
     * @param string $table
     * @param type $conditions
     * @param type $columns
     */
	protected function setJoin($join_type, $table, $conditions, $columns)
	{
		extract($this->setTable($table, true));
		$this->setColumn($table_name, $columns, $alias);
		$table_name    = '`'.$table_name.'`';
		$alias         = !empty($alias) ? '`' . $alias . '`' : $alias;
		$this->_join[] = array($join_type, $table_name, $alias, self::SQL_ON, $conditions);
	}
	
	/**
	 * 设置having子句
	 * @param $spec 包含值
	 */
	public function having($spec)
	{
		$this->setHaving($spec, self::SQL_AND);
		return $this;
	}
	
	/**
	 * 设置having子句  or条件
     * 
	 * @param unknown $spec
	 * @return Star_Model_Mysqli_Select
	 */
	public function orHaving($spec)
	{
		$this->setHaving($spec, self::SQL_OR);
		return $this;
	}
	
    /**
     * 设置Having参数
     * 
     * @param type $spec
     * @param type $having_type
     */
	protected function setHaving($spec, $having_type = self::SQL_AND)
	{
		$spec = is_string($spec) ? array($spec) : $spec;
		foreach ($spec as $value)
		{
			$this->_having[] = !count($this->_having) ? '(' . $value . ')' : $having_type . ' (' . $value . ')';
		}
	}
	
	/**
	 * 设计记录获取条数
     * 
	 * @param $number 所要获取的记录条数
	 */
	public function limit($number)
	{
		$this->_limit = $number;
		return $this;
	}
	
	/**
	 * 分页查询
     * 
	 * @param $page           
	 * @param $page_number
	 */
	public function limitPage($page, $page_number)
	{
		$page        = ($page <= 0) ? 1 : $page;
		$page_number = ($page_number < 0) ? 1 : $page_number;
		$offset      = ($page - 1) * $page_number;
		$this->_limit_page = compact('page_number', 'offset');
		return $this;
	}
	
	/**
	 * 添加分组查询条件
     * 
	 * @param $spec 分组字段  
	 */
	public function group($spec)
	{
		if (is_string($spec))
		{
			$spec = array($spec);
		}
		$this->_group_by[] = implode(',', $spec);
		return $this;
	}
	
	/**
	 * 设置排序条件
     * 
	 * @param $spec 排序字段  
	 */
	public function order($spec)
	{
		$spec = is_string($spec) ? array($spec) : $spec;
		foreach ($spec as $value)
		{
			$value     = trim($value);
			$direction = self::SQL_ASC;
			if (preg_match('/(.+)\s(.+)/si', $value))
			{
				list($value, $direction) = explode(' ',$value);
				$direction = strtoupper($direction) == self::SQL_ASC ? $direction : self::SQL_DESC;
			}
			$this->_order_by[] = $value . ' ' . $direction;
		}
		return $this;
	}
	
	/**
	 * 查询
     * 
	 * @param unknown $sql
	 * @return string
	 */
	protected function renderSelect($sql)
	{
		return $sql = self::SQL_SELECT;
	}
	
	/**
	 * 查询字段
     * 
	 * @param unknown $sql
	 * @return string
	 */
	protected function renderColumns($sql)
	{
		return $sql .= implode(', ', $this->_column);
	}
	
	/**
	 * 查询添加form
     * 
	 * @param unknown $sql
	 * @return string
	 */
	protected function renderFrom($sql)
	{
		$sql .= self::SQL_FROM . ' ';
		return $sql .= empty($this->_alias) ? '`'.$this->_table.'`' : '`'.$this->_table . '` `' . $this->_alias.'`';
	}
	
    /**
     * 查询添加join
     * 
     * @param string $sql
     * @return string
     */
	protected function renderJoin($sql)
	{
		if (!empty($this->_join) && is_array($this->_join))
		{
			foreach ($this->_join as $value)
			{
				$sql .= implode(' ', $value) . ' ';
			}
		}
		return $sql;
	}
	
    /**
     * 查询添加where条件
     * 
     * @param type $sql
     * @return type
     */
	protected function renderWhere($sql)
	{
		if (!empty($this->_where) && is_array($this->_where))
		{
			$sql .= self::SQL_WHERE . ' ';
			$sql .= implode(' ', $this->_where);
		}
		return $sql;
	}
	
    /**
     * 查询添加group条件
     * 
     * @param type $sql
     * @return type
     */
	protected function renderGroup($sql)
	{
		if (!empty($this->_group_by) && is_array($this->_group_by))
		{
			$sql .= self::SQL_GROUP . ' ';
			$sql .= implode(', ',$this->_group_by);
			
		}
		return $sql;
	}
	
    /**
     * 查询添加having条件
     * 
     * @param type $sql
     * @return type
     */
	protected function renderHaving($sql)
	{
		if (!empty($this->_having) && is_array($this->_having))
		{
			$sql .= self::SQL_HAVING . ' ';
			$sql .= implode(' ', $this->_having);
		}
		return $sql;
	}
	
    /**
     * 查询添加order排序
     * 
     * @param type $sql
     * @return type
     */
	protected function renderOrder($sql)
	{
		if (!empty($this->_order_by) && is_array($this->_order_by))
		{
			$sql .= self::SQL_ORDER . ' ';
			$sql .= implode(', ', $this->_order_by);
		}
		return  $sql;
	}
	
    /**
     * 查询添加限制条数
     * 
     * @param type $sql
     * @return type
     */
	protected function renderLimit($sql)
	{
		if (!empty($this->_limit) || !empty($this->_limit_page))
		{
			$sql .= self::SQL_LIMIT . ' ';
			!empty($this->_limit_page) && is_array($this->_limit_page) && extract($this->_limit_page);
			$sql .= isset($page_number) && isset($offset) ? $page_number . ' ' . self::SQL_OFFSET . ' ' . $offset : $this->_limit;
		}
		return $sql;
	}
	
	/**
	 * 整合整句sql
	 */
	public function assemble()
	{
		$sql = '';
		foreach ($this->_parts as $value)
		{
			$method = 'render' . ucfirst($value);
			if (method_exists($this, $method))
			{
				$sql = $this->$method($sql) . ' ';
			}
		}
		
		return array(
            'sql' => chop($sql),
            'params' => $this->_params,
        );
	}
	
    /**
     * 返回SQL语句
     * 
     * @return type
     */
	public function __toString()
	{
		try {
            $sql_info = $this->assemble();
            $sql = $sql_info['sql'];
            $params = $sql_info['params'];
            $sql  = $this->getCompeleSql($sql, $params);
            
        } catch (Exception $e) {
            $sql = '';
        }
		return (string) $sql;
	}
    
    /**
     * 返回完整的执行SQL语句
     * 
     * @param type $sql
     * @param array $params
     * @return type
     */
    protected function getCompeleSql($sql, Array $params)
    {
        $sql_len = strlen($sql);
        $identifers = array();
        for ($i = 0; $i < $sql_len; $i++)
        {
            if ($sql[$i] == '?')
            {
                $identifers[] = $i;
            }
        }
        
        if (!empty($identifers) && count($params) == count($identifers))
        {
            $params = array_reverse($params);
            $identifers = array_reverse($identifers);
            foreach ($identifers as $key => $value)
            {
				$sql = substr_replace($sql, is_numeric($params[$key]) ? $params[$key] : '"' . $this->disposeQuote($params[$key]) . '"', $value, 1);
            }
            
        }
        return $sql;
    }
}
?>