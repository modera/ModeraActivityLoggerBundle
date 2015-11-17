<?php

namespace Modera\MJRSecurityIntegrationBundle\Tests\Unit\EventListener;

use Modera\MJRSecurityIntegrationBundle\EventListener\AjaxAuthenticationValidatingListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2015 Modera Foundation
 */
class AjaxAuthenticationValidatingListenerTest extends \PHPUnit_Framework_TestCase
{
    private function createMockEvent($isAjax, $pathInfo = '', $e = null)
    {
        $request = \Phake::mock('Symfony\Component\HttpFoundation\Request');
        \Phake::when($request)->isXmlHttpRequest()->thenReturn($isAjax);
        \Phake::when($request)->getPathInfo()->thenReturn($pathInfo);

        $event = \Phake::mock('Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent');
        \Phake::when($event)->getRequest()->thenReturn($request);
        \Phake::when($event)->getException()->thenReturn($e);

        return $event;
    }

    public function testOnKernelExceptionWithNotAjaxRequest()
    {
        $event = $this->createMockEvent(false);

        $lnr = new AjaxAuthenticationValidatingListener('/mega-backend');

        $this->assertEquals(AjaxAuthenticationValidatingListener::RESULT_NOT_AJAX, $lnr->onKernelException($event));
    }

    public function testOnKernelExceptionWithNoBackend()
    {
        $event = $this->createMockEvent(true, '/another-backend');

        $lnr = new AjaxAuthenticationValidatingListener('/mega-backend');

        $this->assertEquals(AjaxAuthenticationValidatingListener::RESULT_NOT_BACKEND_REQUEST, $lnr->onKernelException($event));
    }

    public function testOnKernelExceptionWithInvalidException()
    {
        $event = $this->createMockEvent(true, '/mega-backend', new \RuntimeException());

        $lnr = new AjaxAuthenticationValidatingListener('/mega-backend');

        $lnr->onKernelException($event);

        \Phake::verify($event, \Phake::times(2))->getRequest();
        \Phake::verify($event)->getException();
        \Phake::verifyNoOtherInteractions($event);
    }

    public function testOnKernelException()
    {
        $event = $this->createMockEvent(true, '/mega-backend', new AccessDeniedException());

        $lnr = new AjaxAuthenticationValidatingListener('/mega-backend');

        $lnr->onKernelException($event);

        \Phake::verify($event, \Phake::times(2))->getRequest();
        \Phake::verify($event)->getException();
        \Phake::verify($event)->setResponse(\Phake::capture($response));

        \Phake::verifyNoOtherInteractions($event);

        /* @var JsonResponse $response */

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\JsonResponse', $response);
        $content = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($content));
        $this->assertArrayHasKey('success', $content);
        $this->assertFalse($content['success']);
        $this->assertArrayHasKey('message', $content);
        $this->assertTrue('' != $content['message']);
    }
}
