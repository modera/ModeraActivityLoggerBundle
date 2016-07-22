<?php

namespace Modera\DirectBundle\Api;

use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class ControllerFinder
{
    /**
     * Find all controllers from a bundle.
     *
     * @param BundleInterface $bundle
     *
     * @return string[]
     */
    public function getControllers(BundleInterface $bundle)
    {
        $dir = $bundle->getPath().'/Controller';

        $controllers = array();

        if (is_dir($dir)) {
            $finder = new Finder();
            $finder->files()->in($dir)->name('*Controller.php');

            foreach ($finder as $file) {
                if ($file->getRelativePath() === 'Base') {
                    continue;
                }

                // we expect classes to follow PSR class-loading standard
                $controllerName = substr($file->getPathname(), strlen($bundle->getPath()) + 1, -1 * strlen('.php'));
                $controllerName = str_replace(DIRECTORY_SEPARATOR, '\\', $controllerName);

                $controllers[] = $bundle->getNamespace().'\\'.$controllerName;
            }
        }

        return $controllers;
    }
}
