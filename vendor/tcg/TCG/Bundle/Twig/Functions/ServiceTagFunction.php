<?php

namespace TCG\Bundle\Twig\Functions;


use TCG\Component\Kernel\Container;

class ServiceTagFunction implements TwigFunctionInterface
{
    public static function getTwigFunction(Container $container = null)
    {
        return new \Twig_SimpleFunction('tag', function($tag) use ($container) {
            return $container->tag($tag);
        });
    }
}