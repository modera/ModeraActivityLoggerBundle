<?php

namespace Modera\ServerCrudBundle;

use Sli\ExpanderBundle\DependencyInjection\CompositeContributorsProviderCompilerPass;
use Sli\ExpanderBundle\Ext\ExtensionPoint;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ModeraServerCrudBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        // also see \Modera\ServerCrudBundle\ExceptionHandling\EnvAwareExceptionHandler
        $exceptionHandlersProvider = new ExtensionPoint('modera_server_crud.exception_handling.handlers');
        $exceptionHandlersProvider->setDescription('Allows to add additional exception handlers that will be used by AbstractCrudController.');
        $container->addCompilerPass($exceptionHandlersProvider->createCompilerPass());

        // see \Modera\ServerCrudBundle\Controller\AbstractCrudController::interceptAction
        // CAP stands for "Controller Action Interceptor"
        $caiProviders = new ExtensionPoint('modera_server_crud.intercepting.cai');
        $caiProviders->setDescription('Allows to contribute Controller Action Interceptors used by AbstractCrudController.');
        $container->addCompilerPass($caiProviders->createCompilerPass());
    }
}
