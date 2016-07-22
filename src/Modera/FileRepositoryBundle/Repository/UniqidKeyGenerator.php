<?php

namespace Modera\FileRepositoryBundle\Repository;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class UniqidKeyGenerator implements StorageKeyGeneratorInterface
{
    private $preserveExtension;

    /**
     * @param bool $preserveExtension If this parameter is set to TRUE then when a filename is generated original's file
     *                                extension will be added to the new filename.
     */
    public function __construct($preserveExtension = false)
    {
        $this->preserveExtension = $preserveExtension;
    }

    /**
     * {@inheritdoc}
     */
    public function generateStorageKey(\SplFileInfo $file, array $context = array())
    {
        return uniqid().($this->preserveExtension ? '.'.$file->getExtension() : '');
    }
}
