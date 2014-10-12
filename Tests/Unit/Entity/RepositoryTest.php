<?php

namespace Modera\FileRepositoryBundle\Tests\Unit\Entity;

use Modera\FileRepositoryBundle\Entity\Repository;
use Modera\FileRepositoryBundle\Exceptions\InvalidRepositoryConfig;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function test__construct()
    {
        $thrownException = null;

        try {
            new Repository('foo', array());
        } catch (InvalidRepositoryConfig $e) {
            $thrownException = $e;
        }

        $this->assertNotNull($thrownException);
        $this->assertEquals('filesystem', $thrownException->getMissingConfigurationKey());
        $this->assertEquals(array(), $thrownException->getConfig());
    }
}