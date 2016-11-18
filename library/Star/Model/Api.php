<?php
/**
 * @package library\Star\Model
 */

/**
 * API MODEL
 * 
 * @package library\Star\Model
 * @author zhangqinyang  2013/08/26
 * 
 */
Class Star_Model_Api
{    
    protected $server_name = '';
    protected $timeout = 3000;
	protected static $config = array();

    public function __construct($options = array()) {
        
        if ($options)
        {
            $this->setOptions(($options));
        } 
    }
    
    /**
     * 配置API
     * 
     * @param type $options 
     */
    public function setOptions($options)
    {
        if (is_array($options))
        {
            $methods = get_class_methods(__CLASS__);
            foreach ($options as $key => $option)
            {
                $method = 'set' . ucfirst($key);
                if (in_array($method, $methods))
                {
                    $this->$method($option);
                }
            }
        }
    }
    
    /**
     * api返回数据
     * 
     * @param type $script_name
     * @param type $params
     * @param type $method
     * @param type $cookie
     * @param type $protocol
     * @param int $timeout
     * @return type 
     */
    public function api($script_name, $params = '', $method = 'get', $cookie = '', $protocol = 'http', $timeout = 0, $headers = array())
    {
        $query_string = $this->getQueryString($params);
        $cookie_string = $this->getCookieString($cookie);
        return $this->httpQuery($script_name, $query_string, $method, $cookie_string, $protocol, $timeout, $headers);
    }
	
	/**
	 * POST JSON
	 * 
	 * @param type $script_name
	 * @param type $params
	 * @param type $cookie
	 * @param type $protocol
	 * @param type $timeout
	 * @return type
	 */
	protected function apiJson($script_name, $params = '', $cookie = '', $protocol = 'http', $timeout = 0, $headers = array())
	{
		$query_string = is_array($params) ? json_encode($params) : $params;
		$cookie_string = $this->getCookieString($cookie);
		array_push($headers, 'Content-Type: application/json');
		return $this->httpQuery($script_name, $query_string, 'post', $cookie_string, $protocol, $timeout, $headers);
	}

	private function httpQuery($script_name, $query_string, $method, $cookie_string, $protocol, $timeout, $headers = array())
	{
		if (strcmp($protocol . "://", substr($script_name, 0, strlen($protocol . "://"))) !== 0)
        {
            $url = $protocol . "://" . $this->getServerName() . $script_name;
        } else {
            $url = $script_name;
        }
        
        $ch = curl_init();
	    if ('GET' == strtoupper($method))
	    {
		    curl_setopt($ch, CURLOPT_URL, "$url?$query_string");
	    }
	    else 
        {
		    curl_setopt($ch, CURLOPT_URL, $url);
		    curl_setopt($ch, CURLOPT_POST, 1);
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
	    }
        
	    curl_setopt($ch, CURLOPT_HEADER, false);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    //curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout == 0 ? $this->timeout : $timeout);

        // disable 100-continue
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));

		if (!empty($headers))
		{
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}
		
	    if (!empty($cookie_string))
	    {
	    	curl_setopt($ch, CURLOPT_COOKIE, $cookie_string);
	    }
	    
	    if ('https' == $protocol)
	    {
	    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	    }
	
	    $rs = curl_exec($ch);
	    $err = curl_error($ch);

	    if (false === $rs || !empty($err))
	    {
		    $errno = curl_errno($ch);
		    $info = curl_getinfo($ch);
		    curl_close($ch);
            
	        $message = array(
	        	'errno' => $errno,
	            'msg' => $err,
	        	'info' => $info,
	        );
            
            $stact_trace = Star_Debug::Trace(); //返回堆栈详细信息
            $stact_trace = implode("\n", $stact_trace);
            Star_Log::log($url . "?" . $query_string . "\n" . json_encode($message) . "\n" . $stact_trace, 'query_error');
            
            return ;
	    }
	    
       	curl_close($ch);
        return json_decode($rs, true);
	}

	/**
     * 返回请求参数
     * 
     * @param type $params
     * @return type 
     */
    protected function getQueryString($params)
    {
        if (is_array($params))
        {
            return http_build_query($params);
        }

		return $params;
    }
    
    /**
     * 返回cookie参数
     * 
     * @param $params
     * @return type 
     */
    protected function getCookieString($params)
    {
        if (is_array($params))
        {
            return http_build_cookie($params);
        }
		return $params;
    }
    
    /**
     * 设置config
     * 
     * @param array $config
     */
    public static function setting($config)
    {
        self::$config = $config;
    }

    /**
     * 设置server_name
     * 
     * @param type $server_name 
     */
    public function setServerName($server_name)
    {
        $this->server_name = $server_name;
    }
    
    /**
     * 返回server_name
     * 
     * @return type 
     */
    public function getServerName()
    {
        $config = self::$config;
        if (empty($this->server_name) && isset($config['server_name']))
        {
            $this->server_name = $config['server_name'];
        }

        return $this->server_name;
    }
}

?>