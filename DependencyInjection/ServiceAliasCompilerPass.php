<?php

namespace Modera\BackendSecurityBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Modera\BackendSecurityBundle\DependencyInjection\ModeraBackendSecurityExtension;

/**
 * Adds a service with ID "modera_backend_security.service.mail_service" to service container
 * that you can use in your application logic without the need to use specific implementation.
 *
 * @author    Stas Chychkan <stas.chichkan@modera.net>
 * @copyright 2015 Modera Foundation
 */
class ServiceAliasCompilerPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        $config = $container->getParameter(ModeraBackendSecurityExtension::CONFIG_KEY);

        $aliasConfig = array();
        $aliasConfig['modera_backend_security.service.mail_service'] = $config['mail_service'];

        $container->addAliases($aliasConfig);
    }
} 