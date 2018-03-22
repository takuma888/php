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
        $template_path = '@' . $module_name . '/' . $template_path;
        return $this->renderTemplate($template_path, $context);
    }

    protected function renderTemplate($template_path = 'index/index.html.twig', array $context = array())
    {
        /** @var \Twig_Loader_Filesystem $environment */
        $environment = getContainer()->get('twig.filesystem_loader');
        $global_template_path = null;
        $namespace_template_path = null;
        if (strpos($template_path, '@') === 0) {
            $global_template_path = substr($template_path, 1);
            $namespace_template_path = $template_path;
        } else {
            $global_template_path = $template_path;
        }
        /** @var \Twig_Environment $engine */
        $engine = getContainer()->get('twig.engine');
        try {
            $template = $engine->loadTemplate($global_template_path);
            $response = $template->render($context);
            return new Response($response);
        } catch (\Exception $e) {
            if ($namespace_template_path) {
                $namespace = substr($template_path, 1, strpos($template_path, '/') - 1);
                $custom_template_dir = $this->addTemplateRoot();
                if ($custom_template_dir) {
                    $environment->addPath($custom_template_dir, $namespace);
                }
                $template = $engine->loadTemplate($namespace_template_path);
                $response = $template->render($context);
                return new Response($response);
            } else {
                throw new \Exception('Twig模板文件格式错误: ' . $template_path);
            }
        }
    }


    public function addTemplateRoot()
    {
        $module = $this->getModule();
        $moduleRoot = $module->getRoot();
        $templateRoot = $moduleRoot . '/Template';
        return realpath($templateRoot);
    }
}