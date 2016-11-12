<?php

/*
 * @package library\Star\Controller\Router\Route\Regex
 */

/**
 * Star_Controller_Router_Route_Regex 路由正则匹配
 *
 * @author zhangqinyang
 */
class Star_Controller_Router_Route_Regex extends Star_Controller_Router_Route_Abstract
{
    
    /**
     * 匹配
     * 
     * @param type $path
     * @param type $partial
     * @return boolean
     */
    public function match($path, $partial = false)
    {
        if ($partial == true)
        {
            $regex = '#^' . $this->_route . '#';
        } else{
            $regex = '#^' . $this->_route . '$#';
        }
        
        if (preg_match($regex, $path, $matches))
        {
            unset($matches[0]);
            return $this->getParams($matches);
        } else {
            return false;
        }
    }
}

?>
