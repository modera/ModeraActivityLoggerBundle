<?php

namespace Modera\SecurityAwareJSRuntimeBundle;

use Sli\ExpanderBundle\DependencyInjection\CompositeContributorsProviderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Bundle provides provides integration which is necessary to make jsruntime to be security aware ( authentication,
 * authorization ).
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class ModeraSecurityAwareJSRuntimeBundle extends Bundle
{
    const ROLE_BACKEND_USER = 'ROLE_BACKEND_USER';

    public function build(ContainerBuilder $container)
    {
        // allows to contribute client-side DI container service definitions that will be configured only
        // after user has successfully authenticated
        $container->addCompilerPass(
            new CompositeContributorsProviderCompilerPass('modera_security_aware_js_runtime.client_di_service_defs_provider')
        );
    }
}
