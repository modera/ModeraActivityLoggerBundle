<?php

namespace Modera\FileRepositoryBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('modera_file_repository');

        $rootNode
            ->children()
                // deprecated
                ->scalarNode('is_enabled')
                    ->defaultValue(null)
                ->end()
                // deprecated
                ->scalarNode('route_url_prefix')
                    ->defaultValue(null)
                ->end()
                // deprecated
                ->scalarNode('get_file_route')
                    ->defaultValue(null)
                ->end()

                // This node add ability to control access to stored files through the proxy controller
                // See: \Modera\FileRepositoryBundle\Entity\StoredFile::getUrl
                ->arrayNode('controller')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('is_enabled')
                            ->defaultValue(true)
                        ->end()
                        ->scalarNode('route_url_prefix')
                            ->defaultValue('/u')
                        ->end()
                        // See: Modera\FileRepositoryBundle\StoredFile\UrlGenerator
                        ->scalarNode('get_file_route')
                            ->defaultValue('modera_file_repository.get_file')
                        ->end()
                    ->end()
                ->end()
                // Must implement \Modera\FileRepositoryBundle\StoredFile\UrlGeneratorInterface
                ->scalarNode('default_url_generator')
                    ->defaultValue('modera_file_repository.stored_file.url_generator')
                ->end()
                ->arrayNode('url_generators')
                    ->prototype('variable')->end()
                ->end()
                // Should point to an implementation of \Modera\FileRepositoryBundle\Intercepting\InterceptorsProviderInterface
                // interface
                ->scalarNode('interceptors_provider')
                    ->defaultValue('modera_file_repository.intercepting.default_interceptors_provider')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
