<?php

namespace Modera\DynamicallyConfigurableAppBundle\ValueHandling;

use Modera\ConfigBundle\Config\ConfigurationEntryInterface;
use Modera\ConfigBundle\Config\ValueUpdatedHandlerInterface;
use Modera\DynamicallyConfigurableAppBundle\ModeraDynamicallyConfigurableAppBundle as Bundle;

/**
 * When "kernel_env", "kernel_debug" configuration entries are updated will synchronize its values with
 * kernel.json.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class KernelConfigWriter implements ValueUpdatedHandlerInterface
{
    /**
     * @inheritDoc
     */
    public function onUpdate(ConfigurationEntryInterface $entry)
    {
        if (!in_array($entry->getName(), array(Bundle::CONFIG_KERNEL_DEBUG, Bundle::CONFIG_KERNEL_ENV))) {
            return;
        }

        $reflKernel = new \ReflectionClass('AppKernel');

        $path = dirname($reflKernel->getFileName()) . DIRECTORY_SEPARATOR . 'kernel.json';

        $kernelJson = @file_get_contents($path);
        if (false === $kernelJson) {
            throw new \RuntimeException('Unable to find kernel.json, looked in ' . $path);
        }
        $kernelJson = implode("\n", array_slice(explode("\n", $kernelJson), 1));
        $kernelJson = json_decode($kernelJson, true);

        if ($entry->getName() == Bundle::CONFIG_KERNEL_DEBUG) {
            $kernelJson['debug'] = $entry->getValue() == 'true';
        } else if ($entry->getName() == Bundle::CONFIG_KERNEL_ENV) {
            $kernelJson['env'] = $entry->getValue();
        }

        $encodedKernelJson = "// this file is used by web/app.php to control with what configuration AppKernel should be created with\n";
        $encodedKernelJson.= json_encode($kernelJson, \JSON_PRETTY_PRINT);

        file_put_contents($path, $encodedKernelJson);
    }
} 