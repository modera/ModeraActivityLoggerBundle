<?php

namespace Modera\FileUploaderBundle\Tests\Unit\Controller;

use Modera\FileRepositoryBundle\Exceptions\FileValidationException;
use Modera\FileUploaderBundle\Controller\UniversalUploaderController;
use Modera\FileUploaderBundle\Uploading\WebUploader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2015 Modera Foundation
 */
class UniversalUploaderControllerTest extends \PHPUnit_Framework_TestCase
{
    private $container;

    /**
     * @var UniversalUploaderController
     */
    private $ctr;

    private $webUploader;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->container = \Phake::mock('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->webUploader = \Phake::mock(WebUploader::clazz());

        $this->ctr = new UniversalUploaderController();
        $this->ctr->setContainer($this->container);
    }

    public function testUploadActionWhenNotEnabled()
    {
        $thrownException = null;
        try {
            $this->ctr->uploadAction(new Request());
        } catch (NotFoundHttpException $e) {
            $thrownException = $e;
        }

        $this->assertNotNull($thrownException);
        $this->assertEquals(404, $thrownException->getStatusCode());
    }

    private function teachContainer(Request $request, $isUploaderEnabled, $uploaderResult)
    {
        if ($uploaderResult instanceof \Exception) {
            \Phake::when($this->webUploader)
                ->upload($request)
                ->thenThrow($uploaderResult)
            ;
        } else {
            \Phake::when($this->webUploader)
                ->upload($request)
                ->thenReturn($uploaderResult)
            ;
        }

        \Phake::when($this->container)
            ->getParameter('modera_file_uploader.is_enabled')
            ->thenReturn($isUploaderEnabled)
        ;

        \Phake::when($this->container)
            ->get('modera_file_uploader.uploading.web_uploader')
            ->thenReturn($this->webUploader)
        ;
    }

    public function testUploadActionWhenNoUploadHandledRequest()
    {
        $request = new Request();

        $this->teachContainer($request, true, false);

        $result = $this->ctr->uploadAction($request);
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\JsonResponse', $result);

        $content = json_decode($result->getContent(), true);
        $this->assertArrayHasKey('success', $content);
        $this->assertFalse($content['success']);
        $this->assertArrayHasKey('error', $content);
        $this->assertContains('Unable', $content['error']);
    }

    public function testUploadActionSuccess()
    {
        $request = new Request();

        $response = array(
            'success' => true,
            'blah' => 'foo',
        );

        $this->teachContainer($request, true, $response);

        $result = $this->ctr->uploadAction($request);
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\JsonResponse', $result);

        $this->assertSame($response, json_decode($result->getContent(), true));
    }

    public function testUploadActionWithValidationException()
    {
        $request = new Request();

        $exception = FileValidationException::create(new \SplFileInfo(__FILE__), ['some error']);

        $this->teachContainer($request, true, $exception);

        $result = $this->ctr->uploadAction($request);
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\JsonResponse', $result);

        $content = json_decode($result->getContent(), true);
        $this->assertArrayHasKey('success', $content);
        $this->assertFalse($content['success']);
        $this->assertArrayHasKey('error', $content);
        $this->assertArrayHasKey('errors', $content);
        $this->assertTrue(is_array($content['errors']));
        $this->assertEquals(1, count($content['errors']));
        $this->assertContains('some error', $content['errors'][0]);
    }
}
