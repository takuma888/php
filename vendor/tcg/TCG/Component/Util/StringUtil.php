<?php

namespace TCG\Component\Util;

class StringUtil
{

    /**
     * 小写下划线模式
     * @param $string
     * @return string
     */
    public static function underscore($string)
    {
        return strtolower(preg_replace(array('/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'), array('\\1_\\2', '\\1_\\2'), strtr($string, '_', '.')));
    }

    /**
     * 大小写驼峰模式，首字母大写
     * @param $string
     * @return string
     */
    public static function camelcase($string)
    {
        return strtr(ucwords(strtr($string, array('_' => ' ', '.' => '_ ', '\\' => '_ '))), array(' ' => ''));
    }
}