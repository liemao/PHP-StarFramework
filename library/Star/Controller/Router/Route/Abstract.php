<?php
/**
 * @package library\Star\Controller\Router\Route\Abstract
 */

/**
 * route Abstract
 * 
 * @package library\Star\Controller\Router\Route\Abstract
 * @author zhangqinyang
 */
require 'Star/Controller/Router/Route/Interface.php';
abstract class Star_Controller_Router_Route_Abstract implements Star_Controller_Router_Route_Interface
{
    CONST URL_DELIMITER = '/';
    
    protected $_route = null;
    
    protected $_defaults = array();
    
    protected $_maps = array();
    
    /**
     * 构造函数
     * 
     * @param type $route
     * @param type $defaults
     * @param type $maps
     */
    public function __construct($route, $defaults = array(), $maps = array()) 
    {
        $this->_route = $route;
        $this->_defaults = $defaults;
        $this->_maps = $maps;
    }

    /**
     * 返回路由参数
     * 
     * @param type $values
     * @return type
     */
    public function getParams($values = array())
    {
        $params = array();
        
        if (isset($this->_defaults['module']))
        {
            $params[] = $this->_defaults['module'];
        }
        
        if (isset($this->_defaults['controller']))
        {
            $params[] = $this->_defaults['controller'];
        }
        
        if (isset($this->_defaults['action']))
        {
            $params[] = $this->_defaults['action'];
        }
        
        if (!empty($values))
        {
            foreach ($values as $key => $value)
            {
                if (!empty($this->_maps) && isset($this->_maps[$key]))
                {
                    $params[] = $this->_maps[$key];
                    $params[] = $value;
                } else {
                    $params[] = $value;
                }
            }
        }
        
        return $params;
    }

}