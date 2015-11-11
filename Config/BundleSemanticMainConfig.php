<?php

namespace Modera\MjrIntegrationBundle\Config;

use Modera\MjrIntegrationBundle\DependencyInjection\ModeraMjrIntegrationExtension;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * This implementation will read config from bundle's semantic config.
 *
 * @see \Modera\MjrIntegrationBundle\DependencyInjection\Configuration
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class BundleSemanticMainConfig implements MainConfigInterface
{
    private $config;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->config = $container->getParameter(ModeraMjrIntegrationExtension::CONFIG_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->config['deployment_name'];
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return $this->config['deployment_url'];
    }

    /**
     * {@inheritdoc}
     */
    public function getHomeSection()
    {
        return $this->config['home_section'];
    }
}
