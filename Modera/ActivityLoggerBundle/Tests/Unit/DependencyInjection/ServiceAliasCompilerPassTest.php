<?php

namespace Modera\ActivityLoggerBundle\Tests\Unit\DependencyInjection;

use Modera\ActivityLoggerBundle\DependencyInjection\ModeraActivityLoggerExtension;
use Modera\ActivityLoggerBundle\DependencyInjection\ServiceAliasCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DummyContainerBuilder extends ContainerBuilder
{

}

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ServiceAliasCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    public function testProcess()
    {
        $builder = new ContainerBuilder();
        $builder->setParameter(ModeraActivityLoggerExtension::CONFIG_KEY, array(
            'activity_manager' => 'some_service_id'
        ));

        $this->assertFalse($builder->hasAlias('modera_activity_logger.manager.activity_manager'));

        $cp = new ServiceAliasCompilerPass();
        $cp->process($builder);

        $this->assertEquals('some_service_id', $builder->getAlias('modera_activity_logger.manager.activity_manager'));
    }
} 