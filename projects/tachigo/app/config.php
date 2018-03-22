<?php
define('START_MICRO_TIME', microtime(true));
error_reporting(E_ALL);
ini_set('display_errors', 'on');
// cookie跨域
header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');

if (!function_exists('pre')) {
    /**
     * @param $var
     * @param bool|true $exit
     */
    function pre($var, $exit = true)
    {
        if (is_array($var) || is_object($var)) {
            $output = print_r($var, true);
        } else {
            $output = var_export($var, true);
        }

        if (PHP_SAPI == 'cli') {
            echo $output;
        } else {
            echo '<pre>', $output, '</pre>';
        }

        if ($exit) {
            exit();
        }
    }
}
if (!function_exists('tick')) {
    /**
     * @param $var
     * @throws Exception
     */
    function tick($var)
    {
        if (is_array($var) || is_object($var)) {
            $output = print_r($var, true);
        } else {
            $output = var_export($var, true);
        }
        echo $output, "\n";
    }
}


!defined('ENV') && define('ENV', 'develop');
define('ROOT', realpath(__DIR__ . '/..'));
define('APP_ROOT', ROOT . '/app');
define('LOG_ROOT', ROOT . '/log');
define('SRC_ROOT', ROOT . '/src');
define('CACHE_ROOT', ROOT . '/cache');
define('VENDOR_ROOT', realpath(ROOT . '/../../vendor'));



define('START_TIME', time()); // 定义当前时间戳
define('TIME_ZONE', 'Asia/Shanghai'); // 定义当前时区
date_default_timezone_set(TIME_ZONE); // 设置市区
define('START_DATE', date('Y-m-d', START_TIME)); // 定义当前日期（年-月-日）
define('START_DATETIME', date('Y-m-d H:i:s', START_TIME)); // 定义当前日期时间（年-月-日 时:分:秒）

