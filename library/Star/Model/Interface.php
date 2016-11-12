<?php
/**
 * @package library\Star\Model
 */

/**
 *
 * @package library\Star\Model
 * @author zhangqinyang 2010/05/27
 *
 */
interface Star_Model_Interface {
	
	public function connect($db);
	
	/**
     * 插入数据
     * 
     * @param type $table
     * @param array $data
     */
	public function insert($table, Array $data);
	
	/**
     * 更新数据
     * 
     * @param type $table
     * @param type $where
     * @param array $data
     * @param type $quote_indentifier
     */
	public function update($table, $where, Array $data, $quote_indentifier = true);
	
	/**
     * 删除数据
     * 
     * @param type $table
     * @param type $where
     */
	public function delete($table, $where);
	
	/**
     * 返回结果集
     * 
     * @param type $where
     * @param type $conditions
     * @param type $table
     * @param type $order
     * @param type $page
     * @param type $page_size
     */
	public function fetchAll($where, $conditions = null, $table = null, $order = null, $page = null, $page_size = null);
	
	/**
     * 返回结果集第一个字段
     * 
     * @param Star_Model_Pdo_Mysql_Select $where
     * @param type $conditions
     * @param type $table
     * @param type $order
     */
	public function fetchOne($where, $conditions = null, $table = null, $order = null);
	
	/**
     * 返回一行结果集
     * 
     * @param Star_Model_Pdo_Mysql_Select $where
     * @param type $conditions
     * @param type $table
     * @param type $order
     */
	public function fetchRow($where, $conditions = null , $table = null, $order = null);
	
	/**
     * 返回第一个查询字段集合
     * 
     * @param type $where
     * @param type $conditions
     * @param type $table
     * @param type $order
     * @param type $page
     * @param type $page_size
     */
	public function fetchCol($where, $conditions = null , $table = null, $order = null, $page = null, $page_size = null);
	
	/**
     * 执行SQL
     * 
     * @param type $sql
     * @return type
     */
    public function query($sql);
	
	public function select();
	
	public function close();
}

?>