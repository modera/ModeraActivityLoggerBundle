<?php

namespace Modera\JSRuntimeIntegrationBundle\Config;

use Modera\JSRuntimeIntegrationBundle\DependencyInjection\ModeraJSRuntimeIntegrationExtension;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * This implementation will read config from bundle's semantic config.
 *
 * @see \Modera\JSRuntimeIntegrationBundle\DependencyInjection\Configuration
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
        $this->config = $container->getParameter(ModeraJSRuntimeIntegrationExtension::CONFIG_KEY);
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->config['deployment_name'];
    }

    /**
     * @inheritDoc
     */
    public function getUrl()
    {
        return $this->config['deployment_url'];
    }

    /**
     * @inheritDoc
     */
    public function getHomeSection()
    {
        return $this->config['home_section'];
    }
} 