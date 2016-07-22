<?php

namespace Modera\ServerCrudBundle\Intercepting;

use Modera\ServerCrudBundle\Controller\AbstractCrudController;
use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * Handles interceptors invoking process.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class InterceptorsManager
{
    private $interceptorsProvider;

    /**
     * @param ContributorInterface $interceptorsProvider
     */
    public function __construct(ContributorInterface $interceptorsProvider)
    {
        $this->interceptorsProvider = $interceptorsProvider;
    }

    /**
     * @param string                 $actionName
     * @param array                  $params
     * @param AbstractCrudController $controller
     *
     * @throws InvalidInterceptorException
     * @throws \InvalidArgumentException   When bad $actionName is given
     */
    public function intercept($actionName, array $params, AbstractCrudController $controller)
    {
        if (!in_array($actionName, array('create', 'get', 'list', 'remove', 'update', 'getNewRecordValues', 'batchUpdate'))) {
            throw new \InvalidArgumentException(sprintf(
                'Action name can only be either of these: create, get, list or remove, update, getNewRecordValues, but "%s" given',
                $actionName
            ));
        }

        foreach ($this->interceptorsProvider->getItems() as $interceptor) {
            if (!($interceptor instanceof ControllerActionsInterceptorInterface)) {
                throw InvalidInterceptorException::create($interceptor);
            }

            /* @var ControllerActionsInterceptorInterface $interceptor */

            $interceptor->{'on'.ucfirst($actionName)}($params, $controller);
        }
    }
}
