<?php

namespace Modera\RoutingBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2015 Modera Foundation
 */
class DelegatingLoaderCloningCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        /*
         * Since Symfony 2.7 \Symfony\Bundle\FrameworkBundle\Routing\DelegatingLoader::load method
         * has validation (using instance variable $loading) that prevents using DelegatingLoader recursively,
         * to work this around we are making a clone of its original definition and then using a created service
         * in \Modera\RoutingBundle\Routing\Loader
         */
        $container->setDefinition(
            'modera_routing.symfony_delegating_loader', clone $container->getDefinition('routing.loader')
        );
    }
}
