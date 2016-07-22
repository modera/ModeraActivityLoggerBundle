<?php

namespace Modera\MJRCacheAwareClassLoaderBundle\VersionResolving;

/**
 * Implementations are responsible for resolving what version of MF is installed.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
interface VersionResolverInterface
{
    /**
     * Method must return installed MF version.
     *
     * @return string|number
     */
    public function resolve();
}
