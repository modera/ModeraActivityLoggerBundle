<?php

namespace Modera\ServerCrudBundle\Tests\Unit\ExceptionHandling;

use Modera\ServerCrudBundle\ExceptionHandling\EnvAwareExceptionHandler;
use Modera\ServerCrudBundle\ExceptionHandling\ExceptionHandlerInterface;
use Sli\ExpanderBundle\Ext\ContributorInterface;

class DummyExceptionHandler implements ExceptionHandlerInterface
{
    public $e;
    public $operation;

    public function createResponse(\Exception $e, $operation)
    {
        $this->e = $e;
        $this->operation = $operation;

        return 'muhaha';
    }
}

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class EnvAwareExceptionHandlerTest extends \PHPUnit_Framework_TestCase
{
    /* @var EnvAwareExceptionHandler $handler */
    private $handler;

    private $handlersProvider;
    private $kernel;

    public function setUp()
    {
        $this->handlersProvider = $this->getMock(ContributorInterface::CLAZZ);
        $this->kernel = $this->getMock('Symfony\Component\HttpKernel\Kernel', array(), array(), '', false);

        $this->handler = new EnvAwareExceptionHandler($this->handlersProvider, $this->kernel);
    }

    private function teachHandlersProvider(array $handlers = array())
    {
        $this->handlersProvider->expects($this->atLeastOnce())
                               ->method('getItems')
                               ->will($this->returnValue($handlers));
    }

    private function teachKernel($env)
    {
        $this->kernel->expects($this->atLeastOnce())
                     ->method('getEnvironment')
                     ->will($this->returnValue($env));
    }

    private function createException()
    {
        $thrownException = null;
        try {
            throw new \RuntimeException('something');
        } catch (\RuntimeException $e) {
            return $e;
        }
    }

    private function assertValidResultStructure(array $result)
    {
        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('success', $result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('exception', $result);
        $this->assertTrue($result['exception']);
    }

    public function testCreateResponseInProdEnv()
    {
        $this->teachHandlersProvider();
        $this->teachKernel('prod');

        $e = $this->createException();

        $result = $this->handler->createResponse($e, 'foo-operation');

        $this->assertValidResultStructure($result);
        $this->assertEquals(2, count($result));
    }

    public function testCreateResponseInOtherEnv()
    {
        $this->teachHandlersProvider();
        $this->teachKernel('test');

        $result = $this->handler->createResponse($this->createException(), 'foo-operation');

        $this->assertValidResultStructure($result);
        $this->assertArrayHasKey('exception_class', $result);
        $this->assertEquals('RuntimeException', $result['exception_class']);
        $this->assertArrayHasKey('stack_trace', $result);
        $this->assertTrue(is_array($result['stack_trace']));
        $this->assertArrayHasKey('file', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertEquals('something', $result['message']);
    }

    public function testCreateResponseUsingDelegate()
    {
        $delegate = new DummyExceptionHandler();

        $this->teachHandlersProvider(array($delegate));

        $e = $this->createException();

        $result = $this->handler->createResponse($e, 'foobar-operation');

        $this->assertEquals('muhaha', $result);
        $this->assertSame($e, $delegate->e);
        $this->assertEquals('foobar-operation', $delegate->operation);
    }
}