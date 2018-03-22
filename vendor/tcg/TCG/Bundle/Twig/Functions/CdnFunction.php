<?php

namespace TCG\Bundle\Twig\Functions;


use TCG\Component\Kernel\Container;

class CdnFunction implements TwigFunctionInterface
{
    public static function getTwigFunction(Container $container = null)
    {
        return new \Twig_SimpleFunction('cdn', function($asset_path) use ($container) {
            $cdn_url = $container->getParameter('cdn_url');
            if ($cdn_url) {
                $cdn_version_suffix = $container->getParameter('cdn_version_suffix');
                if ($cdn_version_suffix) {
                    return $cdn_url . '/' . ltrim($asset_path, '/') . (strpos($asset_path, '?') === false ? '?' : '&') . 'v=' . $cdn_version_suffix;
                } else {
                    return $cdn_url . '/' . ltrim($asset_path, '/');
                }
            } else {
                /** @var \Symfony\Component\HttpFoundation\Request $request */
                $request = $container->tag('request');
                $base_path = $request->getBasePath();
                $domain = $request->getHost();
                $port = $request->getPort();
                $port = ($port == 80 || $port == 443)  ? '' : ':' . $port;
                $cdn_version_suffix = $container->getParameter('cdn_version_suffix');
                if ($cdn_version_suffix) {
                    return '//' . $domain . $port . $base_path .'/' . ltrim($asset_path, '/') . (strpos($asset_path, '?') === false ? '?' : '&') . 'v=' . $cdn_version_suffix;
                } else {
                    return '//' . $domain . $port . $base_path .'/' . ltrim($asset_path, '/');
                }
            }
        });
    }
}