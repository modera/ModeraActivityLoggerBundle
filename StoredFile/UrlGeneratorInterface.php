<?php

namespace Modera\FileRepositoryBundle\StoredFile;

use Modera\FileRepositoryBundle\Entity\StoredFile;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2015 Modera Foundation
 */
interface UrlGeneratorInterface
{
    /**
     * @param StoredFile $storedFile
     * @return string
     */
    public function generateUrl(StoredFile $storedFile);
}
