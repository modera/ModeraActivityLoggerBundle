<?php

namespace Modera\ModuleBundle\Manipulator;

use Symfony\Component\HttpKernel\KernelInterface;
use Sensio\Bundle\GeneratorBundle\Manipulator\Manipulator;

/**
 * Changes the PHP code of a Kernel.
 *
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class KernelManipulator extends Manipulator
{
    protected $kernel;
    protected $reflected;

    protected $doc =
<<<DOC
    /**
     * Auto generated, do not change!
     *
     * Makes it possible to dynamically inject bundles to kernel.
     *
     * @param array \$bundles
     *
     * @return array
     */
DOC;

    // MPFE-757
    // it is very important that in a snippet below we use "require" function to load external PHP file
    // because when cache:clear command is used, it seems that Symfony creates two instances of Kernel
    // class, and first time when Kernel class is created, "require_once" will load external fine well, but in second
    // instance of Kernel require_once will do nothing, because file has already been loaded during this PHP
    // interpreter session and this results in things such that Symfony doesn't see routes registered
    // by bundles from $moduleBundlesFile file.
    protected $template =
<<<TEMPLATE
    public function registerModuleBundles(array \$bundles)
    {
        \$moduleBundlesFile = __DIR__ . '/%s';
        if (file_exists(\$moduleBundlesFile)) {
            \$moduleBundles = require \$moduleBundlesFile;
            if (is_array(\$moduleBundles)) {
                \$resolver = new \Modera\\ModuleBundle\ModuleHandling\BundlesResolver();

                \$bundles = \$resolver->resolve(
                    array_merge(\$bundles, \$moduleBundles), \$this
                );
            }
        }

        return \$bundles;
    }
TEMPLATE;

    /**
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->reflected = new \ReflectionObject($kernel);
    }

    /**
     * @param string $bundlesFilename A name of a file which will hold dynamically instantiated bundles.
     *
     * @return bool
     */
    public function addCode($bundlesFilename)
    {
        if (!$this->reflected->getFilename()) {
            return false;
        }

        $src = file($this->reflected->getFilename());

        try {
            $method = $this->reflected->getMethod('registerModuleBundles');

            $lines = array_merge(
                array(
                    str_replace(
                        'return $bundles;',
                        'return $this->registerModuleBundles($bundles);',
                        implode('', array_slice($src, 0, $method->getStartLine() - 1))
                    ),
                ),
                array(
                    sprintf($this->template, $bundlesFilename),
                    "\n",
                ),
                array_slice($src, $method->getEndLine())
            );
        } catch (\ReflectionException $e) {
            $line = count($src) - 1;
            while ($line > 0) {
                if (trim($src[$line]) == '}') {
                    break;
                }
                --$line;
            }

            $lines = array_merge(
                array(
                    str_replace(
                        'return $bundles;',
                        'return $this->registerModuleBundles($bundles);',
                        implode('', array_slice($src, 0, $line))
                    ),
                ),
                array(
                    "\n",
                    $this->doc,
                    "\n",
                    sprintf($this->template, $bundlesFilename),
                    "\n",
                ),
                array_slice($src, $line)
            );
        }

        file_put_contents($this->reflected->getFilename(), implode('', $lines));

        return true;
    }
}
