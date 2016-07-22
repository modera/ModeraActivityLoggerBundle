<?php

namespace Modera\BackendDashboardBundle;

use Sli\ExpanderBundle\DependencyInjection\CompositeContributorsProviderCompilerPass;
use Sli\ExpanderBundle\Ext\ExtensionPoint;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class ModeraBackendDashboardBundle
 *
 * @author    Alex Rudakov <alexandr.rudakov@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ModeraBackendDashboardBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        $dashboardProvider = new ExtensionPoint('modera_backend_dashboard.dashboard');
        $dashboardProvider->setDescription('Allows to contribute new dashboard panels to Backend/Dashboard section.');

        $container->addCompilerPass($dashboardProvider->createCompilerPass());
    }
}
