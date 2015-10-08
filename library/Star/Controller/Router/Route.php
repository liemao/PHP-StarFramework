<?php

/*
 * @package library\Star\Controller\Router\Route
 */

/**
 * Description of Star_Controller_Router_Route
 *
 * @author zhangqinyang
 */
class Star_Controller_Router_Route extends Star_Controller_Router_Route_Abstract
{
    public function __construct($route, $defaults = array(), $maps = array()) 
    {
        parent::__construct($route, $defaults, $maps);
    }

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
