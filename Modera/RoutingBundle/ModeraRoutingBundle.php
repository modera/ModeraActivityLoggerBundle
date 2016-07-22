<?php

namespace Modera\RoutingBundle;

use Modera\RoutingBundle\DependencyInjection\DelegatingLoaderCloningCompilerPass;
use Sli\ExpanderBundle\Ext\ExtensionPoint;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2013 Modera Foundation
 */
class ModeraRoutingBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(
            new DelegatingLoaderCloningCompilerPass()
        );

        $routingResourcesProvider = new ExtensionPoint('modera_routing.routing_resources');
        $docs = <<<TEXT
This extension points make it possible for bundles to dynamically contribute routing resources so Symfony can detect them,
this way when a new bundle is added then you don't need to update root routing.yml file every time.
This how a sample contribution could look like:

use Sli\ExpanderBundle\Ext\ContributorInterface;

class RoutingResourcesProvider implements ContributorInterface
{
    /**
     * @inheritDoc
     */
    public function getItems()
    {
        return array(
            '@ModeraBackendLanguagesBundle/Resources/config/routing.yml'
        );
    }
}
TEXT;
        $routingResourcesProvider->setDetailedDescription($docs);
        $routingResourcesProvider->setDescription('Allows to dynamically add routing files.');
        $container->addCompilerPass($routingResourcesProvider->createCompilerPass());
    }
}
