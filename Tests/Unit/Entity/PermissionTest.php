<?php

namespace Modera\SecurityBundle\Tests\Unit\Entity;

use Modera\SecurityBundle\Model\Permission;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class PermissionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorAndGetters()
    {
        $p = new Permission('foo name', 'FOO_ROLE', 'foo_category', 'bar description');

        $this->assertEquals('foo name', $p->getName());
        $this->assertEquals('FOO_ROLE', $p->getRole());
        $this->assertEquals('foo_category', $p->getCategory());
        $this->assertEquals('bar description', $p->getDescription());
    }
} 