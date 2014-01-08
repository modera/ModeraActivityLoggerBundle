<?php

namespace Modera\SecurityAwareJSRuntimeBundle\Contributions;

use Modera\JSRuntimeIntegrationBundle\DependencyInjection\ModeraJSRuntimeIntegrationExtension;
use Modera\SecurityAwareJSRuntimeBundle\DependencyInjection\ModeraSecurityAwareJSRuntimeExtension;
use Sli\ExpanderBundle\Ext\ContributorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
     * @param string $route
     * @return string
     */
    private function getUrl($route)
    {
        if ('/' !== $route[0]) {
            return $this->container->get('router')->generate($route, array(), UrlGeneratorInterface::ABSOLUTE_PATH);
        }

        return $route;
    }

    /**
     * {@inheritDoc}
     */
    public function getItems()
    {
        $bundleConfig = $this->container->getParameter(ModeraSecurityAwareJSRuntimeExtension::CONFIG_KEY);

        return [
            'security_manager' => [
                'className' => 'MF.security.AjaxSecurityManager',
                'args' => [
                    [
                        'urls' => [
                            'login'           => $this->getUrl($bundleConfig['login_url']),
                            'isAuthenticated' => $this->getUrl($bundleConfig['is_authenticated_url']),
                            'logout'          => $this->getUrl($bundleConfig['logout_url']),
                        ],
                        'configProvider' => '@config_provider'
                    ]
                ]
            ],
            'ui_security_plugin' => array(
                'className' => 'Modera.securityawarejsruntime.runtime.plugin.UiSecurityPlugin',
                'tags' => ['runtime_plugin']
            )
        ];
    }
}