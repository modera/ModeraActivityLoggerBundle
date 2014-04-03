<?php

namespace Modera\ServerCrudBundle\Tests\Unit\Intercepting;

use Modera\ServerCrudBundle\Controller\AbstractCrudController;
use Modera\ServerCrudBundle\Intercepting\ControllerActionsInterceptorInterface;
use Modera\ServerCrudBundle\Intercepting\InterceptorsManager;
use Modera\ServerCrudBundle\Intercepting\InvalidInterceptorException;
use Sli\ExpanderBundle\Ext\ContributorInterface;

class DummyInterceptor implements ControllerActionsInterceptorInterface
{
    public $invocations = array(
        'create' => array(),
        'update' => array(),
        'get' => array(),
        'list' => array(),
        'remove' => array(),
        'getNewRecordValues' => array()
    );

    public function onCreate(array $params, AbstractCrudController $controller)
    {
        $this->invocations['create'][] = array($params, $controller);
    }

    /**
     * @inheritDoc
     */
    public function onUpdate(array $params, AbstractCrudController $controller)
    {
        $this->invocations['update'][] = array($params, $controller);
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

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class InterceptorsManagerTest extends \PHPUnit_Framework_TestCase
{
    /* @var InterceptorsManager */
    private $mgr;
    private $provider;
    private $controller;

    protected function setUp()
    {
        $this->provider = $this->getMock(ContributorInterface::CLAZZ);
        $this->mgr = new InterceptorsManager($this->provider);
        $this->controller = $this->getMock(AbstractCrudController::clazz());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidActionGiven()
    {
        $this->mgr->intercept('xxx', array(), $this->controller);
    }

    public function testInvalidInterceptorProvided()
    {
        $obj = new \stdClass();

        $this->provider->expects($this->atLeastOnce())
                       ->method('getItems')
                       ->will($this->returnValue(array($obj)));

        $thrownException = null;
        try {
            $this->mgr->intercept('get', array(), $this->controller);
        } catch (InvalidInterceptorException $e) {
            $thrownException = $e;
        }

        $this->assertNotNull($thrownException);
        /* @var InvalidInterceptorException $e */
        $this->assertSame($obj, $e->getInterceptor());
        $this->assertTrue('' != $e->getMessage());
    }

    private function assertInvocation($interceptor, $type)
    {
        $givenParams = array('foo', 'bar');
        $givenController = $this->controller;

        $this->mgr->intercept($type, $givenParams, $givenController);

        $this->assertEquals(1, count($interceptor->invocations[$type]));
        $this->assertSame($givenParams, $interceptor->invocations[$type][0][0]);
        $this->assertSame($givenController, $interceptor->invocations[$type][0][1]);
    }

    public function testIntercept()
    {
        $interceptor1 = new DummyInterceptor();

        $this->provider->expects($this->atLeastOnce())
            ->method('getItems')
            ->will($this->returnValue(array($interceptor1)));

        $this->assertInvocation($interceptor1, 'create');
        $this->assertInvocation($interceptor1, 'get');
        $this->assertInvocation($interceptor1, 'update');
        $this->assertInvocation($interceptor1, 'list');
        $this->assertInvocation($interceptor1, 'remove');
        $this->assertInvocation($interceptor1, 'getNewRecordValues');
    }
} 