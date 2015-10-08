<?php

/*
 * @package library\Star\Controller\Router\Route\Static
 */

/**
 * Star_Controller_Router_Route_Static 静态路由
 *
 * @author zhangqinyang
 */
class Star_Controller_Router_Route_Static extends Star_Controller_Router_Route_Abstract
{
    /**
     * 匹配
     * 
     * @param type $path
     * @param type $partial
     * @return type
     */
    public function match($path, $partial = false)
    {
        $params = false;
        
        if ($partial == true && !empty($this->_route) && (substr($path, 0, strlen($this->_route)) === $this->_route))
        {
            $params = $this->getParams();
        } else if ($path == $this->_route)
        {
            $params = $this->getParams();
        }
        
        return $params;
    }
}

?>
