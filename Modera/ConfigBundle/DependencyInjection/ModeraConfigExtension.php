<?php

namespace Modera\ConfigBundle\DependencyInjection;

use Modera\ConfigBundle\ModeraConfigBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ModeraConfigExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        /*
        $kernelBundles = $container->getParameter('kernel.bundles');
        if (isset($kernelBundles['ModeraSecurityBundle']) && null == $config['owner_entity']) {
            $config['owner_entity'] = 'Modera\SecurityBundle\Entity\User';
        }
        */

        if ($config['owner_entity']) {
            $listener = $container->getDefinition('modera_config.listener.owner_relation_mapping_listener');

            $listener->addTag('doctrine.event_listener', array(
                'event' => 'loadClassMetadata',
            ));
        }

        $container->setParameter(ModeraConfigBundle::CONFIG_KEY, $config);
    }
}
