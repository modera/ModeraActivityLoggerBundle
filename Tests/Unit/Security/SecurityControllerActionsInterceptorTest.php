<?php

namespace Modera\ServerCrudBundle\Tests\Unit\Security;

use Modera\ServerCrudBundle\Controller\AbstractCrudController;
use Modera\ServerCrudBundle\Security\AccessDeniedHttpException;
use Modera\ServerCrudBundle\Security\SecurityControllerActionsInterceptor;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class SecurityControllerActionsInterceptorTest extends \PHPUnit_Framework_TestCase
{
    private $controller;
    private $securityContext;
    /* @var SecurityControllerActionsInterceptor */
    private $interceptor;

    protected function setUp()
    {
        $this->controller = $this->getMock(AbstractCrudController::clazz());
        $this->securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $this->interceptor = new SecurityControllerActionsInterceptor($this->securityContext);
    }

    public function testCheckAccess()
    {
        $config = array(
            'security' => array(
                'role' => 'ROLE_FOO'
            )
        );

        $this->controller->expects($this->atLeastOnce())
                         ->method('getPreparedConfig')
                         ->will($this->returnValue($config));


        $thrownException = null;
        try {
            $this->interceptor->checkAccess("it doesn't matter in this case", $this->controller);
        } catch (AccessDeniedHttpException $e) {
            $thrownException = $e;
        }

        $this->assertNotNull($thrownException);
        $this->assertEquals('ROLE_FOO', $thrownException->getRole());
    }

    public function assertExceptionThrown($actionName)
    {
        $config = array(
            'security' => array(
                'actions' => array(
                    'create' => 'ROLE_CREATE',
                    'update' => 'ROLE_UPDATE',
                    'get' => 'ROLE_GET',
                    'list' => 'ROLE_LIST',
                    'remove' => 'ROLE_REMOVE',
                    'getNewRecordValues' => 'ROLE_GRV',
                )
            )
        );

        $this->securityContext->expects($this->atLeastOnce())
            ->method('isGranted')
            ->with($this->equalTo($config['security']['actions'][$actionName]))
            ->will($this->returnValue(false));

        $this->controller->expects($this->atLeastOnce())
            ->method('getPreparedConfig')
            ->will($this->returnValue($config));


        $thrownException = null;
        try {
            $this->interceptor->{'on' . ucfirst($actionName)}(array(), $this->controller);
        } catch (AccessDeniedHttpException $e) {
            $thrownException = $e;
        }

        $this->assertNotNull($thrownException);
        $this->assertEquals($config['security']['actions'][$actionName], $thrownException->getRole());
    }

    public function assertAccessAllowed($actionName)
    {
        $config = array(
            'security' => array(
                'actions' => array(
                    'create' => 'ROLE_CREATE',
                    'update' => 'ROLE_UPDATE',
                    'get' => 'ROLE_GET',
                    'list' => 'ROLE_LIST',
                    'remove' => 'ROLE_REMOVE',
                    'getNewRecordValues' => 'ROLE_GRV',
                )
            )
        );

        $this->securityContext->expects($this->atLeastOnce())
             ->method('isGranted')
             ->with($this->equalTo($config['security']['actions'][$actionName]))
             ->will($this->returnValue(true));

        $this->controller->expects($this->atLeastOnce())
             ->method('getPreparedConfig')
             ->will($this->returnValue($config));

        $this->interceptor->{'on' . ucfirst($actionName)}(array(), $this->controller);
    }

    public function testOnCreateDenied()
    {
        $this->assertExceptionThrown('create');
    }

    public function testOnCreateAllowed()
    {
        $this->assertAccessAllowed('create');
    }

    public function testOnUpdate()
    {
        $this->assertExceptionThrown('update');
    }

    public function testOnUpdateAllowed()
    {
        $this->assertAccessAllowed('update');
    }

    public function testOnGet()
    {
        $this->assertExceptionThrown('get');
    }

    public function testOnGetAllowed()
    {
        $this->assertAccessAllowed('get');
    }

    public function testOnList()
    {
        $this->assertExceptionThrown('list');
    }

    public function testOnListAllowed()
    {
        $this->assertAccessAllowed('list');
    }

    public function testOnRemove()
    {
        $this->assertExceptionThrown('remove');
    }

    public function testOnRemoveAllowed()
    {
        $this->assertAccessAllowed('remove');
    }

    public function testOnGetNewRecordValues()
    {
        $this->assertExceptionThrown('getNewRecordValues');
    }

    public function testOnGetNewRecordValuesAllowed()
    {
        $this->assertAccessAllowed('getNewRecordValues');
    }
} 