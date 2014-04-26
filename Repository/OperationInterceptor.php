<?php

namespace Modera\FileRepositoryBundle\Repository;

use Modera\FileRepositoryBundle\Entity\StoredFile;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
interface OperationInterceptor
{
    public function beforePut(\SplFileInfo $file);

    public function onPut(StoredFile $storedFile, \SplFileInfo $file);

    public function afterPut(StoredFile $storedFile, \SplFileInfo $file);
} 