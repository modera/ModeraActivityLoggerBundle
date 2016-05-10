<?php

namespace Modera\ServerCrudBundle\Tests\Fixtures;

use Modera\ServerCrudBundle\Controller\AbstractCrudController;
use Modera\ServerCrudBundle\Intercepting\ControllerActionsInterceptorInterface;

class DummyInterceptor implements ControllerActionsInterceptorInterface
{
    public $invocations = array(
        'create' => array(),
        'update' => array(),
        'get' => array(),
        'list' => array(),
        'remove' => array(),
        'getNewRecordValues' => array(),
    );

    public function onCreate(array $params, AbstractCrudController $controller)
    {
        $this->invocations['create'][] = array($params, $controller);
    }

    /**
     * {@inheritdoc}
     */
    public function onUpdate(array $params, AbstractCrudController $controller)
    {
        $this->invocations['update'][] = array($params, $controller);
    }

    /**
     * {@inheritdoc}
     */
    public function onBatchUpdate(array $params, AbstractCrudController $controller)
    {
        $this->invocations['batchUpdate'][] = array($params, $controller);
    }

    public function onGet(array $params, AbstractCrudController $controller)
    {
        $this->invocations['get'][] = array($params, $controller);
    }

    public function onList(array $params, AbstractCrudController $controller)
    {
        $this->invocations['list'][] = array($params, $controller);
    }

    public function onRemove(array $params, AbstractCrudController $controller)
    {
        $this->invocations['remove'][] = array($params, $controller);
    }

    public function onGetNewRecordValues(array $params, AbstractCrudController $controller)
    {
        $this->invocations['getNewRecordValues'][] = array($params, $controller);
    }
}
