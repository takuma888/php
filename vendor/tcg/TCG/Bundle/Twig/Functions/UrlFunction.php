<?php

namespace TCG\Bundle\Twig\Functions;

use Symfony\Component\Routing\Generator\UrlGenerator;
use TCG\Component\Kernel\Container;

class UrlFunction implements TwigFunctionInterface
{
    public static function getTwigFunction(Container $container = null)
    {
        return new \Twig_SimpleFunction('url', function ($name, $params = null) use ($container) {
            if (is_array($name)) {
                $name = $name[0];
                if (isset($name[1])) {
                    $params = $name[1];
                }
            }
            $params = (array) $params;
            if (!$params) {
                $params = array();
            }
            /** @var UrlGenerator $url_generator */
            $url_generator = $container->tag('url_generator');
            return $url_generator->generate($name, $params, UrlGenerator::ABSOLUTE_URL);
        });
    }
}