<?php

namespace Modera\SecurityBundle\Tests\Unit\Service;

use Modera\SecurityBundle\Entity\User;
use Modera\SecurityBundle\Service\UserService;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class UserServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException RuntimeException
     */
    public function testRemove()
    {
        $em = \Phake::mock('Doctrine\ORM\EntityManager');
        $rootUserHandler = \Phake::mock('Modera\SecurityBundle\RootUserHandling\RootUserHandlerInterface');

        $user = \Phake::mock(User::clazz());

        \Phake::when($rootUserHandler)->isRootUser($user)->thenReturn(true);

        $service = new UserService($em, $rootUserHandler);
        $service->remove($user);
    }

    public function testFind()
    {
        $em = \Phake::mock('Doctrine\ORM\EntityManager');
        $repo = \Phake::mock('Doctrine\Common\Persistence\ObjectRepository');
        $rootUserHandler = \Phake::mock('Modera\SecurityBundle\RootUserHandling\RootUserHandlerInterface');
        $service = new UserService($em, $rootUserHandler);

        \Phake::when($em)->getRepository(User::clazz())->thenReturn($repo);

        $user1 = \Phake::mock(User::clazz());
        \Phake::when($user1)->getId()->thenReturn(1);
        \Phake::when($user1)->getGender()->thenReturn(User::GENDER_MALE);
        $user2 = \Phake::mock(User::clazz());
        \Phake::when($user2)->getId()->thenReturn(2);
        \Phake::when($user2)->getGender()->thenReturn(User::GENDER_FEMALE);
        $user3 = \Phake::mock(User::clazz());
        \Phake::when($user3)->getId()->thenReturn(3);
        \Phake::when($user3)->getGender()->thenReturn(User::GENDER_MALE);

        \Phake::when($repo)->findOneBy(array('id' => 0))->thenReturn(null);
        $this->assertNull($service->findUserBy('id', 0));

        \Phake::when($repo)->findOneBy(array('id' => 1))->thenReturn($user1);
        $this->assertEquals($user1, $service->findUserBy('id', 1));

        \Phake::when($repo)->findBy(array('gender' => User::GENDER_MALE))->thenReturn(array($user1, $user3));
        $this->assertEquals(array($user1, $user3), $service->findUsersBy('gender', User::GENDER_MALE));
    }

    public function testRootUser()
    {
        $em = \Phake::mock('Doctrine\ORM\EntityManager');
        $rootUserHandler = \Phake::mock('Modera\SecurityBundle\RootUserHandling\RootUserHandlerInterface');
        $service = new UserService($em, $rootUserHandler);

        $user = \Phake::mock(User::clazz());
        \Phake::when($rootUserHandler)->getUser()->thenReturn($user);
        \Phake::when($rootUserHandler)->isRootUser($user)->thenReturn(true);

        $this->assertEquals($user, $service->getRootUser());
        $this->assertTrue($service->isRootUser($user));
    }
}
