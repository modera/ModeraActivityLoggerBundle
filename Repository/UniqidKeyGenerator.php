<?php

namespace Modera\FileRepositoryBundle\Repository;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class UniqidKeyGenerator implements StorageKeyGeneratorInterface
{
    /**
     * @inheritDoc
     */
    public function generateStorageKey(\SplFileInfo $file, array $context = array())
    {
        return uniqid();
    }
} 