<?php

namespace Modera\SecurityBundle\Tests\Unit\RootUserHandler;

use Doctrine\ORM\Query;
use Modera\SecurityBundle\DependencyInjection\ModeraSecurityExtension;
use Modera\SecurityBundle\Entity\Permission;
use Modera\SecurityBundle\Entity\User;
use Modera\SecurityBundle\RootUserHandling\SemanticConfigRootUserHandler;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class SemanticConfigRootUserHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testIsRootUser()
    {
        $container = \Phake::mock('Symfony\Component\DependencyInjection\ContainerInterface');
        $bundleConfig = array(
            'root_user' => array(
                'query' => array('dat', 'is', 'query'),
            ),
        );

        $em = \Phake::mock('Doctrine\ORM\EntityManager');

        \Phake::when($container)->getParameter(ModeraSecurityExtension::CONFIG_KEY)->thenReturn($bundleConfig);
        \Phake::when($container)->get('doctrine.orm.entity_manager')->thenReturn($em);

        $handler = new SemanticConfigRootUserHandler($container);

        $anonymousUser = \Phake::mock(User::clazz());

        $dbUser = \Phake::mock(User::clazz());
        \Phake::when($dbUser)->isEqualTo($anonymousUser)->thenReturn('dat is true');

        $userRepository = \Phake::mock('Doctrine\Common\Persistence\ObjectRepository');
        \Phake::when($userRepository)->findOneBy($bundleConfig['root_user']['query'])->thenReturn($dbUser);
        \Phake::when($em)->getRepository(User::clazz())->thenReturn($userRepository);

        $this->assertEquals('dat is true', $handler->isRootUser($anonymousUser));
    }

    public function testGetRolesWithAsterisk()
    {
        $em = \Phake::mock('Doctrine\ORM\EntityManager');
        $container = \Phake::mock('Symfony\Component\DependencyInjection\ContainerInterface');
        $bundleConfig = array(
            'root_user' => array(
                'roles' => '*',
            ),
        );

        $databaseRoles = array(
            array('roleName' => 'FOO_ROLE'),
            array('roleName' => 'BAR_ROLE'),
        );

        \Phake::when($container)->get('doctrine.orm.entity_manager')->thenReturn($em);
        \Phake::when($container)->getParameter(ModeraSecurityExtension::CONFIG_KEY)->thenReturn($bundleConfig);
        $query = \Phake::mock('Doctrine\ORM\AbstractQuery');
        \Phake::when($em)->createQuery(sprintf('SELECT e.roleName FROM %s e', Permission::clazz()))->thenReturn($query);
        \Phake::when($query)->getResult(Query::HYDRATE_SCALAR)->thenReturn($databaseRoles);

        $handler = new SemanticConfigRootUserHandler($container);

        $this->assertSame(array('FOO_ROLE', 'BAR_ROLE'), $handler->getRoles());
    }

    public function testGetRolesAsArray()
    {
        $container = \Phake::mock('Symfony\Component\DependencyInjection\ContainerInterface');
        $bundleConfig = array(
            'root_user' => array(
                'roles' => array('FOO_ROLE', 'BAR_ROLE'),
            ),
        );

        \Phake::when($container)->getParameter(ModeraSecurityExtension::CONFIG_KEY)->thenReturn($bundleConfig);

        $handler = new SemanticConfigRootUserHandler($container);

        $this->assertSame($bundleConfig['root_user']['roles'], $handler->getRoles());
    }

    /**
     * @expectedException RuntimeException
     */
    public function testGetRolesNeitherStringNorArrayDefined()
    {
        $container = \Phake::mock('Symfony\Component\DependencyInjection\ContainerInterface');
        $bundleConfig = array(
            'root_user' => array(
                'roles' => new \stdClass(),
            ),
        );

        \Phake::when($container)->getParameter(ModeraSecurityExtension::CONFIG_KEY)->thenReturn($bundleConfig);

        $handler = new SemanticConfigRootUserHandler($container);

        $handler->getRoles();
    }
}
