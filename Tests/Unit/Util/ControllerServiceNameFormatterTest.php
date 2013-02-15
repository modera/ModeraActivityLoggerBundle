<?php

namespace Modera\FoundationBundle\Tests\Unit\Util;

use Modera\FoundationBundle\Util\ControllerServiceNameFormatter;

/**
 * @copyright 2013 Modera Foundation
 * @author Sergei Lissovski <sergei.lissovski@modera.net>
 */ 
class ControllerServiceNameFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function testFormatPrefix()
    {
        $f = new ControllerServiceNameFormatter();
        $this->assertEquals('mycompany.foo', $f->formatPrefix('MyCompany\\FooBundle\\Controller\\Y'));
        $this->assertEquals('mycompany.foo', $f->formatPrefix('MyCompany\\Bundle\\FooBundle\\Controller\\X'));
        $this->assertEquals('mycompany.foo', $f->formatPrefix('MyCompany\\Bundle\\FooBundle\\Controller\\Sub\\Z'));
    }

    /**
     * @expectedException RuntimeException
     */
    public function testFormatPrefixBadNamespace()
    {
        $f = new ControllerServiceNameFormatter();
        $f->formatPrefix('MyCompany\\FooBundle\\Y');
    }
}
