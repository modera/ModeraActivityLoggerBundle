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
        $msg = is_callable($role)
             ? 'You are not allowed to perform this action.'
             : "Security role '$role' is required to perform this action.";

        $e = new AccessDeniedHttpException($msg);
        if (!is_callable($role)) {
            $e->setRole($role);
        }

        throw $e;
    }

    /**
     * @param string $actionName
     * @param array $params
     * @param AbstractCrudController $controller
     */
    public function checkAccess($actionName, array $params, AbstractCrudController $controller)
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

                if (is_callable($role)) {
                    if (!call_user_func($role, $this->securityContext, $params, $actionName)) {
                        $this->throwAccessDeniedException($role);
                    }
                } else if (!$this->securityContext->isGranted($role)) {
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
        $this->checkAccess('create', $params, $controller);
    }

    /**
     * @inheritDoc
     */
    public function onUpdate(array $params, AbstractCrudController $controller)
    {
        $this->checkAccess('update', $params, $controller);
    }

    /**
     * @inheritDoc
     */
    public function onBatchUpdate(array $params, AbstractCrudController $controller)
    {
        $this->checkAccess('batchUpdate', $params, $controller);
    }

    /**
     * @inheritDoc
     */
    public function onGet(array $params, AbstractCrudController $controller)
    {
        $this->checkAccess('get', $params, $controller);
    }

    /**
     * @inheritDoc
     */
    public function onList(array $params, AbstractCrudController $controller)
    {
        $this->checkAccess('list', $params, $controller);
    }

    /**
     * @inheritDoc
     */
    public function onRemove(array $params, AbstractCrudController $controller)
    {
        $this->checkAccess('remove', $params, $controller);
    }

    /**
     * @inheritDoc
     */
    public function onGetNewRecordValues(array $params, AbstractCrudController $controller)
    {
        $this->checkAccess('getNewRecordValues', $params, $controller);
    }

    static public function clazz()
    {
        return get_called_class();
    }
} 