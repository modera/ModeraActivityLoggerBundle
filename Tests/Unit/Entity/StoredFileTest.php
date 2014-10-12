<?php

namespace Modera\FileRepositoryBundle\Tests\Unit\Entity;

use Modera\FileRepositoryBundle\Entity\StoredFile;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */ 
class StoredFileTest extends \PHPUnit_Framework_TestCase
{
    public function test__construct()
    {
        $filename = uniqid() . '.txt';
        $filePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;
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
}