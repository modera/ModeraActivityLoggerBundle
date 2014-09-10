<?php

namespace Modera\MJRSecurityIntegrationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('modera_mjr_security_integration');

        $rootNode
            ->children()
                ->scalarNode('login_url')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('logout_url')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('is_authenticated_url')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('extjs_ajax_timeout')
                    ->cannotBeEmpty()
                    ->defaultValue(60000)
                ->end()
            ->end();

        return $treeBuilder;
    }
}
