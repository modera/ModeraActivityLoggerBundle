<?php

namespace Modera\FileRepositoryBundle\Tests\Functional\Repository;

use Doctrine\ORM\Tools\SchemaTool;
use Modera\FileRepositoryBundle\Entity\Repository;
use Modera\FileRepositoryBundle\Entity\StoredFile;
use Modera\FileRepositoryBundle\Repository\FileRepository;
use Modera\FoundationBundle\Testing\FunctionalTestCase;
use Sli\AuxBundle\Util\Toolkit;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class FileRepositoryTest extends FunctionalTestCase
{
    static private $st;

    // override
    static public function doSetUpBeforeClass()
    {
        self::$st = new SchemaTool(self::$em);
        self::$st->createSchema(array(
            self::$em->getClassMetadata(Repository::clazz()),
            self::$em->getClassMetadata(StoredFile::clazz())
        ));
    }

    // override
    static public function doTearDownAfterClass()
    {
        self::$st->dropSchema(array(
            self::$em->getClassMetadata(Repository::clazz()),
            self::$em->getClassMetadata(StoredFile::clazz())
        ));
    }

    public function testHowWellItWorks()
    {
        /* @var FileRepository $fr */
        $fr = self::$container->get('modera_file_repository.repository.file_repository');

        $this->assertNull($fr->getRepository('dummy_repository'));

        $repositoryConfig = array(
            'storage_key_generator' => 'modera_file_repository.repository.uniqid_key_generator',
            'filesystem' => 'dummy_tmp_fs'
        );

        $repository = $fr->createRepository('dummy_repository', $repositoryConfig, 'My dummy repository');

        $this->assertInstanceOf(Repository::clazz(), $repository);
        $this->assertNotNull($repository->getId());
        $this->assertEquals('dummy_repository', $repository->getName());
        $this->assertEquals('My dummy repository', $repository->getLabel());
        $this->assertSame($repositoryConfig, $repository->getConfig());
        $this->assertInstanceOf(
            'Symfony\Component\DependencyInjection\ContainerInterface',
            Toolkit::getPropertyValue($repository, 'container')
        );

        // ---

        $fileContents = 'foo contents';
        $filePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'our-dummy-file.txt';
        file_put_contents($filePath, $fileContents);

        $file = new File($filePath);

        $storedFile = $fr->put($repository->getName(), $file, array());

        $this->assertInstanceOf(StoredFile::clazz(), $storedFile);
        $this->assertNotNull($storedFile->getId());
        $this->assertNotNull($storedFile->getStorageKey());
        $this->assertEquals('our-dummy-file.txt', $storedFile->getFilename());
        $this->assertNotNull($storedFile->getCreatedAt());
        $this->assertEquals('txt', $storedFile->getExtension());
        $this->assertEquals('text/plain', $storedFile->getMimeType());
        $this->assertSame($repository, $storedFile->getRepository());
        $this->assertEquals($fileContents, $storedFile->getContents());
        $this->assertTrue('' != $storedFile->getChecksum());
        $this->assertEquals($file->getSize(), $storedFile->getSize());

        // ---

        $fs = $storedFile->getRepository()->getFilesystem();

        $this->assertTrue($fs->has($storedFile->getStorageKey()));

        self::$em->remove($storedFile);
        self::$em->flush();

        $this->assertFalse($fs->has($storedFile->getStorageKey()));
    }
}