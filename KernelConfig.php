<?php

namespace Modera\DynamicallyConfigurableAppBundle;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class KernelConfig
{
    /**
     * @return array
     */
    static public function read() {
        $defaultMode = array(
            'env'   => 'prod',
            'debug' => false
        );

        $reflKernel = new \ReflectionClass('AppKernel');

        $mode = file_get_contents(dirname($reflKernel->getFileName()) . '/kernel.json');

        if (false == $mode) {
            return $defaultMode;
        } else {
            $mode = json_decode($mode, true);
            if (is_array($mode) && isset($mode['env']) && isset($mode['debug'])) {
                return $mode;
            } else {
                return $defaultMode;
            }
        }
    }
}
