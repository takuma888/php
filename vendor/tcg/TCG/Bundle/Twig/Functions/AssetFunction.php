<?php

namespace TCG\Bundle\Twig\Functions;


use TCG\Component\Kernel\Container;

class AssetFunction implements TwigFunctionInterface
{
    public static function getTwigFunction(Container $container = null)
    {
        return new \Twig_SimpleFunction('asset', function($asset_path) use ($container) {
            /** @var \Symfony\Component\HttpFoundation\Request $request */
            $request = $container->tag('request');
            $base_path = $request->getBasePath();
            $domain = $request->getHost();
            $port = $request->getPort();
            $port = ($port == 80 || $port == 443)  ? '' : ':' . $port;
            return '//' . $domain . $port . $base_path .'/' . ltrim($asset_path, '/');
        });
    }
}