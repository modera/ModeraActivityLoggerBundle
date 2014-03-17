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

        return array(
            'config_provider' => array(
                'className' => 'MF.runtime.config.AjaxConfigProvider',
                'args' => [
                    array('url' => $bundleConfig['client_runtime_config_provider_url'])
                ]
            )
        );
    }
}