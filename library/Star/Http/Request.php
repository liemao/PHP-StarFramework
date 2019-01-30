<?php
/**
 * @package library\Star\Http
 */

/**
 * 导入文件
 */
require 'Star/Http/Abstract.php';

/**
 * request 类
 * 
 * @package library\Star\Http
 * @author zhangqinyang
 *
 */
class Star_Http_Request extends Star_Http_Abstract
{
    protected $params = array();
    protected $path_info = '';
    public static $trace_id = '';

    /**
     * 构造方法
     */
    public function __construct()
	{
        //添加支持application/json请求数据
        if ($this->getMethod() == 'POST' && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false)
        {
            $request_body = @file_get_contents('php://input');
            $_POST = json_decode($request_body, true);
        }

		$this->params = array_merge($_GET, $_POST);
        $this->initTraceId();
	}

    protected function initTraceId()
    {
        $client_ip = self::getIp();
        $user_agent = self::getUserAgent();
        $micotime = microtime();
        $trace_id = substr(md5($client_ip . $user_agent . $micotime), 0, 6);
        self::$trace_id = $trace_id . '-' . $client_ip;
    }


    /**
     * 是否是POST请求
     * 
     * @return boolean 
     */
	public function isPost()
	{
		if ('POST' == $this->getMethod())
		{
			return true;
		}
		
		return false;
	}
	
	/**
	 * 获取request的访问路径
     * 
	 * @return string
	 */
	public function getPathInfo()
	{
        if ($this->path_info)
        {
            return $this->path_info;
        }
        
		$path = '';

		if (isset($_SERVER['REDIRECT_URL']) && $_SERVER['REDIRECT_URL']) //是否重定向
		{
			$path = $_SERVER['REDIRECT_URL'];
            if ($_SERVER['DOCUMENT_ROOT'] != dirname($_SERVER['SCRIPT_FILENAME'])) 
            {
                $path = str_replace(str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_FILENAME']) , '', $path); //去除目录路径
            }
            $path = trim($path, '\\/');
		} else if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'])
		{
            $url_info = parse_url(ltrim($_SERVER['REQUEST_URI'], '\\/'));
            
            if (!isset($url_info['path']))
            {
                $url_info['path'] = '';
            }
            
            if ($_SERVER['PHP_SELF'] == $url_info['path'])
            {
                $path = str_replace($_SERVER['SCRIPT_NAME'], '', $url_info['path']);
            } else {
                $file_path = ltrim(str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_FILENAME']), '\\/');
                $path = str_replace($file_path, '', $url_info['path']); //去除目录路径
            }
            $path = trim($path, '\\/');
		}
        $this->path_info = $path;
		return $this->path_info;
		
	}
    
   /**
     * 判断是否是缓存数据
     * @return boolean
     */
    public static function isCache()
    {
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && time() < strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']))
        {
            return true;
        }
        return false;
    }
    
	/**
     * 是否是GET请求
     * 
     * @return boolean 
     */
	public function isGet()
	{
		if ('GET' == $this->getMethod())
		{
			return true;
		}
		
		return false;
	}
	
    /**
     * 是否是ajax请求， 只针对jQuery框架有效
     * 
     * @return boolean 
     */
	public function isAjax()
	{
		if ((isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
            || stripos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
		{
			return true;
		}
		
		return false;
	}

    public function isWeixin()
    {
        if (strpos($_SERVER["HTTP_USER_AGENT"],"MicroMessenger"))
        {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 返回所有请求参数
     * 
     * @return type 
     */
	public function getParams()
	{
		return $this->params;
	}
	
    /**
     * 通过key返回参数
     * 
     * @param  $key
     * @param  $default 默认值
     * @return type 
     */
	public function getParam($key, $default = '')
	{
		return !empty($this->params[$key]) ? $this->params[$key] : $default;
	}
	
    /**
     * 通过key设置参数
     * 
     * @param type $key
     * @param type $value 
     */
	public function setParam($key, $value)
	{
		$this->params[$key] = $value;
	}
	
    /**
     * 获取请求方法
     * 
     * @return type 
     */
	protected function getMethod()
	{
		return isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
	}
	
    /**
     * 返回请求HOST
     * 
     * @return type 
     */
	public static function getHost()
	{
		return isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
	}

    /**
     *
     */
    public function getTraceId()
    {
        return self::$trace_id;
    }
    
    /**
     * 返回用户访问IP 
     */
    public static function getIp()
    {
        $realip = '';
        if (isset($_SERVER)){
            if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
                $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
                $realip = $_SERVER["HTTP_CLIENT_IP"];
            } else if (isset ($_SERVER["REMOTE_ADDR"])){
                $realip = $_SERVER["REMOTE_ADDR"];
            }
        } else {
            if (getenv("HTTP_X_FORWARDED_FOR")){
                $realip = getenv("HTTP_X_FORWARDED_FOR");
            } else if (getenv("HTTP_CLIENT_IP")) {
                $realip = getenv("HTTP_CLIENT_IP");
            } else {
                $realip = getenv("REMOTE_ADDR");
            }
        }
        return $realip;
    }
    
    /**
     * 返回用户浏览器信息
     * 
     * @return type 
     */
    public static function getUserAgent()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    }
}

?>
