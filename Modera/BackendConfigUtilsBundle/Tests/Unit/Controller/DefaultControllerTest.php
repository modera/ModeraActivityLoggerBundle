<?php

namespace Modera\BackendConfigUtilsBundle\Tests\Unit\Controller;

use Modera\BackendConfigUtilsBundle\Controller\DefaultController;
use Modera\ConfigBundle\Entity\ConfigurationEntry;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2016 Modera Foundation
 */
class DefaultControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DefaultController
     */
    private $c;

    public function setUp()
    {
        $this->c = new DefaultController();
    }

    public function testGetConfigHydration()
    {
        $config = $this->c->getConfig();

        $this->assertTrue(is_array($config));
        $this->assertTrue(isset($config['hydration']['groups']['list']));

        $hydrator = $config['hydration']['groups']['list'];

        $this->assertTrue(is_callable($hydrator));

        $entry = \Phake::mock(ConfigurationEntry::clazz());

        $this->teachEntry($entry, 'getId', 'foo_id');
        $this->teachEntry($entry, 'getName', 'foo_name');
        $this->teachEntry($entry, 'getReadableName', 'foo_rn');
        $this->teachEntry($entry, 'getReadableValue', 'foo_rv');
        $this->teachEntry($entry, 'getValue', 'foo_v');
        $this->teachEntry($entry, 'isReadOnly', 'foo_ro');
        $this->teachEntry($entry, 'getClientHandlerConfig', 'foo_ch');

        $result = $hydrator($entry);

        $this->assertTrue(is_array($result));
        foreach (['id', 'name', 'readableName', 'readableValue', 'value', 'isReadOnly', 'editorConfig'] as $key) {
            $this->assertArrayHasKey($key, $result);
        }

        $this->assertEquals('foo_id', $result['id']);
        $this->assertEquals('foo_name', $result['name']);
        $this->assertEquals('foo_rn', $result['readableName']);
        $this->assertEquals('foo_rv', $result['readableValue']);
        $this->assertEquals('foo_ro', $result['isReadOnly']);
        $this->assertEquals('foo_ch', $result['editorConfig']);
    }

    public function testGetConfigMapDataOnUpdate()
    {
        $config = $this->c->getConfig();

        $this->assertTrue(is_array($config));
        $this->assertArrayHasKey('map_data_on_update', $config);
        $this->assertTrue(is_callable($config['map_data_on_update']));

        $mapper = $config['map_data_on_update'];

        $entry = \Phake::mock(ConfigurationEntry::clazz());
        $this->teachEntry($entry, 'isReadOnly', true);
    }

    private function teachEntry($mock, $methodName, $returnValue)
    {
        \Phake::when($mock)
            ->$methodName()
            ->thenReturn($returnValue)
        ;
    }
}
