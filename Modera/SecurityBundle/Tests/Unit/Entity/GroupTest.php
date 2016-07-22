<?php

namespace Modera\SecurityBundle\Tests\Unit\Entity;

use Modera\SecurityBundle\Entity\Group;

/**
 * @author    Alex Plaksin <alex.plaksin@modera.net>
 * @copyright 2016 Modera Foundation
 */
class GroupTest extends \PHPUnit_Framework_TestCase
{
    public function testNormalizeRefNameString()
    {
        $this->assertEquals('QWERTY', Group::normalizeRefNameString('qwerty'));
        $this->assertEquals('QT', Group::normalizeRefNameString('!1q34%^&* ~@342T'));
    }
}
