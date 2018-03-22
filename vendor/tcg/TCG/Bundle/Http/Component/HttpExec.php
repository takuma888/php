<?php

namespace TCG\Bundle\Http\Component;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGenerator;
use TCG\Component\Kernel\Exec;

abstract class HttpExec extends Exec
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Session
     */
    public function getSession()
    {
        /** @var Session $session */
        $session = $this->getRequest()->getSession();
        return $session;
    }


    /**
     * @param $name
     * @param array $params
     * @return string
     */
    public function url($name, array $params = array())
    {
        /** @var UrlGenerator $url_generator */
        $url_generator = getContainer()->tag('url_generator');
        return $url_generator->generate($name, $params, UrlGenerator::ABSOLUTE_URL);
    }

    /**
     * @param $name
     * @param array $params
     * @return string
     */
    public function path($name, array $params = array())
    {
        /** @var UrlGenerator $url_generator */
        $url_generator = getContainer()->tag('url_generator');
        return $url_generator->generate($name, $params, UrlGenerator::ABSOLUTE_PATH);
    }


    /**
     * @param $route
     * @param array $params
     * @return null|RedirectResponse
     */
    public function redirect($route, array $params = array())
    {
        return new RedirectResponse($this->url($route, $params));
    }

    /**
     * @param $url
     * @return RedirectResponse
     */
    public function jump($url)
    {
        return new RedirectResponse($url);
    }

    /**
     * @param array $array
     * @return null|JsonResponse|RedirectResponse
     */
    public function json(array $array)
    {
        return new JsonResponse($array);
    }
}