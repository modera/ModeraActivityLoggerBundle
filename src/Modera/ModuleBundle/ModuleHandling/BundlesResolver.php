<?php

namespace Modera\ModuleBundle\ModuleHandling;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Service is responsible for handling implementations of ModuleBundleInterface - which makes it possible
 * that a bundle would provide some additional bundles into the system.
 *
 * @experimental
 * @private
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2015 Modera Foundation
 */
class BundlesResolver
{
    /**
     * @param BundleInterface[] $bundles
     * @param KernelInterface   $kernel
     *
     * @return BundleInterface[]
     */
    private function doResolve(array $bundles, KernelInterface $kernel)
    {
        $resolvedBundles = [];

        foreach ($bundles as $bundle) {
            if ($bundle instanceof ModuleBundleInterface) {
                $resolvedBundles = array_merge(
                    $resolvedBundles, $this->doResolve($bundle->getBundles($kernel), $kernel)
                );
            }

            $resolvedBundles[] = $bundle;
        }

        return $resolvedBundles;
    }

    /**
     * @param BundleInterface[] $moduleBundles
     * @param KernelInterface   $kernel
     *
     * @return BundleInterface[]
     */
    public function resolve(array $moduleBundles, KernelInterface $kernel)
    {
        $resolvedBundles = $this->doResolve($moduleBundles, $kernel);

        $uniqueBundles = array();
        foreach ($resolvedBundles as $bundle) {
            if (!isset($uniqueBundles[$bundle->getName()])) {
                $uniqueBundles[$bundle->getName()] = $bundle;
            }
        }

        return array_values($uniqueBundles);
    }
}
