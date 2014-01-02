<?php

namespace Modera\AdminGeneratorBundle;

use Sli\ExpanderBundle\DependencyInjection\CompositeContributorsProviderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ModeraAdminGeneratorBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(
            new CompositeContributorsProviderCompilerPass('modera_admin_generator.sections_provider')
        );

        // see \Modera\AdminGeneratorBundle\ExceptionHandling\EnvAwareExceptionHandler
        $container->addCompilerPass(
            new CompositeContributorsProviderCompilerPass('modera_admin_generator.exception_handling.handlers_provider')
        );
    }
}
