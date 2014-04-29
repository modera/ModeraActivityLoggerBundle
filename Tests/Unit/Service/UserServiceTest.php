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
} 