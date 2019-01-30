<?php
/**
 * @package library\Star\View
 */

/**
 * Start_view
 * 
 * @package library\Star\View
 * @author zhangqinyang
 *
 */
abstract class Star_View_Abstract {
	
	protected $_script_name = '';
    protected $_cache_path = '';
    protected $_base_name = '';
	protected $_is_controller = true;
	protected $_is_display = true; //是否显示view
    protected $_module;
    protected $_controller;
    protected $_action;
    protected $_postfix = '.phtml'; //后缀
	protected $_theme_name = 'scripts';
    protected $_widget_name = 'widgets';
	protected $layout = '';
	protected $default_view = 'views';
    protected $is_template = false; //是否启动模板引擎，默认关闭模板引擎
    protected $encoding = 'UTF-8'; //默认编码
    protected $static_options = array(); //静态资源配置
    protected $js_options = array(); //js配置
    protected $css_options = array(); //css配置
    protected $is_cache = false; //页面是否缓存
    protected $timeout = 600; //默认缓存时间
    protected $cache_directory = 'caches'; //缓存目录
    protected $cache_name = 'index'; //缓存名
    protected $is_flush = false; //是否强制刷新缓存
    protected $assign = array();

    /**
     * 构造方法
     * @param array $options
     */
    public function __construct($options = array())
	{	
		$this->setOption($options);
		$this->run();
	}
	
	abstract protected function setOption(Array $options);
	
	protected function run()
	{

	}

    protected function setTemplate($is_template)
    {
        return $this->is_template = $is_template;
    }

    /**
     * 返回缓存文件存储路径
     *
     * @return type
     */
    public function getCachePath()
    {
        if (!$this->_cache_path) //缓存目录路径，防止开启layout修改base_name
        {
            $this->_cache_path = dirname($this->_base_name);
        }
        
        return $this->_cache_path;
    }

    /**
     * 设置缓存文件存储路径
     *
     * @param type $cache_path
     * @return type
     */
    protected function setCachePath($cache_path)
    {
        return $this->_cache_path = $cache_path;
    }

    /**
	 * 设置控制器名
     * 
	 * @param string $script_name
	 * @param bool $is_controller
	 * @return Star_View_Abstract
	 */
	public function setScriptName($script_name, $is_controller = true)
	{
		$this->_is_controller = $is_controller;
		$this->_script_name = $script_name;
		return $this;
	}
	
	/**
	 * 是否加载view
     * 
	 * @param bool $is_display
	 */
    protected function setDisplay($is_display)
    {
        if ($is_display == true)
        {
            $this->setRender();
        } else
        {
            $this->setNoRender();
        }
    }
    
    /**
     * 设置不加载view
     * 
     * @return Star_View_Abstract
     */
	public function setNoRender()
	{
		$this->_is_display = false;
		return $this;
	}
    
	/**
	 * 设置加载view
     * 
	 * @return Star_View_Abstract
	 */
    public function setRender()
    {
        $this->_is_display = true;
        return $this;
    }
	
    /**
     * 设置基础路径
     * 
     * @param unknown $base_path
     * @return Star_View_Abstract
     */
	public function setBasePath($base_path)
	{
		$this->_base_name = $base_path;
		return $this;
	}

    /**
     * 设置view文件后缀
     *
     * @param type $postfix
     * @return $this
     */
    protected function setPostfix($postfix)
    {
        $this->_postfix = $postfix;
        return $this;
    }

    /**
     * 返回view是否添加conroller目录
     *
     * @return type
     */
    public function getIsController()
    {
        return $this->_is_controller;
    }

    /**
     * 设置view路径是否默认添加controller
     *
     * @param type $bool
     * @return $this
     */
    protected function setIsController($bool)
    {
        $this->_is_controller = (bool) $bool;
        return $this;
    }


    /**
	 * 获取基础路径
	 * @return string
	 */
	public function getBasePath()
	{
		return $this->_base_name;
	}
	
	/**
	 * 获取控制器文件名
	 * @return string
	 */
	public function getScriptName()
	{
		return $this->_script_name;
	}

    /**
     * 返回module
     *
     * @return type
     */
    public function getModule()
    {
        return $this->_module;
    }

    /**
     * 设置modules
     *
     * @param type $module
     * @return $this
     */
    public function setModule($module)
    {
        $this->_module = $module;
        return $this;
    }

    /**
     * 返回controller
     * 
     * @return type
     */
    public function getController()
    {
        return $this->_controller;
    }

    /**
	 * 设置控制器
	 * @param string $controller
	 * @return Star_View_Abstract
	 */
	public function setController($controller)
	{
		$this->_controller = $controller;
		return $this;
	}

    /**
     * 返回action
     *
     * @return type
     */
    public function getAction()
    {
        return $this->_action;
    }
    
	/**
	 * 设置控制器中具体某方法
     * 
	 * @param tring $action
	 * @return Star_View_Abstract
	 */
    public function setAction($action)
    {
        $this->_action = $action;
        return $this;
    }
	
    /**
     * 获取视图路径
     * 
     * @return Ambigous <type, string>
     */
	public function getViewPath()
	{
        $view_segments = array($this->_base_name, $this->_theme_name);
		if ($this->_is_controller == true)
		{           
            array_push($view_segments, $this->_controller);
		}
        
        array_push($view_segments, $this->_script_name);
        $view_path = Star_Loader::getFilePath($view_segments, $this->_postfix);
		return $view_path;
	}

    /**
     * 设置网站编码
     * 
     * @param type $encoding
     * @return \Star_View_Abstract 
     */
	public function setEncoding($encoding)
	{
		$this->encoding = $encoding;
		return $this;
	}
	
    /**
     * 返回网站编码
     * 
     * @return type 
     */
	public function getEncoding()
	{
		return $this->encoding;
	}
	
	/**
	 * 加载视图
     * 
	 * @throws Star_Exception
	 */
	public function loadView()
	{
		if ($this->_is_display == false)
		{
			return;
		}
		
		$view_path = $this->getViewPath();

		if (!is_file($view_path))
		{
			throw new Star_Exception( $view_path . ' not found', 500);

			exit;
		}

        //是否启动模板引擎
        if ($this->is_template == true)
        {
            $template = new Star_Template();
            $template_complite_path = $template->setView($this)
                     ->parseTemplate();
            include $template_complite_path;
        } else {
            $view_path = realpath($view_path);
            include $view_path;
        }
	}
	
	/**
	 * 设置视图主题
     * 
	 * @param unknown $theme
	 * @return Star_View_Abstract
	 */
	public function setTheme($theme)
	{
		$this->_theme_name = $theme;
		return $this;
	}
	
	/**
	 * 获取视图主题
     * 
	 * @return string
	 */
	public function getTheme()
	{
		return $this->_theme_name;
	}
	
	/**
	 * 获取layout文件名
     * 
	 * @return string
	 */
	public function layout()
	{
		return $this->layout;
	}
	
	/**
	 * 设置layout
     * 
	 * @param Star_Layout $star_layout
	 * @return Star_View_Abstract
	 */
	public function setLayout(Star_Layout $star_layout)
	{
		$this->layout = $star_layout;
		return $this;
	}
	
	/**
	 * 保存变量至视图变量
     * 
	 * @param unknown $key
	 * @param string $value
	 * @return Star_View_Abstract
	 */
	public function assign($key, $value = null)
	{
		if (is_array($key))
        {
            foreach ($key as $k => $val)
            {
                $this->assign[$k] = $val;
            }
        } else
        {
            $this->assign[$key] = $value;
        }
        
        return $this;
	}
    
    /**
     * 设置静态资源基础路径
     * 
     * @param type $path
     * @return \Star_View_Abstract 
     */
    public function setStaticBasePath($path)
    {
        $this->static_options['basePath'] = $path;
        return $this;
    }
    
    /**
     * 设置JS基础路径
     * 
     * @param type $path
     * @return type 
     */
    public function setJsBasePath($path)
    {
        $this->js_options['basePath'] = $path;
        return $this;
    }
    
    /**
     * 设置css基础路径
     * 
     * @param type $path
     * @return \Star_View_Abstract 
     */
    public function setCssBasePath($path)
    {
        $this->css_options['basePath'] = $path;
        return $this;
    }
    
    /**
     * 设置静态资源版本号
     * 
     * @param type $version
     * @return \Star_View_Abstract 
     */
    public function setStaticVersion($version)
    {
        $this->static_options['version'] = $version;
        return $this;
    }

    /**
     * 设置js版本号
     * 
     * @param type $version
     * @return \Star_View_Abstract 
     */
    public function setJsVersion($version)
    {
        $this->js_options['version'] = $version;
        return $this;
    }
    
    /**
     * 设置css样式版本号
     * 
     * @param type $version
     * @return \Star_View_Abstract 
     */
    public function setCssVersion($version)
    {
        $this->css_options['version'] = $version;
        return $this;
    }
    
    /**
     * 设置js加载文件
     * 
     * @param type $files 
     */
    public function setJsFiles($files)
    {
        if (isset($this->js_options['files']))
        {
            $this->js_options['files'] = array_merge((array) $this->js_options['files'], (array) $files);
        } else{
            $this->js_options['files'] = (array) $files;
        }
        return $this;
    }
    
    /**
     * 设置css加载文件
     * 
     * @param type $files
     * @return \Star_View_Abstract 
     */
    public function setCssFiles($files)
    {
        if (isset($this->css_options['files']))
        {
            $this->css_options['files'] = array_merge((array) $this->css_options['files'], (array) $files);
        } else{
            $this->css_options['files'] = (array) $files;
        }
        return $this;
    }
    
    /**
     * 返回静态资源基础路径
     * 
     * @return type 
     */
    public function getStaticBasePath()
    {
        return isset($this->static_options['basePath']) ? $this->static_options['basePath'] : "";
    }
    
    /**
     * 返回静态资源版本号
     * 
     * @return type 
     */
    public function getStaticVersion()
    {
        return isset($this->static_options['version']) ? $this->static_options['version'] : "";
    }


    /**
     * 返回JS版本号
     * 
     * @return type 
     */
    public function getJsVersion()
    {
        return isset($this->js_options['version']) && $this->js_options['version'] ? $this->js_options['version'] : (isset($this->static_options['version']) ? $this->static_options['version'] : "");
    }
    
    /**
     * 返回css版本号
     * 
     * @return type 
     */
    public function getCssVersion()
    {
        return isset($this->css_options['version']) && $this->css_options['version'] ? $this->css_options['version'] : (isset($this->static_options['version']) ?  $this->static_options['version'] : "");
    }
    
    /**
     * 设置css配置文件
     * 
     * @param type $options
     * @return \Star_View_Abstract 
     */
    public function setCssConfig($options)
    {
        //设置基础路径
        if (isset($options['basePath']) && !empty($options['basePath']))
        {
            $this->setCssBasePath($options['basePath']);
        }

        //添加加载css文件
        if (isset($options['files']) && !empty($options['files']))
        {
            $this->setCssFiles($options['files']);
        }
        
        //设置css版本号
        if (isset($options['version']) && !empty($options['version']))
        {
            $this->setCssVersion($options['version']);
        }
        
        return $this;
    }
    
    public function setStaticConfig($options)
    {
        //设置静态资源基础路径
        if (isset($options['basePath']) && !empty($options['basePath']))
        {
            $this->setStaticBasePath($options['basePath']);
        }
        
        //设置静态资源版本号
        if (isset($options['version']) && !empty($options['version']))
        {
            $this->setStaticVersion($options['version']);
        }
    }

    /**
     * 添加js加载配置
     * 
     * @param type $options 
     */
    public function setJsConfig($options)
    {   
        //设置基础路径
        if (isset($options['basePath']) && !empty($options['basePath']))
        {
            $this->setJsBasePath($options['basePath']);
        }

        //添加加载js文件
        if (isset($options['files']) && !empty($options['files']))
        {
            $this->setJsFiles($options['files']);
        }
        
        //设置js版本号
        if (isset($options['version']) && !empty($options['version']))
        {
            $this->setJsVersion($options['version']);
        }
        
        return $this;
    }
    
    /**
     * 加载js文件
     * 
     * @return type 
     */
    public function loadJs()
    {
        $js_html = '';
        
        $base_path = $this->getJsBasePath();
        
        $version = $this->getJsVersion();

        if (isset($this->js_options['files']) && !empty($this->js_options['files']))
        {
            foreach ((array) $this->js_options['files'] as $file_name)
            {
                $file_path = Star_Loader::getFilePath(array($base_path, $version, $file_name), '.js', '/');
                
                $js_html .= "<script type='text/javascript' src='{$file_path}'></script>";
            }
        }

        return $js_html;
    }
    
    /**
     * 读取页面CSS HTML体
     * 
     * @return string
     */
    public function loadCss()
    {
        $css_html = '';
        $base_path = $this->getCssBasePath();
        $version = $this->getCssVersion();
        if (isset($this->css_options['files']) && !empty($this->css_options['files'])) 
        {
            foreach ((array) $this->css_options['files'] as $file_name)
            {
                $file_path = Star_Loader::getFilePath(array($base_path, $version, $file_name), '.css', '/');
                $css_html .= "<link rel='stylesheet' type='text/css' href='{$file_path}' />";
            }
        }
        
        return $css_html;
    }
    
    /**
     * 返回js基础路径
     * 
     * @return type 
     */
    public function getJsBasePath()
    {
        return isset($this->js_options['basePath']) && $this->js_options['basePath'] ? $this->js_options['basePath'] : (isset($this->static_options['basePath']) ? $this->static_options['basePath'] : "");
    }
    
    /**
     * 返回CSS基础路径
     * @return multitype:
     */
    public function getCssBasePath()
    {
        return isset($this->css_options['basePath']) && $this->css_options['basePath'] ? $this->css_options['basePath'] : (isset($this->static_options['basePath']) ? $this->static_options['basePath'] : "");
    }
    
    /**
     * 设置缓存超时时间
     * 
     * @param unknown $timeout
     * @return Star_View_Abstract
     */
    public function setCacheTimeout($timeout)
    {
        $this->timeout = (int) $timeout;
        return $this;
    }

    public function getCacheDirectory()
    {
        return $this->cache_directory;
    }

    /**
     * 设置缓存路径
     * 
     * @param type $directory
     * @return \Star_View_Abstract 
     */
    public function setCacheDirectory($directory)
    {
        !empty($directory) && $this->cache_directory = $directory;
        return $this;
    }
    
    /**
     * 是否缓存
     * 
     * @return type 
     */
    public function isCache()
    {
        return $this->is_cache;
    }
    
    /**
     * 判断缓存是否过期
     * 
     * @return boolean
     */
    public function cacheIsExpire()
    {
        $cache_path = $this->getCacheFileName();
        
        if (!file_exists($cache_path) || (time() - filemtime($cache_path) >= $this->timeout) || $this->is_flush == true)
        {
            return true;
        } else
        {
            return false;
        }
    }

    /**
     * 读取缓存信息
     * 
     * @param type $cache_path
     * @return boolean
     * @throws Star_Exception 
     */
    public function loadCache()
    {
        $cache_path = $this->getCacheFileName(); //缓存文件

        //判断文件是否存在
        if (!is_file($cache_path))
        {
            return false;
        }
        
        //文件是否可以
        if (!is_readable($cache_path))
        {
            throw new Star_Exception("Connt open $cache_path for read");
        }

        //缓存是否超时
        if ($this->cacheIsExpire() == true)
        {
            return false;
        }
        
        include $cache_path; //载入缓存文件
    }
    
    /**
     * 清空缓存
     * 
     * @return boolean
     */
    public function flushCache()
    {
        return $this->is_flush = true;
    }


    /**
     * 保存缓存内容
     * 
     * @param unknown $body
     * @throws Star_Exception
     */
    public function saveCache($body)
    {
        $file_name = $this->getCacheFileName();
        
        if (!is_dir(dirname($file_name)))
        {
            mkdir(dirname($file_name), 0777, true);
        }
        
        if (false === ($handle = @fopen($file_name, 'w')))
        {
            throw new Star_Exception("Connt open $file_name for writing");
        }

        fwrite($handle, $body);
        fclose($handle);
    }

    /**
     * 开启页面缓存
     * 
     * @param string $cache_name
     * @param number $timeout
     * @param string $is_flush
     */
    public function openCache($cache_name = 'index', $timeout = 0, $is_flush = false)
    {
        if ($timeout > 0)
        {
            $this->setCacheTimeout($timeout);
        }
        
        if (!empty($cache_name))
        {
            $this->setCacheName($cache_name);
        }
        
        if ($is_flush == true)
        {
            $this->flushCache();
        }
        
        $this->is_cache = true;
        Star_Http_Response::setBrownerCache($this->timeout);
    }
    
    /**
     * 设置缓存名称
     * 
     * @param unknown $cache_name
     */
    public function setCacheName($cache_name)
    {
        $this->cache_name = $cache_name;
    }
    
    /**
     * 返回文件路径
     * 
     * @return Ambigous <type, string>
     */
    public function getCacheFileName()
    {
        if (!$this->_cache_path) //缓存目录路径，防止开启layout修改base_name
        {
            $this->_cache_path = dirname($this->_base_name);
        }

        $segments = array(
            $this->_cache_path,
            $this->cache_directory,
            $this->_module,
            $this->_controller,
            $this->_action,
            $this->cache_name
        );
        
        $path = Star_Loader::getFilePath($segments, '.html');
        return $path;
    }
    
    /**
     * 异步返回， 提前返回数据给前端，继续执行后面逻辑 
     */
    public function anynReturn()
    {
        if (function_exists('fastcgi_finish_request'))
        {
            ob_end_flush();
            fastcgi_finish_request();
            ob_start();
        }
    }
    
    public function __set($name, $value) 
    {
        $this->assign[$name] = $value;
    }
    
    public function __get($name) 
    {
        return isset($this->assign[$name]) ? $this->assign[$name] : '';
    }

    /**
     * 加载挂件
     * 
     * @param $file_name
     * @return type
     */
    public function loadWidget($file_name)
    {

        $view_path = $this->getWidgetPath($file_name);
        if (!is_file($view_path))
        {
            throw new Star_Exception( $view_path . ' not found', 500);
            exit;
        }

        if ($this->is_template == true)
        {
            $template = new Star_Template();
            $template_complite_path = $template->setView($this)
                     ->parseWidget($file_name);
            include $template_complite_path;
        } else {
            $view_path = realpath($view_path);
            include $view_path;
        }
    }

    public function getWidgetDir()
    {
        return $this->_widget_name;
    }

    /**
     * 获取挂件绝对地址
     * 
     * @param $file_name
     * @return type
     */
    public function getWidgetPath($file_name)
    {
        $view_segments = array($this->_base_name, $this->_widget_name,$file_name);
        $view_path = Star_Loader::getFilePath($view_segments, $this->_postfix);
        return $view_path;
    }

}

?>