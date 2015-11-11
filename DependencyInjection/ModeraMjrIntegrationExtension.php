<?php

namespace Modera\MjrIntegrationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ModeraMjrIntegrationExtension extends Extension
{
    const CONFIG_KEY = 'modera_mjr_integration.config';
    const CONFIG_APP_NAME = 'modera_mjr_integration.config.app_name';
    const CONFIG_ROUTE_PREFIX = 'modera_mjr_integration.route_prefix';

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter(self::CONFIG_KEY, $config);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $container->setParameter(self::CONFIG_KEY, $config);
        $container->setParameter(self::CONFIG_APP_NAME, $config['app_name']);
        $container->setParameter(self::CONFIG_ROUTE_PREFIX, $config['route_prefix']);
    }
}
