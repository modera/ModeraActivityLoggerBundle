<?php


namespace Modera\ServerCrudBundle\Intercepting;

use Modera\ServerCrudBundle\Controller\AbstractCrudController;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
interface ControllerActionsInterceptorInterface
{
    /**
     * @param array                  $params
     * @param AbstractCrudController $controller
     *
     * @return void
     */
    public function onCreate(array $params, AbstractCrudController $controller);

    /**
     * @param array                  $params
     * @param AbstractCrudController $controller
     *
     * @return void
     */
    public function onUpdate(array $params, AbstractCrudController $controller);

    /**
     * @param array                  $params
     * @param AbstractCrudController $controller
     *
     * @return void
     */
    public function onGet(array $params, AbstractCrudController $controller);

    /**
     * @param array                  $params
     * @param AbstractCrudController $controller
     *
     * @return void
     */
    public function onList(array $params, AbstractCrudController $controller);

    /**
     * @param array                  $params
     * @param AbstractCrudController $controller
     *
     * @return void
     */
    public function onRemove(array $params, AbstractCrudController $controller);

    /**
     * @param array                  $params
     * @param AbstractCrudController $controller
     *
     * @return void
     */
    public function onGetNewRecordValues(array $params, AbstractCrudController $controller);
}