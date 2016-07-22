<?php

namespace Modera\MjrIntegrationBundle\Contributions;

use Modera\MjrIntegrationBundle\AssetsHandling\AssetsProvider;
use Modera\MjrIntegrationBundle\DependencyInjection\ModeraMjrIntegrationExtension;
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
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        $bundleConfig = $this->container->getParameter(ModeraMjrIntegrationExtension::CONFIG_KEY);

        $services = array(
            'config_provider' => array(
                'className' => 'MF.runtime.config.AjaxConfigProvider',
                'args' => array(
                    array('url' => $bundleConfig['client_runtime_config_provider_url']),
                ),
            ),
        );

        /* @var AssetsProvider $assetsProvider */
        $assetsProvider = $this->container->get('modera_mjr_integration.assets_handling.assets_provider');

        $jsAssets = $assetsProvider->getJavascriptAssets(AssetsProvider::TYPE_NON_BLOCKING);
        $cssAssets = $assetsProvider->getCssAssets(AssetsProvider::TYPE_NON_BLOCKING);

        if (count(array_merge($jsAssets, $cssAssets)) > 0) {
            $services = array_merge($services, array(
                'non_blocking_assets_loader' => array(
                    'className' => 'MF.misc.NonBlockingAssetsLoader',
                    'args' => array(
                        array(
                            'js' => $jsAssets,
                            'css' => $cssAssets,
                        ),
                    ),
                ),
                'non_blocking_assets_workench_loading_blocking_plugin' => array(
                    'className' => 'Modera.mjrintegration.runtime.plugin.WorkbenchLoadingBlockingPlugin',
                    'tags' => array('runtime_plugin'),
                ),
            ));
        }

        return $services;
    }
}
