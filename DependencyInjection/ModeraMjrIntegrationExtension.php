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
    const CONFIG_ROUTES_PREFIX = 'modera_mjr_integration.routes_prefix';

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

        // making sure that 'route_prefix' still works
        if ('' != $config['route_prefix']) {
            $msg = implode(' ', [
                'modera_mjr_integration/route_prefix is deprecated since version 1.4.1',
                'and will be removed in 2.0. Use modera_mjr_integration/routes_prefix instead.',
            ]);
            trigger_error($msg, E_USER_DEPRECATED);

            $config['routes_prefix'] = $config['route_prefix'];
        }

        $container->setParameter(self::CONFIG_KEY, $config);
        $container->setParameter(self::CONFIG_APP_NAME, $config['app_name']);
        $container->setParameter(self::CONFIG_ROUTES_PREFIX, $config['routes_prefix']);
    }
}
