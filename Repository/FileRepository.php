<?php

namespace Modera\FileRepositoryBundle\Repository;

use Doctrine\ORM\EntityManager;
use Gaufrette\Filesystem;
use Modera\FileRepositoryBundle\Entity\Repository;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class FileRepository
{
    /* @var EntityManager $em */
    private $em;
    /* @var ContainerInterface $container */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->em = $container->get('doctrine.orm.entity_manager');
        $this->container = $container;
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

    /**
     * @param string $name
     * @param array $config
     * @param string $label
     *
     * @return Repository
     */
    public function createRepository($name, array $config, $label)
    {
        $repository = new Repository($name, $config);
        $repository->setLabel($label);
        $repository->init($this->container);

        $this->em->persist($repository);
        $this->em->flush();

        return $repository;
    }

    /**
     * @throws \RuntimeException
     *
     * @param $repositoryName
     * @param \SplFileInfo $file
     * @param array $context
     *
     * @return \Modera\FileRepositoryBundle\Entity\StoredFile
     */
    public function put($repositoryName, \SplFileInfo $file, array $context)
    {
        $repository = $this->getRepository($repositoryName);
        if (!$repository) {
            throw new \RuntimeException();
        }

        $repository->beforePut($file);

        $storedFile = $repository->createFile($file, $context);

        $contents = @file_get_contents($file->getPathname());
        if (false === $contents) {
            throw new \RuntimeException(sprintf(
                'Unable to read contents of "%s" file!', $file->getPath()
            ));
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

            throw $e;
        }

        $repository->afterPut($storedFile, $file);

        return $storedFile;
    }
} 