<?php

namespace Modera\ServerCrudBundle\Security;

use Modera\ServerCrudBundle\Controller\AbstractCrudController;
use Modera\ServerCrudBundle\Intercepting\ControllerActionsInterceptorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Interceptor allows to add security enforcement logic to AbstractCrudController.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class SecurityControllerActionsInterceptor implements ControllerActionsInterceptorInterface
{
    private $authorizationChecker;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
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
     * @param string                 $actionName
     * @param array                  $params
     * @param AbstractCrudController $controller
     */
    public function checkAccess($actionName, array $params, AbstractCrudController $controller)
    {
        $config = $controller->getPreparedConfig();

        if (isset($config['security'])) {
            $security = $config['security'];

            if (isset($security['role'])) {
                $role = $security['role'];

                if (!$this->authorizationChecker->isGranted($role)) {
                    $this->throwAccessDeniedException($role);
                }
            }

            if (isset($security['actions']) && isset($security['actions'][$actionName])) {
                $role = $security['actions'][$actionName];

                if (is_callable($role)) {
                    if (!call_user_func($role, $this->authorizationChecker, $params, $actionName)) {
                        $this->throwAccessDeniedException($role);
                    }
                } elseif (!$this->authorizationChecker->isGranted($role)) {
                    $this->throwAccessDeniedException($role);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function onCreate(array $params, AbstractCrudController $controller)
    {
        $this->checkAccess('create', $params, $controller);
    }

    /**
     * {@inheritdoc}
     */
    public function onUpdate(array $params, AbstractCrudController $controller)
    {
        $this->checkAccess('update', $params, $controller);
    }

    /**
     * {@inheritdoc}
     */
    public function onBatchUpdate(array $params, AbstractCrudController $controller)
    {
        $this->checkAccess('batchUpdate', $params, $controller);
    }

    /**
     * {@inheritdoc}
     */
    public function onGet(array $params, AbstractCrudController $controller)
    {
        $this->checkAccess('get', $params, $controller);
    }

    /**
     * {@inheritdoc}
     */
    public function onList(array $params, AbstractCrudController $controller)
    {
        $this->checkAccess('list', $params, $controller);
    }

    /**
     * {@inheritdoc}
     */
    public function onRemove(array $params, AbstractCrudController $controller)
    {
        $this->checkAccess('remove', $params, $controller);
    }

    /**
     * {@inheritdoc}
     */
    public function onGetNewRecordValues(array $params, AbstractCrudController $controller)
    {
        $this->checkAccess('getNewRecordValues', $params, $controller);
    }

    public static function clazz()
    {
        return get_called_class();
    }
}
