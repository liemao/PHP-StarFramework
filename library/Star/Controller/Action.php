<?php
/**
 * @package library\Star\Controller
 */
/**
 * 导入文件
 */
require 'Star/Controller/Action/Interface.php';

/**
 * controller 基础类
 * 
 * @package library\Star\Controller
 * @author zhangqinyang
 */
class Star_Controller_Action implements Star_Controller_Action_Interface{

    protected $is_terminate = false; //是否终止 init终止则不执行action
    protected $request;
	protected $response;
	public $view;
	public $layout;
    protected $front_controller;
    protected static $_message_script = 'message';
    protected static $_warning_script = 'warning';

    /**
     * 构造函数
     * 
     * @param Star_Http_Request $request
     * @param Star_Http_Response $response
     * @param Star_View $view
     */
    final public function __construct(Star_Http_Request $request, Star_Http_Response $response, Star_View $view)
	{
		$this->setRequest($request)
             ->setResponse($response)
             ->setView($view);
        $star_layout = Star_Layout::getMvcInstance();
        if ($star_layout instanceof Star_Layout)
        {
            $this->initLayout($star_layout);
        }
		$this->init();
	}
	
    /**
     * 初始化
     */
	public function init()
	{
		
	}
	
    /**
     * 设置REQUEST
     * 
     * @param Star_Http_Request $request
     * @return \Star_Controller_Action 
     */
	protected function setRequest(Star_Http_Request $request)
	{
		$this->request = $request;
		return $this;
	}
	
    /**
     * 设置RESPONSE
     * 
     * @param Star_Http_Response $response
     * @return \Star_Controller_Action 
     */
    protected function setResponse(Star_Http_Response $response)
    {
        $this->response = $response;
        return $this;
    }

    /**
	 * 魔法函数call
     * 
	 * @param string $method_name
	 * @param array $args
	 * @throws Star_Exception
	 */
	public function __call($method_name, $args)
	{
		if (substr($method_name, -6) == 'Action')
		{
			$action = substr($method_name, 0, strlen($method_name) - 6);
			throw new Star_Exception(__CLASS__ . '::' . $action . 'Action not exists', 404);
		}
		throw new Star_Exception(__CLASS__ . '::' . $method_name . ' Method not exists', 500);
	}
	
    /**
     * 执行
     */
	public function run()
	{

	}
	
    /**
     * 初始化view
     * 
     * @param Star_View $star_view
     * @return type 
     */
	public function setView(Star_View $view)
	{
		$this->view = $view;
		return $this;
	}
    
    /**
     * 设置FrontController
     * 
     * @param Star_Controller_Front $front_contorller
     * @return \Star_Controller_Action
     */
    public function setFrontController(Star_Controller_Front $front_contorller)
    {
        $this->front_controller = $front_contorller;
        return $this;
    }


    /**
     * 执行request action 请求
     * 
     * @param string $action
     */
    public function dispatch($action) 
    {
        if ($this->is_terminate == true)
        {
            return ;
        }

        if (method_exists($this, $action))
        {
            $this->$action();
            $this->view->loadView();
            
            //开启layout 加载layout
            if ($this->layout instanceof Star_Layout && $this->layout->isEnabled() == true)
            {
                $body = ob_get_contents();
                ob_clean();
                $this->setLayout($body);
            }

            //判断是否需要更新缓存
            if ($this->view->isCache() == true && $this->view->cacheIsExpire() == true)
            {
                $this->saveViewCache(); //存储页面缓存
            }
        } else {
            $this->__call($action, array());
        }

    }
    
    /**
     * 保存页面缓存 
     */
    private function saveViewCache()
    {
        //开启缓存，且缓存超时或者缓存不存在，则写入缓存
        if ($this->view->isCache() == true && $this->view->cacheIsExpire() == true)
        {
            $this->view->saveCache(ob_get_contents());
        }
        
        return $this->view->isCache();
    }
    
    
    /**
     * 设置layout
     * 
     * @param type $star_view
     * @param type $star_layout
     * @param type $body 
     */
	private function setLayout($body)
	{
		$this->layout->setView($this->view);
		$this->layout->assign($this->layout->getContentKey(), $body);
		$this->layout->render();
	}
	
    /**
     * 初始化 layout
     * 
     * @param Star_Layout $star_layout
     * @return \Star_Controller_Action 
     */
	public function initLayout(Star_Layout $star_layout)
	{
		$this->layout = $star_layout;
		return $this;
	}
	
    /**
     * 关闭layout
     * 
     * @return \Star_Controller_Action 
     */
	protected function disableLayout()
	{
		if ($this->layout instanceof Star_Layout)
		{
			$this->layout->disableLayout();
		}
        
        return $this;
	}
	
    /**
     * 重新指定显示页面
     * 
     * @param type $action
     * @param type $is_controller
     * @return \Star_Controller_Action 
     */
	protected function render($action, $is_controller = true)
	{
        
		$this->view->setScriptName($action, $is_controller)->setRender();
        
        return $this;
	}
	
    /**
     * 页面重定向
     * 
     * @param type $url
     * @return type 
     */
	protected function redirect($url)
	{
		header('Location:' . $url);
		return ;
	}

    //init终止，不继续执行action
    protected function terminateAction()
    {
        return $this->is_terminate = true;
    }
	
    /**
     * 显示提示信息
     * 
     * @return type 
     */
	protected function showMessage()
	{
        $this->terminateAction();
		$args = func_get_args();
		$this->view->message = $args[0];
        $this->view->url = $args[1];
		return $this->render(self::$_message_script, false);
	}

    /**
     * 显示警告信息
     * 
     * @return type 
     */
	protected function showWarning()
	{
        $this->terminateAction();
		$args = func_get_args();
		$this->view->message = isset($args[0]) ? $args[0] : '';
        $this->view->url = isset($args[1]) ? $args[1] : '';
		return $this->render(self::$_warning_script, false);
	}
	
    /**
     * 显示json数据
     * 
     * @return type 
     */
	protected function showJson()
	{   
		$args = func_get_args();
        if (is_array($args[0])) {
            $response = $args[0];
        } else {
            $status = isset($args[0]) ? $args[0] : 200;
            $message = '';
            $data = array();

            if ($status !== 200)
            {
                $message = isset($args[1]) ? $args[1] : $message;
                $data = isset($args[2]) ? $args[2] : $data;
            } else {
                $data = isset($args[1]) ? $args[1] : $data;
                $message = isset($args[2]) ? $args[2] : $message;
            }

            $response = array(
                'status' => $status,
                'message' => $message,
                'data' => $data,
            );
        }

        $this->terminateAction();
        $this->disableLayout();
        $this->view->setNoRender();
        
        if (isset($_GET['callback']) && !empty($_GET['callback']))
        {
            echo htmlspecialchars($_GET['callback']) . '(' . json_encode($response) . ')';
        } else{
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
	}
    
    /**
     * 显示404
     * 
     * @throws Star_Exception
     */
    protected function show404()
    {
        $this->terminateAction();
        throw new Star_Exception('404 not found.', 404);
    }
    
    /**
	 * 设置不加载view
	 */
    protected function setNoRender()
    {
        $this->view->setNoRender();
        $this->disableLayout();
    }
    
    /**
     * 获取请求信息
     * @return Star_Http_Request
     */
    protected function getRequest()
    {
        return $this->request;
    }
    
    /**
     * 开启页面缓存 
     */
    protected function openCache($cache_key = '', $timeout = 0, $is_flush = false)
    {
        empty($cache_key) && $cache_key = $this->getRequest()->getActionName();
        $this->view->openCache($cache_key, $timeout, $is_flush);
    }
    
    /**
     * 是否有页面缓存
     * 
     * @return type 
     */
    protected function hasCache()
    {
        return $this->view->cacheIsExpire() == true ? false : true;
    }

    /**
     * 显示页面缓存 
     */
    protected function showCache()
    {
        $this->setNoRender();
        $this->view->loadCache();
    }
    
    /**
     * 强制刷新页面缓存 
     */
    protected function flushCache()
    {
        $this->view->flushCache();
    }
    
    /**
     * 设置消息script
     * 
     * @param string $script 
     */
    public static function setMessageScript($script)
    {
        self::$_message_script = $script;
    }
    
    /**
     * 设置警告script
     * 
     * @param string $script 
     */
    public static function setWarningScript($script)
    {
        self::$_warning_script = $script;
    }
}

?>
