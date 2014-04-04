<?php

namespace Modera\ServerCrudBundle\Security;

use Modera\ServerCrudBundle\Controller\AbstractCrudController;
use Modera\ServerCrudBundle\Intercepting\ControllerActionsInterceptorInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Interceptor allows to add security enforcement logic to AbstractCrudController.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class SecurityControllerActionsInterceptor implements ControllerActionsInterceptorInterface
{
    private $securityContext;

    /**
     * @param SecurityContextInterface $securityContext
     */
    public function __construct(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    private function throwAccessDeniedException($role)
    {
        $e = new AccessDeniedHttpException("Security role '$role' is required to perform this action.");
        $e->setRole($role);
        throw $e;
    }

    public function checkAccess($actionName, AbstractCrudController $controller)
    {
        $config = $controller->getPreparedConfig();

        if (isset($config['security'])) {
            $security = $config['security'];

            if (isset($security['role'])) {
                $role = $security['role'];

                if (!$this->securityContext->isGranted($role)) {
                    $this->throwAccessDeniedException($role);
                }
            }

            if (isset($security['actions']) && isset($security['actions'][$actionName])) {
                $role = $security['actions'][$actionName];

                if (!$this->securityContext->isGranted($role)) {
                    $this->throwAccessDeniedException($role);
                }
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function onCreate(array $params, AbstractCrudController $controller)
    {
        $this->checkAccess('create', $controller);
    }

    /**
     * @inheritDoc
     */
    public function onUpdate(array $params, AbstractCrudController $controller)
    {
        $this->checkAccess('update', $controller);
    }

    /**
     * @inheritDoc
     */
    public function onGet(array $params, AbstractCrudController $controller)
    {
        $this->checkAccess('get', $controller);
    }

    /**
     * @inheritDoc
     */
    public function onList(array $params, AbstractCrudController $controller)
    {
        $this->checkAccess('list', $controller);
    }

    /**
     * @inheritDoc
     */
    public function onRemove(array $params, AbstractCrudController $controller)
    {
        $this->checkAccess('remove', $controller);
    }

    /**
     * @inheritDoc
     */
    public function onGetNewRecordValues(array $params, AbstractCrudController $controller)
    {
        $this->checkAccess('getNewRecordValues', $controller);
    }

    static public function clazz()
    {
        return get_called_class();
    }
} 