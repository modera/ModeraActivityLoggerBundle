<?php

namespace Modera\FileRepositoryBundle\Tests\Unit\StoredFile;

use Symfony\Component\Routing\RouterInterface;
use Modera\FileRepositoryBundle\StoredFile\UrlGenerator;

/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
class UrlGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerateUrl()
    {
        $repository = \Phake::mock('Modera\FileRepositoryBundle\Entity\Repository');
        \Phake::when($repository)->getName()->thenReturn('repository-name');

        $storedFile = \Phake::mock('Modera\FileRepositoryBundle\Entity\StoredFile');
        \Phake::when($storedFile)->getStorageKey()->thenReturn('storage-key');
        \Phake::when($storedFile)->getRepository()->thenReturn($repository);
        \Phake::when($storedFile)->getFilename()->thenReturn('file-name');

        $routeName = 'some-route';
        $storageKey = $storedFile->getStorageKey();
        $storageKey .= '/'.$storedFile->getRepository()->getName();
        $storageKey .= '/'.$storedFile->getFilename();

        $url = $routeName.'/'.$storageKey;

        $router = \Phake::mock('Symfony\Component\Routing\RouterInterface');
        \Phake::when($router)->generate($routeName, array(
            'storageKey' => $storageKey,
        ), RouterInterface::NETWORK_PATH)->thenReturn($url);

        $urlGenerator = new UrlGenerator($router, $routeName);

        $this->assertEquals($url, $urlGenerator->generateUrl($storedFile));
    }
}
