<?php
/**
 * @package library\Star\Cache
 */

/**
 * Memcach缓存类
 *
 * @package library\Star\Cache
 * @author zhangqinyang
 */

class Star_Cache_Memcache implements Star_Cache_Interface {

	/**
	 * memcache实例
	 * @var unknown
	 */
	public $memcache = null;
	
	/**
	 * 构造方法   实例化$memcache变量
	 * @param array $config
	 */
	public function __construct(array $config)
	{
		$this->memcache = new Memcache();
		if (isset($config['multi_cache']) && $config['multi_cache'] == true)
		{
			foreach ((array) $config['server'] as $memcache)
			{
				$this->memcache->addServer($memcache['host'], $memcache['port']);
			}
		} else
		{
			$this->memcache->addServer($config['host'], $config['port']);
		}
	}
	
	/**
     * 添加缓存
     * 
     * @param string $key
     * @param string|array $value
     * @param int $lefttime
     * @return bool
     */
	public function add($key, $value, $lefttime = 0)
	{
		if ($lefttime == 0)
		{
			return $this->memcache->add($key, $value, false);
		} else
		{
			return $this->memcache->add($key, $value, false, $lefttime);
		}
	}
	
	/**
     * 获取缓存
     * 
     * @param string $key
     * @return type
     */
	public function get($key)
	{
		return $this->memcache->get($key);
	}
	
	/**
     * 设置缓存
     * 
     * @param string $key
     * @param string|array $value
     * @param int $lefttime
     * @return type
     */
	public function set($key, $value, $lefttime = 0)
	{
		if ($lefttime == 0)
		{
			return $this->memcache->set($key, $value, false);
		} else
		{
			return $this->memcache->set($key, $value, false, $lefttime);
		}
	}
	
	/**
     * 删除缓存
     * 
     * @param string $key
     * @return type
     */
	public function delete($key)
	{
		return $this->memcache->delete($key);
	}
    
	/**
	 * 关闭缓存链接
	 */
    public function close()
    {
        if (is_object($this->memcache))
        {
            $this->memcache->close();
        }
    }

    public function incr($key, $increment_value = 1)
    {
        if ($this->memcache->get($key) === false)
        {
            return $this->memcache->set($key, $increment_value, false);
        }
        return $this->memcache->increment($key, $increment_value);
    }

    public function decr($key, $decrement_value = 1)
    {
        if ($this->memcache->get($key) === false)
        {
            return $this->memcache->set($key, $decrement_value, false);
        }
        return $this->memcache->decrement($key, $decrement_value);
    }

    public function expire($key, $timeout)
    {
        return ;
    }
}

?>