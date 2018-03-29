<?php

namespace TCG\Bundle\Twig\Component;

use Symfony\Component\HttpFoundation\Response;
use TCG\Bundle\Http\Component\HttpExec;

abstract class TwigHttpExec extends HttpExec
{
    /**
     * @param string $template_path
     * @param array $context
     * @return Response
     * @throws \Exception
     */
    public function render($template_path = 'index/index.html.twig', array $context = array())
    {
        if (strpos($template_path, '@') !== false) {
            return $this->renderTemplate($template_path, $context);
        }
        $module = $this->getModule();
        $module_name = $module->getName();
        $exec_name = $module->getExecRoot();
        if ($exec_name) {
            $module_name .= ':' . $exec_name;
        }
        $template_path = '@' . $module_name . '/' . $template_path;
        return $this->renderTemplate($template_path, $context);
    }

    /**
     * @param string $template_path
     * @param array $context
     * @return Response
     * @throws \Exception
     */
    protected function renderTemplate($template_path = 'index/index.html.twig', array $context = array())
    {
        $global_template_path = null;
        $namespace_template_path = null;
        /** @var \Twig_Environment $engine */
        $engine = getContainer()->get('twig.engine');
        try {
            $template = $engine->loadTemplate($template_path);
            $response = $template->render($context);
            return new Response($response);
        } catch (\Exception $e) {
            throw $e;
        }
    }

}