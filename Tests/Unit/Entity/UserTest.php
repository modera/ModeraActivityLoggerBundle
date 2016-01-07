<?php

namespace Modera\SecurityBundle\Tests\Unit\Entity;

use Modera\SecurityBundle\Entity\Group;
use Modera\SecurityBundle\Entity\Permission;
use Modera\SecurityBundle\Entity\User;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2016 Modera Foundation
 */
class UserTest extends \PHPUnit_Framework_TestCase
{
    public function testGetRawRoles()
    {
        $user = new User();

        $this->assertEquals(0, count($user->getRawRoles()));

        // ---

        $groupPermission = \Phake::mock(Permission::clazz());
        $userPermission = \Phake::mock(Permission::clazz());

        $group = \Phake::mock(Group::clazz());
        \Phake::when($group)
            ->getPermissions()
            ->thenReturn([$groupPermission])
        ;

        $user->addPermission($userPermission);
        $user->setGroups([$group]);

        $userRoles = $user->getRawRoles();

        $this->assertEquals(2, count($userRoles));
        $this->assertSame($groupPermission, $userRoles[0]);
        $this->assertSame($userPermission, $userRoles[1]);
    }

    public function testGetRoles()
    {
        $user = new User();

        $this->assertEquals(['ROLE_USER'], $user->getRoles());

        // ---

        $rootUserHandler = \Phake::mock('Modera\SecurityBundle\RootUserHandling\RootUserHandlerInterface');
        \Phake::when($rootUserHandler)
            ->isRootUser($user)
            ->thenReturn(false)
        ;
        \Phake::when($rootUserHandler)
            ->getRoles()
            ->thenReturn(['ROLE_FOO', 'ROLE_BAR'])
        ;

        $user->init($rootUserHandler);
        $this->assertEquals(['ROLE_USER'], $user->getRoles());

        // ---

        \Phake::when($rootUserHandler)
            ->isRootUser($user)
            ->thenReturn(true)
        ;

        $this->assertEquals(['ROLE_FOO', 'ROLE_BAR'], $user->getRoles());
    }
}
