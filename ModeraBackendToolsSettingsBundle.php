<?php

namespace Modera\BackendToolsSettingsBundle;

use Sli\ExpanderBundle\DependencyInjection\CompositeContributorsProviderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ModeraBackendToolsSettingsBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        // Use this extension point to provide sections to "Backend / Tools / Settings" section.
        // See \Modera\BackendToolsSettingsBundle\Section\SectionInterface
        $container->addCompilerPass(
            new CompositeContributorsProviderCompilerPass('modera_backend_tools_settings.contributions.sections_provider')
        );
    }
}
