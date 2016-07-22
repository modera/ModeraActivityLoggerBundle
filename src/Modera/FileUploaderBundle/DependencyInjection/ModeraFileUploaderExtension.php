<?php

namespace Modera\FileUploaderBundle\DependencyInjection;

use Modera\FileUploaderBundle\Uploading\AllExposedRepositoriesGateway;
use Modera\FileUploaderBundle\Uploading\ExposedGatewayProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ModeraFileUploaderExtension extends Extension
{
    const CONFIG_KEY = 'modera_file_uploader.config';

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $container->setParameter(self::CONFIG_KEY, $config);
        $container->setParameter('modera_file_uploader.is_enabled', $config['is_enabled']);
        $container->setParameter('modera_file_uploader.uploader_url', $config['url']);

        if (true == $config['expose_all_repositories']) {
            $gateway = new Definition(AllExposedRepositoriesGateway::clazz());
            $gateway->addArgument(new Reference('modera_file_repository.repository.file_repository'));

            $container->setDefinition('modera_file_uploader.uploading.all_exposed_repositories_gateway', $gateway);

            $provider = new Definition(ExposedGatewayProvider::clazz());
            $provider->addArgument(new Reference('modera_file_uploader.uploading.all_exposed_repositories_gateway'));
            $provider->addTag('modera_file_uploader.uploading.gateways_provider');

            $container->setDefinition('modera_file_uploader.uploading.all_exposed_repositories_gateway_provider', $provider);
        }
    }
}
