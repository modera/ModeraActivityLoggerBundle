<?php

namespace Modera\JSRuntimeIntegrationBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('modera_js_runtime_integration');

        $rootNode
            ->children()
                ->scalarNode('home_section')
                    ->defaultValue('home')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('extjs_path') // web accessible path to extjs library
                    ->defaultValue('/extjs')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('runtime_path')
                    ->cannotBeEmpty()
                    ->defaultValue('/modera/js-runtime/src')
                ->end()
                ->scalarNode('viewport_class') // for example: MF.runtime.applications.authenticationaware.view.Viewport
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('server_config_provider_service')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('client_runtime_config_provider_url')
                    ->cannotBeEmpty()
                    ->defaultValue('get-config')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
