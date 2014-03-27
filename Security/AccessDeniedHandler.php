<?php

namespace Modera\SecurityBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    /**
     * Handles an access denied failure.
     *
     * @param Request $request
     * @param AccessDeniedException $accessDeniedException
     *
     * @return Response may return null
     */
    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(
                array(
                    'success' => false,
                    'message' => $accessDeniedException->getMessage(),
                )
            );
        }
    }
}
