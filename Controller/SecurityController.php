<?php

namespace Modera\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Modera\SecurityBundle\Security\Authenticator;
use Symfony\Component\HttpFoundation\Session\Session;

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
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return array(
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        );
    }

    /**
     * @Route("/is-authenticated", name="_security_is_authenticated")
     */
    public function isAuthenticatedAction(Request $request)
    {
        $this->initSession($request);

        /* @var SecurityContextInterface $sc */
        $sc = $this->get('security.context');

        $response = Authenticator::getAuthenticationResponse($sc->getToken());

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