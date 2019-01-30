<?php
/**
 * @package library\Star\Cache
 */

/**
 * Memcached 缓存类
 *
 * @package library\Star\Cache
 * @author zhangqinyang
 * 
 */
class Star_Cache_Memcached implements Star_Cache_Interface {

	/**
	 * memcached实例
	 * @var unknown
	 */
	public $memcached = null;
	
	/**
	 * 构造方法   实例化$memcached变量
	 * @param array $config
	 */
	public function __construct(array $config)
	{
		$this->memcached = new Memcached();
		
		if (isset($config['multi_cache']) && $config['multi_cache'] == true)
		{
			$this->memcached->addServers($config['server']);
			$this->memcached->setOption(Memcached::OPT_DISTRIBUTION, Memcached::DISTRIBUTION_CONSISTENT);
			$this->memcached->setOption(Memcached::OPT_HASH, Memcached::HASH_CRC);
		} else
		{
			$this->memcached->addServer($config['host'], $config['port']);
		}
	}
	
	/**
	 * 根据key所在服务器映射出该服务器信息
     * 
	 * @param int $key
	 */
	protected function getServerByKey($key)
	{
		return $this->memcached->getServerBykey($key);
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
			return $this->memcached->add($key, $value);
		} else
		{
			return $this->memcached->add($key, $value,  $lefttime);
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
		return $this->memcached->get($key);
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
			return $this->memcached->set($key, $value);
		} else
		{
			return $this->memcached->set($key, $value, $lefttime);
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
		return $this->memcached->delete($key);
	}

    public function incr($key, $increment_value = 1)
    {
        if ($this->memcached->get($key) === false)
        {
            return $this->memcached->set($key, $increment_value);
        }
        return $this->memcached->increment($key, $increment_value);
    }

    public function decr($key, $decrement_value = 1)
    {
        if ($this->memcached->get($key) === false)
        {
            return $this->memcached->set($key, $decrement_value);
        }
        return $this->memcached->decrement($key, $decrement_value);
    }

    public function expire($key, $timeout)
    {
        return ;
    }

    /**
	 * 关闭缓存
	 */
    public function colse()
    {
         $this->memcached = null;
    }
}

?>