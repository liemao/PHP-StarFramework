<?php
/**
 * @package library\Star\Queue
 */

/**
 * 队列  接口
 * @package library\Star\Queue
 * @author zhangqinyang
 *
 */
interface Star_Queue_Interface {

    /**
     * 添加队列
     *
     * @param type $key
     * @param type $data
     */
    public function add($key, $data);

    /**
     * 消费队列
     *
     * @param type $key
     */
    public function consume($key);

    /**
     * 队列长度
     *
     * @param type $key
     */
    public function length($key);
}

?>