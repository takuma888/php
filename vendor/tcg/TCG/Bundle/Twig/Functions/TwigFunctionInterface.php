<?php

namespace TCG\Bundle\Twig\Functions;

interface TwigFunctionInterface
{
    /**
     * @return \Twig_SimpleFunction
     */
    public static function getTwigFunction();
}