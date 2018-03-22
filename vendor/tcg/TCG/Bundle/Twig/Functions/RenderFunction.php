<?php

namespace TCG\Bundle\Twig\Functions;

use TCG\Bundle\Twig\Component\TwigHttpExec;
use TCG\Component\Kernel\Container;

class RenderFunction implements TwigFunctionInterface
{
    public static function getTwigFunction(Container $container = null)
    {
        return new \Twig_SimpleFunction('render', function($route_name_or_controller, array $arguments = array()) use ($container) {
            $kernel = $container->getKernel();
            /** @var \Symfony\Component\HttpFoundation\Request $request */
            $request = $container->tag('request');
            if (strpos($route_name_or_controller, ':') === false) {
                /** @var \Symfony\Component\Routing\Matcher\UrlMatcher $url_matcher */
                $url_matcher = $container->tag('url_matcher');
                $routing_info = $url_matcher->match($request->getPathInfo());
                if (!isset($routing_info['_controller'])) {
                    throw new \Exception('路由 ' . $routing_info['_route'] . ' 没有配置 defaults 的 _controller 选项');
                }
                $controller = $routing_info['_controller'];
                unset($routing_info['_controller']);
                $arguments = array_merge($routing_info, $arguments);
            } else {
                $controller = $route_name_or_controller;
            }
            /** @var \Symfony\Component\HttpFoundation\Request $request */
            list($module, $controller, $action) = explode(':', $controller);
            $module = trim($module, '@');
            $module = $kernel->getModule($module);
            /** @var TwigHttpExec $exec */
            $exec = $module->getExec($controller, $action);
            $exec->setRequest($request);
            if (method_exists($exec, '__invoke')) {
                $rcm = new \ReflectionMethod($exec, '__invoke');
                $params = $rcm->getParameters();
                $args = [];
                foreach ($params as $param) {
                    $arg = isset($arguments[$param->getName()]) ? $arguments[$param->getName()] : null;
                    $args[] = $arg;
                }
                /** @var \Symfony\Component\HttpFoundation\Response $response */
                /** @var callable $exec */
                $response = call_user_func_array($exec, $args);
                return $response->getContent();
            } else {
                throw new \Exception('Executor ' . get_class($exec) . ' must has the __invoke public function!');
            }
        });
    }
}