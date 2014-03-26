<?php

namespace Modera\BackendToolsBundle;

use Sli\ExpanderBundle\DependencyInjection\CompositeContributorsProviderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ModeraBackendToolsBundle extends Bundle
{
    const ROLE_ACCESS_TOOLS_SECTION = 'ROLE_BACKEND_TOOLS_ACCESS_SECTION';

    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(
            new CompositeContributorsProviderCompilerPass('modera_backend_tools.sections_provider')
        );
    }
}
