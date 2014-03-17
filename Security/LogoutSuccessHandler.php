<?php

namespace Modera\SecurityBundle\Security;

use Symfony\Component\Security\Http\Logout\DefaultLogoutSuccessHandler;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class LogoutSuccessHandler extends DefaultLogoutSuccessHandler
{
    /**
     * @param Request $request
     * @return JsonResponse|RedirectResponse|Response
     */
    public function onLogoutSuccess(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $response = array('success' => true);
            return new JsonResponse($response);
        } else {
            return parent::onLogoutSuccess($request);
        }
    }
}