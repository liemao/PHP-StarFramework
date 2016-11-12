<?php
/**
 * @package library\Star\Http
 */

/**
 * http抽象类
 * 
 * @package library\Star\Http
 * @author zhangqinyang
 *
 */
abstract class Star_Http_Abstract
{
    protected $module_name = '';

    protected $controller_name = '';
	
	protected $action_name = '';

    /**
     * 构造函数
     */
    public function __construct()
	{
		
	}
    
    /**
     * 返回模块名称
     * 
     * @return stirng
     */
    public function getModuleName()
    {
        return $this->module_name;
    }
    
    /**
     * 返回控制器名称
     * 
     * @return string
     */
    public function getControllerName()
    {
        return $this->controller_name;
    }
    
    /**
     * 返回action名称
     * 
     * @return string
     */
    public function getActionName()
    {
        return $this->action_name;
    }

    /**
     * 设置模块名
     * 
     * @param string $module
     * @return \Star_Http_Abstract
     */
    public function setModuleName($module)
    {
        $this->module_name = $module;
        return $this;
    }
    
    /**
     * 设置当前controller
     * 
     * @param string $controller_name
     * @return \Star_Http_Abstract
     */
	public function setControllerName($controller_name)
	{
		!empty($controller_name) && $this->controller_name = $controller_name;
        return $this;
	}
	
    /**
     * 设置当前action
     * 
     * @param string $action_name
     * @return \Star_Http_Abstract
     */
	public function setActionName($action_name)
	{
		!empty($action_name) && $this->action_name = $action_name;
        return $this;
	}
}

?>