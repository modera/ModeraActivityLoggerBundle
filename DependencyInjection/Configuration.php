<?php

namespace Modera\SecurityBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('modera_security');

        $rootNode
            ->children()
                ->scalarNode('root_user_handler')
                    ->cannotBeEmpty()
                    ->defaultValue('modera_security.root_user_handler.semantic_config_root_user_handler')
                ->end()
                ->arrayNode('root_user')
                    ->addDefaultsIfNotSet()
                    ->cannotBeEmpty()
                    ->children()
                        // these configuration properties are only used when
                        // 'modera_security.root_user_handler.semantic_config_root_user_handler' service is used
                        // as 'root_user_handler'
                        ->variableNode('query')
                            ->defaultValue(array('id' => 1))
                            ->cannotBeEmpty()
                        ->end()
                        ->variableNode('roles') // * - means all privileges
                            // it can also be array with roles names
                            ->defaultValue('*')
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('access_control')
                    ->defaultValue(array())
                    ->prototype('array')
                        ->prototype('variable')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
