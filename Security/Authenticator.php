<?php

namespace Modera\SecurityBundle\Security;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Modera\SecurityBundle\Entity\User;
use Modera\SecurityBundle\Model\UserInterface;

/**
 * @internal
 *
 * TODO since the class is no longer does any kind of authentication it should be renamed to something more meaningful
 *
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class Authenticator implements AuthenticationFailureHandlerInterface, AuthenticationSuccessHandlerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param RegistryInterface $doctrine
     */
    public function __construct(RegistryInterface $doctrine)
    {
        $this->om = $doctrine->getManager();
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($request->isXmlHttpRequest()) {
            $result = array(
                'success' => false,
                'message' => $exception->getMessage(),
            );

            return new JsonResponse($result);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $user = $token->getUser();
        if ($user instanceof UserInterface && UserInterface::STATE_NEW == $user->getState()) {
            /* @var User $user */
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
     *
     * @return array
     */
    public static function getAuthenticationResponse(TokenInterface $token)
    {
        $response = array('success' => false);
        if ($token->isAuthenticated() && $token->getUser() instanceof User) {
            /* @var User $user */
            $user = $token->getUser();
            $response = array(
                'success' => true,
                'profile' => self::userToArray($user),
            );
        }

        return $response;
    }

    /**
     * @param User $user
     *
     * @return array
     */
    public static function userToArray(User $user)
    {
        return array(
            'id' => $user->getId(),
            'name' => $user->getFullName(),
            'email' => $user->getEmail(),
            'username' => $user->getUsername(),
        );
    }
}
