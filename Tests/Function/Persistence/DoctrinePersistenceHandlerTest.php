<?php

namespace Modera\AdminGeneratorBundle\Tests\Functional\Persistence;

require_once __DIR__ . '/../entities.php';

use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Modera\AdminGeneratorBundle\Persistence\DoctrinePersistenceHandler;
use Modera\AdminGeneratorBundle\Persistence\OperationResult;
use Modera\FoundationBundle\Testing\IntegrationTestCase;
use Sli\AuxBundle\Util\Toolkit;
use Modera\AdminGeneratorBundle\Tests\Functional\DummyUser;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class DoctrinePersistenceHandlerTest extends IntegrationTestCase
{
    static public function doSetUpBeforeClass()
    {
        $driver = new AnnotationDriver(
            self::$kernel->getContainer()->get('annotation_reader'),
            array(__DIR__)
        );

        Toolkit::addMetadataDriverForEntityManager(self::$em, $driver, 'Modera\AdminGeneratorBundle\Tests\Functional');
        Toolkit::createTableFoEntity(self::$em, DummyUser::clazz());
    }

    static public function doTearDownAfterClass()
    {
        Toolkit::dropTableForEntity(self::$em, DummyUser::clazz());
    }

    /**
     * @return DoctrinePersistenceHandler
     */
    private function getHandler()
    {
        return self::$container->get('modera_admin_generator.persistence.default_handler');
    }

    public function testServiceExistence()
    {
        $this->assertInstanceOf(DoctrinePersistenceHandler::clazz(), $this->getHandler());
    }

    public function testSave()
    {
        $repository = self::$em->getRepository(DummyUser::clazz());

        $this->assertEquals(0, count($repository->findAll()));

        $user = new DummyUser();
        $user->firstname = 'Vassily';
        $user->lastname = 'Pupkin';

        $result = $this->getHandler()->save($user);

        $this->assertInstanceOf(OperationResult::clazz(), $result);
        $this->assertEquals(1, count($result->getCreatedEntities()));

        $this->assertNotNull($user->id);

        $this->assertEquals(1, count($repository->findAll()));

        $fetchedUser = $repository->find($user->getId());

        $this->assertInstanceOf(DummyUser::clazz(), $fetchedUser);
        $this->assertSame($user->firstname, $fetchedUser->firstname);
        $this->assertSame($user->lastname, $fetchedUser->lastname);
    }
}