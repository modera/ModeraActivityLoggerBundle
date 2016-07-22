<?php

namespace Modera\FileRepositoryBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ModeraFileRepositoryExtension extends Extension
{
    const CONFIG_KEY = 'modera_file_repository.config';

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $deprecated = array('is_enabled', 'route_url_prefix', 'get_file_route');
        $container->setParameter(self::CONFIG_KEY, $config);
        foreach ($config as $key => $value) {
            if (in_array($key, $deprecated) && null !== $value) {
                @trigger_error('Configuration "'.$key.'" is deprecated.', E_USER_DEPRECATED);
                $config['controller'][$key] = $value;
            }

            $container->setParameter(self::CONFIG_KEY.'.'.$key, $value);

            if ('controller' == $key) {
                foreach ($value as $k => $v) {
                    $container->setParameter(self::CONFIG_KEY.'.'.$key.'.'.$k, $v);
                }
            }
        }

        $container->setDefinition(
            'modera_file_repository.intercepting.interceptors_provider',
            $container->getDefinition($config['interceptors_provider'])
        );
    }
}
