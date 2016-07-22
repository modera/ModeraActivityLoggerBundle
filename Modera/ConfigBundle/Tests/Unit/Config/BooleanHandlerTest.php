<?php

namespace Modera\ConfigBundle\Tests\Unit\Config;

use Modera\ConfigBundle\Config\BooleanHandler;
use Modera\ConfigBundle\Entity\ConfigurationEntry;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 */
class BooleanHandlerTest extends \PHPUnit_Framework_TestCase
{
    private $entry;
    /* @var BooleanHandler */
    private $handler;

    public function setUp()
    {
        $this->entry = $this->getMock(
            ConfigurationEntry::clazz(), array(), array(), '', null, false
        );
        $this->handler = new BooleanHandler();
    }

    public function testGetReadableValueWithNoConfigAnd1IsReturned()
    {
        $this->entry->expects($this->once())
                    ->method('getDenormalizedValue')
                    ->will($this->returnValue(1));

        $this->assertEquals('true', $this->handler->getReadableValue($this->entry));
    }

    public function testGetReadableValueWithNoConfigAnd0IsReturned()
    {
        $this->entry->expects($this->once())
            ->method('getDenormalizedValue')
            ->will($this->returnValue(0));

        $this->assertEquals('false', $this->handler->getReadableValue($this->entry));
    }

    private function createEntryWithServerConfig($clientValue, array $config)
    {
        $entry = $this->getMock(
            ConfigurationEntry::clazz(), array(), array(), '', null, false
        );

        $entry->expects($this->once())
              ->method('getDenormalizedValue')
              ->will($this->returnValue(1));

        $entry->expects($this->atLeastOnce())
              ->method('getServerHandlerConfig')
              ->will($this->returnValue($config));

        return $entry;
    }

    public function testGetReadableValueWithConfig()
    {
        $this->assertEquals(
            'Aye!',
            $this->handler->getReadableValue($this->createEntryWithServerConfig(1, array('true_text' => 'Aye!')))
        );

        $this->assertEquals(
            'Nein!',
            $this->handler->getReadableValue($this->createEntryWithServerConfig(0, array('true_text' => 'Nein!')))
        );
    }

    public function testGetValue()
    {
        $this->entry->expects($this->once())
             ->method('getDenormalizedValue')
             ->will($this->returnValue('serverValue'));

        $this->assertEquals('serverValue', $this->handler->getValue($this->entry));
    }

    public function testConvertToStorageValue()
    {
        $this->assertTrue(false === $this->handler->convertToStorageValue('xxx', $this->entry));
        $this->assertTrue(true === $this->handler->convertToStorageValue(1, $this->entry));
        $this->assertTrue(true === $this->handler->convertToStorageValue('1', $this->entry));
        $this->assertTrue(true === $this->handler->convertToStorageValue(true, $this->entry));
        $this->assertTrue(true === $this->handler->convertToStorageValue('true', $this->entry));
    }
}
