<?php

namespace Modera\FileRepositoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * When this entity is removed from database associated with it physical file be automatically removed as well.
 *
 * Instances of this class are not meant to be created directly, please use
 * {@class \Modera\FileRepositoryBundle\Repository\FileRepository::put} instead.
 *
 * @ORM\Entity
 * @ORM\Table("modera_filerepository_storedfile")
 * @ORM\HasLifecycleCallbacks
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class StoredFile
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Repository
     *
     * @ORM\ManyToOne(targetEntity="Repository", inversedBy="files")
     */
    private $repository;

    /**
     * This is a filename that is used to identify this file in "filesystem".
     *
     * @ORM\Column(type="text")
     */
    private $storageKey;

    /**
     * Full filename. For example - /dir1/dir2/file.txt
     *
     * @var string
     */
    private $filename;

    /**
     * Some additional metadata you may want to associate with this file.
     *
     * @ORM\Column(type="array")
     */
    private $meta = array();

    /**
     * Some value that your application logic can understand and identify a user. It could be user entity id, for example.
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $author;

    /**
     * Some tag that later can be used to figure what this stored file belongs to. It can be whatever value that your
     * logic can parse, no restrictions implied.
     */
    private $owner;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * File extension. For example, for file "file.jpg" this field will contain "jpg".
     *
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $extension;

    /**
     * @var null|string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $mimeType;

    /**
     * @param Repository $repository
     * @param \SplFileInfo $file
     * @param array $context
     */
    public function __construct(Repository $repository, \SplFileInfo $file, array $context = array())
    {
        $this->repository = $repository;

        $this->storageKey = $repository->generateStorageKey($file, $context);
        if (!$this->storageKey) {
            throw new \RuntimeException('No storage key has been generated!');
        }

        $this->createdAt = new \DateTime('now');

        $this->filename = $file->getFilename();
        $this->extension = $file->getExtension();

        if ($file instanceof File) {
            $this->mimeType = $file->getMimeType();
        }
        if ($file instanceof UploadedFile) {
            $this->filename = $file->getClientOriginalName();
            $this->extension = $file->getClientOriginalExtension();
        }
    }

    static public function clazz()
    {
        return get_called_class();
    }

    /**
     * This method is not meant to be used directly.
     *
     * @ORM\PreRemove
     */
    public function onRemove()
    {
        $this->repository->getFilesystem()->delete($this->storageKey);
    }

    /**
     * @return string
     */
    public function getContents()
    {
        return $this->repository->getFilesystem()->read($this->storageKey);
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->repository->getFilesystem()->size($this->storageKey);
    }

    /**
     * @return string
     */
    public function getChecksum()
    {
        return $this->repository->getFilesystem()->checksum($this->storageKey);
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @return null|string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @return mixed
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @return \Modera\FileRepositoryBundle\Entity\Repository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @return mixed
     */
    public function getStorageKey()
    {
        return $this->storageKey;
    }
} 