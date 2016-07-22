<?php

namespace Modera\BackendSecurityBundle\Tests\Functional\Controller;

use Doctrine\ORM\Tools\SchemaTool;
use Modera\ActivityLoggerBundle\Entity\Activity;
use Modera\BackendSecurityBundle\Controller\UsersController;
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
class UsersControllerTest extends FunctionalTestCase
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

        $entityPermission4 = new Permission();
        $entityPermission4->setRoleName('ROLE_MANAGE_USER_PROFILES');
        $entityPermission4->setDescription('ROLE_MANAGE_USER_PROFILES');
        $entityPermission4->setName('ROLE_MANAGE_USER_PROFILES');
        $entityPermission4->setCategory($entityPermissionCategory);

        static::$em->persist($entityPermission);
        static::$em->persist($entityPermission2);
        static::$em->persist($entityPermission3);
        static::$em->persist($entityPermission4);
        static::$em->flush();

        $group = new Group();
        $group->setRefName('BACKEND-USER');
        $group->setName('backend-user');
        $group->addPermission($entityPermission);
        $group->addPermission($entityPermission2);
        $group->addPermission($entityPermission3);
        $group->addPermission($entityPermission4);

        static::$user->addToGroup($group);

        static::$em->persist($group);
        static::$em->persist(static::$user);

        static::$em->flush();
    }

    public function testMainFormHydration()
    {
        $this->assertNotNull(static::$user);

        $user = static::$em->find(User::clazz(), static::$user->getId());

        $this->assertNotNull($user);

        $this->assertEquals(static::$user, $user);

        $userMeta = array('rootElement' => array('subElement' => 'subElementValue', 'subElement2' => 'subElementValue2'),
        );
        static::$user->setMeta($userMeta);

        static::$em->flush();

        $controller = $this->getController();

        $response = $controller->listAction(
            array(
                'hydration' => array('profile' => 'list'),
                'page' => 1,
                'start' => 0,
                'limit' => 25,
            )
        );

        $this->assertArrayHasKey('success', $response);
        $this->assertArrayHasKey('total', $response);
        $this->assertArrayHasKey('items', $response);

        //assuming this is first test in file
        $this->assertGreaterThanOrEqual(1, count($response['items']));

        $hydratedUser = $response['items'][0];
        $this->assertArrayHasKey('id', $hydratedUser);
        $this->assertArrayHasKey('username', $hydratedUser);
        $this->assertArrayHasKey('email', $hydratedUser);
        $this->assertArrayHasKey('firstName', $hydratedUser);
        $this->assertArrayHasKey('lastName', $hydratedUser);
        $this->assertArrayHasKey('middleName', $hydratedUser);
        $this->assertArrayHasKey('state', $hydratedUser);
        $this->assertArrayHasKey('groups', $hydratedUser);
        $this->assertCount(1, $hydratedUser['groups']);
        $this->assertArrayHasKey('meta', $hydratedUser);
        $this->assertEquals($userMeta, $hydratedUser['meta']);
    }

    public function testIsMetaInfoStoredOnCreation()
    {
        $userMeta = array(
            'modera_backend_service_account_plugin' => array(
                'isService' => true,
                'password' => '1234',
            ),
        );

        $params = array(
            'record' => array(
                'id' => '',
                'lastName' => 'Full Display Name',
                'username' => 'serviceAccount',
                'email' => 'test1@test.com',
                'meta' => $userMeta,
            ),
        );

        $controller = $this->getController();

        $response = $controller->createAction($params);

        $this->assertTrue($response['success']);
        /*
         * @var User[]
         */
        $userList = static::$em->getRepository(User::clazz())->findAll();

        $lastUser = array_pop($userList);

        $this->assertEquals('test1@test.com', $lastUser->getEmail());
        $this->assertEquals($userMeta, $lastUser->getMeta());

        return $lastUser;
    }

    public function testIsMetaInfoStoredOnCreation_NoMeta()
    {
        $params = array(
            'record' => array(
                'id' => '',
                'lastName' => 'Full Display Name',
                'username' => 'serviceAccount_NoMeta',
                'email' => 'test3@test.com',
                'meta' => '',
            ),
        );

        $controller = $this->getController();

        $response = $controller->createAction($params);

        $this->assertTrue($response['success']);
        /*
         * @var User[]
         */
        $userList = static::$em->getRepository(User::clazz())->findAll();

        $lastUser = array_pop($userList);

        $this->assertEquals('test3@test.com', $lastUser->getEmail());
        $this->assertEquals(array(), $lastUser->getMeta());

        return $lastUser;
    }

    /**
     * @depends testIsMetaInfoStoredOnCreation
     *
     * @param User $user
     */
    public function testIsMetaInfoStoredOnUpdate(User $user)
    {
        $userMeta = array(
            'modera_backend_service_account_plugin' => array(
                'isService' => true,
                'password' => '5678',
            ),
        );

        $params = array(
            'record' => array(
                'id' => $user->getId(),
                'lastName' => $user->getLastName(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'meta' => $userMeta,
            ),
        );

        $controller = $this->getController();

        $response = $controller->updateAction($params);

        $this->assertTrue($response['success']);
        /*
         * @var User[] $userList
         */
        $userFromDb = static::$em->getRepository(User::clazz())->find($user->getId());

        $this->assertEquals('test1@test.com', $userFromDb->getEmail());
        $this->assertEquals($userMeta, $userFromDb->getMeta());
    }

    /**
     * @depends testIsMetaInfoStoredOnCreation
     *
     * @param User $user
     */
    public function testIsMetaStoredOnUpdate_NoMeta(User $user)
    {
        $params = array(
            'record' => array(
                'id' => $user->getId(),
                'lastName' => $user->getLastName(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'meta' => '',
            ),
        );

        $controller = $this->getController();

        $response = $controller->updateAction($params);

        $this->assertTrue($response['success']);
        /*
         * @var User[]
         */
        $userList = static::$em->getRepository(User::clazz())->findAll();

        $lastUser = array_pop($userList);

        $this->assertEquals('test1@test.com', $user->getEmail());
        $this->assertTrue(is_array($user->getMeta()));
        $this->assertCount(0, $user->getMeta());
    }

    public function doSetUp()
    {
        $token = new UsernamePasswordToken(static::$user, '1234', 'secured_area');

        static::$container->get('security.token_storage')->setToken($token);
    }

    /**
     * @return UsersController
     */
    private function getController()
    {
        $controller = new UsersController();
        $controller->setContainer(static::$container);

        return $controller;
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
            Activity::clazz(),
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
