<?php

namespace Modera\FileRepositoryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gaufrette\Filesystem;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Every repository is associated one underlying Gaufrette filesystem.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class Repository
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Stores configuration for this repository. Some standard configuration properties:
     *
     *  * filesystem  -- DI service ID which points to \Gaufrette\Filesystem which will be used to store files for
     *                   this repository.
     *  * storage_key_generator -- DI service ID of class which implements {@class StorageKeyGeneratorInterface}.
     *
     * @var array
     */
    private $config = array();

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $label;

    /**
     * @ORM\OneToMany(targetEntity="File", mappedBy="repository")
     */
    private $files;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;

        $this->files = new ArrayCollection();
    }

    public function beforePut(\SplFileInfo $file)
    {

    }

    public function onPut(StoredFile $storedFile, \SplFileInfo $file)
    {

    }

    public function afterPut(StoredFile $storedFile, \SplFileInfo $file)
    {

    }

    /**
     * @param ContainerInterface $container
     */
    public function init(ContainerInterface $container)
    {
        $this->container = $container;
    }

    static public function clazz()
    {
        return get_called_class();
    }

    /**
     * @return Filesystem
     */
    public function getFilesystem()
    {
        return $this->container->get($this->config['filesystem']);
    }

    /**
     * @param \SplFileInfo $file
     * @param array $context
     *
     * @return string
     */
    public function generateStorageKey(\SplFileInfo $file, array $context)
    {
        return $this->container->get($this->config['storage_key_generator'])->generateStorageKey($file, $context);
    }

    /**
     * @param \SplFileInfo $file
     * @param array $context
     *
     * @return StoredFile
     */
    public function createFile(\SplFileInfo $file, array $context = array())
    {
        $result = new StoredFile($this, $file, $context);
        $this->files->add($result);

        return $result;
    }
} 