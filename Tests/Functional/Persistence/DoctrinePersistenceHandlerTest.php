<?php

namespace Modera\ServerCrudBundle\Tests\Functional\Persistence;

require_once __DIR__.'/../entities.php';

use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Modera\FoundationBundle\Testing\FunctionalTestCase;
use Modera\ServerCrudBundle\Persistence\DoctrinePersistenceHandler;
use Modera\ServerCrudBundle\Persistence\OperationResult;
use Sli\AuxBundle\Util\Toolkit;
use Modera\ServerCrudBundle\Tests\Functional\DummyUser;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class DoctrinePersistenceHandlerTest extends FunctionalTestCase
{
    public static function doSetUpBeforeClass()
    {
        $driver = new AnnotationDriver(
            self::$kernel->getContainer()->get('annotation_reader'),
            array(__DIR__)
        );

        Toolkit::addMetadataDriverForEntityManager(self::$em, $driver, 'Modera\ServerCrudBundle\Tests\Functional');
        Toolkit::createTableFoEntity(self::$em, DummyUser::clazz());
    }

    public static function doTearDownAfterClass()
    {
        Toolkit::dropTableForEntity(self::$em, DummyUser::clazz());
    }

    /**
     * @return DoctrinePersistenceHandler
     */
    private function getHandler()
    {
        return self::$container->get('modera_server_crud.persistence.default_handler');
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

    public function testUpdate()
    {
        $user = new DummyUser();
        $user->firstname = 'Vassily';
        $user->lastname = 'Pupkin';

        self::$em->persist($user);
        self::$em->flush();

        $user->lastname = 'Blah';

        $result = $this->getHandler()->update($user);

        $updatedEntities = $result->getUpdatedEntities();
        $this->assertEquals(1, count($updatedEntities));
        $this->assertArrayHasKey(0, $updatedEntities);
        $this->assertArrayHasKey('entity_class', $updatedEntities[0]);
        $this->assertEquals(DummyUser::clazz(), $updatedEntities[0]['entity_class']);
        $this->assertArrayHasKey('id', $updatedEntities[0]);
        $this->assertEquals($user->id, $updatedEntities[0]['id']);
    }

    /**
     * @return DummyUser[]
     */
    private function loadSomeData()
    {
        $users = array();

        for ($i = 0; $i < 10; ++$i) {
            $user = new DummyUser();
            $user->firstname = 'Vassily '.$i;
            $user->lastname = 'Pupkin '.$i;

            self::$em->persist($user);

            $users[] = $user;
        }
        self::$em->flush();

        return $users;
    }

    public function testQuery()
    {
        $this->loadSomeData();

        /* @var DummyUser[] $result */
        $result = $this->getHandler()->query(DummyUser::clazz(), array(
            'limit' => 5,
            'page' => 2,
            'start' => 0,
        ));

        $this->assertTrue(is_array($result));
        $this->assertEquals(5, count($result));
        $this->assertEquals(8, $result[0]->id);
    }

    public function testGetCount()
    {
        $query = array(
            'limit' => 5,
            'page' => 2,
            'start' => 0,
        );

        $this->assertEquals(0, $this->getHandler()->getCount(DummyUser::clazz(), $query));

        $this->loadSomeData();

        $this->assertEquals(10, $this->getHandler()->getCount(DummyUser::clazz(), $query));
    }

    public function testRemove()
    {
        $users = $this->loadSomeData();

        $result = $this->getHandler()->remove($users);

        $this->assertInstanceOf(OperationResult::clazz(), $result);
        $this->assertEquals(10, count($result->getRemovedEntities()));

        foreach ($result->getRemovedEntities() as $entry) {
            $this->assertNull(self::$em->getRepository($entry['entity_class'])->find($entry['id']));
        }
    }

    public function testResolveEntityPrimaryKeyFields()
    {
        $fields = $this->getHandler()->resolveEntityPrimaryKeyFields(DummyUser::clazz());

        $this->assertTrue(is_array($fields));
        $this->assertEquals(1, count($fields));
        $this->assertTrue(in_array('id', $fields));
    }

    public function testUpdateBatch()
    {
        $users = $this->loadSomeData();

        foreach ($users as $i => $user) {
            $user->firstname .= ''.$i;
        }

        $result = $this->getHandler()->updateBatch($users);

        $updatedEntities = $result->getUpdatedEntities();
        $this->assertEquals(count($users), count($updatedEntities));

        self::$em->clear();

        $ids = array();
        foreach ($users as $user) {
            $ids[] = $user->id;
        }

        foreach ($users as $user) {
            $this->assertTrue(in_array($user->id, $ids));

            $dbUser = self::$em->find(DummyUser::clazz(), $user->id);

            $this->assertEquals($user->firstname, $dbUser->firstname);
        }
    }
}
