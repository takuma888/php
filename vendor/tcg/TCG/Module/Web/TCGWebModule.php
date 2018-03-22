<?php

namespace TCG\Module\Web;

use TCG\Bundle\Http\Component\HttpExec;
use TCG\Bundle\Http\TCGHttpBundle;
use TCG\Bundle\Twig\TCGTwigBundle;
use TCG\Component\Kernel\AppKernel;
use TCG\Component\Kernel\Module;
use TCG\Component\Kernel\StartKernelInterface;

class TCGWebModule extends Module implements StartKernelInterface
{

    public function getBundles()
    {
        return [
            new TCGHttpBundle(),
            new TCGTwigBundle(),
        ];
    }


    public function getParent()
    {
        return [
            'TCGBaseModule',
        ];
    }



    public function bootKernel(AppKernel $kernel)
    {
        $kernel->setEntry($this);
    }


    public function run(AppKernel $kernel)
    {
        $container = $kernel->getContainer();
        /** @var \Symfony\Component\HttpFoundation\Request $request */
        $request = $container->tag('request');
        /** @var \Symfony\Component\Routing\Matcher\UrlMatcher $urlMatcher */
        $urlMatcher = $container->tag('url_matcher');
        try {
            $routingInfo = $urlMatcher->match($request->getPathInfo());
            if (!isset($routingInfo['_controller'])) {
                throw new \Exception('路由 ' . $routingInfo['_route'] . ' 没有配置 defaults 的 _controller 选项');
            }
            $controller = $routingInfo['_controller'];
            list($module, $controller, $action) = explode(':', $controller);
            $module = trim($module, '@');
            $module = $kernel->getModule($module);
            /** @var HttpExec $exec */
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
                $response->send();
                return $response;
            } else {
                throw new \Exception('Executor ' . get_class($exec) . ' must has the __invoke public function!');
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

}