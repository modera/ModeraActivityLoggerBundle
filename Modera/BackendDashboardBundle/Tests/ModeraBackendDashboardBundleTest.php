<?php
/**
 * @copyright 2013 Modera Foundation
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
 */
 

namespace Modera\BackendDashboardBundle\Tests;


use Modera\BackendDashboardBundle\ModeraBackendDashboardBundle;
use Sli\ExpanderBundle\DependencyInjection\CompositeContributorsProviderCompilerPass;

class ModeraBackendDashboardBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testContributorIsInjected()
    {
        $containerBuilder = \Phake::mock('Symfony\Component\DependencyInjection\ContainerBuilder');

        $bundle = new ModeraBackendDashboardBundle($containerBuilder);

        $bundle->build($containerBuilder);

        \Phake::verify($containerBuilder)->addCompilerPass(\Phake::capture($pass));

        $this->assertInstanceOf('Sli\ExpanderBundle\DependencyInjection\CompositeContributorsProviderCompilerPass', $pass);

        /** @var CompositeContributorsProviderCompilerPass $pass */

        $this->assertEquals('modera_backend_dashboard.dashboard_provider', $pass->getProviderServiceId());
        $this->assertEquals('modera_backend_dashboard.dashboard_provider', $pass->getContributorServiceTagName());
    }
} 