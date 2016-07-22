<?php

namespace Modera\ConfigBundle\Tests\Unit\DependencyInjection;

use Modera\ConfigBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.org>
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testNoExplicitConfigProvided()
    {
        $configuration = new Configuration();

        $processor = new Processor();
        $config = $processor->processConfiguration($configuration, array());

        $this->assertArrayHasKey('owner_entity', $config);
        $this->assertNull($config['owner_entity']);
    }

    public function testWithConfigGiven()
    {
        $configuration = new Configuration();

        $processor = new Processor();
        $config = $processor->processConfiguration($configuration, array(
            'modera_config' => array(
                'owner_entity' => 'FooEntity',
            ),
        ));

        $this->assertArrayHasKey('owner_entity', $config);
        $this->assertEquals('FooEntity', $config['owner_entity']);
    }
}
