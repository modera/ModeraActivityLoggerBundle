<?php

namespace Modera\MjrIntegrationBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('modera_mjr_integration');

        $rootNode
            ->children()
                ->arrayNode('menu_items')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('name')
                                ->cannotBeEmpty()
                                ->isRequired()
                            ->end()
                            ->scalarNode('id')
                                ->cannotBeEmpty()
                                ->isRequired()
                            ->end()
                            ->scalarNode('section')
                                ->cannotBeEmpty()
                                ->defaultValue('$ns.runtime.Section')
                            ->end()
                            ->scalarNode('namespace')
                                ->cannotBeEmpty()
                                ->isRequired()
                            ->end()
                            ->scalarNode('path')
                                ->cannotBeEmpty()
                                ->isRequired()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                // web accessible path to extjs library, you shouldn't specify any specific JS file
                ->scalarNode('extjs_path')
                    ->defaultValue('/extjs')
                    ->cannotBeEmpty()
                ->end()
                // if this is set to TRUE then the most developer friendly version of extjs will be included,
                // most verbose debug will be provided regarding errors
                ->scalarNode('extjs_console_warnings')
                    ->defaultValue(false)
                ->end()
                ->scalarNode('runtime_path')
                    ->cannotBeEmpty()
                    ->defaultValue('/modera/mjr/src')
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
                // this property will be used only if 'main_config_provider' contains 'modera_mjr_integration.config.bundle_semantic_config'
                ->scalarNode('home_section')
                    ->defaultValue('home')
                    ->cannotBeEmpty()
                ->end()
                // this property will be used only if 'main_config_provider' contains 'modera_mjr_integration.config.bundle_semantic_config'
                ->scalarNode('deployment_name')
                    ->defaultValue(null)
                ->end()
                // this property will be used only if 'main_config_provider' contains 'modera_mjr_integration.config.bundle_semantic_config'
                ->scalarNode('deployment_url')
                    ->defaultValue(null)
                ->end()
                // DI service ID that implements \Modera\MjrIntegrationBundle\Config\MainConfigInterface
                // In your application code you can use "modera_mjr_integration.config.main_config" service which will
                // get automatically resolved to a value configured by this property
                ->scalarNode('main_config_provider')
                    ->defaultValue('modera_mjr_integration.config.bundle_semantic_config')
                ->end()
                // this is going to be used as configuration parameter for instance of Ext.app.Application and
                // corresponding server controller's action that will be responsible for generating subclass
                // of Application class. A value for this configuration property must be a valid not-escaped hash object
                // value. For example, valid names are:
                // Foo
                // Mega_Application
                // Invalid names:
                // Mega Application
                ->scalarNode('app_name')
                    ->defaultValue('ModeraFoundation')
                ->end()
                // Used by MF to prefix all routes which are related to backend,
                // it makes sense to have this value as a configuration key so later we would be able to refer
                // to in routing configuration.
                // NB! If you are changing this parameter don't forget to update firewall rules in security.yml
                // to keep you admin interface secured.
                ->scalarNode('routes_prefix')
                    ->defaultValue('/backend')
                ->end()
                // Deprecated and will be removed as of 2.0. Please use "routes_prefix" instead
                ->scalarNode('route_prefix')
                    ->defaultValue('')
                ->end()
                // Specifies what class ExtJs application should extend, this might be useful
                // if you need to tweak some bootstrapping logic. For more details how this
                // configuration parameter can be used you can take a look at
                // \Modera\MJRSecurityIntegrationBundle\Controller\IndexController::indexAction
                // and @MJRSecurityIntegrationBundle/Resources/views/Index/application.html.twig
                ->scalarNode('app_base_class')
                    ->defaultValue('')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
