<?php

namespace Modera\TranslationsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class TranslationHandlersCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('modera_translations.service.translation_handlers_chain')) {
            return;
        }

        $definition = $container->getDefinition(
            'modera_translations.service.translation_handlers_chain'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'modera_translations.translation_handler'
        );
        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall(
                'addHandler',
                array(new Reference($id))
            );
        }
    }
}
