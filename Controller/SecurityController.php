<?php

namespace Modera\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Modera\SecurityBundle\Security\Authenticator;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class SecurityController extends Controller
{
    /**
     * @param Request $request
     */
    protected function initSession(Request $request)
    {
        $session = $request->getSession();
        if ($session instanceof Session && !$session->getId()) {
            $session->start();
        }
    }

    /**
     * @Route("/login", name="_security_login")
     * @Template()
     */
    public function loginAction(Request $request)
    {
        /* @var AuthenticationUtils $helper */
        $helper = $this->get('security.authentication_utils');

        return array(
            'last_username' => $helper->getLastUsername(),
            'error'         => $helper->getLastAuthenticationError(),
        );
    }

    /**
     * @Route("/is-authenticated", name="_security_is_authenticated")
     */
    public function isAuthenticatedAction(Request $request)
    {
        $this->initSession($request);

        /* @var TokenStorageInterface $tokenStorage */
        $tokenStorage = $this->get('security.token_storage');

        $response = Authenticator::getAuthenticationResponse($tokenStorage->getToken());

        return new JsonResponse($response);
    }

    /**
     * @Route("/login_check", name="_security_check")
     */
    public function securityCheckAction()
    {
        // The security layer will intercept this request
    }

    /**
     * @Route("/logout", name="_security_logout")
     */
    public function logoutAction()
    {
        // The security layer will intercept this request
    }
}