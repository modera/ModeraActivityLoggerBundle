<?php

namespace Modera\FileUploaderBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('modera_file_uploader');

        $rootNode
            ->children()
                // if set to FALSE the controller that allows to upload files is not accessible
                ->scalarNode('is_enabled')
                    ->defaultValue(false)
                ->end()
                // a URL where uploader controller will be available at
                ->scalarNode('url')
                    ->defaultValue('uploader-gateway')
                ->end()
                // when is set to TRUE then it will be possible to upload files to all of registered repositories
                // (see ModeraFileRepositoryBundle)
                ->scalarNode('expose_all_repositories')
                    ->defaultValue(true)
                ->end()
            ->end()
        ;

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
