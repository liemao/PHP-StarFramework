<?php
/**
 * @package library\Star
 */

/**
 * star layout
 * 
 * @package library\Star
 * @author zhangqinyang
 *
 */
class Star_Layout {

	protected $enabled = false; //layout开启状态
	
	protected $content_key = 'content'; //view 打印layout key内容
		
	protected $layout = 'layout';

	protected $postfix = 'phtml'; //layout后缀，默认为phtml
	
	protected $_view = null; //Star_view类
	
	protected $layout_base_path = null;
	
	protected $layout_script_path = 'scripts';
	
	protected $view = null;
	
	protected static $mvc_instance = null;
    
    public $content = null;
	
    /**
     * 构造函数
     * 
     * @param type $options
     */
	public function __construct($options = array())
	{
		if (!empty($options))
		{
			$this->setOptions($options);
		}
		
		$this->enableLayout();
	}
	
	/**
	 * 启动layout
	 * 
	 * @param array $options
	 */
	public static function startMvc(array $options)
	{
		if (self::$mvc_instance == null)
		{
			self::$mvc_instance = new self($options);
		}

		return self::$mvc_instance;
	}
	
    /**
     * 返回布局实例
     * 
     * @return type
     */
	public static function getMvcInstance()
	{
		return self::$mvc_instance;
	}
	
    /**
     * 开启layout
     * 
     * @return \Star_Layout 
     */
	public function enableLayout()
	{
		$this->enabled = true;
		return $this;
	}
	
    /**
     * 关闭layout
     * 
     * @return \Star_Layout 
     */
	public function disableLayout()
	{
		$this->enabled = false;
		return $this;
	}
	
    /**
     * 是否开启layout
     * 
     * @return type 
     */
	public function isEnabled()
	{
		return $this->enabled;
	}
	
	public function getView()
	{
		return $this->view;
	}
	
	/**
	 * 设置layout参数
	 * 
	 * @param unknown_type $options
	 */
	public function setOptions($options)
	{
		if (isset($options['script_path']) && !empty($options['script_path']))
		{
			$this->layout_script_path = $options['script_path'];
		}
		
		if (isset($options['base_path']) && !empty($options['base_path']))
		{
			$this->layout_base_path = $options['base_path'];
		}
        
        if (isset($options['layout']) && !empty($options['layout']))
        {
            $this->layout = $options['layout'];
        }
	}
	
    /**
     * 设置脚本路径
     * 
     * @param type $script_path
     */
	public function setScriptPath($script_path)
	{
		$this->layout_script_path = $script_path;
	}
	
    /**
     * 设置基础路径
     * 
     * @param type $base_path
     */
	public function setBasePath($base_path)
	{
		$this->layout_base_path = $base_path;
	}
	
    /**
     * 设置layout
     * 
     * @param type $layout
     */
	public function setLayout($layout)
	{
		$this->layout = $layout;
	}
	
    /**
     * 返回layout
     * 
     * @return type
     */
	public function getLayout()
	{
		return $this->layout;
	}
	
    /**
     * 返回基础路径
     * 
     * @return type
     */
	public function getBasePath()
	{
		return $this->layout_base_path;
	}
	
    /**
     * 返回脚本路径
     * 
     * @return type
     */
	public function getScriptPath()
	{
		return $this->layout_script_path;
	}
	
    /**
     * 设置view
     * 
     * @param Star_View $view
     * @return \Star_Layout
     */
	public function setView(Star_View $view)
	{
		$this->view = $view;
		$this->view->setLayout($this);
		return $this;
	}
	
    /**
     * render
     * 
     * @throws Star_Exception
     */
	public function render()
	{
        if ($this->view instanceof Star_View)
        {
            $this->view->setBasePath($this->layout_base_path);
            $this->view->setTheme($this->layout_script_path);
            $this->view->setScriptName($this->layout, false);
            $this->view->setRender()->loadView();
            
        } else
        {
            throw new Star_Exception("Star_layout view is not object.");
        }
	}
	
    /**
     * 变量赋值
     * 
     * @param type $specs
     * @param type $value
     * @return \Star_Layout
     */
	public function assign($specs, $value = null)
	{
		if (is_string($specs))
		{
			$this->$specs = $value;
		}
		
		if (is_array($specs))
		{
			foreach ($specs as $key => $value)
			{
				$this->$key = $value;
			}
		}
		
		return $this;
	}
	
    /**
     *  返回内容key
     * 
     * @return type
     */
	public function getContentKey()
	{
		return $this->content_key;
	}
	
    /**
     * 设置内容key
     * 
     * @param type $content_key
     * @return \Star_Layout
     */
	public function setContentKey($content_key)
	{
		if (!empty($content_key))
		{
			$this->content_key = $content_key;
		}
		
		return $this;
	}
}

?>
