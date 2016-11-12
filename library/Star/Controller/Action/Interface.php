<?php
/**
 * @package library\Star\Controller\Action
 */

/**
 * 控制器 接口
 *
 * @package library\Star\Controller\Action
 * @author zhangqinyang
 *
 */
interface Star_Controller_Action_Interface {
	
    /**
     * 构造函数
     * 
     * @param Star_Http_Request $request
     * @param Star_Http_Response $response
     * @param Star_View $view
     */
	public function __construct(Star_Http_Request $request, Star_Http_Response $response, Star_View $view);
	
    /**
     * 设置View
     * 
     * @param Star_View $view
     */
	public function setView(Star_View $view);
	
    /**
     * 消息分发
     * 
     * @param string $action
     */
    public function dispatch($action);
}

?>