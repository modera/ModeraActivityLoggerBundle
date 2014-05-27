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
        $exceptionHandlersProviderDescription = <<<TEXT
You will need to use this extension point if you need to apply some custom handling to server-side exceptions. For more
details please see \Modera\ServerCrudBundle\ExceptionHandling\EnvAwareExceptionHandler and
\Modera\ServerCrudBundle\ExceptionHandling\ExceptionHandlerInterface.
TEXT;
        $exceptionHandlersProvider->setDetailedDescription($exceptionHandlersProviderDescription);
        $container->addCompilerPass($exceptionHandlersProvider->createCompilerPass());

        // see \Modera\ServerCrudBundle\Controller\AbstractCrudController::interceptAction
        // CAP stands for "Controller Action Interceptor"
        $caiProviders = new ExtensionPoint('modera_server_crud.intercepting.cai');
        $caiProviders->setDescription('Allows to contribute Controller Action Interceptors used by AbstractCrudController.');
        $caiProvidersDescription = <<<TEXT
Allow to add Controller Action Interceptors, for more details please see
\Modera\ServerCrudBundle\Intercepting\ControllerActionsInterceptorInterface.
TEXT;
        $caiProviders->setDetailedDescription($caiProvidersDescription);
        $container->addCompilerPass($caiProviders->createCompilerPass());
    }
}
