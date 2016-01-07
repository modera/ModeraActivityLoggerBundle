<?php

namespace Modera\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class SecurityController extends Controller
{
    /**
     * @Route("/login", name="_security_login")
     * @Template()
     */
    public function loginAction()
    {
        /* @var AuthenticationUtils $helper */
        $helper = $this->get('security.authentication_utils');

        return array(
            'last_username' => $helper->getLastUsername(),
            'error' => $helper->getLastAuthenticationError(),
        );
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
