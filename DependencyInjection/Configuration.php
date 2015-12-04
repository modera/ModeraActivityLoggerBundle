<?php

namespace Modera\BackendSecurityBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('modera_backend_security');

        $rootNode
            ->children()
                // must contain service container ID of an \Modera\BackendSecurityBundle\Service\MailServiceInterface
                // implementation.
                ->scalarNode('mail_service')
                    ->cannotBeEmpty()
                    ->defaultValue('modera_backend_security.service.default_mail_service')
                ->end()
                ->scalarNode('mail_sender')
                    ->defaultValue('no-reply@no-reply')
                    ->cannotBeEmpty()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
