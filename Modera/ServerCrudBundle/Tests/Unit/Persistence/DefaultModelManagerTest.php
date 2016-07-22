<?php

namespace Modera\ServerCrudBundle\Tests\Unit\Persistence;

use Modera\ServerCrudBundle\Persistence\DefaultModelManager;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class DefaultModelManagerTest extends \PHPUnit_Framework_TestCase
{
    /* @var DefaultModelManager $mgr */
    private $mgr;

    public function setUp()
    {
        $this->mgr = new DefaultModelManager();
    }

    public function testGenerateModelIdFromEntityClass()
    {
        $this->assertEquals(
            'modera.admin_generator.foo',
            $this->mgr->generateModelIdFromEntityClass('Modera\AdminGenerator\Entity\Foo')
        );

        $this->assertEquals(
            'modera.admin_generator.sub.bar',
            $this->mgr->generateModelIdFromEntityClass('Modera\AdminGenerator\Entity\Sub\Bar')
        );
    }

    public function testGenerateEntityClassFromModelId()
    {
        $this->assertEquals(
            'Modera\AdminGeneratorBundle\Entity\Foo',
            $this->mgr->generateEntityClassFromModelId('modera.admin_generator.foo')
        );

        $this->assertEquals(
            'Modera\AdminGeneratorBundle\Entity\Sub\Bar',
            $this->mgr->generateEntityClassFromModelId('modera.admin_generator.sub.bar')
        );
    }
}
