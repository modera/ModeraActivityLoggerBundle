<?php

namespace Modera\FileRepositoryBundle\Tests\Unit\Controller;

use Symfony\Component\HttpFoundation\Response;
use Modera\FileRepositoryBundle\Entity\StoredFile;
use Modera\FileRepositoryBundle\Controller\StoredFileController;
use Modera\FileRepositoryBundle\DependencyInjection\ModeraFileRepositoryExtension;

class DummyController extends StoredFileController
{
    /**
     * @var StoredFile
     */
    public $storedFile = null;

    protected function getFile($storageKey)
    {
        return $this->storedFile;
    }

    public function setEnabled($status)
    {
        \Phake::when($this->container)
            ->getParameter(ModeraFileRepositoryExtension::CONFIG_KEY.'.controller.is_enabled')
            ->thenReturn($status)
        ;
    }
}

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2015 Modera Foundation
 */
class StoredFileControllerTest extends \PHPUnit_Framework_TestCase
{
    private function createStoredFileController()
    {
        $user = \Phake::mock('Symfony\Component\Security\Core\User\UserInterface');
        $container = \Phake::mock('Symfony\Component\DependencyInjection\ContainerInterface');
        $token = \Phake::mock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $tokenStorage = \Phake::mock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface');

        \Phake::when($token)->getUser()->thenReturn($user);
        \Phake::when($tokenStorage)->getToken()->thenReturn($token);

        \Phake::when($container)->has('security.token_storage')->thenReturn(true);
        \Phake::when($container)->get('security.token_storage')->thenReturn($tokenStorage);

        $ctrl = new DummyController();
        $ctrl->setContainer($container);
        $ctrl->setEnabled(true);

        return $ctrl;
    }

    private function createStoredFile($storageKey, $content)
    {
        if ($storageKey) {
            $parts = explode('/', $storageKey);
            if (count($parts) > 1) {
                $filename = $parts[count($parts) - 1];
            } else {
                $filename = 'foo.txt';
            }
            list($name, $extension) = explode('.', $filename);

            $mimeType = array(
                'txt' => 'text/plain',
            );

            $storedFile = \Phake::mock(StoredFile::clazz());

            \Phake::when($storedFile)->getStorageKey()->thenReturn($parts[0]);
            \Phake::when($storedFile)->getFilename()->thenReturn($filename);
            \Phake::when($storedFile)->getMimeType()->thenReturn($mimeType[$extension]);
            \Phake::when($storedFile)->getExtension()->thenReturn($extension);
            \Phake::when($storedFile)->getCreatedAt()->thenReturn(new \DateTime());
            \Phake::when($storedFile)->getContents()->thenReturn($content);
            \Phake::when($storedFile)->getSize()->thenReturn(strlen($content));

            return $storedFile;
        }

        return;
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testGetAction()
    {
        $ctrl = $this->createStoredFileController();
        $request = \Phake::mock('Symfony\Component\HttpFoundation\Request');

        $resp = $ctrl->getAction($request, '');
        $this->assertEquals(Response::HTTP_NOT_FOUND, $resp->getStatusCode());
        $this->assertEquals('File not found.', $resp->getContent());

        $content = 'Hello World!';
        $storageKey = 'storage-key/repository-name/file-name.txt';
        $ctrl->storedFile = $this->createStoredFile($storageKey, $content);

        $resp = $ctrl->getAction($request, 'storage-key');
        $this->assertEquals(Response::HTTP_OK, $resp->getStatusCode());
        $this->assertEquals($content, $resp->getContent());
        $this->assertNull($resp->headers->get('content-disposition'));

        $resp = $ctrl->getAction($request, $storageKey);
        $this->assertEquals(Response::HTTP_OK, $resp->getStatusCode());
        $this->assertEquals($content, $resp->getContent());
        $this->assertNull($resp->headers->get('content-disposition'));

        $content = 'Download test';
        $ctrl->storedFile = $this->createStoredFile($storageKey, $content);
        \Phake::when($request)->get('dl')->thenReturn('');

        $resp = $ctrl->getAction($request, 'storage-key/repository-name/download-me.txt');
        $this->assertEquals(Response::HTTP_OK, $resp->getStatusCode());
        $this->assertEquals($content, $resp->getContent());
        $this->assertEquals('attachment; filename="download-me.txt"', $resp->headers->get('content-disposition'));

        $resp = $ctrl->getAction($request, 'storage-key/foo.txt');
        $this->assertEquals(Response::HTTP_OK, $resp->getStatusCode());
        $this->assertEquals($content, $resp->getContent());
        $this->assertEquals('attachment; filename="foo.txt"', $resp->headers->get('content-disposition'));

        $ctrl->setEnabled(false);
        $resp = $ctrl->getAction($request, 'Exception');
    }
}
