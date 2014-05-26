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
     * @param array \$bundles
     * @return array
     */
DOC;

    protected $template =
<<<TEMPLATE
    public function registerModuleBundles(array \$bundles)
    {
        \$moduleBundles = require_once '%s';
        if (is_array(\$moduleBundles)) {
            foreach (\$moduleBundles as \$bundle) {
                if (!in_array(\$bundle, \$bundles)) {
                    \$bundles[] = \$bundle;
                }
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
     * @param string $file
     * @return bool
     */
    public function addCode($file)
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
                    )
                ),
                array(
                    sprintf($this->template, $file),
                    "\n"
                ),
                array_slice($src, $method->getEndLine())
            );
        } catch (\ReflectionException $e) {
            $line = count($src) - 1;
            while ($line > 0) {
                if (trim($src[$line]) == '}') {
                    break;
                }
                $line--;
            }

            $lines = array_merge(
                array(
                    str_replace(
                        'return $bundles;',
                        'return $this->registerModuleBundles($bundles);',
                        implode('', array_slice($src, 0, $line))
                    )
                ),
                array(
                    "\n",
                    $this->doc,
                    "\n",
                    sprintf($this->template, $file),
                    "\n"
                ),
                array_slice($src, $line)
            );
        }

        file_put_contents($this->reflected->getFilename(), implode('', $lines));

        return true;
    }
}
