<?php
/**
 * @package library\Star\Controller\Front
 */
/**
 * 导入文件
 */

/**
 * controller front类
 * 
 * @package library\Star\Controller\Front
 * @author zhangqinyang
 */
require 'Star/Controller/Action.php';
require 'Star/Controller/Router/Route/Module.php';
class Star_Controller_Front{

    protected $debug = false; //默认关闭
    protected $request; //Star_Http_Request
	protected $response; //Star_Http_Response
    protected $view; //Star_View
    protected $routes = array();
    protected $controller_directory; //controller目录
    protected $has_module = false; //是否配置module
    protected $defualt_module = 'default'; //默认module
    protected $module_controller_directoryectory_name = 'controllers'; //modules controller目录名
    protected $module_name = ''; //module名称
    protected $module_controller_directory = array(); //module controller目录
    protected $default_controller_name = 'index';
    protected $default_action_name = 'index';
    protected $module_key = 'module';
    protected $controller_key = 'Controller';
    protected $action_key = 'Action';
    protected $url_delimiter = '/';
    
    /**
     * 构造函数
     * 
     * @param Star_Http_Request $request
     * @param Star_Http_Response $response
     * @param type $options
     */
	public function __construct(Star_Http_Request $request, Star_Http_Response $response, $options = array())
	{
		$this->request = $request;
        $this->response = $response;
        $this->setOptions($options);
	}
    
    /**
     * 设置options
     * 
     * @param type $options
     * @return \Star_Controller_Front 
     */
    public function setOptions($options = array())
    {
        if (!empty($options))
        {
            $this->options = $options;
            $methods = get_class_methods($this);
            $methods = array_flip($methods);
            foreach ($options as $key => $option)
            {
                $method = 'set' . ucfirst($key);
                if (array_key_exists($method, $methods))
                {
                    $this->$method($option);
                }
            }
        }
        return $this;
    }
    
    /**
     * 设置view
     * 
     * @param Star_View $view
     * @return $this 
     */
    public function setView(Star_View $view)
    {
        $this->view = $view;
        return $this;
    }
    
    /**
     * module是否存在
     * 
     * @param string $module
     * @return bool
     */
    public function isValidModule($module)
    {
        if (empty($module) || !is_string($module))
        {
            return false;
        }
        
        if ($this->getModuleControllerDirectory($module))
        {
            return true;
        } else
        {
            return false;
        }
    }
    
    /**
     * 添加 module controller路径
     * 
     * @param string $directory
     * @param string $module
     * @return type 
     */
    public function addModuleControllerDirectory($directory, $module)
    {
        if ($module === null)
        {
            $module = $this->defualt_module;
        }
        $this->has_module = true;
        
        return $this->module_controller_directory[$module] = $directory;
    }
    
    /**
     * 设置module路径
     * 
     * @param string $path
     * @return \Star_Controller_Front 
     */
    public function setModuleDirectory($path)
    {
        if (is_string($path))
        {
            try{
                $dir = new DirectoryIterator($path);
            } catch(Exception $e) {
                throw new Star_Exception("Directory $path not readable", 500);
            }

            foreach ($dir as $file) 
            {
                if ($file->isDot() || !$file->isDir()) {
                    continue;
                }
                $module    = $file->getFilename();
                $directory = $file->getPathname() . DIRECTORY_SEPARATOR . $this->getModuleControllerDirectoryName();
                $this->addModuleControllerDirectory($directory, $module);
            }
        } else if (is_array($path)){
            foreach ($path as $module => $directory)
            {
                $this->addModuleControllerDirectory($directory, $module);
            }
        }
        return $this;
    }
    
    /**
     * 设置module controller目录名
     * 
     * @param type $name 
     */
    public function setModuleControllerDirectoryName($name = 'controllers')
    {
        $this->module_controller_directoryectory_name = $name;
    }
    
    /**
     * 返回module controller
     * 
     * @return type 
     */
    public function getModuleControllerDirectoryName()
    {
        return $this->module_controller_directoryectory_name;
    }
    
    /**
     * 返回module controller目录
     * 
     * @param type $module
     * @return type 
     */
    public function getModuleControllerDirectory($module=null)
    {
        if ($module == null)
        {
            return $this->module_controller_directory;
        } 
        if (isset($this->module_controller_directory[$module]))
        {
            return $this->module_controller_directory[$module];
        }
        
        return null;
    }

    /**
     * 返回module name
     * 
     * @return type 
     */
    public function getModuleName()
    {
        return $this->module_name;
    }
    
    /**
     * 返回controller key
     * 
     * @return type 
     */
    public function getControllerKey()
    {
        return $this->controller_key;
    }
    
    /**
     * 返回action key
     * 
     * @return type 
     */
    public function getActionKey()
    {
        return $this->action_key;
    }
    
    /**
     * 设置默认module
     * 
     * @param type $module_name
     * @return type
     */
    public function setDefaultModuleName($module_name)
    {
        return $this->defualt_module = $module_name;
    }

    /**
     * 设置默认controller
     * 
     * @param type $controller 
     */
	public function setDefaultControllerName($controller_name = 'index')
	{
        $this->default_controller_name = $controller_name;
        return $this;
	}
	
    /**
     * 设置默认action
     * 
     * @param type $action 
     */
	public function setDefaultActionName($action_name = 'index')
	{
        $this->default_action_name = $action_name;
        
        return $this;
	}
    
    /**
     * 设置module name
     * 
     * @param type $module_name
     * @return \Star_Controller_Front 
     */
    protected function setModuleName($module_name)
    {
        !empty($module_name) && $this->module_name = $module_name;
        return $this;
    }
    
    /**
     * 设置controller key
     * 
     * @param type $controller_key
     * @return \Star_Controller_Front 
     */
    public function setControllerKey($controller_key)
    {
        //如果controller_key不为空，则修改controller_key
        if ($controller_key)
        {
            $this->controller_key = ucfirst(trim($controller_key));
        }
        
        return $this;
    }
    
    /**
     * 设置action key
     * 
     * @param type $action_key
     * @return \Star_Controller_Front 
     */
    public function setActionKey($action_key)
    {
        $action_key && $this->action_key = ucfirst(trim($action_key));
        
        return $this;
    }
    
    /**
     * 消息派遣 调用控制器
     * 
     * @return type 
     */
	public function dispatch()
	{
        header('Cache-Control: no-cache');
		header('Content-Type: text/html; charset=' . $this->view->getEncoding());
		ob_start();

        try{
            $controller_name = $this->request->getControllerName();
            $action_name = $this->request->getActionName();
            $controller_class_name = $this->loadClass($controller_name);
            $view_path = dirname($this->getControllerDirectory()) . DIRECTORY_SEPARATOR . 'views';

            //设置view
            $this->view->setBasePath($view_path)
                       ->setController($controller_name)
                       ->setScriptName($action_name)
                       ->setAction($action_name);
            $action = $action_name . $this->action_key;
            $controller = new $controller_class_name($this->request, $this->response, $this->view);            
            $controller->dispatch($action); //执行action
            call_user_func(array('Star_Model_Abstract', 'Close')); //主动关闭数据库链接
        } catch (Exception $e)
        {
            call_user_func(array('Star_Model_Abstract', 'Close')); //主动关闭数据库链接
            return $this->handleException($e);
        }
        
		ob_end_flush();
	}
    
    /**
     * 加载controller
     * 
     * @param string $controller_name
     * @throws Star_Exception
     */
    public function loadClass($controller_name)
    {
        $controller_class_name = ucfirst($controller_name) . $this->controller_key;
        $file_path = Star_Loader::getFilePath(array($this->getControllerDirectory(), $controller_class_name));

        if (Star_Loader::isExist($file_path) == false)
        {
            throw new Star_Exception("{$file_path} not found!", 404);
        }

        //文件是否可读
        if (!Star_Loader::isReadable($file_path))
        {
            throw new Star_Exception("Connot load controller calss {$controller_class_name} from file {$file_path}", 500);
        }
        
        require $file_path;
        //类是否存在
        if (!class_exists($controller_class_name, false))
        {
            throw new Star_Exception("Invalid controller class ({$controller_class_name})", 404);
        }
        
        return $controller_class_name;
    }
    
    /**
     * 设置controller目录
     * 
     * @param type $path
     * @return \Star_Application 
     */
    public function setControllerDirectory($path)
    {
        $this->controller_directory = $path;
        
        return $this;
    }
    
    /**
     * 返回controller目录
     * 
     * @return type 
     */
    public function getControllerDirectory()
    {
        if ($this->controller_directory == null)
        {
            if ($this->module_name)
            {
                $this->controller_directory = $this->getModuleControllerDirectory($this->module_name);
            } else {
                $directory_name = Star_Loader::getLoadTypeByKey($this->controller_key);
                $this->controller_directory = Star_Loader::getModuleDirect($directory_name);
            }
        }

        return $this->controller_directory;
    }
    
    /**
     * 设置debug状态
     * 
     * @param type $flag
     * @return type 
     */
    protected function setDebug($debug = false)
    {
        return $this->debug = $debug;
    }
    
    /**
     * 返回debug状态
     * 
     * @return type 
     */
    protected function getDebug()
    {
        return $this->debug;
    }
    
    /**
     * 添加路由
     * 
     * @param string $name 路由名称
     * @param Star_Controller_Router_Route_Abstract $route
     * @return \Star_Controller_Front
     */
    public function addRouter($name, Star_Controller_Router_Route_Abstract $route)
    {
        $this->routes[$name] = $route;
        return $this;
    }

    /**
     * 路由
     * 
     * @param type $url 
     */
	public function route()
	{
        $path_info = $this->request->getPathInfo();
        $params = false;
        if (!empty($this->routes))
        {
            foreach ($this->routes as $route)
            {
                if (($params = $route->match($path_info)) !== false)
                {
                    break;
                }
            }
        }
        
        if ($params === false)
        {
            $params = explode($this->url_delimiter, $path_info);
        }
	
        $values = array();

        if ($this->has_module == true)
        {
            if (isset($params[0]) && !empty($params[0]) && $this->isValidModule($params[0]))
            {
                $module_name = array_shift($params);
                $this->module_name = $module_name;
                $values[$this->module_key] = $module_name;
            } elseif ($this->isValidModule($this->defualt_module))
            {
                $this->module_name = $this->defualt_module;
                $values[$this->module_key] = $this->defualt_module;
            }
        }

        if (isset($params[0]) && !empty($params[0]))
        {
            $controller_name = array_shift($params);
            $values[$this->controller_key] = strtolower($controller_name);
        } else {
            $values[$this->controller_key] = $this->default_controller_name;
        }

         if (isset($params[0]) && !empty($params[0]))
        {
            $action_name = array_shift($params);
            $values[$this->action_key] = strtolower($action_name);
        } else{
            $values[$this->action_key] = $this->default_action_name;
        }

        if (!empty($params))
        {
            $count = count($params);
            for ($i =0; $i<$count; $i = $i+2)
            {
                $values[$params[$i]] = isset($params[$i+1]) ? $params[$i+1] : '';
            }
        }
        
        $this->setRequestParam($values);
        return $this;
	}

    /**
     * 设置request params
     * 
     * @param type $params 
     */
    public function setRequestParam($params)
    {
        if (is_array($params))
        {
            foreach ($params as $key => $value)
            {
                switch ($key)
                {
                    case $this->module_key:
                        $this->request->setModuleName($value);
                        break;
                    case $this->controller_key:
                        $this->request->setControllerName($value);
                        break;
                    case $this->action_key:
                        $this->request->setActionName($value);
                        break;
                    default :
                        $this->request->setParam($key, $value);
                        break;
                }
            }
        }
    }

    /**
     * 处理异常
     * 
     * @param type $e
     * @return type 
     */
    public function handleException($e)
    {
        if ($e->getCode() == 404 && $this->request->getControllerName() != 'error')
        {
            $this->view->code = 404;
            $this->request->setControllerName('error')
                          ->setActionName('index');
            return $this->dispatch();
        }

        if ($this->debug == true)
        {
            echo nl2br($e->__toString());
        }else{
            call_user_func(array('Star_Log', 'log'), $e->__toString(), 'error');
        }
        
        if ($e->getCode() == 500)
        {
            $this->view->code = 500;
            $this->request->setControllerName('error')
                          ->setActionName('index');
            return $this->dispatch();
        }
    }
}

?>