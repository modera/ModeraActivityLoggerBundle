<?php

namespace Modera\FileRepositoryBundle\Repository;

use Doctrine\ORM\EntityManager;
use Gaufrette\Filesystem;
use Modera\FileRepositoryBundle\Entity\Repository;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class FileRepository
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param string $name
     *
     * @return Repository|null
     */
    public function getRepository($name)
    {
        return $this->em->getRepository(Repository::clazz())->findOneBy(array(
            'name' => $name
        ));
    }

    public function createRepository($name, array $config, $label)
    {
        $repository = new Repository($name);

        // TODO inject container

        return $repository;
    }

    /**
     * @param string $repositoryName
     * @param \SplFileInfo $file
     * @throws \RuntimeException
     */
    public function put($repositoryName, \SplFileInfo $file, array $context)
    {
        $repository = $this->getRepository($repositoryName);
        if (!$repository) {
            throw new \RuntimeException();
        }

        $repository->beforePut($file);

        $storedFile = $repository->createFile($file, $context);

        $contents = @file_get_contents($file->getPath());
        if (false === $contents) {
            throw new \RuntimeException();
        }

        $repository->onPut($storedFile, $file);

        $storageKey = $storedFile->getStorageKey();

        /* @var Filesystem $fs */
        $fs = $repository->getFilesystem();

        // physically stored file
        $fs->write($storageKey, $contents);

        try {
            $this->em->persist($storedFile);
            $this->em->flush($storedFile);
        } catch (\Exception $e) {
            if (!$storedFile->getId()) {
                $fs->delete($storageKey);
            }
        }

        $repository->afterPut($storedFile, $file);
    }
} 