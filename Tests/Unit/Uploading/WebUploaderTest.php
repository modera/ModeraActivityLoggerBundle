<?php

namespace Modera\FileUploaderBundle\Tests\Uploading;

use Modera\FileUploaderBundle\Uploading\WebUploader;
use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class WebUploaderTest extends \PHPUnit_Framework_TestCase
{
    public function testUpload()
    {
        $gateway = \Phake::mock('Modera\FileUploaderBundle\Uploading\UploadGatewayInterface');

        $provider = \Phake::mock(ContributorInterface::CLAZZ);
        \Phake::when($provider)->getItems()->thenReturn(array($gateway));

        $request = \Phake::mock('Symfony\Component\HttpFoundation\Request');

        \Phake::when($gateway)->isResponsible($request)->thenReturn(true);
        \Phake::when($gateway)->upload($request)->thenReturn('foobar');

        $wu = new WebUploader($provider);

        $result = $wu->upload($request);

        \Phake::inOrder(
            \Phake::verify($provider)->getItems(),
            \Phake::verify($gateway)->isResponsible($request),
            \Phake::verify($gateway)->upload($request)
        );
        $this->assertEquals('foobar', $result);
    }
}
