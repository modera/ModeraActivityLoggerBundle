<?php

namespace Modera\FileRepositoryBundle\Repository;

use Modera\FileRepositoryBundle\Entity\StoredFile;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
interface OperationInterceptor
{
    /**
     * @param \SplFileInfo $file
     */
    public function beforePut(\SplFileInfo $file);

    /**
     * @param StoredFile   $storedFile
     * @param \SplFileInfo $file
     */
    public function onPut(StoredFile $storedFile, \SplFileInfo $file);

    /**
     * @param StoredFile   $storedFile
     * @param \SplFileInfo $file
     */
    public function afterPut(StoredFile $storedFile, \SplFileInfo $file);
}
