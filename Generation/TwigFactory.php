<?php

namespace Modera\AdminGeneratorBundle\Generation;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class TwigFactory
{
    /**
     * @return \Twig_Environment
     */
    static public function create()
    {
        $twigLoader = new \Twig_Loader_Filesystem(realpath(__DIR__ . '/../Resources/generation/'));

        return new \Twig_Environment($twigLoader);
    }
}