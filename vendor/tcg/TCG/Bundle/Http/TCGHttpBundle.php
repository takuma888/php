<?php

namespace TCG\Bundle\Http;

use Symfony\Component\Routing\Route;
use TCG\Component\Kernel\AppKernel;
use TCG\Component\Kernel\Bundle;

class TCGHttpBundle extends Bundle
{

    public function bootKernel(AppKernel $kernel)
    {
        $app_container = $kernel->getContainer();
        $configuration = $kernel->getConfig();
        $routes_config = empty($configuration->routing) ? array() : $configuration->routing;
        /** @var \Symfony\Component\Routing\RouteCollection $routes */
        $routes = $app_container->tag('route_collection');
        foreach ($routes_config as $route_name => $route_config) {
            $routes->add($route_name, new Route(
                $route_config['path'],
                $route_config['defaults'] ? $route_config['defaults']->toArray() : [],
                $route_config['requirements'] ? $route_config['requirements']->toArray() : [],
                $route_config['options'] ? $route_config['options']->toArray() : [],
                $route_config['host'],
                $route_config['schemes'] ? $route_config['schemes']->toArray() : [],
                $route_config['methods'] ? $route_config['methods']->toArray() : [],
                $route_config['condition']
            ));
        }
        unset($configuration['routing']);
    }
}