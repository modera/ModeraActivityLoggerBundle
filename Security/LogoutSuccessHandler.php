<?php

namespace Modera\SecurityBundle\Security;

use Symfony\Component\Security\Http\Logout\DefaultLogoutSuccessHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @internal
 *
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class LogoutSuccessHandler extends DefaultLogoutSuccessHandler
{
    /**
     * {@inheritdoc}
     */
    public function onLogoutSuccess(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(array('success' => true));
        } else {
            return parent::onLogoutSuccess($request);
        }
    }
}
