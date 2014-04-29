<?php

namespace Modera\SecurityBundle\Tests\Unit\Security;

use Modera\SecurityBundle\Entity\User;
use Modera\SecurityBundle\Security\AuthenticatedTokenFactory;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class AuthenticatedTokenFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testSuccessfulAuthentication()
    {
        $encoder = \Phake::mock('Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface');
        $encoderFactory = \Phake::mock('Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface');
        $anonymousToken = \Phake::mock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $userProvider = \Phake::mock('Symfony\Component\Security\Core\User\UserProviderInterface');
        $user = \Phake::mock(User::clazz());
        $rootUserHandler = \Phake::mock('Modera\SecurityBundle\RootUserHandling\RootUserHandlerInterface');

        $username = 'vasja';
        $userPassword = '1234';
        $userSalt = 'salo, yum yum';
        $userRoles = array('mega', 'role');
        $tokenCredentials = array('foo', 'credentials');

        \Phake::when($anonymousToken)->getUsername()->thenReturn($username);
        \Phake::when($userProvider)->loadUserByUsername($username)->thenReturn($user);
        \Phake::when($encoderFactory)->getEncoder($user)->thenReturn($encoder);
        \Phake::when($user)->getPassword()->thenReturn($userPassword);
        \Phake::when($anonymousToken)->getCredentials()->thenReturn($tokenCredentials);
        \Phake::when($user)->getSalt()->thenReturn($userSalt);
        \Phake::when($encoder)->isPasswordValid($userPassword, $tokenCredentials, $userSalt)->thenReturn(true);
        \Phake::when($user)->getRoles()->thenReturn($userRoles);

        $tf = new AuthenticatedTokenFactory($encoderFactory, $rootUserHandler);

        $authenticatedToken = $tf->authenticateToken(
            $anonymousToken, $userProvider, 'dummy-pkey'
        );

        $this->assertInstanceOf(
            'Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken',
            $authenticatedToken
        );
        $this->assertSame($tokenCredentials, $authenticatedToken->getCredentials());
        $this->assertSame($user, $authenticatedToken->getUser());
        $this->assertEquals('dummy-pkey', $authenticatedToken->getProviderKey());
        $this->assertEquals(2, count($authenticatedToken->getRoles()));

        \Phake::verify($userProvider, \Phake::atLeast(1))->loadUserByUsername($username);
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AuthenticationException
     */
    public function testWithInvalidPasswordOrUsername()
    {
        $encoder = \Phake::mock('Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface');
        $encoderFactory = \Phake::mock('Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface');
        $anonymousToken = \Phake::mock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $userProvider = \Phake::mock('Symfony\Component\Security\Core\User\UserProviderInterface');
        $user = \Phake::mock(User::clazz());
        $rootUserHandler = \Phake::mock('Modera\SecurityBundle\RootUserHandling\RootUserHandlerInterface');

        $username = 'vasja';
        $userPassword = '1234';
        $userSalt = 'salo, yum yum';
        $userRoles = array('mega', 'role');
        $tokenCredentials = array('foo', 'credentials');

        \Phake::when($anonymousToken)->getUsername()->thenReturn($username);
        \Phake::when($userProvider)->loadUserByUsername($username)->thenThrow(new UsernameNotFoundException());
        \Phake::when($encoderFactory)->getEncoder($user)->thenReturn($encoder);
        \Phake::when($user)->getPassword()->thenReturn($userPassword);
        \Phake::when($anonymousToken)->getCredentials()->thenReturn($tokenCredentials);
        \Phake::when($user)->getSalt()->thenReturn($userSalt);
        \Phake::when($encoder)->isPasswordValid($userPassword, $tokenCredentials, $userSalt)->thenReturn(true);
        \Phake::when($user)->getRoles()->thenReturn($userRoles);

        $tf = new AuthenticatedTokenFactory($encoderFactory, $rootUserHandler);

        $tf->authenticateToken(
            $anonymousToken, $userProvider, 'dummy-pkey'
        );
    }

    public function testAuthenticateAsRootUser()
    {
        $encoder = \Phake::mock('Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface');
        $encoderFactory = \Phake::mock('Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface');
        $anonymousToken = \Phake::mock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $userProvider = \Phake::mock('Symfony\Component\Security\Core\User\UserProviderInterface');
        $user = \Phake::mock(User::clazz());
        $rootUserHandler = \Phake::mock('Modera\SecurityBundle\RootUserHandling\RootUserHandlerInterface');

        $username = 'vasja';
        $userPassword = '1234';
        $userSalt = 'salo, yum yum';
        $tokenCredentials = array('foo', 'credentials');
        $rootUserRoles = array('ROLE_A', 'ROLE_B', 'ROLE_C');

        \Phake::when($anonymousToken)->getUsername()->thenReturn($username);
        \Phake::when($userProvider)->loadUserByUsername($username)->thenReturn($user);
        \Phake::when($encoderFactory)->getEncoder($user)->thenReturn($encoder);
        \Phake::when($user)->getPassword()->thenReturn($userPassword);
        \Phake::when($anonymousToken)->getCredentials()->thenReturn($tokenCredentials);
        \Phake::when($user)->getSalt()->thenReturn($userSalt);
        \Phake::when($encoder)->isPasswordValid($userPassword, $tokenCredentials, $userSalt)->thenReturn(true);
        \Phake::when($rootUserHandler)->isRootUser($user)->thenReturn(true);
        \Phake::when($rootUserHandler)->getRoles()->thenReturn($rootUserRoles);

        $tf = new AuthenticatedTokenFactory($encoderFactory, $rootUserHandler);

        $authenticatedToken = $tf->authenticateToken(
            $anonymousToken, $userProvider, 'dummy-pkey'
        );

        $this->assertInstanceOf(
            'Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken',
            $authenticatedToken
        );
        $this->assertSame($tokenCredentials, $authenticatedToken->getCredentials());
        $this->assertSame($user, $authenticatedToken->getUser());
        $this->assertEquals('dummy-pkey', $authenticatedToken->getProviderKey());
        $this->assertEquals(3, count($authenticatedToken->getRoles()));

        $returnedTokenRoles = array();
        foreach ($authenticatedToken->getRoles() as $role) {
            $returnedTokenRoles[] = $role->getRole();
        }

        $this->assertSame($rootUserRoles, $returnedTokenRoles);
    }
}