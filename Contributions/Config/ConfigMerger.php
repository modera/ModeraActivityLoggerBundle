<?php

namespace Modera\JSRuntimeIntegrationBundle\Contributions\Config;

use Modera\JSRuntimeIntegrationBundle\Config\ConfigMergerInterface;

/**
 * Merges standard and very basic configuration.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class ConfigMerger implements ConfigMergerInterface
{
    private $bundleConfig;

    /**
     * @param array $bundleConfig
     */
    public function __construct(array $bundleConfig)
    {
        $this->bundleConfig = $bundleConfig;
    }

    /**
     * {@inheritDoc}
     */
    public function merge(array $existingConfig)
    {
        return array_merge($existingConfig, array(
            'homeSection' => $this->bundleConfig['home_section']
        ));
    }
}