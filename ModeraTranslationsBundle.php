<?php

namespace Modera\TranslationsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Modera\TranslationsBundle\DependencyInjection\Compiler\TranslationHandlersCompilerPass;
use Modera\FoundationBundle\Translation\T;

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

        $container->addCompilerPass(new TranslationHandlersCompilerPass());
    }
}
