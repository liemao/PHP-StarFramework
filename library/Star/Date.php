<?php
/**
 * @package library\Star
 */

/**
 *
 * Date类
 * 
 * @package library\Star
 * @author zhangqinyang
 *
 */
class Star_Date {
	
    public static function time()
    {
        return $_SERVER['REQUEST_TIME'];
    }
    
    public static function date($time = '')
    {
        return date('Y-m-d', $time > 0 ? $time : $_SERVER['REQUEST_TIME']);
    }
    
    /**
     * 日期转换为时间戳
     * 
     * @param type $date
     * @param type $is_begin
     * @return type 
     */
    public static function dateToTime($date, $is_begin = true)
    {
        list($year, $month, $day) = explode('-', $date);
        return $is_begin == true ? mktime(0, 0, 0, $month, $day, $year) : mktime(23, 59, 59, (int) $month, (int) $day, (int) $year);
    }
    
    /**
     * 时间戳转换为日期
     * @param type $time
     * @return type 
     */
    public static function timeToDate($time='')
    {
        return date('Y-m-d H:i:s', $time ? $time : time());
    }
    
    /**
     * 返回当前第几周
     * 
     * @param type $time
     * @return type 
     */
    public static function getWeek($time='')
    {
        return date('W', $time ? $time : time()); 
    }
    
    /**
     * 返回年周
     * 
     * @param type $time
     * @return type 
     */
    public static function getYearWeek($time='')
    {
        return date('YW', $time ? $time : time());
    }
    
    /**
     * 返回年月
     * 
     * @param type $time
     * @return type 
     */
    public static function getYearMonth($time= '')
    {
        return date('Ym', $time ? $time : time());
    }
    
    /**
     * 返年月日
     * 
     * @param type $time
     * @return type 
     */
    public static function getDate($time='')
    {
        return date('Ymd', $time ? $time : time());
    }
    
    /**
     * 返回上周起始时间戳
     * 
     * @param type $is_begin
     * @return type 
     */
    public static function getLastWeek($is_begin = true)
    {
        $now_time = time();
        $week = date('w', $now_time); //星期几
        list($year, $month, $day) = explode('-', date('Y-m-d', $now_time - ($week + 7 - 1) * 86400));
        $time = mktime(0, 0, 0, $month, $day, $year);
        if ($is_begin == false)
        {
            $time += 7 * 86400 - 1;
        }
        return $time;
    }
    
    /**
     * 返回上月起始时间戳
     * 
     * @param type $is_begin
     * @return type 
     */
    public static function getLastMonth($is_begin = true)
    {
        $now_time = time();
        list($year, $month) = explode('-', date('Y-m', ($now_time - ((date('d', $now_time) + 1) * 86400)))); //返回上个月年月

        if ($is_begin == true)
        {
            $time = mktime(0, 0, 0, $month, 1, $year);
        } else {
            $days = cal_days_in_month(CAL_GREGORIAN, $month, $year); //上个月总共天数
            $time = mktime(23, 59, 59, $month, $days, $year);
        }
        
        return $time;
    }
    
    /**
     * 返回昨天起始时间戳
     * 
     * @param type $is_begin
     * @return type 
     */
    public static function getLastDay($is_begin = true)
    {
        list($year, $month , $day) = explode('-', date('Y-m-d', (time() - 86400)));
        
        if ($is_begin == true)
        {
            $time = mktime(0, 0, 0, $month, $day, $year);
        } else {
            $time = mktime(23, 59, 59, $month, $day, $year);
        }
        
        return $time;
    }
    
    /**
     * 毫秒
     * 
     * @return int
     */
    public static function getMillisecond() 
    {
		list($s1, $s2) = explode(' ', microtime());
		return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
	}


    /**
     * 返回当月开始时间
     * 
     * @return type 
     */
    public static function getThisMonth()
    {
        list($year, $month, $day) = explode('-', date('Y-m-d', time()));
        return mktime(0, 0, 0, $month, 1, $year);
    }
    
    /**
     * 返回当天开始时间
     * 
     * @return type 
     */
    public static function getToday()
    {
        list($year, $month, $day) = explode('-', date('Y-m-d', time()));
        return mktime(0, 0, 0, $month, $day, $year);
    }
    
    /**
     * 秒转换为之前时间
     * 
     * @param int second 
     */
    public static function agoTime($second)
    {
        $ago_time = '';
        if ($second == 0)
        {
            $ago_time = '刚刚';
        }else if ($second < 60){
            $ago_time = $second . '秒前';
        }else if ($second < 3600)
        {
            $ago_time = floor($second / 60) . '分钟前';
        } else if ($second < 86400)
        {
            $ago_time = floor($second/3600) . '小时' . intval(($second%3600)/60) . '分钟前';
        } else if ($second < 86400 * 30){
            $ago_time = floor($second/86400) . '天前';
        } else if ($second < 86400*365){
            $ago_time = floor($second/(86400*30)) . '月前';
        } else{
            $ago_time = floor($second/(86400*365)) . '年前';
        }
        return $ago_time;
    }
    
    /**
     * 判断是否时间格式
     * 
     * @param type $date
     * @return boolean 
     */
    public static function isDate($date)
    {
        if (empty($date))
        {
            return false;
        }
        return preg_match('/^[0-9]{4}(\-|\/)[0-9]{1,2}(\\1)[0-9]{1,2}(|\s+[0-9]{1,2}(:[0-9]{1,2}){0,2})$/', $date);
    }
}

?>