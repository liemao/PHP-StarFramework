<?php
/**
 * @package library\Star
 */

/**
 * 导入文件
 */
require 'Star/Cache/Interface.php';

/**
 * 缓存类
 * 
 * @package library\Star
 * @author zhangqinyang
 *
 */
class Star_Cache {
	
	protected static $instance = null;
	
	protected $cache_types = array('Memcache', 'File', 'Memcached', 'Redis'); //缓存类型
	
	public $caches = array();

    public $configs = array();

    protected static $default_cache = 'default';

    /**
     * 构造函数
     * 
     * @param array $config 
     */
    protected function __construct(array $configs)
	{
		$this->configs = $configs;
	}
	
    /**
     *  工厂方法  
     * 
     * @param array $config
     * @return type
     * @throws Star_Exception 
     */
	public function factory(array $config)
	{
		$cache_adapter = ucfirst($config['adapter']);
		$cache_class = "Star_Cache_{$cache_adapter}";

		if (!in_array($cache_adapter, $this->cache_types))
		{
			throw new Star_Exception( 'Unsupport '. $cache_adapter . ' cache adapter.');
			return ;
		}

		return new $cache_class($config);
	}
        
    /**
     * 获取一个缓存实例
     * @return type 
     */
    public static function getCache()
    {
        if (self::$instance->cache === null)
        {
            self::$instance->cache = self::$instance->factory(self::$instance->config);
        }
        
        return self::$instance->cache;
    }

    public static function connection($connection)
    {
        if (!isset(self::$instance->caches[$connection]))
        {
            $config = isset(self::$instance->configs[$connection]) ? self::$instance->configs[$connection] : self::$instance->configs;
            self::$instance->caches[$connection] = self::$instance->factory($config);
        }

        return self::$instance->caches[$connection];
    }

    /**
     * 单例模式
     * @param array $config
     * @return Star_Cache
     */
	public static function initInstance(array $configs)
	{
		if (self::$instance == null)
		{
			self::$instance = new self($configs);
		}
		
		return self::$instance;
	}
	
    /**
     * 添加缓存
     * 
     * @param type $key
     * @param type $value
     * @param type $lefttime
     * @return type 
     */
	public static function add($key, $value, $lefttime = 0)
	{
		return self::connection(self::$default_cache)->add($key, $value, (int) $lefttime);
	}
	
    /**
     * 根据key返回缓存值
     * 
     * @param type $key
     * @return type 
     */
	public static function get($key)
	{
		return self::connection(self::$default_cache)->get($key);
	}
	
    /**
     * 重置缓存值
     * 
     * @param type $key
     * @param type $value
     * @param type $lefttime
     * @return type 
     */
	public static function set($key, $value, $lefttime = 0)
	{
		return self::connection(self::$default_cache)->set($key, $value, (int) $lefttime);
	}
	
    /**
     * 根据key删除缓存值
     * 
     * @param type $key
     * @return type 
     */
	public static function delete($key)
	{
		return self::connection(self::$default_cache)->delete($key);
	}

    public static function incr($key, $increment_value = 1)
    {
        return self::connection(self::$default_cache)->incr($key, $increment_value);
    }

    public static function decr($key, $decrement_value = 1)
    {
        return self::connection(self::$default_cache)->decr($key, $decrement_value);
    }

    public static function expire($key, $timeout)
    {
        return self::connection(self::$default_cache)->expire($key, $timeout);
    }
}

?>