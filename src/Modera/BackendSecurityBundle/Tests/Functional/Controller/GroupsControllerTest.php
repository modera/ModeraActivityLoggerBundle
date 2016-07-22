<?php

namespace Modera\BackendSecurityBundle\Tests\Functional\Controller;

use Doctrine\ORM\Tools\SchemaTool;
use Modera\BackendSecurityBundle\Controller\GroupsController;
use Modera\FoundationBundle\Testing\FunctionalTestCase;
use Modera\SecurityBundle\Entity\Group;
use Modera\SecurityBundle\Entity\Permission;
use Modera\SecurityBundle\Entity\PermissionCategory;
use Modera\SecurityBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * @author    Alex Plaksin <alex.plaksin@modera.net>
 * @copyright 2016 Modera Foundation
 */
class GroupsControllerTest extends FunctionalTestCase
{
    /**
     * @var SchemaTool
     */
    private static $schemaTool;

    private static $encoder;

    /**
     * @var User
     */
    private static $user;

    /**
     * @var GroupsController
     */
    private static $controller;

    /**
     * {@inheritdoc}
     */
    public static function doSetUpBeforeClass()
    {
        static::$schemaTool = new SchemaTool(static::$em);
        static::$schemaTool->dropSchema(static::getTablesMetadata());
        static::$schemaTool->createSchema(static::getTablesMetadata());

        static::$encoder = static::$container->get('security.encoder_factory');

        static::$user = new User();
        static::$user->setEmail('test@test.com');
        static::$user->setPassword(
            static::$encoder->getEncoder(static::$user)->encodePassword('1234', static::$user->getSalt())
        );
        static::$user->setUsername('testUser');

        $entityPermissionCategory = new PermissionCategory();
        $entityPermissionCategory->setName('backend_user');
        $entityPermissionCategory->setTechnicalName('backend_user');
        static::$em->persist($entityPermissionCategory);

        $entityPermission = new Permission();
        $entityPermission->setRoleName('IS_AUTHENTICATED_FULLY');
        $entityPermission->setDescription('IS_AUTHENTICATED_FULLY');
        $entityPermission->setName('IS_AUTHENTICATED_FULLY');
        $entityPermission->setCategory($entityPermissionCategory);

        $entityPermission2 = new Permission();
        $entityPermission2->setRoleName('ROLE_MANAGE_PERMISSIONS');
        $entityPermission2->setDescription('ROLE_MANAGE_PERMISSIONS');
        $entityPermission2->setName('ROLE_MANAGE_PERMISSIONS');
        $entityPermission2->setCategory($entityPermissionCategory);

        $entityPermission3 = new Permission();
        $entityPermission3->setRoleName('ROLE_ACCESS_BACKEND_TOOLS_SECURITY_SECTION');
        $entityPermission3->setDescription('ROLE_ACCESS_BACKEND_TOOLS_SECURITY_SECTION');
        $entityPermission3->setName('ROLE_ACCESS_BACKEND_TOOLS_SECURITY_SECTION');
        $entityPermission3->setCategory($entityPermissionCategory);

        static::$em->persist($entityPermission);
        static::$em->persist($entityPermission2);
        static::$em->persist($entityPermission3);
        static::$em->flush();

        $group = new Group();
        $group->setRefName('BACKEND-USER');
        $group->setName('backend-user');
        $group->addPermission($entityPermission);
        $group->addPermission($entityPermission2);
        $group->addPermission($entityPermission3);

        static::$user->addToGroup($group);

        static::$em->persist($group);
        static::$em->persist(static::$user);

        static::$em->flush();

        static::$controller = new GroupsController();
        static::$controller->setContainer(static::$container);
    }

    public function doSetUp()
    {
        $token = new UsernamePasswordToken(static::$user, '1234', 'secured_area');

        static::$container->get('security.token_storage')->setToken($token);
    }

    /**
     * {@inheritdoc}
     */
    public static function doTearDownAfterClass()
    {
        static::$schemaTool->dropSchema(static::getTablesMetadata());
    }

    /**
     * Simple correct behavior group create.
     *
     * @return null|object
     */
    public function testCreateAction()
    {
        $beforeGroupsCount = count(static::$em->getRepository(Group::clazz())->findAll());

        $params = array(
            'record' => array(
                'id' => '',
                'name' => 'testName',
                'refName' => 'testRefName',
            ),
        );

        $result = static::$controller->createAction($params);

        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('created_models', $result);
        $this->assertArrayHasKey('modera.security_bundle.group', $result['created_models']);
        $this->assertCount(1, $result['created_models']['modera.security_bundle.group']);

        $afterGroupsCount = count(static::$em->getRepository(Group::clazz())->findAll());
        $this->assertEquals($beforeGroupsCount + 1, $afterGroupsCount);

        $createdGroup = static::$em->getRepository(Group::clazz())->find($result['created_models']['modera.security_bundle.group'][0]);

        $this->assertEquals('testName', $createdGroup->getName());
        $this->assertEquals('TESTREFNAME', $createdGroup->getRefName());

        return $createdGroup;
    }

    /**
     * @depends testCreateAction
     */
    public function testCreateAction_EmptyName()
    {
        $params = array(
            'record' => array(
                'id' => '',
                'name' => '',
                'refName' => '',
            ),
        );

        $result = static::$controller->createAction($params);

        $this->assertArrayHasKey('success', $result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('field_errors', $result);
        $this->assertCount(1, $result['field_errors']);
        $this->assertArrayHasKey('name', $result['field_errors']);
    }

    /**
     * @depends testCreateAction
     */
    public function testCreateAction_DuplicatedRefName()
    {
        $params = array(
            'record' => array(
                'id' => '',
                'name' => 'testName2',
                'refName' => 'testRefName',
            ),
        );

        $result = static::$controller->createAction($params);

        $this->assertArrayHasKey('success', $result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('field_errors', $result);
        $this->assertCount(1, $result['field_errors']);
        $this->assertArrayHasKey('refName', $result['field_errors']);
    }

    /**
     * @depends testCreateAction
     */
    public function testUpdateAction(Group $group)
    {
        $params = array(
            'record' => array(
                'id' => $group->getId(),
                'name' => 'testNameUpdated',
                'refName' => 'testRefNameUpdated',
            ),
        );

        $result = static::$controller->updateAction($params);

        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('updated_models', $result);
        $this->assertArrayHasKey('modera.security_bundle.group', $result['updated_models']);
        $this->assertCount(1, $result['updated_models']['modera.security_bundle.group']);
        $this->assertEquals($group->getId(), $result['updated_models']['modera.security_bundle.group'][0]);

        /** @var Group $groupFromDb */
        $groupFromDb = static::$em->find(Group::clazz(), $group->getId());

        $this->assertEquals('testNameUpdated', $groupFromDb->getName());
        $this->assertEquals('TESTREFNAMEUPDATED', $groupFromDb->getRefName());
    }

    /**
     * @depends testCreateAction
     * @depends testUpdateAction
     *
     * @param Group $group
     *
     * @return Group
     */
    public function testUpdateAction_SameRefName(Group $group)
    {
        $this->assertEquals('TESTREFNAMEUPDATED', $group->getRefName());

        $params = array(
            'record' => array(
                'id' => $group->getId(),
                'name' => 'newTestName',
                'refName' => 'testRefNameUpdated',
            ),
        );

        $result = static::$controller->updateAction($params);

        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('updated_models', $result);
        $this->assertArrayHasKey('modera.security_bundle.group', $result['updated_models']);
        $this->assertCount(1, $result['updated_models']['modera.security_bundle.group']);
        $this->assertEquals($group->getId(), $result['updated_models']['modera.security_bundle.group'][0]);

        /** @var Group $groupFromDb */
        $groupFromDb = static::$em->find(Group::clazz(), $group->getId());

        $this->assertEquals('newTestName', $groupFromDb->getName());
        $this->assertEquals('TESTREFNAMEUPDATED', $groupFromDb->getRefName());

        return $groupFromDb;
    }

    /**
     * @depends testUpdateAction_SameRefName
     *
     * @param Group $group
     */
    public function testUpdateAction_ExistingRefNameUse(Group $group)
    {
        $newGroup = new Group();
        $newGroup->setName('brandNewGroup');
        $newGroup->setRefName('brandNewRefName');

        static::$em->persist($newGroup);
        static::$em->flush();

        $this->assertEquals('TESTREFNAMEUPDATED', $group->getRefName());

        $params = array(
            'record' => array(
                'id' => $group->getId(),
                'name' => 'newTestNameExistingRef',
                'refName' => 'brandNewRefName',
            ),
        );

        $result = static::$controller->updateAction($params);

        $this->assertArrayHasKey('success', $result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('field_errors', $result);
        $this->assertCount(1, $result['field_errors']);
        $this->assertArrayHasKey('refName', $result['field_errors']);
    }

    /**
     * @return array
     */
    private static function getTablesClasses()
    {
        return array(
            Permission::clazz(),
            PermissionCategory::clazz(),
            User::clazz(),
            Group::clazz(),
        );
    }

    private static function getTablesMetadata()
    {
        $metaData = array();

        foreach (static::getTablesClasses() as $class) {
            $metaData[] = static::$em->getClassMetadata($class);
        }

        return $metaData;
    }

    /**
     * {@inheritdoc}
     */
    protected static function getIsolationLevel()
    {
        return self::IM_CLASS;
    }
}
