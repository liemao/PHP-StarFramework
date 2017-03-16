<?php
/**
 * @package library\Star\Application\Cache
 */

/**
 * Redis 缓存类
 * 
 * @package library\Star\Application\Cache
 * @author zhangqinyang
 *
 */
class Star_Cache_Redis implements Star_Cache_Interface {

	/**
	 * redis实例
	 * @var unknown
	 */
	public $redis = null;
	
	/**
	 * 构造方法   实例化$redis变量
	 * @param array $config
	 */
	public function __construct(array $config)
	{
		$this->redis = new Redis();
        $this->redis->connect($config['host'], $config['port']);
	}
	
	/**
	 * 添加redis缓存项
	 * @see Star_Cache_Interface::add()
	 */
	public function add($key, $value, $lefttime = 0)
	{
		if ($lefttime > 0)
        {
            return $this->redis->setex($key, $lefttime, $value);
        } else
        {
            return $this->redis->set($key, $value);
        }
	}
	
	/**
	 * 获取redis缓存项
	 */
	public function get($key)
	{
		return $this->redis->get($key);
	}
	
	/**
	 * 添加redis缓存项
	 * @see Star_Cache_Interface::set()
	 */
	public function set($key, $value, $lefttime = 0)
	{
        if ($lefttime > 0)
        {
            return $this->redis->setex($key, $lefttime, $value);
        } else
        {
            return $this->redis->set($key, $value);
        }
	}
	
	/**
	 * 销毁redis缓存项
	 * @see Star_Cache_Interface::delete()
	 */
	public function delete($key)
	{		
		return $this->redis->del($key);
	}

    public function close()
    {
        $this->redis = null;
    }


    public function __call($method, $arguments) {
        return call_user_func_array(array($this->redis, $method), $arguments);
    }
}

?>