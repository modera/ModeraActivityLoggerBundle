<?php

namespace Modera\SecurityAwareJSRuntimeBundle\Contributions;

use Modera\JSRuntimeIntegrationBundle\DependencyInjection\ModeraJSRuntimeIntegrationExtension;
use Modera\SecurityAwareJSRuntimeBundle\DependencyInjection\ModeraSecurityAwareJSRuntimeExtension;
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
        $bundleConfig = $this->container->getParameter(ModeraSecurityAwareJSRuntimeExtension::CONFIG_KEY);

        return [
            'security_manager' => [
                'className' => 'MF.security.AjaxSecurityManager',
                'args' => [
                    [
                        'urls' => [
                            'login' => $bundleConfig['login_url'],
                            'isAuthenticated' => $bundleConfig['is_authenticated_url'],
                            'logout' => $bundleConfig['logout_url']
                        ]
                    ]
                ]
            ]
        ];
    }
}