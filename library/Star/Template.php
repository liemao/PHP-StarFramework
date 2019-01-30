<?PHP
/**
 * @package library\Star
 */

/**
 *
 * Template 模板引擎
 *
 * @package library\Star
 * @author zhangqinyang
 *
 */
class Star_Template {

    private $compile_cache_dir = 'compile_cache'; //编译缓存目录
    private $template_start_tag = '{{';
    private $template_end_tag = "}}";
    private $expr_tag = '$$$';
    private $str_tag = '@@@';
    private $view;

    protected $expr_keys = array( //表达式关键字，不被处理。关键字不可用于变量打印
        'as',
        'true',
        'false',
    );

    protected $ignore_func = array( //常用变量命，且函数模板不经常使用，忽视掉
        'key',
        'log',
    );

    protected $expr_arr = array(
        'if',
        'elseif',
        'for',
        'foreach',
    );

    public function __construct($options = array()) {
        
    }

    public function setView(Star_View $view)
    {
        $this->view = $view;
        return $this;
    }

    /**
     * 规范标签内容，去除两边空白
     *
     * @param type $content
     * @return $this
     */
    private function standardTagContent(&$content)
    {
        $content = preg_replace("/" . $this->getCompleteTag("\s*(.*?)\s*") . "/", $this->getCompleteTag("$1"), $content);
        return $this;
    }

    /**
     * 替换结束标签
     *
     * @param type $content
     * @return $this
     */
    private function replaceEndTag(&$content)
    {
        $content = preg_replace('/'. $this->getCompleteTag('\/(\w+)') . '/', $this->getPHPContent('}'), $content);
        return $this;
    }

    /**
     * 替换关键字
     *
     * @param type $content
     * @return $this
     */
    private function replaceKeyword(&$content)
    {
        $content = preg_replace('/'. $this->getCompleteTag('@([a-zA-Z]+)') . '/', $this->getPHPContent('$1'), $content);
        return $this;
    }

    /**
     * 替换else标签
     *
     * @param type $content
     * @return $this
     */
    private function replaceElseTag(&$content)
    {
        $content = str_replace($this->getCompleteTag('else'), $this->getPHPContent('} else {'), $content);
        return $this;
    }

    /**
     * 替换普通变量
     *
     * @param type $content
     * @return $this
     */
    private function replaceVar(&$content)
    {
        $content = preg_replace("/".$this->getCompleteTag('([A-Za-z_][A-Za-z\d_]*)')."/", $this->getPHPContent('$$1', true), $content);
        return $this;
    }

    /**
     * 替换view传值变量
     *
     * @param type $content
     * @return $this
     */
    private function replaceViewVar(&$content)
    {
        $content = preg_replace("/".$this->getCompleteTag('\$([A-Za-z_][A-Za-z\d_]*)')."/", $this->getPHPContent('$this->$1', true), $content);
        return $this;
    }

    /**
     * 解析标签
     *
     * @param type $content
     * @return $this
     */
    private function parseTags(&$content)
    {
        preg_match_all("/" . $this->getCompleteTag('(.*?)') . "/", $content, $match_tags);

        if ($match_tags)
        {
            $tags = array_unique($match_tags[1]);
            foreach ($tags as $tag)
            {
               
                $this->replaceTag($content, $tag);
            }
        }
        return $this;
    }

    /**
     * 替换标签内容
     *
     * @param type $content
     * @param type $tag
     * @return $this
     */
    private function replaceTag(&$content, $tag)
    {
        preg_match_all('/[\'\"](.*?)[\'\"]/', $tag, $str_arr); //匹配字符串
        $tag_replace = preg_replace('/[\'\"](.*?)[\'\"]/', $this->str_tag, $tag);
        preg_match_all('/([$]?[a-zA-Z\d_\.]+)/', $tag_replace, $match_arr); //匹配标签内容
        $tag_replace = preg_replace('/([\'\"$]?[a-zA-Z\d_\.]+[\'\"]?)/', $this->expr_tag, $tag_replace);
        $expr = '';

        if ($match_arr)
        {
            $match_arr = $match_arr[0];
            if (in_array(strtolower($match_arr[0]), $this->expr_arr)) //判断是否为表达式，是则优先替换表达式
            {
                $expr = array_shift($match_arr);
                $expr = strtolower($expr);
                $expr_position = strpos($tag_replace, $this->expr_tag);
                $tag_replace = substr_replace($tag_replace, ($expr == 'elseif' ? '} ' . $expr : $expr) . ' ( ', $expr_position, 3);
            }

            foreach ($match_arr as &$val)
            {
                if (in_array(strtolower($val), $this->expr_keys) //判断是否模板关键字，关键字不被处理
                   || in_array(substr($val, 0, 1), array('"', "'")) //判断是否字符串, 首字符引号为字符串， 字符串不被处理
                   || is_numeric($val)) //数字不被处理
                {
                    $val = $val;
                } else {
                    if (strpos($val, '.')) //判断是否数组变量
                    {
                        $val = $this->disposeArrayVar($val);
                    } elseif (substr($val, 0, 1) == '$')
                    {
                        $val = str_replace('$', '$this->', $val);
                    } else {
                        $val = (!in_array($val, $this->ignore_func) && function_exists($val)) ? $val : '$' . $val;
                    }
                }
                $val_position = strpos($tag_replace, $this->expr_tag);
                $tag_replace = substr_replace($tag_replace, $val, $val_position, 3);
                
            }
        }

        if ($str_arr)
        {
            foreach ($str_arr[0] as $string)
            {
                $str_position = strpos($tag_replace, $this->str_tag);
                $tag_replace = substr_replace($tag_replace, $string, $str_position, 3);

            }
        }

        if ($expr) //表达式则末尾添加)
        {
            $tag_replace .= ' ) {';
        }

        $content = str_replace($this->getCompleteTag($tag), $this->getPHPContent($tag_replace, empty($expr) ? true : false), $content);
        return $this;
    }

    /**
     * 处理数组变量
     *
     * @param type $tag
     * @return string
     */
    private function disposeArrayVar($tag)
    {
        $array_keys = explode('.', $tag);
        $array_name = array_shift($array_keys);
        if ($array_keys)
        {
            array_walk($array_keys, function(&$value) {
                $value =  "['".$value."']";
            });
        }

        $repalce_content = (substr($array_name, 0, 1) == '$' ? '$this->' . substr($array_name, 1) : '$' . $array_name) . implode('', $array_keys);
        return $repalce_content;
    }

    /**
     * 替换数组变量
     * 
     * @param type $content
     * @param type $tag
     * @return $this
     */
    private function replaceArrayVar(&$content, $tag)
    {
        $repalce_content = $this->disposeArrayVar($tag);
        $content = str_replace($this->getCompleteTag($tag), $this->getPHPContent($repalce_content), $content);
        return $this;
    }

    /**
     * 解析模板
     *
     * @param type $content
     */
    public function parseTemplate()
    {
        //调试模式则不读取缓存
        if (Star_Config::get('resources.debug') == true || $this->cacheIsExists() == false)
        {
            $content = file_get_contents($this->view->getViewPath());
            $this->standardTagContent($content)
                 ->replaceKeyword($content)
                 ->replaceElseTag($content)
                 ->replaceEndTag($content)
                 ->replaceVar($content)
                 ->replaceViewVar($content)
                 ->parseTags($content);
            $this->saveCache($content);
        }
        $template_compile_path = $this->getCachePath();
        return $template_compile_path;
    }

    public function parseWidget($widget_name)
    {
        //调试模式则不读取缓存
        if (Star_Config::get('resources.debug') == true || $this->widgetCacheIsExists($widget_name) == false)
        {
            $content = file_get_contents($this->view->getWidgetPath($widget_name));
            $this->standardTagContent($content)
                 ->replaceKeyword($content)
                 ->replaceElseTag($content)
                 ->replaceEndTag($content)
                 ->replaceVar($content)
                 ->replaceViewVar($content)
                 ->parseTags($content);
            $this->saveWidgetCache($widget_name, $content);
        }
        $widget_compile_path = $this->getWidgetCachePath($widget_name);
        return $widget_compile_path;
    }

    /**
     * 返回编译缓存路径
     *
     * @return type
     */
    private function getCachePath()
    {
        $segments = array(
            $this->view->getCachePath(),
            $this->view->getCacheDirectory(),
            $this->compile_cache_dir,
            $this->view->getTheme(),
        );

        if ($this->view->getIsController() == true)
        {
            array_push($segments, $this->view->getController());
        }

        array_push($segments, $this->view->getScriptName());
        $path = Star_Loader::getFilePath($segments, '.phtml');
        return $path;
    }

    /**
     * 返回编译挂件缓存路径
     *
     * @return type
     */
    private function getWidgetCachePath($widget_name)
    {
        $segments = array(
            $this->view->getCachePath(),
            $this->view->getCacheDirectory(),
            $this->compile_cache_dir,
            $this->view->getWidgetDir(),
            $widget_name
        );

        $path = Star_Loader::getFilePath($segments, '.phtml');
        return $path;
    }

    /**
     * 编译缓存是否存在
     *
     * @return boolean
     */
    private function widgetCacheIsExists($widget_name)
    {
        if (file_exists($this->getWidgetCachePath($widget_name)))
        {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 存储编译缓存
     *
     * @param type $content
     * @throws Star_Exception
     */
    private function saveWidgetCache($widget_name, $content)
    {
        $cache_path = $this->getWidgetCachePath($widget_name);

        if (!is_dir(dirname($cache_path)))
        {
            mkdir(dirname($cache_path), 0777, true);
        }

        if (false === ($handle = @fopen($cache_path, 'w')))
        {
            throw new Star_Exception("Connt open $cache_path for writing");
        }

        fwrite($handle, $content);
        fclose($handle);
    }

    /**
     * 编译缓存是否存在
     *
     * @return boolean
     */
    private function cacheIsExists()
    {
        if (file_exists($this->getCachePath()))
        {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 存储编译缓存
     *
     * @param type $content
     * @throws Star_Exception
     */
    private function saveCache($content)
    {
        $cache_path = $this->getCachePath();
        
        if (!is_dir(dirname($cache_path)))
        {
            mkdir(dirname($cache_path), 0777, true);
        }

        if (false === ($handle = @fopen($cache_path, 'w')))
        {
            throw new Star_Exception("Connt open $cache_path for writing");
        }

        fwrite($handle, $content);
        fclose($handle);
    }

    /**
     * 返回PHP内容
     *
     * @param type $content
     * @param type $is_echo
     * @return type
     */
    private function getPHPContent($content, $is_echo =false)
    {
        $content = preg_replace('/\s+/', ' ', $content);
        return "<?PHP " . ($is_echo == true ? 'echo ' : '') . $content . ($is_echo == true ? ';' : '') . " ?>";
    }

    /**
     * 返回完整标签内容
     *
     * @param type $content
     * @return type
     */
    private function getCompleteTag($content)
    {
        return "{$this->template_start_tag}{$content}{$this->template_end_tag}";
    }
}

?>