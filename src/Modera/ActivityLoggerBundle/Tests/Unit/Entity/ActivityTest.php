<?php

namespace Modera\ActivityLoggerBundle\Tests\Unit\Entity;

use Modera\ActivityLoggerBundle\Entity\Activity;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ActivityTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $a = new Activity();

        $this->assertInstanceOf('DateTime', $a->getCreatedAt());
    }
} 