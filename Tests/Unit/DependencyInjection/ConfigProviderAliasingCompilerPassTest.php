<?php

namespace Modera\MjrIntegrationBundle\Tests\Unit\DependencyInjection;

use Modera\MjrIntegrationBundle\DependencyInjection\ConfigProviderAliasingCompilerPass;
use Modera\MjrIntegrationBundle\DependencyInjection\ModeraMjrIntegrationExtension;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2016 Modera Foundation
 */
class ConfigProviderAliasingCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    public function testProcess()
    {
        $container = \Phake::mock('Symfony\Component\DependencyInjection\ContainerBuilder');
        \Phake::when($container)
            ->getParameter(ModeraMjrIntegrationExtension::CONFIG_KEY)
            ->thenReturn(array(
                'main_config_provider' => 'foo_service',
            ))
        ;

        $pass = new ConfigProviderAliasingCompilerPass();
        $pass->process($container);

        \Phake::verify($container)
            ->setAlias('modera_mjr_integration.config.main_config', 'foo_service')
        ;
    }
}
