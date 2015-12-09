<?php

namespace Modera\FileRepositoryBundle\Repository;

/**
 * Implementations are responsible for generating keys which will be used to store files using Gaufrette filesystem
 * adapters.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
interface StorageKeyGeneratorInterface
{
    /**
     * @param \SplFileInfo $file
     *
     * @return string
     */
    public function generateStorageKey(\SplFileInfo $file, array $context = array());
}
