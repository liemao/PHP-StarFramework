<?php
/**
 * @package library\Star
 */

/**
 *
 * 队列类
 *
 * @package library\Star
 * @author zhangqinyang
 *
 */
abstract class Star_Queue_Abstract {

    protected $name = ''; //队列名称

    protected $module = 'default'; //模块

    private static $instance = null;

    private static $configs = array(); //队列配置

    private $prifix = 'star_queue_';

    private $adapters = array('redis');

    public function __construct()
    {
        
    }

    public static function setConfigs(Array $configs)
    {
        return self::$configs = $configs;
    }

    protected function getQueue()
    {
        $module = $this->module;
        if (!isset(self::$instance->queues[$module]))
        {
            $configs = self::$configs;

            if (!isset($configs[$module]))
            {
                throw new Star_Exception('Can\'t found ' . $module . ' queue module configuration,Please check queue configuration.');
            }

            if (!isset($configs[$module]['adapter']))
            {
                throw new Star_Exception($module . ' queue  module adpater is empty.');
            }
            
            $adapter = $configs[$module]['adapter'];

            if (isset($this->adapters[$adapter]))
            {
                throw new Star_Exception( 'Unsupported ' . $adapter . ' queue adapter.');
            }

            $adapter_class = 'Star_Queue_' . ucfirst($adapter);
            self::$instance->queues[$module] = new $adapter_class($configs[$module]);
        }
        return self::$instance->queues[$module];
    }

    protected function getQueueKey()
    {
        return $this->prifix . $this->name;
    }

    public function add($data)
    {
        $key = $this->getQueueKey();
        return $this->getQueue()->add($key, $data);
    }

    public function consume()
    {
        $key = $this->getQueueKey();
        return $this->getQueue()->consume($key);
    }

    public function length()
    {
        $key = $this->getQueueKey();
        return $this->getQueue()->length($key);
    }

}

?>