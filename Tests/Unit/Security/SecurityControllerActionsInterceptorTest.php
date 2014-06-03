<?php

namespace Modera\ServerCrudBundle\Tests\Unit\Security;

use Modera\ServerCrudBundle\Controller\AbstractCrudController;
use Modera\ServerCrudBundle\Security\AccessDeniedHttpException;
use Modera\ServerCrudBundle\Security\SecurityControllerActionsInterceptor;
use Symfony\Component\Security\Core\SecurityContextInterface;

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

        $this->teachController($config);

        $thrownException = null;
        try {
            $this->interceptor->checkAccess("it doesn't matter in this case", array(), $this->controller);
        } catch (AccessDeniedHttpException $e) {
            $thrownException = $e;
        }

        $this->assertNotNull($thrownException);
        $this->assertEquals('ROLE_FOO', $thrownException->getRole());
    }

    private function teachController(array $preparedConfig)
    {
        $this->controller->expects($this->atLeastOnce())
             ->method('getPreparedConfig')
             ->will($this->returnValue($preparedConfig));
    }

    private function teachSecurityContext($expectedArgValue, $returnValue)
    {
        $this->securityContext->expects($this->atLeastOnce())
            ->method('isGranted')
            ->with($this->equalTo($expectedArgValue))
            ->will($this->returnValue($returnValue));
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
                    'batchUpdate' => 'ROLE_BATCH_UPDATE'
                )
            )
        );

        $this->teachSecurityContext($config['security']['actions'][$actionName], false);
        $this->teachController($config);

        $thrownException = null;
        try {
            $this->interceptor->{'on' . ucfirst($actionName)}(array(), $this->controller);
        } catch (AccessDeniedHttpException $e) {
            $thrownException = $e;
        }

        $this->assertNotNull($thrownException);
        $this->assertEquals($config['security']['actions'][$actionName], $thrownException->getRole());
    }

    /**
     * @param string $actionName
     */
    private function assertAccessAllowed($actionName)
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
                    'batchUpdate' => 'ROLE_BATCH_UPDATE'
                )
            )
        );

        $this->teachSecurityContext($config['security']['actions'][$actionName], true);
        $this->teachController($config);

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

    public function testOnBatchUpdate()
    {
        $this->assertExceptionThrown('batchUpdate');
    }

    public function testOnBatchUpdateAllowed()
    {
        $this->assertAccessAllowed('batchUpdate');
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

    public function testCheckAccessWithCallable()
    {
        $holder = new \stdClass();

        $config = array(
            'security' => array(
                'actions' => array(
                    'create' => function(SecurityContextInterface $sc, $params, $actionName) use($holder) {
                        $holder->sc = $sc;
                        $holder->params = $params;
                        $holder->actionName = $actionName;

                        return false;
                    },
                )
            )
        );

        $this->teachController($config);

        $thrownException = null;
        try {
            $this->interceptor->checkAccess('create', array('foo'), $this->controller);
        } catch (AccessDeniedHttpException $e) {
            $thrownException = $e;
        }

        $this->assertNotNull($thrownException);
        $this->assertInstanceOf('Symfony\Component\Security\Core\SecurityContextInterface', $holder->sc);
        $this->assertEquals(array('foo'), $holder->params);
        $this->assertEquals('create', $holder->actionName);
    }
} 