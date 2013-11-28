<?php

namespace Modera\JSRuntimeIntegrationBundle;

use Sli\ExpanderBundle\DependencyInjection\CompositeContributorsProviderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Bundle ships basic utilities which simplify integration of Modera JavaScript runtime.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class ModeraJSRuntimeIntegrationBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(
            new CompositeContributorsProviderCompilerPass('mf.jsruntimeintegration.config.config_mergers_provider')
        );

        $container->addCompilerPass(
            new CompositeContributorsProviderCompilerPass('mf.jsruntimeintegration.menu.menu_items_provider')
        );

        $container->addCompilerPass(
            new CompositeContributorsProviderCompilerPass('mf.jsruntimeintegration.csdi.service_definitions_provider')
        );

        $container->addCompilerPass(
            new CompositeContributorsProviderCompilerPass('mf.jsruntimeintegration.sections_provider')
        );

        $container->addCompilerPass(
            new CompositeContributorsProviderCompilerPass('mf.jsruntimeintegration.css_resources_provider')
        );
    }
}
