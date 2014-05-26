<?php

namespace Modera\ServerCrudBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('modera_server_crud');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        $rootNode
            ->children()
                ->scalarNode('persistence_handler')
                    ->defaultValue('modera_server_crud.persistence.default_handler')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('model_manager')
                    ->defaultValue('modera_server_crud.persistence.model_manager')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('entity_validator')
                    ->defaultValue('modera_server_crud.validation.default_entity_validator')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('data_mapper')
                    ->defaultValue('modera_server_crud.data_mapping.default_data_mapper')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('entity_factory')
                    ->defaultValue('modera_server_crud.entity_factory.default_entity_factory')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('exception_handler')
                    ->defaultValue('modera_server_crud.exception_handling.env_aware_handler')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('hydrator')
                    ->defaultValue('modera_server_crud.hydration.hydration_service')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('new_values_factory')
                    ->defaultValue('modera_server_crud.new_values_factory.default_new_values_factory')
                    ->cannotBeEmpty()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
