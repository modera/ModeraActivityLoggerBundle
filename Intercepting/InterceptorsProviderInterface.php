<?php

namespace Modera\FileRepositoryBundle\Intercepting;

use Modera\FileRepositoryBundle\Entity\Repository;

/**
 * Implementations of this interface are responsible for providing interceptors whe a file is being
 * uploaded to a repository.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2015 Modera Foundation
 */
interface InterceptorsProviderInterface
{
    /**
     * @param Repository $repository
     *
     * @return OperationInterceptorInterface[]
     */
    public function getInterceptors(Repository $repository);
}
