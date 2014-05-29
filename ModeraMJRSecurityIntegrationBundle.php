<?php

namespace Modera\MJRSecurityIntegrationBundle;

use Sli\ExpanderBundle\Contributing\ExtensionPointsAwareBundleInterface;
use Sli\ExpanderBundle\DependencyInjection\CompositeContributorsProviderCompilerPass;
use Sli\ExpanderBundle\Ext\ExtensionPoint;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Bundle provides provides integration which is necessary to make MJR to be security aware ( authentication,
 * authorization ).
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class ModeraMJRSecurityIntegrationBundle extends Bundle
{
    const ROLE_BACKEND_USER = 'ROLE_BACKEND_USER';

    // override
    public function build(ContainerBuilder $container)
    {
        $clientDiServiceDefinitionsProvider = new ExtensionPoint('modera_mjr_security_integration.client_di_service_defs');
        $clientDiServiceDefinitionsProvider->setDescription(
            'Allows to contribute client-side DI container service definitions that will be configured only after user has successfully authenticated.'
        );
        $container->addCompilerPass($clientDiServiceDefinitionsProvider->createCompilerPass());
    }
}
