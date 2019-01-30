<?php
/**
 * @package library\Star\Cache
 */

/**
 * 缓存  接口
 * @package library\Star\Cache
 * @author zhangqinyang
 *
 */
interface Star_Cache_Interface {
	
	/**
	 * 添加缓存项
	 * @param string $key
	 * @param  string|array $value
	 * @param int $lefttime
	 */
	public function add($key, $value, $lefttime = 0);
	
	/**
	 * 获取缓存项
	 * @param string $key
	 */
	public function get($key);
	
	/**
	 * 添加缓存项
	 * @param string $key
	 * @param string|array $value
	 * @param int $lefttime
	 */
	public function set($key, $value, $lefttime = 0);
	
	/**
	 * 销毁缓存项
	 * @param string $key
	 */
	public function delete($key);
}

?>