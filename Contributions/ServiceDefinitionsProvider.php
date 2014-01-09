<?php

namespace Modera\JSRuntimeIntegrationBundle\Contributions;

use Modera\JSRuntimeIntegrationBundle\DependencyInjection\ModeraJSRuntimeIntegrationExtension;
use Sli\ExpanderBundle\Ext\ContributorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides service definitions for client-side dependency injection container.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class ServiceDefinitionsProvider implements ContributorInterface
{
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function getItems()
    {
        $bundleConfig = $this->container->getParameter(ModeraJSRuntimeIntegrationExtension::CONFIG_KEY);

        return [
            'config_provider' => [
                'className' => 'MF.runtime.config.AjaxConfigProvider',
                'args' => [
                    ['url' => $bundleConfig['client_runtime_config_provider_url']]
                ]
            ],
            'workbench' => [
                'className' => 'MF.runtime.Workbench',
                'args' => ['@self']
            ],
            'sections_controller_resolver' => [
                'className' => 'MF.runtime.SectionsControllerResolver',
                'args' => ['@config_provider']
            ],
            'process_manager' => [
                'className' => 'MF.process.ProcessManager',
                'args' => [['serviceContainer' => '@self']]
            ],
            'process_monitor' => [
                'className' =>  'MF.process.ConsoleProcessMonitor'
            ],
            'event_bus' => [
                'className' => 'MF.runtime.EventBus'
            ],
            'root_execution_context' => [
                'className' => 'MF.viewsmanagement.executioncontext.BrowserHistoryExecutionContext',
                'args' => [['serviceContainer' => '@self']]
            ],
            'class_loader_configurator' => [
                'className' => 'MF.runtime.ClassLoaderConfigurator',
                'args' => ['@config_provider']
            ],
            'plugin_manager' => [
                'className' => 'MF.runtime.extensibility.PluginManager',
                'args' => [array('container' => '@self')]
            ],
            'data_sync_runtime_plugin' => array(
                'className' => 'MF.misc.DataSyncPlugin',
                'tags' => ['runtime_plugin']
            )
        ];
    }
}