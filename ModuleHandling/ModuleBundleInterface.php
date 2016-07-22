<?php

namespace Modera\ModuleBundle\ModuleHandling;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Your bundle class can implement this interface if it is needed that it would include some other bundles
 * into the system as well.
 *
 * @experimental
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2015 Modera Foundation
 */
interface ModuleBundleInterface
{
    /**
     * @param KernelInterface $kernel
     *
     * @return BundleInterface[]
     */
    public function getBundles(KernelInterface $kernel);
}
