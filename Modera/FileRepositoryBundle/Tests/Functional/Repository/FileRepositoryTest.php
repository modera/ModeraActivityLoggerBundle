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
    private static $st;

    /**
     * {@inheritdoc}
     */
    public static function doSetUpBeforeClass()
    {
        self::$st = new SchemaTool(self::$em);
        self::$st->createSchema(array(
            self::$em->getClassMetadata(Repository::clazz()),
            self::$em->getClassMetadata(StoredFile::clazz()),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public static function doTearDownAfterClass()
    {
        self::$st->dropSchema(array(
            self::$em->getClassMetadata(Repository::clazz()),
            self::$em->getClassMetadata(StoredFile::clazz()),
        ));
    }

    public function testHowWellItWorks()
    {
        /* @var FileRepository $fr */
        $fr = self::$container->get('modera_file_repository.repository.file_repository');

        $this->assertNull($fr->getRepository('dummy_repository'));

        $repositoryConfig = array(
            'storage_key_generator' => 'modera_file_repository.repository.uniqid_key_generator',
            'filesystem' => 'dummy_tmp_fs',
        );

        $this->assertFalse($fr->repositoryExists('dummy_repository'));

        $repository = $fr->createRepository('dummy_repository', $repositoryConfig, 'My dummy repository');

        $this->assertTrue($fr->repositoryExists('dummy_repository'));

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
        $filePath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'our-dummy-file.txt';
        file_put_contents($filePath, $fileContents);

        $file = new File($filePath);

        $storedFile = $fr->put($repository->getName(), $file, array());

        self::$em->clear(); // this way we will make sure that data is actually persisted in database

        /* @var StoredFile $storedFile */
        $storedFile = self::$em->find(StoredFile::clazz(), $storedFile->getId());

        $this->assertInstanceOf(StoredFile::clazz(), $storedFile);
        $this->assertNotNull($storedFile->getId());
        $this->assertNotNull($storedFile->getStorageKey());
        $this->assertEquals('our-dummy-file.txt', $storedFile->getFilename());
        $this->assertNotNull($storedFile->getCreatedAt());
        $this->assertEquals('txt', $storedFile->getExtension());
        $this->assertEquals('text/plain', $storedFile->getMimeType());
        $this->assertSame($repository->getId(), $storedFile->getRepository()->getId());
        $this->assertEquals($fileContents, $storedFile->getContents());
        $this->assertTrue('' != $storedFile->getChecksum());
        $this->assertEquals($file->getSize(), $storedFile->getSize());

        // ---

        $storedFileData = array(
            'id' => $storedFile->getId(),
            'storageKey' => $storedFile->getStorageKey(),
            'filename' => $storedFile->getFilename(),
        );
        $fileContents = 'bar contents';
        $filePath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'bar-dummy-file.txt';
        file_put_contents($filePath, $fileContents);

        $file = new File($filePath);

        $storedFile = $fr->put($repository->getName(), $file, array());
        self::$em->clear(); // this way we will make sure that data is actually persisted in database

        /* @var StoredFile $storedFile */
        $storedFile = self::$em->find(StoredFile::clazz(), $storedFile->getId());

        $this->assertNotEquals($storedFileData['id'], $storedFile->getId());
        $this->assertNotEquals($storedFileData['storageKey'], $storedFile->getStorageKey());
        $this->assertNotEquals($storedFileData['filename'], $storedFile->getFilename());

        // ---

        $repositoryConfig['overwrite_files'] = true;
        $repository = $storedFile->getRepository();
        $repository->setConfig($repositoryConfig);
        self::$em->persist($repository);
        self::$em->flush();

        $storedFileData = array(
            'id' => $storedFile->getId(),
            'storageKey' => $storedFile->getStorageKey(),
            'filename' => $storedFile->getFilename(),
        );
        $storedFile = $fr->put($repository->getName(), $file, array());
        self::$em->clear(); // this way we will make sure that data is actually persisted in database

        /* @var StoredFile $storedFile */
        $storedFile = self::$em->find(StoredFile::clazz(), $storedFile->getId());

        $this->assertEquals($storedFileData['id'], $storedFile->getId());
        $this->assertEquals($storedFileData['storageKey'], $storedFile->getStorageKey());
        $this->assertEquals($storedFileData['filename'], $storedFile->getFilename());

        // ---

        $fs = $storedFile->getRepository()->getFilesystem();

        $this->assertTrue($fs->has($storedFile->getStorageKey()));

        self::$em->remove($storedFile);
        self::$em->flush();

        $this->assertFalse($fs->has($storedFile->getStorageKey()));
    }
}
