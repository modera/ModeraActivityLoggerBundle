<?php

namespace Modera\FileRepositoryBundle\Intercepting;

use Modera\FileRepositoryBundle\Entity\Repository;
use Modera\FileRepositoryBundle\Entity\StoredFile;

/**
 * Implementations of this interface will be able to perform additional actions
 * when a file is being uploaded to a repository.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
interface OperationInterceptorInterface
{
    /**
     * Throwing an exception in this method will prevent a file from
     * being uploaded to a repository.
     *
     * @param \SplFileInfo $file
     * @param Repository   $repository
     */
    public function beforePut(\SplFileInfo $file, Repository $repository);

    /**
     * Method is invoked when a StoredFile is configured but before it is persisted into storage.
     *
     * @param StoredFile   $storedFile
     * @param \SplFileInfo $file
     * @param Repository   $repository
     */
    public function onPut(StoredFile $storedFile, \SplFileInfo $file, Repository $repository);

    /**
     * Method is invoked when a file is uploaded and $storedFile has been successfully persisted.
     *
     * @param StoredFile   $storedFile
     * @param \SplFileInfo $file
     * @param Repository   $repository
     */
    public function afterPut(StoredFile $storedFile, \SplFileInfo $file, Repository $repository);
}
