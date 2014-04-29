<?php

namespace Modera\SecurityBundle\Security;

use Modera\SecurityBundle\RootUserHandling\RootUserHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Class is responsible for taking anonymous authentication token and trying to convert it to authenticated
 * UsernamePasswordToken. Class also takes into account possibility of having "root" user configured.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class AuthenticatedTokenFactory
{
    private $encoderFactory;
    private $rootUserHandler;

    /**
     * @param EncoderFactoryInterface $encoderFactory
     */
    public function __construct(EncoderFactoryInterface $encoderFactory, RootUserHandlerInterface $rootUserHandler)
    {
        $this->encoderFactory = $encoderFactory;
        $this->rootUserHandler = $rootUserHandler;
    }

    /**
     * @param TokenInterface $token
     * @param UserProviderInterface $userProvider
     * @param string $providerKey
     */
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        try {
            $user = $userProvider->loadUserByUsername($token->getUsername());
        } catch (UsernameNotFoundException $e) {
            throw new AuthenticationException('Invalid username or password');
        }

        $encoder = $this->encoderFactory->getEncoder($user);
        $passwordValid = $encoder->isPasswordValid(
            $user->getPassword(),
            $token->getCredentials(),
            $user->getSalt()
        );

        if ($passwordValid) {
            if ($this->rootUserHandler->isRootUser($user)) {
                $roles = $this->rootUserHandler->getRoles();
            } else {
                $roles = $user->getRoles();
            }

            return new UsernamePasswordToken(
                $user,
                $token->getCredentials(),
                $providerKey,
                $roles
            );
        }

        throw new AuthenticationException('Invalid username or password');
    }
} 