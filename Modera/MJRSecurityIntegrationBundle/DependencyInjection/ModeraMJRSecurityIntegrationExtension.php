<?php

namespace Modera\MJRSecurityIntegrationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ModeraMJRSecurityIntegrationExtension extends Extension implements PrependExtensionInterface
{
    const CONFIG_KEY = 'modera_mjr_security_integration.config';

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if (count($config, true) == 0) {
            throw new \RuntimeException('Bundle "ModeraMJRSecurityIntegrationBundle" must be configured in config.yml!');
        }

        $container->setParameter(self::CONFIG_KEY, $config);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        // Secured MJR application relies on AuthenticationRequiredApplication to bootstrap itself
        $container->prependExtensionConfig('modera_mjr_integration', array(
            'app_base_class' => 'MF.runtime.applications.authenticationaware.AuthenticationRequiredApplication',
        ));
    }
}
