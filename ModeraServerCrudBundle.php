<?php

namespace Modera\ServerCrudBundle;

use Sli\ExpanderBundle\DependencyInjection\CompositeContributorsProviderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ModeraServerCrudBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        // see \Modera\ServerCrudBundle\ExceptionHandling\EnvAwareExceptionHandler
        $container->addCompilerPass(
            new CompositeContributorsProviderCompilerPass('modera_server_crud.exception_handling.handlers_provider')
        );

        // see \Modera\ServerCrudBundle\Controller\AbstractCrudController::interceptAction
        // CAP stands for "Controller Action Interceptor"
        $container->addCompilerPass(
            new CompositeContributorsProviderCompilerPass('modera_server_crud.intercepting.cai_providers')
        );
    }
}
