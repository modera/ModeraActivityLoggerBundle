<?php

namespace Modera\TranslationsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Modera\TranslationsBundle\DependencyInjection\Compiler\TranslationHandlersCompilerPass;
use Modera\TranslationsBundle\Helper\T;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ModeraTranslationsBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new TranslationHandlersCompilerPass);
    }

    // override
    public function boot()
    {
        $reflClass = new \ReflectionClass(T::clazz());
        $reflProp = $reflClass->getProperty('container');
        $reflProp->setAccessible(true);
        $reflProp->setValue(null, $this->container);
    }
}
