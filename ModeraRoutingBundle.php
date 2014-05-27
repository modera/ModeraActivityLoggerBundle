<?php

namespace Modera\RoutingBundle;

use Sli\ExpanderBundle\Ext\ExtensionPoint;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Sli\ExpanderBundle\DependencyInjection\CompositeContributorsProviderCompilerPass;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2013 Modera Foundation
 */
class ModeraRoutingBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        $routingResourcesProvider = new ExtensionPoint('modera_routing.routing_resources');
        $routingResourcesProvider->setDescription('Allows to dynamically add routing files.');
        $container->addCompilerPass($routingResourcesProvider->createCompilerPass());
    }
}
