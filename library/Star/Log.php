<?php
/**
 * @package library\Star
 */

/**
 *
 * Log操作类
 *
 * @package library\Star
 * @author zhangqinyang
 *
 */
class Star_Log {
	
    protected static $directory_name = 'logs';
	
	protected static $log_type = 'log';

	private static $model = 'a+';

    protected static $file_path = '';

    public function __construct() {
        
    }
    
    /**
     * 写日志
     * 
     * @param type $message 
     */
    public static function log($message, $type ='')
    {
        $file_name = (empty($type) ? self::$log_type : $type) . "_" . Star_Date::getDate();
        $message = Star_Date::timeToDate().":  {$message}\n";

        if (self::$file_path)
        {
            if(!is_dir(self::$file_path))
            {
                mkdir(self::$file_path, 0775, true);
            }
            $file_path = Star_Loader::getFilePath(array(self::$file_path,  $file_name), '.txt');
        } else
        {
            $directory = Star_Loader::getModuleDirect(self::$directory_name);
            if (!is_dir($directory))
            {
                mkdir($directory, 0775, true);
            }
            
            $file_path = Star_Loader::getFilePath(array($directory,  $file_name), '.txt');
        }

        $handle = fopen($file_path, self::$model, false);
        fwrite($handle, $message);
        fclose($handle);
    }
    
    /**
     * 设置log文件目录
     * 
     * @param type $directory_name 
     */
    public static function setLogDirectoryName($directory_name)
    {
        self::$log_directory = $directory_name;
    }
    
    /**
     * 设置log文件路径
     * @param type $file_path 
     */
    public static function setLogFilePath($file_path)
    {
        self::$file_path = $file_path;
    }
	
	/**
	 * 设置日志类型
	 * 
	 * @param type $type
	 */
	public static function setLogType($type)
	{
		self::$log_type = $type;
	}
}

?>