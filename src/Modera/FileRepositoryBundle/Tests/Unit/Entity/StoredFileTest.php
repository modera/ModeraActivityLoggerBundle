<?php

namespace Modera\FileRepositoryBundle\Tests\Unit\Entity;

use Symfony\Component\HttpFoundation\File\File;
use Modera\FileRepositoryBundle\DependencyInjection\ModeraFileRepositoryExtension;
use Modera\FileRepositoryBundle\Entity\StoredFile;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class StoredFileTest extends \PHPUnit_Framework_TestCase
{
    public function test__construct()
    {
        $filename = uniqid().'.txt';
        $filePath = sys_get_temp_dir().DIRECTORY_SEPARATOR.$filename;
        file_put_contents($filePath, 'blah');

        $file = new File($filePath);

        $context = array('foo');
        $dummyStorageKey = 'storage-key';

        $repository = $this->getMock('Modera\FileRepositoryBundle\Entity\Repository', array(), array(), '', false);
        $repository->expects($this->atLeastOnce())
                   ->method('generateStorageKey')
                   ->with($this->equalTo($file), $this->equalTo($context))
                   ->will($this->returnValue($dummyStorageKey));

        $storedFile = new StoredFile($repository, $file, $context);

        $this->assertEquals($filename, $storedFile->getFilename());
        $this->assertEquals($dummyStorageKey, $storedFile->getStorageKey());
        $this->assertEquals('txt', $storedFile->getExtension());
        $this->assertEquals('text/plain', $storedFile->getMimeType());
    }

    public function testGetUrl()
    {
        $container = \Phake::mock('Symfony\Component\DependencyInjection\ContainerInterface');

        \Phake::when($container)
            ->getParameter(ModeraFileRepositoryExtension::CONFIG_KEY.'.default_url_generator')
            ->thenReturn('default_url_generator')
        ;

        \Phake::when($container)
            ->getParameter(ModeraFileRepositoryExtension::CONFIG_KEY.'.url_generators')
            ->thenReturn(array(
                'foo' => 'foo_url_generator',
                'bar' => 'bar_url_generator',
            ))
        ;

        $defaultUrlGenerator = \Phake::mock('Modera\FileRepositoryBundle\StoredFile\UrlGeneratorInterface');
        \Phake::when($container)->get('default_url_generator')->thenReturn($defaultUrlGenerator);

        $fooUrlGenerator = \Phake::mock('Modera\FileRepositoryBundle\StoredFile\UrlGeneratorInterface');
        \Phake::when($container)->get('foo_url_generator')->thenReturn($fooUrlGenerator);

        \Phake::when($container)->get('bar_url_generator')->thenReturn('not_url_generator');

        $context = array();
        $splFile = new \SplFileInfo(__FILE__);
        $repository = \Phake::mock('Modera\FileRepositoryBundle\Entity\Repository');
        \Phake::when($repository)->generateStorageKey($splFile, $context)->thenReturn('storage-key');
        $storedFile = new StoredFile($repository, $splFile, $context);
        $storedFile->init($container);

        \Phake::when($defaultUrlGenerator)->generateUrl($storedFile)->thenReturn('default_url');
        \Phake::when($fooUrlGenerator)->generateUrl($storedFile)->thenReturn('foo_url');

        \Phake::when($repository)->getConfig()->thenReturn(array('filesystem' => 'foo'));
        $this->assertEquals('foo_url', $storedFile->getUrl());

        \Phake::when($repository)->getConfig()->thenReturn(array('filesystem' => 'bar'));
        $this->assertEquals('default_url', $storedFile->getUrl());

        \Phake::when($repository)->getConfig()->thenReturn(array('filesystem' => 'baz'));
        $this->assertEquals('default_url', $storedFile->getUrl());
    }
}
