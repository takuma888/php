<?php

namespace TCG\Bundle\Twig\Functions;


class DumpFunction implements TwigFunctionInterface
{
    public static function getTwigFunction()
    {
        return new \Twig_SimpleFunction('dump', function ($var) {
            if (is_array($var) || is_object($var)) {
                $output = print_r($var, true);
            } else {
                $output = var_export($var, true);
            }
            if (PHP_SAPI == 'cli') {
                return $output;
            } else {
                return '<pre>' . $output . '</pre>';
            }
        });
    }
}