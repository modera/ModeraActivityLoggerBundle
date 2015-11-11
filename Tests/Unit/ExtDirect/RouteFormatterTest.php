<?php

namespace Modera\MJRSecurityIntegrationBundle\Tests\Unit\ExtDirect;

use Modera\MJRSecurityIntegrationBundle\ExtDirect\RouteFormatter;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2015 Modera Foundation
 */
class RouteFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function testFormat()
    {
        $f = new RouteFormatter('/foo');

        $this->assertEquals('/foo/bar', $f->format('/bar'));
        $this->assertEquals('/foo/bar', $f->format('bar'));

        $f = new RouteFormatter('foo/');

        $this->assertEquals('foo/bar', $f->format('/bar'));
        $this->assertEquals('foo/bar', $f->format('bar'));
    }
}
