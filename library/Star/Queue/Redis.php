<?php
/**
 * @package library\Star\Queue
 */

/**
 * Redis 队列类
 * 
 * @package library\Star\Queue
 * @author zhangqinyang
 *
 */
class Star_Queue_Redis implements Star_Queue_Interface {

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

    public function add($key, $data) {
        return $this->redis->rPush($key, $data);
    }

    public function consume($key) {
        return $this->redis->lPop($key);
    }

    public function length($key) {
        return $this->redis->lSize($key);
    }
	
}

?>