<?php

namespace Modera\MJRCacheAwareClassLoaderBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('modera_mjr_cache_aware_class_loader');

        $rootNode
            ->children()
                ->scalarNode('is_enabled')
                    ->defaultValue(true)
                ->end()
                ->scalarNode('version')->end()
                ->scalarNode('url')
                    ->defaultValue('extjs-class-loader.js')
                ->end()
                ->scalarNode('version_resolver')
                    ->defaultValue('modera_mjr_cache_aware_class_loader.standard_version_resolver')
                ->end()
            ->end()
        ;

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
