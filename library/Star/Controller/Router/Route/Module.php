<?php

/*
 * @package library\Star\Controller\Router\Route\Module
 */

/**
 * Star_Controller_Router_Route_Module 默认路由
 *
 * @author zhangqinyang
 */

class Star_Controller_Router_Route_Module extends Star_Controller_Router_Route_Abstract
{
    
    public function __construct() 
    {
        
    }
    
    public function match($path)
    {
        $params = array();
        if (!empty($path))
        {
            $values = explode(self::URL_DELIMITER, $path);
            $params = $this->getParams($values);
        }
        return $params;
    }
}

?>
