<?php

namespace Modera\MJRSecurityIntegrationBundle\Contributions;

use Modera\MJRSecurityIntegrationBundle\DependencyInjection\ModeraMJRSecurityIntegrationExtension;
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
    /**
     * @var ContainerInterface
     */
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
     *
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
     * {@inheritdoc}
     */
    public function getItems()
    {
        $bundleConfig = $this->container->getParameter(ModeraMJRSecurityIntegrationExtension::CONFIG_KEY);

        return array(
            'security_manager' => array(
                'className' => 'MF.security.AjaxSecurityManager',
                'args' => array(
                    array(
                        'urls' => array(
                            'login' => $this->getUrl($bundleConfig['login_url']),
                            'isAuthenticated' => $this->getUrl($bundleConfig['is_authenticated_url']),
                            'logout' => $this->getUrl($bundleConfig['logout_url']),
                        ),
                        'authorizationMgr' => '@authorization_mgr',
                    ),
                ),
            ),
            'extdirect_api_script_injector' => array(
                'className' => 'Modera.mjrsecurityintegration.runtime.ExtDirectApiScriptInjectorPlugin',
                'args' => array(
                    array(
                        'directApiUrl' => $this->getUrl('api'),
                    ),
                ),
                'tags' => array('runtime_plugin'),
            ),
            'profile_context_menu' => array(
                'className' => 'Modera.mjrsecurityintegration.runtime.ProfileContextMenuPlugin',
                'tags' => array('runtime_plugin'),
            ),
            'modera_backend_security.activation_security_interceptor' => array(
                'className' => 'MF.activation.security.ActivationSecurityInterceptor',
                'args' => array(
                    array(
                        'securityMgr' => '@security_manager',
                    ),
                ),
                'tags' => array('activation_interceptor'),
            ),
            'modera_backend_security.auth_required_delegated_error_handler' => array(
                'className' => 'Modera.mjrsecurityintegration.runtime.AuthRequiredDelegatedErrorHandler',
                'args' => array(
                    array(
                        'exceptionClass' => 'Symfony\Component\Security\Core\Exception\AccessDeniedException',
                        'securityMgr' => '@security_manager',
                    ),
                ),
                'tags' => array('delegated_server_error_handler'),
            ),
        );
    }
}
