<?php

namespace Modera\ConfigBundle\Tests\Unit\Entity;

use Modera\ConfigBundle\Entity\ConfigurationEntry;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2016 Modera Foundation
 */
class ConfigurationEntryTest extends \PHPUnit_Framework_TestCase
{
    public function testSetName()
    {
        $ce = new ConfigurationEntry('foo');

        $this->assertEquals('foo', $ce->getName());

        $ce->setName('bar');

        $this->assertEquals('bar', $ce->getName());
    }
}
