<?php

namespace Modera\ConfigBundle\Tests\Unit\Config;

use Modera\ConfigBundle\Config\AsIsHandler;
use Modera\ConfigBundle\Entity\ConfigurationEntry;

/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
class AsIsHandlerTest extends \PHPUnit_Framework_TestCase
{
    private $entry;
    /* @var AsIsHandler */
    private $handler;

    public function setUp()
    {
        $this->entry = $this->getMock(
            ConfigurationEntry::clazz(), array(), array(), '', null, false
        );
        $this->handler = new AsIsHandler();
    }

    public function testGetReadableValue()
    {
        $this->entry->expects($this->once())
             ->method('getDenormalizedValue')
             ->will($this->returnValue('clientValue'));

        $this->assertEquals('clientValue', $this->handler->getReadableValue($this->entry));
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
        $this->assertEquals('xxx', $this->handler->convertToStorageValue('xxx', $this->entry));
    }
}
