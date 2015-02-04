<?php

namespace Modera\SecurityBundle\Security;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\Authentication\SimpleFormAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Modera\SecurityBundle\Entity\User;
use Modera\SecurityBundle\Model\UserInterface;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class Authenticator implements SimpleFormAuthenticatorInterface, AuthenticationFailureHandlerInterface, AuthenticationSuccessHandlerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;
    private $authenticatedTokenFactory;

    /**
     * @param AuthenticatedTokenFactory $authenticatedTokenFactory
     */
    public function __construct(RegistryInterface $doctrine, AuthenticatedTokenFactory $authenticatedTokenFactory)
    {
        $this->om = $doctrine->getManager();
        $this->authenticatedTokenFactory = $authenticatedTokenFactory;
    }

    /**
     * @param Request $request
     * @param $username
     * @param $password
     * @param $providerKey
     * @return UsernamePasswordToken
     */
    public function createToken(Request $request, $username, $password, $providerKey)
    {
        return new UsernamePasswordToken($username, $password, $providerKey);
    }

    /**
     * @param TokenInterface $token
     * @param UserProviderInterface $userProvider
     * @param $providerKey
     * @return UsernamePasswordToken
     * @throws \Symfony\Component\Security\Core\Exception\AuthenticationException
     */
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        return $this->authenticatedTokenFactory->authenticateToken(
            $token, $userProvider, $providerKey
        );
    }

    /**
     * @param TokenInterface $token
     * @param $providerKey
     * @return bool
     */
    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof UsernamePasswordToken && $token->getProviderKey() === $providerKey;
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     * @return JsonResponse
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($request->isXmlHttpRequest()) {
            $result = array(
                'success' => false,
                'message' => $exception->getMessage()
            );
            return new JsonResponse($result);
        }
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @return JsonResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $user = $token->getUser();
        if ($user instanceof UserInterface && UserInterface::STATE_NEW == $user->getState()) {
            $user->setState(UserInterface::STATE_ACTIVE);
            $this->om->persist($user);
            $this->om->flush();
        }

        if ($request->isXmlHttpRequest()) {
            $result = static::getAuthenticationResponse($token);

            return new JsonResponse($result);
        }
    }

    /**
     * @param TokenInterface $token
     * @return array
     */
    static public function getAuthenticationResponse(TokenInterface $token)
    {
        $response = array('success' => false);
        if ($token->isAuthenticated() && $token->getUser() instanceof User) {
            /* @var User $user */
            $user = $token->getUser();
            $response = array(
                'success' => true,
                'profile' => array(
                    'id'       => $user->getId(),
                    'name'     => $user->getFullName(),
                    'email'    => $user->getEmail(),
                    'username' => $user->getUsername(),
                )
            );
        }

        return $response;
    }
}